<?php
include ("../lang/". $_POST['i18n'] . ".php" ."");

session_start() ;
$_SESSION['admin_username'] = $_POST['admin_username'];
$_SESSION['admin_password'] = $_POST['admin_password'];
$_SESSION['admin_email']    = $_POST['admin_name'];

?>

	<div class="progress">
		<?php echo utf8_encode(PROGRESS_STEP_6) ?>		
	</div>
						
	<div class="sections">
		<div class="info">
			<?php echo utf8_encode(INFO_STEP_6) ?>
		</div>
	
		
		<div class="clear"></div>
		<div class="clear"></div>
		

		
	</div>
		<div class="buttons">
			<button class="button button-back" onclick="step_5('<?php echo $_POST['i18n'] ?>')" ><?php echo BACK ?></button>
			<button class="button button-next" onclick="step_7('<?php echo $_POST['i18n'] ?>')" ><?php echo INSTALL ?></button>
		</div>
		
		<div class="clear"></div>

	<div class="clear"></div>

