<?php

namespace App\modules\cronjobs\src;

use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\featureDAO;

use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\loginModel;
use App\modules\admin\models\mysql\featureModel;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class cronJobsServices
{
    /**
     * @var object
     */
    protected $cronJobsLogger;
    
    /**
     * @var object
     */
    protected $cronJobsEmailLogger;

    /**
     * @var object
     */
    protected $appSrc;

    public function __construct()
    {
        $this->appSrc = new appServices();

        /**
         * LOG
         */
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $this->appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->cronJobsLogger  = new Logger('helpdezk');
        $this->cronJobsLogger->pushHandler($stream);

        // Clone the first one to only change the channel
        $this->cronJobsEmailLogger = $this->cronJobsLogger->withName('email');

    }

    public function _isCli()
    {
        if(defined('STDIN'))
        {
            $this->cronJobsLogger->info("CLI environment. STDIN", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }

        if(php_sapi_name() === 'cli')
        {
            $this->cronJobsLogger->info("CLI environment. CLI", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }

        if(array_key_exists('SHELL', $_ENV) ) {
            $this->cronJobsLogger->info("CLI environment. SHELL", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }

        if(empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
        {
            $this->cronJobsLogger->info("CLI environment. Not REMOTE_ADDR", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }

        if(!array_key_exists('REQUEST_METHOD', $_SERVER))
        {
            $this->cronJobsLogger->info("CLI environment. Not REQUEST_METHOD", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }

        $this->cronJobsLogger->info("Not a CLI environment", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        return false;
    }


    /**
     * Get the value of cronJobsLogger
     *
     * @return  object
     */ 
    public function getCronJobsLogger()
    {
        return $this->cronJobsLogger;
    }
    
    /**
     * _setCronSession
     * 
     * en_us Set cron's session
     * pt_br Define a sessão do cron
     *
     * @param  mixed $moduleId
     * @return void
     */
    public function _setCronSession($modulePrefix=null)
    {
        session_start();
        // system settings
        $loginDAO = new loginDAO;
        $featureDTO = new featureModel;

        $retSettings = $loginDAO->fetchConfigGlobalData($featureDTO);
        if(!$retSettings['status']){
            $this->cronJobsLogger->error("Can't get system settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'Error' => $retSettings['push']['message']]);
        }else{
            $sysSettings = $retSettings['push']['object']->getGlobalSettingsList();
            foreach($sysSettings as $key=>$val) {
                $ses = $val['session_name'];
                $val = $val['value'];
                $_SESSION[$ses] = $val;
            }
        }
        
        // module settings
        $moduleDAO = new moduleDAO;
        $moduleDTO = new moduleModel;
        
        if($modulePrefix){// if module's prefix is setup
            $aModules = array($modulePrefix);
        }else{// otherwise, gets all active modules
            $aModules = $this->appSrc->_getActiveModules();
            $aModules = ($aModules) ? array_column($aModules,'tableprefix') : array();
        }
        
        if(count($aModules) > 0){
            foreach($aModules as $key=>$val){
                if(empty($val))
                    continue;

                $moduleDTO->setTablePrefix($val);
        
                $retModuleSettings = $moduleDAO->fetchConfigDataByModule($moduleDTO);
                if(!$retModuleSettings['status']){
                    $this->cronJobsLogger->error("Can't get module settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'Error' => $retModuleSettings['push']['message']]);
                }else{
                    $this->cronJobsLogger->info("Module's settings got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        
                    $modSettings = $retModuleSettings['push']['object']->getSettingsList();
                    foreach($modSettings as $k=>$v) {
                        $sesssionName = $v['session_name'];
                        $sessionVal = $v['value'];
                        $_SESSION[$val][$sesssionName] = $sessionVal;
                    }
                }
            }
        }
    }
}