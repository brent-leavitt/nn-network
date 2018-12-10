<?php 
/*
// NB_NET CPT

Description: This class setups the model for the CPT created in the Network. It may be accessible to other plugins that also dependent upon this class. Possibly. 

*/
namespace modl;

class NNCPT{
	
	public CONST TD = NN_TD; //Text Domain
	
	public 	$cpt_args = array(
		'post_type' => '', 			//
		'post_name' => '', 			//
		'post_name_single' => '', 	//
		'post_item' => '', 			//
		'post_items' => '', 		//
		'description' => '', 		//
		'menu_pos' => 51,			//
		'menu_icon' => 'awards',	//
		'supports' => array( 'title', 'editor', 'page-attributes', 'revisions', 'comments' ),		//
		'cap_posts' => '',			//
		'cap_post' => '',			//
		'rewrite' => ''				//
		
	);

			
	
	
	public function __construct( $args ){
		
		//declare the custom post types here. 
		$this->set_post_type_args( $args );
		$this->register_cpt();
	}
	
	
	public function set_post_type_args( $args ){
		
		$a = $this->cpt_args; 
		
		//Get avaiable parameters sent at time of initialization. 
		foreach( $args as $key => $arg ){
			if( key_exists( $key, $a ) ){
				$a[ $key ] = $arg;
			}
		}
		
		//Then for any empty fields, fill in based on post_type value. 
		if( !empty( $a[ 'post_type' ] ) ){
			$p_name = $a[ 'post_type' ];
			
			//Set Post Name if Empty, Uppercase first letter and add s. 
			if( empty( $a[ 'post_name' ] ) )
				$a[ 'post_name' ] = ucfirst( $p_name ).'s';
				
			//Set Post Name Single, Uppercase first letter
			if( empty( $a[ 'post_name_single' ] ) )
				$a[ 'post_name_single' ] = ucfirst( $p_name );
			
			//Set Post Item, Uppercase first letter
			if( empty( $a[ 'post_item' ] ) )
				$a[ 'post_item' ] = ucfirst( $p_name );
			
			//Set Post Items, Uppercase first letter, make plural
			if( empty( $a[ 'post_items' ] ) )
				$a[ 'post_items' ] = ucfirst( $p_name ).'s';
			
			//Set Post Description
			if( empty( $a[ 'description' ] ) )
				$a[ 'description' ] =  ' This is the '.$p_name.' post type.';
			
			//Set rewrite
			if( empty( $a[ 'rewrite' ] ) )
				$a[ 'rewrite' ] =  $p_name.'s';
			
			//Set Capabilities Posts
			if( empty( $a[ 'cap_posts' ] ) )
				$a[ 'cap_posts' ] = $p_name.'s';
			
			//Set Capabilities Post
			if( empty( $a[ 'cap_post' ] ) )
				$a[ 'cap_post' ] = $p_name;
			
		}
				
		$this->cpt_args = $a; 
		
		
	}
	
	/* 
	public function get_post_type_args(){
		
		
	}
	
	 */
	 
	 
	 
	public function register_cpt( ){
		
		$args = $this->cpt_params();
		$post_type = NN_PREFIX.$this->cpt_args[ 'post_type' ]; 
		
		register_post_type( $post_type, $args );
		
	}
	
	
	/*
	*
	*
	* 	Params: $args = array( 
	*		'post_type' => (str)'', 		//Cardinal Name for the Post Type
	*		'post_name' => (str)'', 		//Display Name of the Post Type
	*		'post_name_single' => (str)'', 	//Singular Version of the Display Name
	*		'post_item' => (str)'', 		//Display Name of an individual Item of the Post Type
	*		'post_items' => (str)'', 		//Display Name of Items (plural) of the Post Type
	*	)
	*	Returns: $labels (array) 	
	*
	*/
	public function cpt_labels(){
		
		$a = $this->cpt_args;
		
		//build labels arguments array. 
		
		$labels = array(
			'name' => _x( $a[ 'post_name' ], 'post type general name', $this->TD),
			'singular_name' => _x( $a[ 'post_name_single' ], 'post type singular name', $this->TD),
			'add_new' => _x('Add New', $a[ 'post_type' ], $this->TD),
			'add_new_item' => __('Add New '.$a[ 'post_item' ], $this->TD),
			'edit_item' => __('Edit '.$a[ 'post_item' ], $this->TD),
			'new_item' => __('New '.$a[ 'post_item' ], $this->TD),
			'all_items' => __('All '.$a[ 'post_items' ], $this->TD),
			'view_item' => __('View '.$a[ 'post_item' ], $this->TD),
			'search_items' => __('Search '.$a[ 'post_items' ], $this->TD),
			'not_found' =>  __('No '.$a[ 'post_items' ].' found', $this->TD),
			'not_found_in_trash' => __('No '.$a[ 'post_items' ].' found in Trash', $this->TD), 
			'parent_item_colon' => '',
			'menu_name' => __( $a[ 'post_name' ], $this->TD)
		);
		
		return $labels;
	}
	
	
	/*
	*
	*
	* 	Params: $args = array( 
	*		'post_type' => (str)'', 		//Cardinal Name for the Post Type
	*		'description' => (str)'', 		//
	*		'menu_pos' => (int)'', 			//Menu Position
	*		'menu_icon' => (str)'', 		//Sub-string from dashicons set. 'dashicons-' is already included
	*		'supports' => (arr)'', 			//Editor items included with this post type
	*		'rewrite' => (str)'', 			//Rewrite Slug
	*		'labels' => (arr)'', 			//an array of label variables
	*		
	*	)
	*	Returns: $post_type_args (array) 	
	*
	*/
	
	public function cpt_params( ){
		
		$a = $this->cpt_args;
		
		$labels = $this->cpt_labels();
		
		$params = array(
			'labels' => $labels,
			'description' => $a[ 'description' ],
			'public' => true ,
			'publicly_queryable' => true,
			'query_var' => true,
			'show_ui' => true,
			'show_in_menu' => false, //Toggle here to hide from main menu. 
			'has_archive' => true, 
			'hierarchical' => true,
			'menu_position' => $a[ 'menu_pos' ],
			'menu_icon' => 'dashicons-'. $a[ 'menu_icon' ],
			'supports' => $a[ 'supports' ],  
			'capability_type'=>'post',
			'capabilities' => array(
				'publish_posts' => 'publish_'.$a[ 'cap_posts' ],
				'edit_posts' => 'edit_'.$a[ 'cap_posts' ],
				'edit_others_posts' => 'edit_others_'.$a[ 'cap_posts' ],
				'delete_posts' => 'delete_'.$a[ 'cap_posts' ],
				'delete_others_posts' => 'delete_others_'.$a[ 'cap_posts' ],
				'read_private_posts' => 'read_private_'.$a[ 'cap_posts' ],
				'edit_post' => 'edit_'.$a[ 'cap_post' ],
				'delete_post' => 'delete_'.$a[ 'cap_post' ],
				'read_post' => 'read_'.$a[ 'cap_post' ],
				'read' => 'read_'.$a[ 'cap_posts' ],
				'edit_private_posts' => 'edit_private_'.$a[ 'cap_posts' ],
				'edit_published_posts' => 'edit_published_'.$a[ 'cap_posts' ],
				'delete_published_posts' => 'delete_published_'.$a[ 'cap_posts' ],
				'delete_private_posts' => 'delete_private_'.$a[ 'cap_posts' ]
			), 
			'map_meta_cap'=> true, 
			'rewrite' => array( 'slug' => $a[ 'rewrite' ] )
		);
		
		return $params;
		
	}
	
}