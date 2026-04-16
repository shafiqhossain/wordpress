<?php
/**
 * MSG LMS Learndash Functions
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
 * Enroll courses
 */
add_action( 'wp_router_generate_routes', 'course_enrollment_add_routes', 20 );
function course_enrollment_add_routes( $router ) {
    $route_args = array(
		'path' => '^course/enroll',
		'query_vars' => array( ),
		'page_callback' => 'course_enrollment_route_callback',
		'page_arguments' => array('course_id'),
		'access_callback' => true,
		'title' => __( 'Course Enrollment' ),
		'template' => false
    );
    $router->add_route( 'course-enrollment', $route_args );
}

function course_enrollment_route_callback( ) {
    //print_r($_GET);
	$course_id = !empty( $_GET['course_id'] ) ? $_GET['course_id'] : '';
	if(empty($course_id)) return;

	//get current user
	$current_user_id = get_current_user_id();
	$membership = user_current_membership($current_user_id);

	//anonymous user not allowed to enroll
	if(empty($current_user_id)) return;

	$allow_enroll_this_course = 0;
	if( $membership ) {
	  $max_enroll_limit = user_membership_course_limit($membership->get_id());
	}
	else {
	  $max_enroll_limit = -1;
	}
	$is_enrolled_into_this_course = 0;

	//check if the course is free
	$is_course_free = course_is_course_free( $course_id, 'course'  );
	if($is_course_free) {
	  $allow_enroll_this_course = 1;
	}

	//if -1, return
	if(!$is_course_free && $max_enroll_limit == -1) return;

	// check membership
	if ( $membership != false ) {
		$enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
		$total_enrolled_courses = user_current_total_course_enrollments( $current_user_id );

		//check if already enrolled to this course
		if(in_array($course_id, $enrolled_courses)) {
		  $is_enrolled_into_this_course = 1;
		}
		else {
		  $is_enrolled_into_this_course = 0;
		}

		if($max_enroll_limit == 0 || $total_enrolled_courses < $max_enroll_limit) {
		  $allow_enroll_this_course = 1;
		}
	}

	$lock = new WP_Lock( "course_enrollment" );
    while (true) {
	  if ( $lock->acquire( WP_Lock::WRITE ) ) {

		// check if user is logged
		if( is_user_logged_in() && !$is_enrolled_into_this_course && $allow_enroll_this_course ) {
		  //get existing enrolled courses
		  $user_courses = learndash_user_get_enrolled_courses($current_user_id);
		  //add the new one
		  $user_courses[] = $course_id;
		  //save
		  learndash_user_set_enrolled_courses( $current_user_id, $user_courses);
		}

		//release the lock
		$lock->release();

		//exit the loop
		break;
	  }
	  else {
		sleep(2);
	  }
	}

	$url = get_post_permalink($course_id);
	wp_redirect( $url, 302 );
	exit;
}


/**
 * Download private presentations
 */
add_action( 'wp_router_generate_routes', 'course_presentation_download_add_routes', 20 );

function course_presentation_download_add_routes( $router ) {
    $route_args = array(
		'path' => '^download/course/presentation',
		'query_vars' => array( ),
		'page_callback' => 'course_presentation_download_route_callback',
		'page_arguments' => array('course_id', 'name'),
		'access_callback' => true,
		'title' => __( 'Course Presentation' ),
		'template' => false
    );
    $router->add_route( 'course-presentation-download', $route_args );
}

