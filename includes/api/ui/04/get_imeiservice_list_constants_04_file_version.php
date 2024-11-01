<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
class iPhoneAdmin
{
    var $xmlData;
    var $xmlResult;
    var $debug;
    var $action;
    function __construct()
    {
        $this->xmlData = new DOMDocument();
    }
    function getResult()
    {
        return $this->xmlResult;
    }
    function action($action, $arr = array())
    {
        if (is_string($action))
        {
            if (is_array($arr))
            {
                if (count($arr))
                {
                    $request = $this->xmlData->createElement("PARAMETERS");
                    $this->xmlData->appendChild($request);
                    foreach ($arr as $key => $val)
                    {
                        $key = strtoupper($key);
                        $request->appendChild($this->xmlData->createElement($key, $val));
                    }
                }
                $posted = array(
                    'username' => SUWP_USERNAME_IMEISERVICELIST_UI,
                    'apiaccesskey' => SUWP_API_ACCESS_KEY_IMEISERVICELIST_UI,
                    'action' => $action,
                    'requestformat' => SUWP_REQUESTFORMAT_IMEISERVICELIST_UI,
                    'parameters' => $this->xmlData->saveHTML());
                 
                $crul = curl_init();
                curl_setopt($crul, CURLOPT_HEADER, false);
                curl_setopt( $crul, CURLOPT_CONNECTTIMEOUT, 10 );
                curl_setopt( $crul, CURLOPT_TIMEOUT, 60 );
                curl_setopt($crul, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                //curl_setopt($crul, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($crul, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($crul, CURLOPT_URL, SUWP_DHRUFUSION_URL_IMEISERVICELIST_UI.'/api/index.php');
                curl_setopt($crul, CURLOPT_POST, true);
                curl_setopt($crul, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($crul, CURLOPT_POSTFIELDS, $posted);
                $response = curl_exec($crul);
                
                // error_log( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> DHRU FUSION REQUEST CHECK : ' . print_r( $response, true) );
                
                if (curl_errno($crul) != CURLE_OK)
                {
                    echo curl_error($crul);
                    curl_close($crul);
                }
                else
                {
                    curl_close($crul);
                    
                    if ($this->debug)
                    {
                        // echo "<textarea rows='20' cols='200'> ";
                        // print_r($response);
                        // echo "</textarea>";
                    }
                    return (json_decode($response, true));
                }
            }
        }
        return false;
    }
}

$api = new iPhoneAdmin();

// Debug on
// $api->debug = true; // disabled v1.8.0

$request = $api->action('imeiservicelist');

if (is_array($request)) {
    // error_log( 'DHRUFUSION ALL IMEI SERVICES: '. print_r($request,true) );
    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($request), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $key1 => $val1) {
      if ($key1 === 'LIST') {
        if (is_array($val1)) {
          foreach ($val1 as $key2 => $val2) {
            $tmp_id = $key2;
            if (is_array($val2)) {
              foreach ($val2 as $key3 => $val3) {
                if ($key3 === 'GROUPNAME') {
                  $tmp_groupname = $val3;
                }
                if ($key3 === 'SERVICES') {
                  $flag_continue = TRUE;
                  if (is_array($val3)) {
                    foreach ($val3 as $key4 => $val4) {
                      if (is_array($val4)) {
                        $tmp_id = '';
                        $tmp_servicename = '';
                        $tmp_credit = '';
                        $tmp_time = '';
                        $tmp_info = '';
                        $tmp_requiresnetwork = '';
                        $tmp_requiresmobile = '';
                        $tmp_requiresprovider = '';
                        $tmp_requirespin = '';
                        $tmp_requireskbh = '';
                        $tmp_requiresmep = '';
                        $tmp_requiresprd = '';
                        $tmp_requirestype = '';
                        $tmp_requireslocks = '';
                        $tmp_requiresreference = '';
                        foreach ($val4 as $key5 => $val5) {
                          if ($key5 === 'SERVICEID') {
                            $tmp_id = $val5;
                          }
                          if ($key5 === 'SERVICENAME') {
                            $tmp_servicename = $val5;
                          }
                          if ($key5 === 'CREDIT') {
                            $tmp_credit = $val5;
                          }
                          if ($key5 === 'TIME') {
                            $tmp_time = $val5;
                          }
                          if ($key5 === 'INFO') {
                            $tmp_info = $val5;
                          }
                          if ($key5 === 'Requires.Network') {
                            $tmp_requiresnetwork = $val5;
                          }
                          if ($key5 === 'Requires.Mobile') {
                            $tmp_requiresmobile = $val5;
                          }
                          if ($key5 === 'Requires.Provider') {
                            $tmp_requiresprovider = $val5;
                          }
                          if ($key5 === 'Requires.PIN') {
                            $tmp_requirespin = $val5;
                          }
                          if ($key5 === 'Requires.KBH') {
                            $tmp_requireskbh = $val5;
                          }
                          if ($key5 === 'Requires.MEP') {
                            $tmp_requiresmep = $val5;
                          }
                          if ($key5 === 'Requires.PRD') {
                            $tmp_requiresprd = $val5;
                          }
                          if ($key5 === 'Requires.Type') {
                            $tmp_requirestype = $val5;
                          }
                          if ($key5 === 'Requires.Locks') {
                            $tmp_requireslocks = $val5;
                          }
                          if ($key5 === 'Requires.Reference') {
                            $tmp_requiresreference = $val5;
                          }
                          if ($tmp_requiresreference != '') {
                            $tmp_key = $tmp_id . '-php-' . $tmp_groupname . '-php-' . $tmp_servicename;
                            $tmp_key_id_display = $tmp_id;
                            $services[] = array(
                              'GROUPID' => '',
                              'ID' => $tmp_id,
                              'ID_DISPLAY' => $tmp_key_id_display,
                              'NETWORKPROVIDER' => '',
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
} else {
    
    // could not connect to API server, probably due to failed internet connection
    $request = array(
          'ERROR' => array(
                        array('MESSAGE' => 'Invalid Access Key'),
                        array('MESSAGE' => 'Invalid Username'),
                          ),
          'apiversion' => 'dhrufusion.2.0',
    );
    
}

*/


/**
BlacklistWW 7 Blacklist Status Samsung, LG, Apple, any model/ Brand WorldWide
TetherPolicy 8 Initial, Current, and Next Tether Policy (SIM LOCK) Info
ReplacementReport 9 iPhone &amp; iPad Replacement Report
IccidMacAddress 10 iPhone &amp; iPad ICCID + MAC Address Report
SoldByInfo 11 iPhone, iPad, iWatch Purchase Country, Date, and Sold By Info
ActivationLockFmip 12 Activation Lock &amp; Find My iPhone Status Report
WarrantyStatusReport 13 Warranty Status Report
FullGSXReportWRFBSM 14 (Full A++) Full GSX Report, Warranty, Replacement Info, FMiP, Blacklist, Sold BY, ICCID, MAC Address
StandardGSXNoWarranty 15 Apple GSX Report ( NO Warranty Info) 
FmipBlacklistSimLock 16 (Popular) Full Security Report (FMiP, Activation, Blacklist, SIMLOCK) 
FullGSXNoReplacement 17 (Recommended) Apple GSX Report, Warranty, FMiP, Sold by, Blacklist, MAC Address, ICCID
GSXFmipMacWarIccid 18 (Popular) Apple GSX Report with Warranty and FMiP, MAC Address, ICCID
GSXSoldByNoReplacementNoBlacklist 19 (Recommended) Apple GSX Report, Warranty, FMiP, Sold by, MAC Address, ICCID
CaseHistory 21 iPhone Case History Check
*/

$file_array = SUWP_PATH_CLUDES . '/api/providers/services/' . $suwp_apitype . '.ini';
// true = Parse with sections
$request = parse_ini_file($file_array, true);

if ( $request ) {
  // error_log( 'IPHONEADMIN ALL IMEI SERVICES: '. print_r($request,true) );
  $flag_continue = TRUE;
  $total = count($request);

  for($count = 0; $count < $total; $count++) {
    
    $tmp_group_id = '';
    $tmp_id = '';
    $tmp_service_id_alt = '';
    $tmp_networkprovider = '';
    $assigned_brand = '';
    $assigned_model = '';
    $tmp_groupname = '';
    $tmp_servicename = '';
    $tmp_info = '';
    $tmp_requiresnetwork = '';
    $tmp_requiresmobile = '';
    $tmp_requiresprovider = '';
    $tmp_requirespin = '';
    $tmp_requireskbh = '';
    $tmp_requiresmep = '';
    $tmp_requiresprd = '';
    $tmp_requirestype = '';
    $tmp_requireslocks = '';
    $tmp_requiresreference = '';
    $tmp_credit = '';
    $tmp_time = '';

    if( !empty($request[$count]['GROUPID'])  && array_key_exists('GROUPID', $request[$count]) ) {
      $tmp_group_id = $request[$count]['GROUPID'];
    }
    if( !empty($request[$count]['ID'])  && array_key_exists('ID', $request[$count]) ) {
      $tmp_id = $request[$count]['ID'];
    }
    if( !empty($request[$count]['SERVICEID'])  && array_key_exists('SERVICEID', $request[$count]) ) {
      $tmp_service_id_alt = $request[$count]['SERVICEID'];
    }
    if( !empty($request[$count]['NETWORKPROVIDER'])  && array_key_exists('NETWORKPROVIDER', $request[$count]) ) {
      $tmp_networkprovider = $request[$count]['NETWORKPROVIDER'];
    }
    if( !empty($request[$count]['ASSIGNEDBRAND'])  && array_key_exists('ASSIGNEDBRAND', $request[$count]) ) {
      $assigned_brand = $request[$count]['ASSIGNEDBRAND'];
    }
    if( !empty($request[$count]['ASSIGNEDMODEL'])  && array_key_exists('ASSIGNEDMODEL', $request[$count]) ) {
      $assigned_model = $request[$count]['ASSIGNEDMODEL'];
    }
    if( !empty($request[$count]['GROUPNAME'])  && array_key_exists('GROUPNAME', $request[$count]) ) {
      $tmp_groupname = $request[$count]['GROUPNAME'];
    }
    if( !empty($request[$count]['SERVICENAME'])  && array_key_exists('SERVICENAME', $request[$count]) ) {
      $tmp_servicename = $request[$count]['SERVICENAME'];
    }
    if( !empty($request[$count]['INFO'])  && array_key_exists('INFO', $request[$count]) ) {
      $tmp_info = $request[$count]['INFO'];
    }
    if( !empty($request[$count]['Requires.Network'])  && array_key_exists('Requires.Network', $request[$count]) ) {
      $tmp_requiresnetwork = $request[$count]['Requires.Network'];
    }
    if( !empty($request[$count]['Requires.Mobile'])  && array_key_exists('Requires.Mobile', $request[$count]) ) {
      $tmp_requiresmobile = $request[$count]['Requires.Mobile'];
    }
    if( !empty($request[$count]['Requires.Provider'])  && array_key_exists('Requires.Provider', $request[$count]) ) {
      $tmp_requiresprovider = $request[$count]['Requires.Provider'];
    }
    if( !empty($request[$count]['Requires.PIN'])  && array_key_exists('Requires.PIN', $request[$count]) ) {
      $tmp_requirespin = $request[$count]['Requires.PIN'];
    }
    if( !empty($request[$count]['Requires.KBH'])  && array_key_exists('Requires.KBH', $request[$count]) ) {
      $tmp_requireskbh = $request[$count]['Requires.KBH'];
    }
    if( !empty($request[$count]['Requires.MEP'])  && array_key_exists('Requires.MEP', $request[$count]) ) {
      $tmp_requiresmep = $request[$count]['Requires.MEP'];
    }
    if( !empty($request[$count]['Requires.PRD'])  && array_key_exists('Requires.PRD', $request[$count]) ) {
      $tmp_requiresprd = $request[$count]['Requires.PRD'];
    }
    if( !empty($request[$count]['Requires.Type'])  && array_key_exists('Requires.Type', $request[$count]) ) {
      $tmp_requirestype = $request[$count]['Requires.Type'];
    }
    if( !empty($request[$count]['Requires.Locks'])  && array_key_exists('Requires.Locks', $request[$count]) ) {
      $tmp_requireslocks = $request[$count]['Requires.Locks'];
    }
    if( !empty($request[$count]['Requires.Reference'])  && array_key_exists('Requires.Reference', $request[$count]) ) {
      $tmp_requiresreference = $request[$count]['Requires.Reference'];
    }
    if( !empty($request[$count]['CREDIT'])  && array_key_exists('CREDIT', $request[$count]) ) {
      $tmp_credit = $request[$count]['CREDIT'];
    }
    if( !empty($request[$count]['TIME'])  && array_key_exists('TIME', $request[$count]) ) {
      $tmp_time = $request[$count]['TIME'];
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
