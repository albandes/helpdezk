<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpLegalGround extends lgpCommon
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
        $this->idprogram =  $this->getIdProgramByController('lgpLegalGround');
		
        $this->loadModel('legalGround_model');
        $this->dbLegalGround = new legalGround_model();

		

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

        $smarty->display('lgp-legalGround-grid.tpl');

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
        
        
        $rsCount = $this->dbLegalGround->getLegalGround($where);
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

        $rsLegalGround =  $this->dbLegalGround->getLegalGround($where,$order,$limit);
        if (!$rsLegalGround['success']) {
            if($this->log)
                $this->logIt("Error: {$rsLegalGround['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsLegalGround['data']->EOF) {
            $status_fmt = ($rsLegalGround['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsLegalGround['data']->fields['idbaselegal'],
                'name'            => $rsLegalGround['data']->fields['nome'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsLegalGround['data']->fields['status']                   
            );
            $rsLegalGround['data']->MoveNext();
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

        $this->makeScreenLegalGround($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-legalGround-create.tpl');
    }
	
	

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $legalGroundID = $this->getParam('legalGroundID');
        $rsLegalGround = $this->dbLegalGround->getLegalGround("WHERE idbaselegal = $legalGroundID") ;

        $this->makeScreenLegalGround($smarty,$rsLegalGround['data'],'update');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idbaselegal', $legalGroundID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-legalGround-update.tpl');

    }

    public function viewLegalGround()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $legalGroundID = $this->getParam('legalGroundID');
        $rsLegalGround = $this->dbLegalGround->getLegalGround("WHERE idbaselegal = $legalGroundID") ;

        $this->makeScreenLegalGround($smarty,$rsLegalGround['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);


        $smarty->assign('hidden_idbaselegal', $legalGroundID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-legalGround-view.tpl');

    }

    function makeScreenLegalGround($objSmarty,$rs,$oper)
    {
        
		
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_legalGround_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('legalGroundName',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('lgp_legalGround_nome',$this->getLanguageWord('Name'));
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_legalGround_nome',$this->getLanguageWord('Name'));
			else
                $objSmarty->assign('legalGroundName',$rs->fields['nome']);
        }

       
		//echo $rs->fields['default'];
         // --- Default ---
        if ($oper == 'update')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? "" : "checked=checked"));
        elseif ($oper == 'echo')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? $this->getLanguageWord('No') : $this->getLanguageWord('Yes')));

    }

    function createLegalGround()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $legalGroundName = strip_tags($_POST['legalGroundName']);
        $default = isset($_POST['default']) ? 1 : 0;

        $this->dbLegalGround->BeginTrans();

        $ret = $this->dbLegalGround->insertLegalGround($legalGroundName,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Type data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbLegalGround->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "legalGroundID" => $ret['id']
        );

        $this->dbLegalGround->CommitTrans();

        echo json_encode($aRet);

    }

    function updateLegalGround()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
		$default = isset($_POST['default']) ? 1 : 0;
        $legalGroundID = $_POST['legalGroundID'];
        $legalGroundName = strip_tags($_POST['legalGroundName']);
        $this->dbLegalGround->BeginTrans();

        $ret = $this->dbLegalGround->updateLegalGround($legalGroundID,$legalGroundName, $default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbLegalGround->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "legalGroundID"    => $legalGroundID
        );

        $this->dbLegalGround->CommitTrans();

        echo json_encode($aRet);

    }

    function statusLegalGround()
    {
        $legalGroundID = $_POST['legalGroundID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbLegalGround->changeStatus($legalGroundID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update legalGround status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "legalGroundID" => $legalGroundID
        );

        echo json_encode($aRet);

    }
	
	public function existLegalGround() {
        $this->protectFormInput();
        $search = $_POST['legalGroundName'];

        $where = "WHERE `nome` LIKE '{$search}'";
        $where .= isset($_POST['legalGroundID']) ? " AND idbaselegal != {$_POST['legalGroundID']}" : "";

        $check = $this->dbLegalGround->getLegalGround($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('legalGround_exists'));
        } else {
            echo json_encode(true);
        }
    }

}
