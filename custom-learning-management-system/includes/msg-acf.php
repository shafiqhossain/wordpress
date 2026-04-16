<?php
/**
 * MSG LMS ACF Functions
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
 * Allows Advanced Custom Fields field groups to be displayed on the Memberships
 * plan edit page
 *
 * @param string[] array of meta box IDs
 */
function msg_acf_wc_memberships_allow_acf_meta_box_ids( $allowed_meta_box_ids ) {

	if ( function_exists( 'acf_get_field_groups' ) ) {

		$field_groups = array_merge(
			acf_get_field_groups( array( 'post_type' => 'wc_membership_plan' ) ),
			acf_get_field_groups( array( 'post_type' => 'wc_user_membership' ) )
		);

		foreach ( $field_groups as $field_group ) {
			$allowed_meta_box_ids[] = 'acf-' . $field_group['key'];
		}
	}

	return $allowed_meta_box_ids;
}

add_filter( 'wc_memberships_allowed_meta_box_ids', 'msg_acf_wc_memberships_allow_acf_meta_box_ids' );
