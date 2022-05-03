<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\holidayDAO;

use App\modules\admin\models\mysql\logoModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\personModel;
use App\modules\admin\models\mysql\holidayModel;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class adminServices
{
    /**
     * @var object
     */
    protected $admlogger;
    
    /**
     * @var object
     */
    protected $admEmailLogger;

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

        $this->admlogger  = new Logger('helpdezk');
        $this->admlogger->pushHandler($stream);

        // Clone the first one to only change the channel
        $this->admEmailLogger = $this->admlogger->withName('email');

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

    public function _makeNavAdm($params)
    {
        $listRecords = $this->_makeMenuAdm();
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();
        $moduleModel->setUserID($_SESSION['SES_COD_USUARIO'])
                    ->setUserType($_SESSION['SES_TYPE_PERSON'])
                    ->setName('admin');

        $retInfo = $moduleDAO->getModuleInfoByName($moduleModel);
        if($retInfo['status']){
            $moduleInfo = $retInfo['push']['object'];
            $aHeader = $this->appSrc->_getHeaderData();
            
            $params['displayMenu_Adm'] = 1;
            $params['listMenu_Adm'] = $listRecords;
            $params['moduleLogo'] = $moduleInfo->getHeaderLogo();
            $params['modulePath'] = $moduleInfo->getPath();
        }

        return $params;

    }

    public function _makeMenuAdm(): array
    {
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel(); 
        $activeModules = $moduleDAO->fetchActiveModules($moduleModel);
        $aModules = array();
        
        if($activeModules['status']){
            $aActiveModules = $activeModules['push']['object']->getActiveList();
            $activeModel = $activeModules['push']['object'];
            foreach($aActiveModules as $k=>$v) {      
                $activeModel->setUserID($_SESSION['SES_COD_USUARIO'])
                            ->setUserType($_SESSION['SES_TYPE_PERSON'])
                            ->setIdModule($v['idmodule']);

                $retCategories = $moduleDAO->fetchModuleActiveCategories($activeModel);
                
                if($retCategories['status']){
                    $categoriesObj = $retCategories['push']['object'];
                    $activeCategories = $categoriesObj->getCategoriesList();

                    foreach($activeCategories as $idx=>$val) {
                        $categoriesObj->setCategoryID($val['category_id']);
                        $retPermissions = $moduleDAO->fetchPermissionMenu($categoriesObj);
                        
                        if($retPermissions['status']){
                            $permissionsObj = $retPermissions['push']['object'];
                            $permissionsMod = $permissionsObj->getPermissionsList();
                            
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
                                    $this->admlogger->error("The controller does not exist: {$controller_path}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
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
     * Returns an array with ID and name of companies
     *
     * @return array
     */
    public function _comboCompany(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $retCompanies = $personDAO->fetchCompanies($personModel);
        
        if($retCompanies['status']){
            $companies = $retCompanies['push']['object']->getCompanyList();
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
        $holidayModel = new holidayModel();
        $retLastYear = $holidayDAO->fetchHolidayYears($holidayModel);
        
        if($retLastYear['status']){
            $lastYear = $retLastYear['push']['object']->getYearList();
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

    /**
     * Returns an array with ID and name of states
     *
     * @param  mixed $countryID
     * @return array
     */
    public function _comboStates($countryID=null): array
    {
        $countryID = !$countryID ? $_SESSION['COUNTRY_DEFAULT'] : $countryID;
        $personDAO = new personDAO();

        $where = "WHERE idcountry = $countryID";
        $retStates = $personDAO->queryStates($where);
         
        if($retStates['status']){
            $states = $retStates['push']['object']->getStateList();
            $aRet = array();
            foreach($states as $k=>$v) {
                $bus =  array(
                    "id" => $v['idstate'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }



}