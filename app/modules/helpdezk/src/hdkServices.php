<?php

namespace App\modules\helpdezk\src;

use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;

use App\modules\admin\models\mysql\moduleModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;

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
}