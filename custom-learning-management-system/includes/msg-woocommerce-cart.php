<?php
/**
 * MSG LMS Woocommerce Cart Functions
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
 * Disable quantity field from all products
 */
function msg_woocommerce_remove_quantity_fields( $return, $product ) {
  return true;
}
add_filter( 'woocommerce_is_sold_individually', 'msg_woocommerce_remove_quantity_fields', 10, 2 );


/**
 * Validate products when added to cart:
 * 1. User should not able to checkout both subscription and course product at the same time
 * 2. If User has an active membership, user should not able to add product in the cart
 * 3. If User has an active Basic membership, untill the course limit is over, should not able to add the courses in cart
 * 4. Allow user, only to upgrade the subscription, restrict downgrade
 * 5. If there is already an active subscription, user should not be able to add subscription product into cart
 * 6. If the product / course, user already enrolled or purchased, user should not able to add it into cart
 * 7. User should not checkout two different subscription at the same time.
 */
function msg_woocommerce_add_to_cart_validate_products( $valid, $product_id, $quantity ) {
    $valid = true;

	//get the product type
    $new_product = wc_get_product( $product_id );
    $new_product_type = $new_product->get_type();

	$wc_subscription = 0;
	$wc_product = 0;
	$has_active_membership = 0;
	$variation_id = 0;
	$new_subscription_level = 0;
	$variation = array();

	if($new_product_type == 'subscription' || $new_product_type == 'variable-subscription') {
	  $wc_subscription = 1;
	  if($new_product_type == 'variable-subscription') {
		$variation_id = isset($_POST['variation_id']) ? $_POST['variation_id'] : 0;
		$variation = new WC_Product_Variation( $variation_id );
		$new_subscription_level = msg_woocommerce_subscription_level( $product_id, $variation_id );
	  }
	}
	else {
	  $wc_product = 1;
	}

	//get current user
	$current_user_id = get_current_user_id();
	$membership = user_current_membership($current_user_id);
	if( $membership ) {
	  $max_enroll_limit = user_membership_course_limit($membership->get_id());
	}
	else {
	  $max_enroll_limit = -1;
	}

	//has active membership
	if($current_user_id && $membership != false) {
	  $has_active_membership = 1;
	}

	$courses_id = 0;
	if($wc_product) {  //if product
	  $courses_id = get_post_meta( $product_id, '_related_course', true );
	  $enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
	  $total_enrolled_courses = user_current_total_course_enrollments( $current_user_id );

	  //check if already enrolled to this course
	  if(in_array($course_id, $enrolled_courses)) {
        // Sets error message.
        wc_add_notice( __( 'You have already enrolled with this course.' ), 'error' );

        $valid = false;
    	return $valid;
	  }

	  if($has_active_membership && $max_enroll_limit == 0) {
        // Sets error message.
        wc_add_notice( __( 'You have an active membership and you can enroll with any course directly!' ), 'error' );

        $valid = false;
    	return $valid;
	  }

	  if( $current_user_id ) {
	    $can_buy_course = user_can_buy_course( $current_user_id);
	    if(!$can_buy_course) {
          // Sets error message.
          wc_add_notice( __( 'You have an active membership, which not allowing you to buy courses separately!' ), 'error');

          $valid = false;
    	  return $valid;
	    }
	  }

	}

	$old_subscription_level = 0;
	$has_subscription = 0;
	$old_variation_id = 0;
	if($wc_subscription) {  //if subscription
		$subscriptions = wcs_get_users_subscriptions( $current_user_id );
		foreach($subscriptions as $subscription) {
		  if ($subscription->has_status(array('active'))) {
			  //echo $subscription->get_id();
			  $has_subscription = 1;
			  $items = $subscription->get_items();
			  foreach($items as $item) {
				$old_variation_id = $item->get_variation_id();
				$old_product_id = $item->get_product_id();
		  		$old_subscription_level = msg_woocommerce_subscription_level( $old_product_id, $old_variation_id );
			  }
		  }
		}

    	//check if the same subscription is active?
    	if($has_subscription && $old_subscription_level == $new_subscription_level) {
          // Sets error message.
          wc_add_notice( __( 'You already have the same active subscription!' ), 'error' );

          $valid = false;
    	  return $valid;
    	}

		//upgrade subscription check
		if($has_subscription && $old_subscription_level > $new_subscription_level) {
          // Sets error message.
          wc_add_notice( __( 'You can only upgrade your existing subscription!' ), 'error' );

          $valid = false;
    	  return $valid;
		}
	}

	$has_subscription_product_type = 0;
	$has_simple_product_type = 0;

    $cart_product_id = 0;
    $cart_variation_id = 0;
    $cart_product_sku = '';

	$subscription_switch = 0;
	$upgraded_or_downgraded = '';

	// check each cart item for our category
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$cart_product = $cart_item['data'];
		$cart_product_type = $cart_product->get_type();

		if($cart_product_type == 'subscription' || $cart_product_type == 'subscription_variation') {
		  $has_subscription_product_type = 1;
		}
		else {
		  $has_simple_product_type = 1;
		}

		if($cart_product_type == 'subscription_variation') {
		  $cart_product_id = $cart_product->get_parent_id();
		  $cart_variation_id = $cart_product->get_id();
		  $subscription_switch = isset($cart_item['subscription_switch']) && !empty($cart_item['subscription_switch']) ? 1 : 0;
		  $upgraded_or_downgraded = isset($cart_item['subscription_switch']['upgraded_or_downgraded']) && !empty($cart_item['subscription_switch']['upgraded_or_downgraded']) ? $cart_item['subscription_switch']['upgraded_or_downgraded'] : '';
		}
	}

	//subscription level of cart subscription
	$cart_subscription_level = msg_woocommerce_subscription_level( $cart_product_id, $cart_variation_id );

	//check if the cart has both subscription and course product
	if ( $wc_product && $has_subscription_product_type) {
	  if( $cart_variation_id ) {
	    $can_buy_course = user_can_buy_course( $current_user_id, $cart_variation_id);
	    if(!$can_buy_course) {
          // Sets error message.
          wc_add_notice( __( 'The subscription you selected, not allowing you to buy courses separately!' ), 'error');

          $valid = false;
    	  return $valid;
	    }
	  }
	}

	//check if the cart and new add-to-cart product is different type
	if ( $wc_subscription && $has_simple_product_type) {
	  if( $variation_id ) {
	    $can_buy_course = user_can_buy_course( $current_user_id, $variation_id);
	    if(!$can_buy_course) {
          // Sets error message.
          wc_add_notice( __( 'The subscription you selected, not allowing you to buy courses separately!' ), 'notice');

          //clear the previous cart
          WC()->cart->empty_cart();

          //$valid = false;
    	  //return $valid;
	    }
	  }
	}

	//check if the cart and new add-to-cart product is both subscription type
	if ( $wc_subscription && $has_subscription_product_type) {
        // Sets error message.
        wc_add_notice( __( 'You can not buy more then one subscription at the same time!' ), 'error' );

        $valid = false;
    	return $valid;
	}

	if ( $has_subscription_product_type) {
	    if($has_subscription && $cart_subscription_level > $old_subscription_level) {
	      if($subscription_switch && $upgraded_or_downgraded == 'upgraded') {
	        //nothing to do, is done from my subscription page
	      }
	      else {
            // Sets error message.
            wc_add_notice('If you want to upgrade your subscription, please do it from your <a class="woo-status-message-link" href="/my-account/subscriptions/">my subscription</a> page.', 'error');

            $valid = false;
    	    return $valid;
	      }
	    }
	}

    return $valid;
}

