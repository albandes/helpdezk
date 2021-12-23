<?php

namespace App\modules\main\src;

use App\modules\admin\dao\mysql\localeDAO;
use App\modules\admin\dao\mysql\themeDAO;

use App\modules\admin\models\mysql\localeModel;
use App\modules\admin\models\mysql\themeModel;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class mainServices
{
    /**
     * @var object
     */
    protected $mainLogger;
    
    /**
     * @var object
     */
    protected $mainEmailLogger;

    public function __construct()
    {
        $appSrc = new appServices();

        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->mainLogger  = new Logger('helpdezk');
        $this->mainLogger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->mainEmailLogger = $this->mainLogger->withName('email');
        
    }


    /**
     * Returns an array with ID and name of themes
     *
     * @return array
     */
    public function _comboTheme(): array
    {
        $themeDAO = new themeDAO();
        $themeModel = new themeModel();
        $retThemes = $themeDAO->fetchThemes($themeModel);
        $aRet = array();

        if($retThemes['status']){
            $themes = $retThemes['push']['object']->getGridList();
            
            foreach($themes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtheme'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * Returns an array with ID and name of locales
     *
     * @return array
     */
    public function _comboLocale(): array
    {
        $localeDAO = new localeDAO();
        $localeModel = new localeModel();
        $retLocales = $localeDAO->fetchLocales($localeModel);
        $aRet = array();

        if($retLocales['status']){
            $locales = $retLocales['push']['object']->getGridList();
            
            foreach($locales as $k=>$v) {
                $bus =  array(
                    "id" => $v['idlocale'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }



}