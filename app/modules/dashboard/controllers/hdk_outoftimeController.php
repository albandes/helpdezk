<?php
class hdk_outoftime extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$idwidget = $this->getParam('idwidget');
        $smarty = $this->retornaSmarty();
		$smarty->assign('idwidget', $idwidget);
        $smarty->display('hdk_outoftime.tpl.html');
    }

    public function json() 
	{
        include 'includes/config/config.php';

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));
		
		$year	= $rs->fields['field1'];		

		$aStatus = array(1,5,3) ;
		$limit 	= count($aStatus);
		$idstatus = implode(',' , $aStatus) ;
		
		
		$bd = new hdk_outoftime_model();
		$where = 	"
					where idstatus In($idstatus)
						and now() >= expire_date
						and year(expire_date) = '$year'
					" ;
		if ($license == 200701006)	{
			$where .= "				and idservice <> 251";
		}		
		$rs = $bd->getTotalRequests($where);
		$vencidas = $rs->fields['total']; 

		$where = 	"
					where date(expire_date) = date(now())
					" ;
		if ($license == 200701006)	{
			$where .= "				and idservice <> 251";
		}		
					
		$rs = $bd->getTotalRequests($where);
		$vencendohoje = $rs->fields['total']; 

		$where = 	"
						where idstatus In(1)
					" ;
		if ($license == 200701006)	{
			$where .= "				and idservice <> 251";
		}		
					
		$rs = $bd->getTotalRequests($where);
		$novas = $rs->fields['total']; 
		
		$json  = "[" ;
		//$json .= "{\"label\":\"Novas\", \"data\":".$novas."}"  . "," ;
		$json .= "{\"label\":\"Vencidas - ".$vencidas."\", \"data\":".$vencidas."}"  . "," ;
		$json .= "{\"label\":\"Vencendo Hoje - ".$vencendohoje."\", \"data\":".$vencendohoje."}"   ;
		$json .= "]";

		echo $json;

    }
	
}




?>