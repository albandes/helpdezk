<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpClassification extends lgpCommon
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
        $this->idprogram =  $this->getIdProgramByController('lgpClassification');

        $this->loadModel('classification_model');
        $this->dbClassification = new classification_model();

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

        $smarty->display('lgp-classification-grid.tpl');

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
        
        
        $rsCount = $this->dbClassification->getClassification($where);
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

        $rsClassification =  $this->dbClassification->getClassification($where,$order,$limit);
        if (!$rsClassification['success']) {
            if($this->log)
                $this->logIt("Error: {$rsClassification['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsClassification['data']->EOF) {
            $status_fmt = ($rsClassification['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsClassification['data']->fields['idclassificacao'],
                'name'            => $rsClassification['data']->fields['nome'],
                'status'        => $rsClassification['data']->fields['status']
            );
            $rsClassification['data']->MoveNext();
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

        $this->makeScreenClassification($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-classification-create.tpl');
    }
	

    

	

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $classificationID = $this->getParam('classificationID');
        $rsClassification = $this->dbClassification->getClassification("WHERE idclassificacao = $classificationID") ;

        $this->makeScreenClassification($smarty,$rsClassification['data'],'update');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idclassificacao', $classificationID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-classification-update.tpl');

    }

    public function viewClassification()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $classificationID = $this->getParam('classificationID');
        $rsClassification = $this->dbClassification->getClassification("WHERE idclassificacao = $classificationID") ;

        $this->makeScreenClassification($smarty,$rsClassification['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idclassificacao', $classificationID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-classification-view.tpl');

    }

    function makeScreenClassification($objSmarty,$rs,$oper)
    {
        
		
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_classification_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('classificationName',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('lgp_classification_nome',$this->getLanguageWord('Name'));
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('lgp_classification_nome',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('classificationName',$rs->fields['nome']);
        }

       

        // --- Default ---
        if ($oper == 'update')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? "" : "checked=checked"));
        elseif ($oper == 'echo')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? $this->getLanguageWord('No') : $this->getLanguageWord('Yes')));

    }

    function createClassification()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $classificationName = trim($_POST['classificationName']);
        $default = isset($_POST['default']) ? 1 : 0;

        $this->dbClassification->BeginTrans();

        $ret = $this->dbClassification->insertClassification($classificationName,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Classification data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbClassification->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "classificationID" => $ret['id']
        );

        $this->dbClassification->CommitTrans();

        echo json_encode($aRet);

    }

    function updateClassification()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
		$default = isset($_POST['default']) ? 1 : 0;
        $classificationID = $_POST['classificationID'];
        $classificationName = trim($_POST['classificationName']);
        $this->dbClassification->BeginTrans();

        $ret = $this->dbClassification->updateClassification($classificationID,$classificationName, $default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbClassification->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "newsID"    => $productID
        );

        $this->dbClassification->CommitTrans();

        echo json_encode($aRet);

    }

    function statusClassification()
    {
        $classificationID = $_POST['classificationID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbClassification->changeStatus($classificationID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update classification status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "classificationID" => $classificationID
        );

        echo json_encode($aRet);

    }
	
	public function existClassification() {
        $this->protectFormInput();
        $search = $_POST['classificationName'];

        $where = "WHERE `nome` LIKE '{$search}'";
        $where .= isset($_POST['classificationID']) ? " AND idclassificacao != {$_POST['classificationID']}" : "";

        $check = $this->dbClassification->getClassification($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('classification_exists'));
        } else {
            echo json_encode(true);
        }
    }

}