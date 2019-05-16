<?php 

/*
 *  New Beginnings Editor PHP Class
 *	Created on 18 July 2013
 *  Updated on 31 July 2014
 *
 *	The purpose of this class is to handle recurring processes related to 
 *	editor pages for the New Beginnings Doula Training website. 
 *
 */

 
 
class NB_Editor{ 

/* 	private static $log_dir_path = '';
	private static $log_dir_url  = ''; */
	
	public function __construct(){
				
		$this->init();
	
	}
	

	/**
	 * Initialization
	 *
	 * @since 1.0
	 **/
	public function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_pages' ) );
		//add_action( 'init', array( __CLASS__, 'process_student' ) );
	}

	/**
	 * Add administration menus
	 *
	 * @since 1.0
	 **/
	public function add_admin_pages() {
	
		//STUDENT Editor Pages
		add_menu_page('Students Overview', 'Students', 'edit_users', 'students',  array( __CLASS__, 'load_students_overview' ) , 'dashicons-heart', 50 );
		add_submenu_page( 'students', 'Add New Student', 'Add New', 'edit_users', 'add_student', array( __CLASS__, 'load_new_student_editor' ) );
		add_submenu_page( NULL, 'Email Student', 'Auto Emails', 'edit_users', 'email_student', array( __CLASS__, 'load_email_student_editor' ) );
		add_submenu_page( 'students', 'Import Students', 'Import Trxn', 'edit_users', 'import_transaction', array( __CLASS__, 'load_import_transaction_editor' ) ); // We may do away with this one. 
		add_submenu_page( NULL, 'Edit Student', '', 'edit_users', 'edit_student', array( __CLASS__, 'load_student_editor' ) );
		
		//TRANSACTION Editor Pages
		add_submenu_page( 'students', 'Location Search', 'Location', 'edit_users', 'location_search', array( __CLASS__,'load_location_search' ) );
		add_submenu_page( NULL, 'Add New Transaction', '', 'edit_users', 'add_transaction',  array( __CLASS__, 'load_new_transaction' ) );
		add_submenu_page( NULL, 'Edit Transaction', '', 'edit_users', 'edit_transaction',  array( __CLASS__, 'load_transaction_editor' ) );
		
		//DEV TEST WINDOW
		
		//if( ( substr( get_bloginfo('url'), 7, 6 ) ) === 'crsdev' ){
			add_submenu_page( 'students', 'Test Window', 'Test Window', 'edit_users', 'test_window',  array( __CLASS__, 'load_test_window_editor' ) );
		//}
		
		//Student Maintenance Window
		add_submenu_page( 'students', 'Maintenance Tools', 'Maintenance', 'edit_users', 'maintenance',  array( __CLASS__, 'load_maintenance_editor' ) );
		
		//Admin Messages Manager page
		add_submenu_page( NULL, 'Message Manager', '', 'edit_users', 'admin_messages',  array( __CLASS__, 'load_admin_messages_manager' ) );
		
		//ASSIGNMENTS Editor page
		add_submenu_page( NULL, 'Edit Grades', '', 'edit_users', 'edit_grades',  array( __CLASS__, 'load_grades_editor' ) );
		
		//ASSIGNMENT MAP editor
		add_submenu_page( 'edit.php?post_type=assignment', 'Assignments Map Manager', 'Map Manager', 'edit_users', 'assignment_map',  array( __CLASS__, 'load_assignment_map_manager' ) );
		
		
		
		
		//MISC - adding space separators. 
		self::add_admin_menu_separator(30);
	}
	
	/*
	 * LOAD STUDENTS OVERVIEW
	 *
	 * @since 1.0
	 **/		

	public function load_students_overview(){
		
		if (!current_user_can('edit_users'))
			wp_die(__('You do not have sufficient permissions to access this page.'));
			
		//global $student_type;
		
		if( !isset ( $_GET['role'] ) ){
			$student_type = NULL;
		} else {
			$student_type = $_GET['role'];	
		}
		
		//Start OUTPUT 
		
		self::nb_student_overview_header();
		
	
		
		if( !class_exists('NB_Students_Tables')){ //This should be available because the Tables class is loaded before the editor class...
			echo "Problems, please fix them.";
		} else {
			$nb_students_list = new NB_Students_Tables();
			
			$nb_students_list->prepare_items( $student_type );
		
			$nb_students_list->display();
		} 
		

		self::nb_admin_footer(); 
	
		//End OUTPUT
	
	}
	
		
	/*
	 * LOAD ASSIGNMENT MAP MANAGER
	 *
	 * @since 2.0
	 **/

	public function load_assignment_map_manager(){
		
		self::nb_admin_header('Assignments Map Manager'); 
		
		$asmt_map = new NB_Assignment_Map();
		//print_pre( $asmt_map->asmt_map );
		$asmt_map->asmt_map_manager();
		//nb_assignment_map_manager(); 
		
		self::nb_admin_footer(); 
	}



	
		
	/*
	 * LOAD NEW STUDENT EDITOR
	 *
	 * @since 1.0
	 **/

	public function load_new_student_editor(){

		self::load_student_form( 'Add New Student' ); //Title is the minimum variable we need to use this function. 
		
	}



	/*
	 * LOAD STUDENT EDITOR
	 *
	 * @since 1.0
	 **/

	public function load_student_editor(){
		
		//Current User has permission to Edit Students... 
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		
		$errors = null;
		$message = null;
		$updated = false; 
		$sid = $_REQUEST['student_id'];
		$student = get_userdata($sid); 
		
		if( !empty($_POST) || wp_verify_nonce($_POST['trees_and_flowers'],'edit_student') ) {
			
			$nbStud = new NB_Student();

			//We need to run a check to see if new user data is being entered. Probably need to also prepare data to be processed via the NB_Student Class. 
			
			if( !isset( $_REQUEST['student_id'] ) ){//We are inserting a new student's information.
				if( isset($_POST['trees_and_flowers']) && ( $_POST['_wp_http_referer'] == '/wp-admin/admin.php?page=add_student' ) )
				//This is a new student being submitted via the add_student page. Most values will be prepared for submission on that page. 
				
				$sPost = $nbStud->student_post;//an array to prep values for add_student
				
				foreach($_POST as $sPostKey => $sPostVal){
					if( array_key_exists($sPostKey, $sPost) ){
						//echo "sPostKey is $sPostKey and sPostVal is $sPostVal. <br>";
						$sPost[$sPostKey] = $sPostVal;
					}
				}
				
				//Set user_login, user_nicename, nickname
				$sPost['user_login'] = $_POST['first_name'].$_POST['last_name'];
				$sPost['user_nicename'] = strtolower($_POST['first_name'].'-'.$_POST['last_name']);
				$sPost['nickname'] = $_POST['first_name'].$_POST['last_name'];
				$sPost['user_pass'] = wp_generate_password( 12, false );
				
				$student = $nbStud->add_student($sPost);//This is where all the processing happens. 
				
				if( is_a( $student, 'WP_User') ){
				
					$added = true;
				
				} elseif( is_wp_error( $student ) ) {
				
					$errors = $student;
					$student = null; //We need to empty this out, because the form will want to use it. 
					
				}
				
				$message = ( isset($added) && ($add == true) )? "We've added a new student account for $student->display_name." : null;
			
			} else {  //We are UPDATING student information that has been passed. 
				
				//We need the current student data to compare to updated data. 
				
				//Here's a couple of more security checks. 
				if( isset($_POST['trees_and_flowers']) && ( $_POST['_wp_http_referer'] == '/wp-admin/admin.php?page=edit_student&student_id='.$sid ) ){
					
					$_POST['ID'] = $_GET['student_id'];
					$updated_student = $nbStud->update_student($_POST, 1, 1); //override set, don't display update detail messages. 
					
				/* 	echo "<br><br> Did you catch that:<pre> ";
					var_dump( $_POST );
					echo "</pre><br><br>"; */
					if( is_a($updated_student, 'WP_User') ) {// New Method from NB_Student class, needs to be created. 
						$updated = true;
						$student = $updated_student;
					}
					//Do we want to send an update message? we could. 
					$message = ( isset($updated) && ($updated === true) )? "We've updated the student account for $student->display_name." : "There was nothing new to update. Thanks anyways.";

				}				
			}// end nonce else. 
		}
		
		$studTitle = "Student Editor: <em>". $student->display_name ."</em>"; //Title for student editor.  
		self::load_student_form( $studTitle, 'add_student', $student, $message, $errors );//Load the Student Form. 
	}


	/*
	 * LOAD STUDENT FORM
	 *
	 * @since 1.0
	 **/	
	 
	
	public function load_student_form( $studTitle, $newAction = null, WP_User $student = null, $message = null, WP_Error $errors = null ){
		
 		if( isset($_REQUEST['student_id']) ){
			$sid = $_REQUEST['student_id'];
		} elseif( is_object($student) ) {
			$sid = $student->ID;
		} else {
			$sid = null;
		} 
		
		
		self::nb_admin_header($studTitle, $newAction); 
		
		if( !isset($errors) &&( is_a( $errors, 'WP_Error') ) ){
			echo '<div class="errors" id="erros"><p>There are errors. I still need to improve upon this.</p></div>';
		}
		
		if($message != null)
			echo '<div class="updated" id="message"><p>'.$message.'</p></div>';
		
		//View Grades for this student
		
		
		if( !empty( $sid ) ){
			echo "<p><a href='/wp-admin/admin.php?page=edit_grades&amp;student_id=$sid' target='_blank'>View Grades</a> | 
			<a href='/wp-admin/admin.php?page=location_search&amp;student_id=$sid' target='_blank'>Find Nearby Students and Alumni</a></p>";
		
			echo '<h3>Student Transaction Records</h3>';
					
			if( class_exists('NB_Transaction_Tables') ){ //This should already be loaded at this point.
			
				$nb_transaction_list = new NB_Transaction_Tables();
				
				$nb_transaction_list->prepare_items();
			
				$nb_transaction_list->display();
			}		
		}
		
		
		/* echo "<h3>Student Photos</h3>";
		echo get_avatar( $sid );
		 */
		
		echo'<form method="post" action="admin.php?page=edit_student';
		if($sid != null)
			echo '&student_id='.intval($sid);
		echo'">';	
		
		
		wp_nonce_field('edit_student','trees_and_flowers');
			
		
		echo'<div id="student_visible" class="switch">
			  <label for="student_visible" >Publicly Visible:</label>
			  <input name="student_visible" type="checkbox"';
		echo ($student->student_visible == 'on')? ' checked ': '';
		echo'>
			  <div class="slider round">&nbsp;</div>
			</div>';
		echo'	
			<h3>Personal Information</h3>
			<table class="form-table">
				
				<tr>
					<td>
						<label for="user_login">User Name</label>
					</td>
					<td>
						<input disabled type="text" id="user_login" name="user_login"  class="regular-text" value="'.$student->data->user_login.'" >
					</td>	
					<td>
						<label for="display_name">Display Name</label>
					</td>
					<td>
						<input type="text" id="display_name" name="display_name"  class="regular-text" value="'.$student->data->display_name.'" >
					</td>
					
				</tr>
				
				<tr>
					<td>
						<label for="first_name">First Name</label>
					</td>
					<td>
						<input type="text" id="first_name" name="first_name"  class="regular-text" value="'.$student->first_name.'" >
					</td>
					<td>
						<label for="last_name">Last Name</label>
					</td>
					<td>
						<input type="text" id="last_name" name="last_name"  class="regular-text" value="'.$student->last_name.'" >
					</td>
					
				</tr>
				
				<tr>
					<td>
						<label for="student_address">Address</label>
					</td>
					<td>
						<input type="text" id="student_address" name="student_address"  class="regular-text" value="'.$student->student_address.'" >
					</td>
					<td>
						<label for="student_address2">Address, Second Line</label
					</td>
					<td>
						<input type="text" id="student_address2" name="student_address2"  class="regular-text" value="'.$student->student_address2.'" >
					</td>
				</tr>
				<tr>
					<td>
						<label for="student_city">City</label>
					</td>
					<td>
						<input type="text" id="student_city" name="student_city"  value="'.$student->student_city.'" >
					</td>
			
					<td>
						<label for="student_state">State</label>
					</td>
					<td>
						<input type="text" id="student_state" name="student_state"  value="'.$student->student_state.'" >
					</td>
				</tr>
				<tr>
					<td>
						<label for="student_postalcode">Postal Code</label>
					</td>
					<td>
						<input type="text" id="student_postalcode" name="student_postalcode"  value="'.$student->student_postalcode.'" >
					</td>
					<td>
						<label for="student_country">Country</label>
					</td>
					<td>
						<input type="text" id="student_country" name="student_country"  class="regular-text" value="'.$student->student_country.'" >
					</td>
				</tr>
			</table>
			
			<h3>Contact Information</h3>
			<table class="form-table">
				
				<tr>
					<td>
						<label for="student_phone">Phone</label>
					
					</td>
					<td>
						
						<input type="text" id="student_phone" name="student_phone"  class="regular-text" value="'.$student->student_phone.'" >
					</td>
					<td>
						  <label for="phone_visible">Publicly Visible:</label>
					</td>
					<td>
						<div id="phone_visible" class="switch">
						  <input name="phone_visible" type="checkbox"';
		echo ($student->phone_visible == 'on')? ' checked ': '';
		echo'>
						  <div class="slider round">&nbsp;</div>
						</div>					
					</td>
				</tr>
				
			
				<tr>			
					<td>
						<label for="user_email">Email</label>
					</td>
					<td>
						<input type="email" id="user_email" name="user_email"  class="regular-text" value="'.$student->data->user_email.'" >
					</td>
					<td>
					
						  <label for="email_visible">Publicly Visible:</label>
					</td>
					<td>
						<div id="email_visible" class="switch">
						  <input name="email_visible" type="checkbox"';
		echo ($student->email_visible == 'on')? ' checked ': '';
		echo'>
						  <div class="slider round">&nbsp;</div>
						</div>					
					</td>
				</tr>
				
				<tr>
					<td>
						<label for="facebook">Facebook</label>
					
					</td>
					<td>
						
						<input type="text" id="facebook" name="facebook"  class="regular-text" value="'.$student->facebook.'" >
					</td>
					<td>
						  <label for="facebook_visible">Publicly Visible:</label>
					</td>
					<td>
						<div id="facebook_visible" class="switch">
						  <input name="facebook_visible" type="checkbox"';
		echo ($student->facebook_visible == 'on')? ' checked ': '';
		echo'>
						  <div class="slider round">&nbsp;</div>
						</div>					
					</td>
				</tr>
				
				
				<tr>
					<td>
						<label for="twitter">Twitter</label>
					
					</td>
					<td>
						
						<input type="text" id="twitter" name="twitter"  class="regular-text" value="'.$student->twitter.'" >
					</td>
					<td>
						  <label for="twitter_visible">Publicly Visible:</label>
					</td>
					<td>
						<div id="twitter_visible" class="switch">
						  <input name="twitter_visible" type="checkbox"';
		echo ($student->twitter_visible == 'on')? ' checked ': '';
		echo'>
						  <div class="slider round">&nbsp;</div>
						</div>					
					</td>
				</tr>
				
				<tr>
					<td>
						<label for="instagram">Instagram</label>
					
					</td>
					<td>
						
						<input type="text" id="instagram" name="instagram"  class="regular-text" value="'.$student->instagram.'" >
					</td>
					<td>
						  <label for="instagram_visible">Publicly Visible:</label>
					</td>
					<td>
						<div id="instagram_visible" class="switch">
						  <input name="instagram_visible" type="checkbox"';
		echo ($student->instagram_visible == 'on')? ' checked ': '';
		echo'>
						  <div class="slider round">&nbsp;</div>
						</div>					
					</td>
				</tr>
				
				
				<tr>
					<td>
						<label for="pinterest">Pinterest</label>
					
					</td>
					<td>
						
						<input type="text" id="pinterest" name="pinterest"  class="regular-text" value="'.$student->pinterest.'" >
					</td>
					<td>
						  <label for="pinterest_visible">Publicly Visible:</label>
					</td>
					<td>
						<div id="pinterest_visible" class="switch">
						  <input name="pinterest_visible" type="checkbox"';
		echo ($student->pinterest_visible == 'on')? ' checked ': '';
		echo'>
						  <div class="slider round">&nbsp;</div>
						</div>					
					</td>
				</tr>
				
				
			</table>
			
			<h3>Payment Information</h3>
			<table class="form-table">
				<tr>
					<td>
						<label for="student_paypal">Paypal Email</label>
					</td>
					<td>
						<input type="email" id="student_paypal" name="student_paypal"  class="regular-text" value="'.$student->student_paypal.'" >
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						<label for="user_registered">Registration Date</label>
					</td>
					<td>
						<input type="text" id="user_registered" name="user_registered"  class="regular-text" value="'.$student->data->user_registered.'" >
					</td>
					<td>
						<label for="last_payment_received">Last Payment Received</label>
					</td>
					<td>
						<input type="text" id="last_payment_received" name="last_payment_received"  class="regular-text" v