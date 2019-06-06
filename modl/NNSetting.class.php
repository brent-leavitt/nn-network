<?php

/*
Setting Class - Model Class for Setting for NBCS Network Plugin
Last Updated 16 Mar 2019
-------------

  Description: This sets up basic modeling parameters for the init\Setting class.

  This is the setting model class
  - 
  
  
---

*/

namespace modl;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNSetting' ) ){
	class NNSetting{
		
		
		//Parameters
		
		
		public $opt_name = '',
		$opt_args = array(),
		$opt_vals = array();
		
		//public $option_group = NN_TD; //
		
		
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/
		
		public function __construct( $opt_name, $args ){
			
			$this->opt_name = $opt_name;
			$this->opt_args = $args;
			$this->init();
			
		}
		
		/*
		Name: init
		Description: 
		
		*/
		
		public function init(){
			
			$this->opt_vals = get_option( $this->opt_name );
			
			if( false == $this->opt_vals ) {	
				add_option( $this->opt_name, '', '', 'no' /* , apply_filters( '', _() ) */ ); //add default values. 
			} // end if
			
			$this->register();
		 
		}

		/*
		Name: init
		Description: 
		
		*/
		
		public function register(){
			
			
			// register a new setting for NN_TD (constant defined in plugin init) page
			register_setting( $this->opt_name, $this->opt_name );
			
			//ep( "Option Name is {$this->opt_name}" );
			
			
			foreach( $this->opt_args as /* $section_key => */ $section_args ){			
				//print_pre( $section_args );
			

				$this->add_section( $section_args );
				
				foreach( $section_args[ 'fields' ] as $field ){
					$field_args = $this->prep_field_args( $section_args, $field );
					$this->add_field( $field_args );  
				}
					
			}
		}

		 
		/*
		Name: string_section_cb
		Description: 
		
		*/
		
		public function string_section_cb( $args )
		{	
			echo '<p>Section Introduction.</p>';
			
			//print_pre( $args );
			
			
		}
		 
		/*
		Name: text_field_cb
		Description: This loads the text field of the form as called from add_settings_field() 
		params: 
			'value', //the value stored in the option table.
			'name',  //the name of the value
			'label', //label for field
			'class'  //? needed?
		
		*/
		
		public function text_field_cb( $args ){
			
			//print_pre( $args );
			
			extract( $args );
			// get the value of the setting we've registered with register_setting()
			$input_name = $this->opt_name.'['.$name.']';
			
			// output the field
			?>
			<input type="text" name="<?php echo $input_name; ?>" value="<?php echo !empty( $value ) ? esc_attr( $value ) : ''; ?>">
			<?php
		}
		



		
				
		
		/*
		Name: add_section
		Description: 
		
		*/
		
		public function add_section( $args ){
			
		/* 	ep( 'This is the add_section function: ' );
			print_pre( $args ); */
			
			//
			extract( $args );
			
			// register a new section in the NN_TD page
			add_settings_section(
				$id.'_section',
				$title.' Section',
				array( $this, $cb_type.'_section_cb'),
				$page
			);
			
		}
		
		
				
		
		/*
		Name: add_field
		Description: 
		
		*/
		
		public function add_field( $arg ){
			
			
			//$id, $title, $callback, $page, $section, $args
			extract( $arg );
			
			// register a new field in the "wporg_settings_section" section, inside the NN_TD page
			add_settings_field(
				$id,
				$title,
				array( $this, $cb ), //<- THIS CALL BACK NEEDS TO BE UNIQUE FOR EVERY FIELD, unless you pass a unique $arg value. 
				$page,
				$section,
				$args
			);
			
			
		}
		
		
		
		/*
		Name: prep_field_args
		Description: Preps field arguments for use in adding a field. 
		Returns: array()
		
		*/
		
		public function prep_field_args( $section, $field ){
			
			$field_id = $section[ 'id' ].'_'.$field[ 'id' ];
			$value = ( !empty( $this->opt_vals[ $field_id ] ) )?  $this->opt_vals[ $field_id ] : '' ;
			
			$args = [
				'id' => $field_id ,
				'title' => $field[ 'title' ],
				'cb' => $field[ 'type' ].'_field_cb',
				'section' => $section[ 'id' ].'_section',
				'page' => $section[ 'page' ],
				'args' => array(
					'name' => $field_id,
					'value' => $value, //the value stored in the option table.
					'label_for'	=> '', //label for field
					'class' => ''  
				)
			];
			
			return $args;
			
		}
		
		
		/*
		Name: default_options
		Description: ***Incomplete ***
		
		*/
		
		/**
		 * Provides default values for the Input Options.
		 */
		public function default_options() {
			
			$defaults = array(
				'' => '',	
			);
			
			return apply_filters( 'default_options', $defaults );
			
		} // end sandbox_theme_default_input_options
		
		
		/*
		Name: __
		Description: 
		
		*/
		
		public function __(){
			
			
			
			
		}
		
		
		
		
	}
}




?>