add_filter( 'woocommerce_add_to_cart_validation', 'msg_woocommerce_add_to_cart_validate_products', 10, 3 );


/**
 * Validate and check woocommerce product/subscription on cart update.
 */
function msg_woocommerce_cart_update_validate( $valid, $cart_item_key, $values, $quantity ) {
    $min_quantity = 1;
    $max_quantity = 1;

	$has_active_membership = 0;

	//get current user
	$current_user_id = get_current_user_id();
	$membership = user_current_membership($current_user_id);
	if( $membership ) {
	  $max_enroll_limit = user_membership_course_limit($membership->get_id());
	}
	else {
	  $max_enroll_limit = -1;
	}

	//has active membership
	if($current_user_id && $membership != false) {
	  $has_active_membership = 1;
	}

	//user's enrolled courses
	$enrolled_courses = array();
	$total_enrolled_courses = 0;
	if(is_user_logged_in()) {
	  $enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
	  $total_enrolled_courses = user_current_total_course_enrollments( $current_user_id );
	}

    $valid = true;

    // max quantity.
    if ( $quantity > $max_quantity) {
        // Sets error message.
        wc_add_notice( __( 'You can buy only one quantity of product / subscription!' ), 'error' );
        WC()->cart->set_quantity( $cart_item_key, $max_quantity );

        $valid = false;
        return $valid;
    }

	$has_subscription_product_type = 0;
	$has_simple_product_type = 0;

    $cart_product_id = 0;
    $cart_variation_id = 0;
    $cart_simple_products = array();

	$subscription_switch = 0;
	$upgraded_or_downgraded = '';

	// check each cart item for our category
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$cart_product = $cart_item['data'];
		$cart_product_type = $cart_product->get_type();

		if($cart_product_type == 'subscription' || $cart_product_type == 'subscription_variation') {
		  $has_subscription_product_type = 1;
		}
		else {
		  $has_simple_product_type = 1;
		  $cart_simple_products[] = $cart_product->get_id();
		}

		if($cart_product_type == 'subscription_variation') {
		  $cart_product_id = $cart_product->get_parent_id();
		  $cart_variation_id = $cart_product->get_id();
		  $subscription_switch = isset($cart_item['subscription_switch']) && !empty($cart_item['subscription_switch']) ? 1 : 0;
		  $upgraded_or_downgraded = isset($cart_item['subscription_switch']['upgraded_or_downgraded']) && !empty($cart_item['subscription_switch']['upgraded_or_downgraded']) ? $cart_item['subscription_switch']['upgraded_or_downgraded'] : '';
		}
	}


	//check if cart has a subscription
	if($has_subscription_product_type && is_user_logged_in()) {
	  //subscription level of cart subscription
	  $cart_subscription_level = msg_woocommerce_subscription_level( $cart_product_id, $cart_variation_id );
	  $old_has_subscription = 0;
	  $old_subscription_level = 0;

	  $subscriptions = wcs_get_users_subscriptions( $current_user_id );
	  foreach($subscriptions as $subscription) {
		if ($subscription->has_status(array('active'))) {
		  $old_has_subscription = 1;
		  $items = $subscription->get_items();
		  foreach($items as $item) {
			$old_variation_id = $item->get_variation_id();
			$old_product_id = $item->get_product_id();
			$old_subscription_level = msg_woocommerce_subscription_level( $old_product_id, $old_variation_id );
		  }
		}
	  }

      //check if the same subscription is active?
      if($old_has_subscription && $old_subscription_level == $cart_subscription_level) {
          // Sets error message.
          wc_add_notice( __( 'You already have the same active subscription!' ), 'error' );

          $valid = false;
    	  return $valid;
      }

	  //upgrade subscription check
	  if($old_has_subscription && $old_subscription_level > $cart_subscription_level) {
          // Sets error message.
          wc_add_notice( __( 'You can only upgrade your existing subscription!' ), 'error' );

          $valid = false;
    	  return $valid;
	  }
	  else if($old_has_subscription && $cart_subscription_level > $old_subscription_level) {
	    if($subscription_switch && $upgraded_or_downgraded == 'upgraded') {
	      //nothing to do, is done from my subscription page
	    }
	    else {
          // Sets error message.
          wc_add_notice('If you want to upgrade your subscription, please do it from your <a class="woo-status-message-link" href="/my-account/subscriptions/">my subscription</a> page.', 'error');

          $valid = false;
    	  return $valid;
	    }
	  }
	}

	//check if the cart has both subscription and course product
	if ( $has_simple_product_type && $has_subscription_product_type) {
	  if( $cart_variation_id ) {
	    $can_buy_course = user_can_buy_course( $current_user_id, $cart_variation_id);
	    if(!$can_buy_course) {
          // Sets error message.
          wc_add_notice( __( 'The subscription you selected, not allowing you to buy courses separately!' ), 'error');

          $valid = false;
    	  return $valid;
	    }
	  }
	}

	//check if the cart and new add-to-cart product is both subscription type
	if ( $has_simple_product_type && !$has_subscription_product_type && is_user_logged_in()) {
	  $can_buy_course = user_can_buy_course( $current_user_id);
	  if($has_active_membership && !$can_buy_course) {
        // Sets error message.
        wc_add_notice( __( 'You have an active membership, which not allowing you to buy courses separately!' ), 'error');

        $valid = false;
    	return $valid;
	  }

	  if($has_active_membership && $max_enroll_limit == 0) {
        // Sets error message.
        wc_add_notice( __( 'You have an active membership and you can enroll with any course directly!' ), 'error' );

        $valid = false;
    	return $valid;
	  }

	  foreach($cart_simple_products as $cart_simple_product_id) {
		$courses_id = get_post_meta( $cart_simple_product_id, '_related_course', true );

		//check if already enrolled to this course
		if(in_array($course_id, $enrolled_courses)) {
		  // Sets error message.
		  wc_add_notice( __( 'You have already enrolled with some of the courses.' ), 'error' );

		  $valid = false;
		  return $valid;
		}
	  }
	}

    return $valid;
}
add_filter( 'woocommerce_update_cart_validation', 'msg_woocommerce_cart_update_validate', 1, 4 );


