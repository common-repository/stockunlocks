<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_update_regular_price_cron( $post_id ) {
	
	// since v1.9.3 for APIs that use text for the service id
	$serviceid_alt = NULL;

	$suwp_apitype = get_field('suwp_apitype', $post_id );
	// not yet converted, use the default
	if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
		$suwp_apitype = '00';
	}

	$plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );
	
	$apidetails = $plugin_admin->suwp_dhru_get_provider_array( $post_id );
	
    // get the api details
    $suwp_dhru_url = $apidetails['suwp_dhru_url'];
    $suwp_dhru_username = $apidetails['suwp_dhru_username'];
    $suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
    
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_single_imei_service_details_constants_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_single_imei_service_details_constants_' . $post_id . '_cron.php' );
    
    $suwp_products = array();
    
    global $wpdb;
    global $woocommerce;
 
	$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
	
	// get the default values for our options
	$options = $plugin_public->suwp_exec_get_current_options();
	
    // collect all of the relevant products in order to update prices
    $suwp_products = $wpdb->get_results("select * from ".$wpdb->prefix."postmeta where meta_key='_suwp_api_provider' AND meta_value='". $post_id ."' ORDER BY post_id ASC");
    
	$provider_title = get_post_field( 'post_title', $post_id );
	$suwp_temp_status = get_post_status( $post_id );
	
	error_log('$post_id = ' . $post_id . ' [Provider] - ' . $provider_title . ', Status = ' . $suwp_temp_status );
	error_log('Potential number of Products to process = ' . count( $suwp_products ) );
	
	if ( $suwp_temp_status == 'publish' ) {
		
		// loop over the products, get the info and check the status
		foreach( $suwp_products as $product ):
			
			$product_id = $product->post_id;
			$product_title = get_post_field( 'post_title', $product_id );
			
			$service_credit_current = '';
			$service_credit_new = '';
			$regular_price_current = '';
			$regular_price_new = '';
			$multiplier_custom_enabled = '';
			$multiplier_custom_value = '';
			$multiplier_global_enabled = '';
			$multiplier_global_value = '';
			
			error_log('$product_id = ' . $product_id . ' [Product] - ' . $product_title );

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
				
				$suwp_postmeta = $wpdb->get_results("select meta_value from ".$wpdb->prefix."postmeta where meta_key='_suwp_api_service_id' AND post_id='". $product_id ."'");
				
				// since v1.9.3 the API provider may use a text value for the service API ID
				// if text value is available, use it
				$suwp_postmeta_alt = $wpdb->get_results("select meta_value from ".$wpdb->prefix."postmeta where meta_key='_suwp_api_service_id_alt' AND post_id='". $product_id ."'");
				
				if ( !empty($suwp_postmeta_alt[0]->meta_value) ) {
					$suwp_postmeta = $suwp_postmeta_alt;
				}

				foreach( $suwp_postmeta as $meta_value ):
				
					$suwp_dhru_referenceid = $meta_value->meta_value;
					
					error_log( '_postmeta table meta_value: '. $suwp_dhru_referenceid . ' [API ID from service Provider]' );
					
					// >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_single_imei_service_details_api_' . $post_id . '_cron.php' );
					include( SUWP_TEMP . 'suwp_get_single_imei_service_details_api_' . $post_id . '_cron.php' );
					
					// Debug on
					$api->debug = true;
				
					$reply =  array();
					$para =  array();
					$services = array();
					$flag_continue = FALSE;
					$request = array();

// UNLOCKBASE .... START
					 if ( $suwp_apitype == '02' ) {
					 
						$para['ID'] = $suwp_dhru_referenceid;

						 /* Call the API */
						 $XML = $api->CallAPI('GetTools');
					 
						 $request = array();
					 
						 if (is_string($XML)) {
								 /* Parse the XML stream */
								 $Data = $api->ParseXML($XML);
					 
								 if (is_array($Data)) {
										 $request = $Data;
										 if (isset($Data['Error'])) {
												 /* The API has returned an error */
												 // print('API error : ' . htmlspecialchars($Data['Error']));
												 $services = array( 'RESULT' => 'ERROR', 'MESSAGE' => htmlspecialchars($Data['Error']) );

										 } else {
												 /* Everything works fine */
												 $flag_continue = TRUE;
												 // error_log( 'UNLOCKBASE-UPDATE REGULAR PRICE CRON - SERVICE DETAILS: '. print_r($Data,true) );
					 
												 foreach ($Data['Group'] as $Group) {
														 foreach ($Group['Tool'] as $Tool) {
															 
															if ( $suwp_dhru_referenceid === $Tool['ID'] ) {

																// error_log( 'UNLOCKBASE-UPDATE FOUND MATCHING SERVICE, DETAILS: name = '. $Tool['Name'] . ', ID = ' . $Tool['ID'] );
						
																$time_taken = '';
																$info = '';
																$assigned_network = '';
																$message = '';
																
																if( array_key_exists('Delivery.Min', $Tool) ) {
																	if( array_key_exists('Delivery.Max', $Tool) ) {
																		if( array_key_exists('Delivery.Unit', $Tool) ) {
																			$time_taken = $Tool['Delivery.Min'] . '-' . $Tool['Delivery.Max'] . ' ' . $Tool['Delivery.Unit'];
																		}
																	}
																} else {
																	if( array_key_exists('Delivery.Max', $Tool) ) {
																		if( array_key_exists('Delivery.Unit', $Tool) ) {
																			$time_taken = $Tool['Delivery.Max'] . ' ' . $Tool['Delivery.Unit'];
																		}
																	} else {
																		if( array_key_exists('Delivery.Unit', $Tool) ) {
																			$time_taken = $Tool['Delivery.Unit'];
																		}
																	}
																}
						
																if( !empty($Tool['Network'])  && array_key_exists('Network', $Tool) ) {
																	$assigned_network = $Tool['Network'];
																}
						
																if( !empty($Tool['Message'])  && array_key_exists('Message', $Tool) ) {
																	$message = htmlspecialchars( strip_tags( str_replace( '</p>', chr(10) , $Tool['Message']) ) );
																}
																
																$services = array(
																	'RESULT' => 'SUCCESS',
																	'SERVICEID' => $Tool['ID'],
																	'SERVICEIDALT' => $serviceid_alt,
																	'USERNAME' => '',
																	'SERVICENAME' => $Tool['Name'],
																	'ID' => $Tool['ID'],
																	'ASSIGNEDBRAND' => '',
																	'MODEL' => $Tool['Requires.Mobile'],
																	'PROVIDER' => $Tool['Requires.Provider'],
																	'MEP' => $Tool['Requires.MEP'],
																	'TYPE' => '',
																	'CREDIT' => $Tool['Credits'],
																);

																/*
																	$services[] = array(
																		'GROUPID' => $Group['ID'],
																		'ID' => $Tool['ID'],
																		'ID_DISPLAY' =>  $Tool['ID'],
																		'NETWORKPROVIDER' => $assigned_network,
																		'GROUPNAME' => $Group['Name'],
																		'SERVICENAME' => $Tool['Name'],
																		'INFO' => $message,
																		'Requires.Network' => $Tool['Requires.Network'],
																		'Requires.Mobile' => $Tool['Requires.Mobile'],
																		'Requires.Provider' => $Tool['Requires.Provider'],
																		'Requires.PIN' => $Tool['Requires.PIN'],
																		'Requires.KBH' => $Tool['Requires.KBH'],
																		'Requires.MEP' =>  $Tool['Requires.MEP'],
																		'Requires.PRD' => $Tool['Requires.PRD'],
																		'Requires.Type' => $Tool['Requires.Type'],
																		'Requires.Locks' => $Tool['Requires.Locks'],
																		'Requires.Reference' => $Tool['Requires.Reference'],
																		'CREDIT' => $Tool['Credits'],
																		'TIME' => $time_taken,
																	);
																	
																*/

															break 2;
														}
												 }
											 }
										 }
								 } else {
										 $flag_continue = FALSE;
										 /* Parsing error */
										 // print('Could not parse the XML stream');
										 $services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'Could not parse the XML stream' );
								 }
						 } else {
								 $flag_continue = FALSE;
								 /* Communication error */
								 // print('Could not communicate with the api');
								 $services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'UNLOCKBASE - Could not communicate with the api' );
						 }
					 }
