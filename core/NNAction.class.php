<?php 

/* Actions Automation - Code Plan for NBCS
Last Updated 2 Oct 2018
-------------

Description: This is the Action Automation business logic for the Network Plugin

A class that takes direction from listeners or users as to what system actions to take.
	-It figures out if the command was initiated by a user or automated response. 
	-It performes what kind of actions?

Action is the master class of all core classes. 
	- As such, it records and reports to the record class. 
		- Starts Recording actions in the constructor method
		- Ends recording and reports to the record class in the destruct method.
		
*/
	
namespace core;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNAction' ) ){
	class NNAction{

	// PROPERTIES
		
		//This is to record the action steps taken. 
		public $record = array();
		
		public $patron = 0; 

		public $data = array();
		
		private $actions = array();
			
		

	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( $data ){
			
			$this->init( $data );
			
		}	
		

	/*
		Name: __Destruct
		Description: 
	*/	
		
		public function __destruct(){
			
			//The last thing this does is send action data to the record keeper. 
			$this->do_record();
			
		}	
		



	/*
		Name: Init
		Description: 
	*/	
		
		private function init( $data ){
			
			//set data to class property
			if( $this->check_data( $data ) ){
				
				//Set super action: 
				//Is the action that is being called set?
				if( isset( $data[ "action" ] ) && is_callable( array( $this, $data[ "action" ] ) ) ){
					
					$super = $data[ "action" ];
					
					//A lite registration must be processed before any payments can be accepted on our website? 
					
					//Is this a registration?
					if( strpos( 'register', $super ) === 0 ){
						
						
						$this->register();
						return; 
				
						
					//If not registration, get patron from submitted data. 	
					} elseif( $this->do_patron( 'find' ) ){
						
						//Set the primary action to be taken. 
						$this->actions[] = 'do_'.$super;
						
						//Process the actions. 
						$this->actions();
						return;
					} 
				} 
				
				//if Not registration and no patron is registered, 
				$this->do_admin_warning( 'The user is not on file. May be illegal.' );	
				
			}
		}	
		


	/*
		Name: actions
		Description: This is a very powerful step. This loops through all set actions in the action paramater, and processes each accordingly. This replaced 4 functions that had parallel code within it. 
		
		This set of processes was replaced with the code below it: 
			//Do Receipt
			$record['do_receipt'] = $this->do_receipt();
			
			//Do Enrollment
			if( in_array( 'do_enrollment', $this->actions ) )
				$record['do_enrollment'] = $this->do_enrollment();
			
			//Do Service
			if( in_array( 'do_service', $this->actions ) )
				$record['do_service'] = $this->do_service();
			
			//Is User Action Needed?
				//If Yes Do User Action.
			if( in_array( 'do_role', $this->actions ) )
				$record[ 'do_role' ] = $this->do_role();
				
			
			//Do notice
			if( in_array( 'do_notice', $this->actions ) )
				$record['do_notice'] = $this->do_notice();
	*/	
		
		public function actions(){
			
			$actions = [ 'invoice', 'receipt', 'enrollment', 'service', 'role', 'notice' ];
			$record = array();
			
			foreach( $actions as $action ){
				
				$do = 'do_'.$action;
				
				if( in_array( $do, $this->actions ) )	
					$record[ $do ] = $this->$do();		
			}			
			
			$this->record_step( __METHOD__ , $record);
			
		}	
			
	/*
		Name: register
		Description: 
	*/	
		
		public function register(){
			
			$record = array();
			
			//do register
			$record[ 'do_patron' ] = $this->do_patron( 'register' );
			
			//do notice
			if( in_array( 'do_notice', $this->actions ) )
				$record['do_notice'] = $this->do_notice();
			
			$this->record_step( __METHOD__ , $record );
		}
		
	/*
		Name: Check Data
		Description: Validates the data being passed. This requires that the data array being sent has 'action' key as first element. If this element is not set, data is seen as invalid. 
		
		That data that is being "returned" to the '->data' property is a flattened array of data for the specfic action to be taken. At this point, we are not preparing the data to insert to the database, not yet. Got to make it uniform first. 
		
		//Do we want to do a real filter/sanitize? 
	*/	
		
		private function check_data( $data ){
			
			//Data is set and is made uniform.
			$data_set = new NNData( $data );

			if( $check = $data_set->valid )
				$this->data = $data_set->get();//Returns an array (not object)
					
			return $check;

		}	
	
		
		
		
	/*
		Name: clean_up
		Description: Ever do_(action) had two clean_up steps at the end of the function: record action taken, and add additional actions. We are doing these things here. 
	*/	
		
		public function clean_up( $func , $record = array(), $obj ){
			
			//Possibly add a timestamp here or in "do_record"
			
			//Store actions taken and their results. 
			$this->record_step( $func , $record );
			
			//What additional actions need to be taken? Ask the object if they have anything else to do. Then merge with existing list of to dos. 
			if( method_exists( $obj, 'get_action' ) )
				$this->actions = array_unique( array_merge( $this->actions, $obj->get_actions() ) );
		}	
		

	/*
		Name: Record Step
		Description: This is separate from clean_up function because it is also called elsewhere. 
	*/	
		
		public function record_step( $func , $data = array() ){
			
			//This could end up being quite bloated. 
			$this->record[] = array( $func => $data ); 
		}	
		
	
				
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}	
		
		
		
	// CORE ACTIONS	

	/*
		Name: do_patron
		Description: Process a patron action and then return the patron ID. 
		param: $action = 'find', 'register', 'reglite'
		//THIS NEEDS WORK.
		returns: true or false
	*/	
		
		public function do_patron( $action ){
			
			//The NNPatron is an extension of WP_User, so 
			
			$patron = new NNPatron( $this->data );
			
			if( ( $patron_id = $patron->$action() ) != false ){
				
				$this->patron = $patron->id;
				
			} else {
				
				//Error Processing. 
				//How to handle error messages and send them back to the front end. 
				
			}
			
			/* 
			$patron = new NNPatron();
			$this->patron = $patron->$action( $this->data ); */
			
			$record = 'The patron ID is: '. $this->patron . ', and the action performed was '. $action ;
			

			$this->clean_up( __METHOD__ , $record, $patron );
		
			return ( $this->patron > 0 )? true : false ;
		}	
		
		
	/*
		Name: do_invoice
		Description: This takes in invoice data and processes it accordingly. It needs to assess whether this is a create, update, delete, or remind. 
	*/	
		
		public function do_invoice(){
		
			$record = array();
			
			//Let's make a data handler object. 
			//This may not be needed now. This is preprocssed. 
			/* $data = new NNData( $this->data );
			$invoice_data = $data->get_invoice_data(); */
			
			$invoice = new NNInvoice( $this->data );
					
			//Check that action is set?
			if( !empty( $invoice->action ) )
				$record[ 'invoice_process' ] = $invoice->process();
		
			$this->clean_up( __METHOD__ , $record, $invoice );
			
			return ( !empty( $record ) )? true : false;
		}	
		
	/*
		Name: do_receipt
		Description: 
	*/	
		
		public function do_receipt(){
			
			$record = array();
			
			$receipt = new NNReceipt( $this->data );
			
			$record['receipt_issue'] = $receipt->issue(); //Returns a post ID for the receipt generated. 
			
			//if( !empty( $record[ 'receipt_id' ] ) )
				//$record[] = $this->do_notice( $record[ 'receipt_id' ] );
			
			$this->clean_up( __METHOD__ , $record, $receipt );
			
			return ( !empty( $record ) )? true : false;
		}	

		
	/*
		Name: do_enrollment
		Description: Sets or updates an enrollment token. 
	*/	
		
		public function do_enrollment(){
			
			$record = array();
			
			$enrollment = new Enrollment( $this->patron );			
			
			//add, expire, annul, or retire enrollment token. Anything else? 
			$record[ 'do_enrollment_process' ] = $enrollment->process( $this->data );
		
			
			$this->clean_up( __METHOD__ , $record, $enrollment );
			
			return ( !empty( $record ) )? true : false;
		}	
		
	/*
		Name: do_service
		Description: Sets or updates a service. Services Can be called to cancel as well. 
		
	*/	
		
		public function do_service(){
			
			$record = array();
			
			//Receives Data with instruction on what to do. 
			$service = new NNService( $this->data );
			
			//if service doesn't exist, let's create it. 
			if( !$service->find() ){
				
				$record[ 'do_service_create' ] = $service->create();
				
			//else if the service exists this must be an update to the service. 
			}elseif( isset( $service->service_id ) ){
				
				$record[ 'do_service_update' ] = $service->update();
				
			}
			 
			do_action( 'NNAction_do_service', $service );
			
			$this->clean_up( __METHOD__ , $record, $service );
				
			return ( !empty( $record ) )? true : false;
		}	

		
	/*	
		Name: do_role
		Description: 
	*/	
		
		public function do_role(){
			
			$record = array();
			
			$role = new NNRole( $this->data );
			
			//Send the role object to the the respective service to adjust rules according to each service. 
			do_action( 'NNAction_Do_Role', $role );
			
			
			/* 	
			
			/Available Role Actions: add, remove, set
			$actions_arr = [ 'add', 'remove', 'set' ];
			
			if( in_array( $action, $actions_arr ) ){
				
				$role_action = $action. '_role';
				$record[ 'role_action' ] = $patron->$role_action( $role );
			} */
			
			//After individual sites have used Role object to update patron roles, post a report to the record.
			$record[ 'do_role_report' ] = $role->report();
			
			$this->clean_up( __METHOD__ , $record, $role );
			
			return ( !empty( $record ) )? true : false;
		}	
		
		
	/*
		Name: do_notice
		Description: This allows for multiple notices to be sent from the results array. 
	*/	
		
		public function do_notice(){
			
			//What information needs to be sent for a notice to be effectively processed?
			
			$record = [];
			
			//Let's make a data handler object. 
			/* 
			
			$data = new NNData( $this->data );
			$notice_data = $data->get_notice_data(); 
			$notices = $data->get_notices(); //returns an array of all notices to be sent. 
			
			*/
			 
			 
			//$notice = new NNNotice();
			//$notice->send_data( $this->data );
			
			/* 
			
			foreach( $notices as $message_slug ){
				$notice = new NNNotice( $notice_data );
				$record[] = $notice->send( $message_slug );
			}
			
			*/
			 
			$notice = new NNNotice( $this->data );
				
			$record['notice_send'] = $notice->send();
			 
			$this->clean_up( __METHOD__ , $record, $notice );
			
			return ( !empty( $record ) )? true : false ; //true or false for success status. 
		}	
		
		
	/*
		Name: Do Record
		Description: 
	*/	
		
		public function do_record(){
			
			$record = new NNRecord( $this->patron, $this->record );
			
			//Send admin warning if fails to record: 
			
			if( is_wp_error( $record ) )
				do_admin_warning( $msg );
			
		}
		


	/*
		Name: do_admin_warning
		Description: 
	*/	
		
		public function do_admin_warning( $msg ){
			
			//Add DevTool for sending admin notices.
			
		}
		

	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}
		
		
		
	//HOLD MANUAL FUNCTIONS UNTIL DETERMINED THAT THEY ARE NO LONGER NEEDED. 	
		
	/*
		Name: Manual Payment Action
		Description: 
	
		
		public function manual_payment(){
			
			$this->record_step( __METHOD__ , $result );
			//Do User 
			//Do Role
			//Do Service
			//Do Receipt
			//Do notice
			
		}	
	*/		
	/*
		Name: Manual Service Action
		Description: 
		
		
		public function manual_service(){
			
			$this->record_step( __METHOD__ , $result );
			
			//Do Service Action
			//Is User Action Needed?
				//If Yes Do User Action.
			//Do notice	
			
		}	
	*/	
	/*
		Name: Manual Role Action
		Description: 
		
		
		public function manual_role(){
			
			$this->record_step( __METHOD__ , $result );
			//Do Role
			//Do Service? Maybe
			//Do notice
			
		}	
	*/	
	/*
		Name: Manual Invoice Action
		Description: 
		
		
		public function manual_invoice(){
			
			$this->record_step( __METHOD__ , $result );
			//Do invoice
			//do notice
			
		}	
	*/	
	/*
		Name: Manual Register
		Description: 
		
		
		public function manual_register(){
			
			$this->record_step( __METHOD__ , $result );
			//do register
			//do notice
		}	
		
	*/	
		
		
	}
}
/*
Super Actions: 

		- time_service_action
		
		- time_invoice_action
		
		- time_payment_action
		
		- time_role_action
		
		
		- manual_payment_action
		
		- manual_service_action
		
		- manual_role_action
		
		- manual_invoice_action

		- manual_register_action
		

Intermediate Actions: 
	(Groups of actions that are always the same)
	
		- ? TBD

		
		
Core Actions: 
		
		- do_action
			- do_patron_action
			- do_role_action
			- do_service_action
			
		- do_invoice	
			
			
		- do_receipt
			
			
		- do_notice
		
		- do_record
			- if not empty record[], then process. 
		
		- record_step

Baseline Action / Typical Action Flows: 	
	
	
	- Time Initiated action (case 1) -time_service_action
		-> Enrollment Token Action 
			-> Sometimes, an additionl (user?) Action is taken. (optional)
			-> notice Action
				-> record keeping action
				
	- Time Initiated action (case 2) -time_invoice_action
		-> Invoice Action
			-> notice Action
				-> record keeping action (needed? Yes)
				
	- Time Initiatd action (case 3) - time_payment_action (automatic payments)
		-> Payment action 
			(These are not required to be sequential?)
			(Was an invoice involved?)
			-> user/user role/service action 
			-> receipt action 
				-> notice action
					-> record keeping action 
			
				
				
	-User Initiated Action	-patron_payment_action
		-> Payment action 
			(These are not required to be sequential?)
			(Was an invoice involved?)
			-> user/user role/service action 
			-> receipt action 
				-> notice action
					-> record keeping action 
	
	-User Initiated Action	- manual_register_action
		-> Registration action
			-> notice action
				- record action
					
				
				
	- Other User Initiated Action	(case 1) -manual_service_action
		-> enrollment token action (Same steps as above when time initiated)
			-> Sometimes, an Action is taken. (optional)
			-> notice action
				-> record keeping action
			
				
				
	- Other User Initiated Action (case 2) -manual_role_action
		-> user role action
			->notice action (? optional)
				-> record keeping action

	??? DOES patron/roles need to be separated? 
	
				
	- Other User Initiated Action (case 3) -manual_invoice_action
		-> Invoice Action
			- > notice action
				-> record keeping action
		
		
			
---------------
	variables
		- record array
		- init - time or manual //Hows was the action initiated?"What was the initiating action?"
			
		
	functions
		- init
			-collect submitted data for record and further processing. 
			- check for further data based on init vairiable 
				- if it was a manual initiated, who did it, etc. 
				- if it was time initiated, what did it? 

*/




?>
