<?php
/*
* 	
*   Created 2018
*/

namespace misc;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'NNDoPayment' ) ){
	class NNDoPayment{

	
		public $credentials = array( 
			'test' => 'sk_test_o4TdZr2hwSlbbbgzC5SMAdUS',
			'live' => ''
		),
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
			require_once( NBCS_NET_PATH . 'lib/vendor/autoload.php' );	
			
			$key = ( defined( 'NBCS_NET_DEV' ) && NBCS_NET_DEV == true )? $this->credentials[ 'test' ] : $this->credentials[ 'live' ] ;
			
			\Stripe\Stripe::setApiKey( $key );
			
		}	
		

	/*
	*	name: set_data
	*	description: making post data accessible to the payment processors
	*
	*/
		public function set_data( $post ){
			
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
			if( isset( $post[ 'stripePlan' ] ) && !empty( $post[ 'stripePlan' ] ) )
				$this->plan = $post[ 'stripePlan' ];
			
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
			
			return $charge;
			
		}	
		

	/*
		Name: subscription
		Description: Recurring subscriptions
	*/	
	
		public function subscription(){
			
			$subscription = NULL; 
			
			$customer_id = $this->get_customer_id();
			
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
			
			
			//Step 1: Create customer from Token.
			
			$customer = $this->get_customer();
			
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
				
				print_pre( $charge );
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