<?php
/*
nn_network\data\Stripe\PayForm

PayForm - Data Class for NN_Network Class
Last updated on 12 Jul 2019

---
Description: 

*   To Do: Make it so that we can add custom payment amounts for 
		- One time payments not related to trainings. 
		- Account Reactivations
		- Account Payoffs
		
		
*/

namespace nn_network\data\Stripe;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
if( !class_exists( 'PayForm' ) ){
	class PayForm{
		
		//Properties
		
		public $args = array(
			'service_id' => '',
			'enrollment' => '',
			'success_url' => 'thank-you',
			'fail_url' => 'registration',
			'email' => '',
			'payment_type' => '', 
			'price' => 0,
			'button_label' => '',
			'description' => '',
			'interval' => 0,
			'duration' => '',
		);
		
		
		
		//Method

	/*
		Name: 
		Description: 
	*/			
		
		public function __construct( $atts ){
			
			//Setup Arguments Specific to the requested form. 
			$this->set_args( $atts );
			
			//Add user info
			if ( is_user_logged_in() ) {
				
				//Does user have Stripe Customer ID? 
				
					//If Yes, get stripe info about patron. 
				
				//Is Card on file active? 
				
				//If yes, we don't need to run through the checkout script. 
				
				
				//if No, then proceed. 
				$current_user = wp_get_current_user();
				$this->add_user_info( $current_user );
				
			} 
			
		}
			

	/*
		Name: 
		Description: 
	*/				
		
		public function add_user_info( $user ){
			
			$args = $this->args;
			
			//Add User Email
			if( isset( $user->user_email ) )
				$args[ 'patron_email' ] = $user->user_email;
			
			//Add Full Display Name 
			if( isset( $user->user_nicename ) )
				$args[ 'patron_name' ] = $user->user_nicename;
			
			//Add Address First Line
			if( isset( $user->patron_address1 ) )
				$args[ 'patron_address1' ] = $user->patron_address1;
			
			//Add City
			if( isset( $user->patron_city ) )
				$args[ 'patron_city' ] = $user->patron_city;
			
			//Add 
			if( isset( $user->patron_state ) )
				$args[ 'patron_state' ] = $user->patron_state;
			
			//Add 
			if( isset( $user->patron_country ) )
				$args[ 'patron_country' ] = $user->patron_country;
			
			//Add 
			if( isset( $user->patron_zip ) )
				$args[ 'patron_zip' ] = $user->patron_zip;
			
			//Add 
			/* if( isset( $user-> ) )
				$args[ 'patron_' ] = $user->; */
			
			$this->args = $args;
			
		}

				

	/*
		Name: set_args
		Description: Args for a specific payment type are stored in the database's options table. 
	*/				
		public function set_args( $atts ){
			
			//Replace with an options table load to figure out what data to load from database. 
			//We need a settings page that loads values from the Options Table and allows these to be modified in the backend. 
			
			/* $sets = array(
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
			 */
			
		/* 	//This is important in determining the service we are billing for. 
			if( isset( $atts[ 'service_id' ] ) && !empty( $atts[ 'service_id' ] ) )
				$this->args[ 'service_id' ] = $service_id = $atts[ 'service_id' ];
			
			//This help us determine the type of payment being processed. 
			if( isset( $atts[ 'enrollment' ] ) && !empty( $atts[ 'enrollment' ] ) )
				$this->args[ 'enrollment' ] = $enrollment = $atts[ 'enrollment' ];		 */	
			
			//Query for default button sets. 
		//*** ADD functionality to insert other sets of data to allow for custom generated forms. 
			//$att_set = $sets[ $enrollment ];
			
			foreach( $this->args as $key => $val ){
				if( ( isset( $atts[ $key ] ) ) && ( !empty( $atts[ $key ] ) ) ){
		
					$this->args[ $key ] = $atts[ $key ];
					
				}				
			}
		}
		
		
				

	/*
		Name: 
		Description: 
	*/		
		public function get_args(){
			
			return $this->args;
			
		}
	

	/*
		Name: 
		Description: 
	*/			
		public function get_pay_form( $id = '' ){
			
			$site_url = site_url();
			
			//Get Arguments
			$args = $this->get_args();
			
			//Prep data-amount
			$price = intval( $args[ 'price' ] );
			$args[ 'price' ] = $price * 100; 
			
			
			//If an ID is being passed to this funciton, that means that the Source for the transaction is already on file with us. No need to recreate. 
			$source_on_file = ( !empty( $id ) )? true: false ;
			
			$form = '';	
			
			/*$form .= var_export( $args );
			
			array ( 'service_id' => 'BDC', 'enrollment' => 'certificate_full', 'success_url' => 'thank-you', 'fail_url' => 'payment-options', 'email' => '', 'payment_type' => 'payment', 'price' => 400, 'button_label' => 'Pay in Full Now', 'description' => 'Doula Certification - Paid in Full', 'interval' => 1, 'duration' => '2y', ) 
			*/
			$form .= "
				<form action='?collect=payment' method='POST'>
				  
				  <input type='hidden' name='service_id' value='". $args['service_id'] ."' />
				  <input type='hidden' name='enrollment_type' value='". $args['enrollment'] ."' />
				  <input type='hidden' name='price' value='". $args['price'] ."' />
				  <input type='hidden' name='return_success' value='". $args['success_url'] ."' />
				  <input type='hidden' name='return_fail' value='". $args['fail_url'] ."' />
				  <input type='hidden' name='interval' value='". $args['interval'] ."' />
				  <input type='hidden' name='duration' value='". $args['duration'] ."' />";
			
			if( $source_on_file === true ){
				
				$form .= "<input type='hidden' name='stripeCustomer' value='{$id}' />";
				
			}
					
			$form .= wp_nonce_field( 'nn_pay_'.$args[ 'enrollment' ] , '_nn_payment_nonce', true, false );
				  
			if( $args[ 'enrollment' ] == 'library_preview'  || $source_on_file == true ){
				//If either of the above conditions are true, only generate a button. 
				
				$form .= "
					<button type='submit' class='stripe-button-el' style='visibility: visible;'><span style='display: block; min-height: 30px;'>". $args[ 'button_label' ] ."</span></button>
				";
				
			} else {
				//Otherwise, we need to create a Stripe source object. 
				
				$form .= " <script
					src='https://checkout.stripe.com/checkout.js' class='stripe-button'
					data-label='". $args[ 'button_label' ] ."'
					data-key='pk_test_zQj3DSdqQYRMqw9Tu60h6wmK'
					data-amount='". $args[ 'price' ] ."'
					data-name='NB Childbirth Srvcs'
					data-description='". $args[ 'description' ] ."'
					data-image='https://www.trainingdoulas.com/wp-content/uploads/2017/11/HEART-logo.png' //set logo image in options table. 
					data-locale='auto'
					data-zip-code='true'
					";
					
				if( !isset( $args[ 'patron_address1' ] ) || empty( $args[ 'patron_address1' ] )  )	
						$form .= "data-billing-address='true'
					";
					
				if( isset( $args[ 'patron_email' ] ) && !empty( $args[ 'patron_email' ] ) )		
						$form .= "data-email='". $args[ 'patron_email' ]."' ";
						
				$form .= "
						>
					  </script>";
				
			}//END if else statement. 
			
				 
				$form .= "</form>";
				
				
			return $form;
			
			
		}
		
		
	}
}

?>