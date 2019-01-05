<?php 

/* Transaction - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 2 Oct 2018
-------------

Desription: Service the similar interests of the Invoice and Receipt Classes

To Do: Store Transactions for printing purposes. 
Maybe move the source data to the post_exceprt field. 
Maybe move all other data from post_meta to post_content as JSON String? 

---

*/

namespace core\sub;

class NNTransaction extends NNPostData{
	
	
	//Properties

	//What properties are universal to both receipts and invoices, only map those items that have a direct correlation to a post field. Everything else goes to meta, with same key name as the property name. 
	public 
		$NNTransaction_data_map = array(
			'trans_id' 		=> 'post_name',
			'trans_date'	=> 'post_date',
			'trans_status'	=> 'post_status',
			'trans_type'	=> '', 
			'amount' 		=> '',
			'currency' 		=> '',
			'sales_tax' 	=> '',
			'subtotal' 		=> '',
			'net_amount' 	=> '',
			'src_data' 		=> '',
		); 
		
	protected $meta_key = 'NNTransData';
		
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
		//We just need the next parent class. 
		$this->extend_data_map( key( class_parents( $this ) ) );
		//Then the current class (which will be Receipt or invoice)
		$this->extend_data_map( get_class( $this ) );
		
		
		
		//Assign incoming data to the data property for access. 
		$this->data = $data; 
		//Asssign incoming data to respective and available properties. 
		$this->set_data( $data );
		
		if( method_exists( $this, 'set_src_data' ) ) $this->set_src_data();
		
		//If a post ID is set, retrieve the post. 
		if( !empty( $this->ID ) ){
			$this->retrieve();
		}
	}	
			
	

/*
	Name: 
	Description: 
*/	
			
	
	public function __(){
		
		
	}	
		
	
}

?>