<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmLibrary extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmLibrary ');
        
        $this->loadModel('library_model');
        $this->dbLibrary  = new library_model();

        $this->loadModel('titles_model');
        $this->dbTitles  = new titles_model();

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

        $smarty->display('lmm-library-grid.tpl');
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

        $rsCount = $this->dbLibrary ->getLibrary($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Library . {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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

        $rsBrand =  $this->dbLibrary ->getLibrary($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $padrao_fmt = ($rsBrand['data']->fields['default'] == 'Y' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsBrand['data']->fields['idlibrary'],
                'library'       => $rsBrand['data']->fields['name'],                
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

        $this->makeScreenLibrary($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmlibrary-create.tpl');
    }

    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbLibrary->getLibrary("WHERE idlibrary = $lmmID");

        $this->makeScreenLibrary($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idlibrary', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmlibrary-update.tpl');

    }


    public function viewLibrary()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbLibrary->getLibrary("WHERE idlibrary = $lmmID");

        $this->makeScreenLibrary($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmlibrary-view.tpl');

    }


    function makeScreenLibrary($objSmarty,$rs,$oper)
    {
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_library','Informe o nome.');
            else
                $objSmarty->assign('library',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_library','Informe o Nome.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('library',$rs->fields['name']);
        }

        // --- Name ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == "Y" ? "checked=checked" : "");                
        }

    }


    function createLibrary()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $library= trim($_POST['library']);
        $status = isset($_POST['citystatus']) ? 0 : 1;
        $default = isset($_POST['cityDefault']) ? 1 : 0;


        $this->dbLibrary->BeginTrans();

        if($default==1){
            $rem = $this->dbLibrary->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update Library Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbLibrary->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbLibrary->insertLibrary($library,$status,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbLibrary->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbLibrary->CommitTrans();

        echo json_encode($aRet);

    }

    function updateLibrary()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $msg ="";
        $lmmID = $_POST['idlibrary'];
        $library= trim($_POST['library']);
        $default = isset($_POST['cityDefault']) ? 1 : 0;     

        $this->dbLibrary->BeginTrans();

        if($default==1){
            $rem = $this->dbLibrary->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update Library Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbLibrary->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbLibrary->updateLibrary($lmmID,$library,$default);

        if (!$ret['success']){
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbLibrary->RollbackTrans();
            return false;
        }

        $library=$this->_comboLibrary();        
        if(sizeof($library['default'])<=0){
            $retlibrary = $this->dbLibrary->changePadrao($library['ids'][0]);           
            if (!$retlibrary['success']){
                if($this->log)
                    $this->logIt("Can't update library Padrao. {$retlibrary['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }                  
           $msg=$library['ids'][0]==$lmmID?$this->getLanguageWord('Edit_the_field_Pettern'):"";                
     
        }  

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID,
            "message"  =>$msg
        );

        $this->dbLibrary->CommitTrans();

        echo json_encode($aRet);

    }

    function deleteLibrary()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idlibrary_modal'];

        $this->dbLibrary->BeginTrans();

        $ret = $this->dbLibrary->deleteLibrary($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbLibrary->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbLibrary->CommitTrans();

        echo json_encode($aRet);

    }

    function padraoLibrary()
    {
        $lmmID = $_POST['libraryID'];

        $rem = $this->dbLibrary->removerPadrao();

        if (!$rem['success']) {
            if($this->log)
                $this->logIt("Can't update Library Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }


        $ret = $this->dbLibrary->changePadrao($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Library Padrao. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function existLibrary(){

        $library = $_POST['library'];

        $where = "WHERE  name = '$library'";

        $check =  $this->dbLibrary->getLibrary($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Library. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function existLibraryUp()
    {
        
        $library =trim($_POST['library']);
        $where = "WHERE  name = '$library'";
        $where .= (isset($_POST['idlibrary'])) ? "AND idlibrary!= {$_POST['idlibrary']}" : "";
       
        $check =  $this->dbLibrary->getLibrary($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Library. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function statusLibrary()
    {
        $lmmID = $_POST['libraryID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbLibrary->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Library status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function checksLibrary(){

        $idlibrary = $_POST['libraryID'];

        $where = "AND  a.idlibrary = $idlibrary ";

        $check =  $this->dbTitles->getExemplar($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get titles. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $where1 = "WHERE  idlibrary = $idlibrary AND `default` = 'Y'";

        $check1 =  $this->dbLibrary->getLibrary($where1);        
        if (!$check1['success']) {
            if($this->log)
                $this->logIt("Can't get library. {$check1['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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