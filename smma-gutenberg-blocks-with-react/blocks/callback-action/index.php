<?php
/**
 * Callback Action Block registration.
 *
 * @package smma-gutenberg-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Callback Action block.
 */
function smma_callback_action_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'init', 'smma_callback_action_block_init' );
