<?php
//error_reporting(E_ALL);
session_start();
class hdk_requests extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		if(substr($path_default, 0,1)!='/'){
			$path_default='/'.$path_default;
		}

		if ($path_default == "/..") {   
			define(path,"");
		} else {
			define(path,$path_default);
		}
		
        $smarty = $this->retornaSmarty();
		$smarty->assign('path', path);
        $smarty->display('hdk_requests.tpl.html');
    }

    public function json() 
	{
        include 'includes/config/config.php';
		$this->validasessao();
		if (substr($path_default, 0, 1) != '/') {
			$path_default = '/' . $path_default;
		}
		define('path', $path_default);
		// no caso localhost document root seria D:/xampp/htdocs
		$document_root = $_SERVER['DOCUMENT_ROOT'];
		if (substr($document_root, -1) != '/') {
			$document_root = $document_root . '/';
		}
		
		$bd = new hdk_requests_model();
		
		define('DOCUMENT_ROOT', $document_root);
		$analista = $_SESSION['SES_COD_USUARIO'];
		
		$rs = $bd->getOperatorGroups($_SESSION['SES_PERSON_GROUPS']);

		while (!$rs->EOF) {
            $idPersonGroups .= ',' . $rs->fields['idperson'];
            $rs->MoveNext();
        }
		$rResult = $bd->getRequests($_SESSION['SES_COD_USUARIO'],$idPersonGroups, $date_format,  $hour_format);
		//$iFilteredTotal = $rResult->RecordCount();
		$iFilteredTotal = 100;
		$sOutput = '{';
		$sOutput .= '"sEcho": ' . intval($_GET['sEcho']) . ', ';
		$sOutput .= '"iTotalRecords": ' . $iFilteredTotal . ', ';
		$sOutput .= '"iTotalDisplayRecords": ' . $iFilteredTotal . ', ';
		$sOutput .= '"aaData": [ ';

		$i = 0;
		while (!$rResult->EOF) {
			$sOutput .= "[";
			//$sOutput .= '"<img src=\"'.path.'/includes/classes/DataTables/media/images/details_open.png\">",';

			$date = $rResult->fields['date'];
			$time = $rResult->fields['hour'];
			
			$sOutput .= '"' . str_replace('"', '\"', $date." - ".$time) . '",';
			$sOutput .= '"' . str_replace('"', '\"', $rResult->fields['subject']) . '",';
			$sOutput .= '"' . '<a href=\"javascript:;\" onclick=\"openRequest('.$rResult->fields['code_request'].')\">' .  $rResult->fields['code_request'] . '</a>",';
			$sOutput .= '"' . $rResult->fields['expire_date'] . '" ';
			$i++;

			$sOutput .= "],";

			$rResult->MoveNext();
		}
		$sOutput = substr_replace($sOutput, "", -1);
		$sOutput .= '] }';

		echo $sOutput;
		
    }
		
    
public function montajson($label,$array,$hora) {

	$max = count($array);
	$json .= "{";	
	$json .= " \"label\": \"" . $label . "\",";
	$k=0;
	for($i=$max; $i>=1; $i--)
	{
		if ($i==$max) {
			$json .= "\"data\":[";
		}
		$json .= "[" . $hora[$i] . "," . $array[$i] . "]";
		if ($i > 1) 
		{
			$json .= ", " ; 
		}
		if ($i == 1) 
		{
			$json .= "]" ; 
		}
		$k++;
		//print $minimo[$i] . "<br />";
	}
	$json .= "}";	
	
	return $json;
}
	
	
}




?>