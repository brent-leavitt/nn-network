<?PHP 
/*
Notice Template - Sub Core Class Psuedo Code for NBCS Network Plugin
Last Updated 218 Oct 2018
-------------

Desription: This handles the retrieving of notice templates, building them, and returning them to the Notice class for use there. 


	usage: 
		
		$template = new Template( '[template_slug]' );
		
		if( !$tempalte->error ){
			
			//
			$content = $template->build( $source );
			
		} else {
			
			//template not found. 
			
		}
		
---
*/
namespace core\sub;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNNoticeTemplate' ) ){

	class NNNoticeTemplate{
		
		//Properties
		public 
			$slug = '',
			$vars = array(),
			$source = array(),
			$data = array(),
			$content = '',
			$error = false;
		
		
		//Methods
		
			
	/*
		Name: __construct
		Description: 
	*/	
		
		public function __construct( $slug ){
			
			$this->init( $slug );
		}	
				
		
	/*
		Name: init
		Description: We need to know if the template. Sets Error to true if no template is found. 
	*/	
		
		private function init( $slug ){
			
			$this->slug = ( $this->retrieve( $slug ) )? $slug : '' ;
			
			$this->error = ( !empty( $this->slug ) && !empty( $this->content ) )? false : true ; 
		}	
		
		
			
	/*
		Name: retrieve
		Description: Checks to see if the template being requested is available. 
	*/	

		public function retrieve( $slug ){
			
			//Retrieve Post Type by Slug
			if ( $post = get_page_by_path( $slug , OBJECT, 'NBNoticeTempalte' ) )
				$this->content = $post->post_content;

			return ( !empty( $this->content ) )? true : false; //true or false; 
			
		}		
		
		
	/*
		Name: build
		Description: Builds the actual message to be sent to the user by replacing the template variables with source information, then returns it back to the main class for use. 
	*/	
				
		
		public function build( $source ){
			
			//
			
		}	

		
	/*
		Name: 
		Description: 
	*/	
				
		
		public function get_template_vars( $content ){
			
			$pattern = get_shortcode_regex();
			
			preg_match('/'.$pattern.'/s', $content, $matches);
			/* NOT SURE WHAT THIS DOES TO HELP ME?
			if (is_array($matches) && $matches[2] == 'the_shortcode_name') {
			   // $shortcode = $matches[0];
			   // echo do_shortcode($shortcode);
			}	
			*/
			
			// Are there default values assigned to the template? 
				// There could be. 
			
			return $vars; //a list of the template vars to be matched. 
		}		
		
	/*
		Name: pair_template_vars
		Description: 
	*/	
				
		
		public function pair_template_vars( $vars, $data ){
			
			//Assign data to $vars. 
			$data_set = [];
			
			foreach( $vars as $key => $var ){
				
				if( !empty( $data[ $key ] ) ){
					//Set 
					$data_set[ $key ] = $data[ $key ];				
				} elseif( !empty( $vars[ $key ] ) ){
					//if source not set, set the default value. 
					$data_set[ $key ] = $vars[ $key ];
				} else {
					//No source, no default, send back empty. 
					$data_set[ $key ] = '';
				}
				
				//Do we return empty data or no data? 
				//Are there default values? 
				
			}
			
			return $data_set; //array with data paired to needed template vars.  
		}	
		
		
		
		
		
	}
}
?>