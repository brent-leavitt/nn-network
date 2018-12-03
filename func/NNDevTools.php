<?php

/*NB_NET Dev Tools

-----

Description: A list of random functions that help with newtork wide development. 

------


*/


//Dumps a formatted Var_dump result. 
function print_pre( $arr ){
	
	echo "<pre>";
	var_dump( $arr );
	echo "</pre>";
	
}

//wraps and outputs code inside a set of <p> tags. 
function ep( $str ){
	
	echo "<p>". $str ."</p>";

}


//Sends email notices to developer on current action. (Currently goes by different name/ found in Certs LMS)
function nn_dev_notice( $msg ){
	
	//If dev status is set to true. then send admin email notices. 
	
}


function nn_admin_notice( $msg ){
	
	//If admin settings are set to send email, send email to admin. 
	
	
	//Otherwise log message. 
	
	
}
	
	
		
	/*
		Name: random_id
		Description: from PHP.net on the uniqid() page. 
		Used In: core/sub/NNToken class
	*/	
			
	
function nn_random_id( $lenght = 13, $prefix = NN_PREFIX ) {
			// uniqid gives 13 chars, but you could adjust it to your needs.
			if ( function_exists( "random_bytes" ) ){
				$bytes = random_bytes( ceil( $lenght / 2 ) );
			} elseif ( function_exists( "openssl_random_pseudo_bytes" ) ) {
				$bytes = openssl_random_pseudo_bytes( ceil( $lenght / 2 ) );
			} else {
				throw new Exception( "no cryptographically secure random function available" );
			} 
			return $prefix . substr( bin2hex( $bytes ), 0, $lenght );
		}
	
