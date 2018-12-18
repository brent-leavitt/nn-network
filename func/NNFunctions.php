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
	
	$formatter = new misc\NNDataFormat();
	
	//Assigns a third-party source for incoming data. If empty, assume that the data is from in-house. 
	$formatter->set_source( $source );
	
	return $formatter->format( $data );
	
}