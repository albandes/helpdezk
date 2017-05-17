<?php

class Program extends Controllers {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}

	public function index() {
		$user = $_SESSION['SES_COD_USUARIO'];
		$bd = new home_model();
		$typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("program/");
		$access = $this->access($user, $program, $typeperson);
		$smarty = $this->retornaSmarty();
		$db = new programs_model();
		$select = $db->selectModules();
		while (!$select->EOF) {
			$campos[] = $select->fields['idmodule'];
			$valores[] = $select->fields['name'];
			$select->MoveNext();
		}
		$smarty->assign('modulesids', $campos);
		$smarty->assign('modulesvals', $valores);
		$smarty->display('programs.tpl.html');
	}

	public function json() {
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		$prog = "";
		$path = "";
		$page = $_POST['page'];
		$rp = $_POST['rp'];
		if (!$sortorder)
			$sortorder = 'asc';
		if (!$page)
			$page = 1;
		if (!$rp)
			$rp = 10;
		$start = (($page - 1) * $rp);
		$limit = "LIMIT $start, $rp";
		$query = $_POST['query'];
		$qtype = $_POST['qtype'];
		$sortname = $_POST['sortname'];
		$sortorder = $_POST['sortorder'];
		$where = "";
		if ($query) {
			switch ($qtype) {
				case 'tbp.name':
					$where = "and  $qtype LIKE '$query%' ";
					break;
				case 'tbm.name':
					$where = "and  $qtype LIKE '$query%' ";
				default:
					break;
			}
		}
		if (!$sortname or !$sortorder) {
		} else {
			$order = " ORDER BY $sortname $sortorder ";
		}
		$limit = "LIMIT $start, $rp";
		$bd = new programs_model();
		$rsProgram = $bd->selectProgram($where, $order, $limit);
		$qcount = $bd->countProgram($where);
		$total = $qcount->fields['total'];
		$data['page'] = $page;
		$data['total'] = $total;
		while (!$rsProgram->EOF) {
			
			if($rsProgram->fields['smarty'])
				$name_pgr = $langVars[$rsProgram->fields['smarty']];
			else
				$name_pgr = $rsProgram->fields['name'];
			
			if ($rsProgram->fields['status'] == 'A') {
				$status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
			} else {
				$status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
			}
			$rows[] = array(
				"id" => $rsProgram->fields['idprogram'],
				"cell" => array(
					$name_pgr,
					$rsProgram->fields['controller'],
					$rsProgram->fields['module'],
					$rsProgram->fields['category'],
					$status
				)
			);
			$rsProgram->MoveNext();
		}
		$data['rows'] = $rows;
		$data['params'] = $_POST;
		echo json_encode($data);
	}

	public function insertmodal() {
		$smarty = $this->retornaSmarty();
		$db = new programs_model();
		$select = $db->selectModules();
		while (!$select->EOF) {
			$campos[] = $select->fields['idmodule'];
			$valores[] = $select->fields['name'];
			$select->MoveNext();
		}
		$smarty->assign('modulesids', $campos);
		$smarty->assign('modulesvals', $valores);
		$smarty->assign('theme', theme);
		$smarty->assign('path', path);
		$smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/programs/programsinsert.tpl.html');
	}

	public function category() {
		$mod = $_POST['module'];
		$cat = $_POST['category'];
		$bd = new programs_model();
		$sel = $bd->selectCategory($mod);
		$count = $sel->RecordCount();
		if ($count == 0) {
			echo "<option value='0'>Não há categorias para esse módulo!</option>";
			exit();
		} else {
			$i = 0;
			while (!$sel->EOF) {
				$campos[] = $sel->fields['idprogramcategory'];
				$valores[] = $sel->fields['name'];
				if ($campos[$i] == $cat) {
					$selected = "selected";
				} else {
					$selected = '';
				}
				echo "<option value='$campos[$i]' $selected >$valores[$i]</option>";
				$i++;
				$sel->MoveNext();
			}
		}
	}

	public function insert() {

		if (!$this->_checkToken()) return false;
		$idc = $_POST['category'];
		$name = $_POST['name'];
		$controller = $_POST['controller'];
		$smarty = $_POST['smarty'];
		$opnew = $_POST['opnew'];
		$opedit = $_POST['opedit'];
		$opdelete = $_POST['opdelete'];
		$opexport = $_POST['opexport'];
		$opemail = $_POST['opemail'];
		$opsms = $_POST['opsms'];
		if(!$idc || !$name || !$controller) return false;
		$bd = new programs_model();
		$ret = $bd->insertProgram($name, $controller, $smarty, $idc);
		$progid = $bd->selectProgramID($name, $idc, $controller);
		$access = '1';
		$allow = 'Y';
		$typecount = $bd->countTypePerson();
		$default = $bd->insertDefaultPermission($progid, $access, $allow);
		for ($i = 1; $i <= $typecount; $i++) {
			$bd->insertGroupPermission($progid, $i, $access);
		}
		if ($opnew == 'Y') {
			$access = '2';
			$bd->insertDefaultPermission($progid, $access, $allow);
			for ($i = 1; $i <= $typecount; $i++) {
				$bd->insertGroupPermission($progid, $i, $access);
			}
		}
		if ($opedit == 'Y') {
			$access = '3';
			$bd->insertDefaultPermission($progid, $access, $allow);
			for ($i = 1; $i <= $typecount; $i++) {
				$bd->insertGroupPermission($progid, $i, $access);
			}
		}
		if ($opdelete == 'Y') {
			$access = '4';
			$bd->insertDefaultPermission($progid, $access, $allow);
			for ($i = 1; $i <= $typecount; $i++) {
				$bd->insertGroupPermission($progid, $i, $access);
			}
		}
		if ($opexport == 'Y') {
			$access = '5';
			$bd->insertDefaultPermission($progid, $access, $allow);
			for ($i = 1; $i <= $typecount; $i++) {
				$bd->insertGroupPermission($progid, $i, $access);
			}
		}
		if ($opemail == 'Y') {
			$access = '6';
			$bd->insertDefaultPermission($progid, $access, $allow);
			for ($i = 1; $i <= $typecount; $i++) {
				$bd->insertGroupPermission($progid, $i, $access);
			}
		}
		if ($opsms == 'Y') {
			$access = '7';
			$bd->insertDefaultPermission($progid, $access, $allow);
			for ($i = 1; $i <= $typecount; $i++) {
				$bd->insertGroupPermission($progid, $i, $access);
			}
		}
		if ($ret && $default) {
			echo true;
		} else {
			echo false;
		}
	}

	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/programs/programsdisable.tpl.html');
	}

	public function deactivate() {
		if (!$this->_checkToken()) return false;
		$id = $_POST['id'];
		if(!$id) return false;
		$bd = new programs_model();
		$dea = $bd->programDeactivate($id);
		if ($dea) {
			echo true;
		} else {
			echo false;
		}
	}
	
	public function activatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/programs/programsactive.tpl.html');
	}

	public function activate() {
		if (!$this->_checkToken()) return false;
		$id = $_POST['id'];
		if(!$id) return false;
		$bd = new programs_model();
		$dea = $bd->programActivate($id);
		if ($dea) {
			echo true;
		} else {
			echo false;
		}
	}

	public function categoryinsert() {
		if (!$this->_checkToken()) return false;
		$module_post = $_POST['modules2'];
		$name_post = $_POST['newcategoryname'];		
		if(!$module_post || !$name_post) return false;
		$bd = new programs_model();
		$ret = $bd->categoryInsert($name_post, $module_post);
		$last = $bd->lastIdCategory();
		if ($ret) echo $last;
		else echo false;
	}

	public function editmodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$bd = new programs_model();
		$select = $bd->selectModules();
		while (!$select->EOF) {
			$campos[] = $select->fields['idmodule'];
			$valores[] = $select->fields['name'];
			$select->MoveNext();
		}
		$smarty->assign('modulesids', $campos);
		$smarty->assign('modulesvals', $valores);
		$ret = $bd->selectProgramData($id);
		$cat = $ret->fields['idprogramcategory'];
		$smarty->assign('cat', $cat);
		$mod = $bd->selectProgramModule($cat);
		$mod = $mod->fields['idmodule'];
		$select2 = $bd->selectCategory($mod);
		while (!$select2->EOF) {
			$campos1[] = $select2->fields['idprogramcategory'];
			$valores1[] = $select2->fields['name'];
			$select2->MoveNext();
		}
		
		
		$perms = $bd->getDefaultPermission($id);
		while (!$perms->EOF) {
			$arrPerm[$perms->fields['idaccesstype']] = $perms->fields['idaccesstype'];			
			$perms->MoveNext();
		}
		$smarty->assign('arrPerm', $arrPerm);
		$smarty->assign('catids', $campos1);
		$smarty->assign('catvals', $valores1);
		$smarty->assign('id', $id);
		$smarty->assign('name', $ret->fields['name']);
		$smarty->assign('controller', $ret->fields['controller']);
		$smarty->assign('varsmarty', $ret->fields['smarty']);
		$smarty->assign('module', $mod);
		$smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/programs/programsedit.tpl.html');
	}

	public function edit() {
		if (!$this->_checkToken()) return false;
		$id = $_POST['id'];
		$name = $_POST['nameEdit'];
		$controller = $_POST['controllerEdit'];
		$smarty = $_POST['smartyEdit'];
		$category = $_POST['categoryEdit'];
		
		if(!$id || !$category || !$name || !$controller || !$smarty) return false;
		
		$db = new programs_model();
		$db->BeginTrans();
		$upt = $db->updateProgram($id, $name, $controller, $smarty, $category, $smarty);
		if (!$upt) {
			$db->RollbackTrans();
        	return false;
		}		
		
		if($_POST['permEdit'] == "Y"){
			$clearDefaultPerm = $db->clearDefaultPerm($id);
			if (!$clearDefaultPerm) {
				$db->RollbackTrans();
	        	return false;
			}
			$clearGroupPerm = $db->clearGroupPerm($id);
			if (!$clearDefaultPerm) {
				$db->RollbackTrans();
	        	return false;
			}		
			$progid = $id;
			$access = '1';
			$allow = 'Y';
			$typecount = $db->countTypePerson();
			if (!$typecount) {
				$db->RollbackTrans();
	        	return false;
			}
			$default = $db->insertDefaultPermission($progid, $access, $allow);
			if (!$default) {
				$db->RollbackTrans();
	        	return false;
			}
			for ($i = 1; $i <= $typecount; $i++) {
				$grpPer = $db->insertGroupPermission($progid, $i, $access);
				if (!$grpPer) {
					$db->RollbackTrans();
		        	return false;
				}
			}
			
			if ($_POST['opnew'] == 'Y') {
				$access = '2';
				$default = $db->insertDefaultPermission($progid, $access, $allow);
				if (!$default) {
					$db->RollbackTrans();
		        	return false;
				}
				for ($i = 1; $i <= $typecount; $i++) {
					$grpPer = $db->insertGroupPermission($progid, $i, $access);
					if (!$grpPer) {
						$db->RollbackTrans();
			        	return false;
					}
				}
			}
			if ($_POST['opedit'] == 'Y') {
				$access = '3';
				$default = $db->insertDefaultPermission($progid, $access, $allow);
				if (!$default) {
					$db->RollbackTrans();
		        	return false;
				}
				for ($i = 1; $i <= $typecount; $i++) {
					$grpPer = $db->insertGroupPermission($progid, $i, $access);
					if (!$grpPer) {
						$db->RollbackTrans();
			        	return false;
					}
				}
			}
			if ($_POST['opdelete'] == 'Y') {
				$access = '4';
				$default = $db->insertDefaultPermission($progid, $access, $allow);
				if (!$default) {
					$db->RollbackTrans();
		        	return false;
				}
				for ($i = 1; $i <= $typecount; $i++) {
					$grpPer = $db->insertGroupPermission($progid, $i, $access);
					if (!$grpPer) {
						$db->RollbackTrans();
			        	return false;
					}
				}
			}
			if ($_POST['opexport'] == 'Y') {
				$access = '5';
				$default = $db->insertDefaultPermission($progid, $access, $allow);
				if (!$default) {
					$db->RollbackTrans();
		        	return false;
				}
				for ($i = 1; $i <= $typecount; $i++) {
					$grpPer = $db->insertGroupPermission($progid, $i, $access);
					if (!$grpPer) {
						$db->RollbackTrans();
			        	return false;
					}
				}
			}
			if ($_POST['opemail'] == 'Y') {
				$access = '6';
				$default = $db->insertDefaultPermission($progid, $access, $allow);
				if (!$default) {
					$db->RollbackTrans();
		        	return false;
				}
				for ($i = 1; $i <= $typecount; $i++) {
					$grpPer = $db->insertGroupPermission($progid, $i, $access);
					if (!$grpPer) {
						$db->RollbackTrans();
			        	return false;
					}
				}
			}
			if ($_POST['opsms'] == 'Y') {
				$access = '7';
				$default = $db->insertDefaultPermission($progid, $access, $allow);
				if (!$default) {
					$db->RollbackTrans();
		        	return false;
				}
				for ($i = 1; $i <= $typecount; $i++) {
					$grpPer = $db->insertGroupPermission($progid, $i, $access);
					if (!$grpPer) {
						$db->RollbackTrans();
			        	return false;
					}
				}
			}	
		}
		
		$db->CommitTrans();
        echo "OK";
	}

	public function editpermissions() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('editpermissions.tpl.html');
	}
}
?>
