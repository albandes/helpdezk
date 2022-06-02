<?php

class cms_sourceforgecountrys extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$idwidget = $this->getParam('idwidget');
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	
		
        $smarty = $this->retornaSmarty();

		$smarty->assign('idwidget', $idwidget);
		$smarty->assign('urlJSON', $rs->fields['field1']);
		$smarty->assign('refresh', $rs->fields['field2']);
		$smarty->assign('timeToRefresh', $rs->fields['field2']/60000);
		$smarty->assign('days', $rs->fields['field3']);
		$smarty->assign('ticks', $rs->fields['field4']);		
        $smarty->display('cms_sourceforgecountrys.tpl.html');
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
		
		$days	= 180;
		$limit 	= 6;

		$bd = new cms_sourceforgecountrys_model($dbhost, $dbuser, $dbpass, $dbname);
        $rs = $bd->getDownloads($days,$limit);
		
        if ($rs->fields) 
		{
			$i = 1;
			while (!$rs->EOF) 
			{	
				$vlrAnterior = 0;
				$rset = $bd->getDownloadsByCountry($rs->fields['idcountry'],$days);
				$timestamp = strtotime('-'.$days. ' days');
				$dtinicio = date('Y-m-d', $timestamp) ;
				// print $dtinicio ;
				$k=1;
				while (!$rset->EOF) 
				{

					$aDatas[$rset->fields['name']][$rset->fields['date']] 	=  $rset->fields['total'];
					$k++;
					$rset->MoveNext();
				}
				$rs->MoveNext();
				$i++;

			}
			
			$timestamp = strtotime('-'.$days. ' days');
			$dtinicio = date('Y-m-d', $timestamp) ;

			$cont = 0 ;
			$first = true ;
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
		}
		$json .= "}";	
		return $json;
	}
	
	
}




?>