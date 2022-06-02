<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpHolderType extends lgpCommon
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
        $this->idprogram =  $this->getIdProgramByController('lgpHolderType');

        $this->loadModel('holdertype_model');
        $this->dbHoldertype = new holdertype_model();

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

        $smarty->display('lgp-holdertype-grid.tpl');

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
        
        
        $rsCount = $this->dbHoldertype->getHoldertype($where);
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

        $rsHoldertype =  $this->dbHoldertype->getHoldertype($where,$order,$limit);
        if (!$rsHoldertype['success']) {
            if($this->log)
                $this->logIt("Error: {$rsHoldertype['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsHoldertype['data']->EOF) {
            $status_fmt = ($rsHoldertype['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsHoldertype['data']->fields['idtipotitular'],
                'name'            => $rsHoldertype['data']->fields['nome'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsHoldertype['data']->fields['status']
            );
            $rsHoldertype['data']->MoveNext();
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

        $this->makeScreenHoldertype($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-holdertype-create.tpl');
    }
	

    

	

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $holdertypeID = $this->getParam('holdertypeID');
        $rsHoldertype = $this->dbHoldertype->getHoldertype("WHERE idtipotitular = $holdertypeID") ;

        $this->makeScreenHoldertype($smarty,$rsHoldertype['data'],'update');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idholdertype', $holdertypeID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-holdertype-update.tpl');

    }

    public function viewHoldertype()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $holdertypeID = $this->getParam('holdertypeID');
        $rsHoldertype = $this->dbHoldertype->getHoldertype("WHERE idtipotitular = $holdertypeID") ;

        $this->makeScreenHoldertype($smarty,$rsHoldertype['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idholdertype', $holdertypeID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-holdertype-view.tpl');

    }

    function makeScreenHoldertype($objSmarty,$rs,$oper)
    {
        
		
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_holdertype_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('holdertypeName',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('lgp_holdertype_nome',$this->getLanguageWord('Name'));
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_holdertype_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('holdertypeName',$rs->fields['nome']);
        }

       

        // --- Default ---
        if ($oper == 'update')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? "" : "checked=checked"));
        elseif ($oper == 'echo')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? $this->getLanguageWord('No') : $this->getLanguageWord('Yes')));

    }

    function createHoldertype()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $holdertypeName = strip_tags($_POST['holdertypeName']);
        $default = isset($_POST['default']) ? 1 : 0;

        $this->dbHoldertype->BeginTrans();

        $ret = $this->dbHoldertype->insertHoldertype($holdertypeName,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Holdertype data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbHoldertype->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "holdertypeID" => $ret['id']
        );

        $this->dbHoldertype->CommitTrans();

        echo json_encode($aRet);

    }

    function updateHoldertype()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
		$default = isset($_POST['default']) ? 1 : 0;
        $holdertypeID = $_POST['holdertypeID'];
        $holdertypeName = strip_tags($_POST['holdertypeName']);
        $this->dbHoldertype->BeginTrans();

        $ret = $this->dbHoldertype->updateHoldertype($holdertypeID,$holdertypeName, $default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbHoldertype->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "holdertypeID"    => $holdertypeID
        );

        $this->dbHoldertype->CommitTrans();

        echo json_encode($aRet);

    }

    function statusHoldertype()
    {
        $holdertypeID = $_POST['holdertypeID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbHoldertype->changeStatus($holdertypeID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update holdertype status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "holdertypeID" => $holdertypeID
        );

        echo json_encode($aRet);

    }
	
	public function existHoldertype() {
        $this->protectFormInput();
        $search = $_POST['holdertypeName'];

        $where = "WHERE `nome` LIKE '{$search}'";
        $where .= isset($_POST['holdertypeID']) ? " AND idtipotitular != {$_POST['holdertypeID']}" : "";

        $check = $this->dbHoldertype->getHoldertype($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('holdertype_exists'));
        } else {
            echo json_encode(true);
        }
    }

}