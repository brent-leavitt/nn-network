<?php
/*
nn_network\init\Token

Enrollment Token Class - Initial Class for NN Network Plugin
Last Updated on 15 Jul 2019
-------------

  Description: This class initializes the tokens that that are generated and stored in the options table for each site on the network. 

  Functions: 
	//add_tokens
	//update_tokens
	//remove_tokens
	//set_tokens
	//get_tokens
	//add_tokens_to_site
	//remove_tokens_from_site
	//setup
	//remove
	//get_sites
	//init
	//
  
---
	



*/
namespace nn_network\init;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Token' ) ){
	class Token{
		
		//Parameters
		
		public $tokens = array(
			'subscription_preview',  	//
			'subscription_free',  		//
			'subscription_month',  		//
			'subscription_year',  		//
			'service_full',  			//
			'service_recurring',  		//
			'service_manual',  			//
			'srvc_extend_1mo',  		//
			'srvc_extend_6mo',  		//
			'service_renewal',  		//
			/* 'service_',  			//
			'_',  		//
			'_',  		// */
		),
		$name = NN_PREFIX.'service_tokens',
		$autoload = 'no'; 
		
		
		private $site_args = array(		// Get site args. 
			'fields' => 'ids',
			
		);
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/
		
		public function __construct(){
			
			
		}
		
		
		
		/*
		Name: init
		Description: 
		
		*/
		
		public function init(){
			
			if( !empty( $tokens = $this->get_tokens() ) ){
				$this->tokens = $tokens;  				
			}
		}
		
		
		
		/*
		Name: setup
		Description: 
		
		*/
		
		public function setup(){
			
			//Get all sites on network
			$sites = get_sites( $this->site_args );
			
			foreach( $sites as $site_id ){
				
				$this->add_tokens_to_site( $site_id );
			}
			
			
		}
		
		
		/*
		Name: remove
		Description: 
		
		*/
		
		public function remove(){
			
			//Get all sites on network
			$sites = get_sites( $this->site_args );
			
			foreach( $sites as $site_id ){
				
				remove_tokens_to_site( $site_id );
			}
		}		
		
		
		/*
		Name: set_tokens
		Description: 
		
		*/
		
		public function set_tokens( $tokens ){
			
			if( !empty( $tokens ) ){
				
				$this->tokens = $tokens;
				
				$this->update_tokens();
				
				return true;
			}
			
			return false;
			
		}
				
		
		
		/*
		Name: get_tokens
		Description: 
		
		*/
		
		public function get_tokens(){
			
			$tokens = get_tokens( $this->name );
			
			return $tokens;
			
		}
		
		
		
		/*
		Name: add_tokens
		Description: 
		
		*/
		
		public function add_tokens(){
						
			$result = add_option( $this->name, $this->tokens, '', $this->autoload  );
			return ( isset( $result ) )? $result : false;
		}
		
		
		/*
		Name: update_tokens
		Description: 
		
		*/
		
		public function update_tokens(){
			
			update_option( $this->name, $this->tokens, $this->autoload );
		}
		
		
		/*
		Name: remove_tokens
		Description: 
		
		*/
		
		public function remove_tokens(){
			
			if( !empty( get_option( $this->name ) ) ){
				delete_option( $this->name );
			}
		}

		
		
		/*
		Name: add_tokens_to_site
		Description: 
		
		*/
		
		public function add_tokens_to_site( $site_id ){
			
			switch_to_blog( $site_id );
			
			$this->add_tokens();
			
			restore_current_blog();
		}
		
		
		
		/*
		Name: remove_tokens_from_site
		Description: 
		
		*/
		
		public function remove_tokens_from_site( $site_id ){
			
			switch_to_blog( $site_id );
			
			$this->remove_tokens();
			
			restore_current_blog();
		}
		
	}
}




?>


