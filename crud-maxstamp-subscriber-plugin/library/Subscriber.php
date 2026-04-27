<?php
if ( ! defined('ABSPATH') ) exit;

class Subscriber {

    private string $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'maxstamp_subscribers';
    }

    // ── Fetch ─────────────────────────────────────────────────────────────────

    public function all( array $filters = [] ): array {
        global $wpdb;
        $where  = [ '1=1' ];
        $values = [];

        if ( ! empty( $filters['search'] ) ) {
            $s        = '%' . $wpdb->esc_like( $filters['search'] ) . '%';
            $where[]  = '( s.name LIKE %s OR s.email LIKE %s OR s.phone LIKE %s )';
            $values[] = $s; $values[] = $s; $values[] = $s;
        }

        // join subscriptions for type/status filter
        $join = '';
        if ( ! empty( $filters['subscription_type'] ) || ! empty( $filters['status'] ) ) {
            $join    = "INNER JOIN {$wpdb->prefix}maxstamp_subscriptions sub ON sub.subscriber_id = s.id";
            if ( ! empty( $filters['subscription_type'] ) ) {
                $where[]  = 'sub.subscription_type = %s';
                $values[] = $filters['subscription_type'];
            }
            if ( ! empty( $filters['status'] ) ) {
                $where[]  = 'sub.status = %s';
                $values[] = $filters['status'];
            }
        }

        $sql = "SELECT DISTINCT s.* FROM {$this->table} s $join WHERE " . implode(' AND ', $where) . " ORDER BY s.id DESC";

        if ( $values ) {
            $sql = $wpdb->prepare( $sql, ...$values );
        }

        return $wpdb->get_results( $sql ) ?: [];
    }

    public function find( int $id ): ?object {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d", $id
        ) );
    }

    public function by_email( string $email ): ?object {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE email = %s", $email
        ) );
    }

    public function count(): int {
        global $wpdb;
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table}" );
    }

    // ── Write ─────────────────────────────────────────────────────────────────

    /**
     * Create or update a subscriber.
     * Returns subscriber ID, or WP_Error.
     */
    public function upsert( array $data ): int|false {
        global $wpdb;

        $email = sanitize_email( $data['email'] );
        $existing = $this->by_email( $email );

        // Ensure WP user exists
        $wp_user_id = Helper::ensure_wp_user( $email, sanitize_text_field( $data['name'] ) );

        $row = [
            'name'       => sanitize_text_field( $data['name'] ),
            'email'      => $email,
            'phone'      => sanitize_text_field( $data['phone'] ?? '' ),
            'extra'      => sanitize_text_field( $data['extra'] ?? '' ),
            'wp_user_id' => $wp_user_id,
        ];

        if ( $existing ) {
            $wpdb->update( $this->table, $row, ['id' => $existing->id] );
            return (int) $existing->id;
        }

        $row['registered_at'] = current_time('mysql');
        $result = $wpdb->insert( $this->table, $row );
        return $result ? (int) $wpdb->insert_id : false;
    }

    public function update( int $id, array $data ): bool {
        global $wpdb;
        $row = [
            'name'  => sanitize_text_field( $data['name'] ),
            'phone' => sanitize_text_field( $data['phone'] ?? '' ),
            'extra' => sanitize_text_field( $data['extra'] ?? '' ),
        ];
        if ( ! empty( $data['email'] ) ) {
            $row['email'] = sanitize_email( $data['email'] );
        }
        return (bool) $wpdb->update( $this->table, $row, ['id' => $id] );
    }

    public function delete( int $id ): bool {
        global $wpdb;
        // Delete all subscriptions first
        $wpdb->delete( $wpdb->prefix . 'maxstamp_subscriptions', ['subscriber_id' => $id], ['%d'] );
        return (bool) $wpdb->delete( $this->table, ['id' => $id], ['%d'] );
    }
}
