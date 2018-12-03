<?php 

/* Transaction - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 2 Oct 2018
-------------

Desription: Service the similar interests of the Invoice and Receipt Classes

Namespace: /core/sub_core/


---

*/

namespace core/sub;

class NNTransaction extends NNPostData{
	
	
	//Properties

	//What properties are universal to both receipts and invoices?
	public 
		$NNTransaction_data_map = array(
			'TransID' 	=> 'post_name',
			'Type'		=> 'post_type',
			'CreateDate'	=> 'post_date_gmt',
			'Status'		=> 'post_status',
			'SourceTxn'		=> 'post_parent',
			'Amount'		=> (meta) 'NNTransAmount', 
			'TPName'		=> (meta) 'NN3PID',
			'TPID'			=> (meta) 'NN3PTransID',
			'Flags'			=> (meta) 'NNTransFlags',
				$flags = array(
					payee_different = false,
					?
				),
				
			'WebHookData'	=> 'post_excerpt', // (JSON String)
			'ProcessedData' => 'post_content', // (JSON String)
				$data = array(
					//line items
						//Item ID
						//Description
						//Qty
						//Unit Price
						//Discout
						//Account
						//Amount
					//Subtotal
					//Reference
					//Sales Tax
					//Gross Total
					//Transaction Fee
					//Net Amount
						
				)
			), //
			$__ = ''; //
		
	
	//Methods
	
	
/*
	Name: __construct
	Description: 
*/	
			
	
	public function __construct( $data ){
		
		$this->init( $data );
	}	
			
	
/*
	Name: init
	Description: 
*/	
			
	
	public function init( $data ){
		
		$this->set_data( $data );
		
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
		
	
}

?>