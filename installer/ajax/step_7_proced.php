<?php
/**
 * Date: 05/07/2019
 * Time: 10:50
 */

// -- Configuration ----------------
$debug = false ;
$lineBreak = '<br>';
$jqueryVersion = 'jquery-2.1.1.js';
// ---------------------------------


if ($debug)
    ini_set('display_errors',1); ini_set('display_startup_erros',1); error_reporting(E_ALL);//force php to show any error message

ini_set('max_execution_time', '300');
include ("../lang/". $_POST['i18n'] . ".php" ."");
session_start() ;

$sqlFileTables    = "../dumps/db-install-tables.sql";
$sqlFileViews     = "../dumps/db-install-views.sql";
$sqlFileRoutines  = "../dumps/db-install-routines.sql";

$host	=	$_SESSION['db_hostname'];
$dbname	=	$_SESSION['db_name'];
$user	=	$_SESSION['db_username'];
$pass	=	$_SESSION['db_password'];
$port   =  (empty($_SESSION['db_port']) ? '3306' : $_SESSION['db_port'] ) ;

if ($debug) {
    echo '<pre>';  print_r($_SESSION); echo '</pre>';
}

$db = connectDb($host,$user,$pass,$dbname,$port);

// Make tables
$retTables = restoreMysqlDB($sqlFileTables, $db);
if ($retTables['type'] == "error") die($retTables['message']) ;

// Make Views
$retViews  = restoreMysqlDB($sqlFileViews , $db);
if ($retViews['type'] == "error") die($retTables['message']) ;

// Make routines
$retRoutines  = restoreRoutines($sqlFileRoutines , $db);
if ($retRoutines['type'] == "error") die($retTables['message']) ;

// Alter admin user´s data
$retAdmin = alterAdminUser($db);
if ($retAdmin['type'] == "error") die($retAdmin['message']) ;

// Make config file
$retConfig = makeConfig($jqueryVersion);
if ($retConfig['type'] == "error") die($retConfig['message']) ;


?>

<div class="ibox-content profile-content">

    <div id="evolution" </div></p>
    <h4><div  class="fa fa-check-square"></div>&nbsp; <?php echo utf8_encode(DB_CREATE) ?></h4></p>
    <h4><div  class="fa fa-check-square"></div>&nbsp; <?php echo utf8_encode(ADMIN_CREATE) ?></h4></p>
    <h4><div  class="fa fa-check-square"></div>&nbsp; <?php echo utf8_encode(CONFIG_CREATE) ?></h4></p>

    <div class="form-group">&nbsp;</div>

    <?php echo  utf8_encode(URL_HDK) ?> <a href="<?php echo  $_SESSION['site_url'] ?>"><?php echo  $_SESSION['site_url'] ?></a>

</div>


<?php

function alterAdminUser($conn)
{
    global $debug;
    global $lineBreak;

    $query =    "
             UPDATE tbperson 
             SET    login    = '".$_SESSION['admin_username']."',
                    password = '".md5($_SESSION['admin_password'])."',
                    email    = '".$_SESSION['admin_email']."'
             WHERE  idperson = 1           
            ";

    if ($conn->query($query) === TRUE) {
        if ($debug)
            echo "Admin Data update successfully !" . $lineBreak ;
        $response = array(
            "type" => "success",
            "message" => "Admin Data update successfully !"
        );
    }else {
        if ($debug)
            echo "Error updating admin user's data: " . $conn->error . $lineBreak;
        $response = array(
            "type" => "error",
            "message" => "Error updating admin user's data: " . $conn->error
        ) ;
        exit();
    }

    return $response;
}

