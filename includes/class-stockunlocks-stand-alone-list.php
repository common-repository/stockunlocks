<?php

/**
 * Class for displaying Stand Alone WooCommerce Orders (Unlocking)
 * in a WordPress-like Admin Table with row actions to 
 * perform order meta operations
 */
class Stand_Alone_List extends SUWP_List_Table {
  
  /**
	 * The text domain of this plugin.
	 *
	 * @since    1.9.2.3
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
  protected $plugin_text_domain;
  
   /*
	 * Call the parent constructor to override the defaults $args
	 * 
	 * @param string $plugin_text_domain	Text domain of the plugin.	
	 * 
	 * @since 1.9.2.3
	 */
	public function __construct( $plugin_text_domain ) {
  
		$this->plugin_text_domain = $plugin_text_domain;
		
		parent::__construct( array( 
				'plural'	=>	'orders',	// Plural value used for labels and the objects being listed.
				'singular'	=>	'order',		// Singular label for an object being listed, e.g. 'post'.
				'ajax'		=>	false,		// If true, the parent class will call the _js_vars() method in the footer		
      ) );
  }	
  
	public function extra_tablenav( $which ) {
    global $wpdb, $testiURL, $tablename, $tablet;
    $move_on_url_status = '&status-filter=';
    $move_on_url_service = '&service-filter=';
    $move_on_url_provider = '&provider-filter=';
    if ( $which == "top" ){

        ?>
        <div class="alignleft actions bulkactions">
        <div class="has-suwp-api">
        <?php

        $post_type = 'shop_order';
        $var = $this->months_dropdown( $post_type );
       
        $orders = $this->fetch_base_table_data();

        // string comparison ascending
        usort($orders, function ($a, $b) {
          return strcmp($a['post_status'], $b['post_status']);
        });
        
        $status_filters = array_unique( array_column($orders, 'post_status', 'ID') );
        $status_names = array_unique( array_column($orders, 'status_name', 'ID') );
        // error_log('>>>>>>  array_column STATUS: ' . print_r($status_filters,true));
        // error_log('>>>>>>  array_column STATUS NAMES: ' . print_r($status_names,true));

        // https://stackoverflow.com/questions/13652605/extracting-a-parameter-from-a-url-in-wordpress
        // Why not just use the WordPress get_query_var() function?

        if( $status_filters ){
            ?>
            <select name="status-filter" class="suwp-filter-status">
                <option value="">Filter by Status</option>
                <?php
                foreach( $status_filters as $key => $value ) {
                    $selected = '';
                    $status_selected = ( isset( $_GET['status-filter'] ) ) ? esc_sql( $_GET['status-filter'] ) : '';
                    if( $status_selected == $value ) {
                        $selected = ' selected = "selected"';   
                    }
                ?>
                <option value="<?php echo $move_on_url_status . $value; ?>" <?php echo $selected; ?>><?php echo $status_names[$key]; ?></option>
                <?php 
                }
                ?>
            </select>
            <?php   
        }

        // string comparison ascending
        usort($orders, function ($a, $b) {
          return strcmp($a['product_name'], $b['product_name']);
        });

        $service_filters = array_unique( array_column($orders, 'product_name', 'ID') );
       // error_log('>>>>>>  array_column SERVICE: ' . print_r($service_filters,true));

        if( $service_filters ){
          ?>
          <select name="service-filter" class="suwp-filter-service">
              <option value="">Filter by Service</option>
              <?php
              foreach( $service_filters as $service ) {
                  $selected = '';
                  $service_selected = ( isset( $_GET['service-filter'] ) ) ? esc_sql( $_GET['service-filter'] ) : '';
                  if( $service_selected == $service ) {
                      $selected = ' selected = "selected"';   
                  }
              ?>
              <option value="<?php echo $move_on_url_service . $service; ?>" <?php echo $selected; ?>><?php echo $service; ?></option>
              <?php
              }
              ?>
          </select>
          <?php   
        }
        
        // string comparison ascending
        usort($orders, function ($a, $b) {
          return strcmp($a['api_provider_name'], $b['api_provider_name']);
        });
        
        $provider_filters = array_unique( array_column($orders, 'api_provider', 'ID') );
        $provider_names = array_unique( array_column($orders, 'api_provider_name', 'ID') );
        
        // error_log('>>>>>>  array_column STATUS: ' . print_r($provider_filters,true));
        // error_log('>>>>>>  array_column STATUS NAMES: ' . print_r($provider_names,true));

        if( $provider_filters ){
            ?>
            <select name="provider-filter" class="suwp-filter-provider">
                <option value="">Filter by API Provider</option>
                <?php
                foreach( $provider_filters as $key => $value ) {
                    $selected = '';
                    $provider_selected = ( isset( $_GET['provider-filter'] ) ) ? esc_sql( $_GET['provider-filter'] ) : '';
                    if( $provider_selected == $value ) {
                        $selected = ' selected = "selected"';   
                    }
                ?>
                <option value="<?php echo $move_on_url_provider . $value; ?>" <?php echo $selected; ?>><?php echo $provider_names[$key]; ?></option>
                <?php 
                }
                ?>
            </select>
            <?php   
        }

/* PREVIOUS METHOD
          if( $filters ){
            ?>
            <select name="status-filter" class="suwp-filter-cat">
                <option value="">Filter by Status</option>
                <?php
                foreach( $filters as $stat ){
                    $selected = '';
                    $stat_filter = ( isset( $_GET['status-filter'] ) ) ? esc_sql( $_GET['status-filter'] ) : '';
                    if( $stat_filter == $stat['ID'] ){
                        $selected = ' selected = "selected"';   
                    }
                    $has_testis = false;
                    $chk_testis = $wpdb->get_row( "SELECT ID FROM " . $wpdb->prefix . "posts where ID=".$stat['ID'], ARRAY_A );
                    if( $chk_testis['ID'] > 0 ){
                ?>
                <option value="<?php echo $move_on_url_status . $stat['ID']; ?>" <?php echo $selected; ?>><?php echo $stat['ID']; ?></option>
                <?php   
                    }
                }
                ?>
            </select>
            <?php   
        }
*/
        ?>  
        </div>
        </div>
        <?php
    }
    if ( $which == "bottom" ){
        //The code that goes after the table is there
    }
  }
  

