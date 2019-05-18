
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
 <form action="options.php" method="post">
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