<?php
/**
 * @package MSG
 * @version 1.0
 */
/*
Plugin Name: MSG LMS
Description: This plugin for customization of learnDash and Woocommerce to integrate with MSG site.
Author: Shafiq Hossain
Version: 1.0
Author URI: http://www.isoftbd.com/
*/

defined( 'ABSPATH' ) || exit;

// Load functions files.
require_once( dirname( __FILE__ ) . '/msg-functions.php' );

/**
 * The main subscriptions class.
 *
 * @since 1.0
 */
class MSG_Lms {

  function __construct() {
    if ( !is_admin() ) {
      // Fires on every page load after WordPress, all plugins, and the theme are fully loaded and instantiated
      //add_action( 'wp-loaded', array( $this, 'check_current_user_subscription' ) );
      //add_action( 'init', array( $this, 'check_current_user_subscription' ) );
    }
  }

	 /**
	 * Get user's subscriptions list.
	 */
  function check_current_user_subscription() {
    // Check if user is even logged in, if not exit
    if ( !is_user_logged_in() ) return;

    $current_user   = wp_get_current_user(); // get current WP_User
    $user_id    = $current_user->ID; // get user id

    $is_subscription = wcs_user_has_subscription( $user_id ); // check if user has subscription
    if ( $is_subscription ) {
      $subscriptions = wcs_get_users_subscriptions( $user_id ); // get array of all subscriptions

      // Check if there is one subscription or multiple subscriptions per user
      if ( count( $subscriptions ) > 1 ) {

        // Example if you wanted to loop through all subscriptions, in the case of the user having multiple subscriptions
        foreach ( $subscriptions as $sub_id => $subscription ) {
          if ( $subscription->get_status() == 'active' ) {
            // Do something
          }
        }
      }
      else { // Only 1 subscription
        $subscription  = reset( $subscriptions ); // gets first and only value
        if ( $subscription->get_status() == 'active' ) {
            // Do something
          }
      }
    }

  }


}

new MSG_Lms( );
