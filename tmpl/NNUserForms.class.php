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
	
if( !class_exists( 'NNUserForms' ) ){
	class NNUserForms{
	

		//Properties
		
		public $registerlite = array(
			'fields' => array( 
				'login' => [
					'type'		=> 'text',
					'title' 	=> 'Username',
					'detail'	=> 'Select a username, must be unique.',
					'error'		=> 0
				],
				'email' => [
					'type'		=> 'email',
					'title' 	=> 'Email Address',
					'detail'	=> 'Please double-check the spelling of your primary email address',
					'error'		=> 0
				],
				'password' => [
					'type'		=> 'password',
					'title' 	=> 'Password',
					'detail'	=> 'Select a Password',
					'error'		=> 0
				],
				'confirm' => [
					'type'		=> 'password',
					'title' 	=> 'Confirm Password',
					'detail'	=> 'Re-enter your password',
					'error'		=> 0
				],
			),	
			'title' => 'Register',	
			'header' => "Don't have an account?",	
			'submit' => 'Register',	
			'footer' => 'Details on registration.'
		);
		
		public $login = array(
			'fields' => array( 
				'login'		=>	[
					'type'		=> 'text',
					'title' 	=> 'Username',
					'detail'	=> 'Enter your username',
					'error'		=> 0
				],
				'password' 	=>	[
					'type'		=> 'password',
					'title' 	=> 'Password',
					'detail'	=> 'Enter your password',
					'error'		=> 0
				],
			),	
			'title' => 'Login',	
			'header' => "Already have an account? Login now!",	
			'submit' => 'Login',	
			'footer' => 'Details on login.'
		);
		
		
		
		/*
			Name: __construct
			Description: 
		*/	
		
		public function __construct(){			
		
			$this->set_errors();
			
		}
		
		
		
		/*
			Name: form fields
			Description: 
		*/			
		
		public function form( $type, $post = '' ) {
		 
			$vals = $this->$type;
			
			
			/* $action = ( !empty( $post[ 'action' ] ) )?  $post[ 'action' ] : '/';
			unset( $post[ 'action' ] ); */
		 
			$action = 'nn-'. $type .'-nonce';
			
			//ep( $action );	
			//unset( $post[ '_nn_nonce' ] );
			$nonce = wp_nonce_field( $action, '_nn_'.$type.'_nonce', false, false );
		 
		 
			ob_start(); ?>	
				<h3 class="nn_header"><?php _e( $vals[ 'title' ] ); ?></h3>
		 
				<?php 
				// show any error messages after form submission
				$this->show_error_messages( $type ); ?>
		 
		 
		 
				<form id="nn_<?php echo $type; ?>_form" class="nn_form" action="" method="POST">
					<fieldset>
					
						<?php
							if( !empty( $post ) ){
								//remove patron value
								unset( $post[ 'patron' ] );
								foreach( $post as $pkey => $pval ){
									echo $this->hidden( $pkey, $pval ); 
								}	
							}
						
						/*
						
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
						*/
						
						foreach( $vals[ 'fields' ] as $key => $arr ){
							echo $this->fields( $arr, $key ); 
						}
						
						?>
						<p>
						<?php /*
							<input type="hidden" name="_nn_<?php echo $type; ?>_nonce" value="<?php echo wp_create_nonce( $action ); ?>"/> */
							
							echo $nonce;
							?>
							<input type="submit" value="<?php _e( $vals[ 'submit' ] ); ?>"/>
						</p>
					</fieldset>
				</form>
			<?php
			return ob_get_clean();
		}

		
		/*
			Name: fields
			Description: 
		*/	
		
		
		public function fields( $arr, $val, $required = true ){
			
			ob_start(); ?>	
			
				<p>
					<label for="nn_<?php echo $val ?>"><?php echo ucfirst( $arr[ 'title' ] ) ?></label>
					<input name="nn_<?php echo $val ?>" id="nn_<?php echo $val; ?>" class="<?php echo ($required)? 'required':''; ?> <?php echo ( $arr[ 'error' ] !== 0 )? 'error' : '' ;?>" type="<?php echo $arr[ 'type' ]; ?>" placeholder="<?php echo $arr[ 'detail' ]; ?>"/>
				</p>
				
				<?php
			return ob_get_clean();
		}

		/*
			Name: show_error_messags
			Description: $type = type of form. 
		*/	
		
		
		public function show_error_messages( $type ){
			
			if($codes = nn_errors()->get_error_codes()) {
				print_pre( nn_errors() );
				
				echo '<div class="nn_errors">';
					// Loop error codes and display errors
				   foreach($codes as $code){
						$message = nn_errors()->get_error_message( $code );
						echo '<span class="error"><strong>' . __('Oops!') . '</strong> ' . $message . '</span><br/>';
					}
				echo '</div>';
			}	
			
		}
		
		
		/*
			Name: skip
			Description: Skips registration process. 
		*/	
		
		
		public function skip( $post ){
			
			$type = 'skip';
			
			ob_start(); ?>	
				
				<form id="nn_<?php echo $type; ?>_form" class="nn_form" action="/cashier/" method="POST">
					
					<?php
						foreach( $post as $pkey => $pval ){
							echo $this->hidden( $pkey, $pval ); 
						}
					?>
					
					
					<p>
						<input type="hidden" name="patron" value="-1" />
						<input type="hidden" name="_nn_<?php echo $type; ?>_nonce" value="<?php echo wp_create_nonce('nn-'.$type.'-nonce'); ?>"/>
						<input type="submit" value="<?php _e( 'Skip Registration' ); ?>"/>
					</p>
				</form>
					
				<?php
			return ob_get_clean();
		}
		
		
		
		/*
			Name: hidden
			Description: adds hidden values to form. 
		*/	
		
		
		public function hidden( $key, $val ){
	
			return "<input type='hidden' name='$key' value='$val' />";
			
		}		
		
		/*
			Name: set_errors
			Description: If there are errors on the field.  
		*/	
		
		
		public function set_errors(){
	
			$errors = nn_errors();
			
			$e_codes = $errors->get_error_codes();
			
			print_pre( $e_codes );
			//STOPPED HERE. 
			//$this->vals;
			
		}
		
	}
}
?>	