<?php

class Groups extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
		$program = $bd->selectProgramIDByController("groups/");
        $typeperson = $bd->selectTypePerson($user);
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();

        $smarty->display('groups.tpl.html');
    }
	
	public function modalInsert() {
		$smarty = $this->retornaSmarty();
		$db = new groups_model();
        $select = $db->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
		$smarty->display('modais/groups/insert.tpl.html');
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
					$qtype = "tbp.name";
                    $where = "and  $qtype LIKE '%$query%' ";
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

        $bd = new groups_model();
        $rs = $bd->selectGroup($where, $order, $limit);

        $qcount = $bd->countGroups($where);
        $total = $qcount->fields['total'];
        /*
          header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
          header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
          header("Cache-Control: no-cache, must-revalidate" );
          header("Pragma: no-cache" );
          header("Content-type: text/x-json");
         */
        $data['page'] = $page;
        $data['total'] = $total;

        while (!$rs->EOF) {
            if ($rs->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";;
            }
            $rows[] = array(
                "id" => $rs->fields['idgroup'],
                "cell" => array(
                    $rs->fields['name'], 
                    $rs->fields['lvl'],
                    $rs->fields['company'],
                    $status                    
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
        $costumer = $_POST['costumer'];
        $name = $_POST['name'];
        $level = $_POST['level'];
        $repass = $_POST['repass_only'];
		if(!$repass) $repass = "N";

        $db = new groups_model();
        $db2 = new person_model();
        $db->BeginTrans();
        $per = $db2->insertPerson('3', '6', '1', '1', $name, NULL, NULL, 'A', 'N', NULL, NULL, NULL);
        if(!$per){
            $db->RollbackTrans();  
            return false;
        }
        $ret = $db->insertGroup($per, $level, $costumer, $repass);
        if (!$ret) {
            $db->RollbackTrans();  
            return false;
        }
        $db->CommitTrans(); 
        echo "OK";
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/groups/disable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
        $bd = new groups_model();
        $dea = $bd->groupsDeactivate($id);
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
		$smarty->display('modais/groups/active.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new groups_model();
        $dea = $bd->groupsActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        // pegamos o id passado no link no formato /modulo/controller/action/id/variavel pelo método getParam do Framework.
        $id = $this->getParam('id');
        $db = new groups_model();
        $ret = $db->selectGroupData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->assign('idperson', $ret->fields['idperson']);
        $smarty->assign('level', $ret->fields['lvl']);
        $smarty->assign('company', $ret->fields['idcustomer']);
        $smarty->assign('repass', $ret->fields['repass_only']);
        $select = $db->selectCorporations();
        while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpsids', $campos);
        $smarty->assign('corpsvals', $valores);
        $smarty->display('modais/groups/edit.tpl.html');
    }

    public function edit() {
        $costumer = $_POST['costumer'];
        $name = $_POST['name'];
        $level = $_POST['level'];
        $repass = $_POST['repass_only'];
		$id = $_POST['id'];
		$idperson = $_POST['idperson'];
		if(!$repass) $repass = "N";        
        
        $dbp = new person_model();
        $dbp->BeginTrans();
        $per = $dbp->updatePerson($idperson, '3', '6', $name, NULL, 'N', NULL, NULL, NULL, NULL, NULL, NULL);
        if(!$per){
        	$dbp->RollbackTrans();
        	return false;
        }
		
		$db = new groups_model();
        $upd = $db->updateGroup($id, $costumer, $repass, $level);
		if(!$upd){
			$dbp->RollbackTrans();
        	return false;
		}
		$dbp->CommitTrans();
        echo "OK";
    }

    public function services() {
        $smarty = $this->retornaSmarty();
        $bd = new groups_model();
        $select = $bd->selectAllServices();
        $COD_ITEM_ANT = "0";
        //$lista = "<select name='service' id='service' class='w200 mt15'>";
        while (!$select->EOF) { 
          if ($COD_ITEM_ANT != $select->Fields("iditem")) { 
           $lista.= "<option disabled style='background:#E4F4F8'>Item: " .$select->Fields('item')."</option>";
           }
           $COD_ITEM_ANT = $select->Fields('iditem');
           $lista.= "<option value='". $select->Fields('idservice'). "'>". $select->Fields('service') ."</option>";
           $select->MoveNext();
        }
        //$lista.="</select>";
        $smarty->assign('lista', $lista);
        $smarty->display('modais/groups/servicesbygroup.tpl.html');
    }

    public function loadservices() {
        $id = $_POST['id'];
        $bd = new groups_model();
        $selectGr = $bd->selectServiceGroup($id);
        $table = "<table id='attendants_table'>";
        $table.= "(".$selectGr->fields['lvl'].") ".$selectGr->fields['groupname'];
        $table.= "</table>";
        echo $table;
    }

    public function attendants() {
        $smarty = $this->retornaSmarty();
        $bd = new groups_model();
        $select = $bd->selectGroup("AND tbg.status ='A'", "ORDER BY name ASC", NULL);
        while (!$select->EOF) {
            $campos[] = $select->fields['idgroup'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('groupsids', $campos);
        $smarty->assign('groupsvals', $valores);
        $smarty->display('modais/groups/attendantsbygroup.tpl.html');
    }

    public function loadattendants() {
        $id = $_POST['id'];

        $bd = new groups_model();
        $selectGr = $bd->selectAttendants();
        $table = "<table id='attendants_table'>";
        while (!$selectGr->EOF) {
            $check = $bd->checkAttendantGroup($selectGr->fields['idperson'], $id);
            if ($check->fields) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            $table.= "<tr><td><input name='".$selectGr->fields['idperson']."-".$id."' id='".$selectGr->fields['idperson']."-".$id."' type='checkbox' $checked />" . "  <label for='".$selectGr->fields['idperson']."-".$id."'>" . $selectGr->fields['name'] . "</label></tr></td>";
            $selectGr->MoveNext();
        }
        $table.="</table>";
        echo $table;
    }
    public function groupinsert(){
        extract($_POST);
		$data = explode('-', $id);
        
        $bd = new permissions_model();
        $grant = $bd->groupPersonInsert($data[1], $data[0]);
        if ($grant){
            echo "OK";
        }
        else{
            return false;
        }
    }
	
    public function groupdelete(){
        extract($_POST);
        
        //$data = mb_split('-', $id);
		$data = explode('-', $id);
        //echo("person= ".$data['0']." group= ".$data['1']);
        
        $bd = new permissions_model();
        $delete = $bd->groupPersonDelete($data[1], $data[0]);
        if ($delete){
            echo "OK";
        }
        else{
            return false;
        }
    }
	
	public function repassGroups() {
        $smarty = $this->retornaSmarty();
        $bd = new groups_model();
        $select = $bd->getGroupsRepass();
        
		while (!$select->EOF) {
            $campos[] = $select->fields['idperson'];
            $valores[] = $select->fields['grp'] ."(".$select->fields['company'].")";
            $select->MoveNext();
        }
        $smarty->assign('groupsids', $campos);
        $smarty->assign('groupsvals', $valores);
	
        $smarty->display('modais/groups/repassgroups.tpl.html');
    }
	
	public function getCompaniesRepass(){
		$idgroup = $program = $this->getParam('idgroup');
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		$db = new groups_model();
		//PEGAR ALIAS DESTE GRUPO
		$getAlias = $db->getGroupsAlias($idgroup);
		while (!$getAlias->EOF) {
            $idsalias[] = $getAlias->fields['idgroup'];
            $getAlias->MoveNext();
        }        
        $getCompanies = $db->selectCorporations();        
		$list = "<p class='mb10' style='text-align: center;'><strong>".$langVars['List_comp_groups']."</strong></p>";
		$list .= '<ul class="lstForm clearfix">';		
        while (!$getCompanies->EOF) {
            $idcompany = $getCompanies->fields['idperson'];
            $namecompany = $getCompanies->fields['name'];
			$getCompanyGroups = $db->selectGroup("AND tbp2.idperson = $idcompany","ORDER BY name ASC");
			$getCountGroups = $db->countGroups("AND tbg.idcustomer = $idcompany");
			$getCountGroupsTotal = $getCountGroups->fields['total'];	
			if($getCountGroupsTotal > 0){		
				$list .='<li><ul><li class="info w150"><label for="company'.$idcompany.'">'.$namecompany.':</label></li>';
				$list .= '<li class="field"><select name="groupsIds[]" id="company'.$idcompany.'" class="w200"><option value="0">'.$langVars['Select'].'</option>';
				while (!$getCompanyGroups->EOF) {
					$idgroup = $getCompanyGroups->fields['idperson'];
	            	$namegroup = $getCompanyGroups->fields['name'];
					if(in_array($idgroup, $idsalias)){
						$list .='<option value="'.$idgroup.'" selected="selected">'.$namegroup.'</option>';	
					}else{
						$list .='<option value="'.$idgroup.'">'.$namegroup.'</option>';
					}
					$getCompanyGroups->MoveNext();
				}
				$list .= "</select></li></ul></li>";
			}			
            $getCompanies->MoveNext();
        }        
		$list .= "</ul>";
		echo $list;		
	}

	public function insertRepassGroups(){
		$bd = new groups_model();
		$bd->BeginTrans();

		$idalias = $_POST['groupsRepass'];
		$delRep = $bd->deleteGroupsRepass($idalias);

		if($delRep){
			foreach ($_POST['groupsIds'] as $idGroups) {
				if($idGroups != 0){
					$insertAlias = $bd->insertGroupsRepass($idGroups,$idalias);
					if(!$insertAlias){
						$bd->RollbackTrans();
						return false;
					}
				}
			}
			$bd->CommitTrans();
			echo "OK";
		}else{
			$bd->RollbackTrans();
			return false;
		}
	}
	
	public function checkNameGroup(){
		$id_company = $_POST['costumer'];
		$name_group = $_POST['name'];
		$bd = new groups_model();
		$check = $bd->checkNameGroup($id_company, $name_group);
		if($check)
			echo false;
		else
			echo true;		
	}

}

?>
