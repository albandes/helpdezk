<?php

namespace App\modules\helpdezk\src;

use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;
use App\modules\helpdezk\dao\mysql\ticketDAO;
use App\modules\helpdezk\dao\mysql\reasonDAO;
use App\modules\helpdezk\dao\mysql\expireDateDAO;
use App\modules\helpdezk\dao\mysql\groupDAO;

use App\modules\admin\models\mysql\moduleModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;
use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\helpdezk\models\mysql\reasonModel;
use App\modules\helpdezk\models\mysql\expireDateModel;
use App\modules\helpdezk\models\mysql\groupModel;

use App\src\appServices;
use App\src\localeServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class hdkServices
{
    /**
     * @var object
     */
    protected $hdklogger;
    
    /**
     * @var object
     */
    protected $hdkEmailLogger;

    public function __construct()
    {
        $appSrc = new appServices();
        
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);

        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);
        
        $this->hdklogger  = new Logger('helpdezk');
        $this->hdklogger->pushHandler($stream);

        // Clone the first one to only change the channel
        $this->hdkEmailLogger = $this->hdklogger->withName('email');
        
    }

    public function _makeNavHdk($params)
    {
        $moduleDAO = new moduleDAO();
        $appSrc = new appServices();
        $translator = new localeServices();

        $moduleModel = new moduleModel();
        $moduleModel->setUserID($_SESSION['SES_COD_USUARIO'])
                    ->setUserType($_SESSION['SES_TYPE_PERSON'])
                    ->setName('helpdezk');
        
        $params['featured_1'] = true;
        $params['lnk_featured_1'] = $_ENV['HDK_URL'] . '/helpdezk/hdkTicket/index';
        $params['featured_label_1'] = $translator->translate('Tck_title');

        $params['featured_2'] = true;
        $params['lnk_featured_2'] = 'javascript:;';
        $params['featured_label_2'] = $translator->translate('Tck_new_ticket');
        
        if($_SESSION['SES_TYPE_PERSON'] == 3){
            $params['featured_3'] = true;
            $params['lnk_featured_3'] = $_ENV['HDK_URL'] . '/helpdezk/hdkTicket/index/mytickets/1';
            $params['featured_label_3'] = $translator->translate('My_Tickets');
        }
        
        $retInfo = $moduleDAO->getModuleInfoByName($moduleModel); 
        if($retInfo['status']){
            $moduleInfo = $retInfo['push']['object'];
            $moduleModel->setIdModule($moduleInfo->getIdModule());
            $listRecords = $appSrc->_makeMenuByModule($moduleModel);
            
            $params['listMenu_1'] = $listRecords;
            $params['moduleLogo'] = ($moduleInfo->getIdModule() == 1) ? $aHeader['filename'] : $moduleInfo->getHeaderLogo();
            $params['modulePath'] = $moduleInfo->getPath();
        }

        return $params;
    }
    
    /**
     * _comboArea
     *
     * @return array
     */
    public function _comboArea(): array
    {
        $hdkServiceDAO = new hdkServiceDAO();
        $hdkServiceModel = new hdkServiceModel();
        
        $ret = $hdkServiceDAO->fetchArea($hdkServiceModel);        
        if($ret['status']){
            $areas = $ret['push']['object']->getAreaList();
            $aRet = array();
            foreach($areas as $k=>$v) {
                $bus =  array(
                    "id" => $v['idarea'],
                    "text" => "{$v['name']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }
        return $aRet;
    }

    public function _comboAreaHtml()
    {
        $aArea = $this->_comboArea();
        $select = '';

        foreach($aArea as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }
    
    /**
     * _comboType
     *
     * @param  int $areaID
     * @return array
     */
    public function _comboType($areaID): array
    {
        $hdkServiceDAO = new hdkServiceDAO();
        $hdkServiceModel = new hdkServiceModel();
        $hdkServiceModel->setIdArea($areaID);

        $ret = $hdkServiceDAO->fetchTypeByArea($hdkServiceModel);        
        if($ret['status']){
            $types = $ret['push']['object']->getTypeList();
            $aRet = array();
            foreach($types as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtype'],
                    "text" => "{$v['name']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * _comboTypeHtml
     *
     * @param  int $areaID
     * @return void
     */
    public function _comboTypeHtml($areaID)
    {
        $aType = $this->_comboType($areaID);
        $select = '';

        foreach($aType as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }
    
    /**
     * _comboItem
     *
     * @param  int $typeID
     * @return array
     */
    public function _comboItem($typeID): array
    {
        $hdkServiceDAO = new hdkServiceDAO();
        $hdkServiceModel = new hdkServiceModel();
        $hdkServiceModel->setIdType($typeID);

        $ret = $hdkServiceDAO->fetchItemByType($hdkServiceModel);        
        if($ret['status']){
            $items = $ret['push']['object']->getItemList();
            $aRet = array();
            foreach($items as $k=>$v) {
                $bus =  array(
                    "id" => $v['iditem'],
                    "text" => "{$v['name']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * _comboItemHtml
     *
     * @param  int $typeID
     * @return void
     */
    public function _comboItemHtml($typeID)
    {
        $aItem = $this->_comboItem($typeID);
        $select = '';

        foreach($aItem as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }
    
    /**
     * _comboService
     *
     * @param  int $itemID
     * @return array
     */
    public function _comboService($itemID): array
    {
        $hdkServiceDAO = new hdkServiceDAO();
        $hdkServiceModel = new hdkServiceModel();
        $hdkServiceModel->setIdItem($itemID);

        $ret = $hdkServiceDAO->fetchServiceByItem($hdkServiceModel);        
        if($ret['status']){
            $services = $ret['push']['object']->getServiceList();
            $aRet = array();
            foreach($services as $k=>$v) {
                $bus =  array(
                    "id" => $v['idservice'],
                    "text" => "{$v['name']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * _comboServiceHtml
     *
     * @param  int $itemID
     * @return void
     */
    public function _comboServiceHtml($itemID)
    {
        $aService = $this->_comboService($itemID);
        $select = '';

        foreach($aService as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }

    /**
     * _comboReason
     *
     * @param  int $itemID
     * @return array
     */
    public function _comboReason($serviceID): array
    {
        $reasonDAO = new reasonDAO();
        $reasonModel = new reasonModel();
        $reasonModel->setIdService($serviceID);

        $ret = $reasonDAO->fetchReasonByService($reasonModel);        
        if($ret['status']){
            $reasons = $ret['push']['object']->getGridList();
            $aRet = array();
            foreach($reasons as $k=>$v) {
                $bus =  array(
                    "id" => $v['idreason'],
                    "text" => "{$v['name']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * _comboReasonHtml
     *
     * @param  int $serviceID
     * @return void
     */
    public function _comboReasonHtml($serviceID)
    {
        $translator = new localeServices();
        $aReason = $this->_comboReason($serviceID);

        if(count($aReason) <= 0)
            return "<option value='NR'>".$translator->translate('Reason_no_registered')." </option>";
        
        $select = '';        
        foreach($aReason as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }

    public function _comboTypeExpireDate()
    {
        $translator = new localeServices();
        $aRet = array(
            array("id" => 0,"text"=>$translator->translate('Expire_date')),
            array("id" => 1,"text"=>$translator->translate('grd_expiring')),
            array("id" => 2,"text"=>$translator->translate('grd_expiring_today')),
            array("id" => 3,"text"=>$translator->translate('grd_expired')),
            array("id" => 4,"text"=>$translator->translate('grd_expired_n_assumed'))
        );
        
        return $aRet;
    }

    public function _comboTypeView()
    {
        $translator = new localeServices();
        $aRet = array(
            array("id" => 1,"text"=>$translator->translate('grd_show_all')),
            array("id" => 2,"text"=>$translator->translate('grd_show_only_mine')),
            array("id" => 3,"text"=>$translator->translate('grd_show_group'))
        );
        
        return $aRet;
    }

    public function _checkApproval(){

        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketModel->setIdOwner($_SESSION['SES_COD_USUARIO']);

        if($_SESSION['hdk']['SES_OPEN_NEW_REQUEST']){
            if ($_ENV['LICENSE'] == '200701006') {
                $ticketModel->setItemException(true);
            }

            $retTotal = $ticketDAO->getWaitingApprovalRequestsCount($ticketModel);
            $total = (!$retTotal['status']) ? 0 : $retTotal['push']['object']->getTotalRows();

            return $total;
        }else{
            return 0;
        }
    }

    public function _checkVipUser($idPerson)
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setIdOwner($idPerson);

        $retVipUser = $ticketDAO->checksVipUser($ticketModel);
        if($retVipUser['status'] && $retVipUser['push']['object']->getIsUserVip() == "Y")
            return true;
        else
            return false;

    }

    public function _checkVipPriority()
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $retVipPriority = $ticketDAO->checksVipPriority($ticketModel);
        if($retVipPriority['status'] && $retVipPriority['push']['object']->getVipHasPriority() == "Y")
            return true;
        else
            return false;

    }

    public function _getVipPriority()
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $retVipPriority = $ticketDAO->getVipPriority($ticketModel);

        if(!$retVipPriority['status']){
            $retDefault = $ticketDAO->getDefaultPriority($ticketModel);
            if($retDefault['status'])
                $vipPriorityID = $rsDefault->fields['idpriority'];
        }else{
            $vipPriorityID = $retVipPriority['push']['object']->getIdPriority();
        }

        return $vipPriorityID;
    }

    public function _getServicePriority($idService)
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setIdService($idService);
        
        $retServicePriority = $ticketDAO->getIdPriorityByService($ticketModel);

        if(!$retServicePriority['status']){
            $retDefault = $ticketDAO->getDefaultPriority($ticketModel);
            if($retDefault['status'])
                $priorityID = $rsDefault->fields['idpriority'];
        }else{
            $priorityID = $retServicePriority['push']['object']->getIdPriority();
        }

        return $priorityID;
    }
    
    /**
     * Returns priority id
     *
     * @param  int $idPerson   Ticket's owner id
     * @param  int $idService  Service id
     * @return void
     */
    public function _getPriorityId(int $idPerson,int $idService)
    {
        $idPriority = ($this->_checkVipUser($idPerson) &&  $this->_checkVipPriority()) ? $this->_getVipPriority() : $this->_getServicePriority($idService);
        
        return $idPriority;
    }
    
    /**
     * Format the ticket code
     *
     * @param  string $ticketCode
     * @return void
     */
    public function _formatTicketCode(string $ticketCode)
    {
        return substr($ticketCode,0,4).'-'.substr($ticketCode,4,2).'.'.substr($ticketCode,6,6);
    }
    
    /**
     * Return ticket's expiry date
     *
     * @param  mixed $startDate
     * @param  mixed $idPriority
     * @param  mixed $idService
     * @return void
     */
    public function _getTicketExpireDate($startDate=null,$idPriority=null,$idService=null){
        $appSrc = new appServices();
        $expireDateDAO = new expireDateDAO();
        $expireDateModel = new expireDateModel();

        $startDate = (!isset($startDate) || !$startDate) ? date("Y-m-d H:i:s") : $startDate;

        // If have service id
        if(isset($idService)){
            $expireDateModel->setIdService($idService);
            $retExpireDateService = $expireDateDAO->getExpireDateService($expireDateModel);
            if(!$retExpireDateService['status'])
                return false;

            $companyID = $retExpireDateService['push']['object']->getIdCustomer();
            $days = $retExpireDateService['push']['object']->getAttendanceDays();
            $time = $retExpireDateService['push']['object']->getAttendanceHours();
            $timeType = $retExpireDateService['push']['object']->getTimeType();
        }

        //If have priority id and time and days are zero
        if(isset($idPriority) && $time == 0 && $days == 0){
            $expireDateModel->setIdPriority($idPriority);
            $retExpireDateService = $expireDateDAO->getExpireDatePriority($expireDateModel);
            if(!$retExpireDateService['status'])
                return false;

            $days = $retExpireDateService['push']['object']->getAttendanceDays();
            $time = $retExpireDateService['push']['object']->getAttendanceHours();
            $timeType = "H";
        }

        return $appSrc->_getExpireDate($startDate,$days,$time,$timeType,true,false,false,$companyID);
    }

    /**
     * Returns id of the repassing only group
     *
     * @param  mixed $idGroup
     * @return void
     */
    public function _getIdGroupOnlyRepass($idGroup){
        $groupDAO = new groupDAO();
        $groupModel = new groupModel();        
        $groupModel->setIdGroup($idGroup)
                   ->setIdCompany($_SESSION['SES_COD_EMPRESA']);

        $ret = $groupDAO->checkGroupOnlyRepass($groupModel);
        if(!$ret['status'])
            return $idGroup;
        
        if($ret['push']['object']->getIsRepassOnly() == "Y"){
            $retNewGroup = $groupDAO->getNewGroupOnlyRepass($ret['push']['object']);
            if(!$retNewGroup['status'] || $retNewGroup['push']['object']->getNewIdGroup() == 0){
                return $idGroup;
            }else{
                return $retNewGroup['push']['object']->getNewIdGroup();
            }
        } else{
           return $idGroup;
        }
    }
}