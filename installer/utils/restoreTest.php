<?php

error_reporting(E_ALL);
ini_set('max_execution_time', 600); // seconds

$dbUser = 'root';
$dbPass = '';


$time_start = microtime(true);


$sqlFileTables = "db-install-tables.sql";
$sqlFileViews  = "db-install-views.sql";
$sqlFileRoutines  = "db-install-routines.sql";


$conn = mysqli_connect("localhost", $dbUser,$dbPass, "hdk-test");

$retTables = restoreMysqlDB($sqlFileTables, $conn);
$retViews  = restoreMysqlDB($sqlFileViews , $conn);
$retRoutines  = restoreRoutines($sqlFileRoutines , $conn);



// Display Script End time
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;
echo '<b>Total Execution Time:</b> '.round($execution_time,1).' Mins';



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
?>

