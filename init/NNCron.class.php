<?php

/*
Enrollment Token Class - Initial Class Psuedo Code for NBCS Network Plugin
Last Updated 8 Nov 2018
-------------

  Description: This class initializes the tokens that that are generated and stored in the options table for each site on the network. 


---

*/

namespace init;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNCron' ) ){
	class NNCron{
		
		
		//Parameters
		
		public $__ = ''; //
		
		
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/
		
		public function __construct(){
			
			//$this->init();
			
		}
		
		
		/*
		Name: init
		Description: 
		
		*/
		
		public function init(){
			
			//ep( 'There was a CRON JOB initiation called!' );
			
			
			
		}	
		
		
		
		/*
		Name: schedule
		Description: 
		
		*/		
		public function schedule(){
			
			add_filter( 'cron_schedules', array( $this, 'add_cron_interval' ) );
			
			add_action( 'nn_cron_check', array( $this, 'cron_cb' ) );
			
			if ( ! wp_next_scheduled( 'nn_cron_check' ) ) {
				wp_schedule_event( time(), 'five_minutes', 'nn_cron_check' );
			}
			
		}
		
		/*
		Name: add_cron_interval
		Description: Adds the five minute interval to the crons schedule. 
		
		*/
		
		public function add_cron_interval(){
			
			$schedules['five_minutes'] = array(
				'interval' => 300,
				'display'  => esc_html__( 'Every Five Minutes' ),
			);
			 
			return $schedules;
		}
				
		/*
		Name: cron_cb
		Description: 
		
		*/
		
		public function cron_cb(){
			
			do_action( 'nn_cron' );
			
			
			
			
		}
				


				
		/*
		Name: remove_cron
		Description: 
		
		*/
		
		public function remove_cron(){
			
			// Get the timestamp for the next event.
			$timestamp = wp_next_scheduled( 'nn_cron_check' );

			// If this event was created with any special arguments, you need to get those too.
			$original_args = array();

			wp_unschedule_event( $timestamp, 'nn_cron_check' );
		}

				
		/*
		Name: __
		Description: 
		
		*/
		
		public function __(){
			
			
		}
		
		
		
		
	}
}




?>