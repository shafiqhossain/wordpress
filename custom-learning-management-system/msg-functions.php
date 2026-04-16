<?php
/**
 * MSG LMS Functions
 *
 * @author 		Shafiq Hossain
 * @category 	Core
 * @package 	MSG Lms/Functions
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once( dirname( __FILE__ ) . '/includes/msg-acf.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-buddypress.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-learndash.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-woocommerce.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-woocommerce-cart.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-woocommerce-memberships.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-woocommerce-subscriptions.php' );
require_once( dirname( __FILE__ ) . '/includes/msg-migrate.php' );


/**
 * Check if the user has access to the course / product
 *
 * @param int $post_id ID of the Product / Course
 * @param int $user_id ID of the User
 */
function msg_has_access_to_course( $post_id, $user_id, $type = 'course' ){
	if($type == 'product') {
	  $courses_id = get_post_meta( $post_id, '_related_course', true );
	}
	else {
	  $course_id = $post_id;
	}

	//get course meta data
	$meta = get_post_meta( $course_id, '_sfwd-courses', true );

	//get current user
	if($user_id) {
	  $current_user_id = $user_id;
	}
	else {
	  $current_user_id = get_current_user_id();
	}

	//if user not logged in
	if(!$current_user_id) {
        $access = false;
    	return $access;
	}

	$membership = user_current_membership( $current_user_id );
	if( $membership ) {
	  $max_enroll_limit = user_membership_course_limit($membership->get_id());
	}
	else {
	  $max_enroll_limit = -1;
	}

	if($current_user_id && $meta['sfwd-courses_course_price_type'] == 'free') {
        $access = true;
    	return $access;
	}

	//has active membership
	if($current_user_id && $membership != false) {
	  $has_active_membership = 1;
	}

    $access = false;
	$enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
	$total_enrolled_courses = user_current_total_course_enrollments( $current_user_id );

	//if no membership, no access
	if($max_enroll_limit == -1) {
        $access = false;
    	return $access;
	}

	//check if already enrolled to this course
	if(in_array($course_id, $enrolled_courses)) {
        $access = true;
    	return $access;
	}

	if($has_active_membership && $max_enroll_limit == 0) {
        $access = true;
    	return $access;
	}

	if($has_active_membership && $total_enrolled_courses < $max_enroll_limit) {
        $access = true;
    	return $access;
	}

    return false;
}


/**
 * @param int $user_id ID of the User
 * @param int $variation_id ID of the Subscription variation
 *
 * @return bool
 */
function user_can_buy_course( $user_id, $variation_id=0 ){
  if(empty($user_id) && empty($variation_id)) return 1;  //allowed

  if(!empty($user_id) && empty($variation_id)) {
	$membership = user_current_membership($user_id);
	if( $membership ) {
      $user_membership_buy_course = get_post_meta( $membership->get_id(), 'user_membership_buy_course', true );
      if(empty($user_membership_buy_course)) $user_membership_buy_course = 0;

	  return $user_membership_buy_course;
	}
	else {
	  return 1; //allowed: there is no membership yet
	}
  }
  else if(!empty($user_id) && !empty($variation_id)) {
	$variation = new WC_Product_Variation($variation_id);
	if( $variation ) {
      $ld_membership_buy_course = get_post_meta( $variation->get_id(), 'ld_membership_buy_course', true );
      if(empty($ld_membership_buy_course)) $ld_membership_buy_course = 0;

	  return $ld_membership_buy_course;
	}
	else {
	  return 1; //allowed: this is not a subscription variation
	}
  }
  else if(empty($user_id) && !empty($variation_id)) {
	$variation = new WC_Product_Variation($variation_id);
	if( $variation ) {
      $ld_membership_buy_course = get_post_meta( $variation->get_id(), 'ld_membership_buy_course', true );
      if(empty($ld_membership_buy_course)) $ld_membership_buy_course = 0;

	  return $ld_membership_buy_course;
	}
	else {
	  return 1; //allowed: this is not a subscription variation
	}
  }

  return 0;
}

/**
 * @param int $user_membership_id ID of the user membership plan
 */
function user_membership_course_limit($user_membership_id){
  if(empty($user_membership_id)) return -1;

  $post = get_post($user_membership_id);

  if ( $post ) {
    $user_membership_course_limit = get_post_meta( $post->ID, 'user_membership_course_limit', true );
    if($user_membership_course_limit == '') $user_membership_course_limit = -1;

    return $user_membership_course_limit;
  }

  return -1;
}


/**
 * @param int $post_id ID of the post
 * @param string $meta_key Meta key of the post
 */
function check_meta_data_exists($post_id, $meta_key) {
  global $wpdb;

  $sql =  "SELECT * FROM wp_postmeta ";
  $sql .= "WHERE post_id=".$post_id." ";
  $sql .= "AND meta_key='".$meta_key."' ";

  $results = $wpdb->get_results($sql);
  if ( $results ) {
    return true;
  }
  else {
    return false;
  }

  return false;
}
