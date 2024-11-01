<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// sends a unqiue customized email to a recipient
function suwp_send_cron_recipient_email( $comment_content, $email_template_name ) {
	
	$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
	
	$options = $plugin_public->suwp_exec_get_current_options();
	
	error_log('Entering function - suwp_send_cron_recipient_email : ' . $email_template_name);
							
	// setup return variable
	$email_sent = false;
	
	// get email template data
	$email_template_object = suwp_get_cron_email_template( $comment_content, $email_template_name );
	
	// IF email template data was found
	if( !empty( $email_template_object ) ):
	
		$order_subject = '';
		$order_body = '';
		
		// get recipient data
		$recipient_data = suwp_get_cron_recipient_data( $comment_content, $email_template_name );
		
		$replace = array(
		  '{$imei}' => $recipient_data['imei'],
		  '{$orderid}' => $recipient_data['orderid'],
		  '{%Service%}' => get_option('suwp_service_label'),
		  '{%IMEI%}' => get_option('suwp_imei_label'),
		  '{%Serial Number%}' => get_option('suwp_sn_label'),
		  '{%Country%}' => get_option('suwp_country_label'),
		  '{%Network Provider%}' => get_option('suwp_network_label'),
		  '{%Brand%}' => get_option('suwp_brand_label'),
		  '{%Model%}' => get_option('suwp_model_label'),
		  '{%MEP Name%}' => get_option('suwp_mep_label'),
		  '{%KBH/KRH/ESN%}' => get_option('suwp_kbh_label'),
		  '{%Phone Number%}' => get_option('suwp_activation_label'),
		  '{%Response Email%}' => get_option('suwp_emailresponse_label'),
		  '{%Confirm Email%}' => get_option('suwp_emailconfirm_label'),
		  '{%Estimated Delivery Time%}' => get_option('suwp_deliverytime_label'),
		  '{%Code%}' => get_option('suwp_code_label'),
		);
		
		$order_subject = $email_template_object['subject'];
		$order_subject = html_entity_decode(strip_tags(suwp_string_replace_assoc($replace, $order_subject)));
		
		/**
		$recipient_data = array(
			'email' => $email,
			'customerfirstname'=> $customerfirstname,
			'imei'=> $imei,
			'orderid'=> $order_disp,
			'phoneinfo' => $phoneinfo,
			'apiprovider' => $apiprovider,
			'service' => $service,
			'processtime' => $processtime,
			'reply' => $reply,
			'description' => $description,
			'apiresults' => $apiresults,
		);
		**/
		
		$replace = array(
		  '{$customerfirstname}' => $recipient_data['customerfirstname'],
		  '{$customeremail}' => $recipient_data['email'],
		  '{$imei}' => $recipient_data['imei'],
		  '{$reply}' =>  $recipient_data['reply'],
		  '{$reason}' =>  $recipient_data['reply'],
		  '{$apiprovider}' => $recipient_data['apiprovider'],
		  '{$apierrormsg}' =>  $recipient_data['reply'],
		  '{$apierrordesc}' =>  $recipient_data['description'],
		  '{$apiresults}' =>  $recipient_data['apiresults'],
		  '{$orderid}' =>  $recipient_data['orderid'],
		  '{$phoneinfo}' =>  $recipient_data['phoneinfo'],
		  '{$service}' => $recipient_data['service'],
		  '{$processtime}' => $recipient_data['processtime'],
		  '{%Service%}' => get_option('suwp_service_label'),
		  '{%IMEI%}' => get_option('suwp_imei_label'),
		  '{%Serial Number%}' => get_option('suwp_sn_label'),
		  '{%Country%}' => get_option('suwp_country_label'),
		  '{%Network Provider%}' => get_option('suwp_network_label'),
		  '{%Brand%}' => get_option('suwp_brand_label'),
		  '{%Model%}' => get_option('suwp_model_label'),
		  '{%MEP Name%}' => get_option('suwp_mep_label'),
		  '{%KBH/KRH/ESN%}' => get_option('suwp_kbh_label'),
		  '{%Phone Number%}' => get_option('suwp_activation_label'),
		  '{%Response Email%}' => get_option('suwp_emailresponse_label'),
		  '{%Confirm Email%}' => get_option('suwp_emailconfirm_label'),
		  '{%Estimated Delivery Time%}' => get_option('suwp_deliverytime_label'),
		  '{%Code%}' => get_option('suwp_code_label'),
		);
		
		$order_body = $email_template_object['body'];
		$order_body = html_entity_decode(suwp_string_replace_assoc($replace, $order_body));
		// WP likes to replace 'br' with 'nl'. Put the 'br' back.
		$order_body = nl2br($order_body);
		
		// use wp_mail to send email
		// don't send order errors, check reply errors, or network connection failures to customers, send to admin
		switch( $email_template_name ) {
			case 'suwp_order_error':
				$email_sent = wp_mail( array( $options['suwp_copyto_ordererror'] ) , $order_subject, $order_body, $email_template_object['headers'] );
				
				break;
			case 'suwp_connect_fail':
				$email_sent = wp_mail( array( $options['suwp_copyto_checkerror'] ) , $order_subject, $order_body, $email_template_object['headers'] );
				
				break;
			case 'suwp_reply_error':
				$email_sent = wp_mail( array( $options['suwp_copyto_checkerror'] ) , $order_subject, $order_body, $email_template_object['headers'] );
				
				break;
			default:
				$email_sent = wp_mail( array( $recipient_data['email'] ) , $order_subject, $order_body, $email_template_object['headers'] );
		}
		
	endif;
	
	return $email_sent;
	
}

