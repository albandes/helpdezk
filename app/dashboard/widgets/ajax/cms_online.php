<?php
error_reporting(E_ALL);
require("../../../../adodb/adodb.inc.php");

// pegar da tabela de widgets -
$user  = 'futeboldaqui' ;
$senha = 'qpal10';
$banco = 'futeboldaqui';
$host  = '189.38.85.153';
$idsite = 1;

date_default_timezone_set('America/Sao_Paulo');


$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco"); 



//$IDTEMPLATE = (int)$_GET['F_IDTEMPLATE'];


$online = visitantesOnline($db,$idsite);
//$output['text']= $online . " visitantes as " . date("H") . "h ". date("i") . "min " . date("s") . "s" ;    
$output['texto']=  "<br>On line: " .$online . utf8_encode(" às ") . date("H:i:s");
echo json_encode($output);

die();

	/**
	* Função que retorna o total de visitantes online
	*/
	function visitantesOnline($db, $idsite) {
		$sql = 	"
				SELECT 	COUNT(*) as tot 
				FROM 	cms_tbvisitasonline 
				WHERE 	idsite=$idsite
				";
		$rs = $db->Execute($sql);
		if(!$rs) { die("Erro [site.class.php]: " . $db->ErrorMsg() ."<br>".$sql."<br>");	}	
		
		$resultado = $rs->fields['tot'];
		
		// Retorna o valor encontrado ou zero
		//return (!empty($resultado)) ? (int)$resultado[0] : 0;
		return $rs->fields['tot'] ;
	}
?>