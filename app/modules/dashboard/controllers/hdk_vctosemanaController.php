<?php
class hdk_vctosemana extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$idwidget = $this->getParam('idwidget');
        $smarty = $this->retornaSmarty();
		$smarty->assign('idwidget', $idwidget);
        $smarty->display('hdk_vctosemana.tpl.html');
    }

    public function json() 
	{
        include 'includes/config/config.php';
		/*
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));
		$dbhost = $rs->fields['dbhost'];
		$dbuser = $rs->fields['dbuser'];
		$dbpass = $rs->fields['dbpass'];
		$dbname = $rs->fields['dbname'];
		*/
		
		$days	= 7;
		
		$bd = new hdk_vctosemana_model();
		$i = 0	;
		$k = 0;
		while (1==1) {
			$i++;
			if ( $i == 1) {
				$dias = 0 ;
			} else {
				$dias = $i-1;
				
			}	
			
			$data = date('Y-m-d', strtotime("+$dias days"));
			list($ano, $mes, $dia) = explode("-", $data);
			$diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));
			if ($diasemana == 0 or $diasemana == 6) {
				continue ;
			} else {
				$k++;
				$rs = $bd->getExpiraSolicitacao($data);
				$valor[$k] 	= $rs->fields['valor']; 
				//$hora[$k] 	= $dia;
				$hora[$k]   = strtotime($data . " UTC") * 1000;
				if ($k==$days+1) {break;};
				/*
				echo $data ;
				print "<br>";
				echo $rs->fields['valor'];
				print "<br>";
				*/
			}	
		}

		$json  = "[";
		$json .= $this->montajson(utf8_encode("Solicitações"),$valor,$hora);
		$json .= "]";
		
		echo $json;

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
			$json .= "[\"" . $hora[$i] . "\"," . $array[$i] . "]";
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