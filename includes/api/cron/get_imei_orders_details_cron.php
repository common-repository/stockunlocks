<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function suwp_dhru_get_imei_order_details_cron( $post_id ) {
    
    $plugin_admin = new Stock_Unlocks_Admin( 'stockunlocks', STOCKUNLOCKS_VERSION );

    $apidetails = $plugin_admin->suwp_dhru_get_provider_array( $post_id );
    
    // get the api details
    $suwp_dhru_url = $apidetails['suwp_dhru_url'];
    $suwp_dhru_username = $apidetails['suwp_dhru_username'];
    $suwp_dhru_api_key = $apidetails['suwp_dhru_api_key'];
    
		$suwp_apitype = get_field('suwp_apitype', $post_id );
		// not yet converted, use the default
		if ( $suwp_apitype == NULL || $suwp_apitype == '' ) {
			$suwp_apitype = '00';
    }
    
    // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_imei_orders_details_constants_' . $post_id . '_cron.php' );
    include( SUWP_TEMP . 'suwp_get_imei_orders_details_constants_' . $post_id . '_cron.php' );
    
    $suwp_comments = array();
    
    // need to determine if 'wc-suwp-available', 'wc-suwp-avail-part', 'wc-suwp-unavailable'.
    $qty_loop = 1;
    $order_id_results = array();
    
    global $wpdb;

    /** Results of orders being checked on
    'suwp_reply_success': order was replied and a code was obtained -> available
    'suwp_reply_reject': order was replied, but no code was available -> unavailable
    'suwp_reply_error': something went wrong, not enough credit, etc., -> order failed, MUST be recreated from scratch, reply will never change
    **/
    
    // collect all of the relevant comments in order to extract order references
    // sorting results by comment_post_ID ASC because there can be different orders from the same supplier
    // 'suwp_order_success' indicates an order entry that has not yet been processed
    // 'suwp_reply_error' usually indicates that we didn't have enough credit or the service is no longer available with our supplier on a PREVIOUS CHECK
    // a NEW order must be created
    $suwp_comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "comments WHERE comment_type=%s AND comment_author_IP=%s ORDER BY comment_post_ID ASC", 'suwp_order_success', $suwp_dhru_api_key ) );
    
    // error_log( 'GET IMEI ORDER DETAILS CRON $suwp_comments = ' . print_r($suwp_comments,true) );
    
    $suwp_comments_total = count($suwp_comments);
    
    $current_order_id = '';
    
    // need a means to look ahead to determine when the order_id will change
    $suwp_comments_iter = new ArrayIterator($suwp_comments);
    
    // loop over the comments, get the info and check the status
    foreach( $suwp_comments as $comment ):
    
        // get next key and value...
        $suwp_comments_iter->next(); 
        $nextKey = $suwp_comments_iter->key();
        $nextValue = $suwp_comments_iter->current();
        
        $comment_values_next = '';
        $suwp_dhru_imei_next = '';
        $next_order_item_id = 0;
        $next_order_id = 0;
        
        if( is_object( $nextValue ) ) {
            // extract the next imei and order_item_id from the comment
            $comment_values_next = explode( "-php-", trim($nextValue->comment_content));
            $suwp_dhru_imei_next = $comment_values_next[0];
            $next_order_item_id = $comment_values_next[1];
        }
        
        // need to know how many order items have the same order_id
        // to determine when processing is done for that order
        $suwp_order_id_next = $wpdb->get_results("select order_id from ".$wpdb->prefix."woocommerce_order_items where order_item_id='". $next_order_item_id . "'" );
        
        if( is_object( $suwp_order_id_next ) ) {
            $next_order_id = $suwp_order_id_next[0]->order_id;
        }
        
        $suwp_dhru_referenceid = $comment->comment_agent;
        $comment_id = $comment->comment_ID;
        $current_order_id = $comment->comment_post_ID;
        $prev_comment_content = $comment->comment_content;
        // extract the imei and order_item_id from the comment
        $comment_values = explode( "-php-", trim($prev_comment_content));
        $suwp_dhru_imei = $comment_values[0];
        $current_order_item_id = $comment_values[1];
        
        // error_log( 'COMMENT DETAILS - GET IMEI ORDER DETAILS CRON $prev_comment_content = ' . print_r($prev_comment_content,true) );
        // error_log( 'COMMENT DETAILS - GET IMEI ORDER DETAILS CRON $comment_values = ' . print_r($comment_values,true) );
    
        $reply =  array();
        $para =  array();
        $request = array();
     
        // >>> include( plugin_dir_path( __FILE__ ) . 'providers/get_imei_orders_details_api_' . $post_id . '_cron.php' );
        include( SUWP_TEMP . 'suwp_get_imei_orders_details_api_' . $post_id . '_cron.php' );

