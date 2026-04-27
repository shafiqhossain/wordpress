<?php
/**
 * Plugin Name: SMMA Gutenberg Blocks
 * Plugin URI:  https://www.isoftbd.com
 * Description: Custom Gutenberg blocks for SMMA including Recent Projects, Callback Action, Testimonials, Subscribe Now, Newsletter Subscription, and Dashboard Stats blocks.
 * Version:     1.1.0
 * Author:      Shafiq Hossain
 * Author URI:  https://www.isoftbd.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smma-gutenberg-blocks
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom block category for SMMA blocks.
 *
 * @param array $categories Existing block categories.
 * @return array Modified block categories.
 */
function smma_gutenberg_blocks_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'smma-blocks',
				'title' => __( 'SMMA Blocks', 'smma-gutenberg-blocks' ),
				'icon'  => 'screenoptions',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'smma_gutenberg_blocks_block_categories', 10, 2 );

/**
 * Load plugin text domain.
 */
function smma_gutenberg_blocks_load_textdomain() {
	load_plugin_textdomain(
		'smma-gutenberg-blocks',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'init', 'smma_gutenberg_blocks_load_textdomain' );

/**
 * Register the Project custom post type.
 */
function smma_register_project_post_type() {
	$labels = array(
		'name'               => __( 'Projects', 'smma-gutenberg-blocks' ),
		'singular_name'      => __( 'Project', 'smma-gutenberg-blocks' ),
		'menu_name'          => __( 'Projects', 'smma-gutenberg-blocks' ),
		'add_new'            => __( 'Add New', 'smma-gutenberg-blocks' ),
		'add_new_item'       => __( 'Add New Project', 'smma-gutenberg-blocks' ),
		'edit_item'          => __( 'Edit Project', 'smma-gutenberg-blocks' ),
		'new_item'           => __( 'New Project', 'smma-gutenberg-blocks' ),
		'view_item'          => __( 'View Project', 'smma-gutenberg-blocks' ),
		'search_items'       => __( 'Search Projects', 'smma-gutenberg-blocks' ),
		'not_found'          => __( 'No projects found', 'smma-gutenberg-blocks' ),
		'not_found_in_trash' => __( 'No projects found in Trash', 'smma-gutenberg-blocks' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'projects' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-portfolio',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'smma_project', $args );
}
add_action( 'init', 'smma_register_project_post_type' );

/**
 * Register custom meta fields for projects.
 */
function smma_register_project_meta() {
	register_post_meta(
		'smma_project',
		'smma_project_start_date',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);

	register_post_meta(
		'smma_project',
		'smma_project_end_date',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);

	register_post_meta(
		'smma_project',
		'smma_project_short_description',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);
}
add_action( 'init', 'smma_register_project_meta' );

/**
 * Register REST API endpoint for recent projects.
 */
function smma_register_recent_projects_endpoint() {
	register_rest_route(
		'smma/v1',
		'/recent-projects',
		array(
			'methods'             => 'GET',
			'callback'            => 'smma_get_recent_projects',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'smma_register_recent_projects_endpoint' );

/**
 * Callback for recent projects REST endpoint.
 *
 * @return WP_REST_Response
 */
function smma_get_recent_projects() {
	$args = array(
		'post_type'      => 'smma_project',
		'posts_per_page' => 5,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$projects = get_posts( $args );
	$data     = array();

	foreach ( $projects as $project ) {
		$data[] = array(
			'id'                => $project->ID,
			'title'             => get_the_title( $project ),
			'short_description' => get_post_meta( $project->ID, 'smma_project_short_description', true ),
			'start_date'        => get_post_meta( $project->ID, 'smma_project_start_date', true ),
			'end_date'          => get_post_meta( $project->ID, 'smma_project_end_date', true ),
			'link'              => get_permalink( $project ),
		);
	}

	return new WP_REST_Response( $data, 200 );
}

/**
 * Create the newsletter subscribers table on plugin activation.
 */
function smma_create_newsletter_table() {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'smma_newsletter_subscribers';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
		id         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		first_name VARCHAR(100)        NOT NULL DEFAULT '',
		last_name  VARCHAR(100)        NOT NULL DEFAULT '',
		email      VARCHAR(200)        NOT NULL DEFAULT '',
		created_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'smma_newsletter_db_version', '1.0' );
}
register_activation_hook( __FILE__, 'smma_create_newsletter_table' );

/**
 * Register REST API endpoints for newsletter and dashboard.
 */
function smma_register_rest_endpoints() {
	// Newsletter subscribe endpoint.
	register_rest_route(
		'smma/v1',
		'/newsletter-subscribe',
		array(
			'methods'             => 'POST',
			'callback'            => 'smma_newsletter_subscribe',
			'permission_callback' => '__return_true',
			'args'                => array(
				'first_name' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
				'last_name'  => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
				'email'      => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => 'is_email',
				),
			),
		)
	);

	// Dashboard stats endpoint.
	register_rest_route(
		'smma/v1',
		'/dashboard-stats',
		array(
			'methods'             => 'GET',
			'callback'            => 'smma_get_dashboard_stats',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'rest_api_init', 'smma_register_rest_endpoints' );

/**
 * Handle newsletter subscription submission.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function smma_newsletter_subscribe( WP_REST_Request $request ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'smma_newsletter_subscribers';
	$first_name = $request->get_param( 'first_name' );
	$last_name  = $request->get_param( 'last_name' );
	$email      = $request->get_param( 'email' );

	// Check for duplicate email.
	$existing = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$table_name} WHERE email = %s",
			$email
		)
	);

	if ( $existing ) {
		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'This email address is already subscribed.', 'smma-gutenberg-blocks' ),
			),
			409
		);
	}

	$inserted = $wpdb->insert(
		$table_name,
		array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'email'      => $email,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Could not save your subscription. Please try again.', 'smma-gutenberg-blocks' ),
			),
			500
		);
	}

	return new WP_REST_Response(
		array(
			'success' => true,
			'message' => __( 'Thank you for subscribing!', 'smma-gutenberg-blocks' ),
		),
		200
	);
}

/**
 * Return dashboard stats: projects, active subscribers, WooCommerce products.
 *
 * @return WP_REST_Response
 */
function smma_get_dashboard_stats() {
	global $wpdb;

	// Total published projects.
	$total_projects = (int) wp_count_posts( 'smma_project' )->publish;

	// Total newsletter subscribers.
	$table_name        = $wpdb->prefix . 'smma_newsletter_subscribers';
	$total_subscribers = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );

	// Active, non-expired subscriber-role users.
	// Checks for users with the 'subscriber' role whose
	// smma_subscription_status meta is 'active' and
	// smma_subscription_expiry is either empty or in the future.
	$today = current_time( 'Y-m-d' );
	$active_subscribers = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT u.ID)
			 FROM {$wpdb->users} u
			 INNER JOIN {$wpdb->usermeta} um_role
			         ON u.ID = um_role.user_id
			         AND um_role.meta_key = '{$wpdb->prefix}capabilities'
			         AND um_role.meta_value LIKE %s
			 INNER JOIN {$wpdb->usermeta} um_status
			         ON u.ID = um_status.user_id
			         AND um_status.meta_key = 'smma_subscription_status'
			         AND um_status.meta_value = 'active'
			 LEFT JOIN {$wpdb->usermeta} um_expiry
			         ON u.ID = um_expiry.user_id
			         AND um_expiry.meta_key = 'smma_subscription_expiry'
			 WHERE ( um_expiry.meta_value IS NULL
			         OR um_expiry.meta_value = ''
			         OR um_expiry.meta_value >= %s )",
			'%subscriber%',
			$today
		)
	);

	// WooCommerce products (requires WooCommerce to be active).
	$total_products = 0;
	if ( post_type_exists( 'product' ) ) {
		$total_products = (int) wp_count_posts( 'product' )->publish;
	}

	return new WP_REST_Response(
		array(
			'total_projects'        => $total_projects,
			'total_subscribers'     => $active_subscribers,
			'newsletter_subscribers'=> $total_subscribers,
			'total_products'        => $total_products,
			'woocommerce_active'    => post_type_exists( 'product' ),
		),
		200
	);
}

// Include block registration files.
require plugin_dir_path( __FILE__ ) . 'blocks/recent-projects/index.php';
require plugin_dir_path( __FILE__ ) . 'blocks/callback-action/index.php';
require plugin_dir_path( __FILE__ ) . 'blocks/testimonial/index.php';
require plugin_dir_path( __FILE__ ) . 'blocks/subscribe-now/index.php';
require plugin_dir_path( __FILE__ ) . 'blocks/newsletter-subscription/index.php';
require plugin_dir_path( __FILE__ ) . 'blocks/dashboard-stats/index.php';
