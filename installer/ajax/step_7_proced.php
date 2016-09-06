<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('max_execution_time', '300');
include ("../lang/". $_POST['i18n'] . ".php" ."");
session_start() ;

/**
 **   --
 **   -- DATABASE
 **   -- 
 **/
$host	=	$_SESSION['db_hostname'];
$dbname	=	$_SESSION['db_name']; 
$user	=	$_SESSION['db_username']; 
$pass	=	$_SESSION['db_password'];
$port	=	$_SESSION['db_port'] ;

$sqlFileToExecute = "..//banco.sql";

$con = mysql_connect($host,$user,$pass);
if(!($DB=mysql_select_db($dbname,$con))) { 
   echo 'Can\'t use database: ' . $dbname . ".  Err: " . mysql_errno() . " - " . mysql_error();
   exit; 
} 

// Get Mysql version 			
$t=mysql_query("select version() as ve");
echo mysql_error();
$r=mysql_fetch_object($t);
preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $r->ve, $version); 
$versao_mysql = $version[0];
			
$query = "set FOREIGN_KEY_CHECKS = 0;";
mysql_query($query) or die ('Error: ' . mysql_errno() . " - " . mysql_error());
			
			
			
if ($con !== false){
	   // Load and explode the sql file
	   $f = fopen($sqlFileToExecute,"r+");
	   $sqlFile = fread($f,filesize($sqlFileToExecute));
	   $sqlArray = explode(';',$sqlFile);
	   
	   //Process the sql file by statements
	   foreach ($sqlArray as $stmt) {
		//$stmt = 	base64_decode($stmt);
		//die($stmt);
		if (strrpos($stmt	, "/*"))
		{
			continue;
		}
			  if (strlen($stmt)>3){
			  $result = mysql_query($stmt);
			  if (!$result){
					 $sqlErrorCode = mysql_errno();
					 $sqlErrorText = mysql_error();
					 $sqlStmt         = $stmt;
					 print "error: <br> " . mysql_error() . "<br>";
					 print $stmt;
					 exit;
					 break;
			  }
			 }
	   }
}

$SQL = "SELECT * from tmp_blobs ;" ;
$rs  = mysql_query($SQL);
if (!$rs) {  die( "line: " . __LINE__ . " -- Invalid query. Err " . mysql_errno() . " - "  . mysql_error() ."<br><b> query: </b>" . $SQL );}

while ($row = mysql_fetch_row($rs)) {
	if ($versao_mysql >= 5.6 ) 
	{
		$sql = "UPDATE " . $row[1] . " SET " . $row[2] . " = FROM_BASE64(" . $row[2] . ")";
		//print $sql . "<br>";
		$rsBlob  = mysql_query($sql);
		if (!$rsBlob) { die( "line: " . __LINE__ . " -- Invalid query. Err " . mysql_errno() . " - "  . mysql_error() ."<br><b> query: </b>" . $sql );		}
	}
	else
	{
		$sql = "SELECT  *  FROM " . $row[1] ;
		$rsSel  = mysql_query($sql);
		if (!$rsSel) {	die( "line: " . __LINE__ . " -- Invalid query. Err " . mysql_errno() . " - "  . mysql_error() ."<br><b> query: </b>" . $sql );		}
		
		while ($linha = mysql_fetch_array($rsSel)) {
			$decode = base64_decode($linha[$row[2]]);
			$sql = "UPDATE	" . $row[1] . " SET " . $row[2] . " = '". addslashes($decode) .  "'";
			$sql .= "WHERE " . get_primary_key ($row[1]) . " = " . $linha[0];
			$rsDecode  = mysql_query($sql);
			if (!$rsDecode) { die( "line: " . __LINE__ . " -- Invalid query. Err " . mysql_errno() . " - "  . mysql_error() ."<br><b> query: </b>" . $sql );			}
		}
	}	

}

$query = "set FOREIGN_KEY_CHECKS = 1;";
mysql_query($query) or die ('Error: ' . mysql_errno() . " - " . mysql_error());

/**
 **   --
 **   -- END DATABASE
 **   -- 
 **/

//sleep(2);
$_SESSION['admin_username'] ;
$_SESSION['admin_password'] ;
$_SESSION['admin_email']    ;

$query =    "
             UPDATE tbperson 
             SET    login    = '".$_SESSION['admin_username']."',
                    password = '".md5($_SESSION['admin_password'])."',
                    email    = '".$_SESSION['admin_email']."'
             WHERE  idperson = 1           
            ";

mysql_query($query) or die ('Error: ' . mysql_errno() . " - " . mysql_error());
// -------------------------
$url	= $_SESSION['site_url'];
$pos 	= strpos($url, '//');

if ($pos === false) 
{
	$rest = $url ;
} 
else 
{
	// get substring after "//" 
	$rest = substr($url, $pos + 2);
}
// check if exists "/"
$pos_bar = strpos($rest ,'/');
if ($pos_bar === false) 
{
	$path_default = "..";
}
else
{
	if (strlen(substr($rest, $pos_bar+1)) > 0 ) 
	{
		$path_default = substr($rest, $pos_bar+1) ;
	}
	else
	{
		$path_default = ".."; ;
	}
	
}
//--------------------

$a_arc = array();

