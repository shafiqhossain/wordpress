<?php
/**
 * Recent Projects Block registration.
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Recent Projects block.
 */
function smma_recent_projects_block_init() {
	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'smma_recent_projects_render_callback',
		)
	);
}
add_action( 'init', 'smma_recent_projects_block_init' );

/**
 * Renders the Recent Projects block on the front end.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 * @return string Rendered HTML.
 */
function smma_recent_projects_render_callback( $attributes, $content, $block ) {
	$args = array(
		'post_type'      => 'smma_project',
		'posts_per_page' => 5,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$projects = get_posts( $args );

	if ( empty( $projects ) ) {
		return '<div class="smma-recent-projects smma-recent-projects--empty"><p>' . esc_html__( 'No projects found.', 'smma-gutenberg-blocks' ) . '</p></div>';
	}

	ob_start();
	require plugin_dir_path( __FILE__ ) . 'template.php';
	return ob_get_clean();
}