/**
 * Validate the cart when click on checkout process
 */
function msg_woocommerce_cart_validate() {
	$has_active_membership = 0;

	//get current user
	$current_user_id	= get_current_user_id();
	$membership = user_current_membership($current_user_id);
	if( $membership ) {
	  $max_enroll_limit = user_membership_course_limit($membership->get_id());
	}
	else {
	  $max_enroll_limit = -1;
	}

	//has active membership
	if($current_user_id && $membership != false) {
	  $has_active_membership = 1;
	}

	//user's enrolled courses
	$enrolled_courses = array();
	$total_enrolled_courses = 0;
	if(is_user_logged_in()) {
	  $enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
	  $total_enrolled_courses = user_current_total_course_enrollments( $current_user_id );
	}

    $valid = true;

	$has_subscription_product_type = 0;
	$has_simple_product_type = 0;

    $cart_product_id = 0;
    $cart_variation_id = 0;
    $cart_product_sku = '';
    $cart_simple_products = array();

	$subscription_switch = 0;
	$upgraded_or_downgraded = '';

	/*
    [subscription_switch] => Array(
            [subscription_id] => 27186
            [item_id] => 9
            [next_payment_timestamp] => 1595926371
            [upgraded_or_downgraded] => upgraded
            [first_payment_timestamp] => 1595926371
            [end_timestamp] => 1611823971
        )
    */

	// check each cart item for our category
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$cart_product = $cart_item['data'];
		$cart_product_type = $cart_product->get_type();

		if($cart_product_type == 'subscription' || $cart_product_type == 'subscription_variation') {
		  $has_subscription_product_type = 1;
		}
		else {
		  $has_simple_product_type = 1;
		  $cart_simple_products[] = $cart_product->get_id();
		}

		if($cart_product_type == 'subscription_variation') {
		  $cart_product_id = $cart_product->get_parent_id();
		  $cart_variation_id = $cart_product->get_id();
		  $subscription_switch = isset($cart_item['subscription_switch']) && !empty($cart_item['subscription_switch']) ? 1 : 0;
		  $upgraded_or_downgraded = isset($cart_item['subscription_switch']['upgraded_or_downgraded']) && !empty($cart_item['subscription_switch']['upgraded_or_downgraded']) ? $cart_item['subscription_switch']['upgraded_or_downgraded'] : '';
		}
	}

	//check if cart has a subscription
	if($has_subscription_product_type && is_user_logged_in()) {
	  //subscription level of cart subscription
	  $cart_subscription_level = msg_woocommerce_subscription_level( $cart_product_id, $cart_variation_id );
	  $old_has_subscription = 0;
	  $old_subscription_level = 0;

	  $subscriptions = wcs_get_users_subscriptions( $current_user_id );
	  foreach($subscriptions as $subscription) {
		if ($subscription->has_status(array('active'))) {
		  $old_has_subscription = 1;
		  $items = $subscription->get_items();
		  foreach($items as $item) {
			$old_variation_id = $item->get_variation_id();
			$old_product_id = $item->get_product_id();
			$old_subscription_level = msg_woocommerce_subscription_level( $old_product_id, $old_variation_id );
		  }
		}
	  }

      //check if the same subscription is active?
      if($old_has_subscription && $old_subscription_level == $cart_subscription_level) {
          // Sets error message.
          wc_add_notice( __( 'You already have the same active subscription!' ), 'error' );

          $valid = false;
    	  return $valid;
      }

	  //upgrade subscription check
	  if($old_has_subscription && $old_subscription_level > $cart_subscription_level) {
          // Sets error message.
          wc_add_notice( __( 'You can only upgrade your existing subscription!' ), 'error' );

          $valid = false;
    	  return $valid;
	  }
	  else if($old_has_subscription && $cart_subscription_level > $old_subscription_level) {
	    if($subscription_switch && $upgraded_or_downgraded == 'upgraded') {
	      //nothing to do, is done from my subscription page
	    }
	    else {
          // Sets error message.
          wc_add_notice('If you want to upgrade your subscription, please do it from your <a class="woo-status-message-link" href="/my-account/subscriptions/">my subscription</a> page.', 'error');

          $valid = false;
    	  return $valid;
	    }
	  }
	}

	//check if the cart has both subscription and course product
	if ( $has_simple_product_type && $has_subscription_product_type) {
	  if( $cart_variation_id ) {
	    $can_buy_course = user_can_buy_course( $current_user_id, $cart_variation_id);
	    if(!$can_buy_course) {
          // Sets error message.
          wc_add_notice( __( 'The subscription you selected, not allowing you to buy courses separately!' ), 'error');

          $valid = false;
    	  return $valid;
	    }
	  }
	}

	//check if the cart and new add-to-cart product is both subscription type
	if ( $has_simple_product_type && !$has_subscription_product_type && is_user_logged_in()) {
	  $can_buy_course = user_can_buy_course( $current_user_id);
	  if($has_active_membership && !$can_buy_course) {
        // Sets error message.
        wc_add_notice( __( 'You have an active membership, which not allowing you to buy courses separately!' ), 'error');

        $valid = false;
    	return $valid;
	  }

	  if($has_active_membership && $max_enroll_limit == 0) {
        // Sets error message.
        wc_add_notice( __( 'You have an active membership and you can enroll with any course directly!' ), 'error' );

        $valid = false;
    	return $valid;
	  }

	  foreach($cart_simple_products as $cart_simple_product_id) {
		$courses_id = get_post_meta( $cart_simple_product_id, '_related_course', true );

		//check if already enrolled to this course
		if(in_array($course_id, $enrolled_courses)) {
		  // Sets error message.
		  wc_add_notice( __( 'You have already enrolled with some of the courses.' ), 'error' );

		  $valid = false;
		  return $valid;
		}
	  }
	}

    return $valid;
}
add_action( 'woocommerce_check_cart_items', 'msg_woocommerce_cart_validate', 10, 0 );
