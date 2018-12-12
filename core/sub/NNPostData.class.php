<?php 

/* NNPostData - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 22 Oct 2018
-------------

Desription: This is the core class for using the data that is stored multiple Post Type classes used acrossed the network. Namely,  NNReceipt, NNInvoice (grouped together under the NNTransaction Parent Class),  NNNotice,and NNRecord. 

The NNPostData class performs fundamental functions that are useful for the CPTs created specifically for the network.  These CPT are stored exclusively in the CRM database and as such are only initialized therein. 


---

*/

namespace core/sub;

class NNPostData{
	
	
	//Properties

	public 
		$ID = 0,
		$patron_id = 0,
		$post_type = '', //
		$master_site = 2, //This should be stored as an option on the network and retrieved dynamically. But for now...
		$data = array(),
		$data_map = array(
			'patron_id' => 'post_author'
		); //
	
	//This is what get's sent to WordPress
	public $post_arr = array( 
			//'ID' => 0, //add if this an update. 
			'post_author' => 0,
			'post_content' => '',
			'post_content_filtered' => '',
			'post_title' => '',
			'post_excerpt' => '',
			'post_status' => 'draft',
			'post_type' => 'post',
			'comment_status' => '',
			'ping_status' => '',
			'post_password' => '',
			'to_ping' =>  '',
			'pinged' => '',
			'post_parent' => 0,
			'menu_order' => 0,
			'guid' => '',
			'import_id' => 0,
			'context' => '',
			'meta_input' => array(), //this can also be added to send meta data
		);
		
	public $post_meta = array(
		// 'meta_key' => 'meta_value'
		
	);


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

		//Storing source data into the class. Needed. For right now, yes. 
		$this->data = $data; 
	
		$this->set_data( $data );
		
		//If a post ID is set, retrieve the post. 
		if( !empty( $this->ID ) ){
			$this->retrieve();
		}
	}	
			
	
	
/*
	Name: set_data
	Description: This is the initial data to properties setup. If there is an initiating value sent in the data assign it to its respective property. 
*/	
			
	
	public function set_data( $data ){
		
		foreach ( get_object_vars( $this ) as $key => $value ){
			if( isset( $data[ $key ] ) && !empty( $data[ $key ] ) ){
				$this->$key = $data[ $key ];
			}
		}	
	}	
	
	
	
/*
	Name: prepare
	Description: Prepares data being sent in via the data array for insertion into WPDB

			
	
	public function prepare(){
		
		
	}	
*/			
				
/*
	Name: retrieve
	Description: This pulls a custom post type from the database and then assigns it to the requesting class
*/	
			
	
	public function retrieve(){
		
		$post = array();
		$meta = array();
		
		if( isset( $this->ID ) ){
			
			//This information is stored in the CRM/MasterSite
			switch_to_blog( $this->master_site );
			
				$post = get_post(  $this->ID, 'ARRAY_A'  );
				//This only returns post data, not meta. 
				$meta = get_post_meta( $this->ID, '', true);
				
			//Return back to current space. 
			restore_current_blog();
			
			//Clean up and condense meta data returned from WPDB. 
			foreach( $meta as $mkey => $mval ){
				$meta_count = count( $mval );
				
				//if not more than one value is set, remove from array wrapper
				if( $meta_count < 1 )
					unset( $meta[ $mkey ] );
				elseif( $meta_count == 1 )
					$meta[ $mkey ] = $mval[0];
				
				//if value is empty remove from array. 
				if( empty( $meta[ $mkey ] ) )
					unset( $meta[ $mkey ] );
				
			}
				
			//Tuck in the cleaned up meta data into the meta_input space in the post data. 
			$post = array_merge( $post, ['meta_input' => $meta] );

			//$post = array_merge( $post, $meta );
		}
		
		
		$this->post_arr = array_merge( $this->post_arr, $post );
		
		//Assign values to class properties. 
		$this->reverse_map();
		
	}

		
/*
	Name: insert
	Description: Inserts submitted data into the WPDB. This is the final step in submitting data. 
*/			
	
	public function insert(){
		
		//Map data values to the post_arr
		$this->map();
		
		//$this->add_meta(); //NOT NEEDED, Duplicate action from $this->map(). 
		
		//Array_filter drops empty fields if no callback function is provided. 
		$this->post_arr = array_filter( $this->post_arr /*,$callback_function missing*/ );
		
		//This information is stored in the CRM/MasterSite
		switch_to_blog( $this->master_site );
		
		$result = wp_insert_post(  $this->post_arr );
		
		//Return back to current space. 
		restore_current_blog();
		
		if( !is_wp_error( $result ) ){
			
			$this->ID = $result;
			return true;
		}
		
		return $result;
	}

	
	
