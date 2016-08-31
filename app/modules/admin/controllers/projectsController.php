<?php

class Projects extends Controllers {
    

    public function index() {
    	session_start();
        $user = $_SESSION['SES_COD_USUARIO'];
        $smarty = $this->retornaSmarty();
        $smarty->display('projects.tpl.html');
    }

    public function json() {
        $prog = "";
        $path = "";
        $page = $_POST['page'];
        $rp = $_POST['rp'];
        $sortorder = $_POST['sortorder'];
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
                case 'name_project':
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
        $bd = new projects_model();
        $rs = $bd->selectProjects($where, $order, $limit);
        $qcount = $bd->countProjects($where);
        $total = $qcount->fields['total'];
        $data['page'] = $page;
        $data['total'] = $total;
        
        while (!$rs->EOF) {
            
            if($rs->fields['active'])
                $active = "<img src='" . path . "/app/themes/" . theme . "/images/active.gif' height='10px' width='10px'>";
            else
                $active = "<img src='" . path . "/app/themes/" . theme . "/images/notactive.gif' height='10px' width='10px'>";
            
            $rows[] = array(
                "id" => $rs->fields['idproject'],
                "cell" => array(
                    $rs->fields['percent']."%"
                    , $active
                    , $rs->fields['name_project']
                    , $rs->fields['group_name']
                    , $rs->fields['company']
                    , $rs->fields['begin_date']
                    , $rs->fields['end_date']
                    , $rs->fields['name_project']
                )
            );
            $dataformatada = '';
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }
    
    public function projectinsert(){
    	session_start();
        $smarty = $this->retornaSmarty();

        $db = new projects_model();
        $percentual = $db->selectPercentual();
        while (!$percentual->EOF) {
            $percentualField[] = $percentual->fields['percentual']." %";
            $percentualVal[] = $percentual->fields['percentual'];
            $percentual->MoveNext();
        }
        
        $status = $db->selectStatus();
        while (!$status->EOF) {
            $statusField[] = $status->fields['description'];
            $statusVal[] = $status->fields['idstatus_project'];
            $status->MoveNext();
        }
        
        $type = $db->selectTypeProject();
        while (!$type->EOF) {
            $typeField[] = $type->fields['description'];
            $typeVal[] = $type->fields['idtype_project'];
            $type->MoveNext();
        }
        
        $priority = $db->selectPriority();
        while (!$priority->EOF) {
            $priorityField[] = $priority->fields['name'];
            $priorityVal[] = $priority->fields['idpriority'];
            $priority->MoveNext();
        }
        
        $dependencies = $db->selectDependencies();
        while (!$dependencies->EOF) {
            $dependenciesField[] = $dependencies->fields['name_project'];
            $dependenciesVal[] = $dependencies->fields['idproject'];
            $dependencies->MoveNext();
        }
        
        $db_group = new groups_model();
        $group = $db_group->selectGroup();
        while (!$group->EOF) {
            $groupField[] = $group->fields['name'];
            $groupVal[] = $group->fields['idgroup'];
            $group->MoveNext();
        }
        
        $db_person = new person_model();
        $companies = $db_person->getCompanies();
        while (!$companies->EOF) {
            $companiesField[] = $companies->fields['name'];
            $companiesVal[] = $companies->fields['idcompany'];
            $companies->MoveNext();
        }
        
        $smarty->assign('percentual', $percentualField);
        $smarty->assign('percentualVal', $percentualVal);
        $smarty->assign('status', $statusField);
        $smarty->assign('statusVal', $statusVal);
        $smarty->assign('type', $typeField);
        $smarty->assign('typeVal', $typeVal);
        $smarty->assign('priority', $priorityField);
        $smarty->assign('priorityVal', $priorityVal);
        $smarty->assign('group', $groupField);
        $smarty->assign('groupVal', $groupVal);
        $smarty->assign('companies', $companiesField);
        $smarty->assign('companiesVal', $companiesVal);
        $smarty->assign('dependencies', $dependenciesField);
        $smarty->assign('dependenciesVal', $dependenciesVal);
        $smarty->assign('person', $_SESSION['SES_NAME_PERSON']);
        $smarty->assign('SES_COD_USUARIO', $_SESSION['SES_COD_USUARIO']);
        $smarty->assign('SES_COD_JURIDICAL', $_SESSION['SES_COD_EMPRESA']);
        $smarty->display('projectinsert.tpl.html');       
    }
    
    public function projectsave(){
        extract($_POST);
        $my_id = $_SESSION['SES_COD_USUARIO'];
        $db = new projects_model();
        $percentual = $db->setProject($prj_name,$prj_name_min,$my_id,$prj_person,$prj_person_id,$prj_juridical_id,$prj_company,$prj_group,$prj_url,$prj_description,$prj_dtstart,$prj_dtend,$prj_hourstart,$prj_hourend,$prj_type,$prj_perc,$prj_active,$prj_status,$prj_priority);
        $lastId = mysql_insert_id();
		
        foreach($prj_dependences as $prj_dep){
			$db->setDependence($prj_dep, $lastId);
        }
        
        if($percentual)
            echo "OK";
        else
            return false;
    }
	
	public function editform() {
		//error_reporting(E_ALL);
		session_start();
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
		$where = "AND idproject = ".$id;
        $db = new projects_model();
		$ret = $db->selectProjects($where);
		
	    
       	
		
		
		
		
        $percentual = $db->selectPercentual();
        while (!$percentual->EOF) {
            $percentualField[] = $percentual->fields['percentual']." %";
            $percentualVal[] = $percentual->fields['percentual'];
            $percentual->MoveNext();
        }
        
        $status = $db->selectStatus();
        while (!$status->EOF) {
            $statusField[] = $status->fields['description'];
            $statusVal[] = $status->fields['idstatus_project'];
            $status->MoveNext();
        }
        
        $type = $db->selectTypeProject();
        while (!$type->EOF) {
            $typeField[] = $type->fields['description'];
            $typeVal[] = $type->fields['idtype_project'];
            $type->MoveNext();
        }
        
        $priority = $db->selectPriority();
        while (!$priority->EOF) {
            $priorityField[] = $priority->fields['name'];
            $priorityVal[] = $priority->fields['idpriority'];
            $priority->MoveNext();
        }
        
        $dependencies = $db->selectDependencies();
        while (!$dependencies->EOF) {
            $dependenciesField[] = $dependencies->fields['name_project'];
            $dependenciesVal[] = $dependencies->fields['idproject'];
            $dependencies->MoveNext();
        }
        
        $db_group = new groups_model();
        $group = $db_group->selectGroup();
        while (!$group->EOF) {
            $groupField[] = $group->fields['name'];
            $groupVal[] = $group->fields['idgroup'];
            $group->MoveNext();
        }
        
        $db_person = new person_model();
        $companies = $db_person->getCompanies();
        while (!$companies->EOF) {
            $companiesField[] = $companies->fields['name'];
            $companiesVal[] = $companies->fields['idcompany'];
            $companies->MoveNext();
        }
        
		
		
		
		$smarty->assign('name_project', $ret->fields['name_project']);
		$smarty->assign('name_reduzido', $ret->fields['name_reduzido']);
		$smarty->assign('creator', $ret->fields['creator']);
		$smarty->assign('url', $ret->fields['url']);
		$smarty->assign('begin_date', $ret->fields['begin_date']);
		$smarty->assign('end_date', $ret->fields['end_date']);
		$smarty->assign('begin_hour', $ret->fields['begin_hour']);
		$smarty->assign('end_hour', $ret->fields['end_hour']);
		$smarty->assign('description', $ret->fields['description']);
		
		
        $smarty->assign('percentual', $percentualField);
        $smarty->assign('percentualVal', $percentualVal);
        $smarty->assign('status', $statusField);
        $smarty->assign('statusVal', $statusVal);
        $smarty->assign('type', $typeField);
        $smarty->assign('typeVal', $typeVal);
        $smarty->assign('priority', $priorityField);
        $smarty->assign('priorityVal', $priorityVal);
        $smarty->assign('group', $groupField);
        $smarty->assign('groupVal', $groupVal);
        $smarty->assign('companies', $companiesField);
        $smarty->assign('companiesVal', $companiesVal);
        $smarty->assign('dependencies', $dependenciesField);
        $smarty->assign('dependenciesVal', $dependenciesVal);
        
        
        
       
       
	   	
	   
	   
	    
        $smarty->display('projectsformedit.tpl.html');
    }
    
    function deactivate(){        
        $id = $_POST['id'];
        $values = "active_project = 0";
        $db = new projects_model();
        $update = $db->update($values, $id);
        
        if($update)
            echo "OK";
        else
            return false;
    }
    
    function enable(){        
        $id = $_POST['id'];
        $values = "active_project = 1";
        $db = new projects_model();
        $update = $db->update($values, $id);
        
        if($update)
            echo "OK";
        else
            return false;
    }
    
    function delete(){        
        $id = $_POST['id'];
        $db = new projects_model();
        $delete = $db->delete($id);
        
        if($delete)
            echo "OK";
        else
            return false;
    }
}
?>