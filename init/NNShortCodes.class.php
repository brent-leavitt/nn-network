<?php 

/* 

Short Codes - Class Psuedo Code for NBCS Network Plugin
Last Updated 5 Oct 2018
-------------

  Description: 
	
---

	To do's: 
		- what sub-account pages are standard across the network? 
		- Where to put the get_payment_form and get_charge_button functions?
			- Can we call needed classes directly from within the shortcode CB function?
	
	
	
---

Brainstorming: 
		There will be a list of shortcodes that are being loaded here: 
		
		- nb-payment	//
		- nn-g, or maybe gkey	// Guide Template Variables, will each include a key value such as "first_name". 
				-- Functionality for nb-m shortcodes is optional at this point. Actually, I feel like this is a CRM only shortcode. 
		- nb-register 	//Registration Form
		- nb-login 		//login form
		- nb-account	//Account Pages. 
		//Will there be a set of sub account pages. 
			--Figure out what sub pages will be standard across the network.
			

Shortcode functions are only for short codes. All other functionality needs to be handled elsewhere. 

		
Shortcodes and Templates: 
---------------------------
There are additional shortcode pages that will be added to Certs LMS that are not needed across the whole network. 
If I set up an action hook here to insert additional shortcodes and functions for those extra pages, then they can be added to the Certs LMS without having to be network wide. 

Is there an action hook that I could setup here to 
		

*/

namespace init;

use pymt\Stripe\NNStripeDoPayment as DoPayment;
use pymt\Stripe\NNStripePayForm as PayForm;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNShortCodes' ) ){
	class NNShortCodes{
		
		//properties
		
		public $shortcodes =[ 'payment', 'register', 'login', 'account', 'm' ];
		

		
		
		
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
		Description: Setting up available shortcode handlers 
	*/	
	
		public function init(){		
		
			//loop through shortcode array and create callback foreach: 
			foreach( $this->shortcodes as $sc )
				add_shortcode( 'nn_'.$sc , array( $this, 'load_'.$sc.'_cb' ) ); 
		
		}
		

	/*
		Name: load_payment_cb
		Description: 
	*/			
		public function load_payment_cb( $atts ){
			
			$atts_arr = shortcode_atts( array(
					'service_id' => '',	//Three Uppercase letter code that represents a company service (ie. BDC = 'birth doula certification')`
					'enrollment' => '', //see enrollment token types for full list of available types
				), $atts );
			
			//Run User Checks here to determine what type of payment action is needed. 	
			if( is_user_logged_in() ) {
				
				$patron_id = get_current_user_id();
				
				if( !empty( $patron_id ) )
					$stripe_cus_id = get_user_meta( $patron_id, 'stripe_cus_id', true );
				
				if( !empty( $stripe_cus_id ) ){//If Yes, get stripe info about patron.
				
					$payment = new DoPayment( array() ); //Should be sending post data... 
					$customer = $payment->get_customer( $stripe_cus_id );
					
					if( is_object( $customer )  && !empty( $customer ) ){
							//Reference --- https://stackoverflow.com/questions/31045606/retrieve-customers-default-and-active-card-from-stripe
						
						$src = $customer->default_source;
						
						if( !empty( $src ) /* && ( $payment->src_chargeable( $src ) )  */)
							return $this->get_charge_button( $stripe_cus_id, $atts_arr );
					}
				} 
			}
			
			return $this->get_payment_form( $atts_arr );	
		}
		

	/*
		Name: 
		Description: 
	*/			
		public function load_register_cb( $atts ){
			
			
		}
		

	/*
		Name: 
		Description: 
	*/			
		public function load_login_cb( $atts ){
		
		// Lifted from PIPPIN
		
			if(!is_user_logged_in()) {
	 
				global $pippin_load_css;
		 
				// set this to true so the CSS is loaded
				$pippin_load_css = true;
		 
				$output = pippin_login_form_fields();
				
			} else {
			
			
				// could show some logged in user info here
				// $output = 'user info here';
				// Or do a redirect. 
				
			}
			return $output;
			
		
		}


	/*
		Name: 
		Description: 
	*/	
	
		public function get_payment_form( $atts ){
			
			$pay_form = new PayForm( $atts );
			$form = $pay_form->get_pay_form();
			
			return $form;		
			
		}
		

	/*
		Name: 
		Description: 
	*/		
		public function get_charge_button( $pid, $atts ){			
							
			$pay_form = new PayForm( $atts );
			
			$form = $pay_form->get_pay_form( $pid );
			
			return $form;			
			
		}
		


		
		
		//ETC...
		
		
	}
}

?>