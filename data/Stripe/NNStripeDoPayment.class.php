<?php
/*
* 	
*   Created 2018
*/

namespace data\Stripe;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'NNStripeDoPayment' ) ){
	class NNStripeDoPayment{

	
		public $credentials = '', 
		$token 		= NULL, 
		$plan 		= NULL,
		$customer 	= NULL,
		$data 	= array(
			'service_id' => '',
			'enrollment_type' => '',
			'price' => 0,
			'return_success' => 'thank-you',
			'return_fail' => 'registration',
			'interval' => 0,
			'duration' => '',
			'stripeCustomer' => '',
			'stripeToken' => '',
			'stripeTokenType' => '',
			'stripeEmail' => '',
			'stripeBillingName' => '',
			'stripeBillingAddressCountry' => '',
			'stripeBillingAddressCountryCode' => '',
			'stripeBillingAddressZip' => '',
			'stripeBillingAddressLine1' => '',
			'stripeBillingAddressCity' => '',
			'stripeBillingAddressState' => ''
		); 
		
		public function __construct( $post ){
			
			$this->set_props();
			$this->set_data( $post );
			$this->init();
			
			
		}
		
	/*
	*	name: init
	*	description: establish the connection with the processing server. 
	*
	*/
		public function init(){
			
			//Establish connection. 
			require_once( NN_NET_PATH . 'lib/vendor/autoload.php' );	
			
			$key = $this->credentials;
			
			\Stripe\Stripe::setApiKey( $key );
			
			//dump( __LINE__, __METHOD__, $this );
			
		}	
		

	/*
	*	name: set_props
	*	description: Set values for properties as defined in options tables.
	*
	*/
		public function set_props(){
			
			
			
			$opts = get_option( 'nn_network_payment_creds' );
			
			
			//Make Assignments. 
			$environ = ( defined( 'NN_NET_DEV' ) && NN_NET_DEV == true )? 'sandbox' : 'prod';
			
			$key = 'stripe_secret_'. $environ .'_key';
			
			//Set Stripe Credential
			if( isset( $opts[ $key ] ) && !empty( $opts[ $key ] ) )
				$this->credentials = $opts[ $key ];
			
			
			//dump( __LINE__, __METHOD__, $opts );
		
			 
			//Set Payment Token
			/* if( isset( $post[ 'stripeToken' ] ) && !empty( $post[ 'stripeToken' ] ) )
				$this->token = $post[ 'stripeToken' ];
			 */
			
			
		}	
		

	/*
	*	name: set_data
	*	description: making post data accessible to the payment processors
	*
	*/
		public function set_data( $post ){
			
			//dump( __LINE__, __METHOD__, $post );
			
			$data = $this->data;
			
			foreach( $data as $key => $val ){
				if( isset( $post[ $key ] ) && !empty( $post[ $key ] ) )
					$data[ $key ] = $post[ $key ];	
			}
			
			$this->data = $data;
			
			//Set Payment Token
			if( isset( $post[ 'stripeToken' ] ) && !empty( $post[ 'stripeToken' ] ) )
				$this->token = $post[ 'stripeToken' ];
			
			//Set Customer ID
			if( isset( $post[ 'stripeCustomer' ] ) && !empty( $post[ 'stripeCustomer' ] ) )
				$this->customer = $post[ 'stripeCustomer' ];
			
			//Set Subscription Plan
			if( isset( $post[ 'enrollment_type' ] ) && !empty( $post[ 'enrollment_type' ] ) )
				$this->set_plan( $post[ 'enrollment_type' ] );
			
		}	
		

	/*
		Name: set_plan
		Description: 
	*/	
		public function set_plan( $enrollment ){
			
			$opts = get_option( NN_TD.'_cashier_vars' );
			
			$key = 'plan_key_'.$enrollment;
			
			$this->plan = $opts[ $key ];
			
			
		}
		
		
		
	/*
		Name: 
		Description: 
	*/	
		public function payment(){
			
			$charge = NULL;
			
			$args = [
						'amount' => $this->data[ 'price' ],
						'currency' => 'usd',
						'description' => $this->data[ 'service_id' ].' - '.$this->data[ 'enrollment_type' ],
					];
					
			dump( __LINE__, __METHOD__, $args );
			//Must have either a valid Token or a strike Customer ID. 
			if( !empty( $this->token )  ){
				
				$args[ 'source' ] = $this->token;
				
			} elseif( !empty( $this->customer ) ){
				
				$args[ 'customer' ] = $this->customer;
			
			}
			
			if( array_key_exists( 'source' , $args ) || array_key_exists( 'customer' , $args ) ){
				try{

					$charge = \Stripe\Charge::create( $args );
						
				}catch( Exception $e ){
						
					$this->handle_error( $e, 'manual' );
					
				}
			}
			
			dump( __LINE__, __METHOD__, $charge );
			
			return $charge;
			
		}	
		

	/*
		Name: subscription
		Description: Recurring subscriptions
	*/	
	
