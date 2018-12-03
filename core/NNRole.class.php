<?php 

/*  

Role (User Roles) - Core Class Psuedo Code for NBCS Network Plugin
Last Updated 2 Oct 2018
-------------

Description: - Actions regarding updates to user roles are controlled here. 
		
---

- This is a class that get's used in the individual sites but is relative to all sites. How to call? 

- This class gets sent as an object via the do_action hook in the do_role method of the NNAction class. 
	- what methods could be employed to 

- User Role Actions
	- Grant User Access to User Role
		- What user, what role
	- Revoke User Access to User Role
		- what user, what role revoked?
	- Change User Role
		- What user, what changed role? 
		
	- Grant, Change, or Revoke user role on network site
		- what action, what user, what site, what role
	
	- Get, Set, and Unset functions (lower levels of abstraction for Grant, Revoke, and Change)
	
*/

namespace core;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNRole' ) ){
	class NNRole{
		
		//PROPERTIES
		public $patron = 0;
		public $role = '';	
		public $old_role;
		/* public $token = ''; */
		//public $date = ''; //Needed? Why?
		
		private $service;
		private $change = false;
		private $actions = [];
		
		
		
		//Methods
			
	/*
		Name: __construct
		Description: 
	*/	
			
		
		public function __construct( $data ){
			
			//Check that both patron ID and Service are set. 
			//What action is to be taken on the service? 
			
			$this->init( $data );
			
		}
			
	/*
		Name: init
		Description: A call to setup a service for a given user based on parameters set. 
		
	*/	
			
		
		private function init( $data ){
			
			//Set Patron
			$this->patron = $data[ 'patron' ];
			
			//Set Role 
			$patron_data = get_userdata( $this->patron );
			$this->role = implode( ', ', $patron_data->roles ) ; //returns current user role	
			
			//Set Service Object
			$this->service = new NNService( $data );
			//The code below belongs in a separate function that get's called by the 
			
						
		}
		
		
			
			
	/*

		Name: 
		Description: 

	*/	
			
		
		public function __(){
			
			//Find Service ID
			
			
			$service_id = $this->service->find();
			
			$service_status = get_post_status( $service_id );
				
			
		}	
			
			
	/*

		Name: site_roles
		Description: Returns all site roles that are available 

	*/	
			
		
		public function site_roles(){
		
			global $wpdb;
			$user_roles = $wpdb->prefix .'user_roles';

			$options = get_option( $user_roles );

			$roles = [];

			foreach( $options as $role => $stuff  ){
				$roles[] = $role;
			}
			
			return $roles;
			
		}	
			
			
	/*

		Name: set
		Description: this sets the role when an updated role is determined to be needed. 

	*/	
			
		
		public function set( $role ){
			
			$patron = new WP_User( $this->patron );
			
			if( strpos( $role, $patron->roles[0] ) != 0 ){
				$this->old_role = $this->role;
				$patron->set_role( $role );
			}
			
			
			
			if( strpos( $role, $patron->roles[0] ) == 0 ){
				$this->role = $role;
				$this->change = true; 
				return true;
			}
			return false;
		}
				
			
	/*

		Name: update_network
		Description: updates network based on changes to requesting site.

	*/	
			
		
		public function update_network(){
			
			
		}
			
			
	/*

		Name: is_diff 
		Description: if new role is the same as existing role, return true;

	*/	
			
		
		public function is_diff( $new_role ){
			
			return ( strpos( $new_role, $this->role ) == 0 )? false : true; 
			
		}

		
	/*

		Name: report
		Description: reports on what has happened in the role class, because most actions will be handled by other plugins. 

	*/	
			
		
		public function report(){
			
			return ( $this->change )? 'The role was changed to '. $this->status .'.' :  'No change to patron role.' ;
			
		}
		
		
		
	/*

		Name: 
		Description: 

	*/	
			
		
		public function __(){
			
			
		}
		
		
	}//End Class
}


/* 
	IN Separate PLUGIN Create something like this:  

	End result is role for site is updated. 
	Roles across network are also updated. 
	
*/


//FOR USE IN THE CERTS LMS 
function process_role_certs_lms( $role ){
	
	//This retrieves all status from all services associated with the patron in question. 
	$services = $role->service->get_all_status();	
		
	$permissions = array(
		'issued' 	=> 'alumnus_active',
		'active' 	=> 'student_active',
		'expired'	=> 'alumnus',
		'inactive' 	=> 'student',
	);
			
	//CODE FOR CERTS LMS
	foreach( $persmissions as $srvc_status => $role ){	
		if( in_array( $srvc_status, $services ) ){
			$new_role = $role;
			continue;
		}	 	
	}
	
	//if role is different, set new role.
	if( $role->is_diff( $new_role ) )
		$role_set = $role->set( $new_role );
	
	//if new role is set, update the network. 
	if( $role_set )
		$role->update_network();
}

add_action( 'NNAction_Do_Role', 'process_role_certs_lms', 10, 1 );




