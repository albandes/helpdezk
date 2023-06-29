<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\programDAO;
use App\modules\admin\dao\mysql\permissionDAO;

use App\modules\admin\models\mysql\personModel;
use App\modules\admin\models\mysql\programModel;
use App\modules\admin\models\mysql\permissionModel;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\src\cpfServices;

class Person extends Controller
{
    /**
     * @var int
     */
    protected $programId;

    /**
     * @var array
     */
    protected $aPermissions;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->appSrc->_sessionValidate();

        // set program permissions
        $this->programId = $this->appSrc->_getProgramIdByName(__CLASS__);
        $this->aPermissions = $this->appSrc->_getUserPermissionsByProgram($_SESSION['SES_COD_USUARIO'],$this->programId);
        
    }

    /**
     * en_us Renders the holidays home screen template
     *
     * pt_br Renderiza o template da tela de home de feriados
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();
        
        $params = $this->makeScreenPerson();
		
		$this->view('admin','person',$params);
    }

    /**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
    public function makeScreenPerson($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;
        
        // -- Datepicker settings -- 
        $params = $this->appSrc->_datepickerSettings($params);
        
        // -- Companies --
        $params['cmbCompanies'] = $adminSrc->_comboCompany();

        // -- Login types --
        $params['cmbLoginTypes'] = $adminSrc->_comboLoginType();
        $params['defaultLoginType'] = 3;

        // -- Natural person types --
        $params['cmbAccessLevels'] = $adminSrc->_comboNaturalPersonType();

        // -- Juridical person types --
        $params['cmbJuridicalTypes'] = $adminSrc->_comboJuridicalPersonType();

        // -- Permissions groups --
        $params['cmbPermissionGroups'] = $adminSrc->_comboPermissionGroups();

        // -- Groups for attendant --
        $params['cmbGroups'] = $adminSrc->_comboGroup();

        // -- Locations --
        $params['cmbLocations'] = $adminSrc->_comboLocation();
        
        // -- Street types --
        $params['cmbStreetTypes'] = $adminSrc->_comboStreetType();

        // -- Country default --
        $params['countryDefault'] = $_SESSION['COUNTRY_DEFAULT'];

        // -- Search action --
        if($option == 'idx'){
            $params['cmbFilters'] = $this->comboPersonFilters();
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }

        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalError'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-error.latte';

        if($option=='upd'){
            $params['natureTypeId'] = $obj->getPersonNatureId();
            $params['natureType'] = $this->translator->translate($obj->getPersonNature());
            $params['login'] = $obj->getLogin(); 
            $params['selectedLoginType'] = $obj->getIdTypeLogin();
            $params['personName'] = $obj->getName();
            $params['ssnCpf'] = $obj->getSsnCpf();
            $params['personBirthDt'] = (!empty($obj->getDtBirth()) && $obj->getDtBirth() != '0000-00-00') ? $this->appSrc->_formatDate($obj->getDtBirth()) : '';
            $params['personGender'] = $obj->getGender();
            $params['einCnpj'] = $obj->getEinCnpj();
            $params['juridicalTypeSelected'] = $obj->getIdTypePerson();
            $params['personEmail'] = $obj->getEmail();
            $params['companySelected'] = $obj->getIdCompany();
            $params['departmentSelected'] = $obj->getIdDepartment();
            $params['phone'] = $obj->getTelephone();
            $params['branch'] = $obj->getBranchNumber();
            $params['mobile'] = $obj->getCellphone();
            $params['fax'] = $obj->getFax();
            $params['isUserVip'] = $obj->getUserVip();
            $params['accessLevelSelected'] = $obj->getIdTypePerson();
            $params['aPermissionGroups'] = $obj->getPermissionGroupsIdList();
            $params['aGroups'] = $obj->getPersonGroupsIdList();
            $params['timeValue'] = $obj->getTimeValue();
            $params['overtimeWork'] = $obj->getOvertimeWork();
            $params['locationSelected'] = $obj->getLocationId();
            $params['country'] = ($obj->getIdCountry() == 1) ? $_SESSION['COUNTRY_DEFAULT'] : $obj->getIdCountry();
            $params['state'] = $obj->getIdState();
            $params['city'] = $obj->getIdCity();
            $params['neighborhood'] = $obj->getIdNeighborhood();
            $params['zipcode'] = $obj->getZipCode();
            $params['streetTypeSelected'] = $obj->getIdTypeStreet();
            $params['street'] = ($obj->getIdStreet() == 1) ? '' : $obj->getIdStreet();
            $params['number'] = $obj->getNumber();
            $params['complement'] = $obj->getComplement();
            $params['contactPerson'] = $obj->getContactName();
            $params['observation'] = $obj->getObsevation();
            
            $params['displayNatural'] = ($params['natureTypeId'] == 1) ? "" : "d-none";
            $params['displayJuridical'] = ($params['natureTypeId'] == 1) ? "d-none" : "";
            $params['displayAttendant'] = ($params['natureTypeId'] == 1 && $params['accessLevelSelected'] == 3) ? "" : "d-none";
            $params['displayUser'] = ($params['natureTypeId'] == 1 && $params['accessLevelSelected'] == 2) ? "" : "d-none";

            // -- Departments --
            $params['cmbDepartments'] = $adminSrc->_comboDepartment($params['companySelected']);
        }
      
        return $params;
    }

    public function jsonGrid()
    {
        $personDAO = new personDAO(); 

        $where = "";
        $group = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch($filterIndx){                
                case "name": 
                    $filterIndx = "tbp.name";
                    break;
                case "login": 
                    $filterIndx = "tbp.login";
                    break;
                case "email": 
                    $filterIndx = "tbp.email";
                    break;
                case "company": 
                    $filterIndx = "comp.name";
                    break;
                case "department": 
                    $filterIndx = "dep.name";
                    break;
                default:
                    $filterIndx = $filterIndx;
                break;
            }
            
            $where .= " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " AND (pipeLatinToUtf8(tbp.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(tbp.email) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(tbp.login) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(comp.name) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        //show all records (inactive included)
        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= " AND tbp.status = 'A' ";
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        switch($sortIndx){
            case "personType":
            case "personIcon":
                $sortIndx = "typeperson"; 
                break;
            default:
                $sortIndx = $sortIndx;
                break;
        }
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countPersons = $personDAO->countPersons($where,$group); 
        if($countPersons['status']){
            $total_Records = $countPersons['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $persons = $personDAO->queryPersons($where,$group,$order,$limit);
        
        if($persons['status']){
            $aPersons = $persons['push']['object']->getGridList();

            foreach($aPersons as $k=>$v) {
                $statusFmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                switch($v['idtypeperson']){
                    case 1:
                        $icon = "<i class='fa fa-tools'></i>";
                        break;
                    case 2:
                        $icon = "<i class='fa fa-user'></i>";
                        break;
                    case 3:
                        $icon = "<i class='fa fa-headset'></i>";
                        break;
                    case 4:
                        $icon = "<i class='fa fa-building'></i>";
                        break;
                    default:
                        $icon = "<i class='fa fa-hands-helping'></i>";
                        break;
                }

                $data[] = array(
                    'idperson'      => $v['idperson'],
                    'personIcon'    => $icon,
                    'name'          => $v['name'],
                    'login'         => $v['login'],
                    'email'         => $v['email'],
                    'personType'    => $v['typeperson'],
                    'personTypeId'  => $v['idtypeperson'],
                    'company'       => $v['company'],
                    'department'    => $v['department'],
                    'status'        => $statusFmt,
                    'statusVal'     => $v['status']    
                );
            }
            
            $aRet = array(
                "totalRecords" => $total_Records,
                "curPage" => $pq_curPage,
                "data" => $data
            );

            echo json_encode($aRet);
            
        }else{
            echo json_encode(array());            
        }
    }

    /**
     * Returns an array with ID and name of filters
     *
     * @return array
     */
    public function comboPersonFilters(): array
    {
        $aRet = array(
            array("id" => 'name',"text"=>$this->translator->translate('Name'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'login',"text"=>$this->translator->translate('Login'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'email',"text"=>$this->translator->translate('email'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'company',"text"=>$this->translator->translate('Company'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'department',"text"=>$this->translator->translate('Department'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the holidays add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenPerson('add');
        
        $this->view('admin','person-create',$params);
    }

    /**
     * en_us Renders the person update screen
     * pt_br Renderiza o template da tela de atualização do cadastro
     */
    public function formUpdate($personId=null)
    {
        // blocks if the user does not have permission to edit
        if($this->aPermissions[3] != "Y")
            $this->appSrc->_accessDenied();

        $personDAO = new personDAO();
        $personModel = new personModel();
        $personModel->setIdPerson($personId);

        $ret = $personDAO->getPerson($personModel);
        
        $params = $this->makeScreenPerson('upd',$ret['push']['object']);
        $params['personId'] = $personId;
      
        $this->view('admin','person-update',$params);
    }

    /**
     * en_us Write the person or company information to the DB 
     * pt_br Grava no BD as informações da pessoa ou empresa
     */
    public function createPerson()
    {
        $personDAO = new personDAO();
        $personModel = new personModel();

        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $charSearch = array("(",")","-"," ",".");
        $charReplace = array("","","","","");
        $aAdminOpe = array(1,3);

        $natureType = $_POST['natureType'];
        $fax = ($natureType == 1) ? '' : str_replace($charSearch,$charReplace,trim(strip_tags($_POST['fax'])));
        $mobile = ($natureType == 1) ? str_replace($charSearch,$charReplace,trim(strip_tags($_POST['mobile']))) : '';
        $vip =  ($natureType == 1) ? ((isset($_POST['isUserVip'])) ? 'Y' : 'N') : 'N';
        $timeValue = ($natureType == 1 && in_array($_POST['cmbAccessLevel'],$aAdminOpe) && !empty($_POST['timeValue'])) ? $_POST['timeValue'] : 0;
        $overtimeWork = ($natureType == 1 && in_array($_POST['cmbAccessLevel'],$aAdminOpe) && !empty($_POST['overtimeWork'])) ? $_POST['overtimeWork'] : 0;
        $locationId = ($natureType == 1 && $_POST['cmbAccessLevel'] == 2) ? ((isset($_POST['cmbLocation']) && (!empty($_POST['cmbLocation']) && $_POST['cmbLocation'] > 0)) ? $_POST['cmbLocation'] : 0) : 0;

        $personModel->setLogin(($natureType == 1) ? strtolower(trim(strip_tags($_POST['login']))) : '')
                    ->setIdTypeLogin(($natureType == 1) ? $_POST['cmbLoginType'] : 3)
                    ->setPersonNatureId($natureType)
                    ->setPassword(($natureType == 1) ? trim(strip_tags($_POST['password'])) : '')
                    ->setName(trim(strip_tags($_POST['personName'])))
                    ->setEmail(trim(strip_tags($_POST['personEmail'])))
                    ->setTelephone(str_replace($charSearch,$charReplace,trim(strip_tags($_POST['phone']))))
                    ->setBranchNumber(str_replace($charSearch,$charReplace,trim(strip_tags($_POST['branch']))))
                    ->setFax($fax)
                    ->setCellphone($mobile)
                    ->setUserVip($vip)
                    ->setIdTypePerson(($natureType == 1) ? $_POST['cmbAccessLevel'] : $_POST['cmbJuridicalType'])
                    ->setTimeValue($timeValue)
                    ->setOvertimeWork($overtimeWork)
                    ->setLocationId($locationId)
                    ->setChangePasswordFlag(($natureType == 1 && isset($_POST['changePassword'])) ? 1 : 0)
                    ->setThemeId(1)
                    ->setSsnCpf(preg_replace('/[^0-9]/','',trim(strip_tags($_POST['ssnCpf']))))
                    ->setDtBirth((!empty($_POST['personBirthDt'])) ? $this->appSrc->_formatSaveDate($_POST['personBirthDt']) : '0000-00-00')
                    ->setGender((isset($_POST['personGender'])) ? $_POST['personGender'] : '')
                    ->setIdDepartment(($natureType == 1) ? $_POST['cmbDepartment'] : 0)
                    ->setEinCnpj(preg_replace('/[^0-9]/','',trim(strip_tags($_POST['einCnpj']))))
                    ->setDepartment(trim(strip_tags($_POST['defaultDepartment'])))
                    ->setContactName(trim(strip_tags($_POST['contactPerson'])))
                    ->setObsevation(trim(strip_tags($_POST['observation'])))
                    ->setPermissionGroupsList((isset($_POST['cmbPermissionGroups'])) ? $_POST['cmbPermissionGroups'] : array())
                    ->setPersonGroupsList((isset($_POST['cmbGroup'])) ? $_POST['cmbGroup'] : array())
                    ->setIdCity((isset($_POST['fillAddress'])) ? $_POST['city'] : 1)
                    ->setIdNeighborhood((isset($_POST['fillAddress'])) ? $_POST['neighborhood'] : 1)
                    ->setIdStreet((isset($_POST['fillAddress'])) ? $_POST['street'] : 1)
                    ->setAddressTypeId(($natureType == 1) ? 2 : 3)
                    ->setZipCode((isset($_POST['fillAddress'])) ? str_replace($charSearch,$charReplace,trim(strip_tags($_POST['zipcode']))) : '')
                    ->setNumber((isset($_POST['fillAddress'])) ? trim(strip_tags($_POST['number'])) : '')
                    ->setComplement((isset($_POST['fillAddress'])) ? trim(strip_tags($_POST['complement'])) : '');
                    
        $ins = $personDAO->savePersonData($personModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            
            $retInfo = $personDAO->getPerson($ins['push']['object']);
            if($retInfo['status']){
                $personID = $retInfo['push']['object']->getIdPerson();
                $personName = $retInfo['push']['object']->getName();
                $natureID = $retInfo['push']['object']->getPersonNatureId();
                $nature = $retInfo['push']['object']->getPersonNature();
                $login = $retInfo['push']['object']->getLogin();
                $accessLevel = $retInfo['push']['object']->getTypePerson();
            }else{
                $personID = "";
                $personName = "";
                $natureID = "";
                $nature = "";
                $login = "";
                $accessLevel = "";
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $personID = "";
            $personName = "";
            $natureID = "";
            $nature = "";
            $login = "";
            $accessLevel = "";
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "personId" => $personID,
            "personName" => $personName,
            "natureId" => $natureID,
            "nature" => $nature,
            "login" => $login,
            "accessLevel" => $accessLevel
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Update the holiday information to the DB
     *
     * pt_br Atualiza no BD as informações do feriado
     */
    public function updatePerson()
    {
        $personDAO = new personDAO();
        $personModel = new personModel();

        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $charSearch = array("(",")","-"," ",".");
        $charReplace = array("","","","","");
        $aAdminOpe = array(1,3);

        $natureType = $_POST['natureType'];
        $fax = ($natureType == 1) ? '' : str_replace($charSearch,$charReplace,trim(strip_tags($_POST['fax'])));
        $mobile = ($natureType == 1) ? str_replace($charSearch,$charReplace,trim(strip_tags($_POST['mobile']))) : '';
        $vip =  ($natureType == 1) ? ((isset($_POST['isUserVip'])) ? 'Y' : 'N') : 'N';
        $timeValue = ($natureType == 1 && in_array($_POST['cmbAccessLevel'],$aAdminOpe) && !empty($_POST['timeValue'])) ? $_POST['timeValue'] : 0;
        $overtimeWork = ($natureType == 1 && in_array($_POST['cmbAccessLevel'],$aAdminOpe) && !empty($_POST['overtimeWork'])) ? $_POST['overtimeWork'] : 0;
        $locationId = ($natureType == 1 && $_POST['cmbAccessLevel'] == 2) ? ((isset($_POST['cmbLocation']) && (!empty($_POST['cmbLocation']) && $_POST['cmbLocation'] > 0)) ? $_POST['cmbLocation'] : 0) : 0;

        $personModel->setIdPerson($_POST['personId'])
                    ->setIdTypeLogin(($natureType == 1) ? $_POST['cmbLoginType'] : 3)
                    ->setPersonNatureId($natureType)
                    ->setName(trim(strip_tags($_POST['personName'])))
                    ->setEmail(trim(strip_tags($_POST['personEmail'])))
                    ->setTelephone(str_replace($charSearch,$charReplace,trim(strip_tags($_POST['phone']))))
                    ->setBranchNumber(str_replace($charSearch,$charReplace,trim(strip_tags($_POST['branch']))))
                    ->setFax($fax)
                    ->setCellphone($mobile)
                    ->setUserVip($vip)
                    ->setIdTypePerson(($natureType == 1) ? $_POST['cmbAccessLevel'] : $_POST['cmbJuridicalType'])
                    ->setTimeValue($timeValue)
                    ->setOvertimeWork($overtimeWork)
                    ->setLocationId($locationId)
                    ->setSsnCpf(preg_replace('/[^0-9]/','',trim(strip_tags($_POST['ssnCpf']))))
                    ->setDtBirth((!empty($_POST['personBirthDt'])) ? $this->appSrc->_formatSaveDate($_POST['personBirthDt']) : '0000-00-00')
                    ->setGender((isset($_POST['personGender'])) ? $_POST['personGender'] : '')
                    ->setIdDepartment(($natureType == 1) ? $_POST['cmbDepartment'] : 0)
                    ->setEinCnpj(preg_replace('/[^0-9]/','',trim(strip_tags($_POST['einCnpj']))))
                    ->setContactName(trim(strip_tags($_POST['contactPerson'])))
                    ->setObsevation(trim(strip_tags($_POST['observation'])))
                    ->setPermissionGroupsList((isset($_POST['cmbPermissionGroups'])) ? $_POST['cmbPermissionGroups'] : array())
                    ->setPersonGroupsList((isset($_POST['cmbGroup'])) ? $_POST['cmbGroup'] : array())
                    ->setIdCity((!empty($_POST['city'])) ? $_POST['city'] : 1)
                    ->setIdNeighborhood((!empty($_POST['neighborhood'])) ? $_POST['neighborhood'] : 1)
                    ->setIdStreet((!empty($_POST['street'])) ? $_POST['street'] : 1)
                    ->setZipCode((!empty($_POST['zipcode'])) ? str_replace($charSearch,$charReplace,trim(strip_tags($_POST['zipcode']))) : '')
                    ->setNumber((!empty($_POST['number'])) ? trim(strip_tags($_POST['number'])) : '')
                    ->setComplement((!empty($_POST['complement'])) ? trim(strip_tags($_POST['complement'])) : '');
        
        $upd = $personDAO->updatePersonData($personModel);
        
        if(!$upd['status']){
            $st = false;
        }else{
            $st = true;
        }        
        
        $aRet = array(
            "success" => $st,
            "message" => $upd['push']['message'],
            "personId" => (!is_null($upd['push']['object']) && !empty($upd['push']['object'])) ? $personModel->getIdPerson() : ""
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Returns states list in HTML to reload combo
     *
     * @return void
     */
    function ajaxStates()
    {
        $adminSrc = new adminServices();
        echo $adminSrc->_comboStatesHtml($_POST['countryId']);

    }

    function ajaxCities()
    {
        $adminSrc = new adminServices();
        echo $adminSrc->_comboCitiesHtml($_POST['stateId']);

    }

    function ajaxNeighborhood()
    {
        $adminSrc = new adminServices();
        echo $adminSrc->_comboNeighborhoodHtml($_POST['cityId']);

    }

    /**
     * en_us Returns list with data found by the keyword
     * pt_br Retorna a lista com os dados encontrados pela palavra-chave
     *
     * @return void
     */
    function searchCountry()
    {
        $adminSrc = new adminServices();
        
        echo json_encode($adminSrc->_searchCountry($_POST['keyword']));
    }

    /**
     * en_us Returns list with data found by the keyword
     * pt_br Retorna a lista com os dados encontrados pela palavra-chave
     *
     * @return void
     */
    function searchState()
    {
        $adminSrc = new adminServices();
        
        echo json_encode($adminSrc->_searchState($_POST['keyword'],$_POST['countryId']));
    }

    /**
     * en_us Returns list with city's data found by the keyword
     * pt_br Retorna a lista com os dados da cidade encontrados pela palavra-chave
     *
     * @return void
     */
    function searchCity()
    {
        $adminSrc = new adminServices();
        
        echo json_encode($adminSrc->_searchCity($_POST['keyword'],$_POST['stateId']));
    }

    /**
     * en_us Returns list with neighborhood's data found by the keyword
     * pt_br Retorna a lista com os dados do bairro encontrados pela palavra-chave
     *
     * @return void
     */
    function searchNeighborhood()
    {
        $adminSrc = new adminServices();
        
        echo json_encode($adminSrc->_searchNeighborhood($_POST['keyword'],$_POST['cityId']));
    }

    /**
     * en_us Returns list with street's data found by the keyword
     * pt_br Retorna a lista com os dados da rua encontrados pela palavra-chave
     *
     * @return void
     */
    function searchStreet()
    {
        $adminSrc = new adminServices();
        
        echo json_encode($adminSrc->_searchStreet($_POST['keyword']));
    }

    /**
     * 
     * en_us Check if the operator has already been registered before
     *
     * pt_br Verifica se o operador já foi cadastrado anteriormente
     */
    function checkLogin(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        
        $login = trim(strip_tags($_POST['login']));

        $where = "AND tbp.login = '{$login}'";

        $check =  $personDAO->queryPersons($where);
        if(!$check['status']){
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('Login_exists'));
        }else{
            echo json_encode(true);
        }
    }
    
    /**
     * en_us Returns a list of deparment in HTML
     * pt_br Retorna uma lista de departamentos em HTML
     *
     * @return void
     */
    function ajaxDepartment()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $adminSrc = new adminServices();
        echo $adminSrc->_comboDepartmentHtml($_POST['companyId']);
    }
    
    /**
     * en_us Changes person's status
     * pt_br Muda o status da pessoa
     *
     * @return void
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdPerson($_POST['personId'])
                    ->setStatus(($_POST['newStatus'] == "I") ? "N" : $_POST['newStatus']);
        
        $upd = $personDAO->changePersonStatus($personModel);
        if(!$upd['status']){
            return false;
        }

        $this->logger->info("Person # {$_POST['personId']} status was updated.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Renders manage permissions screen
     * pt_br Renderiza a tela de gerenciamento de permissiões
     */
    public function managePermissions($personId=null)
    {
        $personDAO = new personDAO();
        $personModel = new personModel();
        $personModel->setIdPerson($personId);

        $ret = $personDAO->getPerson($personModel);
        
        $params = $this->makeScreenPerson('permission',$ret['push']['object']);
        $params['personId'] = $personId;
        $params['lblPersonName'] = $ret['push']['object']->getName();
      
        $this->view('admin','person-manage-permission',$params);
    }
    
    /**
     * en_us Returns person's permissions to display in grid
     * pt_br Retorna as permissões da pessoa para exibir no grid
     *
     * @return void
     */
    public function jsonGridPermissions()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $programDAO = new programDAO(); 

        $personId = $_POST['personId'];
        $where = "";
        $group = "";
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " WHERE (pipeLatinToUtf8(tbp.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(tbm.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(pvoc.key_value) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(mvoc.key_value) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";

        switch($sortIndx){
            case "module":
                $sortIndx = "module_fmt {$sortDir}, name_fmt ASC"; 
                break;
            case "program":
                $sortIndx = "module_fmt {$sortDir}, name_fmt {$sortDir}"; 
                break;
            default:
                $sortIndx = "{$sortIndx} {$sortDir}";
                break;
        }

        $order = "ORDER BY {$sortIndx}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countPrograms = $programDAO->countPrograms($where,$group); 
        if($countPrograms['status']){
            $total_Records = $countPrograms['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $programs = $programDAO->queryPrograms($where,$group,$order,$limit);
        
        if($programs['status']){
            $aPrograms = $programs['push']['object']->getGridList();

            foreach($aPrograms as $k=>$v) {
                $retPermissions = $this->makePermissionOptions($v['idprogram'],$personId);

                $data[] = array(
                    'idprogram'     => $v['idprogram'],
                    'module'        => $v['module_fmt'],
                    'program'       => $v['name_fmt'],
                    'access'        => ($retPermissions['success']) ? $retPermissions['access'] : "",
                    'new'           => ($retPermissions['success']) ? $retPermissions['new'] : "",
                    'edit'          => ($retPermissions['success']) ? $retPermissions['edit'] : "",
                    'delete'        => ($retPermissions['success']) ? $retPermissions['delete'] : "",
                    'export'        => ($retPermissions['success']) ? $retPermissions['export'] : "",
                    'email'         => ($retPermissions['success']) ? $retPermissions['email'] : "",
                    'sms'           => ($retPermissions['success']) ? $retPermissions['sms'] : "",
                    'idperson'      => $personId  
                );
            }
            
            $aRet = array(
                "totalRecords" => $total_Records,
                "curPage" => $pq_curPage,
                "data" => $data
            );

            echo json_encode($aRet);
            
        }else{
            echo json_encode(array());            
        }
    }
    
    /**
     * en_us Make a list of permission by program in HTML
     * pt_br Cria uma lista de permissões por programa em HTML
     *
     * @param  int $programId
     * @return array
     */
    public function makePermissionOptions($programId,$personId): array
    {
        $permissionDAO = new permissionDAO();
        $permissionModel = new permissionModel();
        $permissionModel->setProgramId($programId)
                        ->setPersonId($personId);

        $ret = $permissionDAO->fetchDefaultPermissionsByProgram($permissionModel);
        if(!$ret['status']){
            $this->logger->error("Could not get default permissions", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return array("success"=>false);
        }
        $aDefPermissions = $ret['push']['object']->getDefaultPermissionList();

        foreach($aDefPermissions as $k=>$v){
            $aDefPerm[$v['idaccesstype']] = $v['idaccesstype'];
        }

        for($accessType = 1;$accessType <=7;$accessType++){
            $ret['push']['object']->setAccessTypeId($accessType);
            
            $retUserPermission = $permissionDAO->getUserPermission($ret['push']['object']);
            if(!$retUserPermission['status']){
                $this->logger->error("Could not get user's permissions", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return array("success"=>false);
            }

            $allow = $retUserPermission['push']['object']->getAllow();
            switch ($accessType) {
                case 1 :
                    $disabled = (!$aDefPerm[1]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $access = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
                case 2 :
                    $disabled = (!$aDefPerm[2]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $new = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
                case 3 :
                    $disabled = (!$aDefPerm[3]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $edit = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
                case 4 :
                    $disabled = (!$aDefPerm[4]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $delete = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
                case 5 :
                    $disabled = (!$aDefPerm[5]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $export = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
                case 6 :
                    $disabled = (!$aDefPerm[6]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $email = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
                case 7 :
                    $disabled = (!$aDefPerm[7]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $sms = "<input type='checkbox' $disabled $checked id='{$accessType}-{$programId}-{$personId}' name='{$accessType}-{$programId}-{$personId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$personId});'>";
                    break;
            }
        }

        $aRet = array(
            "success" => true,
            "access"        => $access,
            "new"           => $new,
            "edit"          => $edit,
            "delete"        => $delete,
            "export"        => $export,
            "email"         => $email,
            "sms"           => $sms,
        );

        return $aRet;
    }

    /**
     * en_us Changes person's status
     * pt_br Muda o status da pessoa
     *
     * @return void
     */
    function grantPermission()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $permissionDAO = new permissionDAO();
        $permissionModel = new permissionModel();

        //Setting up the model
        $permissionModel->setProgramId($_POST['programId'])
                        ->setPersonId($_POST['personId'])
                        ->setAccessTypeId($_POST['accessTypeId'])
                        ->setAllow($_POST['allow']);
        
        $upd = $permissionDAO->grantUserPermission($permissionModel);
        if(!$upd['status']){
            return false;
        }

        $logMsg = ($_POST['allow'] == 'Y') ? "granted" : "removed";
        $this->logger->info("Permission for program # {$_POST['programId']} and user # {$_POST['personId']} was $logMsg.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Shows the attendant's groups
     * pt_br Mostra os grupos do atendente
     *
     * @return void
     */
    function modalAttendantGroups()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ret = $this->makeAttendantGroupHtml($_POST['personId']);
        $st = ($ret) ?  true : false;
        $html = ($ret) ?  $ret : "";

        $aRet = array(
            "success" => true,
            "html"    => $html
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Shows the attendant's groups
     * pt_br Mostra os grupos do atendente
     *
     * @return string
     */
    public function makeAttendantGroupHtml($personId)
    {
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdPerson($personId);
        
        $ret = $personDAO->fetchAttendantGroups($personModel);
        if(!$ret['status']){
            return false;
        }

        $aGroups =  $ret['push']['object']->getPersonGroupsList();
        $html = "";

        if(count($aGroups) > 0){
            foreach($aGroups as $k=>$v){
                $html .= "<tr>
                            <td>{$v['company_name']}</td>
                            <td>
                                {$v['name']}
                                <input type='hidden' name='admAttGrps[]' id='admAttGrps_{$v['idgroup']}' value='{$v['idgroup']}'>
                            </td>
                            <td><a href='javascript:;' onclick='removeGroup({$_POST['personId']},{$v['idgroup']})' class='btn btn-danger'><i class='fa fa-times'></i></a></td>
                        </tr>"; 
            }
        }

        return $html;
    }

    /**
     * en_us Returns list with street's data found by the keyword
     * pt_br Retorna a lista com os dados da rua encontrados pela palavra-chave
     *
     * @return void
     */
    function searchGroup()
    {
        $adminSrc = new adminServices();
        
        echo json_encode($adminSrc->_searchGroup($_POST['keyword']));
    }

    /**
     * en_us Links the group to the attendant
     * pt_br Vincula o grupo ao atendente
     *
     * @return void
     */
    function insertAttendantGroup()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdPerson($_POST['personId'])
                    ->setGroupId($_POST['groupId']);
        
        $ret = $personDAO->linkAttendantGroup($personModel);
        if(!$ret['status']){
            return false;
        }else{
            $retHtml = $this->makeAttendantGroupHtml($_POST['personId']);
            $html = ($retHtml) ?  $retHtml : "";
        }

        $aRet = array(
            "success" => true,
            "html"    => $html
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Links the group to the attendant
     * pt_br Vincula o grupo ao atendente
     *
     * @return void
     */
    function removeAttendantGroup()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdPerson($_POST['personId'])
                    ->setGroupId($_POST['groupId']);
        
        $ret = $personDAO->deleteAttendantGroup($personModel);
        if(!$ret['status']){
            return false;
        }else{
            $retHtml = $this->makeAttendantGroupHtml($_POST['personId']);
            $html = ($retHtml) ?  $retHtml : "";
        }

        $aRet = array(
            "success" => true,
            "html"    => $html
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Adds a new localization in DB
     * pt_br Adiciona uma nova localização no BD
     *
     * @return void
     */
    function insertLocation()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setLocation(trim(strip_tags($_POST['localizationName'])));
        
        $ret = $personDAO->insertLocation($personModel);
        if(!$ret['status']){
            $st = false;
            $localizationId = "";

            $this->logger->error("Could not save a new localization. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $localizationId = $ret['push']['object']->getLocationId();

            $this->logger->info("A new localization was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st,
            "localizationId"    => $localizationId
        );

        echo json_encode($aRet);

    }

     /**
     * en_us Change the user's password in DB
     * pt_br Altera a senha do usuário no BD
     *
     * @return void
     */
    function changePassword()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdPerson($_POST['personId'])
                    ->setPassword(trim(strip_tags($_POST['newPassword'])))
                    ->setChangePasswordFlag($_POST['changePassFlag']);
        
        $ret = $personDAO->updatePassword($personModel);
        if(!$ret['status']){
            $st = false;

            $this->logger->error("Could not save the new password. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;

            $this->logger->info("The new password was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Adds a new state in DB
     * pt_br Adiciona um novo estado no BD
     *
     * @return void
     */
    function insertState()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdCountry($_POST['countryId'])
                    ->setState(trim(strip_tags($_POST['stateName'])))
                    ->setStateAbbr(trim(strip_tags($_POST['stateAbbreviation'])));
        
        $ret = $personDAO->insertState($personModel);
        if(!$ret['status']){
            $st = false;
            $stateId = "";

            $this->logger->error("Could not save a new state. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $stateId = $ret['push']['object']->getIdState();

            $this->logger->info("A new state was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st,
            "stateId"    => $stateId
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Adds a new city in DB
     * pt_br Adiciona uma nova cidade no BD
     *
     * @return void
     */
    function insertCity()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdState($_POST['stateId'])
                    ->setCity(trim(strip_tags($_POST['cityName'])));
        
        $ret = $personDAO->insertCity($personModel);
        if(!$ret['status']){
            $st = false;
            $cityId = "";

            $this->logger->error("Could not save a new city. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $cityId = $ret['push']['object']->getIdCity();

            $this->logger->info("A new city was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st,
            "cityId"    => $cityId
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Adds a new city in DB
     * pt_br Adiciona uma nova cidade no BD
     *
     * @return void
     */
    function insertNeighborhood()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdCity($_POST['cityId'])
                    ->setNeighborhood(trim(strip_tags($_POST['neighborhoodName'])));
        
        $ret = $personDAO->insertNeighborhood($personModel);
        if(!$ret['status']){
            $st = false;
            $neighborhoodId = "";

            $this->logger->error("Could not save a new neighborhood. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $neighborhoodId = $ret['push']['object']->getIdNeighborhood();

            $this->logger->info("A new neighborhood was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st,
            "neighborhoodId"    => $neighborhoodId
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Adds a new city in DB
     * pt_br Adiciona uma nova cidade no BD
     *
     * @return void
     */
    function insertStreet()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdCity($_POST['cityId'])
                    ->setIdTypeStreet($_POST['streetTypeId'])
                    ->setStreet(trim(strip_tags($_POST['streetName'])));
        
        $ret = $personDAO->insertStreet($personModel);
        if(!$ret['status']){
            $st = false;
            $streetTypeId = "";
            $streetId = "";

            $this->logger->error("Could not save a new street. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $streetTypeId = $ret['push']['object']->getIdTypeStreet();
            $streetId = $ret['push']['object']->getIdStreet();

            $this->logger->info("A new street was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st,
            "streetTypeId"    => $streetTypeId,
            "streetId"    => $streetId
        );

        echo json_encode($aRet);

    }
    
    /**
     * en_us Checks SSN or EIN
     * pt_br Verifica CVPF ou CNPJ
     *
     * @return void
     */
    function checkCpfCNPJ(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $personDAO = new personDAO();
        $personModel = new personModel();
        
        $cpfCnpj = (isset($_POST['ssnCpf'])) ? trim(strip_tags($_POST['ssnCpf'])) : trim(strip_tags($_POST['einCnpj']));
        $type = (isset($_POST['ssnCpf'])) ? "CPF" : "CNPJ";
        $cpfCnpj = preg_replace('/[^0-9]/','',$cpfCnpj);
        $cpfCnpjSrc = new cpfServices($cpfCnpj);

        if(!$cpfCnpjSrc->valida()){
            $msg = ($type == "CPF") ? $this->translator->translate("Alert_invalid_cpf") : $this->translator->translate("Alert_invalid_cnpj");
            echo json_encode($msg);
        }else{
            $where = " AND IFNULL(c.ein_cnpj,d.ssn_cpf) = '{$cpfCnpj}'";
            $where .= (isset($_POST['personId'])) ? " AND tbp.idperson != {$_POST['personId']}" : "";
            $check = $personDAO->queryPersons($where);

            if(!$check['status']){
                echo json_encode($this->translator->translate("generic_error_msg"));
                exit;
            }

            $checkObj = $check['push']['object']->getGridList();
    
            if(count($checkObj) > 0){
                $msg =  ($type == "CPF") ? $this->translator->translate('cpf_exists') : $this->translator->translate("ein_cnpj_exists");
                echo json_encode($msg);
            }else{
                echo json_encode(true);
            }
        }
    }


}