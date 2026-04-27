<?php
if ( ! defined('ABSPATH') ) exit;

// ── Admin menu ────────────────────────────────────────────────────────────────
add_action( 'admin_menu', 'maxstamp_admin_menu' );
function maxstamp_admin_menu() {
    add_menu_page(
        'Maxstamp Subscribers',
        'Subscribers',
        'manage_options',
        'maxstamp_dashboard',
        'maxstamp_page_dashboard',
        'dashicons-groups',
        25
    );
    add_submenu_page( 'maxstamp_dashboard', 'Dashboard',          'Dashboard',          'manage_options', 'maxstamp_dashboard',           'maxstamp_page_dashboard' );
    add_submenu_page( 'maxstamp_dashboard', 'All Subscribers',    'All Subscribers',    'manage_options', 'maxstamp_subscribers',         'maxstamp_page_subscribers' );
    add_submenu_page( 'maxstamp_dashboard', 'Add Subscriber',     'Add Subscriber',     'manage_options', 'maxstamp_subscriber_add',      'maxstamp_page_subscriber_add' );
    add_submenu_page( 'maxstamp_dashboard', 'All Subscriptions',  'All Subscriptions',  'manage_options', 'maxstamp_subscriptions',       'maxstamp_page_subscriptions' );
    add_submenu_page( 'maxstamp_dashboard', 'Add Subscription',   'Add Subscription',   'manage_options', 'maxstamp_subscription_add',    'maxstamp_page_subscription_add' );
    add_submenu_page( 'maxstamp_dashboard', 'Settings',           'Settings',           'manage_options', 'maxstamp_settings',            'maxstamp_page_settings' );

    // Hidden pages (edit screens) – registered but not shown in menu
    add_submenu_page( null, 'Edit Subscriber',   '', 'manage_options', 'maxstamp_subscriber_edit',   'maxstamp_page_subscriber_edit' );
    add_submenu_page( null, 'Edit Subscription', '', 'manage_options', 'maxstamp_subscription_edit', 'maxstamp_page_subscription_edit' );
    add_submenu_page( null, 'View Subscriber',   '', 'manage_options', 'maxstamp_subscriber_view',   'maxstamp_page_subscriber_view' );
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function maxstamp_cap_check() {
    if ( ! current_user_can('manage_options') ) wp_die('Access denied.');
}

function maxstamp_admin_wrap( string $title, callable $cb ): void {
    maxstamp_cap_check();
    Helper::auto_expire();
    echo '<div class="wrap maxstamp-admin">';
    echo '<h1 class="wp-heading-inline">' . esc_html($title) . '</h1>';
    $cb();
    echo '</div>';
}

// ── Page: Dashboard ───────────────────────────────────────────────────────────
function maxstamp_page_dashboard(): void {
    maxstamp_admin_wrap( 'Maxstamp Dashboard', function() {
        $sub_r = new Subscriber();
        $sub_n = new Subscription();
        include MAXSTAMP_PAGE . 'admin/dashboard.php';
    });
}

// ── Page: Subscriber list ─────────────────────────────────────────────────────
function maxstamp_page_subscribers(): void {
    maxstamp_admin_wrap( 'All Subscribers', function() {
        $subscriber_model = new Subscriber();
        $subscription_model = new Subscription();
        include MAXSTAMP_PAGE . 'admin/subscriber-list.php';
    });
}

// ── Page: Add subscriber ──────────────────────────────────────────────────────
function maxstamp_page_subscriber_add(): void {
    maxstamp_admin_wrap( 'Add New Subscriber', function() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && Helper::verify_nonce('maxstamp_add_subscriber') ) {
            $subscriber_model  = new Subscriber();
            $subscription_model = new Subscription();

            $sub_id = $subscriber_model->upsert( $_POST );
            if ( $sub_id ) {
                // Also create a subscription
                $subscription_model->create( array_merge( $_POST, ['subscriber_id' => $sub_id] ) );
                Helper::flash_set( 'subscriber_add', '<strong>Success!</strong> Subscriber and subscription created.' );
                Helper::redirect( Helper::admin_url('maxstamp_subscribers') );
            } else {
                $error = 'Could not save subscriber. Please try again.';
            }
        }
        include MAXSTAMP_PAGE . 'admin/subscriber-form.php';
    });
}

// ── Page: Edit subscriber ─────────────────────────────────────────────────────
function maxstamp_page_subscriber_edit(): void {
    maxstamp_admin_wrap( 'Edit Subscriber', function() {
        $id               = (int) ( $_GET['id'] ?? 0 );
        $subscriber_model = new Subscriber();
        $row              = $subscriber_model->find( $id );
        if ( ! $row ) { echo Helper::alert('Subscriber not found.','danger'); return; }

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && Helper::verify_nonce('maxstamp_edit_subscriber') ) {
            $subscriber_model->update( $id, $_POST );
            Helper::flash_set('subscriber_edit', '<strong>Updated!</strong> Subscriber saved.');
            Helper::redirect( Helper::admin_url('maxstamp_subscriber_view', ['id' => $id]) );
        }
        $edit_mode = true;
        include MAXSTAMP_PAGE . 'admin/subscriber-form.php';
    });
}

// ── Page: View subscriber ─────────────────────────────────────────────────────
function maxstamp_page_subscriber_view(): void {
    maxstamp_admin_wrap( 'Subscriber Detail', function() {
        $id                 = (int) ( $_GET['id'] ?? 0 );
        $subscriber_model   = new Subscriber();
        $subscription_model = new Subscription();
        $row                = $subscriber_model->find( $id );
        if ( ! $row ) { echo Helper::alert('Subscriber not found.','danger'); return; }
        $subscriptions = $subscription_model->for_subscriber( $id );
        $flash = Helper::flash_get('subscriber_edit') ?? Helper::flash_get('subscriber_add') ?? Helper::flash_get('subscription_add');
        include MAXSTAMP_PAGE . 'admin/subscriber-view.php';
    });
}