function makeConfig($jqueryVersion)
{
    $path_default = setPathhDefault();
    $a_arc = array();

    $database = "mysqli";

    array_push($a_arc,"<?php \r\n\r\n" );

    array_push($a_arc,"date_default_timezone_set('". $_SESSION['timezone_default'] ."') ;" );

    array_push($a_arc, PHP_EOL );

    array_push($a_arc,"//DATABASE CONFIGURATION" );
    array_push($a_arc,"\$config[\"db_connect\"] \t = \t" . "\"" . $database . "\"" . "; //mysqli = MYSQL | oci8po = Oracle " );
    array_push($a_arc,"\$config[\"db_hostname\"] \t = \t" . "\"" . $_SESSION['db_hostname'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"db_port\"] \t\t = \t"   . "\"" . $_SESSION['db_port'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"db_name\"] \t\t = \t"   . "\"" . $_SESSION['db_name'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"db_username\"] \t = \t" . "\"" . $_SESSION['db_username'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"db_password\"] \t = \t" . "\"" . $_SESSION['db_password'] . "\"" . ";" );
    // Oracle array_push($a_arc,"\$config[\"db_sn\"] \t = \t" . "\"" . $_SESSION['db_sn'] . "\"" . ";" );

    /*
     * url = has to have the slash at the end
     * path default = has no slash either at the beginning or at the end
     */

    array_push($a_arc, PHP_EOL );
    array_push($a_arc,"//SYSTEM CONFIGURATION" );

    array_push($a_arc,"\$config[\"hdk_url\"] \t = \t" . "\"" . $_SESSION['site_url'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"path_default\"] \t = \t" . "\"" . $path_default . "\"" . ";" );
    array_push($a_arc,"\$config[\"lang\"] \t = \t" . "\"" . $_SESSION['lang_default'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"theme\"] \t = \t" . "\"" . $_SESSION['theme_default'] . "\"" . ";" );
    array_push($a_arc,"\$config[\"page_title\"] \t = \t" . "\" [HELPDEZK] - Parracho - A free open source Helpdesk software! \"" . ";" );
    array_push($a_arc,"\$config[\"jquery\"] \t = \t" . "\"". $jqueryVersion ."\"" . ";" );

    array_push($a_arc, PHP_EOL );
    array_push($a_arc,"//EXTERNAL STORAGE CONFIGURATION" );
    array_push($a_arc,"\$config[\"external_storage\"] \t\t = \tfalse ; ");
    array_push($a_arc,"\$config[\"external_storage_path\"] \t = \t 'puth_external_storage_path_here' ;");
    array_push($a_arc,"\$config[\"external_storage_url\"] \t = \t 'puth_external_storage_url_here' ; ");

    array_push($a_arc, PHP_EOL );
    array_push($a_arc,"//LOCATION CONFIGURATION" );
    array_push($a_arc,"if(\$config[\"lang\"] == 'en_US') {");
    array_push($a_arc,"\t \$config[\"id_mask\"] \t = \t" . "\"999-99-9999\"" . ";" );
    array_push($a_arc,"\t \$config[\"ein_mask\"] \t = \t" . "\"99-9999999\"" . ";" );
    array_push($a_arc,"\t \$config[\"zip_mask\"] \t = \t" . "\"00000\"" . ";" );
    array_push($a_arc,"\t \$config[\"phone_mask\"] \t = \t" . "\"(000) 000-0000\"" . ";" );
    array_push($a_arc,"\t \$config[\"cellphone_mask\"] \t = \t" . "\"(00) 00000-0000\"" . ";" );
    array_push($a_arc,"\t \$config[\"date_placeholder\"] \t = \t" . "\"mm/dd/yyyy\"" . ";" );
    array_push($a_arc,"\t \$config[\"log_date_format\"] \t = \t" . "\"%m/%d/%Y %H:%i:%s\"" . ";" );
    array_push($a_arc,"\t \$config[\"date_format\"] \t = \t" . "\"%m/%d/%Y\"" . ";" );
    array_push($a_arc,"\t \$config[\"hour_format\"] \t = \t" . "\"%h:%i %p\"" . ";" );
    array_push($a_arc,"}" );

    array_push($a_arc,"if(\$config[\"lang\"] == 'pt_BR') {");
    array_push($a_arc,"\t \$config[\"id_mask\"] \t = \t" . "\" ?999.999.999-99\"" . ";" );
    array_push($a_arc,"\t \$config[\"ein_mask\"] \t = \t" . "\" ?99.999.999/9999-99 \"" . ";" );
    array_push($a_arc,"\t \$config[\"zip_mask\"] \t = \t" . "\"00000-000\"" . ";" );
    array_push($a_arc,"\t \$config[\"phone_mask\"] \t = \t" . "\"(00) 0000-0000\"" . ";" );
    array_push($a_arc,"\t \$config[\"cellphone_mask\"] \t = \t" . "\"(00) 00000-0000\"" . ";" );
    array_push($a_arc,"\t \$config[\"date_placeholder\"] \t = \t" . "\"dd/mm/yyyy\"" . ";" );
    array_push($a_arc,"\t \$config[\"log_date_format\"] \t = \t" . "\"%d/%m/%Y %H:%i:%s\"" . ";" );
    array_push($a_arc,"\t \$config[\"date_format\"] \t = \t" . "\"%d/%m/%Y\"" . ";" );
    array_push($a_arc,"\t \$config[\"hour_format\"] \t = \t" . "\"%H:%i\"" . ";" );
    array_push($a_arc,"}" );

    array_push($a_arc, PHP_EOL );

    $dirConfig = dirname($_SESSION['config_file']);

    if (!is_dir($dirConfig)){
        $ret = @mkdir($dirConfig, 0777, true);
        if (!$ret) {
            $response = array(
                "type"    => "error",
                "message" => "Error: I was unable to create the directory :  " . $dirConfig
            );
            return $response;
        }
    }

    $fd = fopen($_SESSION['config_file'], "w") or die ("ERROR: Config File Not Writable !!!!");

    if (!$fd) {
        $response = array(
            "type"    => "error",
            "message" => "Error: Config File Not Writable !!!!"
        );
        return $response;
    }
    foreach ($a_arc as $line)
    {
        $fout = fwrite($fd, $line . PHP_EOL);
    }

    fclose($fd);
    session_destroy();

    $response = array(
        "type"    => "success",
        "message" => "Config file write successfully !!!!"
    );
    return $response;

}

function setPathhDefault()
{
    $url	= $_SESSION['site_url'];
    $pos 	= strpos($url, '//');

    if ($pos === false){
        $rest = $url ;
    } else {
        $rest = substr($url, $pos + 2); // get substring after "//"
    }
    // check if exists "/"
    $pos_bar = strpos($rest ,'/');
    if ($pos_bar === false)  {
        $path_default = "..";
    }  else {
        if (strlen(substr($rest, $pos_bar+1)) > 0 ) {
            $path_default = substr($rest, $pos_bar+1) ;
        }  else {
            $path_default = ".."; ;
        }
    }

    return $path_default;
}

function connectDb($host,$user,$pass,$db,$port)
{
    global $debug;
    global $lineBreak;

    $mysqli = new mysqli($host, $user, $pass, '', $port);

    // Check connection
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error . $lineBreak;
        exit();
    }

    // If database is not exist create one
    if (!$mysqli->select_db($db)){
        $sql = "CREATE DATABASE ".$db;
        if ($mysqli->query($sql) === TRUE) {
            $mysqli->select_db($db);
            if ($debug)
                echo "Database ".$db." created successfully" . $lineBreak ;
        }else {
            echo "Error creating database: " . $mysqli->error . $lineBreak;
            exit();
        }
    }

    if ($debug) {
        echo "Success: A proper connection to MySQL was made! The my_db database is great." . $lineBreak;
        echo "Host information: " . mysqli_get_host_info($mysqli) . $lineBreak;
    }

    return $mysqli;
}


function restoreMysqlDB($filePath, $conn)
{
    $sql = '';
    $error = '';


    mysqli_query($conn, "set FOREIGN_KEY_CHECKS = 0;");


    if (file_exists($filePath)) {
        $lines = file($filePath);

        foreach ($lines as $line) {

            // Ignoring comments from the SQL script
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $sql .= $line;

            if (substr(trim($line), - 1, 1) == ';') {
                $result = mysqli_query($conn, $sql);
                if (! $result) {
                    $error .= mysqli_error($conn) . "<br>";
                    echo 'mysql error: '. $error  . '<br>';
                    echo $sql;
                    die();
                }
                $sql = '';
            }
        } // end foreach

        if ($error) {
            $response = array(
                "type" => "error",
                "message" => $error
            );
        } else {
            $response = array(
                "type" => "success",
                "message" => "Database Restore Completed Successfully."
            );
        }
    }  else {
        die('File not found !: ' . $filePath)    ;
    }    // end if file exists
    return $response;
}


function restoreRoutines($filePath, $conn)
{
    $sql = '';
    $error = '';

    mysqli_query($conn, "set FOREIGN_KEY_CHECKS = 0;");

    if (file_exists($filePath)) {
        $lines = file($filePath);

        foreach ($lines as $line) {

            // Ignoring comments from the SQL script
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            if (empty(trim($line)))
                continue;

            //if (substr(trim($line), - 1, 1) == '#') {
            //$sql = substr($line, 0, -1);
            $sql = $line;
            //echo $sql . '<br>';
            $result = mysqli_query($conn, $sql);
            if (! $result) {
                $error .= mysqli_error($conn) . "<br>";
                echo 'mysql error: '. $error  . '<br>';
                echo $sql;
                die();
            }

            //}
        } // end foreach

        if ($error) {
            $response = array(
                "type" => "error",
                "message" => $error
            );
        } else {
            $response = array(
                "type" => "success",
                "message" => "Database Restore Completed Successfully."
            );
        }
    }  else {
        die('File not found !: ' . $filePath)    ;
    }    // end if file exists
    return $response;
}


?>