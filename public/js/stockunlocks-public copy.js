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
	
    // run the populate_brandmodel_fields function, and additionally run it every time a value changes
	if( typeof $brands_dd.val() != 'undefined' ) {
		populate_brandmodel_fields();
		$('select').change(function() {
			populate_brandmodel_fields();
		});
	}
	
	// run the populate_countrynetwork_fields function, and additionally run it every time a value changes
	if( typeof $countries_dd.val() != 'undefined' ) {
		// alert("$countries_dd!");
		populate_countrynetwork_fields();
		$('select').change(function() {
			populate_countrynetwork_fields();
		});
	}
	
	// run the populate_mep_fields function, and additionally run it every time a value changes
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
				
				$brands_dd.html('').append($('<option value="">').text('-----------------'));
				$models_dd.html('').append($('<option value="">').text('-----------------'));
				
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

				$countries_dd.html('').append($('<option value="">').text('-----------------'));
				$networks_dd.html('').append($('<option value="">').text('-----------------'));
				
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
				
				$meps_dd.html('').append($('<option value="">').text('-----------------'));
				
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
		
		// Please select or enter at least one value in the following field(s):
		// Number of characters allowed = {numChars}. Invalid entry: {IMEI} = {numChars}.
		// Digits only: no letters, punctuation, or spaces allowed. Invalid entry: {IMEI}.
		// Not a valid entry: {IMEI}
		// Duplicate values are not allowed: {IMEI}
		// Sorry the email addresses must match: {emailResponse}
		// Please enter a valid email address: {email}
		
		var $txt;
		var $arrayLength;
		var $flag_continue = true;
		var $flag_msg_string = [];
		
		// create references for later verification
		var $is_imei_dd = $('[name="suwp-is-imei"]');
		var $is_api1_dd = $('[name="suwp-is-ap1"]');
		var $is_api2_dd = $('[name="suwp-is-ap2"]');
		var $is_api3_dd = $('[name="suwp-is-ap3"]');
		var $is_api4_dd = $('[name="suwp-is-ap4"]');
		var $is_network_dd = $('[name="suwp-is-network"]');
		var $is_model_dd = $('[name="suwp-is-model"]');
		var $is_mep_dd = $('[name="suwp-is-mep"]');

		var $imeis_dd = $('[name="suwp-imei-values"]');
		var label_imei = $('label[for="imei-values"]').text();

		var $api1_dd = $('[name="suwp-api1-name"]');
		var label_api1 = $('label[for="api1-label"]').text();

		var $api2_dd = $('[name="suwp-api2-name"]');
		var label_api2 = $('label[for="api2-label"]').text();

		var $api3_dd = $('[name="suwp-api3-name"]');
		var label_api3 = $('label[for="api3-label"]').text();

		var $api4_dd = $('[name="suwp-api4-name"]');
		var label_api4 = $('label[for="api4-label"]').text();

		var $network_dd = $('[name="suwp-network-id"]');
		var label_network = $('label[for="network-id"]').text();

		var $country_dd = $('[name="suwp-country-id"]');
		var label_country = $('label[for="country-id"]').text();

		var $brand_dd = $('[name="suwp-brand-id"]');
		var label_brand = $('label[for="brand-id"]').text();

		var $model_dd = $('[name="suwp-model-id"]');
		var label_model = $('label[for="model-id"]').text();

		var $mep_dd = $('[name="suwp-mep-id"]');
		var label_mep = $('label[for="mep-id"]').text();

		var $email_response_dd = $('[name="suwp-email-response"]');
		var label_alt_email_response = $('[name="suwp-alt-email-response-label"]').val();
		var label_email_response = $('label[for="email-response"]').text();

		var $email_confirm_dd = $('[name="suwp-email-confirm"]');
		var label_email_confirm = $('label[for="email-confirm"]').text();
		
		$imeis_vals = $imeis_dd.val();
		$email_response_vals = $email_response_dd.val();
		
		if ( $is_api1_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api1_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_api1 + '</strong>');
			}
		}
		
		if ( $is_api2_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api2_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_api2 + '</strong>');
			}
		}
		
		if ( $is_api3_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api3_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_api3 + '</strong>');
			}
		}
		
		if ( $is_api4_dd.val() ) {
			// confirm that a custom api value was entered
			if ( $api4_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_api4 + '</strong>');
			}
		}
		
		if ( $is_network_dd.val() === 'Required') {
			// confirm that a network was selected
			if ( $network_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_network + '</strong>');
			}
		}
		
		if ( $is_model_dd.val() === 'Required') {
			// confirm that a brand was selected
			if ( $model_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_model + '</strong>');
			}
		}
		
		if ( $is_mep_dd.val() === 'Required') {
			// confirm that a mep was selected
			if ( $mep_dd.val() === '' ) {
				$flag_continue = false;
				$flag_msg_string.push('<strong>' + label_mep + '</strong>');
			}
		}

		$error_string = JSON.stringify($flag_msg_string);
		alert( "CART! ADMIN JS - OPTIONAL ERRORS = " + $error_string + ', forcing the object = ' + $flag_msg_string.values() );
		

		// swal("Bad jobz!", $error_optionals , "error");

		// swal({ title: "Error!", text: "Test to dispaly", type: "error", confirmButtonText: "Cool" });

		if ( $is_imei_dd.val() ) {
			alert( "CART! ADMIN JS - IMEI CHECK READY ... $is_imei_dd = " + $is_imei_dd.val() );

		} else {
			alert( "CART! ADMIN JS - SERIAL NUMBER CHECK READY ... $is_imei_dd = " + $is_imei_dd.val() );

		}

		/** 
		
		alert( "TESTING LABEL VALUE FOR IMEI = " + label_imei );
		alert( "TESTING LABEL VALUE FOR AP1 = " + label_api1 );
		alert( "TESTING LABEL VALUE FOR AP12= " + label_api2 );
		alert( "TESTING LABEL VALUE FOR AP3 = " + label_api3 );
		alert( "TESTING LABEL VALUE FOR AP4 = " + label_api4 );
		alert( "TESTING LABEL VALUE FOR label_network = " + label_network );
		alert( "TESTING LABEL VALUE FOR label_country = " + label_country );
		alert( "TESTING LABEL VALUE FOR label_brand = " + label_brand );
		alert( "TESTING LABEL VALUE FOR label_model = " + label_model );
		alert( "TESTING LABEL VALUE FOR label_mep = " + label_mep );
		alert( "TESTING LABEL VALUE FOR label_email_response = " + label_alt_email_response );
		alert( "TESTING LABEL VALUE FOR label_email_confirm = " + label_email_confirm );

		*/

		$txt = "Please select or enter at least one value in the following field(s):<br>";
		$arrayLength = $flag_msg_string.length;
		for (var i = 0; i < $arrayLength; i++) {
			$txt = $txt + $flag_msg_string[i] + "<br>";
			//Do something
		}
		// success, error, warning, info, question

		if ( $flag_continue ) {
			Swal.fire({
				title: 'Success!',
				text: 'All values passed the test',
				type: 'success',
				confirmButtonText: 'Ok'
			  })
		} else {
			Swal.fire({
				title: 'Error!',
				text: 'Add to cart',
				html: $txt,
				type: 'error',
				confirmButtonText: 'Ok'
			  })
			  return false;
		}

		if( $imeis_vals === '') {

			
			// swal("Good jobz!", "Login Success!", "error");
		
			// >>>>>> return false;
/**
			swal(
				'Youve added the max items!',
				'Change Your box',
				'error'
			);
*/
				// alert( "CART! ADMIN JS - SUBMISSION IS EMPTY" );
				// stop our form from submitting normally
				// return false;
		}
		
		// alert( "CART! ADMIN JS - SUBMISSION IS GOOD = " + $imeis_vals );


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
	
});
