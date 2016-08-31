<?php
class hdk_sla extends Controllers {

    public function home() {
        include 'includes/config/config.php';
		
		$idwidget = $this->getParam('idwidget');
        $smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		$smarty->assign('idwidget', $idwidget);
		$smarty->assign('message', $langVars['Dashboard_UpdatedDaily']);
        $smarty->display('hdk_sla.tpl.html');
    }

    public function json() 
	{
        include 'includes/config/config.php';
		
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();

		$bd = new dash_model();
		$rs = $bd->getWidgetParam($this->getParam('idwidget'));
		
		$year	= $rs->fields['field1'];		
		
		
		$bd = new hdk_sla_model();

		$rs = $bd->getSla();

		if ($license == 200701006)	{
			$where .= "				and idservice <> 251";
		}		
		
		$tot = 	$rs->fields['intime'] + $rs->fields['outoftime'] ;
		$inper = round(($rs->fields['intime']*100) / $tot,2) ;
		$intime  = $rs->fields['intime'] . " [$inper%]";
		$outper = round(($rs->fields['outoftime']*100) / $tot,2) ;
		$outoftime  = $rs->fields['outoftime'] . " [$outper%]";
		
		$uptime = $rs->fields['datetimeupdate'] ;
		
		$json  = "[" ;
		$json .= "{\"label\":\" ".$langVars['Dashboard_SLAFulfillment']." - $intime\", \"data\":".$rs->fields['intime']."}"  . "," ;
		$json .= "{\"label\":\" ".$langVars['Dashboard_SLANotFulfillment']." - $outoftime\", \"data\":".$rs->fields['outoftime']."}"   ;
		$json .= "]";

		echo $json;

    }
	
}




?>