/*
	Name: add_meta
	Description: Adds Metadata to the main post_arr array. 
*/	
			
	
	public function add_meta(){
		
		//Remove empty values, if any. 
		$this->post_meta = array_filter( $this->post_meta );
		
		if( !empty( $this->post_meta ) ){
			
			$this->post_arr[ 'meta_input' ] = $this->post_meta;
			
			return true;
		}
		
		return false;
	}

	
/*
	Name: extend_data_map
	Description: This takes the data_map of the extending class and adds it to the core data map. 

*/			
	
	public function extend_data_map(){
		
		$t_class = $this->get_current_class();
		
		$t_data_map = $t_class.'_data_map';
		
		$this->data_map =  array_merge( $this->data_map, this->$t_data_map );
		
	}		
	
		
	
/*
	Name: get_current_class
	Description: Isolates the top-level data class from it's name space. 
*/	
			
	
	public function get_current_class(){
		
		$class = urlencode( get_class( $this ) );
		
		$t_class = substr( $class , strrpos( $class, '%5C' ) +3 );
		
		return $t_class;
		
	}		
	
	
	
/*
	Name: map_data
	Description: (INACTIVE) 
	
			
	
	public function map_data(){
		
		$this->post_arr = array_map( $this->map(), $this->post_arr, $this->data_map );
		
	}		
	
*/		
	
/*
	Name: map
	Description: This takes all data that is sent in via the $data array var, and maps it to the respective field for insertion in the WPDB. 
*/	
			
	
	public function map() {
		
		$post = $this->post_arr;
		$map = $this->data_map;
		
		$mapped = array();
		$meta = array();
		
		foreach( $map as $key => $val ){
			
			//checking if this class has the requesting property set. 
			if( !empty( $this->data[ $key ] ) ){ //This is going through the properties of the class to connect them to the post_arr value. 
			
				//Now we're checking if the post_arr has the requesting key set.
				if( array_key_exists( $val, $post ) ){
					$mapped[ $val ] =  $this->data[ $key ];
					
				//If not, add val to meta array that we'll tack on at the end.
				} else {
					$meta[ $val ] = $this->$key;
				}	
			}
		}
		
		$mapped[ 'meta_input' ] = $meta; //Add the meta data to the array.
		
		//Checking if any properties are matched to the actual post_arr keys.
		foreach( $post as $key => $val ){
			if( isset( $this->$key ) )
				$mapped[ $key ] = $this->$key;
		}
		
		//This will merge any duplicate keys with the later vallues of mapped overriding the existing. 
		$this->post_arr = array_merge( $this->post_arr, $mapped );
		
		//needed? 
		return $mapped;
	}
	
	
	
/*
	Name: reverse_map
	Description: Takes data from a WP_POST call and maps it to class values. 
	Does not process Meta_data yet. 
	
*/			
	
	
	public function reverse_map(){
		
		$post = $this->post_arr;
		$map = $this->data_map;
		
		$mapped = array();
		
		foreach( $map as $key => $val ){
			
			//Look at post data. 
			if( !is_string( $val ) )
				continue;
			
			if( !empty( $post[ $val ] )  ){
				
				//Is there a mapped propertied for this line of post data?
				if( isset( $this->$key ) ){
					
					$this->$key = $post[ $val ];
				}
			}
		}
		
		foreach( get_object_vars( $this )  as $key => $val ){
			if( isset( $post[ $key ] )  ){
				
				$this->$key = $post[ $key ];
				
			}
		}
		
	}


	
	
/*
	Name: destroy
	Description: 

			
	
	public function destroy(){
		
		
	}	
*/		
	
	
/*
	Name: 
	Description: 

			
	
	public function __(){
		
		
	}
		
*/	

	
	
		
	
}

/*	
//DEV TEST CODE

$NNdata = array( 'ID' => 23 );

$NNcpt = new NNCPT( $NNdata );
echo "<br />". $NNcpt->ID;
echo "<br /><pre>";
var_dump( $NNcpt );
echo "</pre>";
*/


?>