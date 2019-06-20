<?php 

/* 

Collect //Functionality Class

---

Description: This is the Listener Object for Payments being Processed through ***Stripe*** via the QueryVar Handler. 
	- Collect is responsible for the front end of all payment processing. 

	- Not sure if other transactions will need to pass through here or not. 

	To do, add action hooks to individual plugins to determine what type of registration should be performed. Always we will check for a full registration call first. 
	
	If a registration call is set, but hasn't be performed yet for a user, value is 0. If a user is registered, value is 1.
	
	
// Collection Page
*/

namespace data\Stripe;

use data\Stripe\NNStripeDoPayment as DoPayment;
use proc\NNDoTransaction as DoTransaction;


if( !class_exists( 'NNStripeCollect' ) ){
	class NNStripeCollect{
			
		//Properties
		public $type = '';
		
		
		
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
		Description: 
	*/		
		private function init(){
			//dump( __LINE__, __METHOD__, $_POST );
			
			//This class shoudl only fire if there is form data to be processed. 
			if( !isset( $_POST ) || empty( $_POST ) ){
				print( 'Form data is not set or is empty!' );
				exit;
			}  
			
			//Sanitize incoming POST data. 
			$post = filter_var_array( $_POST, FILTER_SANITIZE_STRING );
			
			//Check if Nonce is set. 
			if ( ! isset( $post['_nn_payment_nonce'] )  || ! wp_verify_nonce( $post['_nn_payment_nonce'], 'nn_pay_'.$post[ 'enrollment_type' ] ) 
			) {
			   print 'Sorry, your payment request did not validate.';
			   exit;
			} 
			
			//If response is not false...
			if( ( $response = $this->process( $post ) ) !== false ){
				
				$json = json_encode( $response, JSON_FORCE_OBJECT );
				
				dump( __LINE__, __METHOD__, $json );
				
				//If a successful transaction has been completed, let's format data for use with the system first. 
				/* $formatter = new misc/NNDataFormat( $response, '' );
			
				//Incomplete... needs work. 
				$formatter->add_post_data( $post );
				
				$formatted = $formatter->do_formatting();
				
				 */
				
				$next_step = $this->next_step( $post, $response );
				
				//$arr = nn_format_data( $response, 'Stripe' );
				//format data
				
				switch( $this->type ){
					case 'subscription':
						$tx_id = $response[ 'latest_invoice' ];
						break;
						
					case 'manual':
						$tx_id = $response[ 'id' ]; //?
						break;
						
					case 'payment':
					default:
						$tx_id = $response[ 'id' ];
						break;
					
				}
				
				
				$params = array(
					'tx_id' => $tx_id,
					//'register' => true,
					
				);
				
				
				$redirect = ( !empty( $next_step ) )? $next_step :  $post[ 'return_success' ];
				
				//$this->redirect( $redirect, $params );
				//dump( __LINE__, __METHOD__, $post );
				//dump( __LINE__, __METHOD__, $response );
				//dump( __LINE__, __METHOD__, $params );
			} else {
				
				$this->redirect( $post[ 'return_fail' ] );
				
			}
			
			
		}
		

	/*
		Name: process
		Description: The reponse that comes out of this method is where we finally get workable data to begin to process. 
	*/	
		public function process( $post ){
				
			$response = NULL;
			
		
			//This is from old code which class is presently found on NBCS_Network plugin on the cbldev server. 
			$t_action = new DoTransaction( $post );
			
			$this->type = $type = $t_action->type;
			//print_pre( $t_action ); 
			//echo "TYPE is $type . <br />"; 
			//If no type set. Stop all action. 
			if( $type == false )				
				wp_die( 'Are \'ya lost, stranger?' );
			
			if( $type !== 'no_pay' ){
				
				$payment = new DoPayment( $post );
				
				//$type = 'subscription', 'manual', 'invoice', 'payment', //add 'refund'
				$response = $payment->$type();
				 
				//If response comes out being NULL here, process as failed and redirect to failed paid attempt... maybe. 
			} 
			
			//If response is not empty, send response data. If empty, return false. 
			return ( !empty( $response ) )? $response : false ;
			
			
			/* 
			
			print_pre( $response );
			
			//Post Reponse to Transaction Class
			if( $response !== NULL ) //If NULL, nothing to add. 
				$posted = $t_action->post_response( $type, $response  );
			
			//query for existing user ID. 
			//$user = $t_action->get_user();
			
			//Record Transaction
			//$recorded = $t_action->record( $user, $response );
			
			//
			 */
		}		
				
	
	/*
		Name: redirect
		Description: 
	*/		
		
		public function redirect( $page, $params = [] ){
			
			$url = $page;
			
			if( !empty( $params ) ){
				$query = http_build_query( $params );
				$url .=  '/?'.$query;	
			}
			
			wp_safe_redirect( $url ); exit;
		}	
						
	
	/*
		Name: next_step
		Description: 
	*/		
		
		public function next_step( $post, $response ){
			
			$patron_id =  get_current_user_id();
			
			if( empty( $patron_id ) ){
				$patron = get_user_by( 'email', $post[ 'stripeEmail' ] );
				$patron_id = ( !empty( $patron ) )? $patron->ID : 0 ; 
			}
			
			if( empty( $patron_id ) ){
				$patron = get_user_by_meta( 'stripe_customer', $response->customer );
				$patron_id = ( !empty( $patron ) )? $patron->ID : 0 ; 
			}
			
			if( empty( $patron_id ) ){
				//store reciept
				nn_unassigned_transaction( $this->type, $post, $response );
				//This would create a transaction post type without an assigned user. 
				
				return 'receipt';
			}
				 
			//If at this point, no user account has been found. Store the transaction in an unassigned transactions area, don't create a user account. Acknowledge payment, and have them check their email for reciept and further instructions. 
			
			//if user account does exist, is registration needed? 
			// That is an individual site question. 
			// So the childbirth library would check to see if a lite regstiraiton is needed. It sets the applicable parameters if need. 
			// The certificate LMS checks if a full regsitration is needed. It sets the applicable meta data paramaters if needed. 
			do_action( 'nn_register_check' );
			
			$meta = get_user_meta( $patron_id );
			
			//full registration
			if( isset( $meta[ 'nn_register' ] ) && $meta[ 'nn_register' ] !== 1 )			
				return 'register';
				
			//lite registration
			if( isset( $meta[ 'nn_register_lite' ] ) && $meta[ 'nn_register_lite' ] !== 1 )			
				return 'register_lt';
				
			
			return null;
			//Questions to Answer: 
			// 1: Does User have an account? 
			// 1.1: Is user logged in? 
			// 1.2: Does email address or customer ID match any records on file?
			// 2: Does User need Registration? 
			// 3: If yes, What type of registration is needed? 			
		}	
	}
}	

