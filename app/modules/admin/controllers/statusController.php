<?php
class Status extends Controllers {
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("status/");
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('status.tpl.html');
    }
	
	public function modalInsert(){
		$smarty = $this->retornaSmarty();
        $bd = new status_model();
		$where = "WHERE idstatus = idstatus_source";
		$order = "ORDER BY name ASC";
        $rs = $bd->selectStatus($where, $order, null);
        while (!$rs->EOF) {
            $campos[] = $rs->fields['idstatus'];
            $valores[] = $rs->fields['name'];
            $rs->MoveNext();
        }
        $smarty->assign('statusids', $campos);
        $smarty->assign('statusvals', $valores);		
		$smarty->display('modais/status/insert.tpl.html');
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

        $bd = new status_model();
        $rs = $bd->selectStatus($where, $order, $limit);

        $qcount = $bd->countStatus($where);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rs->EOF) {
            if( $rs->fields['status'] == 'A'){
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            }
            else{
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $color = $rs->fields['color'];
            $color = "<div id='color' style='background-color:$color; height:5px; width:5px; border:1px solid #000;'></div>";
            $rows[] = array(
                "id" => $rs->fields['idstatus'],
                "cell" => array(
                    $rs->fields['name'],
                    $rs->fields['user_view'],
                    $color,
                    $status
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }
    public function insert(){
        $name = $_POST['name'];
        $user = $_POST['user'];
        $color = $_POST['color'];
        $groupby = $_POST['groupby'];
        
        $bd = new status_model();
        $ret = $bd->insertStatus($name, $user, $color, $groupby);
        if($ret){
            echo "ok";
        }
        else{
            return false;
        }
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/status/delete.tpl.html');
	}
	
    public function delete() {
        $id = $_POST['id'];
        $bd = new status_model();
        $dea = $bd->statusDelete($id);
        if ($dea) {
            echo "ok";
        } else {
           return false;
        }
    }
    public function editform(){
        $smarty = $this->retornaSmarty();
        // pegamos o id passado no link no formato /modulo/controller/action/id/variavel pelo método getParam do Framework.
        $id = $this->getParam('id');
        $bd = new status_model();
        $ret = $bd->selectStatusData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->assign('user_view', $ret->fields['user_view']);
        $smarty->assign('color', $ret->fields['color']);
		$smarty->assign('groupby', $ret->fields['idstatus_source']);
		
		if($ret->fields['idstatus_source'] == $id)
			$smarty->assign('disabled', 'disabled="disabled"');
		else
			$smarty->assign('disabled', '');
		
		$where = "WHERE idstatus = idstatus_source";
		$order = "ORDER BY name ASC";
        $rs = $bd->selectStatus($where, $order, null);
        while (!$rs->EOF) {
            $campos[] = $rs->fields['idstatus'];
            $valores[] = $rs->fields['name'];
            $rs->MoveNext();
        }
        $smarty->assign('statusids', $campos);
        $smarty->assign('statusvals', $valores);
		
		
        //$smarty->display('statusformedit.tpl.html');
		$smarty->display('modais/status/edit.tpl.html');
    }
    public function edit() {
        $name = $_POST['name'];
        $user = $_POST['user'];
        $color = $_POST['color'];
        $groupby = $_POST['groupby'];
		$id = $_POST['id'];
		
        $db = new status_model();
        $updt = $db->updateStatus($id, $name, $user, $color, $groupby);
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
		$smarty->display('modais/status/disable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
        $bd = new status_model();
        $dea = $bd->statusDeactivate($id);
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
		$smarty->display('modais/status/active.tpl.html');
	}
	
    public function activate() {
        $id = $_POST['id'];
        $bd = new status_model();
        $dea = $bd->statusActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
}
?>
