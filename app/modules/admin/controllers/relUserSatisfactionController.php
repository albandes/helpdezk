<?php
class relUserSatisfaction extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relUserSatisfaction/");
        $access = $this->access($user, $program, $typeperson);
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        
        $db = new department_model();
        $select = $db->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
		
		$campos = "";
		$valores = "";
		
		$db = new person_model();
        $select = $db->selectPerson("AND tbp.idtypeperson IN(1,3)", "ORDER BY name ASC");
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('personids', $campos);
        $smarty->assign('personvals', $valores);
				
        $smarty->display('relUserSatisfaction.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relUserSatisfaction_model();
        
        $date_field = "req.entry_date";
      	$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $where = "AND " . $date_interval;
      
		if($_POST['cmbCompany'])
			$where .= " and req.idperson_juridical = ".$_POST['cmbCompany'];
		
		if($_POST['cmbPerson'])
			$where .= " AND req_charge.id_in_charge = ".$_POST['cmbPerson']." AND req_charge.ind_in_charge = 1";
		
        $rs = $db->getUserSatisfaction($where);	
		
		while (!$rs->EOF) {
        	$percent_total += $rs->fields['total'];
			$rs->MoveNext();
        }
		$rs->MoveFirst();
		
        $output = array();
        while (!$rs->EOF) {
            $output[] = array(
            					"evaluation"  	=> $rs->fields['name'],
            					"requests"  	=> $rs->fields['total'],
            					"percent"		=> number_format($rs->fields['total'] * 100 / $percent_total, 2, ",", ".")."%"
                            ) ;							
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }
}
?>