// hint: returns an array of email template data IF the template exists
function suwp_get_cron_email_template( $comment_content, $email_template_name ) {
	
    global $wpdb;
    
	$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
	
	$options = $plugin_public->suwp_exec_get_current_options();
	
	// setup return variable
	$template_data = array();
	
	// create new array to store email templates
	$email_templates = array();
	
	$headers = array();
	
	switch( $email_template_name ) {
		case 'suwp_order_success':
			
			$From_name = trim( $options['suwp_fromname_ordersuccess'] );
			$Bcc = trim( $options['suwp_copyto_ordersuccess'] );
			
			if( !empty($From_name) ) {
				$headers[] = 'From: '. $From_name .' <'. $options['suwp_fromemail_ordersuccess'] .'>';
			} else {
				$headers[] = 'From: '. $options['suwp_fromemail_ordersuccess'];
			}
			
			if( !empty($Bcc) ) {
				$headers[] = 'Bcc: ' . $Bcc;
			}
			
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$option_name = 'suwp_message_ordersuccess';
			
			$email_templates['suwp_order_success'] = array(
				'subject' => $options['suwp_subject_ordersuccess'],
				'body' => $options['suwp_message_ordersuccess'],
				'headers'  => $headers,
			);
			
			break;
		case 'suwp_order_error':
			
			// note: customer will not be notified of this submission error
			// no need to Bcc since being sent directly to admin
			$From_name = trim( $options['suwp_fromname_ordererror'] );
			
			if( !empty($From_name) ) {
				$headers[] = 'From: '. $From_name .' <'. $options['suwp_fromemail_ordererror'] .'>';
			} else {
				$headers[] = 'From: '. $options['suwp_fromemail_ordererror'];
			}
			
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			
			$email_templates['suwp_order_error'] = array(
				'subject' => $options['suwp_subject_ordererror'],
				'body' => $options['suwp_message_ordererror'],
				'headers'  => $headers,
			);
			
			break;
		case 'suwp_connect_fail':
			
			// note: customer will not be notified of this connection failure
			// no need to Bcc since being sent directly to admin
			$From_name = trim( $options['suwp_fromname_checkerror'] );
			
			if( !empty($From_name) ) {
				$headers[] = 'From: '. $From_name .' <'. $options['suwp_fromemail_checkerror'] .'>';
			} else {
				$headers[] = 'From: '. $options['suwp_fromemail_checkerror'];
			}
			
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			
			$email_templates['suwp_connect_fail'] = array(
				'subject' => $options['suwp_subject_checkerror'],
				'body' => $options['suwp_message_checkerror'],
				'headers'  => $headers,
			);
			
			break;
		case 'suwp_reply_success':
			
			$From_name = trim( $options['suwp_fromname_orderavailable'] );
			$Bcc = trim( $options['suwp_copyto_orderavailable'] );
			
			if( !empty($From_name) ) {
				$headers[] = 'From: '. $From_name .' <'. $options['suwp_fromemail_orderavailable'] .'>';
			} else {
				$headers[] = 'From: '. $options['suwp_fromemail_orderavailable'];
			}
			
			if( !empty($Bcc) ) {
				$headers[] = 'Bcc: ' . $Bcc;
			}
			
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			
			$email_templates['suwp_reply_success'] = array(
				'subject' => $options['suwp_subject_orderavailable'],
				'body' => $options['suwp_message_orderavailable'],
				'headers'  => $headers,
			);
			
			break;
		case 'suwp_reply_reject':
			
			$From_name = trim( $options['suwp_fromname_orderrejected'] );
			
			if( !empty($From_name) ) {
				$headers[] = 'From: '. $From_name .' <'. $options['suwp_fromemail_orderrejected'] .'>';
			} else {
				$headers[] = 'From: '. $options['suwp_fromemail_orderrejected'];
			}
			
			$Bcc = trim( $options['suwp_copyto_orderrejected'] );
			
			if( !empty($Bcc) ) {
				$headers[] = 'Bcc: ' . $Bcc;
			}
			
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			
			$email_templates['suwp_reply_reject'] = array(
				'subject' => $options['suwp_subject_orderrejected'],
				'body' => $options['suwp_message_orderrejected'],
				'headers'  => $headers,
			);
			
			break;
		case 'suwp_reply_error':
			
			// note: customer will not be notified of this fail to check/reply on order error
			// no need to Bcc since being sent directly to admin
			$From_name = trim( $options['suwp_fromname_checkerror'] );
			
			if( !empty($From_name) ) {
				$headers[] = 'From: '. $From_name .' <'. $options['suwp_fromemail_checkerror'] .'>';
			} else {
				$headers[] = 'From: '. $options['suwp_fromemail_checkerror'];
			}
			
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			
			$email_templates['suwp_reply_error'] = array(
				'subject' => $options['suwp_subject_checkerror'],
				'body' => $options['suwp_message_checkerror'],
				'headers'  => $headers,
			);
			
			break;
	}

	// IF the requested email template exists
	if( isset( $email_templates[ $email_template_name ] ) ):
	
		// add template data to return variable
		$template_data = $email_templates[ $email_template_name ];
	
	endif;
	
	// return template data
	return $template_data;
	
}

