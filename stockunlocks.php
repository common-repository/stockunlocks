<?php

 /**
 *
 * @link              https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since             1.5.0
 * @package           Stock_Unlocks
 *
 * @wordpress-plugin
 * Plugin Name: StockUnlocks
 * Plugin URI: https://www.stockunlocks.com/forums/forum/stockunlocks-wordpress-plugin/
 * Description: Automate your mobile unlocking store with the StockUnlocks plugin combined with WooCommerce and the power of various mobile unlocking APIs. Now, focus your time and energy where they're needed the most.
 * Version: 1.9.5.25
 * Author: StockUnlocks
 * Author URI: https://www.stockunlocks.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 4.0
 * Tested up to: 5.5.1
 * WC requires at least: 3.0.0
 * WC tested up to: 4.5.2
 * 
 * Text Domain: stockunlocks
 * Domain Path:       /languages
 */
 
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUWP_TEMP', get_temp_dir() );
define( 'SUWP_SLUG', 'stockunlocks' );
define( 'SUWP_SLUG_TEST', 'stockunlocks-test' );
define( 'SUWP_PLUGIN', 'stockunlocks/stockunlocks.php' );
define( 'SUWP_PLUGIN_TEST', 'stockunlocks-test/stockunlocks-test.php' );
define( 'SUWP_DIR', dirname( __FILE__ ) );
define( 'SUWP_URL', plugin_dir_url( __FILE__ ) );
define( 'SUWP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SUWP_PATH_ADPART', SUWP_PATH . 'admin/partials/' );
define( 'SUWP_PATH_PUPART', SUWP_PATH . 'public/partials/' );
define( 'SUWP_PATH_CLUDES', SUWP_PATH . 'includes/' );
define( 'kKLSSKjVsell5zJz8M', 'kKLSSKjVsel5zJz8M.php' );
define( 'kKLSSKjVsel5zJz8M', 'kKLSSKjVsell5zJz8M.php' );


define( 'SUWP_SOURCE_MANAGER', 'https://secure.facetsnovum.com/api/license-manager/v1' );
define( 'SUWP_LICENSE_EMAIL_BASIC', 'support@stockunlocks.com' );
define( 'SUWP_LICENSE_KEY_BASIC', 'ob^U7*z5DFAy7CB$#R%Z)FM^' );

/**
 * Current plugin version.
 */
define( 'STOCKUNLOCKS_VERSION', '1.9.5.25' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-stockunlocks-activator.php
 */
function activate_stockunlocks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-stockunlocks-activator.php';
	Stock_Unlocks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-stockunlocks-deactivator.php
 */
function deactivate_stockunlocks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-stockunlocks-deactivator.php';
	Stock_Unlocks_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-stockunlocks-uninstaller.php
 */
function uninstall_stockunlocks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-stockunlocks-uninstaller.php';
	Stock_Unlocks_Uninstaller::uninstall();
}

register_activation_hook( __FILE__, 'activate_stockunlocks' );
register_deactivation_hook( __FILE__, 'deactivate_stockunlocks' );
register_uninstall_hook( __FILE__, 'uninstall_stockunlocks' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-stockunlocks.php';


// Including ACF is allowed as per: https://www.advancedcustomfields.com/resources/including-acf-in-a-plugin-theme/
include_once( plugin_dir_path( __FILE__ ) .'includes/lib/advanced-custom-fields/acf.php' );
include_once( plugin_dir_path( __FILE__ ) .'includes/api/cron/suwp_cron_email_templates.php' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.5.0
 */
function run_stockunlocks() {

	$plugin = new Stock_Unlocks();
	$plugin->run();

}
run_stockunlocks();
