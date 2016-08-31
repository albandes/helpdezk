<?php

class Department extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
    	$smarty = $this->retornaSmarty();
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("department/");
        $access = $this->access($user,$program,$typeperson);
        $smarty->display('departments.tpl.html');
    }
		
    public function json() {
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
                case 'tbd.name':
                    $where = "and  $qtype LIKE '$query%' ";
                    break;
                default:
                    $where = "";
                    break;
            }
        }
        if (!$sortname or !$sortorder) {
            //$order = " ORDER BY department ASC ";
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";

        $bd = new department_model();
        $rs = $bd->selectDepartment($where, $order, $limit);

        $qcount = $bd->countDepartment($where);
        $total = $qcount->fields['total'];
        
        $data['page'] = $page;
        $data['total'] = $total;

        while (!$rs->EOF) {
            if ($rs->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $rows[] = array(
                "id" => $rs->fields['iddepartment'],
                "cell" => array(
                    $rs->fields['name']
                    , $rs->fields['department']
                    , $status
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    
	public function modalInsert(){
		$smarty = $this->retornaSmarty();
        $db = new department_model();
        $select = $db->selectCorporations(NULL, "ORDER BY name ASC", NULL);
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
		$smarty->display('modais/departments/insert.tpl.html');
	}
    
    public function insert() {
        $name = $_POST['name'];
        $company = $_POST['company'];

        $bd = new department_model();
        $ret = $bd->insertDepartment($name, $company);
        if ($ret) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new department_model();
        $ret = $bd->selectDepartmentData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->assign('company', $ret->fields['idperson']);
        $select = $bd->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
        $smarty->display('modais/departments/edit.tpl.html');
    }

    public function edit() {
    	$id = $_POST['id'];
        $name = $_POST['name'];
        $company = $_POST['company'];
        $bd = new department_model();
        $upd = $bd->updateDepartment($id, $name, $company);
        if ($upd) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/departments/disable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
       	$bd = new department_model();
        $dea = $bd->departmentDeactivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function activatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/departments/active.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new department_model();
        $dea = $bd->departmentActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/departments/delete.tpl.html');
	}
    
    public function delete() {
        $id = $_POST['id'];
        $bd = new department_model();
        $del = $bd->departmentDelete($id);
        if ($del) {
           echo "OK";
        } else {
            return false;
        }
    }
	
	public function checkDepartmentName(){
		$id_company = $_POST['company'];
		$name_dep = $_POST['name'];
		$bd = new department_model();
		$check = $bd->checkDepartmentName($id_company, $name_dep);
		if($check)
			echo false;
		else
			echo true;		
	}

}

?>
