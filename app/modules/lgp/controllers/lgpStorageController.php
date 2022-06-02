<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpStorage extends lgpCommon
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
        $this->idprogram =  $this->getIdProgramByController('lgpStorage');
		
        $this->loadModel('storage_model');
        $this->dbStorage = new storage_model();

		

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

        $smarty->display('lgp-storage-grid.tpl');

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
        
        
        $rsCount = $this->dbStorage->getStorage($where);
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

        $rsStorage =  $this->dbStorage->getStorage($where,$order,$limit);
        if (!$rsStorage['success']) {
            if($this->log)
                $this->logIt("Error: {$rsStorage['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsStorage['data']->EOF) {
            $status_fmt = ($rsStorage['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsStorage['data']->fields['idarmazenamento'],
                'name'            => $rsStorage['data']->fields['nome'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsStorage['data']->fields['status']
            );
            $rsStorage['data']->MoveNext();
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

        $this->makeScreenStorage($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-storage-create.tpl');
    }
	
	

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $storageID = $this->getParam('storageID');
        $rsStorage = $this->dbStorage->getStorage("WHERE idarmazenamento = $storageID") ;

        $this->makeScreenStorage($smarty,$rsStorage['data'],'update');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idarmazenamento', $storageID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-storage-update.tpl');

    }

    public function viewStorage()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $storageID = $this->getParam('storageID');
        $rsStorage = $this->dbStorage->getStorage("WHERE idarmazenamento = $storageID") ;

        $this->makeScreenStorage($smarty,$rsStorage['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);


        $smarty->assign('hidden_idarmazenamento', $storageID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-storage-view.tpl');

    }

    function makeScreenStorage($objSmarty,$rs,$oper)
    {
        
		
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_storage_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('storageName',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('lgp_storage_nome',$this->getLanguageWord('Name'));
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_storage_nome',$this->getLanguageWord('Name'));
			else
                $objSmarty->assign('storageName',$rs->fields['nome']);
        }

       
		//echo $rs->fields['default'];
         // --- Default ---
        if ($oper == 'update')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? "" : "checked=checked"));
        elseif ($oper == 'echo')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? $this->getLanguageWord('No') : $this->getLanguageWord('Yes')));

    }

    function createStorage()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $storageName = strip_tags($_POST['storageName']);
        $default = isset($_POST['default']) ? 1 : 0;

        $this->dbStorage->BeginTrans();

        $ret = $this->dbStorage->insertStorage($storageName,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Type data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbStorage->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "storageID" => $ret['id']
        );

        $this->dbStorage->CommitTrans();

        echo json_encode($aRet);

    }

    function updateStorage()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
		$default = isset($_POST['default']) ? 1 : 0;
        $storageID = $_POST['storageID'];
        $storageName = strip_tags($_POST['storageName']);
        $this->dbStorage->BeginTrans();

        $ret = $this->dbStorage->updateStorage($storageID,$storageName, $default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbStorage->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "storageID"    => $storageID
        );

        $this->dbStorage->CommitTrans();

        echo json_encode($aRet);

    }

    function statusStorage()
    {
        $storageID = $_POST['storageID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbStorage->changeStatus($storageID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update storage status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "storageID" => $storageID
        );

        echo json_encode($aRet);

    }
	
	public function existStorage() {
        $this->protectFormInput();
        $search = $_POST['storageName'];

        $where = "WHERE `nome` LIKE '{$search}'";
        $where .= isset($_POST['storageID']) ? " AND idarmazenamento != {$_POST['storageID']}" : "";

        $check = $this->dbStorage->getStorage($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('storage_exists'));
        } else {
            echo json_encode(true);
        }
    }

}