// UNLOCKBASE .... START
      if ($suwp_apitype == '02') {
        
        $para['ID'] = $suwp_dhru_referenceid;

        /* Call the API */

        // error_log( 'UNLOCKBASE IMEIORDERDEETS about to ... (GetOrders), order # = '. $para['ID'] );

        $XML = $api->CallAPI('GetOrders', $para );

        if (is_string($XML)) {
            /* Parse the XML stream */
            $Data = $api->ParseXML($XML);

            if (is_array($Data)) {

                // error_log( 'UNLOCKBASE IMEIORDERDEETS (GetOrders) RESPONSE: '. print_r($Data,true) );

                $request = $Data;
                
                if (isset($Data['Error'])) {
                    /* The API has returned an error */
                    // print('API error : ' . htmlspecialchars($Data['Error']));
                    $reply = array(
                    'ORDERID' => $para['ID'],
                    'RESULTS' => 'OTHER',
                    'MESSAGE' => htmlspecialchars($Data['Error']),
                    );
                } else {
                    /* Everything works fine */
                
                    foreach ($Data['Order'] as $Order) {
                
                        $code = 'Rejected by Admin';
                        $statusId = $Order['Status'];
                        $available = $Order['Available'];
                        $tmp_status = '';
                        $tmp_result = '';

                        if( array_key_exists('Codes', $Order) && $Order['Codes'] != NULL ) {
                            $code = $Order['Codes'];
                        }

                        // dhru fusion >>> 0 = pending; 3 = unavailable; 4 = available
                        if( $statusId === 'Delivered' ) {
                            
                            switch ($available) {
                                case 'True':
                                $tmp_result = 'SUCCESS';
                                $tmp_status = '4';
                                break;

                                case 'False':
                                $tmp_result = 'SUCCESS';
                                $tmp_status = '3';
                                break;
                            }

                            $reply = array(
                                'ORDERID' => $para['ID'],
                                'RESULTS' => $tmp_result,
                                'IMEI' => $Order['IMEI'],
                                'STATUS' => $tmp_status,
                                'CODE' => $code,
                                'COMMENTS' => $statusId,
                            );
                        }

                        if( $statusId === 'Canceled' ) {

                            $tmp_result = 'SUCCESS';
                            $tmp_status = '3';

                            $reply = array(
                                'ORDERID' => $para['ID'],
                                'RESULTS' => $tmp_result,
                                'IMEI' => $Order['IMEI'],
                                'STATUS' => $tmp_status,
                                'CODE' => $code,
                                'COMMENTS' => $statusId,
                            );
                        }
                    }
                }
            } else {
                /* Parsing error */
                // print('Could not parse the XML stream');
                $reply = array(
                'ORDERID' => $para['ID'],
                'RESULTS' => 'OTHER',
                'MESSAGE' => 'Could not parse the XML stream. Try again later.',
                );
            }
        } else {
            /* Communication error */
            // print('Could not communicate with the api');
            $reply = array(
            'ORDERID' => $para['ID'],
            'RESULTS' => 'OTHER',
            'MESSAGE' => 'Could not communicate with the api. Try again later.',
            );
        }
      }
// UNLOCKBASE .... END

