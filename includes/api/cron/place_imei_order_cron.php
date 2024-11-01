<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_place_imei_order_cron( $current_order_item_id ) {

    global $wpdb;
    $product_id = wc_get_order_item_meta( $current_order_item_id, '_product_id', true );
    
    // get the api provider id from 'product' entry postmeta (meta_key, meta_vaue)
    $post_id = get_field('_suwp_api_provider', $product_id );

	$suwp_apitype = get_field('suwp_apitype', $post_id );
	// not yet converted, use the default
	if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
		$suwp_apitype = '00';
	}

    // get the order_id for this current order
    $suwp_order = $wpdb->get_results( $wpdb->prepare( "SELECT order_id FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_item_id=%d", $current_order_item_id ) );
    $current_order_id = $suwp_order[0]->order_id;
    
	$suwp_order_items = get_option( 'suwp_order_items' );
	
    // get the api service id from 'product' entry postmeta (meta_key, meta_vaue)
    // actual required parameter to be submitted with the order via api
    $suwp_dhru_serviceid = get_field('_suwp_api_service_id', $product_id );
    $suwp_dhru_serviceid_alt = get_field('_suwp_api_service_id_alt', $product_id );
    $qty_sold = wc_get_order_item_meta( $current_order_item_id, '_qty', true );
    $qty_sent = wc_get_order_item_meta( $current_order_item_id, '_suwp_qty_sent', true );
    $suwp_brand_id = wc_get_order_item_meta( $current_order_item_id, 'suwp_brand_id', true );
    $suwp_model_id = wc_get_order_item_meta( $current_order_item_id, 'suwp_model_id', true );
    $suwp_mep_id = wc_get_order_item_meta( $current_order_item_id, 'suwp_mep_id', true );
    $suwp_mep_name = wc_get_order_item_meta( $current_order_item_id, 'suwp_mep_name', true );
    $suwp_network_id = wc_get_order_item_meta( $current_order_item_id, 'suwp_network_id', true );
	
	// since v1.9.5, introduction of custom api field values
	$hideimei_status = get_field('_suwp_hideimei_status', $product_id );
	$hideimei = false;
	if ( $hideimei_status == 'yes') {
		$hideimei = true;
	}
	$suwp_api1_name = get_field('_suwp_custom_api1_name', $product_id );
	$suwp_api2_name = get_field('_suwp_custom_api2_name', $product_id );
	$suwp_api3_name = get_field('_suwp_custom_api3_name', $product_id );
	$suwp_api4_name = get_field('_suwp_custom_api4_name', $product_id );
	$suwp_api1_value = trim( wc_get_order_item_meta( $current_order_item_id, 'suwp_api1_name', true ) );
	$suwp_api2_value = trim( wc_get_order_item_meta( $current_order_item_id, 'suwp_api2_name', true ) );
	$suwp_api3_value = trim( wc_get_order_item_meta( $current_order_item_id, 'suwp_api3_name', true ) );
	$suwp_api4_value = trim( wc_get_order_item_meta( $current_order_item_id, 'suwp_api4_name', true ) );
	
    // error_log( 'NEW - CUSTOM API FIELD NAMES:  $current_order_item_id = ' . $current_order_item_id. ', $suwp_api1_name = ' . $suwp_api1_name. ', $suwp_api2_name = ' . $suwp_api2_name . ', $suwp_api3_name = '. $suwp_api3_name . ', $suwp_api4_name = ' . $suwp_api4_name . ', PRODUCT ID = ' . $product_id);
    // error_log( 'NEW - CUSTOM API FIELD VALUES:  $current_order_item_id = ' . $current_order_item_id. ', $suwp_api1_name = ' . $suwp_api1_value. ', $suwp_api2_name = ' . $suwp_api2_value . ', $suwp_api3_name = '. $suwp_api3_value . ', $suwp_api4_name = ' . $suwp_api4_value . ', PRODUCT ID = ' . $product_id);
	
	$imei = get_option('suwp_not_required_msg') . chr(10);
	if ( !$hideimei ) {
		$imei = wc_get_order_item_meta( $current_order_item_id, 'suwp_imei_values', true );
		
		// extra measures: only process if there is a valid IMEI available
		if( trim($imei) == NULL) {
			error_log( 'ERROR - $imei is NULL, skip this ...');
			goto suwp_end;
		}
	} else {
		error_log( 'PROCESS - $imei is not required ...');
	}

    // error_log( 'NEW - suwp_dhru_place_imei_order_cron:  $current_order_item_id = ' . $current_order_item_id. ', $product_id = ' . $product_id. ', $current_order_id = ' . $current_order_id . ', $qty_sold = '. $qty_sold . ', $suwp_order_items = ' . $suwp_order_items  );
    
    $plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );

    $apidetails = $plugin_admin->suwp_dhru_get_provider_array( $post_id );
    
    // get the api details
    $suwp_dhru_url = $apidetails['suwp_dhru_url'];
    $suwp_dhru_username = $apidetails['suwp_dhru_username'];
    $suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
	
	// >>> include( plugin_dir_path( __FILE__ ) . 'providers/place_imei_order_constants_' . $current_order_item_id . '_cron.php' ); 

	$suwp_file_contents = SUWP_TEMP . 'suwp_place_imei_order_constants_' . $current_order_item_id . '_cron.php';
	include( $suwp_file_contents );

	// error_log( 'THE CONTENTZ = '. file_get_contents($suwp_file_contents) );

    // proceed to loop through the number of IMEI, submitting an order for each,
    // saving each result to a custom, private comment for later retrieval.
    $imei_values = array();
    $imei_values = explode( "\n", trim($imei));
    $imei_count = count( $imei_values );
    $suwp_dhru_imei = '';
	$create_comment = false;
	$update_qty_sent = false;
	
    // loop over all imei and place order for each
    foreach( $imei_values as $value ):
		
		$suwp_dhru_imei = '';

		$suwp_dhru_imei = trim($value);
		// since 1.9.5.22: added the order item number in case multiple order items contains the same IMEI
		// previously was exiting when should have continued processing remaining IMEI
		// However, DHRU API will NOT ALLOW the same IMEI for different services at one time
		// Now, this order will properly FAIL if same IMEI is detected mulitple times rather than
		// charging the customer for ALL of them and only returning one reply
		$postfix = "-php-" . $current_order_item_id;
		$suwp_dhru_imei_like = $suwp_dhru_imei . $postfix . '%'; // ... begins with
        $comment_type = 'order_note';
		
		error_log( 'CHECK - suwp_dhru_imei_like,postfix  = ' . print_r( $suwp_dhru_imei_like, true ) );
		
		// for plugin versions pre v1.7.5
		// there should only be one API comment entry per order item (IMEI), remove everything but the latest one
		// more than one will throw off the final order status
		// $suwp_comments = $wpdb->get_results('SELECT comment_ID, comment_post_ID, comment_type FROM `' . $wpdb->prefix . 'comments` WHERE `comment_post_ID`=' . $current_order_id . ' AND NOT `comment_type`=\'order_note\' AND `comment_content` LIKE \'%' . $suwp_dhru_imei . '%\' ORDER BY comment_ID DESC');
		$suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type='order_note' AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id , $suwp_dhru_imei_like ) );
		
		error_log( 'NEW - $suwp_comments = ' . print_r( $suwp_comments, true ) );
		
		$del_comments = array();
		$del_comments_removed = array();
		foreach( $suwp_comments as $key => $loop_comments_item_id ):
			if( $loop_comments_item_id->comment_post_ID === $current_order_id ) {
				$del_comments[] = $loop_comments_item_id->comment_ID;
			}
		endforeach; // foreach( $suwp_comments as $key => $loop_comments_item_id )
		
		// only keep the latest comment ... remove the first array item
		// if only one item, it's ok, it won't get deleted
		if( !empty($del_comments) ) {
			$del_comments_removed = array_shift($del_comments);
		}
		
		if( !empty($del_comments) ) {
			foreach( $del_comments as $key => $item_id ):
				wp_delete_comment( $item_id, true );
			endforeach;
		}
		
        // before submitting, determine if this particular IMEI has already been successfully accepted
        // it's possible that this order failed and is now reset to 'wc-processing' in order
        // to catch what didn't make it the first time around. It's just nice not to be charged twice ;-)
        
        // need to look in the 'comments' table: comment_post_ID is the order number, comment_content starts with the target IMEI number
        // make sure we're looking at the $current_order_id (comment_post_ID)
        // the comment_content contains the IMEI in question AND the comment_type MUST be anything but 'suwp_order_success' in order to process
        // if 'suwp_order_success', it's already been submitted, leave it alone ; if this comment is of comment_type 'order_note', leave it alone
		// THIS NEVER WORKED: $suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type=%s AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id, $comment_type , $suwp_dhru_imei_like ) );
		// $suwp_comments = $wpdb->get_results('SELECT comment_ID, comment_post_ID, comment_type FROM `' . $wpdb->prefix . 'comments` WHERE `comment_post_ID`=' . $current_order_id . ' AND NOT `comment_type`=\'order_note\' AND `comment_content` LIKE \'%' . $suwp_dhru_imei . '%\' ORDER BY comment_ID DESC');
		$suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type='order_note' AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id , $suwp_dhru_imei_like ) );
		
		 /**
		 'suwp_order_error': possible duplicate imei, insufficient funds, etc.; do not auto resend, hold for action, don't $update_qty_sent
		 'suwp_order_success': successful order submission, get the reference id, $update_qty_sent
		 'suwp_connect_fail': possible connection failure, never connected to the API server, don't $update_qty_sent
		 **/
		 
		$comment_ID = '';
		$comment_post_ID = '';
	    $comment_type = '';
		
		$submit_order = false;
		
		if( empty($suwp_comments) ) {
			// brand new order item, submit it
			error_log('initial order submit: $suwp_comments does not exist, create it');
			$submit_order = true;
		} else {
			error_log('initial order submit: $suwp_comments exists, use it');
		}
		
		 /** Results of orders being placed
		 'suwp_order_success': successful order submission, get the reference id, $update_qty_sent
		 'suwp_order_error': possible duplicate imei, insufficient funds, etc.; do not auto resend, hold for action, don't $update_qty_sent
		 'suwp_connect_fail': possible connection failure, never connected to the API server, don't $update_qty_sent
		 **/
		 // NEW v1.8.0 ability to resubmit failed replies: 'suwp_reply_error', made possible by updating the API order reference below
		foreach( $suwp_comments as $key => $loop_comments_item_id ):
			$comment_post_ID = $loop_comments_item_id->comment_post_ID;
			$comment_type = $loop_comments_item_id->comment_type;
			if( ( $comment_post_ID === $current_order_id && $comment_type === 'suwp_order_error' ) ||
			   ( $comment_post_ID === $current_order_id && $comment_type === 'suwp_connect_fail' ) ||
			   ( $comment_post_ID === $current_order_id && $comment_type === 'suwp_reply_error' ) ) {
				$submit_order = true;
			}
		endforeach; // foreach( $suwp_comments as $key => $loop_comments_item_id )
		
		if( $submit_order ) {
			error_log( $suwp_dhru_imei . '  BEING SUBMITTED, NOT YET PROCESSED.' );
		} else {
			// skip this IMEI, it's been answered already
			error_log( $suwp_dhru_imei . '  BEING SKIPPED, ALREADY PROCESSED.' );
			continue;
		}
		
		$request = array();
        $reply =  array();
        $para = array();
		$flag_continue = FALSE;
		
		// >>> include( plugin_dir_path( __FILE__ ) . 'providers/place_imei_order_api_' . $current_order_item_id . '_cron.php' );
		
		$suwp_file_contents = SUWP_TEMP . 'suwp_place_imei_order_api_' . $current_order_item_id . '_cron.php';
		include( $suwp_file_contents );
		
		// error_log( 'THE CONTENTZ = '. file_get_contents($suwp_file_contents) );

        // error_log( $suwp_dhru_serviceid . ' - (SERVICE ID) >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CURRENT IMEI VALUE >>>> ' . $suwp_dhru_imei );
        
        // error_log( $suwp_dhru_serviceid . ' - (SERVICE ID) >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CURRENT $para VALUE >>>> ' . print_r( $para, true ) );
        
		// DRHU: REQUIRED PARAMETERS 
		// $para['IMEI']
		// $para['ID']
        // $para['MODELID'] = "";
        // $para['PROVIDERID'] = "";
        // $para['MEP'] = "";
        // $para['PIN'] = "";
        // $para['KBH'] = "";
        // $para['PRD'] = "";
        // $para['TYPE'] = "";
        // $para['REFERENCE'] = "";
        // $para['LOCKS'] = "";
				
		// GSM FUSION / NAKSHSOFT: REQUIRED PARAMETERS 
		// $para['IMEI']
		// $para['networkId']
		// [...]
		
        // UNLOCKBASE REQUIRED PARAMETERS 
		// $para['IMEI']
		// $para['Tool']
        // $para['MODELID'] = "";
        // $para['PROVIDERID'] = "";
        // $para['MEP'] = "";
        // $para['PIN'] = "";
        // $para['KBH'] = "";
        // $para['PRD'] = "";
        // $para['TYPE'] = "";
        // $para['REFERENCE'] = "";
		// $para['LOCKS'] = "";

