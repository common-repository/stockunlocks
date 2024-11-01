<?php

/**
 * The plugin area to view the ordermeta
 */

$plugin_text_domain = 'stockunlocks';

	if( current_user_can('edit_users' ) ) { ?>
<div id="wpcontent">
		<h2> <?php echo __('Displaying Ordermeta for ' . $order->ID . ' (' . $order->post_title . ')', $plugin_text_domain ); ?> </h2>

		<button type="submit" class="button save_order button-primary" name="save" value="Update">Close</button>
		<!--
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'admin.php' ) ) ); ?>"><?php _e( 'Back', $plugin_text_domain ) ?></a>
		-->

<?php

		$ordermeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=%d", $ID ) );
		
		// error_log(">>>>>>> VIEW - HERE IS THE ORDER META >>>>>>>>> :" . print_r($ordermeta ,true) );
    
		echo '<div class="card">';
		foreach ($ordermeta as $row) {
			foreach( $row as $key => $value ) {
				$v = (is_array($value)) ? implode(', ', $value) : $value;            
				echo '<p">'. $key . ': ' . $v . '</p>';
			}
		}
		echo '</div><br>';
?>
	<button type="submit" class="button save_order button-primary" name="save" value="Update">Close</button>
	<!--
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'admin.php' ) ) ); ?>"><?php _e( 'Back', $plugin_text_domain ) ?></a>
	-->
</div>

<?php
	}
	else {  
?>
<div id="wpcontent">
		<p> <?php echo __( 'You are not authorized to perform this operation.', $plugin_text_domain ) ?> </p>
</div>
<?php   
	}
