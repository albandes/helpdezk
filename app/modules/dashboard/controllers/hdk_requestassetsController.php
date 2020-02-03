<?php
error_reporting(1);
class hdk_requestassets extends Controllers {

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
		
		$idwidget = $this->getParam('idwidget');
		
		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));	
		
        $smarty = $this->retornaSmarty();

		$smarty->assign('idwidget', $idwidget);
		$smarty->assign('path', path);
		$smarty->assign('refresh', $rs->fields['field1']);
		$smarty->assign('timeToRefresh', $rs->fields['field1']/60000);
		$smarty->assign('ticks', $rs->fields['field2']);
		
        $smarty->display('hdk_request_assets.tpl.html');
    }

    public function json() 
	{
		

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));
		
	
		$aStatus = array(1,3) ;
		$limit 	= count($aStatus);
		$idstatus = implode(',' , $aStatus) ;

		$bd = new hdk_requestsassets_model();
		

			$dt = "2016-05-17 08:00:00";
			$dt = date('Y-m-d H:i:s');
			$dtinicio = $dt ." -20 hour";
			$dtinicio = date('Y-m-d H:i:s', strtotime($dtinicio));

			$aDatas = array();
			$i = 1;
			$j = 0;
			for ($i = 1; $i <= 80; $i++) 
			{	
				$j = $j + 15; 
				$dtquery = $dtinicio ." +$j minute";
				$dtquery = date('Y-m-d H:i:s', strtotime($dtquery));

				$rset = $bd->getStatusByEntryDate('1',$dtquery);
				$aDatas[$rset->fields['status_name']][$dtquery] = $rset->fields['total']  ;
				//print_r($aDatas);
				$temp[$i]       = $rset->fields['total'] ;
				
				//$aDatas[$i]    = strtotime(date('Y-m-d H:i:s', strtotime($dtquery)) . " UTC") * 1000 ;
				
			}
			//print_r($aDatas); die();
			$json  = "[";
			$json .= $this->montajson(utf8_encode("Novas"),$temp,$aDatas);
			
			$json .= "]";  
			
			echo $json;
			exit;
			
		
		
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