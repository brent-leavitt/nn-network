<?php 

/*

/
Register Lite Class //Template Class

---

Description: This is used for the simple registration forms that will be employed around the network, such as newsletter subscriptions or any other promotional activities that will be needed. This is a a form that corresponds with related scripts of the same name. 

	- Typically one or two fields only: 
		- Name.  
		- Email Address. 
		- We may be crafty and be able  to do some location scouting based on IP Address or information. 
		
	

	
//From Pippin plugins: 
*/

	

namespace tmpl;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNRegisterLite' ) ){
	class NNRegisterLite{
	

		
		/*
			Name: __construct
			Description: 
		*/	
		
		public function __construct(){			
		
		
			
		}
		
		
		
		/*
			Name: form fields
			Description: 
		*/			
		
		public function form_fields() {
		 
			ob_start(); ?>	
				<h3 class="nn_header"><?php _e('Register'); ?></h3>
		 
				<?php 
				// show any error messages after form submission
				$this->show_error_messages(); ?>
		 
				<form id="nn_registration_form" class="nn_form" action="" method="POST">
					<fieldset>
						<p>
							<label for="nn_user_Login"><?php _e('Username'); ?></label>
							<input name="nn_user_login" id="nn_user_login" class="required" type="text"/>
						</p>
						<p>
							<label for="nn_user_email"><?php _e('Email'); ?></label>
							<input name="nn_user_email" id="nn_user_email" class="required" type="email"/>
						</p>
						<p>
							<label for="password"><?php _e('Password'); ?></label>
							<input name="nn_user_pass" id="password" class="required" type="password"/>
						</p>
						<p>
							<label for="password_again"><?php _e('Password Again'); ?></label>
							<input name="nn_user_pass_confirm" id="password_again" class="required" type="password"/>
						</p>
						<p>
							<input type="hidden" name="nn_register_nonce" value="<?php echo wp_create_nonce('nn-register-nonce'); ?>"/>
							<input type="submit" value="<?php _e('Register'); ?>"/>
						</p>
					</fieldset>
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
?>	