// GSM FUSION .... START
      if ($suwp_apitype == '01' || $suwp_apitype == '03') {
        
        $para['ID'] = $suwp_dhru_referenceid;

        $api->action( 'getimeis', array('orderIds' => $para['ID']) );
        $api->XmlToArray($api->getResult());
        $arrayData = $api->createArray();

        $api_using = '';

        if( $suwp_apitype == '01' ) {
          $api_using = 'GSMFUSION';
        }
        if( $suwp_apitype == '03' ) {
          $api_using = 'NAKSHSOFT';
        }
        
				// error_log( $api_using . ' GET IMEI ORDER DETAILS RESPONSE, FOR ORDER = '. $suwp_dhru_referenceid . ': '. print_r($arrayData,true) );
					
        $reply = array(
          'ORDERID' => $para['ID'],
          'RESULTS' => 'OTHER',
          'MESSAGE' => 'Possible network connection error, do nothing.',
        );

        if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
        {
          $reply = array(
            'ORDERID' => $para['ID'],
            'RESULTS' => 'ERROR',
            'MESSAGE' => $arrayData['error'][0],
          );
        }

        if(isset($arrayData['result']['imeis']) && sizeof($arrayData['result']['imeis']) > 0)
          $request = $arrayData['result']['imeis'];
        $total = count($request);
        
        for($count = 0; $count < $total; $count++)
        {
          
           // error_log( $api_using . ' , LOOPING THROUGH ORDERS ... GET IMEI ORDER DETAILS RESPONSE, FOR SAVED ORDER = '. $suwp_dhru_referenceid . ': REMOTE ORDER = '. $request[$count]['id'] );
          
            $package = '';
            $code = 'Rejected by Admin';
            $requestedat = '';
            $statusId = '';
            $status = '';
            $tmp_imei = '';
            $tmp_status = '';
            $tmp_result = '';

            if( array_key_exists('package', $request[$count]) ) {
              $package = $request[$count]['package'];
            }
            if( array_key_exists('code', $request[$count]) ) {
              $code = $request[$count]['code'];
            }
            if( array_key_exists('requestedat', $request[$count]) ) {
              $requestedat = $request[$count]['requestedat'];
            }
            if( array_key_exists('imei', $request[$count]) ) {
              $tmp_imei = $request[$count]['imei'];
            }
            if( array_key_exists('statusId', $request[$count]) ) {
              $statusId = $request[$count]['statusId'];
            }
            if( array_key_exists('status', $request[$count]) ) {
              $status = $request[$count]['status'];
            }

            // convert the statusId to what we've been using for STATUS value
            // gsm fusion >>> 1 = pending; 2 = completed (accepted for processing) ; 3 = unavailable (rejected); 4 = in process
            // dhru fusion >>> 0 = pending; 3 = unavailable; 4 = available
            switch ($statusId) {
              case '2':
                $tmp_result = 'SUCCESS';
                $tmp_status = '4';
                break;

              case '3':
                $tmp_result = 'SUCCESS';
                $tmp_status = '3';
                break;
            }

            $reply = array(
              'ORDERID' => $para['ID'],
              'RESULTS' => $tmp_result,
              'IMEI' => $tmp_imei,
              'STATUS' => $tmp_status,
              'CODE' => $code,
              'COMMENTS' => $requestedat,
            );

        } // for($count = 0; $count < $total; $count++)
        // error_log( 'GSMFUSIONAPI GET IMEI DETAILS RESPONSE: '. print_r($request,true) );
      }
// GSM FUSION .... END

