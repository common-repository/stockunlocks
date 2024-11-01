<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.5.0
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.5.0
	 */
	public static function deactivate() {
		
		// this function will not only unschedule the task indicated by the timestamp,
		// it will also unschedule all future occurrences of the task.
		
		$timestamp = wp_next_scheduled( 'suwp_cron_hook_2minutes' );
		wp_unschedule_event($timestamp, 'suwp_cron_hook_2minutes' );
		
		$timestamp = wp_next_scheduled( 'suwp_cron_hook_5minutes' );
		wp_unschedule_event($timestamp, 'suwp_cron_hook_5minutes' );
		
		$timestamp = wp_next_scheduled( 'suwp_cron_hook_15minutes' );
		wp_unschedule_event($timestamp, 'suwp_cron_hook_15minutes' );
		
		$timestamp = wp_next_scheduled( 'suwp_cron_hook_30minutes' );
		wp_unschedule_event($timestamp, 'suwp_cron_hook_30minutes' );
		
		$timestamp = wp_next_scheduled( 'suwp_cron_hook_1hour' );
		wp_unschedule_event($timestamp, 'suwp_cron_hook_1hour' );
		
		$timestamp = wp_next_scheduled( 'suwp_cron_hook_3hours' );
		wp_unschedule_event($timestamp, 'suwp_cron_hook_3hours' );
		
		$timestamp = wp_next_scheduled( 'suwp_product_hook_1hour' );
		wp_unschedule_event($timestamp, 'suwp_product_hook_1hour' );
		
		$timestamp = wp_next_scheduled( 'suwp_product_hook_2hours' );
		wp_unschedule_event($timestamp, 'suwp_product_hook_2hours' );
		
		$timestamp = wp_next_scheduled( 'suwp_product_hook_3hours' );
		wp_unschedule_event($timestamp, 'suwp_product_hook_3hours' );
		
		$timestamp = wp_next_scheduled( 'suwp_product_hook_4hours' );
		wp_unschedule_event($timestamp, 'suwp_product_hook_4hours' );
		
		$timestamp = wp_next_scheduled( 'suwp_product_hook_5hours' );
		wp_unschedule_event($timestamp, 'suwp_product_hook_5hours' );
		
		$timestamp = wp_next_scheduled( 'suwp_product_hook_6hours' );
		wp_unschedule_event($timestamp, 'suwp_product_hook_6hours' );
		
	}

}
