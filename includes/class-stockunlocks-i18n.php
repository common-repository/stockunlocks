<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.5.0
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.5.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'stockunlocks',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
