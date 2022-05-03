<?php

namespace App\modules\exp\src;

use App\modules\admin\dao\mysql\moduleDAO;

use App\modules\admin\models\mysql\moduleModel;

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
        
        $moduleModel = new moduleModel();
        $moduleModel->setUserID($_SESSION['SES_COD_USUARIO'])
                    ->setUserType($_SESSION['SES_TYPE_PERSON'])
                    ->setName('Exemplo');
        
        $retInfo = $moduleDAO->getModuleInfoByName($moduleModel);
        if($retInfo['status']){
            $moduleInfo = $retInfo['push']['object'];
            $moduleModel->setIdModule($moduleInfo->getIdModule());
            $aHeader = $appSrc->_getHeaderData();
            
            $listRecords = $appSrc->_makeMenuByModule($moduleModel);
            
            $params['listMenu_1'] = $listRecords;
            $params['moduleLogo'] = ($moduleInfo->getIdModule() == 1) ? $aHeader['filename'] : $moduleInfo->getHeaderLogo();
            $params['modulePath'] = $moduleInfo->getPath();
        }

        return $params;

    }

    



}