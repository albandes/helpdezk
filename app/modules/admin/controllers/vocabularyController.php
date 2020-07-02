<?php
require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class Vocabulary  extends admCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->idprogram =  $this->getIdProgramByController('vocabulary');

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('vocabulary_model');
        $dbVocabulary = new vocabulary_model();
        $this->dbVocabulary = $dbVocabulary;

    }

    public function index()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $smarty->assign('token', $token) ;
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('vocabulary.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/admin/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='key_name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){

            switch ($_POST['searchField']){
                case 'locale_name':
                    $searchField = "b.name";
                    break;
                case 'module_name':
                    $searchField = "c.name";
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;

            }


            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }
        
        $count = $this->dbVocabulary->countVocabulary($where);
        if(is_array($count) && isset($count['status'])){
            if($this->log)
                $this->logIt($count['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
        }

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
        //

        $rsVocabulary = $this->dbVocabulary->getVocabulary($where,null,$order,$limit);
        if(is_array($rsVocabulary) && isset($rsVocabulary['status'])){
            if($this->log)
                $this->logIt($rsVocabulary['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
        }

        while (!$rsVocabulary->EOF) {

            $status_fmt = ($rsVocabulary->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $keyName = strip_tags($rsVocabulary->fields['key_name']);
            $keyValue = strip_tags($rsVocabulary->fields['key_value']);

            $aColumns[] = array(
                'id'        => $rsVocabulary->fields['idvocabulary'],
                'locale'    => $rsVocabulary->fields['locale_name'],
                'module'    => $rsVocabulary->fields['module_name'],
                'key_name'  => $keyName,
                'key_value' => utf8_encode($keyValue),
                'status'    => $status_fmt,
                'statusval' => $rsVocabulary->fields['status']
            );

            $rsVocabulary->MoveNext();

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
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $this->makeScreen($smarty,'','create');

        $smarty->assign('token', $token) ;
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('vocabulary-create.tpl');
    }

    public function formUpdate()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $vocabularyID = $this->getParam('idvocabulary');
        $rs = $this->dbVocabulary->getVocabulary("AND idvocabulary = {$vocabularyID}");
        if(is_array($rs) && isset($rs['status'])){
            $this->dbFingerPrints->RollbackTrans();
            if($this->log)
                $this->logIt('Can\'t get Vocabulary data. Reason: '.$rs['message'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->makeScreen($smarty,$rs,'update');

        $smarty->assign('token', $token) ;
        $smarty->assign('hidden_vocabulary', $vocabularyID) ;
        $smarty->assign('demoversion', $this->demoVersion); // Demo version

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('vocabulary-update.tpl');
    }
    
    function makeScreen($objSmarty,$rs,$oper)
    {
        // --- Locale ---
        $arrLocale = $this->comboLocale(null,null, "ORDER BY `name`");

        if ($oper == 'update') {
            $idLocaleEnable = $rs->fields['idlocale'];
        } elseif ($oper == 'create') {
            $retLocaleID = $this->dbVocabulary->getLocale("WHERE `name` = '{$this->getConfig('lang')}'");
            $idLocaleEnable = ($retLocaleID->fields['idlocale'] && $retLocaleID->fields['idlocale'] != '') ? $retLocaleID->fields['idlocale'] : '';
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblLocale',$rs->fields['locale_name']);
        } else {
            $objSmarty->assign('localeids',  $arrLocale['ids']);
            $objSmarty->assign('localevals', $arrLocale['values']);
            $objSmarty->assign('idlocale', $idLocaleEnable);
        }

        // --- Module ---
        $arrModule = $this->_comboModule(null,"ORDER BY `name`");

        if ($oper == 'update' || $oper == 'echo') {
            $idModuleEnable = $rs->fields['idmodule'];
        } elseif ($oper == 'create') {
            $idModuleEnable = '';
        }

        $objSmarty->assign('moduleids',  $arrModule['ids']);
        $objSmarty->assign('modulevals',$arrModule['values']);
        $objSmarty->assign('idmodule', $idModuleEnable );

        //  --- Key Fields ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('keyName',  $rs->fields['key_name']);
            $objSmarty->assign('keyValue', utf8_encode($rs->fields['key_value']));
        }
    }

    function createVocabulary()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $localeID = $_POST['cmbLocale'];
        $moduleID = $_POST['cmbModule'];
        $keyName = $this->formatLoginToSave($_POST['keyName']);
        $keyValue = utf8_decode($this->formatLoginToSave($_POST['keyValue']));

        $this->dbVocabulary->BeginTrans();

        $ins = $this->dbVocabulary->insertVocabulary($localeID,$moduleID,$keyName,$keyValue);
        if(is_array($ins) && isset($ins['status'])){
            $this->dbVocabulary->RollbackTrans();
            if($this->log)
                $this->logIt('Can\'t insert Vocabulary. Reason: '.$ins['message'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbVocabulary->CommitTrans();

        $aRet = array(
            "status" => true,
            "message" => ""
        );

        echo json_encode($aRet);
    }

    function updateVocabulary()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $vocabularyID = $_POST['idvocabulary'];
        $localeID = $_POST['cmbLocale'];
        $moduleID = $_POST['cmbModule'];
        $keyName = $this->formatLoginToSave($_POST['keyName']);
        $keyValue = utf8_decode($this->formatLoginToSave($_POST['keyValue']));

        $this->dbVocabulary->BeginTrans();

        $upd = $this->dbVocabulary->updateVocabulary($vocabularyID,$localeID,$moduleID,$keyName,$keyValue);
        if(is_array($upd) && isset($upd['status'])){
            $this->dbVocabulary->RollbackTrans();
            if($this->log)
                $this->logIt("Can't update Vocabulary. ID: {$vocabularyID}. Reason: ".$upd['message'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbVocabulary->CommitTrans();

        $aRet = array(
            "status" => true,
            "message" => ""
        );

        echo json_encode($aRet);
    }

    public function checkKeyName() {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $keyName = $_POST['keyName'];
        $where = isset($_POST['vocabularyID'])
                 ? "AND pipeLatinToUtf8(key_name) LIKE  '{$keyName}' AND a.idlocale = {$_POST['localeID']} AND idvocabulary != {$_POST['vocabularyID']}"
            : "AND pipeLatinToUtf8(key_name) LIKE  '{$keyName}' AND a.idlocale = {$_POST['localeID']}";

        $check = $this->dbVocabulary->getVocabulary($where);
        if ($check->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('vocabulary_key_exists'));
        } else {
            echo json_encode(true);
        }
    }

    function changeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $vocabularyID = $_POST['idvocabulary'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbVocabulary->changeStatus($vocabularyID,$newStatus);
        if(is_array($ret) && isset($ret['status'])){
            if($this->log)
                $this->logIt("Can't change Vocabulary's status. ID: {$vocabularyID}. Reason: ".$ret['message'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status" => true,
            "message" => ''
        );

        echo json_encode($aRet);

    }

    function formatLoginToSave($string)
    {
        return addslashes(trim(preg_replace('!\s+!', ' ', $string)));

    }

    public function comboLocale($where=null,$group=null,$order=null,$limit=null)
    {
        $rs = $this->dbVocabulary->getLocale($where,$group,$order,$limit);
        if(is_array($rs) && isset($rs['status'])){
            if($this->log)
                $this->logIt($rs['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idlocale'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function displayButtons($smarty,$permissions)
    {
        (isset($permissions[1]) && $permissions[1] == "Y") ? $smarty->assign('display_btn_add', '') : $smarty->assign('display_btn_add', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_edit', '') : $smarty->assign('display_btn_edit', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_enable', '') : $smarty->assign('display_btn_enable', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_disable', '') : $smarty->assign('display_btn_disable', 'hide');
        (isset($permissions[3]) && $permissions[3] == "Y") ? $smarty->assign('display_btn_delete', '') : $smarty->assign('display_btn_delete', 'hide');
        (isset($permissions[4]) && $permissions[4] == "Y") ? $smarty->assign('display_btn_export', '') : $smarty->assign('display_btn_export', 'hide');
        (isset($permissions[5]) && $permissions[5] == "Y") ? $smarty->assign('display_btn_email', '') : $smarty->assign('display_btn_email', 'hide');
        (isset($permissions[6]) && $permissions[6] == "Y") ? $smarty->assign('display_btn_sms', '') : $smarty->assign('display_btn_sms', 'hide');


    }

}