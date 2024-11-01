<?php

/**
 * The plugin area to admin the ordermeta
 */

$plugin_text_domain = 'stockunlocks';

	if( current_user_can('edit_users' ) ) { ?>
	<div id="wpcontent">
		<h2> <?php echo __('Add Ordermeta for ' . $order->ID . ' (' . $order->post_title . ')', $plugin_text_domain ); ?> </h2>
		<br>
		<div class="card">
			<h4> This is where a form would be added to perform ordermeta operations. </h4>
		</div>
		<br>

		<button type="submit" class="button save_order button-primary" name="save" value="Update">Save</button>
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