// UNLOCKBASE .... END

// GSM FUSION .... START
					if ( $suwp_apitype == '01' ) {
						
						$para['ID'] = $suwp_dhru_referenceid;

						$api->action( 'imeiservices',  array() );
						$api->XmlToArray($api->getResult());
						$arrayData = $api->createArray();

						if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
						{
							// >>> echo '<b>'.$arrayData['error'][0].'</b>';
							// >>> exit;
							$services = array( 'RESULT' => 'ERROR', 'MESSAGE' => $arrayData['error'][0] );
							$flag_continue = FALSE;
						}
						
						// error_log( 'GSMFUSION-UPDATE REGULAR PRICE CRON - SERVICE RESULTS: '. print_r($arrayData,true) );
						
						$request = array();
						if(isset($arrayData['Packages']['Package']) && sizeof($arrayData['Packages']['Package']) > 0) {

							$request = $arrayData['Packages']['Package'];
							$flag_continue = TRUE;
						}

						if ( $flag_continue ) {

							$total = count($request);

							for($count = 0; $count < $total; $count++)
							{
								
								$package_id = '';
								$category = '';
								$category_id = '';
								$package_title = '';
								$package_price = '';
								$time_taken = '';
								$must_read = '';

								if( !empty($request[$count]['PackageId'])  && array_key_exists('PackageId', $request[$count]) ) {
									$package_id = $request[$count]['PackageId'];
								}

								if ( $suwp_dhru_referenceid === $package_id ) {

									if( !empty($request[$count]['Category'])  && array_key_exists('Category', $request[$count]) ) {
										$category = $request[$count]['Category'];
									}
									if( !empty($request[$count]['CategoryId'])  && array_key_exists('CategoryId', $request[$count]) ) {
										$category_id = $request[$count]['CategoryId'];
									}
									if( !empty($request[$count]['PackageTitle'])  && array_key_exists('PackageTitle', $request[$count]) ) {
										$package_title = $request[$count]['PackageTitle'];
									}
									if( !empty($request[$count]['PackagePrice'])  && array_key_exists('PackagePrice', $request[$count]) ) {
										$package_price = $request[$count]['PackagePrice'];
									}
									if( !empty($request[$count]['TimeTaken'])  && array_key_exists('TimeTaken', $request[$count]) ) {
										$time_taken = $request[$count]['TimeTaken'];
									}
									if( !empty($request[$count]['MustRead'])  && array_key_exists('MustRead', $request[$count]) ) {
										$must_read = htmlspecialchars( strip_tags( str_replace('</p>', chr(10) , $request[$count]['MustRead']) ) );
									}
									
									// error_log( 'GSMFUSION-UPDATE FOUND MATCHING SERVICE, DETAILS: name = '. $package_title . ', ID = ' . $package_id );
									
									$services = array(
										'RESULT' => 'SUCCESS',
										'SERVICEID' => $package_id,
										'SERVICEIDALT' => $serviceid_alt,
										'USERNAME' => '',
										'SERVICENAME' => $package_title,
										'ID' => $package_id,
										'ASSIGNEDBRAND' => 'None',
										'MODEL' => 'None',
										'PROVIDER' => 'None',
										'MEP' => 'None',
										'TYPE' => '',
										'CREDIT' => $package_price,
									);
									
									break;
								}
							}
							
						} else {

							// could not connect to API server, probably due to failed internet connection
							$services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'GSM FUSION - Could not communicate with the api' );

						}
						
					}
