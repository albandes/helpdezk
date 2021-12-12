<?php

namespace App\modules\helpdezk\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\holidayDAO;
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

        $moduleInfo = $moduleDAO->getModuleInfoByName('helpdezk');
        if(!is_null($moduleInfo) && !empty($moduleInfo)){
            
            $listRecords = $appSrc->_makeMenuByModule($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$moduleInfo->getIdmodule());
            
            $params['listMenu_1'] = $listRecords;
            $params['moduleLogo'] = ($moduleInfo->getIdmodule() == 1) ? $aHeader['filename'] : $moduleInfo->getHeaderlogo();
            $params['modulePath'] = $moduleInfo->getPath();
        } 

        

        return $params;

    }



}