<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_get_provider_list_cron( $post_id ) {
    
    global $wpdb;
    
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
    
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_provider_list_constants_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_provider_list_constants_' . $post_id . '_cron.php' );

    $meta_value = '_suwp_api_provider';
    
    // collect all services related to this Provider
    $suwp_services = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM " . $wpdb->prefix. "postmeta WHERE meta_key=%s AND meta_value=%d ORDER BY post_id ASC", $meta_value, $post_id ) );
    
    // loop over the services, get the countries/networks, if any
    foreach( $suwp_services as $suwp_service ):
    
        $service_post_id = $suwp_service->post_id;
        $service_post_status = get_post_status( $service_post_id );
        
        // get the API id for this service
        $api_service_id = get_post_meta( $service_post_id, '_suwp_api_service_id', true );
        // ... is it enabled for network provider? None/Required
        $api_is_network = get_post_meta( $service_post_id, '_suwp_is_network', true );
        
        $para = array();
        $para['ID'] = '';
        $reply =  array();
        $services = array();
        $request = array();
        $flag_continue = FALSE;
        
        $para['ID'] = $api_service_id; // NULL = GETS ALL
        
        // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_provider_list_api_' . $post_id . '_cron.php' );
        include( SUWP_TEMP . 'suwp_get_provider_list_api_' . $post_id . '_cron.php' );

// UNLOCKBASE .... START
        if ( ($suwp_apitype == '02') && ($api_is_network == 'Required') && ($service_post_status == 'publish') ) {

          /* Call the API */

          $XML = $api->CallAPI('GetToolNetworks', $para );

          if (is_string($XML)) {
              /* Parse the XML stream */
              $Data = $api->ParseXML($XML);

              if (is_array($Data)) {

                  // error_log( 'UNLOCKBASE GETPROVIDERLIST (GetToolNetworks) RESPONSE: '. print_r($Data,true) );

                  $request = $Data;
                  
                  if (isset($Data['Error'])) {
                      /* The API has returned an error */
                      // print('API error : ' . htmlspecialchars($Data['Error']));
                      // error_log( 'ERROR - UNLOCKBASE GETPROVIDERLIST (GetToolNetworks) RESPONSE: '. htmlspecialchars($Data['Error']) );
                  } else {
                      /* Everything works fine */

                      $flag_continue = TRUE;
                      foreach ($Data['Network'] as $Network) {
                          
                          $country_id = strrev( $Network['Country'] );
                          
                          if ( strlen($country_id) < 4 ) {
                              $country_id = $country_id . 'abcd';
                          }

                          $country_id = preg_replace('/\s+/', '', $country_id);
                          $country_id = substr($country_id, -3) . $country_id;
                          $ar = unpack("C*", $country_id);
                          $tmp_id = ($ar[1]<<24) + ($ar[2]<<16) + ($ar[3]<<8) + $ar[4];
                          
                          $tmp_name = $Network['Country'];
                          $tmp_providername_id = $Network['ID'];
                          $tmp_providername =  $Network['Name'];
                          $tmp_serviceid = $para['ID'];

                          $tmp_key = $tmp_id . '-php-' . $tmp_name . '-php-' . $tmp_providername_id . '-php-' . $tmp_providername;
                          $tmp_key_provider_id_display = $tmp_providername_id;
                          $services[] = array(
                              'ID' => $tmp_key,
                              'COUNTRYID' => $tmp_id,
                              'PROVIDERID' => $tmp_key_provider_id_display,
                              'COUNTRYNAME' => $tmp_name,
                              'PROVIDERNAME' => $tmp_providername,
                              'SERVICEID' => $tmp_serviceid,
                          );

                      }
                  }
              } else {
                  /* Parsing error */
                  // print('Could not parse the XML stream');
                  // error_log( 'ERROR - UNLOCKBASE GETPROVIDERLIST (GetToolNetworks), Could not parse the XML stream' );
              }
          } else {
              /* Communication error */
              // print('Could not communicate with the api');
              // error_log( 'ERROR - UNLOCKBASE GETPROVIDERLIST (GetToolNetworks), Could not communicate with the api' );
          }
        }
// UNLOCKBASE .... END

// GSM FUSION .... START
// ! not documented in GSM Fusion API
// ... how to connect service with Network Provider ...
// check to see if published and enabled for network, then run it
        if ( ( $suwp_apitype == '01' || $suwp_apitype == '03' ) && ($api_is_network == 'Required') && ($service_post_status == 'publish') ) {
             
          $api->action('getproviders', array());
          $api->XmlToArray($api->getResult());
          $arrayData = $api->createArray();

          if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
          {
            // echo '<b>'.$arrayData['error'][0].'</b>';
            // exit;
          }

          if(isset($arrayData['Networks']['Country']) && sizeof($arrayData['Networks']['Country']) > 0) {
            $request = $arrayData['Networks'];
          }
          
          if( !empty($request) ) {
            
            $flag_continue = TRUE;

            // ! not documented with GSM Fusion API
            // ... how to connect service with Network Providers ...
            // this currently associates with ALL GSM Fusion services
            $tmp_serviceid = $api_service_id;

            $total = count($request);
            $Data = $request;
            
            foreach ($Data['Country'] as $Country)
            {
              // $Country['Name']
              // $Country['ID']
              
              foreach ($Country['Network'] as $Network)
              {
                // $Network['Name']
                // $Network['ID']

                $tmp_id = $Country['ID'];
                $tmp_name = $Country['Name'];
                $tmp_providername_id = $Network['ID'];
                $tmp_providername = $Network['Name'];

                $tmp_key = $tmp_id . '-php-' . $tmp_name . '-php-' . $tmp_providername_id . '-php-' . $tmp_providername;
                $services[] = array(
                  'ID' => $tmp_key,
                  'COUNTRYID' => $tmp_id,
                  'PROVIDERID' => $tmp_providername_id,
                  'COUNTRYNAME' => $tmp_name,
                  'PROVIDERNAME' => $tmp_providername,
                  'SERVICEID' => $tmp_serviceid,
                );

              }
              
            }
          }
          // error_log( 'GSMFUSIONAPI GET PROVIDER LIST RESPONSE: '. print_r($request,true) );
        }
// GSM FUSION .... END

// DHRU FUSION .... START
        if ( ($suwp_apitype == '00') && ($api_is_network == 'Required') && ($service_post_status == 'publish') ) {

          // Debug on
          $api->debug = true;
          
          $request = $api->action('providerlist', $para);
          
          if (is_array($request)) {
              // error_log( ' NETWORK PROVIDER RAW RESULTS $request = ' . print_r( $request, true) );
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($request), RecursiveIteratorIterator::SELF_FIRST);
            $tmp_serviceid = '';
            foreach ($iterator as $key1 => $val1) {
              $tmp_succ_result = '';
              $tmp_succ_msg = '';
              $tmp_err_result = '';
              $tmp_err_msg = '';
              if ($key1 === 'ID') {
                $tmp_serviceid = $val1;
              }
              if ($key1 === 'ERROR') {
                $tmp_err_result = $key1;
                $tmp_err_msg = "No Parameter 'ID' submitted, returning all Country and Provider List";
              }
              if ($key1 === 'SUCCESS') {
                $tmp_succ_result = $key1;
                $tmp_succ_msg = 'Country and Provider List';
              }
              if ($key1 === 'LIST') {
                if (is_array($val1)) {
                  foreach ($val1 as $key2 => $val2) {
                    $tmp_id = $key2;
                    if (is_array($val2)) {
                      foreach ($val2 as $key3 => $val3) {
                        if ($key3 === 'NAME') {
                          $tmp_name = $val3;
                        }
                        if ($key3 === 'PROVIDERS') {
                          $flag_continue = TRUE;
                          if (is_array($val3)) {
                            foreach ($val3 as $key4 => $val4) {
                              if (is_array($val4)) {
                                $tmp_providername_id = '';
                                $tmp_providername = '';
                                foreach ($val4 as $key5 => $val5) {
                                  if ($key5 === 'ID') {
                                    $tmp_providername_id = $val5;
                                  }
                                  if ($key5 === 'NAME') {
                                    $tmp_providername = $val5;
                                  }
                                  if ($tmp_providername != '') {
                                    $tmp_key = $tmp_id . '-php-' . $tmp_name . '-php-' . $tmp_providername_id . '-php-' . $tmp_providername;
                                    $tmp_key_provider_id_display = $tmp_providername_id;
                                    $services[] = array(
                                      'ID' => $tmp_key,
                                      'COUNTRYID' => $tmp_id,
                                      'PROVIDERID' => $tmp_key_provider_id_display,
                                      'COUNTRYNAME' => $tmp_name,
                                      'PROVIDERNAME' => $tmp_providername,
                                      'SERVICEID' => $tmp_serviceid,
                                    );
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
// DHRU FUSION .... END

        $item = 0;
            
        if ( $flag_continue ) {
    
            foreach ( $services as $service ) {
              // Begin extraction at the actual network provider.
              if ( ( !empty( $service['ID'] ) && ( $service['ID'] != NULL ) ) ) {
                $reply[] = array(
                  'item' => $item,
                  'country_id' => $service['COUNTRYID'],
                  'network_id' => $service['PROVIDERID'],
                  'country_name' => $service['COUNTRYNAME'],
                  'network_name' => $service['PROVIDERNAME'],
                  'service_id' => $service['SERVICEID'],
                );
                ++$item;
              }
            }
          
        }
                
        $plugin_admin->suwp_dhru_create_countrynetwork( $post_id, $service_post_id, $reply );
        
    endforeach; // foreach( $suwp_services as $service )
    
    return $meta_value;
}

?>