// hint: returns an array of recipient data including order information
function suwp_get_cron_recipient_data( $comment_content, $email_template_name ) {
	
    global $wpdb;
	
	// setup recipient_data
	$recipient_data = array();
	
	/**
	{$customerfirstname} = Customer first name
	{$imei} = Submitted IMEI,
	{$orderid} = Order number,
	{$phoneinfo} = Phone/Device information,
	{$service} = Service name,
	{$reply} = Admin order reply
	**/
	
	// $comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
	
	$comment_values = explode( "-php-", trim($comment_content));
	
	$comment_imei = $comment_values[0];
	$imei = '' ;
	$order_item_id = $comment_values[1];
	
	// $qty_sold = wc_get_order_item_meta( $order_item_id, '_qty', true );
    $suwp_order_id = $wpdb->get_results("select order_id from ".$wpdb->prefix."woocommerce_order_items where order_item_id=". $order_item_id );
	
	$order_id = $suwp_order_id[0]->order_id;
	
	$order = wc_get_order( $order_id );
	
    // var_dump($order);
	// error_log( 'THAT ORDER YOU WERE LOOKING FOR:' );
    // error_log( print_r($order,true) );
	// error_log( $order->get_order_number() );
	
	// Sequential Order Number support: https://docs.woocommerce.com/document/sequential-order-numbers/#faq-compatibility
	// For most plugins this is an easy process, simply replace:
	// the post-id order number (ie $order->id) with $order->get_order_number()
	// anywhere that the order number is displayed.
	
	$order_disp = $order->get_order_number();
	
	$customerfirstname = get_field('_billing_first_name', $order_id );
	$customeremail = get_field('_billing_email', $order_id );
	
	// alternate method to get_field: $customerfirstname = get_post_meta( $order_id, '_billing_first_name', true );
	$product_id = wc_get_order_item_meta( $order_item_id, '_product_id', true );
	$service = html_entity_decode( get_the_title( $product_id ), ENT_QUOTES, 'UTF-8' );
	$email = wc_get_order_item_meta( $order_item_id, 'suwp_email_response', true );

	// since v1.9.5, option to use the payment email address
	if ( !$email ) {
		$email = $customeremail;
	}
	
	$suwp_brand_id = wc_get_order_item_meta( $order_item_id, 'suwp_brand_id', true );
	$suwp_model_id = wc_get_order_item_meta( $order_item_id, 'suwp_model_id', true );
	$suwp_model_name = wc_get_order_item_meta( $order_item_id, 'suwp_model_name', true );
	$suwp_mep_id = wc_get_order_item_meta( $order_item_id, 'suwp_mep_id', true );
	$suwp_mep_name = wc_get_order_item_meta( $order_item_id, 'suwp_mep_name', true );
	$suwp_country_id = wc_get_order_item_meta( $order_item_id, 'suwp_country_id', true );
	$suwp_network_id = wc_get_order_item_meta( $order_item_id, 'suwp_network_id', true );
	$suwp_network_name = wc_get_order_item_meta( $order_item_id, 'suwp_network_name', true );
	$suwp_api1_name = wc_get_order_item_meta( $order_item_id, 'suwp_api1_name', true );
	$suwp_custom_api1_label = wc_get_order_item_meta( $order_item_id, 'suwp_custom_api1_label', true );
	$suwp_api2_name = wc_get_order_item_meta( $order_item_id, 'suwp_api2_name', true );
	$suwp_custom_api2_label = wc_get_order_item_meta( $order_item_id, 'suwp_custom_api2_label', true );
	$suwp_api3_name = wc_get_order_item_meta( $order_item_id, 'suwp_api3_name', true );
	$suwp_custom_api3_label = wc_get_order_item_meta( $order_item_id, 'suwp_custom_api3_label', true );
	$suwp_api4_name = wc_get_order_item_meta( $order_item_id, 'suwp_api4_name', true );
	$suwp_custom_api4_label = wc_get_order_item_meta( $order_item_id, 'suwp_custom_api4_label', true );

	$phoneinfo = '';

	if( $suwp_brand_id != null ){
		$phoneinfo = chr(10) . get_option('suwp_brand_label') . ': '. $suwp_brand_id;
	}
	if( $suwp_model_name != null ){
		// $model_name = $wpdb->get_results( $wpdb->prepare( "SELECT name FROM " . $wpdb->prefix. "suwp_service_model WHERE source_id=%d", $suwp_model_id ) );
		$phoneinfo = $phoneinfo . chr(10) . get_option('suwp_model_label') . ': '. $suwp_model_name;
	}
	if( $suwp_country_id != null ){
		$phoneinfo = $phoneinfo . chr(10) . get_option('suwp_country_label') . ': '. $suwp_country_id;
	}
	if( $suwp_network_name != null ){
		$phoneinfo = $phoneinfo . chr(10) . get_option('suwp_network_label') . ': '. $suwp_network_name;
	}
	if( $suwp_mep_name != null ){
		$phoneinfo = $phoneinfo . chr(10) . get_option('suwp_mep_label') . ': '. $suwp_mep_name;
	}
	if( $suwp_api1_name != null ){
		$phoneinfo = $phoneinfo . chr(10) . $suwp_custom_api1_label . ': '. $suwp_api1_name;
	}
	if( $suwp_api2_name != null ){
		$phoneinfo = $phoneinfo . chr(10) . $suwp_custom_api2_label . ': '. $suwp_api2_name;
	}
	if( $suwp_api3_name != null ){
		$phoneinfo = $phoneinfo . chr(10) . $suwp_custom_api3_label . ': '. $suwp_api3_name;
	}
	if( $suwp_api4_name != null ){
		$phoneinfo = $phoneinfo . chr(10) . $suwp_custom_api4_label . ': '. $suwp_api4_name;
	}
	
	// to obtain API Provider name: $product_id -> post_meta -> _suwp_api_provider -> post_meta -> suwp_sitename
	$apiprovider_id = get_post_meta( $product_id, '_suwp_api_provider', true );
	$processtime = get_post_meta( $product_id, '_suwp_process_time', true );
	$apiprovider = get_post_meta( $apiprovider_id, 'suwp_sitename', true );
	
	$reply = '';
	$description = '';
	
	// look closer at these as some consolidation is in order
	switch( $email_template_name ) {
		case 'suwp_order_success':
			
			/**
			case 'SUCCESS':
			$reply = array(
			  'RESULT' => $tmp_result,
			  'APIID' => $tmp_apiid,
			  'IMEI' => $tmp_imei,
			  'MESSAGE' => $tmp_msg,
			  'REFERENCEID' => $tmp_referenceid,
			);
			
			$comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
			**/
			
			$apiresults = $comment_values[2];
			
			// JSON_FORCE_OBJECT
			// $message = unserialize($comment_values[2]);
			$message = json_decode($comment_values[2], true);
			$imei = $message['IMEI'];
			$reply = $message['MESSAGE'];
			
			// build recipient for return
			$recipient_data = array(
				'email' => $email,
				'customerfirstname'=> $customerfirstname,
				'imei'=> $imei,
				'orderid'=> $order_disp,
				'phoneinfo' => $phoneinfo,
				'apiprovider' => $apiprovider,
				'service' => $service,
				'processtime' => $processtime,
				'reply' => $reply,
				'description' => $description,
				'apiresults' => $apiresults,
			);
		
			break;
		case 'suwp_order_error':
			
			/**
			case 'ERROR':
			  $reply = array(
				'RESULT' => $tmp_result,
				'APIID' => $tmp_apiid,
				'IMEI' => $tmp_imei,
				'MESSAGE' => $tmp_msg,
				'DESCRIPTION' => $tmp_full_desc,
			  );
			
			$comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
			**/
			
			$apiresults = $comment_values[2];
			// JSON_FORCE_OBJECT
			// $message = unserialize($comment_values[2]);
			$message = json_decode($comment_values[2], true);
			$imei = $message['IMEI'];
			$reply = $message['MESSAGE'];
			$description = $message['DESCRIPTION'];
			
			// build recipient for return
			$recipient_data = array(
				'email' => $email,
				'customerfirstname'=> $customerfirstname,
				'imei'=> $imei,
				'orderid'=> $order_disp,
				'phoneinfo' => $phoneinfo,
				'apiprovider' => $apiprovider,
				'service' => $service,
				'processtime' => $processtime,
				'reply' => $reply,
				'description' => $description,
				'apiresults' => $apiresults,
			);
			
			break;
		case 'suwp_connect_fail':
			
			// no reply from the API. Make something up.
			
			$apiresults = 'No API results';
			$imei = $comment_imei;
			$reply = 'Network connection failure';
			$description = 'Possible errors in Provider entry or connectivity issues';
			
			// build recipient for return
			$recipient_data = array(
				'email' => $email,
				'customerfirstname'=> $customerfirstname,
				'imei'=> $imei,
				'orderid'=> $order_disp,
				'phoneinfo' => $phoneinfo,
				'apiprovider' => $apiprovider,
				'service' => $service,
				'processtime' => $processtime,
				'reply' => $reply,
				'description' => $description,
				'apiresults' => $apiresults,
			);
			
			break;
		case 'suwp_reply_success':
			
			/**
			$reply = array(
			  'ORDERID' => $tmp_orderid,
			  'RESULTS' => $tmp_result,
			  'IMEI' => $tmp_imei,
			  'STATUS' => $tmp_status,
			  'CODE' => $tmp_code,
			  'COMMENTS' => $tmp_comments,
			);
			
            $comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
			**/
			
			$apiresults = $comment_values[2];
			// JSON_FORCE_OBJECT
			// $message = unserialize($comment_values[2]);
			$message = json_decode($comment_values[2], true);
			$imei = $message['IMEI'];
			$reply = str_replace("§","<br />",$message['CODE']);
			
			// build recipient for return
			$recipient_data = array(
				'email' => $email,
				'customerfirstname'=> $customerfirstname,
				'imei'=> $imei,
				'orderid'=> $order_disp,
				'phoneinfo' => $phoneinfo,
				'apiprovider' => $apiprovider,
				'service' => $service,
				'processtime' => $processtime,
				'reply' => $reply,
				'description' => $description,
				'apiresults' => $apiresults,
			);
			
			break;
		case 'suwp_reply_reject':
			
			/**
			$reply = array(
			  'ORDERID' => $tmp_orderid,
			  'RESULTS' => $tmp_result,
			  'IMEI' => $tmp_imei,
			  'STATUS' => $tmp_status,
			  'CODE' => $tmp_code,
			  'COMMENTS' => $tmp_comments,
			);
			
            $comment_content = $suwp_dhru_imei . '-php-' .  $current_order_item_id . '-php-' . $reply_serialized . '-php-' . $reply['API_REPLY']; 
			**/
			
			$apiresults = $comment_values[2];
			// JSON_FORCE_OBJECT
			// $message = unserialize($comment_values[2]);
			$message = json_decode($comment_values[2], true);
			$imei = $message['IMEI'];
			$reply = str_replace("§","<br />",$message['CODE']);
			
			// build recipient for return
			$recipient_data = array(
				'email' => $email,
				'customerfirstname'=> $customerfirstname,
				'imei'=> $imei,
				'orderid'=> $order_disp,
				'phoneinfo' => $phoneinfo,
				'apiprovider' => $apiprovider,
				'service' => $service,
				'processtime' => $processtime,
				'reply' => $reply,
				'description' => $description,
				'apiresults' => $apiresults,
			);
			
			break;
		case 'suwp_reply_error':
			
			/**
			case 'ERROR':
			  $reply = array(
				'ORDERID' => $tmp_orderid,
				'RESULTS' => $tmp_result,
				'MESSAGE' => $tmp_msg,
			  );
			
			$comment_content = $suwp_dhru_imei . '-php-' . $current_order_item_id . '-php-' . $reply_serialized;
			**/
			
			$apiresults = $comment_values[2];
			
			// JSON_FORCE_OBJECT
			// $message = unserialize($comment_values[2]);
			$message = json_decode($comment_values[2], true);

			$imei = $comment_imei;
			$description = 'API Service Provider Order ID# = ' . $message['ORDERID'] . '; Results = '. $message['RESULTS'];
			$reply = $message['MESSAGE'];
			
			// build recipient for return
			$recipient_data = array(
				'email' => $email,
				'customerfirstname'=> $customerfirstname,
				'imei'=> $imei,
				'orderid'=> $order_disp,
				'phoneinfo' => $phoneinfo,
				'apiprovider' => $apiprovider,
				'service' => $service,
				'processtime' => $processtime,
				'reply' => $reply,
				'description' => $description,
				'apiresults' => $apiresults,
			);
			
			break;
	}
	
	// return recipient_data
	return $recipient_data;
	
}

