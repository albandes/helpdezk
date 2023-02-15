<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;
use App\modules\helpdezk\dao\mysql\ticketRulesDAO;
use App\modules\admin\dao\mysql\personDAO;

use App\modules\helpdezk\models\mysql\hdkServiceModel;
use App\modules\helpdezk\models\mysql\ticketRulesModel;
use App\modules\admin\models\mysql\personModel;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;

/**
 * hdkService
 */
class hdkService extends Controller
{           
    /**
     * @var int
     */
    protected $programId;

    /**
     * @var array
     */
    protected $aPermissions;

    public function __construct()
    {
        parent::__construct();

        $this->appSrc->_sessionValidate();
        
        $this->programId = $this->appSrc->_getProgramIdByName(__CLASS__);
        $this->aPermissions = $this->appSrc->_getUserPermissionsByProgram($_SESSION['SES_COD_USUARIO'],$this->programId);
    }
        
    /**
     * en_us Renders the service home screen template
     * pt_br Renderiza o template da tela de home de serviço
     *
     * @return void
     */
    public function index()
    {
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();
        
        $params = $this->makeScreenService();
        
        $params['areaTypeList'] = $this->makeAreaTypeList();
		
		$this->view('helpdezk','service',$params);
    }

    public function makeScreenService($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte'; //subir imagem
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        if($option=='upd'){
            $params['id'] = $obj->getIdDepartment();
            $params['department'] = $obj->getDepartment();
            $params['idcompany'] = $obj->getIdCompany();
        }
        
        return $params;
    }

