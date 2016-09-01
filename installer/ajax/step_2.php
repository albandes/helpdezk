<?php
error_reporting(E_ERROR | E_PARSE);

$install_dir	    = "installer/";
$upload_dir 		= "app/uploads/";
$smartycache_dir 	= "system/templates_c/";
$log_dir			= "logs/";
$config_file		= "includes/config/config.php" ;

include ("../lang/". $_POST['i18n'] . ".php" ."");

//echo $_POST['i18n'];
//session_start();
//session_destroy();
/*
session_start();
if (	session_register("LANG") !=  $_POST['i18n']  ) 
{
die('entrou');
	$LANG = $_POST['i18n'];
	session_register("LANG");   
}
*/



if (DIRECTORY_SEPARATOR=='/') 
  $absolute_path = dirname(__FILE__).'/'; 
else 
  $absolute_path = str_replace('\\', '/', dirname(__FILE__)).'/'; 
  
  
if (strnatcmp(phpversion(),'5.2.0') >= 0)
{
	# equal or newer
	$status = OK;
	$class  = "pass";
}
else
{
	# not sufficiant
	$status = NOT_OK;
	$class  = "fail";
} 
	
$srv = 	"
			<div class=\"first odd\">
				<label>PHP Version</label>
				<div class=\"value\"> ". phpversion(). " <span class=\" ".$class." \">". $status ."</span></div>
				<div class=\"clear\"></div>
			</div>

		";

if (ini_get('file_uploads'))
{
	# equal or newer
	$value  = "YES";
	$status = OK;
	$class  = "pass";
}
else
{
	# not sufficiant
	$value  = "NO";
	$status = NOT_OK;
	$class  = "fail";
} 

$srv .= 	"
			<div class=\"even\">
				<label>file_upload</label>
				<div class=\"value\"> ".$value. " <span class=\" ".$class." \">". $status ."</span></div>
				<div class=\"clear\"></div>
			</div>

		";


$srv .= 	"
			<div class=\"odd\">
				<label>upload_max_filesize</label>
				<div class=\"value\"> ".ini_get('upload_max_filesize')."</div>
				<div class=\"clear\"></div>
			</div>

		";		

if (function_exists('mysql_connect')){
	$mysql = true;
	$status = ENABLE;
	$class  = "pass";
} 
else
{
	$status = DISABLE;
	$class  = "fail";
}

$mdl =	"	
			<div class=\"first odd\">
				<label>MySQL</label>
				<div class=\"value\">	". $status."</div>
				<div class=\"clear\"></div>
		";

/**
 **		
 ** Config file
 **
 **/

 
$path = substr($absolute_path, 0, strpos($absolute_path, $install_dir)   );
$name  = $path.$config_file;

session_start() ;
$_SESSION['config_file'] = $name;

clearstatcache();
// - aqui 
/*
if (!file_exists($name)) {
	$mode	= "Not Exists";
	$status = "FAIL"; 
	$class  = "fail";
} else {	
	if (is_writable($name))	
	{
		$mode	= utf8_encode(WRITABLE);
		$status = OK; 
		$class  = "pass";
	}
	else
	{
		$mode	= utf8_encode(NOT_WRITABLE);
		$status = NOT_OK; 
		$class  = "fail";
	}	
}

$fld .= "
		<div class=\"first odd\">
			<label>config.php</label>
			<div class=\"value\">".$mode."<span class=\"".$class."\"> ".$status."</span>	</div>
			<div class=\"clear\"></div>
		</div>

		";

*/

// Upload Dir		
$name  = $path.$upload_dir;
clearstatcache();
if (!file_exists($name)) {
	$mode	= "Not Exists";
	$status = "FAIL"; 
	$class  = "fail";
} else {	
	if (is_writable($name))	
	{
		$mode	= utf8_encode(WRITABLE);
		$status = OK; 
		$class  = "pass";
	}
	else
	{
		$mode	= utf8_encode(NOT_WRITABLE);
		$status = NOT_OK; 
		$class  = "fail";
	}	
}
$fld .= "
		<div class=\"even\">
			<label>".$upload_dir."</label>
			<div class=\"value\">".$mode."<span class=\"".$class."\"> ".$status."</span>	</div>
			<div class=\"clear\"></div>
		</div>

		";		
		
// Smarty Cache Dir		
$name  = $path.$smartycache_dir;
clearstatcache();
if (!file_exists($name)) {
	$mode	= "Not Exists";
	$status = "FAIL"; 
	$class  = "fail";
} else {	
	if (is_writable($name))	
	{
		$mode	= utf8_encode(WRITABLE);
		$status = OK; 
		$class  = "pass";
	}
	else
	{
		$mode	= utf8_encode(NOT_WRITABLE);
		$status = NOT_OK; 
		$class  = "fail";
	}	
}
$fld .= "
		<div class=\"odd\">
			<label>".$smartycache_dir."</label>
			<div class=\"value\">".$mode."<span class=\"".$class."\"> ".$status."</span>	</div>
			<div class=\"clear\"></div>
		</div>

		";		
// Log Dir		
$name  = $path.$log_dir;
clearstatcache();
if (!file_exists($name)) {
	$mode	= "Not Exists";
	$status = "FAIL"; 
	$class  = "fail";
} else {	
	if (is_writable($name))	
	{
		$mode	= utf8_encode(WRITABLE);
		$status = OK; 
		$class  = "pass";
	}
	else
	{
		$mode	= utf8_encode(NOT_WRITABLE);
		$status = NOT_OK; 
		$class  = "fail";
	}	
}
$fld .= "
		<div class=\"even\">
			<label>".$log_dir."</label>
			<div class=\"value\">".$mode."<span class=\"".$class."\"> ".$status."</span>	</div>
			<div class=\"clear\"></div>
		</div>

		";		
		
		
?>

	<div class="progress">
		<?php echo utf8_encode(PROGRESS_STEP_2) ?>
	</div>
						
	<div class="sections">
		<div class="info">
			<?php echo utf8_encode(INFO_STEP_2) ?>
		</div>
		<h2><?php echo utf8_encode(PHP_SETTINGS) ?></h2>
		<div class="grid">
			<?php echo $srv ?>
		</div>		
		<h2><?php echo utf8_encode(PHP_MODULES) ?></h2>
		<div class="grid">
			<?php echo $mdl ?>
		</div>
	</div>
	
	<h2><?php echo utf8_encode(FOLDERS_FILES) ?></h2>
	<div class="grid widegrid">
		<?php echo $fld; ?>
	</div>
	<div class="buttons">
		<button class="button button-back" onclick="step_1()" ><?php echo BACK ?></button>
		<button class="button button-next" onclick="step_3('<?php echo $_POST['i18n'] ?>')" ><?php echo NEXT ?></button>
	</div>
		
	<div class="clear"></div>
	<div class="clear"></div>

