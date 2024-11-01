<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_get_model_list_cron( $post_id ) {
    
    global $wpdb;
    $plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );

    $apidetails = $plugin_admin->suwp_dhru_get_provider_array( $post_id );
    
    $suwp_apitype = get_field('suwp_apitype', $post_id );
    // not yet converted, use the default
    if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
      $suwp_apitype = '00';
    }

    // get the api details
    $suwp_dhru_url = $apidetails['suwp_dhru_url'];
    $suwp_dhru_username = $apidetails['suwp_dhru_username'];
    $suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
    
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_model_list_constants_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_model_list_constants_' . $post_id . '_cron.php' );

    $meta_value = '_suwp_api_provider';
    
    // collect all services related to this Provider ... make sure they're pubished
    $suwp_services = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM " . $wpdb->prefix. "postmeta WHERE meta_key=%s AND meta_value=%d ORDER BY post_id ASC", $meta_value, $post_id ) );
    
    // loop over the services, get the brand/models, if any
    foreach( $suwp_services as $suwp_service ):
    
        $service_post_id = $suwp_service->post_id;
        $service_post_status = get_post_status( $service_post_id );

        // get the API id for this service
        $api_service_id = get_post_meta( $service_post_id, '_suwp_api_service_id', true );
        // ... is it enabled for brand/model? None/Required
        $api_is_model = get_post_meta( $service_post_id, '_suwp_is_model', true );

        $reply =  array();
        $para = array();
        $flag_continue = FALSE;
        $para['ID'] = '';
        $reply =  array();
        $services = array();
        $request = array();
        
        $para['ID'] = $api_service_id; // '212' ; NULL = GETS ALL; got from 'imeiservicelist' [SERVICEID]
        
        // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_model_list_api_' . $post_id . '_cron.php' );
        include( SUWP_TEMP . 'suwp_get_model_list_api_' . $post_id . '_cron.php' );

