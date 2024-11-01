
			   <!-- * Output inline styles here 
					* because there's no reason to keep these
					* enqueued after the alert is dismissed. -->
			   <style>div.updated.suwpremote,
				   div.updated.suwpremote header,
				   div.updated.suwpremote header img,
				   div.updated.suwpremote header h3,
				   div.updated.suwpremote .dismiss,
				   .suwpremote-actions,
				   .suwpremote-action,
				   .suwpremote-action #mc_embed_signup,
				   div.updated.suwpremote .suwpremote-action span.dashicons:before {
					   -webkit-box-sizing: border-box;
					   /* Safari/Chrome, other WebKit */
					   -moz-box-sizing: border-box;
					   /* Firefox, other Gecko */
					   box-sizing: border-box;
					   /* Opera/IE 8+ */
					   width: 100%;
					   position: relative;
					   padding: 0;
					   margin: 0;
					   overflow: hidden;
					   float: none;
					   display: block;
					   text-align: left;
				   }
				   .suwpremote-action a,
				   .suwpremote-action a:hover,
				   div.updated.suwpremote .suwpremote-action.mailchimp:hover,
				   div.updated.suwpremote .suwpremote-action.mailchimp span {
					   -webkit-transition: all 500ms ease-in-out;
					   -moz-transition: all 500ms ease-in-out;
					   -ms-transition: all 500ms ease-in-out;
					   -o-transition: all 500ms ease-in-out;
					   transition: all 500ms ease-in-out;
				   }
				   div.updated.suwpremote {
					   margin: 1rem 0 2rem 0;
				   }
				   div.updated.suwpremote header h3 {
					   line-height: 1.4;
				   }
				   @media screen and (min-width: 280px) {
					   div.updated.suwpremote {
						   border: 0px;
						   background: transparent;
						   -webkit-box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
						   box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
					   }
					   div.updated.suwpremote header {
						   background: #fff;
						   color: <?php echo $dismiss_1_color ?>;
						   position: relative;
						   height: 5rem;
					   }
					   div.updated.suwpremote header img {
						   display: none;
						   max-width: 130px;
						   margin: 0 0 0 1rem;
						   float: left;
					   }
					   div.updated.suwpremote header h3 {
						   float: left;
						   max-width: 60%;
						   margin: 1rem;
						   display: inline-block;
						   color: <?php echo $dismiss_1_color ?>;
					   }
					   div.updated.suwpremote header h3 span {
						   color: #38383A;
						   font-weight: 900;
						   font-family: 'Open Sans Black', 'Open Sans Regular', Verdana, Helvetica, sans-serif;
					   }
					   div.updated.suwpremote a.dismiss {
						   display: block;
						   position: absolute;
						   left: auto;
						   top: 0;
						   bottom: 0;
						   right: 0;
						   width: 6rem;
						   background: rgba(30, 115, 190, 1);
						   color: #fff;
						   text-align: center;
					   }
					   .suwpremote a.dismiss:before {
						   font-family: 'Dashicons';
						   content: "\f153";
						   display: inline-block;
						   position: absolute;
						   top: 50%;
	
						   transform: translate(-50%);
						   right: 40%;
						   margin: auto;
						   line-height: 0;
					   }
					   div.updated.suwpremote a.dismiss:hover {
						   color: #777;
						   background: rgba(30, 115, 190, .7);
					   }
	
					   /* END ACTIVATION HEADER
						* START ACTIONS
						*/
					   div.updated.suwpremote .suwpremote-action {
						   display: table;
					   }
					   .suwpremote-action a,
					   .suwpremote-action #mc_embed_signup {
						   background: rgba(0,0,0,.1);
						   color: rgba(51, 51, 51, 1);
						   padding: 0 1rem 0 6rem;
						   height: 4rem;
						   display: table-cell;
						   vertical-align: middle;
					   }
					   .suwpremote-action.mailchimp {
						   margin-bottom: -1.5rem;
						   top: -.5rem;
					   }
					   .suwpremote-action.mailchimp p {
						   margin: 9px 0 0 0;
					   }
	
					   .suwpremote-action #mc_embed_signup form {
						   display: inline-block;
					   }
	
					   div.updated.suwpremote .suwpremote-action span {
						   display: block;
						   position: absolute;
						   left: 0;
						   top: 0;
						   bottom: 0;
						   height: 100%;
						   width: auto;
					   }
					   div.updated.suwpremote .suwpremote-action span.dashicons:before {
						   padding: 2rem 1rem;
						   color: <?php echo $dismiss_1_color ?>;
						   line-height: 0;
						   top: 50%;
						   transform: translateY(-50%);
						   background: rgba(163, 163, 163, .25);
					   }
					   div.updated.suwpremote .suwpremote-action a:hover,
					   div.updated.suwpremote .suwpremote-action.mailchimp:hover {
						   background: rgba(0,0,0,.2);
					   }
					   div.updated.suwpremote .suwpremote-action a {
						   text-decoration: none;
					   }
	
					   div.updated.suwpremote .suwpremote-action a,
					   div.updated.suwpremote .suwpremote-action #mc_embed_signup {
						   position: relative;
						   overflow: visible;
					   }
					   .suwpremote-action #mc_embed_signup form,
					   .suwpremote-action #mc_embed_signup form input#mce-EMAIL {
						   width: 100%;
					   }
					   div.updated.suwpremote .mailchimp form input#mce-EMAIL + input.submit-button {
						   display: block;
						   position: relative;
						   top: -1.75rem;
						   float: right;
						   right: 4px;
						   border: 0;
						   background: #cccccc;
						   border-radius: 2px;
						   font-size: 10px;
						   color: white;
						   cursor: pointer;
					   }
	
					   div.updated.suwpremote .mailchimp form input#mce-EMAIL:focus + input.submit-button {
						   background: <?php echo $dismiss_1_color ?>;
					   }
	
					   .suwpremote-action #mc_embed_signup form input#mce-EMAIL div#placeholder,
					   input#mce-EMAIL:-webkit-input-placeholder {opacity: 0;}
				   }
				   @media screen and (min-width: 780px) {
					   div.updated.suwpremote header h3 {line-height: 3;}
	
					   div.updated.suwpremote .mailchimp form input#mce-EMAIL + input.submit-button {
						   top: -1.55rem;
					   }
					   div.updated.suwpremote header img {
						   display: inline-block;
					   }
					   div.updated.suwpremote header h3 {
						   max-width: 50%;
					   }
					   .suwpremote-action {
						   width: 100%;
						   float: left;
					   }
					   div.updated.suwpremote .suwpremote-action a {
	
					   }
					   .suwpremote-action a,
					   .suwpremote-action #mc_embed_signup {
						   padding: 0 1rem 0 4rem;
					   }
					   div.updated.suwpremote .suwpremote-action span.dashicons:before {
	
					   }
					   div.updated.suwpremote .suwpremote-action.mailchimp {
						   width: 40%;
					   }
				   }</style>
				   
				   <!-- * Now output the HTML
						* of the banner 			-->
						
				   <div class="updated suwpremote">
					   <header>
						   <!-- Logo -->
						   <img src="<?php echo $dismiss_1_image; ?>"  class="suwp-logo"/>
						   
						   <!-- Message -->
						   <h3><?php _e( $dismiss_1_msg,'suwpremote'); ?></h3>
						   
						   <!-- The Dismiss Button -->
						   <?php printf(__('<a href="%1$s" class="dismiss"></a>', 'suwp'), '?' . $dismiss_1_key . '=0'); ?>
					   </header>
					   
					   <!-- * Now output a few "actions"
							* that the user can take from here -->
							
					   <div class="suwpremote-actions">
						   
						   <!-- Point them to the remote page -->
						   <div class="suwpremote-action">
							   <a href="<?php echo $dismiss_1_dashlink; ?>" <?php echo $dismiss_1_dashtarget; ?> >
								   <span class="dashicons dashicons-info"></span><?php _e( $dismiss_1_dashtxt, 'suwpremote'); ?>
							   </a>
						   </div>
					   </div>
				   </div>
			<?php
