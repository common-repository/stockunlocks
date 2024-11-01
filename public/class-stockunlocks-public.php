<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.stockunlocks.com/unlock-phones-with-your-website/
 * @since      1.5.0
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Stock_Unlocks
 * @subpackage Stock_Unlocks/public
 * @author     StockUnlocks <support@stockunlocks.com>
 */
class Stock_Unlocks_Public {

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
	 * @param      string    $stockunlocks       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $stockunlocks, $version ) {

		$this->stockunlocks = $stockunlocks;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.5.0
	 */
	public function suwp_public_styles() {

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
		// add to que of scripts that get loaded into every page
		// >>> wp_enqueue_style( 'stockunlocks-public-css', plugin_dir_url( __FILE__ ) . 'css/stockunlocks-public.css', array(), $this->version, 'all' );
		
			
		// register scripts with WordPress's internal library
		// wp_register_style('stockunlocks-css-public', plugins_url('/public/css/stockunlocks-public.css?ver=1.5.0',__FILE__));
		wp_register_style('stockunlocks-css-public', plugin_dir_url( __FILE__ ) . 'css/stockunlocks-public.css', array(), $this->version, 'all' );
		
		// add to que of scripts that get loaded into every page
		// wp_enqueue_script('stockunlocks-js-public');
		wp_enqueue_style('stockunlocks-css-public');
	
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.5.0
	 */
	public function suwp_public_scripts() {

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
		// add to que of scripts that get loaded into every page
		// >>>>> wp_enqueue_script( 'stockunlocks-public-js', plugin_dir_url( __FILE__ ) . 'js/stockunlocks-public.js', array( 'jquery' ), $this->version, true );
		
		// register scripts with WordPress's internal library
		// wp_register_script('stockunlocks-js-public', plugins_url('/public/js/stockunlocks.js?ver=1.5.0',__FILE__), array('jquery'),'',true);
		wp_register_script('stockunlocks-public-js', plugin_dir_url( __FILE__ ) . 'js/stockunlocks-public.js', array( 'jquery' ), $this->version, true );
		wp_register_script('sweetalert2.all.min.js', plugin_dir_url( __FILE__ ) . 'js/sweetalert2.all.min.js', array( 'jquery' ), $this->version, true );
		
		// add to que of scripts that get loaded into every page
		wp_enqueue_script('stockunlocks-public-js');
		wp_enqueue_script('sweetalert2.all.min.js');
	
		// get the default values for our options
		$options = $this->suwp_exec_get_current_options();
		
		$phpInfo = array(
			'suwp_home' => get_option( 'home' ),
			'suwp_siteurl' => get_option( 'siteurl' ),
			'suwp_admin_siteurl' => get_admin_url(),
			'suwp_country_label' => $options['suwp_country_label'],
			'suwp_network_label' => $options['suwp_network_label'],
			'suwp_brand_label' => $options['suwp_brand_label'],
			'suwp_model_label' => $options['suwp_model_label'],
			'suwp_service_label' => $options['suwp_service_label'],
			'suwp_imei_label' => $options['suwp_imei_label'],
			'suwp_sn_label' => $options['suwp_sn_label'],
			'suwp_mep_label' => $options['suwp_mep_label'],
			'suwp_kbh_label' => $options['suwp_kbh_label'],
			'suwp_activation_label' => $options['suwp_activation_label'],
			'suwp_emailresponse_label' => $options['suwp_emailresponse_label'],
			'suwp_emailconfirm_label' => $options['suwp_emailconfirm_label'],
			'suwp_deliverytime_label' => $options['suwp_deliverytime_label'],
			'suwp_code_label' => $options['suwp_code_label'],
			'suwp_not_required_msg' => $options['suwp_not_required_msg'],
			'suwp_blank_msg' => $options['suwp_blank_msg'],
			'suwp_payment_email_msg' => $options['suwp_payment_email_msg'],
			'suwp_invalidemail_msg' => $options['suwp_invalidemail_msg'],
			'suwp_nonmatching_msg' => $options['suwp_nonmatching_msg'],
			'suwp_invalidentry_msg' => $options['suwp_invalidentry_msg'],
			'suwp_exceeded_msg' => $options['suwp_exceeded_msg'],
			'suwp_invalidchar_msg' => $options['suwp_invalidchar_msg'],
			'suwp_invalidlength_msg' => $options['suwp_invalidlength_msg'],
			'suwp_invalidformat_msg' => $options['suwp_invalidformat_msg'],
			'suwp_dupvalues_msg' => $options['suwp_dupvalues_msg'],
			
		);
		wp_localize_script( 'stockunlocks-public-js', 'phpInfo', $phpInfo );
	
	}
	
	/**
	* Loaded during 'init'
	* Register the new "suwp_apisource" post type to use for representing unlocking
	* service providers connecting via the API.
	*/
	public function suwp_add_apisources_post_type() {
		
		$extract = get_option( 'suwp_author_info' );
		$include = kKLSSKjVsell5zJz8M;
		if ( is_object( $extract ) ) {
			if( !isset($extract->error) ) {
				$include = $extract->include_11;
			}
		}
		include( SUWP_PATH_CLUDES . $include );
	}
	
	// registers all our custom shortcodes, etc.
	// labels appear above all orders as categories: not sure where 'label' appears yet
	// 'label_count' -> first is for singular, second is for multiple
	public function suwp_register_allcodes() {
				
		$this->suwp_check_woo();
		
		// create the suwp_service product category
		$this->suwp_insert_custom_category();
		
		// create the SU import post status
		$this->suwp_insert_custom_post_status();
		
		$this->suwp_short_codes();
		
		// register custom post statuses
		register_post_status('wc-suwp-manual', array(
			'label' => 'Manual Processing',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Manual Processing <span class="count">(%s)</span>', 'Manual Processing <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-rejected', array(
			'label' => 'Paypal Rejected',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Paypal rejected <span class="count">(%s)</span>', 'Paypal rejected <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-error', array(
			'label' => 'Processing Error',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Processing error <span class="count">(%s)</span>', 'Processing error <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-ordered', array(
			'label' => 'Code Ordered',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Code ordered <span class="count">(%s)</span>', 'Code ordered <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-order-part', array(
			'label' => 'Partially Ordered',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Partially ordered <span class="count">(%s)</span>', 'Partially ordered <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-pending', array(
			'label' => 'Code Pending',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Code pending <span class="count">(%s)</span>', 'Code pending <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-available', array(
			'label' => 'Code Delivered',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Code delivered <span class="count">(%s)</span>', 'Code delivered <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-avail-part', array(
			'label' => 'Codes Partially Delivered',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Codes partially delivered <span class="count">(%s)</span>', 'Codes partially delivered <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-unavailable', array(
			'label' => 'Code Unavailable',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Code unavailable <span class="count">(%s)</span>', 'Code unavailable <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-refunding', array(
			'label' => 'Code Pending Refund',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Code Pending refund <span class="count">(%s)</span>', 'Code Pending refund <span class="count">(%s)</span>')
		));
		
		register_post_status('wc-suwp-refund-part', array(
			'label' => 'Codes Partially Refunded',
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop('Codes Partially refunded <span class="count">(%s)</span>', 'Codes Partially refunded <span class="count">(%s)</span>')
		));
		
	}
	
	// x.x Front End Custom Fields: Woocommerce
	// This code will do the validation for the custom fields.
	// works for a specific category only
	function suwp_custom_fields_validation($flag, $product_id, $cart_item_data) {
			
			$slugs = array();
			$terms = get_the_terms( $product_id, 'product_cat' );
			
			if (is_array($terms)){
			  foreach ( $terms as $term ) {
				$slugs[] = $term->slug; // $term->$vals['term']
			  }
			}
			
			$test = array();
			$i = 1;
			if (is_array($cart_item_data)){
			  foreach ( $cart_item_data as $term ) {
				$test[] = $term; // $term->$vals['term']
				$i++;
			  }
			}
			
			if (in_array('suwp_service', $slugs, true)) {

				$flag_continue = true;
				$flag_msg_blankempty = array();
				$flag_msg_incorrectlength = array();
				$flag_msg_incorrectchar = array();
				$flag_msg_invalidimei = array();
				$flag_msg_duplicatenums = array();
				$flag_msg_mixedemail = array();
				$flag_msg_invalidemail = array();
				$flag_msg_exceedednum = array();

				$label_api1 = get_field('_suwp_custom_api1_label', $product_id );
				$label_api2 = get_field('_suwp_custom_api2_label', $product_id );
				$label_api3 = get_field('_suwp_custom_api3_label', $product_id );
				$label_api4 = get_field('_suwp_custom_api4_label', $product_id );
				$label_imei = get_option('suwp_imei_label');
				$label_sn = get_option('suwp_sn_label');
				$label_network = get_option('suwp_network_label');
				$label_country = get_option('suwp_country_label');
				$label_brand = get_option('suwp_brand_label');
				$label_model = get_option('suwp_model_label');
				$label_mep = get_option('suwp_mep_label');
				$label_email_response = get_option('suwp_emailresponse_label');
				$label_email_confirm = get_option('suwp_emailconfirm_label');

				if ( isset( $_REQUEST['suwp-api1-name'] ) ) {
					// confirm that a custom api value was entered
					if ( !empty(trim($_REQUEST['suwp-api1-name'])) ) {
						// set the label value for later use
						update_option( 'suwp_custom_api1_label', $label_api1 );
						error_log( 'A VALID API 1 VALUE WAS ENTERED' );
					} else {
						error_log( 'NO VALID API 1 VALUE WAS ENTERED' );
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_api1 . '</strong><br>';
					}
				}

				if ( isset( $_REQUEST['suwp-api2-name'] ) ) {
					// confirm that a custom api value was entered
					if ( !empty(trim($_REQUEST['suwp-api2-name'])) ) {
						// set the label value for later use
						update_option( 'suwp_custom_api2_label', $label_api2 );
						error_log( 'A VALID API 2 VALUE WAS ENTERED' );
					} else {
						error_log( 'NO VALID API 2 VALUE WAS ENTERED' );
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_api2 . '</strong><br>';
					}
				}

				if ( isset( $_REQUEST['suwp-api3-name'] ) ) {
					// confirm that a custom api value was entered
					if ( !empty(trim($_REQUEST['suwp-api3-name'])) ) {
						// set the label value for later use
						update_option( 'suwp_custom_api3_label', $label_api3 );
						error_log( 'A VALID API 3 VALUE WAS ENTERED' );
					} else {
						error_log( 'NO VALID API 3 VALUE WAS ENTERED' );
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_api3 . '</strong><br>';
					}
				}

				if ( isset( $_REQUEST['suwp-api4-name'] ) ) {
					// confirm that a custom api value was entered
					if ( !empty(trim($_REQUEST['suwp-api4-name'])) ) {
						// set the label value for later use
						update_option( 'suwp_custom_api4_label', $label_api4 );
						error_log( 'A VALID API 4 VALUE WAS ENTERED' );
					} else {
						error_log( 'NO VALID API 4 VALUE WAS ENTERED' );
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_api4 . '</strong><br>';
					}
				}

				if ( !empty( $_REQUEST['suwp-country-id'] ) ) {
					// confirm that a network was selected
					if ( $_REQUEST['suwp-network-id'] > 0 ) {
						error_log( 'A VALID NETWORK WAS CHOSEN' );
					} else {
						error_log( 'NO NETWORK WAS CHOSEN : ' . $_REQUEST['suwp-network-id']);
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_network . '</strong><br>';
					}
				}
				
				if ( !empty( $_REQUEST['suwp-brand-id'] ) ) {
					// confirm that a network was selected
					if ( $_REQUEST['suwp-model-id'] > 0 ) {
						error_log( 'A VALID MODEL WAS CHOSEN' );
					} else {
						error_log( 'NO MODEL WAS CHOSEN : ' . $_REQUEST['suwp-model-id'] );
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_model . '</strong><br>';
					}
				}
				
				if ( !empty( $_REQUEST['suwp-mep-id'] ) ) {
					// confirm that a network was selected
					if ( $_REQUEST['suwp-mep-id'] > 0 ) {
						error_log( 'A VALID MEP WAS CHOSEN' );
					} else {
						error_log( 'NO MEP WAS CHOSEN' );
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_mep . '</strong><br>';
					}
				}
				
				$email_response = '';
				$email_confirm = '';
				if ( isset( $_REQUEST['suwp-email-response'] ) ) {
					$email_response = $_REQUEST['suwp-email-response'];	
				}
				if ( isset( $_REQUEST['suwp-email-confirm'] ) ) {
					$email_confirm = $_REQUEST['suwp-email-confirm'];	
				}

				$is_paymentemail = false;
				if ( isset( $_REQUEST['suwp-use-payment-email'] ) ) {
					if ( $_REQUEST['suwp-use-payment-email'] ) {
						$is_paymentemail = true;
					}
				}
				
				$hideimei_status = get_field('_suwp_hideimei_status', $product_id );
				$hideimei = false;

				if ( $hideimei_status == 'yes') {
					$hideimei = true;
				}

				$allowtext_status = get_field('_suwp_allowtext_status', $product_id );
				$allowtext = false;
				$serial_text = get_option('suwp_imei_label');

				if ( $allowtext_status == 'yes') {
					$allowtext = true;
					$serial_text = get_option('suwp_sn_label');
				}
				
				$num_allowed = '';
				$serial_limit = get_field('_suwp_serial_limit', $product_id );
				if ( $serial_limit != '' ) {
					$num_allowed = $serial_limit;
				}

				$serial_length = get_field('_suwp_serial_length', $product_id );
				$count_length = true;

				if ( $serial_length === '' ) {
					$count_length = false;
					$serial_text = get_option('suwp_sn_label');
				} else {
					if ( $serial_length < 15 || $serial_length > 15 ) {
						$serial_text = get_option('suwp_sn_label');
					}
				}

				$imei_values = '';
				if ( isset( $_REQUEST['suwp-imei-values'] ) ) {
					$imei_values = trim($_REQUEST['suwp-imei-values']);
				}
				$chk_dup_imei = array();
				$imeis = explode( "\n", trim($imei_values));
				$imeis_new = array();

				// clean out blank entries
				foreach( $imeis as $imei_val ) {
					if ( trim($imei_val) != '') {
						$imeis_new[] = $imei_val;
					}
				}
				$imeis = $imeis_new;
				$imei_count = count( $imeis );

				// hide or display the IMEI/Serial Number field
				if ( !$hideimei ) {

					if ( empty( $imei_values ) ) {
						// empty submission
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $serial_text . '</strong><br>';
					} else {
					
						foreach( $imeis as $imei_val ):

							$imei1 = trim($imei_val);
							$imei = str_replace(' ','',$imei1);

							$chk_dup_imei[] = $imei;

							$actual_length = (int)strlen($imei);
						
							if ( !$allowtext ) {

								if ( $count_length && $actual_length != $serial_length && $this->suwp_is_digits($imei) == 1 ) {

									$flag_continue = false;
									$flag_msg_incorrectlength[] = get_option('suwp_invalidentry_msg') . ': <strong>' . $imei . ' = ' . $actual_length . '</strong><br>';
									
								}

								if ( $this->suwp_is_digits($imei) != 1 ) {

									$flag_continue = false;
									$flag_msg_incorrectchar[] = get_option('suwp_invalidentry_msg') . ': <strong>' . $imei . '</strong><br>';
								
								}
							
								if ( $count_length && $actual_length == $serial_length && $this->suwp_is_digits($imei) == 1 ) {
									
									// only do the suwp_check_imei when imei is 15 digits
									if ($serial_length == 15) {
										
										if ( !$this->suwp_check_imei($imei) ) {
											$flag_continue = false;
											$flag_msg_invalidimei[] = get_option('suwp_invalidentry_msg') . ': <strong>' . $imei . '</strong><br>';
										}
									}

								}
								
							}

							if ( $allowtext ) {

								if ( $count_length && $actual_length != $serial_length ) {
								
									$flag_continue = false;
									$flag_msg_incorrectlength[] = get_option('suwp_invalidentry_msg') . ': <strong>' . $imei . ' = ' . $actual_length . '</strong><br>';
									
								}
								
							}
							
						endforeach; // foreach( $imeis as $imei_val )

						if (count($chk_dup_imei) != count(array_unique($chk_dup_imei)))  {
							// Confirm that there were no duplicate IMEI,S/N values submitted.
							$duplicates = suwp_array_not_unique($chk_dup_imei);
							$duplicates = array_unique($duplicates);
							$dup_txt = '';
							foreach( $duplicates as $dupe ):
								$dup_txt .= '<strong>' . $dupe . '</strong><br>';
							endforeach;
							$flag_continue = false;
							$flag_msg_duplicatenums[] = $dup_txt;
							
						}
						
						if ( $num_allowed != '' ) {
							if ( count($chk_dup_imei) > intval($num_allowed) ) {
								$flag_continue = false;
								$flag_msg_exceedednum[] = '<strong>(' . $serial_text . ') ' . count($chk_dup_imei) . ' != ' . $num_allowed . '</strong><br>';
							}
						}
						
					}

				} // if ( !$hideimei ) {
				
				if ( !$is_paymentemail ) {
						
					if ( empty( $_REQUEST['suwp-email-response'] ) ) {
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_email_response . '</strong><br>';
						
					} elseif ( empty( $_REQUEST['suwp-email-confirm'] ) ) {
						$flag_continue = false;
						$flag_msg_blankempty[] = '<strong>' . $label_email_confirm . '</strong><br>';
						
					} elseif ($email_response != $email_confirm) {
						// both email fields have values, check if match
						$flag_continue = false;
						$flag_msg_mixedemail[] = '<strong>' . $email_response . ' != ' . $email_confirm . '</strong><br>';

					}

				}

				// Not Required
				// Reply to Billing Email Address
				// Invalid entry:
				// blank/empty: Please select or enter at least one value in the following field(s):
				// incorrect length: Number of characters required = {numChars}. Invalid entry: {IMEI} = {numChars}
				// incorrect char: Digits only: no letters, punctuation, or spaces allowed. Invalid entry: {IMEI}
				// invalid imei: Not a valid entry or format: {IMEI}
				// duplicate nums: Duplicate values are not allowed: {IMEI}
				// non-matching email: Sorry, the email addresses do not match: {emailResponse}
				// invalid email: Please enter a valid email address: {email}
				// exceeded total allowed: Exceeded the total number allowed {num}: {imei;s/n}: {num_entered} != {num}
				
				// invalid imei:
				$flag_msg_invalidimei_string = get_option('suwp_invalidformat_msg') . ':<br>';
				foreach( $flag_msg_invalidimei as $msg_val ):
					$flag_msg_invalidimei_string .= $msg_val;
				endforeach;
				// blank/empty: 
				$flag_msg_blankempty_string = get_option('suwp_blank_msg') . ':<br>';
				foreach( $flag_msg_blankempty as $msg_val ):
					$flag_msg_blankempty_string .= $msg_val;
				endforeach;
				// incorrect length:
				$flag_msg_incorrectlength_string = get_option('suwp_invalidlength_msg') . ' = ' . $serial_length . '.<br>';
				foreach( $flag_msg_incorrectlength as $msg_val ):
					$flag_msg_incorrectlength_string .= $msg_val;
				endforeach;
				// incorrect char:
				$flag_msg_incorrectchar_string = get_option('suwp_invalidchar_msg') . ':<br>';
				foreach( $flag_msg_incorrectchar as $msg_val ):
					$flag_msg_incorrectchar_string .= $msg_val;
				endforeach;
				// duplicate nums:
				$flag_msg_duplicatenums_string = get_option('suwp_dupvalues_msg') . ':<br>';
				foreach( $flag_msg_duplicatenums as $msg_val ):
					$flag_msg_duplicatenums_string .= $msg_val;
				endforeach;
				// non-matching email: 
				$flag_msg_mixedemail_string = get_option('suwp_nonmatching_msg') . ':<br>';
				foreach( $flag_msg_mixedemail as $msg_val ):
					$flag_msg_mixedemail_string .= $msg_val;
				endforeach;
				// invalid email: 
				$flag_msg_invalidemail_string = get_option('suwp_invalidemail_msg') . ':<br>';
				foreach( $flag_msg_invalidemail as $msg_val ):
					$flag_msg_invalidemail_string .= $msg_val;
				endforeach;
				// exceeded total allowed:
				$flag_msg_exceedednum_string = get_option('suwp_exceeded_msg') . ' (' . $serial_limit . '):<br>';
				foreach( $flag_msg_exceedednum as $msg_val ):
					$flag_msg_exceedednum_string .= $msg_val;
				endforeach;
				
				if ( !$flag_continue ) {

					if ( !empty($flag_msg_invalidimei) ) {
						wc_add_notice( __( $flag_msg_invalidimei_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_duplicatenums) ) {
						wc_add_notice( __( $flag_msg_duplicatenums_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_exceedednum) ) {
						wc_add_notice( __( $flag_msg_exceedednum_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_incorrectlength) ) {
						wc_add_notice( __( $flag_msg_incorrectlength_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_incorrectchar) ) {
						wc_add_notice( __( $flag_msg_incorrectchar_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_blankempty) ) {
						wc_add_notice( __( $flag_msg_blankempty_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_mixedemail) ) {
						wc_add_notice( __( $flag_msg_mixedemail_string, 'stockunlocks' ), 'error' );
						return false;
					}

					if ( !empty($flag_msg_invalidemail) ) {
						wc_add_notice( __( $flag_msg_invalidemail_string, 'stockunlocks' ), 'error' );
						return false;
					}
					
				}
			}
			
		return $flag;
	}

	
	/**
	 * Hook: Set the quantity based on the number of IMEI submitted
	 */
	public function suwp_modify_cart_before_add( $cart_item_data, $product_id ) {
		
		global $woocommerce;
		
		$cartQty = $woocommerce->cart->get_cart_item_quantities();
		$cartItems = $woocommerce->cart->cart_contents;
		
		$slugs = array();
		$terms = get_the_terms( $product_id, 'product_cat' );
		
		if (is_array($terms)){
		  foreach ( $terms as $term ) {
			$slugs[] = $term->slug; // $term->$vals['term']
		  }
		}
	
		if (in_array('suwp_service', $slugs, true)){
			
			if (array_key_exists($product_id,$cartQty)) {
				
				foreach ($cartItems as $item => $values) {
					// $item = unique cart item id
					// $values['suwp_country_id'];
					// $values['unique_key'];
					// $values['suwp_imei_values'];
					
					// avoid trying to modify non-suwp products
					if (array_key_exists('suwp_imei_values',$values)) {
						
						// error_log('Key exists!');
						
						$imei_values = trim($values['suwp_imei_values']);
						$text_area = explode( "\n", $imei_values);
						$imeis_new = array();
						// clean out blank entries
						foreach( $text_area as $imei_val ) {
							if ( trim($imei_val) != '') {
								$imeis_new[] = $imei_val;
							}
						}
						$text_area = $imeis_new;
						$imei_count = count( $text_area );
						$woocommerce->cart->set_quantity( $item, $imei_count );
						
					}   else    {
						// error_log('Key does not exist!');
						// $woocommerce->cart->set_quantity( $item, 1 );
						
					}
					  
				}
			}
		}
	}
	
	// x.x Front End Custom Fields: Woocommerce
	// This code will store the custom fields ( for the product that is being added to cart ) into cart item data ( each cart item has its own data )
	public function suwp_save_values_to_cutsom_fields( $cart_item_data, $product_id ) {
	
		/* below statement make sure every add to cart action as unique line item */
		$suwp_session_product_key = strtoupper( md5( date('dmYHisu') ) ); // md5( microtime().rand() );
		$cart_item_data['unique_key'] = $suwp_session_product_key;
		$_SESSION['suwp_session_product_key'] = $suwp_session_product_key;
		
		// hidden or special fields
		if( isset( $_REQUEST['suwp-imei-values'] ) ) {
			// >>>>>>>>>> $cart_item_data[ 'suwp_imei_values' ] = $_REQUEST['suwp-imei-values']; // previous
			$imei_vals = $_REQUEST['suwp-imei-values'];
			$imeis1 = trim($imei_vals);
			$imeis = str_replace(' ','',$imeis1);
			$cart_item_data[ 'suwp_imei_values' ] = $imeis;

			$text_area = explode( "\n", $imeis);
			foreach( $text_area as $imei_val ) {
				$actual_length = (int)strlen($imei_val);
				if ( $this->suwp_is_digits($imei_val) == 1 && $actual_length == 15 ) {
					if ( $this->suwp_check_imei($imei_val) ) {
						// standard IMEI
						$cart_item_data[ 'suwp_imei_values_label' ] = get_option('suwp_imei_label');
					} else {
						// serial number (non-IMEI)
						$cart_item_data[ 'suwp_imei_values_label' ] = get_option('suwp_sn_label');
					}
				} else {
					// serial number (non-IMEI)
					$cart_item_data[ 'suwp_imei_values_label' ] = get_option('suwp_sn_label');
				}
			}

		}
		if( isset( $_REQUEST['_suwp-qty-sent'] ) ) {
			$cart_item_data[ '_suwp_qty_sent' ] = $_REQUEST['_suwp-qty-sent'];
		}
		if( isset( $_REQUEST['_suwp-qty-done'] ) ) {
			$cart_item_data[ '_suwp_qty_done' ] = $_REQUEST['_suwp-qty-done'];
		}
		if( isset( $_REQUEST['_suwp-model-name'] ) ) {
			$cart_item_data[ 'suwp_model_name' ] = $_REQUEST['_suwp-model-name'];
		}
		if( isset( $_REQUEST['_suwp-network-name'] ) ) {
			$cart_item_data[ 'suwp_network_name' ] = $_REQUEST['_suwp-network-name'];
		}
		if( isset( $_REQUEST['_suwp-mep-name'] ) ) {
			$cart_item_data[ 'suwp_mep_name' ] = $_REQUEST['_suwp-mep-name'];
		}

		if( isset( $_REQUEST['suwp-api1-name'] ) ) {
			$cart_item_data[ 'suwp_api1_name' ] = $_REQUEST['suwp-api1-name'];
		}
		if( isset( $_REQUEST['suwp-api2-name'] ) ) {
			$cart_item_data[ 'suwp_api2_name' ] = $_REQUEST['suwp-api2-name'];
		}
		if( isset( $_REQUEST['suwp-api3-name'] ) ) {
			$cart_item_data[ 'suwp_api3_name' ] = $_REQUEST['suwp-api3-name'];
		}
		if( isset( $_REQUEST['suwp-api4-name'] ) ) {
			$cart_item_data[ 'suwp_api4_name' ] = $_REQUEST['suwp-api4-name'];
		}
		if( isset( $_REQUEST['suwp-country-id'] ) ) {
			$cart_item_data[ 'suwp_country_id' ] = $_REQUEST['suwp-country-id'];
		}
		if( isset( $_REQUEST['suwp-network-id'] ) ) {
			$cart_item_data[ 'suwp_network_id' ] = $_REQUEST['suwp-network-id'];
		}
		if( isset( $_REQUEST['suwp-brand-id'] ) ) {
			$cart_item_data[ 'suwp_brand_id' ] = $_REQUEST['suwp-brand-id'];
		}
		if( isset( $_REQUEST['suwp-model-id'] ) ) {
			$cart_item_data[ 'suwp_model_id' ] = $_REQUEST['suwp-model-id'];
		}
		if( isset( $_REQUEST['suwp-mep-id'] ) ) {
			$cart_item_data[ 'suwp_mep_id' ] = $_REQUEST['suwp-mep-id'];
		}
		if( isset( $_REQUEST['suwp-kbh'] ) ) {
			$cart_item_data[ 'suwp_kbh' ] = $_REQUEST['suwp-kbh'];
		}
		if( isset( $_REQUEST['suwp-activation-number'] ) ) {
			$cart_item_data[ 'suwp_activation_number' ] = $_REQUEST['suwp-activation-number'];
		}
		if( isset( $_REQUEST['suwp-email-response'] ) ) {
			$cart_item_data[ 'suwp_email_response' ] = $_REQUEST['suwp-email-response'];
		}
		if( isset( $_REQUEST['suwp-email-confirm'] ) ) {
			$cart_item_data[ 'suwp_email_confirm' ] = $_REQUEST['suwp-email-confirm'];
		}
		if( isset( $_REQUEST['suwp-use-payment-email'] ) ) {
			$cart_item_data[ 'suwp_use_payment_email'] = $_REQUEST['suwp-use-payment-email'];
		}
		if( isset( $_REQUEST['suwp-notes'] ) ) {
			$cart_item_data[ 'suwp_notes' ] = $_REQUEST['suwp-notes'];
		}
		
		// error_log( 'LETS TAKE A LOOK AT THE CART ITEM DATA :' . print_r($cart_item_data,true) );

		return $cart_item_data;
	}
	
	// x.x Front End Custom Fields: Woocommerce
	// This code will render the custom data in the cart and checkout page: WEB BROWSER ONLY
	public function suwp_render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
		$custom_items = array();
	
		/* Woo 2.4.2 updates */
		if( !empty( $cart_data ) ) {
			$custom_items = $cart_data;
		}

		if( isset( $cart_item['suwp_imei_values'] ) && $cart_item['suwp_imei_values'] != '' ) {
			
			$suwp_imei_values_label = get_option('suwp_imei_label'); // default value: IMEI
			
			if( isset( $cart_item['suwp_imei_values_label'] ) && $cart_item['suwp_imei_values_label'] != '' ) {
				$suwp_imei_values_label = $cart_item['suwp_imei_values_label'];
			}

			$custom_items[] = array( "name" => $suwp_imei_values_label, "value" => $cart_item['suwp_imei_values'] );
		}
		if( isset( $cart_item['suwp_mep_name'] ) && $cart_item['suwp_mep_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_mep_label'), "value" => $cart_item['suwp_mep_name'] );
		}
		if( isset( $cart_item['suwp_country_id'] ) && $cart_item['suwp_country_id'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_country_label'), "value" => $cart_item['suwp_country_id'] );
		}
		if( isset( $cart_item['suwp_network_name'] ) && $cart_item['suwp_network_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_network_label'), "value" => $cart_item['suwp_network_name'] );
		}
		if( isset( $cart_item['suwp_brand_id'] ) && $cart_item['suwp_brand_id'] != '') {
			$custom_items[] = array( "name" => get_option('suwp_brand_label'), "value" => $cart_item['suwp_brand_id'] );
		}
		if( isset( $cart_item['suwp_model_name'] ) && $cart_item['suwp_model_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_model_label'), "value" => $cart_item['suwp_model_name'] );
		}
		if( isset( $cart_item['suwp_api1_name'] ) && $cart_item['suwp_api1_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_custom_api1_label'), "value" => $cart_item['suwp_api1_name'] );
		}
		if( isset( $cart_item['suwp_api2_name'] ) && $cart_item['suwp_api2_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_custom_api2_label'), "value" => $cart_item['suwp_api2_name'] );
		}
		if( isset( $cart_item['suwp_api3_name'] ) && $cart_item['suwp_api3_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_custom_api3_label'), "value" => $cart_item['suwp_api3_name'] );
		}
		if( isset( $cart_item['suwp_api4_name'] ) && $cart_item['suwp_api4_name'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_custom_api4_label'), "value" => $cart_item['suwp_api4_name'] );
		}
		if( isset( $cart_item['suwp_email_response'] ) && $cart_item['suwp_email_response'] != '' ) {
			$custom_items[] = array( "name" => get_option('suwp_emailresponse_label'), "value" => $cart_item['suwp_email_response'] );
		}
		
		return $custom_items;
	}
	
	// x.x limit the quantity increase/decrease of the suwp_service in the Cart only
	public function suwp_quantity_input_args( $args, $product) {
		
		// print_r($args);
		// echo $args['input_name'];
		$cart_item_id = $args['input_name']; // format: cart[73338a514fd2b6b75ec8eb03d3e78155][qty]
		
		$serial_length = get_post_meta( $product->get_id(), '_suwp_serial_length', true ); // _suwp_service_notes ; _suwp_serial_length
		
		$slugs = array();
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		
		$input_value = $args['input_value'];
		
		if (is_array($terms)) {
		  foreach ( $terms as $term ) {
			$slugs[] = $term->slug;
		  }
		}
		
		// don't allow changing suwp_service because qty is based on the number of IMEI
		// used to display the total number (echo $input_value), not any more since v1.9.3
		
		if (in_array('suwp_service', $slugs, TRUE)){
			
			$args['max_value'] = $input_value;
			$args['min_value'] = $input_value;
			$args['step'] = 1;
			  
			// since v1.9.3, commented out to remove the '1' that was being displayed next to 'Add to cart'
			// echo $input_value;
		}
		
		return $args;
	}
		
	/**
	* Access to the cart page
	*
	* https://docs.woocommerce.com/document/conditional-tags/
	**/				
	public function suwp_access_cart_page_jscript() {
		
		global $woocommerce;
	 
		if ( $woocommerce ) {
			
			if ( is_cart() ) :
				?> 
					<script type="text/javascript">
						
						jQuery( document ).ready( function($){
							$('.woocommerce-cart-form__cart-item cart_item').each(function(){
								var isSUWP = $('.woocommerce-cart-form dd.variation-IMEI', this);
								var parentSelector = $('input-text.qty.text',this);
								if(parentSelector.length && isSUWP.length) {
									// just replace the input with a text representation of the quantity
									parentSelector.replaceWith(function(){
										return '<span class='+this.className+'>'+this.value+'</span>';
									});
								}
							});
							
						});
						
					</script>
				<?php
			endif;
		
		} // if ( $woocommerce )
	}
	
	// remove the add to cart from suwp_service on the shop page ONLY
	public function suwp_remove_add_to_cart_buttons_shop() {
		
		global $product; 
		$slugs = array();
		
		$terms = get_the_terms( $product->get_id(), 'product_cat' ); // $product->id
		
		if (is_array($terms)){
		  foreach ( $terms as $term ) {
			$slugs[] = $term->slug; // $term->$vals['term']
		  }
		}
		
		if (in_array('suwp_service', $slugs, TRUE)) {
	
			if( is_product_category() || is_shop()) { 
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
			}
			
		}
	}
	
	// remove the add to cart from suwp_service on the single product page
	public function suwp_remove_add_to_cart_buttons_product() {
		
		global $product;
		$slugs = array();
		
		$terms = get_the_terms( $product->get_id(), 'product_cat' ); // $product->id
		
		if (is_array($terms)){
		  foreach ( $terms as $term ) {
			$slugs[] = $term->slug; // $term->$vals['term']
		  }
		}
		
		if (in_array('suwp_service', $slugs, TRUE)) {
			
			$is_online = get_post_meta( $product->get_id(), '_suwp_online_status', true  ); // $product->id
			
			//Remove Add to Cart button from product description of product that is 'offline'
			if ( $is_online !== 'yes' ){
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
			
		}
		
	}
	
	public function suwp_view_order_and_thankyou_page( $order_id ){
		
		global $wpdb;

		$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
		
		$order = wc_get_order( $order_id );
		// $order_items = $order->get_items(); // directly obtain oder items
		
		$post_title_txt = '';
		$post_title = array();
		// The loop to get the order items which are WC_Order_Item_Product objects since WC 3+
		foreach( $order->get_items() as $item_id => $item_product ) {
			
			$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
			// $post_title_txt = $item_product['name'];
			
			// resetting here becuase possibly looping multiple times due to multi-value IMEI within order
			$api_reply = array( 
				'RESULT' => 'ERROR',
				'DESCRIPTION' => 'Results Pending',
			);
			
			$api_code = '';
			$device_info = array();
			$is_remote = false;
			$order_status = '';
			$imei = '';
			$t = 0;
			
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
				$is_remote = true;
				
				//Get the product ID
				$product_id = $item_product->get_product_id();
				
				// directly get the WC_Product object
				// $item_product->get_product();
				
				$api_provider = get_post_meta( $product_id, '_suwp_api_provider', true );
				
				if( is_numeric( $api_provider ) && $api_provider > 000 ) {
					
					// since v1.8.6 skip standard WooCommerce products and manually processed unlocking
					$apidetails = $plugin_admin->suwp_dhru_get_provider_array( $api_provider );
					
					// get the api details
					$suwp_dhru_url = $apidetails['suwp_dhru_url'];
					$suwp_dhru_username = $apidetails['suwp_dhru_username'];
					$suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
					$comment_contents = $wpdb->get_results( $wpdb->prepare( "SELECT comment_post_ID, comment_content FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND comment_author_IP=%s ORDER BY comment_post_ID ASC", $order_id, $suwp_dhru_api_key ) );
					
					$device_info_part = '';

					foreach( $comment_contents as $key => $loop_content ):
						
						$json_reply = NULL;
						$post_device_info =  '';
						$comment_values = explode( "-php-", trim($loop_content->comment_content) );
						
						$suwp_dhru_imei = $comment_values[0];
						$current_order_item_id = intval( $comment_values[1] );

						if ( isset($comment_values[2]) ) {
							// JSON_FORCE_OBJECT
							// $api_reply = unserialize( $comment_values[2] );
							$json_reply = json_decode($comment_values[2], true);
						}

						// since v1.9.3, for legacy order displaying
						if ( is_null($json_reply) || empty($json_reply)) {
							if ( isset($comment_values[2]) ) {
								$api_reply = unserialize( $comment_values[2] );
							}
						} else {
							// u00a7 = §
							$api_reply = str_replace("u00a7","<br />",$json_reply);
						}
						
						$current_product_id = wc_get_order_item_meta( $current_order_item_id, '_product_id', true );
						$process_time = get_post_meta( $current_product_id, '_suwp_process_time', true );
						
						$brand_check = wc_get_order_item_meta( $current_order_item_id, 'suwp_brand_id', true );
						$model_check = wc_get_order_item_meta( $current_order_item_id, 'suwp_model_name', true );
						$country_check = wc_get_order_item_meta( $current_order_item_id, 'suwp_country_id', true );
						$network_check = wc_get_order_item_meta( $current_order_item_id, 'suwp_network_name', true );
						$mep_check = wc_get_order_item_meta( $current_order_item_id, 'suwp_mep_name', true );
						
						$post_title_txt = '';
						$order_items = $wpdb->get_results( $wpdb->prepare( "SELECT order_item_name FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_item_id=%d", $current_order_item_id ) );
						
						if( !empty( $order_items ) ) {
							$post_title_txt = $order_items[0]->order_item_name;
						}
						
						if( !empty($brand_check) ) {
							$post_device_info = $post_device_info . get_option('suwp_brand_label') . ': ' . $brand_check . '<br>';
						}
						if( !empty($model_check) ) {
							$post_device_info = $post_device_info . get_option('suwp_model_label') . ': ' . $model_check . '<br>';
						}
						if( !empty($country_check) ) {
							$post_device_info = $post_device_info . get_option('suwp_country_label') . ': ' . $country_check . '<br>';	
						}
						if( !empty($network_check) ) {
							$post_device_info = $post_device_info . get_option('suwp_network_label') . ': ' . $network_check . '<br>';
						}
						if( !empty($mep_check) ) {
							$post_device_info = $post_device_info . get_option('suwp_mep_label') . ': ' . $mep_check . '<br>';
						}
						
						$api_results = '';
						// a reply had been obtained
						if(isset( $api_reply['RESULTS'] ) ) {
							$api_results = $api_reply['RESULTS'];
						}
						// most likely order received (pending)
						if(isset( $api_reply['RESULT'] ) ) {
							$api_results = $api_reply['RESULT'];
						}
						
						switch( $api_results ) {
							
							case 'ERROR':
								
								$description = '';
								$message = '';

								if(isset( $api_reply['DESCRIPTION'] ) ) {
									$description = $api_reply['DESCRIPTION'] . '<br>';
								}
										
								if(isset( $api_reply['MESSAGE'] ) ) {
									$message = $api_reply['MESSAGE'] . '<br>';
								}
								$device_info_part = $message . $description . '<br>';
								break;
							case 'SUCCESS':

								$api_code = '';
								$reply_status = 0;

								if(isset( $api_reply['CODE'] ) ) {
									$api_code = get_option('suwp_code_label') . ': '. '<strong>' . $api_reply['CODE'] . '</strong>' . '<br>';
								}
								if(isset( $api_reply['STATUS'] ) ) {
									$reply_status = (int)$api_reply['STATUS'];
								}
								
								$imei = $api_reply['IMEI'];

								$suwp_imei_values_label = get_option('suwp_imei_label'); // default value: IMEI
			
								$actual_length = (int)strlen($imei);
								if ( $this->suwp_is_digits($imei) == 1 && $actual_length == 15 ) {
									if ( $this->suwp_check_imei($imei) ) {
										// standard IMEI
										$suwp_imei_values_label = get_option('suwp_imei_label');
									} else {
										// serial number (non-IMEI)
										$suwp_imei_values_label = get_option('suwp_sn_label');
									}
								} else {
									// serial number (non-IMEI)
									$suwp_imei_values_label = get_option('suwp_sn_label');
								}

								$device_info_part = get_option('suwp_service_label') . ': ' . $post_title_txt . '<br>' . $suwp_imei_values_label . ': ' . $imei . '<br>' . get_option('suwp_deliverytime_label') . ': ' . $process_time . '<br>' . $api_code . 'Order ID: ' . $order_id  . '<br>';
								
								$order_status = '';

								switch( $reply_status ) {
									case 3:
										// unavailable
										// reasons: Not found, reported stolen/lost, etc.
										$order_status = 'Code Unavailable';
										break;
									case 4:
										// available
										$order_status = 'Code Available';
										break;
									default:
									// new or pending
									$order_status = 'Code Pending';
								}
								break;
						}
						
						$device_info[] = $device_info_part . $post_device_info . $order_status . '<br>';
						
						$post_title[$t] = $post_title_txt;
						$t++;
						
					endforeach; // foreach( $comment_contents as $key => $loop_content )
					
				}
				
			} // if ( $suwp_has_term )
			
		} // foreach( $order->get_items() as $item_id => $item_product )
		
			$i = 0;
			if( $is_remote && !empty( $device_info ) ) :
				
				foreach( $device_info as $key => $device_content ):
				// foreach( $device_info as $device_content ):
				
					if( $i < 1 ) : ?>
						<h2>Code Details</h2>
					<?php
					
					endif;
					
					?>
					
					<table class="woocommerce-table shop_table suwp_code_details">
						<tbody>
							<tr>
								<th><?php echo $post_title[$i] ?></th>
							</tr>
							<tr>
								<td><?php echo $device_content ?></td>
							</tr>
						</tbody>
					</table>
					
				<?php
				
				$i++;
			endforeach;
		
		endif;
		
		?>
		
	<?php }
		
	//////////////////////////////////
	// legacy detection for proper order details display
	public function suwp_woocommerce_version_check( $version ) {
		global $woocommerce;
		if( $woocommerce && version_compare( WC()->version, $version, ">=" ) ) {
			return true;
		}
		return false;
	}
	
	public function suwp_check_woo() {
		
		// x.x Front End Custom Fields: Woocommerce
		// This code will add the custom field with order meta:
		// order details -> browser, order details -> emails, back end -> meta fields
		function suwp_order_item_meta_update_3_0( $item, $cart_item_key, $values, $order ) {
			
			// ... nested, cannot use: $this
			$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
			if(isset($values['suwp_imei_values']) ){
				$imei_values = trim($values['suwp_imei_values']);
				$text_area = explode( "\n", $imei_values);
				$imeis_new = '';
				$suwp_imei_values_label = get_option('suwp_imei_label'); // default value: IMEI
				// clean out blank entries
				foreach( $text_area as $imei_val ) {
					if ( trim($imei_val) != '') {
						$imeis_new .= $imei_val . chr(10);
						$actual_length = (int)strlen($imei_val);

						if ( $plugin_public->suwp_is_digits($imei_val) == 1 && $actual_length == 15 ) {
							if ( $plugin_public->suwp_check_imei($imei_val) ) {
								// standard IMEI
								$suwp_imei_values_label = get_option('suwp_imei_label');
							} else {
								// serial number (non-IMEI)
								$suwp_imei_values_label = get_option('suwp_sn_label');
							}
						} else {
							// serial number (non-IMEI)
							$suwp_imei_values_label = get_option('suwp_sn_label');
						}

					}
				}
				$values['suwp_imei_values'] = $imeis_new;
				// REPLACED: 'suwp_imei_values' with $suwp_imei_values_label
				// this will be the display key ... later on
				$item->add_meta_data('suwp_imei_values', $values['suwp_imei_values'], true);
				$item->add_meta_data('suwp_imei_values_label', $suwp_imei_values_label, true);
				update_option( 'suwp_imei_values_label', $suwp_imei_values_label );
			}
			
			if( isset( $values['suwp_api1_name'] ) ) {
				$item->add_meta_data('suwp_api1_name', $values['suwp_api1_name'], true);
				$item->add_meta_data('suwp_custom_api1_label', get_option('suwp_custom_api1_label'), true);
			}
			if( isset( $values['suwp_api2_name'] ) ) {
				$item->add_meta_data('suwp_api2_name', $values['suwp_api2_name'], true);
				$item->add_meta_data('suwp_custom_api2_label', get_option('suwp_custom_api2_label'), true);
			}
			if( isset( $values['suwp_api3_name'] ) ) {
				$item->add_meta_data('suwp_api3_name', $values['suwp_api3_name'], true);
				$item->add_meta_data('suwp_custom_api3_label', get_option('suwp_custom_api3_label'), true);
			}
			if( isset( $values['suwp_api4_name'] ) ) {
				$item->add_meta_data('suwp_api4_name', $values['suwp_api4_name'], true);
				$item->add_meta_data('suwp_custom_api4_label', get_option('suwp_custom_api4_label'), true);
			}
			if( isset( $values['suwp_country_id'] ) ) {
				$item->add_meta_data('suwp_country_id', $values['suwp_country_id'], true);
				$item->add_meta_data('suwp_country_label', get_option('suwp_country_label'));
			}
			if( isset( $values['suwp_network_name'] ) ) {
				$item->add_meta_data('suwp_network_name', $values['suwp_network_name'], true);
				$item->add_meta_data('suwp_network_label', get_option('suwp_network_label'));
			}
			if( isset( $values['suwp_network_id'] ) ) {
				$item->add_meta_data('suwp_network_id', $values['suwp_network_id'], true);
			}
			if( isset( $values['suwp_brand_id'] ) ) {
				$item->add_meta_data('suwp_brand_id', $values['suwp_brand_id'], true);
				$item->add_meta_data('suwp_brand_label', get_option('suwp_brand_label'));
			}
			if( isset( $values['suwp_model_id'] ) ) {
				$item->add_meta_data('suwp_model_id', $values['suwp_model_id'], true);
			}
			if( isset( $values['suwp_model_name'] ) ) {
				$item->add_meta_data('suwp_model_name', $values['suwp_model_name'], true);
				$item->add_meta_data('suwp_model_label', get_option('suwp_model_label'));
			}
			if( isset( $values['suwp_mep_id'] ) ) {
				$item->add_meta_data('suwp_mep_id', $values['suwp_mep_id'], true);
			}
			if( isset( $values['suwp_mep_name'] ) ) {
				$item->add_meta_data('suwp_mep_name', $values['suwp_mep_name'], true);
				$item->add_meta_data('suwp_mep_label', get_option('suwp_mep_label'));
			}
			if( isset( $values['suwp_kbh'] ) ) {
				$item->add_meta_data('suwp_kbh', $values['suwp_kbh'], true);
				$item->add_meta_data('suwp_kbh_label', get_option('suwp_kbh_label'));
			}
			if( isset( $values['suwp_activation_number'] ) ) {
				$item->add_meta_data('suwp_activation_number', $values['suwp_activation_number'], true);
				$item->add_meta_data('suwp_activation_label', get_option('suwp_activation_label'));
			}
			if( isset( $values['suwp_email_response'] ) ) {
				$item->add_meta_data('suwp_email_response', $values['suwp_email_response'], true);
				$item->add_meta_data('suwp_emailresponse_label', get_option('suwp_emailresponse_label'));
			}
			if( isset( $values['suwp_email_confirm'] ) ) {
				$item->add_meta_data('suwp_email_confirm', $values['suwp_email_confirm'], true);
				$item->add_meta_data('suwp_emailconfirm_label', get_option('suwp_emailconfirm_label'));
			}
			if( isset( $values['suwp_use_payment_email'] ) ) {
				$item->add_meta_data('suwp_use_payment_email', $values['suwp_use_payment_email'], true);
				// NOT YET: $item->add_meta_data('suwp_emailpayment_label', get_option('suwp_emailpayment_label'));
			}
			if( isset( $values['suwp_notes'] ) ) {
				$item->add_meta_data('suwp_notes', $values['suwp_notes'], true);
				// NOT YET: $item->add_meta_data('suwp_notes_label', get_option('suwp_notes_label'));
			}
			if( isset( $values['_suwp_qty_sent'] ) ) {
				$item->add_meta_data('_suwp_qty_sent', $values['_suwp_qty_sent'], true);
			}
			if( isset( $values['_suwp_qty_done'] ) ) {
				$item->add_meta_data('_suwp_qty_done', $values['_suwp_qty_done'], true);
			}
		}
		
		// https://woocommerce.wp-a2z.org/oik_api/wc_display_item_meta/
		function suwp_modify_order_items_meta_display_3_0( $output, $order ) {
			
			// if ever conflicting w/another plugin, extract to act ...
			// var_dump($output);
			// echo '<pre>'; print_r($order); echo '</pre>';
			
			$meta_list = array();
			$html = '';
			$formatted_meta = $order->get_formatted_meta_data();
			// suwp_imei_label
			// suwp_sn_label
			// the label is determined by the formatted values
			// reference the previously stored format ...
			// RESET the format flag when completed
			$suwp_imei_values_label = get_option('suwp_imei_label');

			// REPLACED: 'suwp_imei_values' => get_option('suwp_imei_label'),
			// display key now being set earlier when extracting IMEI/SN
			$replace = array(
				'suwp_imei_values' => get_option('suwp_imei_values_label'),
				'suwp_email_response' => get_option('suwp_emailresponse_label'),
				'suwp_email_confirm' => get_option('suwp_emailconfirm_label'),
				'suwp_brand_id' => get_option('suwp_brand_label'),
				'suwp_model_id' => get_option('suwp_model_label'),
				'suwp_model_name' => get_option('suwp_model_label'),
				'suwp_country_id' => get_option('suwp_country_label'),
				'suwp_network_name' => get_option('suwp_network_label'),
				'suwp_network_id' => get_option('suwp_network_label'),
				'suwp_mep_id' => get_option('suwp_mep_label'),
				'suwp_mep_name' => get_option('suwp_mep_label'),
				'suwp_api1_name' => get_option('suwp_custom_api1_label'),
				'suwp_api2_name' => get_option('suwp_custom_api2_label'),
				'suwp_api3_name' => get_option('suwp_custom_api3_label'),
				'suwp_api4_name' => get_option('suwp_custom_api4_label'),
				
			);

			$args = wp_parse_args( $output, array(
				  'before'    => '<ul class="wc-item-meta"><li>',
				  'after'    => '</li></ul>',
				  'separator'  => '</li><li>',
				  'echo'    => true,
				  'autop'    => false,
				) );
			
			foreach ( $formatted_meta  as $meta_id => $meta ) {
				// error_log( ' META DISPLY KEY >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . $meta->display_key );
				$value = $args['autop'] ? wp_kses_post( wpautop( make_clickable( $meta->display_value ) ) ) : wp_kses_post( make_clickable( $meta->display_value ) );
				// if it's not this, it will be hidden
				// REMOVED: $meta->display_key == "suwp_imei_values" || 
				if( $meta->display_key == "suwp_imei_values" || $meta->display_key == "suwp_email_response" || $meta->display_key == "suwp_brand_id" || $meta->display_key == "suwp_model_name" || $meta->display_key == "suwp_country_id" || $meta->display_key == "suwp_network_name" || $meta->display_key == "suwp_mep_name" || $meta->display_key == "suwp_api1_name" || $meta->display_key == "suwp_api2_name" || $meta->display_key == "suwp_api3_name" || $meta->display_key == "suwp_api4_name") {
					$meta_list[] = '<strong class="wc-item-meta-label">' . wp_kses_post( $meta->display_key ) . ':</strong> ' . $value;
				}
				
			}

			if ( $meta_list ) {
				$meta_list = suwp_string_replace_assoc( $replace, $meta_list );
				$html = $args['before'] . implode( $args['separator'], $meta_list ) . $args['after'];
			}
			
			if ( $args['echo'] ) {
			  echo $html;
			} else {
			  return $html;
			}
			
		}
		
		// deprecated
		function suwp_order_item_meta_update_2_7( $item_id, $values, $cart_item_key ) {
		
			if( isset( $values['suwp_imei_values'] ) ) {
				$imei_values = trim($values['suwp_imei_values']);
				$text_area = explode( "\n", $imei_values);
				$imeis_new = '';
				$suwp_imei_values_label = get_option('suwp_imei_label'); // default value: IMEI

				// ... nested, cannot use: $this
				$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
				

				// clean out blank entries
				foreach( $text_area as $imei_val ) {
					if ( trim($imei_val) != '') {
						$imeis_new .= $imei_val . chr(10);
						$actual_length = (int)strlen($imei_val);

						if ( $plugin_public->suwp_is_digits($imei_val) == 1 && $actual_length == 15 ) {
							if ( $plugin_public->suwp_check_imei($imei_val) ) {
								// standard IMEI
								$suwp_imei_values_label = get_option('suwp_imei_label');
							} else {
								// serial number (non-IMEI)
								$suwp_imei_values_label = get_option('suwp_sn_label');
							}
						} else {
							// serial number (non-IMEI)
							$suwp_imei_values_label = get_option('suwp_sn_label');
						}

					}
				}
				$values['suwp_imei_values'] = $imeis_new;
				// REPLACED: 'suwp_imei_values' with $suwp_imei_values_label
				// this will be the display key ... later on
				wc_add_order_item_meta( $item_id, 'suwp_imei_values', $values['suwp_imei_values'] );
				wc_add_order_item_meta( $item_id, 'suwp_imei_values_label', $suwp_imei_values_label );
				update_option( 'suwp_imei_values_label', $suwp_imei_values_label );
			}

			if( isset( $values['suwp_api1_name'] ) ) {
				wc_add_order_item_meta( $item_id, 'suwp_api1_name', $values['suwp_api1_name'] );
				wc_add_order_item_meta( $item_id, 'suwp_custom_api1_label', get_option('suwp_custom_api1_label') );
			}
			if( isset( $values['suwp_api2_name'] ) ) {
				wc_add_order_item_meta( $item_id, 'suwp_api2_name', $values['suwp_api2_name'] );
				wc_add_order_item_meta( $item_id, 'suwp_custom_api2_label', get_option('suwp_custom_api2_label') );
			}
			if( isset( $values['suwp_api3_name'] ) ) {
				wc_add_order_item_meta( $item_id, 'suwp_api3_name', $values['suwp_api3_name'] );
				wc_add_order_item_meta( $item_id, 'suwp_custom_api3_label', get_option('suwp_custom_api3_label') );
			}
			if( isset( $values['suwp_api4_name'] ) ) {
				wc_add_order_item_meta( $item_id, 'suwp_api4_name', $values['suwp_api4_name'] );
				wc_add_order_item_meta( $item_id, 'suwp_custom_api4_label', get_option('suwp_custom_api4_label') );
			}
			if( isset( $values['suwp_country_id'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_country_id", $values['suwp_country_id'] );
			}
			if( isset( $values['suwp_network_id'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_network_id", $values['suwp_network_id'] );
			}
			if( isset( $values['suwp_network_name'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_network_name", $values['suwp_network_name'] );
			}
			if( isset( $values['suwp_brand_id'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_brand_id", $values['suwp_brand_id'] );
			}
			if( isset( $values['suwp_model_id'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_model_id", $values['suwp_model_id'] );
			}
			if( isset( $values['suwp_model_name'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_model_name", $values['suwp_model_name'] );
			}
			if( isset( $values['suwp_mep_id'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_mep_id", $values['suwp_mep_id'] );
			}
			if( isset( $values['suwp_mep_name'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_mep_name", $values['suwp_mep_name'] );
			}
			if( isset( $values['suwp_kbh'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_kbh", $values['suwp_kbh'] );
			}
			if( isset( $values['suwp_activation_number'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_activation_number", $values['suwp_activation_number'] );
			}
			if( isset( $values['suwp_email_response'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_email_response", $values['suwp_email_response'] );
			}
			if( isset( $values['suwp_email_confirm'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_email_confirm", $values['suwp_email_confirm'] );
			}
			if( isset( $values['suwp_notes'] ) ) {
				wc_add_order_item_meta( $item_id, "suwp_notes", $values['suwp_notes'] );
			}
			if( isset( $values['_suwp_qty_sent'] ) ) {
				wc_add_order_item_meta( $item_id, "_suwp_qty_sent", $values['_suwp_qty_sent'] );
			}
			if( isset( $values['_suwp_qty_done'] ) ) {
				wc_add_order_item_meta( $item_id, "_suwp_qty_done", $values['_suwp_qty_done'] );
			}
			
		}
		
		// deprecated
		function suwp_modify_order_items_meta_display_2_7( $output, $order ) {
			
			// if ever conflicting w/another plugin, extract to act ...
			// var_dump($output);
			// echo '<pre>'; print_r($order); echo '</pre>';
			
			$meta_list = array();
			$html = '';
			$formatted_meta = $order->get_formatted( '_' );

			// REPLACED: 'suwp_imei_values' => get_option('suwp_imei_label'),
			// display key now being set earlier when extracting IMEI/SN
			$replace = array(
				'suwp_imei_values' => get_option('suwp_imei_values_label'),
				'suwp_email_response' => get_option('suwp_emailresponse_label'),
				'suwp_email_confirm' => get_option('suwp_emailconfirm_label'),
				'suwp_brand_id' => get_option('suwp_brand_label'),
				'suwp_model_id' => get_option('suwp_model_label'),
				'suwp_model_name' => get_option('suwp_model_label'),
				'suwp_country_id' => get_option('suwp_country_label'),
				'suwp_network_name' => get_option('suwp_network_label'),
				'suwp_network_id' => get_option('suwp_network_label'),
				'suwp_mep_id' => get_option('suwp_mep_label'),
				'suwp_mep_name' => get_option('suwp_mep_label'),
				'suwp_api1_name' => get_option('suwp_custom_api1_label'),
				'suwp_api2_name' => get_option('suwp_custom_api2_label'),
				'suwp_api3_name' => get_option('suwp_custom_api3_label'),
				'suwp_api4_name' => get_option('suwp_custom_api4_label'),
			);

			$args = wp_parse_args( $output, array(
			  'before'    => '<ul class="wc-item-meta"><li>',
			  'after'    => '</li></ul>',
			  'separator'  => '</li><li>',
			  'echo'    => true,
			  'autop'    => false,
			) );
			
			foreach ( $formatted_meta as $meta ) {
				$value = $args['autop'] ? wp_kses_post( wpautop( make_clickable( $meta['label'] ) ) ) : wp_kses_post( make_clickable( $meta['value'] ) );
				// if it's not this, it will be hidden
				// REMOVED: $meta['key'] == "suwp_imei_values" || 
				if( $meta['key'] == "suwp_imei_values" || $meta['key'] == "suwp_email_response" || $meta['key'] == "suwp_brand_id" || $meta['key'] == "suwp_model_name" || $meta['key'] == "suwp_country_id" || $meta['key'] == "suwp_network_name" || $meta['key'] == "suwp_mep_name" || $meta['key'] == "suwp_api1_name"|| $meta['key'] == "suwp_api2_name"|| $meta['key'] == "suwp_api3_name"|| $meta['key'] == "suwp_api4_name") {
					$meta_list[] = '
										<dt class="variation-' . sanitize_html_class( sanitize_text_field( $meta['key'] ) ) . '">' . wp_kses_post( $meta['label'] ) . ':</dt>
										<dd class="variation-' . sanitize_html_class( sanitize_text_field( $meta['key'] ) ) . '">' . wp_kses_post( wpautop( make_clickable( $meta['value'] ) ) ) . '</dd>
									';
				}
				
			}
			
			if ( $meta_list ) {
				$meta_list = suwp_string_replace_assoc( $replace, $meta_list );
				$html = $args['before'] . implode( $args['separator'], $meta_list ) . $args['after'];
			}
			
			if ( $args['echo'] ) {
			  echo $html;
			} else {
			  return $html;
			}
			
		}

		if( $this->suwp_woocommerce_version_check('2.8') ) {  
			// Use new, updated functions
			add_action('woocommerce_checkout_create_order_line_item','suwp_order_item_meta_update_3_0', 1, 4); // 50, 2
			add_filter( 'woocommerce_display_item_meta', 'suwp_modify_order_items_meta_display_3_0', 99, 2 );
		
		} else {  
			// Use older, deprecated functions
			add_action( 'woocommerce_add_order_item_meta', 'suwp_order_item_meta_update_2_7', 1, 3 ); // 50, 2
			add_filter( 'woocommerce_order_items_meta_display', 'suwp_modify_order_items_meta_display_2_7', 99, 2 );
		}
		
	}
	
	// creates the suwp_service category
	public function suwp_insert_custom_category() {
		
		if(!term_exists('suwp_service')) {
			wp_insert_term(
				'Remote Service',
				'product_cat',
				array(
				  'description'	=> 'Mobile unlocking service',
				  'slug' 		=> 'suwp_service'
				)
			);
		}
	}
	
	// create the SU import post status
	public function suwp_insert_custom_post_status(){
		
		register_post_status( 'imported', array(
			'label'                     => _x( 'Imported', 'post' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Imported <span class="count">(%s)</span>', 'Imported <span class="count">(%s)</span>' ),
		) );
		
	}
	
	public function suwp_short_codes() {
		
		// example: [suwp_delivery_time product_id = 232]
		function suwp_get_delivery_time( $atts ) {
			return get_post_meta( $atts['product_id'], '_suwp_process_time', true );
		}
		add_shortcode('suwp_delivery_time', 'suwp_get_delivery_time');	
	}
	
	public function suwp_is_connected() {
		
		$is_conn = false;
		$connected = @fsockopen("www.google.com", 80, $errno, $errstr, 30); //website, port, errno, errstring, timeout; (try 80 or 443)
		if ($connected){
			$is_conn = true; //action when connected
			fclose($connected);
		}else{
			$is_conn = false; //action in connection failure
		}
		return $is_conn;
		
	}

	/**
	* Returns an int, flagging for digits only, no decimals or characters.
	*
	* @param string $element
	*   A string representation of a numerical value.
	*/
	public function suwp_is_digits($element) {
	  return !preg_match("/[^0-9]/", $element);
	}
   
	/**
	 * Returns an bool, confirming the validity of a 15 digit IMEI.
	 *
	 * @param string $imei
	 *   A string representation of a 15 digit IMEI number.
	 */
	public function suwp_check_imei($imei) {
	  $dig = 0;
	  for ($i = 0; $i < 14; $i += 2) {
		$cdigit = $imei[$i + 1] << 1;
		$dig += $imei[$i] + (int) ($cdigit / 10) + ($cdigit % 10);
	  }
	  $dig = (10 - ($dig % 10)) % 10;
	  if ($dig == $imei[14]) {
		return TRUE;
	  }
	  else {
		return FALSE;
	  }
	}
	
	/**
	* Access to the single product page
	*
	* https://docs.woocommerce.com/document/conditional-tags/
	**/
	public function suwp_access_single_product_jscript() {
		
		global $woocommerce;
	 
		if ( $woocommerce ) {
			
			if ( is_product( ) ):
				if( has_term( 'suwp_service', 'product_cat' ) ) :
					?>
					<script>
						// code here
					</script>
					<?php
				endif;
			endif;
		   
		}
	}

	// retrieves a product based on a unique provider and api service combo
	public function suwp_exec_get_product_id( $apiproviderid, $apiserviceid ) {
		
		// since v1.9.3 the API provider may use a text value for the service API ID
		// if text value is available, use it
		$product_id = 0;
		$suwp_api_key = '_suwp_api_service_id';

		if ( is_string($apiserviceid) ) {
			$suwp_api_key = '_suwp_api_service_id_alt';
		}
		
		try {
			
			// check if product already exists
			$product_query = new WP_Query( 
				array(
					'post_type'	    =>	'product',
					// if already published or imported ..., don't import again. 'trash' is also an option 
					'post_status'   => array('publish', 'imported', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
					'posts_per_page'=> 1, // only expecting one result, so set to '1', get all = '-1'
					'meta_query'    => array(
						'relation'  => 'AND',
						array(
								'key' => '_suwp_api_provider',
								'value' => $apiproviderid,
								'compare' => '=',
						),
						array(
								'key' => $suwp_api_key,
								'value' => $apiserviceid,
								'compare' => '=',
						),
					),
				)
			);
			
			// IF the product exists...
			if( $product_query->have_posts() ):
			
				// get the product_id
				$product_query->the_post();
				
				$product_id = get_the_ID();
				
			endif;
		
		} catch( Exception $e ) {
			
			// a php error occurred
			error_log('----- ERROR - OBTAINING PRODUCT BASED ON PROVIDER POST ID AND API SERVICE ID COMBO ----- ');
			error_log(print_r($e,true));
		}
			
		// reset the Wordpress post object, avoids bleeding memory
		wp_reset_query();
		
		// will return the id if found in db, otherwise returns '0'
		return (int)$product_id;
		
	}
	
	/**
	* Receive an array and return a
	* json string value
	*/
	public function suwp_exec_return_json( $php_array ) {

		// encode result as json string
		$json_result = json_encode( $php_array );
		
		// return result
		die( $json_result ); // whatever process php was in - stop doing that
		
		// stop all other processing 
		exit;
	
	}
	
	// recursively reduces deep arrays to single-dimensional array
	// $preserve_keys: (0=>never, 1=>strings, 2=>always)
	public function suwp_exec_array_flatten($array, $preserve_keys = 1, &$newArray = Array()) {
	  foreach ($array as $key => $child) {
		if (is_array($child)) {
		  $newArray = $this->suwp_exec_array_flatten($child, $preserve_keys, $newArray);
		} elseif ($preserve_keys + is_string($key) > 1) {
		  $newArray[$key] = $child;
		} else {
		  $newArray[] = $child;
		}
	  }
	  return $newArray;
	}

	/**
	* Get the unique act field key
	* from the field name
	*/
	public function suwp_exec_get_acf_key( $field_name ) {
		
		$field_key = $field_name;
		
		switch( $field_name ) {
			
			case 'suwp_apitype':
				$field_key = 'field_58541mz4p0nlf';
				break;
			case 'suwp_activeflag':
				$field_key = 'field_5854d17bd5e5d';
				break;
			case 'suwp_sitename':
				$field_key = 'field_58557dfcd5e5e';
				break;
			case 'suwp_url':
				$field_key = 'field_58557fa7d5e5f';
				break;
			case 'suwp_username':
				$field_key = 'field_58558042d5e60';
				break;
			case 'suwp_apikey':
				$field_key = 'field_585580a8d5e61';
				break;
			case 'suwp_apinotes':
				$field_key = 'field_58558102d5e62';
				break;
			
		}
		
		return $field_key;
		
	}
	
	// returns an array of service data
	function suwp_exec_get_service_data( $service_id ) {

		// since v1.9.3
		$api_service_id = NULL;
		$api_service_id_alt = NULL;
		$provider_name = '';
		$provider_title = '';

		// setup service_data
		$service_data = array();
		
		// get service object
		$service = get_post( $service_id );
		
		// IF service object is valid (checking if this is the correct post type)
		if( isset($service->post_type) && $service->post_type == 'product' ):
		
			$title = $service->post_title;
			$api_service_id = get_post_meta( $service_id, '_suwp_api_service_id', true );
			$api_service_id_alt = get_post_meta( $service_id, '_suwp_api_service_id_alt', true );
			
			// since v1.9.3, if service api id is string, use it
			if ( is_string($api_service_id_alt) && !is_null($api_service_id_alt) && !empty($api_service_id_alt) ) {
				$api_service_id = $api_service_id_alt;
			}

			$price = get_post_meta( $service_id, '_regular_price', true );
			$provider_ID = get_post_meta( $service_id, '_suwp_api_provider', true );
			$provider_name = get_post_meta( $provider_ID, 'suwp_sitename', true );
			$provider_title = get_post_field( 'post_title', $provider_ID );
			$process_time = get_post_meta( $service_id, '_suwp_process_time', true );
			$service_credit = get_post_meta( $service_id, '_suwp_service_credit', true );
			$online_status = get_post_meta( $service_id, '_suwp_online_status', true );
			
			// since v1.9.3
			if ($provider_ID === '000') {
				$provider_name = 'Stand-alone Unlock';
				$provider_title = 'Stand-alone Unlock';
			}
			if ($provider_ID === 'None') {
				$provider_name = 'NONE';
				$provider_title = 'NONE';
			}

			// build service_data for return
			$service_data = array(
				'title'=> $title,
				'api_service_id'=>$api_service_id,
				'price'=>$price,
				'process_time'=>$process_time,
				'service_credit'=>$service_credit,
				'online_status'=>$online_status,
				'provider_name'=>$provider_name,
				'provider_title'=>$provider_title,
				'provider_ID'=>$provider_ID
			);
			
		
		endif;
		
		// return service_data
		return $service_data;
		
	}
	
	/**
	* Return an array
	* of service_id's
	*/
	public function suwp_exec_get_provider_services( $provider_id = 0 ) {
		
		// setup return variable
		$services = false;
		
		// get provider object
		$provider = get_post( $provider_id );
		
		// take a peek at the object
		// var_dump($provider);
	
		if( $this->suwp_exec_validate_provider( $provider ) ):
		
			// query all services from post this provider only
			$services_query = new WP_Query( 
				array(
					'post_type' => 'product',
					'published' => true,
					'posts_per_page' => -1,
					'orderby'=>'post_date',
					'order'=>'DESC',
					'post_status'=>'publish',
					'meta_query' => array(
						array(
							'key' => '_suwp_api_provider', 
							'value' => ':"'.$provider->ID.'"', 
							'compare' => 'LIKE'
						)
					)
				)
			);
		
		elseif( $provider_id === 0 ):
		
			// query all services from all providers
			$services_query = new WP_Query( 
				array(
					'post_type' => 'product',
					'published' => true,
					'posts_per_page' => -1,
					'orderby'=>'post_date',
					'order'=>'DESC',
					'post_status'=>'publish',
					'tax_query' => array(
						array(
							'taxonomy' => 'product_cat',
							'field' => 'slug',
							'terms' => 'suwp_service'
						)
					)
				)
			);
		
		endif;
			
		// IF $services_query isset and query returns results
		if( isset($services_query) && $services_query->have_posts() ):
				
			// set services array
			$services = array();
			
			// loop over results
			while ($services_query->have_posts() ) : 
			
				// get the post object
				$services_query->the_post();
				
				$post_id = get_the_ID();
			
				// append result to services array
				array_push( $services, $post_id);
			
			endwhile;
		
		endif;
		
		// reset wp query/postdata
		wp_reset_query();
		wp_reset_postdata();
		
		// return result
		return $services;
	}
	
	/**
	* Return the amount of
	* services for this provider
	*/
	public function suwp_exec_get_provider_service_count( $provider_id = 0 ) {
		
		// setup return variable
		$count = 0;
		
		// get array of service ids
		$services = suwp_exec_get_provider_services( $provider_id );
		
		// IF array was returned
		if( $services !== false ):
		
			// update count
			$count = count($services);
		
		endif;
		
		// return result
		return $count;
		
	}
	
	/**
	* Validates whether the post object
	* exists and that it's a valid post_type
	*/
	public function suwp_exec_validate_provider( $provider_object ) {
		
		$provider_valid = false;
		
		if( isset($provider_object->post_type) && $provider_object->post_type == 'suwp_apisource' ):
		
			$provider_valid = true;
		
		endif;
		
		return $provider_valid;
		
	}
	
	/**
	* Creates unique id for general use,
	* currently being used for a support id
	*/
	public function suwp_exec_uuid_make() {
		
		$string = substr( strtoupper( md5( date('dmYHisu') ) ),0,24 );
	
		$string = substr($string, 0, 3 ) .'-'.
		substr($string, 3, 3) .'-'.
		substr($string, 6, 3) .'-'.
		substr($string, 9, 3) .'-'.
		substr($string, 12, 3) .'-'.
		substr($string, 15, 3) .'-'.
		substr($string, 18, 3) .'-'.
		substr($string, 21);
		
		return $string;
	
	}
	
	/**
	* Gets an array of plugin option data
	* (group and settings) so as to save it all in one place
	*/
	public function suwp_exec_get_options_settings() {
		
		// setup our return data
		$settings = array( 
			'group'=>'suwp_plugin_options',
			'settings'=>array(
				'suwp_author_info',
				'suwp_license_email',
				'suwp_license_key',
				'suwp_valid_until',
				'suwp_plugin_type',
				'suwp_author_value',
				'suwp_array_posts',
				'suwp_reference_posts',
				'suwp_retrieved_posts',
				'suwp_order_items',
				'suwp_manage_product_sync_run_id',
				'suwp_manage_acf_menu_enabled',
				'suwp_manage_response_email_display',
				'suwp_manage_cron_run_id',
				'suwp_manage_troubleshoot_run_id',
				'suwp_price_enabled_01',
				'suwp_price_adj_default',
				'suwp_price_adj_01',
				'suwp_price_range_01',
				'suwp_price_range_02',
				'suwp_price_adj_02',
				'suwp_price_range_03',
				'suwp_price_range_04',
				'suwp_not_required_msg',
				'suwp_blank_msg',
				'suwp_payment_email_msg',
				'suwp_invalidemail_msg',
				'suwp_nonmatching_msg',
				'suwp_invalidentry_msg',
				'suwp_exceeded_msg',
				'suwp_invalidlength_msg',
				'suwp_invalidchar_msg',
				'suwp_invalidformat_msg',
				'suwp_dupvalues_msg',
				'suwp_service_fieldlabel',
				'suwp_imei_fieldlabel',
				'suwp_sn_fieldlabel',
				'suwp_country_fieldlabel',
				'suwp_network_fieldlabel',
				'suwp_brand_fieldlabel',
				'suwp_model_fieldlabel',
				'suwp_mep_fieldlabel',
				'suwp_kbh_fieldlabel',
				'suwp_activation_fieldlabel',
				'suwp_emailresponse_fieldlabel',
				'suwp_emailconfirm_fieldlabel',
				'suwp_deliverytime_fieldlabel',
				'suwp_code_fieldlabel',
				'suwp_service_label',
				'suwp_imei_label',
				'suwp_sn_label',
				'suwp_country_label',
				'suwp_network_label',
				'suwp_brand_label',
				'suwp_model_label',
				'suwp_mep_label',
				'suwp_kbh_label',
				'suwp_activation_label',
				'suwp_emailresponse_label',
				'suwp_emailconfirm_label',
				'suwp_deliverytime_label',
				'suwp_code_label',
				'suwp_subject_ordersuccess',
				'suwp_message_ordersuccess',
				'suwp_fromname_ordersuccess',
				'suwp_fromemail_ordersuccess',
				'suwp_copyto_ordersuccess',
				'suwp_subject_orderavailable',
				'suwp_message_orderavailable',
				'suwp_fromname_orderavailable',
				'suwp_fromemail_orderavailable',
				'suwp_copyto_orderavailable',
				'suwp_subject_orderrejected',
				'suwp_message_orderrejected',
				'suwp_fromname_orderrejected',
				'suwp_fromemail_orderrejected',
				'suwp_copyto_orderrejected',
				'suwp_subject_ordererror',
				'suwp_message_ordererror',
				'suwp_fromname_ordererror',
				'suwp_fromemail_ordererror',
				'suwp_copyto_ordererror',
				'suwp_subject_checkerror',
				'suwp_message_checkerror',
				'suwp_fromname_checkerror',
				'suwp_fromemail_checkerror',
				'suwp_copyto_checkerror',
			),
		);
		
		// return option data
		return $settings;
		
	}
	
	// returns the requested page option value or it's default
	public function suwp_get_option( $option_name ) {
		 
		// setup return variable
		$option_value = '';	
		
		// get default option values
		$defaults = $this->suwp_get_default_options();
		
		try {
			
			// get the requested option
			switch( $option_name ) {
				
				case 'suwp_author_info':
					// author info
					$option_value = (get_option('suwp_author_info')) ? get_option('suwp_author_info') : $defaults['suwp_author_info'];
					break;
				
				case 'suwp_license_email':
					// license_email
					$option_value = (get_option('suwp_license_email')) ? get_option('suwp_license_email') : $defaults['suwp_license_email'];
					break;
				
				case 'suwp_license_key':
					// license_key
					$option_value = (get_option('suwp_license_key')) ? get_option('suwp_license_key') : $defaults['suwp_license_key'];
					break;
				
				case 'suwp_valid_until':
					// valid_until
					$option_value = (get_option('suwp_valid_until')) ? get_option('suwp_valid_until') : $defaults['suwp_valid_until'];
					break;
				
				case 'suwp_plugin_type':
					// plugin_type
					$option_value = (get_option('suwp_plugin_type')) ? get_option('suwp_plugin_type') : $defaults['suwp_plugin_type'];
					break;
				
				case 'suwp_author_value':
					// author status
					$option_value = (get_option('suwp_author_value')) ? get_option('suwp_author_value') : $defaults['suwp_author_value'];
					break;
				
				case 'suwp_array_posts':
					// array of  posts
					$option_value = (get_option('suwp_array_posts')) ? get_option('suwp_array_posts') : $defaults['suwp_array_posts'];
					break;
				
				case 'suwp_reference_posts':
					// reference posts
					$option_value = (get_option('suwp_reference_posts')) ? get_option('suwp_reference_posts') : $defaults['suwp_reference_posts'];
					break;
				
				case 'suwp_retrieved_posts':
					// retrieved posts
					$option_value = (get_option('suwp_retrieved_posts')) ? get_option('suwp_retrieved_posts') : $defaults['suwp_retrieved_posts'];
					break;
				
				case 'suwp_order_items':
					// ordered items
					$option_value = (get_option('suwp_order_items')) ? get_option('suwp_order_items') : $defaults['suwp_order_items'];
					break;
				
				case 'suwp_manage_product_sync_run_id':
					// product sync run id
					$option_value = (get_option('suwp_manage_product_sync_run_id')) ? get_option('suwp_manage_product_sync_run_id') : $defaults['suwp_manage_product_sync_run_id'];
					break;
				
				case 'suwp_manage_acf_menu_enabled':
					// acf menu settings
					$option_value = (get_option('suwp_manage_acf_menu_enabled')) ? get_option('suwp_manage_acf_menu_enabled') : $defaults['suwp_manage_acf_menu_enabled'];
					break;
				
				case 'suwp_manage_response_email_display':
					// emailresponse field label disply (hide/show)
					$option_value = (get_option('suwp_manage_response_email_display')) ? get_option('suwp_manage_response_email_display') : $defaults['suwp_manage_response_email_display'];
					break;

				case 'suwp_manage_cron_run_id':
					// cron run id
					$option_value = (get_option('suwp_manage_cron_run_id')) ? get_option('suwp_manage_cron_run_id') : $defaults['suwp_manage_cron_run_id'];
					break;
				
				case 'suwp_manage_troubleshoot_run_id':
					// cron troubleshoot id
					$option_value = (get_option('suwp_manage_troubleshoot_run_id')) ? get_option('suwp_manage_troubleshoot_run_id') : $defaults['suwp_manage_troubleshoot_run_id'];
					break;
				
				case 'suwp_price_enabled_01':
					// price adjustment 01 enabled
					$option_value = (get_option('suwp_price_enabled_01')) ? get_option('suwp_price_enabled_01') : $defaults['suwp_price_enabled_01'];
					break;
				
				case 'suwp_price_adj_default':
					// price adjustment default value
					$option_value = (get_option('suwp_price_adj_default')) ? get_option('suwp_price_adj_default') : $defaults['suwp_price_adj_default'];
					break;
				
				case 'suwp_price_adj_01':
					// price adjustment 01 value
					$option_value = (get_option('suwp_price_adj_01')) ? get_option('suwp_price_adj_01') : $defaults['suwp_price_adj_01'];
					break;
				
				case 'suwp_price_range_01':
					// price adjustment 01 range
					$option_value = (get_option('suwp_price_range_01')) ? get_option('suwp_price_range_01') : $defaults['suwp_price_range_01'];
					break;
				
				case 'suwp_price_range_02':
					// price adjustment 02 range
					$option_value = (get_option('suwp_price_range_02')) ? get_option('suwp_price_range_02') : $defaults['suwp_price_range_02'];
					break;
				
				case 'suwp_price_adj_02':
					// price adjustment 02 value
					$option_value = (get_option('suwp_price_adj_02')) ? get_option('suwp_price_adj_02') : $defaults['suwp_price_adj_02'];
					break;
				
				case 'suwp_price_range_03':
					// price adjustment 03 range
					$option_value = (get_option('suwp_price_range_03')) ? get_option('suwp_price_range_03') : $defaults['suwp_price_range_03'];
					break;
				
				case 'suwp_price_range_04':
					// price adjustment 04 range
					$option_value = (get_option('suwp_price_range_04')) ? get_option('suwp_price_range_04') : $defaults['suwp_price_range_04'];
					break;
				case 'suwp_not_required_msg':
					// not required message
					$option_value = (get_option('suwp_not_required_msg')) ? get_option('suwp_not_required_msg') : $defaults['suwp_not_required_msg'];
					break;
				case 'suwp_blank_msg':
					// blank values message
					$option_value = (get_option('suwp_blank_msg')) ? get_option('suwp_blank_msg') : $defaults['suwp_blank_msg'];
					break;
				case 'suwp_payment_email_msg':
					// use payment email message
					$option_value = (get_option('suwp_payment_email_msg')) ? get_option('suwp_payment_email_msg') : $defaults['suwp_payment_email_msg'];
					break;
				case 'suwp_invalidemail_msg':
					// invalid email message
					$option_value = (get_option('suwp_invalidemail_msg')) ? get_option('suwp_invalidemail_msg') : $defaults['suwp_invalidemail_msg'];
					break;
				case 'suwp_nonmatching_msg':
					// non-matching email message
					$option_value = (get_option('suwp_nonmatching_msg')) ? get_option('suwp_nonmatching_msg') : $defaults['suwp_nonmatching_msg'];
					break;
				case 'suwp_invalidentry_msg':
					// invalid entry message
					$option_value = (get_option('suwp_invalidentry_msg')) ? get_option('suwp_invalidentry_msg') : $defaults['suwp_invalidentry_msg'];
					break;
				case 'suwp_exceeded_msg':
					// exceeded total values allowed message
					$option_value = (get_option('suwp_exceeded_msg')) ? get_option('suwp_exceeded_msg') : $defaults['suwp_exceeded_msg'];
					break;
				case 'suwp_invalidlength_msg':
					// invalid length message
					$option_value = (get_option('suwp_invalidlength_msg')) ? get_option('suwp_invalidlength_msg') : $defaults['suwp_invalidlength_msg'];
					break;
				case 'suwp_invalidchar_msg':
					// invalid character message
					$option_value = (get_option('suwp_invalidchar_msg')) ? get_option('suwp_invalidchar_msg') : $defaults['suwp_invalidchar_msg'];
					break;
				case 'suwp_invalidformat_msg':
					// invalid format message
					$option_value = (get_option('suwp_invalidformat_msg')) ? get_option('suwp_invalidformat_msg') : $defaults['suwp_invalidformat_msg'];
					break;
				case 'suwp_dupvalues_msg':
					// duplicate values message
					$option_value = (get_option('suwp_dupvalues_msg')) ? get_option('suwp_dupvalues_msg') : $defaults['suwp_dupvalues_msg'];
					break;
				case 'suwp_service_fieldlabel':
					// service field label value
					$option_value = (get_option('suwp_service_fieldlabel')) ? get_option('suwp_service_fieldlabel') : $defaults['suwp_service_fieldlabel'];
					break;
				case 'suwp_imei_fieldlabel':
					// imei field label value
					$option_value = (get_option('suwp_imei_fieldlabel')) ? get_option('suwp_imei_fieldlabel') : $defaults['suwp_imei_fieldlabel'];
					break;
				case 'suwp_sn_fieldlabel':
					// serial number field label value
					$option_value = (get_option('suwp_sn_fieldlabel')) ? get_option('suwp_sn_fieldlabel') : $defaults['suwp_sn_fieldlabel'];
					break;
				case 'suwp_country_fieldlabel':
					// country field label value
					$option_value = (get_option('suwp_country_fieldlabel')) ? get_option('suwp_country_fieldlabel') : $defaults['suwp_country_fieldlabel'];
					break;
				case 'suwp_country_fieldlabel':
					// country field label value
					$option_value = (get_option('suwp_country_fieldlabel')) ? get_option('suwp_country_fieldlabel') : $defaults['suwp_country_fieldlabel'];
					break;
				case 'suwp_network_fieldlabel':
					// network field label value
					$option_value = (get_option('suwp_network_fieldlabel')) ? get_option('suwp_network_fieldlabel') : $defaults['suwp_network_fieldlabel'];
					break;
				case 'suwp_brand_fieldlabel':
					// brand field label value
					$option_value = (get_option('suwp_brand_fieldlabel')) ? get_option('suwp_brand_fieldlabel') : $defaults['suwp_brand_fieldlabel'];
					break;
				case 'suwp_model_fieldlabel':
					// model field label value
					$option_value = (get_option('suwp_model_fieldlabel')) ? get_option('suwp_model_fieldlabel') : $defaults['suwp_model_fieldlabel'];
					break;
				case 'suwp_mep_fieldlabel':
					// mep field label value
					$option_value = (get_option('suwp_mep_fieldlabel')) ? get_option('suwp_mep_fieldlabel') : $defaults['suwp_mep_fieldlabel'];
					break;
				case 'suwp_kbh_fieldlabel':
					// kbh field label value
					$option_value = (get_option('suwp_kbh_fieldlabel')) ? get_option('suwp_kbh_fieldlabel') : $defaults['suwp_kbh_fieldlabel'];
					break;
				case 'suwp_activation_fieldlabel':
					// activation field label value
					$option_value = (get_option('suwp_activation_fieldlabel')) ? get_option('suwp_activation_fieldlabel') : $defaults['suwp_activation_fieldlabel'];
					break;
				case 'suwp_emailresponse_fieldlabel':
					// emailresponse field label value
					$option_value = (get_option('suwp_emailresponse_fieldlabel')) ? get_option('suwp_emailresponse_fieldlabel') : $defaults['suwp_emailresponse_fieldlabel'];
					break;
				case 'suwp_emailconfirm_fieldlabel':
					// emailconfirm field label value
					$option_value = (get_option('suwp_emailconfirm_fieldlabel')) ? get_option('suwp_emailconfirm_fieldlabel') : $defaults['suwp_emailconfirm_fieldlabel'];
					break;
				case 'suwp_deliverytime_fieldlabel':
					// deliverytime field label value
					$option_value = (get_option('suwp_deliverytime_fieldlabel')) ? get_option('suwp_deliverytime_fieldlabel') : $defaults['suwp_deliverytime_fieldlabel'];
					break;
				case 'suwp_code_fieldlabel':
					// deliverytime field label value
					$option_value = (get_option('suwp_code_fieldlabel')) ? get_option('suwp_code_fieldlabel') : $defaults['suwp_code_fieldlabel'];
					break;

				
				case 'suwp_service_label':
					// service label value
					$option_value = (get_option('suwp_service_label')) ? get_option('suwp_service_label') : $defaults['suwp_service_label'];
					break;
				case 'suwp_imei_label':
					// imei label value
					$option_value = (get_option('suwp_imei_label')) ? get_option('suwp_imei_label') : $defaults['suwp_imei_label'];
					break;
				case 'suwp_sn_label':
					// serial number  label value
					$option_value = (get_option('suwp_sn_label')) ? get_option('suwp_sn_label') : $defaults['suwp_sn_label'];
					break;
				case 'suwp_country_label':
					// country  label value
					$option_value = (get_option('suwp_country_label')) ? get_option('suwp_country_label') : $defaults['suwp_country_label'];
					break;
				case 'suwp_country_label':
					// country  label value
					$option_value = (get_option('suwp_country_label')) ? get_option('suwp_country_label') : $defaults['suwp_country_label'];
					break;
				case 'suwp_network_label':
					// network  label value
					$option_value = (get_option('suwp_network_label')) ? get_option('suwp_network_label') : $defaults['suwp_network_label'];
					break;
				case 'suwp_brand_label':
					// brand  label value
					$option_value = (get_option('suwp_brand_label')) ? get_option('suwp_brand_label') : $defaults['suwp_brand_label'];
					break;
				case 'suwp_model_label':
					// model  label value
					$option_value = (get_option('suwp_model_label')) ? get_option('suwp_model_label') : $defaults['suwp_model_label'];
					break;
				case 'suwp_mep_label':
					// mep  label value
					$option_value = (get_option('suwp_mep_label')) ? get_option('suwp_mep_label') : $defaults['suwp_mep_label'];
					break;
				case 'suwp_kbh_label':
					// kbh  label value
					$option_value = (get_option('suwp_kbh_label')) ? get_option('suwp_kbh_label') : $defaults['suwp_kbh_label'];
					break;
				case 'suwp_activation_label':
					// activation  label value
					$option_value = (get_option('suwp_activation_label')) ? get_option('suwp_activation_label') : $defaults['suwp_activation_label'];
					break;
				case 'suwp_emailresponse_label':
					// emailresponse  label value
					$option_value = (get_option('suwp_emailresponse_label')) ? get_option('suwp_emailresponse_label') : $defaults['suwp_emailresponse_label'];
					break;
				case 'suwp_emailconfirm_label':
					// emailconfirm  label value
					$option_value = (get_option('suwp_emailconfirm_label')) ? get_option('suwp_emailconfirm_label') : $defaults['suwp_emailconfirm_label'];
					break;
				case 'suwp_deliverytime_label':
					// deliverytime  label value
					$option_value = (get_option('suwp_deliverytime_label')) ? get_option('suwp_deliverytime_label') : $defaults['suwp_deliverytime_label'];
					break;
				case 'suwp_code_label':
					// deliverytime  label value
					$option_value = (get_option('suwp_code_label')) ? get_option('suwp_code_label') : $defaults['suwp_code_label'];
					break;
					
				case 'suwp_service_label':
					// imei label value
					$option_value = (get_option('suwp_service_label')) ? get_option('suwp_service_label') : $defaults['suwp_service_label'];
					break;
				case 'suwp_imei_label':
					// imei label value
					$option_value = (get_option('suwp_imei_label')) ? get_option('suwp_imei_label') : $defaults['suwp_imei_label'];
					break;
				case 'suwp_sn_label':
					// serial number label value
					$option_value = (get_option('suwp_sn_label')) ? get_option('suwp_sn_label') : $defaults['suwp_sn_label'];
					break;
				case 'suwp_country_label':
					// country label value
					$option_value = (get_option('suwp_country_label')) ? get_option('suwp_country_label') : $defaults['suwp_country_label'];
					break;
				case 'suwp_country_label':
					// country label value
					$option_value = (get_option('suwp_country_label')) ? get_option('suwp_country_label') : $defaults['suwp_country_label'];
					break;
				case 'suwp_network_label':
					// network label value
					$option_value = (get_option('suwp_network_label')) ? get_option('suwp_network_label') : $defaults['suwp_network_label'];
					break;
				case 'suwp_brand_label':
					// brand label value
					$option_value = (get_option('suwp_brand_label')) ? get_option('suwp_brand_label') : $defaults['suwp_brand_label'];
					break;
				case 'suwp_model_label':
					// model label value
					$option_value = (get_option('suwp_model_label')) ? get_option('suwp_model_label') : $defaults['suwp_model_label'];
					break;
				case 'suwp_mep_label':
					// mep label value
					$option_value = (get_option('suwp_mep_label')) ? get_option('suwp_mep_label') : $defaults['suwp_mep_label'];
					break;
				case 'suwp_kbh_label':
					// kbh label value
					$option_value = (get_option('suwp_kbh_label')) ? get_option('suwp_kbh_label') : $defaults['suwp_kbh_label'];
					break;
				case 'suwp_activation_label':
					// activation label value
					$option_value = (get_option('suwp_activation_label')) ? get_option('suwp_activation_label') : $defaults['suwp_activation_label'];
					break;
				case 'suwp_emailresponse_label':
					// emailresponse label value
					$option_value = (get_option('suwp_emailresponse_label')) ? get_option('suwp_emailresponse_label') : $defaults['suwp_emailresponse_label'];
					break;
				case 'suwp_emailconfirm_label':
					// emailconfirm label value
					$option_value = (get_option('suwp_emailconfirm_label')) ? get_option('suwp_emailconfirm_label') : $defaults['suwp_emailconfirm_label'];
					break;
				case 'suwp_deliverytime_label':
					// deliverytime label value
					$option_value = (get_option('suwp_deliverytime_label')) ? get_option('suwp_deliverytime_label') : $defaults['suwp_deliverytime_label'];
					break;
				case 'suwp_code_label':
					// deliverytime label value
					$option_value = (get_option('suwp_code_label')) ? get_option('suwp_code_label') : $defaults['suwp_code_label'];
					break;

				case 'suwp_subject_ordersuccess':
					// order success subject
					$option_value = (get_option('suwp_subject_ordersuccess')) ? get_option('suwp_subject_ordersuccess') : $defaults['suwp_subject_ordersuccess'];
					break;
				case 'suwp_message_ordersuccess':
					// order success message
					$option_value = (get_option('suwp_message_ordersuccess')) ? get_option('suwp_message_ordersuccess') : $defaults['suwp_message_ordersuccess'];
					break;
				case 'suwp_fromname_ordersuccess':
					// order success from name (when sending email)
					$option_value = (get_option('suwp_fromname_ordersuccess')) ? get_option('suwp_fromname_ordersuccess') : $defaults['suwp_fromname_ordersuccess'];
					break;
				case 'suwp_fromemail_ordersuccess':
					// order success from email (message originator)
					$option_value = (get_option('suwp_fromemail_ordersuccess')) ? get_option('suwp_fromemail_ordersuccess') : $defaults['suwp_fromemail_ordersuccess'];
					break;
				case 'suwp_copyto_ordersuccess':
					// order success copy to (cc destination)
					$option_value = (get_option('suwp_copyto_ordersuccess')) ? get_option('suwp_copyto_ordersuccess') : $defaults['suwp_copyto_ordersuccess'];
					break;
				case 'suwp_subject_orderavailable':
					// order available subject
					$option_value = (get_option('suwp_subject_orderavailable')) ? get_option('suwp_subject_orderavailable') : $defaults['suwp_subject_orderavailable'];
					break;
				case 'suwp_message_orderavailable':
					// order available message
					$option_value = (get_option('suwp_message_orderavailable')) ? get_option('suwp_message_orderavailable') : $defaults['suwp_message_orderavailable'];
					break;
				case 'suwp_fromname_orderavailable':
					// order available from name (when sending email)
					$option_value = (get_option('suwp_fromname_orderavailable')) ? get_option('suwp_fromname_orderavailable') : $defaults['suwp_fromname_orderavailable'];
					break;
				case 'suwp_fromemail_orderavailable':
					// order available from email (message originator)
					$option_value = (get_option('suwp_fromemail_orderavailable')) ? get_option('suwp_fromemail_orderavailable') : $defaults['suwp_fromemail_orderavailable'];
					break;
				case 'suwp_copyto_orderavailable':
					// order available copy to (cc destination)
					$option_value = (get_option('suwp_copyto_orderavailable')) ? get_option('suwp_copyto_orderavailable') : $defaults['suwp_copyto_orderavailable'];
					break;
				case 'suwp_subject_orderrejected':
					// order reply error subject
					$option_value = (get_option('suwp_subject_orderrejected')) ? get_option('suwp_subject_orderrejected') : $defaults['suwp_subject_orderrejected'];
					break;
				case 'suwp_message_orderrejected':
					// order reply error message
					$option_value = (get_option('suwp_message_orderrejected')) ? get_option('suwp_message_orderrejected') : $defaults['suwp_message_orderrejected'];
					break;
				case 'suwp_fromname_orderrejected':
					// order reply error from name (when sending email)
					$option_value = (get_option('suwp_fromname_orderrejected')) ? get_option('suwp_fromname_orderrejected') : $defaults['suwp_fromname_orderrejected'];
					break;
				case 'suwp_fromemail_orderrejected':
					// order reply error from email (message originator)
					$option_value = (get_option('suwp_fromemail_orderrejected')) ? get_option('suwp_fromemail_orderrejected') : $defaults['suwp_fromemail_orderrejected'];
					break;
				case 'suwp_copyto_orderrejected':
					// order reply error copy to (cc destination)
					$option_value = (get_option('suwp_copyto_orderrejected')) ? get_option('suwp_copyto_orderrejected') : $defaults['suwp_copyto_orderrejected'];
					break;
				case 'suwp_subject_ordererror':
					// placing order error subject
					$option_value = (get_option('suwp_subject_ordererror')) ? get_option('suwp_subject_ordererror') : $defaults['suwp_subject_ordererror'];
					break;
				case 'suwp_message_ordererror':
					// placing order error message
					$option_value = (get_option('suwp_message_ordererror')) ? get_option('suwp_message_ordererror') : $defaults['suwp_message_ordererror'];
					break;
				case 'suwp_fromname_ordererror':
					// placing order error from name (when sending email)
					$option_value = (get_option('suwp_fromname_ordererror')) ? get_option('suwp_fromname_ordererror') : $defaults['suwp_fromname_ordererror'];
					break;
				case 'suwp_fromemail_ordererror':
					// placing order error from email (message originator)
					$option_value = (get_option('suwp_fromemail_ordererror')) ? get_option('suwp_fromemail_ordererror') : $defaults['suwp_fromemail_ordererror'];
					break;
				case 'suwp_copyto_ordererror':
					// placing order error copy to (cc destination)
					$option_value = (get_option('suwp_copyto_ordererror')) ? get_option('suwp_copyto_ordererror') : $defaults['suwp_copyto_ordererror'];
					break;
				case 'suwp_subject_checkerror':
					// checking order error subject
					$option_value = (get_option('suwp_subject_checkerror')) ? get_option('suwp_subject_checkerror') : $defaults['suwp_subject_checkerror'];
					break;
				case 'suwp_message_checkerror':
					// checking order error message
					$option_value = (get_option('suwp_message_checkerror')) ? get_option('suwp_message_checkerror') : $defaults['suwp_message_checkerror'];
					break;
				case 'suwp_fromname_checkerror':
					// checking order error from name (when sending email)
					$option_value = (get_option('suwp_fromname_checkerror')) ? get_option('suwp_fromname_checkerror') : $defaults['suwp_fromname_checkerror'];
					break;
				case 'suwp_fromemail_checkerror':
					// checking order error from email (message originator)
					$option_value = (get_option('suwp_fromemail_checkerror')) ? get_option('suwp_fromemail_checkerror') : $defaults['suwp_fromemail_checkerror'];
					break;
				case 'suwp_copyto_checkerror':
					// checking order error copy to (cc destination)
					$option_value = (get_option('suwp_copyto_checkerror')) ? get_option('suwp_copyto_checkerror') : $defaults['suwp_copyto_checkerror'];
					break;
				
			}
			
		} catch( Exception $e) {
			
			// php error
			
		}
		
		// return option value or it's default
		return $option_value;
		
	}
		
	// get's the current options and returns values in associative array
	public function suwp_exec_get_current_options() {
		
		// setup our return variable
		$current_options = array();
		
		try {
		
			// build our current options associative array
			$current_options = array(
				'suwp_author_info' => $this->suwp_get_option('suwp_author_info'),
				'suwp_license_email' => $this->suwp_get_option('suwp_license_email'),
				'suwp_license_key' => $this->suwp_get_option('suwp_license_key'),
				'suwp_valid_until' => $this->suwp_get_option('suwp_valid_until'),
				'suwp_plugin_type' => $this->suwp_get_option('suwp_plugin_type'),
				'suwp_author_value' => $this->suwp_get_option('suwp_author_value'),
				'suwp_array_posts' => $this->suwp_get_option('suwp_array_posts'),
				'suwp_reference_posts' => $this->suwp_get_option('suwp_reference_posts'),
				'suwp_retrieved_posts' => $this->suwp_get_option('suwp_retrieved_posts'),
				'suwp_order_items' => $this->suwp_get_option('suwp_order_items'),
				'suwp_manage_product_sync_run_id' => $this->suwp_get_option('suwp_manage_product_sync_run_id'),
				'suwp_manage_acf_menu_enabled' => $this->suwp_get_option('suwp_manage_acf_menu_enabled'),
				'suwp_manage_response_email_display' => $this->suwp_get_option('suwp_manage_response_email_display'),
				'suwp_manage_cron_run_id' => $this->suwp_get_option('suwp_manage_cron_run_id'),
				'suwp_manage_troubleshoot_run_id' => $this->suwp_get_option('suwp_manage_troubleshoot_run_id'),
				'suwp_price_enabled_01' => $this->suwp_get_option('suwp_price_enabled_01'),
				'suwp_price_adj_default' => $this->suwp_get_option('suwp_price_adj_default'),
				'suwp_price_adj_01' => $this->suwp_get_option('suwp_price_adj_01'),
				'suwp_price_range_01' => $this->suwp_get_option('suwp_price_range_01'),
				'suwp_price_range_02' => $this->suwp_get_option('suwp_price_range_02'),
				'suwp_price_adj_02' => $this->suwp_get_option('suwp_price_adj_02'),
				'suwp_price_range_03' => $this->suwp_get_option('suwp_price_range_03'),
				'suwp_price_range_04' => $this->suwp_get_option('suwp_price_range_04'),
				'suwp_not_required_msg' => $this->suwp_get_option('suwp_not_required_msg'),
				'suwp_blank_msg' => $this->suwp_get_option('suwp_blank_msg'),
				'suwp_payment_email_msg' => $this->suwp_get_option('suwp_payment_email_msg'),
				'suwp_invalidemail_msg' => $this->suwp_get_option('suwp_invalidemail_msg'),
				'suwp_nonmatching_msg' => $this->suwp_get_option('suwp_nonmatching_msg'),
				'suwp_invalidentry_msg' => $this->suwp_get_option('suwp_invalidentry_msg'),
				'suwp_exceeded_msg' => $this->suwp_get_option('suwp_exceeded_msg'),
				'suwp_invalidlength_msg' => $this->suwp_get_option('suwp_invalidlength_msg'),
				'suwp_invalidchar_msg' => $this->suwp_get_option('suwp_invalidchar_msg'),
				'suwp_invalidformat_msg' => $this->suwp_get_option('suwp_invalidformat_msg'),
				'suwp_dupvalues_msg' => $this->suwp_get_option('suwp_dupvalues_msg'),
				'suwp_service_fieldlabel' => $this->suwp_get_option('suwp_service_fieldlabel'),
				'suwp_imei_fieldlabel' => $this->suwp_get_option('suwp_imei_fieldlabel'),
				'suwp_sn_fieldlabel' => $this->suwp_get_option('suwp_sn_fieldlabel'),
				'suwp_country_fieldlabel' => $this->suwp_get_option('suwp_country_fieldlabel'),
				'suwp_network_fieldlabel' => $this->suwp_get_option('suwp_network_fieldlabel'),
				'suwp_brand_fieldlabel' => $this->suwp_get_option('suwp_brand_fieldlabel'),
				'suwp_model_fieldlabel' => $this->suwp_get_option('suwp_model_fieldlabel'),
				'suwp_mep_fieldlabel' => $this->suwp_get_option('suwp_mep_fieldlabel'),
				'suwp_kbh_fieldlabel' => $this->suwp_get_option('suwp_kbh_fieldlabel'),
				'suwp_activation_fieldlabel' => $this->suwp_get_option('suwp_activation_fieldlabel'),
				'suwp_emailresponse_fieldlabel' => $this->suwp_get_option('suwp_emailresponse_fieldlabel'),
				'suwp_emailconfirm_fieldlabel' => $this->suwp_get_option('suwp_emailconfirm_fieldlabel'),
				'suwp_deliverytime_fieldlabel' => $this->suwp_get_option('suwp_deliverytime_fieldlabel'),
				'suwp_code_fieldlabel' => $this->suwp_get_option('suwp_code_fieldlabel'),
				'suwp_service_label' => $this->suwp_get_option('suwp_service_label'),
				'suwp_imei_label' => $this->suwp_get_option('suwp_imei_label'),
				'suwp_sn_label' => $this->suwp_get_option('suwp_sn_label'),
				'suwp_country_label' => $this->suwp_get_option('suwp_country_label'),
				'suwp_network_label' => $this->suwp_get_option('suwp_network_label'),
				'suwp_brand_label' => $this->suwp_get_option('suwp_brand_label'),
				'suwp_model_label' => $this->suwp_get_option('suwp_model_label'),
				'suwp_mep_label' => $this->suwp_get_option('suwp_mep_label'),
				'suwp_kbh_label' => $this->suwp_get_option('suwp_kbh_label'),
				'suwp_activation_label' => $this->suwp_get_option('suwp_activation_label'),
				'suwp_emailresponse_label' => $this->suwp_get_option('suwp_emailresponse_label'),
				'suwp_emailconfirm_label' => $this->suwp_get_option('suwp_emailconfirm_label'),
				'suwp_deliverytime_label' => $this->suwp_get_option('suwp_deliverytime_label'),
				'suwp_code_label' => $this->suwp_get_option('suwp_code_label'),
				'suwp_subject_ordersuccess' => $this->suwp_get_option('suwp_subject_ordersuccess'),
				'suwp_message_ordersuccess' => $this->suwp_get_option('suwp_message_ordersuccess'),
				'suwp_fromname_ordersuccess' => $this->suwp_get_option('suwp_fromname_ordersuccess'),
				'suwp_fromemail_ordersuccess' => $this->suwp_get_option('suwp_fromemail_ordersuccess'),
				'suwp_copyto_ordersuccess' => $this->suwp_get_option('suwp_copyto_ordersuccess'),
				'suwp_subject_orderavailable' => $this->suwp_get_option('suwp_subject_orderavailable'),
				'suwp_message_orderavailable' => $this->suwp_get_option('suwp_message_orderavailable'),
				'suwp_fromname_orderavailable' => $this->suwp_get_option('suwp_fromname_orderavailable'),
				'suwp_fromemail_orderavailable' => $this->suwp_get_option('suwp_fromemail_orderavailable'),
				'suwp_copyto_orderavailable' => $this->suwp_get_option('suwp_copyto_orderavailable'),
				'suwp_subject_orderrejected' => $this->suwp_get_option('suwp_subject_orderrejected'),
				'suwp_message_orderrejected' => $this->suwp_get_option('suwp_message_orderrejected'),
				'suwp_fromname_orderrejected' => $this->suwp_get_option('suwp_fromname_orderrejected'),
				'suwp_fromemail_orderrejected' => $this->suwp_get_option('suwp_fromemail_orderrejected'),
				'suwp_copyto_orderrejected' => $this->suwp_get_option('suwp_copyto_orderrejected'),
				'suwp_subject_ordererror' => $this->suwp_get_option('suwp_subject_ordererror'),
				'suwp_message_ordererror' => $this->suwp_get_option('suwp_message_ordererror'),
				'suwp_fromname_ordererror' => $this->suwp_get_option('suwp_fromname_ordererror'),
				'suwp_fromemail_ordererror' => $this->suwp_get_option('suwp_fromemail_ordererror'),
				'suwp_copyto_ordererror' => $this->suwp_get_option('suwp_copyto_ordererror'),
				'suwp_subject_checkerror' => $this->suwp_get_option('suwp_subject_checkerror'),
				'suwp_message_checkerror' => $this->suwp_get_option('suwp_message_checkerror'),
				'suwp_fromname_checkerror' => $this->suwp_get_option('suwp_fromname_checkerror'),
				'suwp_fromemail_checkerror' => $this->suwp_get_option('suwp_fromemail_checkerror'),
				'suwp_copyto_checkerror' => $this->suwp_get_option('suwp_copyto_checkerror'),
			);
		
		} catch( Exception $e ) {
			
			// php error
		
		}
		
		// return current options
		return $current_options;
		
	
	}
	
	// returns default option values as an associative array
	public function suwp_get_default_options() {
		
		$get_defaults = array();
		$posts_array = array();
		$reference_array = array();
		
		$posts = get_posts(
			array (
				'post_type' => 'suwp_apisource',
				'post_status'   => array('publish', 'pending', 'draft', 'private'),
				'posts_per_page' => -1,
				'orderby' => 'ID',
				'order' => 'ASC'
			)
		);
		
		foreach( $posts as $apiprovider ):
			
			$posts_array[] = $apiprovider->ID;
			
		endforeach;
		
		try {
			
			$suwp_author_info = '';
			$suwp_license_email = SUWP_LICENSE_EMAIL_BASIC;
			$suwp_license_key = SUWP_LICENSE_KEY_BASIC;
			$suwp_valid_until = '';
			$suwp_plugin_type = 0;
			$suwp_author_value = "b";
			$suwp_array_posts = $posts_array;
			$suwp_reference_posts = $reference_array;
			$suwp_retrieved_posts = $reference_array;
			$suwp_order_items = 0;
			$product_sync_run_id = '';
			$acf_menu_setting = 0;
			$suwp_manage_response_email_setting = 0;
			
			// get cron run id
			$cron_run_id = '';
			// get cron troubleshoot id
			$cron_troubleshoot_id = '';
			// get price adjustment vals
			$price_enabled_01 = '';
			$price_adj_default = 1;
			$price_adj_01 = 1;
			$price_range_01 = 0;
			$price_range_02 = 0;
			$price_adj_02 = 1;
			$price_range_03 = 0;
			$price_range_04 = 0;
			// get front page id
			$front_page_id = get_option('page_on_front');
			
			$blog_title = get_bloginfo('name');
			if( empty($blog_title) ) {
				$blog_title = 'YourWebsiteName';
			}
			$admin_email = get_bloginfo('admin_email');
			if( empty($admin_email) ) {
				$admin_email = 'support@yourdomainhere.com';
			}
			$website_url = get_bloginfo('wpurl');
			if( empty($website_url) ) {
				$website_url = 'www.yourwebsitehere.com';
			}

			// setup various text message values
			$suwp_not_required_msg = 'Not Required';
			$suwp_blank_msg = 'Please select or enter at least one value in the following field(s)';
			$suwp_payment_email_msg = 'Reply to Billing Email Address';
			$suwp_invalidemail_msg = 'Please enter a valid email address';
			$suwp_nonmatching_msg = 'Sorry, the email addresses do not match';
			$suwp_invalidentry_msg = 'Invalid entry';
			$suwp_exceeded_msg = 'Exceeded the total number allowed';
			$suwp_invalidlength_msg = 'Number of characters required';
			$suwp_invalidchar_msg = 'Digits only: no letters, punctuation, or spaces allowed';
			$suwp_invalidformat_msg = 'Not a valid entry or format';
			$suwp_dupvalues_msg = 'Duplicate values are not allowed';
			
			// setup product field label values
			$suwp_service_fieldlabel = '{%Service%}:';
			$suwp_imei_fieldlabel = '{%IMEI%}:<br>' .
				chr(10) . '(Total digits = {$charlength}) to display dial: *#06#<br>' .
				chr(10) . 'Bulk Submit: One Per Line';

			$suwp_sn_fieldlabel = '{%Serial Number%}:<br>' .
				chr(10) . '(Total digits = {$charlength})<br>' .
				chr(10) . 'Bulk Submit: One Per Line';
			
			$suwp_country_fieldlabel = '{%Country%}:';
			$suwp_network_fieldlabel = '{%Network Provider%}:';
			$suwp_brand_fieldlabel = '{%Brand%}:';
			$suwp_model_fieldlabel = '{%Model%}:';
			$suwp_mep_fieldlabel = '{%MEP Name%}:';
			$suwp_kbh_fieldlabel = '{%KBH/KRH/ESN%}:';
			$suwp_activation_fieldlabel = '{%Phone Number%}:';

			$suwp_emailresponse_fieldlabel = '{%Response Email%}:<br>' .
				chr(10) . '(Add to your address book: {$adminemail})';

			$suwp_emailconfirm_fieldlabel = '{%Confirm Email%}:';
			$suwp_deliverytime_fieldlabel = '{%Estimated Delivery Time%}:';
			$suwp_code_fieldlabel = '{%Code%}:';
			
			$suwp_service_label = 'Service';
			$suwp_imei_label = 'IMEI';
			$suwp_sn_label = 'Serial Number';
			$suwp_country_label = 'Country';
			$suwp_network_label = 'Network Provider';
			$suwp_brand_label = 'Brand';
			$suwp_model_label = 'Model';
			$suwp_mep_label = 'MEP Name';
			$suwp_kbh_label = 'KBH/KRH/ESN';
			$suwp_activation_label = 'Phone Number';
			$suwp_emailresponse_label = 'Response Email';
			$suwp_emailconfirm_label = 'Confirm Email';
			$suwp_deliverytime_label = 'Estimated Delivery Time';
			$suwp_code_label = 'Code';

			// setup order success email values
			$suwp_subject_ordersuccess = 'Order #{$orderid} Submitted, {%IMEI%} : {$imei}';
			$suwp_message_ordersuccess = 'Dear {$customerfirstname}:' .
				chr(10) .
				chr(10) . 'This message is to inform you that the following has been submitted:' .
				chr(10) .
				chr(10) . 'Order ID: {$orderid}' .
				chr(10) . '{%IMEI%}: {$imei}' .
				chr(10) . '{%Service%}: {$service}' .
				chr(10) . '{%Estimated Delivery Time%}: {$processtime}' .
				chr(10) . '{$phoneinfo}' .
				chr(10) .
				chr(10) . 'Browse to the following page to login to your account and view your order details:' .
				chr(10) . $website_url . '/my-account (Please check your account FIRST before contacting us about the status of your order)' .
				chr(10) .
				chr(10) . 'Once the code is available, we will email the code with instructions to you.' .
				chr(10) . 'If you have any questions or comments, you may contact us by replying to this email.' .
				chr(10) .
				chr(10) . 'NOTE: if you do not receive a notification from us within the time period mentioned above, please sign into your account to see if your code was delivered. If we are not in your address book, our notifications may get blocked by spam filters.' .
				chr(10) .
				chr(10) . 'Once again, thank you for your order.' .
				chr(10) .
				chr(10) . 'Regards, ' .
				chr(10) . $blog_title .
				chr(10) . $website_url .
				chr(10) ;
			$suwp_fromname_ordersuccess = $blog_title;
			$suwp_fromemail_ordersuccess = $admin_email;
			$suwp_copyto_ordersuccess = $admin_email;
			
			// setup order available email values
			$suwp_subject_orderavailable = 'Order #{$orderid} completed - ' . $blog_title . ', {%IMEI%} : {$imei}';
			$suwp_message_orderavailable = 'Dear {$customerfirstname}:' .
				chr(10) .
				chr(10) . 'The code for your mobile phone {%IMEI%} : {$imei} has been successfully calculated.' .
				chr(10) .
				chr(10) . 'Service: {$service}' .
				chr(10) . '{%IMEI%}: {$imei}' .
				chr(10) . '{%Estimated Delivery Time%}: {$processtime}' .
				chr(10) . '{%Code%}: {$reply}' .
				chr(10) . 'Order ID: {$orderid}' .
				chr(10) . '{$phoneinfo}' .
				chr(10) .
				chr(10) . 'Instructions:' .
				chr(10) . $website_url . '/how-to-unlock/unlock-instructions' .
				chr(10) .
				chr(10) . 'Browse to the following page to login to your account and view your order details:' .
				chr(10) . $website_url . '/my-account' .
				chr(10) .
				chr(10) . 'Thanks again, ' .
				chr(10) . $blog_title .
				chr(10) . $website_url .
				chr(10) ;
			$suwp_fromname_orderavailable = $blog_title;
			$suwp_fromemail_orderavailable = $admin_email;
			$suwp_copyto_orderavailable = $admin_email;        
			
			// setup order reply error/rejected email values
			$suwp_subject_orderrejected = 'Code Unsuccessful, {%IMEI%} : {$imei}, Order #{$orderid}';
			$suwp_message_orderrejected = 'Dear {$customerfirstname}:' .
				chr(10) .
				chr(10) . 'Your Order #{$orderid} was Not Found On Database/Not Available.' .
				chr(10) .
				chr(10) . '{%Service%}: {$service}' .
				chr(10) . '{%IMEI%}: {$imei}' .
				chr(10) . '{%Estimated Delivery Time%}: {$processtime}' .
				chr(10) . 'Reason: {$reason}' .
				chr(10) . 'Order ID: {$orderid}' .
				chr(10) . '{$phoneinfo}' .
				chr(10) .
				chr(10) . 'Reasons why not found:' .
				chr(10) . '1) Invalid IMEI.' .
				chr(10) . '2) IMEI is requested through wrong Network.' .
				chr(10) . '    Example: T-Mobile IMEI is requested through AT&T Service.' .
				chr(10) . '    Note: Networks only database unlock codes for their phones.' .
				chr(10) . '3) IMEI is requested prior to scheduled release date by Network.' .
				chr(10) . '    Note: Networks will release unlock codes but not prior to certain date or age of device.' .
				chr(10) . '4) IMEI is lost/stolen, fraud, past due balance and will not be released by Network.' .
				chr(10) . '5) IMEI is requested through wrong Factory service.' .
				chr(10) . '    Example: HTC G2 is requested through LG G2 service.' .
				chr(10) .
				chr(10) . 'Certain orders are non-refundable. However, if this order was refundable,' .
				chr(10) . 'the refund will process within 72 hours. [ONE IMEI REFUND]' .
				chr(10) .
				chr(10) . 'Browse to the following page to login to your account and view your order details:' .
				chr(10) . $website_url . '/my-account' .
				chr(10) .
				chr(10) . 'Thank you, ' .
				chr(10) . 'Admin' .
				chr(10) ;
			$suwp_fromname_orderrejected = $blog_title;
			$suwp_fromemail_orderrejected = $admin_email;
			$suwp_copyto_orderrejected = $admin_email;    
			
			// setup placed order error email values (error at the time of submission)
			$suwp_subject_ordererror = 'ERROR: Order Processing Error/Failure - {%IMEI%} : {$imei}, Order #{$orderid}';
			$suwp_message_ordererror = 'Customer\'s email: {$customeremail}' .
				chr(10) .
				chr(10) . 'Dear ' . $blog_title . ' Admin:' .
				chr(10) .
				chr(10) . 'This message is to inform you that there has been an Order Processing Error/Failure:' .
				chr(10) .
				chr(10) . 'Order ID: {$orderid}' .
				chr(10) . '{%IMEI%}: {$imei}' .
				chr(10) . '{%Service%}: {$service}' .
				chr(10) . '{%Estimated Delivery Time%}: {$processtime}' .
				chr(10) . '{$phoneinfo}' .
				chr(10) .
				chr(10) . 'API Provider: {$apiprovider}' .
				chr(10) .
				chr(10) . 'API Error Message: {$apierrormsg}' .
				chr(10) . 'API Error Description: {$apierrordesc}' .
				chr(10) .
				chr(10) . 'NOTE: The Customer has not been notified about this error.' .
				chr(10) . 'Please take the appropriate action regarding this situation.' .
				chr(10) .
				chr(10) . 'Once resolved, it may be necessary to reset the above order\'s status to "Processing" as it has not been submitted to your Provider.' .
				chr(10) .
				chr(10) . $blog_title .
				chr(10) . $website_url .
				chr(10) ;
			$suwp_fromname_ordererror = $blog_title;
			$suwp_fromemail_ordererror = $admin_email;
			$suwp_copyto_ordererror = $admin_email;  
			
			// setup check order error email values (error checking status of existing order)
			$suwp_subject_checkerror = 'ERROR: Checking Order #{$orderid} - {%IMEI%}: {$imei}';
			$suwp_message_checkerror = 'This message is to inform you that there has been an Order Checking Processing Error.' .
				chr(10) .
				chr(10) . 'Details: The server is attempting to check the status of an existing order, but has failed:' .
				chr(10) .
				chr(10) . 'Order ID: {$orderid}' .
				chr(10) . '{%IMEI%}: {$imei}' .
				chr(10) . '{%Service%}: {$service}' .
				chr(10) . '{%Estimated Delivery Time%}: {$processtime}' .
				chr(10) . '{$phoneinfo}' .
				chr(10) .
				chr(10) . 'API Provider: {$apiprovider}' .
				chr(10) .
				chr(10) . 'API Error Description: {$apierrordesc}' .
				chr(10) . 'API Error Message: {$apierrormsg}' .
				chr(10) . 'API Error Results: {$apiresults}' .
				chr(10) .
				chr(10) . 'NOTE: The Customer has not been notified about this error.' .
				chr(10) . 'Please take the appropriate action regarding this situation.' .
				chr(10) .
				chr(10) . $blog_title .
				chr(10) . $website_url .
				chr(10) ;
			$suwp_fromname_checkerror = $blog_title;
			$suwp_fromemail_checkerror = $admin_email;
			$suwp_copyto_checkerror = $admin_email;  
			
			// setup default email footer
			$default_email_footer = '
				<p>
					Sincerely, <br /><br />
					The '. get_bloginfo('name') .' Team<br />
					<a href="'. get_bloginfo('url') .'">'. get_bloginfo('url') .'</a>
				</p>
			';
		
			// setup defaults array
			$get_defaults = array(
				'suwp_author_info'=>$suwp_author_info,
				'suwp_license_email'=>$suwp_license_email,
				'suwp_license_key'=>$suwp_license_key,
				'suwp_valid_until'=> $suwp_valid_until,
				'suwp_plugin_type'=>$suwp_plugin_type,
				'suwp_author_value'=>$suwp_author_value,
				'suwp_array_posts'=>$suwp_array_posts,
				'suwp_reference_posts'=>$suwp_reference_posts,
				'suwp_retrieved_posts'=>$suwp_retrieved_posts,
				'suwp_order_items'=>$suwp_order_items,
				'suwp_manage_product_sync_run_id'=>$product_sync_run_id,
				'suwp_manage_acf_menu_enabled'=>$acf_menu_setting,
				'suwp_manage_response_email_display'=>$suwp_manage_response_email_setting,
				'suwp_manage_cron_run_id'=>$cron_run_id,
				'suwp_price_enabled_01'=>$price_enabled_01,
				'suwp_price_adj_default'=>$price_adj_default,
				'suwp_price_adj_01'=>$price_adj_01,
				'suwp_price_range_01'=>$price_range_01,
				'suwp_price_range_02'=>$price_range_02,
				'suwp_price_adj_02'=>$price_adj_02,
				'suwp_price_range_03'=>$price_range_03,
				'suwp_price_range_04'=>$price_range_04,
				'suwp_manage_troubleshoot_run_id'=>$cron_troubleshoot_id,
				'suwp_not_required_msg'=>$suwp_not_required_msg,
				'suwp_blank_msg'=>$suwp_blank_msg,
				'suwp_payment_email_msg'=>$suwp_payment_email_msg,
				'suwp_invalidemail_msg'=>$suwp_invalidemail_msg,
				'suwp_nonmatching_msg'=>$suwp_nonmatching_msg,
				'suwp_invalidentry_msg'=>$suwp_invalidentry_msg,
				'suwp_exceeded_msg'=>$suwp_exceeded_msg,
				'suwp_invalidlength_msg'=>$suwp_invalidlength_msg,
				'suwp_invalidchar_msg'=>$suwp_invalidchar_msg,
				'suwp_invalidformat_msg'=>$suwp_invalidformat_msg,
				'suwp_dupvalues_msg'=>$suwp_dupvalues_msg,
				'suwp_service_fieldlabel'=>$suwp_service_fieldlabel,
				'suwp_imei_fieldlabel'=>$suwp_imei_fieldlabel,
				'suwp_sn_fieldlabel'=>$suwp_sn_fieldlabel,
				'suwp_country_fieldlabel'=>$suwp_country_fieldlabel,
				'suwp_network_fieldlabel'=>$suwp_network_fieldlabel,
				'suwp_brand_fieldlabel'=>$suwp_brand_fieldlabel,
				'suwp_model_fieldlabel'=>$suwp_model_fieldlabel,
				'suwp_mep_fieldlabel'=>$suwp_mep_fieldlabel,
				'suwp_kbh_fieldlabel'=>$suwp_kbh_fieldlabel,
				'suwp_activation_fieldlabel'=>$suwp_activation_fieldlabel,
				'suwp_emailresponse_fieldlabel'=>$suwp_emailresponse_fieldlabel,
				'suwp_emailconfirm_fieldlabel'=>$suwp_emailconfirm_fieldlabel,
				'suwp_deliverytime_fieldlabel'=>$suwp_deliverytime_fieldlabel,
				'suwp_code_fieldlabel'=>$suwp_code_fieldlabel,
				'suwp_service_label'=>$suwp_service_label,
				'suwp_imei_label'=>$suwp_imei_label,
				'suwp_sn_label'=>$suwp_sn_label,
				'suwp_country_label'=>$suwp_country_label,
				'suwp_network_label'=>$suwp_network_label,
				'suwp_brand_label'=>$suwp_brand_label,
				'suwp_model_label'=>$suwp_model_label,
				'suwp_mep_label'=>$suwp_mep_label,
				'suwp_kbh_label'=>$suwp_kbh_label,
				'suwp_activation_label'=>$suwp_activation_label,
				'suwp_emailresponse_label'=>$suwp_emailresponse_label,
				'suwp_emailconfirm_label'=>$suwp_emailconfirm_label,
				'suwp_deliverytime_label'=>$suwp_deliverytime_label,
				'suwp_code_label'=>$suwp_code_label,
				'suwp_subject_ordersuccess'=>$suwp_subject_ordersuccess,
				'suwp_message_ordersuccess'=>$suwp_message_ordersuccess,
				'suwp_fromname_ordersuccess'=>$suwp_fromname_ordersuccess,
				'suwp_fromemail_ordersuccess'=>$suwp_fromemail_ordersuccess,
				'suwp_copyto_ordersuccess'=>$suwp_copyto_ordersuccess,
				'suwp_subject_orderavailable'=>$suwp_subject_orderavailable,
				'suwp_message_orderavailable'=>$suwp_message_orderavailable,
				'suwp_fromname_orderavailable'=>$suwp_fromname_orderavailable,
				'suwp_fromemail_orderavailable'=>$suwp_fromemail_orderavailable,
				'suwp_copyto_orderavailable'=>$suwp_copyto_orderavailable,
				'suwp_subject_orderrejected'=>$suwp_subject_orderrejected,
				'suwp_message_orderrejected'=>$suwp_message_orderrejected,
				'suwp_fromname_orderrejected'=>$suwp_fromname_orderrejected,
				'suwp_fromemail_orderrejected'=>$suwp_fromemail_orderrejected,
				'suwp_copyto_orderrejected'=>$suwp_copyto_orderrejected,
				'suwp_subject_ordererror'=>$suwp_subject_ordererror,
				'suwp_message_ordererror'=>$suwp_message_ordererror,
				'suwp_fromname_ordererror'=>$suwp_fromname_ordererror,
				'suwp_fromemail_ordererror'=>$suwp_fromemail_ordererror,
				'suwp_copyto_ordererror'=>$suwp_copyto_ordererror,
				'suwp_subject_checkerror'=>$suwp_subject_checkerror,
				'suwp_message_checkerror'=>$suwp_message_checkerror,
				'suwp_fromname_checkerror'=>$suwp_fromname_checkerror,
				'suwp_fromemail_checkerror'=>$suwp_fromemail_checkerror,
				'suwp_copyto_checkerror'=>$suwp_copyto_checkerror,
				
			);
		
		} catch( Exception $e) {
			
			// php error
			
		}
		
		// return defaults
		return $get_defaults;
	}
	
}
