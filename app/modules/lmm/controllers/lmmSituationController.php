<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmSituation extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmSituation');
        
        $this->loadModel('situation_model');
        $this->dbSituation = new situation_model();

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

        $smarty->display('lmm-situation-grid.tpl');
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

        $rsCount = $this->dbSituation->getSituation($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Situation. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
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

        $rsBrand =  $this->dbSituation->getSituation($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $padrao_fmt = ($rsBrand['data']->fields['default'] == 'Y' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsBrand['data']->fields['idsituation'],
                'situation'    => $rsBrand['data']->fields['name'],                
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

        $this->makeScreenSituation($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmsituation-create.tpl');
    }

    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbSituation->getSituation("WHERE idsituation = $lmmID");

        $this->makeScreenSituation($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idsituation', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmsituation-update.tpl');

    }


    public function viewSituation()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbSituation->getSituation("WHERE idsituation = $lmmID");

        $this->makeScreenSituation($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmsituation-view.tpl');

    }

    function makeScreenSituation($objSmarty,$rs,$oper)
    {
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_situation','Informe o nome.');
            else
                $objSmarty->assign('situation',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_situation','Informe o Nome.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('situation',$rs->fields['name']);
        }

        // --- Name ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == "Y" ? "checked=checked" : "");                
        }

    }


    function createSituation()
    {
         $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $situation= trim($_POST['situation']);
        $status = isset($_POST['citystatus']) ? 0 : 1;
        $default = isset($_POST['cityDefault']) ? 1 : 0;


        $this->dbSituation->BeginTrans();

        if($default==1){
            $rem = $this->dbSituation->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update Situation Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbSituation->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbSituation->insertSituation($situation,$status,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbSituation->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbSituation->CommitTrans();

        echo json_encode($aRet);

    }

    function updateSituation()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $lmmID = $_POST['idsituation'];
        $situation= trim($_POST['situation']);
        $default = isset($_POST['cityDefault']) ? 1 : 0;     

        $this->dbSituation->BeginTrans();

        if($default==1){
            $rem = $this->dbSituation->removerPadrao();

            if (!$rem['success']) {
                if($this->log)
                    $this->logIt("Can't update Situation Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbSituation->RollbackTrans();
                return false;
            }

        }

        $ret = $this->dbSituation->updateSituation($lmmID,$situation,$default);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbSituation->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbSituation->CommitTrans();

        echo json_encode($aRet);
 
    }


    function deleteSituation()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idsituation_modal'];

        $this->dbSituation->BeginTrans();

        $ret = $this->dbSituation->deleteSituation($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbSituation->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbSituation->CommitTrans();

        echo json_encode($aRet);

    }


    function padraoSituation()
    {
        $lmmID = $_POST['situationID'];

        $rem = $this->dbSituation->removerPadrao();

        if (!$rem['success']) {
            if($this->log)
                $this->logIt("Can't update Situation Padrao. {$rem['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }


        $ret = $this->dbSituation->changePadrao($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Situation Padrao. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }


    function existSituation(){

        $situation = $_POST['situation'];

        $where = "WHERE  name = '$situation'";

        $check =  $this->dbSituation->getSituation($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Situation. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function existSituationUp()
    {
        
        $situation =trim($_POST['situation']);
        $where = "WHERE  name = '$situation'";
        $where .= (isset($_POST['idsituation'])) ? "AND idsituation!= {$_POST['idsituation']}" : "";        

        $check =  $this->dbSituation->getSituation($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Situation. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }
    

    function statusSituation()
    {
        $lmmID = $_POST['situationID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbSituation->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Situation status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    /*function checksSituation(){

        $idsituation = $_POST['situationID'];

        $where = "AND  idsituation = $idsituation ";      

        $where = "WHERE  idsituation = $idsituation AND `default` = 'Y'";

        $check =  $this->dbSituation->getSituation($where);        
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get Situation. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true,'message'=>$this->getLanguageWord('Alert_delete_field_pettern')));

        }else {

            echo json_encode(array('success'=>false,'message'));

        }

    }*/
    

}