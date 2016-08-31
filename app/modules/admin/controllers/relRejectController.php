<?php
class relReject extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relReject/");
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
        $select = $db->selectPerson(null, "ORDER BY name ASC");
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('personids', $campos);
        $smarty->assign('personvals', $valores);
				
        $smarty->display('relReject.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relReject_model();
        
        $date_field = "sol.entry_date";
      	$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $where = "AND " . $date_interval;
      
		if($_POST['cmbCompany'])
			$where .= " and sol.idperson_juridical = ".$_POST['cmbCompany'];
		
		if($_POST['cmbPerson'])
			$where .= " AND sol.idperson_creator = ".$_POST['cmbPerson'];
		
        $rs = $db->getRejectRequest($where);
		
        $output = array();
        while (!$rs->EOF) {
        	
			//$motivo = stripslashes(strip_tags(strrchr($rs->fields['description'], "<b>Solicita&ccedil;&atilde;o n&atilde;o pode ser atendida:</b>")));
			
			$motivo = strip_tags($rs->fields['description']);
			$motivo = str_replace("Solicita&ccedil;&atilde;o n&atilde;o pode ser atendida:","",$motivo);
			$motivo = str_replace("Request Could not be attended: ","",$motivo);
			
            $output[] = array(
            					"code"    		=> $rs->fields['code_request'],
            					"subject"  		=> $rs->fields['subject'],
            					"description"  	=> $motivo
                            ) ;
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }
}
?>
