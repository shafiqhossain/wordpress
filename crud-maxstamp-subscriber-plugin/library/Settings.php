<?php
/**
 * Settings – manages plugin options including per-type pricing.
 */
if ( ! defined('ABSPATH') ) exit;

class Settings {

    const OPTION_KEY = 'maxstamp_settings';

    public static function defaults(): array {
        return [
            'currency_symbol' => '$',
            'currency_code'   => 'USD',
            'subscription_types' => [
                'Free'     => [ 'label' => 'Free',     'price' => 0.00,   'color' => 'default', 'description' => 'Basic free access' ],
                'Popular'  => [ 'label' => 'Popular',  'price' => 9.99,   'color' => 'primary', 'description' => 'Most popular plan'  ],
                'Gold'     => [ 'label' => 'Gold',     'price' => 29.99,  'color' => 'warning', 'description' => 'Gold tier access'   ],
                'Silver'   => [ 'label' => 'Silver',   'price' => 19.99,  'color' => 'info',    'description' => 'Silver tier access' ],
                'Platinum' => [ 'label' => 'Platinum', 'price' => 49.99,  'color' => 'success', 'description' => 'Full premium access' ],
            ],
            'public_list_enabled'   => 1,
            'notify_admin_on_new'   => 1,
            'admin_notify_email'    => get_option('admin_email'),
            'subscription_duration' => 365, // days
        ];
    }

    public static function get(): array {
        $saved    = get_option( self::OPTION_KEY, [] );
        $defaults = self::defaults();
        // Deep merge so new keys in defaults are always present
        return array_replace_recursive( $defaults, $saved );
    }

    public static function save( array $data ): void {
        update_option( self::OPTION_KEY, $data );
    }

    /** Return ordered list of type keys */
    public static function type_keys(): array {
        return array_keys( self::get()['subscription_types'] );
    }

    /** Return price for a given type */
    public static function price( string $type ): float {
        $settings = self::get();
        return (float) ( $settings['subscription_types'][ $type ]['price'] ?? 0 );
    }

    /** Return Bootstrap label class for a type */
    public static function color( string $type ): string {
        $settings = self::get();
        return $settings['subscription_types'][ $type ]['color'] ?? 'default';
    }

    public static function currency(): string {
        return self::get()['currency_symbol'];
    }
}