// Config file

$database = "mysqlt";

array_push($a_arc,"<?php \r\n\r\n" );
array_push($a_arc,"\$config[\"db_connect\"] \t = \t" . "\"" . $database . "\"" . "; //mysqlt = MYSQL | oci8po = Oracle " );
array_push($a_arc,"\$config[\"db_hostname\"] \t = \t" . "\"" . $_SESSION['db_hostname'] . "\"" . ";" );
array_push($a_arc,"\$config[\"db_port\"] \t\t = \t"   . "\"" . $_SESSION['db_port'] . "\"" . ";" );
array_push($a_arc,"\$config[\"db_name\"] \t\t = \t"   . "\"" . $_SESSION['db_name'] . "\"" . ";" );
array_push($a_arc,"\$config[\"db_username\"] \t = \t" . "\"" . $_SESSION['db_username'] . "\"" . ";" );
array_push($a_arc,"\$config[\"db_password\"] \t = \t" . "\"" . $_SESSION['db_password'] . "\"" . ";" );
// Oracle array_push($a_arc,"\$config[\"db_sn\"] \t = \t" . "\"" . $_SESSION['db_sn'] . "\"" . ";" );

//
//url tem que ter a barra no final
//path_default = nao tem barra nem no inicio nem no fim 
//

array_push($a_arc," \r\n\r\n " );
//$config[\"\"]
array_push($a_arc,"\$config[\"hdk_url\"] \t = \t" . "\"" . $_SESSION['site_url'] . "\"" . ";" );
array_push($a_arc,"\$config[\"path_default\"] \t = \t" . "\"" . $path_default . "\"" . ";" );
array_push($a_arc,"\$config[\"lang\"] \t = \t" . "\"" . $_SESSION['lang_default'] . "\"" . ";" );
array_push($a_arc,"\$config[\"theme\"] \t = \t" . "\"" . $_SESSION['theme_default'] . "\"" . ";" );

array_push($a_arc,"\$config[\"page_title\"] \t = \t" . "\" [HELPDEZK] - Parracho - A free open source Help desk software! \"" . ";" );

array_push($a_arc,"date_default_timezone_set('". $_SESSION['timezone_default'] ."') ;" );

array_push($a_arc,"if(\$config[\"lang\"] == 'en_US') {");
array_push($a_arc,"\t \$config[\"id_mask\"] \t = \t" . "\" ?999-99-9999\"" . ";" );
array_push($a_arc,"\t \$config[\"ein_mask\"] \t = \t" . "\" ?99-9999999\"" . ";" );

array_push($a_arc,"\t \$config[\"date_format\"] \t = \t" . "\"%m/%d/%Y\"" . ";" );
array_push($a_arc,"\t \$config[\"hour_format\"] \t = \t" . "\"%h:%i %p\"" . ";" );

array_push($a_arc,"}" );

array_push($a_arc,"if(\$config[\"lang\"] == 'pt_BR') {");
array_push($a_arc,"\t \$config[\"id_mask\"] \t = \t" . "\" ?999.999.999-99\"" . ";" );
array_push($a_arc,"\t \$config[\"ein_mask\"] \t = \t" . "\" ?99.999.999/9999-99 \"" . ";" );

array_push($a_arc,"\t \$config[\"date_format\"] \t = \t" . "\"%d/%m/%Y\"" . ";" );
array_push($a_arc,"\t \$config[\"hour_format\"] \t = \t" . "\"%H:%i\"" . ";" );

// Turksh
array_push($a_arc,"}" );
array_push($a_arc,"if(\$config[\"lang\"] == 'tr_TR') {");
array_push($a_arc,"\t \$config[\"id_mask\"] \t = \t" . "\" ?999.999.999-99\"" . ";" );
array_push($a_arc,"\t \$config[\"ein_mask\"] \t = \t" . "\" ?99.999.999/9999-99 \"" . ";" );
array_push($a_arc,"\t \$config[\"date_format\"] \t = \t" . "\"%d.%m.%Y\"" . ";" );
array_push($a_arc,"\t \$config[\"hour_format\"] \t = \t" . "\"%h:%i %p\"" . ";" );
array_push($a_arc,"}" );
//
array_push($a_arc," \r\n\r\n ?>" );

$fd = fopen($_SESSION['config_file'], "w") or die ("ERROR: Config File Not Writable !!!!");

foreach ($a_arc as $line) 
{
	$fout = fwrite($fd, $line . "\r\n");
}

fclose($fd);
session_destroy();


function get_primary_key($tabela)
{
	global $con;
	$sql = "show index from ". $tabela . " where Key_name='PRIMARY' ";
	$t=mysql_query($sql);
	echo mysql_error();
	$r=mysql_fetch_object($t);	
	return $r->Column_name ;
}


?>

<img src="images/icon-check-20.gif" > &nbsp; <?php echo utf8_encode(CONFIG_CREATE) ?> 
<br>
<img src="images/icon-check-20.gif" > &nbsp; <?php echo utf8_encode(ADMIN_CREATE) ?> 
<br>
<br>
<?php echo  utf8_encode(URL_HDK) ?> <a href="<?php echo  $_SESSION['site_url'] ?>"><?php echo  $_SESSION['site_url'] ?></a>  

