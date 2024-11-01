<?php

/**
 * The admin area of the plugin to load the User List Table
 */
?>

<div class="wrap">    
   <h2><?php _e( 'Manage Orders', 'stockunlocks'); ?></h2>
   		<h1 class="wp-heading-inline"></h1>
		<div id="suwp_dashboard_admin_page">			
            <div id="suwp-post-body">		
				<form id="suwp-order-list-form" method="get">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					<?php 
						$order_list_table->views();
						$order_list_table->search_box( __( 'Search orders', 'stockunlocks' ), 'suwp-order-find');
						$order_list_table->display(); 
					?>					
				</form>
            </div>			
        </div>
</div>
