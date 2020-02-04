<?php 

use \PHPUnit\Framework\TestCase;
//use nn_network\Login as Login;
//const NN_PREFIX = 'nn_';

class FirstTest extends TestCase{
	
	
	/**
	 * @test
	 */
	
	public function assert_that_this_is_being_called( ){ 
		
		$login = new nn_network\Login();
		
		$this->assertTrue( false );
	}
		

	
}

?>