// DHRU FUSION .... START
				
				if ($suwp_apitype == '00') {

					$para['IMEI'] = $suwp_dhru_imei;
					$para['ID'] = $suwp_dhru_serviceid;

					if( isset( $suwp_model_id ) && $suwp_model_id != '' ){
						$para['MODELID'] = $suwp_model_id;
					}
					if( isset( $suwp_network_id ) && $suwp_network_id != '' ){
						$para['PROVIDERID'] = $suwp_network_id;
					}
					if( isset( $suwp_mep_id ) && $suwp_mep_id != ''){
						$para['MEP'] = $suwp_mep_id;
					}
					// since v1.9.5, passing custom api values 
					if ( isset( $suwp_api1_value ) && $suwp_api1_value != '' ) {
						$para[$suwp_api1_name] = $suwp_api1_value;
					}
					if ( isset( $suwp_api2_value ) && $suwp_api2_value != '' ) {
						$para[$suwp_api2_name] = $suwp_api2_value;
					}
					if ( isset( $suwp_api3_value ) && $suwp_api3_value != '' ) {
						$para[$suwp_api3_name] = $suwp_api3_value;
					}
					if ( isset( $suwp_api4_value ) && $suwp_api4_value != '' ) {
						$para[$suwp_api4_name] = $suwp_api4_value;
					}
					
					/*
					$api->action( 'placeimeiorder', $para );
					$api->XmlToArray($api->getResult());
					$request = $api->createArray();
					*/

					// Debug on
					$api->debug = true;
					$request = $api->action( 'placeimeiorder', $para );
					$http_code = $api->getHttpCode();
					$http_url = $api->getHttpUrl();
					$http_result = 'OTHER';
					$http_message = 'Possible network connection error, do nothing.';
					$http_description = 'Network or API Error.';

					// error_log( 'DHRU FUSION CURL, 1ST PHASE: '. print_r($api,true) );
	
					if ( $http_code == '406' ) {
						$http_result = 'ERROR';
						$http_message = '<p>ERROR 406 Not Acceptable: This is most likely due to your web hosting environments mod_security settings: https://stackoverflow.com/questions/12928360/how-can-i-disable-mod-security-in-htaccess-file</p>';
						$http_description = '<p>Your web hosting provider is blocking this url: ' . $http_url . '. Please contact your web hosting provider to find out why.</p>' ;
					}

					$reply = array(
						'RESULT' => $http_result,
						'APIID' => $para['ID'],
						'IMEI' => $para['IMEI'],
						'MESSAGE' => $http_message,
						'DESCRIPTION' => $http_description,
					);

					if (is_array($request)) {
							$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($request), RecursiveIteratorIterator::SELF_FIRST);
							foreach ($iterator as $key1 => $val1) {
									if ($key1 === 'ID') {
										$tmp_apiid = $val1;
									}
									if ($key1 === 'IMEI') {
										$tmp_imei = $val1;
									}
									if ($key1 === 'MODELID') {
										$tmp_modelid = $val1;
									}
									if ($key1 === 'PROVIDERID') {
										$tmp_providerid = $val1;
									}
									if ($key1 === 'MEP') {
										$tmp_mep = $val1;
									}
									if ($key1 === 'PIN') {
										$tmp_pin = $val1;
									}
									if ($key1 === 'PRD') {
										$tmp_prd = $val1;
									}
									if ($key1 === 'TYPE') {
										$tmp_type = $val1;
									}
									if ($key1 === 'REFERENCE') {
										$tmp_reference = $val1;
									}
									if ($key1 === 'LOCKS') {
										$tmp_locks = $val1;
									}
									if ($key1 === 'SUCCESS' || $key1 === 'ERROR') {
										$flag_continue = TRUE;
										$tmp_result = $key1;
										if (is_array($val1)) {
											foreach ($val1 as $key2 => $val2) {
												if (is_array($val2)) {
													$tmp_msg = '';
													$tmp_full_desc = '';
													$tmp_referenceid = '';
													foreach ($val2 as $key3 => $val3) {
														if ($key3 === 'MESSAGE') {
															$tmp_msg = $val3;
														}
														if ($key3 === 'FULL_DESCRIPTION') {
															$tmp_full_desc = $val3;
														}
														if ($key3 === 'REFERENCEID') {
															$tmp_referenceid = $val3;
														}
													}
													switch ($tmp_result) {
														case 'ERROR':
															$reply = array(
																'RESULT' => $tmp_result,
																'APIID' => $tmp_apiid,
																'IMEI' => $tmp_imei,
																'MESSAGE' => $tmp_msg,
																'DESCRIPTION' => $tmp_full_desc,
															);
															break;
									
														case 'SUCCESS':
															$reply = array(
																'RESULT' => $tmp_result,
																'APIID' => $tmp_apiid,
																'IMEI' => $tmp_imei,
																'MESSAGE' => $tmp_msg,
																'REFERENCEID' => $tmp_referenceid,
															);
															break;
									
													}
												}
											}
										}
									}
							}
							
					}

					// error_log( 'DHRU FUSION SUBMIT FINAL ORDER REPLY: '. print_r($reply,true) );

				}
				
