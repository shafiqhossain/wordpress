<?php
/**
 * MSG LMS BuddyPress Functions
 *
 * @author 	Shafiq Hossain
 * @category 	Core
 * @package 	MSG Lms/Functions
 * @version    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * My Friends
 */
add_action( 'wp_router_generate_routes', 'buddypress_my_friends_add_routes', 20 );
function buddypress_my_friends_add_routes( $router ) {
    $route_args = array(
		'path' => '^me/my-friends',
		'query_vars' => array( ),
		'page_callback' => 'buddypress_my_friends_route_callback',
		'page_arguments' => array(),
		'access_callback' => true,
		'title' => __( 'My Friends' ),
		'template' => false
    );
    $router->add_route( 'buddypress-my-friends', $route_args );
}

function buddypress_my_friends_route_callback( ) {
	if ( is_user_logged_in() || bp_is_active( 'friends' ) ) {
      $url = bp_loggedin_user_domain() . bp_get_friends_slug() . '/';
    }
    else {
	  $bp_pages = bp_core_get_directory_page_ids( 'all' );
	  $members_page_id = (isset($bp_pages['members']) ? $bp_pages['members'] : 0);
	  if($members_page_id) {
	    $url = get_post_permalink($members_page_id);
	  }
	  else {
        $url = '/members/';
      }
    }

	wp_redirect( $url, 302 );
	exit;
}


/**
 * My Groups
 */
add_action( 'wp_router_generate_routes', 'buddypress_my_groups_add_routes', 20 );
function buddypress_my_groups_add_routes( $router ) {
    $route_args = array(
		'path' => '^me/my-groups',
		'query_vars' => array( ),
		'page_callback' => 'buddypress_my_groups_route_callback',
		'page_arguments' => array(),
		'access_callback' => true,
		'title' => __( 'My Groups' ),
		'template' => false
    );
    $router->add_route( 'buddypress-my-groups', $route_args );
}

function buddypress_my_groups_route_callback( ) {
	if ( is_user_logged_in() || bp_is_active( 'groups' ) ) {
      $url = bp_loggedin_user_domain() . bp_get_groups_slug() . '/';
    }
    else {
	  $bp_pages = bp_core_get_directory_page_ids( 'all' );
	  $groups_page_id = (isset($bp_pages['groups']) ? $bp_pages['groups'] : 0);
	  if($groups_page_id) {
	    $url = get_post_permalink($groups_page_id);
	  }
	  else {
        $url = '/groups/';
      }
    }

	wp_redirect( $url, 302 );
	exit;
}
