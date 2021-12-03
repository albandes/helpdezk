<?php

namespace App\modules\exp\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\holidayDAO;
use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class expServices
{
    /**
     * @var object
     */
    protected $explogger;
    
    /**
     * @var object
     */
    protected $expEmailLogger;

    public function __construct()
    {
        $appSrc = new appServices();

        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->explogger  = new Logger('helpdezk');
        $this->explogger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->expEmailLogger = $this->explogger->withName('email');
        
    }

    public function _makeNavExp($params)
    {
        $moduleDAO = new moduleDAO();
        $appSrc = new appServices();
        
        $moduleInfo = $moduleDAO->getModuleInfoByName('Exemplo');
        if(!is_null($moduleInfo) && !empty($moduleInfo)){
            $aHeader = $appSrc->_getHeaderData();
            
            $listRecords = $appSrc->_makeMenuByModule($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$moduleInfo->getIdmodule());
            
            $params['listMenu_1'] = $listRecords;
            $params['moduleLogo'] = ($moduleInfo->getIdmodule() == 1) ? $aHeader['filename'] : $moduleInfo->getHeaderlogo();
            $params['modulePath'] = $moduleInfo->getPath();
        }

        return $params;

    }

    



}