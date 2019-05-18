<?php 


//Error Class

/*
---

Description: Error handling class for... 
	- login, 
	- registration, 
	what else? 


---
Brainstorming: 
	-Not sure if this is the right place for this or not. 

	
	https://www.sitepoint.com/an-introduction-to-the-wp-error-class/
	
*/


namespace proc;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNError' ) ){
	class NNError{

	
			
		
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
			
	
			
		}
		
		
		public function errors(){
			static $wp_error; // Will hold global variable safely
			return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	
		}
		
		
		
	}
}