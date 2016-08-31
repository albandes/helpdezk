<?php

class CostCenter extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("costcenter/");
        $access = $this->access($user,$program,$typeperson);
		$smarty = $this->retornaSmarty();
        $smarty->display('costcenter.tpl.html');
    }
	
	public function modalInsert(){
		$smarty = $this->retornaSmarty();
        $db = new costcenter_model();
        $select = $db->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
		$smarty->display('modais/costcenter/insert.tpl.html');
	}

    public function json() {
        $smarty = $this->retornaSmarty();
        $langVars2 = $smarty->get_template_vars();
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
                case 'tbc.name':
                    $where = "and  $qtype LIKE '$query%' ";
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

        $bd = new costcenter_model();
        $rs = $bd->selectCostCenter($where, $order, $limit);

        $qcount = $bd->countCostCenter($where);
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
                "id" => $rs->fields['idcodcenter'],
                "cell" => array(
                    $rs->fields['company'],
                    $rs->fields['cod_costcenter'],
                    $rs->fields['name'],
                    $status
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function insert() {
        $name = $_POST['name'];
        $cod = $_POST['cod'];
        $company = $_POST['company'];

        $db = new costcenter_model();
        $ret = $db->insertCostCenter($name, $company, $cod);
        if ($ret) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new costcenter_model();
        $select = $bd->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
        $ret = $bd->getCostCenterData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->assign('company', $ret->fields['idperson']);
        $smarty->assign('cod', $ret->fields['cod_costcenter']);
        $smarty->display('modais/costcenter/edit.tpl.html');
    }
	
	public function edit() {
		$id = $_POST['id'];
        $name = $_POST['name'];
        $cod = $_POST['cod'];
        $company = $_POST['company'];

        $bd = new costcenter_model();
        $edt = $bd->editCostcenter($id, $name, $company, $cod);
        if ($edt) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/costcenter/disable.tpl.html');
	}
	
	public function deactivate() {
        $id = $_POST['id'];
        $bd = new costcenter_model();
        $dea = $bd->costcenterDeactivate($id);
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
		$smarty->display('modais/costcenter/active.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new costcenter_model();
        $dea = $bd->costcenterActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    

}

?>
