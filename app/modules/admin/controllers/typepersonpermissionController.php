<?php

class typepersonpermission extends Controllers {
		
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("typepersonpermission/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('typepersonpermission.tpl.html');
    }

    public function json() {
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
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
                case 'tbp.name':
                    $where = "and  $qtype LIKE '$query%' ";
                    break;
                default:
                    break;
            }
        }
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";

        $bd = new programs_model();
        $rsProgram = $bd->selectProgram($where, $order, $limit);

        $qcount = $bd->countProgram($where);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsProgram->EOF) {
            if ($rsProgram->fields['status'] == 'A') {
                $status = "Active";
            } else {
                $status = "Not Active";
            }
            $idprogram = $rsProgram->fields['idprogram'];
            $db = new permissions_model();
            $checknew = $db->checkForPermissions($idprogram);
            if ($checknew->fields['allow']) {
                $icon = "<img src='" . path . "/app/themes/".theme."/images/" . $this->getConfig('lang') . "-icon-new.png' height='11px' width='28px'>";
            } else {
                $icon = '';
            }
			
			if($rsProgram->fields['smarty'])
				$name_pgr = $langVars[$rsProgram->fields['smarty']];
			else
				$name_pgr = $rsProgram->fields['name'];
			
            $rows[] = array(
                "id" => $rsProgram->fields['idprogram'],
                "cell" => array(
                    $icon,
                    $name_pgr,
                    $rsProgram->fields['module']
                )
            );
            $rsProgram->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function manageperm() {
        $smarty = $this->retornaSmarty();
        // pegamos o id passado no link no formato /modulo/controller/action/id/variavel pelo mรฉtodo getParam do Framework.
        $id = $this->getParam('id');
        $smarty->assign('id', $id);
        $smarty->display('gridtypepersonpermisson.tpl.html');
    }

    public function insertperm() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new permissions_model();
        $sel = $bd->selectTypePrograms();
        $lista = "";
        $vars = array();
        $i = 0;
        while (!$sel->EOF) {
            $lista.="<h3><a href='#'>" . $sel->fields['type'] . "</a></h3>";
            $lista.="<div><ul>";
            $lista.="</BR>";
            $lista.="<div id='insidetext'>";
            $defaultop = $bd->selectDefaultOperations($id);
            while (!$defaultop->EOF) {

                $lista.="aaa<input type='checkbox' id='" . $sel->fields['id'] . "-" . $defaultop->fields['id'] . "' name='" . $sel->fields['id'] . "-" . $defaultop->fields['id'] . "' onchange='send(this.name)';>" . $defaultop->fields['type'] . " IDCAMPO=" . $sel->fields['id'] . "-" . $defaultop->fields['id'] . "</BR>";
                $var[$i] = $sel->fields['type'] . "-" . $defaultop->fields['type'];
                $i++;


                $defaultop->MoveNext();
            }
            $sel->MoveNext();
            $lista.="</div>";
            $lista.="</BR>";
            $lista.="</ul></div>";
        }
        $lista.="</BR>";
        $smarty->assign('var', $var);
        $smarty->assign('lista', $lista);
        $smarty->assign('idprogram', $id);
        $smarty->display('inserttypepersonpermission.tpl.html');
    }

    public function grantpermission() {
        extract($_POST);

        $bd = new permissions_model();
        $grant = $bd->grantPermission($idprogram, $idaccesstype, $idtypeperson, $check);
        if ($grant) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function revokepermission() {
        extract($_POST);

        $bd = new permissions_model();
        $grant = $bd->revokePermission($idprogram, $idaccesstype, $idtypeperson, $check);
        if ($grant) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function typepersonjson() {
        $idprogram = $this->getParam('idprogram');
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
                case 'tbp.name':
                    $where = "and  $qtype LIKE '$query%' ";
                    break;
                default:
                    break;
            }
        }
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";

        $bd = new permissions_model();
        $sel = $bd->selectTypePrograms();
        
        $total = $bd->selectCountTypePrograms();
        
        $data['page'] = $page;
        $data['total'] = $total;
        
		$defPerms = $bd->getDefaultPerms($idprogram);
		while (!$defPerms->EOF) {
			$defP[$defPerms->fields['idaccesstype']] = $defPerms->fields['idaccesstype'];			
			$defPerms->MoveNext();	
		}
		
        while (!$sel->EOF) {
            $typeperson = $sel->fields['id'];
                for ($accesstype = 1; $accesstype <= 7; $accesstype++) {
                    $access = $bd->selectProgramFunctions($idprogram, $typeperson, $accesstype);
                    $disabled = "";
                    switch ($accesstype) {
                        case 1 :
							if(!$defP[1]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $access1 = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $access1 = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                        case 2 :
							if(!$defP[2]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $new = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $new = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                        case 3 :
							if(!$defP[3]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $edit = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $edit = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                        case 4 :
							if(!$defP[4]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $delete = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $delete = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                        case 5 :
							if(!$defP[5]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $export = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $export = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                        case 6 :
							if(!$defP[6]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $email = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $email = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                        case 7 :
							if(!$defP[7]) $disabled = "disabled='disabled'";
                            if ($access->fields['perm'] == 'Y') {
                                $sms = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            } else {
                                $sms = "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            }
                            break;
                    }
                }
            $rows[] = array(
                "id" => $sel->fields['id'],
                "cell" => array(
                    ucfirst($sel->fields['type']),
                    $access1,
                    $new,
                    $edit,
                    $delete,
                    $export,
                    $email,
                    $sms
                )
            );
            $sel->MoveNext();
        }

        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

}

?>
