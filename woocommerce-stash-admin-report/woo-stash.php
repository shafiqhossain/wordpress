<?php

/**
 * Woocommerce Stash Customization
 *
 * @package     Stash
 * @author      Shafiq Hossain
 * @copyright   2021 Shafiq Hossain
 *
 * @wordpress-plugin
 * Plugin Name: Woocommerce Stash Customization
 * Plugin URI:
 * Description: A plugin for customization for Stash
 * Version:     1.0
 * Author:      Shafiq Hossain
 * Text Domain: woo-stash
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	return;


// Plugin Defines
define( "WOS_FILE", __FILE__ );
define( "WOS_DIRECTORY", dirname(__FILE__) );
define( "WOS_TEXT_DOMAIN", dirname(__FILE__) );
define( "WOS_DIRECTORY_BASENAME", plugin_basename( WOS_FILE ) );
define( "WOS_DIRECTORY_PATH", plugin_dir_path( WOS_FILE ) );
define( "WOS_DIRECTORY_URL", plugins_url( null, WOS_FILE ) );

// Require the main class file
require_once( WOS_DIRECTORY . '/include/class-woo-stash-main.php' );