    /**
     * en_us Makes services list in HTML to display on the screen
     * pt_br Faz lista de serviços em HTML para exibir na tela
     *
     * @return string
     */
    function makeAreaTypeList()
    {
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
        
        $ret =  $serviceDAO->fetchAreas($serviceModel);
        if(!$ret['status']){
            $this->logger->error("Error trying get areas. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $aAreas = $ret['push']['object']->getAreaList();
        $tabBody = "<table class='table'>";

        foreach($aAreas as $key=>$val){
            $checkedArea = $val['status'] == 'A' ? 'checked=checked' : '';

            $tabBody .= "<tr>
                            <td colspan='3'>
                                <div class='i-checks'>
                                    <input type='checkbox' class='checkArea' name='area_{$val['idarea']}' value='{$val['idarea']}' id='area_{$val['idarea']}' {$checkedArea}>&nbsp; 
                                    <span class='text-service'>{$val['name']}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <a href='javascript:;' onclick='deleteTarget({$val['idarea']},\"area\")' class='btn btn-danger btn-sm  tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('tooltip_delete_area')}'><i class='fa fa-trash-alt'></i></a>
                                </div>
                            </td>
                            <td></td>
                        </tr>";
            
            $ret['push']['object']->setIdArea($val['idarea']);
            $retType = $serviceDAO->fetchAllTypesByArea($ret['push']['object']);
            if(!$retType['status']){
                $this->logger->error("Error trying get types. Error: {$retType['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return false;
            }

            $aTypes = $ret['push']['object']->getTypeList();
            foreach($aTypes as $k=>$v){
                $checkedType = $v['status'] == 'A' ? 'checked=checked' : '';
                $tabBody .= "<tr>
                                <td></td>
                                <td>
                                    <div class='i-checks'>
                                        <input type='checkbox' class='checkType' name='type_{$v['idtype']}' value='{$v['idtype']}' id='type_{$v['idtype']}' {$checkedType}>&nbsp;
                                        <span>{$v['name']}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href='javascript:;' onclick='editType({$v['idtype']})' class='btn btn-default btn-sm tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('Type_edit')}'><i class='fa fa-edit'></i></a>
                                    </div>                                    
                                </td>
                                <td>
                                    <div>
                                    <a href='javascript:;' onclick='viewType({$v['idtype']})' class='btn btn-default btn-sm  tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('tooltip_list_items')}'><i class='fa fa-bars'></i></a>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                    <a href='javascript:;' onclick='deleteTarget({$v['idtype']},\"type\")' class='btn btn-danger btn-sm  tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('tooltip_delete_type')}'><i class='fa fa-trash-alt'></i></a>
                                    </div>
                                </td>
                            </tr>";
            }

        }

        $tabBody .= "</table>";

        return $tabBody;

    }
    
    /**
     * en_us Changes area's status
     * pt_br Altera o status da área
     *
     * @return void
     */
    public function changeAreaStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
   
        $serviceModel->setIdArea(trim(strip_tags($_POST['areaId'])))
                     ->setStatus(trim(strip_tags($_POST['newStatus'])));

        $upd = $serviceDAO->updateAreaStatus($serviceModel);
        if($upd['status']){
            $st = true;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated') : $this->translator->translate('Alert_deactivated');
            $this->logger->info("Area # {$_POST['areaId']} status was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated_error') : $this->translator->translate('Alert_deactivated_error');
            $this->logger->error("Error trying update area status. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }   
              
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Changes type's status
     * pt_br Altera o status do tipo
     *
     * @return void
     */
    public function changeTypeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
   
        $serviceModel->setIdType(trim(strip_tags($_POST['typeId'])))
                     ->setStatus(trim(strip_tags($_POST['newStatus'])));

        $upd = $serviceDAO->updateTypeStatus($serviceModel);
        if($upd['status']){
            $st = true;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated') : $this->translator->translate('Alert_deactivated');
            $this->logger->info("Type # {$_POST['typeId']} status was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated_error') : $this->translator->translate('Alert_deactivated_error');
            $this->logger->error("Error trying update type status. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }   
              
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Changes item's status
     * pt_br Altera o status do item
     *
     * @return void
     */
    public function changeItemStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
   
        $serviceModel->setIdItem(trim(strip_tags($_POST['itemId'])))
                     ->setStatus(trim(strip_tags($_POST['newStatus'])));

        $upd = $serviceDAO->updateItemStatus($serviceModel);
        if($upd['status']){
            $st = true;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated') : $this->translator->translate('Alert_deactivated');
            $this->logger->info("Item # {$_POST['itemId']} status was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated_error') : $this->translator->translate('Alert_deactivated_error');
            $this->logger->error("Error trying update item status. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }   
              
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Changes item's status
     * pt_br Altera o status do item
     *
     * @return void
     */
    public function changeServiceStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
   
        $serviceModel->setIdService(trim(strip_tags($_POST['serviceId'])))
                     ->setStatus(trim(strip_tags($_POST['newStatus'])));

        $upd = $serviceDAO->updateServiceStatus($serviceModel);
        if($upd['status']){
            $st = true;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated') : $this->translator->translate('Alert_deactivated');
            $this->logger->info("Service # {$_POST['serviceId']} status was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = ($_POST['newStatus'] == 'A') ? $this->translator->translate('Alert_activated_error') : $this->translator->translate('Alert_deactivated_error');
            $this->logger->error("Error trying update service status. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }   
              
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Lists items 
     * pt_br Lista itens
     *
     * @return void
     */
    public function itemList()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
   
        $serviceModel->setIdType(trim(strip_tags($_POST['typeId'])));

        $retInfo = $serviceDAO->getType($serviceModel);
        if(!$retInfo['status']){
            $this->logger->error("Error trying get type's info. Error: {$retInfo['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false, "message"=>$this->translator->translate('generic_error_msg'),"title"=>"","itemList"=>""));
            exit;
        }

        $title = $retInfo['push']['object']->getTypeName();

        $retItems = $serviceDAO->fetchAllItemsByType($retInfo['push']['object']);
        if($retItems['status']){
            $aItem = $retItems['push']['object']->getItemList();
            $msg = "";
            $itemList = "<table class='table'>";

            foreach($aItem as $k=>$v){
                $checkedItem = $v['status'] == 'A' ? 'checked=checked' : '';
                $itemList .= "<tr>
                                <td>
                                    <div class='i-checks'>
                                        <input type='checkbox' class='checkItem' name='item_{$v['iditem']}' value='{$v['iditem']}' id='item_{$v['iditem']}' {$checkedItem}>&nbsp;
                                        <span>{$v['name']}</span>
                                    </div>
                                </td>
                                <td>&nbsp;</td>
                                <td>
                                    <div class='text-end'>
                                        <a href='javascript:;' onclick='editItem({$v['iditem']})' class='btn btn-default btn-sm tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('Item_edit')}'><i class='fa fa-edit'></i></a>
                                    </div>                                    
                                </td>
                                <td>
                                    <div class='text-end'>
                                        <a href='javascript:;' onclick='viewItem({$v['iditem']})' class='btn btn-default btn-sm tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('tooltip_list_services')}'><i class='fa fa-bars'></i></a>
                                    </div>
                                </td>
                                <td>
                                    <div class='text-end'>
                                        <a href='javascript:;' onclick='deleteTarget({$v['iditem']},\"item\")' class='btn btn-danger btn-sm tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('tooltip_delete_item')}'><i class='fa fa-trash-alt'></i></a>
                                    </div>
                                </td>
                            </tr>";
            }

            $itemList .= "</table>";

            $this->logger->info("Item list was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $msg = "";
            $itemList = "";
            $this->logger->error("Error getting item list. Error: {$retItems['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }   
              
        
        $aRet = array(
            "success"   => true,
            "message"   => $msg,
            "title"     => $title,
            "itemList"  => $itemList
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Lists services 
     * pt_br Lista serviços
     *
     * @return void
     */
    public function serviceList()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();
   
        $serviceModel->setIdItem(trim(strip_tags($_POST['itemId'])));

        $retInfo = $serviceDAO->getItem($serviceModel);
        if(!$retInfo['status']){
            $this->logger->error("Error trying get item's info. Error: {$retInfo['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false, "message"=>$this->translatyor->translate('generic_error_msg'),"title"=>"","itemList"=>""));
            exit;
        }

        $title = $retInfo['push']['object']->getItemName();

        $retServices = $serviceDAO->fetchAllServicesByItem($retInfo['push']['object']);
        if($retServices['status']){
            $aItem = $retServices['push']['object']->getServiceList();
            $msg = "";
            $serviceList = "<table class='table'>";

            foreach($aItem as $k=>$v){
                $checkedService = $v['status'] == 'A' ? 'checked=checked' : '';
                $serviceList .= "<tr>
                                    <td>
                                        <div class='i-checks'>
                                            <input type='checkbox' class='checkService' name='service_{$v['idservice']}' value='{$v['idservice']}' id='service_{$v['idservice']}' {$checkedService}>&nbsp;
                                            <span>{$v['name']}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class='pull-right'>
                                            <a href='javascript:;' onclick='editService({$v['idservice']})' class='btn btn-default btn-sm tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('Service_edit')}'><i class='fa fa-edit'></i></a>
                                        </div>                                    
                                    </td>
                                    <td>
                                        <div class='pull-right'>
                                            <a href='javascript:;' onclick='deleteTarget({$v['idservice']},\"service\")' class='btn btn-danger btn-sm tooltip-buttons' data-toggle='tooltip' data-placement='top' title='{$this->translator->translate('tooltip_delete_service')}'><i class='fa fa-trash-alt'></i></a>
                                        </div>                                    
                                    </td>
                                </tr>";
            }

            $serviceList .= "</table>";

            $this->logger->info("Service list was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $msg = "";
            $serviceList = "";
            $this->logger->error("Error getting service list. Error: {$retServices['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }   
              
        
        $aRet = array(
            "success"   => true,
            "message"   => $msg,
            "title"     => $title,
            "serviceList"  => $serviceList
        );

        echo json_encode($aRet);
    }
    
    /**
     * en_us Renders the area's registration modal
     * pt_br Renderiza o modal de cadastro da área
     *
     * @return void
     */
    function modalArea()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $tbody = $this->makeAreaList();

        if(!$tbody){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
            $tbody = "";
        }else{
            $st = true;
            $msg = "";
        }
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
            "areaList"  => $tbody

        );

        echo json_encode($aRet);
    }

    /**
     * en_us Makes services list in HTML to display on the screen
     * pt_br Faz lista de serviços em HTML para exibir na tela
     *
     * @return string
     */
    function makeAreaList()
    {
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $ret = $serviceDAO->fetchAreas($serviceModel);
        if(!$ret['status']){
            $this->logger->error("Error trying get areas. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $aAreas = $ret['push']['object']->getAreaList();

        foreach($aAreas as $k=>$v){
            $checkedArea = $v['status'] == 'A' ? 'checked=checked' : '';
            $tbody .= " <tr>
                            <td class='text-start'>
                                <div class='i-checks'>
                                    <input type='checkbox' class='checkAreaModal' name='areaModal_{$v['idarea']}' value='{$v['idarea']}' id='areaModal_{$v['idarea']}' {$checkedArea}>&nbsp; 
                                    <span class='text-service'>{$v['name']}</span>
                                </div>
                            </td>
                            <td>
                                <a href='javascript:;' onclick='editArea({$v['idarea']})' class='btn btn-default btn-sm' data-toggle='tooltip'><i class='fa fa-edit'></i></a>
                            </td>
                        </tr>";
        }

        return $tbody;
    }

    /**
     * en_us Check if the area has already been registered before
     * pt_br Verifica se a área já foi cadastrada anteriormente
     *
     * @return void
     */
    function checkExistsArea()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $keyword = (isset($_POST['modal-area-name'])) ? addslashes(trim(strip_tags($_POST['modal-area-name']))) : addslashes(trim(strip_tags($_POST['modal-upd-area-name'])));
        $where = "WHERE `name` = '{$keyword}'";
        $where .= (isset($_POST['areaId'])) ? " AND idarea != {$_POST['areaId']}" : "";

        $check =  $serviceDAO->queryAreas($where);
        if(!$check['status']){
            return false;
        }
        
        $checkObj = $check['push']['object']->getAreaList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('area_exists'));
        }else{
            echo json_encode(true);
        }
    }

    /**
     * en_us Write the area's data into DB
     * pt_br Grava os dados da área no BD
     *
     * @return void
     */
    function createArea()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setAreaName(trim(strip_tags($_POST['areaName'])))
                     ->setFlagDefault($_POST['flagDefault']);

        $ins = $serviceDAO->saveArea($serviceModel);
        if(!$ins['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $areaList = "";
            $areaTypeList = "";

            $this->logger->error("Error trying save area data. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $retArea = $this->makeAreaList();
            $retAreaType = $this->makeAreaTypeList();

            $areaList = ($retArea && !empty($retArea)) ? $retArea : "";
            $areaTypeList = ($retAreaType && !empty($retAreaType)) ? $retAreaType : "";

            $this->logger->info("A new area was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "areaList"      => $areaList,
            "areaTypeList"  => $areaTypeList
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the area's edition modal
     * pt_br Renderiza o modal de edição da área
     *
     * @return void
     */
    function modalAreaUpdate()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdArea(trim(strip_tags($_POST['areaId'])));

        $ret = $serviceDAO->getArea($serviceModel);

        if(!$ret['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
            $areaName = "";
            $areaDefault = "";
            $this->logger->error("Error trying get area data. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            $areaName = $ret['push']['object']->getAreaName();
            $areaDefault = $ret['push']['object']->getFlagDefault();
            $this->logger->info("Area {$_POST['areaId']} data found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "areaName"      => $areaName,
            "areaDefault"   => $areaDefault
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates the area's data into DB
     * pt_br Atualiza os dados da área no BD
     *
     * @return void
     */
    function updateArea()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdArea($_POST['areaId'])
                     ->setAreaName(trim(strip_tags($_POST['areaName'])))
                     ->setFlagDefault($_POST['flagDefault']);

        $upd = $serviceDAO->saveUpdateArea($serviceModel);
        if(!$upd['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying update area data. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("Area was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the type's registration modal
     * pt_br Renderiza o modal de cadastro de tipo
     *
     * @return void
     */
    function modalType()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkSrc = new hdkServices();

        if($_POST['viewType'] == 'upd'){
            $serviceDAO = new hdkServiceDAO();
            $serviceModel = new hdkServiceModel();
   
            $serviceModel->setIdType($_POST['typeId']);

            $ret = $serviceDAO->getType($serviceModel);
            if(!$ret['status']){
                $this->logger->error("Error trying get type's info. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = false;
                $msg = $this->translator->translate('generic_error_msg');
                $typeName = "";
                $typeStatus = "";
                $flagDefault = "";
                $flagClassify = "";
                $areaDefault = "";
            }else{
                $this->logger->info("Type # {$_POST['typeId']} data found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = true;
                $msg = "";
                $typeName = $ret['push']['object']->getTypeName();
                $typeStatus = $ret['push']['object']->getStatus();
                $flagDefault = $ret['push']['object']->getFlagDefault();
                $flagClassify = $ret['push']['object']->getFlagClassify();
                $areaDefault = $ret['push']['object']->getIdArea();
            }
        }else{
            $st = true;
            $msg = "";
            $typeName = "";
            $typeStatus = "A";
            $flagDefault = "";
            $flagClassify = "";
            $areaDefault = "";
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "cmbArea"       => $hdkSrc->_comboAllAreasHtml(),
            "areaDefault"   => $areaDefault,
            "typeName"      => $typeName,
            "typeStatus"    => $typeStatus,
            "flagDefault"   => $flagDefault,
            "flagClassify"  => $flagClassify
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the type has already been registered before
     * pt_br Verifica se o tipo já foi cadastrado anteriormente
     *
     * @return void
     */
    function checkExistsType()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $keyword = addslashes(trim(strip_tags($_POST['modal-type-name'])));
        $where = "WHERE idarea = {$_POST['areaId']} AND `name` = '{$keyword}'";
        $where .= (isset($_POST['typeId']) && !empty($_POST['typeId']) && $_POST['typeId'] > 0) ? " AND idtype != {$_POST['typeId']}" : "";

        $check =  $serviceDAO->queryTypes($where);
        if(!$check['status']){
            return false;
        }
        
        $checkObj = $check['push']['object']->getTypeList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('alert_type_exists'));
        }else{
            echo json_encode(true);
        }
    }

    /**
     * en_us Write the type's data into DB
     * pt_br Grava os dados do tipo no BD
     *
     * @return void
     */
    function createType()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdArea($_POST['areaId'])
                     ->setTypeName(trim(strip_tags($_POST['typeName'])))
                     ->setStatus(($_POST['flagAvaliable'] == 1) ? 'A' : 'N')
                     ->setFlagDefault($_POST['flagDefault'])
                     ->setFlagClassify($_POST['flagClassify']);

        $ins = $serviceDAO->saveType($serviceModel);
        if(!$ins['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying save type data. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("A new type was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates the type's data into DB
     * pt_br Atualiza os dados do tipo no BD
     *
     * @return void
     */
    function updateType()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdType($_POST['typeId'])
                     ->setIdArea($_POST['areaId'])
                     ->setTypeName(trim(strip_tags($_POST['typeName'])))
                     ->setStatus(($_POST['flagAvaliable'] == 1) ? 'A' : 'N')
                     ->setFlagDefault($_POST['flagDefault'])
                     ->setFlagClassify($_POST['flagClassify']);

        $upd = $serviceDAO->saveUpdateType($serviceModel);
        if(!$upd['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying update type data. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("Type was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the item's registration modal
     * pt_br Renderiza o modal de cadastro de item
     *
     * @return void
     */
    function modalItem()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkSrc = new hdkServices();

        if($_POST['viewItem'] == 'upd'){
            $serviceDAO = new hdkServiceDAO();
            $serviceModel = new hdkServiceModel();
   
            $serviceModel->setIdItem($_POST['itemId']);

            $ret = $serviceDAO->getItem($serviceModel);
            if(!$ret['status']){
                $this->logger->error("Error trying get item's info. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = false;
                $msg = $this->translator->translate('generic_error_msg');
                $itemName = "";
                $itemStatus = "";
                $flagDefault = "";
                $flagClassify = "";
            }else{
                $this->logger->info("Item # {$_POST['itemId']} data found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = true;
                $msg = "";
                $itemName = $ret['push']['object']->getItemName();
                $itemStatus = $ret['push']['object']->getStatus();
                $flagDefault = $ret['push']['object']->getFlagDefault();
                $flagClassify = $ret['push']['object']->getFlagClassify();
            }
        }else{
            $st = true;
            $msg = "";
            $itemName = "";
            $itemStatus = "A";
            $flagDefault = "";
            $flagClassify = "";
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "itemName"      => $itemName,
            "itemStatus"    => $itemStatus,
            "itemDefault"   => $flagDefault,
            "itemClassify"  => $flagClassify
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the item has already been registered before
     * pt_br Verifica se o item já foi cadastrado anteriormente
     *
     * @return void
     */
    function checkExistsItem()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $keyword = addslashes(trim(strip_tags($_POST['modal-item-name'])));
        $where = "WHERE idtype = {$_POST['typeId']} AND `name` = '{$keyword}'";
        $where .= (isset($_POST['itemId']) && !empty($_POST['itemId']) && $_POST['itemId'] > 0) ? " AND iditem != {$_POST['itemId']}" : "";

        $check =  $serviceDAO->queryItems($where);
        if(!$check['status']){
            return false;
        }
        
        $checkObj = $check['push']['object']->getItemList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('alert_item_exists'));
        }else{
            echo json_encode(true);
        }
    }

    /**
     * en_us Write the item's data into DB
     * pt_br Grava os dados do item no BD
     *
     * @return void
     */
    function createItem()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdType($_POST['typeId'])
                     ->setItemName(trim(strip_tags($_POST['itemName'])))
                     ->setStatus(($_POST['flagAvaliable'] == 1) ? 'A' : 'N')
                     ->setFlagDefault($_POST['flagDefault'])
                     ->setFlagClassify($_POST['flagClassify']);

        $ins = $serviceDAO->saveItem($serviceModel);
        if(!$ins['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying save item data. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("A new item was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates the item's data into DB
     * pt_br Atualiza os dados do item no BD
     *
     * @return void
     */
    function updateItem()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdItem($_POST['itemId'])
                     ->setIdType($_POST['typeId'])
                     ->setItemName(trim(strip_tags($_POST['itemName'])))
                     ->setStatus(($_POST['flagAvaliable'] == 1) ? 'A' : 'N')
                     ->setFlagDefault($_POST['flagDefault'])
                     ->setFlagClassify($_POST['flagClassify']);

        $upd = $serviceDAO->saveUpdateItem($serviceModel);
        if(!$upd['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying update item data. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("Item was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the service's registration modal
     * pt_br Renderiza o modal de cadastro de serviço
     *
     * @return void
     */
    function modalService()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkSrc = new hdkServices();

        if($_POST['viewService'] == 'upd'){
            $serviceDAO = new hdkServiceDAO();
            $serviceModel = new hdkServiceModel();
   
            $serviceModel->setIdService($_POST['serviceId']);

            $ret = $serviceDAO->getService($serviceModel);
            if(!$ret['status']){
                $this->logger->error("Error trying get service's info. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = false;
                $msg = $this->translator->translate('generic_error_msg');
                $groupDefault = "";
                $priorityDefault = "";
                $serviceName = "";
                $limitDays = "";
                $limitTime = "";
                $timeType = "";
                $serviceStatus = "";
                $flagDefault = "";
                $flagClassify = "";
            }else{
                $this->logger->info("Service # {$_POST['serviceId']} data found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = true;
                $msg = "";
                $groupDefault = $ret['push']['object']->getIdGroup();
                $priorityDefault = $ret['push']['object']->getIdPriority();
                $serviceName = $ret['push']['object']->getServiceName();
                $limitDays = $ret['push']['object']->getLimitDays();
                $limitTime = $ret['push']['object']->getLimitTime();
                $timeType = $ret['push']['object']->getTimeType();
                $serviceStatus = $ret['push']['object']->getStatus();
                $flagDefault = $ret['push']['object']->getFlagDefault();
                $flagClassify = $ret['push']['object']->getFlagClassify();
            }
        }else{
            $st = true;
            $msg = "";
            $groupDefault = "";
            $priorityDefault = "";
            $serviceName = "";
            $limitDays = 0;
            $limitTime = 0;
            $timeType = 'H';
            $serviceStatus = "A";
            $flagDefault = "";
            $flagClassify = "";
        }
        
        $aRet = array(
            "success"           => $st,
            "message"           => $msg,
            "cmbGroup"          => $hdkSrc->_comboGroupHtml(),
            "groupDefault"      => $groupDefault,
            "cmbPriority"       => $hdkSrc->_comboPriorityHtml(),
            "priorityDefault"   => $priorityDefault,
            "serviceName"       => $serviceName,
            "limitDays"         => $limitDays,
            "limitTime"         => $limitTime,
            "timeType"          => $timeType,
            "serviceStatus"     => $serviceStatus,
            "serviceDefault"    => $flagDefault,
            "serviceClassify"   => $flagClassify
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the service has already been registered before
     * pt_br Verifica se o service já foi cadastrado anteriormente
     *
     * @return void
     */
    function checkExistsService()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $keyword = addslashes(trim(strip_tags($_POST['modal-service-name'])));
        $where = "AND iditem = {$_POST['itemId']} AND `name` = '{$keyword}'";
        $where .= (isset($_POST['serviceId']) && !empty($_POST['serviceId']) && $_POST['serviceId'] > 0) ? " AND a.idservice != {$_POST['serviceId']}" : "";

        $check =  $serviceDAO->queryServices($where);
        if(!$check['status']){
            return false;
        }
        
        $checkObj = $check['push']['object']->getServiceList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('alert_service_exists'));
        }else{
            echo json_encode(true);
        }
    }

    /**
     * en_us Write the service's data into DB
     * pt_br Grava os dados do serviço no BD
     *
     * @return void
     */
    function createService()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdItem($_POST['itemId'])
                     ->setServiceName(trim(strip_tags($_POST['serviceName'])))
                     ->setIdGroup($_POST['groupId'])
                     ->setIdPriority($_POST['priorityId'])
                     ->setLimitDays($_POST['limitDays'])
                     ->setLimitTime($_POST['limitTime'])
                     ->setTimeType($_POST['timeType'])
                     ->setAttendanceTime(0)
                     ->setStatus(($_POST['flagAvaliable'] == 1) ? 'A' : 'N')
                     ->setFlagDefault($_POST['flagDefault'])
                     ->setFlagClassify($_POST['flagClassify']);

        $ins = $serviceDAO->saveService($serviceModel);
        if(!$ins['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying save service data. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("A new service was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates the service's data into DB
     * pt_br Atualiza os dados do serviço no BD
     *
     * @return void
     */
    function updateService()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $serviceModel->setIdService($_POST['serviceId'])
                     ->setIdItem($_POST['itemId'])
                     ->setServiceName(trim(strip_tags($_POST['serviceName'])))
                     ->setIdGroup($_POST['groupId'])
                     ->setIdPriority($_POST['priorityId'])
                     ->setLimitDays($_POST['limitDays'])
                     ->setLimitTime($_POST['limitTime'])
                     ->setTimeType($_POST['timeType'])
                     ->setAttendanceTime(0)
                     ->setStatus(($_POST['flagAvaliable'] == 1) ? 'A' : 'N')
                     ->setFlagDefault($_POST['flagDefault'])
                     ->setFlagClassify($_POST['flagClassify']);

        $upd = $serviceDAO->saveUpdateService($serviceModel);
        if(!$upd['status']){
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');

            $this->logger->error("Error trying update service data. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            
            $this->logger->info("Service was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the area, type, item or srvice has link with tickets
     * pt_br Verifica se a área, tipo, item ou service possui vínculo com solicitações
     *
     * @return json
     */
    function checkDelete()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();

        $targetId = $_POST['targetId'];
        $targetType = $_POST['targetType'];

        switch ($targetType){
            case 'area':
                $where = "WHERE idarea = $targetId";
                $msg = $this->translator->translate('Alert_dont_delete_area');
                break;
            case 'type':
                $where = "WHERE idtype = $targetId";
                $msg = $this->translator->translate('Alert_dont_delete_type');
                break;
            case 'item':
                $where = "WHERE iditem = $targetId";
                $msg = $this->translator->translate('Alert_dont_delete_item');
                break;
            default:
                $where = "WHERE idservice = $targetId";
                $msg = $this->translator->translate('Alert_dont_delete_service');
                break;
        }

        $check =  $serviceDAO->queryTicketService($where);
        if(!$check['status']){
            $this->logger->error("Error trying get ticket linked with service. Error: {$check['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $checkObj = $check['push']['object']->getTicketList();
        
        if(count($checkObj) > 0){
            $allow = false;
        }else{
            $allow = true;
            $msg = "";
        }

        $aRet = array(
            "success"   => true,
            "message"   => $msg,
            "allow"     => $allow
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Deletes the area, type, item or srvice from DB
     * pt_br Deleta a área, tipo, item ou service do BD
     *
     * @return json
     */
    function deleteTarget()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $targetId = $_POST['targetId'];
        $targetType = $_POST['targetType'];

        switch ($targetType){
            case 'area':
                $serviceModel->setIdArea($targetId);
                $del = $serviceDAO->removeArea($serviceModel);
                $errorMsg = $this->translator->translate('alert_delete_area_failed');
                $successMsg = $this->translator->translate('alert_delete_area_success');
                break;
            case 'type':
                $serviceModel->setIdType($targetId);
                $del = $serviceDAO->removeType($serviceModel);
                $errorMsg = $this->translator->translate('alert_delete_type_failed');
                $successMsg = $this->translator->translate('alert_delete_type_success');
                break;
            case 'item':
                $serviceModel->setIdItem($targetId);
                $del = $serviceDAO->removeItem($serviceModel);
                $errorMsg = $this->translator->translate('alert_delete_item_failed');
                $successMsg = $this->translator->translate('alert_delete_item_success');
                break;
            default:
                $serviceModel->setIdService($targetId);
                $del = $serviceDAO->removeService($serviceModel);
                $errorMsg = $this->translator->translate('alert_delete_service_failed');
                $successMsg = $this->translator->translate('alert_delete_service_success');
                break;
        }

        if(!$del['status']){
            $this->logger->error("Error trying delete {$targetType}. Error: {$del['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $st = false;
            $msg = $errorMsg;
        }else{
            $st = true;
            $msg = $successMsg;
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the service's registration modal
     * pt_br Renderiza o modal de cadastro de serviço
     *
     * @return json
     */
    function modalConfigApproval()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $ret = $serviceDAO->fetchAreas($serviceModel);
        if(!$ret['status']){
            $this->logger->error("Error trying get areas. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $aAreas = $ret['push']['object']->getAreaList();
        $optionHtml = "<option value=''></option>";

        foreach($aAreas as $key=>$val){
            $optionHtml .= "<optgroup label='{$val['name']}'>";

            $ret['push']['object']->setIdArea($val['idarea']);
            $retType = $serviceDAO->fetchAllTypesByArea($ret['push']['object']);
            if(!$retType['status']){
                $this->logger->error("Error trying get types. Error: {$retType['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return false;
            }

            $aTypes = $ret['push']['object']->getTypeList();
            foreach($aTypes as $k=>$v){
                $optionHtml .= "<option value='{$v['idtype']}'>{$v['name']}</option>";
            }

            $optionHtml .= "</optgroup>";
        }

        $st = true;
        $msg = "";
        
        $aRet = array(
            "success"           => $st,
            "message"           => $msg,
            "typeOptions"       => $optionHtml
        );

        echo json_encode($aRet);
    }
    
    /**
     * en_us Returns approver's list
     * pt_br Retorna a lista de aprovadores
     *
     * @return json
     */
    function getApprovers()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();

        $ticketRulesModel->setItemId($_POST['itemId'])
                         ->setServiceId($_POST['serviceId']);

        $ret = $ticketRulesDAO->fetchApprovers($ticketRulesModel);
        if(!$ret['status']){
            $this->logger->error("Error trying get approvers. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $this->logger->info("Approvers get successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        $aApprovers = $ret['push']['object']->getGridList();
        $html = "";
        $flgRecalculate = 0;

        if(sizeof($aApprovers) > 0){
            foreach($aApprovers as $key=>$val){
                $html .= "<tr>
                            <td>
                                {$val['name']}
                                <input type='hidden' class='approver' name='approver[]' id='approver_{$val['idperson']}' value='{$val['idperson']}'>
                            </td>
                            <td>
                                <a href='#' class='btn btn-success btn-up'><i class='fa fa-sort-up'></i></a>
                                <a href='#' class='btn btn-primary btn-down'><i class='fa fa-sort-down'></i></a>
                            </td>
                            <td>
                                <a href='#' class='btn btn-danger btn-remove'><i class='fa fa-user-times'></i></a>
                            </td>
                        </tr>";
                        
                if($val['fl_recalculate'] == 1) 
                    $flgRecalculate = 1;
            }
    
            $st = true;
            $msg = "";
        }else{
            $st = false;
            $msg = "";
        }        
        
        $aRet = array(
            "success"           => $st,
            "message"           => $msg,
            "approverList"      => $html,
            "flgRecalculate"    => $flgRecalculate
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Returns approver's search data
     * pt_br Retorna os dados da pesquisa de aprovador
     *
     * @return json
     */
    function searchApprover()
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $aRet = array();
        
        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($_POST['keyword']))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "pipeLatinToUtf8(tbp.name) LIKE pipeLatinToUtf8('%{$v}%') OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = "AND ($keyword OR pipeLatinToUtf8(tbp.login) LIKE pipeLatinToUtf8('%{$searchStr}%')) AND tbp.idtypeperson IN(1,3) AND tbp.status = 'A'";
        $order = "ORDER BY `name`";
        $aRet = array();

        $ret = $personDAO->queryPersons($where,null,$order);  
        if($ret['status']){
            $users = $ret['push']['object']->getGridList();
            foreach($users as $k=>$v){
                $bus = array(
                    "id" => $v['idperson'],
                    "name" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        echo json_encode($aRet);
    }

    /**
     * en_us Returns approver's list
     * pt_br Retorna a lista de aprovadores
     *
     * @return json
     */
    function saveApprovalSetting()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();

        $ticketRulesModel->setItemId($_POST['modal-cmb-item'])
                         ->setServiceId($_POST['modal-cmb-service'])
                         ->setApproverList(isset($_POST['approver']) ? $_POST['approver'] : array())
                         ->setIsRecalculate(isset($_POST['modal-recalculate']) ? 1 : 0);

        $ret = $ticketRulesDAO->saveApprovalRule($ticketRulesModel);
        if(!$ret['status']){
            $this->logger->error("Error trying save approval rules for service # {$_POST['serviceId']}. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Approval rules was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $st = true;
            $msg = "";
        }       
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Returns approver's list
     * pt_br Retorna a lista de aprovadores
     *
     * @return json
     */
    function modalViewApprovers()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();
        $serviceDAO = new hdkServiceDAO();
        $serviceModel = new hdkServiceModel();

        $ret = $serviceDAO->fetchAllServices($serviceModel);
        if(!$ret['status']){
            $this->logger->error("Error trying get all services. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
            $content = "";
        }else{
            $this->logger->info("Services got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $aServices = $ret['push']['object']->getServiceList();
            $content = "";

            if(sizeof($aServices) <= 0){
                $st = false;
                $msg = $this->translator->translate('generic_error_msg');
            }else{
                $st = true;
                $msg = "";
                
                $curArea = "";
                $curService = "";
                foreach($aServices as $key=>$val){
                    if($val['area_name'] != $curArea){
                        $curArea = $val['area_name'];
                        $content .= "<tr><td colspan='4' class='text-service text-start' style='background-color: #e7eaec;'>{$val['area_name']}</td></tr>";
                    }

                    if($val['service_name'] != $curService){
                        $curService = $val['service_name'];
                        $approverList = "";
                        
                        $ticketRulesModel->setItemId($val['iditem'])
                                         ->setServiceId($val['idservice']);

                        $retApprovers = $ticketRulesDAO->fetchApprovers($ticketRulesModel);
                        if(!$retApprovers['status']){
                            $this->logger->error("Error trying get approvers. Error: {$retApprovers['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                            $approverList .= $this->translator->translate("no_approvers_recorded");
                        }else{
                            $this->logger->info("Approvers got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                            $aApprovers = $retApprovers['push']['object']->getGridList();
                            
                            if(sizeof($aApprovers) > 0){
                                foreach($aApprovers as $k=>$v){
                                    $approverList .= (empty($approverList)) ? "{$v['name']}": "<br>{$v['name']}";
                                }
                            }else{
                                $approverList .= $this->translator->translate("no_approvers_recorded");
                            }
                        }

                        $content .= "<tr>
                                        <td class='text-start'>{$val['type_name']}</td>
                                        <td class='text-start'>{$val['item_name']}</td>
                                        <td class='text-start'>{$val['service_name']}</td>
                                        <td class='text-start'>{$approverList}</td>
                                    </tr>";
                    }
                }
            }
        }       
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
            "content"   => $content 
        );

        echo json_encode($aRet);
    }

    
}