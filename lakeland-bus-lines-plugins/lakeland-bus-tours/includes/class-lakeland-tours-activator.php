<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    lakeland_bus_tours
 * @subpackage lakeland_bus_tours/includes
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Tours_Activator {

	public static $wp_contact_version;


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::$wp_contact_version  = '1.0';
		global $wpdb;
		$installed_ver = get_option( "wp_contact_version" );

		if ( $installed_ver != self::$wp_contact_version ) {

			$table_name = $wpdb->prefix . 'lakeland_contacts';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				date datetime DEFAULT CURRENT_TIMESTAMP,
				name varchar(55) DEFAULT '' NULL,
				email varchar(75) DEFAULT '' NULL,
				phone varchar(20) DEFAULT '' NULL,
				PRIMARY KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'wp_contact_version', self::$wp_contact_version );
		}
	}
}