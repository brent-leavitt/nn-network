<?php 

/*  Receipt - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 22 Oct 2018
-------------

Description: - Receipt issuing is controlled here. Extends Transaction Sub Class


*/

namespace core;

use core\sub\NNTransaction;

class NNReceipt extends NNTransaction{
	
	
	//Properties	
	
	public $post_type = NN_PREFIX.'receipt';
	
	public $NNReceipt_data_map = array(
		'invoice_id'		=> 'post_parent',
		'trans_fee' 		=> '', 
		'reference_type' 	=> '',
		'tp_name' 			=> '',
		'tp_id' 			=> '',
		'payee_type' 		=> '',
		'payee_card' 		=> '',
		'payee_exp' 		=> '',	
	);
	
	public $invoice_id,		$trans_fee,		$reference_type, 	
		$tp_name,		$tp_id,		$payee_type,		$payee_card,		$payee_exp;
		//Methods
	
	
/*  MAY NOT BE NEEDED IF IT IS THE SAME AS PARENT CLASS

	Name: __construct
	Description: 
	
			
	
	public function __construct( $data ){
		
		$this->init( $data );
	}	
			
*/	
/*  
	Name: init
	Description: 
	
			
	//Is this needed, or does the parent class cover the setup. 
	public function init( $data ){
		//copy from parent class. 
		$this->set_data( $data );
		
		
		//Unique to NNReceipt class.
		$this->post_type = 'NNReceipt';
	}	
			
*/	
				
/*
	Name: issue
	Description: This generated a Receipt CPT for storage in the database as a receipt. What is unique to Receipts from Invoices? 
	
*/	
			
	
	public function issue(){
		
		$result = $this->insert();
		//Necessary? Yes. 
		
		//set additional actions only if the receipt was successfully added. 
		if( $result != false )
			$this->set_actions( [ 'do_enrollment', 'do_role', 'do_notice' ] );
		
		return ( $result )? $this->ID : false ; //Returns the receipt CPT ID. 
	}	
	

	
/*
	Name: set_src_data
	Description: 
*/	
			
	
	public function set_src_data(){
		
		$this->src_data = $this->data[ 'src_data' ];
		unset($this->data[ 'src_data' ]);
		//dump( __LINE__, __METHOD__, $this->src_data );
	}	
			

	
/*
	Name: 
	Description: 
*/	
			
	
	public function __(){
		
		
	}	
		
	
}

?>