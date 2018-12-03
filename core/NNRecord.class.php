<?PHP

/*

Record Automation - Code Plan for NBCS
Last Updated 2 Oct 2018
-------------

Desription: This is the record keeping class. 

	The last step in any action taken by the system or a user is to record it in the system database. It requires a listener to be set at the beginning of any action to collect data as the processes are being executed in order to create an effective paper trail. :| how to do this? 

	Collected Data stored in the record can be a numeric array (indicating each main step in the process) with nested associated arrays detailing key information about each step in the process. 
	
		$args = array(
			'do_action' => array(
				'step1' => true,
				'step2' => true
			),
			'do_another' => array(
				'step1' => true,
				'step2' => true
			)
		);
	
	Records and Post in the CRM should be combined into one Record CPT, because both record actions taken on a user's account. 
		
		
---

*/
if( !class_exists( 'NNRecord' ) ){
	class NNRecord{

	// Variables
		
		public $patron = 0; 
		public $record = array();
		


	// Method
					
	/*
		Name: __construct
		Description: 
	*/	

		public function __construct( $patron, $record ){
			
			$this->patron = $patron;
			$this->record = $record;
			return $this->init();
			
		}
					
	/*
		Name: init
		Description: 
	*/		
		public function init(){
			//Take data, process it into a <pre> formatted text for human readibility. 
			//Store the same data as an exerpt 
			
			//Check that properties are set. 
			foreach( get_object_vars( $this ) as $prop ){
				if( empty( $prop ))
					return false;
			}
			
			return $this->save();
			
		}
	
	/*
		Name: save
		Description: 
	*/	
			
		public function save(){
			
			$post_record = array(
				'post_title' => $this->record_title(),
				'post_author' => $this->patron,
				'post_content' => $this->pre_format(),
				'post_excerpt' => serialize( $this->record );
				'post_type' => 'NNRecord',
				
			);
				
			//Save only to the BASE SITE if set.  
			if( defined( 'NN_BASESITE' ) )
				switch_to_blog( NN_BASESITE );

			$result = wp_insert_post( $post_record );
			
			if( defined( 'NN_BASESITE' ) )
				restore_current_blog();
			
			return $result;
			
		}	

		
	/*
		Name: pre_format
		Description: 
	*/		
		
		public function pre_format(){
			
			$output = "<pre>";
			$output .= var_export( $this->record, true );
			$output .= "</pre>";
			
			return $output; 
		}	
				
			
	/*
		Name: record_title
		Description: 
	*/	
			
		public function record_title(){
			
			$date = date( 'y-m-d' );
			$patron = $this->patron;
			$record = $this->record;
			end( $record );
			$method = ucfirst( key( $record ) ); //get the key of the last array which is the initiating method. 
			$title = "$method Record, Patron $patron on $date ";
			
			return $title;
		}	

		
	/*
		Name: 
		Description: 
	*/	
			
		public function __(){
			
			
		}
	}
}	

?>
	