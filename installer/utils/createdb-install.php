<?php

$dbUser = 'root';
$dbPass = '';
$dbHost  = 'localhost';
$dbDatabase = 'hdk-install';

$lineBreak = '<br>';
$debug = true ;


if ($debug)
    ini_set('display_errors',1); ini_set('display_startup_erros',1); error_reporting(E_ALL);//force php to show any error message

$time_start = microtime(true);

$db = connectDb($dbHost,$dbUser,$dbPass,$dbDatabase);

$sqlTables = backupTables($db);
writeFile('db-install-tables.sql',$sqlTables);

$sqlViews = backupViews($db);
writeFile('db-install-views.sql',$sqlViews);

$sqlRoutines = backupRoutines($db);
writeFile('db-install-routines.sql',$sqlRoutines);

die(timeElapsed($time_start));


function backupRoutines($db)
{
    global $dbDatabase ;

    $sql = "SELECT a.ROUTINE_NAME, a.ROUTINE_DEFINITION, a.ROUTINE_TYPE FROM ROUTINES a WHERE ROUTINE_SCHEMA = '".$dbDatabase."'";

    $db->select_db('information_schema');

    $return = "";

    $result = $db->query($sql);

    $data = $result->fetch_array(MYSQLI_ASSOC);

    if (!$result) {
        die('error: ' . mysqli_error($db));
    }

    while($row = $result->fetch_array(MYSQLI_ASSOC))
    {

        $routineType = $row['ROUTINE_TYPE'];
        $routineDefinition = cleanDefinition($row['ROUTINE_DEFINITION']);

        $ret = makeRoutineParameters($db,$row['ROUTINE_NAME'],$dbDatabase,$routineType) ;


        $str  = "DROP ".$routineType." IF EXISTS `".$row['ROUTINE_NAME']."` " . PHP_EOL ;

        if ($ret == false ) { // procedure don´t have parameters
            $str .=  "CREATE  ".$routineType." `".$row['ROUTINE_NAME']."`() " . $routineDefinition .  PHP_EOL ;
            $return .=  $str ;
            $return .= "\n\n\n";
        } else {

            $str .= "CREATE  ".$routineType." `".$row['ROUTINE_NAME']."` (" ;

            $i = 0;
            $cont = mysqli_num_rows($ret);
            while($param = mysqli_fetch_row($ret)) {
                $i++;

                /*
                 0 - ORDINAL_POSITION,
			     1 - PARAMETER_MODE,
			     2 - PARAMETER_NAME,
			     3 - DATA_TYPE,
			     4 - CHARACTER_MAXIMUM_LENGTH
                 5 - DTD_IDENTIFIER
                 6 - CHARACTER_SET_NAME
                 */
                if ($routineType == 'PROCEDURE')
                {
                    $str .= $param[1] . " " . $param[2] . " " . $param[5] ;
                    if ($i < $cont) {
                        $str .= ', ';
                    } else {
                        $str .= ')';
                    }
                }
                elseif ($routineType == 'FUNCTION')
                {
                    if ($param[0] >= 1) {
                        $str .= " " . $param[2] . " " . $param[5] ;
                        if ($i < $cont) {
                            $str .= ', ';
                        } else {
                            $str .= ')';
                        }
                    }  else if ($param[0] == 0) { // Return definition
                        $returnDefinition = " RETURNS " . $param[5]   ;
                        if (!is_null($param[6]))
                            $returnDefinition .= " CHARSET " . $param[6];
                    }
                }

            }

            if ($routineType == 'FUNCTION' ) {
                $str .= " " . $returnDefinition . " " . $routineDefinition ;
            } elseif ($routineType == 'PROCEDURE' ) {
                $str .= " " . $routineDefinition;
            }

            $return .=  $str ;

            $return .= "\n\n\n";

        }

    }

    return $return;

}

function backupViews ($db)
{
    global $dbDatabase ;

    $sql = "SELECT 
          CONCAT(
            'CREATE OR REPLACE VIEW ',
            TABLE_NAME,
            ' AS ',
            -- VIEW_DEFINITION,
            REPLACE( VIEW_DEFINITION, '`".$dbDatabase."`.', '' ) ,
            '; '
          ) AS query 
        FROM
          INFORMATION_SCHEMA.VIEWS 
        WHERE TABLE_SCHEMA = '".$dbDatabase."'  ";



    $return = "";

    $rsViews = $db->query($sql);

    if (!$rsViews) {
        die('error: ' . mysqli_error($db));
    }

    while($data = $rsViews->fetch_array(MYSQLI_ASSOC))
    {
        $return .= $data['query'] ;
        $return .="\n\n\n";
    }

    return $return;

}

