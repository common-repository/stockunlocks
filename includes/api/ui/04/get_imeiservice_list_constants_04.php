<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class iPhoneAdmin {
  var $http_url = '';
  var $http_code = '';

  /**
  * Initialize
  */
  function __construct() {

  }

  /**
  * Return the http_code results.
  */
  function getHttpCode() {
    return $this->http_code;
  }

  /**
  * Connect with the remote client.
  */
   function action( $arr = array() ) {

     $this->http_url = SUWP_DHRUFUSION_URL_IMEISERVICELIST_UI;
     $ch = curl_init( $this->http_url );

     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
     curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
     curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);

     $response = curl_exec($ch);

     if (!curl_errno($ch)) {
       $info = curl_getinfo($ch);
       $this->http_code = $info['http_code'];
     }

     if (is_resource($ch)) {
         curl_close($ch);
     }
     
     return (json_decode($response, TRUE));
  }
}

$api = new iPhoneAdmin();

// execute the request
$request = $api->action([
  'API_ID' => SUWP_USERNAME_IMEISERVICELIST_UI,
  'API_KEY' => SUWP_API_ACCESS_KEY_IMEISERVICELIST_UI,
  'SERVICELIST' => 'ALL',
  // 'UNIQUE_KEY'   => $suwp_unique_key,
  // 'SERVICE'   => $suwp_dhru_serviceid_alt,
  // 'NOTIFY_URL'   => get_site_url(),
  // 'DEVICES'   => $devices,
]);

// error_log( 'IPHONEADMIN ALL IMEI SERVICES: '. print_r($request,true) );

/**
[RESULT] => 1
[MESSAGE] => Request Placed & processed Successfully!
[SERVICELIST] => Array
*/

