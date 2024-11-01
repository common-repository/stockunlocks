<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	require_once( 'class-suwp-license-manager-client.php' );

if ( is_admin() ) {
	$suwp_default_license_path = plugin_dir_path( __FILE__ ) . 'suwp-default-license.txt';
	$suwp_default_license = file_get_contents($suwp_default_license_path);
	
	$suwp_license_email = get_option('suwp_license_email');
	$suwp_license_key = get_option('suwp_license_key');
	
	$product_id = 'stockunlocks-plugin';
	$product_name = 'StockUnlocks Plugin';
	
	if ( $suwp_license_key == NULL || $suwp_license_email == NULL ) {
			
		$product_id = 'stockunlocks-plugin';
		$product_name = 'StockUnlocks Plugin';
	}
	
	if ( !$suwp_license_key == NULL) {
		
		if ( !( $suwp_license_key == SUWP_LICENSE_KEY_BASIC ) ) {
			
			$product_id = 'stockunlocks-plugin-pro';
			$product_name = 'StockUnlocks Pro';
		}
	}
	
	$extract = '';
	$extract_check = get_option('suwp_author_info');
	
	if( !is_object( $extract_check ) ) {
			
		$license_manager = new Suwp_License_Manager_Client(
			$product_id,
			$product_name,
			'stockunlocks-plugin-text',
			SUWP_SOURCE_MANAGER,
			'plugin',
			SUWP_PATH . 'stockunlocks.php'
		);
		
		$extract = $license_manager->get_license_info();
		update_option( 'suwp_author_info', $extract );
		
		if( is_object($extract) ) {
			
			if( !isset($extract->error) ) {
				$suwp_valid_until = 'Never';
				update_option( 'suwp_author_value', $extract->author );
				$valid_until = $extract->valid_until;
				if( $valid_until != '0000-00-00 00:00:00' ) {
					$t = date_create( $valid_until ); // ORIGINAL format = $t->format( 'Y-m-d H:i:s' )
					$suwp_valid_until = $t->format( 'd-M-Y' );
				}
				update_option( 'suwp_valid_until', $suwp_valid_until );
			}
		}
	}
	
	$posts = get_posts(
		array (
			'post_type' => 'suwp_apisource',
			'post_status'   => array('publish', 'pending', 'draft', 'private'),
			'posts_per_page' => -1,
			'orderby' => 'ID',
			'order' => 'ASC'
		)
	);
	
	$extract = get_option('suwp_author_info');
	$posts_array = array();
	
	foreach( $posts as $apiprovider ):
		$posts_array[] = $apiprovider->ID;
	endforeach;
	
	update_option( 'suwp_array_posts', $posts_array );
	update_option( 'suwp_reference_posts', array() );
	
	if ( count ( $posts_array ) > 1 ) {
		
		if ( is_object( $extract ) ) {
			
			if( !isset($extract->error) ) {
				
				$reference_posts = array($posts_array[0], $posts_array[1]);
				
				update_option( 'suwp_reference_posts', $reference_posts );
				
				$include = $extract->include_10;
				$include_array = get_option($include);
				
				if( is_array( $include_array ) ) {
					
					$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
					
					$product_id = $plugin_admin->suwp_exec_product_meta_key();
					
					foreach( $posts_array as $apiprovider ):
						
						$current_provider_id = $apiprovider;
						
						if ( !in_array( $current_provider_id, $reference_posts, true ) ) {
							
							$current_provider = array(
							   'ID' => $current_provider_id,
							   'post_status'    => 'pending',
							);
							
							$post_id = wp_update_post( $current_provider );
							
							if (is_wp_error($post_id)) {
								$errors = $post_id->get_error_messages();
								foreach ($errors as $error) {
									error_log('wp_update_post ERROR: ' .  $error);
								}
							}
							
							update_post_meta( $current_provider_id, 'suwp_activeflag', 0 );
						}	
						
					endforeach;
				}
				
			}
		}
	}	
}
