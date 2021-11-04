<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\src\appServices;

class adminServices
{
    public function __construct()
    {

    }

    public function _makeNavAdm()
    {
        $listRecords = $this->_makeMenuAdm();
        /*$moduleinfo = $this->getModuleInfo($this->idmodule);

        $smarty->assign('displayMenu_Adm',1);
        $smarty->assign('listMenu_Adm',$listRecords);
        //
        if ($this->_externalStorage) {

        } else {

        }
        //
        if ($moduleinfo->fields['idmodule'] == 1)
            $smarty->assign('moduleLogo',$this->getHeaderLogoImage());
        else
            $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);*/

    }

    public function _makeMenuAdm()
    {
        $list = '';
        $moduleDAO = new moduleDAO(); 
        $activeModules = $moduleDAO->fetchActiveModules();
        
        if(!is_null($activeModules) && !empty($activeModules)){
            foreach($activeModules as $k=>$v) {      
                //echo __FILE__." ". __LINE__ ."<br>";
                $activeCategories = $moduleDAO->fetchModulesCategoryAtive($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$v['idmodule']);
                
                if(!is_null($activeCategories) && !empty($activeCategories)){
                    $list .= "<li class='dropdown-submenu'>
                                <a tabindex='-1' href='#'>". $v->fields['smarty'] ."</a>
                                <ul class='dropdown-menu'>";
                    
                    foreach($activeCategories as $idx=>$val) {
                        $list .= "<li class='dropdown-submenu'>
                                <a tabindex='-1' href='#'>". $val['cat_smarty'] ."</a>
                                <ul class='dropdown-menu'>";
                        $permissionsMod = $moduleDAO->fetchModulesCategoryAtivefetchPermissionMenu($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$v['idmodule'],$val['category_id']);
                        
                        if(!is_null($permissionsMod) && !empty($permissionsMod)){
                            foreach($permissionsMod as $permidx=>$permval) {
                                $allow = $groupperm->fields['allow'];
                                $path  = $groupperm->fields['path'];
                                $program = $groupperm->fields['program'];
                                $controller = $groupperm->fields['controller'];
                                $prsmarty = $groupperm->fields['pr_smarty'];

                                $checkbar = substr($groupperm->fields['controller'], -1);
                                if($checkbar != "/") $checkbar = "/";
                                else $checkbar = "";

                                $controllertmp = ($checkbar != "") ? $controller : substr($controller,0,-1);
                                $controller_path = 'app/modules/' . $path . '/controllers/' . $controllertmp . 'Controller.php';

                                if (!file_exists($controller_path)) {
                                    $this->logIt("The controller does not exist: " . $controller_path. ' - program: '. $this->program ,3,'general',__LINE__);
                                }else{
                                    if ($allow == 'Y') {

                                        $list .="<li><a href='" . $this->helpdezkUrl . "/".$path."/" . $controller . $checkbar."index' >" . $smarty->getConfigVars($prsmarty) . "</a></li>";
                                    }
                                }
                            }
                        }
                    }

                }
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