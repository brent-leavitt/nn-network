<?php 

/*
nn_network\modl\Admin Menu 

Admin Menu  - Model Class for  Network Plugin
Last Updated 15 Jul 2019
-------------

	Note: THis was lifted from the Cert LMS as it can be applied across the newtork in principle, must be modified for general adaptation as a model class. 
	
	This is the file where we handle the functions that will create the admin menus for the plugin. 
*/

namespace nn_network\modl;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'AdminMenu' ) ){
	class AdminMenu{
		

		/*
		* 
		*/
		
		public $default_slugs = array(
			'dashboard' 	=> 'index.php',
			'posts' 		=> 'edit.php',
			'media' 		=> 'upload.php',
			'pages' 		=> 'edit.php?post_type=page',
			'comments' 		=> 'edit-comments.php',
			'appearance' 	=> 'themes.php',
			'plugins' 		=> 'plugins.php',
			'users' 		=> 'users.php',
			'tools' 		=> 'tools.php',
			'settings' 		=> 'options-general.php',
		
		);

				
		
		
		public function __construct( ){
			
			
		}
		
	/*
		Add Menu
		params: $slug = string, $pos = int
	*/	
		
		public function add_menu( $slug, $pos, $icon = "warning" ){
			
			if( !array_key_exists( $slug, $this->default_slugs ) ){
				
				//echo "Modl\Admin\NBAdminMenu.class <b>add_menu</b> function called. Slug: $slug and Pos: $pos <br />";
				
				//Setup all paramaters to runn the add_menu_page
					
				//menu_title
				$menu_title = ucwords( str_replace( '_', ' ', $slug ) );
				
				//page_title
				$page_title =  $menu_title. " Overview";
				
				//capability
				$capability = 'edit_users';
				
				//menu slug
				//Menu Slugs can be replaced with PHP files that represent the new page. 
				
				$menu_slug =  $slug;
				
				//callback 
				//  NULL 'cuz $menu_slug loads file. See Plugin Dev manual. 
				
				$callable = array( $this, 'menu_callable' );
				
				//menu icon
				$icon_url = 'dashicons-'.$icon;
				
				//position
				$position = $pos;
				
				
				add_menu_page(
					$page_title, 		//string
					$menu_title, 		//string
					$capability,		//string 
					$menu_slug, 		//string 
					$callable,			//callable
					$icon_url,			//string 
					$position			//int 
				);	
				
			}
			
		}
		
		
		
		public function add_submenu( $slug, $parent ){
			
			//echo "Modl\Admin\NBAdminMenu.class <b>add_submenu</b> function called. Slug: $slug and Parent: $parent <br />";
			
			
			//Setup all parameters to run the add_submeu_page
			
			//Buggy in so much as setting will disapper when default menus are removed and replaced by updated menus. 
			//Parent Slug
			$parent_slug = ( $this->default_slugs[ $parent ] ) ?? $parent ;
				
			//menu_title
			$menu_title = ucwords( str_replace( '_', ' ', $slug ) );
			
			//page_title
			$page_title =  $menu_title;
			
			//capability
			$capability = 'edit_users';//( in_array( $slug, $this->access['admin'] ) )? 'edit_users' : 'read_posts';
			
			//Is this necessary, or can we just assign to callable assuming that a sub page would never be a primary default page. 
			
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
			
			add_submenu_page(
				$parent_slug,	//string 
				$page_title,	//string 
				$menu_title,	//string 
				$capability,	//string 
				$menu_slug,		//string 
				$callable	//callable 
			);
			
			//NOT WORKING AS EXPECTED
			if( isset( $this->default_slugs[ $slug ] ) ){
				add_filter( 'parent_file', function($pf) use( $parent_slug ){
					
					//var_dump( $pf );
					
					return 'admin.php?page='.$parent_slug;
					
				}, 999 );
				
			}
		}
		
		
		
	/*
		param: $slug //menu slug
		reference: https://developer.wordpress.org/reference/functions/remove_menu_page/
	*/	
		
		public function remove_menu( $slug ){
			
			remove_menu_page( $this->default_slugs[ $slug ] );
			
		}
		
		
		public function menu_callable(){
			
			//'certificates-lms/view/admin/'.$slug.'.php';
			global $plugin_page, $title;
			
			echo "<div class='wrap'>";
			//<h1 id='wp-heading-inline'>$title</h1>
			
			$path = NN_PATH.'/nn_network/tmpl/admin/'.$plugin_page.'.php';
			
			//echo "The required Template Path is: $path . ";
			
			if (file_exists($path))
					require $path;
			
			echo"</div>";
			
			
		}
		
	}
}

?>