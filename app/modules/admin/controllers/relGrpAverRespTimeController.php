<?php
class relGrpAverRespTime extends Controllers {

    public function index() {
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        
		$db3 = new person_model();
		$select = $db3->selectPerson('and tbp.idtypeperson IN (1,3)', 'order by tbp.name ASC');
		
        while (!$select->EOF) {
            $campos[]  = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('personids', $campos);
        $smarty->assign('personvals', $valores);
        $smarty->display('relOpeAverRespTime.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';		
		$pipe = new pipegrep();
		$db = new relOpeAverRespTime_model();        
		
		$date_field = "a.entry_date";
		$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) {
			$date_interval = "AND " . $date_interval ;
		}
		if($_POST['operator'] != "ALL")
			$condition = " AND c.id_in_charge = ".$_POST['operator'];
		
        $rs = $db->getResponseTime($date_interval, $condition);
        
        $output = array();
        while (!$rs->EOF) {			
            $output[] = array(
            					"name"    		=> $rs->fields['name'],
            					"min_time" 		=> $pipe->conv_minute_hour($rs->fields['min_time']),
            					"max_time"  	=> $pipe->conv_minute_hour($rs->fields['max_time']),
            					"avg_time"  	=> $pipe->conv_minute_hour($rs->fields['avg_time'])
                            ) ;
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }
}
?>
