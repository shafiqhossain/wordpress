<?php
if ( ! defined('ABSPATH') ) exit;

class Helper {

    /** Admin base URL for a given page slug */
    public static function admin_url( string $page, array $args = [] ): string {
        return add_query_arg( array_merge( ['page' => $page], $args ), admin_url('admin.php') );
    }

    /** Public URL helper */
    public static function public_url( string $path ): string {
        return home_url( '/' . ltrim( $path, '/' ) );
    }

    public static function subscription_statuses(): array {
        return [ 'Active', 'Suspended', 'Cancelled', 'Expired' ];
    }

    public static function status_badge( string $status ): string {
        $map = [
            'Active'    => 'success',
            'Suspended' => 'warning',
            'Cancelled' => 'danger',
            'Expired'   => 'default',
        ];
        $cls = $map[ $status ] ?? 'default';
        return '<span class="label label-' . $cls . '">' . esc_html( $status ) . '</span>';
    }

    public static function type_badge( string $type ): string {
        $cls = Settings::color( $type );
        return '<span class="label label-' . $cls . '">' . esc_html( $type ) . '</span>';
    }

    /** Expire check – marks subscription Expired if past expire_date */
    public static function auto_expire(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'maxstamp_subscriptions';
        $wpdb->query( $wpdb->prepare(
            "UPDATE $table SET status = 'Expired'
             WHERE status = 'Active' AND expire_date < %s",
            current_time('Y-m-d')
        ) );
    }

    /** Create or retrieve a WordPress user for the given email/name */
    public static function ensure_wp_user( string $email, string $name ): int {
        $user = get_user_by( 'email', $email );
        if ( $user ) {
            return $user->ID;
        }
        $parts    = explode( ' ', trim($name), 2 );
        $username = sanitize_user( strtolower( str_replace(' ', '_', $name) ), true );
        // Make username unique
        $base = $username;
        $i    = 1;
        while ( username_exists($username) ) {
            $username = $base . '_' . $i++;
        }
        $password = wp_generate_password(12, false);
        $user_id  = wp_insert_user([
            'user_login'   => $username,
            'user_pass'    => $password,
            'user_email'   => $email,
            'first_name'   => $parts[0],
            'last_name'    => $parts[1] ?? '',
            'role'         => 'subscriber',
            'display_name' => $name,
        ]);
        if ( is_wp_error($user_id) ) {
            return 0;
        }
        // Send credentials email
        wp_new_user_notification( $user_id, null, 'user' );
        return $user_id;
    }

    public static function nonce_field( string $action ): void {
        wp_nonce_field( $action, '_maxstamp_nonce' );
    }

    public static function verify_nonce( string $action ): bool {
        return isset($_POST['_maxstamp_nonce'])
            && wp_verify_nonce( $_POST['_maxstamp_nonce'], $action );
    }

    public static function redirect( string $url ): void {
        wp_safe_redirect( $url );
        exit;
    }

    public static function flash_set( string $key, string $msg, string $type = 'success' ): void {
        set_transient( 'maxstamp_flash_' . $key, ['msg' => $msg, 'type' => $type], 60 );
    }

    public static function flash_get( string $key ): ?array {
        $data = get_transient( 'maxstamp_flash_' . $key );
        if ( $data ) delete_transient( 'maxstamp_flash_' . $key );
        return $data ?: null;
    }

    public static function alert( string $msg, string $type = 'success' ): string {
        return sprintf(
            '<div class="alert alert-%s alert-dismissible fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>%s
             </div>',
            esc_attr($type), wp_kses_post($msg)
        );
    }

    public static function format_date( ?string $date ): string {
        if ( ! $date || $date === '0000-00-00' ) return '—';
        return date_i18n( get_option('date_format'), strtotime($date) );
    }

    public static function format_price( float $price ): string {
        return Settings::currency() . number_format( $price, 2 );
    }
}
