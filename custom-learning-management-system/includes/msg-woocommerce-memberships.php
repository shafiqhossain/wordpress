<?php
/**
 * MSG LMS Memberships Functions
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
 * @param array $tabs contains all WooCommerce Membership plan tabs data
 */
function msg_membership_course_tabs( $tabs ) {

	$tabs['ld_course'] = array(
		'label'  => 'LSM Settings',
		'target' => 'ld_course_settings' /* ID of the HTML tab body element */
	);
	return $tabs;
}

add_filter( 'wc_membership_plan_data_tabs', 'msg_membership_course_tabs' ); /* add tabs */


function msg_membership_course_tab_content(){
	global $post;

	$tabbody = '<div id="ld_course_settings" class="panel woocommerce_options_panel">
		<div class="table-wrap">
		<p>Please, enter the course related settings to synchronize active users of this plan with it.</p>
		<div class="options_group">';

	wp_nonce_field( basename( __FILE__ ), 'ld_course_nonce' );
	$ld_max_course_limit = get_post_meta( $post->ID, 'ld_max_course_limit', true );
	if(empty($ld_max_course_limit)) $ld_max_course_limit = 0;

	$ld_max_corporate_course_limit = get_post_meta( $post->ID, 'ld_max_corporate_course_limit', true );
	if(empty($ld_max_corporate_course_limit)) $ld_max_corporate_course_limit = 0;

	$ld_membership_level = get_post_meta( $post->ID, 'ld_membership_level', true );
	if(empty($ld_membership_level)) $ld_membership_level = 0;

	$ld_download_lessons = get_post_meta( $post->ID, 'ld_download_lessons', true );
	if(empty($ld_download_lessons)) $ld_download_lessons = 0;
	if($ld_download_lessons) {
	  $ld_download_lessons_yes = "checked='checked'";
	  $ld_download_lessons_no = "";
	}
	else {
	  $ld_download_lessons_yes = "";
	  $ld_download_lessons_no = "checked='checked'";
	}

	$ld_watch_video = get_post_meta( $post->ID, 'ld_watch_video', true );
	if(empty($ld_watch_video)) $ld_watch_video = 0;
	if($ld_watch_video) {
	  $ld_watch_video_yes = "checked='checked'";
	  $ld_watch_video_no = "";
	}
	else {
	  $ld_watch_video_yes = "";
	  $ld_watch_video_no = "checked='checked'";
	}

	$ld_membership_buy_course = get_post_meta( $post->ID, 'ld_membership_buy_course', true );
	if(empty($ld_membership_buy_course)) $ld_membership_buy_course = 0;
	if($ld_membership_buy_course) {
	  $ld_membership_buy_course_yes = "checked='checked'";
	  $ld_membership_buy_course_no = "";
	}
	else {
	  $ld_membership_buy_course_yes = "";
	  $ld_membership_buy_course_no = "checked='checked'";
	}

	$tabbody .= '
	  <p class="form-field post_name_field ">
		<label for="ld_max_course_limit">Max Course Limit (Individual):</label>
		<input type="number" name="ld_max_course_limit" id="ld_max_course_limit" value="' . esc_attr( $ld_max_course_limit ) . '" min="0" step="any" required="required" />
		<p>Please enter the max course limit for individual for this membership plan.</p>
	  </p>
	  <p class="form-field post_name_field ">
		<label for="ld_max_course_limit">Max Course Limit (Corporate):</label>
		<input type="number" name="ld_max_corporate_course_limit" id="ld_max_corporate_course_limit" value="' . esc_attr( $ld_max_corporate_course_limit ) . '" min="0" step="any" required="required" />
		<p>Please enter the max course limit for corporate for this membership plan.</p>
	  </p>
	  <hr>
	  <p class="form-field post_name_field ">
		<label for="ld_membership_level">Membership Level:</label>
		<input type="number" name="ld_membership_level" id="ld_membership_level" value="' . esc_attr( $ld_membership_level ) . '" min="0" step="any" required="required" />
		<p>Please enter the membership level. If user has more then one membership granted, this will be used to determine the higher membership plan. 0 means lowest and higer number means higher mebership level.</p>
	  </p>
	  <hr>
	  <p class="form-field ld_download_lessons_field">
		<label for="ld_download_lessons">Download Lessons:</label>
		<span class="ld-download-lessons-selectors">
			<label class="label-radio">
			  <input type="radio" name="ld_download_lessons" class="js-ld-download-lessons-selector js-ld-download-lessons" value="1" '.$ld_download_lessons_yes.'> Yes
			</label>
			<label class="label-radio">
			  <input type="radio" name="ld_download_lessons" class="js-ld-download-lessons-selector js-ld-download-lessons" value="0" '.$ld_download_lessons_no.'> No
			</label>
		</span>
	  </p>
	  <hr>
	  <p class="form-field ld_watch_video_field">
		<label for="ld_watch_video">Watch Video:</label>
		<span class="ld-watch-video-selectors">
			<label class="label-radio">
			  <input type="radio" name="ld_watch_video" class="js-ld-watch-video-selector js-ld-watch-video" value="1" '.$ld_watch_video_yes.'> Yes
			</label>
			<label class="label-radio">
			  <input type="radio" name="ld_watch_video" class="js-ld-watch-video-selector js-ld-watch-video" value="0" '.$ld_watch_video_no.'> No
			</label>
		</span>
	  </p>
	  <hr>
	  <p class="form-field ld-membership-buy-course-field">
		<label for="ld_membership_buy_course">Allow Buy Courses Separately:</label>
		<span class="ld-membership-buy-course-selectors">
			<label class="label-radio">
			  <input type="radio" name="ld_membership_buy_course" class="js-ld-membership-buy-course-selector js-ld-membership-buy-course" value="1" '.$ld_membership_buy_course_yes.'> Yes
			</label>
			<label class="label-radio">
			  <input type="radio" name="ld_membership_buy_course" class="js-ld-membership-buy-course-selector js-ld-membership-buy-course" value="0" '.$ld_membership_buy_course_no.'> No
			</label>
		</span>
	  </p>
	</div><!--.option_group-->
  </div><!--.table-wrap-->
</div><!--#ld_course_settings-->';

	echo $tabbody;
}

