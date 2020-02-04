<?php 
/*
nn_network\init\Login

Login Initiating Class - NN Network Plugin
Last Updated on 15 Jul 2019

---

Description: This handles all the login action 
	
// logs a member in after submitting a form

*/

namespace nn_network\init;

//use proc\NNError as Error; 

// Exit if accessed directly
//if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'Login' ) ){
	class Login{

		private $login,
				$pass,
				$nonce,
				$action;
				
		private $user; 
		private $prefix = 'nn_login_';
		
		
		
		//Methods
		/*
			Name: __construct
			Description: 
		*/	
		
		
		public function __construct(){			
				
			$this->login = $_POST( "nn_login_username" );
			$this->pass = $_POST( "nn_login_password" );
			$this->nonce = $_POST( "_nn_login_nonce" );
			$this->action = ( !empty( $_POST[ 'action' ] ) )?  $_POST[ 'action' ] : '/';
		}
		
		
		
		/**
		* Set user from wordpress. 
		* 
		* return: void 
		*/	
			
		private function set_user(){
			
			$this->user = get_user_by( 'login', $this->login );
			
		}
				
		
		/**
		* Remove properties from POST global.  
		* 
		* return: void 
		*/	
			
		private function unset_props( $props ){
			
			foreach( $props as $prop )
				unset( $_POST[ $prop ] );				
					
		}				
		
		/**
		*  If User name doesn't exist. 
		* 
		* return: boolean
		*/	
			
		private function is_user_empty(  ){
			
			return( empty( $this->user ) )? true : false;
	
		}
		
						
		
		/**
		*  Check if a user object is set.
		* 
		* return: boolean
		*/	
			
		private function is_password_empty(){
			
			return ( !isset( $user_pass ) || $user_pass == '' )? true : false;
				
					
		}
		
						
		
		/**
		*  If user password does not match the submitted password. 
		* 
		* return: boolean
		*/	
			
		private function does_password_match(  ){
			 
			return ( !wp_check_password( $user_pass, $user->user_pass, $user->ID ) )? true : false;
					
		}
							
		
		/**
		*  Checks to see if username and password fields have errors. 
		* 
		* return: void 
		*/	
			
		private function check_for_errors(  ){
			
			if( $this->is_user_empty() ) 				
				nn_errors()->add('nn_login_username', __('Invalid username'));
				
			elseif($this->is_password_empty() )
				nn_errors()->add('nn_login_password', __('Please enter a password'));
			
			elseif( $this->does_password_match() )
				nn_errors()->add('nn_login_password', __('Incorrect password'));	
				
		}
		
			
						
		
		/**
		*  Do final login functionality
		* 
		* return: void
		*/	
			
		private function do_login(){
			 
			// retrieve all error messages
			$errors = nn_errors()->get_error_messages();
			
			// only log the user in if there are no errors
			if( empty( $errors ) ) {
				
				//print_pre( $_POST ); 
				wp_set_current_user( $user->ID, $user->user_login );	
				
				wp_set_auth_cookie( $user->ID ); //optional params: $remember, $secure
				//Replaced by above. wp_setcookie($_POST['nn_patron_login'], $_POST['nn_password'], true);
				
				do_action('wp_login', $user->user_login);
				
				$action = add_query_arg( $_POST, $this->action );
				
				wp_safe_redirect( $action ); exit;
				
			} 		
		}
			
		
		
		
		
		/*
			Name: do_login
			Description: 
		*/			
	
		public function login() {
		 
			if( isset( $this->login ) && wp_verify_nonce( $this->nonce, 'nn-login-nonce' ) ) {
		 
				$this->set_user();
				
				$this->unset_props([ 
					"action", 
					"nn_login_password", 
					"_nn_login_nonce", 
					"_nn_nonce"
				]);
				
				$this->check_for_errors();
				
				$this->do_login();
				
			}
		}	
		
	}
}
?>