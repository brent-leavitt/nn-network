<?php

/*
Settings Class - Class for Settings for NBCS Network Plugin
Last Updated 16 Mar 2019
-------------

  Description: This sets up basic parameters for the Setting class for use with the WP Settings API.


---

*/

namespace init;


use \modl\NNSetting as Setting;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNSettings' ) ){
	class NNSettings{
		
		
		
		//Parameters
		
		public $__ = ''; //
		
		
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/
		
		public function __construct(){
			
			
			$this->init();
		}
		
		
		
		/*
		Name: init
		Description: 
		
		*/
		
		public function init(){
			
			//$this->admin_menu();
			//add_action( 'admin_menu', array( $this, 'add_submenu' ) );
			
		}		
			
		
		/*
		Name: build
		Description: 
		
		*/
		
		public function build(){
			$setting = new Setting();
			
		}	

		
		/*
		Name: callable
		Description: 
		
		*/
		
		public function callable(){
			
			echo "I have been loaded!";
		}		
	

		
		/*
		Name: __
		Description: 
		
		*/
		
		public function __(){
			
			
		}		
	
		
		
		
		
	}
}




?>