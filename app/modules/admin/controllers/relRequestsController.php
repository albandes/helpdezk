<?php
class relRequests extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relRequests/");
        $access = $this->access($user, $program, $typeperson);
        
        $smarty = $this->retornaSmarty();

        $db = new logos_model();

        $reportslogo = $db->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

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
		
		
		
		$db_priority = new priority_model();
        $select = $db_priority->selectPriority();
		
        while (!$select->EOF) {
            $campos[] = $select->fields['idpriority'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('priorityids', $campos);
        $smarty->assign('priorityvals', $valores);
		$campos = '';
        $valores = '';	
					
		$db3 = new person_model();
        $select = $db3->selectPerson(null, "ORDER BY name ASC");
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('personids', $campos);
        $smarty->assign('personvals', $valores);
		$campos = '';
        $valores = '';	
		
		$db_areas = new services_model();
        $rs = $db_areas->selectAreas();
		
		while (!$rs->EOF) {
            $down = $db_areas->getTypeFromAreas($rs->fields['idarea']);
			if ($down->fields) {
				while (!$down->EOF) {
					$campos[] = $down->fields['type'];
            		$valores[] = $down->fields['type_name'];
					$down->MoveNext();
				}
			}
			$rs->MoveNext();
		}
		$smarty->assign('typesids', $campos);
        $smarty->assign('typesvals', $valores);
		$campos = '';
        $valores = '';	
		
		
		/*
        $select = $db3->selectPerson("AND tbp.idtypeperson IN(1,3)", "ORDER BY name ASC");
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('operatorids', $campos);
        $smarty->assign('operatorvals', $valores);
		$campos = '';
        $valores = '';	
		*/
		
		
        $smarty->display('relRequests.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new request_model();
        
		$date_field = "req.entry_date";
      	$date_interval = $pipe->mysql_date_condition($date_field, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_interval) $date = "AND " . $date_interval;
		        
		if($_POST['status'])
			$where = "AND req.idstatus = ".$_POST['status'];
		
		if($_POST['txtowner'])
			$where .= " AND req.idperson_owner = ".$_POST['txtowner'];
		
		if($_POST['cmbCompany'])
			$where .= " AND req.idperson_juridical = ".$_POST['cmbCompany'];
		
		if($_POST['txtPriority'])
			$where .= " AND req.idpriority = ".$_POST['txtPriority'];
		
		if($_POST['cmbType'])
			$where .= " AND req.idtype = ".$_POST['cmbType'];
		
		if($_POST['cmbItem'])
			$where .= " AND req.iditem = ".$_POST['cmbItem'];
		
		if($_POST['cmbService'])
			$where .= " AND req.idservice = ".$_POST['cmbService'];		
		$date_format = $this->getConfig('date_format');
        $rs = $db->getReport($date_format, $date, $where);
        
        $output = array();
        while (!$rs->EOF) {
        	
			$repass = $db->getRepassVal($rs->fields['code_request']);
            $output[] = array(
            					"code"    		=> $rs->fields['code_request'],
            					"name"  		=> $rs->fields['name'],
            					"company"  		=> $rs->fields['company'],
            					"subject"  		=> $rs->fields['subject'],
            					"entry_date"  	=> $rs->fields['date'],
            					"priority"  	=> $rs->fields['priority'],
            					"status"  		=> $rs->fields['status'],
            					"repass"  		=> $repass->RecordCount()
                            ) ;
            $rs->MoveNext();
        }     
        echo json_encode($output);

    }
	
	public function getItem(){
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();		
		$id = $this->getParam('id');		
		$db = new services_model();
        $rs = $db->selectItens($id);
		$count = $rs->RecordCount();
		if($count){
			$option = "<option value=''>".$langVars['Select']."</option>";
			while (!$rs->EOF) {
				$option .= "<option value='".$rs->fields['item']."'>".$rs->fields['item_name']."</option>";
	           	$rs->MoveNext();
	        }
		}else{
			$option = "<option value=''>".$langVars['No_result']."</option>";
		}
		echo $option;
	}
	
	public function getService(){
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();		
		$id = $this->getParam('id');		
		$db = new services_model();
        $rs = $db->selectServices($id);
		$count = $rs->RecordCount();
		if($count){
			$option = "<option value=''>".$langVars['Select']."</option>";
			while (!$rs->EOF) {
				$option .= "<option value='".$rs->fields['service']."'>".$rs->fields['service_name']."</option>";
	           	$rs->MoveNext();
	        }
		}else{
			$option = "<option value=''>".$langVars['No_result']."</option>";
		}
		echo $option;
	}
	
}
?>