// DHRU FUSION .... END

// UNLOCKBASE .... START
				if ($suwp_apitype == '02') {
					
					$para['IMEI'] = $suwp_dhru_imei;
					$para['Tool'] = $suwp_dhru_serviceid;

					if( isset( $suwp_model_id ) && $suwp_model_id != '' ){
						$para['Mobile'] = $suwp_model_id;
					}
					if( isset( $suwp_network_id ) && $suwp_network_id != '' ){
						$para['Network'] = $suwp_network_id;
					}
					if( isset( $suwp_mep_id ) && $suwp_mep_id != ''){
						$para['MEP'] = $suwp_mep_id;
					}

					// error_log( 'UNLOCKBASE PRE SUBMIT ORDER : $suwp_model_id = '. $suwp_model_id . ', $suwp_network_id = ' . $suwp_network_id . ', $suwp_mep_id = ' . $suwp_mep_id . ', $suwp_dhru_imei = ' . $suwp_dhru_imei );

					$XML = $api->CallAPI('PlaceOrder', $para );

					if (is_string($XML)) {
						/* Parse the XML stream */
						$Data = $api->ParseXML($XML);

						if (is_array($Data)) {

							$request = $Data;
							
							// error_log( 'UNLOCKBASE SUBMIT ORDER RESPONSE: '. print_r($Data,true) );
										
							if (isset($Data['Error'])) {
								/* The API has returned an error */
								// print('API error : ' . htmlspecialchars($Data['Error']));
								$reply = array(
									'RESULT' => 'ERROR',
									'APIID' => $para['Tool'],
									'IMEI' => $para['IMEI'],
									'MESSAGE' => htmlspecialchars($Data['Error']),
									'DESCRIPTION' => 'API error',
								);

							} else {
								/* Everything works fine */
								// print('<b>' . htmlspecialchars($Data['Success']) . '</b>');
								$reply = array(
									'RESULT' => 'SUCCESS',
									'APIID' => $para['Tool'],
									'IMEI' => $para['IMEI'],
									'MESSAGE' => htmlspecialchars($Data['Success']),
									'REFERENCEID' => $Data['ID'],
								);

							}
						} else {
							/* Parsing error */
							// print('Could not parse the XML stream');
							$reply = array(
								'RESULT' => 'OTHER',
								'APIID' => $para['Tool'],
								'IMEI' => $para['IMEI'],
								'MESSAGE' => 'Could not parse the XML stream. Try again later.',
								'DESCRIPTION' => 'Parsing error',
							);
						}
					} else {
						/* Communication error */
						// print('Could not communicate with the api');
						$reply = array(
							'RESULT' => 'OTHER',
							'APIID' => $para['Tool'],
							'IMEI' => $para['IMEI'],
							'MESSAGE' => 'Could not communicate with the api. Try again later.',
							'DESCRIPTION' => 'Communication error',
						);
					}
				}