if (is_array($request)) {
  
  if( $request['RESULT'] && array_key_exists('SERVICELIST', $request) && !empty($request['SERVICELIST']) ) {
    
    // error_log( 'IPHONEADMIN ALL IMEI SERVICES: '. print_r($request['SERVICELIST'],true) );
    $flag_continue = TRUE;
    $total = count($request['SERVICELIST']);
    // error_log( 'IPHONEADMIN COUNT IMEI SERVICES: '. print_r($total,true) );
    
    $service_list = array();
    $service_list = $request['SERVICELIST'];

    for($count = 0; $count < $total; $count++) {

      // error_log( 'IPHONEADMIN COUNT = '. print_r($count,true) );
    
      $tmp_group_id = '';
      $tmp_id = '';
      $tmp_service_id_alt = '';
      $tmp_networkprovider = '';
      $assigned_brand = '';
      $assigned_model = '';
      $tmp_groupname = '';
      $tmp_servicename = '';
      $tmp_info = '';
      $tmp_requiresnetwork = 'None';
      $tmp_requiresmobile = 'None';
      $tmp_requiresprovider = 'None';
      $tmp_requirespin = 'None';
      $tmp_requireskbh = 'None';
      $tmp_requiresmep = 'None';
      $tmp_requiresprd = 'None';
      $tmp_requirestype = 'None';
      $tmp_requireslocks = 'None';
      $tmp_requiresreference = 'None';
      $tmp_credit = '';
      $tmp_time = '';
      
      /** example entry
      [TITLE] => iPhone Case History Check
      [SERVICEID] => CaseHistory
      [INPUTTYPE] => 
      [PRICE] => 3.00 USD
      [DESCRIPTION] => This is service is manually processed
      [DELIVERYTIME] => 1-24 Hours
      */

      if( !empty($service_list[$count]['GROUPID'])  && array_key_exists('GROUPID', $service_list[$count]) ) {
        $tmp_group_id = $service_list[$count]['GROUPID'];
      }
      if( !empty($service_list[$count]['ID'])  && array_key_exists('ID', $service_list[$count]) ) {
        $tmp_id = $service_list[$count]['ID'];
      } else {
        $tmp_id = $count+1; // 0 will not store as an ID
      }
      if( !empty($service_list[$count]['SERVICEID'])  && array_key_exists('SERVICEID', $service_list[$count]) ) {
        $tmp_service_id_alt = $service_list[$count]['SERVICEID'];
      }
      if( !empty($service_list[$count]['NETWORKPROVIDER'])  && array_key_exists('NETWORKPROVIDER', $service_list[$count]) ) {
        $tmp_networkprovider = $service_list[$count]['NETWORKPROVIDER'];
      }
      if( !empty($service_list[$count]['ASSIGNEDBRAND'])  && array_key_exists('ASSIGNEDBRAND', $service_list[$count]) ) {
        $assigned_brand = $service_list[$count]['ASSIGNEDBRAND'];
      }
      if( !empty($service_list[$count]['ASSIGNEDMODEL'])  && array_key_exists('ASSIGNEDMODEL', $service_list[$count]) ) {
        $assigned_model = $service_list[$count]['ASSIGNEDMODEL'];
      }
      if( !empty($service_list[$count]['GROUPNAME'])  && array_key_exists('GROUPNAME', $service_list[$count]) ) {
        $tmp_groupname = $service_list[$count]['GROUPNAME'];
      }
      if( !empty($service_list[$count]['TITLE'])  && array_key_exists('TITLE', $service_list[$count]) ) {
        $tmp_servicename = $service_list[$count]['TITLE'];
      }
      if( !empty($service_list[$count]['DESCRIPTION'])  && array_key_exists('DESCRIPTION', $service_list[$count]) ) {
        $tmp_info = $service_list[$count]['DESCRIPTION'];
      }
      if( !empty($service_list[$count]['Requires.Network'])  && array_key_exists('Requires.Network', $service_list[$count]) ) {
        $tmp_requiresnetwork = $service_list[$count]['Requires.Network'];
      }
      if( !empty($service_list[$count]['Requires.Mobile'])  && array_key_exists('Requires.Mobile', $service_list[$count]) ) {
        $tmp_requiresmobile = $service_list[$count]['Requires.Mobile'];
      }
      if( !empty($service_list[$count]['Requires.Provider'])  && array_key_exists('Requires.Provider', $service_list[$count]) ) {
        $tmp_requiresprovider = $service_list[$count]['Requires.Provider'];
      }
      if( !empty($service_list[$count]['Requires.PIN'])  && array_key_exists('Requires.PIN', $service_list[$count]) ) {
        $tmp_requirespin = $service_list[$count]['Requires.PIN'];
      }
      if( !empty($service_list[$count]['Requires.KBH'])  && array_key_exists('Requires.KBH', $service_list[$count]) ) {
        $tmp_requireskbh = $service_list[$count]['Requires.KBH'];
      }
      if( !empty($service_list[$count]['Requires.MEP'])  && array_key_exists('Requires.MEP', $service_list[$count]) ) {
        $tmp_requiresmep = $service_list[$count]['Requires.MEP'];
      }
      if( !empty($service_list[$count]['Requires.PRD'])  && array_key_exists('Requires.PRD', $service_list[$count]) ) {
        $tmp_requiresprd = $service_list[$count]['Requires.PRD'];
      }
      if( !empty($service_list[$count]['Requires.Type'])  && array_key_exists('Requires.Type', $service_list[$count]) ) {
        $tmp_requirestype = $service_list[$count]['Requires.Type'];
      }
      if( !empty($service_list[$count]['Requires.Locks'])  && array_key_exists('Requires.Locks', $service_list[$count]) ) {
        $tmp_requireslocks = $service_list[$count]['Requires.Locks'];
      }
      if( !empty($service_list[$count]['Requires.Reference'])  && array_key_exists('Requires.Reference', $service_list[$count]) ) {
        $tmp_requiresreference = $service_list[$count]['Requires.Reference'];
      }
      if( !empty($service_list[$count]['PRICE'])  && array_key_exists('PRICE', $service_list[$count]) ) {
        $tmp_credit = trim( str_replace("usd","", strtolower($service_list[$count]['PRICE'])) );
      }
      if( !empty($service_list[$count]['DELIVERYTIME'])  && array_key_exists('DELIVERYTIME', $service_list[$count]) ) {
        $tmp_time = $service_list[$count]['DELIVERYTIME'];
      }

      $services[] = array(
        'GROUPID' => $tmp_group_id,
        'ID' => $tmp_id,
        'SERVICEIDALT' => $tmp_service_id_alt,
        'ID_DISPLAY' => $tmp_id,
        'NETWORKPROVIDER' => $tmp_networkprovider,
        'GROUPNAME' => $tmp_groupname,
        'SERVICENAME' => $tmp_servicename,
        'INFO' => htmlspecialchars( strip_tags( str_replace( '</p>', chr(10) , $tmp_info) ) ),
        'Requires.Network' => $tmp_requiresnetwork,
        'Requires.Mobile' => $tmp_requiresmobile,
        'Requires.Provider' => $tmp_requiresprovider,
        'Requires.PIN' => $tmp_requirespin,
        'Requires.KBH' => $tmp_requireskbh,
        'Requires.MEP' => $tmp_requiresmep,
        'Requires.PRD' => $tmp_requiresprd,
        'Requires.Type' => $tmp_requirestype,
        'Requires.Locks' => $tmp_requireslocks,
        'Requires.Reference' => $tmp_requiresreference,
        'CREDIT' => $tmp_credit,
        'TIME' => $tmp_time,
      );

    }

  } else {
    
    // could not parse to API ini file
    // probably due to bad format or failed internet connection
    $request = array(
      'ERROR' => array(
                    array('MESSAGE' => 'Invalid Access Key'),
                    array('MESSAGE' => 'Invalid Username'),
                      ),
      'apiversion' => 'iphoneadmin.1.0',
    );

  }
  
} else {

  // could not parse to API ini file
  // probably due to bad format or failed internet connection
  $request = array(
    'ERROR' => array(
                  array('MESSAGE' => 'Invalid Access Key'),
                  array('MESSAGE' => 'Invalid Username'),
                    ),
    'apiversion' => 'iphoneadmin.1.0',
  );

}
