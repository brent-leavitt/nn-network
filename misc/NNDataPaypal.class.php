<?php 

/* NNDataPaypal Class for NBCS Network Plugin
Last Updated 18 Dec 2018
-------------

Description: 

		
*/
	
namespace misc;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNDataPaypal' ) ){
	class NNDataPaypal{

	// PROPERTIES
		
		public $in; 
		public $data = [];
		/* public $out = []; */
		
		//https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNandPDTVariables/#
		private $data_map = [
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
			$this->to_array();
		}	
		
		

	/*
		Name: to_array
		Description: Convert Raw data to array format and return the array with data. 
	*/	
		
		public function to_array(){
			
			$raw_post_array =  explode('&', $this->in);
						
			$array = array();
			
			foreach( $raw_post_array as $keyval ){
			  
				$exploded = explode( '=', $keyval );
				
				if( count( $exploded ) == 2 )
					$array[ $exploded[ 0 ] ] = urldecode( $exploded[ 1 ] );
			}

			if( !empty( $array ) )
				$this->data = $array;
				
			
		}

		
	/*
		Name: get_data_map
		Description: returns the data_map property which is privately protected. 
	*/	
		
		public function get_data_map(){
			
			return $this->data_map;
			
		}	

		
	/*
		Name: get_action
		Description: 
	*/	
		
		public function get_action(){
			
			//$action = $this->data[ 'custom' ];
			
			return 1;
		}		
				
		
	/*
		Name: get_patron
		Description: 
	*/	
		
		public function get_patron(){
			
			//Is on_behalf_of set in the source data? 
			
			//Get 
			//$p_email = $this->data[ 'payer_email' ];
			
			//$patron = get_user_by( 'email', $p_email );
			
			//what is the payee email? 
			return 2;
		}		
				
		
	/*
		Name: get_service
		Description: 
	*/	
		
		public function get_service(){
			
			//$service = $this->source->get_service();
			
			
			return 3;
		}		
				
		
	/*
		Name: get_token
		Description: 
	*/	
		
		public function get_token(){
			
			//$token = $this->source->get_token();
			
			return 4;
		}		
				
		
	/*
		Name: get_
		Description: CAREFUL WITH THIS ONE. 
	*/	
		
		public function get__(){
			
			
			return 5;
		}		
		




		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}		
	

	}//end of class
}