// UNLOCKBASE .... END

// GSM FUSION .... START
				if ( $suwp_apitype == '01' || $suwp_apitype == '03' ) {

					$para['imei'] = $suwp_dhru_imei;
					$para['networkId'] = $suwp_dhru_serviceid;
			
					if( isset( $suwp_model_id ) && $suwp_model_id != '' ){
						// $para['MODELID'] = $suwp_model_id;
					}
					if( isset( $suwp_network_id ) && $suwp_network_id != '' ){
						// $para['PROVIDERID'] = $suwp_network_id;
					}
					if( isset( $suwp_mep_id ) && $suwp_mep_id != ''){
						// $para['MEP'] = $suwp_mep_id;
					}
					
					$api->action( 'placeorder', $para );
					$api->XmlToArray($api->getResult());
					$arrayData = $api->createArray();
					
					$api_using = '';

					if( $suwp_apitype == '01' ) {
						$api_using = 'GSMFUSION';
					}
					if( $suwp_apitype == '03' ) {
						$api_using = 'NAKSHSOFT';
					}

					// error_log( $api_using . ' SUBMIT ORDER RESPONSE: '. print_r($arrayData,true) );
					
					$reply = array(
						'RESULT' => 'OTHER',
						'APIID' => $para['networkId'],
						'IMEI' => $para['imei'],
						'MESSAGE' => 'Possible network connection error, do nothing.',
						'DESCRIPTION' => 'Network or API Error.',
					);

					if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
					{

						$reply = array(
							'RESULT' => 'ERROR',
							'APIID' => $para['networkId'],
							'IMEI' => $para['imei'],
							'MESSAGE' => $arrayData['error'][0],
							'DESCRIPTION' => $arrayData['error'][0],
						);
					}

					$RESPONSE_ARR_DUP = array();
					if(isset($arrayData['result']['imeis']) && sizeof($arrayData['result']['imeis']) > 0) {
						$request = $arrayData['result']['imeis'];
					}

					if(isset($arrayData['result']['imeiduplicates']) && sizeof($arrayData['result']['imeiduplicates']) > 0) {
						$RESPONSE_ARR_DUP = $arrayData['result']['imeiduplicates'];
					}
					
					$total = count($request);
					if($total > 0)
					{
						for($count = 0; $count < $total; $count++)
						{

							$reply = array(
								'RESULT' => 'SUCCESS',
								'APIID' => $para['networkId'],
								'IMEI' => $para['imei'],
								'MESSAGE' => $request[$count]['status'],
								'REFERENCEID' => $request[$count]['id'],
							);
							
						}
						
					}
					$total_errors = count($RESPONSE_ARR_DUP);
					if($total_errors > 0)
					{
						$arrDupIMEIS = explode(',', $RESPONSE_ARR_DUP[0]['imei']);
						$totalDupIMES = count($arrDupIMEIS);
						for($count = 0; $count < $totalDupIMES; $count++)
						{
							
							$message = $request[$count]['status'] . ' - IMEI already exists.';
							
							$reply = array(
								'RESULT' => 'ERROR',
								'APIID' => $para['networkId'],
								'IMEI' => $arrDupIMEIS[$count],
								'MESSAGE' => $message,
								'DESCRIPTION' => $request[$count]['id'],
							);
							
						}
					}

					// error_log( 'GSMFUSIONAPI SUBMIT ORDER RESPONSE: '. print_r($request,true) );
				}
