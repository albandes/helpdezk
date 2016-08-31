<?php

class Evaluation extends Controllers {
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("evaluation/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('evaluation.tpl.html');
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

        $bd = new evaluation_model();
        $rs = $bd->selectEvaluation($where, $order, $limit);

        $qcount = $bd->countEvaluation($where);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rs->EOF) {
            if ($rs->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $name_ico = $rs->fields['icon_name'];
            $icon = "<img src='" . path . "/app/uploads/icons/" . $rs->fields['icon_name'] . "' height='16' />";
            $rows[] = array(
                "id" => $rs->fields['idevaluation'],
                "cell" => array(
                    $icon,
                    $rs->fields['name'],
                    $rs->fields['question'],
                    $status,
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function modalInsert() {
		$smarty = $this->retornaSmarty();
		$db = new evaluation_model();
        $select = $db->selectQuestionsAll();
        while (!$select->EOF) {
            $campos[] = $select->fields['idquestion'];
            $valores[] = $select->fields['question'];
            $select->MoveNext();
        }	
		
		$path = DOCUMENT_ROOT . path . "/app/uploads/icons/";
		$dh = opendir($path); 
		while (false !== ($filename = readdir($dh))) { 
			if(is_file($path.$filename)){
				$campos2[] = $filename;
            	$valores2[] = $filename;
			}
		}
		
        $smarty->assign('questionids', $campos);
        $smarty->assign('questionvals', $valores);
		$smarty->assign('iconids', $campos2);
        $smarty->assign('iconvals', $valores2);
		$smarty->display('modais/evaluation/insert.tpl.html');
	}
    
    public function insert() {
        $idquest = $_POST['question'];
        $name = $_POST['name'];
		$type = $_POST['txtIcons'];
		$checked = $_POST['chkEval'];
		
		$bd = new evaluation_model();
		$bd->BeginTrans();
		
		if($type == "txtLista"){
			$icon = $_POST['lstIcon'];
		}else{
			$icon = $_SESSION['ICON'];
		}
		
		if($checked){
			$clear = $bd->clearChecked($idquest);
			if(!$clear){
				$bd->RollbackTrans();
				return false;
			}
		}else{
			$checked = 0;
		}
        
        $ret = $bd->insertEvaluation($idquest, $name, $icon, $checked);
        if ($ret) {
        	$bd->CommitTrans();
            echo "ok";
        } else {
            $bd->RollbackTrans();
			return false;
        }
    }

    public function upload() {
        $this->view('upload_icon.php');
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/evaluation/delete.tpl.html');
	}
	
    public function delete() {
        $id = $_POST['id'];
        $bd = new evaluation_model();
        $dea = $bd->evaluationDelete($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new evaluation_model();
        $select = $bd->selectQuestionsAll();
        while (!$select->EOF) {
            $campos[] = $select->fields['idquestion'];
            $valores[] = $select->fields['question'];
            $select->MoveNext();
        }
		$path = DOCUMENT_ROOT . path . "/app/uploads/icons/";
		$dh = opendir($path); 
		while (false !== ($filename = readdir($dh))) { 
			if(is_file($path.$filename)){
				$campos2[] = $filename;
            	$valores2[] = $filename;
			}
		}
		$smarty->assign('iconids', $campos2);
        $smarty->assign('iconvals', $valores2);
        $smarty->assign('questionids', $campos);
        $smarty->assign('questionvals', $valores);
        $ret = $bd->selectEvaluationData($id);
        $smarty->assign('icon', $ret->fields['icon_name']);
		$smarty->assign('checked', $ret->fields['checked']);
        $smarty->assign('id', $id);
        $smarty->assign('question', utf8_decode($ret->fields['idquestion']));
        $smarty->assign('name', utf8_decode($ret->fields['name']));
        $smarty->display('modais/evaluation/edit.tpl.html');
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/evaluation/disable.tpl.html');
	}

    public function deactivate() {
        $id = $_POST['id'];
        $bd = new evaluation_model();
        $dea = $bd->evaluationDeactivate($id);
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
		$smarty->display('modais/evaluation/active.tpl.html');
	}
	
    public function activate() {
        $id = $_POST['id'];
        $bd = new evaluation_model();
        $dea = $bd->evaluationActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function edit() {
        extract($_POST);
		
		$id = $_POST['id'];
		$question = $_POST['question'];
        $name = $_POST['name'];
		$type = $_POST['txtIcons'];
		$checked = $_POST['chkEvalEdit'];
		
		$bd = new evaluation_model();
		$bd->BeginTrans();
		
		if($type == "txtLista"){
			$icon = $_POST['lstIcon'];
		}else{
			$icon = $_SESSION['ICON'];
		}
		
		if($checked){
			$clear = $bd->clearChecked($question);
			if(!$clear){
				$bd->RollbackTrans();
				return false;
			}
		}else{
			$checked = 0;
		}
		
        $edt = $bd->updateEvaluation($id, $name, $icon, $question, $checked);
        if ($edt) {
        	$bd->CommitTrans();
            echo "ok";
        } else {
        	$bd->RollbackTrans();
            return false;
        }
    }

    public function sessionCheck() {
        echo $_SESSION['ICON'];
    }
	
    public function question() {
    	
        session_start();
        $program = 8;
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();        
        $typeperson = $bd->selectTypePerson($user);        
        $access = $this->access($user, $program, $typeperson);		
        $smarty = $this->retornaSmarty();
        $smarty->display('question.tpl.html');
    }
    
    public function json2() {
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

        $bd = new evaluation_model();
        $rs2 = $bd->selectQuestion($where, $order, $limit);

        
        $qcount = $bd->countQuestion($where);
        $total = $qcount->fields['total'];
        
        $data['page'] = $page;
        $data['total'] = $total;

        while (!$rs2->EOF) {
            if ($rs2->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $name_ico = $rs2->fields['icon_name'];
            $icon = "<img src=" . path . "/app/themes/".theme."/images/" . $name_ico . " width='18' height '18' />";
            $rows[] = array(
                "id" => $rs2->fields['idquestion'],
                "cell" => array(
                    $rs2->fields['question'],
                    $status
                )
            );
            $rs2->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

	public function modalInsertQuestion(){
		$smarty = $this->retornaSmarty();
		$smarty->display('modais/question/insert.tpl.html');
	}
    
    public function questioninsert(){
        extract($_POST);
        
        $bd = new evaluation_model();
        $ret = $bd->insertQuestion($question);
        if ($ret) {
            echo "ok";
        } else {
            return false;
        }
    }
    public function questioneditform(){
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new evaluation_model();
        $ret = $bd->selectQuestionData($id);
        $smarty->assign('id', $id);
        $smarty->assign('question', $ret->fields['question']);
        $smarty->display('modais/question/edit.tpl.html');
    }
	
	public function questiondeactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/question/disable.tpl.html');
	}
    
    public function questiondeactivate() {
        $id = $_POST['id'];
        $bd = new evaluation_model();
        $dea = $bd->evaluationQuestionDeactivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function questionactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/question/active.tpl.html');
	}
	
    public function questionactivate() {
        $id = $_POST['id'];
        $bd = new evaluation_model();
        $dea = $bd->evaluationQuestionActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
    
    public function questionedit() {
        $question = $_POST['question'];
		$id = $_POST['id'];
		$bd = new evaluation_model();
		$edt = $bd->updateQuestion($id, $question);
		if ($edt) {
            echo "ok";
		} else {
            return false;
		}
    }
	
}

