<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpEmailTemplate extends lgpCommon
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
        $this->idprogram =  $this->getIdProgramByController('lgpEmailTemplate');

        $this->loadModel('emailtemplate_model');
        $this->dbEmailTemplate = new emailtemplate_model();

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

        $smarty->display('lgp-emailTemplate-grid.tpl');

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
            

            $where .= ' WHERE ' . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }
        
        
        $rsCount = $this->dbEmailTemplate->getEmailTemplate($where);
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

        $rsEmailTemplate =  $this->dbEmailTemplate->getEmailTemplate($where,$order,$limit);
        if (!$rsEmailTemplate['success']) {
            if($this->log)
                $this->logIt("Error: {$rsEmailTemplate['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsEmailTemplate['data']->EOF) {
            $status_fmt = ($rsEmailTemplate['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsEmailTemplate['data']->fields['idtemplate'],
                'name'            => $rsEmailTemplate['data']->fields['name'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsEmailTemplate['data']->fields['status']            
            );
            $rsEmailTemplate['data']->MoveNext();
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

        $this->makeScreenEmailTemplate($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-emailTemplate-create.tpl');
    }


    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $emailTemplateID = $this->getParam('id');
        
        $rsEmailTemplate = $this->dbEmailTemplate->getEmailTemplate("WHERE idtemplate = $emailTemplateID") ;


        $this->makeScreenEmailTemplate($smarty,$rsEmailTemplate['data'],'update');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idtemplate', $emailTemplateID);
        


        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-emailTemplate-update.tpl');

    }

    public function viewEmailTemplate()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $emailTemplateID = $this->getParam('id');
        $rsEmailTemplate = $this->dbEmailTemplate->getEmailTemplate("WHERE idtemplate = $emailTemplateID") ;

        $this->makeScreenEmailTemplate($smarty,$rsEmailTemplate['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        //$smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idtemplate', $emailTemplateID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-emailTemplate-view.tpl');

    }

    function makeScreenEmailTemplate($objSmarty,$rs,$oper)
    {
        
        
        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('lgp_emailTemplate_name',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('emailTemplateName',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('lgp_emailTemplate_name',$this->getLanguageWord('Name'));
        } elseif ($oper == 'echo') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('lgp_emailTemplate_name',$this->getLanguageWord('Name'));
            else
                $objSmarty->assign('emailTemplateName',$rs->fields['name']);
        }

         // --- Descriotion ---
        if ($oper == 'update') {
            if (empty($rs->fields['description']))
                $objSmarty->assign('plh_description','Informe a descrição do aviso.');
            else
                $objSmarty->assign('description',$rs->fields['description']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_description','Informe a descrição do aviso.');
        }elseif($oper == 'echo'){            
                $objSmarty->assign('description',$rs->fields['description']);
        }

       

        // --- Default ---
        if ($oper == 'update')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? "" : "checked=checked"));
        elseif ($oper == 'echo')
            $objSmarty->assign('isdefault',($rs->fields['default'] == 0 ? $this->getLanguageWord('No') : $this->getLanguageWord('Yes')));

    }

    function createEmailTemplate()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $emailTemplateName = strip_tags($_POST['emailTemplateName']);
        $description = trim($_POST['description']);        

        $this->dbEmailTemplate->BeginTrans();

        $ret = $this->dbEmailTemplate->insertEmailTemplate($emailTemplateName,$description);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert EmailTemplate data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbEmailTemplate->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "emailTemplateID" => $ret['id'],
            //"description"   => "'".addslashes($_POST['description'])."'",
        );

        $this->dbEmailTemplate->CommitTrans();

        echo json_encode($aRet);

    }

    function updateEmailTemplate()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
               
        $emailTemplateID = $_POST['emailTemplateID'];
        $emailTemplateName = strip_tags($_POST['emailTemplateName']);
        $description = trim($_POST['description']);
        $this->dbEmailTemplate->BeginTrans();

        $ret = $this->dbEmailTemplate->updateEmailTemplate($emailTemplateID,$emailTemplateName, $description);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbEmailTemplate->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "id"    => $emailTemplateID
        );

        $this->dbEmailTemplate->CommitTrans();

        echo json_encode($aRet);

    }

    function statusEmailTemplate()
    {
        $emailTemplateID = $_POST['idtemplate'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbEmailTemplate->changeStatus($emailTemplateID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update emailTemplate status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "emailTemplateID" => $emailTemplateID
        );

        echo json_encode($aRet);

    }
    
    public function existEmailTemplate() {
        $this->protectFormInput();
        $search = $_POST['emailTemplateName'];

        $where = "WHERE `name` LIKE '{$search}'";
        $where .= isset($_POST['emailTemplateID']) ? " AND idtemplate != {$_POST['emailTemplateID']}" : "";

        $check = $this->dbEmailTemplate->getEmailTemplate($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('emailTemplate_exists'));
        } else {
            echo json_encode(true);
        }
    }

}