<?php
error_reporting(1);
$db = "_hdk-install";
//$db = "hdk-develop";

$file       = 'pt_BR.txt' ;
$langPatch  = "E:/home/rogerio/htdocs/git/helpdezk/app/lang/";
$idmodule   = 2;

if (substr($file, 0, 2) == 'en') {
	$idLocale = 19;
} elseif (substr($file, 0, 2) == 'pt') {
    $idLocale = 77;
}

//$charset= "utf8";
$charset= "latin1";

//$dsn = "mysql:host=localhost;dbname={$db};charset={$charset}";
$dsn = "mysql:host=localhost;dbname={$db}";
$user = 'root';
$password = '';

try {
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}


$lb="<br>";
$lines = file ($langPatch . $file,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
echo '<pre>';


$sql = 	"
    SELECT 
      key_value 
    FROM
      tbvocabulary 
    WHERE key_name = 'MY_Tickets' 
    AND idlocale = 77
			";
	try {
		//$select = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $display = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
		echo $k . " -> " . $display['key_value'] . $lb ;
                
        
        $display = $conn->query($sql)->fetch(PDO::FETCH_OBJ);
		echo $k . " -> " . $display->key_value . $lb ;
        
        $check['error'] = $display->key_value;
        
       die( '{"result":' . json_encode($check, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . '}'); 
        
        
	} catch (PDOException $e) {
        echo $e->getMessage();
    }




die('end ');

foreach ($lines as $line_num => $line) {
    if (substr($line, 0, 1) == '#')
        continue;

	$aLine = explode("=", $line);
	$temp =   str_replace("\"" ,"",ltrim($aLine[1])) ;
    $rows[$aLine[0]]  = $temp;
}





foreach ($rows as $k => $v) {
    if (substr($line, 0, 1) == '#' || $line == '')
        continue;
	$value = addslashes($v);
	$sql = 	"
            SELECT
              idvocabulary,
              idlocale,
              idmodule,
              key_name,
              key_value
            FROM tbvocabulary	
            WHERE
                key_name = '$k'
            AND idlocale = $idLocale
            AND idmodule = '$idmodule'
			";
	try {

		$select = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$count = count($select);
		if($count > 0) {
			echo $k . " -> " . $count . $lb ;
		} else {
			//echo $select[0]['name'] . $lb;
			$idcategory = $select[0]['idcategory'] ;

			$name  = $select[0]['name'] ;

			
			$q   = 	"
                    INSERT INTO tbvocabulary
                    (
                      idlocale
                     ,idmodule
                     ,key_name
                     ,key_value
                    )
                    VALUES
                    (
                     
                      '$idLocale'    
                     ,'$idmodule'              
                     ,'$k'           
                     ,'$value'       
                    )			
					";
            try {
                $executa = $conn->query($q);
            }
            catch(Exception $e) {
                echo 'Exception -> ';
                var_dump($e->getMessage());
                die($q);
            }
		}	
	} 
	catch(Exception $e) {
		echo 'Exception -> ';
		var_dump($e->getMessage());	
		die($sql);
	}
}

die($lb.'End Script');

