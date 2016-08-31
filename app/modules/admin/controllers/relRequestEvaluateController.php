<?php
class relRequestEvaluate extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relRequestEvaluate/");
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
		
		$campos = "";
		$valores = "";
		
		$db = new evaluation_model();
        $select = $db->selectEvaluation();
        while (!$select->EOF) {
            $campos[] = $select->fields['idevaluation'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('evalids', $campos);
        $smarty->assign('evalvals', $valores);
		
		
        $smarty->display('relRequestEvaluate.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
    			
		$pipe = new pipegrep();
		$db = new relRequestEvaluate_model();
        
        $date_field = "req.entry_date";
      	$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $where = "AND " . $date_interval;
      
		if($_POST['cmbCompany'])
			$where .= " and req.idperson_juridical = ".$_POST['cmbCompany'];
		
		if($_POST['cmbPerson'])
			$where .= " AND req_charge.id_in_charge = ".$_POST['cmbPerson'];
		
		IF($_POST['cmbEvaluate'])
			$where .= " AND reqeva.idevaluation = ". $_POST['cmbEvaluate'];
		
        $rs = $db->getRequestsEvaluate($where, $langVars['Observation']);	
		
				
        $output = array();
        while (!$rs->EOF) {
        	if($rs->fields['obs'] > 0) {
        		$rs->fields['obs'] = $this->getArrayObs($rs->fields['code_request']);
			}
			
            $output[] = array(
            					"code_request"  => $rs->fields['code_request'],
            					"user"  		=> $rs->fields['user'],
            					"operator"  	=> $rs->fields['operator'],
            					"company"  		=> $rs->fields['company'],
            					"subject"  		=> $rs->fields['subject'],
            					"date"  		=> $rs->fields['date'],
            					"evaluation"  	=> $rs->fields['evaluation'],
            					"obs"  			=> $rs->fields['obs']            					
                            ) ;
			
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }
    
    
    public function getArrayObs($code){
    	
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
    	
		$db = new relRequestEvaluate_model();
		$obsLang = $langVars['Observation'];
		$rs = $db->getObsEvaluate($code, $obsLang);
		
		$description = str_replace("<strong>".$obsLang.":</strong>","", $rs->fields['description']);
		$description = str_replace("<p><b>".$langVars['Request_closed']."</b></p>","",$description);
		
		while (!$rs->EOF) {			
            $output[] = array(
            					"date"  		=> $this->formatDate($rs->fields['entry_date']),
            					"description"  	=> strip_tags($description)
                            ) ;
			
            $rs->MoveNext();
        }     
		
		return $output;
		
    }
    
}
?>
