<?php
class maq_coletora extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$idwidget = $this->getParam('idwidget');
        $smarty = $this->retornaSmarty();
		$smarty->assign('idwidget', $idwidget);
        $smarty->display('maq_coletora.tpl.html');
    }

    public function json() 
	{
        include 'includes/config/config.php';

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));
		$dbhost = $rs->fields['dbhost'];
		$dbuser = $rs->fields['dbuser'];
		$dbpass = $rs->fields['dbpass'];
		$dbname = $rs->fields['dbname'];
		
		$days	= $rs->fields['field1'];


		$bd = new maq_coletora_model($dbhost, $dbuser, $dbpass, $dbname);
        $rs = $bd->getBilhetesColetora($days);
		
        if ($rs->fields) 
		{
			$i=1;
			while (!$rs->EOF) {
					$valor[$i] 	= $rs->fields['valor']; 
					$hora[$i] 	= strtotime($rs->fields['dtestatcoletora'] . " UTC") * 1000 ; 
					$rs->MoveNext();
					$i++;
			}
				
			$json  = "[";
			$json .= $this->montajson(utf8_encode("Alunos"),$valor,$hora);
			$json .= "]";
			echo $json;

		}	
    }
    
	public function montajson($label,$array,$hora) 
	{
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
		}
		$json .= "}";	
		return $json;
	}
	
}




?>