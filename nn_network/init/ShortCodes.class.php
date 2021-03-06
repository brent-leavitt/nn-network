<?php 

/* 


Short Codes - Class for NN Network Plugin
Last Updated on 15 Jul 2019
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
		
		
		
		
	nn_cashier // atts = skip, which allows for skipping registration or login before making payment, not fully developed yet. 

*/

namespace nn_network\init;

use nn_network\proc\Cashier as Cashier;
use nn_network\tmpl\UserForms as UserForms; 

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'ShortCodes' ) ){
	class ShortCodes{
		
		//properties
		
		public $shortcodes =[ 'payment', 'register', 'login', 'account', 'm', 'cashier', 'payment_login', 'receipt' ];
		

		
		
		
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
		Name: load_register_cb
		Description: 
	*/			
		public function load_register_cb( $atts ){
			return 'We are calling the REGiSTER CB function!!';
			
		}
					

	/*
		Name: account
		Description: 
	*/			
		public function load_account_cb( $atts ){
			
			$atts_arr = shortcode_atts( array(
					'show' => '',	
				), $atts );

			//print_pre( $atts );
			
			return 'We are calling the ACCOUNT CB function!!';
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
			
				//$patron = $post[ 'patron' ] ?? "0"; 
			
				if( !is_user_logged_in() /* || $patron === "0" */ ) //If patron is set to 0. 
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
		 
				//OLD $login = new Login();
				$uforms = new UserForms();
				
				
				//OLD $output = $login->login_form_fields();
				
				$output = $uforms->form( 'login' );
				
				
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
		Name: load_receipt_cb
		Description: 
	*/			
		public function load_receipt_cb( $atts ){
		
			if(!is_user_logged_in()) {
	 
				$post = array(
					'tx_id' => $_REQUEST[ 'tx_id' ],
					'action' => 'receipt'
				);
				
				$uforms = new UserForms();
				
				$output = $uforms->form( 'login', $post );
				
/* 				$output = '<p>Testing this field!</p>'; */
				
			} else {
				
				$tx_id = $_REQUEST[ 'tx_id' ];
				
				$output = "<p>The transaction ID is: $tx_id</p>";
				
				
				// could show some logged in user info here
				// $output = 'user info here';
				// Or do a redirect. 
				
				$output .= '<p>This will programatically pull the receipt by the provided transaction ID! There some work to be done here, because all receipt should be stored elsewhere on the network. But loading a receipt for review should be a network-wide function. Perhaps a function called nn_get_receipt( $receipt_id ) could universally load a receipt by its ID? </p>';
				
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
			
			//The code below was giving this errror message: 
			//Notice: Uninitialized string offset: 0 in /home/87259.cloudwaysapps.com/kquamczdrq/public_html/wp-content/plugins/nn-network/nn_network/init/ShortCodes.class.php on line 330
			
			/* if( $atts[ 0 ] == 'skip' ){
				
				$output .= "<h2>Guest Registration?</h2> 
				<p>Are you registering on behalf of someone else? Not ready to make an account?</p> ";
				
				$output .= $uforms->skip( $post );
				
				$output .= "<p>All account details will be sent to the email used at time of payment.</p>";
			} */
			
			
			return $output;
		}

		
		
		//ETC...
		
		
	}
}

?>