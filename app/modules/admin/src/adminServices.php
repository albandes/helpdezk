<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\holidayDAO;
use App\modules\helpdezk\dao\mysql\departmentDAO;
use App\modules\helpdezk\dao\mysql\groupDAO;

use App\modules\admin\models\mysql\logoModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\personModel;
use App\modules\admin\models\mysql\holidayModel;
use App\modules\helpdezk\models\mysql\departmentModel;
use App\modules\helpdezk\models\mysql\groupModel;

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
     * en_us Returns an array with ID and name of companies
     * pt_br Retorna um array com ID e nome das empresas
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

    public function _comboCountries()
    {
        $personDAO = new personDAO();
        $personModel = new personModel();

        $retCountries = $personDAO->fetchCountries($personModel);
         
        if($retCountries['status']){
            $countries = $retCountries['push']['object']->getCountryList();
            $aRet = array();
            foreach($countries as $k=>$v) {
                $bus =  array(
                    "id" => $v['idcountry'],
                    "text" => $v['printablename']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of states
     * pt_br Retorna um array com Id e nome dos estados
     *
     * @param  mixed $countryID Country Id
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
    
    /**
     * en_us Formats states list in HTML to show in the dropdown
     * pt_br Formata a lista de estados em HTML para mostrar no combo
     *
     * @param  mixed $countryID
     * @return void
     */
    public function _comboStatesHtml($countryID=null)
    {
        $countryID = !$countryID ? $_SESSION['COUNTRY_DEFAULT'] : $countryID;
        $states = $this->_comboStates($countryID);
        $select = '';
        
        foreach($states as $key=>$val) {
            $default = ($val['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$val['id']}' {$default}>{$val['text']}</option>";
        }
        return $select;
    }

    /**
     * en_us Returns an array with ID and name of cities
     * pt_br Retorna um array com Id e nome das cidades
     *
     * @param  mixed $stateID State Id
     * @return array
     */
    public function _comboCities($stateID): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $personModel->setIdState($stateID);

        $retCities = $personDAO->fetchCities($personModel);
        
        if($retCities['status']){
            $cities = $retCities['push']['object']->getCitiesList();
            $aRet = array();
            foreach($cities as $k=>$v) {
                $bus =  array(
                    "id" => $v['idcity'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * en_us Formats cities list in HTML to show in the dropdown
     * pt_br Formata a lista de cidades em HTML para mostrar no combo
     *
     * @param  mixed $stateID
     * @return string
     */
    public function _comboCitiesHtml($stateID)
    {
        $cities = $this->_comboCities($stateID);
        $select = '';

        foreach($cities as $key=>$val) { echo "",print_r($val,true),"\n";
            $default = ($val['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$val['id']}' {$default}>{$val['text']}</option>";
        }
        return $select;
    }

    /**
     * en_us Returns an array with ID and name of neighborhoods
     * pt_br Retorna um array com Id e nome dos bairros
     *
     * @param  mixed $cityID City Id
     * @return array
     */
    public function _comboNeighborhood($cityID): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $personModel->setIdCity($cityID);

        $retNeighborhoods = $personDAO->fetchNeighborhoods($personModel);
         
        if($retNeighborhoods['status']){
            $neighborhoods = $retNeighborhoods['push']['object']->getNeighborhoodList();
            $aRet = array();
            foreach($neighborhoods as $k=>$v) {
                $bus =  array(
                    "id" => $v['idneighborhood'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * en_us Formats neighborhoods list in HTML to show in the dropdown
     * pt_br Formata a lista de bairros em HTML para mostrar no combo
     *
     * @param  mixed $cityID
     * @return string
     */
    public function _comboNeighborhoodHtml($cityID)
    {
        $states = $this->_comboNeighborhood($cityID);
        $select = '';

        foreach($states as $key=>$val) {
            $default = ($val['isdefault'] == 1) ? 'selected="selected"' : '';
            $select .= "<option value='{$val['id']}' {$default}>{$val['text']}</option>";
        }
        return $select;
    }

    /**
     * en_us Returns an array with ID and name of street types
     * pt_br Retorna um array com Id e nome dos tipos de logradouro
     *
     * @return array
     */
    public function _comboStreetType(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $personModel->setLocation($_ENV['DEFAULT_LANG']);

        $retStreetType = $personDAO->fetchStreetTypes($personModel);
         
        if($retStreetType['status']){
            $streetTypes = $retStreetType['push']['object']->getStreetTypeList();
            $aRet = array();
            foreach($streetTypes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtypestreet'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of streets
     * pt_br Retorna um array com Id e nome dos endereços
     *
     * @param  mixed $streetTypeID Street type Id
     * @return array
     */
    public function _comboStreet($streetTypeID): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $personModel->setIdTypeStreet($streetTypeID);

        $retStreet = $personDAO->fetchStreets($personModel);
         
        if($retStreet['status']){
            $streets = $retStreet['push']['object']->getStreetList();
            $aRet = array();
            foreach($streets as $k=>$v) {
                $bus =  array(
                    "id" => $v['idstreet'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * Returns an array with ID and description of employee
     *
     * @return array
     */
    public function _comboEmployee($where=null,$group=null,$order=null)
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $ret = $personDAO->queryPersons($where,$group,$order);
        
        if($ret['status']){
            $employees = $ret['push']['object']->getGridList();
            $aRet = array();
            foreach($employees as $k=>$v) {
                $bus =  array(
                    "id" => $v['idperson'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of modules
     * pt_br Retorna um array com Id e nome dos módulos
     *
     * @return array
     */
    public function _comboModules(): array
    {
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();

        $ret = $moduleDAO->queryModules(null,null,"ORDER BY `name`");
         
        if($ret['status']){
            $aModules = $ret['push']['object']->getGridList();
            $aRet = array();
            foreach($aModules as $k=>$v) {
                $bus =  array(
                    "id" => $v['idmodule'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of companies
     * pt_br Retorna um array com ID e nome das empresas
     *
     * @return array
     */
    public function _comboLoginType(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $retLoginType = $personDAO->fetchLoginTypes($personModel);
        
        if($retLoginType['status']){
            $loginTypes = $retLoginType['push']['object']->getLoginTypeList();
            $aRet = array();
            foreach($loginTypes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtypelogin'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of access level for natural person
     * pt_br Retorna um array com ID e nome dos níveis de acesso para pessoa física
     *
     * @return array
     */
    public function _comboNaturalPersonType(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $retNaturalType = $personDAO->fetchNaturalPersonTypes($personModel);
        
        if($retNaturalType['status']){
            $naturalTypes = $retNaturalType['push']['object']->getNaturalPersonTypeList();
            $aRet = array();
            foreach($naturalTypes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtypeperson'],
                    "text" => $v['name_fmt']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of access level for juridical person
     * pt_br Retorna um array com ID e nome dos níveis de acesso para pessoa jurídica
     *
     * @return array
     */
    public function _comboJuridicalPersonType(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $retJuridicalType = $personDAO->fetchJuridicalPersonTypes($personModel);
        
        if($retJuridicalType['status']){
            $juridicalTypes = $retJuridicalType['push']['object']->getJuridicalPersonTypeList();
            $aRet = array();
            foreach($juridicalTypes as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtypeperson'],
                    "text" => $v['name_fmt']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of permission groups
     * pt_br Retorna um array com ID e nome dos grupos de permissões
     *
     * @return array
     */
    public function _comboPermissionGroups(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $retPermissionGrp = $personDAO->fetchPermissionGroups($personModel);
        
        if($retPermissionGrp['status']){
            $permissionGrp = $retPermissionGrp['push']['object']->getPermissionGroupsList();
            $aRet = array();
            foreach($permissionGrp as $k=>$v) {
                $bus =  array(
                    "id" => $v['idtypeperson'],
                    "text" => $v['name_fmt']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of access level for juridical person
     * pt_br Retorna um array com ID e nome dos níveis de acesso para pessoa jurídica
     *
     * @return array
     */
    public function _comboLocation(): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $retLocation = $personDAO->fetchLocations($personModel);
        
        if($retLocation['status']){
            $locations = $retLocation['push']['object']->getLocationsList();
            $aRet = array();
            foreach($locations as $k=>$v) {
                $bus =  array(
                    "id" => $v['idlocation'],
                    "text" => $v['name']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns array with countries data found by the keyword
     * pt_br Retorna array com dados de países encontrados pela palavra-chave
     *
     * @param  string $keyword  Keyword to search
     * @return array
     */
    public function _searchCountry($keyword): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();

        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($keyword))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "pipeLatinToUtf8(printablename) LIKE pipeLatinToUtf8('%{$v}%') OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = "WHERE ($keyword OR pipeLatinToUtf8(iso) LIKE pipeLatinToUtf8('%{$searchStr}%') OR pipeLatinToUtf8(iso3) LIKE pipeLatinToUtf8('%{$searchStr}%'))";
        $order = "ORDER BY printablename";
        
        $ret = $personDAO->queryCountries($where,null,$order);
        
        if($ret['status']){
            $countries = $ret['push']['object']->getCountryList();
            $aRet = array();
            foreach($countries as $k=>$v) {
                $bus =  array(
                    "id" => $v['idcountry'],
                    "name" => $v['printablename'],
                );

                array_push($aRet,$bus);
            }
        }
        
        return $aRet;
    }

    /**
     * en_us Returns array with states data found by the keyword
     * pt_br Retorna array com dados de estados encontrados pela palavra-chave
     *
     * @param  string $keyword  Keyword to search
     * @return array
     */
    public function _searchState($keyword,$countryId): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $aRet = array();

        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($keyword))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "pipeLatinToUtf8(name) LIKE pipeLatinToUtf8('%{$v}%') OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = (!is_null($countryId) && !empty($countryId)) ? "WHERE ($keyword OR pipeLatinToUtf8(abbr) LIKE pipeLatinToUtf8('%{$searchStr}%')) AND idcountry = {$countryId}" : "";
        $order = "ORDER BY `name` ASC";
        
        $ret = $personDAO->queryStates($where,null,$order);
        
        if($ret['status']){
            $states = $ret['push']['object']->getStateList();
            
            foreach($states as $k=>$v) {
                $bus =  array(
                    "id" => $v['idstate'],
                    "name" => $v['name'],
                );

                array_push($aRet,$bus);
            }
        }
        
        return $aRet;
    }

    /**
     * en_us Returns array with states data found by the keyword
     * pt_br Retorna array com dados de estados encontrados pela palavra-chave
     *
     * @param  string $keyword  Keyword to search
     * @return array
     */
    public function _searchCity($keyword,$stateId): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $aRet = array();

        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($keyword))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "pipeLatinToUtf8(name) LIKE pipeLatinToUtf8('%{$v}%') OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = (!is_null($stateId) && !empty($stateId)) ? "WHERE ($keyword) AND idstate = {$stateId}" : "";
        $order = "ORDER BY `name` ASC";
        
        $ret = $personDAO->queryCities($where,null,$order);
        
        if($ret['status']){
            $cities = $ret['push']['object']->getCitiesList();

            foreach($cities as $k=>$v) {
                $bus =  array(
                    "id" => $v['idcity'],
                    "name" => $v['name'],
                );

                array_push($aRet,$bus);
            }
        }
        
        return $aRet;
    }

    /**
     * en_us Returns array with states data found by the keyword
     * pt_br Retorna array com dados de estados encontrados pela palavra-chave
     *
     * @param  string $keyword  Keyword to search
     * @return array
     */
    public function _searchNeighborhood($keyword,$cityId): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $aRet = array();

        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($keyword))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "pipeLatinToUtf8(name) LIKE pipeLatinToUtf8('%{$v}%') OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = (!is_null($cityId) && !empty($cityId)) ? "WHERE ($keyword) AND idcity = {$cityId}" : "";
        $order = "ORDER BY `name` ASC";
        
        $ret = $personDAO->queryNeighborhoods($where,null,$order);
        
        if($ret['status']){
            $neighborhoods = $ret['push']['object']->getNeighborhoodList();
            
            foreach($neighborhoods as $k=>$v) {
                $bus =  array(
                    "id" => $v['idneighborhood'],
                    "name" => $v['name'],
                );

                array_push($aRet,$bus);
            }
        }
        
        return $aRet;
    }

    /**
     * en_us Returns array with states data found by the keyword
     * pt_br Retorna array com dados de estados encontrados pela palavra-chave
     *
     * @param  string $keyword  Keyword to search
     * @return array
     */
    public function _searchStreet($keyword): array
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $aRet = array();

        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($keyword))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "(pipeLatinToUtf8(a.name) LIKE pipeLatinToUtf8('%{$v}%') OR CONCAT(pipeLatinToUtf8(b.name),' ',pipeLatinToUtf8(a.name)) LIKE pipeLatinToUtf8('%{$v}%')) OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = "AND ($keyword)";
        $order = "ORDER BY `name` ASC";
        
        $ret = $personDAO->queryStreets($where,null,$order);
        
        if($ret['status']){
            $neighborhoods = $ret['push']['object']->getStreetList();
            
            foreach($neighborhoods as $k=>$v) {
                $bus =  array(
                    "id" => $v['idstreet'],
                    "name" => $v['name'],
                    "typeStreetId" => $v['idtypestreet']
                );

                array_push($aRet,$bus);
            }
        }
        
        return $aRet;
    }

    /**
     * en_us Returns an array with ID and name of departements
     * pt_br Retorna um array com Id e nome dos departmentos da empresa selecionada
     *
     * @param  mixed $companyId Company Id
     * @return array
     */
    public function _comboDepartment($companyId): array
    {
        $departmentDAO = new departmentDAO();
        $aRet = array();

        $where = "AND a.idperson = $companyId AND a.status = 'A'";
        $order = "ORDER BY a.name ASC";

        $retDepartment = $departmentDAO->queryDepartment($where,null,$order);
         
        if($retDepartment['status']){
            $departments = $retDepartment['push']['object']->getGridList();
            
            foreach($departments as $k=>$v) {
                $bus =  array(
                    "id" => $v['iddepartment'],
                    "text" => $v['department']
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }
    
    /**
     * en_us Formats departments list in HTML to show in the dropdown
     * pt_br Formata a lista de departamentos em HTML para mostrar no combo
     *
     * @param  mixed $companyId Company Id
     * @return string
     */
    public function _comboDepartmentHtml($companyId)
    {
        $aDepartments = $this->_comboDepartment($companyId);
        $select = '';

        if($aDepartments && count($aDepartments) > 0){
            foreach($aDepartments as $key=>$val) {
                $default = ($val['isdefault'] == 1) ? 'selected="selected"' : '';
                $select .= "<option value='{$val['id']}' {$default}>{$val['text']}</option>";
            }
        }

        return $select;
    }

    /**
     * en_us Returns an array with ID and name of groups
     * pt_br Retorna um array com Id e nome dos grupos
     *
     * @return array
     */
    public function _comboGroup(): array
    {
        $groupDAO = new groupDAO();
        $aRet = array();

        $retGroup = $groupDAO->queryGroups(null,null,"ORDER BY tbp.name ASC");
         
        if($retGroup['status']){
            $groups = $retGroup['push']['object']->getGridList();
            
            foreach($groups as $k=>$v) {
                $bus =  array(
                    "id" => $v['idgroup'],
                    "text" => $v['name']
                );
                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * en_us Returns array with groups data found by the keyword
     * pt_br Retorna array com dados de grupos encontrados pela palavra-chave
     *
     * @param  string $keyword  Keyword to search
     * @return array
     */
    public function _searchGroup($keyword): array
    {
        $groupDAO = new groupDAO();
        $groupModel = new groupModel();
        $aRet = array();

        $searchStr = str_replace(" ","%",addslashes(trim(strip_tags($keyword))));

        $aSearch = explode("%",$searchStr);
        $keyword = "";

        foreach($aSearch as $k=>$v){
            $keyword .= "(pipeLatinToUtf8(tbp.name) LIKE pipeLatinToUtf8('%{$v}%') OR pipeLatinToUtf8(tbp2.name) LIKE pipeLatinToUtf8('%{$v}%')) OR ";
        }
        $keyword = substr($keyword, 0, -4);
        
        $where = "AND ($keyword) ";
        $order = "ORDER BY company ASC, `name` ASC";
        
        $ret = $groupDAO->queryGroups($where,null,$order);
        
        if($ret['status']){
            $groups = $ret['push']['object']->getGridList();
            
            foreach($groups as $k=>$v) {
                $bus =  array(
                    "id" => $v['idgroup'],
                    "name" => $v['name'],
                    "company" => $v['company']
                );

                array_push($aRet,$bus);
            }
        }
        
        return $aRet;
    }

    
}