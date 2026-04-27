<?php
if ( ! defined('ABSPATH') ) exit;

// Handle public subscription form POST
add_action( 'init', 'maxstamp_handle_public_subscribe' );
function maxstamp_handle_public_subscribe() {
    if ( empty($_POST['maxstamp_public_subscribe']) ) return;
    if ( ! Helper::verify_nonce('maxstamp_public_subscribe') ) {
        wp_die('Security check failed.');
    }

    $subscriber_model   = new Subscriber();
    $subscription_model = new Subscription();

    $sub_id = $subscriber_model->upsert( $_POST );
    if ( $sub_id ) {
        $subscription_model->create( array_merge( $_POST, ['subscriber_id' => $sub_id] ) );

        // Admin notification
        $settings = Settings::get();
        if ( $settings['notify_admin_on_new'] && $settings['admin_notify_email'] ) {
            $name  = sanitize_text_field( $_POST['name']  ?? '' );
            $email = sanitize_email(      $_POST['email'] ?? '' );
            $type  = sanitize_text_field( $_POST['subscription_type'] ?? 'Free' );
            wp_mail(
                $settings['admin_notify_email'],
                'New Subscriber: ' . $name,
                "Name: $name\nEmail: $email\nPlan: $type\n\nManage: " . admin_url('admin.php?page=maxstamp_subscribers')
            );
        }

        Helper::redirect( home_url('/subscribe/success') );
    } else {
        // Store error in session-like transient and redirect back
        set_transient( 'maxstamp_public_error_' . md5($_POST['email'] ?? ''), 'Could not process subscription.', 60 );
        Helper::redirect( home_url('/subscribe') );
    }
}
