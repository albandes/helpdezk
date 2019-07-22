<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class typepersonpermission extends admCommon
{
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('permissions_model');
        $dbPermissions = new permissions_model();
        $this->dbPermissions = $dbPermissions;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('typepersonpermission.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='tbp.name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'tbp.name') $searchField = 'tbp.name';
            if ( $_POST['searchField'] == 'tbm.name') $searchField = 'tbm.name';

            $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbProgram->countProgram($where);

        if( $count->fields['total'] > 0 && $rows > 0) {
            $total_pages = ceil($count->fields['total']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsPrograms = $this->dbProgram->selectProgram($where,$order,$limit);
        
        while (!$rsPrograms->EOF) {
            
            $status_fmt = ($rsPrograms->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $name_pgr = ($rsPrograms->fields['smarty']) ? $this->getLanguageWord($rsPrograms->fields['smarty']) : $rsPrograms->fields['name'];

            $checknew = $this->dbPermissions->checkForPermissions($rsPrograms->fields['idprogram']);
            $icon = ($checknew->fields['allow']) ? '<span class="label label-info">'.$this->getLanguageWord('New').'</span>' : '';            
            
            $aColumns[] = array(
                'id'            => $rsPrograms->fields['idprogram'],
                'newflag'       => $icon,
                'name'          => $name_pgr,
                'module'        => $rsPrograms->fields['module'],
                'status'        => $rsPrograms->fields['status']  
            );

            $rsPrograms->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function jsonTypePersonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $idprogram = $this->getParam('idprogram');

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='tbty.name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'tbty.name') $searchField = 'tbty.name';

            if (empty($where))
                $oper = ' WHERE ';
            else
            $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $sel = $this->dbPermissions->selectTypePrograms();
        
        $count = $this->dbPermissions->selectCountTypePrograms();

        if( $count->fields['total'] > 0 && $rows > 0) {
            $total_pages = ceil($count->fields['total']/$rows);
        } else {
            $total_pages = 1;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //
        $defPerms = $this->dbPermissions->getDefaultPerms($idprogram);
		while (!$defPerms->EOF) {
			$defP[$defPerms->fields['idaccesstype']] = $defPerms->fields['idaccesstype'];			
			$defPerms->MoveNext();	
		}

        while (!$sel->EOF) {
            
            $typeperson = $sel->fields['id'];
                for ($accesstype = 1; $accesstype <= 7; $accesstype++) {
                    $access = $this->dbPermissions->selectProgramFunctions($idprogram, $typeperson, $accesstype);
                    $disabled = "";
                    
                    switch ($accesstype) {
                        case 1 :
                            if(!$defP[1]) $disabled = "disabled='disabled'";
                            $acc = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            break;
                        case 2 :
                            if(!$defP[2]) $disabled = "disabled='disabled'";
                            $new = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            break;
                        case 3 :
							if(!$defP[3]) $disabled = "disabled='disabled'";
                            $edit = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";                            
                            break;
                        case 4 :
							if(!$defP[4]) $disabled = "disabled='disabled'";
                            $delete = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            break;
                        case 5 :
							if(!$defP[5]) $disabled = "disabled='disabled'";
                            $export = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            break;
                        case 6 :
							if(!$defP[6]) $disabled = "disabled='disabled'";
                            $email = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            break;
                        case 7 :
							if(!$defP[7]) $disabled = "disabled='disabled'";
                            $sms = ($access->fields['perm'] == 'Y') ? "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' checked='checked' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>" : "<input type='checkbox' $disabled id='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' name='" . $accesstype . "-" . $sel->fields['id'] . "-" . $idprogram ."' onchange='edit2(this.name,".$idprogram.",".$accesstype.",".$typeperson.");'>";
                            break;
                    }
                }           
            
            $aColumns[] = array(
                'idtypeperson'      => $typeperson,
                'typedescrition'    => ucfirst($sel->fields['type']),
                'access'            => $acc,
                'new'               => $new,
                'edit'              => $edit,
                'delete'            => $delete,
                'export'            => $export,
                'email'             => $email,
                'sms'               => $sms,
                'idprogram'         => $idprogram 
            );

            $sel->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function managepermission() {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idProgram = $this->getParam('idprogram');
        
        $smarty->assign('token', $token) ;
        $smarty->assign('hidden_idprogram', $idProgram);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('typepersonpermission-edit.tpl');
    }

    public function grantpermission()
    {
        $idprogram = $_POST['idprogram']; 
        $idaccesstype = $_POST['idaccesstype'];
        $idtypeperson = $_POST['idtypeperson'];
        $check = $_POST['check'];
        
        $this->dbPermissions->BeginTrans();
        $grant = $this->dbPermissions->grantPermission($idprogram, $idaccesstype, $idtypeperson, $check);
        if(!$grant){
			$this->dbPermissions->RollbackTrans();
			return false;
        }
        
        $aRet = array(
            "idprogram" => $idprogram,
            "status"   => 'OK'
        );

        $this->dbPermissions->CommitTrans();

        echo json_encode($aRet);

    }

}