// ── Page: Delete subscriber (action URL) ──────────────────────────────────────
add_action( 'admin_post_maxstamp_delete_subscriber', function() {
    maxstamp_cap_check();
    $id    = (int) ( $_GET['id'] ?? 0 );
    $nonce = $_GET['_wpnonce'] ?? '';
    if ( ! wp_verify_nonce($nonce, 'maxstamp_delete_subscriber_' . $id) ) wp_die('Security check failed.');
    ( new Subscriber() )->delete( $id );
    Helper::flash_set('subscriber_del','Subscriber deleted.');
    Helper::redirect( Helper::admin_url('maxstamp_subscribers') );
});

// ── Page: All subscriptions ───────────────────────────────────────────────────
function maxstamp_page_subscriptions(): void {
    maxstamp_admin_wrap( 'All Subscriptions', function() {
        $subscription_model = new Subscription();
        include MAXSTAMP_PAGE . 'admin/subscription-list.php';
    });
}

// ── Page: Add subscription ────────────────────────────────────────────────────
function maxstamp_page_subscription_add(): void {
    maxstamp_admin_wrap( 'Add New Subscription', function() {
        $subscriber_model = new Subscriber();
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && Helper::verify_nonce('maxstamp_add_subscription') ) {
            $model  = new Subscription();
            $sub_id = $model->create( $_POST );
            if ( $sub_id ) {
                Helper::flash_set('subscription_add','<strong>Success!</strong> Subscription created.');
                Helper::redirect( Helper::admin_url('maxstamp_subscriptions') );
            } else {
                $error = 'Could not save subscription.';
            }
        }
        $subscriber_id = (int) ( $_GET['subscriber_id'] ?? 0 );
        include MAXSTAMP_PAGE . 'admin/subscription-form.php';
    });
}

// ── Page: Edit subscription ───────────────────────────────────────────────────
function maxstamp_page_subscription_edit(): void {
    maxstamp_admin_wrap( 'Edit Subscription', function() {
        $id    = (int) ( $_GET['id'] ?? 0 );
        $model = new Subscription();
        $row   = $model->find( $id );
        if ( ! $row ) { echo Helper::alert('Subscription not found.','danger'); return; }

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && Helper::verify_nonce('maxstamp_edit_subscription') ) {
            $model->update( $id, $_POST );
            Helper::flash_set('subscription_edit','<strong>Updated!</strong> Subscription saved.');
            Helper::redirect( Helper::admin_url('maxstamp_subscriptions') );
        }
        $edit_mode = true;
        include MAXSTAMP_PAGE . 'admin/subscription-form.php';
    });
}

// ── Action: delete subscription ───────────────────────────────────────────────
add_action( 'admin_post_maxstamp_delete_subscription', function() {
    maxstamp_cap_check();
    $id    = (int) ( $_GET['id'] ?? 0 );
    $nonce = $_GET['_wpnonce'] ?? '';
    if ( ! wp_verify_nonce($nonce, 'maxstamp_delete_subscription_' . $id) ) wp_die('Security check failed.');
    ( new Subscription() )->delete( $id );
    Helper::redirect( Helper::admin_url('maxstamp_subscriptions') );
});

// ── Action: quick status change ───────────────────────────────────────────────
add_action( 'admin_post_maxstamp_status_subscription', function() {
    maxstamp_cap_check();
    $id     = (int) ( $_GET['id']     ?? 0 );
    $status = sanitize_text_field( $_GET['status'] ?? '' );
    $nonce  = $_GET['_wpnonce'] ?? '';
    if ( ! wp_verify_nonce($nonce, 'maxstamp_status_' . $id) ) wp_die('Security check failed.');
    if ( in_array($status, Helper::subscription_statuses(), true) ) {
        ( new Subscription() )->update_status( $id, $status );
    }
    Helper::redirect( Helper::admin_url('maxstamp_subscriptions') );
});

// ── Page: Settings ────────────────────────────────────────────────────────────
function maxstamp_page_settings(): void {
    maxstamp_admin_wrap( 'Plugin Settings', function() {
        $saved = false;
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && Helper::verify_nonce('maxstamp_save_settings') ) {
            $data = Settings::get(); // start from current

            $data['currency_symbol']        = sanitize_text_field( $_POST['currency_symbol'] ?? '$' );
            $data['currency_code']          = sanitize_text_field( $_POST['currency_code']   ?? 'USD' );
            $data['public_list_enabled']    = (int) ( $_POST['public_list_enabled'] ?? 0 );
            $data['notify_admin_on_new']    = (int) ( $_POST['notify_admin_on_new'] ?? 0 );
            $data['admin_notify_email']     = sanitize_email( $_POST['admin_notify_email'] ?? '' );
            $data['subscription_duration']  = max(1, (int) ($_POST['subscription_duration'] ?? 365));

            // Subscription types
            foreach ( array_keys($data['subscription_types']) as $type ) {
                if ( isset( $_POST['type_price'][$type] ) ) {
                    $data['subscription_types'][$type]['price']       = (float) $_POST['type_price'][$type];
                    $data['subscription_types'][$type]['description'] = sanitize_text_field( $_POST['type_desc'][$type] ?? '' );
                }
            }
            Settings::save( $data );
            $saved = true;
        }
        include MAXSTAMP_PAGE . 'admin/settings.php';
    });
}
