<?php
class relWorkedRequests extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relWorkedRequests/");
        $access = $this->access($user, $program, $typeperson);        
        $smarty = $this->retornaSmarty();
		
		$db = new logos_model();
        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        

        $db2 = new status_model();
        $select = $db2->selectStatus();
        while (!$select->EOF) {
            $campos[] = $select->fields['idstatus'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('statusids', $campos);
        $smarty->assign('statusvals', $valores);
		$campos = '';
        $valores = '';	
		
		$db = new person_model();
        $select = $db->selectPerson("AND tbp.idtypeperson IN(1,3)", "ORDER BY name ASC");
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('personids', $campos);
        $smarty->assign('personvals', $valores);
		
        $smarty->display('relWorkedRequests.html.tpl');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		$pipe = new pipegrep();
		$db = new relWorkedRequests_model();
        
		$date_field = "l.date";
      	$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $date = "AND " . $date_interval;
		        
		if($_POST['cmbPerson'])
			$where = " AND l.idperson = ".$_POST['cmbPerson'];
		
		if($_POST['status'])
			$where .= " AND l.idstatus = ".$_POST['status'];
		
			
		$date_format = $this->getConfig('date_format');
        $rs = $db->getReport($date_format, $date, $where);
        
        $output = array();
        while (!$rs->EOF) {
    
            $output[] = array(
            					"code"    	=> $rs->fields['code_request'],
            					"name"  	=> $rs->fields['name'],
            					"subject"  	=> $rs->fields['subject'],
            					"date"  	=> $rs->fields['date'],
            					"status"  	=> $rs->fields['status']
                            );			
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }	
	
}