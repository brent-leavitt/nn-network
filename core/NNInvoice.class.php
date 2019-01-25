<?php 

/*  Invoice - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 22 Oct 2018
-------------

Description: - Invoice issuing is controlled here. Extends Transaction Sub Class. This seems like it is only a "receipt" of invoice that is recorded in the database and sent to the patron as a notice. There should then also be a link to pay the invoice. Where? Are these invoices generated by Stripe or PayPal? 


*/
namespace core;

use core\sub\NNTransaction;

class NNInvoice extends NNTransaction{
	
	
	//Properties	
	
	public $post_type = NN_PREFIX.'invoice';
	
	public $NNInvoice_data_map = array(
		'due_date' 		=> '', 
		'amount_due' 		=> '', 
		'amount_remaining' 		=> '', 
		'paid' 		=> '', 
		'receipt_id' 		=> 'post_parent', 
		'' 		=> '', 
		'' 		=> '', 
		'' 		=> '', 
	);
	
	public $due_date,
		$amount_due,
		$amount_remaining,
		$paid, //true or false
		$receipt_id,
		$,
		$,
		$,
		$;
	//Methods
	
	
/*
	Name: __construct
	Description: NOT NEEDED, COVERED BY PARENT CLASS
	
			
	
	public function __construct( $data ){
		
		$this->init( $data );
	}	
			
*/	
/*
	Name: init
	Description: NOT NEEDED, COVERED BY PARENT CLASS
	
			
	
	public function init( $data ){
		
		$this->set_data( $data );
		
	}	
*/			

/*
	Name: issue
	Description: This generated a Invoice CPT for storage in the database as a receipt. What is unique to Receipts from Invoices? 
	
*/	
			
	
	public function issue(){
		
		$result = $this->insert();
		//Necessary? Yes. 
		 
		//set additional actions only if the receipt was successfully added. 
		if( $result != false )
			$this->set_actions( [ 'do_notice' ] );
		
		return ( $result )? $this->ID : false ; //Returns the invoice CPT ID. 
	}	
	
			
/*
	Name: 
	Description: 
*/	
			
	
	public function __(){
		
		
	}	
		
	
}

?>