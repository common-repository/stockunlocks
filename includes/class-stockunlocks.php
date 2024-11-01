<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.5.0
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/includes
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.5.0
	 * @access   protected
	 * @var      Stock_Unlocks_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.5.0
	 * @access   protected
	 * @var      string    $stockunlocks    The string used to uniquely identify this plugin.
	 */
	protected $stockunlocks;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.5.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.5.0
	 */
	public function __construct() {
		
		if ( defined( 'STOCKUNLOCKS_VERSION' ) ) {
			$this->version = STOCKUNLOCKS_VERSION;
		} else {
			$this->version = '1.9.5.25';
		}
		
		$this->stockunlocks = 'stockunlocks';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Stock_Unlocks_Loader. Orchestrates the hooks of the plugin.
	 * - Stock_Unlocks_i18n. Defines internationalization functionality.
	 * - Stock_Unlocks_Admin. Defines all hooks for the admin area.
	 * - Stock_Unlocks_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.5.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-stockunlocks-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-stockunlocks-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-stockunlocks-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-stockunlocks-public.php';

		/**
		 * The class responsible for showing data from a database table in a nice grid view
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lib/class-stockunlocks-list-table.php';

		/**
		 * The class that extends the WP_List_Table
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-stockunlocks-stand-alone-list.php';

		$this->loader = new Stock_Unlocks_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Stock_Unlocks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.5.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Stock_Unlocks_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Stock_Unlocks_Admin( $this->get_stockunlocks(), $this->get_version() );
		
		$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'suwp_upgrade_completed', 10, 2 );

		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'suwp_custom_dashboard_widgets' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'suwp_admin_styles', 10 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'suwp_admin_scripts', 10 );
		
		// use a modal window to edit suwp orders
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'suwp_add_admin_post_edit_scripts');
		// remove the standard suwp admin .js when working within modal
		$this->loader->add_action( 'wp_print_scripts', $plugin_admin, 'suwp_deregister_admin_javascript', 9 );

		// use a modal window to add or view suwp order meta
		$this->loader->add_action( 'admin_action_suwp_ordermeta', $plugin_admin, 'suwp_render_ordermeta_page' );

		$this->loader->add_filter( 'cron_schedules', $plugin_admin,'suwp_add_cron_intervals' );
		
		// in order to get our task to execute we must create our own custom hook
		$this->loader->add_action( 'suwp_cron_hook_2minutes', $plugin_admin, 'suwp_cron_exec_2minutes' );
		$this->loader->add_action( 'suwp_cron_hook_5minutes', $plugin_admin, 'suwp_cron_exec_5minutes' );
		$this->loader->add_action( 'suwp_cron_hook_15minutes', $plugin_admin, 'suwp_cron_exec_15minutes' );
		$this->loader->add_action( 'suwp_cron_hook_30minutes', $plugin_admin, 'suwp_cron_exec_30minutes' );
		$this->loader->add_action( 'suwp_cron_hook_1hour', $plugin_admin, 'suwp_cron_exec_1hour' );
		$this->loader->add_action( 'suwp_cron_hook_3hours', $plugin_admin, 'suwp_cron_exec_3hours' );
		$this->loader->add_action( 'suwp_product_hook_1hour', $plugin_admin, 'suwp_product_exec_1hour' );
		$this->loader->add_action( 'suwp_product_hook_2hours', $plugin_admin, 'suwp_product_exec_2hours' );
		$this->loader->add_action( 'suwp_product_hook_3hours', $plugin_admin, 'suwp_product_exec_3hours' );
		$this->loader->add_action( 'suwp_product_hook_4hours', $plugin_admin, 'suwp_product_exec_4hours' );
		$this->loader->add_action( 'suwp_product_hook_5hours', $plugin_admin, 'suwp_product_exec_5hours' );
		$this->loader->add_action( 'suwp_product_hook_6hours', $plugin_admin, 'suwp_product_exec_6hours' );
		
		// register plugin options
		$this->loader->add_action( 'admin_init', $plugin_admin, 'suwp_register_options' );
		$this->loader->add_filter( "pre_update_option_suwp_plugin_type", $plugin_admin, 'filter_pre_update_option_settings_suwp_plugin_type', 10, 1 );
		
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $plugin_admin, 'suwp_check_for_update' );
		$this->loader->add_filter( 'plugins_api', $plugin_admin, 'suwp_plugins_api_handler', 10, 3 );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_check_wp_version' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_no_services_admin_notice' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_troubleshooting_admin_notice' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_cron_disabled_admin_notice' );
		
		// dismissible notice after first time activation of plugin
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_activation_admin_notice' );
		// remote dismissible notice: all pages except plugin
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_remote_admin_notice' );
		// remote dismissible notice: only on plugin page
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_remote_plugin_admin_notice' );
		// dismissible notice when options are saved: only on plugin page
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'suwp_options_saved_admin_notice' );
		
		// Mainly used for the custom post type view: table, individual viewing/editing
		$this->loader->add_action('admin_footer', $plugin_admin, 'suwp_default_admin_footer');
		
		// setup default admin footer text, appears on every page
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin,'suwp_default_admin_text', 11 );
		
		$this->loader->add_action('admin_footer-post.php', $plugin_admin, 'suwp_append_post_status_list');
		$this->loader->add_filter( 'display_post_states', $plugin_admin, 'suwp_display_imported_state' );
		
		// custom admin menus, including screen option value setting (manage orders)
		$this->loader->add_filter( 'set-screen-option', $plugin_admin, 'suwp_set_screen_option', 10, 3);
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'suwp_admin_menus' );

		// set the plugin submenu as active/current when creating/editing a custom post type
		$this->loader->add_filter('parent_file', $plugin_admin, 'suwp_admin_parent_file');

		$this->loader->add_action( 'suwp_hook_get_admin_notice', $plugin_admin, 'suwp_exec_get_admin_notice' ); // suwp_get_admin_notice
		
		// fix the post_title = 'Auto Draft' on the custom post type. Set post_title to suwp_sitename's post meta value  
		$this->loader->add_action( 'save_post', $plugin_admin,'suwp_apisource_title' );
		
		// register ajax actions (giving permission to submit forms, for example)
		$this->loader->add_action( 'wp_ajax_suwp_parse_import_csv', $plugin_admin, 'suwp_parse_import_csv' );
		$this->loader->add_action( 'wp_ajax_suwp_api_action', $plugin_admin, 'suwp_api_action' );
		$this->loader->add_action( 'wp_ajax_suwp_import_services', $plugin_admin, 'suwp_import_services' );
		$this->loader->add_action( 'wp_ajax_suwp_parse_import_api', $plugin_admin, 'suwp_parse_import_api' );
		$this->loader->add_action( 'wp_ajax_suwp_download_services_csv', $plugin_admin, 'suwp_download_services_csv' );
		$this->loader->add_action( 'wp_ajax_suwp_remote_notice_ignore', $plugin_admin, 'suwp_remote_notice_ignore' );
		
		$this->loader->add_action( 'wp_ajax_suwp_brandmodel_populate_values', $plugin_admin, 'suwp_brandmodel_populate_values' );
		$this->loader->add_action( 'wp_ajax_nopriv_suwp_brandmodel_populate_values', $plugin_admin, 'suwp_brandmodel_populate_values' );
		$this->loader->add_action( 'wp_ajax_suwp_countrynetwork_populate_values', $plugin_admin, 'suwp_countrynetwork_populate_values' );
		$this->loader->add_action( 'wp_ajax_nopriv_suwp_countrynetwork_populate_values', $plugin_admin, 'suwp_countrynetwork_populate_values' );
		$this->loader->add_action( 'wp_ajax_suwp_mep_populate_values', $plugin_admin, 'suwp_mep_populate_values' );
		$this->loader->add_action( 'wp_ajax_nopriv_suwp_mep_populate_values', $plugin_admin, 'suwp_mep_populate_values' );
		
		// register custom admin column headers
		$this->loader->add_filter( 'manage_edit-suwp_apisource_columns', $plugin_admin, 'suwp_apisource_column_headers' );
		// The above handles the ui, this actually does the sorting
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'suwp_apisource_column_orderby' );

		// register custom admin column data ; including 1,2 means that we want column name AND post id.
		$this->loader->add_filter( 'manage_suwp_apisource_posts_custom_column', $plugin_admin, 'suwp_apisource_column_data',1,2 );
		$this->loader->add_action( 'admin_head-edit.php', $plugin_admin, 'suwp_register_custom_admin_titles' );
		
		// format the woocommerce order according to status
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'suwp_styling_admin_order_list_error_wc' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'suwp_styling_admin_order_list_unavailable_wc' );

		$this->loader->add_filter( 'bulk_actions-edit-suwp_apisource', $plugin_admin, 'suwp_bulk_actions' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'suwp_post_published' );
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'suwp_row_actions', 10, 1 );
		$this->loader->add_filter( 'map_meta_cap', $plugin_admin, 'suwp_meta_function', 10, 4 );

		// Advanced Custom Fields Settings
		// Including ACF is allowed as per: https://www.advancedcustomfields.com/resources/including-acf-in-a-plugin-theme/
		// >>> $this->loader->add_filter( 'acf/settings/path', $plugin_admin, 'suwp_acf_settings_path' );
		// >>> $this->loader->add_filter( 'acf/settings/dir', $plugin_admin, 'suwp_acf_settings_dir' );
		$this->loader->add_filter('acf/settings/show_admin', $plugin_admin, 'suwp_acf_show_admin');

		// WooCommerce related
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'suwp_custom_product_data_tab' , 10 , 1 );
		$this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'suwp_custom_product_data_fields' );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'suwp_add_custom_general_fields_save' );
		$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_admin, 'suwp_add_product_custom_fields' );
		$this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'suwp_add_custom_order_statuses' );
		$this->loader->add_filter( 'woocommerce_reports_get_order_report_data_args', $plugin_admin, 'suwp_reports_get_order_custom_report_data_args' );
		// Add a custom field to product bulk edit special page
		$this->loader->add_action( 'woocommerce_product_bulk_edit_start', $plugin_admin, 'suwp_custom_field_product_bulk_edit', 10, 0 );
		// Save the custom fields data when submitted for product bulk edit
		$this->loader->add_action('woocommerce_product_bulk_edit_save', $plugin_admin, 'suwp_save_custom_field_product_bulk_edit', 10, 1);
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Stock_Unlocks_Public( $this->get_stockunlocks(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'suwp_public_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'suwp_public_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'suwp_add_apisources_post_type' );
		
		// registers all custom shortcodes, etc. on init
		$this->loader->add_action( 'init', $plugin_public, 'suwp_register_allcodes' );
		
		$this->loader->add_action( 'wp_footer', $plugin_public, 'suwp_access_single_product_jscript' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'suwp_access_cart_page_jscript' );
		
		// WooCommerce related
		$this->loader->add_action( 'woocommerce_add_to_cart_validation', $plugin_public, 'suwp_custom_fields_validation', 10, 3 );
		$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'suwp_modify_cart_before_add', 10, 2 );
		$this->loader->add_action( 'woocommerce_add_cart_item_data', $plugin_public, 'suwp_save_values_to_cutsom_fields', 10, 2 );
		$this->loader->add_action( 'woocommerce_get_item_data', $plugin_public, 'suwp_render_meta_on_cart_and_checkout', 10, 2 );
		$this->loader->add_action( 'woocommerce_quantity_input_args', $plugin_public, 'suwp_quantity_input_args', 10, 2 );
		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $plugin_public, 'suwp_remove_add_to_cart_buttons_shop', 1 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'suwp_remove_add_to_cart_buttons_product', 5 );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'suwp_view_order_and_thankyou_page', 20 );
		$this->loader->add_action( 'woocommerce_view_order', $plugin_public, 'suwp_view_order_and_thankyou_page', 20 );
		
	}
	

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.5.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.5.0
	 * @return    string    The name of the plugin.
	 */
	public function get_stockunlocks() {
		return $this->stockunlocks;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.5.0
	 * @return    Stock_Unlocks_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.5.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
