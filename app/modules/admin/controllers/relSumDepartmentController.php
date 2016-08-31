<?php
class relSumDepartment extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("relSumDepartment/");
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
        
        $smarty->display('relSumDepartment.tpl.html');
    }
	
	public function table_json() {
		include 'includes/classes/pipegrep/pipegrep.php';
		
		$pipe = new pipegrep();
		$db = new relSumDepartment_model();
		
        
        $date_field_hour = "sol.entry_date";
//        $date_field_hour = "apont.entry_date";
		// changed 30/09/2014 09:27 Rafael Stoever
      	$date_apt = $pipe->oracle_date_condition($date_field_hour, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		if ($date_apt) $date .= " AND" . $date_apt;
		
		if($_POST['cmbCompany'])
			$where .= "AND emp.idperson = " . $_POST['cmbCompany'];
		
		$where .= $date;
        $select = $db->getSummarizedReq($where);
		
		$output = array();
		
        while (!$select->EOF) {
            $iddepartment = $select->fields['iddepartment'];
            $idattendanceway = $select->fields['idattendanceway'];
            
			$where = "and depto.iddepartment = $iddepartment";
			$where .= " and sol.idattendance_way = $idattendanceway";

            $date_field_hour = "apont.entry_date";
           	$date_apt = $pipe->oracle_date_condition($date_field_hour, $_POST['fromdate'] , $_POST['todate'], $this->getConfig('lang')) ;
		    if ($date_apt) $date .= " AND" . $date_apt;
			$where .= $date;

			$rs = $db->getSummarizedTime($where);

	        while (!$rs->EOF) {
				// changed 30/09/2014 09:27 Rafael Stoever
				//$minutes = $pipe->conv_minute_hour($rs->fields["min"]);
				
				// changed 30/09/2014 09:27 Rafael Stoever
				$min_extras = floatval(str_replace(',', '.', str_replace('.', '', $rs->fields['min_extras'])));
				$min_extras = $min_extras + ($min_extras * $this->getConfig('fator_conversao_hs_extra') );
				$extras = $pipe->conv_minute_hour($min_extras);

				// changed 01/10/2014 09:16:13 Rafael Stoever
				$normal = floatval(str_replace(',', '.', str_replace('.', '', $rs->fields['min_normal'])));
				// $normal = $pipe->conv_minute_hour($rs->fields["min_normal"]);
				
				$minutes_total = $normal + $min_extras ;
				$minutes = $pipe->conv_minute_hour($minutes_total);

				$normal = $pipe->conv_minute_hour($normal);
				
				// changed 30/09/2014 09:27 Rafael Stoever
				$output[] = array(
									"company"  		=> $select->fields['company'],
									"department"  	=> $select->fields['department'],
									"total"			=> $select->fields['total'],
									"att_way"		=> $select->fields['att_way'],
									"min_normal"	=> $normal,
									"min_extras"	=> $extras,
									"minutes"		=> $minutes
								) ;
				
				$rs->MoveNext();
			}
			$select->MoveNext();
        }
		

        echo json_encode($output);

    }
}
?>
