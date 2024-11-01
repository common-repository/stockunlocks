<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_get_single_imei_service_details_cron( $post_id ) {
      
    $suwp_apitype = get_field('suwp_apitype', $post_id );
    // not yet converted, use the default
    if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
      $suwp_apitype = '00';
    }

    $suwp_dhru_referenceid = '176'; // not yet implemented, obtained from 'imeiservicelist' [SERVICEID]
    
    $plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );

    $apidetails = $plugin_admin->suwp_dhru_get_provider_array( $post_id );
    
    // get the api details
    $suwp_dhru_url = $apidetails['suwp_dhru_url'];
    $suwp_dhru_username = $apidetails['suwp_dhru_username'];
    $suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
 
    // Debug on
    $api->debug = true;

    $reply =  array();
    $para =  array();
    $services = array();
    $flag_continue = FALSE;
    $request = array();
   
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_single_imei_service_details_constants_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_single_imei_service_details_constants_' . $post_id . '_cron.php' );
    
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_single_imei_service_details_api_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_single_imei_service_details_api_' . $post_id . '_cron.php' );

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
                  // error_log( 'UNLOCKBASE-GET SINGLE IMEI CRON - SERVICE DETAILS: '. print_r($Data,true) );
    
                  foreach ($Data['Group'] as $Group) {
                      foreach ($Group['Tool'] as $Tool) {
                        
                        if ( $suwp_dhru_referenceid === $Tool['ID'] ) {

                          // error_log( 'UNLOCKBASE-GET SINGLE IMEI FOUND MATCHING SERVICE, DETAILS: name = '. $Tool['Name'] . ', ID = ' . $Tool['ID'] );
      
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
          $services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'Could not communicate with the api' );
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
            
            // error_log( 'GSMFUSION-GET SINGLE IMEI FOUND MATCHING SERVICE, DETAILS: name = '. $package_title . ', ID = ' . $package_id );
            
            $services = array(
              'RESULT' => 'SUCCESS',
              'SERVICEID' => $package_id,
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
        $services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'Could not communicate with the api' );

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
            
            // error_log( 'UNLOCKINGPORTAL-GET SINGLE IMEI FOUND MATCHING SERVICE, DETAILS: name = '. $package_title . ', ID = ' . $package_id );
            
            $services = array(
              'RESULT' => 'SUCCESS',
              'SERVICEID' => $package_id,
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
        $services = array( 'RESULT' => 'ERROR', 'MESSAGE' => 'Could not communicate with the api' );

      }
      
    }
// UNLOCKINGPORTAL .... END

// DHRU FUSION .... START
    if ( $suwp_apitype == '00' ) {

      $para['ID'] = $suwp_dhru_referenceid;
      $request = $api->action('getimeiservicedetails', $para);

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
                          $assigned_brand = '';
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
                                  case 'model':
                                  $model = $val5;
                                  break;
                                  case 'assigned_brand':
                                  $assigned_brand = $val5;
                                  break;
                                  case 'provider':
                                  $provider = $val5;
                                  break;
                                  case 'mep':
                                  $mep = $val5;
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
                        $services = array('RESULT' => $tmp_result, 'MESSAGE' => $tmp_msg);
                        break;
      
                      case 'SUCCESS':
                        $services = array(
                          'RESULT' => $tmp_result,
                          'SERVICEID' => $tmp_serviceid,
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
  
    return $services;

}

?>
