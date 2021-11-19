<?php

namespace App\src;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\src\loginServices;

class appServices
{
    public function _getHelpdezkVersion(): string
    {
        // Read the version.txt file
        $versionFile = $this->_getHelpdezkPath() . "/version.txt";

        if (is_readable($versionFile)) {
            $info = file_get_contents($versionFile, FALSE, NULL, 0, 50);
            if ($info) {
                return trim($info);
            } else {
                return '1.0';
            }
        } else {
            return '1.0';
        }

    }

    public function _getHelpdezkPath()
    {
        $pathInfo = pathinfo(dirname(__DIR__));
        return $pathInfo['dirname'];
    }
    
    public function _getPath()
    {
        $docRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $dirName = str_replace("\\","/",dirname(__DIR__,PATHINFO_BASENAME));
        $path_default = str_replace($docRoot,'',$dirName);
        
        if (substr($path_default, 0, 1) != '/') {
            $path_default = '/' . $path_default;
        }

        if ($path_default == "/..") {
            $path = "";
        } else {
            $path = $path_default;
        }
        
        return $path;
    }
    
    public function _getLayoutTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/layout.latte';
    }
    
    public function _getNavbarTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/nav-main.latte';
    }
    
    public function _getFooterTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/footer.latte';
    }
    
    public function _getDefaultParams(): array
    {
        $loginSrc = new loginServices();
        return array(
            "path"			    => $this->_getPath(),
            "lang_default"	    => $_ENV["DEFAULT_LANG"],
            "layout"		    => $this->_getLayoutTemplate(),
            "version" 		    => $this->_getHelpdezkVersion(),
            "navBar"		    => $this->_getNavbarTemplate(),
            "footer"		    => $this->_getFooterTemplate(),
            "demoVersion" 	    => empty($_ENV['DEMO']) ? 0 : $_ENV['DEMO'], // Demo version - Since January 29, 2020
            "isroot"            => ($_SESSION['SES_COD_USUARIO'] == 1) ? true : false,
            "hasadmin"          => ($_SESSION['SES_TYPE_PERSON'] == 1 && $_SESSION['SES_COD_USUARIO'] != 1) ? true : false,
            "navlogin"          => ($_SESSION['SES_COD_USUARIO'] == 1) ? $_SESSION['SES_NAME_PERSON'] : $_SESSION['SES_LOGIN_PERSON'],
            "adminhome"         => $_ENV['HDK_URL'].'/admin/home/index',
            "adminlogo"         => 'adm_header.png',
            "hashelpdezk"       => $loginSrc->_isActiveHelpdezk(),
            "helpdezkhome"      => $_ENV['HDK_URL'].'/helpdezk/home/index',
            "logout"            => $_ENV['HDK_URL'].'/main/home/logout',
            "id_mask"           => $_ENV['ID_MASK'],
            "ein_mask"          => $_ENV['EIN_MASK'],
            "zip_mask"          => $_ENV['ZIP_MASK'],
            "phone_mask"        => $_ENV['PHONE_MASK'],
            "cellphone_mask"    => $_ENV['CELLPHONE_MASK'],
            "mascdatetime"      => str_replace('%', '', "{$_ENV['DATE_FORMAT']} {$_ENV['HOUR_FORMAT']}"),
            "mascdate"          => str_replace('%', '', $_ENV['DATE_FORMAT']),
            "timesession"       => (!$_SESSION['SES_TIME_SESSION']) ? 600 : $_SESSION['SES_TIME_SESSION'],
            "modules"           => (!isset($_SESSION['SES_COD_USUARIO'])) ? array() :$this->_getModulesByUser($_SESSION['SES_COD_USUARIO'])
        );
    }

    public function _getHelpdezkVersionNumber()
    {
        $exp = explode('-', $this->_getHelpdezkVersion());
        return $exp[2];
    }

    public function _getActiveModules()
    {
        $moduleDAO = new moduleDAO();
        $activeModules = $moduleDAO->fetchActiveModules();
        return (!is_null($activeModules) && !empty($activeModules)) ? $activeModules : false;

    }

    /**
     * Returns header's logo data
	 * 
     * @return array header's logo data (path, width, height)
     */
	public function _getHeaderData(): array 
    {
        $aRet = [];
        
		$logoDAO = new logoDao(); 
        $logo = $logoDAO->getLogoByName("header");
        
        $objLogo = $logo['data'];
        if ($_ENV['EXTERNAL_STORAGE']) {
            $pathLogoImage = $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' . $objLogo->getFileName();
        } else {
            
            $pathLogoImage = $this->_getHelpdezkPath() . '/storage/uploads/logos/' . $objLogo->getFileName();
        }
		
        if (empty($objLogo->getFileName()) or !file_exists($pathLogoImage)){
            $aRet['image'] 	= ($_ENV['EXTERNAL_STORAGE'] ? $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' : $_ENV['HDK_URL'] . '/storage/uploads/logos/') . 'default/login.png';
			$aRet['width'] 	= "227";
			$aRet['height'] = "70";
            $aRet['filename'] = 'default/login.png';
        }else{
            $aRet['image'] 	= ($_ENV['EXTERNAL_STORAGE'] ? $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' : $_ENV['HDK_URL'] . '/storage/uploads/logos/') . $objLogo->getFileName();
			$aRet['width'] 	= $objLogo->getWidth();
			$aRet['height'] = $objLogo->getHeight();
            $aRet['filename'] = $objLogo->getFileName();
		}
        
		return $aRet;
    }

	
	/**
	 * en_us Returns an array with module data for the side menu
     *
     * pt_br Retorna um array com os dados dos módulos para o menu lateral
	 *
	 * @param  int $userID
	 * @return array
	 */
	public function _getModulesByUser(int $userID): array 
    {
        $aRet = [];
		$moduleDAO = new moduleDao(); 
        $aModule = $moduleDAO->fetchExtraModulesPerson($userID);
        if(!is_null($aModule) && !empty($aModule)){
            foreach($aModule as $k=>$v) {
                $prefix = $v['tableprefix'];
                if(!empty($prefix)) {
                    $modSettings = $moduleDAO->fetchConfigDataByModule($prefix);
                    if (!is_null($modSettings) && !empty($modSettings)){
                        $aRet[] = array(
                            'idmodule' => $v['idmodule'],
                            'path' => $v['path'],
                            'class' => $v['class'],
                            'headerlogo' => $v['headerlogo'],
                            'reportslogo' => $v['reportslogo'],
                            'varsmarty' => $v['smarty']
                        );
                    }
                }
            }
        }else{
            return array();
        }
        
        return $aRet;
    }

    /**
     * en_us Check if the user is logged in
     *
     * pt_br Verifica se o usuário está logado
     *
     * @param  mixed $mob
     * @return void
     * 
     * @since November 03, 2017
     */
    public function _sessionValidate($mob=null) {
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
            if($mob){
                echo 1;
            }else{
                $this->_sessionDestroy();
                header('Location:' . $_ENV['HDK_URL'] . '/admin/login');
            }
        }
    }
        
    /**
     * en_us Clear the session variable
     *
     * pt_br Limpa a variável de sessão
     * 
     * @return void
     * 
     * @since November 03, 2017
     */
    public function _sessionDestroy()
    {
        session_start();
        session_unset();
        session_destroy();
    }
    
    /**
     * en_us Return calendar settings
     *
     * pt_br Retorna as configurações do calendário
     *
     * @return array
     */
    public function _datepickerSettings(): array
    {
        $aRet = [];
        switch ($_ENV['DEFAULT_LANG']) {
            case 'pt_br':
                $aRet['dtpFormat'] = "dd/mm/yyyy";
                $aRet['dtpLanguage'] = "pt-BR";
                $aRet['dtpAutoclose'] = true;
                $aRet['dtpOrientation'] = "bottom auto";
                $aRet['dtpickerLocale'] = "bootstrap-datepicker.pt-BR.min.js";
                $aRet['dtSearchFmt'] = 'd/m/Y';
                break;
            case 'es_es':
                $aRet['dtpFormat'] = "dd/mm/yyyy";
                $aRet['dtpLanguage'] = "es";
                $aRet['dtpAutoclose'] = true;
                $aRet['dtpOrientation'] = "bottom auto";
                $aRet['dtpickerLocale'] = "bootstrap-datepicker.es.min.js";
                $aRet['dtSearchFmt'] = 'd/m/Y';
                break;
            default:
                $aRet['dtpFormat'] = "mm/dd/yyyy";
                $aRet['dtpAutoclose'] = true;
                $aRet['dtpOrientation'] = "bottom auto";
                $aRet['dtpickerLocale'] = "";
                $aRet['dtSearchFmt'] = 'm/d/Y';
                break;

        }

        return $aRet;
    }

}