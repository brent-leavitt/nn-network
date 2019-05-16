<?php 

/*


Registration Lite //Functionality Class

---

Description: 
	- Why a lite version? Because We're only registering an email address or name and email address as most. 
	
	- This really depends upon the weight of the main register class. 
		- If it can be modularized then maybe it is no heavier to use the main class?
		
---
*/

namespace init;


// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNRegisterLite' ) ){
	class NNRegisterLite{

		//Methods
		/*
			Name: __construct
			Description: 
		*/	
		
		public function __construct(){			
				
			$this->init();
			
		}
		
		
		
		/*
			Name: init
			Description: 
		*/	
		public function init(){
			
			$this->do_registerlite();
			
		}
		
		
		/*
			Name: do_login
			Description: 
		*/	
		public function do_registerlite() {
			if( isset( $_POST[ "nn_login" ] ) && wp_verify_nonce( $_POST[ 'nn_registerlite_nonce' ], 'nn-registerlite-nonce' ) ){
				$user_login		= $_POST[ "nn_login" ];	
				$user_email		= $_POST[ "nn_email" ];
				/* $user_first 	= $_POST[ "nn_user_first" ];
				$user_last	 	= $_POST[ "nn_user_last" ]; */
				$user_pass		= $_POST[ "nn_password" ];
				$pass_confirm 	= $_POST[ "nn_confirm" ];
		 
				// this is required for username checks
				require_once(ABSPATH . WPINC . '/registration.php');
		 
				if( username_exists( $user_login ) ) {
					// Username already registered
					nn_errors()->add('username_unavailable', __( 'Username already taken' ));
				}
				if( !validate_username($user_login)) {
					// invalid username
					nn_errors()->add( 'username_invalid', __( 'Invalid username' ) );
				}
				if( $user_login == '') {
					// empty username
					nn_errors()->add( 'username_empty', __( 'Please enter a username' ) );
				}
				if( !is_email( $user_email ) ) {
					//invalid email
					nn_errors()->add( 'email_invalid', __( 'Invalid email' ) );
				}
				if( email_exists( $user_email ) ) {
					//Email address already registered
					nn_errors()->add( 'email_used', __( 'Email already registered' ) );
				}
				if( $user_pass == '') {
					// passwords do not match
					nn_errors()->add( 'password_empty', __( 'Please enter a password' ) );
				}
				if( $user_pass != $pass_confirm ) {
					// passwords do not match
					nn_errors()->add( 'password_mismatch', __( 'Passwords do not match' ) );
				}
		 
				$errors = nn_errors()->get_error_messages();
		 
				// only create the user in if there are no errors
				if( empty( $errors ) ) {
		 
					$new_user_id = wp_insert_user( array(
							'user_login'		=> $user_login,
							'user_pass'	 		=> $user_pass,
							'user_email'		=> $user_email,
							/* 'first_name'		=> $user_first,
							'last_name'			=> $user_last, */
							'user_registered'	=> date( 'Y-m-d H:i:s' ),
							'role'				=> 'subscriber'
						)
					);
					if( $new_user_id ) {
						// send an email to the admin alerting them of the registration
						wp_new_user_notification($new_user_id);
		 
						// log the new user in
						/* wp_setcookie($user_login, $user_pass, true);
						wp_set_current_user($new_user_id, $user_login);	
						do_action('wp_login', $user_login); */
		 
						// send the newly created user to the home page after logging them in
						wp_redirect( home_url( 'sign-in' ) ); exit;
					}
		 
				}
		 
			}
		}	
		
	}
}
?>