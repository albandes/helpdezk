<?php
require_once '../../../../includes/adodb/adodb.inc.php';
require_once '../../../../includes/config/config.php';

$dsn = 'mysql://' . $db_username  . ':' . $db_password . '@' . $db_hostname . '/' . $db_name  .''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conex�o com o banco");   

$sql = 	"
		select
		   title,
		   url
		from dsh_tbcategory
		";
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

$aCat['categories'] = array();
$aCat['categories']['category'] = array();

$i = 1;
while (!$rs->EOF) 
{	
	$tmp = array(
			'id' => $i,
			'title' => $rs->fields['title'],
			'amount' => 1,
			'url' => $rs->fields['url']
		);
		
	array_push($aCat['categories']['category'], $tmp);
	
	$i++;
	$rs->MoveNext();
}

echo json_encode($aCat) ;
/*
CREATE TABLE `dsh_tbcategory` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;



insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (1,'Helpdezk','../../../app/modules/dashboard/jsonfeed/category1_widgets.json');
insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (2,'General','../../../app/modules/dashboard/jsonfeed/category2_widgets.json');

/branch/dashboard/index/getWidgets


*/

?>

