<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/admin
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      string    $stockunlocks    The ID of this plugin.
	 */
	private $stockunlocks;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.5.0
	 * @param      string    $stockunlocks       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $stockunlocks, $version ) {
		
		$this->stockunlocks = $stockunlocks;
		$this->version = $version;
		
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.5.0
	 */
	public function suwp_admin_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Stock_Unlocks_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Stock_Unlocks_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		// register scripts with WordPress's internal library
		// add to que of scripts that get loaded into every admin page
		wp_enqueue_style( 'stockunlocks-admin.css', plugin_dir_url( __FILE__ ) . 'css/stockunlocks-admin.css', array(), $this->version, 'all' );
			
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.5.0
	 */
	public function suwp_admin_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Stock_Unlocks_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Stock_Unlocks_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		// register scripts with WordPress's internal library
		// add to que of scripts that get loaded into every admin page
		// >>>>>>> wp_enqueue_script( 'stockunlocks-admin-js', plugin_dir_url( __FILE__ ) . 'js/stockunlocks-admin.js', array( 'jquery' ), $this->version, true );
		
		// register scripts with WordPress's internal library
		// wp_register_script('stockunlocks-js-private', plugins_url('/admin/js/stockunlocks.js?ver=1.5.0',__FILE__), array('jquery'),'',true);
		wp_register_script('stockunlocks-admin-js', plugin_dir_url( __FILE__ ) . 'js/stockunlocks-admin.js', array( 'jquery' ), $this->version, true );
		wp_register_script('sweetalert2.all.min.js', plugin_dir_url( __FILE__ ) . 'js/sweetalert2.all.min.js', array( 'jquery' ), $this->version, true );
		
		// add to que of scripts that get loaded into every admin page
		wp_enqueue_script('stockunlocks-admin-js');
		wp_enqueue_script('sweetalert2.all.min.js');
		
		$phpInfo = array(
			'suwp_home' => get_option( 'home' ),
			'suwp_siteurl' => get_option( 'siteurl' ),
			'suwp_admin_siteurl' => get_admin_url(),
		);
		wp_localize_script( 'stockunlocks-admin-js', 'phpInfo', $phpInfo );
		
	}
	
	// custom dashboard widget for StockUnlocks
	public function suwp_custom_dashboard_widgets() {
		global $wp_meta_boxes;
		
		function suwp_custom_dashboard_help() {
			echo '<p>Welcome to the StockUnlocks Plugin! Need help? Contact the developer <a href="mailto:support@stockunlocks.com">here</a>. For StockUnlocks Tutorials visit: <a href="https://www.youtube.com/stockunlocks" target="_blank">StockUnlocks Channel</a></p>';
		}
		
		wp_add_dashboard_widget('custom_help_widget', 'StockUnlocks Support', 'suwp_custom_dashboard_help');
	}
	
	// remove items from the bulk actions dropdown for suwp_apisource custom post type
	public function suwp_bulk_actions( $actions ){
		unset( $actions[ 'edit' ] );
		// unset( $actions[ 'trash' ] );
		return $actions;
	}
	
	// the suwp_apisource doesn't need to be viewed outside of editing and tables.
	// therefore removing to "View post" or similar, after editing
	public function suwp_post_published( $messages ) {
		global $post;

		if ( is_object( $post ) ) {

		if( $post->post_type === 'suwp_apisource' ) {

				unset($messages['post'][1]); // Post updated. View post
				unset($messages['post'][4]); // Post updated.
				unset($messages['post'][6]); // Post published. View post
				unset($messages['post'][7]); // Post saved.
				unset($messages['post'][10]); // Post draft updated. Preview post
				
				$messages['suwp_apisource'] = array(
					0 => '', // Unused. Messages start at index 1.
					1 => sprintf( __('Post updated.') ),
					4 => __('Post updated.'),
					6 => sprintf( __('Post published.') ),
					7 => __('Post saved.'),
					10 => sprintf( __('Post draft updated.') ),
					);
			}
		}
		
		return $messages;
	}
	
	public function suwp_apisource_title ( $post_id ) {
		global $wpdb;
		global $post;

		if ( is_object( $post ) ) {

			if( $post->post_type === 'suwp_apisource' ) {

				$title = get_post_meta($post_id, 'suwp_sitename', true);
				$where = array( 'ID' => $post_id );
				$wpdb->update( $wpdb->posts, array( 'post_title' => $title ), $where );
			}
		}
	}
	
	public function suwp_row_actions( $actions ) {
		global $post;

		if ( is_object( $post ) ) {

			if( $post->post_type === 'suwp_apisource' ) {
				
				$extract = get_option('suwp_author_info');
				
				if( is_object( $extract ) ) {
					if( !isset($extract->error) ) {
						$include = $extract->include_10;
						
						$include_array = get_option($include);
						
						if( is_array( $include_array ) ) {
							
							if( count( $include_array ) > 1 ) {
									
								$array_posts = get_option('suwp_array_posts');
								$suwp_post = get_post();
								$post_id = $suwp_post->ID;
								
								if ( ! in_array( $post_id, $include_array, true ) ){
									return array();
								} else {
									unset( $actions['view'] );
								}
								
							}
							
						} else {
							
							unset( $actions['view'] );
						}
					}
				}
			}
		}
		
		return $actions;
	}
	
	public function suwp_meta_function( $caps, $cap, $user_id, $args ) {
		global $post;

		$extract = get_option('suwp_author_info');

		if ( is_object( $extract ) ) {
			
			if( !isset($extract->error) ) {
					
				$include = $extract->include_10;
				$suwp_return = $extract->include_9;
				
				$include_array = get_option($include);
				
				$to_filter = ['edit_post', 'delete_post', 'edit_page', 'delete_page'];
			
				// If the capability being filtered isn't of our interest, just return current value
				if ( ! in_array( $cap, $to_filter, true ) ) {
					return $caps;
				}
				
				if ( is_object( $post ) ) {

					if( $post->post_type === 'suwp_apisource' ) {
						
						if( is_array( $include_array ) ) {
							
							if( count( $include_array ) > 1 ) {
						
								if ( ! empty( $args[0] ) ) {
									
									if ( ! in_array( $args[0], $include_array, true ) ){
										
										return [ $suwp_return ];
									}
								}
							}
						}
					}
				}
			}
		}
		
		// Every user is allowed to exist.
		// Return this array, the check for capability will be true
		return [ 'exist' ];
	}
	
	public function suwp_acf_show_admin() {
		// return current_user_can('manage_options');
		$suwp_acf_option = get_option( 'suwp_manage_acf_menu_enabled' );
		return $suwp_acf_option;
	}

	// cleaning up suwp_apisource
	public function suwp_cleanup_apiprovider() {
		
		global $wpdb;
		$old_post_types = array('suwp_apiprovider' => 'suwp_apisource');
		foreach ($old_post_types as $old_type=>$type) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = REPLACE(post_type, %s, %s) 
								 WHERE post_type LIKE %s", $old_type, $type, $old_type ) );
			
		}	
	}
	
	public function suwp_toggle_plugin() {
		// Check to see if plugin is already active
		if(is_plugin_active(SUWP_PLUGIN)) {
			error_log( 'StockUnlocks - TOGGLE PLUGIN ...' );
			deactivate_plugins(SUWP_PLUGIN);
			activate_plugin(SUWP_PLUGIN);
		}
	}
	
	/**
	* This function runs when WordPress completes its upgrade process
	* It iterates through each plugin updated to see if suwp is included
	* @param $upgrader_object Array
	* @param $options Array
	*/
	public function suwp_upgrade_completed( $upgrader_object, $options ) {
		$suwp_plugin = plugin_basename( __FILE__ );
		if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			foreach( $options['plugins'] as $plugin ) {
				if( $plugin == $suwp_plugin ) {
					$this->suwp_cleanup_apiprovider();
					$this->suwp_toggle_plugin();
				}
			}
		 }
	}
	
	public function suwp_append_post_status_list(){
		// $("#save-post").text("Save Imported"); // supposed to change the button text, but doesn't
		// this actually changes the button, but not at the right time: $("#save-post").val("Save Imported");
		// removed this: $(".misc-pub-section label").append("'.$label.'"); it was slapping labels all over the place
		 global $post;
		 $complete = '';
		 $label = '';
		 if($post->post_type == 'product'){
			  if($post->post_status == 'imported'){
				   $complete = 'selected=\"selected\"';
				   $label = '<span id=\"post-status-display\"> Imported</span>';
			  }
			  echo '
			  <script>
			  jQuery(document).ready(function($){
				   $("#post_status").val("imported");
				   $("select#post_status").append("<option value=\"imported\" '.$complete.'>Imported</option>");
			  });
			  </script>
			  ';
		 }
	}
	
	public function suwp_display_imported_state( $states ) {
		global $post;
		$arg = get_query_var( 'post_status' );
		if($arg != 'imported'){
			 if($post->post_status == 'imported'){
				  return array('Imported');
			 }
		}
	   return $states;
	}
	
	// Add to list of WC Order statuses
	// These labels appear in browser for customer and when editing the order as admin
	public function suwp_add_custom_order_statuses($order_statuses) {
		$new_order_statuses = array();
	
		// add new order status after processing
		foreach ($order_statuses as $key => $status) {
			$new_order_statuses[$key] = $status;
			if ('wc-processing' === $key) {
				$new_order_statuses['wc-suwp-manual'] = 'Manual Processing';
				$new_order_statuses['wc-suwp-rejected'] = 'Paypal rejected';
				$new_order_statuses['wc-suwp-error'] = 'Processing error';
				$new_order_statuses['wc-suwp-ordered'] = 'Code ordered';
				$new_order_statuses['wc-suwp-order-part'] = 'Partially ordered';
				$new_order_statuses['wc-suwp-pending'] = 'Code pending';
				$new_order_statuses['wc-suwp-available'] = 'Code delivered';
				$new_order_statuses['wc-suwp-avail-part'] = 'Codes partially delivered';
				$new_order_statuses['wc-suwp-unavailable'] = 'Code unavailable';
				$new_order_statuses['wc-suwp-refunding'] = 'Code Pending refund';
				$new_order_statuses['wc-suwp-refund-part'] = 'Codes Partially refunded';
			}
		}
		return $new_order_statuses;
	}
	
	// Admin reports for custom order status
	// Just drop "wc" as the prefix
	public function suwp_reports_get_order_custom_report_data_args( $args ) {
		$args['order_status'] = array(
									  'completed',
									  'processing',
									  'on-hold',
									  'suwp-manual',
									  'suwp-rejected',
									  'suwp-error',
									  'suwp-ordered',
									  'suwp-order-part',
									  'suwp-pending',
									  'suwp-available',
									  'suwp-avail-part',
									  'suwp-unavailable',
									  'suwp-refunding',
									  'suwp-refund-part'
									  );
		
		return $args;
	}
	
	public function suwp_apisource_column_headers( $columns ) {
		
		// creating custom column header data
		$columns = array(
			'cb'=>'<input type="checkbox" />',
			'title'=>__('Site Name'),
			'active'=>__('Active'),
			'user_name'=>__('USER NAME'),
			'url'=>__('API URL'),
			'post_id'=>__('Post ID'),
			'api_key'=>__('API Key'),
		);
		
		// returning new columns
		return $columns;
		
	}
	
	public function suwp_apisource_column_orderby( $query ) {  
		
		if ( function_exists('get_current_screen')  ) {
		
			$screen = get_current_screen();
			
			if (is_object($screen)) {
					
				$current_screen = $screen->base;
				
				//This value will be determined by the column clicked
				//and its associated sort identifier (see above!)
				$orderby = $query->get( 'orderby');  
				$order   = $query->get( 'order' );
				
				if ($current_screen == 'edit') {
					
					if ('suwp_apisource' == $query->get('post_type')) {
						
						if( 'asc' == $order ) {  
							$query->set( 'order' , 'asc' );
							$query->set( 'orderby', 'the_title');
							return;
						} else {
							$query->set( 'order' , 'desc' );
							$query->set( 'orderby', 'the_title');
							return;
							
						}
					}
				}
				
			}
		}
	}
	
	public function suwp_apisource_column_data( $column, $post_id ) {
		
		// setup our return text
		$output = '';
		
		switch( $column ) {
			
			case 'title':
				// get the custom provider name data
				$provider = get_field('suwp_sitename', $post_id ); // $title = get_the_title( $post_id );
				$output .= $provider;
				break;
			case 'active':
				// get the site active data
				$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );
				$active = 'No';
				
				switch( $suwp_activeflag ) {
					case 0:
						$active = 'No';
						break;
					case 1:
						$active = 'Yes';
						break;
				} 
				
				$output .= $active;
				break;
			case 'user_name':
				// get the user name data
				$username = get_field('suwp_username', $post_id );
				$output .= $username;
				break;
			case 'url':
				// get the site url data
				$urlsite = get_field('suwp_url', $post_id );
				$output .= $urlsite;
				break;
			case 'post_id':
				// get the post identifier
				$output .= $post_id .' status: '.get_post_status( $post_id );
				break;
			case 'api_key':
				// get the api key
				$apidetails = $this->suwp_dhru_get_provider_array( $post_id );
				$output .= $apidetails['suwp_dhru_api_key'];
				break;
			
		}
		
		// echo the output
		echo $output;
		
	}
	
	// registers special custom admin title columns
	public function suwp_register_custom_admin_titles() {
		
		// handles custom admin title "title" column data for post types without titles
		function suwp_custom_admin_titles( $title, $post_id ) {
			
			 global $post;
			 
			 $output = $title;
			
			 if( isset($post->post_type) ):
						 switch( $post->post_type ) {
								 case 'suwp_apisource':
										 $provider = get_field('suwp_sitename', $post_id );
										 $output = $provider;
										 break;
						 }
				 endif;
			
			 return $output;
		}
		
		add_filter(
			'the_title',
			'suwp_custom_admin_titles',
			99,
			2
		);
	}
	
	// format the woocommerce order according to status: WooCommerce - Error
	public function suwp_styling_admin_order_list_error_wc() {
		global $pagenow, $post;

		if( $post ) {
			if( $pagenow != 'edit.php' ) return; // Exit
			if( get_post_type($post->ID) != 'shop_order' ) return; // Exit
		
			// set the custom status
			$order_status = 'suwp-error';

			?>
			<style>
				.order-status.status-<?php echo sanitize_title( $order_status ); ?> {
					background: #eba3a3;
					color: #761919;
				}
			</style>
			<?php
		}

	}
	
	// format the woocommerce order according to status: WooCommerce - Unavailable
	public function suwp_styling_admin_order_list_unavailable_wc() {
		global $pagenow, $post;

		if( $post ) {
			if( $pagenow != 'edit.php' ) return; // Exit
			if( get_post_type($post->ID) != 'shop_order' ) return; // Exit
		
			// set the custom status
			$order_status = 'suwp-unavailable';

			?>
			<style>
				.order-status.status-<?php echo sanitize_title( $order_status ); ?> {
					background: #eba3a3;
					color: #761919;
				}
			</style>
			<?php
		}

	}

	// Display Tabs
	public function suwp_custom_product_data_tab( $product_data_tabs ) {
		$product_data_tabs['suwp-custom-tab'] = array(
			'label' => __( 'StockUnlocks', 'stockunlocks' ),
			'target' => 'suwp_custom_product_data',
		);
		return $product_data_tabs;
	}
	
	public function suwp_custom_product_data_fields() {
		
		global $woocommerce, $post;
		
		$extract = get_option( 'suwp_author_info' );
		$include = kKLSSKjVsel5zJz8M;
		if ( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$include = $extract->include_8;
			}
		}
		include( SUWP_PATH_ADPART . $include );
	}
	
	// Custom General Fields: Woocommerce
	// saving field values
	public function suwp_add_custom_general_fields_save( $post_id ) {
	
		// Text Field

		if ( isset($_POST['_suwp_api_provider']) ) {
			$suwp_api_provider_field = sanitize_text_field($_POST['_suwp_api_provider']);
			update_post_meta( $post_id, '_suwp_api_provider', $suwp_api_provider_field );
		}

		if ( isset($_POST['_suwp_process_time']) ) {
			$suwp_process_time_field = sanitize_text_field($_POST['_suwp_process_time']);
			update_post_meta( $post_id, '_suwp_process_time', $suwp_process_time_field );
		}

		if ( isset($_POST['_suwp_is_mep']) ) {
			$suwp_is_mep_select = sanitize_text_field($_POST['_suwp_is_mep']);
			update_post_meta( $post_id, '_suwp_is_mep', $suwp_is_mep_select );
		}

		if ( isset($_POST['_suwp_is_network']) ) {
			$suwp_is_network_select = sanitize_text_field($_POST['_suwp_is_network']);
			update_post_meta( $post_id, '_suwp_is_network', $suwp_is_network_select );
		}

		if ( isset($_POST['_suwp_is_model']) ) {
			$suwp_is_model_select = sanitize_text_field($_POST['_suwp_is_model']);
			update_post_meta( $post_id, '_suwp_is_model', $suwp_is_model_select );
		}

		if ( isset($_POST['_suwp_is_pin']) ) {
			$suwp_is_pin_select = sanitize_text_field($_POST['_suwp_is_pin']);
			update_post_meta( $post_id, '_suwp_is_pin', $suwp_is_pin_select );
		}
		
		if ( isset($_POST['_suwp_is_rm_type']) ) {
			$suwp_is_rm_type_select = sanitize_text_field($_POST['_suwp_is_rm_type']);
			update_post_meta( $post_id, '_suwp_is_rm_type', $suwp_is_rm_type_select );
		}

		if ( isset($_POST['_suwp_is_kbh']) ) {
			$suwp_is_kbh_select = sanitize_text_field($_POST['_suwp_is_kbh']);
			update_post_meta( $post_id, '_suwp_is_kbh', $suwp_is_kbh_select );
		}

		if ( isset($_POST['_suwp_is_reference']) ) {
			$suwp_is_reference_select = sanitize_text_field($_POST['_suwp_is_reference']);
			update_post_meta( $post_id, '_suwp_is_reference', $suwp_is_reference_select );
		}

		if ( isset($_POST['_suwp_is_service_tag']) ) {
			$suwp_is_service_tag_select = sanitize_text_field($_POST['_suwp_is_service_tag']);
			update_post_meta( $post_id, '_suwp_is_service_tag', $suwp_is_service_tag_select );
		}

		if ( isset($_POST['_suwp_is_activation']) ) {
			$suwp_is_activation_select = sanitize_text_field($_POST['_suwp_is_activation']);
			update_post_meta( $post_id, '_suwp_is_activation', $suwp_is_activation_select );
		}

		if ( isset($_POST['_suwp_price_group_id']) ) {
			$suwp_price_group_id_field = sanitize_text_field($_POST['_suwp_price_group_id']);
			update_post_meta( $post_id, '_suwp_price_group_id', $suwp_price_group_id_field );
		}

		if ( isset($_POST['_suwp_price_group_name']) ) {
			$suwp_price_group_name_field = sanitize_text_field($_POST['_suwp_price_group_name']);
			update_post_meta( $post_id, '_suwp_price_group_name', $suwp_price_group_name_field );
		}

		if ( isset($_POST['_suwp_assigned_brand']) ) {
			$suwp_assigned_brand_textarea = sanitize_text_field( $_POST['_suwp_assigned_brand']);
			update_post_meta( $post_id, '_suwp_assigned_brand', $suwp_assigned_brand_textarea );
		}

		if ( isset($_POST['_suwp_assigned_model']) ) {
			$suwp_assigned_model_textarea = sanitize_text_field($_POST['_suwp_assigned_model']);
			update_post_meta( $post_id, '_suwp_assigned_model', $suwp_assigned_model_textarea );
		}

		if ( isset($_POST['_suwp_serial_limit']) ) {
			$suwp_serial_limit_field = sanitize_text_field($_POST['_suwp_serial_limit']);
			update_post_meta( $post_id, '_suwp_serial_limit', $suwp_serial_limit_field );
		}

		if ( isset($_POST['_suwp_serial_length']) ) {
			$suwp_serial_length_field = sanitize_text_field($_POST['_suwp_serial_length']);
			update_post_meta( $post_id, '_suwp_serial_length', $suwp_serial_length_field );
		}

		if ( isset($_POST['_suwp_api_service_id']) ) {
			$suwp_api_service_id_field = sanitize_text_field($_POST['_suwp_api_service_id']);
			update_post_meta( $post_id, '_suwp_api_service_id', $suwp_api_service_id_field );
		}

		if ( isset($_POST['_suwp_api_service_id_alt']) ) {
			$suwp_api_service_id_alt_field = trim( sanitize_text_field($_POST['_suwp_api_service_id_alt']) );
			update_post_meta( $post_id, '_suwp_api_service_id_alt', $suwp_api_service_id_alt_field );
		}
		
		if ( isset($_POST['_suwp_custom_api1_name']) ) {
			$suwp_custom_api1_name_field = sanitize_text_field($_POST['_suwp_custom_api1_name']);
			update_post_meta( $post_id, '_suwp_custom_api1_name', $suwp_custom_api1_name_field );
		}

		if ( isset($_POST['_suwp_custom_api1_label']) ) {
			$suwp_custom_api1_label_field = sanitize_text_field($_POST['_suwp_custom_api1_label']);
			update_post_meta( $post_id, '_suwp_custom_api1_label', $suwp_custom_api1_label_field );
		}
		
		if ( isset($_POST['_suwp_custom_api1_values']) ) {
			$suwp_custom_api1_values_field = sanitize_text_field($_POST['_suwp_custom_api1_values']);
			update_post_meta( $post_id, '_suwp_custom_api1_values', $suwp_custom_api1_values_field );
		}

		if ( isset($_POST['_suwp_custom_api2_name']) ) {
			$suwp_custom_api2_name_field = sanitize_text_field($_POST['_suwp_custom_api2_name']);
			update_post_meta( $post_id, '_suwp_custom_api2_name', $suwp_custom_api2_name_field );
		}

		if ( isset($_POST['_suwp_custom_api2_label']) ) {
			$suwp_custom_api2_label_field = sanitize_text_field($_POST['_suwp_custom_api2_label']);
			update_post_meta( $post_id, '_suwp_custom_api2_label', $suwp_custom_api2_label_field );
		}
		
		if ( isset($_POST['_suwp_custom_api2_values']) ) {
			$suwp_custom_api2_values_field = sanitize_text_field($_POST['_suwp_custom_api2_values']);
			update_post_meta( $post_id, '_suwp_custom_api2_values', $suwp_custom_api2_values_field );
		}

		if ( isset($_POST['_suwp_custom_api3_name']) ) {
			$suwp_custom_api3_name_field = sanitize_text_field($_POST['_suwp_custom_api3_name']);
			update_post_meta( $post_id, '_suwp_custom_api3_name', $suwp_custom_api3_name_field );
		}

		if ( isset($_POST['_suwp_custom_api3_label']) ) {
			$suwp_custom_api3_label_field = sanitize_text_field($_POST['_suwp_custom_api3_label']);
			update_post_meta( $post_id, '_suwp_custom_api3_label', $suwp_custom_api3_label_field );
		}
		
		if ( isset($_POST['_suwp_custom_api3_values']) ) {
			$suwp_custom_api3_values_field = sanitize_text_field($_POST['_suwp_custom_api3_values']);
			update_post_meta( $post_id, '_suwp_custom_api3_values', $suwp_custom_api3_values_field );
		}

		if ( isset($_POST['_suwp_custom_api4_name']) ) {
			$suwp_custom_api4_name_field = sanitize_text_field($_POST['_suwp_custom_api4_name']);
			update_post_meta( $post_id, '_suwp_custom_api4_name', $suwp_custom_api4_name_field );
		}

		if ( isset($_POST['_suwp_custom_api4_label']) ) {
			$suwp_custom_api4_label_field = sanitize_text_field($_POST['_suwp_custom_api4_label']);
			update_post_meta( $post_id, '_suwp_custom_api4_label', $suwp_custom_api4_label_field );
		}
		
		if ( isset($_POST['_suwp_custom_api4_values']) ) {
			$suwp_custom_api4_values_field = sanitize_text_field($_POST['_suwp_custom_api4_values']);
			update_post_meta( $post_id, '_suwp_custom_api4_values', $suwp_custom_api4_values_field );
		}
		
		if ( isset($_POST['_suwp_not_found']) ) {
			$suwp_not_found_textarea = sanitize_text_field($_POST['_suwp_not_found']);
			update_post_meta( $post_id, '_suwp_not_found', $suwp_not_found_textarea );
		}

		if ( isset($_POST['_suwp_assigned_country']) ) {
			$suwp_assigned_country_textarea = sanitize_text_field($_POST['_suwp_assigned_country']);
			update_post_meta( $post_id, '_suwp_assigned_country', $suwp_assigned_country_textarea );
		}
		
		if ( isset($_POST['_suwp_assigned_network']) ) {
			$suwp_assigned_network_textarea = sanitize_text_field($_POST['_suwp_assigned_network']);
			update_post_meta( $post_id, '_suwp_assigned_network', $suwp_assigned_network_textarea );
		}

		$suwp_hideimei_status_checkbox = isset( $_POST['_suwp_hideimei_status'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_suwp_hideimei_status', sanitize_text_field($suwp_hideimei_status_checkbox) );
		
		$suwp_allowtext_status_checkbox = isset( $_POST['_suwp_allowtext_status'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_suwp_allowtext_status', sanitize_text_field($suwp_allowtext_status_checkbox) );
		
		$suwp_online_status_checkbox = isset( $_POST['_suwp_online_status'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_suwp_online_status', sanitize_text_field($suwp_online_status_checkbox) );
		
		if ( isset($_POST['_suwp_service_notes']) ) {
			$suwp_service_notes_textarea = sanitize_text_field($_POST['_suwp_service_notes']);
			update_post_meta( $post_id, '_suwp_service_notes', $suwp_service_notes_textarea );
		}

		if ( isset($_POST['_suwp_price_adj']) ) {
			$suwp_price_adj_field = sanitize_text_field($_POST['_suwp_price_adj']);
			update_post_meta( $post_id, '_suwp_price_adj', $suwp_price_adj_field );
		}

		if ( isset($_POST['_suwp_price_adj_custom']) ) {
			$suwp_price_adj_custom_field = sanitize_text_field($_POST['_suwp_price_adj_custom']);
			update_post_meta( $post_id, '_suwp_price_adj_custom', $suwp_price_adj_custom_field );
		}

		if ( isset($_POST['_suwp_service_credit']) ) {
			$suwp_service_credit_field = sanitize_text_field($_POST['_suwp_service_credit']);
			update_post_meta( $post_id, '_suwp_service_credit', $suwp_service_credit_field );
		}
	}
	
	public function suwp_add_product_custom_fields() {
		
		global $post;
		
		// translation text
		$text_digits = __('Total digits', 'stockunlocks');
		$text_to_display = __('to display dial', 'stockunlocks');
		$text_bulk_submit = __('Bulk Submit: One Per Line', 'stockunlocks');
		$text_response_email = __('Response Email', 'stockunlocks');
		$text_please_add = __('Add to your address book', 'stockunlocks');
		$text_confirm_email = __('Confirm Email', 'stockunlocks');
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		$options = $plugin_public->suwp_exec_get_current_options();
		
		$from_email = trim( $options['suwp_fromemail_ordersuccess'] );
		
		if( has_term( 'suwp_service', 'product_cat' ) ) {
			
			$extract = get_option( 'suwp_author_info' );
			if ( is_object( $extract ) ) {
				if( !isset($extract->error) ) {
					
					$imei_label = $options['suwp_imei_label'];
					$sn_label = $options['suwp_sn_label'];
					$country_fieldlabel = $options['suwp_country_fieldlabel'];
					$network_fieldlabel = $options['suwp_network_fieldlabel'];
					$brand_fieldlabel = $options['suwp_brand_fieldlabel'];
					$model_fieldlabel = $options['suwp_model_fieldlabel'];
					$mep_fieldlabel = $options['suwp_mep_fieldlabel'];
					$kbh_fieldlabel = $options['suwp_kbh_fieldlabel'];
					$activation_fieldlabel = $options['suwp_activation_fieldlabel'];
					$emailresponse_fieldlabel = $options['suwp_emailresponse_fieldlabel'];
					$emailconfirm_fieldlabel = $options['suwp_emailconfirm_fieldlabel'];
					$msg_payment_email = $options['suwp_payment_email_msg'];
					$deliverytime_fieldlabel = $options['suwp_deliverytime_fieldlabel'];
					$code_fieldlabel = $options['suwp_code_fieldlabel'];
					$response_email_display = $options['suwp_manage_response_email_display'];
					
					$serial_limit = get_field('_suwp_serial_limit', $post->ID );
					$is_imei = true;
					$is_mep = get_field('_suwp_is_mep', $post->ID );
					$is_network = get_field('_suwp_is_network', $post->ID );
					$is_model = get_field('_suwp_is_model', $post->ID );
					$is_pin = get_field('_suwp_is_pin', $post->ID );
					$is_rm_type = get_field('_suwp_is_rm_type', $post->ID );
					$is_kbh = get_field('_suwp_is_kbh', $post->ID );
					$is_reference = get_field('_suwp_is_reference', $post->ID );
					$is_service_tag = get_field('_suwp_is_service_tag', $post->ID );
					$is_activation = get_field('_suwp_is_activation', $post->ID );

					$hideimei_status = get_field('_suwp_hideimei_status', $post->ID );
					$hideimei = false;
					$allowtext_status = get_field('_suwp_allowtext_status', $post->ID );
					$allowtext = false;
					$imei_fieldlabel = $options['suwp_imei_fieldlabel'];
					$sn_fieldlabel = $options['suwp_sn_fieldlabel'];
					$suwp_serial_length = get_field('_suwp_serial_length', $post->ID );

					if ( suwp_field_replace_preg_match($imei_fieldlabel) ){
						$imei_fieldlabel = suwp_field_replace_preg_match($imei_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($sn_fieldlabel) ){
						$sn_fieldlabel = suwp_field_replace_preg_match($sn_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($country_fieldlabel) ){
						$country_fieldlabel = suwp_field_replace_preg_match($country_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($network_fieldlabel) ){
						$network_fieldlabel = suwp_field_replace_preg_match($network_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($brand_fieldlabel) ){
						$brand_fieldlabel = suwp_field_replace_preg_match($brand_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($model_fieldlabel) ){
						$model_fieldlabel = suwp_field_replace_preg_match($model_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($mep_fieldlabel) ){
						$mep_fieldlabel = suwp_field_replace_preg_match($mep_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($kbh_fieldlabel) ){
						$kbh_fieldlabel = suwp_field_replace_preg_match($kbh_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($activation_fieldlabel) ){
						$activation_fieldlabel = suwp_field_replace_preg_match($activation_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($emailresponse_fieldlabel) ){
						$emailresponse_fieldlabel = suwp_field_replace_preg_match($emailresponse_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($emailconfirm_fieldlabel) ){
						$emailconfirm_fieldlabel = suwp_field_replace_preg_match($emailconfirm_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($deliverytime_fieldlabel) ){
						$deliverytime_fieldlabel = suwp_field_replace_preg_match($deliverytime_fieldlabel);
					}
					if ( suwp_field_replace_preg_match($code_fieldlabel) ){
						$code_fieldlabel = suwp_field_replace_preg_match($code_fieldlabel);
					}

					if ( $suwp_serial_length === '' ) {
						$suwp_serial_length = '&#8734;'; // &inifn; &#8734;
					}
					$replace = array(
						'{$charlength}' => $suwp_serial_length,
						'{$adminemail}' => $from_email,
					);

					$imei_text = suwp_string_replace_assoc( $replace, $imei_fieldlabel );
					$sn_text = suwp_string_replace_assoc( $replace, $sn_fieldlabel );
					$emailresponse_text = suwp_string_replace_assoc( $replace, $emailresponse_fieldlabel );
					
					$serial_text = $imei_text;

					if ( $hideimei_status == 'yes') {
						$hideimei = true;
					}

					if ( $allowtext_status == 'yes') {
						$allowtext = true;
						$serial_text = $sn_text;
						$is_imei = false;
					}

					$count_length = true;
					
					if ( $suwp_serial_length === '' ) {
						$count_length = false;
						$serial_text = $sn_text;
						$is_imei = false;
					} else {
						if ( $suwp_serial_length < 15 || $suwp_serial_length > 15 ) {
							$serial_text = $sn_text;
							$is_imei = false;
						}
					}

					if ( $serial_limit === '1' && $is_imei ) {
						$serial_text = $imei_label . ':';
					} elseif ( $serial_limit === '1' && !$is_imei ) {
						$serial_text = $sn_label . ':';
					}

					$serial_text_entry = '<textarea cols="40" rows="5" wrap="soft" name="suwp-imei-values"></textarea>';
					if ( $serial_limit === '1') {
						$serial_text_entry = '<input type="text" size="40" name="suwp-imei-values" value="" />';
					}

					$brand_values = trim( get_field('_suwp_assigned_brand', $post->ID ) );
					$brand_groups = array();
					$brand_drop = false;
					$model_values = trim( get_field('_suwp_assigned_model', $post->ID ) );
					$model_groups = array();
					$model_drop = false;
					$brand_model_drop = false;
					$is_brand_model = false;

					$country_values = trim( get_field('_suwp_assigned_country', $post->ID ) );
					$country_groups = array();
					$country_drop = false;
					$network_values = trim( get_field('_suwp_assigned_network', $post->ID ) );
					$network_groups = array();
					$network_drop = false;
					$country_network_drop = false;
					$is_country_network = false;

					// error_log('RETRIEVING BOTH THE BRAND/MODEL AND COUNTRY/NETWORK VALUES, $brand_values = ' . print_r($brand_values,true) . ', $model_values = ' . print_r($model_values, true) . ', $country_values = ' . print_r($country_values,true) . ', $network_values = ' . print_r($network_values,true) );
					
					if ( $brand_values != '' && $model_values != '' ) {
						if (strpos($brand_values, '::') !== false) {
							$brand_groups = ( explode("::",$brand_values) );
							$brand_drop = true;
						}
						if (strpos($model_values, '::') !== false) {
							$model_groups = ( explode("::",$model_values) );
							$model_drop = true;
						}
						if ($brand_drop && $model_drop) {
							$brand_model_drop = true;
						}
					}

					if ( $country_values != '' && $network_values != '' ) {
						if (strpos($country_values, '::') !== false) {
							$country_groups = ( explode("::",$country_values) );
							$country_drop = true;
						}
						if (strpos($network_values, '::') !== false) {
							$network_groups = ( explode("::",$network_values) );
							$network_drop = true;
						}
						if ($country_drop && $network_drop) {
							$country_network_drop = true;
						}
					}
					
					// >>> error_log('CHECKING OUT THE VALUES FIELD: $brand_values = ' . $brand_values .  ', $model_values = ' . $model_values . ', $country_values = ' . $country_values .', $network_values = ' . $network_values );
					
					// >>> error_log('CHECKING OUT THE FIRST ARRAY VALUES FIELD: $brand_groups = ' . print_r($brand_groups,true).  ', $model_groups = ' . print_r($model_groups,true) . ', $country_groups = ' . print_r($country_groups,true) .', $network_groups = ' . print_r($network_groups,true) );
					
					$api1_label = trim( get_field('_suwp_custom_api1_label', $post->ID ) );
					$api1_name = trim( get_field('_suwp_custom_api1_name', $post->ID ) );
					$api1_values = trim( get_field('_suwp_custom_api1_values', $post->ID ) );
					$is_api1 = false;
					$is_api1_drop = false;

					$api1_selection = array();

					$api2_label = trim( get_field('_suwp_custom_api2_label', $post->ID ) );
					$api2_name = trim( get_field('_suwp_custom_api2_name', $post->ID ) );
					$api2_values = trim( get_field('_suwp_custom_api2_values', $post->ID ) );
					$is_api2 = false;
					$is_api2_drop = false;
					$api2_selection = array();

					$api3_label = trim( get_field('_suwp_custom_api3_label', $post->ID ) );
					$api3_name = trim( get_field('_suwp_custom_api3_name', $post->ID ) );
					$api3_values = trim( get_field('_suwp_custom_api3_values', $post->ID ) );
					$is_api3 = false;
					$is_api3_drop = false;
					$api3_selection = array();

					$api4_label = trim( get_field('_suwp_custom_api4_label', $post->ID ) );
					$api4_name = trim( get_field('_suwp_custom_api4_name', $post->ID ) );
					$api4_values = trim( get_field('_suwp_custom_api4_values', $post->ID ) );
					$is_api4 = false;
					$is_api4_drop = false;
					$api4_selection = array();

					// >>> error_log('CHECKING OUT THE VALUES FIELD: $api1_values = ' . $api1_values .  ', $api2_values = ' . $api2_values . ', $api3_values = ' . $api3_values .', $api4_values = ' . $api4_values );
					
					// since v1.9.5, passing custom api values 
					if ( $api1_label != '' && $api1_name != '' ) {
						$is_api1 = true;
					}
					if ( $api1_label != '' && $api1_values != '' ) {
						if (strpos($api1_values, '::') !== false) {
							$api1_selection = ( explode("::",$api1_values) );
							$is_api1_drop = true;
						}
					}
					if ( $api2_label != '' && $api2_name != '' ) {
						$is_api2 = true;
					}
					if ( $api2_label != '' && $api2_values != '' ) {
						if (strpos($api2_values, '::') !== false) {
							$api2_selection = ( explode("::",$api2_values) );
							$is_api2_drop = true;
						}
					}
					if ( $api3_label != '' && $api3_name != '' ) {
						$is_api3 = true;
					}
					if ( $api3_label != '' && $api3_values != '' ) {
						if (strpos($api3_values, '::') !== false) {
							$api3_selection = ( explode("::",$api3_values) );
							$is_api3_drop = true;
						}
					}
					if ( $api4_label != '' && $api4_name != '' ) {
						$is_api4 = true;
					}
					if ( $api4_label != '' && $api4_values != '' ) {
						if (strpos($api4_values, '::') !== false) {
							$api4_selection = ( explode("::",$api4_values) );
							$is_api4_drop = true;
						}
					}
					
					// >>> error_log('CHECKING OUT THE ARRAY VALUES FIELD: $api1_values = ' . print_r($api1_selection,true).  ', $api2_values = ' . print_r($api2_selection,true) . ', $api3_values = ' . print_r($api3_selection,true) .', $is_api4_selection = ' . print_r($api4_selection,true) );
					
					$yes = 'Required';
					$no = 'None';
					
					if ($is_model == $yes || $brand_model_drop) {
						$is_brand_model = true;
					}

					if ($is_network == $yes || $country_network_drop) {
						$is_country_network = true;
					}

					echo ' <table class="variations" cellspacing="0">
						<tbody>';
					
					if ($is_api1 && !$is_api1_drop) {
							
						echo '<tr>
									<td class="value">
									
									<div class="suwp-group">
										<label for="api1-label" name="suwp-api1-label">'
										. $api1_label .
										'</label>
									</div>
									
									<div class="suwp-group">
										<input type="text" name="suwp-api1-name" value="" />
									</div>
									
									</td>
								</tr>';
					}

					if ($is_api1_drop) {
							
						echo '<tr>
							   <td class="value">
							   
							 <div class="suwp-group">
									 <label for="api1-label" name="suwp-api1-label">'
										 . $api1_label . 
									 '</label>
							 </div>
							 
							 <div class="suwp-group">
								   <select name="suwp-api1-name">
								   <option value="">
								   		-----------------
								   </option>';

								   foreach($api1_selection as $key => $value):
										if( trim($value) != '' ) {
											echo '<option value="'.$value.'">'.$value.'</option>';
										}
									endforeach;
												 
								 echo '</select>
							 </div>
								   
							   </td>
							 </tr>';
					}

					if ($is_api2 && !$is_api2_drop) {
							
						echo '<tr>
									<td class="value">
									
									<div class="suwp-group">
										<label for="api2-label" name="suwp-api2-label">'
										. $api2_label .
										'</label>
									</div>
									
									<div class="suwp-group">
										<input type="text" name="suwp-api2-name" value="" />
									</div>
									
									</td>
								</tr>';
					}

					if ($is_api2_drop) {
							
						echo '<tr>
							   <td class="value">
							   
							 <div class="suwp-group">
									 <label for="api2-label" name="suwp-api2-label">'
										 . $api2_label . 
									 '</label>
							 </div>
							 
							 <div class="suwp-group">
								   <select name="suwp-api2-name">
								   <option value="">
								   		-----------------
								   </option>';

								   foreach($api2_selection as $key => $value):
										if( trim($value) != '' ) {
											echo '<option value="'.$value.'">'.$value.'</option>';
										}
									endforeach;
												 
								 echo '</select>
							 </div>
								   
							   </td>
							 </tr>';
					}

					if ($is_api3 && !$is_api3_drop) {
							
						echo '<tr>
									<td class="value">
									
									<div class="suwp-group">
										<label for="api3-label" name="suwp-api3-label">'
										. $api3_label .
										'</label>
									</div>
									
									<div class="suwp-group">
										<input type="text" name="suwp-api3-name" value="" />
									</div>
									
									</td>
								</tr>';
					}

					if ($is_api3_drop) {
							
						echo '<tr>
							   <td class="value">
							   
							 <div class="suwp-group">
									 <label for="api3-label" name="suwp-api3-label">'
										 . $api3_label . 
									 '</label>
							 </div>
							 
							 <div class="suwp-group">
								   <select name="suwp-api3-name">
								   <option value="">
										-----------------
								   </option>';

								   foreach($api3_selection as $key => $value):
										if( trim($value) != '' ) {
											echo '<option value="'.$value.'">'.$value.'</option>';
										}
									endforeach;
												 
								 echo '</select>
							 </div>
								   
							   </td>
							 </tr>';
					}

					if ($is_api4 && !$is_api4_drop) {
							
						echo '<tr>
									<td class="value">
									
									<div class="suwp-group">
										<label for="api4-label" name="suwp-api4-label">'
										. $api4_label .
										'</label>
									</div>
									
									<div class="suwp-group">
										<input type="text" name="suwp-api4-name" value="" />
									</div>
									
									</td>
								</tr>';
					}

					if ($is_api4_drop) {
							
						echo '<tr>
							   <td class="value">
							   
							 <div class="suwp-group">
									 <label for="api4-label" name="suwp-api4-label">'
										 . $api4_label . 
									 '</label>
							 </div>
							 
							 <div class="suwp-group">
								   <select name="suwp-api4-name">
								   <option value="">
								   		-----------------
								   </option>';

								   foreach($api4_selection as $key => $value):
										if( trim($value) != '' ) {
											echo '<option value="'.$value.'">'.$value.'</option>';
										}
									endforeach;
												 
								 echo '</select>
							 </div>
								   
							   </td>
							 </tr>';
					}

					if ($is_country_network) {
						  
					  echo '<tr>
							  <td class="value">
							  
							<div class="suwp-group">
									<label for="country-id" name="suwp-country-id-label">'
										. $country_fieldlabel .
									'</label>
							</div>
							
							<div class="suwp-group">
								  <select name="suwp-country-id">';
								  
									// get all our country list
									// collect all countries related to this Product
									// create the select option for that list
									$option = '<option>-----------------</option>';
									
									// echo the new option	
									echo $option;
													
								echo '</select>
							</div>
								
							  </td>
							</tr>';
							
					  echo '<tr>
							  <td class="value">
							  
							<div class="suwp-group">
									<label for="network-id" name="suwp-network-id-label">'
										. $network_fieldlabel . 
									'</label>
							</div>
							
							<div class="suwp-group">
								  <select name="suwp-network-id">';
								  
									// get all our network list
									// collect all networks related to this Product
									// create the select option for that list
									$option = '<option>-----------------</option>';
									
									// echo the new option	
									echo $option;
														
								echo '</select>
							</div>
								  
							  </td>
							</tr>';
					}
					
					if ($is_brand_model) {
							
					  echo '<tr>
							  <td class="value">
							  
							<div class="suwp-group">
									<label for="brand-id" name="suwp-brand-id-label">'
										. $brand_fieldlabel . 
									'</label>
							</div>
							
							<div class="suwp-group">
								  <select name="suwp-brand-id">';
								  
									// get all our brand list
								 
									// collect all brands related to this Product
									// create the select option for that list
									$option = '<option>-----------------</option>';
									
									// echo the new option	
									echo $option;
									
								echo '</select>
							</div>
								  
							  </td>
						 </tr>';
							
					echo '<tr>
							  <td class="value">
							  
							<div class="suwp-group">
									<label for="model-id" name="suwp-model-id-label">'
										. $model_fieldlabel . 
									'</label>
							</div>
							
							<div class="suwp-group">
								  <select name="suwp-model-id">';
								  
									// collect all models related to this Product
									// create the select option for that list
									$option = '<option>-----------------</option>';
									
									// echo the new option	
									echo $option;
												
								echo '</select>
							</div>
								  
							  </td>
							</tr>';
					}
				
					if ($is_mep == $yes) {
							
					   echo '<tr>
							  <td class="value">
							  
							<div class="suwp-group">
									<label for="mep-id" name="suwp-mep-id-label">'
										. $mep_fieldlabel . 
									'</label>
							</div>
							
							<div class="suwp-group">
								  <select name="suwp-mep-id">';
								  
									// get all our mep list
									// collect all meps related to this Product
									// create the select option for that list
									$option = '
										<option value="">
											-----------------
										</option>';
									
									// echo the new option	
									echo $option;
												
								echo '</select>
							</div>
								  
							  </td>
							</tr>';
					}
					
				if ( !$hideimei ) {

					echo '<tr>
							<td class="value">
			
							<div class="suwp-group">
									<label for="imei-values" name="suwp-imei-values-label">'
										. $serial_text . 
									'</label>
							</div>
							
							<div class="suwp-group">'
								. $serial_text_entry .
							'</div>
							
							</td>
							</tr>';
					}

				if ($is_kbh == $yes) {
						
					echo '<tr>
								<td class="value">
								
								<div class="suwp-group">
									<label for="kbh-values" name="suwp-kbh-values-label">'
										. $kbh_fieldlabel . 
									'</label>
								</div>
								
								<div class="suwp-group">
									<input type="text" name="suwp-kbh-values" value="" />
								</div>
								
								</td>
							</tr>';
				}
				
				if ($is_activation == $yes) {
							
					echo '<tr>
								<td class="value">
								
								<div class="suwp-group">
									<label for="activation-number" name="suwp-activation-number-label">'
										. $activation_fieldlabel . 
									'</label>
								</div>
								
								<div class="suwp-group">
									<input type="text" name="suwp-activation-number" value="" />
								</div>
								
								</td>
								
							</tr>';
				}
				
				if ($response_email_display == '0') {

					echo '<tr>
								<td class="value">
			
								<div class="suwp-group">
									<label for="email-response" name="suwp-email-response-label">'
										. $emailresponse_text . 
									'</label>
								</div>
								
								<div class="suwp-group">
									<input type="email" name="suwp-email-response" id="email-response" value="" />
								</div>
								
								</td>
							</tr>';
							
					echo '<tr>
								<td class="value">
								
								<div class="suwp-group">
									<label for="email-confirm" name="suwp-email-confirm-label">'
										. $emailconfirm_fieldlabel . 
									'</label>
								</div>
								
								<div class="suwp-group">
									<input type="email" name="suwp-email-confirm" id="email-confirm" value="" />
								</div> 
							   
								</td>
							</tr>';
							
					echo '<tr>
								<td class="value">
								
								<div class="suwp-group">
									<input type="checkbox" name="suwp-use-payment-email" id="suwp-payment-email" value="1"> '. $msg_payment_email .
								'</div>
								
								</td>
							</tr>';
				} else {
					// manually set to use the payment email address for replies
					echo '<tr>
							<td class="value">
							
							<div class="suwp-group">
								<input type="hidden" name="suwp-use-payment-email-hidden" id="suwp-payment-email-hidden" value="1">
								<input type="hidden" name="suwp-use-payment-email" id="suwp-payment-email" value="1">
							</div>
							
							</td>
					</tr>';
				}

					do_action('suwp_after_product_custom_fields');
			
					echo '
							<tr>
								<td class="value">
									<input type="hidden" name="suwp-brand-model-drop" value="' . $brand_model_drop . '" />
									<input type="hidden" name="suwp-country-network-drop" value="' . $country_network_drop . '" />
									<input type="hidden" name="_suwp-qty-sent" value="0" />
									<input type="hidden" name="_suwp-qty-done" value="0" />
									<input type="hidden" name="_suwp-model-name" value="" />
									<input type="hidden" name="_suwp-network-name" value="" />
									<input type="hidden" name="_suwp-mep-name" value="" />
									<input type="hidden" name="suwp-is-hideimei" value="' . $hideimei . '" />
									<input type="hidden" name="suwp-is-allow-text" value="' . $allowtext . '" />
									<input type="hidden" name="suwp-serial-length" value="' . $suwp_serial_length . '" />
									<input type="hidden" name="suwp-serial-limit" value="' . $serial_limit . '" />
									<input type="hidden" name="suwp-is-count-length" value="' . $count_length . '" />
									<input type="hidden" name="suwp-is-imei" value="' . $is_imei . '" />
									<input type="hidden" name="suwp-is-ap1" value="' . $is_api1 . '" />
									<input type="hidden" name="suwp-is-ap2" value="' . $is_api2 . '" />
									<input type="hidden" name="suwp-is-ap3" value="' . $is_api3 . '" />
									<input type="hidden" name="suwp-is-ap4" value="' . $is_api4 . '" />
									<input type="hidden" name="suwp-is-mep" value="' . $is_mep . '" />
									<input type="hidden" name="suwp-is-country-network" value="' . $is_country_network . '" />
									<input type="hidden" name="suwp-is-brand-model" value="' . $is_brand_model . '" />
									<input type="hidden" name="suwp-is-pin" value="' . $is_pin . '" />
									<input type="hidden" name="suwp-is-rm-ype" value="' . $is_rm_type . '" />
									<input type="hidden" name="suwp-is-kbh" value="' . $is_kbh . '" />
									<input type="hidden" name="suwp-is-reference" value="' . $is_reference . '" />
									<input type="hidden" name="suwp-is-service-tag" value="' . $is_service_tag . '" />
									<input type="hidden" name="suwp-is-activation" value="' . $is_activation . '" />
								</td>
							</tr>

						  </tbody>
					</table>';
				
				}
			}
		}
	}
	
	public function suwp_custom_field_product_bulk_edit() {
		?>
			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php _e('Auto Adjust Price', 'stockunlocks'); ?></span>
					<span class="input-text-wrap">
						<select name="_change_suwp_aap">
						<?php
							$options = array(
								''  => __( '— No change —', 'stockunlocks' ),
								'disabled' => __( 'Disabled', 'stockunlocks' ),
								'custom' => __( 'Custom', 'stockunlocks' ),
								'global' => __( 'Global', 'stockunlocks' ),
							);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>
			</div>
		<?php
	}
	
	public function suwp_save_custom_field_product_bulk_edit( $product ){
		if ( $product->is_type('simple') || $product->is_type('external') ){
			$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
			$slugs = array();
			$terms = get_the_terms( $product_id, 'product_cat' );
			if ( is_array($terms) ){
				foreach ( $terms as $term ) {
				$slugs[] = $term->slug;
				}
			}
			if ( in_array('suwp_service', $slugs, TRUE) ) {
				if ( isset( $_REQUEST['_change_suwp_aap'] ) ) {
					if ( !empty( $_REQUEST['_change_suwp_aap'] ) ) {
						error_log( '....... suwp_custom_field_product_bulk_edit: ' . $_REQUEST['_change_suwp_aap'] );
						update_post_meta( $product_id, '_suwp_price_adj', sanitize_text_field( $_REQUEST['_change_suwp_aap'] ) );
					}
				}
			}
		}
	}
	
	public function suwp_brandmodel_populate_values() {

		global $wpdb;
		
		$brands_models = array();
		
		// collect the posted values from the submitted form
	
		$brand_model_drop = key_exists('brand_model_drop', $_POST) ? $_POST['brand_model_drop'] : false;
		$brand = key_exists('brand', $_POST) ? $_POST['brand'] : false;
		$model = key_exists('model', $_POST) ? $_POST['model'] : false;
		$post_id = key_exists('post_id', $_POST) ? $_POST['post_id'] : false;
		
		if ( $brand_model_drop ) {
			
			$brand_values = trim( get_field('_suwp_assigned_brand', $post_id ) );
			$brand_groups = array();
			$model_values = trim( get_field('_suwp_assigned_model', $post_id ) );
			$model_groups = array();

			if ( $brand_values != '' && $model_values != '' ) {
				if (strpos($brand_values, '::') !== false) {
					$brand_groups = ( explode("::",$brand_values) );
				}
				if (strpos($model_values, '::') !== false) {
					$model_groups = ( explode("::",$model_values) );
				}
			}

			$brand_groups = array_filter($brand_groups);
			$model_groups = array_filter($model_groups);
			
			// loop over each brand list
			foreach ($brand_groups as $key_brand => &$list_brand):
				$current_brand_key = intval($key_brand)+1;
				// error_log('CHECKING OUT THE $brand_groups, $key = '. $current_brand_key . ', $list_brand = ' . print_r($list_brand,true) );
			
				foreach( $model_groups as $key_model => &$list_model ):
					$current_model_key = intval($key_model)+1;
					if ($current_brand_key === $current_model_key) {
						// error_log('CHECKING OUT THE $model_groups, $key = '. $current_model_key . ', $list_model = ' . print_r($list_model,true) );
						if (strpos($list_model, ',,') !== false) {
							$temp_model_key = 0;
							$model_values = ( explode(",,",$list_model) );
							foreach( $model_values as &$model_name ) {
								$temp_model_key++;
								$list_0_name = html_entity_decode( $list_brand );
								$list_1_name = html_entity_decode( $model_name );
								
								$brands_models[$list_0_name][$list_1_name][] = $current_model_key . $temp_model_key;
							}
						}
					}
					
				endforeach;
				
			endforeach;

			// error_log('CHECKING OUT THE $brands_models = ' . print_r($brands_models,true) );

		} else {

			// collect all brands related to this Product
			$brands = $wpdb->get_results( $wpdb->prepare( "SELECT source_id, name FROM " . $wpdb->prefix. "suwp_service_brand WHERE product_id=%d ORDER BY name ASC", $post_id ) );
			
			// error_log( '$brands >>>>>>>>>>>>>>>>>>>' . print_r( $brands, true ) );
			
			// loop over each brand list
			foreach( $brands as &$list_brand ):
				
				// collect all models related to this Product
				// This will change based on the selected Brand above. So, need to match on the brand_id
				$models = $wpdb->get_results( $wpdb->prepare( "SELECT source_id, name FROM " . $wpdb->prefix. "suwp_service_model WHERE brand_id=%d AND product_id=%d ORDER BY name ASC", $list_brand->source_id, $post_id ) );
				
				// error_log( '$models >>>>>>>>>>>>>>>>>>>' . print_r( $models, true ) );
				// loop over each model list
				foreach( $models as &$list_model ):
				
					$list_0_name = html_entity_decode( $list_brand->name );
					$list_1_name = html_entity_decode( $list_model->name );
					
					$brands_models[$list_0_name][$list_1_name][] = $list_model->source_id;
					
				endforeach;

			endforeach;
		}

		// setup the initial array that will be returned to the the client side script as a JSON object.
	
		$return_array = array(
				'brands' => array_keys($brands_models),
				'models' => array(),
				'current_brand' => false,
				'current_model' => false
			);
		
		// populate the $return_array with the necessary values
	
		if ( $brand ) {
			$return_array['current_brand'] = $brand;
			// $return_array['models'] = array_keys($brands_models[$brand]);
			if( array_key_exists($brand, $brands_models) ) {
				$return_array['models'] = $brands_models[$brand];
			}
			if ( $model ) {
				$return_array['current_model'] = $model;
			}
	
			// encode the $return_array as a JSON object and echo it
			echo json_encode( $return_array );
			wp_die();
		}
	}
	
	public function suwp_countrynetwork_populate_values() {
		
		global $wpdb;
		
		$countries_networks = array();
		
		// collect the posted values from the submitted form
	
		$country_network_drop = key_exists('country_network_drop', $_POST) ? $_POST['country_network_drop'] : false;
		$country = key_exists('country', $_POST) ? $_POST['country'] : false;
		$network = key_exists('network', $_POST) ? $_POST['network'] : false;
		$post_id = key_exists('post_id', $_POST) ? $_POST['post_id'] : false;
		
		if ( $country_network_drop ) {
			
			$country_values = trim( get_field('_suwp_assigned_country', $post_id ) );
			$country_groups = array();
			$network_values = trim( get_field('_suwp_assigned_network', $post_id ) );
			$network_groups = array();

			if ( $country_values != '' && $network_values != '' ) {
				if (strpos($country_values, '::') !== false) {
					$country_groups = ( explode("::",$country_values) );
				}
				if (strpos($network_values, '::') !== false) {
					$network_groups = ( explode("::",$network_values) );
				}
			}

			$country_groups = array_filter($country_groups);
			$network_groups = array_filter($network_groups);

			// error_log('CHECKING OUT THE $country_groups VALUES = ' . print_r($country_groups,true) );

			// loop over each country list
			foreach ($country_groups as $key_country => &$list_country):
				$current_country_key = intval($key_country)+1;
				// error_log('CHECKING OUT THE $country_groups, $key = '. $current_country_key . ', $list_country = ' . print_r($list_country,true) );
			
				foreach( $network_groups as $key_network => &$list_network ):
					$current_network_key = intval($key_network)+1;
					if ($current_country_key === $current_network_key) {
						// error_log('CHECKING OUT THE $network_groups, $key = '. $current_network_key . ', $list_network = ' . print_r($list_network,true) );
						if (strpos($list_network, ',,') !== false) {
							$temp_name_key = 0;
							$network_values = ( explode(",,",$list_network) );
							foreach( $network_values as &$network_name ) {
								$temp_name_key++;
								$list_0_name = html_entity_decode( $list_country );
								$list_1_name = html_entity_decode( $network_name );
								
								$countries_networks[$list_0_name][$list_1_name][] = $current_network_key . $temp_name_key;
							}
						}
					}
					
				endforeach;
				
			endforeach;

			// error_log('CHECKING OUT THE $countries_networks = ' . print_r($countries_networks,true) );

		} else {

			// collect all countries related to this Product
			$countries = $wpdb->get_results( $wpdb->prepare( "SELECT source_id, name FROM " . $wpdb->prefix. "suwp_network_country WHERE product_id=%d ORDER BY name ASC", $post_id ) );
			
			// loop over each country list
			foreach( $countries as &$list_country ):
				
				// collect all networks related to this Product
				// This will change based on the selected Country above. So, need to match on the country_id
				$networks = $wpdb->get_results( $wpdb->prepare( "SELECT source_id, name FROM " . $wpdb->prefix. "suwp_network WHERE country_id=%d AND product_id=%d ORDER BY name ASC", $list_country->source_id, $post_id ) );
				
				// loop over each network list
				foreach( $networks as &$list_network ):
					
					$list_0_name = html_entity_decode( $list_country->name );
					$list_1_name = html_entity_decode( $list_network->name );
					
					$countries_networks[$list_0_name][$list_1_name][] = $list_network->source_id;
					
				endforeach;

			endforeach;
		}

		// setup the initial array that will be returned to the the client side script as a JSON object.
	
		$return_array = array(
				'countries' => array_keys( $countries_networks ),
				'networks' => array(),
				'current_country' => false,
				'current_network' => false
			);
		
		// populate the $return_array with the necessary values
	
		if( $country ) {
			$return_array['current_country'] = $country;
			if( array_key_exists( $country, $countries_networks ) ) {
				$return_array['networks'] = $countries_networks[$country];
			}
			if( $network ) {
				$return_array['current_network'] = $network;
			}
	
			// encode the $return_array as a JSON object and echo it
			echo json_encode( $return_array );
			wp_die();
		}
	}
	
	public function suwp_mep_populate_values() {
		
		global $wpdb;
		
		$provider_meps = array();
		
		// collect the posted values from the submitted form
	
		$mep = key_exists('mep', $_POST) ? $_POST['mep'] : false;
		$post_id = key_exists('post_id', $_POST) ? $_POST['post_id'] : false;
		
		// since this is a service [or Product], we must find the related API provider
		// MEPs are not tied to services. The entire list is pulled down from the Provider.
		$provider_id = get_post_meta( $post_id, '_suwp_api_provider', true );
		
		// collect all meps related to this Product
		$meps = $wpdb->get_results( $wpdb->prepare( "SELECT source_id, name FROM " . $wpdb->prefix. "suwp_provider_mepname WHERE post_id=%d ORDER BY name ASC", $provider_id ) );
		
		// error_log( '$simmple meps >>>>>>>>>>>>>>>>>> ' . print_r( $meps, true) );
		
		// loop over each mep list
		foreach( $meps as &$list_mep ):
			
			// error_log( '$list_mep->name >>>>>>>>>>>>>>>>>> ' . print_r( $list_mep->name, true) );
		
			$list_0_name = html_entity_decode( $list_mep->name );
			
			$provider_meps[$list_0_name][] = $list_mep->source_id;
			
		endforeach;
	
		// error_log( '$provider_meps >>>>>>>>>>>>>>>>>> ' . print_r( $provider_meps, true) );
		
		// setup the initial array that will be returned to the the client side script as a JSON object.
	
		$return_array = array(
				'meps' => array(),
				'current_mep' => false
			);
		
		// populate the $return_array with the necessary values
	
		if ( $provider_meps ) {
			$return_array['current_mep'] = $mep;
			$return_array['meps'] = $provider_meps;
	
			// encode the $return_array as a JSON object and echo it
			echo json_encode($return_array);
			wp_die();
		}
	}
	
	public function suwp_cron_exec_2minutes() {
		
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( $cron_run == '2min' ) {
			error_log('STOCKUNLOCKS CRON RUNNING EVERY 2 MINUTES, OK!');
			$this->suwp_run_cron_run( $cron_run );
		} else {
			// error_log('STOCKUNLOCKS CRON NOT RUNNING EVERY 5 MINUTES, BUT EVERY : ' . $cron_run);
		}
    
	}
	
	public function suwp_cron_exec_5minutes() {
		
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( $cron_run == '5min' ) {
			error_log('STOCKUNLOCKS CRON RUNNING EVERY 5 MINUTES, OK!');
			$this->suwp_run_cron_run( $cron_run );
		} else {
			// error_log('STOCKUNLOCKS CRON NOT RUNNING EVERY 5 MINUTES, BUT EVERY : ' . $cron_run);
		}
    
	}

	public function suwp_cron_exec_15minutes() {
		
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		 
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( $cron_run == '15min' ) {
			error_log('STOCKUNLOCKS CRON RUNNING EVERY 15 MINUTES, OK!');
			$this->suwp_run_cron_run( $cron_run );
		} else {
			// error_log('STOCKUNLOCKS CRON NOT RUNNING EVERY 15 MINUTES, BUT EVERY : ' . $cron_run);
		}
		
	}
	
	public function suwp_cron_exec_30minutes() {
		
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		 
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( $cron_run == '30min' ) {
			error_log('STOCKUNLOCKS CRON RUNNING EVERY 30 MINUTES, OK!');
			$this->suwp_run_cron_run( $cron_run );
		} else {
			// error_log('STOCKUNLOCKS CRON NOT RUNNING EVERY 30 MINUTES, BUT EVERY : ' . $cron_run);
		}
		
	}
	
	public function suwp_cron_exec_1hour() {
		
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		 
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( $cron_run == '1hr' ) {
			error_log('STOCKUNLOCKS CRON RUNNING EVERY 1 HOUR, OK!');
			$this->suwp_run_cron_run( $cron_run );
		} else {
			// error_log('STOCKUNLOCKS CRON NOT RUNNING EVERY 1 HOUR, BUT EVERY : ' . $cron_run);
		}
		
	}
	
	public function suwp_cron_exec_3hours() {
	
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		
		$this->suwp_run_sutasks();
		$this->suwp_run_cron();
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( $cron_run == '3hrs' ) {
			error_log('STOCKUNLOCKS CRON RUNNING EVERY 3 HOURS, OK!');
			$this->suwp_run_cron_run( $cron_run );
		} else {
			// error_log('STOCKUNLOCKS CRON NOT RUNNING EVERY 3 HOURS, BUT EVERY : ' . $cron_run);
		}
		
	}
	
	public function suwp_product_exec_1hour() {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_product_sync_run_id'];
		
		if ( $cron_run == '1hr' ) {
			error_log('STOCKUNLOCKS PRODUCT SYNC RUNNING EVERY HOUR, OK!');
			$this->suwp_run_product_run( $cron_run );
		}
		
	}
	
	public function suwp_product_exec_2hours() {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_product_sync_run_id'];
		
		if ( $cron_run == '2hrs' ) {
			error_log('STOCKUNLOCKS PRODUCT SYNC RUNNING EVERY 2 HOURS, OK!');
			$this->suwp_run_product_run( $cron_run );
		}
		
	}
	
	public function suwp_product_exec_3hours() {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_product_sync_run_id'];
		
		if ( $cron_run == '3hrs' ) {
			error_log('STOCKUNLOCKS PRODUCT SYNC RUNNING EVERY 3 HOURS, OK!');
			$this->suwp_run_product_run( $cron_run );
		}
		
	}
	
	public function suwp_product_exec_4hours() {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_product_sync_run_id'];
		
		if ( $cron_run == '4hrs' ) {
			error_log('STOCKUNLOCKS PRODUCT SYNC RUNNING EVERY 4 HOURS, OK!');
			$this->suwp_run_product_run( $cron_run );
		}
		
	}
	
	public function suwp_product_exec_5hours() {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_product_sync_run_id'];
		
		if ( $cron_run == '5hrs' ) {
			error_log('STOCKUNLOCKS PRODUCT SYNC RUNNING EVERY 5 HOURS, OK!');
			$this->suwp_run_product_run( $cron_run );
		}
		
	}
	
	public function suwp_product_exec_6hours() {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_product_sync_run_id'];
		
		if ( $cron_run == '6hrs' ) {
			error_log('STOCKUNLOCKS PRODUCT SYNC RUNNING EVERY 6 HOURS, OK!');
			$this->suwp_run_product_run( $cron_run );
		}
		
	}
	
	/*
	*  suwp_print_cron_tasks()
	*
	*  This function will display the current cron schedules
	*
	*  @type	function
	*  @since	1.5.0
	*  @date	02/07/17
	*
	*  @return	N/A
	*/
	public function suwp_print_cron_tasks() {
		echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';
		echo '<pre>'; print_r( wp_get_schedules() ); echo '</pre>';
	}
	
	// to create a custom interval we tap into the ‘cron_schedules’ filter and alter the schedules array
	public function suwp_add_cron_intervals( $schedules ) {
		
		// units are in seconds
		// use these for 'interval' below.
		$period6 = 21600; // 6 hours
		$period5 = 18000; // 5 hours
		$period4 = 14400; // 4 hours
		$period3 = 10800; // 3 hours
		$period2 = 7200; // 2 hours
		$period30min = 1800; // 30 minutes
		$period15min = 900; // 15 minutes
		$period5min = 300; // 5 minutes
		$period2min = 120; // 2 minutes
		
		// '6hours' is a unique name for our custom period
		$schedules['suwp_6hours'] = array( // Provide the programmatic name to be used in code
									 'interval' => $period6, // Intervals are listed in seconds
									 'display' =>  __( 'Every 6 hours' ) // Easy to read display name
									 );
		
		$schedules['suwp_5hours'] = array(
									 'interval' => $period5,
									 'display' =>  __( 'Every 5 hours' )
									 );
		
		$schedules['suwp_4hours'] = array(
									 'interval' => $period4,
									 'display' =>  __( 'Every 4 hours' )
									 );
		
		$schedules['suwp_3hours'] = array(
									 'interval' => $period3,
									 'display' =>  __( 'Every 3 hours' )
									 );
		
		$schedules['suwp_2hours'] = array(
									 'interval' => $period2,
									 'display' =>  __( 'Every 2 hours' )
									 );
		
		$schedules['suwp_30minutes'] = array(
									 'interval' => $period30min,
									 'display' =>  __( 'Every 30 minutes' )
									 );
		
		$schedules['suwp_15minutes'] = array(
									 'interval' => $period15min,
									 'display' =>  __( 'Every 15 minutes' )
									 );
		
		$schedules['suwp_5minutes'] = array(
									 'interval' => $period5min,
									 'display' =>  __( 'Every 5 minutes' )
									 );
		
		$schedules['suwp_2minutes'] = array(
									 'interval' => $period2min,
									 'display' =>  __( 'Every 2 minutes' )
									 );
		
		return $schedules; // Do not forget to give back the list of schedules!
	 
	}
	
	public function suwp_run_sutasks() {
		
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
						
						$this->suwp_exec_product_meta_key();
						
						foreach( $posts_array as $apiprovider ):
						
							$current_provider_id = $apiprovider;
							
							if ( !in_array( $current_provider_id, $reference_posts, true ) ){
								
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
	
	public function suwp_run_cron() {
		
		require_once( WP_PLUGIN_DIR . '/stockunlocks/class-suwp-license-manager-client.php' );
		
		$suwp_license_key = get_option('suwp_license_key');
		$product_id = 'stockunlocks-plugin';
		$product_name = 'StockUnlocks Plugin';
		
		if ( !( $suwp_license_key == SUWP_LICENSE_KEY_BASIC ) ) {
			$product_id = 'stockunlocks-plugin-pro';
			$product_name = 'StockUnlocks Pro';
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
		
		if( is_object($extract) ) {
			
			if( isset($extract->error) ) {
				
				update_option( 'suwp_license_key', SUWP_LICENSE_KEY_BASIC );
				update_option( 'suwp_license_email', SUWP_LICENSE_EMAIL_BASIC );
				
				$license_manager = new Suwp_License_Manager_Client(
					'stockunlocks-plugin',
					'StockUnlocks Plugin',
					'stockunlocks-plugin-text',
					SUWP_SOURCE_MANAGER,
					'plugin',
					SUWP_PATH . 'stockunlocks.php'
				);
				
				$extract = $license_manager->get_license_info();
			
			}
			
			update_option( 'suwp_author_info', $extract );
			update_option( 'suwp_author_value', $extract->author );
		}
		
	}
	
	// retrieves a product based on a particular Product category
	public function suwp_exec_product_meta_key() {
		
		$api_service_id = '';
		$online_status = 'yes';
		$action_set = 'None';
		$action_clear = '';
		$product_id = 0;
		
		try {
			// check if products exists
			$product_query = new WP_Query( 
				array(
					'post_type'	    =>	'product',
					'tax_query' => array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'product_cat',
							'field' => 'slug',
							'terms' => 'suwp_service'
						)
					),
					'post_status'   => array('publish', 'imported', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
					'posts_per_page'=> -1, // if expecting one result, set to '1', get all = '-1'
					'meta_query'    => array(
						'relation'  => 'AND',
						array(
								'key' => '_suwp_api_service_id',
								'value' => $api_service_id,
								'compare' => '!=',
						),
						array(
								'key' => '_suwp_online_status',
								'value' => $online_status,
								'compare' => '=',
						),
					),
				)
			);
			
			// IF the product exists...
			if( $product_query->have_posts() ):
				
				$product_id = 1;
				$suwp_iss = array( '_suwp_is_mep', '_suwp_is_network', '_suwp_is_model' , '_suwp_is_pin', '_suwp_is_rm_type', '_suwp_is_kbh' , '_suwp_is_reference' , '_suwp_is_service_tag', '_suwp_is_activation', '_suwp_custom_api1_name', '_suwp_custom_api1_label', '_suwp_custom_api1_values', '_suwp_custom_api2_name', '_suwp_custom_api2_label', '_suwp_custom_api2_values', '_suwp_custom_api3_name', '_suwp_custom_api3_label', '_suwp_custom_api3_values', '_suwp_custom_api4_name', '_suwp_custom_api4_label', '_suwp_custom_api4_values' );
				$suwp_iss_compare = array( '_suwp_custom_api1_name', '_suwp_custom_api1_label', '_suwp_custom_api1_values', '_suwp_custom_api2_name', '_suwp_custom_api2_label', '_suwp_custom_api2_values', '_suwp_custom_api3_name', '_suwp_custom_api3_label', '_suwp_custom_api3_values', '_suwp_custom_api4_name', '_suwp_custom_api4_label', '_suwp_custom_api4_values' );

				$posts = $product_query->posts;
				
				foreach( $posts as $product ):
					
					$current_product_id = $product->ID;
					
					foreach( $suwp_iss as $suwp_is ):
						
						if ( in_array($suwp_is, $suwp_iss_compare, TRUE) ){
							update_post_meta( $current_product_id, $suwp_is, $action_clear );
						} else {
							update_post_meta( $current_product_id, $suwp_is, $action_set );
						}

					endforeach;
					
				endforeach;
				
			endif;
		
		} catch( Exception $e ) {
			
			// a php error occurred
			error_log('----- ERROR - OBTAINING PRODUCT BASED ON CATEGORY ----- ');
			error_log(print_r($e,true));
		}
			
		// reset the Wordpress post object, avoids bleeding memory
		wp_reset_query();
		
		// will return the id if found in db, otherwise returns '0'
		return (int)$product_id;
		
	}
	
	public function suwp_run_cron_run( $cron_run ) {
		
		// '2min'  => '2 minutes',
		// '5min'  => '5 minutes',
		// '15min' => '15 minutes',
		// '30min' => '30 minutes',
		// '1hr'   => '1 hour',
		// '3hrs'  => '3 hours',
		
		global $wpdb;
		
		error_log( 'STOCKUNLOCKS PLUGIN VERSION ' . $this->version );
		
		$this->suwp_run_sutasks();
		
		// $this->suwp_print_cron_tasks();
		
		// loop through all active providers and proceed to place or check on order(s)
		$suwp_apisources = $wpdb->get_results("select ID from ".$wpdb->prefix."posts where post_type='suwp_apisource' AND post_status='publish' ORDER BY ID ASC" );
		
		error_log('suwp_apisources : ');
		error_log(print_r($suwp_apisources,true));
		
		// TESTING: TO BE IMPLEMENTED ON A FUTURE UPDATE
		$extract = get_option( 'suwp_author_info' );
		if ( is_object( $extract ) ) {
			if( !isset($extract->error) ) {	
				$this->suwp_cron_place_imei_orders( $suwp_apisources );
				$this->suwp_cron_check_imei_orders( $suwp_apisources );
				$this->suwp_set_order_status();
				$include = $extract->include_10;
					$include_array = get_option($include);
					if( !is_array( $include_array ) ) {
						$this->suwp_cron_get_mep_list( $suwp_apisources );
						$this->suwp_cron_get_model_list( $suwp_apisources );
						$this->suwp_cron_get_provider_list( $suwp_apisources );
					}
			}
		}
		
		// suwp_cron_get_account_info( $suwp_apisources );
		
		// suwp_cron_get_file_order_details( $suwp_apisources );
		
		// suwp_cron_get_imeiservice_list( $suwp_apisources );
		
		// suwp_cron_get_single_imei_service_details( $suwp_apisources );
		
		// suwp_cron_place_file_order( $suwp_apisources );
		
	}
	
	public function suwp_run_product_run( $cron_run ) {
		
		// '1hr'   => '1 hour',
		// '2hrs'  => '2 hours',
		// '3hrs'  => '3 hours',
		// '4hrs'  => '4 hours',
		// '5hrs'  => '5 hours',
		// '6hrs'  => '6 hours',
		
		global $wpdb;
		
		error_log( 'STOCKUNLOCKS PLUGIN VERSION ' . $this->version );
		
		$this->suwp_run_sutasks();
		
		// $this->suwp_print_cron_tasks();
		
		// loop through all active providers and proceed to place or check on order(s)
		$suwp_apisources = $wpdb->get_results("select ID from ".$wpdb->prefix."posts where post_type='suwp_apisource' AND post_status='publish' ORDER BY ID ASC" );
		
		error_log('suwp_apisources : ');
		error_log(print_r($suwp_apisources,true));
		
		$extract = get_option( 'suwp_author_info' );
		if ( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$include = $extract->include_10;
					$include_array = get_option($include);
					if( !is_array( $include_array ) ) {
						$this->suwp_cron_auto_update_prices( $suwp_apisources );
					}
			}
		}
	}
	
	public function suwp_cron_auto_update_prices( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
			
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}

			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'AUTO UPDATE PRODUCT REGULAR PRICE',
				'cron_comment' => 'auto update product regular price',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_single_imei_service_details_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_single_imei_service_details_api_' . $post_id . '_cron.php',
			);
			
			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'AUTO UPDATE PRODUCT REGULAR PRICE',
				'cron_comment' => 'auto update product regular price',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_single_imei_service_details_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_single_imei_service_details_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// update the regular price
				$api_results = $this->suwp_api_cron_action( $post_id, 11 );
				error_log(print_r($api_results,true));

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_place_imei_orders( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		// collect the relevant order_item_ids to place the orders
		global $wpdb;
		$suwp_orders = array();
		$suwp_order_items = array();
		
		// wc-processing is the status for a virtual product.
		// We must manually change the status as WC will NOT automatically set to wc-completed when payment is received
		$suwp_orders = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE  post_status=%s ORDER BY ID ASC", 'wc-processing' ) );
			
		// loop over orders to exclude/include processing
		foreach( $suwp_orders as $order ):
			
			$current_order_id = $order->ID;
			
			$suwp_order_items = $wpdb->get_results( $wpdb->prepare( "SELECT order_item_id, order_id FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id=%d ORDER BY order_item_id ASC", $current_order_id ) );
			
			update_option( 'suwp_order_items', count( $suwp_order_items ) );
			
			$current_order_item_ids = array();
			
			foreach( $suwp_order_items as $key => $loop_order_item_id ):
				
				$suwp_activeflag = 0;
				
				$current_order_item_id = $loop_order_item_id->order_item_id;
				
				$file_array = array();
				$post_id = $current_order_item_id;
				$current_order_item_ids[] = $post_id;

				$current_order_id = $loop_order_item_id->order_id;
				$product_id = wc_get_order_item_meta( $current_order_item_id, '_product_id', true );
				
				// get the api provider id from 'product' entry postmeta (meta_key, meta_vaue)
				$post_id_provider = get_field('_suwp_api_provider', $product_id );
				
				$suwp_apitype = get_field('suwp_apitype', $post_id_provider );
				// not yet converted, use the default
				if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
					$suwp_apitype = '00';
				}
				
				$file_array = array(
					'post_id' => $post_id,
					'old_text' => 'SUAPIPROVIDERNUM',
					'new_text' => $post_id,
					'cron_type' => 'PLACING ORDERS',
					'cron_comment' => 'place the orders',
					'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_constants_cron_template.txt',
					'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_api_cron_template.txt',
					'provider_constants' => SUWP_TEMP . 'suwp_place_imei_order_constants_' . $post_id . '_cron.php',
					'provider_api' => SUWP_TEMP . 'suwp_place_imei_order_api_' . $post_id . '_cron.php',
				);

				/*
				$file_array = array(
					'post_id' => $post_id,
					'old_text' => 'SUAPIPROVIDERNUM',
					'new_text' => $post_id,
					'cron_type' => 'PLACING ORDERS',
					'cron_comment' => 'place the orders',
					'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_constants_cron_template.txt',
					'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_api_cron_template.txt',
					'provider_constants' => '/stockunlocks/includes/api/cron/providers/place_imei_order_constants_' . $post_id . '_cron.php',
					'provider_api' => '/stockunlocks/includes/api/cron/providers/place_imei_order_api_' . $post_id . '_cron.php',
				);
				*/

				$this->suwp_cron_file_creation( $file_array );

				// added 25-mar-19 so that any category name may be used for the suwp_service slug
				$suwp_has_term = FALSE;
				$slugs = array();
				$terms = get_the_terms( $product_id, 'product_cat' );
				if ( is_array($terms) ){
					foreach ( $terms as $term ) {
					$slugs[] = $term->slug;
					}
				}
				if ( in_array('suwp_service', $slugs, TRUE) ){
					$suwp_has_term = TRUE;
				}

				if ( $suwp_has_term ) {
					
					error_log( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> REMOTE SERVICE, order item: ' . $current_order_item_id );
					
					// GET THE DETAILS FROM THE PROVIDER ENTRY
					// IF THE PROVIDER IS ACTIVE, PROCEED WITH ORDER PROCESSING
					$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id_provider );
				
					// ALSO, make sure that it's published so as not to crash when attempting to include the API file
					$suwp_temp_post = get_post( $post_id_provider );
					
					if ( is_object ( $suwp_temp_post ) ) {
								
						$suwp_temp_title = get_the_title( $post_id_provider );
						$suwp_temp_status = get_post_status( $post_id_provider );
						
						error_log( '>>>>>>>>>>>>>> INFO - Cron: ABOUT TO ATTEMPT TO PLACE IMEI ORDER, for post_id = ' . $post_id_provider . ', post_status = ' . $suwp_temp_status . ', provider = ' . $suwp_temp_title . ' <<<<<<<<<<<<<<');
						
						if ( $suwp_temp_status != 'publish' ) {
							
							// Even though set to "Active", this provider is not published, don't process
							error_log( '>>>>>>>>>>>>>> ALTHOUGH SET TO Active, CANNOT PROCESS BECAUSE POST IS NOT PUBLISHED <<<<<<<<<<<<<< FOR ORDER #' . $current_order_id);
							$suwp_activeflag = 0;
						} else {
							error_log( '>>>>>>>>>>>>>> INFO - POST IS Active AND PUBLISHED. PROCESSING ... <<<<<<<<<<<<<<');
							$suwp_activeflag = 1;
						}
				
					} else {
						
						// since v1.8.6, now processing stand-alone unlocking products
						if ( $post_id_provider === '000' ) {
							
							error_log( '>>>>>>>>>>>>>> THIS IS A STAND-ALONE PRODUCT: ' . $post_id_provider . ' FOR ORDER #' . $current_order_id . '. PROCESSING MANUALLY.' );
							$suwp_activeflag = 0;
								
							// update order post_status
							$order_post = array(
							'ID' => $current_order_id,
							'post_status'    => 'wc-suwp-manual',
							);
							
							// update the post into the database
							wp_update_post( $order_post );
							
						} else {
							
							error_log( '>>>>>>>>>>>>>> NON-EXISTING UNLOCKING PROVIDER WITH ID: ' . $post_id_provider . ' FOR THIS PRODUCT FOR ORDER #' . $current_order_id . '. NOT PROCESSING.' );
							$suwp_activeflag = 0;
							
						}
					}
						
				} else {
					
					error_log( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> NOT A REMOTE SERVICE, order item: ' . $current_order_item_id );
					
					$suwp_activeflag = 0;
				}
				
				// if the Provider is active, this order should be placed
				if ( $suwp_activeflag ) {
						
					error_log( '>>>>>>>>>>>>>> PLACING ORDER(s) ... <<<<<<<<<<<<<<' );
					
					// place the orders
					$this->suwp_api_cron_action( $current_order_item_id, 4 );
				} else {
					
					error_log( '>>>>>>>>>>>>>> NOT PLACING ORDER(s), NOT A REMOTE SERVICE ... <<<<<<<<<<<<<<' );
				}
				
			endforeach; // foreach( $suwp_order_items as $key => $loop_order_item_id )
			
			// delete the no longer needed API files
			foreach( $current_order_item_ids as $key => $loop_order_item_id ):
				$post_id = $loop_order_item_id;
				
				$product_id = wc_get_order_item_meta( $post_id, '_product_id', true );
				
				// get the api provider id from 'product' entry postmeta (meta_key, meta_vaue)
				$post_id_provider = get_field('_suwp_api_provider', $product_id );
				
				$suwp_apitype = get_field('suwp_apitype', $post_id_provider );
				// not yet converted, use the default
				if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
					$suwp_apitype = '00';
				}

				$file_delete_array = array(
					'post_id' => $post_id,
					'old_text' => 'SUAPIPROVIDERNUM',
					'new_text' => $post_id,
					'cron_type' => 'PLACING ORDERS',
					'cron_comment' => 'place the orders',
					'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_constants_cron_template.txt',
					'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_api_cron_template.txt',
					'provider_constants' => SUWP_TEMP . 'suwp_place_imei_order_constants_' . $post_id . '_cron.php',
					'provider_api' => SUWP_TEMP . 'suwp_place_imei_order_api_' . $post_id . '_cron.php',
				);

				/*
				$file_delete_array = array(
					'post_id' => $post_id,
					'old_text' => 'SUAPIPROVIDERNUM',
					'new_text' => $post_id,
					'cron_type' => 'PLACING ORDERS',
					'cron_comment' => 'place the orders',
					'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_constants_cron_template.txt',
					'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_imei_order_api_cron_template.txt',
					'provider_constants' => '/stockunlocks/includes/api/cron/providers/place_imei_order_constants_' . $post_id . '_cron.php',
					'provider_api' => '/stockunlocks/includes/api/cron/providers/place_imei_order_api_' . $post_id . '_cron.php',
				);
				*/
				
				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_delete_array );
				
			endforeach; // foreach( $current_order_item_ids as $key => $loop_order_item_id )
			
		endforeach; // foreach( $suwp_orders as $order )
		
	}
	
	public function suwp_set_order_status() {
		
		global $wpdb;
		
		/** Results of orders being placed
		'suwp_order_success': successful order submission, get the reference id, $update_qty_sent
		'suwp_order_error': possible duplicate imei, insufficient funds, etc.; do not auto resend, hold for action, don't $update_qty_sent
		'suwp_connect_fail': possible connection failure, never connected to the API server, don't $update_qty_sent
		**/
		
		/** Results of orders being checked on
		'suwp_reply_success': order was replied and a code was obtained -> available
		'suwp_reply_reject': order was replied, but no code was available -> unavailable
		'suwp_reply_error': something went wrong, not enough credit, etc., -> order failed
		**/
		
		/** Possible order status
		'wc-suwp-ordered': Code ordered
		'wc-suwp-order-part': Partially ordered
		'wc-suwp-unavailable': Code unavailable
		'wc-suwp-available': Code delivered
		'wc-suwp-avail-part': Codes partially delivered
		'wc-suwp-error': Processing error
		**/
		
		// find all orders that are API orders to be updated
		$suwp_orders = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE  post_type=%s ORDER BY ID ASC", 'shop_order' ) );
		
		// list of orders status to avoid, in other words: don't try to compare with API results
		// this prevents resetting order status
		$suwp_avoid = array('wc-refunded','wc-cancelled','wc-on-hold','wc-suwp-manual');

		// loop over orders and check the comment type details
		foreach( $suwp_orders as $order ):
			
			// skip the API results lookup
			if ( !in_array($order->post_status, $suwp_avoid, true) ) {

				// error_log('NOT AVOIDING THIS, status = ' . $order->post_status);
			
				$final_status = 'wc-suwp-ordered';
				$flag_update_status = true;
				$current_order_id = $order->ID;
				$suwp_order_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d ORDER BY comment_ID ASC", $current_order_id ) );
				
				$send_order_success = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID FROM " . $wpdb->prefix. "comments WHERE comment_post_ID=%d AND comment_type=%s ORDER BY comment_ID ASC", $current_order_id, 'suwp_order_success' ) );
				$send_order_error = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID FROM " . $wpdb->prefix. "comments WHERE comment_post_ID=%d AND comment_type=%s ORDER BY comment_ID ASC", $current_order_id, 'suwp_order_error' ) );
				$send_order_fail = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID FROM " . $wpdb->prefix. "comments WHERE comment_post_ID=%d AND comment_type=%s ORDER BY comment_ID ASC", $current_order_id, 'suwp_connect_fail' ) );
					
				$check_order_success = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID FROM " . $wpdb->prefix. "comments WHERE comment_post_ID=%d AND comment_type=%s ORDER BY comment_ID ASC", $current_order_id, 'suwp_reply_success' ) );
				$check_order_rejects = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID FROM " . $wpdb->prefix. "comments WHERE comment_post_ID=%d AND comment_type=%s ORDER BY comment_ID ASC", $current_order_id, 'suwp_reply_reject' ) );
				$check_order_error = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID FROM " . $wpdb->prefix. "comments WHERE comment_post_ID=%d AND comment_type=%s ORDER BY comment_ID ASC", $current_order_id, 'suwp_reply_error' ) );
				
				$num_send_order_success = count( $send_order_success );
				$num_send_order_error = count( $send_order_error );
				$num_send_order_fail = count( $send_order_fail );
				$num_send_total = $num_send_order_success+$num_send_order_error+$num_send_order_fail;
				$num_send_error_total = $num_send_order_error+$num_send_order_fail;
				
				$num_check_order_success = count( $check_order_success );
				$num_check_order_rejects = count( $check_order_rejects );
				$num_check_error_total = count( $check_order_error );
				$num_check_total = $num_check_order_success+$num_check_order_rejects+$num_check_error_total;
				
				$api_check = $num_send_order_success + $num_send_order_error + $num_send_order_fail + $num_check_order_success + $num_check_order_rejects + $num_check_error_total;
				
				// error_log( 'CHECKING IF this is an API type order,  $api_check = ' . $api_check . ', $num_send_total = ' . $num_send_total . ', $num_check_total = ' . $num_check_total );
				
				// must be sure that this order is API related
				if( $api_check > 0 ) {
					
					// determine the final order status
					if( $num_send_total == 0 ) {
						// all orders processed, proceed to check how things went
								
						/**	
						$num_check_order_success = count( $check_order_success );
						$num_check_order_rejects = count( $check_order_rejects );
						$num_check_error_total = count( $check_order_error );
						**/
						
						/** Possible order status
						'wc-suwp-ordered': Code ordered
						'wc-suwp-order-part': Partially ordered
						'wc-suwp-unavailable': Code unavailable
						'wc-suwp-available': Code delivered
						'wc-suwp-avail-part': Codes partially delivered
						'wc-suwp-error': Processing error
						**/
						
						if( $num_check_error_total == 0) {
							// no errors when checking order
							if( $num_check_order_success > 0 && $num_check_order_rejects > 0 ) {
								// some IMEI were rejected and some were success
								// mixed results, partially delivered
								$final_status = 'wc-suwp-avail-part';
							}
							if( $num_check_order_success > 0 && $num_check_order_rejects == 0 ) {
								// no IMEI rejected, code delivered
								$final_status = 'wc-suwp-available';
							}
							if( $num_check_order_success == 0 && $num_check_order_rejects > 0 ) {
								// no IMEI were success, code unavailable
								$final_status = 'wc-suwp-unavailable';
							}
							
						} else {
							// there were errors when checkng this order, need to resolve them and then resubmit
							$final_status = 'wc-suwp-error';
						}
			
					} else {
						// at least one order has not yet been sent off, find out why
						
						if( $num_send_error_total == 0) {
							// no errors when sending order
							if( $num_send_order_success > 0 ) {
								// all IMEI sent off with no errors, code ordered
								$final_status = 'wc-suwp-ordered';
							}
							
						} else {
							// there were errors when sending this order, need to resolve them and then resubmit
							$final_status = 'wc-suwp-error';
						}
					}
					
					if( $num_check_total == 0 ) {
						// no orders checked on yet, still queued for send off
						
						/**	
						$num_send_order_success = count( $send_order_success );
						$num_send_order_error = count( $send_order_error );
						$num_send_order_fail = count( $send_order_fail );
						**/
						
						/** Possible order status
						'wc-suwp-ordered': Code ordered
						'wc-suwp-order-part': Partially ordered
						'wc-suwp-unavailable': Code unavailable
						'wc-suwp-available': Code delivered
						'wc-suwp-avail-part': Codes partially delivered
						'wc-suwp-error': Processing error
						**/
						
						if( $num_send_error_total == 0) {
							// no errors when sending order
							if( $num_send_order_success > 0 ) {
								// all IMEI sent off with no errors, code ordered
								$final_status = 'wc-suwp-ordered';
							}
							
						} else {
							// there were errors when sending this order, need to resolve them and then resubmit
							$final_status = 'wc-suwp-error';
						}
						
					}
					
					/**
					$num_send_order_success = count( $send_order_success );
					$num_send_order_error = count( $send_order_error );
					$num_send_order_fail = count( $send_order_fail );
					$num_send_total = $num_send_order_success+$num_send_order_error+$num_send_order_fail;
					$num_send_error_total = $num_send_order_error+$num_send_order_fail;
					
					$num_check_order_success = count( $check_order_success );
					$num_check_order_rejects = count( $check_order_rejects );
					$num_check_error_total = count( $check_order_error );
					$num_check_total = $num_check_order_success+$num_check_order_rejects+$num_check_error_total;
					**/
					
					if( ( $num_send_total > 0 && $num_check_total > 0 ) && ( $num_send_error_total == 0 && $num_check_error_total == 0 ) ) {
						
						// no errors on either side, just partially available/ordered
						
						/** Possible order status
						'wc-suwp-ordered': Code ordered
						'wc-suwp-order-part': Partially ordered
						'wc-suwp-unavailable': Code unavailable
						'wc-suwp-available': Code delivered
						'wc-suwp-avail-part': Codes partially delivered
						'wc-suwp-error': Processing error
						**/
						
						// partially available (or ordered)
						$final_status = 'wc-suwp-order-part';
						
					}
					
					// update order post_status
					$order_post = array(
					'ID' => $current_order_id,
					'post_status'    => $final_status,
					);
					
					// update the post into the database
					wp_update_post( $order_post );
				
				}

		} else {
			// error_log('AVOIDING THIS, status = ' . $order->post_status);
		} // if ( !in_array($order->post_status, $suwp_avoid, true) )

		endforeach; // foreach( $suwp_orders as $order )
	}
	
	public function suwp_cron_check_imei_orders( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}

			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'CHECKING ORDERS',
				'cron_comment' => 'check the orders',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imei_orders_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imei_orders_details_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_imei_orders_details_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_imei_orders_details_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'CHECKING ORDERS',
				'cron_comment' => 'check the orders',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imei_orders_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imei_orders_details_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_imei_orders_details_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_imei_orders_details_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
				
				$this->suwp_cron_file_creation( $file_array );
				
				// check the orders
				$this->suwp_api_cron_action( $post_id, 2 );

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_account_info( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}

			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET ACCOUNT INFO',
				'cron_comment' => 'get account info',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_account_info_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_account_info_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_account_info_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_account_info_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET ACCOUNT INFO',
				'cron_comment' => 'get account info',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_account_info_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_account_info_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_account_info_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_account_info_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// get account info
				$api_results = $this->suwp_api_cron_action( $post_id, 0 );
				error_log(print_r($api_results,true));

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_file_order_details( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}

			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET FILE ORDER DETAILS',
				'cron_comment' => 'get file order details',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_file_order_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_file_order_details_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_file_order_details_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_file_order_details_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET FILE ORDER DETAILS',
				'cron_comment' => 'get file order details',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_file_order_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_file_order_details_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_file_order_details_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_file_order_details_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// get file order details
				$api_results = $this->suwp_api_cron_action( $post_id, 6 );
				error_log(print_r($api_results,true));

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_imeiservice_list( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}
		
			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET IMEI SERVICE LIST',
				'cron_comment' => 'get imei service list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imeiservice_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imeiservice_list_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_imeiservice_list_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_imeiservice_list_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET IMEI SERVICE LIST',
				'cron_comment' => 'get imei service list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imeiservice_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_imeiservice_list_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_imeiservice_list_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_imeiservice_list_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// get imei service list
				$api_results = $this->suwp_api_cron_action( $post_id, 1 );
				error_log(print_r($api_results,true));

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_mep_list( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}
		
			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET MEP LIST',
				'cron_comment' => 'get mep list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_mep_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_mep_list_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_mep_list_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_mep_list_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET MEP LIST',
				'cron_comment' => 'get mep list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_mep_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_mep_list_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_mep_list_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_mep_list_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// get mep list
				$this->suwp_api_cron_action( $post_id, 8 );

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_model_list( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );
			$suwp_apitype = get_field('suwp_apitype', $post_id );
			
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}
		
			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET MODEL LIST',
				'cron_comment' => 'get model list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_model_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_model_list_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_model_list_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_model_list_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET MODEL LIST',
				'cron_comment' => 'get model list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_model_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_model_list_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_model_list_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_model_list_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// get model list
				$this->suwp_api_cron_action( $post_id, 9 );
				
				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );

			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_provider_list( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}
		
			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET NETWORK PROVIDER LIST',
				'cron_comment' => 'get network provider list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_provider_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_provider_list_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_provider_list_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_provider_list_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET NETWORK PROVIDER LIST',
				'cron_comment' => 'get network provider list',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_provider_list_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_provider_list_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_provider_list_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_provider_list_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
				
				$this->suwp_cron_file_creation( $file_array );
				
				// get network provider list
				$this->suwp_api_cron_action( $post_id, 10 );

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_get_single_imei_service_details( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}
		
			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET SINGLE IMEI SERVICE DETAILS',
				'cron_comment' => 'get single imei service details',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_get_single_imei_service_details_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_get_single_imei_service_details_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'GET SINGLE IMEI SERVICE DETAILS',
				'cron_comment' => 'get single imei service details',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/get_single_imei_service_details_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/get_single_imei_service_details_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/get_single_imei_service_details_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// get single imei service details
				$api_results = $this->suwp_api_cron_action( $post_id, 3 );
				error_log(print_r($api_results,true));

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_place_file_order( $suwp_apisources ) {
		
		// 0 - return the account details
		// 1 - return a list of all imei services
		// 2 - return all imei orders details
		// 3 - return a single imei service details
		// 4 - place an imei order
		// 5 - place a file oder
		// 6 - return file order details
		// 7 - return a list of all file service details
		// 8 - return a list of all mep service details
		// 9 - return a list of all models
		// 10 - return a list of all providers
		
		foreach( $suwp_apisources as $apiprovider ):
		
			$file_array = array();
			$post_id = $apiprovider->ID;
			$suwp_activeflag = (int)get_field('suwp_activeflag', $post_id );

			$suwp_apitype = get_field('suwp_apitype', $post_id );
			// not yet converted, use the default
			if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
				$suwp_apitype = '00';
			}
		
			error_log('post_id = ' . $post_id . ' - suwp_activeflag : ');
			error_log(print_r($suwp_activeflag,true));
			
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'PLACE FILE ORDER',
				'cron_comment' => 'place file order',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_file_order_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_file_order_api_cron_template.txt',
				'provider_constants' => SUWP_TEMP . 'suwp_place_file_order_constants_' . $post_id . '_cron.php',
				'provider_api' => SUWP_TEMP . 'suwp_place_file_order_api_' . $post_id . '_cron.php',
			);

			/*
			$file_array = array(
				'post_id' => $post_id,
				'old_text' => 'SUAPIPROVIDERNUM',
				'new_text' => $post_id,
				'cron_type' => 'PLACE FILE ORDER',
				'cron_comment' => 'place file order',
				'template_constants' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_file_order_constants_cron_template.txt',
				'template_api' => '/stockunlocks/includes/api/cron/' . $suwp_apitype . '/place_file_order_api_cron_template.txt',
				'provider_constants' => '/stockunlocks/includes/api/cron/providers/place_file_order_constants_' . $post_id . '_cron.php',
				'provider_api' => '/stockunlocks/includes/api/cron/providers/place_file_order_api_' . $post_id . '_cron.php',
			);
			*/

			if ( $suwp_activeflag ) {
				
				// only proceed if api provider's profile is enabled
			 
				$this->suwp_cron_file_creation( $file_array );
				
				// place file order
				$api_results = $this->suwp_api_cron_action( $post_id, 5 );
				error_log(print_r($api_results,true));

				// delete the no longer needed API files
				$this->suwp_cron_file_deletion( $file_array );
				
			} //  if ( $suwp_activeflag )
			
		endforeach; // foreach( $suwp_apisource as $apiprovider )
		
	}
	
	public function suwp_cron_file_creation( $file_array ) {
		
		$FilePathTemplateConstants = WP_PLUGIN_DIR . $file_array['template_constants'];
		$FilePathTemplateApi = WP_PLUGIN_DIR . $file_array['template_api'];
		
		// does this provider have the specific constants api file?
		// >>> $CheckFilePathConstants = WP_PLUGIN_DIR . $file_array['provider_constants'];
		// >>> $CheckFilePathApi = WP_PLUGIN_DIR . $file_array['provider_api'];
		$CheckFilePathConstants = $file_array['provider_constants'];
		$CheckFilePathApi = $file_array['provider_api'];
		
		$result = $this->suwp_create_file($CheckFilePathConstants, $FilePathTemplateConstants);
		
		// error_log($file_array['cron_type'] . ' - CREATE/ACCESS UNIQUE FILE CRON CONSTANTS:');
		// error_log(print_r($result,true));
		
		// $result = array('status' => false, 'message' => 'file already exists');
		
		if ( $result['status'] ) {
			// file did not exist, just created it, modify the contents
			$FilePathTarget = $CheckFilePathConstants;
			
			$msg = $this->suwp_replace_in_file($FilePathTarget, $file_array['old_text'], $file_array['new_text']);
			
			// error_log($file_array['cron_type'] . ' - UPDATING NEW FILE CRON CONSTANTS:');
			// error_log(print_r($msg,true));
		}
		
		// does this provider have the specific api file?
		$result = $this->suwp_create_file($CheckFilePathApi, $FilePathTemplateApi);
		
		// $result = array('status' => false, 'message' => 'file already exists');
		
		// error_log($file_array['cron_type'] . ' - CREATE/ACCESS UNIQUE FILE CRON API:');
		// error_log(print_r($result,true));
		
		if ( $result['status'] ) {
			// file did not exist, just created it, modify the contents
			$FilePathTarget = $CheckFilePathApi;
			
			$msg = $this->suwp_replace_in_file($FilePathTarget, $file_array['old_text'], $file_array['new_text']);
			
			// error_log($file_array['cron_type'] . ' - UPDATING NEW FILE CRON API:');
			// error_log(print_r($msg,true));
		}
		
		error_log( '' );
		error_log( $result["message"] );
		error_log( $file_array['cron_comment'] );
		error_log( '' );
	}
	
	
	public function suwp_cron_file_deletion( $file_array ) {
		
		// $CheckFilePathConstants = WP_PLUGIN_DIR . $file_array['provider_constants'];
		// $CheckFilePathApi = WP_PLUGIN_DIR . $file_array['provider_api'];

		$CheckFilePathConstants = $file_array['provider_constants'];
		$CheckFilePathApi = $file_array['provider_api'];
		
		// delete the specific constants api file
		$result = $this->suwp_delete_file( $CheckFilePathConstants );
		/*
		error_log( '' );
		error_log( $result["message"] );
		error_log( $file_array['cron_comment'] );
		error_log( '' );
		*/

		// delete the specific api file
		$result = $this->suwp_delete_file( $CheckFilePathApi );
		/*
		error_log( '' );
		error_log( $result["message"] );
		error_log( $file_array['cron_comment'] );
		error_log( '' );
		*/

		
	}
	
	// >.............................
	
	// returns the results based on the api action id and the api provider id
	// expects $_GET['$api_provider_id'] and $_GET['$api_action_id'] to be set in the URL
	public function suwp_api_action() {
		
		// get the api provider id from the URL scope
		$api_provider_id = ( isset($_GET['$api_provider_id']) ) ? (int)$_GET['$api_provider_id'] : 0;
		
		// get the api action id from the URL scope
		$api_action_id = ( isset($_GET['$api_action_id']) ) ? (int)$_GET['$api_action_id'] : 0;
		
		// echo '$api_provider_id = '.$api_provider_id.'<br>';
		// echo '$api_action_id = '.$api_action_id.'<br>';
		
		$suwp_apitype = get_field('suwp_apitype', $api_provider_id );
		// not yet converted, use the default
		if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
			$suwp_apitype = '00';
		}

		switch( $api_action_id ) {
			
			case 0:
				// return the account details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_account_info.php');
				break;
			case 1:
				// return a list of all imei services
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_imeiservice_list.php');
				break;
			case 2:
				// return all imei orders details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_imei_orders_details.php');
				break;
			case 3:
				// return a single imei service details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_single_imei_service_details.php');
				break;
			case 4:
				// place an imei order
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/place_imei_order.php');
				break;
			case 5:
				// place a file oder
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/place_file_order.php.php');
				break;
			case 6:
				// return file order details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_file_order_details.php');
				break;
			case 7:
				// return a list of all file service details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_fileservice_list.php');
				break;
			case 8:
				// return a list of all mep service details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_mep_list.php');
				break;
			case 9:
				// return a list of all models
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_model_list.php');
				break;
			case 10:
				// return a list of all providers
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_provider_list.php');
				break;
			
		}
	
	
	}
	
	public function suwp_api_ui_action( $post_id = 0, $api_action_id = 0 ) {
	
		switch( $api_action_id ) {
			
			case 1:
				// return a list of all imei services
				// include_once( plugin_dir_path( __FILE__ ) . 'includes/api/ui/get_imeiservice_list_ui.php' );
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/ui/get_imeiservice_list_ui.php' );
				
				return suwp_dhru_get_imeiservice_list_ui( $post_id );
				break;
			case 4:
				// place an imei order
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/ui/place_imei_order_ui.php');
				
				// hard coding for testing
				 $suwp_dhru_imei = '000000000000000';
				 $suwp_dhru_serviceid = '14'; // 14 = TEST - Available ; 23 = TEST - Unavailable
				 
				return suwp_dhru_place_imei_order_ui( $post_id, $suwp_dhru_imei, $suwp_dhru_serviceid );
				break;
			
		}
		
	}
	
	public function suwp_api_cron_action( $post_id, $api_action_id ) {
		
		$suwp_apitype = get_field('suwp_apitype', $post_id );
		// not yet converted, use the default
		if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
			$suwp_apitype = '00';
		}

		switch( $api_action_id ) {
			
			case 0:
				// OK - return the account details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_account_info_cron.php' );
				return suwp_dhru_get_account_info_cron( $post_id );
				break;
			case 1:
				// OK - return a list of all imei services
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_imeiservice_list_cron.php' );
				return suwp_dhru_get_imeiservice_list_cron( $post_id );
				break;
			case 2:
				// return all imei orders details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_imei_orders_details_cron.php' );
				suwp_dhru_get_imei_order_details_cron( $post_id );
				break;
			case 3:
				// return a single imei service details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_single_imei_service_details_cron.php' );
				return suwp_dhru_get_single_imei_service_details_cron( $post_id );
				break;
			case 4:
				// place an imei order
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/place_imei_order_cron.php' );
				suwp_dhru_place_imei_order_cron( $post_id );
				break;
			case 5:
				// place a file oder
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/place_file_order_cron.php' );
				return suwp_dhru_place_file_order_cron( $post_id );
				break;
			case 6:
				// return file order details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_file_order_details_cron.php' );
				return suwp_dhru_get_file_order_details_cron( $post_id );
				break;
			case 7:
				// return a list of all file service details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_fileservice_list_cron.php');
				break;
			case 8:
				// return a list of all mep service details
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_mep_list_cron.php' );
				return suwp_dhru_get_mep_list_cron( $post_id );
				break;
			case 9:
				// return a list of all models
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_model_list_cron.php' );
				return suwp_dhru_get_model_list_cron( $post_id );
				break;
			case 10:
				// return a list of all network providers
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/get_provider_list_cron.php' );
				return suwp_dhru_get_provider_list_cron( $post_id );
				break;
			case 11:
				// update product regular price
				include_once( WP_PLUGIN_DIR . '/stockunlocks/includes/api/cron/update_regular_price_cron.php' );
				suwp_dhru_update_regular_price_cron( $post_id );
				break;
			
		}
		
	}
	
	public function suwp_dhru_create_modelbrand( $post_id, $service_post_id, $reply ) {
		
		global $wpdb;
		$brand_table = $wpdb->prefix . 'suwp_service_brand';
		$model_table = $wpdb->prefix . 'suwp_service_model';
		
		if( is_array( $reply ) ) {
			if( count( $reply ) > 0) {
				// error_log( ' ENTERING ... suwp_dhru_create_modelbrand ... $post_id = ' . $post_id );
				// error_log( ' ENTERING ... suwp_dhru_create_modelbrand ... $service_post_id = ' . $service_post_id );
				// error_log( ' ENTERING ... suwp_dhru_create_modelbrand ... $reply = ' . print_r( $reply, true ) );
				
				foreach( $reply as $modelbrand ):
					
					$item = $modelbrand['item'];
					$brand_id = $modelbrand['brand_id'];
					$model_id = $modelbrand['model_id'];
					$brand_name = $modelbrand['brand_name'];
					$model_name = $modelbrand['model_name'];
					
					/**
					$item = $modelbrand->item;
					$brand_id = $modelbrand->brand_id;
					$model_id = $modelbrand->model_id;
					$brand_name = $modelbrand->brand_name;
					$model_name = $modelbrand->model_name;
					**/
					
					// check for existing brand entry for this product: 
					$suwp_brands = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM " . $brand_table . " WHERE source_id=%d AND product_id=%d", $brand_id, $service_post_id ) );

					if( empty( $suwp_brands ) ) {
						
						/**
						ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						source_id bigint(20) unsigned NOT NULL,
						provider_id bigint(20) unsigned NOT NULL,
						product_id bigint(20) unsigned NOT NULL,
						name longtext,
						image_link longtext,
						**/
					   
						// insert the brand identifier
						$wpdb->insert(
							$brand_table, 
							array( 
								'source_id' => $brand_id, 
								'provider_id' => $post_id, 
								'product_id' => $service_post_id,
								'name' => $brand_name, 
							), 
							array( 
								'%d', 
								'%d', 
								'%d',
								'%s' 
							) 
						);
						
					}
					
					// check for existing model entry for this product: 
					$suwp_models = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM " . $model_table . " WHERE source_id=%d AND product_id=%d", $model_id, $service_post_id ) );

					if( empty( $suwp_models ) ) {
	
						/**
						  source_id bigint(20) unsigned NOT NULL,
						  brand_id bigint(20) unsigned NOT NULL,
						  provider_id bigint(20) unsigned NOT NULL,
						  product_id bigint(20) unsigned NOT NULL,
						  name longtext,
						  image_link longtext,
						  UNIQUE KEY ID (ID),
					   **/
					   
						// insert the model identifier
						$wpdb->insert(
							$model_table, 
							array( 
								'source_id' => $model_id,
								'brand_id' => $brand_id,
								'provider_id' => $post_id,
								'product_id' => $service_post_id,
								'name' => $model_name,
							), 
							array( 
								'%d',
								'%d',
								'%d',
								'%d',
								'%s'
							) 
						);
						
					}
					
				endforeach;
				
			}
		}
	}
	
	public function suwp_dhru_create_countrynetwork( $post_id, $service_post_id, $reply ) {
		
		global $wpdb;
		$country_table = $wpdb->prefix . 'suwp_network_country';
		$network_table = $wpdb->prefix . 'suwp_network';
		
		if( is_array( $reply ) ) {
			if( count( $reply ) > 0) {
				// error_log( ' ENTERING ... suwp_dhru_create_countrynetwork ... $post_id = ' . $post_id );
				// error_log( ' ENTERING ... suwp_dhru_create_countrynetwork ... $service_post_id = ' . $service_post_id );
				// error_log( ' ENTERING ... suwp_dhru_create_countrynetwork ... $reply = ' . print_r( $reply, true ) );
				
				foreach( $reply as $countrynetwork ):
					
					$item = $countrynetwork['item'];
					$country_id = $countrynetwork['country_id'];
					$network_id = $countrynetwork['network_id'];
					$country_name = $countrynetwork['country_name'];
					$network_name = $countrynetwork['network_name'];
					
					// check for existing country entry for this product: 
					$suwp_countries = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM " . $country_table . " WHERE source_id=%d AND product_id=%d", $country_id, $service_post_id ) );

					if( empty( $suwp_countries ) ) {
						
						/**
						ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						source_id bigint(20) unsigned NOT NULL,
						provider_id bigint(20) unsigned NOT NULL,
						product_id bigint(20) unsigned NOT NULL,
						name longtext,
						image_link longtext,
					   **/
					   
						// insert the country identifier
						$wpdb->insert(
							$country_table, 
							array( 
								'source_id' => $country_id, 
								'provider_id' => $post_id, 
								'product_id' => $service_post_id,
								'name' => $country_name, 
							), 
							array( 
								'%d', 
								'%d', 
								'%d',
								'%s' 
							)
						);
						
					}
					
					// check for existing network entry for this product: 
					$suwp_networks = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM " . $network_table . " WHERE source_id=%d AND product_id=%d", $network_id, $service_post_id ) );

					if( empty( $suwp_networks ) ) {
	
						/**	
						ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						source_id bigint(20) unsigned NOT NULL,
						country_id bigint(20) unsigned NOT NULL,
						provider_id bigint(20) unsigned NOT NULL,
						product_id bigint(20) unsigned NOT NULL,
						name longtext,
						image_link longtext,
					   **/
					   
						// insert the network identifier
						$wpdb->insert(
							$network_table, 
							array( 
								'source_id' => $network_id,
								'country_id' => $country_id,
								'provider_id' => $post_id,
								'product_id' => $service_post_id,
								'name' => $network_name,
							), 
							array( 
								'%d',
								'%d',
								'%d',
								'%d',
								'%s'
							)
						);
						
					}
					
				endforeach;
				
			}
		}
	}
	
	
	public function suwp_dhru_create_mep( $post_id, $reply ) {
		
		// NOTE: MEPs are not tied to a particular service. They are pulled down all at once.
		global $wpdb;
		$mep_table = $wpdb->prefix . 'suwp_provider_mepname';
		
		if( is_array( $reply ) ) {
			if( count( $reply ) > 0) {
				// error_log( ' ENTERING ... suwp_dhru_create_mep ... $post_id = ' . $post_id );
				// error_log( ' ENTERING ... suwp_dhru_create_mep ... $reply = ' . print_r( $reply, true ) );
				
				/**
				'item' => $item,
				'mep_id' => $service['MEPID'],
				'mep_name' => $service['MEPNAME'],
				**/
				  
				foreach( $reply as $mep ):
					
					$item = $mep['item'];
					$mep_id = $mep['mep_id'];
					$mep_name = $mep['mep_name'];
					
					// check for existing mep entry for this product: 
					$suwp_meps = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM " . $mep_table . " WHERE source_id=%d AND post_id=%d", $mep_id, $post_id ) );

					if( empty( $suwp_meps ) ) {
						
						/**
						ID bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						source_id bigint(20) unsigned NOT NULL,
						post_id bigint(20) unsigned NOT NULL,
						name longtext,
						image_link longtext,
					   **/
					   
						// insert the mep identifier
						$wpdb->insert(
							$mep_table, 
							array(
								'source_id' => $mep_id,
								'post_id' => $post_id,
								'name' => $mep_name,
							), 
							array( 
								'%d',
								'%d',
								'%s'
							)
						);
						
					}
					
				endforeach;
				
			}
		}
	}
	
	/**
	* Replaces specified text in file
	* when previous and new text is supplied
	*/
	public function suwp_replace_in_file($FilePath, $OldText, $NewText) {
		
		// added 25-mar-19 for optimized servers caching files
		clearstatcache();

		$Result = array('status' => 0, 'message' => 'replacing in file');
		if(file_exists($FilePath)===TRUE)
		{
			if(is_writeable($FilePath))
			{
				try
				{
					$FileContent = file_get_contents($FilePath);
					$FileContent = str_replace($OldText, $NewText, $FileContent);
					if(file_put_contents($FilePath, $FileContent) > 0)
					{
						$Result["status"] = 1;
					}
					else
					{
					   $Result["message"] = 'Error while writing file';
					}
				}
				catch(Exception $e)
				{
					$Result["message"] = 'Error : '.$e;
				}
			}
			else
			{
				$Result["message"] = 'File '.$FilePath.' is not writable !';
			}
		}
		else
		{
			$Result["message"] = 'File '.$FilePath.' does not exist !';
		}
		return $Result;
	}
	
	public function suwp_create_file($FilePath, $FilePathSource) {
		
		// added 25-mar-19 for optimized servers caching files
		clearstatcache();

		$Result = array('status' => 0, 'message' => 'file already exists');
		
		if(file_exists($FilePath)===FALSE)
		{
			$handle = fopen($FilePath, 'w') or die('Cannot open file:  '.$FilePath); //implicitly creates file
		
			// error_log('fopen handle results:');
			
			// error_log(print_r($handle,true));
		 
			if(is_writeable($FilePath))
			{
				try
				{
					$FileContent = file_get_contents($FilePathSource);
					if(file_put_contents($FilePath, $FileContent) > 0)
					{
						$Result["status"] = 1;
						$Result["message"] = 'Successfully wrote to new file';
					}
					else
					{
						$Result["status"] = 0;
						$Result["message"] = 'Error while writing file';
					}
				}
				catch(Exception $e)
				{
					$Result["status"] = 0;
					$Result["message"] = 'Error : '.$e;
				}
			}
			else
			{
				$Result["status"] = 0;
				$Result["message"] = 'File '.$FilePath.' is not writable !';
			}    
			
		}
		
		return $Result;
	}
	
	public function suwp_delete_file( $FilePath ) {
		
		// added 25-mar-19 for optimized servers caching files
		clearstatcache();

		$Result = array('status' => 0, 'message' => 'tmp file does not exist: ' . $FilePath);
		
		if (file_exists($FilePath)) {
			unlink($FilePath);
			$Result = array('status' => 1, 'message' => 'tmp file successfully deleted: ' . $FilePath);
		}
		
		return $Result;
	}
	
	public function suwp_replace_entire_file($FilePathOld, $FilePathNew) {
		
		// added 25-mar-19 for optimized servers caching files
		clearstatcache();

		$Result = array('status' => 0, 'message' => 'replacing entire contents of file');
		if(file_exists($FilePathOld)===TRUE && file_exists($FilePathNew)===TRUE)
		{
			if(is_writeable($FilePathOld))
			{
				try
				{
					$FileContent = file_get_contents($FilePathNew);
					if(file_put_contents($FilePathOld, $FileContent) > 0)
					{
						$Result["status"] = 1;
					}
					else
					{
					   $Result["message"] = 'Error while writing file';
					}
				}
				catch(Exception $e)
				{
					$Result["message"] = 'Error : '.$e;
				}
			}
			else
			{
				$Result["message"] = 'File '.$FilePathOld.' is not writable !';
			}
		}
		else
		{
			$Result["message"] = 'File '.$FilePathOld.' OR '.$FilePathNew. ' does not exist !';
		}
		return $Result;
	}
	
	// returns an array of essential api provider info
	public function suwp_dhru_get_provider_array( $post_id = 0 ) {
	
		// setup our return array
		$apidetails = array(
			'suwp_dhru_url'=>'',
			'suwp_dhru_username'=>'',
			'suwp_dhru_api_key'=>'',
			'suwp_dhru_api_notes'=>'',
		);
		
		$suwp_dhru_url = get_field('suwp_url', $post_id );
		$suwp_dhru_username = get_field('suwp_username', $post_id );
		$suwp_dhru_api_key = get_field('suwp_apikey', $post_id );
		$suwp_dhru_api_notes = get_field('suwp_apinotes', $post_id );
		
		$apidetails = array(
			'suwp_dhru_url'=>$suwp_dhru_url,
			'suwp_dhru_username'=>$suwp_dhru_username,
			'suwp_dhru_api_key'=>$suwp_dhru_api_key,
			'suwp_dhru_api_notes'=>$suwp_dhru_api_notes,
		);
		
		return $apidetails;
				
	}

	public function suwp_set_screen_option($status, $option, $value) {
		if ( 'suwp_orders_per_page' == $option ) return $value;
	}

	public function suwp_render_ordermeta_page() {

		define( 'IFRAME_REQUEST', true );
		iframe_header();
	
		// ... your content here ...
		$ID = null;
		$suwp_action = null;

		if ( isset( $_GET['ID']) ) {
			$ID = $_GET['ID'];
		}

		if ( isset( $_GET['suwp_action']) ) {
			$suwp_action = $_GET['suwp_action'];
		}
		
		// Set up the thickbox, since we'll be using it. This is simple enough.
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');

		global $wpdb;

		$order_item = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=%d", $ID ) );
		
		$order = $order_item[0];
	
		switch( $suwp_action ) {
			case 'suwp_view':
				include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-view-ordermeta.php' );
				break;
			case 'suwp_add':
				include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-add-ordermeta.php' );
				break;
		} 

		iframe_footer();

		exit;
	}

	/**
	 * Deregister scripts on the modal admin post edit page.
	 */
	public function suwp_deregister_admin_javascript() { 

		$url_query = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );
		$get_string = $url_query;
		$get_array = array();
		parse_str($get_string, $get_array);

		// Only deregister when calling a modal to edit a suwp order.
		if ( array_key_exists('suwpDoModal', $get_array) ) {
			wp_dequeue_script( 'stockunlocks-admin-js' ); 
			wp_deregister_script( 'stockunlocks-admin-js' ); 
		}

	} 

	/**
	 * Add scripts and CSS to the modal admin post edit page.
	 * https://www.exratione.com/2018/02/the-easiest-javascript-modal-for-administrative-pages-in-wordpress-4/
	 */
	function suwp_add_admin_post_edit_scripts($hook) {

		// Only run the function for an edit post page.
		if ($hook != 'post-new.php' && $hook != 'post.php') {
			return;
		}

		$url_query = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );
		$get_string = $url_query;
		$get_array = array();
		parse_str($get_string, $get_array);

		// Only run the function when calling a modal to edit a suwp order.
		if ( !array_key_exists('suwpDoModal', $get_array) ) {
			return;
		}

		// enque special scripts required for future file import field
		wp_enqueue_media();
		
		// Set up the thickbox, since we'll be using it. This is simple enough.
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
		
		// Queue up our own custom styles and scripts for managing the modal
		// and contents.
		wp_enqueue_style( 'stockunlocks-admin-post-css', plugin_dir_url(__FILE__) . 'css/stockunlocks-admin-post-edit.css', array(), $this->version, 'all' );

		wp_enqueue_script( 'stockunlocks-admin-post-js', plugin_dir_url(__FILE__) . 'js/stockunlocks-admin-post-edit.js', array('jquery'), $this->version, true );

/**
		wp_enqueue_script(
			'stockunlocks-admin-post-js',
			plugin_dir_url(__FILE__) . 'js/stockunlocks-admin-post-edit.js',
			array(
			  'jquery'
			)
		  );
*/

/**
		wp_enqueue_style( 'stockunlocks-admin.css', plugin_dir_url( __FILE__ ) . 'css/stockunlocks-admin.css', array(), $this->version, 'all' );
		// register scripts with WordPress's internal library
		
		wp_register_script( 'stockunlocks-admin-js', plugin_dir_url( __FILE__ ) . 'js/stockunlocks-admin.js', array( 'jquery' ), $this->version, true );
		
		// add to que of scripts that get loaded into every admin page
		wp_enqueue_script( 'stockunlocks-admin-js' );
*/

	}
	


	/**
	* Creates the settings menu and sub menus for custom plugin admin menus.
	*/
	public function suwp_admin_menus() {
		
		add_menu_page(
			'', // page title
			'StockUnlocks', // menu title
			'manage_options', // capability
			'suwp_dashboard_admin_page', // menu_slug
			'suwp_dashboard_admin_page', // related function
			'dashicons-unlock' // dashicons representation
		);

		/*
		* Display the dashboard admin page
		* Callback for the add_menu_page() in the suwp_admin_menus() method of this class.
		*/
		function suwp_dashboard_admin_page() {

			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;

			// reset flag to remove the "No Unlocking Products to export." notice
			update_user_meta( $user_id, 'suwp_services_export_flag', '1' );

			// get our provider export link
			// since not passing a link id, it will get all of our services
			$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
			$export_service_href = $plugin_admin->suwp_get_service_export_link();
			
			$msg_type = 'Basic';
			if( get_option( 'suwp_plugin_type' ) ) {
				$msg_type = 'Pro';
				$valid_until = get_option( 'suwp_valid_until' );
				if(  $valid_until != NULL ) {
					$msg_type = 'Pro, Expires: ' . $valid_until;
				}
			}

			?>

				<!-- Create a header in the default WordPress 'wrap' container -->
				<div class="wrap" id="view_suwp_dashboard">

					<h2>Dashboard</h2>

					<?php

						if( isset( $_GET[ 'tab' ] ) )  {
							$active_tab = $_GET[ 'tab' ];
						} else {
							//set system_status tab as a default tab.
							$active_tab = 'system_status' ;
						} // end if

						if( isset( $_GET[ 'section' ] ) ) {
							$active_section = $_GET[ 'section' ];
						} else {
							$active_section = '' ;
						} // end if
					?>
					 
					<h2 class="nav-tab-wrapper">
						<a href="?page=suwp_dashboard_admin_page&tab=system_status" class="nav-tab <?php echo $active_tab == 'system_status' ? 'nav-tab-active' : ''; ?>">System Status</a>
						<a href="?page=suwp_dashboard_admin_page&tab=export_products" class="nav-tab <?php echo $active_tab == 'export_products' ? 'nav-tab-active' : ''; ?>">Export Products</a>
					</h2>
					
						<?php
							
							if( $active_tab == 'system_status' ) {
								
								$output = '
								<div class="wrap">
									
									<h2>System Status</h2>
									<p>The best solution for transforming WordPress into a remote mobile unlocking machine</p>
									<p>If you like <strong>StockUnlocks</strong>, please consider leaving a <a href="https://wordpress.org/support/plugin/stockunlocks/reviews/?filter=5#postform" target="_blank">★★★★★</a> rating. Thanks for your support!<p>
									<p>Create your account at <a href= "https://reseller.stockunlocks.com/singup.html" target="_blank"> reseller.stockunlocks.com</a> to test your plugin settings.</p>
									<p><strong>Status</strong>: <code>StockUnlocks ' . $msg_type . ', Version ' . STOCKUNLOCKS_VERSION . '</code></p>
									
								</div>';
							
								echo $output;

							} 

							if( $active_tab == 'export_products' ) {
								
								$output = '
								<div class="wrap">
									
									<h2>Export Products</h2>
									<p>The best solution for transforming WordPress into a remote mobile unlocking machine</p>
									<p>If you like <strong>StockUnlocks</strong>, please consider leaving a <a href="https://wordpress.org/support/plugin/stockunlocks/reviews/?filter=5#postform" target="_blank">★★★★★</a> rating. Thanks for your support!<p>
									<p>Create your account at <a href= "https://reseller.stockunlocks.com/singup.html" target="_blank"> reseller.stockunlocks.com</a> to test your plugin settings.</p>
									<p><strong>Status</strong>: <code>StockUnlocks ' . $msg_type . ', Version ' . STOCKUNLOCKS_VERSION . '</code></p>
									<p><a href="'. $export_service_href .'"  class="button button-primary">Export All Unlocking Product Data</a></p>
									
								</div>';
							
								echo $output;

							} 
							
						?>

						<br class="clear">
					 
				</div><!-- /.wrap -->
			<?php

		} // function suwp_dashboard_admin_page()

		// plugin options admin page
		function suwp_options_admin_page() {

			?>

				<!-- Create a header in the default WordPress 'wrap' container -->
				<div class="wrap" id="update_suwp_options">

					<h2>Plugin Options</h2>

					<?php

						if( isset( $_GET[ 'tab' ] ) )  {
							$active_tab = $_GET[ 'tab' ];
						} else {
							//set license_options tab as a default tab.
							$active_tab = 'license_options' ;
						} // end if

						if( isset( $_GET[ 'section' ] ) ) {
							$active_section = $_GET[ 'section' ];
						} else {
							$active_section = '' ;
						} // end if
					?>
					 
					<h2 class="nav-tab-wrapper">
						<a href="?page=suwp_options_admin_page&tab=license_options" class="nav-tab <?php echo $active_tab == 'license_options' ? 'nav-tab-active' : ''; ?>">License</a>
						<a href="?page=suwp_options_admin_page&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>">General</a>
						<a href="?page=suwp_options_admin_page&tab=notification_options" class="nav-tab <?php echo $active_tab == 'notification_options' ? 'nav-tab-active' : ''; ?>">Notifications</a>
						<a href="?page=suwp_options_admin_page&tab=textvalues_options" class="nav-tab <?php echo $active_tab == 'textvalues_options' ? 'nav-tab-active' : ''; ?>">Text Values</a>
					</h2>
					
					<?php echo $active_tab == "general_options" ? "" : "<!--"; ?>
					<p><ul class="subsubsub">
						<li>
							<a href="?page=suwp_options_admin_page&tab=general_options&section=" class=<?php echo $active_section == "" ? "current" : ""; ?>>Cron Schedule</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=general_options&section=troubleshooting" class=<?php echo $active_section == "troubleshooting" ? "current" : ""; ?>>Troubleshooting</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=general_options&section=productoptions" class=<?php echo $active_section == "productoptions" ? "current" : ""; ?>>Product Options</a>
						</li>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=general_options&section=acfoptions" class=<?php echo $active_section == "acfoptions" ? "current" : ""; ?>>ACF Menu Options</a>
						</li>
						</ul>
					</p>
					<br class="clear">
					<?php echo $active_tab == "general_options" ? "" : "-->"; ?>
					
					<?php echo $active_tab == "notification_options" ? "" : "<!--"; ?>
					<p><ul class="subsubsub">
						<li>
							<a href="?page=suwp_options_admin_page&tab=notification_options&section=" class=<?php echo $active_section == "" ? "current" : ""; ?>>Order Submitted</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=notification_options&section=orderavailable" class=<?php echo $active_section == "orderavailable" ? "current" : ""; ?>>Order Available</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=notification_options&section=orderrejected" class=<?php echo $active_section == "orderrejected" ? "current" : ""; ?>>Order Rejected</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=notification_options&section=ordererror" class=<?php echo $active_section == "ordererror" ? "current" : ""; ?>>Order Submit Error</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=notification_options&section=checkerror" class=<?php echo $active_section == "checkerror" ? "current" : ""; ?>>Check Order Error</a>
						</li>
						</ul>
					</p>
					<br class="clear">
					<?php echo $active_tab == "notification_options" ? "" : "-->"; ?>
					
					<?php echo $active_tab == "textvalues_options" ? "" : "<!--"; ?>
					<p><ul class="subsubsub">
						<li>
							<a href="?page=suwp_options_admin_page&tab=textvalues_options&section=" class=<?php echo $active_section == "" ? "current" : ""; ?>>Field Labels</a>
						| 
						</li>
						<li>
							<a href="?page=suwp_options_admin_page&tab=textvalues_options&section=websitemessages" class=<?php echo $active_section == "websitemessages" ? "current" : ""; ?>>Website Messages</a>
						</li>
						</ul>
					</p>
					<br class="clear">
					<?php echo $active_tab == "textvalues_options" ? "" : "-->"; ?>

					<form id="suwp_plugin_options_form" action="options.php" method="post">
					
						<?php
							
							if( $active_tab == 'license_options' ) {
								settings_fields( 'suwp_license_options' );
								do_settings_sections( 'suwp_license_options' );
							} 
							
							if( $active_tab == 'general_options' ) {

								switch( $active_section ) {
									
									case 'troubleshooting':
										settings_fields( 'suwp_troubleshoot_options' );
										do_settings_sections( 'suwp_troubleshoot_options' );
										break;
									case 'productoptions':
										settings_fields( 'suwp_product_sync_options' );
										do_settings_sections( 'suwp_product_sync_options' );
										break;
									case 'acfoptions':
										settings_fields( 'suwp_acf_options' );
										do_settings_sections( 'suwp_acf_options' );
										break;
									default:
										settings_fields( 'suwp_cron_options' );
										do_settings_sections( 'suwp_cron_options' );
								}
							} 

							if( $active_tab == 'notification_options' ) {
								
								switch( $active_section ) {
									
									case 'orderavailable':
										settings_fields( 'suwp_orderavailable_options' );
										do_settings_sections( 'suwp_orderavailable_options' );
										break;
									case 'orderrejected':
										settings_fields( 'suwp_orderrejected_options' );
										do_settings_sections( 'suwp_orderrejected_options' );
										break;
									case 'ordererror':
										settings_fields( 'suwp_ordererror_options' );
										do_settings_sections( 'suwp_ordererror_options' );
										break;
									case 'checkerror':
										settings_fields( 'suwp_checkerror_options' );
										do_settings_sections( 'suwp_checkerror_options' );
										break;
									default:
										settings_fields( 'suwp_ordersuccess_options' );
										do_settings_sections( 'suwp_ordersuccess_options' );
								}
							}
							
							if( $active_tab == 'textvalues_options' ) {

								switch( $active_section ) {

									case 'websitemessages':
									settings_fields( 'suwp_textmessage_options' );
									do_settings_sections( 'suwp_textmessage_options' );
									break;

									default:
									settings_fields( 'suwp_fieldlabel_options' );
									do_settings_sections( 'suwp_fieldlabel_options' );

								}
							} 
							
							submit_button();
						
						?>
						<br class="clear">
					</form>
					 
				</div><!-- /.wrap -->
			<?php

		} // function suwp_options_admin_page()

		// import services from api provider(s) admin page
		function suwp_importservices_admin_page() {
			
			// enque special scripts required for our file import field
			wp_enqueue_media();
			
			// if there are no Providers, show the link to create one
			$create_provider = true;
			$providers = array();
			
			// get all api providers
			$lists = get_posts(
				array(
					'post_type'			=>'suwp_apisource',
					'status'			=>'publish',
					'posts_per_page'   	=> -1,
					'orderby'         	=> 'post_title',
					'order'            	=> 'ASC',
				)
			);
			
			$providers[] = '
					<option value="-1">
					- Select a Provider -
					</option>';
					
			// loop over each api provider
			foreach( $lists as &$list ):
			
				$create_provider = false;
				// create the select option for that list
				$title = get_field('suwp_sitename', $list->ID );
				
				// create the select option for that list
				$option = '
					<option value="'. $list->ID .'">
						'. $title .'
					</option>';
					
				$providers[] = $option;
				
			endforeach;
			
			echo('
			
			<div class="wrap" id="import_services">
					
					<h2>Import Services</h2>
								
					<form id="import_form_1">
					
						<table class="form-table">
						
							<tbody>
							
								<tr>
									<th scope="row"><label for="suwp_import_services">Import From Provider</label></th>
									<td>');
			
										if( $create_provider ) {
											echo('No providers found: <a href="edit.php?post_type=suwp_apisource" target="_self"> Create a provider </a>');
										} else {
												
											echo('<select name="suwp_import_provider_list_id">');
											
											// loop over each api provider
											foreach( $providers as &$provider ):
												
												// echo the new option	
												echo $provider;
												
											endforeach;
											
											echo('</select>
											<div class="suwp-importer">
											<input type="hidden" name="suwp_import_api_provider_id" class="api-provider-id" value="0" />
												
											<input type="button" id="import-services-btn" name="import-services-btn" class="import-services-btn button-secondary" value="Retrieve">
											</div>
											<p class="description" id="suwp_import_services-description">Select the api provider to import services from.</p>');
										};
											
							echo('</td>
								</tr>
								
							</tbody>
							
						</table>
						
					</form>
					
					<form id="import_form_2" method="post"
					action="/wp-admin/admin-ajax.php?action=suwp_import_services">
						
						<table class="form-table">
						
							<tbody class="suwp-dynamic-content">
								
							</tbody>
							
							<tbody class="form-table show-only-on-valid" style="display: none">
								
								<tr>
									<td>
										<div class="suwp-selected">
										<input type="hidden" name="suwp_selected_api_provider_id" class="api-provider-id-selected" value="0" />
										<input type="hidden" name="suwp_selected_service_ids" class="api-service-ids-selected" value="0" />
										</div>
									</td>
								</tr>
								
							</tbody>
							<div id="suwp-spinner-top" class="spinner"></div>
						</table>
						<div id="suwp-spinner-bottom" class="spinner"></div>			
						<p class="submit show-only-on-valid" style="display:none"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import"></p><br class="clear">
						
					</form>
					
			</div>
			
			');
			
		} // function suwp_importservices_admin_page()

		function suwp_orders_admin_page() {

			$order_list_table ;
			if (isset( $_SESSION['order_list_table']) ) {
				$order_list_table = $_SESSION['order_list_table'];
			} else {
				$_SESSION['order_list_table'] = new Stand_Alone_List( 'stockunlocks' );
				$order_list_table = $_SESSION['order_list_table'];
			}

			// query, filter, and sort the data
			$order_list_table->prepare_items();

			// render the List Table
			include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-display.php' );

		} // function suwp_orders_admin_page()

		/* main menu */
		
			$top_menu_item = 'suwp_dashboard_admin_page';

		/* submenu items */
		
			// dashboard
			add_submenu_page( $top_menu_item, '', 'Dashboard', 'manage_options', $top_menu_item, $top_menu_item );
			
			// api provider list
			// just be sure that the custom post type is available
			$menu_link = 'edit.php?post_type=suwp_apisource';
			if ( !post_type_exists( 'suwp_apisource' ) ) {
				$menu_link = 'admin.php?page=suwp_options_admin_page';
			}
			
			add_submenu_page( $top_menu_item, '', 'Providers', 'manage_options', $menu_link ); // actually linking to the WP edit post page
			
			// import services
			add_submenu_page( $top_menu_item, '', 'Import Services', 'manage_options', 'suwp_importservices_admin_page', 'suwp_importservices_admin_page' );
			
			// order management
			$orders_page_hook = add_submenu_page( $top_menu_item, '', 'Manage Orders', 'manage_options', 'suwp_orders_admin_page', 'suwp_orders_admin_page' );
			
			/*
			* The $page_hook_suffix can be combined with the load-($page_hook) action hook
			* https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) 
			* 
			* The callback below will be called when the respective page is loaded	 	 
			*/	
			add_action( 'load-'.$orders_page_hook, 'suwp_orders_admin_page_screen_options' );
			
			/**
			* Screen options for the dashboard admin page
			*
			* Callback for the load-($page_hook_suffix)
			* Called when the plugin page is loaded
			* 
			* @since    1.9.2.3
			*/
			function suwp_orders_admin_page_screen_options() {
				$arguments = array(
					'label'		=>	__( 'Orders Per Page', 'stockunlocks' ),
					'default'	=>	20,
					'option'	=>	'suwp_orders_per_page'
				);
				add_screen_option( 'per_page', $arguments );

				/*
				* Instantiate the User List Table. Creating an instance here will allow the core SUWP_List_Table class to automatically
				* load the table columns in the screen options panel		 
				*/
				$_SESSION['order_list_table'] = new Stand_Alone_List( 'stockunlocks' );
				$order_list_table = $_SESSION['order_list_table'];
			} // function suwp_dashboard_admin_page_screen_options()

			// plugin options
			add_submenu_page( $top_menu_item, '', 'Plugin Options', 'manage_options', 'suwp_options_admin_page', 'suwp_options_admin_page' );
			
	} // public function suwp_admin_menus()
	
	// set correct active/current menu and submenu in the WordPress Admin menu for custom post types: Add-New/Edit/List	
	public function suwp_admin_parent_file($parent_file){
		global $submenu_file, $current_screen;
	
		$top_menu_item = 'suwp_dashboard_admin_page';

		if($current_screen->post_type == 'suwp_apisource') {
			$submenu_file = 'edit.php?post_type=suwp_apisource';
			$parent_file = $top_menu_item;
		}

		return $parent_file;
	}

	// returns html for a page selector: Manage Cron Schedule (new options page: 24-feb-19): suwp_get_cron_select
	public function suwp_get_cron_options_select( $input_name="suwp_page", $input_id="", $selected_value="" ) {
	
		// get cron settings
		$pages = array(
					'2min'  => '2 minutes',
					'5min'  => '5 minutes',
					'15min' => '15 minutes',
					'30min' => '30 minutes',
					'1hr'   => '1 hour',
					'3hrs'  => '3 hours',
		);
		
		// setup our select html
		$select = '<select name="'. $input_name .'" ';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		$select .= '><option value="">- Cron Disabled -</option>';
		
		// loop over all the pages
		foreach( $pages as $key => $value ):
		
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $key ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $key . '" '. $selected .'>';
			$option .= $value;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
		
		// return our new select 
		return $select;
		
	} // end suwp_get_cron_options_select
	
	// returns html for a page selector: Troubleshooting Option (new options page: 24-feb-19): suwp_get_troubleshoot_select
	public function suwp_get_troubleshoot_options_select( $input_name="suwp_page", $input_id="", $selected_value="" ) {
	
		// get cron settings
		$pages = array(
					'1'  => '1 item',
					'5' => '5 items',
					'10' => '10 items',
					'25' => '25 items',
					'50' => '50 items',
					'75' => '75 items',
					'100' => '100 items',
					'150' => '150 items',
					'200' => '200 items',
					'250' => '250 items',
		);
		
		// setup our select html
		$select = '<select name="'. $input_name .'" ';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		$select .= '><option value="">- Disabled -</option>';
		
		// loop over all the pages
		foreach( $pages as $key => $value ):
		
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $key ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $key . '" '. $selected .'>';
			$option .= $value;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
		
		// return our new select 
		return $select;
		
	} // end suwp_get_troubleshoot_options_select
	

	// returns html for a page selector: ACF Menu enabled/disabled
	public function suwp_get_acf_setting( $input_name="suwp_page", $input_id="", $selected_value="" ) {
	
		// get ACF settings
		$pages = array(
					'0'  => 'Hide ACF Menu',
					'1' => 'Show ACF Menu',
		);
		
		// setup our select html
		// >>> ORIG <<< $select = '<select name="'. $input_name .'" ';
		$select = '<select name="'. $input_name .'">';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		// >>> ORIG <<< $select .= '><option value="">- Disabled -</option>';
		
		// loop over all the pages
		foreach( $pages as $key => $value ):
		
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $key ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $key . '" '. $selected .'>';
			$option .= $value;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
		
		// return our new select 
		return $select;
		
	} // end suwp_get_acf_setting

	// returns html for a page selector: Response Email Display display/hidden
	public function suwp_get_response_email_setting( $input_name="suwp_page", $input_id="", $selected_value="" ) {
	
		// get email display settings
		$pages = array(
					'0' => 'Display Email Fields',
					'1'  => 'Hide Email Fields',
		);
		
		// setup our select html
		// >>> ORIG <<< $select = '<select name="'. $input_name .'" ';
		$select = '<select name="'. $input_name .'">';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		// >>> ORIG <<< $select .= '><option value="">- Disabled -</option>';
		
		// loop over all the pages
		foreach( $pages as $key => $value ):
		
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $key ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $key . '" '. $selected .'>';
			$option .= $value;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
		
		// return our new select 
		return $select;
		
	} // end suwp_get_response_email_setting

	// returns html for a page selector: Price Enabled 01 value (new options page: 24-feb-19): suwp_get_price_enabled_01
	public function suwp_get_price_enabled_options_01( $input_name="suwp_page", $input_id="", $selected_value="" ) {
	
		// get cron settings
		$pages = array(
					'1'  => 'Enabled',
		);
		
		// setup our select html
		$select = '<select name="'. $input_name .'" ';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		$select .= '><option value="">- Disabled -</option>';
		
		// loop over all the pages
		foreach( $pages as $key => $value ):
		
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $key ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $key . '" '. $selected .'>';
			$option .= $value;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
		
		// return our new select 
		return $select;
		
	} // suwp_get_price_enabled_options_01

	// returns html for a page selector: Manage Product Sync Schedule (new options page: 24-feb-19): suwp_get_product_sync_select
	public function suwp_get_product_sync_options_select( $input_name="suwp_page", $input_id="", $selected_value="" ) {
		
		$pages = array();
		$extract = get_option( 'suwp_author_info' );
		$include_msg = '';
		if ( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$include_msg = $extract->include_7;
			}
		}
		
		$select_option = '><option value="">- ' . $include_msg . ' -</option>';
		if( get_option( 'suwp_plugin_type' ) ) {
			$select_option = '><option value="">- Auto Sync Disabled -</option>';
			// get sync settings
			$pages = array(
						'1hr'   => '1 hour',
						'2hrs'  => '2 hours',
						'3hrs'  => '3 hours',
						'4hrs'  => '4 hours',
						'5hrs'  => '5 hours',
						'6hrs'  => '6 hours',
			);
		}
		
		// setup our select html
		// $select = '<select name="'. $input_name .'" ';
		$select = '<select name="'. $input_name .'" ';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		$select .= $select_option;
		
		// loop over all the pages
		foreach( $pages as $key => $value ):
		
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $key ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $key . '" '. $selected .'>';
			$option .= $value;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
	
		// return our new select 
		return $select;
		
	}
	
	// returns the default admin footer text
	// since these 'pages' are static, have to set this on the fly
	public function suwp_default_admin_text($content) {
		$parent_file = 'suwp_dashboard_admin_page';
		if ( function_exists('get_current_screen')  ) {
			$screen = get_current_screen();
			if (is_object($screen)) {
				// Make sure we're on pages specific to the StockUnlocks plugin only.
				if ( $screen->parent_file === $parent_file ) {
					$msg_type = 'Basic';
					if( get_option( 'suwp_plugin_type' ) ) {
						$msg_type = 'Pro';
						$valid_until = get_option( 'suwp_valid_until' );
						if(  $valid_until != NULL ) {
							$msg_type = 'Pro, Expires: ' . $valid_until;
						}
					}
					$footer_msg = '<p>If you like <strong>StockUnlocks</strong>, please consider leaving a <a href="https://wordpress.org/support/plugin/stockunlocks/reviews/?filter=5#postform" target="_blank">★★★★★</a> rating. Thanks for your support!<p>
									<p>Create your account at <a href= "https://reseller.stockunlocks.com/singup.html" target="_blank"> reseller.stockunlocks.com</a> to test your plugin settings. <strong>Status:</strong> <code>StockUnlocks ' . $msg_type . ', Version ' . STOCKUNLOCKS_VERSION . '</code></p>';
					
					return $footer_msg;
				}
			}
		}
	}
	
	public function filter_pre_update_option_settings_suwp_plugin_type( $array ) {
		
		$suwp_license_key = get_option('suwp_license_key');
		$plugin_type = 0;
		if( $suwp_license_key != SUWP_LICENSE_KEY_BASIC && $suwp_license_key != '' ) {
			$plugin_type = 1;	
		}
		$array = $plugin_type;
		return $array; 
	}
	
	public function suwp_check_for_update( $transient ) {

		require_once( WP_PLUGIN_DIR . '/stockunlocks/class-suwp-license-manager-client.php' );
		
		// error_log('....... ********** SUWP_CHECK FOR UPDATES == HANDLER  ..... PLUGIN ....');

		$api_url = SUWP_SOURCE_MANAGER;
		
		$product_id = 'stockunlocks-plugin';
		$product_name = 'StockUnlocks Plugin';
		$text_domain = 'stockunlocks-plugin-text';
		$type = 'plugin';
		$plugin_file = SUWP_PATH . 'stockunlocks.php';

		$suwp_license_key = get_option( 'suwp_license_key' );
		
		if ( !$suwp_license_key == NULL) {
			
			if ( !( $suwp_license_key == SUWP_LICENSE_KEY_BASIC ) ) {
				
				$product_id = 'stockunlocks-plugin-pro';
				$product_name = 'StockUnlocks Pro';
			}
		}

		/* FOR TESTING
		$product_id = 'stockunlocks-test';
		$product_name = 'StockUnlocks Test';
		$text_domain = 'stockunlocks-test-plugin-text';
		$api_endpoint = $api_url;
		$type = 'plugin';
		$plugin_file = '/Applications/XAMPP/xamppfiles/htdocs/stockunlocks/wp-content/plugins/stockunlocks-test/stockunlocks-test.php';
		*/

		$license_manager = new Suwp_License_Manager_Client(
			$product_id,
			$product_name,
			$text_domain,
			$api_url,
			$type,
			$plugin_file
		);
		
		$transient_results = $license_manager->check_for_update( $transient );
	
		return $transient_results;
	}

	public function suwp_plugins_api_handler( $res, $action, $args ) {

		require_once( WP_PLUGIN_DIR . '/stockunlocks/class-suwp-license-manager-client.php' );
		
		if ( $action == 'plugin_information' ) {

				// error_log('....... ********** SUWP_ACTION == PLUGINS API HANDLER  ..... PLUGIN ....');

				$api_url = SUWP_SOURCE_MANAGER;
				
				
				$product_id = 'stockunlocks-plugin';
				$product_name = 'StockUnlocks Plugin';
				$text_domain = 'stockunlocks-plugin-text';
				$type = 'plugin';
				$plugin_file = SUWP_PATH . 'stockunlocks.php';

				$suwp_license_key = get_option( 'suwp_license_key' );
				
				if ( !$suwp_license_key == NULL) {
					
					if ( !( $suwp_license_key == SUWP_LICENSE_KEY_BASIC ) ) {
						
						$product_id = 'stockunlocks-plugin-pro';
						$product_name = 'StockUnlocks Pro';
					}
				}
				

				/* FOR TESTING
				$product_id = 'stockunlocks-test';
				$product_name = 'StockUnlocks Test';
				$text_domain = 'stockunlocks-test-plugin-text';
				$api_endpoint = $api_url;
				$type = 'plugin';
				$plugin_file = '/Applications/XAMPP/xamppfiles/htdocs/stockunlocks/wp-content/plugins/stockunlocks-test/stockunlocks-test.php';
				*/
				
				$license_manager = new Suwp_License_Manager_Client(
					$product_id,
					$product_name,
					$text_domain,
					$api_url,
					$type,
					$plugin_file
				);
				
				$handler_results = $license_manager->plugins_api_handler( $res, $action, $args );
			
				return $handler_results;
				
		}

		// Not our request, let WordPress handle this.
		return false;
	}

	// returns a unique link for downloading a services csv
	public function suwp_get_service_export_link( $provider_id = 0 ) {
		
		$link_href = 'admin-ajax.php?action=suwp_download_services_csv&provider_id='. $provider_id;
		
		// return service link
		return esc_url($link_href);
		
	}
	
	// creates a new service or updates and existing one
	public function suwp_save_service( $service_data ) {
		
		global $woocommerce;
		
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		// get the default values for our options
		$options = $plugin_public->suwp_exec_get_current_options();
		
		error_log('');
		
		// flags for specific services : 'None' or 'Required'
		
		// setup default service id
		// 0 means the service was not saved
		$product_id = 0;
		
		// set up the product array
		// flag: 0 means added, 1 means updated
		$product_array = array(
			'product_id' => $product_id,
			'flag' => '',
		);
		
		try {

			$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
			// since v1.9.3 the API provider may use a text value for the service API ID
			// if text value is available, use it
			$suwp_api_val = $service_data['api'];
			if ( is_string($service_data['apialt']) ) {
				$suwp_api_val = $service_data['apialt'];
			}

			$product_id = $plugin_public->suwp_exec_get_product_id( $service_data['apiproviderid'], $suwp_api_val );
		
			// Writing an array to the log: error_log(print_r($array,true));
			// error_log(print_r($service_data,true));
		
			// IF the product does not already exists...
			if ( !$product_id ) {
				
				// add new product to database
				error_log('----- NON-EXISTING : ADDING NEW PRODUCT TO THE DATABASE ----- ');
				$product_id = $this->suwp_add_product_to_database( $service_data );
				
				$product_array = array(
					'product_id' => $product_id,
					'flag' => 0,
				);
				
			} else {
				error_log('----- PRODUCT ALREADY EXISTED IN THE DATABASE >>> UPDATE ITS VALUES ----- ');
				
				// will update existing product
				$product_array = array(
					'product_id' => $product_id,
					'flag' => 1,
				);
				
			}
			
			$service_credit = $service_data['credit'];
			// moved here 30-aug-18: 
			$service_credit_current = get_post_meta( $product_id, '_suwp_service_credit', true );
			
			// add/update custom meta data
			$flag = $product_array['flag'];
				
			if ( $flag ) {
				
				// added 30-aug-18: the _suwp_service_credit value must always match the Provider's
				// only update if price is different
				if ( $service_credit_current != $service_credit ) {
					update_post_meta( $product_id, '_suwp_service_credit', $service_credit );
				}
				
				// >>> update_post_meta( $post_id, '_suwp_process_time', $product_data['time'] );
				
				// update existing product, check settings for adjusting Product Regular price
				// is this product enabled for custom price adjustment?
				$price_adj = 'NOT YET SET';
				$price_adj_val = get_post_meta( $product_id, '_suwp_price_adj', true );
				
				// Check if the custom field is available.
				if ( ! empty( $price_adj_val ) ) {
					$price_adj = $price_adj_val;
				}
				
				$price = get_post_meta( $product_id, '_regular_price', true );
				
				$service_credit_new = $service_credit;
				$regular_price_current = $price;
				$regular_price_new = 'NO CHANGE';
				$multiplier_custom_enabled = $price_adj;
				// $multiplier_custom_value = '';
				
				$multiplier_global_enabled = 'DISABLED';
				$multiplier_global_value = 'ERROR - ATTEMPTING TO USE VALUES WHILE GLOBAL IS DISABLED!';
				
				error_log( 'Current Product service credit = ' . $service_credit_current);
				error_log( 'Current Remote service credit = ' . $service_credit_new);
				error_log( 'Current Product regular price = ' . $regular_price_current);
				error_log( 'Product automatic price adjustment setting = ' . $multiplier_custom_enabled);
				
				switch( $price_adj ) {
					
					case 'disabled':
						
						// don't make any price adjustments
						
						break;
					case 'custom':
						
						// use the settings found directly on the Product
						$price_adj_custom = get_post_meta( $product_id, '_suwp_price_adj_custom', true );
						$multiplier_custom_value = $price_adj_custom;
						
						error_log( '[CUSTOM] price multiplier = ' . $multiplier_custom_value);
			
						$regular_price = ( (float)$service_credit * (float)$price_adj_custom );
						$regular_price_txt = sprintf("%01.2f", $regular_price);
						
						if ( $woocommerce ) {
							
							// only update if price is different
							if ( $price != $regular_price_txt ) {
								update_post_meta( $product_id, '_suwp_service_credit', $service_credit );
								update_post_meta( $product_id, '_regular_price', $regular_price_txt );
								update_post_meta( $product_id, '_price', $regular_price_txt );
								$regular_price_new = $regular_price_txt;
							}
							
						}
						
						error_log( '[CUSTOM] NEW Product regular price = ' . $regular_price_new);
						
						break;
					case 'global':
						
						// use the settings found in Plugin Options
						$credit = (float)$service_credit;
						$more_equal = (float)$options['suwp_price_range_01']; // more than or equal to
						$more_equal_mult = (float)$options['suwp_price_adj_01']; // more than or equal to multiplier
						$less_equal = (float)$options['suwp_price_range_02']; // less than or equal to
						$less_equal_mult = (float)$options['suwp_price_adj_02']; // less than or equal to multiplier
						$default_mult = (float)$options['suwp_price_adj_default']; // default multiplier
						
						if ( $woocommerce ) {
							
							// check if the global option is enabled
							// 1 = Enabled, '' = disabled
							if ( $options['suwp_price_enabled_01'] === '1' ) {
								
								$multiplier_global_enabled = 'enabled';
								
								if ($credit >= $more_equal) {
									
									$multiplier_global_value = $more_equal_mult;
									
									$regular_price = ( (float)$service_credit * (float)$more_equal_mult );
									$regular_price_txt = sprintf("%01.2f", $regular_price);
											
									// only update if price is different
									if ( $price != $regular_price_txt ) {
										update_post_meta( $product_id, '_suwp_service_credit', $service_credit );
										update_post_meta( $product_id, '_regular_price', $regular_price_txt );
										update_post_meta( $product_id, '_price', $regular_price_txt );
										$regular_price_new = $regular_price_txt;
									}
									
								} elseif ($credit <= $less_equal) {
									
									$multiplier_global_value = $less_equal_mult;
									
									$regular_price = ( (float)$service_credit * (float)$less_equal_mult );
									$regular_price_txt = sprintf("%01.2f", $regular_price);
											
									// only update if price is different
									if ( $price != $regular_price_txt ) {
										update_post_meta( $product_id, '_suwp_service_credit', $service_credit );
										update_post_meta( $product_id, '_regular_price', $regular_price_txt );
										update_post_meta( $product_id, '_price', $regular_price_txt );
										$regular_price_new = $regular_price_txt;
									}
									
								} else {
									
									$multiplier_global_value = $default_mult;
									
									// default multiplier
									$regular_price = ( (float)$service_credit * (float)$default_mult );
									$regular_price_txt = sprintf("%01.2f", $regular_price);
											
									// only update if price is different
									if ( $price != $regular_price_txt ) {
										update_post_meta( $product_id, '_suwp_service_credit', $service_credit );
										update_post_meta( $product_id, '_regular_price', $regular_price_txt );
										update_post_meta( $product_id, '_price', $regular_price_txt );
										$regular_price_new = $regular_price_txt;
									}
									
								} // if ($credit >= $more_equal)
								
							} // if ( $options['suwp_price_enabled_01'] === '1' )
							
							error_log( '[GLOBAL] automatic price adjustment setting = ' . $multiplier_global_enabled);
							error_log( '[GLOBAL] price multiplier = ' . $multiplier_global_value);
							error_log( '[GLOBAL] NEW Product regular price = ' . $regular_price_new);
							
						} // if ( $woocommerce )
					
				} // switch( $price_adj )
			
			} else {
				
				// new import, simply set the Product service credit value (from Supplier)
				update_post_meta( $product_id, '_suwp_service_credit', $service_credit );
				update_post_meta( $product_id, '_regular_price', $service_credit );
				update_post_meta( $product_id, '_price', $service_credit );
				
			}// if ( $flag )
						
		} catch( Exception $e ) {
			
			// a php error occurred
			error_log('----- ERROR - ADDING OR CHECKING FOR PRODUCT TO THE DATABASE ----- ');
			error_log(print_r($e,true));
		}
		
		return $product_array;
		
	}
	
	
	public function suwp_change_price_by_type( $product_id, $multiply_price_by, $price_type ) {
		$the_price = get_post_meta( $product_id, '_' . $price_type, true );
		$the_price *= $multiply_price_by;
		update_post_meta( $product_id, '_' . $price_type, $the_price );
	}
	
	public function suwp_change_price_all_types( $product_id, $multiply_price_by ) {
		$this->suwp_change_price_by_type( $product_id, $multiply_price_by, 'price' );
		$this->suwp_change_price_by_type( $product_id, $multiply_price_by, 'sale_price' );
		$this->suwp_change_price_by_type( $product_id, $multiply_price_by, 'regular_price' );
	}
	
	/*
	 * 'suwp_change_product_price' is main function you should call to change product's price (NOT TESTED)
	 */
	function suwp_change_product_price( $product_id, $multiply_price_by ) {
		$this->suwp_change_price_all_types( $product_id, $multiply_price_by );	
		$product = wc_get_product( $product_id ); // Handling variable products
		if ( $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				$this->suwp_change_price_all_types( $variation['variation_id'], $multiply_price_by );
			}
		}
	}
	
	public function suwp_add_product_to_database( $product_data ) {
	
		$suwp_api_provider = $product_data['apiproviderid'];
		$suwp_api_service_id = $product_data['api'];
		$suwp_api_service_id_alt = $product_data['apialt'];
		$suwp_serial_length = 15;
		
		$user_id = get_current_user_id();
		$category = get_term_by( 'slug', 'suwp_service', 'product_cat' );
		$cat_id = $category->term_id;
		
		$regular_price_txt = sprintf("%01.2f", $product_data['credit']);
		
		// None or Required
		
		$post_id = wp_insert_post( array(
			'post_author' => $user_id,
			'post_title' => $product_data['name'],
			'post_content' => $product_data['info'],
			'post_excerpt' => $product_data['info'],
			'post_status' => 'imported', // draft, publish
			'post_type' => "product",
		) );
		
		wp_set_object_terms( $post_id, $cat_id, 'product_cat' );
		wp_set_object_terms( $post_id, 'simple', 'product_type' );
		update_post_meta( $post_id, '_visibility', 'visible' );
		update_post_meta( $post_id, '_stock_status', 'instock');
		update_post_meta( $post_id, 'total_sales', '0' );
		update_post_meta( $post_id, '_downloadable', 'no' );
		update_post_meta( $post_id, '_virtual', 'yes' );
		update_post_meta( $post_id, '_regular_price', $regular_price_txt );
		update_post_meta( $post_id, '_price', $regular_price_txt );
		update_post_meta( $post_id, '_sale_price', '' );
		update_post_meta( $post_id, '_purchase_note', '' );
		update_post_meta( $post_id, '_featured', 'no' );
		update_post_meta( $post_id, '_weight', '' );
		update_post_meta( $post_id, '_length', '' );
		update_post_meta( $post_id, '_width', '' );
		update_post_meta( $post_id, '_height', '' );
		update_post_meta( $post_id, '_sku', '' );
		update_post_meta( $post_id, '_product_attributes', array() );
		update_post_meta( $post_id, '_sale_price_dates_from', '' );
		update_post_meta( $post_id, '_sale_price_dates_to', '' );
		update_post_meta( $post_id, '_price', '' );
		update_post_meta( $post_id, '_sold_individually', '' );
		update_post_meta( $post_id, '_manage_stock', 'no' );
		update_post_meta( $post_id, '_backorders', 'no' );
		update_post_meta( $post_id, '_stock', '' );
		update_post_meta( $post_id, '_suwp_serial_length', $suwp_serial_length );
		update_post_meta( $post_id, '_suwp_api_provider', $suwp_api_provider );
		update_post_meta( $post_id, '_suwp_api_service_id', $suwp_api_service_id );
		update_post_meta( $post_id, '_suwp_api_service_id_alt', $suwp_api_service_id_alt );
		update_post_meta( $post_id, '_suwp_process_time', $product_data['time'] );
		update_post_meta( $post_id, '_suwp_service_credit', $product_data['credit'] );
		update_post_meta( $post_id, '_suwp_price_group_id', $product_data['groupid'] );
		update_post_meta( $post_id, '_suwp_price_group_name', $product_data['groupname'] );
		update_post_meta( $post_id, '_suwp_service_notes', $product_data['info'] );
		update_post_meta( $post_id, '_suwp_is_network', $product_data['provider'] );
		// since v1.9.5, was never used and shouldn't have been because this field is manual input
		// update_post_meta( $post_id, '_suwp_assigned_network', $product_data['networkprovider'] );
		update_post_meta( $post_id, '_suwp_is_model', $product_data['mobile'] );
		update_post_meta( $post_id, '_suwp_is_pin', $product_data['pin'] );
		update_post_meta( $post_id, '_suwp_is_kbh', $product_data['kbh'] );
		update_post_meta( $post_id, '_suwp_is_mep', $product_data['mep'] );
		update_post_meta( $post_id, '_suwp_is_rm_type', $product_data['type'] );
		update_post_meta( $post_id, '_suwp_is_reference', $product_data['reference'] );
		update_post_meta( $post_id, '_suwp_online_status', 'yes' );
	
		return $post_id;
		
	}
	
	// sample Provider entry
	public function suwp_add_stockunlocks_to_database() {
	
		static $suwp_default_created;
		
		if ( $suwp_default_created === null ) {
				
			$user_id = get_current_user_id();
			
			$post_id = wp_insert_post( array(
				'post_author' => $user_id,
				'post_title' => 'The Real 2',
				'post_content' => '',
				'post_status' => 'publish', // draft, publish
				'post_type' => "suwp_apisource",
			) );
			
			update_post_meta( $post_id, 'suwp_activeflag', '1' );
			update_post_meta( $post_id, 'suwp_sitename', 'StockUnlocks');
			update_post_meta( $post_id, 'suwp_url', 'https://reseller.stockunlocks.com/' );
			update_post_meta( $post_id, 'suwp_username', 'Your assigned username' );
			update_post_meta( $post_id, 'suwp_apikey', 'XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX' );
			update_post_meta( $post_id, 'suwp_apinotes', 'Your Own Mobile Unlocking Website. Please visit https://reseller.stockunlocks.com/singup.html for account creation.' );
			
			$suwp_default_created = $post_id;
				
		}
		
		return $suwp_default_created;
		
	}
	
	// imports new services from our import admin page
	// >>> this function is a form handler and expect services data in the $_POST scope
	public function suwp_import_services() {
		
		error_log( '' );
		error_log( '' );
		error_log('>>>>> ----- START IMPORT SUBMISSION ----- <<<<< ');
		error_log( '' );
		error_log( '' );
		
		$retrieved_posts = get_option('suwp_retrieved_posts');
		error_log('TOTAL = ' . count( $retrieved_posts ) . ' >>>>> ----- RETRIEVED: suwp_retrieved_posts ALL ----- <<<<<' );
		error_log('VIEW = ' . print_r( $retrieved_posts,true ));
		
		// setup our return array
		$result = array(
			'status' => 0,
			'message' => 'Could not import services. ',
			'error' => '',
			'errors' => array(),
		);
		
		try {
			
			// get the provider id to import to
			$api_provider_id = (isset($_POST['suwp_selected_api_provider_id'])) ? (int)$_POST['suwp_selected_api_provider_id'] : 0;
			
			// check api_provider_id contents
			if ( !$api_provider_id ) {
					
				error_log('----- ERROR - NO api_provider_id DETECTED IN IMPORT SUBMISSION, SET TO ZERO ----- ');
	
				$api_provider_id = 0;
				
			} else {
				
				error_log('----- SUCCESS - api_provider_id DETECTED IN IMPORT SUBMISSION >>> ' . $api_provider_id . ' ----- ');
	
			}
			
			$checked_services = (isset($_POST['suwp_selected_service_ids'])) ? (array)$_POST['suwp_selected_service_ids'] : array();
			
			// get the selected services rows to import
			$selected_rows = explode(',', $checked_services[0]);

			$row_count = count($selected_rows);
			
			error_log('----- NUMBER OF ROWS RETURNED FROM IMPORT SUBMISSION >>> ' . $row_count . ' ----- ');
	
			$product_array = array();
						
			// setup a variable for counting the added and modified services
			$added_count = 0;
			$updated_count = 0;
			
			if ( is_array( $selected_rows ) ) {
	
				foreach ( $selected_rows as $api ) {
					
					// 'serviceid' is included when 'all' is selected
					// do a test for is_numeric to exclude it.
					if ( is_numeric( $api ) ) {
						
						$id = intval($api);
						
						error_log('');
						error_log($retrieved_posts[$id]['servicename'] . ' : ----- PROCESSING API ROW DURING IMPORT SUBMISSION >>> ' . $id . ' ----- ');
					
						// build our service data 
						$service_data = array(
							'apiproviderid' => $api_provider_id,
							'api' => $id,
                  			'apialt' => $retrieved_posts[$id]['serviceidalt'],
							'name' => $retrieved_posts[$id]['servicename'],
							'time' => $retrieved_posts[$id]['time'],
							'credit' => $retrieved_posts[$id]['credit'],
							'groupid' => $retrieved_posts[$id]['groupid'],
							'groupname' => $retrieved_posts[$id]['groupname'],
							'info' => $retrieved_posts[$id]['info'],
							'network' => $retrieved_posts[$id]['network'],
							'networkprovider' => $retrieved_posts[$id]['networkprovider'],
							'mobile' => $retrieved_posts[$id]['mobile'],
							'provider' => $retrieved_posts[$id]['provider'],
							'pin' => $retrieved_posts[$id]['pin'],
							'kbh' => $retrieved_posts[$id]['kbh'],
							'mep' => $retrieved_posts[$id]['mep'],
							'prd' => $retrieved_posts[$id]['prd'],
							'type' => $retrieved_posts[$id]['type'],
							'locks' => $retrieved_posts[$id]['locks'],
							'reference' => $retrieved_posts[$id]['reference'],
						);
						
						// add the product to the database
						$product_array = $this->suwp_save_service( $service_data );
						
						// flag: 0 means added, 1 means updated
						$product_id = $product_array['product_id'];
						
						// IF product was created or updated successfully
						if ( $product_id ) {
						
							// updated our added count
							error_log('----- OBTAINED PRODUCT ID DURING IMPORT SUBMISSION >>> ' . $product_id . ' ----- ');
							
							$flag = $product_array['flag'];
							
							if ( $flag ) {
								// updated existing product
								$updated_count++;
							} else {
								// added a new product
								$added_count++;
							}
							
						} else {
							
							error_log('----- ERROR - NO PRODUCT ID OBTAINED DURING IMPORT SUBMISSION, PRODUCT ID IS >>> ' . $product_id . ' ----- ');
							
						} // if ( $product_id ) {
						
					} else {
						
						error_log('----- SKIPPING ROW DURING IMPORT SUBMISSION BECAUSE ITS VALUE IS >>> ' . $api . ', NOT NUMERIC ----- ');
	
					} // if ( is_numeric( $id ) && !empty( $id ) ) {
				}
				
				// IF no products were actually added or updated...
				if( $added_count == 0 && $updated_count == 0):
				
					error_log('----- ERROR - NO SERVICES IMPORTED OR UPDATED INTO TO THE DATABASE >>> MODIFY wp-config.php or php.ini ----- ');
				
					// return error message
					$result['error'] = 'No services were imported or updated. Please select fewer services or modify memory settings in wp-config.php or php.ini';
				
				else:
				
					error_log('----- SUCCESS - ADDED OR UPDATED SERVICES IN THE DATABASE TOTAL >>> ADDED = ' . $added_count . ', UPDATED = ' . $updated_count . ' ----- ');
				
					// IF products were added...
					// return success!
					$result = array(
						'status' => 1,
						'message' => 'SUCCESS - Service(s) : ' . $added_count .' imported, ' . $updated_count . ' updated.',
						'error' => '',
						'errors' => array(),
					);
				
				endif;
			
			} // if ( is_array( $selected_rows ) ) 
		
		} catch( Exception $e ) {
			
			// php error
			error_log('----- PHP ERROR: suwp_import_services() ----- ');
			error_log(print_r($e,true));
			
		}
		
		error_log( '' );
		error_log( '' );
		error_log( '>>>>> ----- END IMPORT SUBMISSION ----- <<<<<' );
		error_log( '' );
		error_log( '' );
		
		update_option('suwp_retrieved_posts', array() );
		
		// find the log and send it
		
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		// return the result as json
		$plugin_public->suwp_exec_return_json( $result );
		
	}
	
	// this function retrieves services data from the remote server in the form of a php array
	// it then returns that array in a json formatted object
	// this function is an ajax post form handler
	// expects: $_POST['suwp_import_provider_list_id']
	public function suwp_parse_import_api() {
		
		error_log( '' );
		error_log( '' );
		error_log('>>>>> ----- START RETRIEVE ... ----- <<<<< ');
		error_log( '' );
		error_log( '' );
		
		// setup our return array
		$result = array(
			'status' => 0,
			'provider_id' => '',
			'message' => 'Could not import remote services. ',
			'error' => '',
			'data1' => array(),
			'data2' => array(),
		);
		
		try {
		
			// get the provider id from $_POST['suwp_import_provider_list_id']
			$provider_id = (isset($_POST['suwp_import_provider_list_id'])) ? esc_attr( $_POST['suwp_import_provider_list_id'] ) : 0;
			
			error_log( '' );
			error_log( '' );
			error_log('>>>>> ----- RETRIEVE: PROVIDER ID ----- <<<<<  = ' . $provider_id);
			error_log( '' );
			error_log( '' );
			
			$api_id = (int)sanitize_text_field($provider_id);
			
			if ( ! $api_id ) {
				$api_id = 0;
			}
			
			$reply = $this->suwp_api_ui_action( $api_id, $api_action_id = 1 );
			
			// $reply[0] = $reply_part
			// $reply[1] = $reply_all
			
			// if there is a reply
			if( !empty($reply) ):
				
				update_option( 'suwp_retrieved_posts', $reply[1] );
				
				// setup our return array
				$result = array(
					'status'=> 1,
					'provider_id' => $provider_id,
					'message'=> 'Imported remote services successfully.',
					'error'=> '',
					'data1'=> $reply[0],
					'data2'=> $reply[1],
				);
				
			else:
			
				// return an error message if we could not retrieve the file
				$result['error']='Failed connection to the remote server or no assigned services available. ';
				$result['data1']=$reply;
				
			endif;
			
		} catch( Exception $e ) {
			
			// php error
				
			error_log( '' );
			error_log( '' );
			error_log('>>>>> ----- RETRIEVE: PHP ERROR ----- <<<<<  = ' . print_r( $e, true ) );
			error_log( '' );
			error_log( '' );
		
		}
		
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		// return the result as json
		$plugin_public->suwp_exec_return_json( $result );
		
	}
	
	// generates a .csv file of services data
	// expects $_GET['provider_id'] to be set in the URL
	public function suwp_download_services_csv() {
	
		//Get current user
		global $current_user ;
		$user_id = $current_user->ID;

		// reset flag to remove the "No Unlocking Products to export." notice
		update_user_meta( $user_id, 'suwp_services_export_flag', '1' );
		
		// get the provider id from the URL scope
		$provider_id = ( isset($_GET['provider_id']) ) ? (int)$_GET['provider_id'] : 0;
		
		// setup our return data
		$csv = '';
		
		// get the provider object
		$provider = get_post( $provider_id );
		
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		// get the provider's services or get all services if no provider id is given
		$services = $plugin_public->suwp_exec_get_provider_services( $provider_id );
		
		// IF we have confirmed services
		if( $services !== false ):
			
			// get the current date
			$now = new DateTime();
			
			// setup a unique filename for the generated export file
			$fn1 = 'stockunlocks-export-provider_id-'. $provider_id .'-date-'. $now->format('Ymd'). '.csv';
			$fn2 = WP_PLUGIN_DIR .'/stockunlocks/exports/'.$fn1;
			
			// open new file in write mode
			$fp = fopen($fn2, 'w');
			
			$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
			// get the first services' data
			$service_data = $plugin_public->suwp_exec_get_service_data( $services[0] );
			
			// remove the subscriptions and name column from the data
			// unset($service_data['subscriptions']);
			// unset($service_data['name']);
			
			// build our csv headers array from $service_data's data keys
			$csv_headers = array();
			foreach( $service_data as $key => $value ):
				array_push($csv_headers, $key);
			endforeach;
			
			// append $csv_headers to our csv file
			fputcsv($fp, $csv_headers);
		
			// loop over all our services
			foreach( $services as &$service_id ):
					
				$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
				
				// get the service data of the current service
				$service_data = $plugin_public->suwp_exec_get_service_data( $service_id );
			
				// remove the subscriptions and name columns from the data
				// unset($service_data['subscriptions']);
				// unset($service_data['name']);
				
				// append this services' data to our csv file
				fputcsv($fp, $service_data);
			
			endforeach;
			
			// read open our new file is read mode
			$fp = fopen($fn2, 'r');
			// read our new csv file and store it's contents in $fc
			$fc = fread($fp, filesize($fn2) );
			// close our open file pointer
			fclose($fp);
		
			// setup file headers
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=".$fn1);
			// echo the contents of our file and return it to the browser
			echo($fc);
			// exit php processes 
			exit;
			
		else:
			
			wp_redirect(admin_url('/admin.php?page=suwp_dashboard_admin_page'), 302);
			
			// set the flag to display the "No Unlocking Products to export." notice
			update_user_meta( $user_id, 'suwp_services_export_flag', '0' );
			
			// stop all other processing 
			exit;
		
		endif;
		
		// return false if we were unable to download our csv
		return false;
		
	}
	
	/**
	* Checks the current version of wordpress and displays
	* message in the plugin page if the version is untested
	*/
	public function suwp_check_wp_version() {
		
		global $pagenow;
		
		
		if ( $pagenow == 'plugins.php' && is_plugin_active('stockunlocks/stockunlocks.php') ):
		
			// get the wp version
			$wp_version = get_bloginfo('version');
			
			// tested vesions
			// these are the versions we've tested our plugin in
			$tested_versions = array(
				'4.2.0',
				'4.2.1',
				'4.2.2',
				'4.2.3',
				'4.2.4',
				'4.2.5',
				'4.2.6',
				'4.7',
				'4.7.1',
			);
			
			$tested_range = array(4.0,4.6);
			
			// IF the current wp version is  in our tested versions...
			if( (float)$wp_version >= (float)$tested_range[0] && (float)$wp_version <= (float)$tested_range[1] ):
			
				// we're good!
			
			else:
				
				// get notice html
				// $notice = suwp_exec_get_admin_notice('StockUnlocks has not been tested in your version of WordPress. It may still work though...','error');
				$notice = do_action('suwp_exec_get_admin_notice','StockUnlocks has not been tested in your version of WordPress. It may still work though...','error');
				
				// echo the notice html
				// echo( $notice );
				
			endif;
		
		endif;
		
	}
	
	/**
	* Returns html formatted
	* for WP admin notices
	*/
	public function suwp_exec_get_admin_notice( $message, $class ) {
		
		// setup our return variable
		$output = '';
		
		try {
			
			// create output html
			$output = '
			 <div class="'. $class .'">
				<p>'. $message .'</p>
			</div>
			';
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return output
		return $output;
		
	}
	
	public function suwp_no_services_admin_notice() {
	
		//Get current user
		global $current_user ;
		$user_id = $current_user->ID;

		$screen = get_current_screen();
		
		if ( $screen->id == 'toplevel_page_suwp_dashboard_admin_page' ) {
			
			if ( get_user_meta($user_id, 'suwp_services_export_flag' ) ) {

				if ( get_user_meta($user_id, 'suwp_services_export_flag', true ) == '0') {
					$notice = $this->suwp_exec_get_admin_notice('No Unlocking Products to export.','notice notice-warning is-dismissible');
					echo $notice;
				}
			}
		}
	}
	
	public function suwp_troubleshooting_admin_notice() {
		
		$screen = get_current_screen();
		$redirect_url = '';
		
		if ( $screen->id == 'stockunlocks_page_suwp_importservices_admin_page' ) {
			$redirect_url = '. <a href="'. admin_url('/admin.php?page=suwp_options_admin_page&tab=general_options&section=troubleshooting') .'">Disable here</a>';
		}
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$troubleshoot_items = $options['suwp_manage_troubleshoot_run_id'];
		// parent_base ; parent_file ; suwp_dashboard_admin_page ; suwp_importservices_admin_page
		if ( ( $screen->id == 'stockunlocks_page_suwp_importservices_admin_page' || $screen->id == 'stockunlocks_page_suwp_options_admin_page' ) && $troubleshoot_items > 0 ) {
				$notice = $this->suwp_exec_get_admin_notice('Troubleshooting Option is enabled. Now limiting Service Imports to : ' . $troubleshoot_items . '' . $redirect_url,'notice notice-error ');
				echo $notice;
		}
	}
	
	public function suwp_cron_disabled_admin_notice() {
		
		$screen = get_current_screen();
		$redirect_url = '';
		
		if ( $screen->id !== 'stockunlocks_page_suwp_options_admin_page' ) {
			$redirect_url = '<a href="'. admin_url('/admin.php?page=suwp_options_admin_page&tab=general_options') .'">Enable here</a>';
		}
		
		// get the default values for our options
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

		$options = $plugin_public->suwp_exec_get_current_options();
		
		$cron_run = $options['suwp_manage_cron_run_id'];
		
		if ( ( $screen->parent_file == 'suwp_dashboard_admin_page' ) && $cron_run == '') {
				$notice = $this->suwp_exec_get_admin_notice('Cron is disabled for the StockUnlocks plugin. No orders are being processed with your Provider(s). Automated emails are not being sent out. '. $redirect_url,'notice notice-error ');
				echo $notice;
		}
	}
	
	/**
	* Registers all our plugin options
	* to be used throughout 
	*/
	public function suwp_register_options() {
		
		// make sure that we can use session variables
		/** DISABLED
		if (!session_id()) {
			session_start(['read_and_close' => true,]);
		}
		*/

		// get plugin options settings
		$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		$options = $plugin_public->suwp_exec_get_options_settings();
		$options_default = $plugin_public->suwp_get_default_options();
		
		// loop over settings
		foreach( $options['settings'] as $setting ):
		
			// register this setting
			register_setting($options['group'], $setting);
		
		endforeach;
		
		$options = $plugin_public->suwp_exec_get_current_options();
		
		// tab content for plugin options
		
		$this->suwp_initialize_options_license($options);
		$this->suwp_initialize_options_cron($options);
		$this->suwp_initialize_options_troubleshoot($options);
		$this->suwp_initialize_options_product_sync($options);
		$this->suwp_initialize_options_libraries($options);
		$this->suwp_initialize_options_fieldlabels($options, $options_default);
		$this->suwp_initialize_options_textmessages($options, $options_default);
		$this->suwp_initialize_options_ordersuccess($options);
		$this->suwp_initialize_options_orderavailable($options);
		$this->suwp_initialize_options_orderrejected($options);
		$this->suwp_initialize_options_ordererror($options);
		$this->suwp_initialize_options_checkerror($options);

		$this->suwp_notice_ignore();
		
	}
	
	/* This is the action that allows
	* the user to dismiss the banner
	* it basically sets a tag to their 
	* user meta data
	*/
	public function suwp_notice_ignore() {
		
		//Get the global user
		global $current_user;
		$user_id = $current_user->ID;
		
		/* If user clicks to ignore the notice, 
		* add that to their user meta 
		* the banner then checks whether this tag
		* exists already or not.
		* See here: http://codex.wordpress.org/Function_Reference/add_user_meta
		*/
		// standard dismissable banner for plugin page after plugin activation
		if ( isset($_GET['suwp_notice_ignore']) && '0' == $_GET['suwp_notice_ignore'] ) {
		   add_user_meta($user_id, 'suwp_activation_ignore_notice', 'true', true);
		}
		
		// remote dismissable banner for plugin page only
		$dismiss_1_key = '';
		$extract = get_option('suwp_author_info');
		
		if( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$dismiss_1_key = $extract->dismiss_1_key;
				
				if ( isset($_GET[$dismiss_1_key]) && '0' == $_GET[$dismiss_1_key] ) {
				   add_user_meta($user_id, $dismiss_1_key, 'true', true);
				}
			}
		}
	}
	
	public function suwp_initialize_options_license($options) {

		update_option( 'suwp_plugin_type', 'initialize' );

		$product_sync_label = '<em><code>Product Sync Schedule</code></em></span>';
		$msg_license = '';
		$pro_link = '<a href="https://www.stockunlocks.com/product/stockunlocks-plugin-pro/" target="_blank"> Upgrade to StockUnlocks Pro </a>';
		$msg_type = 'Basic';
		if( get_option( 'suwp_plugin_type' ) ) {
			$pro_link = '';
			$product_sync_label = 'Product Sync Schedule';
			$msg_type = 'Pro (v'. STOCKUNLOCKS_VERSION . ')';
			$valid_until = get_option( 'suwp_valid_until' );
			if( $valid_until != NULL ) {
				$msg_type = 'Pro (v'. STOCKUNLOCKS_VERSION . '), Expires: ' . $valid_until;
			}
			$msg_license = '<strong>Status</strong>: <code>StockUnlocks ' . $msg_type . '</code>';
		} else {
			$msg_license = '<p><strong>Status</strong>: <code>StockUnlocks ' . $msg_type . ' (v' . STOCKUNLOCKS_VERSION . ')</code> ' . $pro_link . '</p>Enter your registered license information to enable <strong>Pro Features</strong>.<p>Otherwise: <strong><span style="color:#FF0000">DO NOT CHANGE</span></strong>.</p>';
			// with a flick of a switch ... $msg_license = '<p><strong>Status</strong>: <code>StockUnlocks ' . $msg_type . ' (v' . STOCKUNLOCKS_VERSION . ')</code> ' . $pro_link . '</p>Enter your registered license information to enable <strong>Pro Features</strong>. <p><strong><span style="color:#FF0000">AS OF MM nST, YYYY, StockUnlocks Basic will no longer be supported.</span></strong></p> <p><strong><span style="color:#FF0000">After this date, a Pro License will be required to use this plugin.</span></strong></p>';
		}
		
		$license_email = $options['suwp_license_email'];
		$license_key = $options['suwp_license_key'];

		add_settings_section(
			'suwp_license_section',         // ID used to identify this section and with which to register options
			'LICENSE: StockUnlocks Plugin', // Title to be displayed on the administration page
			'suwp_license_callback',        // Callback used to render the description of the section
			'suwp_license_options'          // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_status_license',          // ID used to identify the field throughout the theme
			'License Details',              // The label to the left of the option interface element
			'suwp_status_license_callback', // The name of the function responsible for rendering the option interface
			'suwp_license_options',         // The page on which this option will be displayed
			'suwp_license_section',         // The name of the section to which this field belongs
			$msg_license                    // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_license_email',                     
			'License e-mail address',              
			'suwp_license_email_callback',  
			'suwp_license_options',                          
			'suwp_license_section', // The name of the section to which this field belongs
			$license_email          // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_license_key',                      
			'License key',               
			'suwp_license_key_callback',   
			'suwp_license_options',                          
			'suwp_license_section', // The name of the section to which this field belongs
			$license_key            // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_license_callback($args) {
			
			$html = '<p>StockUnlocks Plugin License Details.</p>';
			
			echo $html;

		} // end suwp_license_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_status_license_callback($args) {
			
			echo $args;
			
		} // end suwp_status_license_callback

		function suwp_license_email_callback($args) {
			
			$html = '<input type="email" id="suwp_license_email" name="suwp_license_options[suwp_license_email]" style="width: 25em" value="'. strtolower( $args ) .'" class="" />
			<p class="description" id="suwp_license_email-description">Registered e-mail address.</p>';
			
			$html .= '<input type="hidden" name="suwp_license_email" value="'. strtolower( $args ) .'" class="" />';
			
			echo $html;
			
		} // end suwp_license_email_callback
		
		function suwp_license_key_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<input type="text" id="suwp_license_key" name="suwp_license_options[suwp_license_key]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_license_key-description">Provided license key.</p>';
			
			$html .= '<input type="hidden" name="suwp_license_key" value="'. $args .'" class="" />';
			
			echo $html;
			
		} // end suwp_license_key_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_license_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_license_options',
			'suwp_license_options',
			$args_validate
		);
		
		function suwp_license_validate($input) {

			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			$show_success = true;

			if( isset( $input['suwp_license_email'] ) )
				update_option( 'suwp_license_email', $input['suwp_license_email']);

			if( isset( $input['suwp_license_key'] ) )
				update_option( 'suwp_license_key', $input['suwp_license_key'] );

			require_once( WP_PLUGIN_DIR . '/stockunlocks/class-suwp-license-manager-client.php' );

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
						$t = date_create( $valid_until );
						$suwp_valid_until = $t->format( 'd-M-Y' );
					}
					update_option( 'suwp_valid_until', $suwp_valid_until );
				} else {
					$show_success = false;
				}
			} else {
				$show_success = false;
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

			update_option( 'suwp_plugin_type', 'finalize' );
			
			if ( $show_success ) {
				add_user_meta($user_id, 'suwp_options_saved', 'true', true);
			}

			return $input;
		}
		
	} // end suwp_initialize_options_license

	public function suwp_initialize_options_cron($options) {

		$manage_cron_run_id = $options['suwp_manage_cron_run_id'];

		$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
		$cron_run_id = $plugin_admin->suwp_get_cron_options_select( 'suwp_cron_options[suwp_manage_cron_run_id]', 'suwp_manage_cron_run_id', $manage_cron_run_id );
		
		add_settings_section(
			'suwp_cron_section',    // ID used to identify this section and with which to register options
			'Manage Cron Schedule', // Title to be displayed on the administration page
			'suwp_cron_callback',   // Callback used to render the description of the section
			'suwp_cron_options'     // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_manage_cron_run_id',          // ID used to identify the field throughout the theme
			'Frequency',                        // The label to the left of the option interface element
			'suwp_manage_cron_run_id_callback', // The name of the function responsible for rendering the option interface
			'suwp_cron_options',                // The page on which this option will be displayed
			'suwp_cron_section',                // The name of the section to which this field belongs
			$cron_run_id                        // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_cron_callback($args) {
			
			$html = '<p>Cron Schedule Details.</p>';
			
			// echo $html;

		} // end suwp_cron_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_manage_cron_run_id_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = $args;

			$html .= '<p class="description" id="suwp_manage_cron_run_id-description">This setting controls how often StockUnlocks will process and check on orders. <br />
			IMPORTANT: When set to \'- Cron Disabled -\', no orders will be processed and no automated messages will be sent to customers or administrators.</p>';
					
			echo $html;
			
		} // end suwp_manage_cron_run_id_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_cron_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_cron_options',
			'suwp_cron_options',
			$args_validate
		);
		
		function suwp_cron_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_manage_cron_run_id'] ) )
				update_option( 'suwp_manage_cron_run_id', $input['suwp_manage_cron_run_id']);

			
			return $input;
		}
		
	} // end suwp_initialize_options_cron

	public function suwp_initialize_options_troubleshoot($options) {

		$manage_troubleshoot_run_id = $options['suwp_manage_troubleshoot_run_id'];

		$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
		$troubleshoot_run_id = $plugin_admin->suwp_get_troubleshoot_options_select( 'suwp_troubleshoot_options[suwp_manage_troubleshoot_run_id]', 'suwp_manage_troubleshoot_run_id', $manage_troubleshoot_run_id );
		
		add_settings_section(
			'suwp_troubleshoot_section',  // ID used to identify this section and with which to register options
			'Troubleshooting Option',     // Title to be displayed on the administration page
			'suwp_troubleshoot_callback', // Callback used to render the description of the section
			'suwp_troubleshoot_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_manage_troubleshoot_run_id',          // ID used to identify the field throughout the theme
			'Status',                                   // The label to the left of the option interface element
			'suwp_manage_troubleshoot_run_id_callback', // The name of the function responsible for rendering the option interface
			'suwp_troubleshoot_options',                // The page on which this option will be displayed
			'suwp_troubleshoot_section',                // The name of the section to which this field belongs
			$troubleshoot_run_id                        // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_troubleshoot_callback($args) {
			
			$html = '<p>Troubleshooting Details.</p>';
			
			// echo $html;

		} // end suwp_troubleshoot_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_manage_troubleshoot_run_id_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = $args;

			$html .= '<p class="description" id="suwp_manage_troubleshoot_run_id-description">When enabled, StockUnlocks will limit the number of Services to be imported from a Provider. <br />
			<span style="color:#FF0000">IMPORTANT</span>: This is only used when trying to resolve memory issues while importing services. Set to \'- Disabled -\' to retrieve all services.</p>';

			echo $html;
			
		} // end suwp_manage_troubleshoot_run_id_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_troubleshoot_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_troubleshoot_options',
			'suwp_troubleshoot_options',
			$args_validate
		);
		
		function suwp_troubleshoot_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_manage_troubleshoot_run_id'] ) )
				update_option( 'suwp_manage_troubleshoot_run_id', $input['suwp_manage_troubleshoot_run_id']);

			return $input;
		}
		
	} // end suwp_initialize_options_troubleshoot

	public function suwp_initialize_options_product_sync($options) {

		$product_sync_label = '<em><code>Product Sync Schedule</code></em></span>';
		$msg_license = '';
		$pro_link = '<a href="https://www.stockunlocks.com/product/stockunlocks-plugin-pro/" target="_blank"> Upgrade to StockUnlocks Pro </a>';
		$msg_type = 'Basic';
		if( get_option( 'suwp_plugin_type' ) ) {
			$pro_link = '';
			$product_sync_label = 'Product Sync Schedule';
			$msg_type = 'Pro (v'. STOCKUNLOCKS_VERSION . ')';
			$valid_until = get_option( 'suwp_valid_until' );
			if( $valid_until != NULL ) {
				$msg_type = 'Pro (v'. STOCKUNLOCKS_VERSION . '), Expires: ' . $valid_until;
			}
			$msg_license = '<strong>Status</strong>: <code>StockUnlocks ' . $msg_type . '</code>';
		} else {
			$msg_license = '<p><strong>Status</strong>: <code>StockUnlocks ' . $msg_type . ' (v' . STOCKUNLOCKS_VERSION . ')</code> ' . $pro_link . '</p>Enter your registered license information to enable <strong>Pro Features</strong>.<p>Otherwise: <strong><span style="color:#FF0000">DO NOT CHANGE</span></strong>.</p>';
		}
		
		$selected_product_sync_run_id = $options['suwp_manage_product_sync_run_id'];
		$selected_price_enabled_01 = $options['suwp_price_enabled_01'];

		$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
		$product_sync_run_id = $plugin_admin->suwp_get_product_sync_options_select( 'suwp_product_sync_options[suwp_manage_product_sync_run_id]', 'suwp_manage_product_sync_run_id', $selected_product_sync_run_id );
		$price_enabled_01 = $plugin_admin->suwp_get_price_enabled_options_01( 'suwp_product_sync_options[suwp_price_enabled_01]', 'suwp_price_enabled_01', $selected_price_enabled_01 );

		$price_range_01 = $options['suwp_price_range_01'];
		$price_range_02 = $options['suwp_price_range_02'];
		$price_adj_01 = $options['suwp_price_adj_01'];
		$price_adj_02 = $options['suwp_price_adj_02'];
		$price_adj_default = $options['suwp_price_adj_default'];

		add_settings_section(
			'suwp_product_sync_section',  // ID used to identify this section and with which to register options
			'Product Options',            // Title to be displayed on the administration page
			'suwp_product_sync_callback', // Callback used to render the description of the section
			'suwp_product_sync_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_manage_product_sync_run_id',          // ID used to identify the field throughout the theme
			$product_sync_label,                        // The label to the left of the option interface element
			'suwp_manage_product_sync_run_id_callback', // The name of the function responsible for rendering the option interface
			'suwp_product_sync_options',                // The page on which this option will be displayed
			'suwp_product_sync_section',                // The name of the section to which this field belongs
			array( $product_sync_run_id,                // The arguments to pass to the callback.
			$pro_link
			)
		);
		
		add_settings_field( 
			'suwp_price_enabled_01',          // ID used to identify the field throughout the theme
			'Price Adjustment Option',        // The label to the left of the option interface element
			'suwp_price_enabled_01_callback', // The name of the function responsible for rendering the option interface
			'suwp_product_sync_options',      // The page on which this option will be displayed
			'suwp_product_sync_section',      // The name of the section to which this field belongs
			array( $price_enabled_01,         // The arguments to pass to the callback.
			$price_range_01,
			$price_range_02,
			$price_adj_01,
			$price_adj_02,
			$price_adj_default
			)
		);

		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_product_sync_callback($args) {
			
			$html = '<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';
			
			echo $html;

		} // end suwp_product_sync_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_manage_product_sync_run_id_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = $args[0];

			$html .= '<p class="description" id="suwp_manage_product_run_id-description"><strong>Automatic Price Updating</strong>: This setting controls how often StockUnlocks will automatically synchronize your imported Products with your Provider. <br />
			When set to \'- Auto Sync Disabled -\', prices for imported Products WILL NOT automatically adjust when your Provider updates their price(s).</p>
			<p class="description" id="suwp_manage_product_run_id-description">You need to also enable the settings in the <strong>\'Price Adjustment Option\'</strong> section below OR enable the  <strong>\'Auto Adjust Price\'</strong> setting on the individual Product.</p>' . $args[1];

			echo $html;
			
		} // end suwp_manage_product_sync_run_id_callback
		
		function suwp_price_enabled_01_callback($args) {
			
			$html = $args[0];

			$html .= '
			<p>When source credit is <strong>more than</strong> or equal to <input type="number" id="suwp_price_range_01" name="suwp_product_sync_options[suwp_price_range_01]" style="width: 5em" min="0" step="0.01" value="'. $args[1] .'" class="" />, multiply my price by 
			<input type="number" id="suwp_price_adj_01" name="suwp_product_sync_options[suwp_price_adj_01]" style="width: 6em" min="1" step="0.01" value="'. $args[3] .'" class="" /> <br />
			
			When source credit is <strong>less than</strong> or equal to <input type="number" id="suwp_price_range_02" name="suwp_product_sync_options[suwp_price_range_02]" style="width: 5em" min="0" step="0.01" value="'. $args[2] .'" class="" />, multiply my price by 
			<input type="number" id="suwp_price_adj_02" name="suwp_product_sync_options[suwp_price_adj_02]" style="width: 6em" min="1" step="0.01" value="'. $args[4] .'" class="" />
			</p><br />
			
			<p><strong>Default</strong>: multiply my price by <input type="number" id="suwp_price_adj_default" name="suwp_product_sync_options[suwp_price_adj_default]" style="width: 6em" min="1" step="0.01" value="'. $args[5] .'" class="" /> when the settings above do not apply.</p><br />';

			echo $html;
			
		} // end suwp_price_enabled_01_callback

		function suwp_price_adj_02_callback($args) {
			
			$html = $args[0];

			$html .= '
			<p>When source credit is <strong>more than</strong> or equal to <input type="number" id="suwp_price_range_01" name="suwp_product_sync_options[suwp_price_range_01]" style="width: 5em" min="0" step="0.01" value="'. $args[1] .'" class="" />, multiply my price by 
			<input type="number" id="suwp_price_adj_01" name="suwp_product_sync_options[suwp_price_adj_01]" style="width: 6em" min="1" step="0.01" value="'. $args[3] .'" class="" /> <br />
			
			When source credit is <strong>less than</strong> or equal to <input type="number" id="suwp_price_range_02" name="suwp_product_sync_options[suwp_price_range_02]" style="width: 5em" min="0" step="0.01" value="'. $args[2] .'" class="" />, multiply my price by 
			<input type="number" id="suwp_price_adj_02 name="suwp_product_sync_options[suwp_price_adj_02]" style="width: 6em" min="1" step="0.01" value="'. $args[4] .'" class="" />
			</p><br />
			
			<p><strong>Default</strong>: multiply my price by <input type="number" id="suwp_price_adj_default" name="suwp_product_sync_options[suwp_price_adj_default]" style="width: 6em" min="1" step="0.01" value="'. $args[5] .'" class="" /> when the settings above do not apply.</p><br />';

			echo $html;
			
		} // end suwp_price_enabled_01_callback

		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_product_sync_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_product_sync_options',
			'suwp_product_sync_options',
			$args_validate
		);
		
		function suwp_product_sync_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_price_enabled_01'] ) )
				update_option( 'suwp_price_enabled_01', $input['suwp_price_enabled_01']);

			if( isset( $input['suwp_manage_product_sync_run_id'] ) )
				update_option( 'suwp_manage_product_sync_run_id', $input['suwp_manage_product_sync_run_id']);

			if( isset( $input['suwp_price_range_01'] ) )
				update_option( 'suwp_price_range_01', $input['suwp_price_range_01']);

			if( isset( $input['suwp_price_range_02'] ) )
				update_option( 'suwp_price_range_02', $input['suwp_price_range_02']);

			if( isset( $input['suwp_price_adj_01'] ) )
				update_option( 'suwp_price_adj_01', $input['suwp_price_adj_01']);

			if( isset( $input['suwp_price_adj_02'] ) )
				update_option( 'suwp_price_adj_02', $input['suwp_price_adj_02']);

			if( isset( $input['suwp_price_adj_default'] ) )
				update_option( 'suwp_price_adj_default', $input['suwp_price_adj_default']);
			
			return $input;
		}
		
	} // end suwp_initialize_options_product_sync


	public function suwp_initialize_options_libraries($options) {

		$manage_acf_menu_enabled = $options['suwp_manage_acf_menu_enabled'];

		$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
		$acf_menu_setting = $plugin_admin->suwp_get_acf_setting( 'suwp_acf_options[suwp_manage_acf_menu_enabled]', 'suwp_manage_acf_menu_enabled', $manage_acf_menu_enabled );
		
		add_settings_section(
			'suwp_acf_section',  // ID used to identify this section and with which to register options
			'ACF Menu Options',     // Title to be displayed on the administration page
			'suwp_acf_menu_callback', // Callback used to render the description of the section
			'suwp_acf_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_manage_acf_menu_enabled',          // ID used to identify the field throughout the theme
			'Status',                                   // The label to the left of the option interface element
			'suwp_manage_acf_menu_enabled_callback', // The name of the function responsible for rendering the option interface
			'suwp_acf_options',                // The page on which this option will be displayed
			'suwp_acf_section',                // The name of the section to which this field belongs
			$acf_menu_setting                        // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_acf_menu_callback($args) {
			
			$html = '<p>ACF Menu Details.</p>';
			
			// echo $html;

		} // end suwp_acf_menu_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_manage_acf_menu_enabled_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = $args;

			$html .= '<p class="description" id="suwp_manage_acf_menu_enabled-description">Update this in order to display or hide the Advanced Custom Fields (ACF) menu while using the StockUnlocks plugin. <br />
			<span style="color:#FF0000">NOTE</span>: This is for convenience only, it does not affect the functionality of the StockUnlocks or the ACF plugins.</p>';

			echo $html;
			
		} // end suwp_manage_acf_menu_enabled_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_acf_menu_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_acf_options',
			'suwp_acf_options',
			$args_validate
		);
		
		function suwp_acf_menu_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_manage_acf_menu_enabled'] ) )
				update_option( 'suwp_manage_acf_menu_enabled', $input['suwp_manage_acf_menu_enabled']);

			return $input;
		}
		
	} // end suwp_initialize_options_libraries

	public function suwp_initialize_options_fieldlabels($options, $options_default) {
		
		$service_default = $options_default['suwp_service_fieldlabel'];
		$imei_default = $options_default['suwp_imei_fieldlabel'];
		$sn_default = $options_default['suwp_sn_fieldlabel'];
		$country_default = $options_default['suwp_country_fieldlabel'];
		$network_default = $options_default['suwp_network_fieldlabel'];
		$brand_default = $options_default['suwp_brand_fieldlabel'];
		$model_default = $options_default['suwp_model_fieldlabel'];
		$mep_default = $options_default['suwp_mep_fieldlabel'];
		$kbh_default = $options_default['suwp_kbh_fieldlabel'];
		$activation_default = $options_default['suwp_activation_fieldlabel'];
		$emailresponse_default = $options_default['suwp_emailresponse_fieldlabel'];
		$emailconfirm_default = $options_default['suwp_emailconfirm_fieldlabel'];
		$deliverytime_default = $options_default['suwp_deliverytime_fieldlabel'];
		$code_default = $options_default['suwp_code_fieldlabel'];

		$service_fieldlabel = $options['suwp_service_fieldlabel'];
		$imei_fieldlabel = $options['suwp_imei_fieldlabel'];
		$sn_fieldlabel = $options['suwp_sn_fieldlabel'];
		$country_fieldlabel = $options['suwp_country_fieldlabel'];
		$network_fieldlabel = $options['suwp_network_fieldlabel'];
		$brand_fieldlabel = $options['suwp_brand_fieldlabel'];
		$model_fieldlabel = $options['suwp_model_fieldlabel'];
		$mep_fieldlabel = $options['suwp_mep_fieldlabel'];
		$kbh_fieldlabel = $options['suwp_kbh_fieldlabel'];
		$activation_fieldlabel = $options['suwp_activation_fieldlabel'];
		$emailresponse_fieldlabel = $options['suwp_emailresponse_fieldlabel'];
		$emailconfirm_fieldlabel = $options['suwp_emailconfirm_fieldlabel'];
		$deliverytime_fieldlabel = $options['suwp_deliverytime_fieldlabel'];
		$code_fieldlabel = $options['suwp_code_fieldlabel'];


		$manage_display_email_setting = $options['suwp_manage_response_email_display'];
		$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
		$display_email_setting = $plugin_admin->suwp_get_response_email_setting( 'suwp_fieldlabel_options[suwp_manage_response_email_display]', 'suwp_manage_response_email_display', $manage_display_email_setting );
		
		add_settings_section(
			'suwp_fieldlabel_section',  // ID used to identify this section and with which to register options
			'FIELD LABELS',            // Title to be displayed on the administration page
			'suwp_fieldlabel_callback', // Callback used to render the description of the section
			'suwp_fieldlabel_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_service_fieldlabel',                        
			'Service Field Label',                            
			'suwp_service_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($service_fieldlabel,
			$service_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_imei_fieldlabel',                        
			'IMEI Field Label',                            
			'suwp_imei_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($imei_fieldlabel,
			$imei_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_sn_fieldlabel',                        
			'Serial Number Field Label',                            
			'suwp_sn_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($sn_fieldlabel,
			$sn_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_country_fieldlabel',                        
			'Country Field Label',                            
			'suwp_country_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($country_fieldlabel,
			$country_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_network_fieldlabel',                        
			'Network Field Label',                            
			'suwp_network_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($network_fieldlabel,
			$network_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_brand_fieldlabel',                        
			'Brand Field Label',                            
			'suwp_brand_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($brand_fieldlabel,
			$brand_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_model_fieldlabel',                        
			'Model Field Label',                            
			'suwp_model_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($model_fieldlabel,
			$model_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_mep_fieldlabel',                        
			'MEP Field Label',                            
			'suwp_mep_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($mep_fieldlabel,
			$mep_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_kbh_fieldlabel',                        
			'KBH Field Label',                            
			'suwp_kbh_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($kbh_fieldlabel,
			$kbh_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_activation_fieldlabel',                        
			'Activation Field Label',                            
			'suwp_activation_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($activation_fieldlabel,
			$activation_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_manage_response_email_display',          // ID used to identify the field throughout the theme
			'Email Display Status',                                   // The label to the left of the option interface element
			'suwp_manage_response_email_display_callback', // The name of the function responsible for rendering the option interface
			'suwp_fieldlabel_options',                // The page on which this option will be displayed
			'suwp_fieldlabel_section',                // The name of the section to which this field belongs
			$display_email_setting                        // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_emailresponse_fieldlabel',                        
			'Email Response Field Label',                            
			'suwp_emailresponse_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($emailresponse_fieldlabel,
			$emailresponse_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_emailconfirm_fieldlabel',                        
			'Email Confirm Field Label',                            
			'suwp_emailconfirm_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($emailconfirm_fieldlabel,
			$emailconfirm_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_deliverytime_fieldlabel',                        
			'Delivery Time Field Label',                            
			'suwp_deliverytime_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($deliverytime_fieldlabel,
			$deliverytime_default)        // The arguments to pass to the callback.  
		);

		add_settings_field( 
			'suwp_code_fieldlabel',                        
			'Code Field Label',                            
			'suwp_code_fieldlabel_callback',
			'suwp_fieldlabel_options', 
			'suwp_fieldlabel_section', // The name of the section to which this field belongs
			array($code_fieldlabel,
			$code_default)        // The arguments to pass to the callback.  
		);

		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_fieldlabel_callback($args) {

			$html = '<p><strong>TEXT VALUES:</strong> Customize the labels that identify fields appearing on all remote unlocking Products.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';
			
			echo $html;
		} // end suwp_fieldlabel_callback

		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_service_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_service_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . $args[1] . '</p><br>
			<p class="label" id="suwp_service_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_service_fieldlabel" name="suwp_fieldlabel_options[suwp_service_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_service_fieldlabel-description1">Enter the label that will appear next to the <strong>Service</strong> field.</p>
			<p class="description" id="suwp_service_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_service_fieldlabel_callback

		function suwp_imei_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			// Render the output
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<p class="label" id="suwp_imei_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_imei_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_imei_fieldlabel" name="suwp_fieldlabel_options[suwp_imei_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_imei_fieldlabel-description1">Enter the label that will appear next to the <strong>IMEI</strong> field.</p>
			<p class="description" id="suwp_imei_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>
			<p class="description" id="suwp_imei_fieldlabel-description3">Use the <strong>{$charlength}</strong> variable to display the number of characters allowed, if any.</p>';
			
			echo $html;
			
		} // end suwp_imei_fieldlabel_callback

		function suwp_sn_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}

			// Render the output
			$html = '<p class="label" id="suwp_sn_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_sn_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_sn_fieldlabel" name="suwp_fieldlabel_options[suwp_sn_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_sn_fieldlabel-description1">Enter the label that will appear next to the <strong>Serial Number</strong> field.</p>
			<p class="description" id="suwp_sn_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>
			<p class="description" id="suwp_sn_fieldlabel-description3">Use the <strong>{$charlength}</strong> variable to display the number of characters allowed, if any.</p>';
			
			echo $html;
			
		} // end suwp_sn_fieldlabel_callback

		function suwp_country_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_country_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_country_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_country_fieldlabel" name="suwp_fieldlabel_options[suwp_country_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_country_fieldlabel-description1">Enter the label that will appear next to the <strong>Country</strong> field.</p>
			<p class="description" id="suwp_country_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_country_fieldlabel_callback

		function suwp_network_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_network_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_network_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_network_fieldlabel" name="suwp_fieldlabel_options[suwp_network_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_network_fieldlabel-description1">Enter the label that will appear next to the <strong>Network</strong> field.</p>
			<p class="description" id="suwp_network_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_network_fieldlabel_callback

		function suwp_brand_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_brand_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_brand_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_brand_fieldlabel" name="suwp_fieldlabel_options[suwp_brand_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_brand_fieldlabel-description1">Enter the label that will appear next to the <strong>Brand</strong> field.</p>
			<p class="description" id="suwp_brand_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_brand_fieldlabel_callback

		function suwp_model_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_model_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_model_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_model_fieldlabel" name="suwp_fieldlabel_options[suwp_model_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_model_fieldlabel-description1">Enter the label that will appear next to the <strong>Model</strong> field.</p>
			<p class="description" id="suwp_model_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_model_fieldlabel_callback

		function suwp_mep_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_mep_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_mep_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_mep_fieldlabel" name="suwp_fieldlabel_options[suwp_mep_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_mep_fieldlabel-description1">Enter the label that will appear next to the <strong>MEP</strong> field.</p>
			<p class="description" id="suwp_mep_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_mep_fieldlabel_callback

		function suwp_kbh_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_kbh_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_kbh_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_kbh_fieldlabel" name="suwp_fieldlabel_options[suwp_kbh_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_kbh_fieldlabel-description1">Enter the label that will appear next to the <strong>KBH</strong> field.</p>
			<p class="description" id="suwp_kbh_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_kbh_fieldlabel_callback

		function suwp_activation_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_activation_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_activation_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_activation_fieldlabel" name="suwp_fieldlabel_options[suwp_activation_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_activation_fieldlabel-description1">Enter the label that will appear next to the <strong>Activation</strong> field.</p>
			<p class="description" id="suwp_activation_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_activation_fieldlabel_callback

		function suwp_manage_response_email_display_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<hr>' . $args;

			$html .= '<p class="description" id="suwp_manage_response_email_display-description">Update this in order to display or hide the Response/Confirm Email fields while using the StockUnlocks plugin. <br />
			<span style="color:#FF0000">NOTE</span>: When \'hidden\', all confirmation emails are automatcially sent to the customer\'s Billing Email Address.</p>';

			echo $html;
			
		} // end suwp_manage_response_email_display_callback

		function suwp_emailresponse_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			$show_box = get_option('suwp_manage_response_email_display');
			$html = '';

			if ( $show_box ) {

				$html = '<p class="label" id="suwp_emailresponse_fieldlabel_hidden-label"><span style="color:#FF0000"><strong>NOT BEING DISPLAYED</span>: \'Hide Email Fields\' is selected.</strong></p><br>
				<p class="label" id="suwp_emailresponse_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
				<p class="label" id="suwp_emailresponse_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
				<textarea type="text" id="suwp_emailresponse_fieldlabel" name="suwp_fieldlabel_options[suwp_emailresponse_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
				<p class="description" id="suwp_emailresponse_fieldlabel-description1">Enter the label that will appear next to the <strong>Email Response</strong> field.</p>
				<p class="description" id="suwp_emailresponse_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>
				<p class="description" id="suwp_emailresponse_fieldlabel-description3">Use the <strong>{$adminemail}</strong> variable to display the email address found in:</p>
				<p class="description" id="suwp_emailresponse_fieldlabel-description4" style="padding-left: 15px;">Plugin Options > Notifications > Order Submitted: <strong>Order Submitted From Email</strong> field.</p>';
				
			} else {

				$html = '<p class="label" id="suwp_emailresponse_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
				<p class="label" id="suwp_emailresponse_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
				<textarea type="text" id="suwp_emailresponse_fieldlabel" name="suwp_fieldlabel_options[suwp_emailresponse_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
				<p class="description" id="suwp_emailresponse_fieldlabel-description1">Enter the label that will appear next to the <strong>Email Response</strong> field.</p>
				<p class="description" id="suwp_emailresponse_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>
				<p class="description" id="suwp_emailresponse_fieldlabel-description3">Use the <strong>{$adminemail}</strong> variable to display the email address found in:</p>
				<p class="description" id="suwp_emailresponse_fieldlabel-description4" style="padding-left: 15px;">Plugin Options > Notifications > Order Submitted: <strong>Order Submitted From Email</strong> field.</p>';
				
			}

			// Render the output
			echo $html;
			
		} // end suwp_emailresponse_fieldlabel_callback

		function suwp_emailconfirm_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			$show_box = get_option('suwp_manage_response_email_display');
			$html = '';

			if ( $show_box ) {

				$html = '<p class="label" id="suwp_emailconfirm_fieldlabel_hidden-label"><span style="color:#FF0000"><strong>NOT BEING DISPLAYED</span>: \'Hide Email Fields\' is selected.</strong></p><br>
				<p class="label" id="suwp_emailconfirm_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
				<p class="label" id="suwp_emailconfirm_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
				<textarea type="text" id="suwp_emailconfirm_fieldlabel" name="suwp_fieldlabel_options[suwp_emailconfirm_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
				<p class="description" id="suwp_emailconfirm_fieldlabel-description1">Enter the label that will appear next to the <strong>Email Confirm</strong> field.</p>
				<p class="description" id="suwp_emailconfirm_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
				
			} else {

				$html = '<p class="label" id="suwp_emailconfirm_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
				<p class="label" id="suwp_emailconfirm_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
				<textarea type="text" id="suwp_emailconfirm_fieldlabel" name="suwp_fieldlabel_options[suwp_emailconfirm_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
				<p class="description" id="suwp_emailconfirm_fieldlabel-description1">Enter the label that will appear next to the <strong>Email Confirm</strong> field.</p>
				<p class="description" id="suwp_emailconfirm_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
				
			}

			// Render the output
			echo $html . '<hr>';
			
		} // end suwp_emailconfirm_fieldlabel_callback

		function suwp_deliverytime_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_deliverytime_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_deliverytime_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_deliverytime_fieldlabel" name="suwp_fieldlabel_options[suwp_deliverytime_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_deliverytime_fieldlabel-description1">Enter the label that will appear next to the <strong>Delivery Time</strong> field.</p>
			<p class="description" id="suwp_deliverytime_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_deliverytime_fieldlabel_callback

		function suwp_code_fieldlabel_callback($args) {
			
			$args_label = '<strong><span style="color:#FF0000">ERROR</span></strong> - The label\'s text must be formatted like this: <strong>{%Label%}</strong>';
			
			if ( suwp_field_replace_preg_match($args[0]) ) {
				$args_label = suwp_field_replace_preg_match($args[0]);
				$suwp_serial_length = '15';
				$from_email = trim( get_option('suwp_fromemail_ordersuccess') );
				$replace = array(
					'{$charlength}' => $suwp_serial_length,
					'{$adminemail}' => $from_email,
				);
				$args_label = suwp_string_replace_assoc( $replace, $args_label );
			}
			
			// Render the output
			$html = '<p class="label" id="suwp_code_fieldlabel_orig-label"><strong>Default</strong>:<br> ' . nl2br(htmlentities($args[1])) . '</p><br>
			<p class="label" id="suwp_code_fieldlabel-label"><strong><span style="color:#336699">Active</span></strong>:<br>' . $args_label . '</p>
			<textarea type="text" id="suwp_code_fieldlabel" name="suwp_fieldlabel_options[suwp_code_fieldlabel]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_code_fieldlabel-description1">Enter the label that will appear next to the <strong>Code</strong> field.</p>
			<p class="description" id="suwp_code_fieldlabel-description2">The label must be formatted like this: <strong>{%Label%}</strong>. Additional text is allowed.</p>';
			
			echo $html;
			
		} // end suwp_code_fieldlabel_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update fieldlabel options',
            'sanitize_callback' => 'suwp_fieldlabel_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_fieldlabel_options',
			'suwp_fieldlabel_options',
			$args_validate
		);
		
		function suwp_fieldlabel_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);
			// suwp_options_saved_admin_notice

			if( isset( $input['suwp_service_fieldlabel'] ) )
				$args = trim($input['suwp_service_fieldlabel']);
				update_option( 'suwp_service_fieldlabel', $args );
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_service_label', $args_label);
				} else {
					update_option( 'suwp_service_label', 'Service');
				}

			if( isset( $input['suwp_imei_fieldlabel'] ) )
				$args = trim($input['suwp_imei_fieldlabel']);
				update_option( 'suwp_imei_fieldlabel', $args );
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_imei_label', $args_label);
				} else {
					update_option( 'suwp_imei_label', 'IMEI');
				}

			if( isset( $input['suwp_sn_fieldlabel'] ) )
				$args = trim($input['suwp_sn_fieldlabel']);
				update_option( 'suwp_sn_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_sn_label', $args_label);
				} else {
					update_option( 'suwp_sn_label', 'Serial Number');
				}
				
			if( isset( $input['suwp_country_fieldlabel'] ) )
				$args = trim($input['suwp_country_fieldlabel']);
				update_option( 'suwp_country_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_country_label', $args_label);
				} else {
					update_option( 'suwp_country_label', 'Country');
				}

			if( isset( $input['suwp_network_fieldlabel'] ) )
				$args = trim($input['suwp_network_fieldlabel']);
				update_option( 'suwp_network_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_network_label', $args_label);
				} else {
					update_option( 'suwp_network_label', 'Network Provider');
				}

			if( isset( $input['suwp_brand_fieldlabel'] ) )
				$args = trim($input['suwp_brand_fieldlabel']);
				update_option( 'suwp_brand_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_brand_label', $args_label);
				} else {
					update_option( 'suwp_brand_label', 'Brand');
				}

			if( isset( $input['suwp_model_fieldlabel'] ) )
				$args = trim($input['suwp_model_fieldlabel']);
				update_option( 'suwp_model_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_model_label', $args_label);
				} else {
					update_option( 'suwp_model_label', 'Model');
				}

			if( isset( $input['suwp_mep_fieldlabel'] ) )
				$args = trim($input['suwp_mep_fieldlabel']);
				update_option( 'suwp_mep_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_mep_label', $args_label);
				} else {
					update_option( 'suwp_mep_label', 'MEP Name');
				}

			if( isset( $input['suwp_kbh_fieldlabel'] ) )
				$args = trim($input['suwp_kbh_fieldlabel']);
				update_option( 'suwp_kbh_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_kbh_label', $args_label);
				} else {
					update_option( 'suwp_kbh_label', 'KBH/KRH/ESN');
				}

			if( isset( $input['suwp_activation_fieldlabel'] ) )
				$args = trim($input['suwp_activation_fieldlabel']);
				update_option( 'suwp_activation_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_activation_label', $args_label);
				} else {
					update_option( 'suwp_activation_label', 'Phone Number');
				}

			if( isset( $input['suwp_manage_response_email_display'] ) )
				update_option( 'suwp_manage_response_email_display', $input['suwp_manage_response_email_display']);
			
			if( isset( $input['suwp_emailresponse_fieldlabel'] ) )
				$args = trim($input['suwp_emailresponse_fieldlabel']);
				update_option( 'suwp_emailresponse_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_emailresponse_label', $args_label);
				} else {
					update_option( 'suwp_emailresponse_label', 'Response Email');
				}

			if( isset( $input['suwp_emailconfirm_fieldlabel'] ) )
				$args = trim($input['suwp_emailconfirm_fieldlabel']);
				update_option( 'suwp_emailconfirm_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_emailconfirm_label', $args_label);
				} else {
					update_option( 'suwp_emailconfirm_label', 'Confirm Email');
				}

			if( isset( $input['suwp_deliverytime_fieldlabel'] ) )
				$args = trim($input['suwp_deliverytime_fieldlabel']);
				update_option( 'suwp_deliverytime_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_deliverytime_label', $args_label);
				} else {
					update_option( 'suwp_deliverytime_label', 'Estimated Delivery Time');
				}

			if( isset( $input['suwp_code_fieldlabel'] ) )
				$args = trim($input['suwp_code_fieldlabel']);
				update_option( 'suwp_code_fieldlabel', $args);
				if ( suwp_field_extract_preg_match($args) ) {
					$args_label = suwp_field_extract_preg_match($args);
					update_option( 'suwp_code_label', $args_label);
				} else {
					update_option( 'suwp_code_label', 'Code');
				}
			
			return $input;
		}
		
	} // end suwp_initialize_options_fieldlabels

	public function suwp_initialize_options_textmessages($options, $options_default) {
		
		$not_required_default = $options_default['suwp_not_required_msg'];
		$blank_default = $options_default['suwp_blank_msg'];
		$payment_email_default = $options_default['suwp_payment_email_msg'];
		$invalidemail_default = $options_default['suwp_invalidemail_msg'];
		$nonmatching_default = $options_default['suwp_nonmatching_msg'];
		$invalidentry_default = $options_default['suwp_invalidentry_msg'];
		$exceeded_default = $options_default['suwp_exceeded_msg'];
		$invalidchar_default = $options_default['suwp_invalidchar_msg'];
		$invalidlength_default = $options_default['suwp_invalidlength_msg'];
		$invalidformat_default = $options_default['suwp_invalidformat_msg'];
		$dupvalues_default = $options_default['suwp_dupvalues_msg'];

		$not_required_msg = $options['suwp_not_required_msg'];
		$blank_msg = $options['suwp_blank_msg'];
		$payment_email_msg = $options['suwp_payment_email_msg'];
		$invalidemail_msg = $options['suwp_invalidemail_msg'];
		$nonmatching_msg = $options['suwp_nonmatching_msg'];
		$invalidentry_msg = $options['suwp_invalidentry_msg'];
		$exceeded_msg = $options['suwp_exceeded_msg'];
		$invalidchar_msg = $options['suwp_invalidchar_msg'];
		$invalidlength_msg = $options['suwp_invalidlength_msg'];
		$invalidformat_msg = $options['suwp_invalidformat_msg'];
		$dupvalues_msg = $options['suwp_dupvalues_msg'];

		add_settings_section(
			'suwp_textmessage_section',  // ID used to identify this section and with which to register options
			'WEBSITE MESSAGES',            // Title to be displayed on the administration page
			'suwp_textmessage_callback', // Callback used to render the description of the section
			'suwp_textmessage_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_not_required_msg',                        
			'Not Required Message',                            
			'suwp_not_required_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($not_required_msg,
			$not_required_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_blank_msg',                        
			'Blank/Empty Message',                            
			'suwp_blank_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($blank_msg,
			$blank_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_payment_email_msg',                        
			'Use Billing Email Message',                            
			'suwp_payment_email_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($payment_email_msg,
			$payment_email_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_invalidemail_msg',                        
			'Invalid Email Message',                            
			'suwp_invalidemail_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($invalidemail_msg,
			$invalidemail_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_nonmatching_msg',                        
			'Non-matching Email Message',                            
			'suwp_nonmatching_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($nonmatching_msg,
			$nonmatching_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_invalidentry_msg',                        
			'Invalid Entry Message',                            
			'suwp_invalidentry_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($invalidentry_msg,
			$invalidentry_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_exceeded_msg',                        
			'Exceeded Total Message',                            
			'suwp_exceeded_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($exceeded_msg,
			$exceeded_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_invalidchar_msg',                        
			'Digits Only Message',                            
			'suwp_invalidchar_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($invalidchar_msg,
			$invalidchar_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_invalidlength_msg',                        
			'Invalid Length Message',                            
			'suwp_invalidlength_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($invalidlength_msg,
			$invalidlength_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_invalidformat_msg',                        
			'Invalid Format Message',                            
			'suwp_invalidformat_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($invalidformat_msg,
			$invalidformat_default)        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_dupvalues_msg',                        
			'Duplicate Values Message',                            
			'suwp_dupvalues_msg_callback',
			'suwp_textmessage_options', 
			'suwp_textmessage_section', // The name of the section to which this field belongs
			array($dupvalues_msg,
			$dupvalues_default)        // The arguments to pass to the callback.  
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_textmessage_callback($args) {

			$html = '<p><strong>TEXT VALUES:</strong> Customize the plugin generated messages appearing on your website.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';
			
			echo $html;
		} // end suwp_textmessage_callback

		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_not_required_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_not_required_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_not_required_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_not_required_msg" name="suwp_textmessage_options[suwp_not_required_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_not_required_msg-description">The message that a particular value is not required.</p>';
			
			echo $html;
			
		} // end suwp_not_required_msg_callback

		function suwp_blank_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_blank_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_blank_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_blank_msg" name="suwp_textmessage_options[suwp_blank_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_blank_msg-description">The message that a value was left blank.</p>';
			
			echo $html;
			
		} // end suwp_blank_msg_callback

		function suwp_payment_email_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_payment_email_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_payment_email_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_payment_email_msg" name="suwp_textmessage_options[suwp_payment_email_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_payment_email_msg-description">The message that the payment email may be used.</p>';
			
			echo $html;
			
		} // end suwp_payment_email_msg_callback

		function suwp_invalidemail_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_invalidemail_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_invalidemail_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_invalidemail_msg" name="suwp_textmessage_options[suwp_invalidemail_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_invalidemail_msg-description">The message that an invalid email was entered.</p>';
			
			echo $html;
			
		} // end suwp_invalidemail_msg_callback

		function suwp_nonmatching_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_nonmatching_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_nonmatching_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_nonmatching_msg" name="suwp_textmessage_options[suwp_nonmatching_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_nonmatching_msg-description">The message that the response email and the confirm email do not match.</p>';
			
			echo $html;
			
		} // end suwp_nonmatching_msg_callback

		function suwp_invalidentry_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_invalidentry_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_invalidentry_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_invalidentry_msg" name="suwp_textmessage_options[suwp_invalidentry_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_invalidentry_msg-description">The message that an entry was invalid.</p>';
			
			echo $html;
			
		} // end suwp_invalidentry_msg_callback

		function suwp_exceeded_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_exceeded_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_exceeded_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_exceeded_msg" name="suwp_textmessage_options[suwp_exceeded_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_exceeded_msg-description">The message that what was entered exceeded the total allowed quantity.</p>';
			
			echo $html;
			
		} // end suwp_exceeded_msg_callback

		function suwp_invalidchar_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_invalidchar_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_invalidchar_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_invalidchar_msg" name="suwp_textmessage_options[suwp_invalidchar_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_invalidchar_msg-description">The message that a non-numerical character was entered.</p>';
			
			echo $html;
			
		} // end suwp_invalidchar_msg_callback

		function suwp_invalidlength_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_invalidlength_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_invalidlength_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_invalidlength_msg" name="suwp_textmessage_options[suwp_invalidlength_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_invalidlength_msg-description">The message that the character length of what was entered was too long.</p>';
			
			echo $html;
			
		} // end suwp_invalidlength_msg_callback

		function suwp_invalidformat_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_invalidformat_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_invalidformat_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_invalidformat_msg" name="suwp_textmessage_options[suwp_invalidformat_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_invalidformat_msg-description">The message that the value entered was incorrectly formatted.</p>';
			
			echo $html;
			
		} // end suwp_invalidformat_msg_callback

		function suwp_dupvalues_msg_callback($args) {
			
			// Render the output
			$html = '<p class="label" id="suwp_dupvalues_msg_orig-label"><strong>Default</strong>: ' . nl2br(htmlentities($args[1])) . '</p>
			<p class="label" id="suwp_dupvalues_msg-label"><strong><span style="color:#336699">Active</span></strong>: ' . $args[0] . '</p>
			<textarea type="text" id="suwp_dupvalues_msg" name="suwp_textmessage_options[suwp_dupvalues_msg]" rows="5" cols="30" style="width:500px;">' . $args[0] . '</textarea>
			<p class="description" id="suwp_dupvalues_msg-description">The message that duplicate values were entered.</p>';
			
			echo $html;
			
		} // end suwp_dupvalues_msg_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update textmessage options',
            'sanitize_callback' => 'suwp_textmessage_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_textmessage_options',
			'suwp_textmessage_options',
			$args_validate
		);
		
		function suwp_textmessage_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_not_required_msg']) )
				$args = trim($input['suwp_not_required_msg']);
				update_option( 'suwp_not_required_msg', $args );

			if( isset( $input['suwp_blank_msg'] ) )
				$args = trim($input['suwp_blank_msg']);
				update_option( 'suwp_blank_msg', $args );

			if( isset( $input['suwp_payment_email_msg'] ) )
				$args = trim($input['suwp_payment_email_msg']);
				update_option( 'suwp_payment_email_msg', $args );

			if( isset( $input['suwp_invalidemail_msg'] ) )
				$args = trim($input['suwp_invalidemail_msg']);
				update_option( 'suwp_invalidemail_msg', $args );

			if( isset( $input['suwp_nonmatching_msg'] ) )
				$args = trim($input['suwp_nonmatching_msg']);
				update_option( 'suwp_nonmatching_msg', $args );

			if( isset( $input['suwp_invalidentry_msg'] ) )
				$args = trim($input['suwp_invalidentry_msg']);
				update_option( 'suwp_invalidentry_msg', $args );

			if( isset( $input['suwp_exceeded_msg'] ) )
				$args = trim($input['suwp_exceeded_msg']);
				update_option( 'suwp_exceeded_msg', $args );

			if( isset( $input['suwp_invalidchar_msg'] ) )
				$args = trim($input['suwp_invalidchar_msg']);
				update_option( 'suwp_invalidchar_msg', $args );

			if( isset( $input['suwp_invalidlength_msg'] ) )
				$args = trim($input['suwp_invalidlength_msg']);
				update_option( 'suwp_invalidlength_msg', $args );

			if( isset( $input['suwp_invalidformat_msg'] ) )
				$args = trim($input['suwp_invalidformat_msg']);
				update_option( 'suwp_invalidformat_msg', $args );

			if( isset( $input['suwp_dupvalues_msg'] ) )
				$args = trim($input['suwp_dupvalues_msg']);
				update_option( 'suwp_dupvalues_msg', $args );

			return $input;
		}
		
	} // end suwp_initialize_options_textmessages

	public function suwp_initialize_options_ordersuccess($options) {

		$subject_ordersuccess = $options['suwp_subject_ordersuccess'];
		$message_ordersuccess = $options['suwp_message_ordersuccess'];
		$fromname_ordersuccess = $options['suwp_fromname_ordersuccess'];
		$fromemail_ordersuccess = $options['suwp_fromemail_ordersuccess'];
		$copyto_ordersuccess = $options['suwp_copyto_ordersuccess'];

		add_settings_section(
			'suwp_ordersuccess_section',  // ID used to identify this section and with which to register options
			'ORDER SUBMITTED',            // Title to be displayed on the administration page
			'suwp_ordersuccess_callback', // Callback used to render the description of the section
			'suwp_ordersuccess_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_subject_ordersuccess',                        
			'Order Submitted Subject',                            
			'suwp_subject_ordersuccess_callback',   
			'suwp_ordersuccess_options', 
			'suwp_ordersuccess_section', // The name of the section to which this field belongs
			$subject_ordersuccess        // The arguments to pass to the callback.  
		);
		
		add_settings_field( 
			'suwp_message_ordersuccess',                     
			'Order Submitted Message',                         
			'suwp_message_ordersuccess_callback',    
			'suwp_ordersuccess_options', 
			'suwp_ordersuccess_section', // The name of the section to which this field belongs
			$message_ordersuccess        // The arguments to pass to the callback.    
		);

		add_settings_field( 
			'suwp_fromname_ordersuccess',          // ID used to identify the field throughout the theme
			'Order Submitted From Name',           // The label to the left of the option interface element
			'suwp_fromname_ordersuccess_callback', // The name of the function responsible for rendering the option interface
			'suwp_ordersuccess_options',           // The page on which this option will be displayed
			'suwp_ordersuccess_section',           // The name of the section to which this field belongs
			$fromname_ordersuccess                 // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_fromemail_ordersuccess',                     
			'Order Submitted From Email',              
			'suwp_fromemail_ordersuccess_callback',  
			'suwp_ordersuccess_options',                          
			'suwp_ordersuccess_section', // The name of the section to which this field belongs
			$fromemail_ordersuccess      // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_copyto_ordersuccess',                      
			'Order Submitted Copy To',               
			'suwp_copyto_ordersuccess_callback',   
			'suwp_ordersuccess_options',                          
			'suwp_ordersuccess_section', // The name of the section to which this field belongs
			$copyto_ordersuccess         // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_ordersuccess_callback($args) {

			$html = '<p><strong>NOTIFICATION:</strong> This is the message the customer receives after successfully placing an order for processing by this plugin.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';
			
			echo $html;
		} // end suwp_ordersuccess_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_subject_ordersuccess_callback($args) {
			
			// Render the output
			$html = '<input type="text" id="suwp_subject_ordersuccess" name="suwp_ordersuccess_options[suwp_subject_ordersuccess]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_subject_ordersuccess-description">The subject line for the message.</p>';
			
			echo $html;
			
		} // end suwp_subject_ordersuccess_callback

		function suwp_message_ordersuccess_callback($args) {
			
			// wp_editor will act funny if it's stored in a string so we run it like this...
			wp_editor( $args, 'suwp_message_ordersuccess', array( 'textarea_rows'=>8 , 'textarea_name' => 'suwp_ordersuccess_options[suwp_message_ordersuccess]') );
			
			$html = '<p class="description" id="suwp_message_ordersuccess-description1">This is the message the customer receives after successfully placing an order for processing by this plugin.</p>
			<p class="description" id="suwp_message_ordersuccess-description2">Values appearing like this: <strong>{%Value%}</strong> are custom labels. They may be edited <a href="?page=suwp_options_admin_page&tab=textvalues_options" >HERE</a>. Use the <strong>Default</strong> value to reference your custom label.</p>
			<p class="description" id="suwp_message_ordersuccess-description3">Available variables: {$customerfirstname} = Customer first name, {$imei} = Submitted IMEI, {$orderid} = Order number, {$phoneinfo} = Phone/Device information, {$service} = Service name, {$processtime} = Estimated delivery, {$reply} = Admin order reply </p>';
			
			echo $html;
					
		} // end suwp_message_ordersuccess_callback

		function suwp_fromname_ordersuccess_callback($args) {
			
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<input type="text" id="suwp_fromname_ordersuccess" name="suwp_ordersuccess_options[suwp_fromname_ordersuccess]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromname_ordersuccess-description">The name associated with the \'From Email\' address. </p>';
			
			echo $html;
			
		} // end suwp_fromname_ordersuccess_callback
		
		function suwp_fromemail_ordersuccess_callback($args) {
			
			$html = '<input type="email" id="suwp_fromemail_ordersuccess" name="suwp_ordersuccess_options[suwp_fromemail_ordersuccess]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromemail_ordersuccess-description">Originates from your website. Usually an admin account.</p>';
			
			echo $html;
			
		} // end suwp_fromemail_ordersuccess_callback
		
		function suwp_copyto_ordersuccess_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<input type="email" id="suwp_copyto_ordersuccess" name="suwp_ordersuccess_options[suwp_copyto_ordersuccess]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_copyto_ordersuccess-description">Send a copy to this address. Usually an admin account.</p>';
			
			echo $html;
			
		} // end suwp_copyto_ordersuccess_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_ordersuccess_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_ordersuccess_options',
			'suwp_ordersuccess_options',
			$args_validate
		);
		
		function suwp_ordersuccess_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_subject_ordersuccess'] ) )
				update_option( 'suwp_subject_ordersuccess', trim($input['suwp_subject_ordersuccess']) );

			if( isset( $input['suwp_message_ordersuccess'] ) )
				update_option( 'suwp_message_ordersuccess', $input['suwp_message_ordersuccess'] );

			if( isset( $input['suwp_fromname_ordersuccess'] ) )
				update_option( 'suwp_fromname_ordersuccess', trim($input['suwp_fromname_ordersuccess']) );

			if( isset( $input['suwp_fromemail_ordersuccess'] ) )
				update_option( 'suwp_fromemail_ordersuccess', $input['suwp_fromemail_ordersuccess'] );

			if( isset( $input['suwp_copyto_ordersuccess'] ) )
				update_option( 'suwp_copyto_ordersuccess', $input['suwp_copyto_ordersuccess'] );

			return $input;
		}
		
	} // end suwp_initialize_options_ordersuccess

	public function suwp_initialize_options_orderavailable($options) {
		
		$subject_orderavailable = $options['suwp_subject_orderavailable'];
		$message_orderavailable = $options['suwp_message_orderavailable'];
		$fromname_orderavailable = $options['suwp_fromname_orderavailable'];
		$fromemail_orderavailable = $options['suwp_fromemail_orderavailable'];
		$copyto_orderavailable = $options['suwp_copyto_orderavailable'];

		add_settings_section(
			'suwp_orderavailable_section',  // ID used to identify this section and with which to register options
			'ORDER AVAILABLE',              // Title to be displayed on the administration page
			'suwp_orderavailable_callback', // Callback used to render the description of the section
			'suwp_orderavailable_options'  // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_subject_orderavailable',                        
			'Order Available Subject',                            
			'suwp_subject_orderavailable_callback',   
			'suwp_orderavailable_options', 
			'suwp_orderavailable_section', // The name of the section to which this field belongs
			$subject_orderavailable        // The arguments to pass to the callback.  
		);
    
		add_settings_field( 
			'suwp_message_orderavailable',                     
			'Order Available Message',                         
			'suwp_message_orderavailable_callback',    
			'suwp_orderavailable_options', 
			'suwp_orderavailable_section', // The name of the section to which this field belongs
			$message_orderavailable        // The arguments to pass to the callback.    
		);

		add_settings_field( 
			'suwp_fromname_orderavailable',          // ID used to identify the field throughout the theme
			'Order Available From Name',             // The label to the left of the option interface element
			'suwp_fromname_orderavailable_callback', // The name of the function responsible for rendering the option interface
			'suwp_orderavailable_options',           // The page on which this option will be displayed
			'suwp_orderavailable_section',           // The name of the section to which this field belongs
			$fromname_orderavailable                 // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_fromemail_orderavailable',                     
			'Order Available From Email',              
			'suwp_fromemail_orderavailable_callback',  
			'suwp_orderavailable_options',                          
			'suwp_orderavailable_section', // The name of the section to which this field belongs
			$fromemail_orderavailable      // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_copyto_orderavailable',                      
			'Order Available Copy To',               
			'suwp_copyto_orderavailable_callback',   
			'suwp_orderavailable_options',                          
			'suwp_orderavailable_section', // The name of the section to which this field belongs
			$copyto_orderavailable         // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_orderavailable_callback($args) {
			$html = '<p><strong>NOTIFICATION:</strong> This is the message sent to the customer when the code is successful after placing an order for processing by this plugin.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';

			echo $html;

		} // end suwp_orderavailable_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_subject_orderavailable_callback($args) {
			
			// Render the output
			$html = '<input type="text" id="suwp_subject_orderavailable" name="suwp_orderavailable_options[suwp_subject_orderavailable]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_subject_orderavailable-description">The subject line for the message.</p>';
			
			echo $html;
			
		} // end suwp_subject_orderavailable_callback

		function suwp_message_orderavailable_callback($args) {
			
			// Render the output
			// wp_editor will act funny if it's stored in a string so we run it like this...
			wp_editor( $args, 'suwp_message_orderavailable', array( 'textarea_rows'=>8 , 'textarea_name' => 'suwp_orderavailable_options[suwp_message_orderavailable]') );
			
			$html = '<p class="description" id="suwp_message_orderavailable-description1">This is the message sent to the customer when the code is successful after placing an order for processing by this plugin.</p>
			<p class="description" id="suwp_message_orderavailable-description2">Values appearing like this: <strong>{%Value%}</strong> are custom labels. They may be edited <a href="?page=suwp_options_admin_page&tab=textvalues_options" >HERE</a>. Use the <strong>Default</strong> value to reference your custom label.</p>
			<p class="description" id="suwp_message_orderavailable-description3">Available variables: {$customerfirstname} = Customer first name, {$imei} = Submitted IMEI, {$orderid} = Order number, {$phoneinfo} = Phone/Device information, {$service} = Service name, {$processtime} = Estimated delivery, {$reply} = Admin order reply </p>';
			
			echo $html;

		} // end suwp_message_orderavailable_callback

		function suwp_fromname_orderavailable_callback($args) {
			
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<input type="text" id="suwp_fromname_orderavailable" name="suwp_orderavailable_options[suwp_fromname_orderavailable]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromname_orderavailable-description">The name associated with the \'From Email\' address. </p>';
			
			echo $html;

		} // end suwp_fromname_orderavailable_callback
		
		function suwp_fromemail_orderavailable_callback($args) {
			
			$html = '<input type="email" id="suwp_fromemail_orderavailable" name="suwp_orderavailable_options[suwp_fromemail_orderavailable]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromemail_orderavailable-description">Originates from your website. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_fromemail_orderavailable_callback
		
		function suwp_copyto_orderavailable_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<input type="email" id="suwp_copyto_orderavailable" name="suwp_orderavailable_options[suwp_copyto_orderavailable]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_copyto_orderavailable-description">Send a copy to this address. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_copyto_orderavailable_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_orderavailable_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_orderavailable_options',
			'suwp_orderavailable_options',
			$args_validate
		);
		
		function suwp_orderavailable_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_subject_orderavailable'] ) )
				update_option( 'suwp_subject_orderavailable', trim($input['suwp_subject_orderavailable']) );

			if( isset( $input['suwp_message_orderavailable'] ) )
				update_option( 'suwp_message_orderavailable', $input['suwp_message_orderavailable'] );

			if( isset( $input['suwp_fromname_orderavailable'] ) )
				update_option( 'suwp_fromname_orderavailable', trim($input['suwp_fromname_orderavailable']) );

			if( isset( $input['suwp_fromemail_orderavailable'] ) )
				update_option( 'suwp_fromemail_orderavailable', $input['suwp_fromemail_orderavailable'] );

			if( isset( $input['suwp_copyto_orderavailable'] ) )
				update_option( 'suwp_copyto_orderavailable', $input['suwp_copyto_orderavailable'] );

			return $input;
		}
		
	} // end suwp_initialize_options_orderavailable
	
	public function suwp_initialize_options_orderrejected($options) {

		$subject_orderrejected = $options['suwp_subject_orderrejected'];
		$message_orderrejected = $options['suwp_message_orderrejected'];
		$fromname_orderrejected = $options['suwp_fromname_orderrejected'];
		$fromemail_orderrejected = $options['suwp_fromemail_orderrejected'];
		$copyto_orderrejected = $options['suwp_copyto_orderrejected'];

		add_settings_section(
			'suwp_orderrejected_section',  // ID used to identify this section and with which to register options
			'ORDER REJECTED',              // Title to be displayed on the administration page
			'suwp_orderrejected_callback', // Callback used to render the description of the section
			'suwp_orderrejected_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_subject_orderrejected',                        
			'Order Rejected Subject',                            
			'suwp_subject_orderrejected_callback',   
			'suwp_orderrejected_options', 
			'suwp_orderrejected_section', // The name of the section to which this field belongs
			$subject_orderrejected        // The arguments to pass to the callback.  
		);
    
		add_settings_field( 
			'suwp_message_orderrejected',                     
			'Order Rejected Message',                         
			'suwp_message_orderrejected_callback',    
			'suwp_orderrejected_options', 
			'suwp_orderrejected_section', // The name of the section to which this field belongs
			$message_orderrejected        // The arguments to pass to the callback.    
		);

		add_settings_field( 
			'suwp_fromname_orderrejected',          // ID used to identify the field throughout the theme
			'Order Rejected From Name',             // The label to the left of the option interface element
			'suwp_fromname_orderrejected_callback', // The name of the function responsible for rendering the option interface
			'suwp_orderrejected_options',           // The page on which this option will be displayed
			'suwp_orderrejected_section',           // The name of the section to which this field belongs
			$fromname_orderrejected                 // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_fromemail_orderrejected',                     
			'Order Rejected From Email',              
			'suwp_fromemail_orderrejected_callback',  
			'suwp_orderrejected_options',                          
			'suwp_orderrejected_section', // The name of the section to which this field belongs
			$fromemail_orderrejected      // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_copyto_orderrejected',                      
			'Order Rejected Copy To',               
			'suwp_copyto_orderrejected_callback',   
			'suwp_orderrejected_options',                          
			'suwp_orderrejected_section', // The name of the section to which this field belongs
			$copyto_orderrejected         // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_orderrejected_callback($args) {
			$html = '<p><strong>NOTIFICATION:</strong> This is the message sent to the customer when the code is unsuccessful after placing an order for processing by this plugin.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';

			echo $html;

		} // end suwp_orderrejected_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_subject_orderrejected_callback($args) {
			
			// Render the output
			$html = '<input type="text" id="suwp_subject_orderrejected" name="suwp_orderrejected_options[suwp_subject_orderrejected]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_subject_orderrejected-description">The subject line for the message.</p>';
			
			echo $html;
			
		} // end suwp_subject_orderrejected_callback

		function suwp_message_orderrejected_callback($args) {
			
			// Render the output
			// wp_editor will act funny if it's stored in a string so we run it like this...
			wp_editor( $args, 'suwp_message_orderrejected', array( 'textarea_rows'=>8 , 'textarea_name' => 'suwp_orderrejected_options[suwp_message_orderrejected]') );
			
			$html = '<p class="description" id="suwp_message_orderrejected-description1">This is the message sent to the customer when the code is unsuccessful after placing an order for processing by this plugin.</p>
			<p class="description" id="suwp_message_orderrejected-description2">Values appearing like this: <strong>{%Value%}</strong> are custom labels. They may be edited <a href="?page=suwp_options_admin_page&tab=textvalues_options" >HERE</a>. Use the <strong>Default</strong> value to reference your custom label.</p>
			<p class="description" id="suwp_message_orderrejected-description3">Available variables: {$customerfirstname} = Customer first name, {$imei} = Submitted IMEI, {$orderid} = Order number, {$phoneinfo} = Phone/Device information, {$service} = Service name, {$processtime} = Estimated delivery, {$reply} = Admin order reply </p>';
			
			echo $html;

		} // end suwp_message_orderrejected_callback

		function suwp_fromname_orderrejected_callback($args) {
			
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<input type="text" id="suwp_fromname_orderrejected" name="suwp_orderrejected_options[suwp_fromname_orderrejected]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromname_orderrejected-description">The name associated with the \'From Email\' address. </p>';
			
			echo $html;

		} // end suwp_fromname_orderrejected_callback
		
		function suwp_fromemail_orderrejected_callback($args) {
			
			$html = '<input type="email" id="suwp_fromemail_orderrejected" name="suwp_orderrejected_options[suwp_fromemail_orderrejected]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromemail_orderrejected-description">Originates from your website. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_fromemail_orderrejected_callback
		
		function suwp_copyto_orderrejected_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<input type="email" id="suwp_copyto_orderrejected" name="suwp_orderrejected_options[suwp_copyto_orderrejected]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_copyto_orderrejected-description">Send a copy to this address. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_copyto_orderrejected_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_orderrejected_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_orderrejected_options',
			'suwp_orderrejected_options',
			$args_validate
		);
		
		function suwp_orderrejected_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_subject_orderrejected'] ) )
				update_option( 'suwp_subject_orderrejected', trim($input['suwp_subject_orderrejected']) );

			if( isset( $input['suwp_message_orderrejected'] ) )
				update_option( 'suwp_message_orderrejected', $input['suwp_message_orderrejected'] );

			if( isset( $input['suwp_fromname_orderrejected'] ) )
				update_option( 'suwp_fromname_orderrejected', trim($input['suwp_fromname_orderrejected']) );

			if( isset( $input['suwp_fromemail_orderrejected'] ) )
				update_option( 'suwp_fromemail_orderrejected', $input['suwp_fromemail_orderrejected'] );

			if( isset( $input['suwp_copyto_orderrejected'] ) )
				update_option( 'suwp_copyto_orderrejected', $input['suwp_copyto_orderrejected'] );

			return $input;
		}
		
	} // end suwp_initialize_options_orderrejected
	
	public function suwp_initialize_options_ordererror($options) {

		$subject_ordererror = $options['suwp_subject_ordererror'];
		$message_ordererror = $options['suwp_message_ordererror'];
		$fromname_ordererror = $options['suwp_fromname_ordererror'];
		$fromemail_ordererror = $options['suwp_fromemail_ordererror'];
		$copyto_ordererror = $options['suwp_copyto_ordererror'];

		add_settings_section(
			'suwp_ordererror_section',  // ID used to identify this section and with which to register options
			'ORDER SUBMIT ERROR',       // Title to be displayed on the administration page
			'suwp_ordererror_callback', // Callback used to render the description of the section
			'suwp_ordererror_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_subject_ordererror',                        
			'Order Submit Error Subject',                            
			'suwp_subject_ordererror_callback',   
			'suwp_ordererror_options', 
			'suwp_ordererror_section', // The name of the section to which this field belongs
			$subject_ordererror        // The arguments to pass to the callback.  
		);
    
		add_settings_field( 
			'suwp_message_ordererror',                     
			'Order Submit Error Message',                         
			'suwp_message_ordererror_callback',    
			'suwp_ordererror_options', 
			'suwp_ordererror_section', // The name of the section to which this field belongs
			$message_ordererror        // The arguments to pass to the callback.    
		);

		add_settings_field( 
			'suwp_fromname_ordererror',          // ID used to identify the field throughout the theme
			'Order Submit Error From Name',      // The label to the left of the option interface element
			'suwp_fromname_ordererror_callback', // The name of the function responsible for rendering the option interface
			'suwp_ordererror_options',           // The page on which this option will be displayed
			'suwp_ordererror_section',           // The name of the section to which this field belongs
			$fromname_ordererror                 // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_fromemail_ordererror',                     
			'Order Submit Error From Email',              
			'suwp_fromemail_ordererror_callback',  
			'suwp_ordererror_options',                          
			'suwp_ordererror_section', // The name of the section to which this field belongs
			$fromemail_ordererror      // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_copyto_ordererror',                      
			'Order Submit Error Copy To',               
			'suwp_copyto_ordererror_callback',   
			'suwp_ordererror_options',                          
			'suwp_ordererror_section', // The name of the section to which this field belongs
			$copyto_ordererror         // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_ordererror_callback($args) {
			$html = '<p><strong>NOTIFICATION:</strong> This is the message sent to the admin when the order fails to be submitted by this plugin.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';

			echo $html;

		} // end suwp_ordererror_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_subject_ordererror_callback($args) {
			
			// Render the output
			$html = '<input type="text" id="suwp_subject_ordererror" name="suwp_ordererror_options[suwp_subject_ordererror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_subject_ordererror-description">The subject line for the message.</p>';
			
			echo $html;
			
		} // end suwp_subject_ordererror_callback

		function suwp_message_ordererror_callback($args) {
			
			// Render the output
			// wp_editor will act funny if it's stored in a string so we run it like this...
			wp_editor( $args, 'suwp_message_ordererror', array( 'textarea_rows'=>8 , 'textarea_name' => 'suwp_ordererror_options[suwp_message_ordererror]') );
			
			$html = '<p class="description" id="suwp_message_ordererror-description1">This is the message sent to the admin when the order fails to be submitted by this plugin.</p>
			<p class="description" id="suwp_message_ordererror-description2">Values appearing like this: <strong>{%Value%}</strong> are custom labels. They may be edited <a href="?page=suwp_options_admin_page&tab=textvalues_options" >HERE</a>. Use the <strong>Default</strong> value to reference your custom label.</p>
			<p class="description" id="suwp_message_ordererror-description3">Available variables: {$customerfirstname} = Customer first name, {$imei} = Submitted IMEI, {$orderid} = Order number, {$phoneinfo} = Phone/Device information, {$service} = Service name, {$processtime} = Estimated delivery, {$reply} = Admin order reply </p>';
			
			echo $html;

		} // end suwp_message_ordererror_callback

		function suwp_fromname_ordererror_callback($args) {
			
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<input type="text" id="suwp_fromname_ordererror" name="suwp_ordererror_options[suwp_fromname_ordererror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromname_ordererror-description">The name associated with the \'From Email\' address. </p>';
			
			echo $html;

		} // end suwp_fromname_ordererror_callback
		
		function suwp_fromemail_ordererror_callback($args) {
			
			$html = '<input type="email" id="suwp_fromemail_ordererror" name="suwp_ordererror_options[suwp_fromemail_ordererror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromemail_ordererror-description">Originates from your website. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_fromemail_ordererror_callback
		
		function suwp_copyto_ordererror_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<input type="email" id="suwp_copyto_ordererror" name="suwp_ordererror_options[suwp_copyto_ordererror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_copyto_ordererror-description">Send a copy to this address. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_copyto_ordererror_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_ordererror_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_ordererror_options',
			'suwp_ordererror_options',
			$args_validate
		);
		
		function suwp_ordererror_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_subject_ordererror'] ) )
				update_option( 'suwp_subject_ordererror', trim($input['suwp_subject_ordererror']) );

			if( isset( $input['suwp_message_ordererror'] ) )
				update_option( 'suwp_message_ordererror', $input['suwp_message_ordererror'] );

			if( isset( $input['suwp_fromname_ordererror'] ) )
				update_option( 'suwp_fromname_ordererror', trim($input['suwp_fromname_ordererror']) );

			if( isset( $input['suwp_fromemail_ordererror'] ) )
				update_option( 'suwp_fromemail_ordererror', $input['suwp_fromemail_ordererror'] );

			if( isset( $input['suwp_copyto_ordererror'] ) )
				update_option( 'suwp_copyto_ordererror', $input['suwp_copyto_ordererror'] );

			return $input;
		}
		
	} // end suwp_initialize_options_ordererror

	public function suwp_initialize_options_checkerror($options) {

		$subject_checkerror = $options['suwp_subject_checkerror'];
		$message_checkerror = $options['suwp_message_checkerror'];
		$fromname_checkerror = $options['suwp_fromname_checkerror'];
		$fromemail_checkerror = $options['suwp_fromemail_checkerror'];
		$copyto_checkerror = $options['suwp_copyto_checkerror'];

		add_settings_section(
			'suwp_checkerror_section',  // ID used to identify this section and with which to register options
			'CHECK ORDER ERROR',        // Title to be displayed on the administration page
			'suwp_checkerror_callback', // Callback used to render the description of the section
			'suwp_checkerror_options'   // Page on which to add this section of options
		);
		
		add_settings_field( 
			'suwp_subject_checkerror',                        
			'Check Order Error Subject',                            
			'suwp_subject_checkerror_callback',   
			'suwp_checkerror_options', 
			'suwp_checkerror_section', // The name of the section to which this field belongs
			$subject_checkerror        // The arguments to pass to the callback.  
		);
    
		add_settings_field( 
			'suwp_message_checkerror',                     
			'Check Order Error Message',                         
			'suwp_message_checkerror_callback',    
			'suwp_checkerror_options', 
			'suwp_checkerror_section', // The name of the section to which this field belongs
			$message_checkerror        // The arguments to pass to the callback.    
		);

		add_settings_field( 
			'suwp_fromname_checkerror',          // ID used to identify the field throughout the theme
			'Check Order Error From Name',       // The label to the left of the option interface element
			'suwp_fromname_checkerror_callback', // The name of the function responsible for rendering the option interface
			'suwp_checkerror_options',           // The page on which this option will be displayed
			'suwp_checkerror_section',           // The name of the section to which this field belongs
			$fromname_checkerror                 // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_fromemail_checkerror',                     
			'Check Order Error From Email',              
			'suwp_fromemail_checkerror_callback',  
			'suwp_checkerror_options',                          
			'suwp_checkerror_section', // The name of the section to which this field belongs
			$fromemail_checkerror      // The arguments to pass to the callback.
		);
		
		add_settings_field( 
			'suwp_copyto_checkerror',                      
			'Check Order Error Copy To',               
			'suwp_copyto_checkerror_callback',   
			'suwp_checkerror_options',                          
			'suwp_checkerror_section', // The name of the section to which this field belongs
			$copyto_checkerror         // The arguments to pass to the callback.
		);
		
		/* ------------------------------------------------------------------------ *
		* Section Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_checkerror_callback($args) {
			$html = '<p><strong>NOTIFICATION:</strong> This is the message sent to the admin when attempting to check the status of an existing order fails.</p>
			<p><strong>TIP:</strong> To resotre default values, clear all values in the desired field and "Save Changes".</p>';

			echo $html;

		} // end suwp_checkerror_callback
		
		/* ------------------------------------------------------------------------ *
		* Field Callbacks
		* ------------------------------------------------------------------------ */
		
		function suwp_subject_checkerror_callback($args) {
			
			// Render the output
			$html = '<input type="text" id="suwp_subject_checkerror" name="suwp_checkerror_options[suwp_subject_checkerror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_subject_checkerror-description">The subject line for the message.</p>';
			
			echo $html;
			
		} // end suwp_subject_checkerror_callback

		function suwp_message_checkerror_callback($args) {
			
			// Render the output
			// wp_editor will act funny if it's stored in a string so we run it like this...
			wp_editor( $args, 'suwp_message_checkerror', array( 'textarea_rows'=>8 , 'textarea_name' => 'suwp_checkerror_options[suwp_message_checkerror]') );
			
			$html = '<p class="description" id="suwp_message_checkerror-description1">This is the message sent to the admin when attempting to check the status of an existing order fails.</p>
			<p class="description" id="suwp_message_checkerror-description2">Values appearing like this: <strong>{%Value%}</strong> are custom labels. They may be edited <a href="?page=suwp_options_admin_page&tab=textvalues_options" >HERE</a>. Use the <strong>Default</strong> value to reference your custom label.</p>
			<p class="description" id="suwp_message_checkerror-description3">Available variables: {$customerfirstname} = Customer first name, {$imei} = Submitted IMEI, {$orderid} = Order number, {$phoneinfo} = Phone/Device information, {$service} = Service name, {$processtime} = Estimated delivery, {$reply} = Admin order reply </p>';
			
			echo $html;

		} // end suwp_message_checkerror_callback

		function suwp_fromname_checkerror_callback($args) {
			
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<input type="text" id="suwp_fromname_checkerror" name="suwp_checkerror_options[suwp_fromname_checkerror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromname_checkerror-description">The name associated with the \'From Email\' address. </p>';
			
			echo $html;

		} // end suwp_fromname_checkerror_callback
		
		function suwp_fromemail_checkerror_callback($args) {
			
			$html = '<input type="email" id="suwp_fromemail_checkerror" name="suwp_checkerror_options[suwp_fromemail_checkerror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_fromemail_checkerror-description">Originates from your website. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_fromemail_checkerror_callback
		
		function suwp_copyto_checkerror_callback($args) {
			
			// checkbox code
			// $html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked(1, get_option('show_footer'), false) . '/>'; 
			// $html .= '<label for="show_footer"> '  . $args[0] . '</label>'; 
			// echo $html;

			$html = '<input type="email" id="suwp_copyto_checkerror" name="suwp_checkerror_options[suwp_copyto_checkerror]" style="width: 25em" value="'. $args .'" class="" />
			<p class="description" id="suwp_copyto_checkerror-description">Send a copy to this address. Usually an admin account.</p>';
			
			echo $html;

		} // end suwp_copyto_checkerror_callback
		
		// Register the fields with WordPress
		$args_validate = array(
			'type' => 'string', 
			'description' => 'function to update suwp options',
            'sanitize_callback' => 'suwp_checkerror_validate',
            'default' => array(),
			);
		
		register_setting(
			'suwp_checkerror_options',
			'suwp_checkerror_options',
			$args_validate
		);
		
		function suwp_checkerror_validate($input) {
			
			//Get current user
			global $current_user ;
			$user_id = $current_user->ID;
			add_user_meta($user_id, 'suwp_options_saved', 'true', true);

			if( isset( $input['suwp_subject_checkerror'] ) )
				update_option( 'suwp_subject_checkerror', trim($input['suwp_subject_checkerror']) );

			if( isset( $input['suwp_message_checkerror'] ) )
				update_option( 'suwp_message_checkerror', $input['suwp_message_checkerror'] );

			if( isset( $input['suwp_fromname_checkerror'] ) )
				update_option( 'suwp_fromname_checkerror', trim($input['suwp_fromname_checkerror']) );

			if( isset( $input['suwp_fromemail_checkerror'] ) )
				update_option( 'suwp_fromemail_checkerror', $input['suwp_fromemail_checkerror'] );

			if( isset( $input['suwp_copyto_checkerror'] ) )
				update_option( 'suwp_copyto_checkerror', $input['suwp_copyto_checkerror'] );

			return $input;
		}
		
	} // end suwp_initialize_options_checkerror

	/* Display a standard notice that can be dismissed 
	* Just leverages WordPress's core function
	* admin_notices
	* See here: http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	*/
	public function suwp_activation_admin_notice() {
		
		$dismiss_1_image = '';
		$extract = get_option('suwp_author_info');
		
		if( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$dismiss_1_image = $extract->dismiss_1_image;
			}
		}
		
		//Get current user
		global $current_user ;
		$user_id = $current_user->ID;
		
		//Get the current page to add the notice to
		global $pagenow;
		
		//Make sure we're on the plugins page.
		if ( $pagenow == 'plugins.php' ) {
			
			// delete_user_meta( $user_id, 'suwp_activation_ignore_notice', 'true' ); // >>>>>>>>>>>>>> REMOVE THIS FOR PRODUCTION
			
			// If the user hasn't already dismissed the alert, 
			// Output the activation banner
			if (!get_user_meta($user_id, 'suwp_activation_ignore_notice')) {
			 
				 include( SUWP_PATH . 'admin/partials/stockunlocks-admin-install-banner.php' );
			 
			}
		}
	}
	
	// display a notice when any custom options page is saved
	public function suwp_options_saved_admin_notice() {

		//Get current user
		global $current_user ;
		$user_id = $current_user->ID;

		// dismissible notice after suwp options are saved
		if ( get_user_meta($user_id, 'suwp_options_saved' ) ) {			
			$notice = $this->suwp_exec_get_admin_notice('<strong>Settings saved.</strong>','updated notice is-dismissible');
			echo $notice;
			delete_user_meta( $user_id, 'suwp_options_saved' );
		}

	}

	/* Display a remote notice that can be dismissed 
	* Just leverages WordPress's core function
	* admin_notices
	* See here: http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	*/
	public function suwp_remote_plugin_admin_notice() {
		
		$extract = get_option('suwp_author_info');
		
		if( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$dismiss_1_banner = $extract->dismiss_1_banner;
				$dismiss_1_color = $extract->dismiss_1_color;
				$dismiss_1_key  = $extract->dismiss_1_key;
				$dismiss_1_key_del  = $extract->dismiss_1_key_del;
				$dismiss_1_path = $extract->dismiss_1_path;
				$dismiss_1_image = $extract->dismiss_1_image;
				$dismiss_1_msg  = $extract->dismiss_1_msg;
				$dismiss_1_dashtxt = $extract->dismiss_1_dashtxt;
				$dismiss_1_dashlink = $extract->dismiss_1_dashlink;
				$dismiss_1_dashtarget = $extract->dismiss_1_dashtarget;
				
				//Get current user
				global $current_user ;
				$user_id = $current_user->ID;
				
				//Get the current page to add the notice to
				global $pagenow;
				
				//Make sure we're on the plugins page.
				if ( $pagenow == 'plugins.php' ) {
					
					// delete previously used meta key
					delete_user_meta( $user_id, $dismiss_1_key_del );
					
					// If the user hasn't already dismissed the alert, 
					// Output the activation banner
					if ( !get_user_meta($user_id, $dismiss_1_key ) && $dismiss_1_banner === 'true') {
					 
						 include( SUWP_PATH . $dismiss_1_path );
					}
				}
			}
		}
	}
	
	/* Display a remote notice that can be dismissed 
	* Just leverages WordPress's core function
	* admin_notices
	* See here: http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	*/
	public function suwp_remote_notice_ignore() {
		
		//Get the global user
		global $current_user;
		$user_id = $current_user->ID;
		
		/* If user clicks to ignore the notice, 
		* add that to their user meta 
		* the banner then checks whether this tag
		* exists already or not.
		* See here: http://codex.wordpress.org/Function_Reference/add_user_meta
		*/
		$extract = get_option('suwp_author_info');
		
		if( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				
				$dismiss_0_key  = $extract->dismiss_0_key;
				
				if( isset( $_GET[$dismiss_0_key] ) && '0' == $_GET[$dismiss_0_key] && isset( $_GET['screen_id_url'] ) ) {
					
					$screen_id_url = $_GET['screen_id_url'];
					$admin_url = '';
					
					switch( $screen_id_url ) {
						
						case 'toplevel_page_suwp_dashboard_admin_page':
							$admin_url = '/admin.php?page=suwp_dashboard_admin_page';
							break;
						case 'edit-suwp_apisource':
							$admin_url = '/edit.php?post_type=suwp_apisource';
							break;
						case 'stockunlocks_page_suwp_importservices_admin_page':
							$admin_url = '/admin.php?page=suwp_importservices_admin_page';
							break;
						case 'stockunlocks_page_suwp_options_admin_page':
							$admin_url = '/admin.php?page=suwp_options_admin_page';
							break;
					}
					
					if( isset( $admin_url ) ) {
						
						add_user_meta($user_id, $dismiss_0_key, 'true', true);
						wp_redirect( admin_url( $admin_url ), 302 );
						
						// stop all other processing 
						exit;
					}
					
				} else {
					
					// return false if unable to obtain url variables 
					return false;
				}
			}
		}
	}
	
	public function suwp_remote_admin_notice() {
		
		$parent_file = 'suwp_dashboard_admin_page';
		$extract = get_option('suwp_author_info');
		
		if( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$dismiss_0_banner = $extract->dismiss_0_banner;
				$dismiss_0_color = $extract->dismiss_0_color;
				$dismiss_0_key  = $extract->dismiss_0_key;
				$dismiss_0_key_del  = $extract->dismiss_0_key_del;
				$dismiss_0_path = $extract->dismiss_0_path;
				$dismiss_0_image = $extract->dismiss_0_image;
				$dismiss_0_msg  = $extract->dismiss_0_msg;
				$dismiss_0_dashtxt = $extract->dismiss_0_dashtxt;
				$dismiss_0_dashlink = $extract->dismiss_0_dashlink;
				$dismiss_0_dashtarget = $extract->dismiss_0_dashtarget;
					
			   //Get current user
			   global $current_user ;
			   $user_id = $current_user->ID;
			   
			   //Get the current page to add the notice to
			   global $pagenow;
			   
				if (is_object( get_current_screen() ) ) {
					
					$screen = get_current_screen();
					
					// Make sure we're on pages specific to the StockUnlocks plugin only.
					if ( $screen->parent_file === $parent_file ) {
						
						// delete previously used meta key
						delete_user_meta( $user_id, $dismiss_0_key_del );
						
						// If it's active and if the user hasn't already dismissed the alert,
						// Output the remote banner
						if ( !get_user_meta($user_id, $dismiss_0_key ) && $dismiss_0_banner === 'true') {
						
							include( SUWP_PATH . $dismiss_0_path );
						}
					}
				}
			}
		}
	}
	
	// returns a unique link for closing the remote banner
	public function suwp_get_remote_notice_ignore_link( $ingnore_id = 0 ) {
		
		// $dismiss_0_key = 'suwp_remote_ignore_notice';
		
		$extract = get_option('suwp_author_info');
		
		if( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$dismiss_0_key  = $extract->dismiss_0_key;
					
				$link_href = 'admin-ajax.php?action=suwp_remote_notice_ignore&' . $dismiss_0_key . '='. $ingnore_id;
				// return the remove link
				return esc_url($link_href);
			}
		}
		
		return '';
	}
	
	// messages for the admin
	public function suwp_default_admin_footer() {
		
		// >>> $plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
		$show_notice = false;
		$screen = get_current_screen();
		$extract = get_option('suwp_author_info');
					
		if( is_object( $extract ) ) {
			
			if( isset($extract->error) ) {
				
				if( is_object( get_current_screen() ) ) {
					
					$screen = get_current_screen();
					
					if( $screen->id !== 'stockunlocks_page_suwp_options_admin_page' ) {
							
						if( $screen->parent_file == 'suwp_dashboard_admin_page' ) {
									
							wp_redirect(admin_url('/admin.php?page=suwp_options_admin_page'), 302);
							
							// stop all other processing 
							exit;
						}
						
					} else {
						
						// need to refresh the admin dashboard links to get new 'Providers' link
						// otherwise, clicking on any other StockUnlocks sub_menu will do it
					}
					
					if( $screen->parent_file == 'suwp_dashboard_admin_page' ) {
										
						// >>> Please enter the correct values and try again. To use the basic license, simply clear all field values and <strong>Save Changes</strong>
						$notice = $this->suwp_exec_get_admin_notice( $extract->error, 'error notice');
						
						// echo the notice html
						echo( $notice );
					}
				}
				
			} else {
			
				$show_notice = true;
			}
			
		} else {
			
			// connection problem
			if (is_object( get_current_screen() ) ) {
				
				$screen = get_current_screen();
			
				if( $screen->id !== 'stockunlocks_page_suwp_options_admin_page' ) {
						
					if( $screen->parent_file == 'suwp_dashboard_admin_page' ) {
								
						wp_redirect(admin_url('/admin.php?page=suwp_options_admin_page'), 302);
						
						// stop all other processing 
						exit;
					}
				}
			
				if( $screen->parent_file == 'suwp_dashboard_admin_page' ) {
									
					// updated notice, error notice, update-nag notice, is-dismissible
					// >>> $notice = $plugin_admin->suwp_exec_get_admin_notice('ERROR- No connection to the license server. Restore the connection and enter a valid license e-mail and key combination and <strong>Save Changes</strong>.', 'error notice');
					$notice = $this->suwp_exec_get_admin_notice('ERROR- No connection to the license server. Restore the connection and enter a valid license e-mail and key combination and <strong>Save Changes</strong>.', 'error notice');
					
					// echo the notice html
					echo( $notice );
				}
			}
		}
		
		if( $show_notice ) {
			
			$latest_version = $extract->version;
			$msg_0_banner = $extract->msg_0_banner;
			$msg_0 = $extract->msg_0;
			$msg_0_class = $extract->msg_0_class;
			$msg_0_link = $extract->msg_0_link;
			$msg_0_link_text = $extract->msg_0_link_text;
			$msg_0_link_target = $extract->msg_0_link_target;
			
			if ($msg_0_banner === 'true') {
				
				if (is_object( get_current_screen() ) ) {
					
					$screen = get_current_screen();
					
					if( $screen->parent_file == 'suwp_dashboard_admin_page' || $screen->id == 'suwp_apisource') {
						
						// 'target="_blank"';
						
						// $redirect_url = '<a href="'. 'https://www.stockunlocks.com/pro' .'">More Info</a>';
						$redirect_url = '<a href="'. $msg_0_link .'" '. $msg_0_link_target . '>' . $msg_0_link_text . '</a>';
						
						// the 'update-nag notice' class messes up the Providers custom post type view
						// simply replace with the 'updated notice' for now.
						if( $screen->id == 'edit-suwp_apisource' || $screen->id == 'suwp_apisource' ) {
							$msg_0_class = 'updated notice';
						}
						
						// updated notice, error notice, update-nag notice, is-dismissible
						// >>> $notice = $plugin_admin->suwp_exec_get_admin_notice($msg_0 . '  ' . $redirect_url, $msg_0_class );
						$notice = $this->suwp_exec_get_admin_notice($msg_0 . '  ' . $redirect_url, $msg_0_class );
						
						// echo the notice html
						echo( $notice );
					}
				}
			}
			
			// upgrade? only for Providers screen
			$msg_3_banner = $extract->msg_3_banner;
			$msg_3 = $extract->msg_3;
			$msg_3_class = $extract->msg_3_class;
			$msg_3_link = $extract->msg_3_link;
			$msg_3_link_text = $extract->msg_3_link_text;
			$msg_3_link_target = $extract->msg_3_link_target;
			
			if ($msg_3_banner === 'true') {
				
				if (is_object( get_current_screen() ) ) {
					
					$screen = get_current_screen();
					
					if( $screen->parent_file == 'suwp_dashboard_admin_page' || $screen->id == 'suwp_apisource') {
						
						// the 'update-nag notice' class messes up the Providers custom post type view
						// simply replace with the 'updated notice' for now.
						if( $screen->id == 'edit-suwp_apisource' || $screen->id == 'suwp_apisource' ) {
							$msg_3_class = 'updated notice';
							
							// 'target="_blank"';
							
							// $redirect_url = '<a href="'. 'https://www.stockunlocks.com/pro' .'">More Info</a>';
							$redirect_url = '<a href="'. $msg_3_link .'" '. $msg_3_link_target . '>' . $msg_3_link_text . '</a>';
							
							// updated notice, error notice, update-nag notice, is-dismissible
							// >>> $notice = $plugin_admin->suwp_exec_get_admin_notice($msg_0 . '  ' . $redirect_url, $msg_0_class );
							$notice = $this->suwp_exec_get_admin_notice($msg_3 . '  ' . $redirect_url, $msg_3_class );
							
							// echo the notice html
							echo( $notice );
						
						}
					}
				}
			}
			
			if( STOCKUNLOCKS_VERSION < $latest_version ) {
				
				if (is_object( get_current_screen() ) ) {
					
					$screen = get_current_screen();
					$redirect_url = '';
					
					if ( $screen->parent_file == 'suwp_dashboard_admin_page' ) {
					
						$redirect_url = '<a href="'. admin_url('/update-core.php') .'">Update Here</a>';
						
						// updated notice, error notice, update-nag notice, is-dismissible
						// >>> $notice = $plugin_admin->suwp_exec_get_admin_notice('The StockUnlocks plugin needs to be upgraded. You are using version: <strong>' . STOCKUNLOCKS_VERSION . '</strong>. The latest version is: <strong>' . $latest_version . '</strong>. ' . $redirect_url,'update-nag notice');
						$notice = $this->suwp_exec_get_admin_notice('The StockUnlocks plugin needs to be udpated. You are using version: <strong>' . STOCKUNLOCKS_VERSION . '</strong>. The latest version is: <strong>' . $latest_version . '</strong>. ' . $redirect_url,'updated notice');
						
						// echo the notice html
						echo( $notice );
					}
				}
			}
		}	
	}
	
	// hint: increases _suwp_qty_sent count by one
	public function suwp_update_qty_sent( $uid ) {
		
		global $wpdb;
		
		// setup our return value
		$return_value = false;
		
		try {
			
			$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
			
			// get current imei quantity sent count
			$current_count = $wpdb->get_var( 
				$wpdb->prepare( 
					"
						SELECT meta_value 
						FROM $table_name 
						WHERE meta_key = '_suwp_qty_sent' AND order_item_id = %s
					", 
					$uid
				) 
			);
			
			// set new count
			$new_count = (int)$current_count+1;
			
			// update imei quantity sent for this order entry
			$wpdb->query(
				$wpdb->prepare( 
					"
						UPDATE $table_name
						SET meta_value = $new_count 
						WHERE meta_key = '_suwp_qty_sent' AND order_item_id = %s
					", 
					$uid
				) 
			);
			
			$return_value = true;
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		return $return_value;
		
	}

	// hint: return the _suwp_qty_sent value; the typical woocommerce function was caching results
	public function suwp_get_qty_sent( $uid ) {
		
		global $wpdb;
		
		// setup our return value
		$return_value = -1;
		
		try {
			
			$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
			
			// get current imei quantity sent count
			$current_count = $wpdb->get_var( 
				$wpdb->prepare( 
					"
						SELECT meta_value 
						FROM $table_name 
						WHERE meta_key = '_suwp_qty_sent' AND order_item_id = %s
					", 
					$uid
				) 
			);
			
			// set return value
			$return_value = $current_count;
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		return $return_value;
		
	}

	// hint: increases _suwp_qty_done count by one
	public function suwp_update_qty_done( $uid ) {
		
		global $wpdb;
		
		// setup our return value
		$return_value = false;
		
		try {
			
			$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
			
			// get current imei quantity done (replied) count
			$current_count = $wpdb->get_var( 
				$wpdb->prepare( 
					"
						SELECT meta_value 
						FROM $table_name 
						WHERE meta_key = '_suwp_qty_done' AND order_item_id = %s
					", 
					$uid
				) 
			);
			
			// set new count
			$new_count = (int)$current_count+1;
			
			// update imei quantity done (replied) for this order entry
			$wpdb->query(
				$wpdb->prepare( 
					"
						UPDATE $table_name
						SET meta_value = $new_count 
						WHERE meta_key = '_suwp_qty_done' AND order_item_id = %s
					", 
					$uid
				) 
			);
			
			$return_value = true;
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		return $return_value;
		
	}
	
	// hint: return the _suwp_qty_done value; the typical function was caching results
	public function suwp_get_qty_done( $uid ) {
		
		global $wpdb;
		
		// setup our return value
		$return_value = -1;
		
		try {
			
			$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
			
			// get current imei quantity done (replied) count
			$current_count = $wpdb->get_var( 
				$wpdb->prepare( 
					"
						SELECT meta_value 
						FROM $table_name 
						WHERE meta_key = '_suwp_qty_done' AND order_item_id = %s
					", 
					$uid
				) 
			);
			
			// set return value
			$return_value = $current_count;
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		return $return_value;
		
	}
	
	public function suwp_search_for_comment_type($id, $array) {
		foreach ($array as $key => $val) {
		   if ($val['comment_type'] === $id) {
			   return $key;
		   }
		}
		return null;
	}

}
