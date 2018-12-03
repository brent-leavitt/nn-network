<?php 

/* 

Collect //Functionality Class

---

Description: This is the Listener Object for Payments being Processed through Stripe via the QueryVar Handler. 
	- Collect is responsible for the front end of all payment processing. 

	
	
	- Not sure if other transactions will need to pass through here or not. 





// Collection Page
*/

namespace proc;

if( !class_exists( 'NNCollect' ) ){
	class NNCollect{
			
		//Properties
		
		
		
		
		//Methods
		
		public function __construct(){
			
			$POST = $this->init();
			
			$this->process( $POST );
			
			
		}
		
		
/*
*	name: init
*	description: 
*
*/		
		
		private function init(){
			
			//This class shoudl only fire if there is form data to be processed. 
			if( !isset( $_POST ) || empty( $_POST ) ){
				
				print( 'Form data is not set or is empty!' );
				exit;
			}  
			
			//Sanitize incoming POST data. 
			$POST = filter_var_array( $_POST, FILTER_SANITIZE_STRING );

			//Check if Nonce is set. 
			if ( ! isset( $POST['_nb_payment_nonce'] )  || ! wp_verify_nonce( $POST['_nb_payment_nonce'], 'nbcs_pay_'.$POST[ 'enrollment_type' ] ) 
			) {

			   print 'Sorry, your nonce did not verify.';
			   exit;

			} 
			
			return $POST; 
			
		}
		
		
		
/*
*
*
*/		
			
		public function process( $post ){
				
			$response = NULL;
			
			//This is from old code which class is presently found on NBCS_Network plugin on the cbldev server. 
			$t_action = new nbcs_net_do_transaction( $post );
			
			$type = $t_action->type;
			
			echo "TYPE is $type . <br />"; 
			//If no type set. Stop all action. 
			if( $type == false )				
				wp_die( 'Are \'ya lost, stranger?' );
			
			if( $type !== 'no_pay' ){
				
				$payment = new nbcs_net_do_payment( $post );
				
				switch( $type ){
					
					case 'subscription':
						$response = $payment->subscription();
						break;
						
					case 'invoice':
						$response = $payment->invoice();
						break;	
						
					case 'manual':
						$response = $payment->manual();
						break;
					
					case 'payment':
						$response = $payment->payment();
						break;
					
				}
				 
				//If response comes out being NULL here, process as failed and redirect to failed paid attempt... maybe. 
			} 
			
			print_pre( $response );
			
			//Post Reponse to Transaction Class
			if( $response !== NULL ) //If NULL, nothing to add. 
				$posted = $t_action->post_response( $type, $response  );
			
			//query for existing user ID. 
			//$user = $t_action->get_user();
			
			//Record Transaction
			//$recorded = $t_action->record( $user, $response );
			
			//
			
		}		
				
		
		
/*
*
*
*/		
					
		
		
		
	}
}	

$collector = new nbcs_net_collect();

	
	

	
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