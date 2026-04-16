<?php

/**
 * Booster
 *
 * @package     FourTheLocalSchool
 * @author      Shafiq Hossain
 * @copyright   2021 Shafiq Hossain
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: FourTheLocalSchools
 * Plugin URI:
 * Description: Various customization for the site
 * Version:     1.0.0
 * Author:      Shafiq Hossain
 * Author URI:
 * Text Domain: fourthelocalschools
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

// Plugin Defines
define( "WPS_FILE", __FILE__ );
define( "WPS_DIRECTORY", dirname(__FILE__) );
define( "WPS_TEXT_DOMAIN", dirname(__FILE__) );
define( "WPS_DIRECTORY_BASENAME", plugin_basename( WPS_FILE ) );
define( "WPS_DIRECTORY_PATH", plugin_dir_path( WPS_FILE ) );
define( "WPS_DIRECTORY_URL", plugins_url( null, WPS_FILE ) );

// Require the main class file
require_once( WPS_DIRECTORY . '/include/fourthelocalschools-main-class.php' );
