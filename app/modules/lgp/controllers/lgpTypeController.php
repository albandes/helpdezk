<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpType extends lgpCommon
{
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {
		
        set_time_limit(0);
        parent::__construct();
        session_start();
        $this->sessionValidate();
		
        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'LGPD';
        $this->idmodule = $this->getIdModule($this->modulename);
		
        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpType');
		
        $this->loadModel('type_model');
        $this->dbType = new type_model();

		

    }

	
    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $token = $this->_makeToken();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);

        $smarty->assign('token', $token) ;

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('lgp-type-grid.tpl');

    }


    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='nome';
        if(!$sord)
            $sord ='ASC';
		
		if ($_POST['_search'] == 'true'){
            

            $where .= ' WHERE ' . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }
        
        
        $rsCount = $this->dbType->getTipo($where);
        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Error: {$rsCount['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        } 
        $count = $rsCount['data']->RecordCount();

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";

        $rsTipo =  $this->dbType->getTipo($where,$order,$limit);
        if (!$rsTipo['success']) {
            if($this->log)
                $this->logIt("Error: {$rsTipo['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsTipo['data']->EOF) {
            $status_fmt = ($rsTipo['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsTipo['data']->fields['idtipodado'],
                'name'            => $rsTipo['data']->fields['nome'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsTipo['data']->fields['status']
            );
            $rsTipo['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

     public function formCreate()
    {
        $smarty = $this->retornaSmarty(); 
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeScreenType($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-type-create.tpl');
    }
	
	

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $tipoID = $this->getParam('tipoID');
        $rsTipo = $this->dbType->getTipo("WHERE idtipodado = $tipoID") ;

        $this->makeScreenType($smarty,$rsTipo['data'],'update');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idtipodado', $tipoID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-type-update.tpl');

    }

    public function viewType()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $tipoID = $this->getParam('tipoID');
        $rsTipo = $this->dbType->getTipo("WHERE idtipodado = $tipoID") ;

        $this->makeScreenType($smarty,$rsTipo['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idtipodado', $tipoID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-type-view.tpl');

    }

    function makeScreenType($objSmarty,$rs,$oper)
    {
        
		
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_type_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('tipoName',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('lgp_type_nome',$this->getLanguageWord('Name'));
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_type_nome',$this->getLanguageWord('Name'));
			else
                $objSmarty->assign('tipoName',$rs->fields['nome']);
        }

       
		//echo $rs->fields['default'];
         // --- Default ---
        if ($oper == 'update')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? "" : "checked=checked"));
        elseif ($oper == 'echo')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? $this->getLanguageWord('No') : $this->getLanguageWord('Yes')));

    }

    function createType()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $tipoName = strip_tags($_POST['tipoName']);
        $tipoDefault = isset($_POST['tipoDefault']) ? 1 : 0;

        $this->dbType->BeginTrans();

        $ret = $this->dbType->insertType($tipoName,$tipoDefault);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Type data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbType->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "tipoID" => $ret['id']
        );

        $this->dbType->CommitTrans();

        echo json_encode($aRet);

    }

    function updateType()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
		$tipoDefault = isset($_POST['tipoDefault']) ? 1 : 0;
        $tipoID = $_POST['tipoID'];
        $tipoName = strip_tags($_POST['tipoName']);
        $this->dbType->BeginTrans();

        $ret = $this->dbType->updateType($tipoID,$tipoName, $tipoDefault);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbType->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "tipoID"    => $tipoID
        );

        $this->dbType->CommitTrans();

        echo json_encode($aRet);

    }

    function statusType()
    {
        $tipoID = $_POST['tipoID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbType->changeStatus($tipoID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update type status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "tipoID" => $tipoID
        );

        echo json_encode($aRet);

    }
	
	public function existType() {
        $this->protectFormInput();
        $search = $_POST['tipoName'];

        $where = "WHERE `nome` LIKE '{$search}'";
        $where .= isset($_POST['tipoID']) ? " AND idtipodado != {$_POST['tipoID']}" : "";

        $check = $this->dbType->getTipo($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('type_exists'));
        } else {
            echo json_encode(true);
        }
    }

}