//$collector = new nbcs_net_collect();

	
	

	
//STEP 2: Record Transaction
	

//STEP 2.5: Set Enrollment Token 
	//Enrollment tokens handle notification process
	
	

//STEP 3: Take Appropriate Actions (Set Permissions, Redirection)


	//If new user, proceed to registration page if for new student registration. 
	
	
	//If for library: 
		//If new user: 
			//If paying, process user information
			
			//If free, forward to limited registration form. 
		
		//If user exists:
			//prompt login. 
			
			//retreive password.
			
			
			
//IF FAILED: 
	//redirect to specified page. 
	
	
//END RESULTS

//1. Childbirth Library - Free Preview
	//An Account Registration Page. 
	
//2. Childbirth LIbrary - Payment Plan
	//If already logged in, An Account Registration Page with updated contact info from Payment. 
	//If not logged in, An Account Registration Page, where login credentials are created, billing info added from payment. 
	
//3. Childbirth Library - Payment Plan Change
	//If Upgrade or Downgrade, A confirmation page to report action taken. Link to Account Overview page. 
	
//4. Doula Training - Full Certificate Plan
//5. Doula Training - Recurring/Manual Payment Plans. 
	//If not logged in / No account associated with email address, send to Account Registration Page. 
	//If account is recognized, Account Verification Page. 
	
//6. Doula Training - Coaching Payment	
	//If logged in, A confirmation page that explains what to expect. 
	//Should not have access, if not logged in. 
	
//7. Doula Training - Cert Extension 1 mo. 
//8. Doula Training - Cert Extension 6 mo. 
	//If logged in, A Confirmation Page that explains what to expect. 
		//Link to Account, Last studied link. 
		
//9. Doula Training - Certificate Renewal
	//logged in, A Confirmation Page that explains full renewal process. 
	
	



?>