// IPHONEADMIN .... START
      if ($suwp_apitype == '04') {
        
        // execute the request
        $request =  $api->action([
          'API_ID' => $suwp_dhru_username,
          'API_KEY' => $suwp_dhru_api_key,
          'UNIQUE_KEY'   => $suwp_dhru_referenceid,
        ]);
        
        $http_code = $api->getHttpCode();
        // error_log( 'IPHONEADMIN HTTP_CODE = ' . $http_code );
        
        // error_log( 'IPHONEADMIN ORDER ALL DETAILS RESPONSE (UNIQUE KEY: = ' . $suwp_dhru_referenceid . ') : '. print_r($request,true) );
        
        if (is_array($request)) {

          if( $request['RESULT'] && array_key_exists('REPORT_RESULT', $request) && !empty($request['REPORT_RESULT']['REPORT']) ) {
              
            // error_log( 'IPHONEADMIN ORDER DETAILS REPORT_RESULT EXISTS AND REPORT IS NOT EMPTY - NEW2: '. print_r($request['REPORT_RESULT'],true) );

            $tmp_result = 'OTHER';
            $tmp_status = '0';

            // convert the statusId to what we've been using for STATUS value
            // gsm fusion >>> 1 = pending; 2 = completed (accepted for processing) ; 3 = unavailable (rejected); 4 = in process
            // >>> dhru fusion >>> 0 = pending; 3 = unavailable; 4 = available
            // iPhoneAdmin >>> FINISHED = available
            
            $statusId = $request['REPORT_RESULT']['STATUS'];

            switch ($statusId) {
              case 'FINISHED':
                $tmp_result = 'SUCCESS';
                $tmp_status = '4';
                break;
            }

            $reportmeta = $request['REPORT_RESULT']['REPORT'];
            $tmp_comments = 'REPORTED:' . $request['REPORT_RESULT']['REPORTED'];
            $tmp_code = NULL;

            $report_result = array();
            array_walk_recursive($reportmeta, function($value, $key) use(&$report_result) {
              // u00a7 = §, to be later replaced by <br />
              $report_result[] = $key. ': ' . str_replace("Array","",$value) . '§';
            });
            
            // error_log( "array_walk_recursive, REPORT_RESULT > REPORT ... " . print_r($report_result,true) );
            
            foreach( $report_result as $key => $value ) {
              // u00a7 = §, to be later replaced by <br />
              $tmp_code .= $value;
            }
            
            // error_log( 'IPHONEADMIN ORDER DETAILS FINAL REPORT tmp_code : ' . chr(10) . print_r($tmp_code,true) );

            // false alarm, the order is still pending
            if ( is_null($tmp_code) || empty($tmp_code)) {
              $tmp_result = 'OTHER';
              $tmp_status = '0';
            }

            // !!!!! >>>>>>>>>>>>> FOR TESTING ONLY. COMMENT OUT WHEN DONE <<<<<<<<<<<< !!!!!
            // $tmp_result = 'OTHER';
            // $tmp_status = '0';
            // !!!!! >>>>>>>>>>>>> FOR TESTING ONLY. COMMENT OUT  WHEN DONE <<<<<<<<<<<< !!!!!

            $reply = array(
              'ORDERID' => $suwp_dhru_referenceid,
              'RESULTS' => $tmp_result,
              'IMEI' => $suwp_dhru_imei,
              'STATUS' => $tmp_status,
              'CODE' => $tmp_code,
              'COMMENTS' => $tmp_comments,
            );

          } else {
            
            // error_log( 'IPHONEADMIN ORDER DETAILS REPORT_RESULT IS EMPTY/DOES NOT EXIST OR REPORT IS EMPTY: '. print_r($request['REPORT_RESULT'],true) );

            // The API has failed to report
            $reply = array(
              'ORDERID' => $suwp_dhru_referenceid,
              'RESULTS' => 'OTHER',
              'MESSAGE' => 'Order not yet ready or could not communicate with the api. Try again later.',
            );
          }
          
        } else {
          
          // error_log( 'IPHONEADMIN ORDER DETAILS ERROR RESPONSE: '. print_r($request,true) );
          
          /* Communication error */
          // print('Order is not ready or could not communicate with the api');
          $reply = array(
            'ORDERID' => $suwp_dhru_referenceid,
            'RESULTS' => 'OTHER',
            'MESSAGE' => 'Order not yet ready or could not communicate with the api. Try again later.',
            );
        }
        
      }
// IPHONEADMIN .... END

