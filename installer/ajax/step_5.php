<?php
include ("../lang/". $_POST['i18n'] . ".php" ."");

session_start() ;
$_SESSION['db_hostname'] = $_POST['db_hostname'];
$_SESSION['db_port']     = $_POST['db_port'];
$_SESSION['db_name']     = $_POST['db_name'];
$_SESSION['db_username'] = $_POST['db_username'];
$_SESSION['db_password'] = $_POST['db_password'];

?>


<script src="../installer/js/password_strength/password_strength.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="../installer/password_strength.css" />



<script   type="text/javascript">

$(document).ready(function ()
{
	$(".field_admin_password").passStrength({
		
		shortPass: 		"top_shortPass",
		badPass:		"top_badPass",
		goodPass:		"top_goodPass",
		strongPass:		"top_strongPass",
		baseStyle:		"top_testresult",
		
		userid:			"#field_admin_username",
		messageloc:		0,
		
		label_shortPass: 	'<?php echo SHORT_PASS?>',
		label_badPass: 		'<?php echo BAD_PASS?>',
		label_goodPass: 	'<?php echo GOOD_PASS?>',
		label_strongPass: 	'<?php echo STRONG_PASS?>',
		label_samePassword: '<?php echo SAME_PASS?>'
	
	});
	

})

</script>

	<div class="progress">
		<?php echo utf8_encode(PROGRESS_STEP_5) ?>		
	</div>
						
	<div class="sections">
		<div class="info">
			<?php echo utf8_encode(INFO_STEP_5) ?>
		</div>
	
		<div class="row">
			<label for="field_admin_username" ><?php echo utf8_encode(ADMIN_USERNAME) ?></label>
			<div class="field">
				<input type="text" id="field_admin_username" name="admin_username"   class="text" value="admin"/>
			</div>
		</div>
		
		<div class="row">
			<label for="field_admin_password" ><?php echo utf8_encode(ADMIN_PASSWORD) ?></label>
			<div class="field">
				<input type="text" id="field_admin_password" name="admin_password"   class="text field_admin_password" value="1234" />
			</div>
		</div>

		<div class="row">
			<label for="field_admin_email" ><?php echo utf8_encode(ADMIN_EMAIL) ?></label>
			<div class="field">
				<input type="text" id="field_admin_email" name="admin_email" class="text" value="user@domain.com" />
			</div>
		</div>
		<div class="clear"></div>
		
		<div class="clear"></div>
		<div class="clear"></div>
		

		
	</div>
		<div class="buttons">
			<button class="button button-back" onclick="step_4('<?php echo $_POST['i18n'] ?>')" ><?php echo BACK ?></button>
			<button class="button button-next" onclick="step_6('<?php echo $_POST['i18n'] ?>')" ><?php echo NEXT ?></button>
		</div>
		
		<div class="clear"></div>

	<div class="clear"></div>

