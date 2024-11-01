// wait until the page and jQuery have loaded before running the code below
jQuery(document).ready(function($){
	
	// alert("PUBLIC JS - phpInfo.suwp_siteurl = " + phpInfo.suwp_siteurl);
	
	// values provided by Product page.
	// Need to modify for other pages
    // var $home_dd = phpInfo.suwp_home;
    var $siteurl_dd = phpInfo.suwp_siteurl;
	var doc_pathname = '/wp-admin/admin-ajax.php';
	
	// setup our wp ajax URL
	var wpajax_url =  $siteurl_dd + doc_pathname;
	
	/**
	alert("wpajax_url = " + wpajax_url );
	alert("document.location.protocol = " + document.location.protocol );
	alert("document.location.host = " + document.location.host );
	alert("document.location.pathname = " + document.location.pathname );
	alert("MODIFIED doc_pathname = " + doc_pathname );
	**/
	
    $(".suwp-group").each(function(){
		// alert("THE variations VALUE = " + $(this).find(':input').attr('name') ); //<-- Should return all input elements in that specific form.
	});
	
	$(".suwp-group :input").change(function() { // :input includes textfields, etc
		// var name = $(this).attr('name');
        // var val = $(this).val();
		// alert("suwp-group :input.change, name = " + name + ", value = " + val );    
      });
    
    var $post_id_dd = $('[name="add-to-cart"]');
	
	// create references to the brand/model dropdown fields for later use.
    var $brandmodeldrop_dd = $('[name="suwp-brand-model-drop"]');
    var $countrynetworkdrop_dd = $('[name="suwp-country-network-drop"]');
    var $brands_dd = $('[name="suwp-brand-id"]');
    var $models_dd = $('[name="suwp-model-id"]'); 
    var $modelname_dd = $('[name="_suwp-model-name"]');
	
    // create references to the country/network dropdown fields for later use.
    var $countries_dd = $('[name="suwp-country-id"]');
    var $networks_dd = $('[name="suwp-network-id"]'); 
    var $networkname_dd = $('[name="_suwp-network-name"]');

    // create references to the mep dropdown field for later use.
    var $meps_dd = $('[name="suwp-mep-id"]'); 
    var $mepname_dd = $('[name="_suwp-mep-name"]');
	
	var $email_response_dd = $('[name="suwp-email-response"]');
	var label_email_response = phpInfo.suwp_emailresponse_label;

	var $email_confirm_dd = $('[name="suwp-email-confirm"]');
	var label_email_confirm = phpInfo.suwp_emailconfirm_label;
	
	var $is_usepaymentemail = false;

    var $payment_email_hidden = $('[name="suwp-use-payment-email-hidden"]');
	
	if( typeof $payment_email_hidden.val() === 'undefined' ) {
		// not hiding the response email fields, do the validation
		// console.log("RUN THE CHECKBOX");
		$('#suwp-payment-email').on('change',function(){
			var _val = $(this).is(':checked') ? true : false;
			// alert(_val);
			$is_usepaymentemail = _val;
			$('[name="suwp-email-response"]').prop('readOnly', _val);
			$('[name="suwp-email-confirm"]').prop('readOnly', _val);
			if ($is_usepaymentemail) {
				$('[name="suwp-email-response"]').val(null);
				$('[name="suwp-email-confirm"]').val(null);
				$('label[for="email-confirm"]').hide();
				$('#email-response, #email-confirm').hide();
				$(this).val('1');
			} else {
				$('label[for="email-confirm"]').show();
				$('#email-response, #email-confirm').show();
				$(this).val('0');
			}
		});

	} else {
		// hiding the response email fields, use the billing email and let it flow
		// console.log("DO NOT RUN THE CHECKBOX");
		$is_usepaymentemail = true;
	}

    // run the populate_brandmodel_fields function and run it every time a value changes
	if( typeof $brands_dd.val() != 'undefined' ) {
		populate_brandmodel_fields();
		$('select').change(function() {
			populate_brandmodel_fields();
		});
	}
	
	// run the populate_countrynetwork_fields function run it every time a value changes
	if( typeof $countries_dd.val() != 'undefined' ) {
		// alert("$countries_dd!");
		populate_countrynetwork_fields();
		$('select').change(function() {
			populate_countrynetwork_fields();
		});
	}
	
	// run the populate_mep_fields function and run it every time a value changes
	if( typeof $meps_dd.val() != 'undefined' ) {
		// alert("$meps_dd!");
		populate_mep_fields();
		$('select').change(function() {
			populate_mep_fields();
		});
	}

	/**
	 * Sort object properties (only own properties will be sorted).
	 * @param {object} obj object to sort properties
	 * @param {string|int} sortedBy 1 - sort object properties by specific value.
	 * @param {bool} isNumericSort true - sort object properties as numeric value, false - sort as string value.
	 * @param {bool} reverse false - reverse sorting.
	 * @returns {Array} array of items in [[key,value],[key,value],...] format.
	 */
	function sort_properties(obj, sortedBy, isNumericSort, reverse) {
		sortedBy = sortedBy || 1; // by default first key
		isNumericSort = isNumericSort || false; // by default text sort
		reverse = reverse || false; // by default no reverse

		var reversed = (reverse) ? -1 : 1;

		var sortable = [];
		for (var key in obj) {
			if (obj.hasOwnProperty(key)) {
				sortable.push([key, obj[key]]);
			}
		}
		if (isNumericSort)
			sortable.sort(function (a, b) {
				return reversed * (a[1][sortedBy] - b[1][sortedBy]);
			});
		else
			sortable.sort(function (a, b) {
				var x = a[1][sortedBy].toLowerCase(),
					y = b[1][sortedBy].toLowerCase();
				return x < y ? reversed * -1 : x > y ? reversed : 0;
			});

		return sortable; // array in format [ [ key1, val1 ], [ key2, val2 ], ... ]
	}
	
	// $('select').change(function() {
	function populate_brandmodel_fields() {
		
		// set up form action url
		var form_action_url = wpajax_url + '?action=suwp_brandmodel_populate_values';
		
		var form_data = {

            // action needs to match the action hook part after wp_ajax_nopriv_ and wp_ajax_ in the server side script.
            // pass all the currently selected values to the server side script.
			'brand_model_drop' : $brandmodeldrop_dd.val(),
            'brand' : $brands_dd.val(),
            'model' : $models_dd.val(),
			'post_id' : $post_id_dd.val(),
			
        };
		
		// send the file to php for processing...
		$.ajax({
			url: form_action_url,
			type: 'post',
			dataType: 'json',
			data: form_data,
			success: function( response ) {
				
				all_values = response;
				
				$brands_dd.html('').append($('<option>').text('-----------------'));
				$models_dd.html('').append($('<option>').text('-----------------'));
				
				$.each(all_values.brands, function() {
					
					$option = $("<option>").text(this).val(this);
					if (all_values.current_brand == this) {
						$option.attr('selected','selected');
					}
					$brands_dd.append($option);
				});
				
				$modelname_dd.val('');
				$.each(all_values.models, function(key, value) {
					
					$option = $("<option>").text(key).val(value);
					
					if (all_values.current_model == $option.val()) {
						$option.attr('selected','selected');
						$modelname_dd.val(key);
					}
					$models_dd.append($option);
				});	
			}
		});
	}
		
	function populate_countrynetwork_fields() {
		
		// set up form action url
		var form_action_url = wpajax_url + '?action=suwp_countrynetwork_populate_values';
		
		var form_data = {

            // action needs to match the action hook part after wp_ajax_nopriv_ and wp_ajax_ in the server side script.
            // pass all the currently selected values to the server side script.
			'country_network_drop' : $countrynetworkdrop_dd.val(),
            'country' : $countries_dd.val(),
            'network' : $networks_dd.val(),
			'post_id' : $post_id_dd.val(),
			
        };
		
		// send the file to php for processing...
		$.ajax({
			url: form_action_url,
			type: 'post',
			dataType: 'json',
			data: form_data,
			success: function( response ) {
				
				all_values = response;
				// alert("all_values.countries = " + JSON.stringify( all_values.countries ) );
				// alert("all_values.networks = " + JSON.stringify( all_values.networks ) );

				$countries_dd.html('').append($('<option>').text('-----------------'));
				$networks_dd.html('').append($('<option>').text('-----------------'));
				
				$.each(all_values.countries, function() {
					
					$option = $("<option>").text(this).val(this);
					if (all_values.current_country == this) {
						$option.attr('selected','selected');
					}
					$countries_dd.append($option);
				});

				$networkname_dd.val('');
				$.each(all_values.networks, function(key, value) {
					
					$option = $("<option>").text(key).val(value);
					
					if (all_values.current_network == $option.val()) {
						$option.attr('selected','selected');
						$networkname_dd.val(key);
					}
					$networks_dd.append($option);
				});	
			}
		});
	}
		
	function populate_mep_fields() {
		
		// set up form action url
		var form_action_url = wpajax_url + '?action=suwp_mep_populate_values';
		
		var form_data = {

            // action needs to match the action hook part after wp_ajax_nopriv_ and wp_ajax_ in the server side script.
            // pass all the currently selected values to the server side script.
            'mep' : $meps_dd.val(),
			'post_id' : $post_id_dd.val(),
			
        };
		
		// send the file to php for processing...
		$.ajax({
			url: form_action_url,
			type: 'post',
			dataType: 'json',
			data: form_data,
			success: function( response ) {
				
				all_values = response;
				
				$meps_dd.html('').append($('<option>').text('-----------------'));
				
				// alert( "all_values.meps " + JSON.stringify( all_values.meps ) );
				
				$mepname_dd.val('');
				$.each(all_values.meps, function(key, value) {
					
					$option = $("<option>").text(key).val(value);
					
					if (all_values.current_mep == $option.val()) {
						$option.attr('selected','selected');
						$mepname_dd.val(key);
					}
					$meps_dd.append($option);
				});
			}
		});
	}
	
	// cart validation without losing browswer values
	$(".cart").submit(function() {
		
		var $arrayLength;
		var $flag_continue = true;
		var $flag_msg_blankempty = [];
		var $flag_msg_incorrectlength = [];
		var $flag_msg_incorrectchar = [];
		var $flag_msg_invalidimei = [];
		var $flag_msg_duplicatenums = [];
		var $flag_msg_mixedemail = [];
		var $flag_msg_invalidemail = [];
		var $flag_msg_exceedednum = [];
		
		// create references for later verification
		var $is_hideimei = $('[name="suwp-is-hideimei"]');
		var $is_allow_text_dd = $('[name="suwp-is-allow-text"]');
		var $is_serial_limit_dd = $('[name="suwp-serial-limit"]');
		var $is_count_length_dd = $('[name="suwp-is-count-length"]');
		var $serial_length_dd = $('[name="suwp-serial-length"]');
		var $is_imei_dd = $('[name="suwp-is-imei"]');
		var $is_api1_dd = $('[name="suwp-is-ap1"]');
		var $is_api2_dd = $('[name="suwp-is-ap2"]');
		var $is_api3_dd = $('[name="suwp-is-ap3"]');
		var $is_api4_dd = $('[name="suwp-is-ap4"]');
		var $is_network_dd = $('[name="suwp-is-country-network"]');
		var $is_model_dd = $('[name="suwp-is-brand-model"]');
		var $is_mep_dd = $('[name="suwp-is-mep"]');

		var $imeis_dd = $('[name="suwp-imei-values"]');
		var label_imei = phpInfo.suwp_imei_label;
		var label_sn = phpInfo.suwp_sn_label;

		var $api1_dd = $('[name="suwp-api1-name"]');
		var label_api1 = $('label[for="api1-label"]').text();

		var $api2_dd = $('[name="suwp-api2-name"]');
		var label_api2 = $('label[for="api2-label"]').text();

		var $api3_dd = $('[name="suwp-api3-name"]');
		var label_api3 = $('label[for="api3-label"]').text();

		var $api4_dd = $('[name="suwp-api4-name"]');
		var label_api4 = $('label[for="api4-label"]').text();

		var $network_dd = $('[name="suwp-network-id"]');
		var label_network = phpInfo.suwp_network_label;

		// >>> var $country_dd = $('[name="suwp-country-id"]');
		// >>> var label_country = phpInfo.suwp_country_label;

		var $brand_dd = $('[name="suwp-brand-id"]');
		var label_brand = phpInfo.suwp_brand_label;

		var $model_dd = $('[name="suwp-model-id"]');
		var label_model = phpInfo.suwp_model_label;

		var $mep_dd = $('[name="suwp-mep-id"]');
		var label_mep = phpInfo.suwp_mep_label;

		// >>> var suwp_not_required_msg = phpInfo.suwp_not_required_msg;
		var suwp_blank_msg = phpInfo.suwp_blank_msg;
		// >>> var suwp_payment_email_msg = phpInfo.suwp_payment_email_msg;
		var suwp_invalidemail_msg = phpInfo.suwp_invalidemail_msg;
		var suwp_nonmatching_msg = phpInfo.suwp_nonmatching_msg;
		var suwp_invalidentry_msg = phpInfo.suwp_invalidentry_msg;
		var suwp_exceeded_msg = phpInfo.suwp_exceeded_msg;
		var suwp_invalidchar_msg = phpInfo.suwp_invalidchar_msg;
		var suwp_invalidlength_msg = phpInfo.suwp_invalidlength_msg;
		var suwp_invalidformat_msg = phpInfo.suwp_invalidformat_msg;
		var suwp_dupvalues_msg = phpInfo.suwp_dupvalues_msg;

		if ( $is_api1_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api1_dd.val().trim() === '' ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_api1 + '</strong>');
			}
		}
		
		if ( $is_api2_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api2_dd.val().trim() === '' ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_api2 + '</strong>');
			}
		}
		
		if ( $is_api3_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api3_dd.val().trim() === '' ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_api3 + '</strong>');
			}
		}
		
		if ( $is_api4_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api4_dd.val().trim() === '' ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_api4 + '</strong>');
			}
		}

		if ( $is_network_dd.val() ) {
			// confirm that a network was selected
			if ( !($network_dd.val() > 0) ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_network + '</strong>');
			}
		}
		
		if ( $is_model_dd.val() ) {
			// confirm that a brand was selected
			if ( !($model_dd.val() > 0) ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_model + '</strong>');
			}
		}
		
		if ( $is_mep_dd.val() === 'Required') {
			// confirm that a mep was selected
			if ( !($mep_dd.val() > 0)  ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_mep + '</strong>');
			}
		}
		
		if ( !$is_hideimei.val() ) {
			if ( $is_imei_dd.val() ) {
				// dealing with an imei
				if ( $imeis_dd.val().trim() === '') {
					$flag_continue = false;
					$flag_msg_blankempty.push('<strong>' + label_imei + '</strong>');
				} else {
					// alert( "THE VALUES OF IMEI = " + $imeis_dd.val().split('\n'));
					var $array_vals = $imeis_dd.val().split('\n');
					// alert( "THE ARRAY OF IMEI length = " + $array_vals.length);
					var $chk_dup_imei = [];
					var count = 0;
					for(var x = 0; x < $array_vals.length; ++x) {
						// multiple ways to skin an IMEI
						// >>>>>>>>>> var $trimmed = $array_vals[count].str.replace(/ /g,""); // all spaces
						// >>>>>>>>>> var $trimmed = $array_vals[count].str.replace(/\s+/g,''); // all spaces
						// >>>>>>>>>> var $trimmed = $array_vals[count].trim(); // only leading/trailing spaces
						var $trimmed1 = $array_vals[count].trim(); // only leading/trailing spaces
						var $trimmed = $trimmed1.replace(/ /g,"");  // remove ALL spaces
						// alert( "THE VALUE OF TRIMMED IMEI = " + $trimmed);

						if ( $trimmed ) {
							// alert("IMEI NOT NULL");
							$chk_dup_imei.push( $trimmed );
						}
						count++;
					}
					$array_vals = $chk_dup_imei;
					// alert( "THE ARRAY OF IMEI NEW length = " + $array_vals.length);
					$chk_dup_imei = [];
					count = 0;
					for(var i = 0; i < $array_vals.length; ++i) {
						// alert( "THE VALUE OF IMEI  = " + $array_vals[count]);
						$chk_dup_imei.push( $array_vals[count] );
						// alert( count + ", IMEI ITERATION = " + $array_vals[count] + ", length = " + $array_vals[count].length );
						if ( $is_count_length_dd.val() ) {
							if ( $serial_length_dd.val() == 15 ) {
								// if 15, then confirm valid imei
								// alert( "IMEI = " + $array_vals[count] + ", DO THE IMEI VERIFCATION THING = " + $serial_length_dd.val() );
								if ( isIMEI($array_vals[count]) ) {
									// alert("A VALID IMEI : " + $array_vals[count]);
								} else {
									// Invalid entry: {IMEI}
									$flag_continue = false;
									$flag_msg_invalidimei.push(suwp_invalidentry_msg + ': <strong>' + $array_vals[count] + '</strong>');	
									// alert("AN INVALID IMEI : " + $array_vals[count]);
								}
							} else if ( $array_vals[count].length != $serial_length_dd.val() ) {
								// Invalid entry: {IMEI} = {numChars}
								$flag_continue = false;
								$flag_msg_incorrectlength.push(suwp_invalidentry_msg + ': <strong>' + $array_vals[count] + ' = ' + $array_vals[count].length + '</strong>');
								// alert( "IMEI NOT THE REQUIRED CHARACTER LENGTH = " + $serial_length_dd.val() );
							}
						}
						count++;
					}
					var $unique_vals = [...new Set($array_vals)];
					// alert( "THE NUMBER OF IMEI = " + count + ", chk_dup_imei = " + $chk_dup_imei.length + ", unique_vals = " + $unique_vals.length );
					if ( $chk_dup_imei.length != $unique_vals.length ) {
						$flag_continue = false;
						var $dups = arrayNotUnique($chk_dup_imei);
						// alert( "IMEIs CONTAIN DUPLICATE VALUES: duplicates = " + $dups.length);
						var $dup_txt = '';
						count = 0;
						for(var i = 0; i < $dups.length; ++i) {
							$dup_txt += '<strong>' + $dups[count] + '</strong><br>';
							count++;
						}
						// alert( "IMEIs DUP TEXT: dup_txt = " + $dup_txt);
						$flag_msg_duplicatenums.push($dup_txt);		
						// alert( "IMEIs CONTAIN DUPLICATE VALUES" );
					} else {
						// alert( "IMEIs DOES NOT CONTAIN DUPLICATE VALUES" );
					}
					if ( $is_serial_limit_dd.val() ) {
						// alert( "LIMITING THE NUMBER OF IMEI = " + $is_serial_limit_dd.val() );
						if ( $array_vals.length > $is_serial_limit_dd.val() ) {
							$flag_continue = false;
							$flag_msg_exceedednum.push('<strong>(' + label_imei + ') ' + $array_vals.length + ' != ' + $is_serial_limit_dd.val() + '</strong>');
							// alert( "EXCEEDED THE TOTAL NUMBER OF ALLOWED IMEI");
						} else {
							// alert( "DID NOT EXCEED THE TOTAL NUMBER OF ALLOWED IMEI");
						}
					} else {
						// alert( "NOT LIMITING THE NUMBER OF IMEI = " + $is_serial_limit_dd.val() );
					}
				}
			} else {
				// dealing with a serial number
				if ( $imeis_dd.val().trim() === '') {
					$flag_continue = false;
					$flag_msg_blankempty.push('<strong>' + label_sn + '</strong>');
				} else {
					// alert( "THE VALUES OF S/N = " + $imeis_dd.val().split('\n'));
					var $array_vals = $imeis_dd.val().split('\n');
					// alert( "THE ARRAY OF S/N length = " + $array_vals.length);
					var $chk_dup_sn = [];
					var count = 0;
					for(var x = 0; x < $array_vals.length; ++x) {
						var $trimmed1 = $array_vals[count].trim(); // only leading/trailing spaces
						var $trimmed = $trimmed1.replace(/ /g,""); // remove ALL spaces
						if ( $trimmed ) {
							// alert("S/N NOT NULL");
							$chk_dup_sn.push( $trimmed );
						}
						count++;
					}
					$array_vals = $chk_dup_sn;
					// alert( "THE ARRAY OF S/N NEW length = " + $array_vals.length);
					$chk_dup_sn = [];
					count = 0;
					for(var i = 0; i < $array_vals.length; ++i) {
						// alert( "THE VALUE OF S/N  = " + $array_vals[count] + ", count i = " + i );
						$chk_dup_sn.push( $array_vals[count] );
						// alert( count + ", S/N ITERATION = " + $array_vals[count] + ", length = " + $array_vals[count].length );
						if ( $is_count_length_dd.val() ) {
							if( !(isNaN($serial_length_dd.val())) ) {
								if ( $array_vals[count].length != $serial_length_dd.val() ) {
									// Invalid entry: {S/N} = {numChars}
									$flag_continue = false;
									$flag_msg_incorrectlength.push(suwp_invalidentry_msg + ': <strong>' + $array_vals[count] + ' = ' + $array_vals[count].length + '</strong>');
									// alert( "S/N NOT THE ALLOWED CHARACTER LENGTH = " + $serial_length_dd.val() );
								}
							}
						} else {
							// alert( "NOT COUNTING S/N THE LENGTH");
						}
						if ( !($is_allow_text_dd.val()) ) {			
							// alert( "S/N NOT ALLOWING TEXT = " + $is_allow_text_dd.val() );
							if(isNaN($array_vals[count])){
								// Invalid entry: {S/N}
								$flag_continue = false;
								$flag_msg_incorrectchar.push(suwp_invalidentry_msg + ': <strong>' + $array_vals[count] + '</strong>');	
								// alert($array_vals[count] + " : is NOT a number");
							}else{
								// alert($array_vals[count] + " : IS a number");
							}
						}
						count++;
					}
					var $unique_vals = [...new Set($array_vals)];
					// alert( "THE NUMBER OF S/N = " + count + ", chk_dup_sn = " + $chk_dup_sn.length + ", unique_vals = " + $unique_vals.length );
					if ( $chk_dup_sn.length != $unique_vals.length ) {
						// alert( "S/N CONTAIN DUPLICATE VALUES, $chk_dup_sn.length = " + $chk_dup_sn.length);
						$flag_continue = false;
						var $dups = arrayNotUnique($chk_dup_sn);
						// alert( "S/N CONTAIN DUPLICATE VALUES: duplicates = " + $dups.length);
						var $dup_txt = '';
						count = 0;
						for(var i = 0; i < $dups.length; ++i) {
							$dup_txt += '<strong>' + $dups[count] + '</strong><br>';
							count++;
						}
						// alert( "S/N DUP TEXT: dup_txt = " + $dup_txt);
						$flag_msg_duplicatenums.push($dup_txt);
					} else {
						// alert( "S/N DOES NOT CONTAIN DUPLICATE VALUES" );
					}
					if ( $is_serial_limit_dd.val() ) {
						// alert( "LIMITING THE NUMBER OF S/N = " + $is_serial_limit_dd.val() );
						if ( $array_vals.length > $is_serial_limit_dd.val() ) {
							$flag_continue = false;
							$flag_msg_exceedednum.push('<strong>(' + label_sn + ') ' + $array_vals.length + ' != ' + $is_serial_limit_dd.val() + '</strong>');
							// alert( "EXCEEDED THE TOTAL NUMBER OF ALLOWED S/N");
						} else {
							// alert( "DID NOT EXCEED THE TOTAL NUMBER OF ALLOWED S/N");
						}
					} else {
						// alert( "NOT LIMITING THE NUMBER OF S/N = " + $is_serial_limit_dd.val() );
					}
					
				}
			}
		} // $is_hideimei

		if ( !$is_usepaymentemail ) {

			// verify the response email value
			if ( $email_response_dd.val().trim() == '' ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_email_response + '</strong>');
			} else {
				if ( !(ValidateEmail($email_response_dd.val())) ) {
					$flag_continue = false;
					$flag_msg_invalidemail.push('<strong>' + $email_response_dd.val() + '</strong>');
					// alert("RESPONSE EMAIL IS MALFORMED");
				}
			}
			// verify the confirm email value
			if ( $email_confirm_dd.val().trim() == '' ) {
				$flag_continue = false;
				$flag_msg_blankempty.push('<strong>' + label_email_confirm + '</strong>');
			} else {
				if ( !(ValidateEmail($email_confirm_dd.val())) ) {
					$flag_continue = false;
					$flag_msg_invalidemail.push('<strong>' + $email_confirm_dd.val() + '</strong>');
					// alert("CONFIRM EMAIL IS MALFORMED");
				}
			}
			// verify matchting email values
			if ( !($email_response_dd.val() === $email_confirm_dd.val()) && $email_response_dd.val().trim() != '' && $email_confirm_dd.val().trim() != '') {
				$flag_continue = false;
				$flag_msg_mixedemail.push('<strong>' + $email_response_dd.val() + ' != ' + $email_confirm_dd.val() + '</strong>');
				// alert("EMAILS DO NOT MATCH");
			}

		} // if ( !$is_usepaymentemail )
		
		// Not Required
		// Reply to Billing Email Address
		// Invalid entry:
		// blank/empty: Please select or enter at least one value in the following field(s):
		// incorrect length: Number of characters required = {numChars}. Invalid entry: {IMEI} = {numChars}
		// incorrect char: Digits only: no letters, punctuation, or spaces allowed. Invalid entry: {IMEI}
		// invalid imei: Not a valid entry or format: {IMEI}
		// duplicate nums: Duplicate values are not allowed: {IMEI}
		// non-matching email: Sorry, the email addresses do not match: {emailResponse}
		// invalid email: Please enter a valid email address: {email}
		// exceeded total allowed: Exceeded the total number allowed {num}: {imei;s/n}: {num_entered} != {num}

		// blank/empty: 
		var $txt_blankempty = suwp_blank_msg + ":<br>";
		$arrayLength = $flag_msg_blankempty.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_blankempty = $txt_blankempty + $flag_msg_blankempty[i] + "<br>";
		}
		// incorrect length:
		var $txt_incorrectlength = suwp_invalidlength_msg + " = " + $serial_length_dd.val() + ".<br>";
		$arrayLength = $flag_msg_incorrectlength.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_incorrectlength = $txt_incorrectlength + $flag_msg_incorrectlength[i] + "<br>";
		}
		// incorrect char:
		var $txt_incorrectchar = suwp_invalidchar_msg + ":<br>";
		$arrayLength = $flag_msg_incorrectchar.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_incorrectchar = $txt_incorrectchar + $flag_msg_incorrectchar[i] + "<br>";
		}
		// invalid imei:
		var $txt_invalidimei = suwp_invalidformat_msg + ":<br>";
		$arrayLength = $flag_msg_invalidimei.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_invalidimei = $txt_invalidimei + $flag_msg_invalidimei[i] + "<br>";
		}
		// duplicate nums:
		var $txt_duplicatenums = suwp_dupvalues_msg + ":<br>";
		$arrayLength = $flag_msg_duplicatenums.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_duplicatenums = $txt_duplicatenums + $flag_msg_duplicatenums[i] + "<br>";
		}
		// non-matching email: 
		var $txt_mixedemail = suwp_nonmatching_msg + ":<br>";
		$arrayLength = $flag_msg_mixedemail.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_mixedemail = $txt_mixedemail + $flag_msg_mixedemail[i] + "<br>";
		}
		// invalid email: 
		var $txt_invalidemail = suwp_invalidemail_msg + ":<br>";
		$arrayLength = $flag_msg_invalidemail.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_invalidemail = $txt_invalidemail + $flag_msg_invalidemail[i] + "<br>";
		}
		// exceeded total allowed:
		var $txt_exceedednum = suwp_exceeded_msg + " (" + $is_serial_limit_dd.val() + "):<br>";
		$arrayLength = $flag_msg_exceedednum.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt_exceedednum = $txt_exceedednum + $flag_msg_exceedednum[i] + "<br>";
		}
		
		// success, error, warning, info, question
		if ( !$flag_continue ) {

			if ($flag_msg_invalidimei.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_invalidimei,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_duplicatenums.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_duplicatenums,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_exceedednum.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_exceedednum,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_incorrectlength.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_incorrectlength,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_incorrectchar.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_incorrectchar,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_blankempty.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_blankempty,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_mixedemail.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_mixedemail,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
			if ($flag_msg_invalidemail.length > 0) {
				Swal.fire({
					title: 'Error!',
					text: 'Add to cart',
					html: $txt_invalidemail,
					type: 'error',
					confirmButtonText: 'Ok'
				})
				return false;
			}
		}

		// var label = $('#suwp_msg_license');
		// var month = label.attr('month');
		// var year = label.attr('year');
		// var htm = label.html();
		// var text = label.text();
		
		// var x = document.getElementById("#suwp_msg_license").htmlFor;
		// var x = document.getElementById("suwp_msg_license");
		// x.htmlFor;
		// $('.suwp_msg_license').html('');
		// $('.suwp_msg_license').html('');

		// var label_imei = document.getElementById("#suwp-imei-values-label").htmlFor;
		// var label_imei =  $('#suwp-imei-values-label');
		
	});

	function ValidateEmail(email) {
		if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
			return (true)
		}
		return (false)
	}

	function isIMEI (s) {
		var etal = /^[0-9]{15}$/;
			if (!etal.test(s))
			return false;
			sum = 0; mul = 2; l = 14;
			for (i = 0; i < l; i++) {
			digit = s.substring(l-i-1,l-i);
			tp = parseInt(digit,10)*mul;
			if (tp >= 10)
					sum += (tp % 10) +1;
			else
					sum += tp;
			if (mul == 1)
					mul++;
			else
					mul--;
			}
			chk = ((10 - (sum % 10)) % 10);
			if (chk != parseInt(s.substring(14,15),10))
			return false;
			return true;
	}

	function arrayNotUnique(arra1) {
        var object = {};
        var result = [];

        arra1.forEach(function (item) {
          if(!object[item])
              object[item] = 0;
            object[item] += 1;
        })

        for (var prop in object) {
           if(object[prop] >= 2) {
               result.push(prop);
           }
        }

        return result;
	}
	  
});
