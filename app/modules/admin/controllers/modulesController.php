<?php

class Modules extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("modules/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('modules.tpl.html');
    }
	
	public function insertmodal() {
		$smarty = $this->retornaSmarty();
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/modules/insert.tpl.html');
	}
	
    public function insert() {
        if (!$this->_checkToken()) return false;
        $MODNAME = $_POST['name'];
		if(!$MODNAME) return false;
        $bd = new modules_model();
        $check = $bd->checkName($MODNAME);
        if ($check->fields['idmodule']) {
            return false;
        } else {
            $ret = $bd->insertModule($MODNAME);
            if ($ret) {
                echo "ok";
            } else {
                return false;
            }
        }
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
                case 'name':
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

        $bd = new modules_model();
        $rsModule = $bd->selectModule($where, $order, $limit);

        $qcount = $bd->countModule($where, $order);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsModule->EOF) {
            if ($rsModule->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $rows[] = array(
                "id" => $rsModule->fields['idmodule'],
                "cell" => array(
                    $rsModule->fields['name'],
                    $status
                )
            );
            $rsModule->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function delete() {
        $id = substr($_POST['items'], 0, -1);
        $bd = new modules_model();
        $del = $bd->deleteModule("idmodule in ($id)");
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new modules_model();
        $ret = $bd->selectModuleData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/modules/edit.tpl.html');
    }

    public function edit() {
        if (!$this->_checkToken()) return false;

        $id = $_POST['id_module'];
        $name = $_POST['nameEdit'];
		
		if(!$id || !$name) return false;
		
        $db = new modules_model();
        $updt = $db->updateModule($id, $name);
        if ($updt) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/modules/disable.tpl.html');
	}
	
    public function deactivate() {
        if (!$this->_checkToken()) return false;
        $id = $_POST['id'];
		if(!$id || !id == 1) return false;
        $bd = new modules_model();
        $dea = $bd->moduleDeactivate($id);
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
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/modules/active.tpl.html');
	}

    public function activate() {
        if (!$this->_checkToken()) return false;
        $id = $_POST['id'];
        $bd = new modules_model();
        $dea = $bd->moduleActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
}
?>