// GSM FUSION .... END

// UNLOCKINGPORTAL .... START
					if ( $suwp_apitype == '03' ) {
											
						$para['ID'] = $suwp_dhru_referenceid;

						$api->action( 'getpackages',  array() );
						$api->XmlToArray($api->getResult());
						$arrayData = $api->createArray();

						if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
						{
							// >>> echo '<b>'.$arrayData['error'][0].'</b>';
							// >>> exit;
							$services = array( 'RESULT' => 'ERROR', 'MESSAGE' => $arrayData['error'][0] );
							$flag_continue = FALSE;
						}
						
						// error_log( 'UNLOCKINGPORTAL-UPDATE REGULAR PRICE CRON - SERVICE RESULTS: '. print_r($arrayData,true) );
						
						$request = array();
						if(isset($arrayData['Packages']['Package']) && sizeof($arrayData['Packages']['Package']) > 0) {

							$request = $arrayData['Packages']['Package'];
							$flag_continue = TRUE;
						}

						if ( $flag_continue ) {

							$total = count($request);

							for($count = 0; $count < $total; $count++)
							{
								
								$package_id = '';
								$category = '';
								$category_id = '';
								$package_title = '';
								$package_price = '';
								$time_taken = '';
								$must_read = '';

								if( !empty($request[$count]['PackageId'])  && array_key_exists('PackageId', $request[$count]) ) {
									$package_id = $request[$count]['PackageId'];
								}

								if ( $suwp_dhru_referenceid === $package_id ) {

									if( !empty($request[$count]['Category'])  && array_key_exists('Category', $request[$count]) ) {
										$category = $request[$count]['Category'];
									}
									if( !empty($request[$count]['CategoryId'])  && array_key_exists('CategoryId', $request[$count]) ) {
										$category_id = $request[$count]['CategoryId'];
									}
									if( !empty($request[$count]['PackageTitle'])  && array_key_exists('PackageTitle', $request[$count]) ) {
										$package_title = $request[$count]['PackageTitle'];
									}
									if( !empty($request[$count]['PackagePrice'])  && array_key_exists('PackagePrice', $request[$count]) ) {
										$package_price = $request[$count]['PackagePrice'];
									}
									if( !empty($request[$count]['TimeTaken'])  && array_key_exists('TimeTaken', $request[$count]) ) {
										$time_taken = $request[$count]['TimeTaken'];
									}
									if( !empty($request[$count]['MustRead'])  && array_key_exists('MustRead', $request[$count]) ) {
										$must_read = htmlspecialchars( strip_tags( str_replace('</p>', chr(10) , $request[$count]['MustRead']) ) );
									}
									
									// error_log( 'UNLOCKINGPORTAL-UPDATE FOUND MATCHING SERVICE, DETAILS: name = '. $package_title . ', ID = ' . $package_id );
									
									$services = array(
										'RESULT' => 'SUCCESS',
										'SERVICEID' => $package_id,
										'SERVICEIDALT' => $serviceid_alt,
										'USERNAME' => '',
										'SERVICENAME' => $package_title,
										'ID' => $package_id,
										'ASSIGNEDBRAND' => 'None',
										'MODEL' => 'None',
										'PROVIDER' => 'None',
										'MEP' => 'None',
										'TYPE' => '',
										'CREDIT' => $package_price,
									);
									
									break;
								}
							}
							
						} else {

							// could not connect to API server, probably due to failed internet connection
							$services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'UNLOCKINGPORTAL - Could not communicate with the api' );

						}
						
					}
