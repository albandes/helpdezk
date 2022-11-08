<?php

$sqlFileToExecute = "..//db_teste.sql";

$pdo = connectDataBase();

$sth = $pdo->prepare("set FOREIGN_KEY_CHECKS = 0;");
$ret = $sth->execute();
if (!$ret) {
    $arr = $sth->errorInfo();
    print_r($arr);
    echo '<br>';
}



/*
 * echo "<pre>";
 * print_r($sqlArray);
 * echo "</pre><br>";
 */


$sqlArray = makeSqlArray($sqlFileToExecute);
/*
echo "<pre>";
print_r($sqlArray);
echo "</pre><br>";
*/
makeTables($sqlArray,$pdo);
makeBlobs($pdo);

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
                echo "creating table: " . str_replace('`','',$parsed) . "<br>";
            }
        }
    }
}

function makeBlobs($dbh)
{
    $sth = $dbh->query("SELECT * from tmp_blobs");
    while($row = $sth->fetch(PDO::FETCH_OBJ)) {

        $sql = "UPDATE " . $row->tabela . " SET " . $row->campo . " = FROM_BASE64(" . $row->campo . ")";
        echo $sql."<br />\n";

        $stmt = $dbh->prepare($sql);
        $ret = $stmt->execute();

        if (!$ret) {
            $arr = $stmt->errorInfo();
            echo $arr[2]."<br />\n";
        }


    }

}

/*
Exemplo UPDATE:
$sql = "UPDATE posts SET titulo = ?, autor = ?, conteudo = ? WHERE id = ?";
$sqlpre = $con->prepare($sql);
$sqlarray = array("$titulo", "$autor", "$conteudo", "$id");
$sqlpre->execute($sqlarray);
*/


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}



function connectDataBase()
{

    $host	=	"127.0.0.1";
    $dbname	=	"demo";
    $user	=	"pipeadm";
    $pass	=	"qpal10";
    $port	=	"3306" ;

    $dsn = "mysql:host=".$host.";"."dbname=".$dbname;

    try {
        $db = new PDO($dsn,$user,$pass);
    } catch (PDOException $e) {
        die("Error database - Error Info: " . $e->getMessage() . "<br/>");
    }
    return $db;
}