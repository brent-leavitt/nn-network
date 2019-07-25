<?php 
/*
NN Network (Base File) Psuedo Code
5 October 2018
-------------------

// Description : This is the plugin root file for network. 

/*
Plugin Name: NN Network
Plugin URI: https://tech.trainingdoulas.com//
Description: Plugin that encompasses all functions, classes and activities that are needed across the entire NN Network
Version: 1.0
Author: Brent Leavitt
Author URI: https://tech.trainingdoulas.com/
License: GPLv2

*/
namespace nn_network;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !defined( 'NN_PATH' ) )
	define( 'NN_PATH', plugin_dir_path( __FILE__ )  );

if( !defined( 'CRON_DEBUG' ) )
	define( 'CRON_DEBUG', false ); //

if( !defined( 'NN_DEV' ) )
	define( 'NN_DEV', true ); //Used mostly for determining which set of credentials to call. 


if( !defined( 'NN_PREFIX' ) )
	define( 'NN_PREFIX', 'nn_' ); 	//Prefix to use throughout the plugin. 

if( !defined( 'NN_BASESITE' ) )
	define( 'NN_BASESITE', 2 ); 	//The homebase site ID for the network. This maybe different than the network's primary domain, based on how it was first set up. 				

if( !defined( 'NN_TD' ) )
	define( 'NN_TD', 'nn_network' );	//Plugin text domain. 

if( !class_exists( 'NN_Network' ) ){
	class NN_Network{

		public function __construct(){			
			
			$this->autoload_classes();
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ));
			add_action( 'admin_menu', array( $this, 'admin_menus' ));
			//add_action( 'init' ); 
		}
		
		public function init(){
			
			
			if( NN_DEV )
				$dev = new misc\Dev();
			
		 	
			$listener	 = new init\Listener();		//Add Query Vars Listeners
			$shortcodes	 = new init\ShortCodes();		//Shortcodes	
			/*	$email_settings = new init\Email();		//Email Settings
				 */
			 
			//setup Custom Post Types
			$this->set_cpts();
			
			//login controls
			$login = new init\Login();
			
			//register controls
			$reglite = new init\RegisterLite();
			
			//Crons schedule.
			$cron = new init\Cron();
			$cron->schedule();
			
			//setup activation and deactivation hooks
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
			
			//version control?

		}
		
		public function admin_init(){
			$settings = new init\Settings(); //Add a settings page
			$settings->init();
		}
		
		
		public function admin_menus(){
			
			//Define Menus to add. 
			$args = array(
			/* 		'education' => array(
						'current_submissions',
						'my_students',
						'assignments',
						'coaching_schedule',
						'certificate_generator',
					),
			*/
				'settings' => array( NN_TD )
			);
			
			$menus = new init\AdminMenu( $args, 'add' ); //Add a settings page
			
		}
		
		//Add Custom Post Types
		public function set_cpts(){
			
			$cpts = new init\CPT();
			$cpts->setup();
			
		}

		//Remove Custom Post Types
		public function remove_cpts(){
		
			$cpts = new init\CPT();
			$cpts->remove();
		
		}
		
		public function set_caps(){
			$caps = new init\CPT();
			$caps->set_caps();
			
		}
		
		
		public function set_crons(){
			$cron = new init\Cron();
			$cron->init();
			
		}		
		public function stop_crons(){
			$cron = new init\Cron();
			$cron->remove_cron();
			
		}
		
		
		
		private function autoload_classes( ){
			
			//This loads all classes, as needed, automatically! Slick!
			
			spl_autoload_register( function( $class ){
				
				$path = substr( str_replace( '\\', '/', $class), 0 );
				$path = __DIR__ .'/'. $path . '.class.php';
				
				if (file_exists($path))
					require $path;
				
			} );
		}
		
		
		/* 
		
		//Are there any parameters that need to be setup on register or unregister hooks? 
		What about options tables for enrollment tokens? Or is this something that should be setup dynamically via an interface, per site? 
			- Maybe. 
		
		 */
		
		
		
			
		public function activation(){
		
			
			//Setup Custom Post Types
			$this->set_cpts();
					
			flush_rewrite_rules();	//Clear the permalinks after CPTs have been registered
		
			
		
		
			//Register settings
			
			//register_setting( $option_group, $option_name, $args = array() )
			
			
			//Add Tokens to all sites. 
			/* (HOLD WHILE IN LOCAL DEV)
			$tokens = new init\Token();
			$tokens->setup();
			*/
			
			// Add Custom Roles. 	
			//$this->set_roles();
		
		
			//https://developer.wordpress.org/plugins/users/roles-and-capabilities/
			//https://www.ibenic.com/manage-wordpress-user-roles-capabilities-code/	
			//https://wordpress.stackexchange.com/questions/108338/capabilities-and-custom-post-types
			//Custom Caps need to be given to each user role for each CPT that has been added. 
			//$this->set_caps();
			
			
			
			//Setup a configuration process that explains how to use the plugin. 
		}
		
		
		public function deactivation(){
			
			//Stop Network Cron Jobs
			$this->stop_crons();
			
			//Clean up Post Types being removed. 
			$this->remove_cpts(); 	// unregister the post type, so the rules are no longer in memory
			flush_rewrite_rules();	// clear the permalinks to remove our post type's rules from the database
			
			//Remove tokens from all sites. 
			$tokens = new init\Token();
			//$tokens->remove();
			
			
			/* //See Activiation: 
			//Remove caps given to all roles for plugin specific CPTs. 
			$this->remove_caps();
			$this->remove_roles(); */
			
			//Setup a configuration process that explains how to use the plugin. 
		}
		
		

		
		
	}//End NBCS_NETWORK class
}

//Network Developer Tools
include_once('func/DevTools.php');


//Network Functions 
require_once('func/Functions.php');

//Setup Cron Jobs for Network
//require_once('func/nbcs_net_crons.php');

//User Permissions Across Network
//require_once('func/nbcs_net_permissions.php');

$the_network = new NN_Network();

//include_once( 'dev_space.php' );
?>
