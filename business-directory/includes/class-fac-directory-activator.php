<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/shafiqhossain
 * @since      1.0.0
 *
 * @package    Fac_Directory
 * @subpackage Fac_Directory/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fac_Directory
 * @subpackage Fac_Directory/includes
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class Fac_Directory_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Nothing to do while activate.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if (! wp_next_scheduled ( 'fac_directory_membership_cron' )) {
            wp_schedule_event(time(), 'every_six_hours', 'fac_directory_membership_cron');
        }
	}

}
