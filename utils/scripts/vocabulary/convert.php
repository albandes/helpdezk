<?php

include_once ('search.php');

$hostName       = "localhost";
$dbName         = "hdk-develop";
$userName       = "root";
$userPassword   = "";
//$charset        = "latin1";
$charset        = "utf8";
$idModule       = 2;
$tableName      = "tbvocabulary";
//$tableName      = "tbtest";

$lineBreak = "<br>";

/*
 *
 */


$class = new search();

$class->_arrayExceptions = makeExceptions();

$class->_langPath = "E:/home/rogerio/htdocs/erick/app/lang/";

$class->_langFile = "pt_BR.txt";
$class->_langFile = 'en_US.txt' ;



$class->_searchPath = "E:/home/rogerio/htdocs/git/helpdezk/app/modules";


$arrayLang = $class->getLangVariablesUsage();


$idLocale = getLocale($class->_langFile);
$dbConn = dbConnect($hostName,$dbName,$userName,$userPassword,$charset);

/*
 *
 */

//emptyVocabularyTable($tableName);

foreach ($arrayLang as $row) {

    if ( !langVarExists($row['id'],$tableName,$idLocale,$idModule) ) {
        $keyName  = $row['id'];
        $keyValue = addslashes($row['text']);

        writeVocabulary($tableName,$idLocale,$idModule,$keyName,$keyValue);
    }

}

die('<br>End Script');

function makeExceptions()
{
    $arrayExceptions = array(
                            "pgr_people",
                            "pgr_holidays",
                            "pgr_programs",
                            "pgr_modules",
                            "pgr_status",
                            "pgr_priority",
                            "pgr_groups",
                            "pgr_evaluation",
                            "gr_departments",
                            "pgr_cost_center",
                            "pgr_services",
                            "pgr_req_reason",
                            "pgr_email_config",
                            "pgr_sys_features",
                            "pgr_type_permission",
                            "pgr_person_report",
                            "pgr_downloads",
                            "pgr_logos",
                            "pgr_req_reports",
                            "pgr_import_services",
                            "pgr_ope_aver_resptime",
                            "pgr_rejects_request",
                            "pgr_request_department",
                            "pgr_request_status",
                            "pgr_summarized_department",
                            "pgr_summarized_operator",
                            "pgr_user_satisfaction",
                            "pgr_warnings",
                            "pgr_dash_widgets",
                            "pgr_work_calendar",
                            "pgr_import_people",
                            "pgr_request_operator",
                            "pgr_worked_requests",
                            "pgr_email_request",
                            "pgr_sys_features",
                            "adm_Navbar_name",
                            "hdk_Navbar_name"
                            );

    return $arrayExceptions;

}

function emptyVocabularyTable($tableName)
{
    global $dbConn;

    $query =    "DELETE from {$tableName}";
    try {
        $dbConn->query($query);
    }
    catch(Exception $e) {
        echo 'Exception -> ';
        var_dump($e->getMessage());
        die('<br>' . $query);
    }

    $query = "ALTER TABLE {$tableName} AUTO_INCREMENT = 1";
    try {
        $dbConn->query($query);
    }
    catch(Exception $e) {
        echo 'Exception -> ';
        var_dump($e->getMessage());
        die('<br>' . $query);
    }
}

function writeVocabulary($tableName,$idLocale,$idModule,$keyName,$keyValue)
{
    global $dbConn;

    $q   = 	"       INSERT INTO {$tableName}
                    (
                      idlocale
                     ,idmodule
                     ,key_name
                     ,key_value
                    )
                    VALUES
                    (
                      '{$idLocale}'    
                     ,'{$idModule}'              
                     ,'{$keyName}'           
                     ,'{$keyValue}'       
                    )			
					";
    try {
        $executa = $dbConn->query($q);
    }
    catch(Exception $e) {
        echo 'Exception -> ';
        var_dump($e->getMessage());
        die($q);
    }

}

function langVarExists($keyName,$tableName,$idLocale,$idModule)
{

    global $dbConn;

    $sql = "
            SELECT
              idvocabulary,
              idlocale,
              idmodule,
              key_name,
              key_value
            FROM {$tableName}	
            WHERE
                key_name = '$keyName'
            AND idlocale = $idLocale
            AND idmodule = '$idModule'
			";
    try {

        $select = $dbConn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {

        echo 'Exception -> ';
        var_dump($e->getMessage());
        die($sql);

    }

    $count = count($select);
    if($count > 0) {
        return true;
    } else {
        return false;
    }


}

function getLocale($str)
{
    if (substr($str, 0, 2) == 'en') {
        $idLocale = 19;

    } elseif (substr($str, 0, 2) == 'pt') {
        $idLocale = 77;
    }

    return $idLocale;
}


function dbConnect($hostName,$dbName,$userName,$userPassword,$charset)
{
    $dsn = "mysql:host={$hostName};dbname={$dbName};charset={$charset}";

    try {
        $conn = new PDO($dsn, $userName, $userPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die('<br><br>Error !!!!!!!!!');
    }
}