  protected function months_dropdown( $post_type ) {
    global $wpdb, $wp_locale;
  
    /**
     * Filters whether to remove the 'Months' drop-down from the post list table.
     *
     * @since 1.9.2.3
     *
     * @param bool   $disable   Whether to disable the drop-down. Default false.
     * @param string $post_type The post type.
     */
    if ( apply_filters( 'disable_months_dropdown', false, $post_type ) ) {
      return;
    }
  
    $extra_checks = "AND post_status != 'auto-draft'";
    if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
      $extra_checks .= " AND post_status != 'trash'";
    } elseif ( isset( $_GET['post_status'] ) ) {
      $extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
    }
  
    /*
    $months = $wpdb->get_results(
      $wpdb->prepare(
        "
      SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
      FROM $wpdb->posts
      WHERE post_type = %s
      $extra_checks
      ORDER BY post_date DESC
    ",
        $post_type
      )
    );
    */
    
    $date_table  = $this->fetch_base_table_data();

    // numbers, sort descending by timestamp
    usort($date_table, function($a, $b) {
      return $b['timestamp'] - $a['timestamp'];
    });

    /*
    // string comparison ascending
    usort($date_table, function ($a, $b) {
      return strcmp($a['post_status'], $b['post_status']);
    });
    */

    $months = array();
    $year_month = array();
    $i = 0;
    foreach($date_table as $row) {
      $year = $row['year'];
      $month = $row['month'];
      $year_month_val = $year . ':' .$month;
      if ( !in_array($year_month_val, $year_month, TRUE) ){
        $year_month[] = $year_month_val;
        $tmp_array = array('year'=>$year, 'month'=>$month);
        $months[$i] = (object) $tmp_array;
        $i++;
      }
    }

    /**
     * Filters the 'Months' drop-down results.
     *
     * @since 3.7.0
     *
     * @param object $months    The months drop-down query results.
     * @param string $post_type The post type.
     */
    $months = apply_filters( 'months_dropdown_results', $months, $post_type );
  
    $month_count = count( $months );
  
