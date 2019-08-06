<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkService extends hdkCommon
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

        $this->loadModel('service_model');
        $dbService = new service_model();
        $this->dbService = $dbService;

        $this->loadModel('ticketrules_model');
        $dbRules = new ticketrules_model();
        $this->dbRules = $dbRules;

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

        $tabServices = $this->makeServicesList();
        $smarty->assign("tabservices",$tabServices);
        $smarty->assign('token', $token) ;

        $smarty->display('service.tpl');

    }

    public function makeServicesList()
    {
        $rsAreas = $this->dbService->selectAreas();
        $tabBody = "<table class='table'>";

        while(!$rsAreas->EOF) {
            $checkedArea = $rsAreas->fields['status'] == 'A' ? 'checked=checked' : '';
            $tabBody .= "<tr>
                            <td colspan='4'>
                                <div class='i-checks'>
                                    <input type='checkbox' class='checkArea' name='area_{$rsAreas->fields['idarea']}' value='{$rsAreas->fields['idarea']}' id='area_{$rsAreas->fields['idarea']}' {$checkedArea}>&nbsp; 
                                    <span class='text-service'>{$rsAreas->fields['name']}</span>
                                </div>
                            </td>
                        </tr>";

            $rsTypes = $this->dbService->getTypeFromAreas($rsAreas->fields['idarea']);

            while(!$rsTypes->EOF) {
                $checkedType = $rsTypes->fields['type_status'] == 'A' ? 'checked=checked' : '';
                $tabBody .= "<tr>
                                <td></td>
                                <td>
                                    <div class='i-checks'>
                                        <input type='checkbox' class='checkType' name='type_{$rsTypes->fields['type']}' value='{$rsTypes->fields['type']}' id='type_{$rsTypes->fields['type']}' {$checkedType}>&nbsp;
                                        <span>{$rsTypes->fields['type_name']}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href='javascript:;' onclick='editType({$rsTypes->fields['type']})' class='btn btn-default btn-xs tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->getLanguageWord('Type_edit')}'><i class='fa fa-edit'></i></a>
                                    </div>                                    
                                </td>
                                <td>
                                    <div>
                                    <a href='javascript:;' onclick='viewType({$rsTypes->fields['type']})' class='btn btn-default btn-xs  tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->getLanguageWord('tooltip_list_items')}'><i class='fa fa-bars'></i></a>
                                    </div>
                                </td>
                            </tr>";

                $rsTypes->MoveNext();
            }

            $rsAreas->MoveNext();

        }

        $tabBody .= "</table>";

        return $tabBody;

    }

    public function areaChangeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = $_POST['id'];
        $newStatus = $_POST['newStatus'];

        $updt = $this->dbService->areaChangeStatus($id, $newStatus);
        if ($updt) {
            echo 'OK';
        } else {
            if($this->log)
                $this->logIt('Error change status idarea : '.$id.' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


    }

    public function typeChangeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = $_POST['id'];
        $newStatus = $_POST['newStatus'];

        $updt = $this->dbService->typeChangeStatus($id, $newStatus);
        if ($updt) {
            echo 'OK';
        } else {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


    }

    public function itemChangeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = $_POST['id'];
        $newStatus = $_POST['newStatus'];

        $updt = $this->dbService->itemChangeStatus($id, $newStatus);
        if ($updt) {
            echo 'OK';
        } else {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


    }

    public function serviceChangeStatus()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = $_POST['id'];
        $newStatus = $_POST['newStatus'];

        $updt = $this->dbService->serviceChangeStatus($id, $newStatus);
        if ($updt) {
            echo 'OK';
        } else {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


    }

    public function modalArea()
    {
        
        $aRet = $this->makeAreasList();
        echo json_encode($aRet);

    }

    public function makeAreasList(){
        $rsAreas = $this->dbService->selectAreas();
        $tbody = "";

        while(!$rsAreas->EOF) {
            $checkedArea = $rsAreas->fields['status'] == 'A' ? 'checked=checked' : '';
            $tbody .= " <tr>
                            <td>
                                <div class='i-checks'>
                                    <input type='checkbox' class='checkAreaModal' name='areaModal_{$rsAreas->fields['idarea']}' value='{$rsAreas->fields['idarea']}' id='areaModal_{$rsAreas->fields['idarea']}' {$checkedArea}>&nbsp; 
                                    <span class='text-service'>{$rsAreas->fields['name']}</span>
                                </div>
                            </td>
                            <td>
                                <a href='javascript:;' onclick='editArea({$rsAreas->fields['idarea']})' class='btn btn-default btn-xs tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->getLanguageWord('Area_edit')}'><i class='fa fa-edit'></i></a>
                            </td>
                        </tr>";
            $rsAreas->MoveNext();

        }

        $aRet = array(
            "tablelist" => $tbody
        );

        return $aRet;

    }

    function createArea()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        } 

        $name = addslashes($_POST['modal_area_name']);
		$default = isset($_POST['checkDefaultArea']) ? 1 : 0;

        $this->dbService->BeginTrans();

        if($default == 1){
			$clear = $this->dbService->clearDefaultArea();
			if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                $this->logIt('Insert Area - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
				return false;
			}
		}

        $ret = $this->dbService->areaInsert($name,$default);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Area  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $arrAreas = $this->makeAreasList();
        $tabServices = $this->makeServicesList();

        $aRet = array(
            "status" => 'OK',
            "arealist" => $arrAreas,
            "tabservices" => $tabServices
        );

        echo json_encode($aRet);

    }

    public function modalUpdateArea()
    {
        $idarea = $_POST['idarea'];

        $rsArea = $this->dbService->selectAreaEdit($idarea);
        if ($rsArea) {
            $aRet = array(
                'name'      => $rsArea->fields['name'],
                'default'   => $rsArea->fields['def']
            );
        } else {
            if($this->log)
                $this->logIt('Error get data idarea : '.$idarea.' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        echo json_encode($aRet);

    }

    function updateArea()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        } 

        $id = addslashes($_POST['idarea_upd']);
        $name = addslashes($_POST['area_name_upd']);
		$default = isset($_POST['updDefaultArea']) ? 1 : 0;

        $this->dbService->BeginTrans();

        if($default == 1){
			$clear = $this->dbService->clearDefaultArea();
			if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                $this->logIt('Update Area ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
				return false;
			}
		}

        $ret = $this->dbService->updateArea($id, $name, $default);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Update Area ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    public function modalType()
    {
        $viewType = $_POST['viewType'];

        if($viewType == 'upd'){
            $rsType = $this->dbService->selectTypeEdit($_POST['idtype']);
            if (!$rsType) {
                if($this->log)
                    $this->logIt('Get Type data ID: '.$_POST['idtype'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $bus = array(
                            'name' => $rsType->fields['name'],
                            'status' => $rsType->fields['status'],
                            'selec' => $rsType->fields['selected'],
                            'classify' => $rsType->fields['classify']
                        );
            $areaDefault = $rsType->fields['idarea'];
        }else{
            $bus = array(
                            'name' => '',
                            'status' => 'A',
                            'selec' => 0,
                            'classify' => 0
                        );
            $areaDefault = 0;
        }

        $arrArea = $this->_comboArea();
        $select = '';
        $select .= "<option value=''>".$this->getLanguageWord('Select_area')."</option>";
        
        foreach ( $arrArea['ids'] as $indexKey => $indexValue ) {
            $select .= "<option value='$indexValue'>".$arrArea['values'][$indexKey]."</option>";
        }

        $aRet = array(
            'cmbArea' => $select,
            'defaultArea' => $areaDefault,
            'typeData' => $bus
        );

        echo json_encode($aRet);

    }

    function createType()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $name = addslashes($_POST['modal_type_name']);
        $vardefault = isset($_POST['checkDefault']) ? 1 : 0;
        $status = isset($_POST['checkAvailable']) ? 'A' : 'N';
        $classify = isset($_POST['checkClassification']) ? 1 : 0;
        $area = $_POST['modal_cmbArea'];	

        $this->dbService->BeginTrans();

        if($vardefault == 1){
			$clear = $this->dbService->clearDefaultType($area);
			if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                $this->logIt('Insert Type - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
				return false;
			}
		}

        $ret = $this->dbService->typeInsert($name, $vardefault, $status, $classify, $area);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Type  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    function updateType()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        } 

        $id = addslashes($_POST['idtype_modal']);
        $name = addslashes($_POST['modal_type_name']);
        $vardefault = isset($_POST['checkDefault']) ? 1 : 0;
        $status = isset($_POST['checkAvailable']) ? 'A' : 'N';
        $classify = isset($_POST['checkClassification']) ? 1 : 0;
        $area = $_POST['modal_cmbArea'];

        $this->dbService->BeginTrans();

        if($vardefault == 1){
			$clear = $this->dbService->clearDefaultType($area);
			if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                $this->logIt('Update Type ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
				return false;
			}
		}

        $ret = $this->dbService->updateType($id, $name, $area, $vardefault, $status, $classify);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Update Type ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    public function itemList()
    {
        $idtype = $_POST['id'];

        $name = $this->dbService->selectTypeName($idtype);
        if(!$name){
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Error get Type name - ID: '.$idtype.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $rsItem = $this->dbService->selectItens($idtype);
        if(!$rsItem){
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Error get Type\'s item list - ID: '.$idtype.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }
        $tabBody = "<table class='table'>";

        while(!$rsItem->EOF) {
            $checkedItem = $rsItem->fields['item_status'] == 'A' ? 'checked=checked' : '';
            $tabBody .= "<tr>
                                <td>
                                    <div class='i-checks'>
                                        <input type='checkbox' class='checkItem' name='item_{$rsItem->fields['item']}' value='{$rsItem->fields['item']}' id='item_{$rsItem->fields['item']}' {$checkedItem}>&nbsp;
                                        <span>{$rsItem->fields['item_name']}</span>
                                    </div>
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>
                                    <div class='pull-right'>
                                        <a href='javascript:;' onclick='editItem({$rsItem->fields['item']})' class='btn btn-default btn-xs tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->getLanguageWord('Item_edit')}'><i class='fa fa-edit'></i></a>
                                    </div>                                    
                                </td>
                                <td>
                                    <div class='pull-right'>
                                        <a href='javascript:;' onclick='viewItem({$rsItem->fields['item']})' class='btn btn-default btn-xs  tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->getLanguageWord('tooltip_list_services')}'><i class='fa fa-bars'></i></a>
                                    </div>
                                </td>
                            </tr>";

            $rsItem->MoveNext();
        }

        $tabBody .= "</table>";

        $aRet = array(
            "title" => $name,
            "tabList" => $tabBody
        );

        echo json_encode($aRet);

    }

    public function serviceList()
    {
        $iditem = $_POST['id'];

        $name = $this->dbService->selectItemName($iditem);
        if(!$name){
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Error get Type name - ID: '.$iditem.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $rsService = $this->dbService->selectServices($iditem);
        if(!$rsService){
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Error get Item\'s services list - ID: '.$iditem.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }
        $tabBody = "<table class='table'>";

        while(!$rsService->EOF) {
            $checkedService = $rsService->fields['service_status'] == 'A' ? 'checked=checked' : '';
            $tabBody .= "<tr>
                                <td>
                                    <div class='i-checks'>
                                        <input type='checkbox' class='checkService' name='item_{$rsService->fields['service']}' value='{$rsService->fields['service']}' id='item_{$rsService->fields['service']}' {$checkedService}>&nbsp;
                                        <span>{$rsService->fields['service_name']}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class='pull-right'>
                                        <a href='javascript:;' onclick='editService({$rsService->fields['service']})' class='btn btn-default btn-xs tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->getLanguageWord('Service_edit')}'><i class='fa fa-edit'></i></a>
                                    </div>                                    
                                </td>
                            </tr>";

            $rsService->MoveNext();
        }

        $tabBody .= "</table>";

        $aRet = array(
            "title" => $name,
            "tabList" => $tabBody
        );

        echo json_encode($aRet);

    }

    public function modalItem()
    {
        $viewItem = $_POST['viewItem'];

        if($viewItem == 'upd'){
            $rsItem = $this->dbService->selectItemEdit($_POST['iditem']);
            if (!$rsItem) {
                if($this->log)
                    $this->logIt('Get Item data ID: '.$_POST['iditem'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $aRet = array(
                'name' => $rsItem->fields['name'],
                'status' => $rsItem->fields['status'],
                'selec' => $rsItem->fields['selected'],
                'classify' => $rsItem->fields['classify']
            );
        }else{
            $aRet = array(
                'name' => '',
                'status' => 'A',
                'selec' => 0,
                'classify' => 0
            );
        }

        echo json_encode($aRet);

    }

    function createItem()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idtype = $_POST['idtype_item'];
        $name = addslashes($_POST['modal_item_name']);
        $vardefault = isset($_POST['checkItemDefault']) ? 1 : 0;
        $status = isset($_POST['checkItemAvailable']) ? 'A' : 'N';
        $classify = isset($_POST['checkItemClassification']) ? 1 : 0;

        $this->dbService->BeginTrans();

        if($vardefault == 1){
            $clear = $this->dbService->clearDefaultItem($idtype);
            if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Item - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
        }

        $ret = $this->dbService->insertItem($name, $vardefault, $status, $classify, $idtype);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Item  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    function updateItem()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = addslashes($_POST['iditem_modal']);
        $name = addslashes($_POST['modal_item_name']);
        $vardefault = isset($_POST['checkItemDefault']) ? 1 : 0;
        $status = isset($_POST['checkItemAvailable']) ? 'A' : 'N';
        $classify = isset($_POST['checkItemClassification']) ? 1 : 0;
        $idtype = $_POST['idtype_item'];

        $this->dbService->BeginTrans();

        if($vardefault == 1){
            $clear = $this->dbService->clearDefaultItem($idtype);
            if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Item ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
        }

        $ret = $this->dbService->updateItem($id, $name, $vardefault, $status, $classify);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Update Item ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    public function modalService()
    {
        $viewService = $_POST['viewService'];

        if($viewService == 'upd'){
            $rsService = $this->dbService->selectServiceEdit($_POST['idservice']);
            if (!$rsService) {
                if($this->log)
                    $this->logIt('Get Service data ID: '.$_POST['idservice'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $bus = array(
                'name' => $rsService->fields['name'],
                'days' => $rsService->fields['days_attendance'],
                'limit_time' => $rsService->fields['hours_attendance'],
                'ind_type_time'  => $rsService->fields['ind_hours_minutes'],
                'status' => $rsService->fields['status'],
                'selec' => $rsService->fields['selected'],
                'classify' => $rsService->fields['classify']
            );

            $retGroup =  $this->dbService->selectServiceGroup($_POST['idservice']);
            if (!$rsService) {
                if($this->log)
                    $this->logIt('Get Service data ID: '.$_POST['idservice'].' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            $defaultGroup =  $retGroup->fields['idgroup'];

            $defaultPriority = $rsService->fields['idpriority'];
        }else{
            $bus = array(
                'name' => '',
                'status' => 'A',
                'selec' => 0,
                'classify' => 0
            );

            $defaultGroup = 0;
            $defaultPriority = 0;
        }

        $arrGroup = $this->_comboGroups();
        $grpOptions = '';
        $grpOptions .= "<option value=''>".$this->getLanguageWord('Select_group')."</option>";

        foreach ( $arrGroup['ids'] as $indexKey => $indexValue ) {
            $grpOptions .= "<option value='$indexValue'>".$arrGroup['values'][$indexKey]."</option>";
        }

        $arrPriority = $this->_comboPriority();
        $prioOptions = '';
        $prioOptions .= "<option value=''>".$this->getLanguageWord('Select_priority')."</option>";

        foreach ( $arrPriority['ids'] as $indexKey => $indexValue ) {
            $prioOptions .= "<option value='$indexValue'>".$arrPriority['values'][$indexKey]."</option>";
        }

        $aRet = array(
            'cmbGroup' => $grpOptions,
            'defaultGroup' => $defaultGroup,
            'cmbPriority' => $prioOptions,
            'defaultPriority' => $defaultPriority,
            'serviceData' => $bus
        );

        echo json_encode($aRet);

    }

    function createService()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $iditem = $_POST['iditem_service'];
        $name = addslashes($_POST['modal_service_name']);
        $vardefault = isset($_POST['checkServDefault']) ? 1 : 0;
        $status = isset($_POST['checkServAvailable']) ? 'A' : 'N';
        $classify = isset($_POST['checkServClassification']) ? 1 : 0;
        $priority =  $_POST['modal_cmbPriority'];
        $days = $_POST['limit_days'];
        $limit_time = $_POST['limit_time'];
        $time = $_POST['time'];
        $group = $_POST['modal_cmbGroup'];

        $this->dbService->BeginTrans();

        if($vardefault == 1){
            $clear = $this->dbService->clearDefaultService($iditem);
            if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Service - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
        }

        $ret = $this->dbService->serviceInsert($name, $vardefault, $status, $classify, $iditem, $priority, $time, $days, $limit_time);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Service  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $max = $this->dbService->selectMax();
        if (!$max) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Service  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $max = $max->fields['last'];
        $grpInsert = $this->dbService->serviceGroupInsert($max, $group);
        if(!$grpInsert){
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Service  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    function updateService()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $id = addslashes($_POST['idservice_modal']);
        $name = addslashes($_POST['modal_service_name']);
        $vardefault = isset($_POST['checkServDefault']) ? 1 : 0;
        $status = isset($_POST['checkServAvailable']) ? 'A' : 'N';
        $classify = isset($_POST['checkServClassification']) ? 1 : 0;
        $iditem = $_POST['iditem_service'];
        $priority =  $_POST['modal_cmbPriority'];
        $days = $_POST['limit_days'];
        $limit_time = $_POST['limit_time'];
        $time = $_POST['time'];
        $group = $_POST['modal_cmbGroup'];

        $this->dbService->BeginTrans();

        if($vardefault == 1){
            $clear = $this->dbService->clearDefaultService($iditem);
            if(!$clear){
                $this->dbService->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Service - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
        }

        $ret = $this->dbService->updateService($id, $name, $vardefault, $status, $classify, $priority, $time, $days, $limit_time);

        if (!$ret) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Update Service ID: '.$id.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retGrp = $this->dbService->selectPrevGroup($id);
        if (!$retGrp) {
            $this->dbService->RollbackTrans();
            if($this->log)
                $this->logIt('Update Service ID: '.$id.' get actual Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($group != $retGrp->fields['idgroup']){
            $grpUpdate = $this->dbService->updateServiceGroup($id, $group);
            if(!$grpUpdate){
                $this->dbService->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Service  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $this->dbService->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    public function modalConfApprove()
    {
        $rsAreas = $this->dbService->selectAreas();
        $options = "<option value=''>".$this->getLanguageWord('Select')."</option>";

        while(!$rsAreas->EOF) {
            $options .= "<optgroup label='{$rsAreas->fields['name']}'>";

            $rsTypes = $this->dbService->getTypeFromAreas($rsAreas->fields['idarea']);

            while(!$rsTypes->EOF) {
                $options .= "<option value='{$rsTypes->fields['type']}'>{$rsTypes->fields['type_name']}</option>";

                $rsTypes->MoveNext();
            }

            $options .= "</optgroup>";
            $rsAreas->MoveNext();

        }


        $aRet = array(
            'confCmbType' => $options
        );

        echo json_encode($aRet);

    }

    public function getUsersApprove(){
        $iditem = $_POST['iditem'];
        $idservice = $_POST['idservice'];

        $rsUsers = $this->dbRules->getUsers($iditem, $idservice);
        $options = "";

        while(!$rsUsers->EOF) {
            $options .= "<option value='{$rsUsers->fields['idperson']}'>{$rsUsers->fields['name']}</option>";
            $rsUsers->MoveNext();
        }

        $rsUsersApprv = $this->dbRules->getUsersApprove($iditem, $idservice);
        $tbody = "";
        $flgRecalc = 0;
        $i = 1;

        while(!$rsUsersApprv->EOF) {
            $tbody .= "<tr>
                            <td>
                                {$rsUsersApprv->fields['name']}
                                <input type='hidden' class='apprUser' name='apprUser[]' id='apprUser_{$rsUsersApprv->fields['idperson']}' value='{$rsUsersApprv->fields['idperson']}'>
                            </td>
                            <td>
                                <a href='#' class='btn btn-success btn-up'><i class='fa fa-sort-up'></i></a>
                            </td>
                            <td>
                                <a href='#' class='btn btn-primary btn-down'><i class='fa fa-sort-down'></i></a>
                            </td>
                            <td>
                                <a href='#' class='btn btn-danger btn-remove'><i class='fa fa-user-times'></i></a>
                            </td>
                        </tr>";
            
            if($rsUsersApprv->fields['fl_recalculate'] == 1) 
                $flgRecalc = 1;

            $i++;
            $rsUsersApprv->MoveNext();

        }

        $aRet = array(
            "confCmbUsers" => $options,
            "usersApprvlist" => $tbody,
            "flgRecalc" => $flgRecalc
        );

        echo json_encode($aRet);

    }

    function saveConfApproval()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idType = $_POST['confCmbType'];
        $idItem = $_POST['confCmbItem'];
        $idService = $_POST['confCmbService'];
        $arrAval = $_POST['apprUser'];
        $flgRecalc = isset($_POST['checkRecalculate']) ? 1 : 0;
        $error = 0;

        $this->dbRules->BeginTrans();

        $retDel = $this->dbRules->deleteUsersApprove($idItem, $idService);
        if (!$retDel) {
            $this->dbRules->RollbackTrans();
            if($this->log)
                $this->logIt('Save Conf. Approve - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if(sizeof($arrAval) > 0){
            $i = 1;
            foreach($arrAval as $idperson){
                $ret = $this->dbRules->insertUsersApprove($idItem, $idService, $idperson, $i, $flgRecalc);
                if (!$ret) {
                    $this->dbRulese->RollbackTrans();
                    if($this->log)
                        $this->logIt('Save Conf. Approve  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                $i++;
            }
        }

        $this->dbRules->CommitTrans();

        $aRet = array(
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

}