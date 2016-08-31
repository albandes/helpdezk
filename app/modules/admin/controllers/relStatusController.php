<?php
class relStatus extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relStatus/");
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
				
        $smarty->display('relStatus.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relStatus_model();
        
        $date_field = "sol.entry_date";
      	$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $where = "AND " . $date_interval;
      
		if($_POST['cmbCompany'])
			$where .= " and sol.idperson_juridical = ".$_POST['cmbCompany'];
		
		if($_POST['cmbPerson'])
			$where .= " AND solgrup.id_in_charge = ".$_POST['cmbPerson'];
		
        $rs = $db->getStatusRequest($where);
		
        $output = array();
        while (!$rs->EOF) {        	
			
            $output[] = array(
            					"status"    => $rs->fields['user_view'],
            					"qtd"  		=> $rs->fields['QTD_SOLICITACAO']
                            ) ;
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }
}
?>
