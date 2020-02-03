<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkGroup extends hdkCommon
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

        $this->modulename = 'helpdezk' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('groups_model');
        $dbGroup = new groups_model();
        $this->dbGroup = $dbGroup;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->loadModel('admin/permissions_model');
        $dbPermission = new permissions_model();
        $this->dbPermission = $dbPermission;

        $this->loadModel('service_model');
        $dbService = new service_model();
        $this->dbService = $dbService;

        $this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);

    }

    public function index()
    {

        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);

        $smarty->assign('token', $token) ;
        $smarty->display('group.tpl');

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
            $sidx ='tbp.name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $count = $this->dbGroup->countGroups($where);

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

        $rsGroups = $this->dbGroup->selectGroup($where,$order,$limit);

        while (!$rsGroups->EOF) {

            $status_fmt = ($rsGroups->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

            $aColumns[] = array(
                'idgroup'       => $rsGroups->fields['idgroup'],
                'name'          => $rsGroups->fields['name'],
                'level'         => $rsGroups->fields['lvl'],
                'company'       => $rsGroups->fields['company'],
                'statuslbl'        => $status_fmt,
                'status'     => $rsGroups->fields['status']
            );
            $rsGroups->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);
    }

    public function formCreateGroup()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $smarty->assign('token', $token) ;

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);

        $this->makeScreenGroup($smarty,'','create');

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('group-create.tpl');
    }

    public function formUpdateGroup()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $idgroup = $this->getParam('idgroup');
        
        $rsGroup = $this->dbGroup->selectGroupData($idgroup);

        $this->makeScreenGroup($smarty,$rsGroup,'update');

        $smarty->assign('token', $token) ;
        $smarty->assign('idgroup', $idgroup) ;

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('group-update.tpl');
    }

    function makeScreenGroup($objSmarty,$rs,$action)
    {
        
        // --- Companies ---
        $arrCompanies = $this->_comboCompanies();
        if ($action == 'update') {
            $idCompanyEnable = $rs->fields['idcustomer'];
        } elseif ($action == 'create') {
            $idCompanyEnable = "";
        }
        $objSmarty->assign('grpcompanyids',  $arrCompanies['ids']);
        $objSmarty->assign('grpcompanyvals', $arrCompanies['values']);
        $objSmarty->assign('idgrpcompany', $idCompanyEnable  );

        
        if ($action == 'update'){
            // --- Name ---
            $objSmarty->assign('nameval',$rs->fields['name']);
            $objSmarty->assign('grpperson',$rs->fields['idperson']);
            
            // --- Attendance level ---
            $objSmarty->assign('attlvlval',$rs->fields['level']);

            // --- Flag Repass Only ---
            $flgchecked = $rs->fields['repass_only'] == 'Y' ? 'checked=checked' : '';
            $objSmarty->assign('flgchecked',$flgchecked);
        }
        



    }

    function createGroup()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $costumer = $_POST['cmbGrpCompany'];
        $name = addslashes($_POST['grpName']);
        $level = $_POST['attLevel'];
        $repass = isset($_POST['flgRepassOnly']) ? 'Y' : 'N';

        $this->dbGroup->BeginTrans();

        $ins = $this->dbPerson->insertPerson('3', '6', '1', '1', $name, NULL, NULL, 'A', 'N', NULL, NULL, NULL);
        if(!$ins){
            $this->dbGroup->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Group in tbperson  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ret = $this->dbGroup->insertGroup($ins, $level, $costumer, $repass);
        if (!$ret) {
            $this->dbGroup->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbGroup->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    function updateGroup()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $costumer = $_POST['cmbGrpCompany'];
        $name = addslashes($_POST['grpName']);
        $level = $_POST['attLevel'];
        $repass = isset($_POST['flgRepassOnly']) ? 'Y' : 'N';
        $idgroup = $_POST['idgroup'];
        $idperson = $_POST['idgrpperson'];

        $this->dbGroup->BeginTrans();
        
        $set = "`name` = '$name'";
        $where = "idperson = $idperson";
        $upd = $this->dbPerson->updatePerson($set,$where);
        if(!$upd){
            $this->dbGroup->RollbackTrans();
            if($this->log)
                $this->logIt('Update Group in tbperson  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ret = $this->dbGroup->updateGroup($idgroup, $costumer, $repass, $level);
        if (!$ret) {
            $this->dbGroup->RollbackTrans();
            if($this->log)
                $this->logIt('Update Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbGroup->CommitTrans();

        $aRet = array(
            "idgroup" => $idgroup,
            "status"   => 'OK'
        );

        $this->dbPerson->CommitTrans();

        echo json_encode($aRet);


    }

    function changeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $idGroup = $_POST['idgroup'];
        $newStatus = $_POST['newstatus'];

        $ret = $newStatus == 'A' ? $this->dbGroup->groupsActivate($idGroup) : $this->dbGroup->groupsDeactivate($idGroup);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Group Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idgroup" => $idGroup,
            "status" => 'OK',
            "personstatus" => $newStatus
        );

        echo json_encode($aRet);

    }

    function modalAttendantByGroup(){
        $arrGrps = $this->_comboGroups('','ORDER BY tbp.name');
        $select =  "<option value=''>".$this->getLanguageWord('Select_group')."</option>";

        foreach ( $arrGrps['ids'] as $indexKey => $indexValue ) {
            $select .= "<option value='$indexValue'>".$arrGrps['values'][$indexKey]."</option>";
        }

        $aRet = array(
            "cmblist" => $select
        );

        echo json_encode($aRet);
    }

    function loadAttendantsByGroup(){
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idgroup = $_POST['idgroup'];

        $rsAttendants = $this->dbGroup->selectAttendants();
        if (!$rsAttendants) {
            if($this->log)
                $this->logIt('Get Attendants by Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $table = "<table class='table'>";
        while (!$rsAttendants->EOF) {
            $check =  $this->dbGroup->checkAttendantGroup($rsAttendants->fields['idperson'], $idgroup);
            if (!$check) {
                if($this->log)
                    $this->logIt('Check Attendant in Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            $checked = $check->fields ? 'checked="checked"' : '';

            $table.= "<tr>
                        <td>
                            <div class='i-checks'>
                                <input type='checkbox' class='checkAttendantGrp' name='{$rsAttendants->fields['idperson']}-{$idgroup}' value='{$rsAttendants->fields['idperson']}-{$idgroup}' id='{$rsAttendants->fields['idperson']}-{$idgroup}' {$checked}>&nbsp; 
                                <span>{$rsAttendants->fields['name']}</span>
                            </div>
                        </td>
                    </tr>";
            $rsAttendants->MoveNext();
        }

        $table .= "</table>";

        echo $table;
    }

    function setAttendantsByGroup()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idperson = $_POST['idperson'];
        $idgroup = $_POST['idgroup'];
        $action = $_POST['action'];

        $ret = $action == 'ADD' ? $this->dbPermission->groupPersonInsert($idgroup, $idperson) : $this->dbPermission->groupPersonDelete($idgroup, $idperson);

        if (!$ret) {
            if($this->log)
                $this->logIt('Insert/Delete Attendant in Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        echo 'OK';

    }

    function modalGroupsByService(){
        $rsServices = $this->dbService->selectAllServices();
        $options = "<option value=''>".$this->getLanguageWord('Select')."</option>";
        $iditemtmp = 0;
        $i = 1;

        while(!$rsServices->EOF) {
            $lbl = $this->getLanguageWord('Item').': '.$rsServices->fields['item'];

            if ($iditemtmp != $rsServices->fields("iditem")){
                if($i != 1)
                    $options .= "</optgroup>";
                $options .= "<optgroup label='$lbl'>";
            }


            $options .= "<option value='{$rsServices->fields['idservice']}'>{$rsServices->fields['service']}</option>";

            $iditemtmp = $rsServices->fields("iditem");
            $i++;
            $rsServices->MoveNext();

        }

        $aRet = array(
            "cmblist" => $options
        );

        echo json_encode($aRet);
    }

    function loadGroupsByService(){
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idservice = $_POST['idservice'];

        $rsGroups = $this->dbService->selectServiceGroup($idservice);
        if (!$rsGroups) {
            if($this->log)
                $this->logIt('Get Groups by Service - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $table = "<div class='panel-body'>({$rsGroups->fields['lvl']}) {$rsGroups->fields['groupname']}</div>";

        echo $table;
    }

    function modalSetGroupRepass(){
        $rsGrpRepass = $this->dbGroup->getGroupsRepass();
        $options = "<option value=''>".$this->getLanguageWord('Select')."</option>";
        $iditemtmp = 0;
        $i = 1;

        while(!$rsGrpRepass->EOF) {
            $options .= "<option value='{$rsGrpRepass->fields['idperson']}'>{$rsGrpRepass->fields['grp']} ({$rsGrpRepass->fields['company']})</option>";
            $rsGrpRepass->MoveNext();

        }

        $aRet = array(
            "cmblist" => $options
        );

        echo json_encode($aRet);
    }

    function loadCompaniesRepass(){
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idgroup = $_POST['idgroup'];

        //get this group's alias
        $rsAlias = $this->dbGroup->selectServiceGroup($idgroup);
        if (!$rsAlias) {
            if($this->log)
                $this->logIt('Get Group Alias - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsAlias->EOF) {
            $idsalias[] = $rsAlias->fields['idgroup'];
            $rsAlias->MoveNext();
        }

        $rsCompanies = $this->dbGroup->selectCorporations();

        $table = "<table class='table'>";
        while (!$rsCompanies->EOF) {
            $idcompany = $rsCompanies->fields['idperson'];
            $namecompany = $rsCompanies->fields['name'];

            $rsCompanyGroups = $this->dbGroup->selectGroup("AND tbp2.idperson = $idcompany","ORDER BY name ASC");
            $rsCountGroups = $this->dbGroup->countGroups("AND tbg.idcustomer = $idcompany");
            $totalGroups = $rsCountGroups->fields['total'];

            if( $totalGroups > 0){
                $table .="<tr><td style='vertical-align: middle;'>$namecompany</td>";
                $table .= "<td><select name='groupsIds[]' id='company$idcompany' class='form-control m-b cmb-grp-repass'>
                            <option value=''>{$this->getLanguageWord('Select')}</option>";

                while (!$rsCompanyGroups->EOF) {
                    $idgroup = $rsCompanyGroups->fields['idperson'];
                    $namegroup = $rsCompanyGroups->fields['name'];
                    if(in_array($idgroup, $idsalias)){
                        $table .="<option value='$idgroup' selected='selected'>$namegroup</option>";
                    }else{
                        $table .="<option value='$idgroup'>$namegroup</option>";
                    }
                    $rsCompanyGroups->MoveNext();
                }

                $table .= "</select></td></tr>";
            }

            $rsCompanies->MoveNext();
        }

        $table .= "</table>";

        echo $table;
    }

    function setGroupRepass()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idalias = $_POST['cmbGroupsRepass'];

        $this->dbGroup->BeginTrans();

        $del = $this->dbPerson->deleteGroupsRepass($idalias);
        if(!$del){
            $this->dbGroup->RollbackTrans();
            if($this->log)
                $this->logIt("Delete Group Alias ID: {$idalias}  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach ($_POST['groupsIds'] as $idGroups) {
            if($idGroups != 0){
                $ret = $this->dbGroup->insertGroupsRepass($idGroups,$idalias);
                if (!$ret) {
                    $this->dbGroup->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }
        }

        $this->dbGroup->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

}