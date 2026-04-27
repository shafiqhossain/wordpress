<?php
if ( ! defined('ABSPATH') ) exit;

class Subscription {

    private string $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'maxstamp_subscriptions';
    }

    // ── Fetch ─────────────────────────────────────────────────────────────────

    public function all( array $filters = [] ): array {
        global $wpdb;
        $sub_t  = $this->table;
        $sbr_t  = $wpdb->prefix . 'maxstamp_subscribers';

        $where  = [ '1=1' ];
        $values = [];

        if ( ! empty( $filters['subscriber_id'] ) ) {
            $where[]  = 'sub.subscriber_id = %d';
            $values[] = (int) $filters['subscriber_id'];
        }
        if ( ! empty( $filters['status'] ) ) {
            $where[]  = 'sub.status = %s';
            $values[] = $filters['status'];
        }
        if ( ! empty( $filters['subscription_type'] ) ) {
            $where[]  = 'sub.subscription_type = %s';
            $values[] = $filters['subscription_type'];
        }

        $sql = "SELECT sub.*, s.name AS subscriber_name, s.email AS subscriber_email
                FROM {$sub_t} sub
                INNER JOIN {$sbr_t} s ON s.id = sub.subscriber_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY sub.id DESC";

        if ( $values ) $sql = $wpdb->prepare( $sql, ...$values );

        return $wpdb->get_results( $sql ) ?: [];
    }

    public function find( int $id ): ?object {
        global $wpdb;
        $sub_t = $this->table;
        $sbr_t = $wpdb->prefix . 'maxstamp_subscribers';
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT sub.*, s.name AS subscriber_name, s.email AS subscriber_email
             FROM {$sub_t} sub
             INNER JOIN {$sbr_t} s ON s.id = sub.subscriber_id
             WHERE sub.id = %d", $id
        ) );
    }

    public function for_subscriber( int $subscriber_id ): array {
        return $this->all( ['subscriber_id' => $subscriber_id] );
    }

    public function count_by_type(): array {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT subscription_type, COUNT(*) AS total FROM {$this->table} GROUP BY subscription_type"
        ) ?: [];
    }

    public function count_by_status(): array {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT status, COUNT(*) AS total FROM {$this->table} GROUP BY status"
        ) ?: [];
    }

    // ── Write ─────────────────────────────────────────────────────────────────

    public function create( array $data ): int|false {
        global $wpdb;
        $settings = Settings::get();
        $duration = (int) $settings['subscription_duration']; // days

        $start  = ! empty( $data['start_date'] )
                    ? sanitize_text_field( $data['start_date'] )
                    : current_time('Y-m-d');
        $expire = ! empty( $data['expire_date'] )
                    ? sanitize_text_field( $data['expire_date'] )
                    : date('Y-m-d', strtotime( "$start + $duration days" ) );

        $type       = sanitize_text_field( $data['subscription_type'] );
        $price_paid = isset( $data['price_paid'] )
                        ? (float) $data['price_paid']
                        : Settings::price( $type );

        $row = [
            'subscriber_id'     => (int) $data['subscriber_id'],
            'subscription_type' => $type,
            'status'            => sanitize_text_field( $data['status'] ?? 'Active' ),
            'price_paid'        => $price_paid,
            'registered_at'     => current_time('mysql'),
            'start_date'        => $start,
            'expire_date'       => $expire,
            'notes'             => sanitize_text_field( $data['notes'] ?? '' ),
        ];

        $result = $wpdb->insert( $this->table, $row );
        return $result ? (int) $wpdb->insert_id : false;
    }

    public function update( int $id, array $data ): bool {
        global $wpdb;
        $row = [
            'subscription_type' => sanitize_text_field( $data['subscription_type'] ),
            'status'            => sanitize_text_field( $data['status'] ),
            'price_paid'        => (float) $data['price_paid'],
            'start_date'        => sanitize_text_field( $data['start_date'] ),
            'expire_date'       => sanitize_text_field( $data['expire_date'] ),
            'notes'             => sanitize_text_field( $data['notes'] ?? '' ),
        ];
        return (bool) $wpdb->update( $this->table, $row, ['id' => $id] );
    }

    public function update_status( int $id, string $status ): bool {
        global $wpdb;
        return (bool) $wpdb->update(
            $this->table,
            ['status' => $status],
            ['id'     => $id],
            ['%s'], ['%d']
        );
    }

    public function delete( int $id ): bool {
        global $wpdb;
        return (bool) $wpdb->delete( $this->table, ['id' => $id], ['%d'] );
    }
}
