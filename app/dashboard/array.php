<?php

$vem = 'a:3:{i:0;a:4:{s:16:"mq-online-motivo";i:0;s:10:"hdk-avisos";i:0;s:15:"mq-total-online";i:0;s:9:"srv-banda";i:0;}i:1;a:3:{s:15:"hdk-grafico-sla";i:0;s:31:"hdk-grafico-solicitacoes-mensal";i:0;s:10:"cms-online";i:0;}i:2;a:2:{s:19:"cms-grafico-acessos";i:0;s:11:"cms-adsense";i:0;}}';
$vem='a:1:{i:0;a:3:{s:10:"hdk-avisos";i:0;s:9:"srv-banda";s:1:"0";s:16:"hdk-solicitacoes";s:1:"0";}}';
$widgets = unserialize(stripslashes($vem));	
print_r($widgets) ;
print "<br>";
//---

print_r($widgets[0]);
$widgets[0]["teste"] ="1";
print "<br>";
print_r($widgets[0]);
print "<br>";
print_r($widgets);

exit ;

//--
$novo = AcertaArray($widgets,"hdk-avisos") ;
print_r($novo);	

print "<br>----------------<br>";
PrintArray($widgets);
print "<br>----------------<br>";
PrintArray($novo);

exit;



function AcertaArray($widgets,$nome) {
	$novo = array();
	for($i=0;$i<3;$i++) {
		foreach ($widgets[$i] as $key=>$val) 
		{
			if($key != $nome) {
				$novo[$i][$key] = $val;
			}	
			
		}
	}
	return $novo;	
}  


function PrintArray($array) {

	if(is_array($array))
	{
		foreach($array as $key=>$value)
		{
			if(is_array($value)) {
				PrintArray($value);
			} else {
				echo "$key: $value<br>";
			}
		}
	}

}  


?>
