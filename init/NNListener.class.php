<?php 

/*

Listener - Class Psuedo Code for NBCS Network Plugin
Last Updated 5 Oct 2018
-------------

  Description: Listener via Query Variables. 


To Do: 
	-This will be setup to connect listeners to a list of classes based on the action assigned to the class. 
  
---

	Braintstorming: 
		- What other listeners are missing? 
			- login form submission. 
			- Registration form submission. 
			
			- Any other form submissions. 
				- Newsletter Opt-ins. 
				- Waiting List Opt-ins. 
				- etc. 

*/
namespace init;	

use misc\NNStripeWebHooks as StripeWebHooks;
use proc\NNStripeCollect as StripeCollect;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'NNListener' ) ){
	
	class NNListener{
		
		
		//Properties: 
			//Maybe do a nested array for each value set so that you can do a foreach loop on each set: 
			
			
		public $listeners = array(
			'collect' => array(
					'316' => 'payment'
				), 	
			'baah' => array( 
					'694' => 'webhook'
				),		
				
			/* // etc. 	
			'odd',		//?
			'',			//
			'',			// */
		);
		
		//Methods
		
		public function __construct(){
			
			//This allows us to use wordpress to handle QueryVars requests. 
			add_action( 'template_redirect', array( $this, 'queryVarsListener' ) );
			add_filter( 'query_vars',  array( $this, 'queryVar' ) );
	
		}

		public function queryVar($query_vars) {
			$query_vars[] = 'collect'; // For Stripe Payment Collections
			$query_vars[] = 'baah'; //For Stripe Webhook Listener
			
			
			/* $query_vars[] = 'odd'; // For Cron Job Access */
			return $query_vars;
		}

		public function queryVarsListener() {
			
			foreach( $this->listeners as $listener => $arr ){
				foreach( $arr as $key => $action ){
					
					//Check that the query var is set and is the correct value.					
					if (isset($_GET[ $listener ]) && $_GET[ $listener ] == $key ){
						
						//$collect = new proc\NNCollect();
						//require_once( NBCS_NET_PATH . ( 'func/'.$action.'.php' ) );
						//Stop WordPress entirely
						exit;
					}
						
				}
			}
			
			
			
			
			
			
			//For Payment Collections. 
			if (isset($_GET['collect']) && $_GET['collect'] == 'payment'){//Check that the query var is set and is the correct value.
				
				$collect = new StripeCollect();
				exit;
			}
			
			
			//For Webhook Processing
			if (isset($_GET['baah']) && $_GET['baah'] == '694'){//Check that the query var is set and is the correct value.
				
				$webhook = new StripeWebHooks();
				exit;
			}
			
		/* 	if(isset($_GET['odd']) && $_GET['odd'] == 517){
				//Run NB Cron Tasks Such as invoicing and scheduled registration invites. 
				include "nb_crons.php";
				//Stop the rest of Wordpress. 
				exit;
			} */
		}
	}
}
?>