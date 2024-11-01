<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_get_mep_list_cron( $post_id ) {
    
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
    
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_mep_list_constants_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_mep_list_constants_' . $post_id . '_cron.php' );

    $meta_value = '_suwp_api_provider';
    
    // MEPs are NOT service dependent. There is a list for every Provider
    // no need to loop through all services for one provider
    
    $api_is_mep = '';
    $reply =  array();
    $services = array();
    $request = array();
    $flag_continue = FALSE;

    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_mep_list_api_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_mep_list_api_' . $post_id . '_cron.php' );  

// UNLOCKBASE .... START
    if ( ($suwp_apitype == '02') && ($api_is_mep == 'RequiredXXX') && ($service_post_status == 'publish') ) {
      /* Call the API */

      $XML = $api->CallAPI('GetToolXXXX', $para );

      if (is_string($XML)) {
          /* Parse the XML stream */
          $Data = $api->ParseXML($XML);

          if (is_array($Data)) {

              // error_log( 'UNLOCKBASE GETMEPLIST (GetToolNetworks) RESPONSE: '. print_r($Data,true) );

              $request = $Data;
              
              if (isset($Data['Error'])) {
                  /* The API has returned an error */
                  // print('API error : ' . htmlspecialchars($Data['Error']));
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
          }
      } else {
          /* Communication error */
          // print('Could not communicate with the api');
      }
    }
// UNLOCKBASE .... END

// GSM FUSION .... START (will = 01; API undocumented, skip)
    if ($suwp_apitype == 'XX') {

      $api->action('meplist', array());
      $api->XmlToArray($api->getResult());
      $arrayData = $api->createArray();

      if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
      {
        // echo '<b>'.$arrayData['error'][0].'</b>';
        // exit;
        
      }
      
      /* STUB EXAMPLE TO BE REPLACED WITH MEP
      if(isset($arrayData['Networks']['Country']) && sizeof($arrayData['Networks']['Country']) > 0) {
        $request = $arrayData['Networks'];
      }
      */

      if( !empty($request) ) {
        
        $flag_continue = TRUE;
        
        $total = count($request);
        $Data = $request;

        // [... foreach ...]
      }
      // error_log( 'GSMFUSIONAPI GET MEP LIST RESPONSE: '. print_r($request,true) );
    }    
// GSM FUSION .... END

// DHRU FUSION .... START
    if ($suwp_apitype == '00') {

      // Debug on
      $api->debug = true;
      
      $request = $api->action('meplist');
    
      // error_log( 'RAW MEP RESPONSE:'. print_r( $request, true  ) );
      
      if (is_array($request)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($request), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $key1 => $val1) {
          if ($key1 === 'LIST') {
            if (is_array($val1)) {
              foreach ($val1 as $key2 => $val2) {
                $tmp_id = $key2;
                if (is_array($val2)) {
                  foreach ($val2 as $key3 => $val3) {
                    if ($key3 === 'NAME') {
                      $flag_continue = TRUE;
                      $tmp_mep_name = $val3;
                      if ($tmp_mep_name != '') {
                        $tmp_key = $tmp_id . '-php-' . $tmp_mep_name;
                        $tmp_key_mep_id_display = $tmp_id;
                        $services[] = array(
                          'ID' => $tmp_key,
                          'MEPID' => $tmp_key_mep_id_display,
                          'MEPNAME' => $tmp_mep_name,
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
// DHRU FUSION .... END

    $item = 0;
    
    if ( $flag_continue ) {
        
        foreach ($services as $service) {
          // Begin extraction at the actual MEP.
          if ((!empty($service['ID']) && ($service['ID'] != NULL))) {
            $reply[] = array(
              'item' => $item,
              'mep_id' => $service['MEPID'],
              'mep_name' => $service['MEPNAME'],
            );
            ++$item;
          }
        }
    }
            
    $plugin_admin->suwp_dhru_create_mep( $post_id, $reply );
    
    return $meta_value;
}

?>
