<?php
/**
 * MSG LMS Subscriptions Functions
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
 * Create ld_membership_buy_course for variations
 */
function msg_woocommerce_product_after_variable_attributes( $loop, $variation_data, $variation ) {

  echo '<br><br><div class="variable_subscription_ld_membership_buy_course show_if_variable-subscription" style="">
		  <div class="form-row form-row-full">
			<div class="subscription_ld_membership_buy_course" style="">';

  $ld_membership_buy_course = get_post_meta($variation->ID, 'ld_membership_buy_course', true);
  if(empty($ld_membership_buy_course)) $ld_membership_buy_course = 0;

  woocommerce_wp_radio(
      array(
        'id'            => 'ld_membership_buy_course['. $loop .']',
        'label'         => __('Allow Buy Courses Separately', 'woocommerce' ),
        'value'         => $ld_membership_buy_course,
        'options' => array(
          '1'   => __( 'Yes', 'woocommerce' ),
          '0'   => __( 'No', 'woocommerce' ),
          )
        )
    );

  echo '	</div>
		  </div>
		</div>';
}
add_action( 'woocommerce_product_after_variable_attributes', 'msg_woocommerce_product_after_variable_attributes', 10, 3 );


/**
 * Save ld_membership_buy_course field for variations
*/
function msg_woocommerce_save_product_variation_save_fields( $variation_id, $i) {
  $ld_membership_buy_course = $_POST['ld_membership_buy_course'][$i];
  update_post_meta( $variation_id, 'ld_membership_buy_course', esc_attr( $ld_membership_buy_course ) );
}
add_action( 'woocommerce_save_product_variation', 'msg_woocommerce_save_product_variation_save_fields', 10, 2 );


/**
 * Get the subscriptions lists for the user
 */
function msg_get_user_active_subscription( $user_id = 0 ) {
  if( empty($user_id) && is_user_logged_in() ) {
    $user_id = get_current_user_id();
  }

  // User not logged in, return false
  if( $user_id == 0 ) return false;

  //print 'user_id: '.$user_id.'<br>';

  $active_subscriptions = get_posts( array(
	'numberposts' => -1,
	'meta_key' => '_customer_user',
	'meta_value' => $user_id,
	'post_type' => 'shop_subscription',
	'post_status' => 'wc-active',
  ));

  $current_subscription = array();
  $current_subscription_level = -1;
  foreach($active_subscriptions as $subscription) {
    $subscription_id = $subscription->ID;
	$subscription_data = msg_get_user_subscription_data( $subscription_id );

    $subscription_level = $subscription_data['subscription_level'];
    if($subscription_level > $current_subscription_level) {
      $current_subscription_level = $subscription_level;
	  $current_subscription = $subscription;
    }
  }

  return $current_subscription;
}

/**
 * Get the subscriptions data
 */
function msg_get_user_subscription_data( $subscription_id = 0 ) {
  global $wpdb;

  $results = $wpdb->get_results(
    $wpdb->prepare("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE (meta_key = '_subscription_id' AND meta_value = %d)", $subscription_id)
  );

  $post_id = 0;  //this is actually membership id
  foreach($results as $result){
    $status = get_post_status ( $result->post_id );
	if ( in_array($status, array('wcm-active', 'publish', 'active')) ) {
	  $post_id = $result->post_id;
	  break;
	}
  }

  $product_id = 0;
  $variation_id = 0;
  $order_id = 0;
  $subscription_level = 0;
  $attribute_subscription_plan = '';

  if( $post_id ) {
	$metas = get_post_meta( $post_id );

	foreach($metas as $key => $val){
	  if($key == '_product_id') {
	    $variation_id = $val[0];
	  }
	  elseif($key == '_order_id') {
	    $order_id = $val[0];
	  }
	}
  }

  if( $variation_id ) {
    $product = get_post( $variation_id );
    if( $product ) {
      $product_id = $product->post_parent;
      $subscription_level = $product->menu_order;
    }
	$attribute_subscription_plan = get_post_meta( $variation_id, 'attribute_subscription-plan', true );
  }

  $data = array(
    'product_id' => $product_id,
    'order_id' => $order_id,
    'variation_id' => $variation_id,
    'subscription_id' => $subscription_id,
    'subscription_level' => $subscription_level,
    'attribute_subscription_plan' => $attribute_subscription_plan,
  );


  return $data;
}


/**
 * @param int $post_id ID of the Product
 */
function msg_woocommerce_subscription_level( $post_id, $variation_id=0 ){
  if(empty($post_id)) return 0;

  $post = wc_get_product( $post_id );
  $subscription_level = 0;

  if ( $post && !$variation_id) {
    $subscription_level = get_post_meta( $posts[0]->ID, 'subscription_level', true );
    if(empty($subscription_level)) $subscription_level = 0;
  }
  else {
 	$vars = $post->get_available_variations();
 	foreach($vars as $key => $var) {
 	  if($var['variation_id'] == $variation_id && $var['variation_is_active'] == 1) {
 	    $subscription_level = $key;
 	    break;
 	  }
 	}
  }

  return $subscription_level;
}
