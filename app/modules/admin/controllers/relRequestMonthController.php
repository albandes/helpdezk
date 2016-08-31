<?php
class relRequestMonth extends Controllers {

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
		$campos = "";
		$valores = "";
		
		$dbm = new relRequestMonth_model();
		$select = $dbm->getYears();
		while (!$select->EOF) {
            $campos[] = $select->fields['year'];
            $valores[] = $select->fields['year'];
            $select->MoveNext();
        }
        $smarty->assign('yearids', $campos);
        $smarty->assign('yearvals', $valores);
		$campos = "";
		$valores = "";
		
		$select = $dbm->getSource();
		while (!$select->EOF) {
            $campos[] = $select->fields['idsource'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('sourceids', $campos);
        $smarty->assign('sourcevals', $valores);
		$campos = "";
		$valores = "";
		
		$select = $dbm->getArea();
		while (!$select->EOF) {
            $campos[] = $select->fields['idarea'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('areaids', $campos);
        $smarty->assign('areavals', $valores);
		
		
		
		
				
        $smarty->display('relRequestMonth.tpl.html');
    }
	
	public function table_json() {
		$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();
	
      	$dbm = new relRequestMonth_model();
      
		if($_POST['cmbYear'])
			$year = $_POST['cmbYear'];
		else
			$year = date("Y");
					
		if($_POST['cmbSource'])
			$source = "AND a.idsource = ".$_POST['cmbSource'];
		
		if($_POST['cmbCompany'])
			$company = "AND a.idperson_juridical = ".$_POST['cmbCompany'];
		
		if($_POST['cmbPerson'])
			$operator = "AND b.id_in_charge = ".$_POST['cmbPerson'];

		$rs = $dbm->getReport($year,$source,$company,$operator);		
		
		
		
		$month = array(
					"01" => $langVars['January'],
					"02" => $langVars['February'],
					"03" => $langVars['March'],
					"04" => $langVars['April'],
					"05" => $langVars['May'],
					"06" => $langVars['June'],
					"07" => $langVars['July'],
					"08" => $langVars['August'],
					"09" => $langVars['September'],
					"10" => $langVars['October'],
					"11" => $langVars['November'],
					"12" => $langVars['December']		
					);
		
		
        $output = array();
		$i = 0;
		$total = 0;
        while (!$rs->EOF) {
			
            $output[] = array(
            					"month_txt"	=> $month[$rs->fields['month']],
            					"total"		=> $rs->fields['total']
                            );
			$i++;
			$total += $rs->fields['total'];
			$rs->MoveNext();
		}
		
		$total_end = $total/$i;
		if(!$total_end) $total_end = "0";
		
        $output[] = array(
					"month_txt"	=> $langVars['Monthly_average'],
					"total"		=> $total_end
                );

		echo json_encode($output);

	}
	
	
	
}