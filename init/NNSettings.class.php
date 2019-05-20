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
		
		
		public $option_name = '';
		
		public  $tabs = array(
			'payment_creds' => array(
				'title' => 'Payments',
				'sections' => array(
					'stripe',
					'paypal'
					
				)
			),
			'cashier_vars' => array(
				'title' => 'Cashier Page',
				'sections' => array(
					'cashier'				
				)
			)
		);
		
		//Sections
		public $paypal_section = array(
			'id' => 'paypal',
			'title' => 'PayPal',
			'page' => NN_TD,
			'cb_type' => 'string',
			'cb_string' =>'Please enter your PayPal credentials to use PayPal as a valid payment option.', //Callback used to render the description of the section. 
			'fields' => array(
				array(
					'id' 			=> 	'sandbox_key', 				//
					'title'			=> 	'Sandbox Key',							//Field name
					'type'			=>	'text', 					//Callback based on field's input type.
					'description' 	=>	'field description name.',	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
					
				),
				array(
					'id'			=> 	'prod_key', 				//
					'title'			=> 	'Production Key',							//Field name
					'type'			=>	'text', 					//Callback based on field's input type.
					'description'	=>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
			),
		);
		
		public $stripe_section = array(
			'id' => 'stripe',
			'title' => 'Stripe',
			'page' => NN_TD,
			'cb_type' => 'string',
			'cb_string' =>'Please enter your Stripe credentials to use Stripe as a valid payment option.', //Callback used to render the description of the section. 
			'fields' => array(
				array(
					'id' 			=> 	'sandbox_key', 				//
					'title'			=> 	'Sandbox Key',				//Field name
					'type'			=>	'text', 					//Callback based on field's input type.
					'description' 	=>	'field description name.',	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
				array(
					'id' 			=> 	'prod_key', 				//
					'title'			=> 	'Production Key',			//Field name
					'type'			=>	'text', 					//Callback based on field's input type.
					'description' 	=>	'field description name.',	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
			),
		);
		
		public $cashier_section = array(
			'id' => 'cashier',
			'title' => 'Cashier Options',
			'page' => NN_TD,
			'cb_type' => 'string',
			'cb_string' =>'This is a text string.', //Callback used to render the description of the section. 
			'fields' => array(
				array(
					'id' 			=> 	'option_1', 						//
					'title'			=> 	'Option 1',							//Field name
					'type'			=>	'text', 					//Callback based on field's input type.
					'description' 	=>	'field description name.',	// This is sent to the callback function 
					
				),
				array(
					'id' 			=> 	'option_2', 						//
					'title'			=> 	'Option 2',							//Field name
					'type'			=>	'text', 					//Callback based on field's input type.
					'description' 	=>	'field description name.',	// This is sent to the callback function 

				),
			),
		);
		
		
		
		
		//
		
		
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/
		
		public function __construct(){

	
		}
		
		
		
		/*
		Name: init
		Description: TO_DO - Work on Model Code. 
		
		*/
		
		public function init(){
			
			//$this->admin_menu();
			//add_action( 'admin_menu', array( $this, 'add_submenu' ) );
			
			
			foreach( $this->tabs as $tab => $tab_arr ){
				
				$option_name = NN_TD.'_'.$tab; 
				
				$sections = $tab_arr[ 'sections' ];
				
				$args = array();
			
				foreach( $sections as $section ){
					$section_name =  $section.'_section';
					$args[ $section_name ] = $this->$section_name;
				}
				
				$setting = new Setting( $option_name, $args );
				//print_pre( $setting );
			}			
		}		
			
		
		/*
		Name: render
		Description: 
		
		*/
		
		public function render(){
			
			$active_tab = $this->get_active_tab();
			
			$this->option_name = NN_TD.'_'.$active_tab;
			//maybe set a property for active_tab		
			
			$tab_nav = $this->tab_navigation( $active_tab );
			$form = $this->tab_form( $active_tab );
			
			return $tab_nav . $form;
		}	

	

		
		/*
		Name: get_active_tab
		Description: 
		
		*/
		
		public function get_active_tab( $active_tab = '' ){
			
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'cashier_vars' ) {
				$active_tab = 'cashier_vars';
			} else  {
				$active_tab = 'payment_creds'; //default is first setting in array. 
			} // end if/else 
				
			return $active_tab;
		}		
	
		
		
		/*
		Name: tab_navigation
		Description: 
		
		*/
		
		public function tab_navigation( $active_tab ){
			
			$output ='<h2 class="nav-tab-wrapper">';
			
			foreach( $this->tabs as $tab => $arr ){
				
				$output .='<a href="?page=settings&tab='.$tab.'" class="nav-tab ';
				$output .= $active_tab == $tab ? 'nav-tab-active' : '';
				$output .= '">'.$arr[ 'title' ].'</a>';
			}
			
			$output .= "</h2>";
			
			return $output;
		}		
	
			
		/*
		Name: tab_form
		Description: 
		
		*/
		
		public function tab_form( $active_tab ){
			$output = '<form method="post" action="options.php">';
			
			ob_start();
			
			foreach(  $this->tabs as $tab => $arr ){
				if( $tab == $active_tab ){
					foreach( $arr[ 'sections' ] as $section ){
						$section_prop = $section.'_section';
						$section_id = $this->$section_prop[ 'id' ].'_section';
						
						echo 'The Section ID is: '. $section_id.'<br />';
						echo 'Option Name: '. $this->option_name. '<br />';
						settings_fields( $this->option_name.'_group' );
						do_settings_sections( $this->option_name.'_group' );
					}
							
				}
			}
			/* if( $active_tab == 'display_options' ) {
			
				settings_fields( 'sandbox_theme_display_options' );
				do_settings_sections( 'sandbox_theme_display_options' );
				
			} elseif( $active_tab == 'social_options' ) {
			
				settings_fields( 'sandbox_theme_social_options' );
				do_settings_sections( 'sandbox_theme_social_options' );
				
			} else {
			
				settings_fields( 'sandbox_theme_input_examples' );
				do_settings_sections( 'sandbox_theme_input_examples' );
				
			} // end if/else */
				
			$output .= ob_get_contents();
			ob_end_clean();
			
			$output .=	get_submit_button();
			
			$output .= '</form>';
			
			return $output;
			
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