    if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
      return;
    }
  
    $m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;

    ?>
    <label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
    <select name="m" id="filter-by-date">
      <option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
    <?php

    foreach ( $months as $arc_row ) {
      if ( 0 == $arc_row->year ) {
        continue;
      }
  
      $month = zeroise( $arc_row->month, 2 );
      $year  = $arc_row->year;
  
      printf(
        "<option %s value='%s'>%s</option>\n",
        selected( $m, $year . $month, false ),
        esc_attr( $arc_row->year . $month ),
        /* translators: 1: month name, 2: 4-digit year */
        sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
      );
    }
    ?>
    </select>
    <?php
  }

  protected function get_views() {
    
    $views = array();
    $current = ( !empty($_REQUEST['suwp-order-type']) ? $_REQUEST['suwp-order-type'] : 'all');

    $base_table = $this->fetch_base_table_data();
    $all_count = count($base_table);

    $orderIdsAlone = array();
    $orderIdsAll = array();
    $stand_alone_count = '';
    $providers_count = '';
    $provider_filters = array_column($base_table, 'api_provider', 'ID');

    foreach( $provider_filters as $key=>$val ) {
      // error_log('KEY : ' . $key . ', VALUE : ' . $val);
      if( $val == '000') {
        $orderIdsAlone[] = $key;
      } else {
        $orderIdsAll[] = $key;
      }
    }

    if ( count($orderIdsAll) > 0 ) {
      
      //All link
      $class = ($current == 'all' ? ' class="current"' :'');
      $all_url = 'admin.php?page=suwp_orders_admin_page'; //remove_query_arg('customvar');
      $views['all'] = "<a href='{$all_url }' {$class} >All <span class='count'>(" . $all_count . ")</span></a>";
      
      // Assigned Providers link
      $providers_count = " <span class='count'>(" . count($orderIdsAll) . ")</span>";
      $all_providers_url = 'admin.php?page=suwp_orders_admin_page&suwp-order-type=all-providers'; // add_query_arg('suwp-order-type','all-providers');
      $class = ($current == 'all-providers' ? ' class="current"' :'');
      $views['all-providers'] = "<a href='{$all_providers_url}' {$class} >Assigned Providers" . $providers_count . "</a>";
    }
    
    if ( count($orderIdsAlone) > 0 ) {
      
      //Stand-alone link
      $stand_alone_count = " <span class='count'>(" . count($orderIdsAlone) . ")</span>";
      $stand_alone_url =  'admin.php?page=suwp_orders_admin_page&suwp-order-type=000'; // add_query_arg('suwp-order-type','000');
      $class = ($current == '000' ? ' class="current"' :'');
      $views['000'] = "<a href='{$stand_alone_url}' {$class} >Stand-alone" . $stand_alone_count . "</a>";
    }
    return $views;

  }
  
	/**
	 * Prepares the list of items for displaying.
	 * 
	 * Query, filter data, handle sorting, and pagination, and any other data-manipulation required prior to rendering
	 * 
	 * @since   1.9.2.3
	 */
  public function prepare_items() {

    //Retrieve $suwp-order-type for use in query to get items.
    $suwp_order_type = ( isset($_REQUEST['suwp-order-type']) ? $_REQUEST['suwp-order-type'] : 'all');

    // check if a search was performed.
    $order_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
    
    // error_log('>>>>>> !!!!!!! order_search_key = ' . $order_search_key);

    //used by WordPress to build and fetch the _column_headers property
    $this->_column_headers = $this->get_column_info();
    
    // check and process any actions such as bulk actions.
	  $this->handle_table_actions();

	  // fetch the table data
    $table_data = $this->fetch_table_data();
    // filter the data in case of a search
    if( $order_search_key ) {
      $table_data = $this->filter_table_data( $table_data, $order_search_key );
    }
    
    // code to handle pagination
    $orders_per_page = $this->get_items_per_page( 'suwp_orders_per_page' );
    $table_page = $this->get_pagenum();	
    
    // provide the ordered data to the List Table
    // we need to manually slice the data based on the current pagination
    $this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $orders_per_page ), $orders_per_page );
    
    // set the pagination arguments		
    $total_orders = count( $table_data );
    $this->set_pagination_args( array (
      'total_items' => $total_orders,
      'per_page'    => $orders_per_page,
      'total_pages' => ceil( $total_orders/$orders_per_page )
    ) );
  }

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 1.9.2.3
	 * 
	 * @return array
	 */	
  public function get_columns() {

    $table_columns = array(
      'cb'		=> '<input type="checkbox" />', // to display the checkbox.
      'order_name'	=> __( 'Order', $this->plugin_text_domain ),
      'paid_date'	=> __( 'Date', $this->plugin_text_domain ),
      'status_name' => __( 'Status', $this->plugin_text_domain ),
      'product_name'	=> __( 'Service', $this->plugin_text_domain ),
      'api_provider_name'	=> __( 'Provider', $this->plugin_text_domain ),
      'imei'	=> __( 'IMEI/SN', $this->plugin_text_domain ),
      'order_total'	=> __( 'Total', $this->plugin_text_domain ),
    );

    /*
    $table_columns = array(
      'cb'		=> '<input type="checkbox" />', // to display the checkbox.			 
      'order_item_name'	=> __( 'Order', $this->plugin_text_domain ),
      'order_id'	=> __( 'Date (temp)', $this->plugin_text_domain ),			
      'order_item_type' => _x( 'Status (temp)', 'column name', $this->plugin_text_domain ),
      'order_item_id'		=> __( 'Total (temp)', $this->plugin_text_domain ),
    );
    */

    return $table_columns;
  }

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 1.9.2.3
	 * 
	 * @return array
	 */
  protected function get_sortable_columns() {

    /*
		 * actual sorting still needs to be done by prepare_items.
		 * specify which columns should have the sort icon.
		 * 
		 * key => value
		 * column name_in_list_table => columnname in the db
		 */
    $sortable_columns = array (
        'ID' => array( 'ID', true ),
        'order_name'=>'order_name', 
        'post_title'=>'post_title',
        'paid_date'=>'paid_date',
        'status_name'=>'post_status',
        'product_name'=>'product_name',
        'api_provider_name'=>'api_provider_name',
        'order_total'=>'order_total'
      );

    return $sortable_columns;
  }
	
	/** 
	 * Text displayed when no order data is available 
	 * 
	 * @since   1.9.2.3
	 * 
	 * @return void
	 */
  public function no_items() {
    _e( 'No orders avaliable.', $this->plugin_text_domain );
  }

	/*
	 * Fetch base table data from the WordPress database.
	 * 
	 * @since 1.9.2.3
	 * 
	 * @return	Array
	 */
  public function fetch_base_table_data() {

    global $wpdb;
    $month_query = '';
    $status_query = '';
    $service_query = '';

    // collect suwp specific order_item_ids
    $orderItemIds = array();
    $wpdb_table_itemmeta = $wpdb->prefix . 'woocommerce_order_itemmeta';
    $meta_filter = '\'_suwp_qty_sent\'';
    $meta_query = ' where meta_key=' . $meta_filter . ' ';
    $orderby = 'order_item_id' ; // "CAST('order_item_id' as unsigned)"; 'order_item_id';
    $order = 'ASC'; //  'DESC'; 'ASC';
    $orderitem_query = "SELECT 
                      order_item_id
                    FROM 
                      $wpdb_table_itemmeta
                      $meta_query
                    ORDER BY $orderby $order ";

    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results( $orderitem_query, ARRAY_A );
		foreach ($query_results as $row) {
			foreach( $row as $key => $value ) {
				$orderItemIds[] = $value;
			}
    }

    // collect suwp specific order_ids to be INCLUDED later using post__in
    $orderIds = array();
    $wpdb_table_orderitems = $wpdb->prefix . 'woocommerce_order_items';
		foreach( $orderItemIds as $value ) {
      $order_item_id_query = ' where order_item_id=' . $value . ' ';
      $orderby = 'order_item_id' ; // "CAST('order_item_id' as unsigned)"; 'order_item_id';
      $order = 'ASC'; //  'DESC'; 'ASC';
      $order_item_query = "SELECT 
                      *
                    FROM 
                      $wpdb_table_orderitems
                      $order_item_id_query
                    ORDER BY $orderby $order ";

      foreach( $wpdb->get_results($order_item_query) as $key => $row) {
				$orderIds[] = $row->order_id;
      }
    }

    // error_log( 'fetch_base_table_data collect suwp specific order_ids to be INCLUDED later,  = ' . count($orderIds) . chr(10) . ' : '. print_r($orderIds,true) );
    
    $order_args = array(
      'post__in' => $orderIds,
      'post_type' => array( 'shop_order' ),
      'orderby'   => 'ID',
      'order' => 'ASC',
      'posts_per_page'=> -1,
    );

    $order_posts = new WP_Query( $order_args );

    // error_log( 'WP_Query collect suwp specific order_posts to be INCLUDED later,  = ' . chr(10) . ' : '. print_r($order_posts,true) );
    
    $post_array = array();
    if($order_posts->have_posts()) :
      $i = 0;
      while($order_posts->have_posts()) : $order_posts->the_post();

        $post_array[] = (array) $order_posts->posts[$i];

        $ID = get_the_ID();
        $status_name =  wc_get_order_status_name( get_post_status() );
        $order = new WC_Order( $ID );
        $items = $order->get_items();
        $total_items = count($items);

        $first_name = get_post_meta( $ID, '_billing_first_name', true );
        $last_name = get_post_meta( $ID, '_billing_last_name', true );
        $order_name = '#' . $ID . ' ' . $first_name . ' ' . $last_name;
        $customer_user = get_post_meta( $ID, '_billing_address_index', true );
        $order_total = get_post_meta( $ID, '_order_total', true );

        $timestamp = strtotime( get_post_meta($ID, '_paid_date', true) );
        $date = date_create( get_post_meta( $ID, '_paid_date', true ) );
        $paid_date = date_format( $date,"M d, Y" );
        $time = strtotime(get_the_date());
        $year = date("Y",$time);
        $date = date_parse(date("F",$time));
        // $month = date_parse_from_format( 'M', strval($date['month']) );
        // error_log(' DATE STUFF HERE month : ' . print_r($month ,true));

        $month = zeroise(strval($date['month']), 2);
        
        $product_name = '';
        $imei = '';
        $api_provider = '';
        $api_provider_name = '';

        foreach ( $items as $item ) {
            $order_item_id = $item->get_id();
            $product_id = $item['product_id'];

            $product = wc_get_product( $product_id );

            if ( $product ) {

              $api_provider = get_post_meta( $product_id, '_suwp_api_provider', true );
              $api_provider_name = get_the_title($api_provider);

              if ( $api_provider  == '000' ) {
                $api_provider_name = "Stand-alone";
              }
              if ( $api_provider  == 'None' ) {
                $api_provider_name = "None";
              }
              
              $product_name = $product->get_name();
              $imei = wc_get_order_item_meta( $order_item_id, 'suwp_imei_values', true );
          }

        }

        $post_array[$i]['api_provider'] = $api_provider;
        $post_array[$i]['api_provider_name'] = $api_provider_name;
        $post_array[$i]['product_name'] = $product_name;
        $post_array[$i]['status_name'] = $status_name;
        $post_array[$i]['order_total'] = $order_total;
        $post_array[$i]['timestamp'] = $timestamp;
        $post_array[$i]['paid_date'] = $paid_date;
        $post_array[$i]['m'] = $year . '' .$month;
        $post_array[$i]['year'] = $year;
        $post_array[$i]['month'] = $month;
        $post_array[$i]['total_items'] = $total_items;
        $post_array[$i]['first_name'] = $first_name;
        $post_array[$i]['last_name'] = $last_name;
        $post_array[$i]['order_name'] = $order_name;
        $post_array[$i]['customer_user'] = $customer_user;
        $post_array[$i]['imei'] = $imei;
        $i++;
      endwhile;
    endif;

    // error_log('api_provider = ' . $api_provider . ', api_provider_name = ' . $api_provider_name);

    // error_log('>>>>>> !!!!!!! fetch_base_table_data post_array = ' . print_r($post_array,true));
    // return result array to prepare_items.

    return $post_array;		
  }
  
	/*
	 * Fetch table data from the WordPress database.
	 * 
	 * @since 1.9.2.3
	 * 
	 * @return	Array
	 */
  public function fetch_table_data() {

    global $wpdb;
    $month_query = '';
    $status_query = '';
    $service_query = '';

    ////////////////////////////////
    // collect suwp specific order_item_ids 
    $orderItemIds = array();
    $wpdb_table_itemmeta = $wpdb->prefix . 'woocommerce_order_itemmeta';
    $meta_filter = '\'_suwp_qty_sent\'';
    $meta_query = ' where meta_key=' . $meta_filter . ' ';
    $orderby = 'order_item_id' ; // "CAST('order_item_id' as unsigned)"; 'order_item_id';
    $order = 'ASC'; //  'DESC'; 'ASC';
    $orderitem_query = "SELECT 
                      order_item_id
                    FROM 
                      $wpdb_table_itemmeta
                      $meta_query
                    ORDER BY $orderby $order ";

    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results( $orderitem_query, ARRAY_A );
		foreach ($query_results as $row) {
			foreach( $row as $key => $value ) {
				$orderItemIds[] = $value;
			}
    }

    // collect suwp specific order_ids to be INCLUDED later using post__in
    $orderIds = array();
    $wpdb_table_orderitems = $wpdb->prefix . 'woocommerce_order_items';
		foreach( $orderItemIds as $value ) {
      $order_item_id_query = ' where order_item_id=' . $value . ' ';
      $orderby = 'order_item_id' ; // "CAST('order_item_id' as unsigned)"; 'order_item_id';
      $order = 'ASC'; //  'DESC'; 'ASC';
      $order_item_query = "SELECT 
                      *
                    FROM 
                      $wpdb_table_orderitems
                      $order_item_id_query
                    ORDER BY $orderby $order ";

      foreach( $wpdb->get_results($order_item_query) as $key => $row) {
				$orderIds[] = $row->order_id;
      }
    }

    $month_query = ( isset( $_GET['m'] ) ) ? esc_sql( $_GET['m'] ) : '';
    // error_log('MONTH QUERY : ' . $month_query);
    if( $month_query != '' ) {
      $orderIds = array();
      $order_docs = $this->fetch_base_table_data();
      $month_filters = array_column($order_docs, 'm', 'ID');
      foreach( $month_filters as $key=>$val ) {
        // error_log('KEY : ' . $key . ', VALUE : ' . $val);
        if( $month_query === $val) {
          $orderIds[] = $key;
        }
        // error_log('ALL OF THE SELECTION(S) ' . print_r($orderIds,true));
      } 
    }

    $status_query = ( isset( $_GET['status-filter'] ) ) ? esc_sql( $_GET['status-filter'] ) : '';
    if( $status_query != '' ) {
      $orderIds = array();
      $order_docs = $this->fetch_base_table_data();
      $status_filters = array_column($order_docs, 'post_status', 'ID');
      foreach( $status_filters as $key=>$val ) {
        // error_log('KEY : ' . $key . ', VALUE : ' . $val);
        if( $status_query === $val) {
          $orderIds[] = $key;
        }
        // error_log('ALL OF THE SELECTION(S) ' . print_r($orderIds,true));
      } 
    }

    $service_query = ( isset( $_GET['service-filter'] ) ) ? esc_sql( $_GET['service-filter'] ) : '';
    if( $service_query != '' ) {
      $orderIds = array();
      $order_docs = $this->fetch_base_table_data();
      $service_filters = array_column($order_docs, 'product_name', 'ID');
      foreach( $service_filters as $key=>$val ) {
        // error_log('KEY : ' . $key . ', VALUE : ' . $val);
        if( $service_query === $val) {
          $orderIds[] = $key;
        }
        // error_log('ALL OF THE SELECTION(S) ' . print_r($orderIds,true));
      } 
    }

    $provider_query = ( isset( $_GET['provider-filter'] ) ) ? esc_sql( $_GET['provider-filter'] ) : '';
    if( $provider_query != '' ) {
      $orderIds = array();
      $order_docs = $this->fetch_base_table_data();
      $provider_filters = array_column($order_docs, 'api_provider', 'ID');
      foreach( $provider_filters as $key=>$val ) {
        // error_log('KEY : ' . $key . ', VALUE : ' . $val);
        if( $provider_query === $val) {
          $orderIds[] = $key;
        }
        // error_log('ALL OF THE SELECTION(S) ' . print_r($orderIds,true));
      } 
    }

    // suwp-order-type=stand-alone
    $provider_query = ( isset( $_GET['suwp-order-type'] ) ) ? esc_sql( $_GET['suwp-order-type'] ) : '';
    if( $provider_query == '000' ) {
      $orderIds = array();
      $order_docs = $this->fetch_base_table_data();
      $provider_filters = array_column($order_docs, 'api_provider', 'ID');
      foreach( $provider_filters as $key=>$val ) {
        // error_log('KEY : ' . $key . ', VALUE : ' . $val);
        if( $provider_query === $val) {
          $orderIds[] = $key;
        }
        // error_log('ALL OF THE SELECTION(S) ' . print_r($orderIds,true));
      } 
    }

    // suwp-order-type=all-providers
    $provider_query = ( isset( $_GET['suwp-order-type'] ) ) ? esc_sql( $_GET['suwp-order-type'] ) : '';
    if( $provider_query == 'all-providers' ) {
      $orderIds = array();
      $order_docs = $this->fetch_base_table_data();
      $provider_filters = array_column($order_docs, 'api_provider', 'ID');
      foreach( $provider_filters as $key=>$val ) {
        // error_log('KEY : ' . $key . ', VALUE : ' . $val);
        if( $val != '000') {
          $orderIds[] = $key;
        }
        // error_log('ALL OF THE SELECTION(S) ' . print_r($orderIds,true));
      } 
    }

    $wpdb_table = $wpdb->prefix . 'posts';

    // ordering is done on the fly
    $order_args = array(
      'post__in' => $orderIds,
      'post_type' => array( 'shop_order' ),
      'posts_per_page'=> -1,
    );

    $order_posts = new WP_Query( $order_args );
    $post_array = array();
    if($order_posts->have_posts()) :
      // error_log('>>>>>> !!!!!!! wpdb_table_post order_posts = ' . print_r($order_posts->posts,true));

      $i = 0;
      while($order_posts->have_posts()) : $order_posts->the_post();

        $post_array[] = (array) $order_posts->posts[$i];

        $ID = get_the_ID();
        $status_name =  wc_get_order_status_name( get_post_status() );
        $order_doc = new WC_Order( $ID );
        $items = $order_doc->get_items();
        $total_items = count($items);

        $first_name = get_post_meta( $ID, '_billing_first_name', true );
        $last_name = get_post_meta( $ID, '_billing_last_name', true );
        $order_name = '#' . $ID . ' ' . $first_name . ' ' . $last_name;
        $customer_user = get_post_meta( $ID, '_billing_address_index', true );
        $order_total = get_post_meta( $ID, '_order_total', true );
        $timestamp = strtotime( get_post_meta($ID, '_paid_date', true) );
        $date = date_create( get_post_meta( $ID, '_paid_date', true ) );
        $paid_date = date_format( $date,"M d, Y" );
        $time = strtotime(get_the_date());
        $year = date("Y",$time);
        $date = date_parse(date("F",$time));
        $month = $date['month'];
        
        $product_name = '';
        $imei = '';
        $api_provider = '';
        $api_provider_name = '';

        // error_log('');
        // error_log('');
        // error_log('>>>>>>>>  <<<<<<<< INDIVIDUAL OUTPUT FOR $ORDER_POST $ID = ' . $ID . ', $total_items = ' . $total_items . ', $order_total = ' . $order_total );

        // error_log('>>>>>>>>   <<<<<<<< TESTING TO ISOLATE ERROR ITEM FOR $items: ' . print_r($items,true));

        foreach ( $items as $item ) {
            $order_item_id = $item->get_id();
            $product_id = $item['product_id'];

            // error_log('>>>>>>>>   <<<<<<<< INDIVIDUAL OUTPUT FOR $ORDER_POST: $product_id = ' . $product_id );
            // error_log('');
            // error_log('');

            $product = wc_get_product( $product_id );
            if ( $product ) {

              $api_provider = get_post_meta( $product_id, '_suwp_api_provider', true );
              $api_provider_name = get_the_title($api_provider);

              if ( $api_provider  == '000' ) {
                $api_provider_name = "Stand-alone";
              }
              if ( $api_provider  == 'None' ) {
                $api_provider_name = "None";
              }

              // error_log('');
              // error_log('');
              // error_log('>>>>>>>>   <<<<<<<< INDIVIDUAL OUTPUT FOR $item: $order_item_id = ' . $order_item_id . ', $product_id = ' . $product_id . ', $api_provider = ' . $api_provider . ', $api_provider_name = ' . $api_provider_name . ', ');
          
              // $product->get_type();
              $product_name = $product->get_name();

              // error_log('>>>>>>>>   <<<<<<<< INDIVIDUAL OUTPUT FOR $item: $product_name = ' . $product_name );
              // error_log('');
              // error_log('');

              $imei = wc_get_order_item_meta( $order_item_id, 'suwp_imei_values', true );
              // error_log('TOTAL ITEMS : ' . $total_items . ', THE NAME :' . $product->get_name() . ', PRICE : ' . $product->get_price() . ', IMEI : ' . $imei . ', SATUS : ' . $product->get_status() );
          }

        }

        $post_array[$i]['api_provider'] = $api_provider;
        $post_array[$i]['api_provider_name'] = $api_provider_name;
        $post_array[$i]['product_name'] = $product_name;
        $post_array[$i]['status_name'] = $status_name;
        $post_array[$i]['order_total'] = $order_total;
        $post_array[$i]['timestamp'] = $timestamp;
        $post_array[$i]['paid_date'] = $paid_date;
        $post_array[$i]['m'] = $year . '' .$month;
        $post_array[$i]['year'] = $year;
        $post_array[$i]['month'] = $month;
        $post_array[$i]['total_items'] = $total_items;
        $post_array[$i]['first_name'] = $first_name;
        $post_array[$i]['last_name'] = $last_name;
        $post_array[$i]['order_name'] = $order_name;
        $post_array[$i]['customer_user'] = $customer_user;
        $post_array[$i]['imei'] = $imei;

        // error_log('>>>>>> !!!!!!! wpdb_table_post row_val = ' . get_the_title());
        $i++;
      endwhile;
    endif;
    
    // error_log('>>>>>> !!!!!!! wpdb_table_post test_array = ' . print_r($post_array,true));
    ////////////////////////////////
    // $order = "thiszisise";

    $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : '';
    $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : '';
    // error_log('ORDERING BY : ' . $orderby . ', ORDER : ' . $order);
    // error_log('>>>>>> !!!!!!! ORDER : ' . $order . ', ORDERBY : ' . $orderby);
    
    switch ($order) {
      
      case 'asc':
      
        switch ($orderby) {

          case 'order_name':
          // since v1.9.5.11 numbers, sort ascending by value
          // formerly used 'order_name' string comparison
          usort($post_array, function ($a, $b) {
            return $a['ID'] - $b['ID'];
          });
          break;

          case 'paid_date':
          // since v1.9.5.11 numbers, sort ascending by timestamp
          usort($post_array, function ($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
          });
          break;

          case 'post_status':
          // string comparison ascending
          usort($post_array, function ($a, $b) {
            return strcmp($a['post_status'], $b['post_status']);
          });
          break;

          case 'product_name':
          // string comparison ascending
          usort($post_array, function ($a, $b) {
            return strcmp($a['product_name'], $b['product_name']);
          });
          break;

          case 'api_provider_name':
          // string comparison ascending
          usort($post_array, function ($a, $b) {
            return strcmp($a['api_provider_name'], $b['api_provider_name']);
          });
          break;

          case 'order_total':
          // since v1.9.5.11 numbers, sort ascending by value
          usort($post_array, function ($a, $b) {
            return $a['order_total'] - $b['order_total'];
          });
          break;
          
        }
      break;

      case 'desc':
        
        switch ($orderby) {

          case 'order_name':
          // since v1.9.5.11 numbers, sort descending by value
          // formerly used 'order_name' string comparison
          usort($post_array, function ($a, $b) {
            return $b['ID'] - $a['ID'];
          });
          break;

          case 'paid_date':
          // since v1.9.5.11 numbers, sort descending by timestamp
          usort($post_array, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
          });
          break;

          case 'post_status':
          // string comparison descending
          usort($post_array, function ($a, $b) {
            return strcmp($b['post_status'], $a['post_status']);
          });
          break;

          case 'product_name':
          // string comparison descending
          usort($post_array, function ($a, $b) {
            return strcmp($b['product_name'], $a['product_name']);
          });
          break;

          case 'api_provider_name':
          // string comparison descending
          usort($post_array, function ($a, $b) {
            return strcmp($b['api_provider_name'], $a['api_provider_name']);
          });
          break;

          case 'order_total':
          // since v1.9.5.11 numbers, sort descending by value
          usort($post_array, function ($a, $b) {
            return $b['order_total'] - $a['order_total'];
          });
          break;
          
        }
      break;
      default:
      // since v1.9.5.11 numbers, sort descending by value
      // formerly string comparison by 'ID', now using 'timestamp'
      usort($post_array, function ($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
      });

    }
    
    // return result array to prepare_items.
    return $post_array;		

    ////////////////////////////////
    /* PREVIOUS METHOD FOR RETRIEVAL
    $month_filter = ( isset( $_GET['m'] ) ) ? esc_sql( $_GET['m'] ) : '';
    if( $month_filter > 0 ) {
      $month_query = $month_query . ' where post_date=' . $month_query . ' ';   
    }

    $cat_query = ' where post_type=\'shop_order\' ';
    $cat_filter = ( isset( $_GET['cat-filter'] ) ) ? esc_sql( $_GET['cat-filter'] ) : '';
    if( $cat_filter > 0 ) {
      $cat_query = $cat_query . ' AND ID=' . $cat_filter . ' ';   
    }
    
    
    $wpdb_table = $wpdb->prefix . 'posts';
    $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'ID';
    $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';
    $order_query = "SELECT 
                      ID, post_title, post_status, post_password, post_name, post_modified_gmt
                    FROM 
                      $wpdb_table
                      $cat_query
                    ORDER BY $orderby $order ";

    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results( $order_query, ARRAY_A );
    
    // return result array to prepare_items.
    return $query_results;		
    */
    ////////////////////////////////

  }
  
	/*
	 * Filter the table data based on the order search key
	 * 
	 * @since 1.9.2.3
	 * 
	 * @param array $table_data
	 * @param string $search_key
	 * @returns array
	 */
  public function filter_table_data( $table_data, $search_key ) {
    $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
      foreach( $row as $row_val ) {
        if( stripos( $row_val, $search_key ) !== false ) {
          return true;
        }				
      }			
    } ) );

    return $filtered_table_data;

  }

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
  public function column_default( $item, $column_name ) {	
    
    switch ( $column_name ) {			
      case 'ID':
      case 'order_name':
      case 'post_title':
      case 'paid_date':
      case 'post_title':
      case 'status_name':
      case 'product_name':
      case 'api_provider_name':
      case 'order_total':
        return $item[$column_name];
      default:
        return $item[$column_name];
    }

    /* ORIGINAL CODE
    switch ( $column_name ) {			
      case 'ID':
      case 'post_title':
      case 'post_date':
        return $item[$column_name];
      default:
        return $item[$column_name];
    }
    */

  }

   /**
   * Get value for checkbox column.
   *
   * @param object $item  A row's data.
   * @return string Text to be placed inside the column <td>.
   */
  protected function column_cb( $item ) {
    return sprintf(		
    '<label class="screen-reader-text" for="order_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['ID'] ) . '</label>'
    . "<input type='checkbox' name='orders[]' id='order_{$item['ID']}' value='{$item['ID']}' />"					
    );
  }

  /*
  * Method for rendering the order_name column.
  * 
  * Adds row action links to the order_name column.
  * 
  * @param object $item A singular item (one full row's worth of data).
  * @return string Text to be placed inside the column <td>.
  * 
  */
  protected function column_order_name( $item ) {
    
    add_thickbox();

    /*
		 *  Build usermeta row actions.
		 * 
		 * e.g. /admin.php?page=suwp_orders_admin_page&action=view_ordermeta&order=18&_wpnonce=1984253e5e
		 */
		
    $admin_page_url =  admin_url( 'admin.php' );
    $admin_post_url =  admin_url( 'post.php' );

    // error_log('column_order_item_name item: ' . print_r($item,true));

    // row action to view ordermeta.
  /**
    $query_args_view_ordermeta = array(
      'page'		=>  wp_unslash( $_REQUEST['page'] ),
      'action'	=> 'view_ordermeta',
			'ID'		=> absint( $item['ID']),
      '_wpnonce'	=> wp_create_nonce( 'view_ordermeta_nonce' ),
    );.
  */

    $query_args_view_ordermeta = array(
			'action'	=> 'suwp_ordermeta',
			'suwp_action'	=> 'suwp_view',
			'ID'		=> absint( $item['ID']),
      'TB_iframe' => 'true',
      'width'     => '600',
      'height'    => '400',
      '_wpnonce'	=> wp_create_nonce( 'view_ordermeta_nonce' ),
    );
    $view_ordermeta_link = esc_url( add_query_arg( $query_args_view_ordermeta, $admin_page_url ) );		
    // >>> $actions['view_ordermeta'] = '<a href="' . $view_ordermeta_link . '" class="thickbox">' . __( 'View Meta', $this->plugin_text_domain ) . '</a>';		
    
    // row actions to add ordermeta.
    /**
		$query_args_add_ordermeta = array(
			'page'		=>  wp_unslash( $_REQUEST['page'] ),
			'action'	=> 'add_ordermeta',
			'ID'		=> absint( $item['ID']),
			'_wpnonce'	=> wp_create_nonce( 'add_ordermeta_nonce' ),
    );
    */

		$query_args_add_ordermeta = array(
			'action'	=> 'suwp_ordermeta',
			'suwp_action'	=> 'suwp_add',
			'ID'		=> absint( $item['ID']),
      'TB_iframe' => 'true',
      'width'     => '600',
      'height'    => '400',
			'_wpnonce'	=> wp_create_nonce( 'add_ordermeta_nonce' ),
    );
		$add_ordermeta_link = esc_url( add_query_arg( $query_args_add_ordermeta, $admin_page_url ) );		
		// >>> $actions['add_ordermeta'] = '<a href="' . $add_ordermeta_link . '" class="thickbox">' . __( 'Add Meta', $this->plugin_text_domain ) . '</a>';			
  
    // row actions to edit post.
		$query_args_edit_order = array(
      'post'		=> absint( $item['ID']),
			'action'	=> 'edit',
      'suwpDoModal'    => 'true',
      'TB_iframe' => 'true',
      'width'     => '600',
      'height'    => '400',
    );
		$edit_order_link = esc_url( add_query_arg( $query_args_edit_order, $admin_post_url ) );	
		$actions['edit_order'] = '<a href="' . $edit_order_link . '" class="thickbox">' . __( 'Edit Order', $this->plugin_text_domain ) . '</a>';			
  
    // class="button button-primary thickbox">
    // add_thickbox();
    // <a href="your url" class="thickbox">click here</a>
    
    // similarly add row actions for add ordermeta.
    $row_value = '<strong>' . $item['order_name'] . '</strong>';
    return $row_value . $this->row_actions( $actions );
  }

  /**
   * Returns an associative array containing the bulk action
   *
   * @since    1.9.2.3
   * 
   * @return array
   */
  public function get_bulk_actions() {

    /*
    * on hitting apply in bulk actions the url params are set as
    * ?action=bulk-download&paged=1&action2=-1
    * 
    * action and action2 are set based on the triggers above and below the table		 		    
    */
    $actions = array(
      'bulk-download' => 'Sample action'
    );

    return $actions;
  }

  /**
   * Process actions triggered by the user
   *
   * @since    1.9.2.3
   * 
   */	
  public function handle_table_actions() {	
    
  /*
  * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
  * 
  * action - is set if checkbox from top-most select-all is set, otherwise returns -1
  * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
  */
  
  // check for individual row actions
  $the_table_action = $this->current_action();

  if ( 'view_ordermeta' === $the_table_action ) {
    $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
    // verify the nonce.
    if ( ! wp_verify_nonce( $nonce, 'view_ordermeta_nonce' ) ) {
      $this->invalid_nonce_redirect();
    }
    else {                    
      $this->page_view_ordermeta( absint( $_REQUEST['ID']) );
      $this->graceful_exit();
    }
  }
  
  if ( 'add_ordermeta' === $the_table_action ) {
    $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
    // verify the nonce.
    if ( ! wp_verify_nonce( $nonce, 'add_ordermeta_nonce' ) ) {
      $this->invalid_nonce_redirect();
    }
    else {                    
      $this->page_add_ordermeta( absint( $_REQUEST['ID']) );
      $this->graceful_exit();
    }
  }
  
	// check for table bulk actions   
  if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-download' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-download' ) ) {
    
    $nonce = wp_unslash( $_REQUEST['_wpnonce'] );	
    // verify the nonce.
    /*
    * Note: the nonce field is set by the parent class
    * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
    * 
    */
    if ( ! wp_verify_nonce( $nonce, 'bulk-orders' ) ) { // verify the nonce.
      $this->invalid_nonce_redirect();
    }
    else {

      if ( isset( $_REQUEST['orders'] ) ) {
        $this->page_bulk_download( $_REQUEST['orders']);
        $this->graceful_exit();
      }
    }
  }

}

  /**
   * View a order's meta information.
   *
   * @since   1.9.2.3
   * 
   * @param int $ID  order's ID	 
   */
  public function page_view_ordermeta( $ID ) {

    global $wpdb;

    $order_item = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=%d", $ID ) );
    
    $order = $order_item[0];

    // error_log(">>>>>>> VIEW - HERE IS THE ORDER >>>>>>>>> :" . print_r($order ,true));
    
    include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-view-ordermeta.php' );
	}
	
	/**
	 * Add a meta information for an order.
	 *
	 * @since   1.9.2.3
	 * 
	 * @param int $ID  order's ID	 
	 */	
  public function page_add_ordermeta( $ID ) {
		
    global $wpdb;

    $order_item = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=%d", $ID ) );
    
    $order = $order_item[0];

    // error_log(">>>>>>> ADD - HERE IS THE ORDER >>>>>>>>> :" . print_r($order ,true));

    include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-add-ordermeta.php' );

  }
  
	/**
	 * Edit an order.
	 *
	 * @since   1.9.2.3
	 * 
	 * @param int $ID  order's ID	 
	 */	
  public function post_edit_order( $ID ) {
		
    global $wpdb;

    $order_item = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=%d", $ID ) );
    
    $order = $order_item[0];

    include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-edit-order.php' );
  }

	/**
	 * Bulk process orders.
	 *
	 * @since   1.9.2.3
	 * 
	 * @param array $bulk_order_ids
	 */		
  public function page_bulk_download( $bulk_order_ids ) {
				
    include_once( SUWP_PATH . 'admin/partials/stockunlocks-admin-stand-alone-table-bulk-download.php' );
	}    		
	
	/**
	 * Stop execution and exit
	 *
	 * @since    1.9.2.3
	 * 
	 * @return void
	 */
  public function graceful_exit() {
    exit;
  }
  
	/**
	 * Die when the nonce check fails.
	 *
	 * @since    1.9.2.3
	 * 
	 * @return void
	 */    	 
  public function invalid_nonce_redirect() {
		wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
				__( 'Error', $this->plugin_text_domain ),
				array( 
						'response' 	=> 403, 
						'back_link' =>  esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'admin.php' ) ) ),
					)
		);
   }
   
}