function backupTables ($db)
{
    global $dbDatabase ;

    $tables = array();

    $query =  "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $dbDatabase ."' AND TABLE_TYPE = 'BASE TABLE'" ;
    $result = $db->query($query);

    $i=0;
    while($row = mysqli_fetch_row($result))
    {
        $tables[$i] = $row[0];
        $i++;
    }

    $return = "";

    foreach($tables as $table)
    {
        //$result = mysqli_query($db, 'SELECT * FROM '.$table);
        $rsTables = $db->query('SELECT * FROM '.$table);
        //$num_fields = mysqli_num_fields($rsTables);
        $numFields = $rsTables->field_count;
        $return .= 'DROP TABLE IF EXISTS '.$table.';';

        //$row2 = mysqli_fetch_row(mysqli_query($db, 'SHOW CREATE TABLE '.$table));

        $rs2 = $db->query('SHOW CREATE TABLE '.$table);
        $data = $rs2->fetch_array(MYSQLI_ASSOC) ;

        //echo '<pre>'; print_r($data); die();

        $return.= "\n\n".$data['Create Table'].";\n\n";

        for ($i = 0; $i < $numFields; $i++)
        {
            while($row = $rsTables->fetch_array(MYSQLI_NUM))
            {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j < $numFields; $j++)
                {
                    $row[$j] = addslashes($row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j < ($numFields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
        }
        $return.="\n\n\n";
    }

    return $return;

}

function makeRoutineParameters($db,$routineName,$schemaName,$routineType)
{

    $sql = "
			SELECT 
			  ORDINAL_POSITION,
			  PARAMETER_MODE,
			  PARAMETER_NAME,
			  DATA_TYPE,
			  CHARACTER_MAXIMUM_LENGTH,
			  DTD_IDENTIFIER,
			  CHARACTER_SET_NAME
			FROM
			  PARAMETERS 
			WHERE SPECIFIC_SCHEMA = '".$schemaName."' 
			  AND SPECIFIC_NAME = '".$routineName."' 
			  AND ROUTINE_TYPE = '".$routineType."'
			  ORDER BY   ORDINAL_POSITION ASC
			";

    $db->select_db('information_schema');

    $return = "";
    $result = $db->query($sql);
    if ($result->num_rows == 0) {
        return false;
    } else {
        return $result;
    }

}

function connectDb($host,$user,$pass,$db)
{
    global $lineBreak;
    $link = mysqli_connect($host, $user, $pass, $db);
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error . $lineBreak;
        exit();
    }

    echo "Success: A proper connection to MySQL was made! The my_db database is great." . $lineBreak;
    echo "Host information: " . mysqli_get_host_info($link) . $lineBreak;

    return $mysqli;
}

function writeFile($fileName,$sql)
{
    $handle = fopen($fileName,'w+');
    fwrite($handle, $sql);
    fclose($handle);
    return true ;
}

function timeElapsed($time_start)
{
    $time_end = microtime(true);
    $execution_time = ($time_end - $time_start);
    if ($execution_time >= 60) {
        $execution_time = $execution_time/60;
        $unit = ' min';
    } else {
        $unit = ' s';
    }
    return '<b>Total Execution Time:</b> '.round($execution_time).$unit;
}

function cleanDefinition ($string)
{

    $arrayDef = explode("\n",$string);
    //echo '<pre>'; print_r($arrayDef);
    $newLine='';
    foreach($arrayDef as $line) {
        $pos = strpos($line, '--');
        if ( $pos !== false) {
            //echo $line . '<br>';
            //echo substr($line,0,$pos);
            //echo 'pos: ' . $pos . '<br>';
            if (empty(trim(substr($line,0,$pos)))) {
                continue;
            } else {
                $newLine .= ' ' . substr($line,0,$pos);
                continue;
            }
        }
        $newLine .= ' ' . $line;
    }

    return $newLine;


}