<?php
/*
Plugin Name: Maxstamp Subscriber List
Plugin URI:  https://www.isoftbd.com/
Description: Full-featured subscriber management plugin. Visitors can subscribe from a public page. Admins manage subscribers, subscription types, pricing, and statuses. Each subscription is linked to a WordPress user account.
Version:     3.0
Author:      Shafiq Hossain
Author URI:  https://www.isoftbd.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: maxstamp-subscriber
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Constants ────────────────────────────────────────────────────────────────
define( 'MAXSTAMP_DIR',       plugin_dir_path( __FILE__ ) );
define( 'MAXSTAMP_URL',       plugin_dir_url( __FILE__ ) );
define( 'MAXSTAMP_LIBRARY',   MAXSTAMP_DIR . 'library/' );
define( 'MAXSTAMP_PAGE',      MAXSTAMP_DIR . 'pages/' );
define( 'MAXSTAMP_TEMPLATES', MAXSTAMP_DIR . 'templates/' );
define( 'MAXSTAMP_VERSION',   '3.0' );

// ── Core includes ────────────────────────────────────────────────────────────
require_once MAXSTAMP_LIBRARY . 'Helper.php';
require_once MAXSTAMP_LIBRARY . 'Subscriber.php';
require_once MAXSTAMP_LIBRARY . 'Subscription.php';
require_once MAXSTAMP_LIBRARY . 'Settings.php';
require_once MAXSTAMP_DIR    . 'include/maxstamp-admin.php';
require_once MAXSTAMP_DIR    . 'include/maxstamp-public.php';

// ── Activation ───────────────────────────────────────────────────────────────
register_activation_hook( __FILE__, 'maxstamp_activate' );
function maxstamp_activate() {
    maxstamp_create_tables();
    maxstamp_register_rewrite_rules();
    flush_rewrite_rules();
    // Seed default settings if not present
    if ( ! get_option( 'maxstamp_settings' ) ) {
        update_option( 'maxstamp_settings', Settings::defaults() );
    }
}

// ── Deactivation ─────────────────────────────────────────────────────────────
register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
});

// ── Table creation ───────────────────────────────────────────────────────────
function maxstamp_create_tables() {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $c = $wpdb->get_charset_collate();

    // Subscribers (one record per person)
    $sql1 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}maxstamp_subscribers (
        id              BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
        wp_user_id      BIGINT(20)   UNSIGNED NOT NULL DEFAULT 0  COMMENT 'Linked WP user ID',
        name            VARCHAR(100) NOT NULL,
        email           VARCHAR(100) NOT NULL,
        phone           VARCHAR(30)  NOT NULL DEFAULT '',
        extra           VARCHAR(255) NOT NULL DEFAULT '',
        registered_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_email (email),
        KEY idx_wp_user (wp_user_id)
    ) $c;";

    // Subscriptions (one subscriber can have many)
    $sql2 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}maxstamp_subscriptions (
        id                  BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
        subscriber_id       BIGINT(20)   UNSIGNED NOT NULL,
        subscription_type   VARCHAR(50)  NOT NULL DEFAULT 'Free',
        status              ENUM('Active','Suspended','Cancelled','Expired') NOT NULL DEFAULT 'Active',
        price_paid          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        registered_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        start_date          DATE         NOT NULL,
        expire_date         DATE         NOT NULL,
        notes               VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (id),
        KEY idx_subscriber (subscriber_id),
        KEY idx_status (status),
        KEY idx_type (subscription_type)
    ) $c;";

    dbDelta( $sql1 );
    dbDelta( $sql2 );
}

// ── Custom rewrite rules (URL routing) ───────────────────────────────────────
add_action( 'init', 'maxstamp_register_rewrite_rules' );
function maxstamp_register_rewrite_rules() {
    // Public pages
    add_rewrite_rule( '^subscribe/?$',                        'index.php?maxstamp_action=subscribe',          'top' );
    add_rewrite_rule( '^subscribe/success/?$',                'index.php?maxstamp_action=subscribe_success',  'top' );
    add_rewrite_rule( '^subscribers/?$',                      'index.php?maxstamp_action=subscriber_list',    'top' );
    add_rewrite_rule( '^subscribers/([0-9]+)/?$',             'index.php?maxstamp_action=subscriber_view&maxstamp_id=$matches[1]', 'top' );

    // Admin-side pretty URLs handled via WP admin query vars — see admin.php
}

add_filter( 'query_vars', 'maxstamp_query_vars' );
function maxstamp_query_vars( $vars ) {
    $vars[] = 'maxstamp_action';
    $vars[] = 'maxstamp_id';
    return $vars;
}

// ── Front-end router ─────────────────────────────────────────────────────────
add_action( 'template_redirect', 'maxstamp_router' );
function maxstamp_router() {
    $action = get_query_var( 'maxstamp_action' );
    if ( ! $action ) return;

    switch ( $action ) {
        case 'subscribe':
            maxstamp_render_page( 'subscribe' );
            exit;
        case 'subscribe_success':
            maxstamp_render_page( 'subscribe_success' );
            exit;
        case 'subscriber_list':
            maxstamp_render_page( 'subscriber_list' );
            exit;
        case 'subscriber_view':
            maxstamp_render_page( 'subscriber_view' );
            exit;
    }
}

function maxstamp_render_page( $template ) {
    // Use theme's header/footer; inject our template in between
    define( 'MAXSTAMP_CURRENT_TPL', $template );
    include MAXSTAMP_TEMPLATES . 'wrapper.php';
}

// ── Assets ───────────────────────────────────────────────────────────────────
add_action( 'admin_enqueue_scripts', 'maxstamp_admin_assets' );
function maxstamp_admin_assets() {
    wp_enqueue_style(  'maxstamp-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', [], '3.3.7' );
    wp_enqueue_style(  'maxstamp-admin',     MAXSTAMP_URL . 'style/maxstamp_admin.css',  ['maxstamp-bootstrap'], MAXSTAMP_VERSION );
    wp_enqueue_script( 'maxstamp-bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', ['jquery'], '3.3.7', true );
    wp_enqueue_script( 'maxstamp-admin-js',  MAXSTAMP_URL . 'style/maxstamp_admin.js',  ['jquery'], MAXSTAMP_VERSION, true );
}

add_action( 'wp_enqueue_scripts', 'maxstamp_public_assets' );
function maxstamp_public_assets() {
    if ( ! get_query_var('maxstamp_action') ) return;
    wp_enqueue_style(  'maxstamp-bootstrap',   'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', [], '3.3.7' );
    wp_enqueue_style(  'maxstamp-public',       MAXSTAMP_URL . 'style/maxstamp_public.css', ['maxstamp-bootstrap'], MAXSTAMP_VERSION );
    wp_enqueue_script( 'maxstamp-bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', ['jquery'], '3.3.7', true );
    wp_localize_script( 'jquery', 'maxstamp', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('maxstamp_nonce'),
    ]);
}
