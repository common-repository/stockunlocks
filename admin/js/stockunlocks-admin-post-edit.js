jQuery(document).ready(function() {

	// alert( "parent.location = " + parent.location );
	// alert( JSON.stringify(woocommerce_admin_meta_boxes) );
	// console.log(JSON.stringify(woocommerce_admin_meta_boxes) );

	var metaArray = $.makeArray( woocommerce_admin_meta_boxes )
	$post_id = metaArray[0]['post_id'];

	// div id = "TB_title"
	// 	div id = "TB_ajaxWindowTitle"
	// 	div id = "TB_closeAjaxWindow"
	// 		button type= "button" id = "TB_closeWindowButton"
	// alert( document.getElementById('TB_closeAjaxWindow') );
	// alert( JSON.stringify(document.getElementById('TB_closeWindowButton')) );
	// $iFrameID = window.frameElement.id;
	// alert( $iFrameID );

	/**
	$("button").click(function(e){
		var idClicked = e.target.id;
		console.log("CLICKED IT.");
	});
	*/

	$('#adminmenuback, #adminmenuwrap, #wpadminbar, #woocommerce-order-items, #screen-meta-links').remove();
	$('label[for="customer_user"]').text('Customer:');
	$('#TB_ajaxWindowTitle', window.parent.document).text('StockUnlocks - Edit order #' + $post_id);
	$(".wrap h1").text('Edit order #' + $post_id);
	
	// removes all links, everywhere
	// $('.wrap a').remove();

	// remove specific link
	$("div.wrap").find("a").each(function(){
		var linkText = $(this).text();
		if ( linkText == 'Add order') {
			$(this).remove();
		}
		// $(this).before(linkText);
		// $(this).remove();
	});

	// update parent with changes
	window.self.onunload = refreshParent;
		function refreshParent() {
			parent.location.reload();
	}

	$("form").submit(function(e){
		// alert("Submit .... not.");
		// e.preventDefault();
	});
	
});
