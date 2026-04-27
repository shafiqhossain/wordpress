<?php
/**
 * Testimonial Block registration.
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Testimonial block.
 */
function smma_testimonial_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'init', 'smma_testimonial_block_init' );
