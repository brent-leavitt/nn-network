<?php 

/* 
Cashier Class for NN Network Plugin
Last Updated 11 Feb 2019
-------------

  Description: This builds the cashier Page with the appropriate information for checkout. Note that cashier page handles only on service transaction at a time. Not designed to pay for more than one service. 
	
---
*/

namespace proc;

//use data\Stripe\NNStripeDoPayment as DoPayment;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNCashier' ) ){
	class NNCashier{
		
		//properties
		
		public $vars = '';
		
		/*
		 Incoming Vars: 
			- Enrollment Token, 
			- User ID
			- Service ID
			
		 Needed Vars: 
			- 'payment_type' => 'payment',
			- 'price' => 0,
			- 'button_label' => 'Start 10-Day Preview',
			- 'description' => 'Library Preview Subscription',
			- 'interval' => 1,
			- 'duration' => '10d',
		
		
		 Additional Vars: 
			- setup = true or false. //if setup is complete, then true. 
		*/
		
		
		
		//Methods
	/*
		Name: __construct
		Description: 
	*/	
	
		public function __construct( $in ){			
				
			$this->init( $in );
			
		}
		
	/*
		Name: init
		Description: Setting up available shortcode handlers 
	*/	
	
		public function init( $in ){		
			
			$this->set_args( $in );
			$this->get_vars();
			
			
			
		}
		
		
	/*
		Name: set_args
		Description: Takes incoming Arguments an assigns them to the required vars. 
	*/	
		
		public function set_args( $in ){
			
			
		}
		
	/*
		Name: 
		Description: 
	*/	
		
		public function get_vars(){
			
			
		}
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}	