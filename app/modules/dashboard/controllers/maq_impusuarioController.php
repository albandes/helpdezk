<?php
class maq_impusuario extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	
		
		$idwidget = $this->getParam('idwidget');
        $smarty = $this->retornaSmarty();
		$smarty->assign('path', path);
		$smarty->assign('idwidget', $idwidget);

		$smarty->assign('refresh', $rs->fields['field3']);
		$smarty->assign('timeToRefresh', $rs->fields['field3']/60000);

        $smarty->display('maq_impusuario.tpl.html');
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
		$limit 	= $rs->fields['field2'];

		$bd = new maq_impusuario_model($dbhost, $dbuser, $dbpass, $dbname);
        $rs = $bd->getPrinting($days,$limit);
		
        if ($rs->fields) 
		{
			$i=1;
			while (!$rs->EOF) {
				$rset = $bd->getPrintingByDay($rs->fields['user'] , $days);
				$k=1;
				while (!$rset->EOF) 
				{
					$aDatas[$rs->fields['user']][$rset->fields['dtjob']] 	= $rset->fields['paginas'] ;
					$k++;
					$rset->MoveNext();
				}
				$aHora[$i] 	= strtotime($rs->fields['dtjob'] . " UTC") * 1000 ; 	
				$rs->MoveNext();
				$i++;
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

			$json  = "[";

			$cont=0;
			foreach ($aDatas as $i1 => $n1)     
			{
				//print $i1 . "<br>";
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
	
}




?>