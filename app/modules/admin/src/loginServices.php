<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;

use App\modules\admin\models\mysql\featureModel;
use App\modules\admin\models\mysql\logoModel;
use App\modules\admin\models\mysql\loginModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\personModel;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class loginServices
{
    /**
     * @var object
     */
    protected $loginSrcLogger;
    
    /**
     * @var object
     */
    protected $loginSrcEmailLogger;

    /**
     * @var object
     */
    protected $appSrc;

    /**
     * @var string
     */
    protected $saveMode;

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

        $this->loginSrcLogger  = new Logger('helpdezk');
        $this->loginSrcLogger->pushHandler($stream);

        // Clone the first one to only change the channel
        $this->loginSrcLogger = $this->loginSrcLogger->withName('email');

        // Setting up the save mode of files
        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';
        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->imgDir = "logos/";
            $this->imgBucket = "https://{$bucket}.s3.amazonaws.com/logos/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']) {
                $this->imgDir = $this->appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/logos/');
                $this->imgBucket = $_ENV['EXTERNAL_STORAGE_URL'].'logos/';
            } else {
                $storageDir = $this->appSrc->_setFolder($this->appSrc->_getHelpdezkPath().'/storage/');
                $upDir = $this->appSrc->_setFolder($storageDir.'uploads/');
                $this->imgDir = $this->appSrc->_setFolder($upDir.'logos/');
                $this->imgBucket = $_ENV['HDK_URL']."/storage/uploads/logos/";
            }
        }

    }

    /**
     * Returns login's logo data
	 * 
     * @return array login's logo data (path, width, height)
     */
	public function _getLoginLogoData(): array 
    {
        $logoDAO = new logoDao(); 
        $logoModel = new logoModel();

        $logoModel->setName("login");
        $logo = $logoDAO->getLogoByName($logoModel);
		
        if(!$logo['status']){ //(empty($objLogo->getFileName()) or !){
            $image 	= $this->imgBucket . 'default/login.png';
			$width 	= "227";
			$height = "70";
        }else{
            $objLogo = $logo['push']['object'];            
            
            if($this->saveMode == 'disk'){
                $pathLogoImage = $this->imgDir . $objLogo->getFileName();
                $st = file_exists($pathLogoImage) ? true : false;
            }elseif($this->saveMode == "aws-s3"){
                $pathLogoImage = $this->imgBucket . $objLogo->getFileName();
                $st = (strlen(file_get_contents($this->imgBucket.$value['fileuploaded'])) > 0) ? true : false; 
            }

            if(!$st){
                $image 	= $this->imgBucket . 'default/login.png';
                $width 	= "227";
                $height = "70";
            }else{
                $image 	= $this->imgBucket . $objLogo->getFileName();
			    $width 	= $objLogo->getWidth();
			    $height = $objLogo->getHeight();
            }
		}
        
        $aRet = array(
            'image'  => $image,
            'width'  => $width,
            'height' => $height
        );
        
		return $aRet;
    }	

    // Since November 20
    // Used in user authentication methods. It comes here because it will be used in both admin and helpdezk.
    public function _startSession($idperson): void
    {
        $loginDAO = new loginDAO();
        $loginModel = new loginModel();
        $loginModel->setIdperson($idperson);

        session_start();
        $_SESSION['SES_COD_USUARIO'] = $idperson;
        $_SESSION['REFRESH']         = false;

        //SAVE THE CUSTOMER'S LICENSE
        $_SESSION['SES_LICENSE']    = $_ENV['LICENSE'];
        $_SESSION['SES_ENTERPRISE'] = $_ENV['ENTERPRISE'];
        
        $_SESSION['SES_ADM_MODULE_DEFAULT'] = $this->_pathModuleDefault();
        
        if ($_SESSION['SES_COD_USUARIO'] != 1) {

            if ($this->_isActiveHelpdezk()) {
                
                $userData = $loginDAO->getDataSession($loginModel);
                if($userData['status']){
                    $userObj = $userData['push']['object'];
                    $_SESSION['SES_LOGIN_PERSON']       = $userObj->getLogin();
                    $_SESSION['SES_NAME_PERSON']        = $userObj->getName();
                    $_SESSION['SES_TYPE_PERSON']        = $userObj->getIdtypeperson();
                    $_SESSION['SES_IND_CODIGO_ANOMES']  = true;
                    $_SESSION['SES_COD_EMPRESA']        = $userObj->getIdcompany();
                    $_SESSION['SES_COD_TIPO']           = $userObj->getIdtypeperson();
                
                    $userGroups = $loginDAO->getPersonGroups($loginModel);
                    $_SESSION['SES_PERSON_GROUPS']  = ($userGroups['status']) ? $userGroups['push']['object']->getGroupId() : "";
                }

            } else {
                
                $personDAO = new personDAO();
                $personModel = new personModel();
                $personModel->setIdperson($idperson);

                $userData = $personDAO->getPersonByID($personModel);
                if($userData['status']){
                    $userObj = $userData['push']['object'];
                    $_SESSION['SES_LOGIN_PERSON']   = $userObj->getLogin();
                    $_SESSION['SES_NAME_PERSON']    = $userObj->getName();
                    $_SESSION['SES_TYPE_PERSON']    = $userObj->getIdtypeperson();
                }                

            }

        } else {

            if($this->_isActiveHelpdezk()){

                $_SESSION['SES_NAME_PERSON']        = 'admin';
                $_SESSION['SES_TYPE_PERSON']        = 1;
                $_SESSION['SES_IND_CODIGO_ANOMES']  = true;
                $_SESSION['SES_COD_EMPRESA']        = 1;
                $_SESSION['SES_COD_TIPO']           = 1;

                $userGroups = $loginDAO->fetchAllGroups($loginModel);
                $_SESSION['SES_PERSON_GROUPS'] = ($userGroups['status']) ? $userGroups['push']['object']->getGroupId() : "";

            } else {

                $_SESSION['SES_NAME_PERSON'] = 'admin';
                $_SESSION['SES_TYPE_PERSON'] = 1;
                $_SESSION['SES_COD_EMPRESA'] = 1;

            }
        }

    }

    // Since November 20
    // Used in user authentication methods. It comes here because it will be used in both admin and helpdezk.
    public function _getConfigSession(): void
    {
        $moduleDAO = new moduleDAO();
        $loginDAO = new loginDAO();
        $featureDAO = new featureDAO();

        $moduleModel = new moduleModel();
        $featModel = new featureModel();

        session_start(); 
        if (version_compare($this->appSrc->_getHelpdezkVersionNumber(), '1.0.1', '>' )) {
            
            $activeModules = $this->appSrc->_getActiveModules();
            
            if($activeModules){
                foreach($activeModules as $k=>$v) {
                    $prefix = $v['tableprefix'];
                    if(!empty($prefix)) {
                        $moduleModel->setTableprefix($prefix);
                        $retSettings = $moduleDAO->fetchConfigDataByModule($moduleModel);
                        if ($retSettings['status']){
                            $modSettings = $retSettings['push']['object']->getSettingsList();
                            foreach($modSettings as $key=>$val) {
                                $ses = $val['session_name'];
                                $val = $val['value'];
                                $_SESSION[$prefix][$ses] = $val;
                            }
                        }
                    }
                }
            }

        } else {
            $moduleModel->setTableprefix('hdk');
            $retSettings = $moduleDAO->fetchConfigDataByModule($moduleModel);
            if ($retSettings['status']){
                $modSettings = $retSettings['push']['object']->getSettingsList();
                foreach($modSettings as $key=>$val) {
                    $ses = $val['session_name'];
                    $val = $val['value'];
                    $_SESSION[$ses] = $val;
                    $_SESSION['hdk'][$ses] = $val;
                }
            }
        }
        
        $idperson = $_SESSION['SES_COD_USUARIO'];

        // Global Config Data
        $retGlobalConfig = $loginDAO->fetchConfigGlobalData($featModel);

        if ($retGlobalConfi['status']){
            $globalConfig = $retGlobalConfi['push']['object']->getGlobalSettingsList();
            foreach($globalConfig as $key=>$val) {
                $ses = $val['session_name'];
                $val = $val['value'];
                $_SESSION[$ses] = $val;
            }
        }               
        
        // User config data
        $userSettings = $featureDAO->fetchUserSettings($idperson); //GET COLUMNS OF THE TABLE hdk_tbconfig_user
        if (!is_null($userSettings) && !empty($userSettings)){
            foreach($userSettings as $key=>$val) {
                foreach($val as $k=>$v) {
                    $_SESSION['SES_PERSONAL_USER_CONFIG'][$k] = $v;
                }                
            }
        }

    }

    public function _pathModuleDefault()
    {
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();

        $moduleDefault = $moduleDAO->getModuleDefault($moduleModel); 
        return ($moduleDefault['status']) ? $moduleDefault['push']['object']->getPath() : false;
    }

    public function _isActiveHelpdezk()
    {
        $loginDAO = new loginDAO();
        $loginModel = new loginModel();

        $isActiveHdk = $loginDAO->isActiveHelpdezk($loginModel);
        return ($isActiveHdk['status']) ? $isActiveHdk['push']['object']->getIsActiveHdk() : false;
    }

}