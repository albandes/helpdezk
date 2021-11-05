<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class adminServices
{
    public function __construct()
    {
        // create a log channel
        $dateFormat = "d/m/Y H:i:s";
        $formatter = new LineFormatter(null, $dateFormat);

        $streamAdmSrc = new StreamHandler('storage/logs/helpdezk.log', Logger::DEBUG);
        $streamAdmSrc->setFormatter($formatter);

        $this->loggerAdmSrc  = new Logger('helpdezk');
        $this->loggerAdmSrc->pushHandler($streamAdmSrc);

    }

    public function _makeNavAdm($params)
    {
        $listRecords = $this->_makeMenuAdm();
        $moduleDAO = new moduleDAO();
        $appSrc = new appServices();

        $moduleInfo = $moduleDAO->getModuleInfoByName('admin');
        if(!is_null($moduleInfo) && !empty($moduleInfo)){
            $aHeader = $appSrc->_getHeaderData();
            
            $params['displayMenu_Adm'] = 1;
            $params['listMenu_Adm'] = $listRecords;
            $params['moduleLogo'] = ($moduleInfo->getIdmodule() == 1) ? $aHeader['filename']: $moduleInfo->getHeaderlogo();
            $params['modulePath'] = $moduleInfo->getPath();
        }

        return $params;

    }

    public function _makeMenuAdm(): string
    {
        $list = '';
        $moduleDAO = new moduleDAO(); 
        $activeModules = $moduleDAO->fetchActiveModules();
        
        if(!is_null($activeModules) && !empty($activeModules)){
            foreach($activeModules as $k=>$v) {      
                
                $activeCategories = $moduleDAO->fetchModulesCategoryAtive($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$v['idmodule']);
                
                if(!is_null($activeCategories) && !empty($activeCategories)){
                    $list .= "<li class='dropdown-submenu'>
                                <a tabindex='-1' href='#'>". $v['smarty'] ."</a>
                                <ul class='dropdown-menu'>";
                    
                    foreach($activeCategories as $idx=>$val) {
                        $list .= "<li class='dropdown-item dropdown-submenu'>
                                    <a tabindex='-1' href='#'>". $val['cat_smarty'] ."</a>
                                    <ul class='dropdown-menu'>";
                        $permissionsMod = $moduleDAO->fetchPermissionMenu($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$v['idmodule'],$val['category_id']);
                        
                        if(!is_null($permissionsMod) && !empty($permissionsMod)){
                            foreach($permissionsMod as $permidx=>$permval) {
                                $allow = $permval['allow'];
                                $path  = $permval['path'];
                                $program = $permval['program'];
                                $controller = $permval['controller'];
                                $prsmarty = $permval['pr_smarty'];

                                $checkbar = substr($permval['controller'], -1);
                                if($checkbar != "/") $checkbar = "/";
                                else $checkbar = "";

                                $controllertmp = ($checkbar != "") ? $controller : substr($controller,0,-1);
                                $controller_path = 'app/modules/'. $path  .'/controllers/' . ucfirst($controllertmp)  . '.php';
                                
                                if (!file_exists($controller_path)) {
                                    $this->loggerAdmSrc->error("The controller does not exist: {$controller_path}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                                }else{
                                    if ($allow == 'Y') {

                                        $list .="<li><a class='dropdown-item' href='" . $_ENV['HDK_URL'] . "/".$path."/" . $controller . $checkbar."index' >" . $prsmarty . "</a></li>";
                                    }
                                }
                            }
                        }
                        $list .= "</ul></li>";
                    }
                    $list .= "</ul></li>";
                }
            }
        }
        return $list;
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



}