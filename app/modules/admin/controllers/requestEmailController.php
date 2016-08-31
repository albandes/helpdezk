<?php
class requestEmail extends Controllers {
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("requestEmail/");
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('requestemail.tpl.html');
    }
	
	public function modalInsert(){		
		$smarty = $this->retornaSmarty();
        
		$dbservices = new services_model();
		$select = $dbservices->selectAvailabeAreas();
        while (!$select->EOF) {
            $campos[] = $select->fields['idarea'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('areaids', $campos);
        $smarty->assign('areavals', $valores);		
		$campos = '';
        $valores = '';
		
		$dbperson = new person_model();
		$select = $dbperson->getCompanies();
        while (!$select->EOF) {
            $campos[] = $select->fields['idcompany'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('companyids', $campos);
        $smarty->assign('companyvals', $valores);
        //zera variaveis para liberar memoria
        $campos = '';
        $valores = '';
		
		
		$smarty->display('modais/requestemail/insert.tpl.html');
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

        $bd = new requestEmail_model();
        $rs = $bd->getRequestEmail($where, $order, $limit);

        $database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$rstotal = $this->found_rows();
        	$total = $rstotal->fields['found_rows'];
		} elseif ($database == 'oci8po') {
			$total = $rs->fields['rnum'];
			if(!$total) $total = 0;
		}

        $data['page'] = $page;	
        $data['total'] = $total;
        while (!$rs->EOF) {
            $rows[] = array(
                "id" => $rs->fields['idgetemail'],
                "cell" => array(
                    $rs->fields['serverurl'],
                    $rs->fields['servertype'],
                    $rs->fields['serverport'],
                    $rs->fields['filter_from'],
                    $rs->fields['filter_subject']
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }
    public function insert(){
		$db = new requestEmail_model();
		$db->BeginTrans();
		$ret = $db->insertRequestEmail($_POST['serverInsert'],$_POST['typeInsert'],$_POST['insertPort'],$_POST['insertEmail'],$_POST['insertPassword'],$_POST['cmbService'],$_POST['insertFrom'],$_POST['insertSubject'],$_POST['insertCreateUser'],$_POST['insertDeleteEmails'],$_POST['insertLoginLayout'],$_POST['insertNote']);
		if(!$ret){
			$db->RollbackTrans();
			return false;
		}
		
		if($_POST['cmbInsertDepartment']){
			$idgetemail = $db->TableMaxID('hdk_tbgetemail','idgetemail');
			$dep = $db->insertRequestEmailDepartment($idgetemail, $_POST['cmbInsertDepartment']);
			if(!$dep){
				$db->RollbackTrans();
				return false;
			}
		}
		$db->CommitTrans();
        echo "OK";
        
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/requestemail/delete.tpl.html');
	}
	
    public function delete() {
        $id = $_POST['id'];
        $db = new requestEmail_model();
		$db->BeginTrans();
		
		$rmDep = $db->deleteRequestEmailDepartment($id);
		if(!$rmDep){
			$db->RollbackTrans();
			return false;
		}
		
        $dea = $db->requestEmailDelete($id);
        if (!$dea) {
        	$db->RollbackTrans();
			return false;
        }		
		
		
		$db->CommitTrans();
        echo "OK";		
    }
    public function editform(){
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new requestEmail_model();
		$where = "WHERE idgetemail = $id";
        $ret = $bd->getRequestEmail($where);
		
        $smarty->assign('id', $id);
        $smarty->assign('serverurl', $ret->fields['serverurl']);
        $smarty->assign('servertype', $ret->fields['servertype']);
        $smarty->assign('serverport', $ret->fields['serverport']);
		$smarty->assign('user', $ret->fields['user']);
		$smarty->assign('password', $ret->fields['password']);
		$smarty->assign('ind_create_user', $ret->fields['ind_create_user']);
		$smarty->assign('ind_delete_server', $ret->fields['ind_delete_server']);
		$smarty->assign('filter_from', $ret->fields['filter_from']);
		$smarty->assign('filter_subject', $ret->fields['filter_subject']);
		$smarty->assign('login_layout', $ret->fields['login_layout']);
		$smarty->assign('email_response_as_note', $ret->fields['email_response_as_note']);
		
		$idservice = $ret->fields['idservice'];
		
		$dbservices = new services_model();
		$select = $dbservices->selectAvailabeAreas();
        while (!$select->EOF) {
            $campos[] = $select->fields['idarea'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('areaids', $campos);
        $smarty->assign('areavals', $valores);
		$campos = '';
        $valores = '';

		$getIdItem = $dbservices->selectService("WHERE idservice = $idservice");
		$idItem = $getIdItem->fields['iditem'];
		
		$getServices = $dbservices->selectServices($idItem);
		while (!$getServices->EOF) {
            $campos[] = $getServices->fields['service'];
            $valores[] = $getServices->fields['service_name'];
            $getServices->MoveNext();
        }
        $smarty->assign('serviceids', $campos);
        $smarty->assign('servicevals', $valores);
		$smarty->assign('servicesel', $idservice);
		$campos = '';
        $valores = '';
		
		$getIdType = $dbservices->selectItemEdit($idItem);
		$idType = $getIdType->fields['idtype'];
		
		$getItens = $dbservices->selectItens($idType);
		while (!$getItens->EOF) {
            $campos[] = $getItens->fields['item'];
            $valores[] = $getItens->fields['item_name'];
            $getItens->MoveNext();
        }
        $smarty->assign('itemids', $campos);
        $smarty->assign('itemvals', $valores);
		$smarty->assign('itemsel', $idItem);
		$campos = '';
        $valores = '';
		
		$getIdArea = $dbservices->selectTypeEdit($idType);
		$idArea = $getIdArea->fields['idarea'];
		
		$getTypes = $dbservices->getTypeFromAreas($idArea);
		while (!$getTypes->EOF) {
            $campos[] = $getTypes->fields['type'];
            $valores[] = $getTypes->fields['type_name'];
            $getTypes->MoveNext();
        }
        $smarty->assign('typesids', $campos);
        $smarty->assign('typesvals', $valores);
		$smarty->assign('typessel', $idType);
		$campos = '';
        $valores = '';
		$smarty->assign('areasel', $idArea);
		
		if($ret->fields['ind_create_user'] == 1){			
			$dep = $bd->getRequestEmailDepartment($id);
			$iddepartment = $dep->fields['iddepartment'];
			
			$dbdep = new department_model();
			$getComp = $dbdep->getIdCompany($iddepartment);
			$idcompany = $getComp->fields['idperson'];
			
			$cmbdep = $dbdep->selectDepartment("AND tbp.idperson = $idcompany","ORDER BY department ASC");
			while (!$cmbdep->EOF) {
	            $campos[] = $cmbdep->fields['iddepartment'];
	            $valores[] = $cmbdep->fields['department'];
	            $cmbdep->MoveNext();
	        }
			$smarty->assign('depsids', $campos);
	        $smarty->assign('depvals', $valores);
			$smarty->assign('depsel', $iddepartment);
			$campos = '';
        	$valores = '';
			
			$smarty->assign('companysel', $idcompany);
		}
		
		$dbperson = new person_model();
		$select = $dbperson->getCompanies();
        while (!$select->EOF) {
            $campos[] = $select->fields['idcompany'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('companyids', $campos);
        $smarty->assign('companyvals', $valores);		
		
		$smarty->display('modais/requestemail/edit.tpl.html');
    }

    public function edit() {
			
    	$idgetemail = $_POST['id'];
		
		$db = new requestEmail_model();
		$db->BeginTrans();
		$ret = $db->updateRequestEmail($idgetemail,$_POST['serverEdit'],$_POST['typeEdit'],$_POST['editPort'],$_POST['editEmail'],$_POST['editPassword'],$_POST['cmbService'],$_POST['editFrom'],$_POST['editSubject'],$_POST['editCreateUser'],$_POST['editDeleteEmails'],$_POST['editLoginLayout'],$_POST['editNote']);
		if(!$ret){
			$db->RollbackTrans();
			return false;
		}
		
		$rmDep = $db->deleteRequestEmailDepartment($idgetemail);
		if(!$rmDep){
			$db->RollbackTrans();
			return false;
		}
		
		if($_POST['editCreateUser']){
			$dep = $db->insertRequestEmailDepartment($idgetemail, $_POST['cmbEditDepartment']);
			if(!$dep){
				$db->RollbackTrans();
				return false;
			}
		}
		$db->CommitTrans();
        echo "OK";
    }
		
}
?>
