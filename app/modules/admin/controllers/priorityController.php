<?php

class Priority extends Controllers {
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("priority/");
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('priority.tpl.html');
    }
	
	public function modalInsert(){
		$smarty = $this->retornaSmarty();
     	$bd = new priority_model();
        $order = $bd->selectNextOrder();
        $ord = $order->fields['ord'];
        $ord = $ord + 1;
        $smarty->assign('order', $ord);	 	
		$smarty->display('modais/priority/insert.tpl.html');
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

        $bd = new priority_model();
        $rs = $bd->selectPriority($where, $order, $limit);

        $qcount = $bd->countPriority($where);
        $total = $qcount->fields['total'];
       
        $data['page'] = $page;
        $data['total'] = $total;

        while (!$rs->EOF) {
            if ($rs->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $color = $rs->fields['color'];
            $color = "<div id='color' style='background-color:$color; height:5px; width:5px; border:1px solid #000;'></div>";
            $rows[] = array(
                "id" => $rs->fields['idpriority'],
                "cell" => array(
                    $rs->fields['name']
                    , $rs->fields['ord']
                    , $color
                    , $status
                )
            );
            $dataformatada = '';
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function insert() {
        $name = $_POST['name'];
        $order = $_POST['order'];
        $color = $_POST['color'];
        $default = $_POST['vardefault'];
        $vip = $_POST['vip'];
        $limit_hours = $_POST['limit_hours'];
        $limit_days = $_POST['limit_days'];
		
		if(!$default) $default = 0;
		if(!$vip) $vip = 0;
		

        $db = new priority_model();
        if ($default == 1) {
            $def = $db->updateDefaults();
            $ret = $db->insertPriority($name, $order, $color, $default, $vip, $limit_hours, $limit_days);
            if ($def && $ret) {
                echo "ok";
            } else {
                return false;
            }
        } else {
            $ret = $db->insertPriority($name, $order, $color, $default, $vip, $limit_hours, $limit_days);
            if ($ret) {
                echo "ok";
            } else {
                return false;
            }
        }
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/priority/delete.tpl.html');
	}
	
    public function delete() {
        $id = $_POST['id'];
		if($id > 50){
	        $bd = new priority_model();
	        $dea = $bd->priorityDelete($id);
	        if ($dea) {
	            echo "ok";
	        } else {
	           return false;
	        }
		}
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new priority_model();
        $where = "where idpriority = $id";
        $ret = $bd->selectPriority($where);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->assign('order', $ret->fields['ord']);
        $smarty->assign('color', $ret->fields['color']);
        $smarty->assign('default', $ret->fields['def']);
        $smarty->assign('vip', $ret->fields['vip']);
        $smarty->assign('days', $ret->fields['limit_days']);
        $smarty->assign('hours', $ret->fields['limit_hours']);
        $smarty->display('modais/priority/edit.tpl.html');
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/priority/disable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
        $bd = new priority_model();
        $dea = $bd->priorityDeactivate($id);
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
		$smarty->display('modais/priority/active.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new priority_model();
        $dea = $bd->priorityActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function edit() {
        $name = $_POST['name'];
        $order = $_POST['order'];
        $color = $_POST['color'];
        $default = $_POST['vardefault'];
        $vip = $_POST['vip'];
        $limit_hours = $_POST['limit_hours'];
        $limit_days = $_POST['limit_days'];
		$id = $_POST['id'];
		
		if(!$default) $default = 0;
		if(!$vip) $vip = 0;

        $bd = new priority_model();
        if ($default == 1) {
            $def = $bd->updateDefaults();
            $edt = $bd->editPriority($id, $name, $order, $color, $default, $vip, $limit_hours, $limit_days);
            if ($def && $edt) {
                echo "ok";
            } else {
                return false;
            }
        }
        else{
            $edt = $bd->editPriority($id, $name, $order, $color, $default, $vip, $limit_hours, $limit_days);
            if ($edt) {
                echo "ok";
            } else {
                return false;
            }
        }
    }

}

?>
