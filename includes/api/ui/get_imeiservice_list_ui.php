<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_get_imeiservice_list_ui( $post_id ) {

    $suwp_apitype = get_field('suwp_apitype', $post_id );
    // not yet converted, use the default
    if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
      $suwp_apitype = '00';
    }
    
    $plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );

    $apidetails = $plugin_admin->suwp_dhru_get_provider_array( $post_id );
    
    $dhru_url = $apidetails['suwp_dhru_url'];
    
    $plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );

    $is_connected = $plugin_public->suwp_is_connected();
    
    $services = array();
    $flag_continue = FALSE;
    
    if( $is_connected ) {
        // error_log('INTERNET IS CONNECTED.');
    } else {
        // error_log('INTERNET IS NOT CONNECTED.');
        return $services;
    }
    
    // get the api details
    // Since 1.5.5:  if improperly formatted url, make one
    // otherwise would not pop-up the error in the ui when attempting to import
    if (filter_var($dhru_url, FILTER_VALIDATE_URL) === FALSE) {
        $dhru_url = 'https://reseller.stockunlocks.com';
    }
    $suwp_dhru_url = $dhru_url;
    $suwp_dhru_username = $apidetails['suwp_dhru_username'];
    $suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
    
    define("SUWP_REQUESTFORMAT_IMEISERVICELIST_UI", "JSON");
    define('SUWP_DHRUFUSION_URL_IMEISERVICELIST_UI', $suwp_dhru_url);
    define("SUWP_USERNAME_IMEISERVICELIST_UI", $suwp_dhru_username);
    define("SUWP_API_ACCESS_KEY_IMEISERVICELIST_UI", $suwp_dhru_api_key);
    
    if (!extension_loaded('curl'))
    {
        trigger_error('cURL extension not installed', E_USER_ERROR);
    }
    
    // error_log('>>>>> ----- START GET IMEI SERVICE LIST ----- <<<<< : ' . $suwp_apitype);
        
    include( plugin_dir_path( __FILE__ ) . $suwp_apitype . '/get_imeiservice_list_constants_' . $suwp_apitype . '.php' );
    
    // error_log('>>>>> ----- FINISHING GET IMEI SERVICE LIST ----- <<<<< : ' . $suwp_apitype);

    // error_log('POST SERVICES: $services : ' . print_r($services,true) );

    if ( empty($services) ) {
         $flag_continue = FALSE;
    }
    
	$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
	
	// get the default values for our options
	$options = $plugin_public->suwp_exec_get_current_options();
    $troubleshoot_items = $options['suwp_manage_troubleshoot_run_id'];
	$loop_limit = 0;
    
    // whether or not to limit the number of services retrieved
    $flag_limit = false;
	if ( $troubleshoot_items > 0) {
		$loop_limit = $troubleshoot_items + 1;
        $flag_limit = true;
	}
    
    // JSON_FORCE_OBJECT
    // $request_serialized = serialize($request);
    // $reply_serialized = serialize($services);

    $request_serialized = json_encode($request, JSON_FORCE_OBJECT);
    $reply_serialized = json_encode($services, JSON_FORCE_OBJECT);
    
    $item = 0;

    $complete = array();
    $reply_part = array();
    $reply_all = array();
    
    if ( $flag_continue ) {
        
        // set up column headers
        $reply_part[] = array(
              'serviceid' => 'serviceid',
              'servicename' => 'servicename',
              'time' => 'time',
              'credit' => 'credit',
        );

        $reply_all[] = array(
              'serviceid' => 'serviceid',
              'serviceidalt' => 'serviceidalt',
              'servicename' => 'servicename',
              'time' => 'time',
              'credit' => 'credit',
              'groupname' => 'groupname',
              'info' => 'info',
              'network' => 'network',
              'mobile' => 'mobile',
              'provider' => 'provider',
              'pin' => 'pin',
              'kbh' => 'kbh',
              'mep' => 'mep',
              'prd' => 'prd',
              'type' => 'type',
              'locks' => 'locks',
              'reference' => 'reference',
              'networkprovider' => 'networkprovider',
              'groupid' => 'groupid',
        );
        
        foreach ($services as $service) {
                
            // error_log( 'SERVICE ... ' . print_r($service,true) );

              // Begin extraction at the actual service id.
            if ((!empty($service['ID']) && ($service['ID'] != NULL))) {
                
                ++$item;
                
                // added because iPhoneAdmin service IDs are text, not numeric
                $serviceidalt = NULL;
                if( array_key_exists('SERVICEIDALT', $service) && !empty($service['SERVICEIDALT'])  && ($service['SERVICEIDALT'] != NULL) ) {
                    $serviceidalt = $service['SERVICEIDALT'];
                    // error_log( 'SERVICEIDALT EXISTS = '. print_r($serviceidalt,true) );
                } else {
                    // error_log( 'SERVICEIDALT DOES NOT EXIST!' );
                }

                $reply_part[$service['ID']] = array(
                  'serviceid' => $service['ID_DISPLAY'],
                  'servicename' => $service['SERVICENAME'],
                  'time' => $service['TIME'],
                  'credit' => $service['CREDIT'],
                );
                
                $reply_all[$service['ID']] = array(
                  'serviceid' => $service['ID_DISPLAY'],
                  'serviceidalt' => $serviceidalt,
                  'servicename' => $service['SERVICENAME'],
                  'time' => $service['TIME'],
                  'credit' => $service['CREDIT'],
                  'groupname' => $service['GROUPNAME'],
                  'info' => $service['INFO'],
                  'network' => $service['Requires.Network'],
                  'mobile' => $service['Requires.Mobile'],
                  'provider' => $service['Requires.Provider'],
                  'pin' => $service['Requires.PIN'],
                  'kbh' => $service['Requires.KBH'],
                  'mep' => $service['Requires.MEP'],
                  'prd' => $service['Requires.PRD'],
                  'type' => $service['Requires.Type'],
                  'locks' => $service['Requires.Locks'],
                  'reference' => $service['Requires.Reference'],
                  'networkprovider' => $service['NETWORKPROVIDER'],
                  'groupid' => $service['GROUPID'],
                );
            
            }
        }
        
        if ( $flag_limit ) {
            $reply_part = array_slice($reply_part, 0, $loop_limit, true);
            $reply_all = array_slice($reply_all, 0, $loop_limit, true);
        }
        
        $complete[] = $reply_part;
        $complete[] = $reply_all;
    
    }
       
    // return partial and full details
    return $complete;

}

?>
