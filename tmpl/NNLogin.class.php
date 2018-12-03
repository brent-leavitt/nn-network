//Login Template Class Psuedo Code

---

Description: This is where the HTML is housed for the LOgin form: 

To do's: 
	- A login form listener needs to be setup. 

	
//From Pippin plugins: 
	

// login form fields
function pippin_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="pippin_header"><?php _e('Login'); ?></h3>
 
		<?php
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_login_form"  class="pippin_form" action="" method="post">
			<fieldset>
				<p>
					<label for="pippin_user_Login">Username</label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="pippin_user_pass">Password</label>
					<input name="pippin_user_pass" id="pippin_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="pippin_login_nonce" value="<?php echo wp_create_nonce('pippin-login-nonce'); ?>"/>
					<input id="pippin_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}