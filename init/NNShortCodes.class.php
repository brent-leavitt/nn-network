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

use proc\NNCashier as Cashier;
use tmpl\NNUserForms as UserForms; 

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNShortCodes' ) ){
	class NNShortCodes{
		
		//properties
		
		public $shortcodes =[ 'payment', 'register', 'login', 'account', 'm', 'cashier', 'payment_login' ];
		

		
		
		
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
			
			
			
			return $this->get_payment_form( $atts_arr );	
		}
		

	/*
		Name: 
		Description: 
	*/			
		public function load_register_cb( $atts ){
			
			
		}
			

	/*
		Name: load_cashier_cb
		Description: 
	*/			
		public function load_cashier_cb( $atts ){
			
			if( is_admin() == false ){ //Disable on admin screen. 
				
				
				//print_pre( $_SERVER );				
				
				$post = $_REQUEST;
				print_pre( $post );
			
				if( /* empty( $post[ 'patron' ] ) ||  */$post[ 'patron' ] === "0" ) //If patron is set to 0. 
					return $this->get_user_forms( $post , $atts );
				
				//Get Cashier Class
				$cashier = new Cashier();
				
				$display = $cashier->display();
				
				return $display;
			}
		}
		

	/*
		Name: 
		Description: 
	*/			
		public function load_login_cb( $atts ){
		
		// Lifted from PIPPIN
		
			if(!is_user_logged_in()) {
	 
				//global $nn_load_css;
		 
				// set this to true so the CSS is loaded
				//$nn_load_css = true;
		 
				$login = new Login();
				
				$output = $login->login_form_fields();
				
/* 				$output = '<p>Testing this field!</p>'; */
				
			} else {
			
			
				// could show some logged in user info here
				// $output = 'user info here';
				// Or do a redirect. 
				
				$output = '<p>You are already logged in. Move on!</p>';
				
			}
			return $output;
			
		
		}		

	/*
		Name: 
		Description: 
	*/			
		public function load_payment_login_cb( $atts ){
		
		// Lifted from PIPPIN
		
			if(!is_user_logged_in()) {
	 
				//global $nn_load_css;
		 
				// set this to true so the CSS is loaded
				//$nn_load_css = true;
				
				$output = $this->get_user_forms();
				
/* 				$output = '<p>Testing this field!</p>'; */
				
			} else {
			
			
				// could show some logged in user info here
				// $output = 'user info here';
				// Or do a redirect. 
				
				$output = '<p>You are already logged in. Move on!</p>';
				
			}
			return $output;
			
		
		}


	/*
		Name: get_payment_form
		Description: 
	*/	
	
		public function get_payment_form( $atts ){
			
			$enrollment = $atts[ 'enrollment' ];
			$service = $atts[ 'service_id' ];
			$action = 'nn_payment_'.$enrollment.'_'.$service;
			//ep( $action );	
			
			$nonce = wp_nonce_field( $action, '_nn_nonce', false, false );
			
			$patron = wp_get_current_user();
			$patron_id = ( !empty( $patron ) )? $patron->ID : 0 ; 
			
			//$url_ref = $_SERVER['REQUEST_URI']; may be irrelevant
			
			$btn = "
			<form method='post' action='/cashier/'>
				<input type='hidden' name='action' value='/cashier/' />	
				<input type='hidden' name='enrollment' value='$enrollment' />	
				<input type='hidden' name='patron' value='$patron_id' />	
				<input type='hidden' name='service' value='$service' />	
				$nonce		
				<input type='submit' value='Start $service' />
			</form>";
			
			return $btn;
			
			
			/* 
			$pay_form = new PayForm( $atts );
			$form = $pay_form->get_pay_form();
			
			return $form;	 */	
			
		}
		


		public function get_user_forms( $post = '', $atts = '' ){
			
			$uforms = new UserForms();
			
			//GET REGISTRATION FORM
			$output = $uforms->form( 'registerlite', $post );
			
			// GET LOGIN FORM
			$output .= $uforms->form( 'login', $post );
			
			//ALLOW FOR SKIP REGISTRATION OPTION, JUMP STRAIGHT TO CHECKOUT FOR GUEST REGISTRATION
			//if Skip Attr is set
			if( $atts[ 0 ] == 'skip' ){
				
				$output .= "<h2>Guest Registration?</h2> 
				<p>Are you registering on behalf of someone else? Not ready to make an account?</p> ";
				
				$output .= $uforms->skip( $post );
				
				$output .= "<p>All account details will be sent to the email used at time of payment.</p>";
			}
			
			
			return $output;
		}

		
		
		//ETC...
		
		
	}
}

?>