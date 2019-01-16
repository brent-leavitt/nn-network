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
			$subject_vars = array(), //Varable extracted from subject
			$content_vars = array(), //Varable extracted from content
			$source, //source data
			$subject = '',
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
		Description: We need to know if the template exists. Sets Error to true if no template is found. 
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
			if ( $post = get_page_by_path( $slug , OBJECT, 'nnnoticetemplate' ) ){
				$this->content = $post->post_content;
				$this->subject = $post->post_title;
			}
			return ( !empty( $this->content ) )? true : false; //true or false; 
			
		}		

							
	/*
		Name: prepare
		Description: This prepares the Notice Template by extracting available template variables from the content of the template and storing them in the 
	*/	
				
		
		public function prepare( $data ){
			
			//dump( __LINE__, __METHOD__, $data );
			$this->source = $data;
			
			
			//Extract Subject Variables
			$this->subject_vars = $this->get_template_vars( $this->subject );
			
			//Build Subject Line
			$this->build( 'subject' );
			
			
			//Extract Content Variables
			$this->content_vars = $this->get_template_vars( $this->content );
			
			
			//Build Content with sent data
			$this->build( 'content' );
			
			dump( __LINE__, __METHOD__, get_object_vars( $this ) );
		}	
		
		
	/*
		Name: build
		Description: Builds the actual message to be sent to the user by replacing the template variables with source information, then returns it back to the main class for use. 
	*/	
				
		
		public function build( $what ){
			
			$out = $this->$what;
			
			$vars = $this->get_template_vars( $this->$what );
			
			foreach( $vars as $value ){
				$search = "[nn_m $value]"; 
				$source = $this->source;
				$replace = $source[ $value ]; //WHY DOESN'T THIS WORK. 
				
				ep( "The value of VALUE is: $value" );
				ep( "The value of replace is: $replace" );
				dump( __LINE__, __METHOD__, $source );
				
				$out = str_replace( $search, $replace, $out );
				
			}
			
			dump( __LINE__, __METHOD__, $out );
			
			$this->$what = $out;
			
		}	
		

		
	/*
		Name: get_template_vars
		Description: 
	*/	
				
		
		public function get_template_vars( $content ){
			
			$pattern = get_shortcode_regex();
			
			preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches );

			$out = array();
			
			if( isset( $matches[2] ) ){
				foreach( $matches[2] as $key => $value ){
					if( 'nn_m' === $value )
						$out[] = $matches[3][$key];  
				}
			}
			
			//dump( __LINE__, __METHOD__, $out);
			
			return $out;
			
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
		
					
	/*
		Name: get_subject 
		Description: Get the notice template Subject
	*/	
				
		
		public function get_subject(){
			
			
			return "Filler Subject Line Goes Here - New Beginnings Doula Training";
		}	
							
	/*
		Name: get_content
		Description: Gets the content of the subject. 
		
	*/	
				
		
		public function get_content(){
			
			
			
			return "Filler content goes here. I'm getting tired of this code, or just tired in general.";
		}	

			
		
									
	/*
		Name: 
		Description: 
	*/	
				
		
		public function __(){
			
			
		}	
			
		
		
		
	}
}
?>