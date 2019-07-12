<?php 


//Login Functionality Class
/*
---

Description: This handles all the login action 
	
// logs a member in after submitting a form

*/

namespace init;

//use proc\NNError as Error; 

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNLogin' ) ){
	class NNLogin{

	
			
		
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
			
			$this->do_login();
			
		}
		
		
		/*
			Name: do_login
			Description: 
		*/			
	
		public function do_login() {
		 
			if( isset( $_POST['nn_login_username'] ) && wp_verify_nonce( $_POST['_nn_login_nonce'], 'nn-login-nonce' ) ) {
		 
				// this returns the user ID and other info from the user name
				$user = get_user_by( 'login', $_POST[ 'nn_login_username' ]);
				
				$user_login	= $_POST[ "nn_login_username" ];	
				$user_pass	= $_POST[ "nn_login_password" ];
				
				//print_pre( $_POST );
				$action = ( !empty( $_POST[ 'action' ] ) )?  $_POST[ 'action' ] : '/';
				unset( $_POST[ "action" ] );
				unset( $_POST[ "nn_login_password" ] );
				//unset( $_POST[ "nn_login_username" ] );
				unset( $_POST[ "_nn_login_nonce" ] );
				unset( $_POST[ "_nn_nonce" ] );
				$action = add_query_arg( $_POST, $action );
				
				//ep( $action );
				
				if( empty( $user ) ) {
					// if the user name doesn't exist
					nn_errors()->add('nn_login_username', __('Invalid username'));
				}elseif( !isset( $user_pass ) || $user_pass == '' ) {
					// if no password was entered
					nn_errors()->add('nn_login_password', __('Please enter a password'));
				}elseif( !wp_check_password( $user_pass, $user->user_pass, $user->ID ) ) {
					// check the user's login with their password
					// if the password is incorrect for the specified user
					nn_errors()->add('nn_login_password', __('Incorrect password'));
				}
		 
				// retrieve all error messages
				$errors = nn_errors()->get_error_messages();
		 
		 
				//print_pre( nn_errors() );
		 
				// only log the user in if there are no errors
				if( empty( $errors ) ) {
					
					//print_pre( $_POST ); 
					wp_set_current_user( $user->ID, $user->user_login );	
					
					wp_set_auth_cookie( $user->ID ); //optional params: $remember, $secure
					//Replaced by above. wp_setcookie($_POST['nn_patron_login'], $_POST['nn_password'], true);
					
					do_action('wp_login', $user->user_login);
					
					wp_safe_redirect( $action ); exit;
					
				} 
			}
		}	
		
	}
}
?>