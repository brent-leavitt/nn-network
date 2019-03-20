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
			
			//dump( __LINE__, __METHOD__, array( 'empty array' ) );
			//add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			
			
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
		Name: admin_menu
		Description: 
		
		*/
		
	
		public function admin_menu(){
		
			
			//Setup all parameters to run the add_submeu_page
			$slug = 'nn_network';
			
			//Parent Slug
			$parent_slug = 'options-general.php';
				
			//menu_title
			$menu_title = ucwords( str_replace( '_', ' ', $slug ) );
			
			//page_title
			$page_title =  $menu_title;
			
			//capability
			$capability = 'manage_options';//( in_array( $slug, $this->access['admin'] ) )? 'edit_users' : 'read_posts';
			
			
			//Menu Slugs can be replaced with PHP files that represent the new page. 
			
			//$menu_slug =  'certificates-lms/view/admin/'.$parent.'/'.$slug.'.php';
			if( !isset( $this->default_slugs[ $slug ] ) ){
				//menu slug
				$menu_slug = $slug;
				//callback 
				$callable = array( $this, 'menu_callable' );
			}else{
				//menu slug
				$menu_slug = $this->default_slugs[ $slug ];
				//callable NULL 'cuz $menu_slug loads file. See Plugin Dev manual. 
				$callable = null;
			
				
			}	
			
			
			add_options_page(
				$page_title,	//string 
				'NN Network',
				'manage_options',
				$slug,
				array(
					$this,
					'callable'
				)
			);
			
			
			/* add_submenu_page(
				$parent_slug,	//string 
				
				$menu_title,	//string 
				$capability,	//string 
				$menu_slug,		//string 
				$callable	//callable 
			);
			 */
			//NOT WORKING AS EXPECTED
			/* if( isset( $this->default_slugs[ $slug ] ) ){
				add_filter( 'parent_file', function($pf) use( $parent_slug ){
					
					//var_dump( $pf );
					
					return 'admin.php?page='.$parent_slug;
					
				}, 999 );
				
			} */
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