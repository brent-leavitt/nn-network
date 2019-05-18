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
		
		
		public  $tabs = array(
			array(
				'tab_id' => 'payment_creds',
				'tab_name' => 'Payments',
				'sections' => array(
					'stripe',
					'paypal'
					
				)
			),
			array(
				'tab_id' => 'cashier_vars',
				'tab_name' => 'Cashier Page',
				'sections' => array(
					'cashier'				
				)
			)
		);
		
		//Sections
		public $paypal_section = array(
			'section_id' => 'paypal',
			'section_name' => 'PayPal',
			'cb_type' => 'string',
			'cb_string' =>'Please enter your PayPal credentials to use PayPal as a valid payment option.', //Callback used to render the description of the section. 
			'fields' => array(
				array(
					0 => 	'sandbox_key', 				//
					1 =>	'text', 					//Callback based on field's input type.
					2 =>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
				array(
					0 => 	'prod_key', 				//
					1 =>	'text', 					//Callback based on field's input type.
					2 =>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
			),
		);
		
		public $stripe_section = array(
			'section_id' => 'stripe',
			'section_name' => 'Stripe',
			'cb_type' => 'string',
			'cb_string' =>'Please enter your Stripe credentials to use Stripe as a valid payment option.', //Callback used to render the description of the section. 
			'fields' => array(
				array(
					0 => 	'sandbox_key', 				//
					1 =>	'text', 					//Callback based on field's input type.
					2 =>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
				array(
					0 => 	'prod_key', 			 	//
					1 =>	'text', 					//Callback based on field's input type.
					2 =>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
			),
		);
		
		public $cashier_section = array(
			'section_id' => 'cashier',
			'section_name' => 'Cashier Options',
			'cb_type' => 'string',
			'cb_string' =>'This is a text string.', //Callback used to render the description of the section. 
			'fields' => array(
				array(
					0 => 	'field 1', 		//
					1 =>	'text', 				//Callback based on field's input type.
					2 =>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
				),
				array(
					0 => 	'field 2', 		//
					1 =>	'text', 				//Callback based on field's input type.
					2 =>	'field description name.'	// This is sent to the callback function as an $arg variable and used as $arg[0]
					
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
			$setting = new Setting();
		}		
			
		
		/*
		Name: render
		Description: 
		
		*/
		
		public function render(){
			
			$active_tab = $this->get_active_tab();
			$tab_nav = $this->tab_navigation( $active_tab );
			$form = $this->form( $active_tab );
			
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
			
			foreach( $this->tabs as $tab ){
				
				$output .='<a href="?page=settings&tab='.$tab[ 'tab_id' ].'" class="nav-tab ';
				$output .= $active_tab == $tab[ 'tab_id' ] ? 'nav-tab-active' : '';
				$output .= '">'.$tab[ 'tab_name' ].'</a>';
			}
			
			$output .= "</h2>";
			
			return $output;
		}		
	
			
		/*
		Name: form
		Description: 
		
		*/
		
		public function form( $active_tab ){
			$output = '<form method="post" action="options.php">';
			
			ob_start();
			
			foreach( $this->tabs as $tab ){
				if( $tab[ 'tab_id' ] == $active_tab ){
					foreach( $tab[ 'sections' ] as $section ){
						$section_prop = $section.'_section';
						$section_id = NN_PREFIX.$this->$section_prop[ 'section_id' ].'_options';
						
						echo 'The Section ID is: '. $section_id.'<br />';
						
						settings_fields( NN_TD );
						do_settings_sections( NN_TD  );
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