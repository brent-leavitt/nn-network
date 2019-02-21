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
	
	$formatter = new misc\NNDataFormat( $data, $source );
	
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