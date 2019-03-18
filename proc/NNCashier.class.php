<?php 

/* 
Cashier Class for NN Network Plugin
Last Updated 11 Feb 2019
-------------

  Description: This builds the cashier Page with the appropriate information for checkout. Note that cashier page handles only on service transaction at a time. Not designed to pay for more than one service. 
	
---
*/

namespace proc;

//Need to separate Thrid Party directly from Cashier page. 
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
			'description' => '',
			'interval' => '',
			'duration' => '',
		
		);
		
		public $in = array(
			'enrollment' => '',
			'patron' => '',
			'service' => '',
		);
		
		public $login_required = false;
		
		public $patron = null;
		
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
			
			//1: Set Args
			$this->set_args();
			
		
			//2: determine if user is logged in or not. 
			$this->get_patron_logged_in(); //Returns ID or 0.
		
		
			//3: details of transaction to be charged for have been collected and staged to payment processors. 
			$this->get_vars();
			
			if( !empty( $this->vars ) ){
				
				
			}

			

			/* NOT USED because $this->display() is called from the Shortcodes class which bypasses this. 
			
			//4: Information is prepared for display on the screen. 

				$this->prepare();

			
			}else{
				
				$this->error[ "no_payment" ] = "No payment to be collected at this time.";	
				
			} */
			
			//$this->set_args();
			//$this->get_vars();
			
			//print_pre( $this );
			
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
			
			//Check if login is required. 
			//This code NOT TESTED YET. Maybe we don't need to be securing this? Wrong? 
			
			if( ( $args[ 'login_required' ] === 1 ) && ( get_current_user_id() === 0 ) ){
				
				$str = "?"; 
				
				foreach( $args as $key => $val ){
					$str .= "$key=$val&";
				}
				
				
				$params = urlencode( $str );
				
				ep( "Not logged in." );
				ep( $params );
				
				//Redirect to login page with needed parameters.
				//wp_redirect( wp_login_url( get_permalink() . $params ) ) );
				
			}
			
			
			return true;
		}
		
	/*
		Name: get_vars()
		Description: This needs to be loaded from the Options Table. If not available. We send a link with instructions on how to do this. The default set should be loaded on installation of the plugin. 
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
			
			//This is important in determining the service we are billing for. 
			if( isset( $this->in[ 'service' ] ) && !empty( $this->in[ 'service' ] ) )
				$this->vars[ 'service_id' ] = $service_id = $this->in[ 'service' ];
			
			$this->vars[ 'enrollment' ] = $enrollment;
			
		}
		
	/*
		Name: 
		Description: 
	*/	
		
		public function load_stripe(){
			
			$stripe_cus_id = null ;
			
			//Run User Checks here to determine what type of payment action is needed. 	
			if( !empty( $this->in[ 'patron' ] ) ){
				
				$patron_id = $this->in[ 'patron' ];
				
			} elseif( is_user_logged_in() ) {
				
				$patron_id = get_current_user_id();
				
			}
			
			if( !empty( $patron_id ) )
				$stripe_cus_id = get_user_meta( $patron_id, 'stripe_cus_id', true );
				
			if( !empty( $stripe_cus_id ) ){//If Yes, get stripe info about patron.
			
				$payment = new DoPayment( array() ); //Should be sending post data... 
				$customer = $payment->get_customer( $stripe_cus_id );
				
				if( is_object( $customer )  && !empty( $customer ) ){
						//Reference --- https://stackoverflow.com/questions/31045606/retrieve-customers-default-and-active-card-from-stripe
					
					$src = $customer->default_source;
					
					//if( !empty( $src ) /* && ( $payment->src_chargeable( $src ) )  */)
				}			
			} 
			
			return $this->get_charge_button( $stripe_cus_id, $this->vars );
			
			
		}
		
	/*
		Name: display
		Description: 
	*/	
		
		public function display(){
			
			//$show = $this->load_stripe();
			
			//$show = print_r( $_POST, true );
			$show = '';
			//if no errors, display output. 
			if( !empty( $this->errors ) ){
				
				foreach( $this->errors as $err_msg )
					$show .= "<p class='error'>$err_msg</p>";				
			
			} else {
				
			//if no errors, let's show the cashier page. 
			
				$show .= $this->prepare();
				
			}
			
			return $show;
		}		
		
		
	/*
		Name: get_charge_button
		Description: 
	*/		
		public function get_charge_button( $pid, $atts ){			
							
			//dump( __LINE__, __METHOD__, $atts );
							
			$pay_form = new PayForm( $atts );
			
			$form = $pay_form->get_pay_form( $pid );
			
			$cc_logos = "<img src='".home_url()."/uploads/01/01/cc_logos.png' alt='Pay with credit card.'>";
			
			return $form . $cc_logos;			
			
		}
				
		
		
	/*
		Name: prepare
		Description: 
	*/	
		
		public function prepare(){
			
			 $prep = [
				'top' 		=> 'patron_info',
				'left' 		=> 'trans_summary',
				'right' 	=> 'checkout_buttons',
				'bottom' 	=> 'lower_info',
			 ];
			 
			foreach( $prep as $pkey => $pval ){
				$$pkey = $this->$pval();
			}
			
			$output = "
				<section id='cashier_top'>
					$top
				</section>
				
				<section id='cashier_left'>
					$left
				</section>
				
				<section id='cashier_right'>
					$right
				</section>
				
				<section id='cashier_bottom'>
					$bottom
				</section>
			";
			
			return $output;
			
		}
	
	/*
		Name: patron_info
		Description: Display current information about logged-in patron or a prompt to login. 
	*/	
		
		public function patron_info(){
			
			if( empty( $this->patron->ID ) ){
				//Not logged in
				$output = "You are not logged in. Please consider logging in to expedite the checkout process. ";
			}else{
				//Logged in. 
				$output = "You are logged in as ";
				$output .= "<strong>".$this->patron->display_name."</strong>. ";
				$output .= "If this is not you, please log out before proceeding. Thank you!";
				
			}
			
			//print_pre( $this->patron );
			
			
			return $output;
		}
	
	/*
		Name: transaction_summary
		Description: 
	*/	
		
		public function trans_summary(){
			
			$output = "<hr>
			<h3>Transaction Summary</h3>
			<pre>
What: {$this->vars[ 'description' ]}
How Much: $ {$this->vars[ 'price' ]}.00 USD
How Long: 1 Month
</pre>
			<hr>
			";
			
			
			
			return $output;
		}	
		
		
	/*
		Name: checkout_buttons
		Description: //3: Available Payment processors are staged. 
	*/	
		
		public function checkout_buttons(){
			
			$output = $this->get_payment_processors();

			
			
			
			return $output;
		}	
		
		
	/*
		Name: 
		Description: 
	*/	
		
		public function lower_info(){
			
			$output = "<hr>
			<small>Please note: All credit card information is securely stored via third party services. Credit card payments made on this site are store directly with STRIPE payments. PayPal transactions are stored with PayPal.  Please review our policies pages below for further details regarding storing of personal data, refunds, etc.</small>
			";
			
			
			
			return $output;
		}
	
	
	/*
		Name: get_patron_logged_in
		Description: 
	*/	
		
		public function get_patron_logged_in(){
			$this->patron = wp_get_current_user();
			return $this->patron->ID;
			
		}

		
	/*
		Name: get_payment_processors
		Description: This determines that available payment processors (Stripe, PayPal, etc.)
	*/	
		
		public function get_payment_processors(){
			//Where's PayPal?
			
			return $this->load_stripe();
			
		}
	
	
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}	