// UNLOCKINGPORTAL .... END

// IPHONEADMIN .... START
				if ($suwp_apitype == '04') {
					
					// execute the request
					$request = $api->action([
						'API_ID' => $suwp_dhru_username,
						'API_KEY' => $suwp_dhru_api_key,
						'SERVICELIST' => $suwp_dhru_referenceid,
						// 'UNIQUE_KEY'   => $suwp_unique_key,
						// 'SERVICE'   => $suwp_dhru_serviceid_alt,
						// 'NOTIFY_URL'   => get_site_url(),
						// 'DEVICES'   => $devices,
					]);
					
					// error_log( 'IPHONEADMIN-UPDATE REGULAR PRICE CRON - SERVICE RESULTS FOR: ID = ' . $suwp_dhru_referenceid . '; ' . print_r($request,true) );
					
					/**
					[RESULT] => 1
					[MESSAGE] => Request Placed & processed Successfully!
					[SERVICELIST] => Array
					*/
					
					if (is_array($request)) {
						
						if( $request['RESULT'] && array_key_exists('SERVICELIST', $request) && !empty($request['SERVICELIST']) ) {
						
						// error_log( 'IPHONEADMIN-UPDATE REGULAR PRICE CRON ALL IMEI SERVICES: '. print_r($request['SERVICELIST'],true) );
						$flag_continue = TRUE;
						$total = count($request['SERVICELIST']);
						// error_log( 'IPHONEADMIN-UPDATE REGULAR PRICE CRON COUNT IMEI SERVICES: '. print_r($total,true) );
						
						$service_list = array();
						$service_list = $request['SERVICELIST'];
					
						for($count = 0; $count < $total; $count++) {
					
							// error_log( 'IPHONEADMIN-UPDATE REGULAR PRICE CRON COUNT++ = '. print_r($count,true) );
						
							$serviceid = '';
							$serviceid_alt = '';
							$username = '';
							$service_name = '';
							$assigned_brand = '';
							$model = '';
							$provider = '';
							$mep = '';
							$type = '';
							$credit = '';
							
							/** example entry
							[TITLE] => iPhone Case History Check
							[SERVICEID] => CaseHistory
							[INPUTTYPE] => 
							[PRICE] => 3.00 USD
							[DESCRIPTION] => This is service is manually processed
							[DELIVERYTIME] => 1-24 Hours
							*/
					
							if( !empty($service_list[$count]['ID'])  && array_key_exists('ID', $service_list[$count]) ) {
								$serviceid = $service_list[$count]['ID'];
							} else {
								$serviceid = $count+1; // 0 will not store as an ID
							}
							if( !empty($service_list[$count]['SERVICEID'])  && array_key_exists('SERVICEID', $service_list[$count]) ) {
								$serviceid_alt = $service_list[$count]['SERVICEID'];
							}
							if( !empty($service_list[$count]['USERNAME'])  && array_key_exists('USERNAME', $service_list[$count]) ) {
								$username = $service_list[$count]['USERNAME'];
							}
							if( !empty($service_list[$count]['TITLE'])  && array_key_exists('TITLE', $service_list[$count]) ) {
								$service_name = $service_list[$count]['TITLE'];
							}
							if( !empty($service_list[$count]['ASSIGNEDBRAND'])  && array_key_exists('ASSIGNEDBRAND', $service_list[$count]) ) {
								$assigned_brand = $service_list[$count]['ASSIGNEDBRAND'];
							}
							if( !empty($service_list[$count]['MODEL'])  && array_key_exists('MODEL', $service_list[$count]) ) {
								$model = $service_list[$count]['MODEL'];
							}
							if( !empty($service_list[$count]['PROVIDER'])  && array_key_exists('PROVIDER', $service_list[$count]) ) {
								$provider = $service_list[$count]['PROVIDER'];
							}
							if( !empty($service_list[$count]['MEP'])  && array_key_exists('MEP', $service_list[$count]) ) {
								$mep = $service_list[$count]['MEP'];
							}
							if( !empty($service_list[$count]['TYPE'])  && array_key_exists('TYPE', $service_list[$count]) ) {
								$type = $service_list[$count]['TYPE'];
							}
							if( !empty($service_list[$count]['PRICE'])  && array_key_exists('PRICE', $service_list[$count]) ) {
								$credit = trim( str_replace("usd","", strtolower($service_list[$count]['PRICE'])) );
							}
						
							// error_log( 'IPHONEADMIN-UPDATE REGULAR PRICE CRON FOUND MATCHING SERVICE, DETAILS: name = '. $service_name . ', ID = ' . $serviceid );
							
							$services = array(
								'RESULT' => 'SUCCESS',
								'SERVICEID' => $serviceid,
								'SERVICEIDALT' => $serviceid_alt,
								'USERNAME' => $username,
								'SERVICENAME' => $service_name,
								'ID' => $serviceid,
								'ASSIGNEDBRAND' => $assigned_brand,
								'MODEL' => $model,
								'PROVIDER' => $provider,
								'MEP' => $mep,
								'TYPE' => $type,
								'CREDIT' => $credit,
							);
					
						}
					
						} else {
					
							// could not connect to API server, probably due to failed internet connection
							$services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'IPHONEADMIN - Could not parse the returned api values for ID: ' . $suwp_dhru_referenceid );
					
						}
						
					} else {
					
						// could not connect to API server, probably due to failed internet connection
						$services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'IPHONEADMIN - Could not communicate with the api for ID: ' . $suwp_dhru_referenceid );

					}
				}
