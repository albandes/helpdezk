<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmCDD extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmCDD');
        
        $this->loadModel('cdd_model');
        $this->dbCDD = new cdd_model();

        $this->loadModel('titles_model');
        $this->dbTitles = new titles_model();

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lmm-cdd-grid.tpl');
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
            $sidx ='code';
        if(!$sord)
            $sord ='ASC';
            
        if ($_POST['_search'] == 'true'){
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbCDD->getCDD($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Collection. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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

        $rsBrand =  $this->dbCDD->getCDD($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsBrand['data']->fields['idcdd'],
                'code'          => $rsBrand['data']->fields['code'], 
                'descr'         => $rsBrand['data']->fields['description'],               
                'status_fmt'    => $status_fmt,
                'status'        => $rsBrand['data']->fields['status']

            );
            $rsBrand['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    //FUNCIONALIDADES ESPECÍFICAS DESSE PROGRAMA
    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenCDD($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmcdd-create.tpl');
    }

    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbCDD->getCDD("WHERE idcdd = $lmmID");

        $this->makeScreenCDD($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idcdd', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmcdd-update.tpl');

    }


    public function viewCDD()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbCDD->getCDD("WHERE idcdd = $lmmID");

        $this->makeScreenCDD($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmcdd-view.tpl');

    }


    function makeScreenCDD($objSmarty,$rs,$oper)
    {
        // --- CDD ---
        if ($oper == 'update') {
            if (empty($rs->fields['code']))
                $objSmarty->assign('plh_code','Informe o CDD.');
            else
                $objSmarty->assign('code',$rs->fields['code']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_code','Informe o CDD.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('code',$rs->fields['code']);
        }

         // --- Description ---
         if ($oper == 'update') {
            if (empty($rs->fields['description']))
                $objSmarty->assign('plh_descr','Descrição');
            else
                $objSmarty->assign('descr',$rs->fields['description']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_descr','Descrição');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('descr',$rs->fields['description']);
        }


        // --- Status ---
        if ($oper == 'update') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == 1 ? "checked=checked" : "");                
        }

    }


    function createCDD()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $code= trim($_POST['code']);
        $descr= trim($_POST['descr']);
        $status = isset($_POST['citystatus']) ? 0 : 1;

        $this->dbCDD->BeginTrans();

        $ret = $this->dbCDD->insertCDD($code,$descr,$status);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbCDD->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbCDD->CommitTrans();

        echo json_encode($aRet);

    }


    function updateCDD()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $lmmID = $_POST['idcdd'];
        $code= trim($_POST['code']);
        $descr= trim($_POST['descr']);       

        $this->dbCDD->BeginTrans();

        $ret = $this->dbCDD->updateCDD($lmmID,$code,$descr);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbCDD->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbCDD->CommitTrans();

        echo json_encode($aRet);

    }


    function deleteCDD()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idcdd_modal'];

        $this->dbCDD->BeginTrans();

        $ret = $this->dbCDD->deleteCDD($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbCDD->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbCDD->CommitTrans();

        echo json_encode($aRet);

    }


    function existCodeCDD(){

        $code =trim( $_POST['code']);

        $where = "WHERE  code = '$code'";
        $where .= (isset($_POST['idcdd'])) ? "AND idcdd!= {$_POST['idcdd']}" : "";

        $check =  $this->dbCDD->getCDD($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get CDD. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }


    function existDescrCDD(){
    
        $code = $_POST['code'];
        $descr = $_POST['descr'];    

        
        $where = "WHERE  code = '$code' AND description ='$descr'";
        $where .= (isset($_POST['idcdd'])) ? "AND idcdd!= {$_POST['idcdd']}" : "";


        $check =  $this->dbCDD->getCDD($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Collection. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }


    function statusCDD()
    {
        $lmmID = $_POST['cddID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbCDD->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update CDD status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function checksCDD(){

        $lmmID = $_POST['cddID'];

        $where = "WHERE  a.idcdd = $lmmID ";

        $check =  $this->dbTitles->getTitles($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get titles. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true));

        } else {

            echo json_encode(array('success'=>false));

        }

    }


    }