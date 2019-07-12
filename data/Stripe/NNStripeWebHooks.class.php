<?php

/*

 - Auxilary Class Psuedo Code for NBCS Network Plugin
Last Updated 4 Oct 2018
-------------

  Description: 


---
*/

namespace data\Stripe;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNStripeWebHooks' ) ){
	class NNStripeWebHooks{

	// PROPERTIES
		

			
			
	// Methods
	
		
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
			
			$post = $_REQUEST;
			
			$timestamp = time();
			// Gather post data.
			$post_arr = array(
				'post_title'    => 'My post'.$timestamp,
				'post_content'  => 'This post was create at '.$timestamp .' Incoming Data is:'. $post,
				'post_status'   => 'draft',
				'post_type' =>'nn_receipt',
				'post_author'   => 1
			);
						
			wp_create_post( $post_arr );
		}
				
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}
				
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}
		
		
		
		
		
	}
}

?>