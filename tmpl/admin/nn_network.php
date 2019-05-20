
<?php 
if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }
 
 // add error/update messages
 
 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 add_settings_error( NN_TD.'_messages', NN_TD.'_message', __( 'Settings Saved', NN_TD ), 'updated' );
 }
 
 // show error/update messages
 settings_errors( NN_TD.'_messages' );
 ?>
 <div class="wrap">
 <h1 id='wp-heading-inline'>NBCS Network Settings Page</h1>
 <form action="options-general.php?page=nn_network" method="post">
 <?php
 // output security fields for the registered setting "wporg_setting_name"
 settings_fields( NN_TD );
 // output setting sections and their fields
 // (sections are registered for "wporg_settings_section", each field is registered to a specific section)
 do_settings_sections( NN_TD );
 // output save settings button
 submit_button( 'Save Settings' );
 ?>
 </form>
 </div>
 
 <?php 
if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }
?>

<!-- Create a header in the default WordPress 'wrap' container -->
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'NN Plugin Settings', NN_TD ); ?></h2>
		
		<?php settings_errors(); ?>
		
		<?php 
		//Incoming array is has tabs already set as setting groups. Take top level info for tab name/values. 		
		
			$settings = new init\NNSettings(); //Add a settings page
			echo $settings->render();
		?>
			
	</div><!-- /.wrap -->