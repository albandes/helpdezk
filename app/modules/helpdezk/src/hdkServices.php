<?php

namespace App\modules\helpdezk\src;

use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;
use App\modules\helpdezk\dao\mysql\ticketDAO;
use App\modules\helpdezk\dao\mysql\reasonDAO;
use App\modules\helpdezk\dao\mysql\expireDateDAO;
use App\modules\helpdezk\dao\mysql\groupDAO;
use App\modules\helpdezk\dao\mysql\hdkEmailFeatureDAO;
use App\modules\helpdezk\dao\mysql\priorityDAO;
use App\modules\helpdezk\dao\mysql\attendanceTypeDAO;

use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\featureModel;
use App\modules\admin\models\mysql\personModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;
use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\helpdezk\models\mysql\reasonModel;
use App\modules\helpdezk\models\mysql\expireDateModel;
use App\modules\helpdezk\models\mysql\groupModel;
use App\modules\helpdezk\models\mysql\hdkEmailFeatureModel;
use App\modules\helpdezk\models\mysql\priorityModel;
use App\modules\helpdezk\models\mysql\attendanceTypeModel;

use App\src\appServices;
use App\src\localeServices;
use App\modules\admin\src\loginServices;
use App\modules\admin\src\adminServices;

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

    /**
     * @var bool
     */
    protected $tracker;

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

        // Tracker Settings
        $this->tracker = ($_SESSION['TRACKER_STATUS'] == 1) ? true : false;
        
    }
    
    /**
     * en_us Setups the module navbar
     * 
     * pt_br Configura a barra de navegação do módulo
     *
     * @param  array $params Array with common parameters
     * @return array         Array with module's parameters added
     */
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
    
    /**
     * _comboAreaHtml
     *
     * @return void
     */
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
    
    /**
     * _comboTypeExpireDate
     *
     * @return void
     */
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
    
    /**
     * _comboTypeView
     *
     * @return void
     */
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
    
    /**
     * _checkApproval
     *
     * @return void
     */
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
    
    /**
     * _checkVipUser
     *
     * @param  mixed $idPerson
     * @return void
     */
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
    
    /**
     * _checkVipPriority
     *
     * @return void
     */
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
    
    /**
     * _getVipPriority
     *
     * @return void
     */
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
    
    /**
     * _getServicePriority
     *
     * @param  mixed $idService
     * @return void
     */
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
    
    /**
     * en_us Setups params to send notifications by email
     * 
     * pt_br Configura os parâmetros para enviar notificações por e-mail
     *
     * @param  array $aParams
     * @return void
     */
    public function _sendNotification($aParams)
    {
        $appSrc = new appServices();
        $moduleDAO = new moduleDAO();        
        $moduleModel = new moduleModel();

        $moduleModel->setName('Helpdezk');
        $transaction    = $aParams['transaction'] ;
        $media          = $aParams['media'] ;
        $ticketCode     = $aParams['code_request'] ;

        if ($media == 'email'){
            $cron = false;
            $smtp = false;
        }

        $retInfo = $moduleDAO->getModuleInfoByName($moduleModel);
        if(!$retInfo['status']){
            return false;
        }

        $this->hdklogger->info("[hdk] Ticket #: {$ticketCode}. Transaction: {$transaction}. Media: {$media}.",['Class' => __CLASS__, 'Method' => __METHOD__]);

        switch($transaction){
            // Send email to the attendant, or group of attendants, when a request is forwarded
            case 'forward-ticket':
                if ($media == 'email') {
                    if ($media == 'email') {
                        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['REPASS_REQUEST_OPERATOR_MAIL'] == '1') {
                            if ( $_SESSION['EM_BY_CRON'] == '1') {
                                $cron = true;
                            } else {
                                $smtp =  true;
                            }
                            $messageTo   = 'forward-ticket';
                            $messagePart = 'Pass the request # ';
                        }
                    }
                }
                break;

            // Sends notification to user when the request receives a note, created by operator
            case 'user-note' :
                if ($media == 'email') {
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['USER_NEW_NOTE_MAIL'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'user-note';
                        $messagePart = 'Add note in request # ';
                    }
                }
                break ;

            // Sends notification to operator when the request receives a note, created by user
            case 'operator-note':
                if ($media == 'email') {
                    if ($media == 'email') {
                        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['OPERATOR_NEW_NOTE'] == '1') {
                            if ( $_SESSION['EM_BY_CRON'] == '1') {
                                $cron = true;
                            } else {
                                $smtp =  true;
                            }
                            $messageTo   = 'operator-note';
                            $messagePart = 'Add note in request # ';
                        }
                    }
                }
                break;

            // Send notification to the attendant, or group of attendants, when a request is reopened by user
            case 'reopen-ticket':
                if ($media == 'email') {
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['REQUEST_REOPENED'] == '1') {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }

                        $messageTo   = 'reopen-ticket';
                        $messagePart = 'Reopen request # ';
                    }
                }
                break;

            // Send notification to the attendant, or group of attendants, when a request is evaluated by user
            case 'evaluate-ticket':
                if($media == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['EM_EVALUATED']) {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'evaluate-ticket';
                        $messagePart = 'Evaluate request # ';
                    }

                }
                break;

            // Sends notification to user, when a request is closed by attendant.
            case 'finish-ticket':
                if($media == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['FINISH_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                            $cron = true;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'finish-ticket';
                        $messagePart = 'Closed request # ';
                    }
                }

                break;

            // Sends notification to the user when a request is assumed
            case 'operator-assume':
                if($media == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['NEW_ASSUMED_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                           $cron = true;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'operator-assume';
                        $messagePart = 'Assumed request # ';
                    }
                }
                break;

            // Sends notification to the user when a request is rejected
            case 'operator-reject':
                if($media == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['REJECTED_MAIL'] == 1) {

                        if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                            $cron = true;
                        } else {
                            $smtp = true;
                        }
                        $messageTo   = 'operator-reject';
                        $messagePart = 'Rejected request # ';
                    }
                }
                break;

            // Sends a notification to the operator or group of operators when a request is opened
            case 'new-ticket-user':
                if($media == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['NEW_REQUEST_OPERATOR_MAIL'] == 1) {
                        if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                            $cron = true;
                        } else {
                            $smtp = true;
                        }
                        $messageTo   = 'new-ticket-user';
                        $messagePart = 'Inserted request # ';
                    }
                }

                // Since November 20, 2020
                if ($this->_existsViewByUrlTable()) {
                    $this->_setUrlToken($ticketCode);
                } else {
                    $this->hdklogger->info("[hdk] hdk_tbviewbyurl table does not exist",['Class' => __CLASS__, 'Method' => __METHOD__]);
                }

                break;
            
            // Send an email to the attendant, or group of attendants, when an auxiliary attendant is add
            case 'add-aux-operator':
                if ($media == 'email') {
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['MAIL_AUX_OPERATOR'] == 1) {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'add-aux-operator';
                        $messagePart = 'An auxiliary attendant was added to the request # ';
                    }
                }

                break;
            
            // Sends notification to the attendant when a request is approved
            case 'approve':
                if($media == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $_SESSION['hdk']['SES_REQUEST_APPROVE'] == 1) {

                        if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                            $cron = true;
                        } else {
                            $smtp = true;
                        }
                        $messageTo   = 'approve';
                        $messagePart = 'Approved. Request # ';
                    }

                }

                break;

            default:
                return false;
        }
        
        if ($media == 'email') {
            if ($cron) {
                $this->hdklogger->info("[hdk] Send by cron.",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);

                $retCron = $appSrc->_saveEmailCron($retInfo['push']['object']->getIdmodule(),$ticketCode,$transaction);
                if(!$retCron['status']){
                    $this->hdklogger->error("[hdk] Error trying to send e-mail by cron. {$retCron['message']}",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
                }else{
                    $this->hdklogger->info("[hdk] {$messagePart} {$ticketCode} - We will perform the method to send e-mail by cron.",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
                }
            } elseif($smtp){
                $this->hdklogger->info("[hdk] Send by smtp.",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);

                $this->hdklogger->info("[hdk] {$messagePart} {$ticketCode} - We will perform the method to send e-mail.",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
                $this->_sendTicketEmail($messageTo,$ticketCode);
            }
        }

        return true ;
    }
    
    /**
     * en_us Checks if view's by URL table exists
     * 
     * pt_br Verifica se a tabela de visualização por URL existe
     *
     * @return bool
     */
    public function _existsViewByUrlTable()
    {
        $featureDAO = new featureDAO();
        $featureModel = new featureModel();
        $featureModel->setTableName('hdk_tbviewbyurl');
        
        $ret = $featureDAO->tableExists($featureModel);
        if ($ret['status'] && ($ret['push']['object']->getExistTable()))
            return true;
        else
            return false;
    }
    
    /**
     * en_us Setups URL's token to access ticket from email
     * 
     * pt_br Configura o token do URL para acessar o ticket do e-mail
     *
     * @param  mixed $ticketCode
     * @return void
     */
    public function _setUrlToken($ticketCode)
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode);

        $ret  = $ticketDAO->getInChargeByTicketCode($ticketModel);
        if(!$ret['status'])
            return;

        $inChargeType = $ret['push']['object']->getInChargeType();
        $inChargeID   = $ret['push']['object']->getIdInCharge();

        if ($inChargeType == 'G') {
            $groupDAO = new groupDAO();
            $groupModel = new groupModel();
            $groupModel->setIdGroup($inChargeID);

            $retGroupOperators = $groupDAO->fetchGroupOperators($groupModel);
            if(!$retGroupOperators['status']){
                $this->hdklogger->error("[hdk] Error getting group's operators.",['Class' => __CLASS__, 'Method' => __METHOD__]);
                return;
            }

            if (count($retGroupOperators['push']['object']->getGridList()) == 0) {
                $this->hdklogger->error("[hdk] Group with id # {$inChargeID} does not have operators!",['Class' => __CLASS__, 'Method' => __METHOD__]);
                return;
            }

            $operators = $retGroupOperators['push']['object']->getGridList();

            foreach($operators as $k=>$v) {
                $this->_saveUrlToken($v['idperson'], $ticketCode);
            }
        } else {
            $this->_saveUrlToken($inChargeID,$ticketCode);
        }

        return;
    }
    
    /**
     * en_us Saves URL's token into DB
     * 
     * pt_br Grava o token do URL no banco de dados
     *
     * @param  int      $idPerson   Emails recipient ID
     * @param  string   $ticketCode Ticket code
     * @return void
     */
    function _saveUrlToken($idPerson,$ticketCode)
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $token =  hash('sha512',rand(100,1000));
        $ticketModel->setIdOperator($idPerson)
                    ->setTicketCode($ticketCode)
                    ->setTicketToken($token);
        
        $ret = $ticketDAO->saveViewByUrl($ticketModel);
        if(!$ret['status']){
            $this->hdklogger->error("[hdk] Error generating token for request preview authentication, idperson {$idPerson}, ticket code {$ticketCode}",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $this->hdklogger->info("[hdk] Generated token for request preview authentication, idperson {$idPerson}, ticket code {$ticketCode}!",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
        }
        return;
    }
    
    /**
     * en_us Sends email
     * pt_br Envia email
     * 
     * @param  string $operation
     * @param  string $ticketCode
     * @param  mixed  $reason
     * @return bool
     */
    public function _sendTicketEmail($operation,$ticketCode,$reason=NULL)
    {
        
        if (!isset($operation)) {
            $this->hdklogger->error("Email code not provided!!!",['Class' => __CLASS__, 'Method' => __METHOD__]);
            return false;
        }

        $appSrc = new appServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $hdkEmailFeatureDAO = new hdkEmailFeatureDAO();
        $hdkEmailFeatureModel = new hdkEmailFeatureModel();

        $hdkEmailFeatureModel->setLocaleName($_ENV['DEFAULT_LANG']);
        
        // Common data
        $ticketModel->setTicketCode($ticketCode);
        $retTicket = $ticketDAO->getTicket($ticketModel);
        if(!$retTicket['status']){
            $this->hdklogger->error("Can't get ticket data, # {$ticketCode}. Error: {$retTicket['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
            return false;
        }

        $ticket = $retTicket['push']['object'];
        $sentTo = "";
        $arrAttach = array();
        
        //$EVALUATION     = $ticketDAO->getEvaluationGiven($ticket);
        $REQUEST        = $ticketCode;
        $SUBJECT        = $ticket->getSubject();
        $REQUESTER      = $ticket->getOwner();
        $RECORD         = $appSrc->_formatDate($ticket->getEntryDate());
        $DESCRIPTION    = $ticket->getDescription();
        $INCHARGE       = $ticket->getInCharge();
        $PHONE          = $ticket->getOwnerPhone();
        $BRANCH         = $ticket->getOwnerBranch();
        $LINK_OPERATOR  = "<pipegrep></pipegrep>";
        //$LINK_USER      = $this->makeLinkUser($code_request);
        // Notes
        $table          = $this->_makeNotesTable($ticketCode);
        $NT_OPERATOR    = $table;

        switch ($operation) {

            //Sends a email to the operator or group of operators when a request is opened
            case "new-ticket-user":
                // Get email template
                $hdkEmailFeatureModel->setSessionName("NEW_REQUEST_OPERATOR_MAIL");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

            // Sends email to the user when a request is assumed
            case 'operator-assume':
                // Get email template
                $hdkEmailFeatureModel->setSessionName("NEW_ASSUMED_MAIL");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $sentTo = $this->_setSendTo($ticketCode);
                $sentTo .= (!$sentTo) ? "{$ticket->getOwnerEmail()}" : ";{$ticket->getOwnerEmail()}";
                $userType = $ticket->getOwnerTypeId();

                //$LINK_OPERATOR = $this->makeLinkOperator($code_request);

                /* if($userType == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request); */
                
                $date = date('Y-m-d H:i');
                $ASSUME = $appSrc->_formatDate($date);

                $table = $this->_makeNotesTable($ticketCode,false);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                break;

            // Sends email to the user, when a request is closed by the attendant.
            case 'finish-ticket':
                // Get email template
                $hdkEmailFeatureModel->setSessionName("FINISH_MAIL");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $sentTo = $this->_setSendTo($ticketCode);
                $sentTo .= (!$sentTo) ? "{$ticket->getOwnerEmail()}" : ";{$ticket->getOwnerEmail()}";
                $userType = $ticket->getOwnerTypeId();

                $date = date('Y-m-d H:i');
                $FINISH_DATE = $appSrc->_formatDate($date);

                //TODO: url to evaluate attendance
                /* $this->loadModel('evaluation_model');
                $ev = new evaluation_model();
                $tk = $ev->getToken($code_request);
                $token = $tk->fields['token'];
                if($token)
                    $LINK_EVALUATE =  $this->helpdezkUrl."/helpdezk/evaluate/index/token/".$token; */

                $table = $this->_makeNotesTable($ticketCode);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                break;

            // Sends email to the user, when a request is rejected by the attendant.
            case 'operator-reject':
                // Get email template
                $hdkEmailFeatureModel->setSessionName("REJECTED_MAIL");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $sentTo = $this->_setSendTo($ticketCode);
                $sentTo .= (!$sentTo) ? "{$ticket->getOwnerEmail()}" : ";{$ticket->getOwnerEmail()}";
                $userType = $ticket->getOwnerTypeId();

                $date = date('Y-m-d H:i');
                $REJECTION = $appSrc->_formatDate($date);

                /* if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request); */

                $table = $this->_makeNotesTable($ticketCode,false);
                $NT_USER = $table;

                //$goto = ('/helpdezk/hdkTicket/viewrequest/id/' . $code_request);
                //$url = '<a href="' . $this->helpdezkUrl . urlencode($goto) . '">' . $l_eml["link_solicitacao"] . '</a>';

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                break;

            // Sends email to user when the request receives a note
            case 'user-note' :
                // Get email template
                $hdkEmailFeatureModel->setSessionName("USER_NEW_NOTE_MAIL");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $sentTo = $this->_setSendTo($ticketCode);
                $sentTo .= (!$sentTo) ? "{$ticket->getOwnerEmail()}" : ";{$ticket->getOwnerEmail()}";
                $userType = $ticket->getOwnerTypeId();

                //
                $FINISH_DATE = $appSrc->_formatDate(date('Y-m-d H:i'));
                //$LINK_OPERATOR = $this->makeLinkOperator($code_request);

               /*  if($typeuser == 2)
                    $LINK_USER = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request); */

                $table = $this->_makeNotesTable($ticketCode);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                //TODO: attach from S3 and new local storage dir
                //if($_SESSION['hdk']['SES_ATTACHMENT_OPERATOR_NOTE']){
                   /*  $rsAttachs = $this->dbTicket->getNoteAttchByCodeRequest($code_request);
                    if($rsAttachs) {
                        $att_path = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/' ;
                        while (!$rsAttachs->EOF) {
                            $ext = strrchr($rsAttachs->fields['filename'], '.');
                            $attachment_dest = $att_path . $rsAttachs->fields['idnote_attachments'] . $ext;

                            $bus = array("filepath" => $attachment_dest,
                                         "filename" => $rsAttachs->fields['filename']);
                            array_push($arrAttach,$bus);

                            $rsAttachs->MoveNext();
                        }
                    } */

                //}

                break;

            // Sends email to operator when the request receives a note
            case 'operator-note' :
                // Get email template
                $hdkEmailFeatureModel->setSessionName("OPERATOR_NEW_NOTE");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                // -- owner data
                $userType = $ticket->getOwnerTypeId();

                $FINISH_DATE = $appSrc->_formatDate(date('Y-m-d H:i'));
                //$LINK_OPERATOR = $this->makeLinkOperator($code_request);
                /* if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request); */

                $table = $this->_makeNotesTable($ticketCode);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

            // Send email to the attendant, or group of attendants, when a request is reopened by user
            case 'reopen-ticket':
                // Get email template
                $hdkEmailFeatureModel->setSessionName("REQUEST_REOPENED");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

            // Send email to the attendant, or group of attendants, when a request is evaluated by user
            case "evaluate-ticket":
                // Get email template
                $hdkEmailFeatureModel->setSessionName("EM_EVALUATED");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

            // Send email to the attendant, or group of attendants, when a request is forwarded
            case "forward-ticket":
                // Get email template
                $hdkEmailFeatureModel->setSessionName("REPASS_REQUEST_OPERATOR_MAIL");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }

                $template = $retTemplate['push']['object'];

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

            //TODO: Request's approval flow
            // Sends email to the attendant, or group of attendants, when a request need approve
            case "approve":
                // Get email template
                $hdkEmailFeatureModel->setSessionName("SES_REQUEST_APPROVE");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }
                
                $template = $retTemplate['push']['object'];

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

            // Sends email to the user when a request is rejected
            /*case "operator_reject":
                $templateId = $dbEmailConfig->getEmailIdBySession("SES_MAIL_OPERATOR_REJECT");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($_SESSION['hdk']['SES_MAIL_OPERATOR_REJECT_ID']);
                while (!$grpEmails->EOF) {
                    if (!$sentTo) {
                        $sentTo = $grpEmails->Fields('email');
                    } else {
                        $sentTo .= ";" . $grpEmails->Fields('email');
                    }
                    $grpEmails->MoveNext();
                }

                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;*/

            // Send email to the attendant, or group of attendants, when a auxiliar attendant is added
            case "add-aux-operator":
                // Get email template
                $hdkEmailFeatureModel->setSessionName("MAIL_AUX_OPERATOR");
                $retTemplate = $hdkEmailFeatureDAO->getEmailTemplateBySession($hdkEmailFeatureModel);
                if(!$retTemplate['status']) {
                    $this->hdklogger->error("[hdk] Send email, request # {$REQUEST}, do not get Template. Error: {$retTemplate['push']['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return false;
                }
                
                $template = $retTemplate['push']['object'];

                $contents = str_replace('"', "'", $template->getBody()) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $template->getSubject();
                eval("\$subject = \"$subject\";");

                // Setups the list of recipients
                $sentTo = $this->_setSendTo($ticketCode);

                break;

        }

        $customHeader = 'X-hdkRequest: '. $REQUEST;

        $msgLog = "request # ".$REQUEST." - Operation: ".$operation;
        $msgLog2 = "request # ".$REQUEST;

        $params = array("subject"       => $subject,
                        "contents"      => $contents,
                        "address"       => $sentTo,
                        "attachment"    => $arrAttach,
                        "idmodule"      => $appSrc->_getModuleID("Helpdezk"),
                        "tracker"       => $this->tracker,
                        "msg"           => $msgLog,
                        "msg2"          => $msgLog2,
                        "customHeader"  => $customHeader,
                        "code_request"  => $REQUEST,
                        "tokenOperatorLink"=> true);


        $done = $appSrc->_sendEmail($params);

        if (!$done['status']) {
            $this->hdkEmailLogger->error("[hdk] E-mail not sent. Ticket # {$REQUEST}. Error: {$done['message']}",['Class' => __CLASS__, 'Method' => __METHOD__]);
            return false ;
        } else {
            $this->hdkEmailLogger->info("[hdk] E-mail sent. Ticket # {$REQUEST}.", ['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
            return true ;
        }

    }
    
    /**
     * en_us Setups a list with recipients
     * 
     * pt_br Configura uma lista com destinatários
     *
     * @param  string $ticketCode Ticket code
     * @return string Recipients list (Email addresses)
     */
    public function _setSendTo($ticketCode)
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode);

        $ret  = $ticketDAO->fetchInChargeEmail($ticketModel);
        if(!$ret['status'])
            return;

        $sentTo = '';
        foreach($ret['push']['object']->getGridList() as $key=>$val){
            $inChargeType   = $val['type']; 
            $inChargeID     = $val['id_in_charge'];
            $inChargeEmail  = $val['email'];
    
            if ($inChargeType == 'G') {
                $groupDAO = new groupDAO();
                $groupModel = new groupModel();
                $groupModel->setIdGroup($inChargeID);
    
                $retGroupOperators = $groupDAO->fetchGroupOperators($groupModel);
                if(!$retGroupOperators['status']){
                    $this->hdklogger->error("[hdk] Error getting group's operators.",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return;
                }
    
                if (count($retGroupOperators['push']['object']->getGridList()) == 0) {
                    $this->hdklogger->error("[hdk] Group with id # {$inChargeID} does not have operators!",['Class' => __CLASS__, 'Method' => __METHOD__]);
                    return;
                }
    
                $operators = $retGroupOperators['push']['object']->getGridList();
    
                foreach($operators as $k=>$v) {
                    $sentTo .= (empty($sentTo) ? "" : ";") . $v['email'];
                }
            } else {
                $sentTo .= (empty($sentTo) ? "" : ";") . $inChargeEmail;
            }
        }

        return $sentTo;
    }
    
    /**
     * en_us Makes HTML table of ticket's notes
     * pt_br Faz tabela HTML de apontamentos da solicitação
     *
     * @param  string $ticketCode   Ticket code
     * @param  mixed $public        List all notes or not - true: all false: notes for attendants
     * @return string               Note's table in html
     */
    public function _makeNotesTable($ticketCode,$public=true)
    {
        $appSrc = new appServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode);
        
        $table = "";
        $ret  = $ticketDAO->fetchTicketNotes($ticketModel);
        if(!$ret['status'])
            return $table;

        $notes = $ret['push']['object']->getNoteList();
        if(count($notes) <= 0)
            return $table;
        
        $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
        foreach($notes as $k=>$v) {
            if($public){
                $table.= "<tr><td height=28><font size=2 face=arial>";
                $table.= $appSrc->_formatDate($v['entry_date']) . " [" . $v["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($v["description"]));
                $table.= "</font><br></td></tr>";
            }else{
                if($v['idtype'] != '2'){
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $appSrc->_formatDate($v['entry_date']) . " [" . $v["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($v["description"]));
                    $table.= "</font><br></td></tr>";
                }
            }
        }
        $table.= "</table>";
        
        return $table;
    }
    
    /**
     * _getTicketAttachments
     *
     * @param  mixed $ticketCode    Ticket code
     * @return array                Parameters returned in array: 
     *                                  [hasAttachment = true/false
     *                                   attachmentHtml =  html of attachments for download]
     */
    public function _getTicketAttachments($ticketCode)
    {
        $appSrc = new appServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode);
        
        $ret  = $ticketDAO->fetchTicketAttachments($ticketModel);
        if(!$ret['status'])
            return array("hasAttachment" => 0, "attachmentHtml" => "");
        
        $attachments = $ret['push']['object']->getAttachments();
        if(count($attachments) <= 0)
            return array("hasAttachment" => 0, "attachmentHtml" => "");
        
        foreach($attachments as $k=>$v) {
            $filename = $v['file_name'];
            $ext = strrchr($filename, '.');
            $idAttach = $v['idrequest_attachment'];
            $attach[$filename] = '<button type="button" class="btn btn-default btn-sm" id="'.$idAttach.'" onclick="download('.$idAttach.',\'request\')"><span class="fa fa-file-alt"></span>  &nbsp;'.$filename.'</button>';
        }
        
        $html = implode(" ", $attach);
        
        return array("hasAttachment" => 1, "attachmentHtml" => $html);
    }

    /**
     * en_us Return an array with priorities data
     * pt_br Retorna um array com dados de prioridades
     *
     * @return array
     */
    public function _comboPriority(): array
    {
        $priorityDAO = new priorityDAO();
        
        $ret = $priorityDAO->queryPriorities();        
        if($ret['status']){
            $priorities = $ret['push']['object']->getGridList();
            $aRet = array();
            foreach($priorities as $k=>$v) {
                $bus =  array(
                    "id" => $v['idpriority'],
                    "text" => "{$v['name']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }
        return $aRet;
    }

    /**
     * en_us Return an array with attendance type data
     * pt_br Retorna um array com dados do tipo de atendimento
     *
     * @return array
     */
    public function _comboAttendanceType($where=null,$group=null,$order=null,$limit=null): array
    {
        $attendanceTypeDAO = new attendanceTypeDAO();
        $order = (!$order) ? "ORDER BY way ASC" : $order;
        
        $ret = $attendanceTypeDAO->queryAttendanceTypes($where,$group,$order,$limit);        
        if($ret['status']){
            $attendanceTypes = $ret['push']['object']->getGridList();
            $aRet = array();
            foreach($attendanceTypes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idattendanceway'],
                    "text" => "{$v['way']}",
                    "isdefault" => isset($v['default']) ? $v['default'] : 0
                );
                array_push($aRet,$bus);
            }
        }
        return $aRet;
    }
    
    /**
     * en_us Returns an array with ticket's auxiliary attendants
     * pt_br Retorna um array com os atendentes auxiliares da solicitação
     *
     * @param  string   $ticketCode    Ticket code
     * @param  bool     $inCond        Indicates type of return [false = all attendants - true = only attendants assigned to the ticket]
     * @return array                   
     */
    public function _comboAuxiliaryAttendant($ticketCode,$inCond=false): array
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode)
                    ->setInCond($inCond);

        $ret = $ticketDAO->fetchAuxiliaryAttendant($ticketModel);
        if($ret['status']){
            $auxiliaryAttendants = $ret['push']['object']->getAuxiliaryAttendantList();
            $aRet = array();
            foreach($auxiliaryAttendants as $k=>$v) {
                $bus =  array(
                    "id" => $v['idperson'],
                    "text" => "{$v['name']}"
                );
                array_push($aRet,$bus);
            }
        }else{
            return false;
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with note's type
     * pt_br Retorna um array com os tipos de apontamentos
     *
     * @return array                   
     */
    public function _comboNoteType(): array
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ret = $ticketDAO->fetchNoteType($ticketModel);        
        if($ret['status']){
            $noteTypes = $ret['push']['object']->getNoteTypeList();
            $aRet = array();
            foreach($noteTypes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtypenote'],
                    "text" => "{$v['note_type_label']}"
                );
                array_push($aRet,$bus);
            }
        }else{
            return false;
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with hour's type
     * pt_br Retorna um array com os tipos de hora
     *
     * @return array                   
     */
    public function _comboHourType(): array
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $aRet = array(
            array("id" => 1,"text" => "Normal"),
            array("id" => 2,"text" => "Extra")
        );

        return $aRet;
    }
    
    /**
     * en_us Returns an array with attendant's groups
     * pt_br Retorna um array com os grupos do atendente
     *
     * @param  int $personID    Attendant id
     * @return array
     */
    public function _comboAttendantGroups($personID): array
    {
        $groupDAO = new groupDAO();
        $groupModel = new groupModel();
        $groupModel->setIdUser($personID);

        $ret = $groupDAO->fetchUserGroups($groupModel);
        if($ret['status']){
            $attendantGroups = $ret['push']['object']->getGridList();
            $aRet = array();
            foreach($attendantGroups as $k=>$v) {
                if((!isset($v['default']) && $k==0)){
                    $isDefault = "Y";
                }elseif(isset($v['default'])){
                    $isDefault = $v['default'];
                }else{
                    $isDefault = "N";
                }

                $bus =  array(
                    "id" => $v['idpergroup'],
                    "text" => "{$v['pername']}",
                    "isdefault" =>  $isDefault
                );
                array_push($aRet,$bus);
            }
        }else{
            return false;
        }

        return $aRet;
    }
    
    /**
     * en_us Returns string with a difference between dates
     * pt_br Retorna uma string com uma diferença entre as datas
     *
     * @param  string $start Start datetime
     * @param  string $end   End datetime
     * @return string
     */
    public function _dateDiff(string $start, string $end): string
    {
        $startDate = getdate(strtotime($start));
        $endDate = getdate(strtotime($end));
        $dif = ($endDate[0] - $startDate[0]) / 60;

        return number_format($dif, 0, '', '');
    }
    
    /**
     * en_us Returns an array with groups/attendants/partners data for dropdown list
     * pt_br Retorna um array com dados de grupos/atendentes/parceiros para lista suspensa
     * 
     * @param  string $type
     * @return array
     */
    public function _comboRepassUsers($type)
    {
        $groupDAO = new groupDAO();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $aRet = array();
        
        switch($type){
            case "group":
                $ret = $groupDAO->queryGroups("AND tbg.status = 'A'",null,"ORDER BY `name`");
                if($ret['status']){
                    $groups = $ret['push']['object']->getGridList();
                   
                    foreach($groups as $k=>$v) {
                        $bus =  array(
                            "id" => $v['idperson'],
                            "text" => "({$v['level']}) {$v['name']}"
                        );
                        array_push($aRet,$bus);
                    }
                }else{
                    return false;
                }
                break;
            case "operator":
                $ret = $ticketDAO->fetchAttendants($ticketModel);
                if($ret['status']){
                    $attendants = $ret['push']['object']->getAttendantList();
                   
                    foreach($attendants as $k=>$v) {
                        $bus =  array(
                            "id" => $v['idperson'],
                            "text" => "{$v['name']}"
                        );
                        array_push($aRet,$bus);
                    }
                }else{
                    return false;
                }
                break;
            default:
                $ret = $ticketDAO->fetchPartners($ticketModel);
                if($ret['status']){
                    $partners = $ret['push']['object']->getPartnerList();
                
                    foreach($partners as $k=>$v) {
                        $bus =  array(
                            "id" => $v['idperson'],
                            "text" => "{$v['name']}"
                        );
                        array_push($aRet,$bus);
                    }
                }else{
                    return false;
                }
                break;
        }

        return $aRet;
    }
    
    /**
     * en_us Convert groups/attendants/partners data in HTML string to show in the dropdown list
     * pt_br Converter dados de grupos/atendentes/parceiros em string HTML para mostrar na lista suspensa
     *
     * @param  string $type
     * @return string
     */
    public function _comboRepassListHtml($type)
    {
        $repassList = $this->_comboRepassUsers($type);
        $select = '';

        foreach($repassList as $key=>$val) {
            $default = ($val['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$val['id']}' {$default}>{$val['text']}</option>";
        }
        return $select;
    }
    
    /**
     * en_us Returns a string with groups/attendants/partners abilities list
     * pt_br Retorna uma string com a lista de habilidades de grupos/atendentes/parceiros
     *
     * @param  string $type
     * @param  int $repassID
     * @return string
     */
    public function _abilitiesListHtml($type,$repassID) {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setIdOperator($repassID);

        $abilities = "";
		if ($type == 'group') {
            $ret = $ticketDAO->fetchGroupAbilities($ticketModel);
            if ($ret['status']) {
                $aAbilities = $ret['push']['object']->getGridList();
                foreach ($aAbilities as $k=>$v) {
                    $abilities .= '<li class="list-group-item">'.$v['service'].'</li>';
                }
            }
        }
		elseif ($type == 'operator') {
            $ret = $ticketDAO->fetchAttendantAbilities($ticketModel);
            if ($ret['status']) {
                $aAbilities = $ret['push']['object']->getGridList();
                foreach ($aAbilities as $k=>$v) {
                    $abilities .= '<li class="list-group-item">'.$v['service'].'</li>';
                }
            }
        }
		return $abilities;
    }

	/**
     * en_us Returns a string with group attendants list or attendants/partners groups list
     * pt_br Retorna uma string com lista de atendentes do grupo ou lista de grupos de atendentes/parceiros
     *
     * @param  string $type
     * @param  int $repassID
     * @return string
     */
    public function _groupsListHtml($type,$repassID) {
        $groupDAO = new groupDAO();
        $groupModel = new groupModel();
        
        $html = "";
        if ($type == 'group') {
            $groupModel->setIdGroup($repassID);

            $ret = $groupDAO->fetchGroupOperators($groupModel);
            if ($ret['status']) {
                $aOperators = $ret['push']['object']->getGridList();
                foreach ($aOperators as $k=>$v) {
                    $html .= '<li class="list-group-item">'.$v['operator_name'].'</li>';
                }
            }
        } elseif ($type == 'operator') {
            $groupModel->setIdUser($repassID);

            $ret = $groupDAO->fetchUserGroups($groupModel);
            if ($ret['status']) {
                $aOperators = $ret['push']['object']->getGridList();
                foreach ($aOperators as $k=>$v) {
                    $html .= '<li class="list-group-item">'.$v['pername'].'</li>';
                }
            }
        }
		return $html;
    }
    
    /**
     * en_us Returns the name of the attendant or group to whom the request was forwarded
     * pt_br Retorna o nome do atendente ou grupo para quem a solicitação foi repassada
     *
     * @param  mixed $type      Attendant type [group or operator]
     * @param  mixed $repassID  Attendant id
     * @return string
     */
    public function _getRepassName($type,$repassID) {
        $groupDAO = new groupDAO();
        $personDAO = new personDAO();
        $personModel = new personModel();
        
        switch($type){
            case "group":
                $ret = $groupDAO->queryGroups("AND tbg.idperson = {$repassID}");
                if($ret['status']){
                    if($ret['status']){
                        $retRs = $ret['push']['object']->getGridList();
                        $repassName = $retRs[0]['name'];
                    }else{
                        $repassName = "";
                    }
                }else{
                    $repassName = "";
                }
                break;
            case "operator":
                $personModel->setIdPerson($repassID);
                $ret = $personDAO->getPersonByID($personModel);
                if($ret['status']){
                    $repassName = $ret['push']['object']->getName();
                }else{
                    $repassName = "";
                }
                break;
            default:
                //TODO: Get name partner
                $repassName = "";
                break;
        }

        return $repassName;
    }

    /**
     * en_us Returns an array with users data for dropdown list
     * pt_br Retorna um array com dados dos usuários para lista suspensa
     * 
     * @param  string $type
     * @return array
     */
    public function _comboUsers()
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $aRet = array();
        
        $ret = $personDAO->queryPersons(null,null,"ORDER BY tbp.name");
        if($ret['status']){
            $users = $ret['push']['object']->getGridList();
            foreach($users as $k=>$v) {
                $bus =  array(
                    "id" => $v['idperson'],
                    "text" => "{$v['name']}"
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with users data for dropdown list
     * pt_br Retorna um array com dados dos usuários para lista suspensa
     * 
     * @param  string $type
     * @return array
     */
    public function _comboSource()
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $aRet = array();
        
        $ret = $ticketDAO->fetchSources($ticketModel);
        if($ret['status']){
            $users = $ret['push']['object']->getSourceList();
            foreach($users as $k=>$v) {
                $bus =  array(
                    "id" => $v['idsource'],
                    "text" => "{$v['name']}"
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * en_us Authenticates the user by the token
     * pt_br Autentica o usuário pelo token
     *
     * @param  string $ticketCode   Ticket code
     * @param  string $token        Token sended by email
     * @return bool
     */
    function _tokenAuthentication(string $ticketCode,string $token): bool
    {
        $loginSrc = new loginServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketModel->setTicketCode($ticketCode)
                    ->setTicketToken($token);

        // -- Get user data by ticket code and token
        $retUser = $ticketDAO->getUserDataByToken($ticketModel);
        if(!$retUser['status']){
            return false;
        }

        $userID = $retUser['push']['object']->getIdUser();
        $userTypeID = $retUser['push']['object']->getUserTypeId();

        if($userID == 0){
            $this->hdklogger->info("User data not found. Ticket #: {$ticketCode} Token: {$token}",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        if ($userTypeID != 3){// Only attendants
            $this->hdklogger->info("User {$userID} is not an attendant. Ticket #: {$ticketCode} Token: {$token}",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
            

        if ($_SESSION['SES_COD_USUARIO'] == $userID){
            $this->hdklogger->info("User {$userID} is already logged. Ticket #: {$ticketCode} Token: {$token}",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }
            

        $loginSrc->_startSession($userID);
        $loginSrc->_getConfigSession();
        if($_SESSION['SES_MAINTENANCE'] == 1){
            //TODO: make a message page for maintenance
            /* $msg = html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8');
            die($msg); */
        }

        $this->hdklogger->info("User {$userID} logged successfully. Ticket #: {$ticketCode} Token: {$token}",['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__]);
        return true;
    }

    /**
     * en_us Returns an array with areas data to use in a dropdown list
     * pt_br Retorna um array com dados de áreas para usar em um combo
     *
     * @return array
     */
    public function _comboAllAreas(): array
    {
        $hdkServiceDAO = new hdkServiceDAO();
        $hdkServiceModel = new hdkServiceModel();
        
        $ret = $hdkServiceDAO->fetchAreas($hdkServiceModel);        
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
    
    /**
     * en_us Format areas data in HTML to use in a dropdown list
     * pt_br Formata dados de áreas em HTML para usar em um combo
     *
     * @return void
     */
    public function _comboAllAreasHtml()
    {
        $aArea = $this->_comboAllAreas();
        $select = "<option value=''></option>";

        foreach($aArea as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }

    /**
     * en_us Format areas data in HTML to use in a dropdown list
     * pt_br Formata dados de áreas em HTML para usar em um combo
     *
     * @return void
     */
    public function _comboGroupHtml()
    {
        $admSrc = new adminServices();
        $aArea = $admSrc->_comboGroup();
        $select = "<option value=''></option>";

        foreach($aArea as $k=>$v) {
            $select .= "<option value='{$v['id']}'>{$v['text']}</option>";
        }

        return $select;
    }

    /**
     * en_us Format areas data in HTML to use in a dropdown list
     * pt_br Formata dados de áreas em HTML para usar em um combo
     *
     * @return void
     */
    public function _comboPriorityHtml()
    {
        $aArea = $this->_comboPriority();
        $select = "<option value=''></option>";

        foreach($aArea as $k=>$v) {
            $default = ($v['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$v['id']}' {$default}>{$v['text']}</option>";
        }

        return $select;
    }

}