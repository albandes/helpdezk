<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\src\appServices;

class loginServices
{
    public function __construct()
    {

    }

    /**
     * Returns login's logo data
	 * 
     * @return array login's logo data (path, width, height)
     */
	public function _getLoginLogoData(): array 
    {
        $aRet = [];
        $appSrc = new appServices();
		$logoDAO = new logoDao(); 
        $logo = $logoDAO->getLogoByName("login");
        
        $objLogo = $logo['data'];
        if ($_ENV['EXTERNAL_STORAGE']) {
            $pathLogoImage = $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' . $objLogo->getFileName();
        } else {
            
            $pathLogoImage = $appSrc->_getHelpdezkPath() . '/storage/uploads/logos/' . $objLogo->getFileName();
        }
		
        if (empty($objLogo->getFileName()) or !file_exists($pathLogoImage)){
            $aRet['image'] 	= ($_ENV['EXTERNAL_STORAGE'] ? $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' : $_ENV['HDK_URL'] . '/storage/uploads/logos/') . 'default/login.png';
			$aRet['width'] 	= "227";
			$aRet['height'] = "70";
        }else{
            $aRet['image'] 	= ($_ENV['EXTERNAL_STORAGE'] ? $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' : $_ENV['HDK_URL'] . '/storage/uploads/logos/') . $objLogo->getFileName();
			$aRet['width'] 	= $objLogo->getWidth();
			$aRet['height'] = $objLogo->getHeight();
		}
        
		return $aRet;
    }	

    // Since November 20
    // Used in user authentication methods. It comes here because it will be used in both admin and helpdezk.
    public function _startSession($idperson)
    {
        $loginDAO = new loginDAO();

        session_start();
        $_SESSION['SES_COD_USUARIO'] = $idperson;
        $_SESSION['REFRESH']         = false;

        //SAVE THE CUSTOMER'S LICENSE
        $_SESSION['SES_LICENSE']    = $_ENV['LICENSE'];
        $_SESSION['SES_ENTERPRISE'] = $_ENV['ENTERPRISE'];
        
        $_SESSION['SES_ADM_MODULE_DEFAULT'] = $this->_pathModuleDefault();
        
        if ($_SESSION['SES_COD_USUARIO'] != 1) {

            if ($this->_isActiveHelpdezk()) {
                
                $userData = $loginDAO->getDataSession($idperson);
                if(!is_null($userData) && !empty($userData)){
                    $_SESSION['SES_LOGIN_PERSON']       = $userData->getLogin();
                    $_SESSION['SES_NAME_PERSON']        = $userData->getName();
                    $_SESSION['SES_TYPE_PERSON']        = $userData->getIdtypeperson();
                    $_SESSION['SES_IND_CODIGO_ANOMES']  = true;
                    $_SESSION['SES_COD_EMPRESA']        = $userData->getIdcompany();
                    $_SESSION['SES_COD_TIPO']           = $userData->getIdtypeperson();
                
                    $userGroups = $loginDAO->getPersonGroups($idperson);
                    $_SESSION['SES_PERSON_GROUPS']  = (!is_null($userGroups) && !empty($userGroups)) ? $userGroups->getGroupId() : "";
                }

            } else {
                
                $personDAO = new personDAO();
                $userData = $personDAO->getPersonByID($idperson);
                if(!is_null($userData) && !empty($userData)){
                    $_SESSION['SES_LOGIN_PERSON']   = $userData->getLogin();
                    $_SESSION['SES_NAME_PERSON']    = $userData->getName();
                    $_SESSION['SES_TYPE_PERSON']    = $userData->getIdtypeperson();
                }                

            }

        } else {

            if($this->_isActiveHelpdezk()){

                $_SESSION['SES_NAME_PERSON']        = 'admin';
                $_SESSION['SES_TYPE_PERSON']        = 1;
                $_SESSION['SES_IND_CODIGO_ANOMES']  = true;
                $_SESSION['SES_COD_EMPRESA']        = 1;
                $_SESSION['SES_COD_TIPO']           = 1;

                $userGroups = $loginDAO->fetchAllGroups();
                $_SESSION['SES_PERSON_GROUPS'] = (!is_null($userGroups) && !empty($userGroups)) ? $userGroups->getGroupId() : "";

            } else {

                $_SESSION['SES_NAME_PERSON'] = 'admin';
                $_SESSION['SES_TYPE_PERSON'] = 1;
                $_SESSION['SES_COD_EMPRESA'] = 1;

            }
        }

    }

    // Since November 20
    // Used in user authentication methods. It comes here because it will be used in both admin and helpdezk.
    public function _getConfigSession()
    {
        $appSrc = new appServices();
        $moduleDAO = new moduleDAO();

        session_start(); 
        if (version_compare($appSrc->_getHelpdezkVersionNumber(), '1.0.1', '>' )) {
            
            $activeModules = $appSrc->_getActiveModules();
            
            if($activeModules){
                foreach($activeModules as $k=>$v) {
                    $prefix = $v['tableprefix'];
                    if(!empty($prefix)) {
                        $modSettings = $moduleDAO->fetchConfigDataByModule($prefix);
                        if (!is_null($activeModules) && !empty($activeModules)){
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
            $data = $this->dbIndex->getConfigData();
            if($data) {
                while (!$data->EOF) {
                    $ses = $data->fields['session_name'];
                    $val = $data->fields['value'];
                    $_SESSION[$ses] = $val;
                    $_SESSION[$prefix][$ses] = $val;
                    $data->MoveNext();
                }
            }
        }
        
        $idperson = $_SESSION['SES_COD_USUARIO'];

        // Global Config Data
        $rsConfig = $this->dbIndex->getConfigGlobalData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION[$ses] = $val;
            $rsConfig->MoveNext();
        }

        // User config data
        $this->loadModel('admin/userconfig_model');
        $cf = new userconfig_model();
        $columns = $cf->getColumns(); //GET COLUMNS OF THE TABLE

        $database = $this->getConfig('db_connect');

        while (!$columns->EOF) {
            if($this->isMysql($database)) {
                $cols[] = strtolower($columns->fields['Field']);
            } elseif($database == 'oci8po') {
                $cols[] = strtolower($columns->fields['column_name']);
            }
            $columns->MoveNext();
        }


        $idconf = $cf->checkConf($idperson); //CHECK IF USER HAVE PERSONAL CONFIG, IF DNO'T HAVE IT'S CREATE

        $getUserConfig = $cf->getConf($cols,$idconf);
        foreach ($cols as $key => $value) {
            $_SESSION['SES_PERSONAL_USER_CONFIG'][$value] = $getUserConfig->fields[$value];
        }

    }

    public function _pathModuleDefault()
    {
        $moduleDAO = new moduleDAO(); 
        $moduleDefault = $moduleDAO->getModuleDefault(); 
        return (!is_null($moduleDefault) && !empty($moduleDefault)) ? $moduleDefault->getPath() : false;
    }

    public function _isActiveHelpdezk()
    {
        $loginDAO = new loginDAO();
        $isActiveHdk = $loginDAO->isActiveHelpdezk();
        return (!is_null($isActiveHdk) && !empty($isActiveHdk)) ? $isActiveHdk->getIsActiveHdk() : false;
    }

}