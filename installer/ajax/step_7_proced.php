<?php
/**
 * Date: 05/07/2019
 * Time: 10:50
 */

error_reporting(E_ERROR | E_PARSE);
ini_set('max_execution_time', '300');
include ("../lang/". $_POST['i18n'] . ".php" ."");
session_start() ;

$sqlFileToExecute = "..//db.sql";

$pdo = connectDataBase();

$sth = $pdo->prepare("set FOREIGN_KEY_CHECKS = 0;");
$ret = $sth->execute();
if (!$ret) {
    $arr = $sth->errorInfo();
    print_r($arr);
    echo '<br>';
}

$sqlArray = makeSqlArray($sqlFileToExecute);
makeTables($sqlArray,$pdo);
makeBlobs($pdo);


//echo '<script type="text/javascript">$("#evolution").html("TESTANDO");</script> ';


?>

<div class="ibox-content profile-content">

    <div id="evolution" </div></p>
    <h4><div id="etapa1" class="fa fa-check-square"></div>&nbsp; <?php echo utf8_encode(CONFIG_CREATE) ?></h4></p>
    <h4><div id="etapa2" class="fa fa-check-square"></div>&nbsp; <?php echo utf8_encode(ADMIN_CREATE) ?></h4></p>

    <div class="form-group">&nbsp;</div>

    <?php echo  utf8_encode(URL_HDK) ?> <a href="<?php echo  $_SESSION['site_url'] ?>"><?php echo  $_SESSION['site_url'] ?></a>

</div>


<?php


function connectDataBase()
{

    $host	=	$_SESSION['db_hostname'];
    $dbname	=	$_SESSION['db_name'];
    $user	=	$_SESSION['db_username'];
    $pass	=	$_SESSION['db_password'];
    $port	=	$_SESSION['db_port'] ;

    $dsn = "mysql:host=".$host.";"."dbname=".$dbname;

    try {
        $db = new PDO($dsn,$user,$pass);
    } catch (PDOException $e) {
        die("Error database - Error Info: " . $e->getMessage() . "<br/>");
    }
    return $db;
}


function makeSqlArray($file)
{
    $contentFile = file_get_contents($file);
    $rows = explode("\n", $contentFile);
    $content = '';
    // Clear comments
    foreach($rows as $line) {
        if (strrpos($line,"--") !== false
            OR (strrpos($line, "#")) !== false) {
            continue;
        }
        $content .= $line;
    }

    return explode(';',$content);

}

function makeTables($sqlArray, $pdo)
{
    //Process the sql file by statements
    foreach ($sqlArray as $stmt) {
        if (strrpos($stmt	, "/*") OR strrpos($stmt	, "#"))
        {
            continue;
        }
        if (strlen($stmt)>3){
            $sth = $pdo->prepare($stmt);
            $ret = $sth->execute();
            if (!$ret){
                echo "Error: <br>" . $stmt . "<br><br>";
                print "Mysql Error: <br><pre>";
                print_r($pdo->errorInfo()) . "<br>";
                exit;
                break;
            }

            //echo "Query: <br>" . $stmt . "<br><br>";;

            $pos = strpos( $stmt, 'CREATE TABLE IF NOT EXISTS' );

            if ($pos > 0) {
                //echo 'Encontrado: ' . $pos . "<br>";
                $parsed = get_string_between($stmt,'EXISTS', '(');
                echo "Creating table: " . str_replace('`','',$parsed) . "<br>";
            }
        }
    }
}

function makeBlobs($dbh)
{
    $sth = $dbh->query("SELECT * from tmp_blobs");
    while($row = $sth->fetch(PDO::FETCH_OBJ)) {

        $sql = "UPDATE " . $row->tabela . " SET " . $row->campo . " = FROM_BASE64(" . $row->campo . ")";

        $stmt = $dbh->prepare($sql);
        $ret = $stmt->execute();

        if (!$ret) {
            $arr = $stmt->errorInfo();
            echo $arr[2]."<br />\n";
        }

    }

}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

?>