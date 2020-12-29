<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class Program extends admCommon
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
        $this->idprogram =  $this->getIdProgramByController('program');


        $this->loadModel('programs_model');
        $dbProgram = new programs_model();
        $this->dbProgram = $dbProgram;



    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        $smarty->display('programs.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $this->protectFormInput();

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
            if ( $_POST['searchField'] == 'category') $searchField = 'tbtp.name';
            if ( $_POST['searchField'] == 'tbm.name') $searchField = 'tbm.name';

            if (empty($where))
                $oper = ' AND ';
            else
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
            $name_pgr = $this->getLanguageWord($rsPrograms->fields['smarty']) ? $this->getLanguageWord($rsPrograms->fields['smarty']) : $rsPrograms->fields['name'];
            
            $aColumns[] = array(
                'id'            => $rsPrograms->fields['idprogram'],
                'name'          => $name_pgr,
                'controller'    => $rsPrograms->fields['controller'],
                'module'        => $rsPrograms->fields['module'],
                'category'      => $rsPrograms->fields['category'],
                'status'        => $status_fmt,
                'statusval'     => $rsPrograms->fields['status']  
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

    public function formCreateProgram()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenPrograms($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('programs-create.tpl');
    }

    public function formUpdateProgram()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idProgram = $this->getParam('idprogram');
        
        $rsProgram = $this->dbProgram->selectProgramData($idProgram);

        $this->makeScreenPrograms($smarty,$rsProgram,'update',$idProgram);

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idprogram', $idProgram);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('programs-update.tpl');

    }

    function makeScreenPrograms($objSmarty,$rs,$oper,$idprogram=NULL)
    {
        // --- Module droplist ---
        $plh_msg = $this->getLanguageWord('Select_module');
        $arrModule = $this->_comboModule();
        
        if ($oper == 'update' || $oper == 'echo') {
            $retModule = $this->dbProgram->selectProgramModule($rs->fields['idprogramcategory']);
            $idModuleEnable = $retModule->fields['idmodule'];
            $objSmarty->assign('module_description',$retModule->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_module_select', $plh_msg);
            $idModuleEnable = $arrModule['ids'][0];            
        } 

        $objSmarty->assign('moduleids',  $arrModule['ids']);
        $objSmarty->assign('modulevals',$arrModule['values']);
        $objSmarty->assign('idmodule', $idModuleEnable );

        // --- Category droplist ---
        $plh_msg = $this->getLanguageWord('Select_category');
        $arrCategory = $this->_comboCategory($idModuleEnable);
        
        if ($oper == 'update') {
            $idCategoryEnable = $rs->fields['idprogramcategory'];
                $objSmarty->assign('category_description',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $idCategoryEnable = $arrCategory['ids'][0];
            $objSmarty->assign('plh_category_select', $plh_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('category_description',$rs->fields['name']);
        }

        $objSmarty->assign('categoryids',  $arrCategory['ids']);
        $objSmarty->assign('categoryvals',$arrCategory['values']);
        $objSmarty->assign('idcategory', $idCategoryEnable );

        // --- Program name ---
        $plh_path_msg = $this->getLanguageWord('plh_program_description');
        
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_program_description',$plh_path_msg);
            else
                $objSmarty->assign('program_description',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_program_description', $plh_path_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('program_description',$rs->fields['name']);
        }

        // --- Program controller ---
        $plh_path_msg = $this->getLanguageWord('plh_controller_description');
        
        if ($oper == 'update') {
            if (empty($rs->fields['controller']))
                $objSmarty->assign('plh_controller_description',$plh_path_msg);
            else
                $objSmarty->assign('controller_description',$rs->fields['controller']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_controller_description', $plh_path_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('controller_description',$rs->fields['controller']);
        }

        // --- Smarty variable ---
        $plh_smarty_msg = $this->getLanguageWord('plh_smarty_variable');
    
        if ($oper == 'update') {
            if (empty($rs->fields['smarty']))
                $objSmarty->assign('plh_module_smartyvar',$plh_smarty_msg);
            else
                $objSmarty->assign('smartyvar',$rs->fields['smarty']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_module_smartyvar', $plh_smarty_msg);            
        } elseif ($oper == 'echo') {
            $objSmarty->assign('smartyvar',$rs->fields['smarty']);
        }

        // --- Default permissions ---
        if ($oper == 'update') {
            $retPerms = $this->dbProgram->getDefaultPermission($idprogram);
            while (!$retPerms->EOF) {
                $arrPerm[$retPerms->fields['idaccesstype']] = $retPerms->fields['idaccesstype'];			
                $retPerms->MoveNext();
            }

            $objSmarty->assign('arrPerm', $arrPerm);
        }
       

    }

    function createProgram()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->protectFormInput();

        $idc = $_POST['cmbCategory'];
		$name = $_POST['txtName'];
		$controller = $_POST['txtController'];
        $smarty = $_POST['txtSmarty'];
        $arrOpe = $_POST['ope'];

        $this->dbProgram->BeginTrans();

        $ret = $this->dbProgram->insertProgram($name, $controller, $smarty, $idc);
        if(!$ret){
			$this->dbProgram->RollbackTrans();
			return false;
        }
        
        $progid = $this->dbProgram->selectProgramID($name, $idc, $controller);

        $access = '1';
		$allow = 'Y';
        $typeperson = $this->dbProgram->getTypePerson();
        while(!$typeperson->EOF) {
            $arrType[] = $typeperson->fields['idtypeperson'];
            $typeperson->MoveNext();
        }

		$default = $this->dbProgram->insertDefaultPermission($progid, $access, $allow);
		foreach($arrType as $typeaccess) {
            $this->dbProgram->insertGroupPermission($progid, $typeaccess, $access);
        }
        
        if(isset($arrOpe)){
            foreach($arrOpe as $ope){
                $this->dbProgram->insertDefaultPermission($progid, $ope, $allow);
                foreach($arrType as $typeaccess) {
                    $this->dbProgram->insertGroupPermission($progid, $typeaccess, $ope);
                }
            }
        }

        $aRet = array(
            "idprogram" => $progid,
            "description" => $name
        );

        $this->dbProgram->CommitTrans();

        echo json_encode($aRet);

    }

    function updateProgram()
    {

        $this->protectFormInput();

        $id = $_POST['idprogram'];
        $idc = $_POST['cmbCategory'];
		$name = $_POST['txtName'];
		$controller = $_POST['txtController'];
        $smarty = $_POST['txtSmarty'];
        $flagPerms = $_POST['flagPerm'];
        $arrOpe = $_POST['ope'];

        $this->dbProgram->BeginTrans();

        $ret = $this->dbProgram->updateProgram($id, $name, $controller, $smarty, $idc);

        if(!$ret){
			$this->dbProgram->RollbackTrans();
			return false;
        }

        if(isset($flagPerms)){
            $clearDefaultPerm = $this->dbProgram->clearDefaultPerm($id);
			if (!$clearDefaultPerm) {
				$this->dbProgram->RollbackTrans();
	        	return false;
			}
			$clearGroupPerm = $this->dbProgram->clearGroupPerm($id);
			if (!$clearDefaultPerm) {
				$this->dbProgram->RollbackTrans();
	        	return false;
            }

            $access = '1';
            $allow = 'Y';
            $typeperson = $this->dbProgram->getTypePerson();
            while(!$typeperson->EOF) {
                $arrType[] = $typeperson->fields['idtypeperson'];
                $typeperson->MoveNext();
            }

            $default = $this->dbProgram->insertDefaultPermission($id, $access, $allow);
            foreach($arrType as $typeaccess) {
                $this->dbProgram->insertGroupPermission($id, $typeaccess, $access);
            }            

            foreach($arrOpe as $ope){
                $this->dbProgram->insertDefaultPermission($id, $ope, $allow);
                foreach($arrType as $typeaccess) {
                    $this->dbProgram->insertGroupPermission($id, $typeaccess, $ope);
                }
            }
        }



        $aRet = array(
            "idprogram" => $id,
            "status"   => 'OK'
        );

        $this->dbProgram->CommitTrans();

        echo json_encode($aRet);


    }

    public function categoryinsert()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->protectFormInput();

        $module_post = $_POST['cmbModuleMod'];
        $name_post = $_POST['txtNewCategory'];
        
        $this->dbProgram->BeginTrans();

        $ret = $this->dbProgram->categoryInsert($name_post, $module_post);
        if(!$ret){
			$this->dbProgram->RollbackTrans();
			return false;
        }
        
        $categoryid = $this->dbProgram->lastIdCategory();

        $aRet = array(
            "idcategory" => $categoryid,
            "status" => "ok"
        );

        $this->dbProgram->CommitTrans();
        echo json_encode($aRet);

    }

    public function ajaxModule()
    {
        echo $this->comboModuleHtml();
    }

    public function comboModuleHtml()
    {
        $arrModule = $this->_comboModule();
        $select = '';
        
        foreach ( $arrModule['ids'] as $indexKey => $indexValue ) {
            if ($arrModule['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrModule['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function ajaxCategory()
    {
        echo $this->comboCategoryHtml($_POST['idmodule']);
    }

    public function comboCategoryHtml($idmodule)
    {
        $arrCategory = $this->_comboCategory($idmodule);
        $select = '';
        
        foreach ( $arrCategory['ids'] as $indexKey => $indexValue ) {
            if ($arrCategory['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrCategory['values'][$indexKey]."</option>";
        }

        return $select;
    }

    function statusProgram()
    {
        $this->protectFormInput();

        $idProgram = $this->getParam('idprogram');
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbProgram->changeProgramStatus($idProgram,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Program Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idprogram" => $idProgram,
            "status" => 'OK',
            "programstatus" => $newStatus
        );

        echo json_encode($aRet);

    }

}