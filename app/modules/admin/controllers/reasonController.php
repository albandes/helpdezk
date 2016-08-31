<?php
class Reason extends Controllers{
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index(){
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
		$program = $bd->selectProgramIDByController("reason/");
        $typeperson = $bd->selectTypePerson($user);
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('reason.tpl.html');
    }
	
	public function modalInsert(){
		$smarty = $this->retornaSmarty();
        $db = new reason_model();
        $select = $db->selectService();
        while (!$select->EOF) {
            $campos[] = $select->fields['idservice'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('typeids', $campos);
        $smarty->assign('typevals', $valores);
		$smarty->display('modais/reason/insert.tpl.html');
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
                case 'tbr.reason':
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

        $bd = new reason_model();
        $rs = $bd->selectReason($where, $order, $limit);

        $qcount = $bd->countReason($where);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rs->EOF) {
            if ($rs->fields['status'] == "A") {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $rows[] = array(
                "id" => $rs->fields['idreason'],
                "cell" => array(
                   $rs->fields['service'],
                   $rs->fields['reason'],
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
		$reason = $_POST['reason'];
        $type = $_POST['service'];
        $available = $_POST['available'];
        if(!$available)
			$available = "N";
		
        $bd = new reason_model();
        $ret = $bd->insertReason($reason, $type, $available);
        if ($ret){
            echo "ok";
        }
        else {
            return false;
        }
    }
    
    public function editform() {
        $smarty = $this->retornaSmarty();
        // pegamos o id passado no link no formato /modulo/controller/action/id/variavel pelo método getParam do Framework.
        $id = $this->getParam('id');
        $db = new reason_model();
        $ret = $db->selectReasonData($id);
        $select = $db->selectAllServices();
        while (!$select->EOF) {
            $campos[] = $select->fields['idservice'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('typeids', $campos);
        $smarty->assign('typevals', $valores);
        $smarty->assign('id', $id);
        $smarty->assign('type', $ret->fields['idservice']);
        $smarty->assign('reason', $ret->fields['reason']); 
        $smarty->assign('available', $ret->fields['status']);
        $smarty->display('modais/reason/edit.tpl.html');
    }
    
    public function edit() {
    	$id = $_POST['id'];
        $reason = $_POST['reason'];
        $type = $_POST['service'];
        $available = $_POST['available'];
		if(!$available)
			$available = "N";
				
        $bd = new reason_model();
        $upd = $bd->updateReason($id, $reason, $type, $available);
        if ($upd) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function deactivate() {
        $id = substr($_POST['items'], 0, -1);
        $bd = new reason_model();
        $dea = $bd->deactivateReason($id);
        if ($dea) {
            return "ok";
        } else {
            return false;
        }
    }

    public function activate() {
        $id = substr($_POST['items'], 0, -1);
        $bd = new reason_model();
        $dea = $bd->activateReason($id);
        if ($dea) {
            return "ok";
        } else {
            return false;
        }
    }
    
	public function deletemodal() {
        $smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/reason/delete.tpl.html');
    }
	
    public function delete() {
        $id = $_POST['id'];
        $bd = new reason_model();
        $del = $bd->reasonDelete($id);
        if ($del) {
            echo "ok";
        } else {
            return false;
        }
    }
}
?>
