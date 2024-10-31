<?php

/**
 * Fired during plugin activation
 *
 * @link       http://enigmatheme.club
 * @since      1.0.0
 *
 * @package    Puchi
 * @subpackage Puchi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Puchi
 * @subpackage Puchi/includes
 * @author     Bayu Idham Fathurachman <bayu_idham@yahoo.com>
 */
class Puchi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
                self::puchi_create_db();
	}
        
        private static function puchi_create_db(){
                global $wpdb;
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                
                $table_statistic = $wpdb->prefix . "puchi_statistic";
                if ($wpdb->get_var("show tables like '" . $table_statistic . "'") !== $table_statistic) {
                        $sql = "CREATE TABLE IF NOT EXISTS $table_statistic (
                                        id MEDIUMINT(20) NOT NULL AUTO_INCREMENT,
					split_id MEDIUMINT(20),
                                        page_id MEDIUMINT(20),
                                        content_title TINYTEXT,
					type VARCHAR(10),
                                        tracker TINYTEXT,
                                        weight TINYINT(5),
					city VARCHAR(255),
                                        country VARCHAR(255),
                                        ip_address VARCHAR(255),
                                        country_code VARCHAR(10),
                                        date DATETIME,
                                        
                                        UNIQUE KEY id (id)
                                );";
                        dbDelta($sql);
                }
        }
}
