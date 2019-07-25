<?php
/*
nn_network\proc\DoTransaction

Do Transaction Class - Processing Class  for NN Network Plugin
Last Updated on 15 Jul 2019
------------- 

	Description:  

	
*/

namespace nn_network\proc;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'DoTransaction' ) ){
	class DoTransaction{
			
		public $types = array( 
			'no_pay', 
			'payment', 
			'subscription', 
			'manual',
			'invoice',
			'refund'
		),
		$type = NULL,
		$post = [];
		
	
	/*
		Name: 
		Description: 
	*/		
		public function __construct( $post ){
			
			$this->post = $post;
			
			$this->init();
		}
		
		
	
	/*
		Name: 
		Description: 
	*/		
		
		private function init(){
			
			$this->set_type();
			
		}
		
		
		
	
	/*
		Name: 
		Description: 
	*/		
		public function set_type(){
			//Based on Post Data being Sent, cast transaction type. 
			
			if( empty( $this->post ) )
				return;
			
			$type = NULL;
			$post = $this->post;
			
			$enroll = ( is_string( $post[ 'enrollment_type' ] ) )? $post[ 'enrollment_type'	] : false; 
			
			if( $enroll != false ){
				
				switch( $post[ 'enrollment_type' ] ){
				//No Pays
					case 'library_preview':
					case 'library_free':
						$type = 'no_pay'; 
						break;
						
				//Subscriptions (Monthly Recurring)
					case 'library_month':
					case 'library_year':
					case 'certificate_recurring':
						$type = 'subscription'; 
						break;
						
				//Manual (Series with a Manual Payment Option)
					case 'certificate_manual':
						$type = 'manual'; 
						break;
				
				//Else Payment
					case 'certificate_full':
					case 'cert_extend_1mo':
					case 'cert_extend_6mo':
					case 'certificate_renewal':
					case 'certificate_payoff':
					case 'payment_special':
					//No DEFAULT 
						$type = 'payment'; 
						break;
						
						
				//
				}
				
				//We're just being super secure here. If not specified, Set as NULL. 
				$this->type = ( in_array( $type, $this->types ) )? $type : NULL;
				
			}	
		}


	/*
		Name: 
		Description: 
	*/	
		public function record( $user, $response ){
			
			//What is needed to create the transaction? 
				//A user ID to assign to the transaction.
				//Key Transactional Information. 
					//Transaction ID
					//user ID
					//Date
					//Amount
					//3Party ID 
					//3P Transaction ID
					//Transaction Type - Invoice, Payment, Refund, etc
					//Status
					//Data
					
		
			//The end result is that we end up a with a record in the database of the transaction that has just occured. 
			//Return true on successful recording in DB. 
			// 
			
			
			//what kind of data is being sent here? 
			//
			echo "Type of transaction: $type <br /> ";
			
			echo "A charge has been collected! For the amount of <strong>". $response->amount ."</strong>. <br />" ;
			
			echo "<pre>";
			
			var_dump( $response );
			
			echo "</pre>";

			//return ()? true : false ; 
			
		}

	/*
		Name: 
		Description: 
	*/	
		public function meaning(){
			
			
		}



	
		
	}
}

?>