// UNLOCKBASE .... START
        if ( ($suwp_apitype == '02') && ($api_is_model == 'Required') && ($service_post_status == 'publish') ) {

          /* Call the API */

          $XML = $api->CallAPI('GetToolMobiles', array('ID' => $para['ID']));

          if (is_string($XML)) {
              /* Parse the XML stream */
              $Data = $api->ParseXML($XML);

              if (is_array($Data)) {

                  // error_log( 'UNLOCKBASE GETMODELLIST (GetToolMobiles) RESPONSE: '. print_r($Data,true) );

                  $request = $Data;
                  
                  if (isset($Data['Error'])) {
                      /* The API has returned an error */
                      // print('API error : ' . htmlspecialchars($Data['Error']));
                  } else {
                      /* Everything works fine */
                      $flag_continue = TRUE;
                      foreach ($Data['Mobile'] as $Mobile) {
                          
                          $brand_id = strrev( $Mobile['Brand'] );

                          if ( strlen($brand_id) < 4 ) {
                              $brand_id = $brand_id . 'abcd';
                          }

                          $brand_id = preg_replace('/\s+/', '', $brand_id);
                          $brand_id = substr($brand_id, -3) . $brand_id;
                          $ar = unpack("C*", $brand_id);
                          $tmp_id = ($ar[1]<<24) + ($ar[2]<<16) + ($ar[3]<<8) + $ar[4];
                          
                          $tmp_name = $Mobile['Brand'];
                          $tmp_model_id = $Mobile['ID'];
                          $tmp_model_name =  $Mobile['Name'];

                          $tmp_key = $tmp_id . '-php-' . $tmp_name . '-php-' . $tmp_model_id . '-php-' . $tmp_model_name;
                          $services[] = array(
                          'ID' => $tmp_key,
                          'BRANDID' => $tmp_id,
                          'MODELID' => $tmp_model_id,
                          'BRANDNAME' => $tmp_name,
                          'MODELNAME' => $tmp_model_name,
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

// GSM FUSION .... START
// ! not documented in GSM Fusion API
// ... how to connect service with Brand/Model ...
// check to see if published and enabled for brand/model, then run it
        if ( ( $suwp_apitype == '01' || $suwp_apitype == '03' ) && ($api_is_model == 'Required') && ($service_post_status == 'publish') ) {
          
          $api->action('getmobiles', array());
          $api->XmlToArray($api->getResult());
          $arrayData = $api->createArray();

          if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
          {
            // echo '<b>'.$arrayData['error'][0].'</b>';
            // exit;
          }

          if(isset($arrayData['Mobiles']['Brand']) && sizeof($arrayData['Mobiles']['Brand']) > 0) {
            $request = $arrayData['Mobiles'];
          }
          
          if( !empty($request) ) {
            
            $flag_continue = TRUE;

            $total = count($request);
            $Data = $request;
            
            foreach ($Data['Brand'] as $Brand)
            {
              // $Brand['Name']
              // $Brand['ID']

              foreach ($Brand['Mobile'] as $Mobile)
              {
                // $Mobile['Name']
                // $Mobile['ID']

                $tmp_id = $Brand['ID'];
                $tmp_name = $Brand['Name'];
                $tmp_model_id = $Mobile['ID'];
                $tmp_model_name =  $Mobile['Name'];

                $tmp_key = $tmp_id . '-php-' . $tmp_name . '-php-' . $tmp_model_id . '-php-' . $tmp_model_name;
                $services[] = array(
                  'ID' => $tmp_key,
                  'BRANDID' => $tmp_id,
                  'MODELID' => $tmp_model_id,
                  'BRANDNAME' => $tmp_name,
                  'MODELNAME' => $tmp_model_name,
                );

              }
              
            }
            
          }
					// error_log( 'GSMFUSIONAPI GET MODEL LIST RESPONSE: '. print_r($request,true) );
        }
// GSM FUSION .... END     
  
// DHRU FUSION .... START
        if ( ($suwp_apitype == '00') && ($api_is_model == 'Required') && ($service_post_status == 'publish') ) {
        
          // Debug on
          $api->debug = true;
          
          $request = $api->action('modellist', $para);
          
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
                          $tmp_name = $val3;
                        }
                        if ($key3 === 'MODELS') {
                          $flag_continue = TRUE;
                          if (is_array($val3)) {
                            foreach ($val3 as $key4 => $val4) {
                              if (is_array($val4)) {
                                $tmp_model_id = '';
                                $tmp_model_name = '';
                                foreach ($val4 as $key5 => $val5) {
                                  if ($key5 === 'ID') {
                                    $tmp_model_id = $val5;
                                  }
                                  if ($key5 === 'NAME') {
                                    $tmp_model_name = $val5;
                                  }
                                  if ($tmp_model_name != '') {
                                    $tmp_key = $tmp_id . '-php-' . $tmp_name . '-php-' . $tmp_model_id . '-php-' . $tmp_model_name;
                                    $tmp_key_model_id_display = $tmp_model_id;
                                    $services[] = array(
                                      'ID' => $tmp_key,
                                      'BRANDID' => $tmp_id,
                                      'MODELID' => $tmp_key_model_id_display,
                                      'BRANDNAME' => $tmp_name,
                                      'MODELNAME' => $tmp_model_name,
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
              // Begin extraction at the actual model.
              if ( ( !empty( $service['ID'] ) && ( $service['ID'] != NULL ) ) ) {
                $reply[] = array(
                  'item' => $item,
                  'brand_id' => $service['BRANDID'],
                  'model_id' => $service['MODELID'],
                  'brand_name' => $service['BRANDNAME'],
                  'model_name' => $service['MODELNAME'],
                );
                ++$item;
              }
            }
            
        }
        
        $plugin_admin->suwp_dhru_create_modelbrand( $post_id, $service_post_id, $reply );
        
    endforeach; // foreach( $suwp_services as $service )
    
    return $meta_value;
}

?>
