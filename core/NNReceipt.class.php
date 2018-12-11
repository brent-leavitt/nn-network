<?php 

/*  Receipt - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 22 Oct 2018
-------------

Description: - Receipt issuing is controlled here. Extends Transaction Sub Class


*/

namespace core;

use core/sub/NNTransaction;

class NNReceipt extends NNTransaction{
	
	
	//Properties

	public 
		$ = '', //
		$__ = ''; //
	
	
	//Methods
	
	
/*  MAY NOT BE NEEDED IF IT IS THE SAME AS PARENT CLASS

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
			
	//Is this needed, or does the parent class cover the setup. 
	public function init( $data ){
		//copy from parent class. 
		$this->set_data( $data );
		
		
		//Unique to NNReceipt class.
		$this->post_type = 'NNReceipt';
	}	
			
	
				
/*
	Name: issue
	Description: This generated a Receipt CPT for storage in the database as a receipt. What is unique to Receipts from Invoices? 
	
*/	
			
	
	public function issue(){
		
		$result = $this->insert();
		//Necessary?
		
		//add action from parent class. 
		$this->set_actions( [ 'do_role', 'do_notice' ] );
		
	}	
	

	
/*
	Name: 
	Description: 
*/	
			
	
	public function __(){
		
		
	}	
		
	
}

?>