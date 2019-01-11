<?php 
/*
// NB_NET CPT

Description: This class initializes CPT that are declared at a network level. 

The Network has only one network wide CPT: Guides (AKA Enrollment steps, or service actions.) 


Planning: 
	Guides are linked to Enrollment tokens (multiples) and are set from within the meta for the specific guide. Will call the options table from database and allow for the selection of enrollment tokens to apply to each guide. 


*/

namespace init;

use \modl\NNCPT as CPT;

class NNCPT{
	
	public $post_types = array(
		'nnguide',
	);
	
	public function __construct( ){
		
		$this->setup();
	}
	
	public function setup(){
		//Define specific CPTs for use across the network. 

		//Guide
		$guide	= new CPT( array( 
			'post_type'=>'guide',
			'description'=>'enrollment actions or services used for assigning behaviours to tokens',
			/* 'menu_pos'=>53,*/
			'menu_icon'=>'portfolio', 
			'hierarchical' => false,
			'exclude_from_search' => true,
			'supports' => array( 
				'title', 
				'editor', 
				'comments', 
				'author', 
				'revisions' 
			)
		) );
		
		add_filter( 'rwmb_meta_boxes', array( $this, 'set_meta_boxes') );
	}
	
	
	private function set_meta_boxes( $meta_boxes ) {
		$prefix = NN_PREFIX;
		
		//For Guides(
		//(Replace below code when ready)
		/* [OLD]
			$meta_boxes[] = array(
			'id' => 'crm_posts',
			'title' => esc_html__( 'User', 'nbcs-crm' ),
			'post_types' => array( 'post' ),
			'context' => 'side',
			'priority' => 'high',
			'autosave' => true,
			'fields' => array(
				array(
					'id' => $prefix . 'user',
					'type' => 'user',
					'field_type' => 'select_advanced',
				),
				array(
					'id' => $prefix . 'contact',
					'name' => esc_html__( 'Type of Contact', 'nbcs-crm' ),
					'type' => 'select',
					'placeholder' => esc_html__( 'Select an Item', 'nbcs-crm' ),
					'options' => array(
						'form_inquiry' => 'Form Inquiry',
						'chat' => 'Chat',
						'email_sent' => 'Email Sent',
						'email_received' => 'Email Received',
						'phone_sent' => 'Phone Call Made',
						'phone_received' => 'Phone Call Received',
						'admin_note' => 'Admin Note',
						'automated' => 'Automated',
						
					),
				),
				
			),
		); */
		

		return $meta_boxes;
	}

	
	public function remove(){
		
		$types = $this->post_types;
		foreach( $types as $type )
			unregister_post_type( $type );
		
	}
}