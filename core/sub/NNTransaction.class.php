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
		); //
	public $post_meta = array(
		'NNTransData' => []
		// 'meta_key' => 'meta_value'
		
	);
		$__ = ''; //
		
	
	private $actions = [];
	//Methods
	
	
/*
	Name: __construct
	Description: Commented out because this is the same as the parent class. 

			
	
	public function __construct( $data ){
		
		$this->init( $data );
	}	
*/				
	
/*
	Name: init
	Description: 
*/	
			
	
	public function init( $data ){
		
		//Adds the transaction data map to the postData data map. 
		$this->extend_data_map();
		//Assign incoming data to the data property for access. 
		$this->data = $data; 
		//Asssign incoming data to respective and available properties. 
		$this->set_data( $data );
		
		//If a post ID is set, retrieve the post. 
		if( !empty( $this->ID ) ){
			$this->retrieve();
		}
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