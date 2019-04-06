<?php

/*
Setting Class - Model Class for Setting for NBCS Network Plugin
Last Updated 16 Mar 2019
-------------

  Description: This sets up basic modeling parameters for the init\Setting class.

---

*/

namespace modl;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNSetting' ) ){
	class NNSetting{
		
		
		//Parameters
		
		//public $option_group = NN_TD; //
		
		
		
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
			
			
			
			$this->register();
		}
		
		
		/*
		Name: register
		Description: 
		
		*/
		
		public function register(){
			
			// register a new setting for NN_TD (constant defined in plugin init) page
			register_setting( NN_TD, 'wporg_setting_name' );
		 
			// register a new section in the NN_TD page
			add_settings_section(
				'wporg_settings_section',
				'WPOrg Settings Section',
				array( $this, 'wporg_settings_section_cb'),
				NN_TD
			);
		 
			// register a new field in the "wporg_settings_section" section, inside the NN_TD page
			add_settings_field(
				'wporg_settings_field',
				'WPOrg Setting',
				array( $this, 'wporg_settings_field_cb'),
				NN_TD,
				'wporg_settings_section',
				array(
					'label_for' => 'Field 1 Label',
					'class' => 'field_1_class'
				)
			);
			
			
			// Second register a new field in the "wporg_settings_section" section, inside the NN_TD page
			add_settings_field(
				'wporg_settings_field2',
				'WPOrg Setting 2',
				array( $this, 'wporg_settings_field_cb'),
				NN_TD,
				'wporg_settings_section'
			);

			/* 
			$option_name = 'fish_fries';
			
			$args = array(
				'type' 				=> 'integer', //'string', 'boolean', 'integer', and 'number'.
				'description' 		=> '', //(string)
				'sanitize_callback' => '', //(callable)
				'show_in_rest' 		=> false, //(bool)
				'default' 			=> 517, //(mixed/array)
			);
			
			$section = 'test_setting';
			
			register_setting( $this->option_group, $option_name, $args );
			
			add_settings_section( $section, 'Test Setting', null, 'general' );
			
			add_settings_field( 'test_field', 'Test Setting Field', array( $this, 'test_field_cb'), 'general', $section, $args );
			 */
		}

		 
		// section content cb
		public function wporg_settings_section_cb( $section_args )
		{	
			echo '<p>WPOrg Section Introduction.</p>';
			
			print_pre( $section_args );
		}
		 
		// field content cb
		public function wporg_settings_field_cb( $field_args )
		{
			
			print_pre( $field_args );
			
			// get the value of the setting we've registered with register_setting()
			$setting = get_option('wporg_setting_name');
			
			
			// output the field
			?>
			<input type="text" name="wporg_setting_name" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
			<?php
		}
		



		
		
		/*
		Name: test_field_cb
		Description: 
		
		*/
		
		public function test_field_cb(){
			
			
			$options = get_option( NN_TD );
			
			print_pre( $options );
			
			?>
			<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['custom_data'] ); ?>"
 name="<?php echo NN_TD;?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
 value="<?php echo ( !empty( $options[ 'fish_fries' ] ))? $options[ 'fish_fries' ]:""; ?>"  placeholder="Nothing set yet!" />
			<?php 
			 do_settings_sections( $section );
			
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