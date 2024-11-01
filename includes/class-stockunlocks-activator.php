<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.5.0
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks_Activator {

	/**
	 * Code that is run at plugin activation.
	 *
	 * Cron schedules are being set up as well as custom tables.
	 *
	 * @since    1.5.0
	 */
	public static function activate() {

		require_once(  WP_PLUGIN_DIR . '/stockunlocks/class-suwp-license-manager-client.php' );
		
		// make sure cleanup takes place
		if ( Stock_Unlocks_Activator::suwp_cleanup_apiprovider_activation() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_cleanup_apiprovider_activation <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_cleanup_apiprovider_activation <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		// setup custom database provider_mepname_table
		if ( Stock_Unlocks_Activator::suwp_create_plugin_provider_mepname_table() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_create_plugin_provider_mepname_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_create_plugin_provider_mepname_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		// setup custom database service_brand_table
		if ( Stock_Unlocks_Activator::suwp_create_plugin_service_brand_table() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_create_plugin_service_brand_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_create_plugin_service_brand_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		// setup custom database service_model_table
		if ( Stock_Unlocks_Activator::suwp_create_plugin_service_model_table() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_create_plugin_service_model_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_create_plugin_service_model_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		// setup custom database network_country_table
		if ( Stock_Unlocks_Activator::suwp_create_plugin_network_country_table() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_create_plugin_network_country_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_create_plugin_network_country_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		// setup custom database network_table
		if ( Stock_Unlocks_Activator::suwp_create_plugin_network_table() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_create_plugin_network_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_create_plugin_network_table <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		// setup custom database rewardstable
		if ( Stock_Unlocks_Activator::suwp_create_plugin_rewardstable() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_create_plugin_rewardstable <<<<<<<<<<<<<<<<<<<<<<<<<<');
        } else {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> UNDEFINED: suwp_create_plugin_rewardstable <<<<<<<<<<<<<<<<<<<<<<<<<<');
		}
		
		$suwp_license_email = get_option( 'suwp_license_email' );
		$suwp_license_key = get_option( 'suwp_license_key' );
		
		$product_id = 'stockunlocks-plugin';
		$product_name = 'StockUnlocks Plugin';
		
		if ( !isset( $suwp_license_email ) || !isset( $suwp_license_key ) ) {
			
			error_log('ACTIVATOR >>>>>>>>>>>>>> email/key NULL <<<<<<<<<<<<<<');
			update_option( 'suwp_license_email', SUWP_LICENSE_EMAIL_BASIC );
			update_option( 'suwp_license_key', SUWP_LICENSE_KEY_BASIC );
			$product_id = 'stockunlocks-plugin';
			$product_name = 'StockUnlocks Plugin';
		}
			
		if ( !$suwp_license_key == NULL) {
			
			if ( !( $suwp_license_key == SUWP_LICENSE_KEY_BASIC ) ) {
				
				$product_id = 'stockunlocks-plugin-pro';
				$product_name = 'StockUnlocks Pro';
			}
		}
		
		$license_manager = new Suwp_License_Manager_Client(
			$product_id,
			$product_name,
			'stockunlocks-plugin-text',
			SUWP_SOURCE_MANAGER,
			'plugin',
			SUWP_PATH . 'stockunlocks.php'
		);
		
		$extract = $license_manager->get_license_info();
	
		if (is_object($extract)) {
			
			if(isset($extract->error)) {
				
				error_log('ACTIVATOR >>>>>>>>>>>>>> $license_info: ERROR <<<<<<<<<<<<<< ' . $extract->error);		
			} else {
				
				error_log('ACTIVATOR >>>>>>>>>>>>>> $license_info: SUCCESS <<<<<<<<<<<<<< ' );
					
				update_option( 'suwp_author_info', $extract );
				update_option( 'suwp_author_value', $extract->author );
			}
			
		} else {
			
			error_log('ACTIVATOR >>>>>>>>>>>>>> $license_info: NOT AN OBJECT <<<<<<<<<<<<<<');
		}
		
		
		// make sure the cron schedules are ready
		if ( Stock_Unlocks_Activator::suwp_verify_cron_schedule() ) {
			error_log('>>>>>>>>>>>>>>>>>>>>>>> DEFINED: suwp_verify_cron_schedule <<<<<<<<<<<<<<<<<<<<<<<<<<');
        }
	}
	
	// cleaning up suwp_apisource on activation
	private static function suwp_cleanup_apiprovider_activation() {
		
		error_log('>>>>>>>>>>>>>>>>>>>>>>> RUNNING: suwp_cleanup_apiprovider_activation <<<<<<<<<<<<<<<<<<<<<<<<<<');
		
		global $wpdb;
		$old_post_types = array('suwp_apiprovider' => 'suwp_apisource');
		foreach ($old_post_types as $old_type=>$type) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = REPLACE(post_type, %s, %s) 
								 WHERE post_type LIKE %s", $old_type, $type, $old_type ) );
			
		}
		
		return true;
	}
	
	// ensure that the cron task is not already scheduled
	private static function suwp_verify_cron_schedule() {
		
		error_log('>>>>>>>>>>>>>>>>>>>>>>> RUNNING: suwp_verify_cron_schedule <<<<<<<<<<<<<<<<<<<<<<<<<<');
		
		if( !wp_next_scheduled( 'suwp_cron_hook_2minutes' ) ) {
			wp_schedule_event( time(), 'suwp_2minutes', 'suwp_cron_hook_2minutes' );
		}
		
		if( !wp_next_scheduled( 'suwp_cron_hook_5minutes' ) ) {
			wp_schedule_event( time(), 'suwp_5minutes', 'suwp_cron_hook_5minutes' );
		}
		
		if( !wp_next_scheduled( 'suwp_cron_hook_15minutes' ) ) {
			wp_schedule_event( time(), 'suwp_15minutes', 'suwp_cron_hook_15minutes' );
		}
		
		if( !wp_next_scheduled( 'suwp_cron_hook_30minutes' ) ) {
			wp_schedule_event( time(), 'suwp_30minutes', 'suwp_cron_hook_30minutes' );
		}
		
		if( !wp_next_scheduled( 'suwp_cron_hook_1hour' ) ) {
			wp_schedule_event( time(), 'hourly', 'suwp_cron_hook_1hour' );
		}
		
		if( !wp_next_scheduled( 'suwp_cron_hook_3hours' ) ) {
			wp_schedule_event( time(), 'suwp_3hours', 'suwp_cron_hook_3hours' );
		}
		
		if( !wp_next_scheduled( 'suwp_product_hook_1hour' ) ) {
			wp_schedule_event( time(), 'hourly', 'suwp_product_hook_1hour' );
		}
		
		if( !wp_next_scheduled( 'suwp_product_hook_2hours' ) ) {
			wp_schedule_event( time(), 'suwp_2hours', 'suwp_product_hook_2hours' );
		}
		
		if( !wp_next_scheduled( 'suwp_product_hook_3hours' ) ) {
			wp_schedule_event( time(), 'suwp_3hours', 'suwp_product_hook_3hours' );
		}
		
		if( !wp_next_scheduled( 'suwp_product_hook_4hours' ) ) {
			wp_schedule_event( time(), 'suwp_4hours', 'suwp_product_hook_4hours' );
		}
		
		if( !wp_next_scheduled( 'suwp_product_hook_5hours' ) ) {
			wp_schedule_event( time(), 'suwp_5hours', 'suwp_product_hook_5hours' );
		}
		
		if( !wp_next_scheduled( 'suwp_product_hook_6hours' ) ) {
			wp_schedule_event( time(), 'suwp_6hours', 'suwp_product_hook_6hours' );
		}
	
		// return true
		$return_value = true;
	}
	
	// creates custom rewardstable for the plugin
	private static function suwp_create_plugin_rewardstable() {
		
		global $wpdb;
		
		// setup return value
		$return_value = false;
		
		try {
			
			$table_name = $wpdb->prefix . "suwp_reward_links";
			$charset_collate = $wpdb->get_charset_collate();
			
			// sql for our table creation
			$sql = "CREATE TABLE $table_name (
				id mediumint(11) NOT NULL AUTO_INCREMENT,
				uid varchar(128) NOT NULL,
				product_id varchar(128) NOT NULL,
				subscriber_id mediumint(11) NOT NULL,
				list_id mediumint(11) NOT NULL,
				attachment_id mediumint(11) NOT NULL,
				downloads mediumint(11) DEFAULT 0 NOT NULL ,
				UNIQUE KEY id (id)
				) $charset_collate;";
			
			// make sure we include wordpress functions for dbDelta	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
			// dbDelta will create a new table if none exists or update an existing one
			dbDelta($sql);
			
			// return true
			$return_value = true;
		
		} catch( Exception $e ) {
			
			error_log('>>>>>>>>>>>>>>>>>>>>>>> ERRROR: suwp_create_plugin_rewardstable <<<<<<<<<<<<<<<<<<<<<<<<<< >>' . print_r( $e,true ) );
		
		}
		
		// return result
		return $return_value;
	}

	// creates custom suwp_provider_mepname table for the plugin
	private static function suwp_create_plugin_provider_mepname_table() {
		
		global $wpdb;
		
		// setup return value
		$return_value = false;
		
		try {
			
			$sql = '';
			$table_name = $wpdb->prefix . "suwp_provider_mepname";
			$charset_collate = $wpdb->get_charset_collate();
			
			// sql for our table creation
			$sql = "CREATE TABLE $table_name (
			   ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			   source_id bigint(20) unsigned NOT NULL,
			   post_id bigint(20) unsigned NOT NULL,
			   name longtext,
			   image_link longtext,
			   UNIQUE KEY ID (ID)
			   ) $charset_collate;";
			
			// make sure we include wordpress functions for dbDelta	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			   
			// dbDelta will create a new table if none exists or update an existing one
			dbDelta($sql);

			// return true
			$return_value = true;
		
		} catch( Exception $e ) {
			
			error_log('>>>>>>>>>>>>>>>>>>>>>>> ERRROR: suwp_create_plugin_provider_mepname_table <<<<<<<<<<<<<<<<<<<<<<<<<< >>' . print_r( $e,true ) );
		
		}
		
		// return result
		return $return_value;
	}
	
	// creates custom suwp_service_brand table for the plugin
	private static function suwp_create_plugin_service_brand_table() {
		
		global $wpdb;
		
		// setup return value
		$return_value = false;
		
		try {
			
			$sql = '';
			$table_name = $wpdb->prefix . "suwp_service_brand";
			$posts_table = $wpdb->prefix . "posts";
			$charset_collate = $wpdb->get_charset_collate();
			
			// sql for our table creation
			$sql = "CREATE TABLE $table_name (
			   ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			   source_id bigint(20) unsigned NOT NULL,
			   provider_id bigint(20) unsigned NOT NULL,
			   product_id bigint(20) unsigned NOT NULL,
			   name longtext,
			   image_link longtext,
			   UNIQUE KEY ID (ID)
			   ) $charset_collate;";
			
			// make sure we include wordpress functions for dbDelta	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			   
			// dbDelta will create a new table if none exists or update an existing one
			dbDelta($sql);

			// return true
			$return_value = true;
		
		} catch( Exception $e ) {
			
			error_log('>>>>>>>>>>>>>>>>>>>>>>> ERRROR: suwp_create_plugin_service_brand_table <<<<<<<<<<<<<<<<<<<<<<<<<< >>' . print_r( $e,true ) );
		
		}
		
		// return result
		return $return_value;
	}
	
	// creates custom suwp_service_model table for the plugin
	private static function suwp_create_plugin_service_model_table() {
		
		global $wpdb;
		
		// setup return value
		$return_value = false;
		
		try {
			
			$sql = '';
			$table_name = $wpdb->prefix . "suwp_service_model";
			$posts_table = $wpdb->prefix . "posts";
			$charset_collate = $wpdb->get_charset_collate();
			
			// sql for our table creation
			$sql = "CREATE TABLE $table_name (
			   ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			   source_id bigint(20) unsigned NOT NULL,
			   brand_id bigint(20) unsigned NOT NULL,
			   provider_id bigint(20) unsigned NOT NULL,
			   product_id bigint(20) unsigned NOT NULL,
			   name longtext,
			   image_link longtext,
			   UNIQUE KEY ID (ID)
			   ) $charset_collate;";
			
			// make sure we include wordpress functions for dbDelta	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			// dbDelta will create a new table if none exists or update an existing one
			dbDelta($sql);
			
			// return true
			$return_value = true;
			
		} catch( Exception $e ) {
			
			error_log('>>>>>>>>>>>>>>>>>>>>>>> ERRROR: suwp_create_plugin_service_model_table <<<<<<<<<<<<<<<<<<<<<<<<<< >>' . print_r( $e,true ) );
		
		}
		
		// return result
		return $return_value;
	}
	
	// creates custom suwp_network_country table for the plugin
	private static function suwp_create_plugin_network_country_table() {
		
		global $wpdb;
		
		// setup return value
		$return_value = false;
		
		try {
			
			$sql = '';
			$table_name = $wpdb->prefix . "suwp_network_country";
			$charset_collate = $wpdb->get_charset_collate();
			
			// sql for our table creation
			$sql = "CREATE TABLE $table_name (
			   ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			   source_id bigint(20) unsigned NOT NULL,
			   provider_id bigint(20) unsigned NOT NULL,
			   product_id bigint(20) unsigned NOT NULL,
			   name longtext,
			   image_link longtext,
			   UNIQUE KEY ID (ID)
			   ) $charset_collate;";
			
			// make sure we include wordpress functions for dbDelta	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			// dbDelta will create a new table if none exists or update an existing one
			dbDelta($sql);
			
			// return true
			$return_value = true;
			
		} catch( Exception $e ) {
			
			error_log('>>>>>>>>>>>>>>>>>>>>>>> ERRROR: suwp_create_plugin_network_country_table <<<<<<<<<<<<<<<<<<<<<<<<<< >>' . print_r( $e,true ) );
		
		}
		
		// return result
		return $return_value;
	}
	
	// creates custom suwp_network table for the plugin
	private static function suwp_create_plugin_network_table() {
		
		global $wpdb;
		
		// setup return value
		$return_value = false;
		
		try {
			
			$sql = '';
			$table_name = $wpdb->prefix . "suwp_network";
			$charset_collate = $wpdb->get_charset_collate();
			
			// sql for our table creation
			$sql = "CREATE TABLE $table_name (
			   ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			   source_id bigint(20) unsigned NOT NULL,
			   country_id bigint(20) unsigned NOT NULL,
			   provider_id bigint(20) unsigned NOT NULL,
			   product_id bigint(20) unsigned NOT NULL,
			   name longtext,
			   image_link longtext,
			   UNIQUE KEY ID (ID)
			   ) $charset_collate;";
			
			// make sure we include wordpress functions for dbDelta	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			// dbDelta will create a new table if none exists or update an existing one
			dbDelta($sql);
			
			// return true
			$return_value = true;
			
		} catch( Exception $e ) {
			
			error_log('>>>>>>>>>>>>>>>>>>>>>>> ERRROR: suwp_create_plugin_network_table <<<<<<<<<<<<<<<<<<<<<<<<<< >>' . print_r( $e,true ) );
		
		}
		
		// return result
		return $return_value;
	}
}
