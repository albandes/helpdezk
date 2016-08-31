<?php

class Widget extends Controllers {
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {		
		$user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("widget/");
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('widget.tpl.html');		
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

        $bd = new widget_model();
        $rs = $bd->selectWidget($where, $order, $limit);

        $qcount = $bd->countWidget($where);
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
                "id" => $rs->fields['idwidget'],
                "cell" => array(
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

    public function modalInsert() {
		$smarty = $this->retornaSmarty();
		$db = new widget_model();
        $select = $db->selectCategoryAll();
        while (!$select->EOF) {
            $ids[] = $select->fields['idcategory'];
            $values[] = $select->fields['title'];
            $select->MoveNext();
        }	
        $smarty->assign('categoryids', $ids);
        $smarty->assign('categoryvals', $values);		
		$smarty->display('modais/widget/insert.tpl.html');
	}
    
    public function insert() {
        $idcategory  = $_POST['category'];
        $name 		 = $_POST['name'];
		$description = $_POST['description'];
		$creator     = $_POST['author'];
		$controller  = $_POST['controller'];
        $dbhost 	 = $_POST['dbhost'];
        $dbname 	 = $_POST['dbname'];
        $dbuser 	 = $_POST['dbuser'];
        $dbpass 	 = $_POST['dbpass'];
        $field1 	 = $_POST['field1'];
        $field2 	 = $_POST['field2'];
        $field3 	 = $_POST['field3'];
        $field4 	 = $_POST['field4'];
        $field5 	 = $_POST['field5'];
		$image 		 = $_SESSION['IMAGE'];
		
        $bd = new widget_model();
		$bd->BeginTrans();
        $ret = $bd->insertWidget($idcategory,$name,$description,$creator,$controller,$dbhost,$dbname,$dbuser,$dbpass,$field1,$field2,$field3,$field4,$field5,$image);

        if (!$ret) {
			$bd->RollbackTrans();
            return false;
        }

        $ret = $bd->insertCategoryHasWidget($bd->InsertID(),$idcategory);
		if (!$ret) {
			$bd->RollbackTrans();
            return false;
        } else {
			$bd->CommitTrans();
			echo "ok";	
			return true;
		}	
    }

    public function upload() {
        $this->view('upload_dsh_image.php');
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/widget/delete.tpl.html');
	}
	
    public function delete() {
        $id = $_POST['id'];
        $bd = new widget_model();

        $del = $bd->widgetDelete($id);
		$ret = $bd->categoryhaswidgetDelete($id);
		if (!$ret) {
            return false;
        } else {
			echo "ok";	
		}	
    }

 	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/widget/disable.tpl.html');
	}

    public function deactivate() {
        $id = $_POST['id'];
        $bd = new widget_model();
        $dea = $bd->widgetDeactivate($id);
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
		$smarty->display('modais/widget/active.tpl.html');
	}
	
    public function activate() {
        $id = $_POST['id'];
        $bd = new widget_model();
        $dea = $bd->widgetActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

   public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
		$db = new widget_model();
        $select = $db->selectCategoryAll();

        while (!$select->EOF) {
            $ids[]   = $select->fields['idcategory'];
            $values[] = $select->fields['title'];
            $select->MoveNext();
        }	
		
        $smarty->assign('categoryids', $ids);
        $smarty->assign('categoryvals', $values);		

        $ret = $db->selectWidgetData($id);
        $smarty->assign('category', utf8_decode($ret->fields['idcategory']));

        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
		$smarty->assign('controller', $ret->fields['controller']);
		$smarty->assign('author', $ret->fields['creator']);
		$smarty->assign('description', $ret->fields['description']);
		$smarty->assign('dbhost', $ret->fields['dbhost']);
		$smarty->assign('dbname', $ret->fields['dbname']);		
		$smarty->assign('dbuser', $ret->fields['dbuser']);
		$smarty->assign('dbpass', $ret->fields['dbpass']);
		$smarty->assign('field1', $ret->fields['field1']);	
		$smarty->assign('field2', $ret->fields['field2']);	
		$smarty->assign('field3', $ret->fields['field3']);			
		$smarty->assign('field4', $ret->fields['field4']);			
		$smarty->assign('field5', $ret->fields['field5']);		
		$smarty->assign('oldfile', $ret->fields['image']);		
		
        $smarty->display('modais/widget/edit.tpl.html');
    }


    public function edit() {
        extract($_POST);

        $idcategory  = $_POST['categoryEdit'];
        $name 		 = $_POST['nameEdit'];
		$description = $_POST['descriptionEdit'];
		$creator     = $_POST['authorEdit'];
		$controller  = $_POST['controllerEdit'];
        $dbhost 	 = $_POST['dbhostEdit'];
        $dbname 	 = $_POST['dbnameEdit'];
        $dbuser 	 = $_POST['dbuserEdit'];
        $dbpass 	 = $_POST['dbpassEdit'];
        $field1 	 = $_POST['field1Edit'];
        $field2 	 = $_POST['field2Edit'];
        $field3 	 = $_POST['field3Edit'];
        $field4 	 = $_POST['field4Edit'];
        $field5 	 = $_POST['field5Edit'];
		$image 		 = $_SESSION['IMAGE'];

		$id = $_POST['id'];

		if($_SESSION['IMAGE']) {
			$image = $_SESSION['IMAGE'];
		} else {
			$image = $_POST['oldfile'];
		}
		

        $bd = new widget_model();
		$bd->BeginTrans();
        $edt = $bd->updateWidget($id,$name,$description,$creator,$controller,$dbhost,$dbname,$dbuser,$dbpass,$field1,$field2,$field3,$field4,$field5,$image);
		if(!$edt) {return false;}
		
		$ret = $bd->updateCategoryHasWidget($_POST['id'],$_POST['categoryEdit']);
		if (!$ret) {
			$bd->RollbackTrans();
            return false;
        } else {
			$bd->CommitTrans();
			echo "ok";	
		}	
    }

    public function sessionCheck() {
        echo $_SESSION['IMAGE'];
    }
	
}
?>
