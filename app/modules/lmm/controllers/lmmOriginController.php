<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmOrigin extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmOrigin');
        
        $this->loadModel('origin_model');
        $this->dbOrigin = new origin_model();

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

        $smarty->display('lmm-origin-grid.tpl');
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
            $sidx ='name';
        if(!$sord)
            $sord ='ASC';
            
        if ($_POST['_search'] == 'true'){
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbOrigin->getOrigin($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Origin. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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

        $rsBrand =  $this->dbOrigin->getOrigin($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $padrao_fmt = ($rsBrand['data']->fields['default'] == 'Y' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsBrand['data']->fields['idorigin'],
                'origin'        => $rsBrand['data']->fields['name'],                
                'status_fmt'    => $status_fmt,
                'status'        => $rsBrand['data']->fields['status'],
                'padrao_fmt'    => $padrao_fmt,
                'padrao'        => $rsBrand['data']->fields['default']

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

        $this->makeScreenOrigin($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmorigin-create.tpl');
    }

    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbOrigin->getOrigin("WHERE idorigin = $lmmID");

        $this->makeScreenOrigin($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idorigin', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmorigin-update.tpl');

    }


    public function viewOrigin()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbOrigin->getOrigin("WHERE idorigin = $lmmID");

        $this->makeScreenOrigin($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmorigin-view.tpl');

    }


    function makeScreenOrigin($objSmarty,$rs,$oper)
    {
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_origin','Informe o nome.');
            else
                $objSmarty->assign('origin',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_origin','Informe o Nome.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('origin',$rs->fields['name']);
        }

        // --- Name ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == "Y" ? "checked=checked" : "");                
        }

    }


    function createOrigin()
    {
         $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $origin= trim($_POST['origin']);
        $status = isset($_POST['citystatus']) ? 0 : 1;
        $default = isset($_POST['cityDefault']) ? 1 : 0;


        $this->dbOrigin->BeginTrans();

        if($default==1){
            $rem = $this->dbOrigin->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update Origin Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbOrigin->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbOrigin->insertOrigin($origin,$status,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbOrigin->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbOrigin->CommitTrans();

        echo json_encode($aRet);

    }


    function updateOrigin()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $msg ="";
        $lmmID = $_POST['idorigin'];
        $origin= trim($_POST['origin']);
        $default = isset($_POST['cityDefault']) ? 1 : 0;     

        $this->dbOrigin->BeginTrans();

        if($default==1){
            $rem = $this->dbOrigin->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update Origin Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbOrigin->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbOrigin->updateOrigin($lmmID,$origin,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbOrigin->RollbackTrans();
            return false;
        }

        $origin=$this->_comboOrigin();        
        if(sizeof($origin['default'])<=0){
            $retorigin = $this->dbOrigin->changePadrao($origin['ids'][0]);           
            if (!$retorigin['success']) {
                if($this->log)
                    $this->logIt("Can't update Origin Padrao. {$retorigin['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }           
           $msg=$origin['ids'][0]==$lmmID?$this->getLanguageWord('Edit_the_field_Pettern'):"";
     
        } 

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID,
            "message"  =>$msg
        );

        $this->dbOrigin->CommitTrans();

        echo json_encode($aRet);

    }


    function deleteOrigin()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idorigin_modal'];

        $this->dbOrigin->BeginTrans();

        $ret = $this->dbOrigin->deleteOrigin($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbOrigin->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbOrigin->CommitTrans();

        echo json_encode($aRet);

    }


    function padraoOrigin()
    {
        $lmmID = $_POST['originID'];

        $rem = $this->dbOrigin->removerPadrao();

        if (!$rem['success']) {
            if($this->log)
                $this->logIt("Can't update Origin Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }


        $ret = $this->dbOrigin->changePadrao($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Origin Padrao. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }
    

    function existOrigin(){

        $origin = $_POST['origin'];

        $where = "WHERE  name = '$origin'";

        $check =  $this->dbOrigin->getOrigin($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Origin. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function existOriginUp()
    {

        $origin =trim($_POST['origin']);
        $where = "WHERE  name = '$origin'";
        $where .= (isset($_POST['idorigin'])) ? "AND idorigin!= {$_POST['idorigin']}" : "";        

        $check =  $this->dbOrigin->getOrigin($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Origin. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function statusOrigin()
    {
        $lmmID = $_POST['originID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbOrigin->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Origin status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function checksOrigin(){

        $idorigin = $_POST['originID'];

        $where = "AND  a.idorigin = $idorigin";

        $check =  $this->dbTitles->getExemplar($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get titles. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $where1 = "WHERE  idorigin = $idorigin AND `default` = 'Y'";

        $check1 =  $this->dbOrigin->getOrigin($where1);        
        if (!$check1['success']) {
            if($this->log)
                $this->logIt("Can't get Origin. {$check1['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true,'message'=>$this->getLanguageWord('Alert_delete_field')));

        }elseif ($check1['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true,'message'=>$this->getLanguageWord('Alert_delete_field_pettern')));

        }else {

            echo json_encode(array('success'=>false,'message'));

        }
    }


}