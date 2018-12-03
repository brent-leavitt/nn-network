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
		$__ = '', //
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