// IPHONEADMIN .... END


// DHRU FUSION .... START
					if ( $suwp_apitype == '00' ) {

						$para['ID'] = $suwp_dhru_referenceid;
						$request = $api->action('getimeiservicedetails', $para);

						// error_log('DHRU-UPDATE REFERENCE ID = ' . print_r($para['ID'], true) );
						// error_log('DHRU-UPDATE REGULAR PRICE $request RESULTS = ' . print_r($request, true) );
						
						if (is_array($request)) {
							$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($request), RecursiveIteratorIterator::SELF_FIRST);
							foreach ($iterator as $key1 => $val1) {
								if ($key1 === 'ID') {
									$tmp_serviceid = $val1;
								}
								if ($key1 === 'SUCCESS' || $key1 === 'ERROR') {
									$flag_continue = TRUE;
									$tmp_result = $key1;
									if (is_array($val1)) {
									foreach ($val1 as $key2 => $val2) {
										if (is_array($val2)) {
										$tmp_msg = '';
										foreach ($val2 as $key3 => $val3) {
											if ($key3 === 'MESSAGE') {
											$tmp_msg = $val3;
											}
											if ($key3 === 'LIST') {
											if (is_array($val3)) {
												$username = '';
												$service_name = '';
												$credit = '';
												$id = '';
												$model = '';
												$assigned_brand = '';
												$provider = '';
												$mep = '';
												$assigned_model = '';
												$assigned_provider = '';
												$type = '';
												foreach ($val3 as $key4 => $val4) {
												switch ($key4) {
													case 'username':
													$username = $val4;
													break;
							
													case 'service_name':
													$service_name = $val4;
													break;
							
													case 'credit':
													$credit = $val4;
													break;
							
													case 'purchase_cost':
													$purchase_cost = $val4;
													break;
							
													case 'id':
													$id = $val4;
													break;
							
													case 'API':
													$id_api = $val4;
													break;
							
													case 'assigned_model':
													$assigned_model = $val4;
													break;
							
													case 'assigned_provider':
													$assigned_provider = $val4;
													break;
							
													case 'type':
													$type = $val4;
													break;
							
													case 'listing':
													$listing = $val4;
													break;
							
													case 'notification_mail':
													$notification_mail = $val4;
													break;
							
												}

												if (is_array($val4)) {
													foreach ($val4 as $key5 => $val5) {
													switch ($key5) {
														case 'assigned_brand':
														$assigned_brand = $val5;
														break;
														case 'model':

														switch ($val5) {
															case '0':
															$model = 'None';
															break;
															case '1':
															$model = 'Required';
															break;
														}

														break;
														case 'provider':

														switch ($val5) {
															case '0':
															$provider = 'None';
															break;
															case '1':
															$provider = 'Required';
															break;
														}

														break;
														case 'mep':

														switch ($val5) {
															case '0':
															$mep = 'None';
															break;
															case '1':
															$mep = 'Required';
															break;
														}

														break;
														
													}
													}
												}
												}
											}
											}
										}
							
										switch ($tmp_result) {
											case 'ERROR':
											$services = array('RESULT' => $tmp_result, 'MESSAGE' => 'DHRU FUSION - ' . $tmp_msg);
											break;
											case 'SUCCESS':
											$services = array(
												'RESULT' => $tmp_result,
												'SERVICEID' => $tmp_serviceid,
												'SERVICEIDALT' => $serviceid_alt,
												'USERNAME' => $username,
												'SERVICENAME' => $service_name,
												'ID' => $id,
												'ASSIGNEDBRAND' => $assigned_brand,
												'MODEL' => $model,
												'PROVIDER' => $provider,
												'MEP' => $mep,
												'TYPE' => $type,
												'CREDIT' => $credit,
											);
											break;
							
										}
										}
									}
									}
								}
							}
						}
					}