// GSM FUSION .... END

// IPHONEADMIN .... START
				if ($suwp_apitype == '04') {
					
					$suwp_unique_key = md5(microtime().rand());
					$devices = json_encode( array($suwp_dhru_imei) );

					if( isset( $suwp_model_id ) && $suwp_model_id != '' ){
						$para['Mobile'] = $suwp_model_id;
					}
					if( isset( $suwp_network_id ) && $suwp_network_id != '' ){
						$para['Network'] = $suwp_network_id;
					}
					if( isset( $suwp_mep_id ) && $suwp_mep_id != ''){
						$para['MEP'] = $suwp_mep_id;
					}

					// error_log( 'IPHONEADMIN PRE SUBMIT ORDER : $suwp_model_id = '. $suwp_model_id . ', $suwp_network_id = ' . $suwp_network_id . ', $suwp_mep_id = ' . $suwp_mep_id . ', $suwp_dhru_imei = ' . $suwp_dhru_imei );

					// $request = $api->action( 'placeimeiorder', $para );
					
					// error_log( 'IPHONEADMIN CURL API: '. print_r($api,true) );

					// execute the request
					$request =  $api->action([
						'API_ID' => $suwp_dhru_username,
						'API_KEY' => $suwp_dhru_api_key,
						'UNIQUE_KEY'   => $suwp_unique_key,
						'SERVICE'   => $suwp_dhru_serviceid_alt,
						'NOTIFY_URL'   => get_site_url(),
						'DEVICES'   => $devices,
					]);
					
					$http_code = $api->getHttpCode();
					// error_log( 'IPHONEADMIN HTTP_CODE = ' . $http_code );

					$http_result = 'OTHER';
					$http_message = 'Possible network connection error, do nothing.';
					$http_description = 'Network or API Error.';

					$reply = array(
						'RESULT' => $http_result,
						'APIID' => $suwp_dhru_serviceid,
						'IMEI' => $suwp_dhru_imei,
						'MESSAGE' => $http_message,
						'DESCRIPTION' => $http_description,
					);

					// error_log( 'IPHONEADMIN SUBMIT ORDER RESPONSE: '. print_r($request,true) );
					
					if (is_array($request)) {

						// RESULT = 1 for success, 0 for fail
						if ( $request['RESULT'] ) {
							// Everything works fine
							// print('<b>' . htmlspecialchars($request['MESSAGE']) . '</b>');
							$reply = array(
								'RESULT' => 'SUCCESS',
								'APIID' => $suwp_dhru_serviceid,
								'IMEI' => $suwp_dhru_imei,
								'MESSAGE' => htmlspecialchars($request['MESSAGE']),
								'REFERENCEID' => $request['REQUEST_ID'],
							);

						} else {
							// The API has returned an error
							// print('API error : ' . htmlspecialchars($request['MESSAGE']));
							$reply = array(
								'RESULT' => 'ERROR',
								'APIID' => $suwp_dhru_serviceid,
								'IMEI' => $suwp_dhru_imei,
								'MESSAGE' => htmlspecialchars($request['MESSAGE']),
								'DESCRIPTION' => 'API error',
							);

						}
						
					} else {
						
						// error_log( 'IPHONEADMIN SUBMIT ORDER ERROR RESPONSE: '. print_r($request,true) );
						
						// Communication error
						// print('Could not communicate with the api');
						$reply = array(
							'RESULT' => 'OTHER',
							'APIID' => $suwp_dhru_serviceid,
							'IMEI' => $suwp_dhru_imei,
							'MESSAGE' => 'Could not communicate with the api. Try again later.',
							'DESCRIPTION' => 'Communication error',
						);
					}
				}
