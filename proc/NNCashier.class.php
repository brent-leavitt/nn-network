<?php 

/* 
Cashier Class for NN Network Plugin
Last Updated 11 Feb 2019
-------------

  Description: This builds the cashier Page with the appropriate information for checkout. Note that cashier page handles only on service transaction at a time. Not designed to pay for more than one service. 
	
---
*/

namespace proc;

use data\Stripe\NNStripeDoPayment as DoPayment;
use data\Stripe\NNStripePayForm as PayForm;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'NNCashier' ) ){
	class NNCashier{
		
		//properties
		
		public $vars = array(
			'payment_type' => '',
			'price' => '',
			'button_label' => '',
			'descirption' => '',
			'interval' => '',
			'duration' => '',
		
		);
		
		public $in = array(
			'enrollment' => '',
			'patron' => '',
			'service' => '',
		);
		
		
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
	
		public function __construct(){			
				
			$this->init();
			
		}
		
	/*
		Name: init
		Description: Setting up available shortcode handlers 
	*/	
	
		public function init(){		
			
			$this->set_args();
			$this->get_vars();
			
			
			
		}
		
		
	/*
		Name: set_args
		Description: Takes incoming Arguments an assigns them to the required vars. 
	*/	
		
		public function set_args(){
			
			$args = nn_sanitize_text_array( $_POST );
			
			print_pre( $args );
			$enrollment = $args[ 'enrollment' ];
			$service = $args[ 'service' ];
			$action = 'nn_payment_'.$enrollment.'_'.$service;
			//ep( $action );			
					
			//check nonce
			if ( ! wp_verify_nonce( $_POST[ '_nn_nonce' ], $action ) ) {
				// This nonce is not valid.
				die( 'Security check' ); 
			}
						
			//check that all values are set. 
			foreach( $this->in as $in_key => $in_val  ){
				$check = $args[ $in_key ];
				if( isset( $check ) && !empty( $check ) )
					$this->in[ $in_key ] = $check;
				else
					return false;
			}
			
			
			return true;
		}
		
	/*
		Name: 
		Description: 
	*/	
		
		public function get_vars(){
			  
			
			$sets = array(
				'library_preview' => array(
					'payment_type' => 'payment',
					'price' => 0,
					'button_label' => 'Start 10-Day Preview',
					'description' => 'Library Preview Subscription',
					'interval' => 1,
					'duration' => '10d',
				),		
				'library_month' => array(
					'payment_type' => 'subscription',
					'price' => 20,
					'button_label' => 'Start Monthly Plan',
					'description' => 'Library Monthly Subscription',
					'interval' => 0,
					'duration' => '1m',
				),				
				'library_year' => array(
					'payment_type' => 'subscription',
					'price' => 200,
					'button_label' => 'Start Annual Plan',
					'description' => 'Library Annual Subscription',
					'interval' => 0,
					'duration' => '1y',
				),
				'certificate_full' => array(
					'payment_type' => 'payment',
					'price' => 400,
					'button_label' => 'Pay in Full Now',
					'description' => 'Doula Certification - Pay in Full',
					'interval' => 1,
					'duration' => '2y',
					
				),
				'certificate_recurring' => array(
					'payment_type' => 'subscription',
					'price' => 50,
					'button_label' => 'Pay Automatic Monthly',
					'description' => 'Doula Certification - Automatic Payment Plan',
					'interval' => 12,
					'duration' => '1m',
				),
				'certificate_manual' => array(
					'payment_type' => 'invoice',
					'price' => 50,
					'button_label' => 'Pay Manual Monthly ',
					'description' => 'Doula Certification - Manual Payment Plan',
					'interval' => 12,
					'duration' => '1m',
				),
				'coaching_month' => array(
					'payment_type' => 'payment',
					'price' => 40,
					'button_label' => 'Start Coaching',
					'description' => 'Doula Coaching Service',
					'interval' => 1,
					'duration' => '1m',
				),
				'cert_extend_1mo' => array(
					'payment_type' => 'payment',
					'price' => 50,
					'button_label' => 'Extend One Month',
					'description' => 'Certification Extension - 1 mo. ',
					'interval' => 1,
					'duration' => '1m',
				),
				'cert_extend_6mo' => array(
					'payment_type' => 'payment',
					'price' => 25,
					'button_label' => 'Extend Six Months',
					'description' => 'Certification Extension - 6 mo.',
					'interval' => 1,
					'duration' => '6m',
				),
				'certificate_renewal' => array(
					'payment_type' => 'payment',
					'price' => 50,
					'button_label' => 'Renew Now',
					'description' => 'Certification Renewal',
					'interval' => 1,
					'duration' => '2y',
				),
			
			);
			
			
			//This is important in determining the service we are billing for. 
			if( isset( $this->in[ 'service' ] ) && !empty( $this->in[ 'service' ] ) )
				$service_id = $this->in[ 'service' ];
			
			//This help us determine the type of payment being processed. 
			if( isset( $this->in[ 'enrollment' ] ) && !empty( $this->in[ 'enrollment' ] ) )
				$enrollment = $this->in[ 'enrollment' ];			
			
			//Query for default button sets. 
		//*** ADD functionality to insert other sets of data to allow for custom generated forms. 
			$att_set = $sets[ $enrollment ];
			
			foreach( $this->vars as $key => $val ){
				if( isset( $att_set[ $key ] )  && !empty( $att_set[ $key ] ) )
					$this->vars[ $key ] = $att_set[ $key ];			
			}
		}
		
	/*
		Name: 
		Description: 
	*/	
		
		public function load_stripe(){
			
			
			//Run User Checks here to determine what type of payment action is needed. 	
			if( !empty( $this->in[ 'patron' ] ) ){
				
				$patron_id = $this->in[ 'patron' ];
				
			} elseif( is_user_logged_in() ) {
				
				$patron_id = get_current_user_id();
				
			}
			
			if( !empty( $patron_id ) )
				$stripe_cus_id = get_user_meta( $patron_id, 'stripe_cus_id', true );
				ep( "stripe CuS ID is $stripe_cus_id." );
			if( !empty( $stripe_cus_id ) ){//If Yes, get stripe info about patron.
			
				$payment = new DoPayment( array() ); //Should be sending post data... 
				$customer = $payment->get_customer( $stripe_cus_id );
				
				if( is_object( $customer )  && !empty( $customer ) ){
						//Reference --- https://stackoverflow.com/questions/31045606/retrieve-customers-default-and-active-card-from-stripe
					
					$src = $customer->default_source;
					
					if( !empty( $src ) /* && ( $payment->src_chargeable( $src ) )  */)
						return $this->get_charge_button( $stripe_cus_id, $this->vars );
				}
			} 
			
			
			
		}
		
	/*
		Name: display
		Description: 
	*/	
		
		public function display(){
			
			$show = $this->load_stripe();
			
			//$show = print_r( $_POST, true );
			
			
			
			return $show;
		}		
		
		
	/*
		Name: get_charge_button
		Description: 
	*/		
		public function get_charge_button( $pid, $atts ){			
							
			$pay_form = new PayForm( $atts );
			
			$form = $pay_form->get_pay_form( $pid );
			
			return $form;			
			
		}
				
		
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}	