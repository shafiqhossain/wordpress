<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/shafiqhossain
 * @since      1.0.0
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Fac_Directory
 * @subpackage Fac_Directory/includes
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class Fac_Directory_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Nothing to do white deactivate.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        if (wp_next_scheduled ( 'fac_directory_membership_cron' )) {
            $timestamp = wp_next_scheduled('fac_directory_membership_cron');
            wp_unschedule_event($timestamp, 'fac_directory_membership_cron');
        }
	}

}
