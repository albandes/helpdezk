<?php
class relDepartments extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relDepartments/");
        $access = $this->access($user, $program, $typeperson);
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);
        
        $db = new department_model();
		// 02/10/2014 15:44:16 changed by Rafael Stoever
        $select = $db->selectCorporations('','order by name');
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
		
				
        $smarty->display('relDepartments.tpl.html');
    }
	
	public function getDepartments(){
		$smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
		
		$db = new department_model();
		$where = "AND tbd.idperson = ".$_POST['id_company'];
        $select = $db->selectDepartment($where);
		
		$count = $select->RecordCount();
		if($count){
			$sel = "<option value=''>".$langVars["Select"]."</option>";
			while (!$select->EOF) {
				$sel .= "<option value='".$select->fields['iddepartment']."'>".$select->fields['department']."</option>";            
	            $select->MoveNext();
	        }
		}else{
			$sel = "<option value=''>".$langVars["No_result"]."</option>";
		}
		echo $sel;		
	}
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';		
		$pipe = new pipegrep();
		$db = new relDepartments_model();
        
//        $date_field = "sol.entry_date";
        $date_field = "apont.entry_date";
		// changed 30/09/2014 09:27 Rafael Stoever
      	$date_interval = $pipe->oracle_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $where = "AND " . $date_interval;
      
		if($_POST['cmbCompany']) {
			//$where .= " and depto.idperson = ".$_POST['cmbCompany'];
			$where .= " and sol.idperson_juridical = ".$_POST['cmbCompany'];
	    }
		if($_POST['txtDepartment'])
			$where .= " and depto.iddepartment = ".$_POST['txtDepartment'];
		$date_format = $this->getConfig('oracle_format_date');
        $rs = $db->getDepartments($date_format, $where);
//print_r($rs);
//exit;		
        $output = array();
		$total_minutes = 0;
        while (!$rs->EOF) {
        	$minutes = floatval(str_replace(',', '.', str_replace('.', '', $rs->fields['minutes'])));
            $output[] = array(
            					"code"    		=> $rs->fields['code_request'],
            					"subject"    	=> $rs->fields['subject'],
            					"att_way"    	=> $rs->fields['way'],
            					"date"  		=> $rs->fields['entry_date'],
            					"username"  	=> $rs->fields['username'],
            					"company"  		=> $rs->fields['company'],
            					"department"  	=> $rs->fields['department'],
            					"status"  		=> $rs->fields['status'],
								"minutes"  		=> $pipe->conv_minute_hour($minutes)
								
                            ) ;
							
			$total_minutes += $minutes;
            $rs->MoveNext();
        }
		$output[] = array(
							"code"    		=> 'TOTAL',
							"subject"    	=> '',
							"att_way"    	=> '',
							"date"  		=> '',
							"username"  	=> '',
							"company"  		=> '',
							"department"  	=> '',
							"status"  		=> '',
							"minutes"  		=> $pipe->conv_minute_hour($total_minutes)
							
						) ;
//print_r($output) ; exit ;
        echo json_encode($output);

    }
}
?>
