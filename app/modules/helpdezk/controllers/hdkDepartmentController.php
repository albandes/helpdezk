<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkDepartment extends hdkCommon
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

        $this->loadModel('department_model');
        $dbDepartment = new department_model();
        $this->dbDepartment = $dbDepartment;

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
        $smarty->display('department.tpl');

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
            $sidx ='company,department';
        if(!$sord)
            $sord ='asc';

        switch ($sidx){
            case 'company':
                $order = "company ".$sord.", department ASC";
                break;
            default:
                $order = $sidx." ".$sord;
                break;
        }

        if ($_POST['_search'] == 'true'){
            switch ($_POST['searchField']){
                case 'company':
                    $searchField = 'tbp.`name`';
                    break;
                default:
                    $searchField = 'tbd.`name`';
                    break;
            }

            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField,$_POST['searchString']);

        }

        $rsCount = $this->dbDepartment->selectDepartment($where);
        $count = $rsCount->RecordCount();

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $order";
        $limit = "LIMIT $start , $rows";
        //

        $rsDepartment = $this->dbDepartment->selectDepartment($where,null,$order,$limit);
        
        while (!$rsDepartment->EOF) {
            $status_fmt = ($rsDepartment->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'=> $rsDepartment->fields['iddepartment'],
                'company'=> $rsDepartment->fields['company'],
                'department'=> $rsDepartment->fields['department'],
                'status' => $status_fmt,
                'statusval' => $rsDepartment->fields['status']
            );
            $rsDepartment->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateDepartment()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenDepartment($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('department-create.tpl');
    }

    public function formUpdateDepartment()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $iddepartment = $this->getParam('iddepartment');
        $where = "AND tbd.iddepartment = $iddepartment";
        
        $rsDepartment = $this->dbDepartment->selectDepartment($where);

        $this->makeScreenDepartment($smarty,$rsDepartment,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_iddepartment', $iddepartment);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('department-update.tpl');

    }

    function makeScreenDepartment($objSmarty,$rs,$oper)
    {
        if ($oper == 'update') {
            $objSmarty->assign('txtDepartment',  $rs->fields['department']);
            $objSmarty->assign('companyname',  $rs->fields['company']);
        }

        // --- Company ---        
        if ($oper == 'update') {
            $idCompanyDefault = $rs->fields['idcompany'];
        } elseif ($oper == 'create') {
            $idCompanyDefault = "";            
        } 
        $arrCompany = $this->_comboCompanies();        
        $objSmarty->assign('companyids',  $arrCompany['ids']);
        $objSmarty->assign('companyvals', $arrCompany['values']);
        $objSmarty->assign('idcompany', $idCompanyDefault);     
        
    }

    function createDepartment()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        

        $idcompany = $_POST['cmbCompany'];
        $department = addslashes($_POST['txtDepartment']);

        $where = "WHERE idperson = $idcompany AND pipeLatinToUtf8(`name`) = pipeLatinToUtf8('$department')";
        $check =  $this->dbDepartment->checkDepartment($where);
        if ($check->RecordCount() > 0) {
            $aRet = array(
                "status" => "Error",
                "message" => $this->getLanguageWord('Department_exists')
            );

            echo json_encode($aRet);
            exit;
        }

        $this->dbDepartment->BeginTrans();
        
        $ret = $this->dbDepartment->insertDepartment($idcompany,$department);

        if(!$ret){
            $this->dbDepartment->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbDepartment->CommitTrans();
        
        $aRet = array(
            "status" => "Ok"
        );

        echo json_encode($aRet);

    }

    function updateDepartment()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        
        
        $iddepartment = $_POST['iddepartment'];
        $idcompany = $_POST['idcompany'];
        $department = addslashes($_POST['txtDepartment']);

        $where = "WHERE idperson = $idcompany AND pipeLatinToUtf8(`name`) = pipeLatinToUtf8('$department') AND iddepartment != $iddepartment";
        $check =  $this->dbDepartment->checkDepartment($where);
        if ($check->RecordCount() > 0) {
            $aRet = array(
                "status" => "Error",
                "message" => $this->getLanguageWord('Department_exists')
            );

            echo json_encode($aRet);
            exit;
        }

        $this->dbDepartment->BeginTrans();
        $ret = $this->dbDepartment->updateDepartment($iddepartment, $idcompany, $department);

        if(!$ret){
            $this->dbDepartment->RollbackTrans();
            if($this->log)
                $this->logIt('Update Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbDepartment->CommitTrans();
        
        $aRet = array(
            "status" => "Ok"
        );

        echo json_encode($aRet);

    }

    function changeDepartmentStatus()
    {
        $iddepartment = $this->getParam('iddepartment');
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbDepartment->updateDepartmentStatus($iddepartment,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Department Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idreason" => $iddepartment,
            "status" => 'OK',
            "departmentstatus" => $newStatus
        );

        echo json_encode($aRet);

    }

    public function checkDepartment() {
        $value = $_POST['txtDepartment'];
        $idcompany = $_POST['companyId'];
        $iddepartment = $_POST['departmentId'];

        if(!$idcompany || $idcompany == ""){
            echo json_encode($this->getLanguageWord('Select_company'));
            exit;
        }

        $where = ($iddepartment && $iddepartment != '') 
                 ? "WHERE idperson = $idcompany AND pipeLatinToUtf8(`name`) = pipeLatinToUtf8('$value') AND iddepartment != $iddepartment"
                 : "WHERE idperson = $idcompany AND pipeLatinToUtf8(`name`) = pipeLatinToUtf8('$value')";

        $check =  $this->dbDepartment->checkDepartment($where);
        if ($check->fields) {
            echo json_encode($this->getLanguageWord('Department_exists'));
        } else {
            echo json_encode(true);
        }
    }

    public function modalDelete() {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $iddepartment = $_POST['departmentId'];

        $where = "AND tbd.iddepartment = $iddepartment";

        $rsDepartment =  $this->dbDepartment->selectDepartment($where);
        if(!$rsDepartment){
            if($this->log)
                $this->logIt('Get Department Data - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $rsHasPerson =  $this->dbDepartment->getPersonByDepartment($iddepartment);
        if(!$rsHasPerson){
            if($this->log)
                $this->logIt('Get Person by Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($rsHasPerson->RecordCount() > 0){
            $list =  $this->listDepartmentHtml($rsDepartment->fields['idcompany'],$iddepartment);
            $arrRet = array(
                'hasPerson' => 1,
                'companyName' => $rsDepartment->fields['company'],
                'departmentList' => $list
            );
        }else{
            $arrRet = array(
                'hasPerson' => 0
             );
        }

        echo json_encode($arrRet);
    }

    public function listDepartmentHtml($companyID,$disabledID)
    {

        $arrDepartment = $this->_comboDepartment($companyID);
        $select = "<option value=''>".$this->getLanguageWord('Select_department')."</option>";
        foreach ( $arrDepartment['ids'] as $indexKey => $indexValue ) {
            $disable = $disabledID == $indexValue ? "disabled=disabled" : "";
            $select .= "<option value='$indexValue' {$disable}>".$arrDepartment['values'][$indexKey]."</option>";
        }

        return $select;
    }

    function deleteDepartment()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        //echo "<pre>", print_r($_POST,true), "</pre>";
        $iddepartment = $_POST['iddepartment'];
        $dephasperson = $_POST['hasperson'];
        $newdepartment = $_POST['newdepartment'];

        $this->dbDepartment->BeginTrans();

        if($dephasperson == 1){
            $updDepHasPerson = $this->dbDepartment->updatePersonDepartment($iddepartment,$newdepartment);
            if (!$updDepHasPerson) {
                $this->dbDepartment->RollbackTrans();
                if($this->log)
                    $this->logIt('Update hdk_tbdepartment_has_person - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }


        $dea = $this->dbDepartment->departmentDelete($iddepartment);
        if (!$dea) {
            $this->dbDepartment->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Department - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbDepartment->CommitTrans();

        $aRet = array(
            "iddepartment" => $iddepartment,
            "status"   => 'OK'
        );

        echo json_encode($aRet);

    }



}