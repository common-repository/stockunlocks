
		<!-- id below must match target registered in above suwp_custom_product_data_tab function -->
		<div id="suwp_custom_product_data" class="panel woocommerce_options_panel">
			<?php
			
			$extract = get_option( 'suwp_author_info' );
			$include_msg = '';
			$include_attr = '';
			if ( is_object( $extract ) ) {
				if( !isset($extract->error) ) {
					$include_msg = $extract->include_7;
					$include_attr = $extract->include_6;
				}
			}
			
			// get all api providers
			$lists = get_posts(
				array(
					'post_type'			=>'suwp_apisource',
					'status'			=>'publish',
					'posts_per_page'   	=> -1,
					'orderby'         	=> 'post_title',
					'order'            	=> 'ASC',
				)
			);
			
			$options_tmp = array();
			$options_tmp[] = array(
						 'None'  => __( '- None -', 'stockunlocks' ),
						 '000'  => __( 'Stand-alone Unlock', 'stockunlocks' ),
					);
			
			$options = array();
			// loop over providers
			foreach( $lists as &$list ):
				
				// create the select option for that list
				$title = get_field('suwp_sitename', $list->ID ); // $list->post_title
				
				// Check if the custom field is available.
				if ( ! empty( $title ) ) {
					
					// $title = $title . ': ' . $list->post_title;
					$options_tmp[] = array(
						 $list->ID  => __( $title, 'stockunlocks' ),
					);
				
				}
				
			endforeach;
			
			$plugin_public = new Stock_Unlocks_Public( 'stockunlocks', STOCKUNLOCKS_VERSION );
			
			$options = $plugin_public->suwp_exec_array_flatten($options_tmp, 2);
			$suwp_link = ' <a href="https://www.stockunlocks.com/product/stockunlocks-plugin-pro/" target="_blank"> Go Pro </a>';
	
			$no_yes = array();
			$no_yes = array(
							'None' => __( 'No', 'stockunlocks' ),
							'Required' => __( 'Required', 'stockunlocks' ),
							);
			
			$disabled_enabled = array();
			$disabled_enabled = array(
							'disabled' => __( 'Disabled', 'stockunlocks' ),
							'custom' => __( 'Custom', 'stockunlocks' ),
							'global' => __( 'Global', 'stockunlocks' ),
							);
			
			$custom_attributes = array($include_attr => $include_attr);

			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_api_provider', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'API provider', 'stockunlocks' ), 
				'options'         => $options,
				'desc_tip'      => 'true',
				'description'   => __( 'Select the API Provider for this service.', 'stockunlocks' ) 
			) );
			
			// Text Field
			// 'required' was affecting non-Remote Service Products when saving
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_api_service_id', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API id', 'stockunlocks' ), 
				// 'placeholder' => '',
				'desc_tip'    => 'true',
				// 'custom_attributes' => array( 'required' => 'required' ),
				'description' => __( 'The API id for this service. WARNING: Only change this if you know what you are doing.', 'stockunlocks' )
			) );

			// Select Field: For display only
			$api_service_id_alt_arr = array();
			$api_service_id_alt = get_post_meta( $post->ID, '_suwp_api_service_id_alt', true );
			// Check if the custom field is available.
			if ( ! empty( $api_service_id_alt ) ) {
				$api_service_id_alt_arr = array(
							$api_service_id_alt  => $api_service_id_alt,
						);
			}
			woocommerce_wp_select( array( 
				'id'            => '_suwp_api_service_id_alt', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'API id (text value)', 'stockunlocks' ), 
				'options'       => $api_service_id_alt_arr,
				'desc_tip'      => 'true',
				'description'   => __( 'For display only. Value obtained from Supplier.', 'stockunlocks' ) 
			) );
			
			// Text Field
			woocommerce_wp_text_input( array( 
				'id'            => '_suwp_process_time', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Estimated Delivery Time', 'stockunlocks' ), 
				'placeholder'   => '',
				'desc_tip'      => 'false',
				'description' => __( 'Enter the estimated reply time.', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_mep', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is mep', 'stockunlocks' ), 
				'options'       => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Does this service require an mep selection?', 'stockunlocks' ) 
			) );
			
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_network', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is network', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Does this service require a network selection?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_model', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is model', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Does this service require a model selection?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_pin', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is pin', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Is ths a pin type service?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_rm_type', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is RM type', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Is ths a RM type service?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_kbh', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is kbh', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Is ths a kbh type service?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_reference', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is Reference', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Is ths a reference tag type service?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_service_tag', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is Service Tag', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Is ths a service tag type service?', 'stockunlocks' ) 
			) );
			
			// Select Field
			woocommerce_wp_select( array( 
				'id'            => '_suwp_is_activation', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Is Activation', 'stockunlocks' ),  
				// 'placeholder'   => '',
				'options'         => $no_yes,
				'desc_tip'      => 'true',
				'description'   => __( 'Is ths an activation type service?', 'stockunlocks' ) 
			) );
			
			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_price_group_id', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'Price Group id', 'stockunlocks' ), 
				'placeholder' => '',
				'desc_tip'    => 'true',
				'description' => __( 'Enter the Price Group ID for this service.', 'stockunlocks' ) 
			) );
			
			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_price_group_name', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'Price Group Name', 'stockunlocks' ), 
				'placeholder' => '',
				'desc_tip'    => 'true',
				'description' => __( 'Enter the Price Group Name for this service.', 'stockunlocks' ) 
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';

			// Checkbox
			$hideimei_status = get_post_meta( $post->ID, '_suwp_hideimei_status', true );
			$hideimei_status_val = 'no';
			// Check if the custom field is available.
			if ( ! empty( $hideimei_status ) ) {
				$hideimei_status_val = $hideimei_status;
			}
			
			woocommerce_wp_checkbox( array( 
				'id'            => '_suwp_hideimei_status',
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Hide Serial field', 'stockunlocks' ),
				'value'         => $hideimei_status_val,
				'desc_tip'      => 'true',
				'description'   => __( 'When "Hide Serial field", the Serial Number field will not be displayed on the website.', 'stockunlocks' ),
			) );
				
			// Number Field
			woocommerce_wp_text_input( array( 
					'id'            => '_suwp_serial_limit', 
					'wrapper_class' => 'show_if_simple', 
					'label'         => __( 'Serial Max Quantity', 'stockunlocks' ), 
					'placeholder'   => '', 
					'type'          => 'number', 
					'custom_attributes' => array(
							'step'  => '1',
							'min'   => '1'
						),
					'desc_tip'      => 'true',
					'description'   => __( 'Enter the maximum number of IMEI or Serial Number(s) allowed to be submitted: blank = no limit.', 'stockunlocks' ), 
			) );

			// Number Field
			woocommerce_wp_text_input( array( 
					'id'            => '_suwp_serial_length', 
					'wrapper_class' => 'show_if_simple', 
					'label'         => __( 'Serial Max Length', 'stockunlocks' ), 
					'placeholder'   => '', 
					'type'          => 'number', 
					'custom_attributes' => array(
							'step'  => '1',
							'min'   => '1'
						),
					'desc_tip'      => 'true',
					'description'   => __( 'Enter the maximum character length of a single IMEI or Serial Number: blank = any length.', 'stockunlocks' ), 
			) );
			
			// Checkbox
			$allowtext_status = get_post_meta( $post->ID, '_suwp_allowtext_status', true );
			$allowtext_status_val = 'no';
			// Check if the custom field is available.
			if ( ! empty( $allowtext_status ) ) {
				$allowtext_status_val = $allowtext_status;
			}
			
			woocommerce_wp_checkbox( array( 
				'id'            => '_suwp_allowtext_status', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Allow text', 'stockunlocks' ),
				'value'         => $allowtext_status_val,
				'desc_tip'      => 'true',
				'description'   => __( 'When "Allow text", serial numbers may include text values. Otherwise, only numeric values are allowed.', 'stockunlocks' ),
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api1_name',
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-1 Field Name', 'stockunlocks' ),
				'placeholder' => 'No spaces! user_name, password_1, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-1 back-end field name (Remote API server requires this).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api1_label', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-1 Field Label', 'stockunlocks' ),
				'placeholder' => 'Descriptive: User Name, Password, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-1 Field Label (Helpful info for users).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api1_values', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-1 Field Values', 'stockunlocks' ),
				'placeholder' => 'value1::value2::value3::etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-1 Field Values (Predetermined values). Double colon seperated.', 'stockunlocks' ) 
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api2_name', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-2 Field Name', 'stockunlocks' ),
				'placeholder' => 'No spaces! user_name, password_2, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-2 back-end field name (Remote API server requires this).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api2_label', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-2 Field Label', 'stockunlocks' ),
				'placeholder' => 'Descriptive: User Name, Password, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-2 Field Label (Helpful info for users).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api2_values', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-2 Field Values', 'stockunlocks' ),
				'placeholder' => 'value1::value2::value3::etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-2 Field Values (Predetermined values). Double colon seperated.', 'stockunlocks' ) 
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api3_name', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-3 Field Name', 'stockunlocks' ),
				'placeholder' => 'No spaces! user_name, password_3, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-3 back-end field name (Remote API server requires this).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api3_label', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-3 Field Label', 'stockunlocks' ),
				'placeholder' => 'Descriptive: User Name, Password, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-3 Field Label (Helpful info for users).', 'stockunlocks' ) 
			) );
			
			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api3_values', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-3 Field Values', 'stockunlocks' ),
				'placeholder' => 'value1::value2::value3::etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-3 Field Values (Predetermined values). Double colon seperated.', 'stockunlocks' ) 
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api4_name', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-4 Field Name', 'stockunlocks' ),
				'placeholder' => 'No spaces! user_name, password_4, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-4 back-end field name (Remote API server requires this).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api4_label', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-4 Field Label', 'stockunlocks' ),
				'placeholder' => 'Descriptive: User Name, Password, etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-4 Field Label (Helpful info for users).', 'stockunlocks' ) 
			) );

			// Text Field
			woocommerce_wp_text_input( array( 
				'id'          => '_suwp_custom_api4_values', 
				'wrapper_class' => 'show_if_simple', 
				'label'       => __( 'API-4 Field Values', 'stockunlocks' ),
				'placeholder' => 'value1::value2::value3::etc.',
				'desc_tip'    => 'true',
				'custom_attributes' => $custom_attributes,
				'description' => __( 'Enter the Custom API-4 Field Values (Predetermined values). Double colon seperated.', 'stockunlocks' ) 
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';

			// Textarea
			woocommerce_wp_textarea_input( 
				array( 
					'id'          => '_suwp_not_found', 
					'label'       => __( 'Not Found Options', 'stockunlocks' ), 
					'placeholder' => '', 
					'value'       => get_post_meta( $post->ID, '_suwp_not_found', true ),
					'desc_tip'      => 'true',
					'description' => __( 'List of options when code not found, comma separated.', 'stockunlocks' ),
			) );
			
			// Textarea
			woocommerce_wp_textarea_input( 
				array( 
					'id'          => '_suwp_assigned_brand', 
					'label'       => __( 'Assigned Brand', 'stockunlocks' ), 
					'placeholder' => 'brand1::brand2::brand3::etc.', 
					'value'       => get_post_meta( $post->ID, '_suwp_assigned_brand', true ),
					'desc_tip'      => 'true',
					'description' => __( 'List of assigned brands, double colon separated.', 'stockunlocks' ),
			) );
			
			// Textarea
			woocommerce_wp_textarea_input( 
				array( 
					'id'          => '_suwp_assigned_model', 
					'label'       => __( 'Assigned Model', 'stockunlocks' ), 
					'placeholder' => 'brand1_model1,,brand1_model2::brand2_model1,,brand2_model1,,etc.', 
					'value'       => get_post_meta( $post->ID, '_suwp_assigned_model', true ),
					'desc_tip'      => 'true',
					'description' => __( 'List of assigned models, double comma separated. Double colon starts a new brand.', 'stockunlocks' ),
			) );
			
			// Textarea
			woocommerce_wp_textarea_input( 
				array( 
					'id'          => '_suwp_assigned_country', 
					'label'       => __( 'Assigned Country', 'stockunlocks' ),
					'placeholder' => 'country1::country2::country3::etc.', 
					'value'       => get_post_meta( $post->ID, '_suwp_assigned_country', true ),
					'desc_tip'      => 'true',
					'description' => __( 'List of assigned countries, double colon separated.', 'stockunlocks' ),
			) );
			
			// Textarea
			woocommerce_wp_textarea_input( 
				array( 
					'id'          => '_suwp_assigned_network', 
					'label'       => __( 'Assigned Network', 'stockunlocks' ), 
					'placeholder' => 'country1_network1,,country1_network2::country2_network1,,country2_network1,,etc.', 
					'value'       => get_post_meta( $post->ID, '_suwp_assigned_network', true ),
					'desc_tip'      => 'true',
					'description' => __( 'List of assigned networks, double comma separated. Double colon starts a new country.', 'stockunlocks' ),
			) );
			
			echo '<div class="options_group show_if_simple" style="display: block;">';
			echo '</div>';
			
			// Checkbox
			$online_status = get_post_meta( $post->ID, '_suwp_online_status', true );
			$online_status_val = 'yes';
			// Check if the custom field is available.
			if ( ! empty( $online_status ) ) {
				$online_status_val = $online_status;
			}
			   
			woocommerce_wp_checkbox( array( 
				'id'            => '_suwp_online_status', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Online', 'stockunlocks' ),
				'value'         => $online_status_val,
				'desc_tip'      => 'true',
				'description'   => __( 'When "Online", orders may be submitted. Otherwise, this service will be displayed as "Offline”.', 'stockunlocks' ),
			) );
			
			// Textarea
			woocommerce_wp_textarea_input( 
				array( 
					'id'          => '_suwp_service_notes', 
					'label'       => __( 'Service notes', 'stockunlocks' ), 
					'placeholder' => '', 
					'value'       => get_post_meta( $post->ID, '_suwp_service_notes', true ),
					'desc_tip'      => 'true',
					'description' => __( 'Holding area for previously used service details or future ideas.', 'stockunlocks' ),
			) );
			
			// Select Field: Disabled, Custom, Global
			woocommerce_wp_select( array( 
				'id'            => '_suwp_price_adj', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Auto Adjust Price', 'stockunlocks' ), 
				'options'       => $disabled_enabled,
				'desc_tip'      => 'true',
				'description'   => __( 'Automatically adjust Regular price when supplier credit changes; Custom > based on settings below; Global > based on Plugin Options.', 'stockunlocks' ) 
			) );
			
			// Number Field
			$price_adj_custom = get_post_meta( $post->ID, '_suwp_price_adj_custom', true );
			$price_adj_custom_val = '1';
			// Check if the custom field is available.
			if ( ! empty( $price_adj_custom ) ) {
				$price_adj_custom_val = $price_adj_custom;
			}
					
			woocommerce_wp_text_input( array( 
					'id'            => '_suwp_price_adj_custom', 
					'wrapper_class' => 'show_if_simple', 
					'label'         => __( 'Custom price multiplier', 'stockunlocks' ), 
					'placeholder'   => '',
					'value'       => $price_adj_custom_val,
					'type'          => 'number', 
					'custom_attributes' => array(
							'step'  => '0.01',
							'min'   => '1'
						),
					'desc_tip'      => 'true',
					'description'   => __( 'Automatically adjust Regular price by a custom multiplier value when supplier credit changes. Only works when Custom is selected above.', 'stockunlocks' ), 
			) );
			
			// Select Field: For display only
			$service_credit_arr = array();
			$service_credit = get_post_meta( $post->ID, '_suwp_service_credit', true );
			// Check if the custom field is available.
			if ( ! empty( $service_credit ) ) {
				$service_credit_arr = array(
							$service_credit  => $service_credit,
						);
			}
			woocommerce_wp_select( array( 
				'id'            => '_suwp_service_credit', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __( 'Service credit', 'stockunlocks' ), 
				'options'       => $service_credit_arr,
				'desc_tip'      => 'true',
				'description'   => __( 'The required credit for this service. Value obtained from Supplier. For display only.', 'stockunlocks' ) 
			) );
			
			// Hidden field
			/**
			woocommerce_wp_hidden_input(
				array( 
					'id'    => '_hidden_field', 
					'value' => 'hidden_value'
					)
			);
			**/
			
			?>
		</div>
		<?php
