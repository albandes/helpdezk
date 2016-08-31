<?php
class relOpeAverAttenTime extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relOpeAverAttenTime/");
        $access = $this->access($user, $program, $typeperson);
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);


        //
        $db_comp = new department_model();
        $select = $db_comp->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
        $campos = '';
        $valores = '';
        //

		$db3 = new person_model();
		$select = $db3->selectPerson('and tbp.idtypeperson IN (1,3)', 'order by tbp.name ASC');
		
        while (!$select->EOF) {
            $campos[]  = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('personids', $campos);
        $smarty->assign('personvals', $valores);
        $smarty->display('relOpeAverAttenTime.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relOpeAverAttenTime_model();     
		
		$date_field = "a.entry_date";
		$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) {
			$date_interval = "AND " . $date_interval ;
		}
		if($_POST['operator'] != "ALL")
			$condition = " AND c.id_in_charge = ".$_POST['operator'];
		//
        if($_POST['cmbCompany'])
            $condition .= " AND a.idperson_juridical = ".$_POST['cmbCompany'];
        //
        $rs = $db->getResponseTime($date_interval, $condition);
        
        $output = array();
		$i = 0;
        while (!$rs->EOF) {			
            $output['result'][] = array(
            					"name"    		=> $rs->fields['name'],
            					"company"    		=> $rs->fields['company'],
            					"min_time" 		=> $pipe->conv_minute_hour($rs->fields['min_time']),
            					"max_time"  	=> $pipe->conv_minute_hour($rs->fields['max_time']),
            					"avg_time"  	=> $pipe->conv_minute_hour($rs->fields['avg_time'])
                            ) ;
			
			$min += $rs->fields['min_time'];
			$max += $rs->fields['max_time'];
			$avg += $rs->fields['avg_time'];
			$i++;
            $rs->MoveNext();
        }
		if($i != 0){
		$output['avg'] = array(
								"min_avg" => $pipe->conv_minute_hour($min/$i),
								"max_avg" => $pipe->conv_minute_hour($max/$i),
								"avg_avg" => $pipe->conv_minute_hour($avg/$i)
							);
		}
        echo json_encode($output);

    }
}
?>
