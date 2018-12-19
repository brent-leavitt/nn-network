<?php 

/* 

NNDataPaypal Class for NBCS Network Plugin
Last Updated 19 Dec 2018
-------------

Description: 

		
*/
	
namespace misc;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNDataStripe' ) ){
	class NNDataStripe{

	// PROPERTIES
		
			//https://stripe.com/docs/api
		private $stripe_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];

		
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( ){
			
			
		}	
		

	/*
		Name: __Destruct
		Description: 
	*/	
		
		public function __destruct(){
			
			
			
		}	
		



	/*
		Name: Init
		Description: 
	*/	
		
		private function init(){
			
			
			
		}	
		
		

	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}

		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}

	}//end of class
}

?>