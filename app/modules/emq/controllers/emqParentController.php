<?php
require_once(HELPDEZK_PATH . '/app/modules/emq/controllers/emqCommonController.php');

class emqParent  extends emqCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->modulename = 'intranet' ;
        $this->program  = basename( __FILE__ );

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('acd/acdstudent_model');
        $dbStudent = new acdstudent_model();
        $this->dbStudent = $dbStudent;

        $this->loadModel('parent_model');
        $dbParent = new parent_model();
        $this->dbParent = $dbParent;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('emq-parent.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='idparent';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            switch ($_POST['searchField']){
                case 'sender':
                    $searchField = "`from`";
                    break;
                case 'toname':
                    $searchField = "`to`";
                    break;
                case 'ts':
                    $searchField = "FROM_UNIXTIME(ts,'%Y-%m-%d')";
                    $_POST['searchString'] = str_replace("'", "", $this->formatSaveDate($_POST['searchString']));
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }

            $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbParent->countParents($where);

        if( $count->fields['total'] > 0 && $rows > 0) {
            $total_pages = ceil($count->fields['total']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsParents = $this->dbParent->getParents($where,$order,$limit);

        while (!$rsParents->EOF) {

            //$status_fmt = ($rsEmails->fields['sent_state'] == 'S') ? '<span class="label label-info">S</span>' : '<span class="label label-danger">N</span>';

            $aColumns[] = array(
                'idparent'   => $rsParents->fields['idparent'],
                'name'   => $rsParents->fields['name'],
                'email'    => $rsParents->fields['email']
            );
            $rsParents->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);
    }

    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        $this->makeScreenParent($smarty,'','','create');

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('emq-parent-create.tpl');
    }

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        $idparent = $this->getParam('idparent');

        $rs = $this->dbStudent->getParentData("AND a.idparent = $idparent");
        $rsStudent = $this->dbParent->getBindConfigs($idparent);

        $this->makeScreenParent($smarty,$rs,$rsStudent,'update');

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('emq-parent-update.tpl');
    }

    function makeScreenParent($objSmarty,$rs,$rsStudent,$typeAction)
    {
        $objSmarty->assign('idparent',  $rs->fields['idparent']);
        $objSmarty->assign('idperson_profile',  $rs->fields['idperson_profile']);

        $objSmarty->assign('parentName',  $rs->fields['name']);
        $objSmarty->assign('parentCpf',  $rs->fields['cpf']);
        $objSmarty->assign('parentEmail',  $rs->fields['email']);


        // --- Gender ---
        $arrGender = $this->_comboGender();
        if ($typeAction == 'update') {
            $idGenderEnable = $rs->fields['idgender'];
        } elseif ($typeAction == 'create') {
            $idGenderEnable = $arrGender['ids'][0];
        }

        $objSmarty->assign('genderids',  $arrGender['ids']);
        $objSmarty->assign('gendervals', $arrGender['values']);
        $objSmarty->assign('idgender', $idGenderEnable);

        // --- Bind List ---
        if ($typeAction == 'update') {
            while (!$rsStudent->EOF) {
                $emailSms = $rsStudent->fields['email_sms'] == 'Y' ? 'checked=checked': '';
                $bankTicket = $rsStudent->fields['bank_ticket'] == 'Y' ? 'checked=checked': '';
                $accessApp = $rsStudent->fields['access_app'] == 'Y' ? 'checked=checked': '';

                $arrStudents[] = array(
                    'idstudent'     => $rsStudent->fields['idstudent'],
                    'idkinship'     => $rsStudent->fields['idkinship'],
                    'email_sms'     => $emailSms,
                    'bank_ticket'   => $bankTicket,
                    'access_app'    => $accessApp

                );
                $rsStudent->MoveNext();
            }
            $objSmarty->assign('arrStudents', $arrStudents);
        }


        // --- Student ---
        $arrStudent = $this->_comboStudent();
       if ($typeAction == 'create') {
            $idStudentEnable = 0;
        }
        $objSmarty->assign('studentids',  $arrStudent['ids']);
        $objSmarty->assign('studentvals', $arrStudent['values']);
        $objSmarty->assign('idproduto', $idStudentEnable );

        // --- Kinship ---
        $arrKinship = $this->_comboKinship();
        if ($typeAction == 'create') {
            $idKinshipEnable = 0;
        }
        $objSmarty->assign('kinshipids',  $arrKinship['ids']);
        $objSmarty->assign('kinshipvals', $arrKinship['values']);
        $objSmarty->assign('idkinship', $idKinshipEnable );

    }

    public function createParent()
    {
        //echo "<pre>"; print_r($_POST);    echo "</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrSearch = array(".","_","-");
        $arrReplace = array("","","");

        $parentName = addslashes($_POST['parentName']);
        $parentCpf = addslashes(str_replace($arrSearch,$arrReplace,$_POST['parentCpf']));
        $parentEmail = $_POST['parentEmail'];
        $parentGender = $_POST['parentGender'];
        $idstudent = $_POST['idstudent'];
        $idkinship = $_POST['idkinship'];
        $checkEmailSms = $_POST['checkEmailSms'];
        $checkBankTicket = $_POST['checkBankTicket'];
        $checkAccessApp = $_POST['checkAccessApp'];

        $this->dbParent->BeginTrans();

        $ret = $this->dbParent->insertParent($parentName,$parentGender,$parentCpf,$parentEmail);

        if(!$ret){
            $this->dbParent->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Parent - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retProfile = $this->dbParent->insertParentProfile($ret);
        if(!$retProfile){
            $this->dbParent->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Parent Profile - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach ($idstudent as $k=>$v){
            if($v != 0){
                $emailSms = isset($checkEmailSms[$k]) ? 'Y' : 'N';
                $bankTicket = isset($checkBankTicket[$k]) ? 'Y' : 'N';
                $accessApp = isset($checkAccessApp[$k]) ? 'Y' : 'N';

                $insBindConfigs = $this->dbParent->insertBindConfigs($v,$retProfile,$idkinship[$k],$emailSms,$bankTicket,$accessApp);
                if(!$insBindConfigs){
                    $this->dbParent->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert Parent Configs - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }

        }

        $this->dbParent->CommitTrans();

        $aRet = array(
            "idparent" => $retProfile,
            "status" => 'ok'
        );

        echo json_encode($aRet);

    }

    public function updateParent()
    {
        //echo "<pre>"; print_r($_POST);    echo "</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrSearch = array(".","_","-");
        $arrReplace = array("","","");

        $idparent = $_POST['idparent'];
        $idperson_profile = $_POST['idperson_profile'];
        $parentName = addslashes($_POST['parentName']);
        $parentCpf = addslashes(str_replace($arrSearch,$arrReplace,$_POST['parentCpf']));
        $parentEmail = $_POST['parentEmail'];
        $parentGender = $_POST['parentGender'];
        $idstudent = $_POST['idstudent'];
        $idkinship = $_POST['idkinship'];
        $checkEmailSms = $_POST['checkEmailSms'];
        $checkBankTicket = $_POST['checkBankTicket'];
        $checkAccessApp = $_POST['checkAccessApp'];

        $this->dbParent->BeginTrans();

        $ret = $this->dbParent->updateParent($idperson_profile,$parentName,$parentGender,$parentCpf,$parentEmail);

        if(!$ret){
            $this->dbParent->RollbackTrans();
            if($this->log)
                $this->logIt('Update Parent - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delBind = $this->dbParent->deleteBindConfigs($idparent);
        if(!$delBind){
            $this->dbParent->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Parent Configs - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach ($idstudent as $k=>$v){
            if($v != 0){
                $emailSms = isset($checkEmailSms[$k]) ? 'Y' : 'N';
                $bankTicket = isset($checkBankTicket[$k]) ? 'Y' : 'N';
                $accessApp = isset($checkAccessApp[$k]) ? 'Y' : 'N';

                $insBindConfigs = $this->dbParent->insertBindConfigs($v,$idparent,$idkinship[$k],$emailSms,$bankTicket,$accessApp);
                if(!$insBindConfigs){
                    $this->dbParent->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert Parent Configs - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }

        }

        $this->dbParent->CommitTrans();

        $aRet = array(
            "idparent" => $idparent,
            "status" => 'ok'
        );

        echo json_encode($aRet);

    }

    function ajaxStudent()
    {
        echo $this->comboStudentHtml();
    }

    function ajaxKinship()
    {
        echo $this->comboKinshipHtml();
    }

    public function comboStudentHtml()
    {
        $arrType = $this->_comboStudent();
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            $select .= "<option value='$indexValue'>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function comboKinshipHtml()
    {
        $arrType = $this->_comboKinship();
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            $select .= "<option value='$indexValue'>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function checkEmail() {
        $arrSearch = array(".","_","-");
        $arrReplace = array("","","");

        $txtEmail = str_replace($arrSearch,$arrReplace,addslashes($_POST['txtEmail']));

        $where = "WHERE REPLACE(REPLACE(REPLACE(email,'.',''),'_',''),'-','') LIKE '$txtEmail'";

        $ret = $this->dbEmails->getEmailParent($where);
        if (!$ret) {
            if($this->log)
                $this->logIt('E-mail address verification  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->RecordCount() > 0){echo json_encode($this->getLanguageWord('emq_exists_email_address'));}
        else{echo json_encode(true);}

    }

    function createEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $enrollmentID = $_POST['enrollmentId'];
        $txtEmail = addslashes($_POST['txtEmail']);
        $txtEmail = $txtEmail;

        $this->dbEmails->BeginTrans();

        $ins = $this->dbEmails->insertEmailParent($enrollmentID,$txtEmail);

        if (!$ins) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Parent's E-mail Address  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbEmails->CommitTrans();

        $aRet = array(
            "idhost" => $ins,
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }



}