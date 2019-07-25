<?php 

/*

//Login Template Class Psuedo Code

---

Description: This is where the HTML is housed for the LOgin form: 

To do's: 
	- A login form listener needs to be setup. 

	
//From Pippin plugins: 
*/

	

namespace tmpl;


//use proc\NNError as Error; 

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNLogin' ) ){
	class NNLogin{
	

		
		/*
			Name: __construct
			Description: 
		*/	
		
		public function __construct(){			
		
		
			
		}
		
		
		
		/*
			Name: login form fields
			Description: 
		*/			
		
		public function login_form_fields() {
		 
			ob_start(); ?>
				<h3 class="nn_header"><?php _e('Login'); ?></h3>
		 
				<?php
				// show any error messages after form submission
				$this->show_error_messages(); ?>
		 
				<form id="nn_login_form"  class="nn_form" action="" method="post">
					<section>
						<p>
							<label for="nn_patron_login">Username</label>
							<input name="nn_patron_login" id="nn_patron_login" class="required" type="text"/>
						</p>
						<p>
							<label for="nn_user_pass">Password</label>
							<input name="nn_user_pass" id="nn_user_pass" class="required" type="password"/>
						</p>
						<p>
							<input type="hidden" name="nn_login_nonce" value="<?php echo wp_create_nonce('nn-login-nonce'); ?>"/>
							<input id="nn_login_submit" type="submit" value="Login"/>
						</p>
					</section>
				</form>
			<?php
			return ob_get_clean();
		}

		/*
			Name: show_error_messags
			Description: 
		*/	
		
		
		public function show_error_messages(){
			
			if($codes = nn_errors()->get_error_codes()) {
				
				echo '<div class="nn_errors">';
					// Loop error codes and display errors
				   foreach($codes as $code){
						$message = nn_errors()->get_error_message( $code );
						echo '<span class="error"><strong>' . __('Oops!') . '</strong> ' . $message . '</span><br/>';
					}
				echo '</div>';
			}	
			
		}
	}
}