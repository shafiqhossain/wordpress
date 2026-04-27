<?php
/**
 * Subscribe Now Block registration.
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Subscribe Now block.
 */
function smma_subscribe_now_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'init', 'smma_subscribe_now_block_init' );
