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
	
	//Assigns a third-party source for incoming data. If empty, assume that the data is from in-house. 
	$formatter->set_source( $source );
	
	return $formatter->format();
	
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