<?php

class category extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
		$user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("category/");
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('category.tpl.html');
    }
	
	public function insertmodal() {
		$smarty = $this->retornaSmarty();
		$smarty->display('modais/category/categoryinsert.tpl.html');
	}
	
    public function insert() {
        $CategoryName = $_POST['name'];
		if(!$CategoryName) return false;
        $bd = new category_model();
        $check = $bd->checkName($CategoryName);
        if ($check->fields['idcategory']) {
            return false;
        } else {
            $ret = $bd->insertcategory($CategoryName);
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

        $bd = new category_model();
        $rsCategory = $bd->selectCategory($where, $order, $limit);

        $qcount = $bd->countCategory($where, $order);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsCategory->EOF) {
            if ($rsCategory->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $rows[] = array(
                "id" => $rsCategory->fields['idcategory'],
                "cell" => array(
                    $rsCategory->fields['title'],
                    $status
                )
            );
            $rsCategory->MoveNext();
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
        $bd = new category_model();
        $ret = $bd->selectCategoryData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['title']);
        $smarty->display('modais/category/categoryedit.tpl.html');
    }

    public function edit() {
        $id = $_POST['id_category'];
        $name = $_POST['nameEdit'];
		
		if(!$id || !$name) return false;
		
        $db = new category_model();
        $updt = $db->updateCategory($id, $name);
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
		$smarty->display('modais/category/categorydisable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
		if(!$id || !id == 1) return false;
        $bd = new category_model();
        $dea = $bd->categoryDeactivate($id);
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
		$smarty->display('modais/category/categoryactive.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new category_model();
        $dea = $bd->categoryActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
}
?>