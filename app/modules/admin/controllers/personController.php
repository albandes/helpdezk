<?php
require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class Person  extends admCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->program  = basename( __FILE__ );

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;

        $this->loadModel('person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->loadModel('programs_model');
        $dbProgram = new programs_model();
        $this->dbProgram = $dbProgram;

        $this->loadModel('permissions_model');
        $dbPermissions = new permissions_model();
        $this->dbPermissions = $dbPermissions;

        $this->loadModel('helpdezk/groups_model');
        $dbGroups = new groups_model();
        $this->dbGroups = $dbGroups;

        $this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);
    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('persons.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='tbp.name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){

            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);

        }
        
        $count = $this->dbPerson->countPersonGrid($where);

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";        
        $limit = "LIMIT $start , $rows";
        //

        $rsPersons = $this->dbPerson->selectPersonGrid($where,$order,$limit);

        while (!$rsPersons->EOF) {

            $status_fmt = ($rsPersons->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

            switch($rsPersons->fields['idtypeperson']){
                case 1:
                    $icon = "fa-tools";
                    break;
                case 2:
                    $icon = "fa-user";
                    break;
                case 3:
                    $icon = "fa-headset";
                    break;
                case 4:
                    $icon = "fa-building";
                    break;
                default:
                    $icon = "fa-hands-helping";
                    break;
            }

            $typeperson_fmt = '<i class="fa '.$icon.'"></i>';

            $aColumns[] = array(
                'id'            => $rsPersons->fields['idperson'],
                'typeicone'     => $typeperson_fmt,
                'name'          => $rsPersons->fields['name'],
                'login'         => $rsPersons->fields['login'],
                'email'         => $rsPersons->fields['email'],
                'typeperson'    => $this->getLanguageWord('type_user_'. $rsPersons->fields['typeperson']),
                'company'       => $rsPersons->fields['company'],
                'department'    => $rsPersons->fields['department'],
                'status'        => $status_fmt,
                'statusval'     => $rsPersons->fields['status'],
                'idtypeperson'  => $rsPersons->fields['idtypeperson']

            );
            $rsPersons->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);
    }

    public function formCreatePerson()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $this->makeScreenPerson($smarty,'','create');

        $smarty->assign('token', $token) ;
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('person-create.tpl');
    }

    public function formUpdatePerson()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        $tn = $this->dbPerson->selectTypeNature($idPerson);
        $typenature = $tn->fields['idnatureperson'];

        $rsPerson = $typenature == 1 ? $this->dbPerson->selectPerson("AND a.idperson = $idPerson") : $this->dbPerson->selectCompanyData("AND jur.idperson = $idPerson");

        $this->makeScreenPerson($smarty,$rsPerson,'update');

        if($typenature == 1){
            $smarty->assign('displayNatural', '') ;
            $smarty->assign('displayJuridical', 'hide') ;

            if($rsPerson->fields['idtypeperson'] == 2){
                $smarty->assign('displayOperator', 'hide') ;
                $smarty->assign('displayUser', '') ;
            }else{
                $smarty->assign('displayOperator', '') ;
                $smarty->assign('displayUser', 'hide') ;
            }
        }else{
            $smarty->assign('displayNatural', 'hide') ;
            $smarty->assign('displayJuridical', '') ;
            $smarty->assign('displayOperator', 'hide') ;
            $smarty->assign('displayUser', 'hide') ;
        }

        $smarty->assign('token', $token) ;
        $smarty->assign('hidden_idperson', $idPerson) ;
        $smarty->assign('idnatureperson', $typenature) ;
        $smarty->assign('txtCategory', $this->getLanguageWord($tn->fields['name']));
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('person-update.tpl');
    }
    
    function makeScreenPerson($objSmarty,$rs,$oper)
    {
        if(!empty($rs->fields['login'])){
            $objSmarty->assign('txtLogin',$rs->fields['login']);
        }else{
            $objSmarty->assign('txtLogin','');
        }

        $objSmarty->assign('personName',$rs->fields['name']);

        if(!empty($rs->fields['ssn_cpf'])){
            $objSmarty->assign('cpfVal',$rs->fields['ssn_cpf']);
        }else{
            $objSmarty->assign('cpfVal','');
        }
        $dtbirth = (!empty($rs->fields['dtbirth']) && $rs->fields['dtbirth'] != '0000-00-00') ? $rs->fields['dtbirth_fmt'] : '';
        $objSmarty->assign('dtbirthVal',$dtbirth);
        if(!empty($rs->fields['gender'])){
            if($rs->fields['gender'] == 'F'){
                $objSmarty->assign('checkM','');
                $objSmarty->assign('checkF','checked');
            }else{
                $objSmarty->assign('checkM','checked');
                $objSmarty->assign('checkF','');
            }

        }else{
            $objSmarty->assign('checkM','');
            $objSmarty->assign('checkF','');
        }

        if(!empty($rs->fields['ein_cnpj'])){
            $objSmarty->assign('tipojuridico','checked');
        }else{
            $objSmarty->assign('tipojuridico','');
        }
        $objSmarty->assign('emailVal',$rs->fields['email']);
        $objSmarty->assign('phoneVal',$rs->fields['telephone']);
        $objSmarty->assign('branchVal',$rs->fields['branch_number']);

        if(!empty($rs->fields['cellphone'])){
            $objSmarty->assign('mobileVal',$rs->fields['cellphone']);
        }else{
            $objSmarty->assign('mobileVal','');
        }
        if(!empty($rs->fields['fax'])){
            $objSmarty->assign('faxVal','checked');
        }else{
            $objSmarty->assign('faxVal','');
        }

        $objSmarty->assign('checkVip',($rs->fields['user_vip'] == 'N' ? '' : 'checked'));
        $objSmarty->assign('cpersonVal',(isset($rs->fields['contact_person']) ? $rs->fields['contact_person'] : ''));
        $objSmarty->assign('obsVal',(isset($rs->fields['observation']) ? $rs->fields['observation'] : ''));


        // --- Type Login ---
        $arrTypeLogin = $this->_comboTypeLogin(null,"ORDER BY `name`");
        if ($oper == 'update') {
            $idTypeLoginEnable = $rs->fields['idtypelogin'];
        } elseif ($oper == 'create') {
            $idTypeLoginEnable = 3;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('logintypeids',  $arrTypeLogin['ids']);
            $objSmarty->assign('logintypevals', $arrTypeLogin['values']);
            $objSmarty->assign('idlogintype', $idTypeLoginEnable  );
        }

        // --- Companies ---
        $arrCompanies = $this->_comboCompanies();
        if ($oper == 'update') {
            $idCompanyEnable = isset($rs->fields['idcompany']) ? $rs->fields['idcompany']: $_SESSION['SES_COD_EMPRESA'];
        } elseif ($oper == 'create') {
            $idCompanyEnable = $arrCompanies['ids'][0];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('juridicalids',  $arrCompanies['ids']);
            $objSmarty->assign('juridicalvals', $arrCompanies['values']);
            $objSmarty->assign('idjuridical', $idCompanyEnable  );
        }

        // --- Departments ---
        $arrDepartments = $this->_comboDepartment($idCompanyEnable);
        if ($oper == 'update') {
            $idDepartmentEnable = $rs->fields['iddepartment'];
        } elseif ($oper == 'create') {
            $idDepartmentEnable = $arrDepartments['ids'][0];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('departmentids',  $arrDepartments['ids']);
            $objSmarty->assign('departmentvals', $arrDepartments['values']);
            $objSmarty->assign('iddepartment', $idDepartmentEnable  );
        }

        // --- Type Person ---
        $wTypePerson = "WHERE idtypeperson IN (1,2,3)";
        $arrTypePerson = $this->_comboTypePerson($wTypePerson,'',"ORDER BY name ASC");
        if ($oper == 'update') {
            $idTypePersonEnable = $rs->fields['idtypeperson'];
        } elseif ($oper == 'create') {
            $idTypePersonEnable = 0;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('levelids',  $arrTypePerson['ids']);
            $objSmarty->assign('levelvals', $arrTypePerson['values']);
            $objSmarty->assign('idlevel', $idTypePersonEnable  );
        }

        // --- Type Company ---
        $wTypeCompany = "WHERE idtypeperson IN (4,5,8)";
        $arrTypeCompany = $this->_comboTypePerson($wTypeCompany,'',"ORDER BY name ASC");
        if ($oper == 'update') {
            $idTypeCompanyEnable = $rs->fields['idtypeperson'];
        } elseif ($oper == 'create') {
            $idTypeCompanyEnable = $arrTypeCompany['ids'][0];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('levelcompanyids',  $arrTypeCompany['ids']);
            $objSmarty->assign('levelcompanyvals', $arrTypeCompany['values']);
            $objSmarty->assign('idlevelcompany', $idTypeCompanyEnable  );
        }

        // --- Permission Groups ---
        $wPermGroups = "WHERE permissiongroup='Y'";
        $arrPermGroups = $this->_comboTypePerson($wPermGroups,'',"ORDER BY name ASC");
        if ($oper == 'update') {
            $idPermGroupsEnable = array();
            $rsCheck = $this->dbPerson->getPersonTypes($rs->fields['idperson']);
            while(!$rsCheck->EOF) {
                array_push($idPermGroupsEnable,$rsCheck->fields['idtypeperson']) ;
                $rsCheck->MoveNext();
            }
        } elseif ($oper == 'create') {
            $idPermGroupsEnable = array();
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('permgroupsids',  $arrPermGroups['ids']);
            $objSmarty->assign('permgroupsvals', $arrPermGroups['values']);
            $objSmarty->assign('idpermgroups', $idPermGroupsEnable  );
        }

        // --- Location ---
        $arrLocation = $this->_comboLocation();
        $objSmarty->assign('plh_location_select',$this->getLanguageWord('Select_location'));
        if ($oper == 'update') {
            $idLocationEnable = $rs->fields['cod_location'];
        } elseif ($oper == 'create') {
            $idLocationEnable = $arrLocation['ids'][0];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('locationids',  $arrLocation['ids']);
            $objSmarty->assign('locationvals', $arrLocation['values']);
            $objSmarty->assign('idlocation', $idLocationEnable  );
        }

        /* -- Endereco -- */

        // --- Country ---
        if ($oper == 'update') {
            $idCountryEnable = $rs->fields['idcountry'];
        } elseif ($oper == 'create') {
            $idCountryEnable = $this->getIdCountryDefault();
        }
        if ($oper == 'echo') {
            $objSmarty->assign('pais',$rs->fields['printablename']);
        } else {
            $arrCountry = $this->comboCountries();
            $objSmarty->assign('countryids',  $arrCountry['ids']);
            $objSmarty->assign('countryvals', $arrCountry['values']);
            $objSmarty->assign('idcountry', $idCountryEnable  );
        }
        
        // --- State ---
        if ($oper == 'update') {
            $idStateEnable = $rs->fields['idstate'];
        } elseif ($oper == 'create') {
            $idStateEnable = $this->getIdStateDefault();
        }
        if ($oper == 'echo') {
            $objSmarty->assign('estado',$rs->fields['estado']);
        } else {
            $arrCountry = $this->comboStates($idCountryEnable);
            $objSmarty->assign('stateids',  $arrCountry['ids']);
            $objSmarty->assign('statevals', $arrCountry['values']);
            $objSmarty->assign('idstate',   $idStateEnable);
        }
        
        // --- City ---
        if ($oper == 'update') {
            $idCityEnable = $rs->fields['idcity'];
        } elseif ($oper == 'create') {
            $idCityEnable = $this->getIdCityDefault($idStateEnable);
        }
        if ($oper == 'echo') {
            $objSmarty->assign('cidade', utf8_encode($rs->fields['cidade']));
        } else {
            $arrCity = $this->comboCity($idStateEnable);
            $objSmarty->assign('cityids',  $arrCity['ids']);
            $objSmarty->assign('cityvals', $arrCity['values']);
            $objSmarty->assign('idcity',   $idCityEnable);
        }

        // --- Neighborhood ---
        if ($oper == 'update'){
            $idNeighborhoodEnable = $rs->fields['idneighborhood'];
        } elseif ($oper == 'create') {
            $arrNeighborhood = $this->getIdNeighborhoodDefault($idCityEnable);
        }
        if ($oper == 'echo') {
            $objSmarty->assign('bairro', $rs->fields['bairro']);
        } else {
            $arrNeighborhood = $this->comboNeighborhood($idCityEnable);
            $objSmarty->assign('neighborhoodids',  $arrNeighborhood['ids']);
            $objSmarty->assign('neighborhoodvals', $arrNeighborhood['values']);
            $objSmarty->assign('idneighborhood',   $idNeighborhoodEnable);
        }
        
        // --- Cep ---
        if ($oper == 'update' or $oper == 'create' ) {
            if (empty($rs->fields['zipcode']))
                $objSmarty->assign('plh_cep', 'Informe o cep.');
            else
                $objSmarty->assign('cep', $rs->fields['zipcode']);
        } elseif ($oper == 'echo'){
            $objSmarty->assign('cep', $rs->fields['zipcode']);
        }
        
        // --- Type Street ---
        if ($oper == 'update') {
            $idTypeStreetEnable = $rs->fields['idtypestreet'];
        } elseif ($oper == 'create') {
            $idTypeStreetEnable = '';
        }
        if ($oper == 'echo') {
            $objSmarty->assign('tipologradouro', $rs->fields['tipologradouro']);
        } else {
            $arrTypestreet = $this->comboTypeStreet();
            $objSmarty->assign('typestreetids',  $arrTypestreet['ids']);
            $objSmarty->assign('typestreetvals', $arrTypestreet['values']);
            $objSmarty->assign('idtypestreet', $idTypeStreetEnable  );
        }
        
        // --- Address ---
        if ($oper == 'update') {
            $idStreetEnable = $rs->fields['idstreet'];
        } elseif ($oper == 'create') {
            $idStreetEnable = 1;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('tipologradouro', $rs->fields['tipologradouro']);
        } else {
            $arrStreet = $this->_comboStreet("","","ORDER BY name");
            $objSmarty->assign('streetids',  $arrStreet['ids']);
            $objSmarty->assign('streetvals', $arrStreet['values']);
            $objSmarty->assign('idstreet', $idStreetEnable  );
        }

        // --- Number ---
        if ($oper == 'update') {
            if (!empty($rs->fields['number']))
                $objSmarty->assign('numberVal',$rs->fields['number']);
        }  elseif ($oper == 'echo'){
            $objSmarty->assign('numberVal',$rs->fields['number']);
        }

        // --- Complement ---
        if ($oper == 'update') {
            if ($oper == 'update') {
                if (empty($rs->fields['complement']))
                    $objSmarty->assign('plh_complemento','Informe o complemento.');
                else
                    $objSmarty->assign('complementVal',$rs->fields['complement']);
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_complemento','Informe o complemento.');
        }  elseif ($oper == 'echo'){
            $objSmarty->assign('complementVal',$rs->fields['complement']);
        }

        /* -- Fim endereco -- */

        // --- Person Groups ---
        $arrPersonGroups = $arrGrps = $this->_comboGroups('','ORDER BY tbp.name');
        if ($oper == 'update') {
            $idPersonGroupsEnable = array();
            $rsCheck = $this->dbPerson->getPersonGroups($rs->fields['idperson']);
            while(!$rsCheck->EOF) {
                array_push($idPersonGroupsEnable,$rsCheck->fields['idgroup']) ;
                $rsCheck->MoveNext();
            }
        } elseif ($oper == 'create') {
            $idPersonGroupsEnable = array();
        }
        if ($oper == 'echo') {
            $objSmarty->assign('lblTypeLogin',$rs->fields['printablename']);
        } else {
            $objSmarty->assign('persongroupsids',  $arrPersonGroups['ids']);
            $objSmarty->assign('persongroupsvals', $arrPersonGroups['values']);
            $objSmarty->assign('idpersongroups', $idPersonGroupsEnable  );
        }



    }

    function createPerson()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        

        //echo"<pre>"; print_r($_POST['permgroups']); echo"</pre><br>";
        $charSearch = array("(",")","-"," ",".");
        $charReplace = array("","","","","");
        $arrAdminOpe = array(1,3);

        $natureperson = $_POST['category'] == 'natural' ? 1 : 2;
        $login = $natureperson == 1 ? $_POST['login'] : NULL;
        $logintype = $natureperson == 1 ? $_POST['logintype'] : 3;
        $password = $natureperson == 1 ? md5($_POST['password']) : NULL;

        $name = addslashes($_POST['personName']);
        $email = addslashes($_POST['email']);
        $phone = addslashes(str_replace($charSearch,$charReplace,$_POST['phone']));
        $branch = addslashes(str_replace($charSearch,$charReplace,$_POST['branch']));
        $mobile = $natureperson == 1 ? addslashes(str_replace($charSearch,$charReplace,$_POST['mobile'])) : '';
        $fax = $natureperson == 1 ? '' : addslashes(str_replace($charSearch,$charReplace,$_POST['fax']));

        $viptmp = isset($_POST['vip']) ? 'Y' : 'N';
        $vip = $natureperson == 1 ? $viptmp : 'N';

        $typeuser = $natureperson == 1 ? $_POST['type_user'] : $_POST['type_company'];

        $time_value = ($natureperson == 1 && in_array($_POST['type_user'],$arrAdminOpe)) ? $_POST['time_value'] : '';
        $overtime = ($natureperson == 1 && in_array($_POST['type_user'],$arrAdminOpe)) ? $_POST['overtime'] : '';

        $location = ($natureperson == 1 && $_POST['type_user'] == 2) ? $_POST['location'] : NULL;
        $changePassInsert = ($natureperson == 1 && isset($_POST['changePassInsert'])) ? 1 : 0;

        $idtheme = '1';
        $dtcreate = date('Y-m-d H:i:s');
        $status = 'A';

        $this->dbPerson->BeginTrans();

        $ins = $this->dbPerson->insertPersonAdmin($logintype, $typeuser, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $phone, $branch, $mobile, $fax, $login, $password, $time_value, $overtime, $location, $changePassInsert);
        if(!$ins){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Person  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
        }

        $idperson = $ins;

        $idstreet = isset($_POST['filladress']) ? $_POST['address'] : 1;
        $typeaddress = $natureperson == 1 ? 2 : 3;
        $city = isset($_POST['filladress']) ? $_POST['city'] : 1;
        $idneighborhood = isset($_POST['filladress']) ? $_POST['neighborhood'] : 1;
        $number = isset($_POST['filladress']) ? addslashes($_POST['number']) : '';
        $complement = isset($_POST['filladress']) ? addslashes($_POST['complement']) : '';
        $zipcode = isset($_POST['filladress']) ? str_replace($charSearch,$charReplace,$_POST['zipcode']) : '';


        $insAddressData = $this->dbPerson->insertAddressData($idperson, $city, $idneighborhood, $idstreet, $typeaddress, $number, $complement, $zipcode);
        if(!$insAddressData){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Person / Save Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $dtbirth = $_POST['dtbirth'] != '' ? $this->formatSaveDate($_POST['dtbirth']) : '0000-00-00';
        $cpf = str_replace($charSearch,$charReplace,$_POST['cpf']);
        $gender = isset($_POST['gender']) ? $_POST['gender'] : NULL;

        $cnpj = str_replace($charSearch,$charReplace,$_POST['cnpj']);
        $department = $natureperson == 1 ? $_POST['department'] : $_POST['department_default'];
        $contact = addslashes($_POST['cperson']);
        $observation = addslashes($_POST['observation']);

        if($natureperson == 1){
            $insNatural = $this->dbPerson->insertNaturalData($idperson, $cpf, $dtbirth, $gender);
            if (!$insNatural) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Person / Save Natural Data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            $depart = $this->dbPerson->insertInDepartment($idperson, $department);
            if (!$depart) {
                //$this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Person / Save Natural Department  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            // Since 2016-05-09 - Rogério Albandes
            if(!empty($_POST['permgroups'])){
                foreach ($_POST['permgroups'] as $idtypeperson) {
                    $insPersonTypes = $this->dbPerson->insertPersonTypes($idperson,$idtypeperson);
                    if(!$insPersonTypes) {
                        $this->dbPerson->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Person / Save Permission Groups  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
            }

            // Since 2019-11
            if(!empty($_POST['persongroups'])){
                foreach ($_POST['persongroups'] as $idgroup) {
                    $insPersonGroups = $this->dbPerson->insertGroupPerson($idgroup, $idperson);
                    if(!$insPersonGroups) {
                        $this->dbPerson->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Person, Save Administrator/Operator Groups  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
            }
        }else{
            $depart = $this->dbPerson->insertDepartment($idperson, $department);
            if (!$depart) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Person / Save Juridical Department  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $insJuridical = $this->dbPerson->insertJuridicalData($idperson, $cnpj, $contact, $observation);
            if (!$insJuridical) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Person / Save Juridical Data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

        }

        
        $this->dbPerson->CommitTrans();
        
        $aRet = array(
            "idperson" => $idperson,
            "description" => $_POST['personName']
        );

        echo json_encode($aRet);

    }

    function updatePerson()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //echo"<pre>"; print_r($_POST); echo"</pre><br>";
        $charSearch = array("(",")","-"," ",".");
        $charReplace = array("","","","","");
        $arrAdminOpe = array(1,3);

        $idperson = $_POST['idperson'];
        $natureperson = $_POST['category'];
        $logintype = $natureperson == 1 ? $_POST['logintype'] : 3;
        $typeuser = $natureperson == 1 ? $_POST['type_user'] : $_POST['type_company'];

        $name = addslashes($_POST['personName']);
        $email = addslashes($_POST['email']);
        $phone = addslashes(str_replace($charSearch,$charReplace,$_POST['phone']));
        $branch = addslashes(str_replace($charSearch,$charReplace,$_POST['branch']));
        $mobile = $natureperson == 1 ? addslashes(str_replace($charSearch,$charReplace,$_POST['mobile'])) : '';
        $fax = $natureperson == 1 ? '' : addslashes(str_replace($charSearch,$charReplace,$_POST['fax']));

        $viptmp = isset($_POST['vip']) ? 'Y' : 'N';
        $vip = $natureperson == 1 ? $viptmp : 'N';

        $time_value = ($natureperson == 1 && in_array($typeuser,$arrAdminOpe)) ? $_POST['time_value'] : 0;
        $overtime = ($natureperson == 1 && in_array($typeuser,$arrAdminOpe)) ? $_POST['overtime'] : 0;


        $location = ($natureperson == 1 && $typeuser == 2) ? $_POST['location'] : NULL;

        if($natureperson == 1){
            if(empty($time_value)) $time_value = 0;
            if(empty($overtime)) $overtime = 0;
            $set = "idtypelogin = $logintype, idtypeperson = $typeuser,  name = '$name',
                    email = '$email', user_vip = '$vip', phone_number = '$phone', branch_number = '$branch',
                    cel_phone = '$mobile', time_value = $time_value, 
                    overtime = $overtime";
            if($location){$set .= ", cod_location = '$location' ";}
        }else{
            $set = "idtypeperson = $typeuser,  name = '$name', email = '$email', phone_number = '$phone', 
                    branch_number = '$branch', fax = '$fax'";
        }

        $where = "idperson = $idperson";
        $this->dbPerson->BeginTrans();

        $upd = $this->dbPerson->updatePerson($set,$where);
        if(!$upd){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Update Person  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idstreet = $_POST['address'];
        $city = $_POST['city'];
        $idneighborhood = $_POST['neighborhood'];
        $number = addslashes($_POST['number']);
        $complement = addslashes($_POST['complement']);
        $zipcode = str_replace($charSearch,$charReplace,$_POST['zipcode']);

        $updAddressData = $this->dbPerson->updateAddressData($idperson, $city, $idneighborhood, $idstreet, $number, $complement, $zipcode);
        if(!$updAddressData){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Update Person / Update Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $dtbirth = $_POST['dtbirth'] != '' ? $this->formatSaveDate($_POST['dtbirth']) : '0000-00-00';
        $cpf = str_replace($charSearch,$charReplace,$_POST['cpf']);
        $gender = isset($_POST['gender']) ? $_POST['gender'] : NULL;

        $cnpj = str_replace($charSearch,$charReplace,$_POST['cnpj']);
        $department = $natureperson == 1 ? $_POST['department'] : $_POST['department_default'];
        $contact = addslashes($_POST['cperson']);
        $observation = addslashes($_POST['observation']);

        if($natureperson == 1){
            $updNatural = $this->dbPerson->updateNaturalData($idperson, $cpf, $dtbirth, $gender);
            if (!$updNatural) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Person / Update Natural Data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            $depart = $this->dbPerson->updatePersonDepartment($idperson, $department);
            if (!$depart) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Person / Update Natural Department  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            // Since 2016-05-09 - Rogério Albandes
            $del = $this->dbPerson->delPersonTypes($idperson) ;
            if (!$del) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Person / Delete Permission Groups  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            if(!empty($_POST['permgroups'])){
                foreach ($_POST['permgroups'] as $idtypeperson) {
                    $insPersonTypes = $this->dbPerson->insertPersonTypes($idperson,$idtypeperson);
                    if(!$insPersonTypes) {
                        $this->dbPerson->RollbackTrans();
                        if($this->log)
                            $this->logIt('Update Person / Update Permission Groups  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
            }

            // Since 2019-11
            $delGrps = $this->dbPerson->deletePersonGroups($idperson) ;
            if (!$delGrps) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Person. Delete Administrator/Operator Groups - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            if(!empty($_POST['persongroups'])){
                foreach ($_POST['persongroups'] as $idgroup) {
                    $insPersonGroups = $this->dbPerson->insertGroupPerson($idgroup, $idperson);
                    if(!$insPersonGroups) {
                        $this->dbPerson->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Person, Save Administrator/Operator Groups - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
            }
        }else{
            $updJuridical = $this->dbPerson->updateJuridicalData($idperson, $cnpj, $contact, $observation);
            if (!$updJuridical) {
                $this->dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Person / Update Juridical Data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

        }

        $aRet = array(
            "idperson" => $idperson,
            "status"   => 'OK'
        );

        $this->dbPerson->CommitTrans();

        echo json_encode($aRet);


    }

    function ajaxStates()
    {
        echo $this->comboStatesHtml($_POST['countryId']);

    }

    function ajaxCities()
    {
        echo $this->comboCitesHtml($_POST['stateId']);

    }

    function ajaxNeighborhood()
    {
        echo $this->comboNeighborhoodHtml($_POST['cityId']);

    }

    function completeStreet()
    {
        $aRet = array();

        $where = "WHERE `name` LIKE  '%". $this->getParam('search')."%'";
        $group = 'GROUP BY NAME';
        $order = 'ORDER BY NAME ASC';

        $rs = $this->dbPerson->getStreet($where,$group,$order);

        while (!$rs->EOF) {
            array_push($aRet,$rs->fields['name']);
            $rs->MoveNext();
        }
        //$array = array_map('htmlentities',$aRet);
        //$json = html_entity_decode(json_encode($array));
        //$json = json_encode($aRet);
        echo $this->makeJsonUtf8Compat($aRet);
    }

    public function checklogin() {
        $login = $_POST['login'];

        $check = $this->dbPerson->checkLogin($login);
        if ($check->fields) {
            echo json_encode($this->getLanguageWord('Login_exists'));
        } else {
            echo json_encode(true);
        }
    }

    function statusPerson()
    {
        $idPerson = $this->getParam('idperson');
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbPerson->changeStatus($idPerson,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Person Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idperson" => $idPerson,
            "status" => 'OK',
            "personstatus" => $newStatus
        );

        echo json_encode($aRet);

    }

    public function managePersonPerms() {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        
        $smarty->assign('token', $token) ;
        $smarty->assign('hidden_idperson', $idPerson);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('person-permission.tpl');
    }

    public function jsonPermissionGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $idperson = $this->getParam('idperson');

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='tbp.name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'tbp.name') $searchField = 'tbp.name';
            $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbProgram->countProgram($where);

        if( $count->fields['total'] > 0 && $rows > 0) {
            $total_pages = ceil($count->fields['total']/$rows);
        } else {
            $total_pages = 1;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";

        $sel = $this->dbProgram->selectProgram($where, $order, $limit);

        while (!$sel->EOF) {

            $program = $sel->fields['idprogram'];
            $name_pgr = ($sel->fields['smarty']) ? $this->getLanguageWord($sel->fields['smarty']) : $sel->fields['name'];

            $defPerms = $this->dbPermissions->getDefaultPerms($program);
            while (!$defPerms->EOF) {
                $defP[$defPerms->fields['idaccesstype']] = $defPerms->fields['idaccesstype'];
                $defPerms->MoveNext();
            }

            for ($accesstype = 1; $accesstype <= 7; $accesstype++) {
                $access = $this->dbPermissions->getPersonPermission($program, $idperson, $accesstype);

                switch ($accesstype) {
                    case 1 :
                        $disabled = (!$defP[1]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $acc = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                    case 2 :
                        $disabled = (!$defP[2]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $new = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                    case 3 :
                        $disabled = (!$defP[3]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $edit = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                    case 4 :
                        $disabled = (!$defP[4]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $delete = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                    case 5 :
                        $disabled = (!$defP[5]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $export = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                    case 6 :
                        $disabled = (!$defP[6]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $email = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                    case 7 :
                        $disabled = (!$defP[7]) ? "disabled='disabled'" : "";
                        $checked = ($access->fields['allow'] == 'Y') ? "checked='checked'" : "";
                        $sms = "<input type='checkbox' $disabled $checked id='" . $accesstype . "-" . $program . "-" . $idperson ."' name='" . $accesstype . "-" . $program . "-" . $idperson ."' onchange='edit2(this.name,".$program.",".$accesstype.",".$idperson.");'>";
                        break;
                }
            }

            $aColumns[] = array(
                'idprogram'      => $program,
                'programname'    => $name_pgr,
                'access'            => $acc,
                'new'               => $new,
                'edit'              => $edit,
                'delete'            => $delete,
                'export'            => $export,
                'email'             => $email,
                'sms'               => $sms,
                'idperson'         => $idperson
            );

            $sel->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $sel->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function grantpermission()
    {
        $idprogram = $_POST['idprogram'];
        $idaccesstype = $_POST['idaccesstype'];
        $idperson = $_POST['idperson'];
        $check = $_POST['check'];

        $this->dbPermissions->BeginTrans();
        $grant = $this->dbPermissions->grantPermissionPerson($idprogram, $idperson, $idaccesstype, $check);

        if(!$grant){
            $this->dbPermissions->RollbackTrans();
            return false;
        }

        $aRet = array(
            "idperson" => $idperson,
            "status"   => 'OK'
        );

        $this->dbPermissions->CommitTrans();

        echo json_encode($aRet);

    }

    public function modalAttendantGroups()
    {

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $idperson       = $_POST['idperson'];

        $aRet = $this->makeAttendantGroupsScreen($idperson);

        echo json_encode($aRet);

    }

    public function makeAttendantGroupsScreen($idperson)
    {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);


        $rsAttGrps = $this->dbPerson->getPersonGroups($idperson);
        $tbody = '';
        $groupsIds = '';

        while(!$rsAttGrps->EOF) {
           $tbody .= "<tr><td>".$rsAttGrps->fields['name']."<input type='hidden' class='admAttGrps' name='admAttGrps[]' id='admAttGrps_".$rsAttGrps->fields['idgroup']."' value='".$rsAttGrps->fields['idgroup']."'></td><td><a href='javascript:;' onclick='removeAttGrps(this)' class='btn btn-danger'><i class='fa fa-times'></i></a></td></tr>";
           $groupsIds .= $rsAttGrps->fields['idgroup'].",";
           $rsAttGrps->MoveNext();
        }

        $idsGrp = substr($groupsIds,0,-1);
        $groups = $idsGrp != '' ? " AND tbg.idgroup NOT IN ($idsGrp)" : '';
        $where = "AND tbg.status ='A' $groups";
        $order = "ORDER BY name ASC";

        $arrGrps = $this->_comboGroups($where,$order);
        $select = '';

        foreach ( $arrGrps['ids'] as $indexKey => $indexValue ) {
            if ($arrGrps['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }

            $select .= "<option value='$indexValue' $default>".$arrGrps['values'][$indexKey]."</option>";
        }

        $aRet = array(
            "cmblist" => $select,
            "tablelist" => $tbody
        );

        return $aRet;

    }

    public function insertAttendantGroups()
    {

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $idperson = $_POST['idperson'];
        $idgroup = $_POST['groupid'];

        $this->dbPermissions->BeginTrans();
        $rs = $this->dbPermissions->groupPersonInsert($idgroup, $idperson);
        if(!$rs){
            $this->dbPermissions->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Attendant Groups - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbPermissions->CommitTrans();

        $aRet = $this->makeAttendantGroupsScreen($idperson);

        echo json_encode($aRet);

    }

    public function deleteAttendantGroups()
    {

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $idperson = $_POST['idperson'];
        $idgroup = $_POST['groupid'];

        $this->dbPermissions->BeginTrans();
        $rs = $this->dbPermissions->groupPersonDelete($idgroup, $idperson);
        if(!$rs){
            $this->dbPermissions->RollbackTrans();
            if($this->log)
                $this->logIt("Delete Attendant Groups - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbPermissions->CommitTrans();

        $aRet = $this->makeAttendantGroupsScreen($idperson);

        echo json_encode($aRet);

    }

    public function changePassword() {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idperson = $_POST['idperson'];
        $password = md5($_POST['newpassword']);
        $changepass = isset($_POST['changepass']) ? $_POST['changepass'] : 0;
        
        $change = $this->dbPerson->changePassword($idperson, $password, $changepass);
        if (!$change) {
            if($this->log)
                $this->logIt('Change Password  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $aRet = array(
            "idperson" => $idperson,
            "status"   => 'OK'
        );

        echo json_encode($aRet);

    }

    public function insertLocation() {
    	
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $location = addslashes($_POST['location']);
        
        $rs = $this->dbPerson->insertLocation($location);
        if (!$rs) {
            if($this->log)
                $this->logIt('Insert Location  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $aRet = array(
            "idlocation" => $rs,
            "status"   => 'OK'
        );

        echo json_encode($aRet);
    }

    function ajaxLocation()
    {
        $arrLocation = $this->_comboLocation();
        $select = '';

        foreach ( $arrLocation['ids'] as $indexKey => $indexValue ) {
            if ([$indexKey] == 0) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrLocation['values'][$indexKey]."</option>";
        }
        
        echo $select;

    }

    function insertState()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCountry = $_POST['idCountry'];
        $nameState = addslashes($_POST['nameState']);
        $abbr = addslashes($_POST['abbrState']);

        $this->dbPerson->BeginTrans();

        $ret = $abbr != '' ? $this->dbPerson->insertState($idCountry,$nameState,$abbr) : $this->dbPerson->insertState($idCountry,$nameState);
        if(!$ret){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt("Insert State - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $idState = $this->dbPerson->selectMaxState();

        $this->dbPerson->CommitTrans();

        $aRet = array(
            "idstate" => $idState
        );

        echo json_encode($aRet);
    }

    function insertCity()
    {
        if (!$this->_checkToken()) {
            if ($this->log)
                $this->logIt('Error Token - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $idState = $_POST['idState'];
        $nameCity = addslashes($_POST['nameCity']);

        $this->dbPerson->BeginTrans();

        $idCity = $this->dbPerson->insertCity($idState, $nameCity);
        if(!$idCity){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt("Insert City - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbPerson->CommitTrans();

        $aRet = array(
            "idcity" => $idCity
        );

        echo json_encode($aRet);
    }

    function insertNeighborhood()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCity = $_POST['idCity'];
        $nameNeighborhood = addslashes($_POST['nameNeighborhood']);

        $this->dbPerson->BeginTrans();

        $idNeighborhood = $this->dbPerson->insertNeighborhood($idCity,$nameNeighborhood);
        if(!$idNeighborhood){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Neighborhood - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbPerson->CommitTrans();

        $aRet = array(
            "idneighborhood" => $idNeighborhood
        );

        echo json_encode($aRet);
    }

    function insertStreet()
    {
        if (!$this->_checkToken()) {
            if ($this->log)
                $this->logIt('Error Token - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $idTypeStreet = $_POST['idTypeStreet'];
        $nameStreet = addslashes($_POST['nameStreet']);

        $this->dbPerson->BeginTrans();

        $idstreet = $this->dbPerson->insertStreet($idTypeStreet,$nameStreet);
        if(!$idstreet){
            $this->dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Street - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbPerson->CommitTrans();

        $aRet = array(
            "idstreet" => $idstreet
        );

        echo json_encode($aRet);
    }

    function ajaxStreet()
    {
        $where = "WHERE idtypestreet = ".$_POST['typestreetId'];
        $arrStreet = $this->_comboStreet($where);
        $select = '';

        foreach ( $arrStreet['ids'] as $indexKey => $indexValue ) {
            if ([$indexKey] == 0) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrStreet['values'][$indexKey]."</option>";
        }

        echo $select;

    }

    function ajaxDepartment()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $arrDepartment = $this->_comboDepartment($_POST['companyId']);
        $select = '';

        foreach ( $arrDepartment['ids'] as $indexKey => $indexValue ) {
            if ([$indexKey] == 0) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrDepartment['values'][$indexKey]."</option>";
        }

        echo $select;

    }

}