// IPHONEADMIN .... END

        // create unique comment(s) based on api reply
                    
        // $flat_request = suwp_array_flatten($request, 2);
        // error_log('PLACE ORDERS FLAT : ' . print_r($flat_request, true));

		// >>> $reply = 'holds specific elements extracted from the API message(s)';
		// >>> $request = 'the ENTIRE results of the API messages, all in one';

		
		// JSON_FORCE_OBJECT
        // >>> $reply_serialized = serialize($reply);
		// >>> $reply['API_REPLY'] = serialize($request);
		
		$reply_serialized = json_encode($reply, JSON_FORCE_OBJECT);
		$reply['API_REPLY'] = json_encode($request, JSON_FORCE_OBJECT);
        
        $email_template_name = '';
        $comment_msg = '';

        $blog_title = get_bloginfo('name');
        if( empty($blog_title) ) {
            $blog_title = ''; // 'YourWebsiteName'
        }
        $admin_email = get_bloginfo('admin_email');
        if( empty($admin_email) ) {
            $admin_email = ''; // 'support@yourdomainhere.com'
        }
        $website_url = get_bloginfo('wpurl');
        if( empty($website_url) ) {
            $website_url = ''; // www.yourwebsitehere.com'
        }
        
        switch ($reply['RESULT']) {
        
          case 'ERROR':
            // possible duplicate imei, insufficient funds, etc.
            
			// one API comment per order item
			// if the comment doesn't already exist, create it
			// THIS NEVER WORKED: $suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type=%s AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id, $comment_type , $suwp_dhru_imei_like ) );
			// $suwp_comments = $wpdb->get_results('SELECT comment_ID, comment_post_ID, comment_type FROM `' . $wpdb->prefix . 'comments` WHERE `comment_post_ID`=' . $current_order_id . ' AND NOT `comment_type`=\'order_note\' AND `comment_content` LIKE \'%' . $suwp_dhru_imei . '%\' ORDER BY comment_ID DESC');
			$suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type='order_note' AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id , $suwp_dhru_imei_like ) );
			
			$create_comment = false;
			$update_qty_sent = false;
			
			/**
			'suwp_order_error': possible duplicate imei, insufficient funds, etc.; do not auto resend, hold for action, don't $update_qty_sent
			'suwp_order_success': successful order submission, get the reference id, $update_qty_sent
			'suwp_connect_fail': possible connection failure, never connected to the API server, don't $update_qty_sent
			**/
		 
			if( empty($suwp_comments) ) {
				// brand new order, create comment entry, don't update qty sent, error
				error_log('ERROR order submit: $suwp_comments does not exist, create it');
				$create_comment = true;
				$update_qty_sent = false;
			} else {
				error_log('ERROR order submit: $suwp_comments exists, use it');
			}
			
			foreach( $suwp_comments as $key => $loop_comments_item_id ):
				$comment_ID = $loop_comments_item_id->comment_ID;
				$comment_post_ID = $loop_comments_item_id->comment_post_ID;
				$comment_type = $loop_comments_item_id->comment_type;
				if( $comment_post_ID === $current_order_id && $comment_type === 'suwp_connect_fail' ) {
					$update_qty_sent = false;
				}
			endforeach; // foreach( $suwp_comments as $key => $loop_comments_item_id )
			
			$comment_agent = strip_tags( $reply['DESCRIPTION'] ); // no longer used for comment_agent after v1.8.0, replaced with order_item_id value
            $comment_msg = $reply['MESSAGE'] . '; ' . $comment_agent; // stripping tags cause messing with order display
            $time = current_time('mysql');
            $comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
            $email_template_name = 'suwp_order_error';
            
			if ( $create_comment ) {
				
				// create a new comment
				$commentdata = array(
					'comment_post_ID'       => $current_order_id,
					'comment_author'        => 'StockUnlocks-php-'.$suwp_dhru_username,
					'comment_author_email'  => $suwp_dhru_username, // dhru api username
					'comment_author_url'    => $suwp_dhru_url, // dhru api url
					'comment_content'       => $comment_content, // contains imei with api message
					'comment_agent'         => $current_order_item_id, // v1.8.0 ; can be used later
					'comment_type'          => $email_template_name, // suwp_order_success, suwp_order_error, or suwp_connect_fail
					'comment_parent'        => 0,
					'user_id'               => 0,
					'comment_author_IP'     => $suwp_dhru_api_key, // dhru api access key
					'comment_date'          => $time,
					'comment_date_gmt'      => $time,
					'comment_karma'         => 0,
					'comment_approved'      => 1,
				);
				
				//Insert new comment and get the comment ID
			   $comment_id = wp_insert_comment( $commentdata );
			   
			} else {
				
				// update existing comment
				$comment = array();
				$comment['comment_ID'] = $comment_ID;
				$comment['comment_content'] = $comment_content;
				$comment['comment_agent'] = $current_order_item_id; // v1.8.0 ; can be used later
				$comment['comment_type'] = $email_template_name;
				$comment['comment_author_IP'] = $suwp_dhru_api_key; // v1.8.0 ; to enable for reprocessing
				$comment['comment_date'] = $time;
				$comment['comment_date_gmt'] = $time;
				$comment_id = wp_update_comment( $comment );
			}
			
            // notify customer and, optionally, Bcc: admin with results 
            if ( suwp_send_cron_recipient_email( $comment_content, $email_template_name ) ) {
                
                error_log("SUCCESS : Place IMEI Order ERROR message successfully sent.");
            
            } else {
                // failed to send
                error_log("ERROR : Place IMEI Order ERROR message failed to send.");
            }
                
            // NEW: ADD A COMMENT TO THE ACTUAL ORDER ...
            $commentdata = array(
                'comment_post_ID' => $current_order_id, // to which post the comment will show up
                'comment_author' => $blog_title, //fixed value - can be dynamic 
                'comment_author_email' => $admin_email, //fixed value - can be dynamic 
                'comment_author_url' => $website_url, //fixed value - can be dynamic 
                'comment_content' => $comment_msg, //fixed value - can be dynamic
                'comment_approved' => 1,
                'comment_agent' => 'WooCommerce',
                'comment_type' => 'order_note', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
                'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
                'user_id' => get_current_user_id(), //passing current user ID or any predefined as per the demand
            );
            
            //Insert new comment and get the comment ID
            $comment_id = wp_insert_comment( $commentdata );
            
            break;
            
          case 'SUCCESS':
            // successful order submission, get the reference id
            
			// one API comment per order item
			// if the comment doesn't already exist, create it
			// THIS NEVER WORKED: $suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type=%s AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id, $comment_type , $suwp_dhru_imei_like ) );
			// $suwp_comments = $wpdb->get_results('SELECT comment_ID, comment_post_ID, comment_type FROM `' . $wpdb->prefix . 'comments` WHERE `comment_post_ID`=' . $current_order_id . ' AND NOT `comment_type`=\'order_note\' AND `comment_content` LIKE \'%' . $suwp_dhru_imei . '%\' ORDER BY comment_ID DESC');
			$suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type='order_note' AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id , $suwp_dhru_imei_like ) );
			
			$create_comment = false;
			$update_qty_sent = false;
			
			/**
			'suwp_order_error': possible duplicate imei, insufficient funds, etc.; do not auto resend, hold for action, don't $update_qty_sent
			'suwp_order_success': successful order submission, get the reference id, $update_qty_sent
			'suwp_connect_fail': possible connection failure, never connected to the API server, don't $update_qty_sent
			**/
		 
			if( empty($suwp_comments) ) {
				// brand new order, create comment entry, update qty sent
				error_log('SUCCESS order submit: $suwp_comments does not exist, create it');
				$create_comment = true;
				$update_qty_sent = true;
			} else {
				error_log('SUCCESS order submit: $suwp_comments exists, use it');
			}
			
			foreach( $suwp_comments as $key => $loop_comments_item_id ):
				$comment_ID = $loop_comments_item_id->comment_ID;
				$comment_post_ID = $loop_comments_item_id->comment_post_ID;
				$comment_type = $loop_comments_item_id->comment_type;
				if( $comment_post_ID === $current_order_id && $comment_type === 'suwp_connect_fail' ) {
					$update_qty_sent = true;
				}
			endforeach; // foreach( $suwp_comments as $key => $loop_comments_item_id )
			
      $comment_msg = $reply['MESSAGE'];
			$time = current_time('mysql');
			$comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
			$email_template_name = 'suwp_order_success';
			
			if ( $create_comment ) {
				
				// create a new comment
				$commentdata = array(
                    'comment_post_ID'       => $current_order_id,
                    'comment_author'        => 'StockUnlocks-php-'.$suwp_dhru_username,
                    'comment_author_email'  => $suwp_dhru_username, // dhru api username
                    'comment_author_url'    => $suwp_dhru_url, // dhru api url
                    'comment_content'       => $comment_content, // contains imei with api message, no longer than 20 chars!!
                    'comment_agent'         => $reply['REFERENCEID'], // $reply['REFERENCEID'] if success, $reply['DESCRIPTION'] if error, or suwp_default_fail
                    'comment_type'          => $email_template_name, // suwp_order_success, suwp_order_error, or suwp_connect_fail
                    'comment_parent'        => 0,
                    'user_id'               => 0,
                    'comment_author_IP'     => $suwp_dhru_api_key, // dhru api access key
                    'comment_date'          => $time,
                    'comment_date_gmt'      => $time,
                    'comment_karma'         => 0,
                    'comment_approved'      => 1,
                );
                
                //Insert new comment and get the comment ID
                $comment_id = wp_insert_comment( $commentdata );
                
			} else {
				
				// update existing comment
				$comment = array();
				$comment['comment_ID'] = $comment_ID;
				$comment['comment_content'] = $comment_content;
				$comment['comment_agent'] = $reply['REFERENCEID']; // NEW v1.8.0, previously used an old reference id, forever blocking a resubmission of the IMEI
				$comment['comment_type'] = $email_template_name;
				$comment['comment_author_IP'] = $suwp_dhru_api_key; // v1.8.0 ; to enable for reprocessing
				$comment['comment_date'] = $time;
				$comment['comment_date_gmt'] = $time;
				$comment_id = wp_update_comment( $comment );
			}
		
			// notify customer and, optionally, Bcc: admin with results 
			if ( suwp_send_cron_recipient_email( $comment_content, $email_template_name ) ) {
			
				error_log("SUCCESS : Place IMEI Order SUCCESS message successfully sent.");
		
			} else {
				// failed to send
				error_log("ERROR : Place IMEI Order SUCCESS message failed to send.");
			}
			
			// NEW: ADD A COMMENT TO THE ACTUAL ORDER ...
			$commentdata = array(
				'comment_post_ID' => $current_order_id, // to which post the comment will show up
				'comment_author' => $blog_title, //fixed value - can be dynamic 
				'comment_author_email' => $admin_email, //fixed value - can be dynamic 
				'comment_author_url' => $website_url, //fixed value - can be dynamic 
				'comment_content' => $comment_msg, //fixed value - can be dynamic
				'comment_approved' => 1,
				'comment_agent' => 'WooCommerce',
				'comment_type' => 'order_note', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
				'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
				'user_id' => get_current_user_id(), //passing current user ID or any predefined as per the demand
			);
			
			//Insert new comment and get the comment ID
			$comment_id = wp_insert_comment( $commentdata );
			
            // increment the '_suwp_qty_sent' in the _woocommerce_order_itemmeta table
			if( $update_qty_sent ) {
				$plugin_admin->suwp_update_qty_sent( $current_order_item_id );
			}
			
            break;
            
        default:
            
            // possible connection failure
            // use this info to recover and resubmit, if necessary
            
			// one API comment per order item
			// if the comment doesn't already exist, create it
			// THIS NEVER WORKED: $suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type=%s AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id, $comment_type , $suwp_dhru_imei_like ) );
			// $suwp_comments = $wpdb->get_results('SELECT comment_ID, comment_post_ID, comment_type FROM `' . $wpdb->prefix . 'comments` WHERE `comment_post_ID`=' . $current_order_id . ' AND NOT `comment_type`=\'order_note\' AND `comment_content` LIKE \'%' . $suwp_dhru_imei . '%\' ORDER BY comment_ID DESC');
			$suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, comment_post_ID, comment_type FROM " . $wpdb->prefix . "comments WHERE comment_post_ID=%d AND NOT comment_type='order_note' AND comment_content LIKE %s ORDER BY comment_ID DESC", $current_order_id , $suwp_dhru_imei_like ) );
			
			$create_comment = false;
			$update_qty_sent = false;
			
			/**
			'suwp_order_error': possible duplicate imei, insufficient funds, etc.; do not auto resend, hold for action, don't $update_qty_sent
			'suwp_order_success': successful order submission, get the reference id, $update_qty_sent
			'suwp_connect_fail': possible connection failure, never connected to the API server, don't $update_qty_sent
			**/
		 
			if( empty($suwp_comments) ) {
				// brand new order, create comment entry, don't update qty sent, connection error
				error_log('DEFAULT order submit: $suwp_comments does not exist, create it');
				$create_comment = true;
				$update_qty_sent = false;
			} else {
				error_log('DEFAULT order submit: $suwp_comments exists, use it');
			}
			
			foreach( $suwp_comments as $key => $loop_comments_item_id ):
				$comment_ID = $loop_comments_item_id->comment_ID;
				$comment_post_ID = $loop_comments_item_id->comment_post_ID;
				$comment_type = $loop_comments_item_id->comment_type;
				if( $comment_post_ID === $current_order_id && $comment_type === 'suwp_connect_fail' ) {
					$update_qty_sent = false;
				}
			endforeach; // foreach( $suwp_comments as $key => $loop_comments_item_id )
			
            $time = current_time('mysql');
            $comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id;
            $email_template_name = 'suwp_connect_fail';
            
			if ( $create_comment ) {
				
				// create a new comment
				// SHOULD RESET, OR LEAVE IT ALONE FOR NEXT RUN??
				$commentdata = array(
					'comment_post_ID'       => $current_order_id,
					'comment_author'        => 'StockUnlocks-php-'.$suwp_dhru_username,
					'comment_author_email'  => $suwp_dhru_username, // dhru api username
					'comment_author_url'    => $suwp_dhru_url, // dhru api url
					'comment_content'       => $comment_content, // contains imei with api message, no longer than 20 chars!!
					'comment_agent'         => $current_order_item_id, // v1.8.0 ; can be used later
					'comment_type'          => $email_template_name, // suwp_order_success, suwp_order_error, or suwp_connect_fail
					'comment_parent'        => 0,
					'user_id'               => 0,
					'comment_author_IP'     => $suwp_dhru_api_key, // dhru api access key
					'comment_date'          => $time,
					'comment_date_gmt'      => $time,
					'comment_karma'         => 0,
					'comment_approved'      => 1,
				);
				
				// insert new comment and get the comment ID
				$comment_id = wp_insert_comment( $commentdata ); // wp_new_comment ; wp_insert_comment
				
			} else {
				
				// update existing comment
				$comment = array();
				$comment['comment_ID'] = $comment_ID;
				$comment['comment_content'] = $comment_content;
				$comment['comment_agent'] = $current_order_item_id; // v1.8.0 ; can be used later
				$comment['comment_type'] = $email_template_name;
				$comment['comment_author_IP'] = $suwp_dhru_api_key; // v1.8.0 ; to enable for reprocessing
				$comment['comment_date'] = $time;
				$comment['comment_date_gmt'] = $time;
				$comment_id = wp_update_comment( $comment );
			}
			
            // only notify admin about results 
            if( suwp_send_cron_recipient_email( $comment_content, $email_template_name ) ) {
                
                error_log("SUCCESS : Place IMEI Order CONNECT FAIL message successfully sent.");
            
            } else {
                // failed to send
                error_log("ERROR : Place IMEI Order CONNECT FAIL message failed to send.");
            }
            
            // ??? ADD A PRIVATE COMMENT TO THE ORDER ...
        }
        
    endforeach; // foreach( $imei_values as $value )
	
	suwp_end:
}    

?>