add_action( 'wc_membership_plan_data_panels', 'msg_membership_course_tab_content' ); /* add tab content */


/**
 * @param int $post_id ID of the membership plan
 */
function msg_membership_course_settings_save( $post_id ){
	if ( !isset( $_POST['ld_course_nonce'] ) || !wp_verify_nonce( $_POST['ld_course_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	$post = get_post( $post_id );

	/* save data only for membership plan custom post type */
	if ( $post->post_type == 'wc_membership_plan' ) {
	    $ld_max_course_limit = sanitize_text_field( $_POST['ld_max_course_limit'] );
	    if(empty($ld_max_course_limit)) $ld_max_course_limit = 0;

	    $ld_max_corporate_course_limit = sanitize_text_field( $_POST['ld_max_corporate_course_limit'] );
	    if(empty($ld_max_corporate_course_limit)) $ld_max_corporate_course_limit = 0;

	    $ld_membership_level = sanitize_text_field( $_POST['ld_membership_level'] );
	    if(empty($ld_membership_level)) $ld_membership_level = 0;

	    $ld_download_lessons = sanitize_text_field( $_POST['ld_download_lessons'] );
	    if(empty($ld_download_lessons)) $ld_download_lessons = 0;

	    $ld_watch_video = sanitize_text_field( $_POST['ld_watch_video'] );
	    if(empty($ld_watch_video)) $ld_watch_video = 0;

	    $ld_membership_buy_course = sanitize_text_field( $_POST['ld_membership_buy_course'] );
	    if(empty($ld_membership_buy_course)) $ld_membership_buy_course = 0;

		update_post_meta( $post_id, 'ld_max_course_limit', $ld_max_course_limit );
		update_post_meta( $post_id, 'ld_max_corporate_course_limit', $ld_max_corporate_course_limit );
		update_post_meta( $post_id, 'ld_membership_level', $ld_membership_level );
		update_post_meta( $post_id, 'ld_download_lessons', $ld_download_lessons );
		update_post_meta( $post_id, 'ld_watch_video', $ld_watch_video );
		update_post_meta( $post_id, 'ld_membership_buy_course', $ld_membership_buy_course );
	}


	return $post_id;
}

add_action( 'save_post', 'msg_membership_course_settings_save' );


/**
 * Save data only for user membership plan custom post type
*/
function msg_membership_wc_memberships_user_membership_created( $membership_plan, $args = array()) {
    $ld_max_course_limit = get_post_meta( $membership_plan->get_id(), 'ld_max_course_limit', true );
    if(empty($ld_max_course_limit)) $ld_max_course_limit = 0;

    $ld_max_corporate_course_limit = get_post_meta( $membership_plan->get_id(), 'ld_max_corporate_course_limit', true );
    if(empty($ld_max_corporate_course_limit)) $ld_max_corporate_course_limit = 0;

	$user_membership_download_lessons = get_post_meta( $membership_plan->get_id(), 'ld_download_lessons', true );
    if(empty($user_membership_download_lessons)) $user_membership_download_lessons = 0;

	$user_membership_watch_video = get_post_meta( $membership_plan->get_id(), 'ld_membership_watch_video', true );
    if(empty($user_membership_watch_video)) $user_membership_watch_video = 0;

	$user_membership_buy_course = get_post_meta( $membership_plan->get_id(), 'ld_membership_buy_course', true );
    if(empty($user_membership_buy_course)) $user_membership_buy_course = 0;

	$user_membership_id = isset($args['user_membership_id']) ? $args['user_membership_id'] : 0;
	if($user_membership_id) {
	  update_post_meta( $user_membership_id, 'user_membership_course_limit', $ld_max_course_limit );
	  update_post_meta( $user_membership_id, 'user_membership_corporate_course_limit', $ld_max_corporate_course_limit );
	  update_post_meta( $user_membership_id, 'user_membership_download_lessons', $user_membership_download_lessons );
	  update_post_meta( $user_membership_id, 'user_membership_watch_video', $user_membership_watch_video );
	  update_post_meta( $user_membership_id, 'user_membership_buy_course', $user_membership_buy_course );
	}
}
add_action( 'wc_memberships_user_membership_created', 'msg_membership_wc_memberships_user_membership_created', 10, 8);


/**
 * The the highest membership user have
 *
 * @param int $user_id ID of the user
 */
function user_current_membership($user_id){
  if(empty($user_id)) return false;

  $current_membership = false;
  $current_membership_level = 0;

  $memberships = wc_memberships_get_user_active_memberships( $user_id );
  foreach($memberships as $membership) {
    $plan = $membership->get_plan();
    if($current_membership == false) $current_membership = $membership;

    if($plan != false) {
	  $membership_level = get_membership_plan_level($plan->get_id());
	  if(empty($membership_level)) $membership_level = 0;

	  if($membership_level > $current_membership_level) {
	    $current_membership = $membership;
	    $current_membership_level = $membership_level;
	  }
    }
  }

  return $current_membership;
}

/**
 * @param int $membership_plan name of the membership plan
 */
function get_membership_plan_level($membership_plan_id){
  if(empty($membership_plan_id)) return 0;

  $post = get_post($membership_plan_id);

  if ( $post ) {
    $ld_membership_level = get_post_meta( $post->ID, 'ld_membership_level', true );
    if(empty($ld_membership_level)) $ld_membership_level = 0;

    return $ld_membership_level;
  }

  return 0;
}


/**
 * The the highest membership plan user have
 *
 * @param int $user_id ID of the user
 */
function user_current_membership_plan($user_id){
  if(empty($user_id)) return false;

  $current_plan = false;
  $current_membership_level = 0;

  $memberships = wc_memberships_get_user_active_memberships( $user_id );
  foreach($memberships as $membership) {
    $plan = $membership->get_plan();
    if($current_plan == false) $current_plan = $plan;

    if($plan != false) {
	  $membership_level = get_membership_plan_level($plan->get_id());
	  if(empty($membership_level)) $membership_level = 0;

	  if($membership_level > $current_membership_level) {
	    $current_plan = $plan;
	    $current_membership_level = $membership_level;
	  }
    }
  }

  return $current_plan;
}
