<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkRequestEmail extends hdkCommon
{
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('requestemail_model');
        $dbRequestEmail = new requestemail_model();
        $this->dbRequestEmail = $dbRequestEmail;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $smarty->assign('token', $this->_makeToken()) ;

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('request-email.tpl');

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
            $sidx ='idgetemail';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);

        }

        $rsCount = $this->dbRequestEmail->getRequestEmail($where);
        $count = $rsCount->RecordCount();

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

        $rsReqEmails = $this->dbRequestEmail->getRequestEmail($where,$order,$limit);
        
        while (!$rsReqEmails->EOF) {
            
            $aColumns[] = array(
                'id'=> $rsReqEmails->fields['idgetemail'],
                'serverurl'=> $rsReqEmails->fields['serverurl'],
                'servertype'=> $rsReqEmails->fields['servertype'],
                'user' => $rsReqEmails->fields['user']
            );
            $rsReqEmails->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateRequestEmail()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenReqEmail($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('request-email-create.tpl');
    }

    public function formUpdateRequestEmail()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idgetemail = $this->getParam('idgetemail');
        $where = "WHERE a.idgetemail = $idgetemail";
        
        $rsGetEmail = $this->dbRequestEmail->requestEmailData($where);

        $this->makeScreenReqEmail($smarty,$rsGetEmail,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idgetemail', $idgetemail);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('request-email-update.tpl');

    }

    function makeScreenReqEmail($objSmarty,$rs,$oper)
    {
        $objSmarty->assign('txtServer',  $rs->fields['serverurl']);
        $objSmarty->assign('txtPort',  $rs->fields['serverport']);
        $objSmarty->assign('txtEmail',  $rs->fields['user']);
        $objSmarty->assign('txtPassword',  $rs->fields['password']);
        $objSmarty->assign('txtFilterSender',  $rs->fields['filter_from']);
        $objSmarty->assign('txtFilterSubject',  $rs->fields['filter_subject']);
        $objSmarty->assign('checkedCreateUser',  $rs->fields['ind_create_user'] == 1 ? "checked=checked" : "");
        $objSmarty->assign('checkedDeleteEmails',  $rs->fields['ind_delete_server'] == 1 ? "checked=checked" : "");
        $objSmarty->assign('checkedNote',  $rs->fields['email_response_as_note'] == 1 ? "checked=checked" : "");
        $objSmarty->assign('flghide',  $rs->fields['ind_create_user'] == 1 ? "" : "hide");
        
        // --- Server Type ---        
        if ($oper == 'update') {
            $idServerDefault = $rs->fields['servertype'];
        } elseif ($oper == 'create') {
            $idServerDefault = "";            
        } 
        $arrSrvType = $this->_comboServerType();        
        $objSmarty->assign('srvtypeids',  $arrSrvType['ids']);
        $objSmarty->assign('srvtypevals', $arrSrvType['values']);
        $objSmarty->assign('idsrvtype', $idServerDefault);
        
        // --- Area ---        
        if ($oper == 'update') {
            $idAreaDefault = $rs->fields['idarea'];
        } elseif ($oper == 'create') {
            $idAreaDefault = $this->_getIdCoreDefault('area');            
        } 
        $arrArea = $this->_comboArea();        
        $objSmarty->assign('areaids',  $arrArea['ids']);
        $objSmarty->assign('areavals', $arrArea['values']);
        $objSmarty->assign('idarea', $idAreaDefault);
        
        // --- Type ---
        $arrType = $this->_comboType($idAreaDefault);
        if ($oper == 'update') {
            $idtype = $rs->fields['idtype'];
        } elseif ($oper == 'create') {
            $idtype = $arrType['ids'][0];            
        }        
        $objSmarty->assign('typeids',  $arrType['ids']);
        $objSmarty->assign('typevals', $arrType['values']);

        // --- Item ---
        $arrItem = $this->_comboItem($idtype);
        if ($oper == 'update') {
            $iditem = $rs->fields['iditem'];
        } elseif ($oper == 'create') {
            $iditem = $arrItem['ids'][0];            
        }
        $objSmarty->assign('itemids',  $arrItem['ids']);
        $objSmarty->assign('itemvals', $arrItem['values']);

        // --- Service ---
        $arrService = $this->_comboService($iditem);
        if ($oper == 'update') {
            $idservice = $rs->fields['idservice'];
        } elseif ($oper == 'create') {
            $idservice = $arrService['ids'][0];            
        }
        $objSmarty->assign('serviceids',  $arrService['ids']);
        $objSmarty->assign('servicevals', $arrService['values']);

        // --- Login layout ---        
        if ($oper == 'update') {
            $idLayoutDefault = $rs->fields['login_layout'];
        } elseif ($oper == 'create') {
            $idLayoutDefault = "";            
        } 
        $arrLoginLayout = $this->_comboLoginLayout();        
        $objSmarty->assign('loginlayoutids',  $arrLoginLayout['ids']);
        $objSmarty->assign('loginlayoutvals', $arrLoginLayout['values']);
        $objSmarty->assign('idloginlayout', $idLayoutDefault);

        // --- Company ---        
        if ($oper == 'update') {
            $idCompanyDefault = $rs->fields['idperson'];
        } elseif ($oper == 'create') {
            $idCompanyDefault = "";            
        } 
        $arrCompany = $this->_comboCompanies();        
        $objSmarty->assign('companyids',  $arrCompany['ids']);
        $objSmarty->assign('companyvals', $arrCompany['values']);
        $objSmarty->assign('idcompany', $idCompanyDefault);

        // --- Department ---        
        if ($oper == 'update' && $rs->fields['iddepartment']) {
            $idDepartmentDefault = $rs->fields['iddepartment'];
            
            $arrDepartment = $this->_comboDepartment($idCompanyDefault);        
            $objSmarty->assign('departmentids',  $arrDepartment['ids']);
            $objSmarty->assign('departmentvals', $arrDepartment['values']);
            $objSmarty->assign('iddepartment', $idDepartmentDefault);
        } 
        
        

    }

    function createRequestEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        
        
        $server = addslashes($_POST['txtServer']);
        $servertype = $_POST['cmbSrvType'];
        $port = addslashes($_POST['txtPort']);
        $email = addslashes($_POST['txtEmail']);
        $password = addslashes($_POST['txtPassword']);
        $idservice = $_POST['cmbService'];
        $sender = addslashes($_POST['txtFilterSender']);
        $subject = addslashes($_POST['txtFilterSubject']);
        $createuser = isset($_POST['checkCreateUser']) ? 1 : NULL;
        $iddepartment = $_POST['cmbDepartment'];
        $deleteemail = isset($_POST['checkDeleteEmails']) ? 1 : NULL;
        $idloginlayout = $_POST['cmbLoginLayout'];
        $emailnote = isset($_POST['checkNote']) ? 1 : NULL;

        $this->dbRequestEmail->BeginTrans();
        
        $ret = $this->dbRequestEmail->insertRequestEmail($server,$servertype,$port,$email,$password,$idservice,$sender,$subject,$createuser,$deleteemail,$idloginlayout,$emailnote);

        if(!$ret){
            $this->dbRequestEmail->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Request E-mail - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($iddepartment && $iddepartment != ''){
            $idgetemail = $this->dbRequestEmail->TableMaxID('hdk_tbgetemail','idgetemail');
            $insDepartment = $this->dbRequestEmail->insertRequestEmailDepartment($idgetemail, $iddepartment);

            if(!$insDepartment){
                $this->dbRequestEmail->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Request E-mail Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

        }

        $this->dbRequestEmail->CommitTrans();
        
        $aRet = array(
            "status" => "Ok"
        );

        echo json_encode($aRet);

    }

    function updateRequestEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        
        
        $idgetemail = $_POST['idgetemail'];
        $server = addslashes($_POST['txtServer']);
        $servertype = $_POST['cmbSrvType'];
        $port = addslashes($_POST['txtPort']);
        $email = addslashes($_POST['txtEmail']);
        $password = addslashes($_POST['txtPassword']);
        $idservice = $_POST['cmbService'];
        $sender = addslashes($_POST['txtFilterSender']);
        $subject = addslashes($_POST['txtFilterSubject']);
        $createuser = isset($_POST['checkCreateUser']) ? 1 : NULL;
        $iddepartment = $_POST['cmbDepartment'];
        $deleteemail = isset($_POST['checkDeleteEmails']) ? 1 : NULL;
        $idloginlayout = $_POST['cmbLoginLayout'];
        $emailnote = isset($_POST['checkNote']) ? 1 : NULL;

        $this->dbRequestEmail->BeginTrans();
        $ret = $this->dbRequestEmail->updateRequestEmail($idgetemail,$server,$servertype,$port,$email,$password,$idservice,$sender,$subject,$createuser,$deleteemail,$idloginlayout,$emailnote);

        if(!$ret){
            $this->dbRequestEmail->RollbackTrans();
            if($this->log)
                $this->logIt('Update Request E-mail - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $rmDep = $this->dbRequestEmail->deleteRequestEmailDepartment($idgetemail);
		if(!$rmDep){
			$this->dbRequestEmail->RollbackTrans();
            if($this->log)
                $this->logIt('Remove Request E-mail Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
		}

        if($createuser == 1 && ($iddepartment && $iddepartment != '')){
            $updDepartment = $this->dbRequestEmail->insertRequestEmailDepartment($idgetemail,$iddepartment);

            if(!$updDepartment){
                $this->dbRequestEmail->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Request E-mail Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

        }

        $this->dbRequestEmail->CommitTrans();
        
        $aRet = array(
            "status" => "Ok"
        );

        echo json_encode($aRet);

    }

    function deleteRequestEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idgetemail = $_POST['idgetemail'];

        $this->dbRequestEmail->BeginTrans();

        $rmDep = $this->dbRequestEmail->deleteRequestEmailDepartment($idgetemail);
		if(!$rmDep){
            $this->dbRequestEmail->RollbackTrans();
            if($this->log)
                $this->logIt('Remove Request E-mail Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
		}
		
        $dea = $this->dbRequestEmail->requestEmailDelete($idgetemail);
        if (!$dea) {
            $this->dbRequestEmail->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Request E-mail - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
        }

        $this->dbRequestEmail->CommitTrans();

        $aRet = array(
            "idgetemail" => $idgetemail,
            "status"   => 'OK'
        );

        echo json_encode($aRet);

    }

    public function ajaxTypes()
    {
        echo $this->_comboTypeHtml($_POST['areaId']);
    }

    public function ajaxItens()
    {
        echo $this->_comboItemHtml($_POST['typeId']);
    }

    public function ajaxServices()
    {
        echo $this->_comboServiceHtml($_POST['itemId']);
    }

    public function ajaxDepartments()
    {
        echo $this->_comboDepartmentHtml($_POST['companyId']);
    }

}