<?php
/*

nn_network\init\Roles

Roles - An Initializing Class used accross the network
Last Updated on 2 Oct 2019
-------------

  Description: Roles and Caps Management

  // TO-DO's: 
  //Add functions for activation and deactivate of plugin. 
	//Such as add plugin roles on activation. 
	//Remove plugin roles, and restore default roles on deactivation. 


*/

namespace nn_network\init;	

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'Roles' ) ){
	class Roles {

		
		// Properties

		//Roles to be added
		public $add = array();

		
		//These default roles are to be removed
		public $remove = array();
		
		//The default role that a new user receives. 
		public $default_role = '';
		
		
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/

		
		public function __construct(){
			
			//Remove default roles. 
			$this->remove_roles();
			
			//Add new roles as specified by specific plugin.
			$this->add_roles();
			
			//Set Default Role: 
			update_option( 'default_role', $this->default_role );

		}


		public function add_roles( ){
			
			global $admin_notices;
			
			//If no new roles to add, let's end here. 
			if( empty( $this->add ) ) return;
			
			foreach ($this->add as $role ){		
				
				$result = add_role( $role, ucfirst( $role ) );
				
				if ( null !== $result ) {
					$admin_notices .= " New role '$role' created! <br />";
				}
				else {
					$admin_notices .= "Oh... the '$role' role already exists. <br />";
				}
			}
			
		}


		public function remove_roles( ){
			
			if( empty( $this->remove ) ) return;
			
			$roles = array_reverse( $this->add ); 
			
			//All roles to be removed.
			$remove = array_merge( $roles, $this->remove );
			
			//Remove them one by one. 			
			foreach( $remove as $role ){
				if( get_role( $role ) ){
					  remove_role( $role );
				}
			}
			
		}


	
	}
	
}



?>