<?php 

/*
nn_network\init\RegisterLite

Registration Lite //Functionality Class

---

Description: 
	- Why a lite version? Because We're only registering an email address or name and email address as most. 
	
	- This really depends upon the weight of the main register class. 
		- If it can be modularized then maybe it is no heavier to use the main class?
		
---
*/

namespace nn_network\init;


// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'RegisterLite' ) ){
	class RegisterLite{

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
			if( isset( $_POST[ "nn_registerlite_username" ] ) && wp_verify_nonce( $_POST[ '_nn_registerlite_nonce' ], 'nn-registerlite-nonce' ) ){
				
				
				$user_login		= $_POST[ "nn_registerlite_username" ];	
				$user_email		= $_POST[ "nn_registerlite_email" ];
				/* $user_first 	= $_POST[ "nn_registerlite_userfirst" ];
				$user_last	 	= $_POST[ "nn_registerlite_userlast" ]; */
				$user_pass		= $_POST[ "nn_registerlite_password" ];
				$pass_confirm 	= $_POST[ "nn_registerlite_confirm" ];
		 
				$action = ( !empty( $_POST[ 'action' ] ) )?  $_POST[ 'action' ] : '';
				unset( $_POST[ 'action' ] );
				unset( $_POST[ "nn_registerlite_password" ] );
				unset( $_POST[ "nn_registerlite_confirm" ] );
				//unset( $_POST[ "nn_registerlite_email" ] );
				//unset( $_POST[ "nn_registerlite_username" ] );
				unset( $_POST[ "_nn_registerlite_nonce" ] );
				unset( $_POST[ "_nn_nonce" ] );
				$action = add_query_arg( $_POST, $action );
		 
		 
				// this is required for username checks
				//require_once(ABSPATH . WPINC . '/registration.php');
		 
				if( $user_login == '') {
					// empty username
					nn_errors()->add( 'nn_registerlite_username', __( 'Please enter a username' ) );
				}
				if( username_exists( $user_login ) ) {
					// Username already registered
					nn_errors()->add('nn_registerlite_username', __( 'Username already taken' ));
				}
				if( !validate_username( $user_login ) ) {
					// invalid username
					nn_errors()->add( 'nn_registerlite_username', __( 'Invalid username' ) );
				}
				if( !is_email( $user_email ) ) {
					//invalid email
					nn_errors()->add( 'nn_registerlite_email', __( 'Invalid email' ) );
				}
				if( email_exists( $user_email ) ) {
					//Email address already registered
					nn_errors()->add( 'nn_registerlite_email', __( 'Email already registered' ) );
				}
				if( $user_pass == '') {
					// passwords do not match
					nn_errors()->add( 'nn_registerlite_password', __( 'Please enter a password' ) );
				}
				if( $user_pass != $pass_confirm ) {
					// passwords do not match
					nn_errors()->add( 'nn_registerlite_confirm', __( 'Passwords do not match' ) );
				}
		 
				$errors = nn_errors()->get_error_messages();
				
				// only create the user if there are no errors
				if( empty( $errors ) ) {
		 
					$new_user_id = wp_insert_user( array(
							'user_login'		=> $user_login,
							'user_pass'	 		=> $user_pass,
							'user_email'		=> $user_email,
							/* 'first_name'		=> $user_first,
							'last_name'			=> $user_last, */
							'user_registered'	=> date( 'Y-m-d H:i:s' ),
							'role'				=> 'nb_subscriber'
						)
					);
					
					if( $new_user_id ) {
						
						// send an email to the admin alerting them of the registration
						wp_new_user_notification( $new_user_id );
		 
						//print_pre( $_POST ); 
						wp_set_current_user( $new_user_id, $user_login );	
						
						wp_set_auth_cookie( $new_user_id ); //optional params: $remember, $secure
						//Replaced by above. wp_setcookie($_POST['nn_patron_login'], $_POST['nn_password'], true);
						
						do_action('wp_login', $user_login );
		 
						// send the newly created user to the home page after logging them in
						wp_safe_redirect( $action ); exit;
					}
		 
				}
		 
			}
		}	
		
	}
}
?>