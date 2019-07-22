<?php

$log = true;
$logfile = '../logs/tracker.log';

/**
 *
 **/


include('../includes/config/config.php');
include('../includes/classes/pipegrep/remoteAddress.php');


$class = new RemoteAddress();
$ipaddress = $class->getIpAddress();
$idemail = removeQuotes($_GET['id']);
$campaign = removeQuotes($_GET['campaign']);
$php_self = $_SERVER['PHP_SELF'];
$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
$http_referer = $_SERVER['HTTP_REFERER'];
$request_uri = $_SERVER['REQUEST_URI'];
$request_time = $_SERVER['REQUEST_TIME'];


$pdo = getDb();


$print_date = str_replace("%","",$config['date_format']) . " " . str_replace("%","",$config['hour_format']);

$active = getStatus($pdo);


if (!$active) {
    displayImage();
    exit;
}

$sql =  "
        INSERT INTO tbtracker (
          idemail,
          campaign,
          ipaddress,
          php_self,
          http_user_agent,
          http_referer,
          request_uri,
          request_time,
          DATE
        )
        VALUES
          (
            '$idemail',
            '$campaign',
            '$ipaddress',
            '$php_self',
            '$http_user_agent',
            '$http_referer',
            '$request_uri',
            '$request_time',
            NOW()
          ) ;


        ";

try {
    $statement = $pdo->exec($sql);
} catch(PDOException $e) {
    echo $e->getMessage();
    if($log)
        logit("[".date($print_date)."]" . " - Error insert into database, file " . __FILE__ . ", line " . __LINE__ , $logfile);
    displayImage();
    exit;
}

if($log)
    logit("[".date($print_date)."]" . " - Campaign: $campaign , IdEmail: $idemail, Subject:  " . getSubject($pdo, $idemail), $logfile);

displayImage();

exit;

function removeQuotes($string)
{
    $str = str_replace('"', "", $string);
    return str_replace("'", "", $str);
}

function getStatus($conn)
{
    $sql = "SELECT value FROM tbconfig WHERE session_name = 'TRACKER_STATUS' LIMIT 1";
    $result = $conn->query( $sql );
    $row = $result->fetchAll( PDO::FETCH_ASSOC );

    if (empty($row))
        return false;
    else
        return $row['0']['value'];

}

function displayImage()
{
    header('Content-type: image/png');
    echo gzinflate(base64_decode('6wzwc+flkuJiYGDg9fRwCQLSjCDMwQQkJ5QH3wNSbCVBfsEMYJC3jH0ikOLxdHEMqZiTnJCQAOSxMDB+E7cIBcl7uvq5rHNKaAIA'));
}

function getSubject($conn, $idemail)
{
    $sql = "SELECT a.subject FROM tbemail a WHERE a.idemail = $idemail ";
    $result = $conn->query( $sql );
    $row = $result->fetchAll( PDO::FETCH_ASSOC );

    if (empty($row))
        return false;
    else
        return $row['0']['subject'];

}

function getDb()
{
    global $config;
    $dsn = 'mysql:host='.$config['db_hostname'].';dbname='.$config['db_name'];
    try{
        $pdo = new PDO($dsn,$config['db_username'], $config['db_password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    catch(PDOException $ex){
        if($log) logit("[".date($print_date)."]" . " - Run cron/dashboard_stats.php" , $logfile);
        die(json_encode(array('outcome' => false, 'message' => $ex->getMessage())));
    }
}

function logit($str, $file)
{
    if (!file_exists($file)) {
        if($fp = fopen($file, 'a')) {
            @fclose($fp);
            return logit($str, $file);
        } else {
            return false;
        }
    }
    if (is_writable($file)) {
        $str = time().'	'.$str;
        $handle = fopen($file, "a+");
        fwrite($handle, $str."\r\n");
        fclose($handle);
        return true;
    } else {
        return false;
    }
}