// DHRU FUSION .... START
				if ($suwp_apitype == '00') {
          
          $para['ID'] = $suwp_dhru_referenceid;

          // Debug on
          $api->debug = true;
          $request = $api->action('getimeiorder', $para);
          
          if (is_array($request)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($request), RecursiveIteratorIterator::SELF_FIRST);
            $tmp_orderid = '';
            foreach ($iterator as $key1 => $val1) {
              if ($key1 === 'ID') {
                $tmp_orderid = $val1;
              }
              if ($key1 === 'SUCCESS' || $key1 === 'ERROR') {
                $flag_continue = TRUE;
                $tmp_result = $key1;
                if (is_array($val1)) {
                  foreach ($val1 as $key2 => $val2) {
                    if (is_array($val2)) {
                      $tmp_msg = '';
                      $tmp_imei = '';
                      $tmp_status = '';
                      $tmp_code = '';
                      $tmp_comments = '';
                      foreach ($val2 as $key3 => $val3) {
                        if ($key3 === 'MESSAGE') {
                          $tmp_msg = $val3;
                        }
                        if ($key3 === 'IMEI') {
                          $tmp_imei = $val3;
                        }
                        if ($key3 === 'STATUS') {
                          $tmp_status = $val3;
                        }
                        if ($key3 === 'CODE') {
                          $tmp_code = $val3;
                        }
                        if ($key3 === 'COMMENTS') {
                          $tmp_comments = $val3;
                        }
                      }
                      switch ($tmp_result) {
                        case 'ERROR':
                          $reply = array(
                            'ORDERID' => $tmp_orderid,
                            'RESULTS' => $tmp_result,
                            'MESSAGE' => $tmp_msg,
                          );
                          break;
                          
                        case 'SUCCESS':
                          $reply = array(
                            'ORDERID' => $tmp_orderid,
                            'RESULTS' => $tmp_result,
                            'IMEI' => $tmp_imei,
                            'STATUS' => $tmp_status,
                            'CODE' => $tmp_code,
                            'COMMENTS' => $tmp_comments,
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

        // $flat_request = suwp_array_flatten($request, 2);
        // error_log('GET ORDERS FLAT : ' . print_r($flat_request, true));
        
        // JSON_FORCE_OBJECT
        // >>> $reply_serialized = serialize($reply);
        // >>> $reply['API_REPLY'] = serialize($request);

        $reply_serialized = json_encode($reply, JSON_FORCE_OBJECT);
        $reply['API_REPLY'] = json_encode($request, JSON_FORCE_OBJECT);

        $reply_status = 0;
        
        // error_log( 'REPLY DETAILS - GET IMEI ORDER DETAILS CRON $reply_serialized = ' . print_r($reply_serialized,true) );
        
        // echo '<br>';
        // print_r($reply);
        // echo '<br>';
        
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
        
        switch ($reply['RESULTS']) {
        
        /**
        'suwp_reply_success'
        'suwp_reply_reject'
        'suwp_reply_error'
        **/
         
          case 'ERROR':
            
            $comment_msg = $reply['RESULTS'] . '; ' . $reply['MESSAGE'];
            
            // reasons for possible error: usually not enough credit: Permanent ERROR, recreate order
            // create message, inform admin AND customer
            // update comment to reflect changes: 'comment_type' from 'suwp_order_success' to 'suwp_reply_error'
            
            /**
            'ORDERID' => $tmp_orderid,
            'RESULTS' => $tmp_result,
            'MESSAGE' => $tmp_msg,
            **/
            
            // do not increment the '_suwp_qty_done' in the _woocommerce_order_itemmeta table
            // since this was an error, it will not increment as 'done', giving the ability to flag order as:
            // 'wc-suwp-avail-part', 'wc-suwp-unavailable'.
            $time = current_time('mysql');
            
            // $comment = get_comment( $comment_id );
            
            $comment_content = $suwp_dhru_imei . '-php-' .  $current_order_item_id . '-php-' . $reply_serialized;
            $email_template_name = 'suwp_reply_error';
                            
            // update comment
            $commentarr = array();
            $commentarr['comment_ID'] = $comment_id;
            $commentarr['comment_content'] = $comment_content;
            $commentarr['comment_author_email'] = $suwp_dhru_username;
            $commentarr['comment_author_url'] = $suwp_dhru_url;
            $commentarr['comment_agent'] = $current_order_item_id; // v1.8.0 ; can be used later
            $commentarr['comment_type'] = $email_template_name; // no longer than 20 chars!!
            $commentarr['comment_author_IP'] = $suwp_dhru_api_key;
            $commentarr['comment_date'] = $time;
            $commentarr['comment_date_gmt'] = $time;
            
            wp_update_comment( $commentarr );
            
            // notify customer and, optionally, Bcc: admin with results 
            if ( suwp_send_cron_recipient_email( $comment_content, $email_template_name ) ) {
                
                error_log("ERROR: Get IMEI Order message successfully sent : " . $email_template_name);
                
            } else {
                 // failed to send
                error_log("ERROR: Get IMEI Order message failed to send : " . $email_template_name);
                
            }
            
            // ??? ADD A CUSTOMER COMMENT TO THE ORDER, TO BE SEEN BY CUSTOMER ...
            
            break;
            
          case 'SUCCESS':
            
            $comment_msg = $reply['CODE'];
            $reply_status = (int)$reply['STATUS'];
            
            // great, a code or unlocked status probably received
            // create message, inform admin AND customer
            // update comment to reflect changes: 'comment_type' from 'suwp_order_success' to 'suwp_reply_success'
            
            /**
            'ORDERID' => $tmp_orderid,
            'RESULTS' => $tmp_result,
            'IMEI' => $tmp_imei,
            'STATUS' => $tmp_status,
            'CODE' => $tmp_code,
            'COMMENTS' => $tmp_comments,
            **/
            
            // error_log("ACTUAL: switch( $reply_status ): " . $reply_status);
            
            $order_status = '';
            
            switch( $reply_status ) {
                case 3:
                    // unavailable
                    // reasons: Not found, reported stolen/lost, etc.
                    $order_status = 'Code Unavailable';
                    error_log("POST: switch( $reply_status ): unavailable");
                    $email_template_name = 'suwp_reply_reject'; 
                    break;
                case 4:
                    // available
                    $order_status = 'Code Available';
                    error_log("POST: switch( $reply_status ): available");
                    $email_template_name = 'suwp_reply_success';
                    break;
                default:
                // new or pending, try later
                $qty_loop--;
                
            }
            
            if( $reply_status > 1) {
                
                    // error_log("reply_status > 1, email_template_name = " . $email_template_name . ', comment_id = '. $comment_id);
                    
                // increment the '_suwp_qty_done' in the _woocommerce_order_itemmeta table
                // based on totals, later update post_status = 'wc-suwp-available', 'wc-suwp-avail-part', 'wc-suwp-unavailable'.
                if ( $plugin_admin->suwp_update_qty_done( $current_order_item_id ) ) {
                        
                    $time = current_time('mysql');
                    $comment_content = $suwp_dhru_imei . '-php-' .  $current_order_item_id . '-php-' . $reply_serialized;
                    
                    // update comment
                    $commentarr = array();
                    $commentarr['comment_ID'] = $comment_id;
                    $commentarr['comment_content'] = $comment_content;
                    $commentarr['comment_author_email'] = $suwp_dhru_username;
                    $commentarr['comment_author_url'] = $suwp_dhru_url;
                    $commentarr['comment_agent'] = $current_order_item_id; // v1.8.0 ; can be used later
                    $commentarr['comment_type'] = $email_template_name; // no longer than 20 chars!!, helps us determine later if the code is truly unavailable, even though successfully replied 
                    $commentarr['comment_author_IP'] = $suwp_dhru_api_key;
                    $commentarr['comment_date'] = $time;
                    $commentarr['comment_date_gmt'] = $time;
                    
                    $update_results = wp_update_comment( $commentarr );
                    
                    // error_log("update_results = ". $update_results);
                    
                    // notify customer and, optionally, Bcc: admin with results 
                    if ( suwp_send_cron_recipient_email( $comment_content, $email_template_name ) ) {
                        
                         error_log("SUCCESS: Get IMEI Order message successfully sent : " . $email_template_name);
                        
                    } else {
                        // failed to send
                       error_log("SUCCESS: Get IMEI Order message failed to send : " . $email_template_name);
                    
                    }
                            
                    // NEW: ADD A COMMENT TO THE ACTUAL ORDER ...
                        
                    $commentdata = array(
                        'comment_post_ID' => $current_order_id, // to which post the comment will show up
                        'comment_author' => $blog_title, //fixed value - can be dynamic 
                        'comment_author_email' => $admin_email, //fixed value - can be dynamic 
                        'comment_author_url' => $website_url, //fixed value - can be dynamic 
                        'comment_content' => 'IMEI: ' . $suwp_dhru_imei . ', ' . $order_status . ': ' . $comment_msg, //fixed value - can be dynamic
                        'comment_approved' => 1,
                        'comment_agent' => 'WooCommerce',
                        'comment_type' => 'order_note', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
                        'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
                        'user_id' => get_current_user_id(), //passing current user ID or any predefined as per the demand
                    );
                    
                    //Insert new comment and get the comment ID
                    $comment_id = wp_insert_comment( $commentdata );
                
                } //  if ( suwp_update_qty_done( $current_order_item_id ) )
                
            } // if( $reply_status > 1)
            
            break;
            
        default:
            
            // possible connection failure
            // do nothing. try again later
            
        } // switch ($reply['RESULTS'])
        
    endforeach; // foreach( $suwp_comments as $comment )
    
}

?>
