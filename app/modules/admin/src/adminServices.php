<?php

namespace App\modules\admin\src;

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

    public function _makeMenuAdm(): array
    {
        $moduleDAO = new moduleDAO(); 
        $activeModules = $moduleDAO->fetchActiveModules();
        $aModules = array();
        
        if(!is_null($activeModules) && !empty($activeModules)){
            foreach($activeModules as $k=>$v) {      
                
                $activeCategories = $moduleDAO->fetchModulesCategoryAtive($_SESSION['SES_COD_USUARIO'],$_SESSION['SES_TYPE_PERSON'],$v['idmodule']);
                
                if(!is_null($activeCategories) && !empty($activeCategories)){
                    foreach($activeCategories as $idx=>$val) {
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
                                        $aModules[$v['smarty']][$val['cat_smarty']][$prsmarty] = array("url"=>$_ENV['HDK_URL'] . "/".$path."/" . $controller . $checkbar."index", "program_name"=>$prsmarty);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $aModules;
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
        
    /**
     * Returns an array with ID and name of companies
     *
     * @return array
     */
    public function _comboCompany(): array
    {
        $personDAO = new personDAO();
        $companies = $personDAO->fetchCompanies();
        
        if(!is_null($companies) && !empty($companies)){
            $aRet = array();
            foreach($companies as $k=>$v) {
                $bus =  array(
                    "id" => $v['idcompany'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * Returns an array with ID and description of years available on DB
     *
     * @return array
     */
    public function _comboLastYear()
    {
        $holidayDAO = new holidayDAO();
        $lastYear = $holidayDAO->fetchHolidayYears();
        
        if(!is_null($lastYear) && !empty($lastYear)){
            $aRet = array();
            foreach($lastYear as $k=>$v) {
                $bus =  array(
                    "id" => $v['holiday_year'],
                    "text" => $v['holiday_year']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    public function _comboNextYear()
    {
        $date = date("Y");
        $aRet = array();
        for($i = $date; $i <= $date+5; $i++){
            $bus =  array(
                "id" => $i,
                "text" => $i
            );

            array_push($aRet,$bus);                            			
        }

        return $aRet;
    }



}