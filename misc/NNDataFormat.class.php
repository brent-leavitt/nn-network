<?php 

/* NNDataFormat Class for NBCS Network Plugin
Last Updated 18 Dec 2018
-------------

Description: 

		
*/
	
namespace misc;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNDataFormat' ) ){
	class NNDataFormat{

	// PROPERTIES
		
		// 
		//public $data;
		public $in;								//Incoming Data String
		private $data; 							//Incoming Data Converted to Array. 
		private $out; 							//Outgoing Data Array
		private $source;						//The Source Class: Stripe, PayPal, Default. 
		
		private $output_format = array(
			'action' => '',						//Primary Action 
			'service' =>  '', 					//
			'patron' => '', 					//
			'token' => '', 						//
			'data' => array(					//The data key may be replaced with the name of the Primary Action. 
				'type' => '', 					//type of data = reminder (NEEDED?)
				'template' => 0, 				//what notice (template) is being sent?
				'template_vars' = array(),
				'create_date' => '', 			//Create Date or issue Date
				'due_date' => '', 				//Due Date
				'trans_type' => '', 			//Transaction Type, like "charge", "payment", "refund", etc. 
				'trans_status' => '',			//Transaction Status
				'trans_descrip' => '',			//Description of the Transaction
				'currency' => '',				//Currency (only accepting USD)
				'subtotal' => '',		 		//Subtotal before taxes
				'discount' => '',		 		//Discount on Subtotal
				'sales_tax' => '',		 		//Sales Tax
				'gross_amount' => '', 			//Transaction Gross Amount
				'trans_fee' => '',  	 		//Transaction Fee
				'net_amount' => '',		 		//Amount Collected After Fees
				'reference_ID' => '',	 		//Reference ID
				'reference_type' => '',			//Reference Type
				'tp_name' => '', 				//ThirdParty Name, like "stripe" or PayPal 
				'tp_id' => '', 					//ThirdParty Transaction ID
				
				'line_items' => array(
					array(
						'li_id' => '', 			//Item ID
						'li_descrip' => '',		//Description
						'li_qty' => '', 		//Qty
						'unit_price' => '',		//Unit Price
						'li_discount' => '', 	//Discout
						'account' => '', 		//Account
						'li_amount' => '', 		//Amount
					),
					//etc...
				),
				'payee' => array(
					'full_name' => '',			//
					'user_name' => '', 			//
					'display_name' => '', 		//
					'first_name' => '',			//
					'last_name' => '',			//
					'address' => '',			//	
					'address1' => '',			//	
					'city' => '',				//	
					'state' => '',				//	
					'zip' => '',				//	
					'country' => '',			//	
					'email' => '',				//	
					'phone' => '',				//	
					'type' => '',				//paypal, visa, mastercard, etc. 
					'card' => '',				//last4 of 
					'exp' => '',				//expiration date. 
					'on_behalf_of' => '',		//email_address 
					'password' => '',			//?
				),
				'src_data' => '',				//JSON String of Transactional Source Data. 
				'' => '',						// (what else)?
			)
		)
		
		);
		
		private $default_data_map = [
		
		];			// 'nn_value' => '3rd_party_value'
		
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
		
		
		//https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNandPDTVariables/#
		private $paypal_data_map = [
			'create_date' => 'payment_date',
			'trans_type' => 'txn_type',
			'trans_status' => 'payment_status',
			'currency' => 'mc_currency',
			'discount' => 'discount',		 		//Discount on Subtotal
			'sales_tax' => 'tax',		 			//Sales Tax
			'gross_amount' => 'mc_gross', 			//Transaction Gross Amount
			'trans_fee' => 'mc_fee',  	 			//Transaction Fee
			'tp_id' => 'txn_id', 		
			'full_name' => 'address_name',			//
			'first_name' => 'first_name',			//
			'last_name' => 'last_name',				//
			'address' => 'address_street',			//	
			'city' => 'address_city',				//	
			'state' => 'address_state',				//	
			'zip' => 'address_zip',					//	
			'country' => 'address_country',			//	
			'email' => 'payer_email',				//	
			'phone' => 'contact_phone',				//	

		];

		
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( $data ){
			
			$this->init( $data );
			
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
		
		private function init( $data ){
			
			$this->in = $data;
			
		}	
		
		

	/*
		Name: set_source
		Description: This sets the source of the data. 
	*/	
		
		public function set_source( $source ){
			
			//We may want to set some security checks. 
			
			//Set Source Name Property
			if( !empty( $source ) ){
				
				//All Source Class Names will be formated with first letter cap, all else lower case: ex. Paypal. 
				$source = ucfirst( strtolower( $source ) );
				
				$src_class = 'NNData'.$this->source;
				
				$this->source = new $src_class();
				
				//Convert incoming data to an array. This will vary according on where the data is coming from. 
				$this->data = $this->source->to_array( $this->in );
				
			}
			
		}
		
		
	/*
		Name: format
		Description: 
	*/	
		
		public function format(){
			
			if( empty( $this->in ) ) 
				return; 
			
			
			$this->do_formatting( $this->in );
				
			return ( !empty( $this->out ) )? $this->out : false;
			
			
		}
		
		
	/*
		Name: do_formatting
		Description: 
	*/	
		
		private function do_formatting( $data ){
			
			//mapping tool
			$data_map = $this->get_data_map();
			
			//output format
			$output = $this->do_mapping( $this->output_format, $data_map );
			
			
			
			//Then remove empty fields from the output array. 
			
			
			
			//Base intergrity check of data. 
				//Is there enough incoming data to do something with? 
			
			if( $this->ingetrity( $output ) )
				$this->out = $output;
		}
		
		
		
	/*
		Name: get_data_map
		Description: 
	*/	
		
		private function get_data_map(){
			
			//get source name
			$data_map = ( !empty( $this->source ) )? $this->source : 'default' ;
			
			$data_map .= '_data_map';
			
			return $this->$data_map;
			
		}
		
	/*
		Name: integrity
		Description: Checks the integrity of the outputted data. If key fields are in place then return true. 
	*/	
		
		public function integrity( $data ){
			
			$fields = [ 'action', 'service', 'patron', 'token' ];
			
			foreach( $fields as $field ){
				if( !isset( $field ) || empty( $field ) )
					return false;
			}
			
			return true; 
		}
		
		
		
	/*
		Name: do_mapping
		Description: 
	*/	
		
		public function do_mapping( $output, $data_map){
			//Then for each field in the output format, look for a suitable input. 
			foreach( $output as $o_key => $o_val ){

				//generate potential method name.
				$set_key = 'set_'. $o_key; 
				
				//look for method by $o_key name; 
				if( method_exists( $this, $set_key ) ){ 
					$output[ $o_key ] = $this->$set_key();
					continue;
				}
				
				//if is an array, go recursively deeper. 
				if( is_array( $o_val ) ){
					$output[ $o_key ]  = $this->do_mapping( $o_val, $data_map );
					continue; 
				}
				
				//if no, run data_map on value
				
				$output[ $o_key ] = $this->find_in_source( $data_map[ $o_key ] );
			}
			
			
		}		
		
		
	/*
		Name: set_action
		Description: 
	*/	
		
		public function set_action(){
			
			
		}		
				
		
	/*
		Name: set_patron
		Description: 
	*/	
		
		public function set_patron(){
			
			
		}		
				
		
	/*
		Name: set_service
		Description: 
	*/	
		
		public function set_service(){
			
			
		}		
				
		
	/*
		Name: set_token
		Description: 
	*/	
		
		public function set_token(){
			
			
		}		
				
		
	/*
		Name: set_
		Description: CAREFUL WITH THIS ONE. 
	*/	
		
		public function set__(){
			
			
		}		
		
		
	/*
		Name: find_in_source
		Description: looking in the source array for the requested value. 
	*/	
		
		public function find_in_source( $key, $data = $this->in;){
			
			$result = '';
			//If this doesn't return a result. pop off the first word, and look deeper. 
			if( empty( $result = $data[ $key ] ) ){
				$pos = strpos( $key, '_' );
				$key_one = substr( $key, 0, $pos  );
				$key_two = substr( $key, $pos + 1 );
				$result = $data[ $key_one ][ $key_two ];
			}
			return ( !empty( $result ) )? $result : '' ; 
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