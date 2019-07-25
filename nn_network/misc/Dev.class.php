<?php 

/*
nn_network\misc\Dev;



*/

namespace nn_network\misc;

use people_lms\core\Enrollment as Enrollment;
use people_lms\core\Action as Action;
use nn_network\misc\DataFormat as Format;

if( !class_exists( 'Dev' ) ){
	class Dev{
		
		
		public function __construct(){
					
			$this->init();
		
		}
		

		/**
		 * Initialization
		 *
		 * @since 1.0
		 **/
		public function init() {
			add_action( 'admin_menu', array( __CLASS__, 'add_dev_pages' ) );
			//add_action( 'init', array( __CLASS__, 'process_student' ) );
		}

		/**
		 * Add Dev Pages
		 *
		 * @since 1.0
		 **/
		public static function add_dev_pages() {
		
			//DEV TEST WINDOW
			add_menu_page('Development', 'Development', 'edit_users', 'development',  array( __CLASS__, 'load_dev_overview' ) , '
	dashicons-admin-generic', 1 );
			add_submenu_page( 'development', ' Dev Screen', 'Dev Screen', 'edit_users', 'dev_screen',  array( __CLASS__, 'load_dev_screen' ) );
			
		}
		

		/**
		 * Load Dev Overview
		 *
		 * @since 1.0
		 **/
		public static function load_dev_overview(){
			
			global $title;
			
			echo "<div class='wrap'>
			<h1 id='wp-heading-inline'>$title</h1>";
			
			
			echo "This is the development overview page for the NBCS Network Plugin";
			
			
			echo"</div>";
			
		}
		

		/**
		 * Load Dev Overview
		 *
		 * @since 1.0
		 **/	
		public static function load_dev_screen(){	
			
			
			global $title;
			
			echo "<div class='wrap'>
			<h1 id='wp-heading-inline'>$title</h1>";
			
			
			echo "<p>This is the Development Screen page for the NBCS Plugin. Ref: /misc/Dev </p>";
			
			
			if( isset( $_POST['nn_login'] ) && wp_verify_nonce( $_POST['_nn_login_nonce'], 'nn-login-nonce' ) ) {
				
				nn_errors()->add('empty_username', __('Invalid username'));
			
				nn_errors()->add('empty_password', __('Incorrect password'));
				
				$errors = nn_errors()->get_error_messages();
				
				print_pre( $errors );
				
				
			} else {
				
				$uforms = new \tmpl\UserForms();

				//GET REGISTRATION FORM
				//$output = $uforms->form( 'registerlite', $post );

				// GET LOGIN FORM
				$output .= $uforms->form( 'login', $post );

				echo $output;
				
			}
			

			
			
			
		
			
			//self::data_format_test();
			
			/* ep( 'Scheduled Cron Jobs are in the array below. ' );
			$crons = _get_cron_array();
			print_pre( $crons ); */
			
			//self::sandbox();
			
			echo "</div>";
			
		}
		
		

		/**
		 * Sandbox
		 *
		 * @since 1.0
		 **/	
		public function sandbox(){
			
			//Incoming data: 
				
						
				$json = '{"id":"ch_1Dg9sb2eZvKYlo2C4nXsQytS","object":"charge","amount":100,"amount_refunded":0,"application":null,"application_fee":null,"balance_transaction":"txn_19XJJ02eZvKYlo2ClwuJ1rbA","captured":false,"created":1544529505,"currency":"usd","customer":null,"description":"MyFirstTestCharge(createdforAPIdocs)","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{},"on_behalf_of":null,"order":null,"outcome":null,"paid":true,"payment_intent":null,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":],"has_more":false,"total_count":0,"url":"/v1/charges/ch_1Dg9sb2eZvKYlo2C4nXsQytS/refunds"},"review":null,"shipping":null,"source":{"id":"card_19yUNL2eZvKYlo2CNGsN6EWH","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"unchecked","dynamic_last4":null,"exp_month":12,"exp_year":2020,"fingerprint":"Xt5EWLLDS7FJjR1c","funding":"credit","last4":"4242","metadata":{},"name":"JennyRosen","tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded","transfer_group":null}';

				$array = json_decode( $json , true );
					
				//Pre Processing: 
				$data = array(
					'action' => 'receipt',		//Action = payment. 
					'service' =>  'fdc', //
					'patron' => 2, //
					'token' => 'certificate_one_month', //
					'receipt' => array(
						'trans_type' => 'payment', 		//Transaction Type, like "charge", "payment", "refund", etc. 
						'trans_date' => '2019-01-28 11:34:00', 		//Transaction Date
						'trans_status' => 'complete',		//Transaction Status
						'trans_descrip' => '',		//Description of the Transaction
						'currency' => 'USD',			//Currency (only accepting USD)
						'amount' => '50.00', 			//Transaction Gross Amount
						'trans_fee' => '1.38',  	 	//Transaction Fee
						'subtotal' => '48.62',		 	//Subtotal before taxes
						'sales_tax' => '0.00',		 	//Sales Tax
						'net_amount' => '50.00',		 	//Amount Collected After Fees
						'reference_ID' => '',	 	//Reference ID - invoice? 
						'reference_type' => 'invoice',		//Reference Type
						'tp_name' => 'stripe', 			//ThirdParty Name, like "stripe" or PayPal 
						'tp_id' => '0567891234', 				//ThirdParty Transaction ID
						
						'line_items' => array(
							array(
								'li_id' => 'One Month Registation Fees', 			//Item ID
								'li_descrip' => 'One Month Registation Fees',		//Description
								'li_qty' => 1, 		//Qty
								'unit_price' => '50.00',		//Unit Price
								'li_discount' => '0', 	//Discout
								'account' => '50.00', 		//Account
								'li_amount' => '50.00', 		//Amount
							),
							//etc...
						),
						'payee' => array(
							'first_name' => 'Jane',			//
							'last_name' => 'Salazar',			//
							'address' => '1234 E 567 N',			//	
							'address1' => '',			//	
							'city' => 'Provo',				//	
							'state' => 'UT',				//	
							'zip' => '84602',				//	
							'country' => 'US',			//	
							'email' => 'test@trainingdoulas.com',				//	
							'phone' => '555-555-5555',				//	
							'type' => 'visa',				//paypal, visa, mastercard, etc. 
							'card' => '1234',				//last4 of 
							'exp' => '09/20',				//expiration date. 
							'on_behalf_of' => '',		//email_address 
							'' => '',					//
						),
						'src_data' => $json,				//JSON String of Transactional Source Data. 
						'' => '',						// (what else)?
					)
				);
				
			
			//Action: 
			
			$action = new Action( $data );
				
			
			//dump( __LINE__, __METHOD__, $action );
			
		
		}

		
		/**
		 * Sandbox
		 *
		 * @since 1.0
		 **/	
		public function data_format_test(){		
			
			ep( "Data Format Test function is called." );
		
			$json = '{"id":"sub_FFwx7oktiVnBq8","object":"subscription","application_fee_percent":null,"billing":"charge_automatically","billing_cycle_anchor":1560563531,"billing_thresholds":null,"cancel_at":null,"cancel_at_period_end":false,"canceled_at":null,"collection_method":"charge_automatically","created":1560563531,"current_period_end":1563155531,"current_period_start":1560563531,"customer":"cus_FFwxFKGxCqMJoD","days_until_due":null,"default_payment_method":null,"default_source":null,"default_tax_rates":{},"discount":null,"ended_at":null,"items":{"object":"list","data":{"0":{"id":"si_FFwxr3oogWgloR","object":"subscription_item","billing_thresholds":null,"created":1560563531,"metadata":{},"plan":{"id":"plan_F6dP7S9MilOACx","object":"plan","active":true,"aggregate_usage":null,"amount":500,"billing_scheme":"per_unit","created":1558415029,"currency":"usd","interval":"month","interval_count":1,"livemode":false,"metadata":{},"nickname":"Childbirth Library Monthly Subscription","product":"prod_DXuvzA0BGIwaGm","tiers":null,"tiers_mode":null,"transform_usage":null,"trial_period_days":null,"usage_type":"licensed"},"quantity":1,"subscription":"sub_FFwx7oktiVnBq8","tax_rates":{}}},"has_more":false,"total_count":1,"url":"\/v1\/subscription_items?subscription=sub_FFwx7oktiVnBq8"},"latest_invoice":"in_1ElR3vEY0jlqbLN4dbH9HHlM","livemode":false,"metadata":{},"plan":{"id":"plan_F6dP7S9MilOACx","object":"plan","active":true,"aggregate_usage":null,"amount":500,"billing_scheme":"per_unit","created":1558415029,"currency":"usd","interval":"month","interval_count":1,"livemode":false,"metadata":{},"nickname":"Childbirth Library Monthly Subscription","product":"prod_DXuvzA0BGIwaGm","tiers":null,"tiers_mode":null,"transform_usage":null,"trial_period_days":null,"usage_type":"licensed"},"quantity":1,"schedule":null,"start":1560563531,"start_date":1560563531,"status":"active","tax_percent":null,"trial_end":null,"trial_start":null}';
			
			$response = json_decode( $json );
			
			$post = array(
				"action" 		=> "receipt",
				"service_id" 		=> "BDC",
				"enrollment_type" 	=> "library_month",
				"price"				=> "500", 
				"return_success"	=> "thank-you",  
				"return_fail"		=> "registration",  
				"interval"			=> "0",  
				"duration"			=> "1m",  
				"_nn_payment_nonce"	=> "39a36d7ba3",  
				"_wp_http_referer"	=> "/cashier/",  
				"stripeToken"		=> "tok_1EkFwoEY0jlqbLN4gkGuSUUx",  
				"stripeTokenType"	=> "card",  
				"stripeEmail"		=> "518@trainingdoulas.com",  
				"stripeBillingName"	=> "TestUser518",  
				"stripeBillingAddressCountry"	=> "United States",  
				"stripeBillingAddressCountryCode"	=> "US",  
				"stripeBillingAddressZip"	=> "65340",  
				"stripeBillingAddressLine1"	=> "1234 N 5678 W",  
				"stripeBillingAddressCity"	=> "Marshall",  
				"stripeBillingAddressState"	=> "MO"
			);
			
			
			$formatter = new Format( $response, 'Stripe' );
				
			//Incomplete... needs work. 
			$formatter->add_post_data( $post );
			
			$formatted = $formatter->set_format();
			
			
			dump( __LINE__, __METHOD__, $formatted );
			
		}
	}
}


?>