function course_presentation_download_route_callback( ) {
    //print_r($_GET);
	$course_id = !empty( $_GET['course_id'] ) ? $_GET['course_id'] : '';
	$name = !empty( $_GET['name'] ) ? $_GET['name'] : '';

	//get current user
	$user = wp_get_current_user();

	if(!empty($course_id) && !empty($name)) {
	  // check if user is logged
	  if( is_user_logged_in() && (in_array('administrator', $user->roles) || in_array('subscriber', $user->roles)) ) {
	    $file_name = $name;
	    print $file_name.'<br>';
	    $presentation_file = "{$_SERVER['DOCUMENT_ROOT']}/wp-content/uploads/course-presentations/{$file_name}";
	    $presentation_file_name = "{$file_name}";
	    print $presentation_file.'<br>';
	    if( file_exists( $presentation_file ) ) {
		  header( 'Cache-Control: public' );
		  header( 'Content-Description: File Transfer' );
		  header( "Content-Disposition: attachment; filename={$presentation_file_name}" );
		  header( 'Content-Type: application/octet-stream' );
		  header( 'Content-Transfer-Encoding: binary' );
		  readfile( $presentation_file );
		  exit;
	    }
	  }
	}

    return __( 'No file found!' );
}


/**
 * Check if the user has access to the course / product
 *
 * @param int $post_id ID of the Product / Course
 * @param int $user_id ID of the User
 */
function msg_has_enrolled_in_course( $post_id, $user_id, $type = 'course' ){
	if($type == 'product') {
	  $courses_id = get_post_meta( $post_id, '_related_course', true );
	}
	else {
	  $course_id = $post_id;
	}

	//get current user
	$current_user_id	= get_current_user_id();

    $access = false;
	$enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
	$total_enrolled_courses = user_current_total_course_enrollments( $current_user_id );

	//check if already enrolled to this course
	if(in_array($course_id, $enrolled_courses)) {
        $access = true;
    	return $access;
	}

	return false;
}


/**
 * Return the users current total course enrollments, excluding the free courses
 *
 * @param int $user_id ID of the user
 */
function user_current_total_course_enrollments( $user_id ){
  if(empty($user_id)) return 0;

  $total_enrolled_free_courses = 0;
  $enrolled_courses = learndash_get_user_courses_from_meta( $user_id );
  if(is_array($enrolled_courses) && count($enrolled_courses)) {
    foreach($enrolled_courses as $course_id) {
      $is_free = course_is_course_free( $course_id, 'course' );
      if($is_free) {
        ++$total_enrolled_free_courses;
      }
    }
  }

  $total_enrolled_courses = count($enrolled_courses);
  $total_enrolled_courses = $total_enrolled_courses - $total_enrolled_free_courses;

  return $total_enrolled_courses;
}

/**
 * Check if the current course is free
 *
 * @param int $course_id ID of the Course
 */
function course_is_course_free( $post_id, $type = 'course'  ) {
	if($type == 'product') {
	  $courses_id = get_post_meta( $post_id, '_related_course', true );
	}
	else {
	  $course_id = $post_id;
	}

    $is_free = false;

	//get course meta data
	$meta = get_post_meta( $course_id, '_sfwd-courses', true );

	if($meta['sfwd-courses_course_price_type'] == 'free') {
        $is_free = true;
    	return $is_free;
	}

	return $is_free;
}


/**
 * Return current user's membership info and
 * Available slots for enrollment
 */
add_action( 'wp_router_generate_routes', 'membership_available_slots_add_routes', 20 );
function membership_available_slots_add_routes( $router ) {
    $route_args = array(
		'path' => '^membership/available_slots',
		'query_vars' => array( ),
		'page_callback' => 'membership_available_slots_route_callback',
		'page_arguments' => array('course_id'),
		'access_callback' => true,
		'title' => __( 'Membership Available Slots' ),
		'template' => false
    );
    $router->add_route( 'membership-available-slots', $route_args );
}

