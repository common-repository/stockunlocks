<?php

/**
 * Fired during plugin uninstall
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 */

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @since      1.5.0
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks_Uninstaller {

	/**
	 * Code that is run at plugin uninstall.
	 *
	 * Must ensure that the environment is left as we found it ;-)
	 *
	 * @since    1.5.0
	 */
	public static function uninstall() {
		
		Stock_Unlocks_Uninstaller::suwp_remove_plugin_tables();
		Stock_Unlocks_Uninstaller::suwp_remove_post_custom_data();
		Stock_Unlocks_Uninstaller::suwp_remove_post_product_data();
		Stock_Unlocks_Uninstaller::suwp_remove_options();
	}

	// removes our custom database tabels
	public static function suwp_remove_plugin_tables() {
		
		// get WP's wpdb class
		global $wpdb;
		
		// setup return variable
		$tables_removed = false;
		
		try {
			
			// get our custom table name
			$table_0 = $wpdb->prefix . "suwp_reward_links";
			$table_1 = $wpdb->prefix . "suwp_provider_mepname";
			$table_2 = $wpdb->prefix . 'suwp_service_brand';
			$table_3 = $wpdb->prefix . 'suwp_service_model';
			$table_4 = $wpdb->prefix . 'suwp_network_country';
			$table_5 = $wpdb->prefix . 'suwp_network';
			
			$tables = array( $table_0, $table_1, $table_2, $table_3, $table_4, $table_5 );
					
			foreach( $tables as $table_name ):
				// delete table from database
				$tables_removed = $wpdb->query("DROP TABLE IF EXISTS $table_name;");
			endforeach;
			
		} catch( Exception $e ) {
			
			error_log( 'ERROR - Removing StockUnlocks plugin database: ' . print_r( $e, true ) );
		}
		
		// return result
		return $tables_removed;
		
	}
	
	// removes plugin related post data: custom post types
	public static function suwp_remove_post_custom_data() {
		
		// get WP's wpdb class
		global $wpdb;
		
		// setup return variable
		$data_removed = false;
		
		try {
			
			// get our custom table name
			$table_name = $wpdb->prefix . "posts";
			
			// set up custom post types array
			$custom_post_types = array(
				'suwp_apisource',
			);
			
			// remove data from the posts db table where post types are equal to our custom post types
			$data_removed = $wpdb->query(
				$wpdb->prepare( 
					"
						DELETE FROM $table_name 
						WHERE post_type = %s
					", 
					$custom_post_types[0]
				) 
			);
				
			// get the table names for postmeta and posts with the correct prefix
			$table_name_1 = $wpdb->prefix . "postmeta";
			$table_name_2 = $wpdb->prefix . "posts";
			$wpID = 'NULL';
			
			// delete orphaned meta data
			$wpdb->query(
					"
					DELETE pm
					FROM $table_name_1 pm
					LEFT JOIN $table_name_2 wp ON wp.ID = pm.post_id
					WHERE wp.ID IS NULL
					
					"
				);
		
		} catch( Exception $e ) {
			
			// php error
			error_log( 'ERROR - Removing StockUnlocks custom post type data : ' . print_r( $e,true ) );
			
		}
		
		// return result
		return $data_removed;
		
	}
	
	// removes plugin related post data: products
	public static function suwp_remove_post_product_data() {
		
		// get WP's wpdb class
		global $wpdb;
		
		// setup return variable
		$data_removed = false;
		
		try {
			
			// get our custom table name
			$table_name = $wpdb->prefix . "posts";
			
			// set up product post types array
			$product_post_ids = array();
			$post_type = 'product';
			
			$suwp_products = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $table_name WHERE post_type=%s ORDER BY ID ASC", $post_type ) );
			
			$suwp_iss = array( '_suwp_api_provider', '_suwp_is_mep', '_suwp_is_network', '_suwp_is_model' , '_suwp_is_pin', '_suwp_is_rm_type', '_suwp_is_kbh' , '_suwp_is_reference' , '_suwp_is_service_tag', '_suwp_is_activation' );
			$action_set = 'None';
			$status = 'pending';
			
			foreach( $suwp_products as $product ):
				$current_product_id = $product->ID;
				
				// added 25-mar-19 so that any category name may be used for the suwp_service slug
				$suwp_has_term = FALSE;
				$slugs = array();
				$terms = get_the_terms( $current_product_id, 'product_cat' );
				if ( is_array($terms) ){
					foreach ( $terms as $term ) {
					$slugs[] = $term->slug;
					}
				}
				if ( in_array('suwp_service', $slugs, TRUE) ){
					$suwp_has_term = TRUE;
				}

				if ( $suwp_has_term ) {
					$product_post_ids[] = $current_product_id;
					// remove Product from Provider and reset all options, leave as pending
					wp_remove_object_terms( $current_product_id, 'suwp_service', 'product_cat' );
					foreach( $suwp_iss as $suwp_is ):
						update_post_meta( $current_product_id, $suwp_is, $action_set );
					endforeach;
					update_post_meta( $current_product_id, '_suwp_api_service_id', '' );
					update_post_meta( $current_product_id, '_suwp_price_adj', 'disabled' );
					update_post_meta( $current_product_id, '_suwp_price_adj_custom', 1 );
					update_post_meta( $current_product_id, '_suwp_service_credit', 0.0 );
					$post = array( 'ID' => $current_product_id, 'post_status' => $status );
					wp_update_post($post);
				}
			endforeach;
			
			$delete_products = false;
			// future: have the option to actually delete all Remote Service Products
			// for now just unassign and leave in the system
			if( $delete_products ) {
				
				foreach ($product_post_ids as $key => $id):
					error_log( 'foreach - $product_post_ids as $key => $id : ' . $id );
					// remove data from the posts db table where post types are equal to our custom post types
					$data_removed = $wpdb->query(
						$wpdb->prepare( 
							"
								DELETE FROM $table_name 
								WHERE ID = %d
							", 
							$id
						) 
					);
				endforeach;
				
			}
			
			// get the table names for postmeta and posts with the correct prefix
			$table_name_1 = $wpdb->prefix . "postmeta";
			$table_name_2 = $wpdb->prefix . "posts";
			$wpID = 'NULL';
			
			if( !empty( $product_post_ids ) ) {
				// delete orphaned meta data
				$wpdb->query(
						"
						DELETE pm
						FROM $table_name_1 pm
						LEFT JOIN $table_name_2 wp ON wp.ID = pm.post_id
						WHERE wp.ID IS NULL
						
						"
					);
			}
			
		} catch( Exception $e ) {
			
			// php error
			error_log( 'ERROR - Removing StockUnlocks product post data : ' . print_r( $e,true ) );
			
		}
		
		// return result
		return $data_removed;
		
	}
	
	// removes any custom options from the database
	public static function suwp_remove_options() {
		
		// get WP's wpdb class
		global $wpdb;
		
		$options_removed = false;
		
		try {
			
			// get plugin options settings
			$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
			$options = $plugin_public->suwp_exec_get_options_settings();
			
			// loop over all the settings
			foreach( $options['settings'] as &$setting ):
				delete_option( $setting );
				$options_removed = true;
			endforeach;

			$tab_options = array(
				'suwp_cron_options',
				'suwp_license_options',
				'suwp_ordersuccess_options',
				'suwp_product_sync_options',
				'suwp_troubleshoot_options',
				'suwp_checkerror_options',
				'suwp_orderavailable_options',
				'suwp_ordererror_options',
				'suwp_orderrejected_options',
				'suwp_fieldlabel_options',
				'suwp_textmessage_options',
				'suwp_cron_options',
				'suwp_license_options',
				'suwp_ordersuccess_options',
				'suwp_product_sync_options',
				'suwp_troubleshoot_options',
				'suwp_checkerror_options',
				'suwp_orderavailable_options',
				'suwp_ordererror_options',
				'suwp_orderrejected_options',
				'suwp_fieldlabel_options',
				'suwp_textmessage_options',
			 );

			 foreach( $tab_options as &$setting ):
				delete_option( $setting );
				$options_removed = true;
			 endforeach;

		} catch( Exception $e ) {
			
			// php error
			error_log( 'ERROR - Removing StockUnlocks options : ' . print_r( $e,true ) );
			
		}
		
		// return result
		return $options_removed;
		
	}

}
