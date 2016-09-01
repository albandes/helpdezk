<?php
error_reporting(E_ERROR | E_PARSE);

include ("../lang/". $_POST['i18n'] . ".php" ."");

session_start ;


?>

	<div class="progress">
		<?php echo utf8_encode(PROGRESS_STEP_7) ?>		
	</div>
						
	<div class="sections">
		<div class="info">
			<div id="install_status"> <img src="images/ajax-loader.gif" > &nbsp; <?php echo utf8_encode(WAIT_INSTALL) ?> </div>
		</div>
	
		<div class="row" id="db_progress">

		</div>		
	<div class="clear"></div>
	
	
	<div class="clear"></div>
		

		
	</div>
		
	<div class="clear"></div>
	<div class="clear"></div>