		public function subscription(){
			
			$subscription = NULL; 
			
			$customer_id = $this->get_customer_id();
			
			//dump( __LINE__, __METHOD__, $customer_id );
			
			if( ( $customer_id != NULL ) && ( $this->plan != NULL ) ){
				
				try{
					
					//Setup MetaData: installments_paid, on plans that are limited. 
					//Other Metadata: service_id,
					
					$subscription = \Stripe\Subscription::create([
						'customer' => $customer_id,
						'items' => [['plan' => $this->plan ]],
						
					]);
					
					
					
				}catch( Exception $e ){
					
					$this->handle_error( $e, 'subscription' );
				}
									
			}//End if
			
			
			return $subscription;
			
		}
			
		
	
	/*
		Name: 
		Description: This is sending out manual invoices or invoice by email that allow users a pay period by which to make payment on the invoice. 
	
	*/		

		public function manual(){
			
			
			$args = [
						'amount' => $this->data[ 'price' ],
						'currency' => 'usd',
						'description' => $this->data[ 'service_id' ].' - '.$this->data[ 'enrollment_type' ],
					];
			
			//Must have either a valid Token or a strike Customer ID. 
			if( !empty( $this->token )  ){
				
				$args[ 'source' ] = $this->token;
				
			} elseif( !empty( $this->customer ) ){
				
				$args[ 'customer' ] = $this->customer;
			
			}
			
			
			//Step 1: Create customer from Token.
			
			$customer = $this->get_customer( $args );
			
			//Step 2: Create charge from Customer_ID and Source_ID
			
			//Step 3: Create subscription with Customer_ID
			
			$manual = NULL; 
			$customer = NULL; 
			$source = NULL;
			$charge = NULL;
			
			$customer = $this->create_customer();			
				
				
			//print_pre( $customer );
			
			
			
			if( is_object( $customer ) && !empty( $customer->id ) ){
				
				$source = $customer->default_source; //Assign Source. 
			
				try{
					
					$charge = \Stripe\Charge::create([
						'amount' => $this->data[ 'price' ],
						'currency' => 'usd',
						'description' => $this->data[ 'service_id' ].' - '.$this->data[ 'enrollment_type' ],
						'source' => $source,
						'customer' => $customer->id,
						'metadata'=> array(
							'service_id' => $this->data[ 'service_id' ],
							'enrollment_type' => $this->data[ 'enrollment_type' ],
						)
					]);
						
				}catch( Exception $e ){
						
					$this->handle_error( $e, 'manual' );
					
				}

			}
			
			
			if( is_object( $charge ) && ( $charge->paid == true ) ){
				
				try{
					
					
					$manual = \Stripe\Subscription::create([
						'customer' => $customer->id,
						'items' => [['plan' => 'plan_DXvCbzdfBg5j0n']],
						'billing' => 'send_invoice',
						'days_until_due' => 30,
						'trial_period_days' => 30,
						'metadata'=> array(
							'installments_paid' => 1,
							'service_id' => $this->data[ 'service_id' ],
							'enrollment_type' => $this->data[ 'enrollment_type' ],
						)
					]);			
				
					
				}catch( Exception $e ){
					
					$this->handle_error( $e, 'manual' );
				}
									
			}//end if, if not charged, manual doesn't get changed from NULL, so NULL is returned as a failure.
			
			return $manual;
		}
			

	/*
		Name: 
		Description: 
	*/			
		public function create_customer(){
			
			$customer = NULL;
			
			try{
				
				$customer = \Stripe\Customer::create(
					array(
					  'email' => $this->data[ 'stripeEmail' ],
					  'source' => $this->token // obtained with Stripe.js
					)
				);
				
			}catch( Exception $e ){
				
				$this->handle_error( $e, 'create_customer' );
			}
			
			return $customer;
			
						
		}
				

	/*
		Name: 
		Description: 
	*/			
		public function get_customer( $stripe_id ){
			
			$customer = null;
			
			try{
				
				$customer = \Stripe\Customer::retrieve( $stripe_id ); 
				
				
			}catch( Exception $e ){
				
				$this->handle_error( $e, 'get_customer' );
			}
			
			return $customer;
		}		

	/*
		Name: 
		Description: 
	*/			
		public function get_customer_id(){
			
			$customer_id = NULL;
			
			if( $this->customer == NULL ){
				$stripeCustomer = $this->create_customer();
				$customer_id = $stripeCustomer->id;
			} else {
				$customer_id = $this->customer;				
			}
			
			return $customer_id;
		}



	/*
		Name: 
		Description: 
	*/	
		public function src_chargeable( $src_id ){
						
			$source = \Stripe\Source::retrieve( $src_id );
			
			
			return ( strcmp( $source->status, 'chargeable' ) == 0 )? true : false ;
			
		}
		

	/*
		Name: 
		Description: 
	*/				
		public function handle_error( $error, $action ){
			
			echo "The HANDLE ERROR method was called in the NBCS_NET_DO_PAYMENT class";
		}
	}
	
	
}
?>