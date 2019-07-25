<?php 
if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }
?>

<!-- Create a header in the default WordPress 'wrap' container -->
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'NN Plugin Settings '.NN_TD, NN_TD ); ?></h2>
		
		<?php 
		//Incoming array is has tabs already set as setting groups. Take top level info for tab name/values. 		
		
			$settings = new \nn_network\init\Settings(); //Add a settings page
			echo $settings->render();
		?>
			
	</div><!-- /.wrap -->