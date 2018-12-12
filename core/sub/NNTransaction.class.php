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
			'TransID' 		=> 'post_name',
			'Type'			=> 'post_type',
			'CreateDate'	=> 'post_date_gmt',
			'Status'		=> 'post_status',
			'Flags'			=> 'NNTransFlags', 	//(meta)
				
				
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
		
	
	private $actions = [];
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

	Name: get_actions
	Description: this makes the additional needed actions accessible to the NNAction.class.php
	
*/	
		
	
	public function get_actions(){
		
		return ( !empty( $this->actions ) )? $this->actions : false ;
		
	}	
				
/*
	Name: set_actions
	Description: This allows child objects to send actions to the private $actions array. 
*/	
			
	
	public function set_actions( $actions ){

		foreach( $actions as $action )
			$this->actions[] = $action;
		
	}	
			
/*
	Name: 
	Description: 
*/	
			
	
	public function __(){
		
		
	}	
		
	
}

?>