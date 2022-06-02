<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmClassification extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmClassification');
        
        $this->loadModel('classification_model');
        $this->dbClassification = new classification_model();

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

        $smarty->display('lmm-classification-grid.tpl');
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

        $rsCount = $this->dbClassification->getClassification($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Classification. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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

        $rsBrand =  $this->dbClassification->getClassification($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $padrao_fmt = ($rsBrand['data']->fields['default'] == 'Y' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'                => $rsBrand['data']->fields['idclassification'],
                'classification'    => $rsBrand['data']->fields['name'],                
                'status_fmt'        => $status_fmt,
                'status'            => $rsBrand['data']->fields['status'],
                'padrao_fmt'        => $padrao_fmt,
                'padrao'            => $rsBrand['data']->fields['default']

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

        $this->makeScreenClassification($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmclassification-create.tpl');
    }


    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbClassification->getClassification("WHERE idclassification = $lmmID");

        $this->makeScreenClassification($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idclassification', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmclassification-update.tpl');

    }


    public function viewClassification()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbClassification->getClassification("WHERE idclassification = $lmmID");

        $this->makeScreenClassification($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmclassification-view.tpl');

    }


    function makeScreenClassification($objSmarty,$rs,$oper)
    {
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_classification','Informe o nome.');
            else
                $objSmarty->assign('classification',$rs->fields['name']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_classification','Informe o Nome.');
        }elseif ($oper == 'echo') {
            $objSmarty->assign('classification',$rs->fields['name']);
        }

        // --- Name ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == "Y" ? "checked=checked" : "");                
        }

    }


    function createClassification()
    {
         $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $classification= trim($_POST['classification']);
        $status = isset($_POST['citystatus']) ? 0 : 1;
        $default = isset($_POST['cityDefault']) ? 1 : 0;


        $this->dbClassification->BeginTrans();

        if($default==1){
            $rem = $this->dbClassification->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update classification Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbClassification->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbClassification->insertClassification($classification,$status,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbClassification->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
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
        
        $msg ="";
        $lmmID = $_POST['idclassification'];
        $classification= trim($_POST['classification']);
        $default = isset($_POST['cityDefault']) ? 1 : 0;     

        $this->dbClassification->BeginTrans();

        if($default==1){
            $rem = $this->dbClassification->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update classification Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbClassification->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbClassification->updateClassification($lmmID,$classification,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbClassification->RollbackTrans();
            return false;
        }

        $classification=$this->_comboClassification();        
        if(sizeof($classification['default'])<=0){
            $retclassification = $this->dbClassification->changePadrao($classification['ids'][0]);           
            if (!$retclassification['success']) {
                if($this->log)
                    $this->logIt("Can't update classification Padrao. {$retclassification['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }           
           $msg=$classification['ids'][0]==$lmmID?$this->getLanguageWord('Edit_the_field_Pettern'):"";
     
        }        
    

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID,
            "message"  =>$msg
        );

        $this->dbClassification->CommitTrans();

        echo json_encode($aRet);

    }


    function deleteClassification()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idclassification_modal'];

        $this->dbClassification->BeginTrans();

        $ret = $this->dbClassification->deleteClassification($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbClassification->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbClassification->CommitTrans();

        echo json_encode($aRet);

    }


    function padraoClassification()
    {
        $lmmID = $_POST['classificationID'];

        $rem = $this->dbClassification->removerPadrao();

        if (!$rem['success']) {
            if($this->log)
                $this->logIt("Can't update classification Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }


        $ret = $this->dbClassification->changePadrao($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Classification Padrao. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }


    function existClassification(){

        $classification = $_POST['classification'];

        $where = "WHERE  name = '$classification'";

        $check =  $this->dbClassification->getClassification($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Classification. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        }else {

            echo json_encode(true);

        }

    }

    function existClassificationUp(){

        $classification =trim($_POST['classification']);
        $where = "WHERE  name = '$classification'";
        $where .= (isset($_POST['idclassification'])) ? "AND idclassification!= {$_POST['idclassification']}" : "";

        $check =  $this->dbClassification->getClassification($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Classification. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        }else {

            echo json_encode(true);

        }

    }


    function statusClassification()
    {
        $lmmID = $_POST['classificationID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbClassification->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Classification status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function checksClassification(){

        $lmmID = $_POST['classificationID'];

        $where = "WHERE  a.idclassification = $lmmID ";

        $check =  $this->dbTitles->getTitles($where);        
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get titles. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $where1 = "WHERE  idclassification = $lmmID AND `default` = 'Y'";

        $check1 =  $this->dbClassification->getClassification($where1);        
        if (!$check1['success']) {
            if($this->log)
                $this->logIt("Can't get classification. {$check1['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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