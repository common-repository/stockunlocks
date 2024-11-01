<?php

		$su_go = true;
		$check_array = get_option( 'suwp_reference_posts' );
		if(is_array( $check_array ) ) {
			if( count( $check_array ) > 1 ) {
			    if ( is_object( $extract ) ) {
				if( !isset( $extract->error ) ) {
					$su_go = $extract->include_9;
				}
			    }
			}	
		}
		
		$name_providers = 'Providers - StockUnlocks Basic';
		if( get_option( 'suwp_plugin_type' ) ) {
			$name_providers = 'Providers';
		}
		
		$labels = array(
			'name' => __( $name_providers, $this->stockunlocks ),
			'singular_name' => __( 'Provider', $this->stockunlocks ),
			'menu_name' => __( 'Providers', $this->stockunlocks ),
			'name_admin_bar' => __( 'Providers', $this->stockunlocks ),
			'add_new' => __( 'Add New', $this->stockunlocks ),
			'add_new_item' => __( 'Add New Provider', $this->stockunlocks ),
			'edit_item' => __( 'Edit Provider', $this->stockunlocks ),
			'new_item' => __( 'New Provider', $this->stockunlocks ),
			'view_item' => __( 'View Provider', $this->stockunlocks ),
			'search_item' => __( 'Search Providers', $this->stockunlocks ),
			'not_found' => __( 'No providers found', $this->stockunlocks ),
			'not_found_in_trash' => __( 'No providers found in trash', $this->stockunlocks ),
			'all_items' => __( 'All Providers', $this->stockunlocks ),
			);
	
		$args = array(
			"label" => "Providers",
			'labels' => $labels,
			"description" => "",
			'public' => false,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			'has_archive' => false,
			"show_in_menu" => false,
			"exclude_from_search" => true,
			"capability_type" => "post",
			'supports' => false,
			"capabilities" => array( 'create_posts' => $su_go ),
			"map_meta_cap" => true,
			"hierarchical" => false,
			'rewrite' => array( "slug" => "suwp_apisource", "with_front" => false ),
			);
	
		register_post_type( 'suwp_apisource', $args );
		
		if(function_exists("register_field_group")) {
			
			register_field_group(array (
				'id' => 'acf_provider-details',
				'title' => 'Provider Details',
				'fields' => array (
					array (
						'key' => 'field_5854d17bd5e5d',
						'label' => 'Active',
						'name' => 'suwp_activeflag',
						'type' => 'select',
						'instructions' => 'Set this to Yes if you would like to process and extract orders for this API Provider.',
						'required' => 1,
						'choices' => array (
							0 => 'No',
							1 => 'Yes',
						),
						'default_value' => 0,
						'allow_null' => 1,
						'multiple' => 0,
					),
					array (
						'key' => 'field_58541mz4p0nlf',
						'label' => 'API Type',
						'name' => 'suwp_apitype',
						'type' => 'select',
						'instructions' => 'Set this to the desired API.',
						'required' => 1,
						'choices' => array (
							'00' => 'Dhru Fusion',
							'01' => 'GSM Fusion',
							'04' => 'iPhoneAdmin',
							'03' => 'NakshSoft',
							'02' => 'UnlockBase',
						),
						'default_value' => '',
						'allow_null' => 1,
						'multiple' => 0,
					),
					array (
						'key' => 'field_58557dfcd5e5e',
						'label' => 'Site Name',
						'name' => 'suwp_sitename',
						'type' => 'text',
						'instructions' => 'A short name description for this API Provider.',
						'required' => 1,
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_58557fa7d5e5f',
						'label' => 'API URL',
						'name' => 'suwp_url',
						'type' => 'text',
						'instructions' => "Format: <strong>https://www.url.com</strong> or <strong>https://url.com</strong>",
						'required' => 1,
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_58558042d5e60',
						'label' => 'API Username',
						'name' => 'suwp_username',
						'type' => 'text',
						'instructions' => 'The registered username used with the API provider\'s website above.',
						'required' => 1,
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_585580a8d5e61',
						'label' => 'API Access Key',
						'name' => 'suwp_apikey',
						'type' => 'text',
						'instructions' => 'The API Access Key assigned to you by this API provider.',
						'required' => 1,
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_58558102d5e62',
						'label' => 'API Notes',
						'name' => 'suwp_apinotes',
						'type' => 'textarea',
						'instructions' => 'Any notes about this API Provider.',
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'formatting' => 'br',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'suwp_apisource',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'acf_after_title',
					'layout' => 'default',
					'hide_on_screen' => array (
						0 => 'permalink',
						1 => 'the_content',
						2 => 'excerpt',
						3 => 'custom_fields',
						4 => 'discussion',
						5 => 'comments',
						6 => 'revisions',
						7 => 'slug',
						8 => 'author',
						9 => 'format',
						10 => 'featured_image',
						11 => 'categories',
						12 => 'tags',
						13 => 'send-trackbacks',
					),
				),
				'menu_order' => 0,
			));
		}
