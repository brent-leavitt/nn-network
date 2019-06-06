<?php

/*Plugin Functions

-----

Description: class independent list of functions
------


*/

/*
	Name: nn_format_data
	Description: This takes data from different third-party sources and returns a universally formatted array of transaction data for use in the NN_Actions core functionality and elsewhere throughout the plugin. 
*/	
	
function nn_format_data( $data, $source = '' ){
	
	$formatter = new misc\NNDataFormat( $data );
	
	$formatted = array();
	
	switch( $source ){
		
		case 'stripe':
				
			$formatted = $formatter->stripe();
			break;
			
		default:
			
			$formatted = $formatter->basic();
			break;
		
	}
	
	return ( !empty( $formatted ) )? $formatted : NULL ;
}



/*
	Name: nn_do_core_action
	Description: This initiates the backend core processes 
	Params: (string) $action, (array) $data
*/

function nn_do_core_action( $action, $data ){
	
	
	$actor = new core\NNAction( $data );
	
	$actor->$action();
	
	//incomplete
	
	return true;
	
}

/*
	Name: nn_switch_to_base_blog
	Description: This checks if a base site value is set and switches to it if 
*/

function nn_switch_to_base_blog(){
	
	if( defined( 'NN_BASESITE' ) && is_multisite() )
				switch_to_blog( NN_BASESITE );
}

/*
	Name: nn_return_from_base_blog
	Description: This checks if a base site value is set and switches back to the source blog. 
*/

function nn_return_from_base_blog(){
	
	if( defined( 'NN_BASESITE' ) && is_multisite() )
				restore_current_blog();
			
}


//Credit: https://wppeople.net/blog/a-simple-two-way-function-to-encrypt-or-decrypt-a-string/
function nn_crypt( $string, $action = 'e' ) {
    
	// you may change these values to your own
    $secret_key = 'nn_secret_key';
    $secret_iv = 'nn_secret_iv';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output =  openssl_encrypt( $string, $encrypt_method, $key, 0, $iv  );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( $string, $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}

/*
	Name: nn_sanitize_text_array
	Description: This cleans out all incoming form that is exclusively string data to avoid anything malicious on the backend. 
*/
function nn_sanitize_text_array( $in ){
	
	$out = array();
	foreach( $in as $in_key => $in_val ){
		
		$out[ $in_key ] = sanitize_text_field( $in_val );
	}
	
	return ( !empty( $out ) )? $out : '';
}


/*
	Name: nn_errors
	Description: Error handling funciton taken from Pippens Plugins
*/

function nn_errors(){
	
		static $wp_error; // Will hold global variable safely
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	
}


/*
	Name: nn_referer_requst_match
	Description: A security check for when $_GET data is being sent via wp_redirect. 
*/

function nn_referer_requst_match(){
	
	$source = parse_url( $_SERVER[ 'HTTP_REFERER' ] );
	$source_test = $source[ 'scheme' ].'://'.$source[ 'host' ];
	//If site is local host add port:
	$source_test .= ( strcmp( $source[ 'host' ], 'localhost' ) === 0 )?':'.$source[ 'port' ] : '';
	$site_url = get_site_url();
	
	if( strcmp( $source_test, $site_url ) !== 0 )
		return false;
	
	$path_test = rtrim( $source[ 'path' ] ,"/");
	$request_test = rtrim( strtok( $_SERVER['REQUEST_URI'], '?' ) , "/"); 
	
	if( strcmp( $path_test, $request_test ) !== 0 )
		return false;
	
	return true;
	
	//$_SERVER['REQUEST_URI']
	//$_SERVER['HTTP_REFERER'] 
	//These TWO MUST MATCH for $_GET. 
	
}


/*
	Name: nn_not_admin
	Description: Keep Non-Admin users out of admin areas. 
*/

function nn_not_admin(){
	if( is_user_logged_in() && !current_user_can( 'administrator' ) ){
		
	 if( is_admin() ){
		
			$site_url = site_url();
			wp_redirect($site_url); 
			exit; 
			
		}	
	}
}

add_action('init', 'nn_not_admin');


/*
	Name: nn_admin_bar
	Description: Disable the default admin bar for all users except administrators. 
*/

function nn_admin_bar($admin_bar) {
	return ( current_user_can( 'administrator' ) ) ? $admin_bar : false;
}

add_filter( 'show_admin_bar' , 'nn_admin_bar'); 




/*
	Name: nn_
	Description:
*/

function nn_(){
	
	
}

?>