function membership_available_slots_route_callback( ) {
    //print_r($_REQUEST);
    $data = array('status' => 0, 'message' => '');

	$course_id = !empty( $_REQUEST['course_id'] ) ? $_REQUEST['course_id'] : '';
	if(empty($course_id)) {
	  $data['status'] = 0;
	  $data['message'] = 'No course reference found!';
	  echo json_encode($data);
	  return;
	  //wp_die();
	}

	//get current user
	$current_user_id = get_current_user_id();
	$membership = user_current_membership($current_user_id);

	//anonymous user not allowed to enroll
	if(empty($current_user_id)) {
	  $data['status'] = 0;
	  $data['message'] = 'No user reference found!';
	  echo json_encode($data);
	  return;
	  //wp_die();
	}

	$max_enroll_limit = 0;
	$next_enroll_limit = 0;
	$membership_name = '';
	if( $membership ) {
	  $max_enroll_limit = user_membership_course_limit($membership->get_id());
      $membership_name = $membership->plan->post->post_title;
	}
	else {
	  $data['status'] = 0;
	  $data['message'] = 'No membership info found!';
	  echo json_encode($data);
	  return;
	  //wp_die();
	}

	//total enrollments
	$total_enrollments = user_current_total_course_enrollments( $current_user_id );
	if($max_enroll_limit != 0) {
	  $next_enroll_limit = $max_enroll_limit - $total_enrollments;
	}

	//check if already enrolled
	$enrolled_courses = learndash_get_user_courses_from_meta( $current_user_id );
	if(in_array($course_id, $enrolled_courses)) {
	  $data['status'] = 0;
	  $data['message'] = 'You have already enrolled to this course!';
	  echo json_encode($data);
	  return;
	  //wp_die();
	}

	//check if the course is free
	$is_course_free = course_is_course_free( $course_id, 'course'  );
	if($is_course_free) {
	  $data['status'] = 1;

	  if($max_enroll_limit == 0) {
	    $message = 'Your Membership Plan: <strong>'.$membership_name.' (All Courses)</strong><br>';
	    $message .= 'Slots Available: <strong>Unlimited</strong><br>';
		$message .= 'You can enroll <strong>any available</strong> courses.';
	  }
	  else {
	    $message = 'Your Membership Plan: <strong>'.$membership_name.' ('.$max_enroll_limit.' Courses + Free Courses)</strong><br>';
	    $message .= 'Slots Available: <strong>'.$max_enroll_limit.'</strong><br>';
		$message .= 'If you enroll to this course, you will have <strong>'.$next_enroll_limit.'</strong> slots left.';
	  }

	  $data['message'] = $message;
	  echo json_encode($data);
	  //wp_die();
	  return;
	}

	//check course access permission
	$has_access_to_course = msg_has_access_to_course( $course_id, $current_user_id, 'course' );
	if($has_access_to_course) {
	  $data['status'] = 1;

	  if($max_enroll_limit == 0) {
	    $message = 'Your Membership Plan: <strong>'.$membership_name.' (All Courses)</strong><br>';
	    $message .= 'Slots Available: <strong>Unlimited</strong><br>';
		$message .= 'You can enroll <strong>any available</strong> courses.';
	  }
	  else {
	    $message = 'Your Membership Plan: <strong>'.$membership_name.' ('.$max_enroll_limit.' Courses + Free Courses)</strong><br>';
	    $message .= 'Slots Available: <strong>'.$max_enroll_limit.'</strong><br>';
		$message .= 'If you enroll to this course, you will have <strong>'.($next_enroll_limit-1).'</strong> slots left.';
	  }

	  $data['message'] = $message;
	  echo json_encode($data);
	  //wp_die();
	  return;
	}

	//it should not reach here, fallback message
	$data['status'] = 0;
	$data['message'] = 'Unknown Error! Please contact with customer support.';
	echo json_encode($data);
	wp_die();
}

/**
  * Return total course enrollments
  */
function msg_course_total_enrollment( $course_id ) {
  global $wpdb;

  $meta_key = 'ld_sent_notification_enroll_course_'.$course_id;

  $sql  = 'SELECT count(*) as total ';
  $sql .= 'FROM wp_usermeta ';
  $sql .= "WHERE meta_key = '".$meta_key."' ";
  $sql .= "AND meta_value = 1 ";

  $results = $wpdb->get_results($sql);

  $total = 0;
  foreach($results as $row) {
	$total = $row->total;
  }
  if(empty($total)) $total = 0;

  return $total;
}


