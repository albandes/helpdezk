<?php
error_reporting(1);
class hdk_harduser extends Controllers {

    public function home() {
        include 'includes/config/config.php';

		$idwidget = $this->getParam('idwidget');

        $smarty = $this->retornaSmarty();

		$smarty->assign('idwidget', $idwidget);
        $smarty->display('hdk_harduser.tpl.html');
    }

    public function json() 
	{
        include 'includes/config/config.php';

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));

		$year	= $rs->fields['field1'];		
		$limit  = $rs->fields['field2'] ; 

		$bd = new hdk_harduser_model();
        $rs = $bd->getUserByRequests($year,$limit);

		$inicio = date("Y"). "-01-01";
		$fim = date("Y-m-d") ;
		$days = $this->dataNumeroDias($inicio,$fim);

        if ($rs->fields) 
		{
			$i = 1;
			while (!$rs->EOF) 
			{	
				$vlrAnterior = 0;
				$rset = $bd->getRequestsByDay($rs->fields['idperson_creator'],$year);

				$k=1;
				while (!$rset->EOF) 
				{
					$name = utf8_decode($rs->fields['name']) ;
					$aDatas[$name][$rset->fields['entry_date']] 	=  $rset->fields['total'];
					$k++;
					$rset->MoveNext();
				}
				$rs->MoveNext();
			}
			$timestamp = strtotime('-'.$days. ' days');
			$dtinicio = date('Y-m-d', $timestamp) ;

			foreach ($aDatas as $i1 => $n1)     
			{
				$first = true ;
				$ant = 0 ;
				for ($k = 1; $k <= $days; $k++) 
				{
					if ($first) {
						$dtcompara = $dtinicio;
						$first = false;
						$ant = 0 ;
					} else {
						$adiciona = $dtinicio ."+". $k . " days";
						$compara = strtotime($adiciona) ;
						$dtcompara = date('Y-m-d', $compara);
					}

					if (array_key_exists($dtcompara, $aDatas[$i1]) )
					{
						$aDatas[$i1][$dtcompara] = $aDatas[$i1][$dtcompara] + $ant;
						$ant = $aDatas[$i1][$dtcompara] ;
					}
					else 
					{
						$aDatas[$i1][$dtcompara] = $ant;
					}
				}
			}
			
			
			$timestamp = strtotime('-'.$days. ' days');
			$dtinicio = date('Y-m-d', $timestamp) ;

			$json  = "[";
			$cont=0;
			foreach ($aDatas as $i1 => $n1)     
			{
				$cont++;
				for ($k = 1; $k <= $days; $k++) 
				{
					$adiciona = $dtinicio ."+". $k . " days";
					$compara = strtotime($adiciona) ;
					$dtcompara = date('Y-m-d', $compara);
					if (array_key_exists($dtcompara, $aDatas[$i1]) )
					{
						$imp = $aDatas[$i1][$dtcompara] ;
					} 
					else 
					{
						$imp = 0 ;	
					}
					$aImpressoes[$k] 	= $imp; 		
					$aEixoX[$k] 		= strtotime($dtcompara . " UTC") * 1000; 
				}
					
				$json .= $this->montajson(utf8_encode($i1),$aImpressoes,$aEixoX);
				if ($k != $days and $cont !=$limit )
				{
					$json .= ",";
				}	

			}
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


	public function diasDatas($data_inicial,$data_final)
	{
		$time_inicial = strtotime($data_inicial);
		$time_final = strtotime($data_final);
		// Calcula a diferença de segundos entre as duas datas:
		$diferenca = $time_final - $time_inicial; 
		// Calcula a diferença de dias
		$dias = (int)floor( $diferenca / (60 * 60 * 24)); 
		return $dias;
	}
	

	
}




?>