//FOR USE IN THE CBL
function process_role_cbl( $role ){
	
	//new role is based on whether the status of the service is active or not. 	
	$new_role = ( strpos( 'active',  $role->service->status ) == 0 )? 'subscriber' : 'visitor';	
	
	//if role is different, set new role.
	if( $role->is_diff( $new_role ) )
		$role_set = $role->set( $new_role );
	
	//if new role is set, update the network. 
	if( $role_set )
		$role->update_network();
}

add_action( 'NNAction_Do_Role', 'process_role_cbl', 10, 1 );


//*** NEED TO DO ONE FOR PEOPLE CRM
	
	
	//Current user role is already set at this point
	//Service that may effect the change to user role is also loaded. 
	
	//$role->role current isset
	//$role->service->status updated isset
	
	//What are all the services on this site that would affect a user's role? 
		//collect them into an array 
		
		/* 
		$services = $role->service->get_all_status();	
		
		$services = [
			'(service_id)' => '(service_status)', 
			'(service_id)' => '(service_status)', 
			'(service_id)' => '(service_status)', 
			'(service_id)' => '(service_status)', 
		 ] */
		
		/*
		
			
		
		$roles = $this->site_roles(); //Returns an array of all current roles on the site. 
		
		
		<?php



			$blogs_ids = get_sites();

			foreach( $blogs_ids as $b ){

				switch_to_blog( $b->blog_id  );
				
				global $wpdb;
				$user_roles = $wpdb->prefix .'user_roles';
				
				$options = get_option( $user_roles );
				
				restore_current_blog();
				
				$roles = [];
				
				foreach( $options as $role => $stuff  ){
					$roles[] = $role;
				}
				
				echo "<pre>";
				var_export( $roles );
				echo "</pre>";
			}	



		
		//SITE 1
		array (
		  0 => 'administrator',
		  1 => 'editor',
		  2 => 'author',
		  3 => 'contributor',
		  4 => 'subscriber',
		  5 => 'customer',
		  6 => 'shop_manager',
		  7 => 'wpseo_manager',
		  8 => 'wpseo_editor',
		)
		
		//SITE 2
		array (
		  0 => 'administrator',
		  1 => 'trainer',
		  2 => 'resource',
		  3 => 'alumnus_active',
		  4 => 'alumnus',
		  5 => 'student_active',
		  6 => 'student',
		  7 => 'inquirer',
		  8 => 'birther',
		  9 => 'supporter',
		  10 => 'mother',
		  11 => 'learner',
		  12 => 'reader',
		  13 => 'inactive',
		)

		//SITE 4
		array (
		  0 => 'administrator',
		  1 => 'editor',
		  2 => 'author',
		  3 => 'contributor',
		  4 => 'subscriber',
		  5 => 'wpseo_manager',
		  6 => 'wpseo_editor',
		)

		//SITE 5
		array (
		  0 => 'administrator',
		  1 => 'other',
		  2 => 'inactive',
		  3 => 'student',
		  4 => 'alumnus',
		)
		
		//SITE 6
		array (
		  0 => 'administrator',
		  1 => 'alumnus_active',
		  2 => 'alumnus_inactive',
		  3 => 'student_full_active',
		  4 => 'student_full_inactive',
		  5 => 'student_partial_active',
		  6 => 'student_partial_inactive',
		)
		
		*/
	
		//What rules affect the decision making process of a role to be assigned? 	
			//Ever site has different user permissions and different rules governing those permissions. How do I abstract that? 
		
		//If patron has a service with this status, grant this role. 
			//foreach service assigned to a patron, 

			//What is the highest service status? These are not hierarchical. 
			
			//Childbirth Library = roles are subscriber or visitor (paid/free), based on service status
			//PPL CRM = roles are many and represent progress and are mostly externally influenced. 
			//Certs LMS = roles based on service status, but are not heirarchical per se. 
			
		/*
			PERMISSIONS for CERTS LMS: 
			- if at least one service is issued, role is alumnus_active. 
			- if at least one service is active but not issued, role is student_active.
			- if at least one service is expired but not issued or active, role is alumnus.
			- if at least one service is inactive but not issued, acitve, or expired, role is student. 
			- if none of these are set, roles are revoked. (This should never happen. 
			
			$permissions = array(
				'issued' => 'alumnus_active',
				'active' => 'student_active',
				'expired' => 'alumnus',
				'inactive' => 'student',
			);
			
			
			//CODE FOR CERTS LMS
			
			foreach( $persmissions as $srvc_status => $role ){	
				if( in_array( $srvc_status, $services ) )
					return $role->set( $role );		
			}
		
			
			PERMISSIONS for PPL CRM: 
			- if role exists for Certs LMS, it trumps any other network role, except for resource (in PPL CRM). 
			- if no account on Cert LMS, and no local services have been added, look for roles on CBL. 
			
			
			
			PERMISSION for CBL: 
			- this is simply dependent upon the active/inactive status of their subscription to the library. 
			- 
			
		
		*/