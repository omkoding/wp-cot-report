<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.omkoding.com/
 * @since      1.0.0
 *
 * @package    Cot_Report
 * @subpackage Cot_Report/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cot_Report
 * @subpackage Cot_Report/includes
 * @author     Om Koding <omkodingdotcom@gmail.com>
 */
class Cot_Report_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		self::setupDB();	
	}

	public static function setupDB()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . 'cot';

		$sql = "CREATE TABLE $table_name (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`date` date NOT NULL,
			`symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			`current_nc_long` decimal(8,0) NOT NULL,
			`current_nc_short` decimal(8,0) NOT NULL,
			`current_nc_spreads` decimal(8,0) NOT NULL,
			`current_c_long` decimal(8,0) NOT NULL,
			`current_c_short` decimal(8,0) NOT NULL,
			`changes_nc_long` decimal(8,0) NOT NULL,
			`changes_nc_short` decimal(8,0) NOT NULL,
			`changes_nc_spreads` decimal(8,0) NOT NULL,
			`changes_c_long` decimal(8,0) NOT NULL,
			`changes_c_short` decimal(8,0) NOT NULL,
			`oi_current` decimal(8,0) NOT NULL,
			`oi_change` decimal(8,0) NOT NULL,
			`oi_nc_long` decimal(8,1) NOT NULL,
			`oi_nc_short` decimal(8,1) NOT NULL,
			`oi_nc_spreads` decimal(8,1) NOT NULL,
			`oi_c_long` decimal(8,1) NOT NULL,
			`oi_c_short` decimal(8,1) NOT NULL,
			`created_at` timestamp NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `date_symbol` (`date`,`symbol`)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

}
