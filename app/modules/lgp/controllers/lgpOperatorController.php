<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpOperator extends lgpCommon
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

        $this->operatorID = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'LGPD';
        $this->idmodule = $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpOperator');

        $this->loadModel('operator_model');
        $this->dbOperator = new operator_model();

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

        $smarty->display('lgp-operator-grid.tpl');

    }


    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $where = '';

        $typePerson = $this->dbDataMapping->getLgpTypePerson("LGP_operator"); //get type person ID of person who access data mapped
        if(!$typePerson['success']){
            if($this->log)
                $this->logIt("Can't get lgpd person has access data.\n".$typePerson['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }
        
        $where = count($typePerson['data']) > 0 ? "WHERE a.idtypeperson = {$typePerson['data'][0]['idtypeperson']}" : "";

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='a.name';
        if(!$sord)
            $sord ='ASC';
        
        
        if ($_POST['_search'] == 'true'){
            if($_POST['searchField'] == 'operator'){
                $_POST['searchField'] = 'a.name';
            }
            if($_POST['searchField'] == 'idnatureperson'){
                $_POST['searchField'] = 'a.idnatureperson';
            }

            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']) ;
        }
        
        
        $rsCount = $this->dbOperator->getOperator($where);
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

        $rsOperator =  $this->dbOperator->getOperator($where,$order,$limit);
        if (!$rsOperator['success']) {
            if($this->log)
                $this->logIt("Error: {$rsOperator['message']}.\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }        

        while (!$rsOperator['data']->EOF) {
            $status_fmt = ($rsOperator['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            
            $aColumns[] = array(
                'id'            => $rsOperator['data']->fields['idperson'],
                'name'          => $rsOperator['data']->fields['operator'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsOperator['data']->fields['status'],
                'idnatureperson'      => $rsOperator['data']->fields['idnatureperson']== 1 ? 
                                            $this->getLanguageWord('natural') : 
                                            $this->getLanguageWord('juridical'),
                'contact_person'=> $rsOperator['data']->fields['contact_person'],

                'phone_number'         => $rsOperator['data']->fields['phone_number'],
                'cel_phone'  => $rsOperator['data']->fields['cel_phone']
            );

           /*$lblOperatorCategory = $rs->fields['idnatureperson'] == 1 ? 
            $this->getLanguageWord('natural') : 
            $this->getLanguageWord('juridical');
            $objSmarty->assign('txtCategory',$lblOperatorCategory);
*/
            $rsOperator['data']->MoveNext();
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

        $this->makeScreenOperator($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('lgp-operator-create.tpl');
    }
    

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $operatorID = $this->getParam('operatorID');
        $rsOperator = $this->dbOperator->getOperator("WHERE a.idperson = $operatorID") ;
        
        
        $this->makeScreenOperator($smarty,$rsOperator['data'],'update');

        $typeID = $rsOperator['data']->fields['idnatureperson'];
        


        if($typeID == 1){
             $smarty->assign('displayNatural', '') ;
             $smarty->assign('displayJuridical', 'hide') ;           
        }else{
            $smarty->assign('displayNatural', 'hide') ;
            $smarty->assign('displayJuridical', '') ;
        }

        
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idperson', $operatorID);
        $smarty->assign('idnatureperson',$rsOperator['data']->fields['idnatureperson']);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('lgp-operator-update.tpl');

    }

    public function viewOperator()
    {
       $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $operatorID = $this->getParam('operatorID');
        $rsOperator = $this->dbOperator->getOperator("WHERE a.idperson = $operatorID") ;

        $this->makeScreenOperator($smarty,$rsOperator['data'],'echo');

        $typeID = $rsOperator['data']->fields['idnatureperson'];       

        if($typeID == 1){
             $smarty->assign('displayNatural', '') ;
             $smarty->assign('displayJuridical', 'hide') ;           
        }else{
            $smarty->assign('displayNatural', 'hide') ;
            $smarty->assign('displayJuridical', '') ;
        }


        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('summernote_version', $this->summernote);

        $smarty->assign('hidden_idperson', $operatorID);
        //$smarty->assign('idnatureperson', $typeID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language        
        
        $smarty->display('lgp-operator-view.tpl');

    }

    function makeScreenOperator($objSmarty,$rs,$oper)
    {        
        
        // --- Name ---
        if ($oper == 'update') {

            if (empty($rs->fields['operator']))
                $objSmarty->assign('lgp_operator_name',$this->getLanguageWord('Name'));
            else{
                $objSmarty->assign('operatorName',$rs->fields['operator']);
            }
            $objSmarty->assign('operatorContact',$rs->fields['contact_person']);

            $objSmarty->assign('operatorPhone',$rs->fields['phone_number']);            
            $objSmarty->assign('operatorMobile',$rs->fields['cel_phone']);
            $objSmarty->assign('idnatureperson',$rs->fields['idnatureperson']);            

        } elseif ($oper == 'create') {
                $objSmarty->assign('lgp_operator_name',$this->getLanguageWord('Name'));

        }elseif ($oper == 'echo') {
            if (empty($rs->fields['operator']))
                $objSmarty->assign('lgp_operator_name',$this->getLanguageWord('Name'));
            else{
                $objSmarty->assign('operatorName',$rs->fields['operator']);
            }
            $objSmarty->assign('operatorContact',$rs->fields['contact_person']);

            $objSmarty->assign('operatorPhone',$rs->fields['phone_number']);            
            $objSmarty->assign('operatorMobile',$rs->fields['cel_phone']);

            if (empty($rs->fields['idnatureperson']))
                $objSmarty->assign('txtCategory',$this->getLanguageWord('naturetype'));
            else{
                $objSmarty->assign('idnatureperson',$rs->fields['naturetype']);        
            }

            $lblOperatorCategory = $rs->fields['idnatureperson'] == 1 ? 
            $this->getLanguageWord('natural') : 
            $this->getLanguageWord('juridical');
            $objSmarty->assign('txtCategory',$lblOperatorCategory);
            
            
        }
          
    }

    function createOperator()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }           
        $typeID = $_POST['categoryOperator'] == '1' ? 1 : 2;
        //$typeID = $typeID == 2 ? $_POST['categoryOperator'] : 1;        
        $operatorName = strip_tags($_POST['operatorName']);
        $phone = $_POST['operatorPhone'];
        $celPhone = $_POST['operatorMobile'];
        $contact = $_POST['operatorContact'];

        $typePerson = $this->dbDataMapping->getLgpTypePerson("LGP_operator"); //get type person ID of person who access data mapped
        if(!$typePerson['success']){
            if($this->log)
                $this->logIt("Can't get lgpd operator data.\n".$typePerson['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }
        $typePersonID = $typePerson['data'][0]['idtypeperson'];

        $this->dbOperator->BeginTrans();

        $ret = $this->dbOperator->insertOperator($typePersonID, $typeID, $operatorName, $phone, $celPhone);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Operator data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbOperator->RollbackTrans();
            return false;
        }
        $operatorID = $ret['id'];
        if ($typeID = 2){
            $retContact = $this->dbOperator->insertOperatorContact($operatorID,$contact);

            if (!$retContact['success']) {
                if($this->log)
                    $this->logIt("Can't insert Operator data. {$retContact['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbOperator->RollbackTrans();
                return false;
            }
        }
        

        $aRet = array(
            "success" => true,
            "operatorID" => $operatorID
        );

        $this->dbOperator->CommitTrans();

        echo json_encode($aRet);

    }

    function updateOperator()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $operatorID = $_POST['operatorID'];
        $operatorName = strip_tags($_POST['operatorName']);
        $operatorPhone = $_POST['operatorPhone'];
        $operatorMobile = $_POST['operatorMobile'];
        $typeID = $_POST['categoryOperator'] == '1' ? 1 : 2;
        
        $this->dbOperator->BeginTrans();

        $ret = $this->dbOperator->updateOperator($operatorID,$operatorName, $operatorPhone, $operatorMobile);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update news data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbOperator->RollbackTrans();
            return false;
        }
        $contact = $_POST['operatorContact'];        
        if ($typeID = 2){
            $retContact = $this->dbOperator->updatetOperatorContact($operatorID,$contact);

            if (!$retContact['success']) {
                if($this->log)
                    $this->logIt("Can't insert Operator data. {$retContact['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbOperator->RollbackTrans();
                return false;
            }
        }

        $aRet = array(
            "success"   => true,
            "operatorID" => $operatorID
        );

        $this->dbOperator->CommitTrans();

        echo json_encode($aRet);

    }

    function statusOperator()
    {
        $operatorID = $_POST['operatorID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbOperator->changeStatus($operatorID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update operator status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "operatorID" => $operatorID
        );

        echo json_encode($aRet);

    }
    
    public function existOperator() {
        $this->protectFormInput();
        $search = $_POST['operatorName'];

        $where = "WHERE a.name LIKE '{$search}'";
        $where .= isset($_POST['operatorID']) ? " AND a.idperson != {$_POST['operatorID']}" : "";

        $check = $this->dbOperator->getOperator($where);
        if ($check['data']->RecordCount() > 0) {
            echo json_encode($this->getLanguageWord('operator_exists'));
        } else {
            echo json_encode(true);
        }
    }

}