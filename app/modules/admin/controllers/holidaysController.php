<?php
class Holidays extends Controllers {
    public $database;
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
        $this->database = $this->getConfig('db_connect');
	}

	public function index() {
		$smarty = $this->retornaSmarty();
		$user = $_SESSION['SES_COD_USUARIO'];
		$bd = new home_model();
		$program = $bd->selectProgramIDByController("holidays/");
		$typeperson = $bd->selectTypePerson($user);
		$access = $this->access($user, $program, $typeperson);
		$smarty->display('holidays.tpl.html');
	}
	
	public function json() {
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();

		$prog = "";
		$path = "";
		$page = $_POST['page'];
		$rp = $_POST['rp'];
		if (!$sortorder) $sortorder = 'asc';
		if (!$page) $page = 1;
		if (!$rp) $rp = 15;
		$start = (($page - 1) * $rp);
		$limit = "LIMIT $start, $rp";
		$query = $_POST['query'];
		$qtype = $_POST['qtype'];
		$sortname = $_POST['sortname'];
		$sortorder = $_POST['sortorder'];
		$where = "";
		if ($query) {
			switch ($qtype) {
				case 'HOLIDAY_DESCRIPTION':
					$where = "where  $qtype LIKE '$query%' ";
					break;
				default:
					$where = "";
					break;
			}
		}
		if (!$sortname or !$sortorder) {
		} else {
			$order = " ORDER BY $sortname $sortorder ";
		}
		$limit = "LIMIT $start, $rp";
		$bd = new holidays_model();
		$rsHoliday = $bd->selectHoliday($where, $order, $limit);
		$qcount = $bd->countHoliday($where);
		$total = $qcount->fields['total'];		
		$data['page'] = $page;
		$data['total'] = $total;

		while (!$rsHoliday->EOF) {
            if ($this->database == 'oci8po') {
                $dataformatada = $rsHoliday->fields['holiday_date'];
            }else{
                $dataformatada = $this->formatDate($rsHoliday->fields['holiday_date']);
            }
			
			if(isset($rsHoliday->fields['idperson'])){
				$type_holiday = $rsHoliday->fields['name'];
			}else{
				$type_holiday = $langVars['National_holiday'];
			}
			
			//$dataformatada = $rsHoliday->fields['holiday_date'];
			$rows[] = array(
				"id" => $rsHoliday->fields['idholiday'],
				"cell" => array(
					utf8_decode($rsHoliday->fields['holiday_description'])
					, $dataformatada
					, $type_holiday
				)
			);
			$dataformatada = '';
			$rsHoliday->MoveNext();
		}
		$data['rows'] = $rows;
		$data['params'] = $_POST;
		echo json_encode($data);
	}

	public function insertmodal() {
		$smarty = $this->retornaSmarty();
		$smarty->assign('theme', theme);
		$smarty->assign('path', path);
		
		$db = new groups_model();
        $select = $db->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
		
		$smarty->display('modais/holidays/holidaysinsert.tpl.html');
	}

    public function insert() {
		
		if(!$_POST['holiday_date'] || !$_POST['holiday_description']) return false;
		$bd = new holidays_model();
		$bd->BeginTrans();
        if ($this->database == 'oci8po') {
            $data = array(
                'holiday_date' => "TO_DATE ('".$_POST['holiday_date']."','DD/MM/YYYY')",
                'holiday_description' => "'".utf8_encode($_POST['holiday_description'])."'"
            );

        }else{
            $data = array(
                'holiday_date' => $this->formatSaveDate($_POST['holiday_date']),
                'holiday_description' => "'".utf8_encode($_POST['holiday_description'])."'"
            );
        }

		$ins = $bd->insertHoliday($data);
		if(!$ins){
			$bd->RollbackTrans();
			return false;
		}
		
		if($_POST['company'] != 0){
			$id_holiday = $bd->TableMaxID('tbholiday','idholiday');
			
			$data = array(
                'idholiday' => $id_holiday,
                'idperson' => $_POST['company']
            );
			
			$ins = $bd->insertHolidayHasCompany($data);
			if(!$ins){
				$bd->RollbackTrans();
				return false;
			}
		}
		
		$bd->CommitTrans();
		echo "OK";
	}
	
	public function editmodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('theme', theme);
		$smarty->assign('path', path);
		$bd = new holidays_model();
		$ret = $bd->selectHolidaysData($id);
        if ($this->database == 'oci8po') {
		    $dataformatada = $ret->fields['holiday_date'];
        }else{
            $dataformatada = $this->formatDate($ret->fields['holiday_date']);
        }

		$smarty->assign('id', $id);
		$smarty->assign('date', $dataformatada);
		$smarty->assign('description', utf8_decode($ret->fields['holiday_description']));
		$smarty->display('modais/holidays/holidayseditmodal.tpl.html');
	}

	public function edit() {
		$id = $_POST['id'];
		$desc = $_POST['description'];
		$holiday_date = $_POST['date'];
		if(!$id || !$desc || !$holiday_date) return false;
		$db = new holidays_model();
        if ($this->database == 'oci8po') {
            $updt = $db->updateHoliday($id, utf8_encode($desc), "TO_DATE ('".$holiday_date."','DD/MM/YYYY')");
        }else{
            $updt = $db->updateHoliday($id, utf8_encode($desc), $this->formatSaveDate($holiday_date));
        }

		if ($updt) echo true;
		else echo false;
	}

	public function importmodal() {
		$smarty = $this->retornaSmarty();
		$db = new holidays_model();
		$select = $db->getYearsHolidays();
		while (!$select->EOF) {
			$campos[] = $select->fields['holiday_year'];
			$valores[] = $select->fields['holiday_year'];
			$select->MoveNext();
		}
		$smarty->assign('year_field', $campos);
		$smarty->assign('year_val', $valores);
		$smarty->display('modais/holidays/holidaysimport.tpl.html');
	}

	public function load() {
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		$year = $this->getParam('year');
		$db = new holidays_model();
		$order = "ORDER BY HOLIDAY_DATE";
		$ret = $db->selectHolidayByYear($year, $order);
		$count = $db->countAllHolidays($year);
		$date = date('Y');
		$i = 0;
		$resultado = array(
							'year' => $year,
							'count' => $count,
							'result' => array()
						);
		while (!$ret->EOF) {
			
			if(isset($ret->fields['idperson'])){
				$type_holiday = $ret->fields['name'];
			}else{
				$type_holiday = $langVars['National_holiday'];
			}
			
			$dataformatada = $this->formatDate($ret->fields['holiday_date']);
			$resultado['result'][$i]['date']	= $dataformatada;
			$resultado['result'][$i]['name']	= utf8_decode($ret->fields['holiday_description']);
			$resultado['result'][$i]['type']	= $type_holiday;
			$i++;
			$ret->MoveNext();
        }
		echo json_encode($resultado);
    }

	public function import() {
		$year = $_POST['fromyear'];
		$nextyear = $_POST['year2'];
		if(!$year || !$nextyear) return false;
		$db = new holidays_model();
		$db->BeginTrans();
		$count = $db->countAllHolidays($year);
		$sel = $db->selectHolidayByYear($year);
		while (!$sel->EOF) {
			$desc = $sel->fields['holiday_description'];
			$newdate = $sel->fields['holiday_date'];

            $newdate = substr($newdate, 4);
			$newdate = $nextyear . $newdate;
			
			$database = $this->getConfig('db_connect');
			if ($database == 'oci8po') {
                $newdate = $sel->fields['holiday_date'];
                $newdate = substr($newdate, 0, 6);
                $newdate = $newdate . $nextyear;
				$dataformatada = $newdate ;
				$newdate = "to_date('".$dataformatada."','DD/MM/YYYY')" ;
				$ins = $db->insertHoliday(array('holiday_date' => $newdate,'holiday_description' => "'".addslashes($desc)."'"));
			}elseif($database == "mysqlt"){
				$ins = $db->insertHoliday(array('holiday_date' => "'".$newdate."'",'holiday_description' => "'".addslashes($desc)."'"));
			}

			if(!$ins){
				$db->RollbackTrans();
				return false;
			}
			
			if(isset($sel->fields['idperson'])){
				$id_holiday = $db->TableMaxID('tbholiday','idholiday');
				
				$data = array(
	                'idholiday' => $id_holiday,
	                'idperson' => $sel->fields['idperson']
	            );
				
				$ins = $db->insertHolidayHasCompany($data);
				if(!$ins){
					$bd->RollbackTrans();
					return false;
				}
			}
			
			$db->CommitTrans();
			$sel->MoveNext();
		}
		if ($ins) echo true;
		else return false;
	}
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/holidays/delete.tpl.html');
	}
	
	public function delete() {
        $id = $_POST['id'];
		$db = new holidays_model();
		$db->BeginTrans();
		
		$del = $db->holidayDeleteHasCompany($id);
		if(!$del){
			$db->RollbackTrans();
			return false;
		}
		
        $del = $db->holidayDelete($id);
		if(!$del){
			$db->RollbackTrans();
			return false;
		}
		
		$db->CommitTrans();
        echo "ok";
    }

}