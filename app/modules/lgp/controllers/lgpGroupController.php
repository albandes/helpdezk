<?php
require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpGroup extends lgpCommon {
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

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lgpGroup');
        
        $this->loadModel('lgpgroup_model');
        $this->dbGroup = new lgpgroup_model();

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
        $this->_makeNavlgp($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('token', $this->_makeToken());

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lgp-group-grid.tpl');


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
            $sidx ='nome';
        if(!$sord)
            $sord ='ASC';
        
        //CONFERIR SISTEMA DE BUSCA***************
        if ($_POST['_search'] == 'true'){

            //echo $_POST['searchField'];

            if($_POST['searchField'] == "group_name"){

                $search_field = "b.name";

                $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);

            }else if($_POST['searchField'] == "company_name"){

                $search_field = "c.name";

                $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);

            }

        }

        $rsCount = $this->dbGroup->getGroup($where);
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

        $rsGroup =  $this->dbGroup->getGroup($where, $order,$limit);

        //print_r($rsGroup);

        if (!$rsGroup['success']) {
            if($this->log)
                $this->logIt("Can't get Group data. {$rsGroup['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsGroup['data']->EOF) {
            $status_fmt = ($rsGroup['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'                => $rsGroup['data']->fields['idgroup'],
                'idcompany'            => $rsGroup['data']->fields['idcompany'],
                'idtypeperson'          => $rsGroup['data']->fields['idtypeperson'],
                'group_name'          => $rsGroup['data']->fields['group_name'],
                'company_name'          => $rsGroup['data']->fields['company_name'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsGroup['data']->fields['status']

            );
            $rsGroup['data']->MoveNext();
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

        $this->makeScreenGroup($smarty,'','create');

        $smarty->assign('token', $this->_makeToken());
        $typePerson = $this->dbGroup->getTypePersonName("LGP_group");

        $smarty->assign('typepersonid', $typePerson['data']->fields['idtypeperson']);
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgpgroup-create.tpl');
    }

    public function formUpdate()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $groupID = $this->getParam('groupID');
        $rsGroup = $this->dbGroup->getGroup("AND idgroup = $groupID");

        $this->makeScreenGroup($smarty,$rsGroup['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_groupID', $groupID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgpgroup-update.tpl');

    }
    public function viewGroup()
    {
        $smarty = $this->retornaSmarty();

        $groupID = $this->getParam('groupID');
        $rsGroup = $this->dbGroup->getGroup("AND idgroup = $groupID");

        $this->makeScreenGroup($smarty,$rsGroup['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgpgroup-view.tpl');

    }


    function makeScreenGroup($objSmarty,$rs,$oper)
    {

        // --- Name ---
        if ($oper == 'update') {

            //The area combo item is pre-selected when the update form is loaded
            $objSmarty->assign('idcompany',$rs->fields['idcompany']);

            if (empty($rs->fields['group_name'])){
                
                $objSmarty->assign('plh_nome','Novo');

            }else{

                //print_r($rs->fields['group_name']);
                $objSmarty->assign('groupName',$rs->fields['group_name']);

            }

            //COMBO EMPRESA
            $companies = $this->_comboCompanies();
            $objSmarty->assign('companyIds', $companies['ids']);
            $objSmarty->assign('companyVals', $companies['values']);

        } elseif ($oper == 'create') {

            //Se o campo cargo estiver vazio
            if (empty($rs->fields['group_name']))
                //smarty plh_course recebe "Informe o curso da turma"
                $objSmarty->assign('plh_nome','Informe o nome do grupo');

            //COMBO EMPRESA
            $companies = $this->_comboCompanies();
            $objSmarty->assign('companyIds', $companies['ids']);
            $objSmarty->assign('companyVals', $companies['values']);
            $objSmarty->assign('idcompany', '');

        } elseif ($oper == 'echo') {

            $objSmarty->assign('groupCompany',$rs->fields['company_name']);
            $objSmarty->assign('groupName',$rs->fields['group_name']);

        }

    }

    function createGroup()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $typepersonid = $_POST['typepersonid'];
        $company = $_POST['cmbGroupCompany'];
        $groupName = $_POST['groupName'];

        //To prevent XSS
        $typepersonid_ = strip_tags($typepersonid);
        $company_ = strip_tags($company);
        $groupName_ = strip_tags($groupName);

        //echo $typepersonid_, $company_, $groupname_;

        $this->dbGroup->BeginTrans();

        $ret = $this->dbGroup->insertGroup($typepersonid_, $groupName_, $company_);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Group data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbGroup->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "groupID" => $ret['id']
        );

        $this->dbGroup->CommitTrans();

        echo json_encode($aRet);

    }

    function updateGroup()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $groupID = strip_tags($_POST['groupID']); 
        $company = strip_tags($_POST['cmbGroupCompany']);
        $groupName = strip_tags($_POST['groupName']);

        $this->dbGroup->BeginTrans();

        $ret = $this->dbGroup->updateGroup($company,$groupName, $groupID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Group data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbGroup->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "groupID" => $groupID
        );

        $this->dbGroup->CommitTrans();

        echo json_encode($aRet);

    }

    public function statusGroup(){

        $groupID = strip_tags($_POST['groupID']);
        $newStatus = strip_tags($_POST['newStatus']);

        //echo $groupID, $newStatus;

        $ret = $this->dbGroup->statusGroup($groupID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Group status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "groupID" => $groupID
        );

        echo json_encode($aRet);
    }

    function modalPeopleByGroup(){
        $arrGrps = $this->_comboGroups("AND a.status = 'A' ","ORDER BY b.name");
        $select =  "<option value='' disabled hidden selected>".$this->getLanguageWord('Select_group')."</option>";

        foreach ( $arrGrps['ids'] as $indexKey => $indexValue ) {

            if($arrGrps['NF']){
                $indexValue = "NF";
            }

            $select .= "<option value='$indexValue'>".$arrGrps['values'][$indexKey]."</option>";

        }
        
        $aRet = array(
            "cmblist" => $select
        );

        echo json_encode($aRet);
    }

    function _comboGroups($where=null,$order=null,$limit=null){

        $rs = $this->dbGroup->selectGroup($where,$order,$limit);

        if($rs->RecordCount() > 0){
            $fieldsID[] = $rs->fields[''];
            $values[]   = $rs->fields[''];
            while (!$rs->EOF) {
                $fieldsID[] = $rs->fields['idgroup'];
                $values[]   = $rs->fields['group_name'];
                $rs->MoveNext();
            }
        }else{
            $fieldsID[] = "";
            $values[]   = $this->getLanguageWord('No_result');
            $arrRet['NF'] = true;
        }


        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    function loadPeopleByGroup(){

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idgroup = strip_tags($_POST['idgroup']);

       if($idgroup == "NF"){

            echo false;

       }else{

            $rsType = $this->dbGroup->getTypePersonName("LGP_personhasaccess");
            $idTypeLGP = $rsType['data']->fields['idtypeperson'];
            $where = "WHERE `idtypeperson` IN(2, 3, $idTypeLGP)";
            $order = "ORDER BY `name` ASC";
            $rsPeoples = $this->dbGroup->selectPeoples($where, $order);

            if (!$rsPeoples['success']) {
                if($this->log)
                    $this->logIt('Get Peoples by Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $table = "<table class='table'>";

            //Check if the person exists in a recorded group
            while (!$rsPeoples['data']->EOF) {
                $check =  $this->dbGroup->checkPeopleGroup($rsPeoples['data']->fields['idperson'], $idgroup);
                if (!$check) {
                    if($this->log)
                        $this->logIt('Check People in Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                $checked = $check['data']->fields ? 'checked="checked"' : '';

                $table.= "<tr>
                            <td>
                                <div class='i-checks'>
                                    <input type='checkbox' class='checkPeopleGroup' name='{$rsPeoples['data']->fields['idperson']}-{$idgroup}' value='{$rsPeoples['data']->fields['idperson']}-{$idgroup}' id='{$rsPeoples['data']->fields['idperson']}-{$idgroup}' {$checked}>&nbsp; 
                                    <span>{$rsPeoples['data']->fields['name']}</span>
                                </div>
                            </td>
                        </tr>";

                $rsPeoples['data']->MoveNext();
            }

            $table .= "</table>";

            echo $table;

       }

    
    }

    function setPeopleByGroup()
    {
        $this->protectFormInput();

        $idperson = strip_tags($_POST['idperson']);
        $idgroup = strip_tags($_POST['idgroup']);
        $action = strip_tags($_POST['action']);

        $this->dbGroup->BeginTrans();
        $ret = $action == 'ADD' ? $this->dbGroup->groupPersonInsert($idgroup, $idperson) : $this->dbGroup->groupPersonDelete($idgroup, $idperson);

        if(!$ret){
            $this->dbGroup->RollbackTrans();
            if($this->log)
                $this->logIt("Insert People Groups - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbGroup->CommitTrans();

        echo json_encode($ret);

    }

    //Validation
    public function existgroup() {
        $this->protectFormInput();

        $groupName = strip_tags($_POST['groupName']);

        //Validação do create
        $where = "AND b.name = '$groupName'";

        //Validação do Update
        $where .= isset($_POST['groupID']) ? " AND idgroup != {$_POST['groupID']}" : "";

        $check = $this->dbGroup->getGroup($where);
        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord("lgp_groupexists"));

        } else {
            echo json_encode(true);
        }
    }



}