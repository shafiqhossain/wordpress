<?php
/**
 * MSG LMS Woocommerce Functions
 *
 * @author 		Shafiq Hossain
 * @category 	Core
 * @package 	MSG Lms/Functions
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Exclude products from a particular category on the shop page
 */
function msg_woocommerce_pre_get_posts_query( $q ) {
    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
      'taxonomy' => 'product_cat',
      'field' => 'slug',
      'terms' => array( 'subscription' ), // Don't display products in the subscription category on the shop page.
      'operator' => 'NOT IN'
    );

    $q->set( 'tax_query', $tax_query );
}
add_action( 'woocommerce_product_query', 'msg_woocommerce_pre_get_posts_query' );


/**
 * Register subscription tab
 */
add_filter( 'woocommerce_product_data_tabs', 'msg_woocommerce_add_subscription_data_tab' );

function msg_woocommerce_add_subscription_data_tab( $product_data_tabs ) {
    $product_data_tabs['subscription-tab'] = array(
        'label' => __( 'Subscription Info', 'woocommerce' ),
        'target' => 'subscription_product_data',
        'class'     => array( 'show_if_subscription', 'show_if_subscription_variable' ),
    );

    return $product_data_tabs;
}

/**
 * Call to output fields
*/
add_action('woocommerce_product_data_panels', 'msg_woocommerce_subscription_product_data_fields');

function msg_woocommerce_subscription_product_data_fields() {
    global $post;
    ?> <div id = 'subscription_product_data'
    class = 'panel woocommerce_options_panel' > <?php
        ?> <div class = 'options_group' > <?php

  wp_nonce_field( basename( __FILE__ ), 'subscription_level_nonce' );
  // Number Field
  woocommerce_wp_text_input(
    array(
      'id' => 'subscription_level',
      'label' => __( 'Subscription level', 'woocommerce' ),
      'placeholder' => '',
      'description' => __( 'Enter the subscription level, which will be used for upgrade. User always be able to upgrade to higher level.', 'woocommerce' ),
      'type' => 'number',
      'custom_attributes' => array(
         'step' => 'any',
         'min' => '0'
      )
    )
  );
        ?> </div>
    </div><?php
}

/**
 * Save subscription level data
*/
function msg_woocommerce_save_subscription_level($post_id) {
	if ( !isset( $_POST['subscription_level_nonce'] ) || !wp_verify_nonce( $_POST['subscription_level_nonce'], basename( __FILE__ ) ) )
        	return $post_id;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	$post = get_post( $post_id );

	/* save data only for woocommerce product post type */
	if ( $post->post_type == 'product' ) {
	    $subscription_level = esc_attr( $_POST['subscription_level']);
	    if(empty($subscription_level)) $subscription_level = 0;
		update_post_meta( $post_id, 'subscription_level', $subscription_level );
	}
	return $post_id;
}
add_action( 'woocommerce_process_product_meta', 'msg_woocommerce_save_subscription_level'  );


/*
 * Add Link (Tab) to My Account menu
 */
add_filter ( 'woocommerce_account_menu_items', 'msg_woocommerce_account_menu_items_links', 40 );
function msg_woocommerce_account_menu_items_links( $menu_links ){
	$menu_links = array_slice( $menu_links, 0, 6, true )
	+ array( 'my_lms_profile' => 'My LMS Profile' )
	+ array( 'my_courses' => 'My Courses' )
	+ array( 'edit_social_network_profile' => 'Edit Social Networking Profile' )
	+ array( 'edit_profile_biography' => 'Edit Biography' )
	+ array_slice( $menu_links, 6, NULL, true );

	flush_rewrite_rules();

	return $menu_links;
}


/**
 * Create end point for custom url in Woocommerce dashboard
 *
 * @param string $url URL from wishlist.
 * @param string $endpoint End point name.
 * @param string $value Not used.
 * @param string $permalink Not used.
 *
 * @return string
 */
add_filter( 'woocommerce_get_endpoint_url', 'msg_my_account_rewrite_endpoint', 4, 10 );
function msg_my_account_rewrite_endpoint( $url, $endpoint, $value, $permalink ) {
  if ( 'my_lms_profile' === $endpoint ) {
	$url = '/my-lms-profile/';
  }
  elseif ( 'my_courses' === $endpoint ) {
	$url = '/my-courses/';
  }
  elseif ( 'edit_social_network_profile' === $endpoint ) {
	//$url = '/edit-social-network-profile/';
    $user_id = get_current_user_id();
    $user_data = get_userdata( $user_id );
    $nick_name = $user_data->user_nicename;

    $url = '/members/'.$nick_name.'/profile/edit/group/1/';
  }
  elseif ( 'edit_profile_biography' === $endpoint ) {
	$url = '/edit-profile-detail/';
  }

  return $url;
}