function suwp_string_replace_assoc(array $replace, $target) {
	return str_replace(array_keys($replace), array_values($replace), $target);
}

function suwp_field_replace_preg_match($args) {
	$inputval = $args;
	preg_match('~{%(.*?)%}~', $inputval, $outputval);
	if ( isset( $outputval[1] ) && $outputval[1] != '' ) {
		$thevalue = '{%' . $outputval[1] . '%}';
		$replace = array(
			$thevalue => $outputval[1],
		);
		$args = suwp_string_replace_assoc($replace, $args);
		return $args;
	} else {
		return false;
	}
}

function suwp_field_extract_preg_match($args) {
	$inputval = $args;
	preg_match('~{%(.*?)%}~', $inputval, $outputval);
	if ( isset( $outputval[1] ) && $outputval[1] != '' ) {
		$args = $outputval[1];
		return $args;
	} else {
		return false;
	}
}

function suwp_array_not_unique($raw_array) {
    $dupes = array();
    natcasesort($raw_array);
    reset($raw_array);

    $old_key   = NULL;
    $old_value = NULL;
    foreach ($raw_array as $key => $value) {
        if ($value === NULL) { continue; }
        if (strcasecmp($old_value, $value) === 0) {
            $dupes[$old_key] = $old_value;
            $dupes[$key]     = $value;
        }
        $old_value = $value;
        $old_key   = $key;
    }
    return $dupes;
}

?>