// DHRU FUSION .... END
				   
				   if( array_key_exists( 'RESULT', $services ) ) {
				   
					   switch ($services['RESULT']) {
						   
						 case 'ERROR':
						   error_log( 'cron: auto update product regular price = ERROR' );
						   error_log( 'MESSAGE: ' . $services['MESSAGE'] );
						   
						   break;
					   
						 case 'SUCCESS':
						   error_log( 'cron: auto update product regular price = SUCCESS' );
						   
						   $service_credit = $services['CREDIT'];
						   $model_val = $services['MODEL'];
						   $network_val = $services['PROVIDER'];
						   $mep_val = $services['MEP'];
						   
						   // check if this product is to be updated
						   
						   // update existing product, check settings for adjusting Product Regular price
						   // is this product enabled for custom price adjustment?
						   $price_adj = 'NOT YET SET';
						   $price_adj_val = get_post_meta( $product_id, '_suwp_price_adj', true );
						   
						   // Check if the custom field is available.
						   if ( !empty( $price_adj_val ) ) {
							   $price_adj = $price_adj_val;
						   }
						   
						   $price = get_post_meta( $product_id, '_regular_price', true );
						   
						   $service_credit_current = get_post_meta( $product_id, '_suwp_service_credit', true );
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
							 
							// 1.9.2: added when upgrading from basic to pro
							$is_model_val = get_post_meta( $product_id, '_suwp_is_model', true );
							$is_network_val = get_post_meta( $product_id, '_suwp_is_network', true );
							$is_mep_val = get_post_meta( $product_id, '_suwp_is_mep', true );
							if ( $model_val != $is_model_val ) {
							update_post_meta( $product_id, '_suwp_is_model', $model_val );
							}
							if ( $network_val != $is_network_val ) {
							update_post_meta( $product_id, '_suwp_is_network', $network_val );
							}
							if ( $mep_val != $is_mep_val ) {
							update_post_meta( $product_id, '_suwp_is_mep', $mep_val );
							}

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
						   
						   break;
					   
					   } // switch ($reply['RESULT'])
				   }
				   
				   error_log( '' );
				   
			   endforeach; // foreach( $suwp_postmeta as $meta_value )
			   
		   } else {
			   
			   error_log( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> AUTO UPDATE: NOT A REMOTE SERVICE, NOT AUTO UPDATING.' );
		   }
		   
		endforeach; // foreach( $suwp_products as $product )
		
    } else {
		
		// Even though set to "Active", this provider is not published, don't auto update
		error_log( '>>>>>>>>>>>>>> AUTO UPDATE: ALTHOUGH SET TO Active, CANNOT PROCESS BECAUSE POST IS NOT PUBLISHED <<<<<<<<<<<<<< ' );
	} // if ( $suwp_temp_status != 'publish' )
	
}

?>
