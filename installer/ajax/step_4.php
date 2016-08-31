<?php
include ("../lang/". $_POST['i18n'] . ".php" ."");

session_start() ;
$_SESSION['site_url'] 		= $_POST['site_url'];
$_SESSION['lang_default'] 	= $_POST['lang_default'];
$_SESSION['theme_default'] 	= $_POST['theme_default'];
$_SESSION['timezone_default'] 	= $_POST['timezone_default'];
?>

	<div class="progress">
		<?php echo utf8_encode(PROGRESS_STEP_4) ?>		
	</div>
						
	<div class="sections">
		<div class="info">
			<?php echo utf8_encode(INFO_STEP_4) ?>
		</div>
	
		<div class="row">
			<label for="field_db_hostname" ><?php echo utf8_encode(DB_HOSTNAME) ?></label>
			<div class="field">
				<input type="text" id="field_db_hostname" name="db_hostname" class="text" />
			</div>
		</div>
		<div class="clear"></div>

		<div class="row">
			<label for="field_db_port" ><?php echo utf8_encode(DB_PORT) ?></label>
			<div class="field">
				<input type="text" id="field_db_port" name="db_port" value="3306"  class="text" />
			</div>
		</div>
		<div class="clear"></div>
		
		<div class="row">
			<label for="field_db_name" ><?php echo utf8_encode(DB_NAME) ?></label>
			<div class="field">
				<input type="text" id="field_db_name" name="db_name" class="text" />
			</div>
		</div>
		<div class="clear"></div>
		
		<div class="row">
			<label for="field_db_username" ><?php echo utf8_encode(DB_USERNAME) ?></label>
			<div class="field">
				<input type="text" id="field_db_username" name="db_username" class="text" />
			</div>
		</div>
		<div class="clear"></div>

		<div class="row">
			<label for="field_db_password" ><?php echo utf8_encode(DB_PASSWORD) ?></label>
			<div class="field">
				<input type="text" id="field_db_password" name="db_password" class="text" />
			</div>
		</div>
		<div class="clear"></div>

		<div class="clear"></div>
		
		<div class="clear"></div>
		
	</div>
		<div class="buttons">
			<button class="button button-back" onclick="step_3('<?php echo $_POST['i18n'] ?>')" ><?php echo BACK ?></button>
			<button class="button button-next" onclick="step_5('<?php echo $_POST['i18n'] ?>')" ><?php echo NEXT ?></button>
		</div>
		
		<div class="clear"></div>

	<div class="clear"></div>

