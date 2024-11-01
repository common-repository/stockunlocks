<?php

/**
 * The plugin area to process the table bulk actions.
 */

$plugin_text_domain = 'stockunlocks';

	if( current_user_can('edit_users' ) ) { ?>
	<!-- <div id="wpcontent"> -->
		<h2> <?php echo __('SAMPLE ACTION - Process bulk operations for the selected orders: <br>', 'stockunlocks' ); ?> </h2>

		<!-- 
			<button type="submit" class="button save_order button-primary" name="save" value="Update">Close</button>
		-->
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'admin.php' ) ) ); ?>"><?php _e( 'Back', $plugin_text_domain ) ?></a>
		
		<h4>
			<ul>
			<?php
				
				global $wpdb;
				
				foreach( $bulk_order_ids as $ID ) {
					
					$order_item = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=%d", $ID ) );
					
					$order = $order_item[0];
					
					echo '<li>' . $order->ID . ' (' . $order->post_title . ')' . '</li>';
				
				}
			?>
			</ul>
		</h4>
		<div class="card">
			<h4> This is where you would perform the operations. </h4>
		</div>
		<br>
		
		<!-- 
			<button type="submit" class="button save_order button-primary" name="save" value="Update">Close</button>
		-->
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'admin.php' ) ) ); ?>"><?php _e( 'Back', $plugin_text_domain ) ?></a>

	<!-- </div> -->
<?php
	}
	else {  
?>
	<!-- <div id="wpcontent"> -->
		<p> <?php echo __( 'You are not authorized to perform this operation.', 'stockunlocks' ) ?> </p>
	<!-- </div> -->
<?php   
	}
