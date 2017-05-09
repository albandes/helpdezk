<?php

class Person extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {

        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("person/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('person.tpl.html');
    }

    public function formatadata($data) {
        $formatada = substr($data, 8, 2) . '/' . substr($data, 5, 2) . '/' . substr($data, 0, 4);
        return $formatada;
    }

    public function json() {
    	$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();
		
        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];

        if (!$sortorder)
            $sortorder = 'asc';
        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);
        $limit = "LIMIT $start, $rp";
        $query = $_POST['query'];
        $qtype = $_POST['qtype'];
        $sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];

        $where = "";
        if ($query) {
            switch ($qtype) {
                case 'tbp.name':
                    $where = "and $qtype LIKE '%$query%'";
                    break;
                 case 'tbp.login':
                    $where = "and $qtype LIKE '%$query%'";
                    break;
                 case 'tbp.email':
                    $where = "and $qtype LIKE '%$query%'";
                    break;
                 case 'dep.name':
                    $where = "and $qtype LIKE '%$query%'";
                    break;
                 case 'comp.name':
                    $where = "and $qtype LIKE '%$query%'";
                    break;
                default:
                    $where = "";
                    break;
            }
        }
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }
        $limit = "LIMIT $start, $rp";

        $bd = new person_model();
        $rsPerson = $bd->selectPersonForJson($where, $order, $limit);
		
        if(!$rsPerson){
            echo "erro linha".__LINE_;
        }

        $qcount = $bd->countPersonForJson($where);
        if(!$qcount){
            echo "erro linha".__LINE_;
        }
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsPerson->EOF) {

            if ($rsPerson->fields['status'] == "A") {
                $status = "<img src='" . path . "/app/themes/" . theme . "/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='" . path . "/app/themes/" . theme . "/images/notactive.gif' height='10px' width='10px'>";
            }

            if ($rsPerson->fields['idtypeperson'] == "1")
            {
                $login = $rsPerson->fields['login'];
                $icon = "<img src='" . path . "/app/themes/" . theme . "/images/admin.png' height='17' width='17'>";
            }
            elseif ($rsPerson->fields['idtypeperson'] == "2")
            {
                $login = $rsPerson->fields['login'];
                $icon = "<img src='" . path . "/app/themes/" . theme . "/images/user.png' height='18' width='18'>";
            }
            elseif ($rsPerson->fields['idtypeperson'] == "3")
            {
                $login = $rsPerson->fields['login'];
                $icon = "<img src='" . path . "/app/themes/" . theme . "/images/atendimento.png' height='17' width='17'>";
            }
            elseif ($rsPerson->fields['idtypeperson'] == "4" || $rsPerson->fields['idtypeperson'] == "5")
            {
                $login = '-';
                $icon = "<img src='" . path . "/app/themes/" . theme . "/images/company.png' height='18' width='18'>";
            }
            elseif ($rsPerson->fields['idtypeperson'] == "7")
            {
                        $login = '--';
                        $icon = "<img src='" . path . "/app/themes/" . theme . "/images/business_icon.png' height='17' width='17'>";
            }

            if(is_null($rsPerson->fields['company']))
                $company = '--';
            else
                $company = $rsPerson->fields['company'];
            
            if(is_null($rsPerson->fields['department']))
                $department = '--';
            else
                $department = $rsPerson->fields['department'];

			$type = $langVars['type_user_'.$rsPerson->fields['typeperson']];
			
            $rows[] = array(
                "id" => $rsPerson->fields['idperson'],
                "cell" => array(
                    $icon
                    , $rsPerson->fields['name']
                    , $login
                    , $rsPerson->fields['email']
                    , $type
                    , $company
                    , $department
                    , $status
                )
            );
            $dataformatada = '';
            $rsPerson->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function personinsert() {
        $smarty = $this->retornaSmarty();
        //$smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/person/insert.tpl.html');
    }

    public function formjuridical() {
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
        $smarty = $this->retornaSmarty();
        $db = new person_model();
        
        $select = $db->getCountry();
        while (!$select->EOF) {
            $campos[] = $select->fields['idcountry'];
            $valores[] = $select->fields['printablename'];
            $select->MoveNext();
        }
        $smarty->assign('countryids', $campos);
        $smarty->assign('countryvals', $valores);
        $campos = '';
        $valores = '';

        $select = $db->getDepartment();
        while (!$select->EOF) {
            $campos[] = $select->fields['iddepartment'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('departmentids', $campos);
        $smarty->assign('departmentvals', $valores);
        $campos = '';
        $valores = '';

        $select = $db->getTypeStreet();
        while (!$select->EOF) {
            $campos[] = $select->fields['idtypestreet'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('typestreetids', $campos);
        $smarty->assign('typestreetvals', $valores);
        $campos = '';
        $valores = '';

        $select = $db->getTypePerson('where idtypeperson in (4,5,8)');
        while (!$select->EOF) {
            $campos[] = $select->fields['idtypeperson'];
            $valores[] = $langVars['type_user_'.$select->fields['name']];
            $select->MoveNext();
        }
        $smarty->assign('levelids', $campos);
        $smarty->assign('levelvals', $valores);
        $smarty->assign('ein_mask', $this->getConfig('ein_mask'));
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/person/formjuridical.tpl.html');
    }

    public function formnatural() {
        $smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		
        $db = new person_model();

        $select = $db->getLoginTypes();
        while (!$select->EOF) {
            $ids[] 	  = $select->fields['idtypelogin'];
            $values[] = $select->fields['name'];
            $select->MoveNext();
        }

        $smarty->assign('logintypeids', $ids);
        $smarty->assign('logintypevals', $values); 
		$smarty->assign('sellogintype', 3); 
		
		$ids='';
		$values='';

        $select = $db->getCompanies(" AND status = 'A'");
        while (!$select->EOF) {
            $campos[] = $select->fields['idcompany'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('juridicalids', $campos);
        $smarty->assign('juridicalvals', $valores);
        //Empty variables to free memory
        $campos = '';  
        $valores = '';

        $select = $db->getTypePerson('where idtypeperson in (1,2,3)');
        while (!$select->EOF) {
            $campos[] = $select->fields['idtypeperson'];
            $valores[] = $langVars['type_user_'.$select->fields['name']];
            $select->MoveNext();
        }
        $smarty->assign('levelids', $campos);
        $smarty->assign('levelvals', $valores);
        //Empty variables to free memory
        $campos = '';   
        $valores = '';

        // Since 2016-05-09
        $aPersonTypes = array();
        $rsPersonTypes = $db->getTypePerson("WHERE permissiongroup='Y'");
        while (!$rsPersonTypes->EOF) {
            $aPersonTypes[$rsPersonTypes->fields['idtypeperson']] = $langVars['type_user_'.$rsPersonTypes->fields['name']];
            $rsPersonTypes->MoveNext();
        }
        $smarty->assign('perm_checkboxes', $aPersonTypes);
        //

        $select = $db->getCountry();
        while (!$select->EOF) {
            $campos[] = $select->fields['idcountry'];
            $valores[] = $select->fields['printablename'];
            $select->MoveNext();
        }
        $smarty->assign('countryids', $campos);
        $smarty->assign('countryvals', $valores);
        //Empty variables to free memory
        $campos = '';  
        $valores = '';

        $select = $db->getTypeStreet();
        while (!$select->EOF) {
            $campos[] = $select->fields['idtypestreet'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('typestreetids', $campos);
        $smarty->assign('typestreetvals', $valores);
        //Empty variables to free memory
        $campos = '';   
        $valores = '';
       
        $select = $db->getLocation();
		$count = $select->RecordCount();
		
		if($count){
			$campos[] = "";
			$valores[] = $langVars['Select'];
	        while (!$select->EOF) {
	            $campos[] = $select->fields['idlocation'];
	            $valores[] = $select->fields['name'];
	            $select->MoveNext();
	        }
        }else{
        	$campos[] = "";
			$valores[] = $langVars['No_result'];
        }
        $smarty->assign('locationids', $campos);
        $smarty->assign('locationvals', $valores);
        $campos = '';
        $valores = '';

        $smarty->assign('mask', $this->getConfig('id_mask'));
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/person/formnatural.tpl.html');
    }

    public function insert() {
        extract($_POST);
    }

    public function editform() {
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
        $id = $this->getParam('id');
        $bd = new person_model();
        $tp = $bd->selectTypePerson($id);
        $typeperson = $tp->fields['idtypeperson'];
		$mytypeperson = $_SESSION['SES_TYPE_PERSON'];

        if ($typeperson == 4 || $typeperson == 5 || $typeperson == 7 || $typeperson == 8) {
            $select = $bd->getTypePerson('where idtypeperson in (4,5,7,8)');
            while (!$select->EOF) {
                $campos[] = $select->fields['idtypeperson'];
                $valores[] = $langVars['type_user_'.$select->fields['name']];
                $select->MoveNext();
            }
            $smarty->assign('levelids', $campos);
            $smarty->assign('levelvals', $valores);
            $campos = '';
            $valores = '';

            $select = $bd->getCountry();
            while (!$select->EOF) {
                $campos[] = $select->fields['idcountry'];
                $valores[] = $select->fields['printablename'];
                $select->MoveNext();
            }
            $smarty->assign('countryids', $campos);
            $smarty->assign('countryvals', $valores);
            $campos = '';
            $valores = '';

            $select = $bd->getTypeStreet();
            while (!$select->EOF) {
                $campos[] = $select->fields['idtypestreet'];
                $valores[] = $select->fields['name'];
                $select->MoveNext();
            }
            $smarty->assign('typestreetids', $campos);
            $smarty->assign('typestreetvals', $valores);
            $campos = '';
            $valores = '';
            $cData = $bd->selectCompanyData($id);
            $name = $cData->fields['name'];
            $email = $cData->fields['email'];
            $ein_cnpj = $cData->fields['ein_cnpj'];
            $typeperson = $cData->fields['idtypeperson'];
            $phone = $cData->fields['telephone'];
            $fax = $cData->fields['fax'];
            $branch = $cData->fields['branch_number'];
            $contact = $cData->fields['contact_person'];
            $obs = $cData->fields['observation'];
			$idaddress = $cData->fields['idaddress'];
			// print_r($cData->fields) ; exit;
			if( !is_null($idaddress) ){
				$addrData = $bd->selectFullAddress($idaddress);
                //print_r($addrData->fields) ; exit;
                $country = $addrData->fields['idcountry'];
                $state = $addrData->fields['idstate'];
                //
                $where = "where idcountry=$country and idstate != 1";
                $rsState = $bd->selectState($where);
                while (!$rsState->EOF) {
                    $aStateIds[]  = $rsState->fields['idstate'];
                    $aStateVals[] = $rsState->fields['name'];
                    $rsState->MoveNext();
                }

                $smarty->assign('stateids', $aStateIds);
                $smarty->assign('statevals', $aStateVals);

                $where = "where idstate=$state";
                $rsCity = $bd->selectCity($where);
                while (!$rsCity->EOF) {
                    $aCityIds[]  = $rsCity->fields['idcity'];
                    $aCityVals[] = $rsCity->fields['name'];
                    $rsCity->MoveNext();
                }

                $smarty->assign('cityids', $aCityIds);
                $smarty->assign('cityvals', $aCityVals);
                //print_r($aStateVals); exit;
                //

	            $city = $addrData->fields['idcity'];
	            $neighborhood = $addrData->fields['neighborhood'];
	            $zipcode = $addrData->fields['zipcode'];
	            $idtypestreet = $addrData->fields['idtypestreet'];
	            $street = $addrData->fields['street'];
	            $number = $addrData->fields['number'];
	            $complement = $addrData->fields['complement'];
			}
		
            $smarty->assign('name', $name);
            $smarty->assign('ein_cnpj', $ein_cnpj);
            $smarty->assign('email', $email);
            $smarty->assign('mask', $this->getConfig('id_mask'));
            $smarty->assign('ein_mask', $this->getConfig('ein_mask'));
            $smarty->assign('typeperson', $typeperson);
            $smarty->assign('phone', $phone);
            $smarty->assign('fax', $fax);
            $smarty->assign('country', $country);
            $smarty->assign('branch', $branch);
            $smarty->assign('contact', $contact);
            $smarty->assign('obs', $obs);
            $smarty->assign('neighborhood', $neighborhood);
            $smarty->assign('zipcode', $zipcode);
            $smarty->assign('idtypestreet', $idtypestreet);
            $smarty->assign('street', $street);
            $smarty->assign('number', $number);
            $smarty->assign('complement', $complement);
            $smarty->assign('state', $state);
            $smarty->assign('city', $city);
            $smarty->assign('id', $id);
            $smarty->assign('token', $this->_makeToken()) ;
            $smarty->display('modals/person/companyeditform.tpl.html');
        } else {
			$select = $bd->getLoginTypes();
			while (!$select->EOF) {
				$ids[] 	  = $select->fields['idtypelogin'];
				$values[] = $select->fields['name'];
				$select->MoveNext();
			}
			$smarty->assign('logintypeids', $ids);
			$smarty->assign('logintypevals', $values); 
			$ids='';
			$values='';
		
            $select = $bd->getCompanies();
            while (!$select->EOF) {
                $campos[] = $select->fields['idcompany'];
                $valores[] = $select->fields['name'];
                $select->MoveNext();
            }
            $smarty->assign('juridicalids', $campos);
            $smarty->assign('juridicalvals', $valores);

            $campos = '';
            $valores = '';
            $select = $bd->getTypePerson('where idtypeperson in (1,2,3)');
            while (!$select->EOF) {
                $campos[] = $select->fields['idtypeperson'];
                $valores[] = $langVars['type_user_'.$select->fields['name']];
                $select->MoveNext();
            }
            $smarty->assign('levelids', $campos);
            $smarty->assign('levelvals', $valores);
            $campos = '';
            $valores = '';

            $select = $bd->getCountry();
            while (!$select->EOF) {
                $campos[] = $select->fields['idcountry'];
                $valores[] = $select->fields['printablename'];
                $select->MoveNext();
            }
            $smarty->assign('countryids', $campos);
            $smarty->assign('countryvals', $valores);
            $campos = '';
            $valores = '';

            $select = $bd->getTypeStreet();
            while (!$select->EOF) {
                $campos[] = $select->fields['idtypestreet'];
                $valores[] = $select->fields['name'];
                $select->MoveNext();
            }
            $smarty->assign('typestreetids', $campos);
            $smarty->assign('typestreetvals', $valores);
            //zera variaveis para liberar memoria
            $campos = '';
            $valores = '';

            // busca valores para montar select da localizacao
            $select = $bd->getLocation();
            while (!$select->EOF) {
                $campos[] = $select->fields['idlocation'];
                $valores[] = $select->fields['name'];
                $select->MoveNext();
            }
            $smarty->assign('locationids', $campos);
            $smarty->assign('locationvals', $valores);
            $campos = '';
            $valores = '';

            $pData = $bd->selectPersonData($id);

            $login = $pData->fields['login'];
			$logintype = $pData->fields['idtypelogin'];
            $name = $pData->fields['name'];
            $email = $pData->fields['email'];
            $ssn_cpf = $pData->fields['ssn_cpf'];
            $dtbirth = $pData->fields['dtbirth'];
            $dtbirth = $this->formatDate($dtbirth);
            $phone = $pData->fields['telephone'];
            $cellphone = $pData->fields['cellphone'];
            $branch = $pData->fields['branch_number'];
            $department = $pData->fields['department'];
            $company = $pData->fields['company'];
            $country = $pData->fields['idcountry'];
            $state = $pData->fields['idstate'];
            $city = $pData->fields['idcity'];
            $neighborhood = $pData->fields['neighborhood'];
            $zipcode = $pData->fields['zipcode'];
            $idtypestreet = $pData->fields['idtypestreet'];
            $street = $pData->fields['street'];
            $number = $pData->fields['num'];
            $complement = $pData->fields['complement'];
            $typeperson = $pData->fields['idtypeperson'];
            $vip = $pData->fields['user_vip'];
			$location = $pData->fields['cod_location'];
			$time_value = $pData->fields['time_value'];
			$overtime = $pData->fields['overtime'];
			
            if ($vip == 'Y') {
                $smarty->assign('vipcheck', 'checked');
            } else {
                $smarty->assign('vipcheck', '');
            }
			
			
			
	        $where = "where idperson=$company";

	        $sel = $bd->getDepartment($where);

			while (!$sel->EOF) {
                $campos[] = $sel->fields['iddepartment'];
                $valores[] = $sel->fields['name'];
                $sel->MoveNext();
            }



            $smarty->assign('departmentids', $campos);
            $smarty->assign('departmentvals', $valores);
			$campos = '';
            $valores = '';

			$select = $bd->getLocation();
			$count = $select->RecordCount();
			
			if($count){
				$campos[] = "";
				$valores[] = $langVars['Select'];
		        while (!$select->EOF) {
		            $campos[] = $select->fields['idlocation'];
		            $valores[] = $select->fields['name'];
		            $select->MoveNext();
		        }
	        }else{
	        	$campos[] = "";
				$valores[] = $langVars['No_result'];
	        }
	        $smarty->assign('locationids', $campos);
	        $smarty->assign('locationvals', $valores);
			$campos = '';
            $valores = '';
			
			
            $gender = $pData->fields['gender'];
            {
                if ($gender == 'M') {
                    $smarty->assign('genderM', 'checked');
                    $smarty->assign('genderF', '');
                } else {
                    $smarty->assign('genderF', 'checked');
                    $smarty->assign('genderM', '');
                }
            }
            if($mytypeperson==2)
                $read = "disabled='disabled'";


            // Since 2016-05-09
            $aPersonTypes = array();
            $rsPersonTypes = $bd->getTypePerson("WHERE permissiongroup='Y'");
            while (!$rsPersonTypes->EOF) {
                $aPersonTypes[$rsPersonTypes->fields['idtypeperson']] = $langVars['type_user_'.$rsPersonTypes->fields['name']];
                $rsPersonTypes->MoveNext();
            }

            $rsCheck = $bd->getPersonTypes($id);
            $aChecked = array();
            while(!$rsCheck->EOF) {
                array_push($aChecked,$rsCheck->fields['idtypeperson']) ;
                $rsCheck->MoveNext();
            }

            $smarty->assign('perm_checkboxes', $aPersonTypes);
            $smarty->assign('perm_id', $aChecked);
            //

            $smarty->assign('id', $id);
            $smarty->assign('login', $login);
			$smarty->assign('sellogintype', $logintype);
            $smarty->assign('name', $name);
            $smarty->assign('email', $email);
            $smarty->assign('dtbirth', $dtbirth);
            $smarty->assign('phone', $phone);
            $smarty->assign('cellphone', $cellphone);
            $smarty->assign('ssn_cpf', $ssn_cpf);
            $smarty->assign('company', $company);
            $smarty->assign('country', $country);
            $smarty->assign('department', $department);
            $smarty->assign('neighborhood', $neighborhood);
            $smarty->assign('zipcode', $zipcode);
            $smarty->assign('idtypestreet', $idtypestreet);
            $smarty->assign('street', $street);
            $smarty->assign('number', $number);
            $smarty->assign('complement', $complement);
            $smarty->assign('typeperson', $typeperson);
            $smarty->assign('mask', $this->getConfig('id_mask'));
            $smarty->assign('state', $state);
            $smarty->assign('branch', $branch);
            $smarty->assign('city', $city);            
            $smarty->assign('read', $read);
			$smarty->assign('location', $location);
			$smarty->assign('time_value', $time_value);
			$smarty->assign('overtime', $overtime);
            $smarty->assign('token', $this->_makeToken()) ;
            $smarty->display('modals/person/personeditform.tpl.html');
        }
    }
	
	public function editformuser() {

		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		
        $id = $_SESSION['SES_COD_USUARIO'];
        $typeperson = $_SESSION['SES_TYPE_PERSON'];
        
        
        $bd = new person_model();
        
		$pData = $bd->selectPersonData($id);
        $login = $pData->fields['login'];
		$logintype = $pData->fields['idtypelogin'];
        $name = $pData->fields['name'];
        $email = $pData->fields['email'];
        $ssn_cpf = $pData->fields['ssn_cpf'];
        $dtbirth = $pData->fields['dtbirth'];
        $dtbirth = $this->formatDate($dtbirth);
        $phone = $pData->fields['telephone'];
        $cellphone = $pData->fields['cellphone'];
        $branch = $pData->fields['branch_number'];
        $department = $pData->fields['department'];
        $company = $pData->fields['company'];
        $country = $pData->fields['idcountry'];
        $state = $pData->fields['idstate'];
        $city = $pData->fields['idcity'];
        $neighborhood = $pData->fields['neighborhood'];
        $zipcode = $pData->fields['zipcode'];
        $idtypestreet = $pData->fields['idtypestreet'];
        $street = $pData->fields['street'];
        $number = $pData->fields['number'];
        $complement = $pData->fields['complement'];
        $typeperson = $pData->fields['idtypeperson'];
        $vip = $pData->fields['user_vip'];
		$location = $pData->fields['cod_location'];
		$time_value = $pData->fields['time_value'];
		$overtime = $pData->fields['overtime'];
			
        //busca os valores para montar o select dos paises
        $select = $bd->getCountry();
        while (!$select->EOF) {
            $campos[] = $select->fields['idcountry'];
            $valores[] = $select->fields['printablename'];
            $select->MoveNext();
        }
        $smarty->assign('countryids', $campos);
        $smarty->assign('countryvals', $valores);
        //zera variaveis para liberar memoria
        $campos = '';
        $valores = '';
		
		if($country > 1){
	        $where = "where idcountry=$country and idstate != 1";
	        $sel = $bd->selectState($where);
	        $count = $sel->RecordCount();
			
	        if ($count == 0) {
	            $campos[] = '0';
	            $valores[] = $langVars['No_result'];
	        } else {
				$campos[] = '1';
	            $valores[] = $langVars['Select'];
				
	            while (!$sel->EOF) {
	                $campos[] = $sel->fields['idstate'];
	                $valores[] = $sel->fields['name'];              
	                $sel->MoveNext();
	            }
	        }
			
			$smarty->assign('stateids', $campos);
	        $smarty->assign('statevals', $valores);
			$smarty->assign('nostate', 0);
		}else{
			$smarty->assign('nostate', 1);			
		}
		$campos = '';
        $valores = '';
		if($state > 1){
	       $where = "where idstate=$state and idstate != 1";
	        $sel = $bd->selectCity($where);
	        $count = $sel->RecordCount();
			
	        if ($count == 0) {
	            $campos[] = '0';
	            $valores[] = $langVars['No_result'];
	        } else {
				$campos[] = '1';
	            $valores[] = $langVars['Select'];
				
	            while (!$sel->EOF) {
	                $campos[] = $sel->fields['idcity'];
	                $valores[] = $sel->fields['name'];              
	                $sel->MoveNext();
	            }
	        }
			
			$smarty->assign('cityids', $campos);
	        $smarty->assign('cityvals', $valores);
			$smarty->assign('nocity', 0);
		}else{
			$smarty->assign('nocity', 1);			
		}
		$campos = '';
        $valores = '';
		
        // busca valores para montar select de tipos de endereรงo
        $select = $bd->getTypeStreet();
        while (!$select->EOF) {
            $campos[] = $select->fields['idtypestreet'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('typestreetids', $campos);
        $smarty->assign('typestreetvals', $valores);
        //zera variaveis para liberar memoria
        $campos = '';
        $valores = '';

        // busca valores para montar select da localizacao
        $select = $bd->getLocation();
        while (!$select->EOF) {
            $campos[] = $select->fields['idlocation'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('locationids', $campos);
        $smarty->assign('locationvals', $valores);
        //zera variaveis para liberar memoria
        $campos = '';
        $valores = '';
        
		
        if ($vip == 'Y') {
            $smarty->assign('vipcheck', 'checked');
        } else {
            $smarty->assign('vipcheck', '');
        }
		
        $where = "where idperson=$company";
        $sel = $bd->getDepartment($where);
		
		while (!$sel->EOF) {
            $campos[] = $sel->fields['iddepartment'];
            $valores[] = $sel->fields['name'];
            $sel->MoveNext();
        }
        $smarty->assign('departmentids', $campos);
        $smarty->assign('departmentvals', $valores);
		$campos = '';
        $valores = '';

		$select = $bd->getLocation();
		$count = $select->RecordCount();
		
		if($count){
			$campos[] = "";
			$valores[] = $langVars['Select'];
	        while (!$select->EOF) {
	            $campos[] = $select->fields['idlocation'];
	            $valores[] = $select->fields['name'];
	            $select->MoveNext();
	        }
        }else{
        	$campos[] = "";
			$valores[] = $langVars['No_result'];
        }
		
        $smarty->assign('locationids', $campos);
        $smarty->assign('locationvals', $valores);
		$campos = '';
        $valores = '';
		
        $gender = $pData->fields['gender'];
        {
            if ($gender == 'M') {
                $smarty->assign('genderM', 'checked');
                $smarty->assign('genderF', '');
            } else {
                $smarty->assign('genderF', 'checked');
                $smarty->assign('genderM', '');
            }
        }
        if($typeperson==2)  $read = "disabled='disabled'";

        $smarty->assign('id', $id);
        $smarty->assign('login', $login);
        $smarty->assign('name', $name);
        $smarty->assign('email', $email);
        $smarty->assign('dtbirth', $dtbirth);
        $smarty->assign('phone', $phone);
        $smarty->assign('cellphone', $cellphone);
        $smarty->assign('ssn_cpf', $ssn_cpf);
        $smarty->assign('company', $company);
        $smarty->assign('country', $country);
        $smarty->assign('department', $department);
        $smarty->assign('neighborhood', $neighborhood);
        $smarty->assign('zipcode', $zipcode);
        $smarty->assign('idtypestreet', $idtypestreet);
        $smarty->assign('street', $street);
        $smarty->assign('number', $number);
        $smarty->assign('complement', $complement);
        $smarty->assign('typeperson', $typeperson);
        $smarty->assign('mask', $this->getConfig('id_mask'));
        $smarty->assign('state', $state);
        $smarty->assign('branch', $branch);
        $smarty->assign('city', $city);            
        $smarty->assign('read', $read);
		$smarty->assign('location', $location);
		$smarty->assign('time_value', $time_value);
		$smarty->assign('overtime', $overtime);
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/person/personeditformuser.tpl.html');
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/person/disable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
        if (!$this->_checkToken()) return false;
        $bd = new person_model();
        $dea = $bd->personDeactivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function activatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/person/active.tpl.html');
	}
	
    public function activate() {
        if (!$this->_checkToken()) return false;
        $id = $_POST['id'];
        $bd = new person_model();
        $dea = $bd->personActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function state() {
    	$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		
        $country = $_POST['country'];
        $state = $_POST['state'];
        $db = new person_model();
        $where = "where idcountry=$country and idstate != 1";
        $sel = $db->selectState($where);
        $count = $sel->RecordCount();
		
        if ($count == 0) {
            echo "<option value='0'>".$langVars['No_result']."</option>";
            exit();
        } else {
            $i = 0;
            echo "<option value='1'>".$langVars['Select']."</option>";
            while (!$sel->EOF) {
                $campos[] = $sel->fields['idstate'];
                $valores[] = $sel->fields['name'];
                if ($campos[$i] == $state) {
                    echo "<option value='$campos[$i]' selected='selected'>" . $valores[$i] . "</option>";
                } else {
                    echo "<option value='$campos[$i]'>" . $valores[$i] . "</option>";
                }
                $i++;
                $sel->MoveNext();
            }
        }
    }

    public function city() {
    	$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		
        $state = $_POST['state'];
        $city = $_POST['city'];
        $db = new person_model();
        $where = "where idstate=$state and idstate != 1";
        $sel = $db->selectCity($where);
        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value='0'>".$langVars['No_result']."</option>";
            exit();
        } else {
            $i = 0;
            echo "<option value='1'>".$langVars['Select']."</option>";
            while (!$sel->EOF) {
                $campos[] = $sel->fields['idcity'];
                $valores[] = $sel->fields['name'];
                if ($campos[$i] == $city) {
                    echo "<option value='$campos[$i]' selected='selected'>" . $valores[$i] . "</option>";
                } else {
                    echo "<option value='$campos[$i]'>" . $valores[$i] . "</option>";
                }
                $i++;
                $sel->MoveNext();
            }
        }
    }

    public function department() {
    	$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		
        $company = $_POST['company'];
        $department = $_POST['department'];
		
		
		if($company == ""){
			echo "<option value=''>".$langVars['Select_department']."</option>";
            exit();
		}
		else{
			$db = new person_model();
	        $where = "where idperson=$company AND status = 'A' ORDER BY name ASC";
	        $sel = $db->getDepartment($where);
	        $count = $sel->RecordCount();	
			
			if ($count == 0) {
	            echo "<option value=''>".$langVars['No_result']."</option>";
	            exit();
	        } else {
	            $i = 0;
	            echo "<option value=''>".$langVars['Select']."</option>";
	            while (!$sel->EOF) {
	                $campos[] = $sel->fields['iddepartment'];
	                $valores[] = $sel->fields['name'];
	                if ($campos[$i] == $department) {
	                    echo "<option value='$campos[$i]' selected='selected'>" . $valores[$i] . "</option>";
	                } else {
	                    echo "<option value='$campos[$i]'>" . $valores[$i] . "</option>";
	                }
	                $i++;
	                $sel->MoveNext();
	            }
	        }
		} 
    }

    public function autocomplete() {
        $term = $_POST['term'];
        $db = new person_model();
        $sel = $db->selectAutocomplete($term);


        while (!$sel->EOF) {
            $row['value'] = $sel->fields['value'];
            $row['id'] = $sel->fields['idstreet'];
            $row_set[] = $row;
            $sel->MoveNext();
        }
        echo json_encode($row_set); //format the array into json data        
    }

    public function streets() {
        $bd = new person_model();
        $rsStreets = $bd->getStreets();
        $output = array();
        while (!$rsStreets->EOF) {
            $output[] = array("id" => $rsStreets->fields['idstreet'],
                "name" => $rsStreets->fields['name']
            );
            $rsStreets->MoveNext();
        }
        echo json_encode($output);
    }

    public function neighborhoods() {
        $bd = new person_model();
        $id = $this->getParam('idcity');
        $rsStreets = $bd->getNeighborhoods($id);
        $output = array();
        if ($rsStreets->fields) {
            while (!$rsStreets->EOF) {
                $output[] = array("id" => $rsStreets->fields['idneighborhood'],
                    "name" => $rsStreets->fields['name']
                );
                $rsStreets->MoveNext();
            }
            echo json_encode($output);
        } else {
            $output[] = '';
            echo json_encode($output);
        }
    }

    public function insertLocation() {
    	extract($_POST);
        if (!$this->_checkToken()) return false;
        $bd = new location_model();
        $rs = $bd->insertLocation($name);
        if ($rs) {
            echo mysql_insert_id();
        } else {
            return false;
        }
    }
	
	public function getLocation(){
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		$bd = new person_model();
		$select = $bd->getLocation();
		$count = $select->RecordCount();
		
        if ($count == 0) {
            echo "<option value='0'>".$langVars['No_result']."</option>";
            exit();
        } else {
            $i = 0;
            echo "<option value='1'>".$langVars['Select']."</option>";
            while (!$select->EOF) {
                $campos[] = $select->fields['idlocation'];
                $valores[] = $select->fields['name'];
				echo "<option value='$campos[$i]'>" . $valores[$i] . "</option>";
				$i++;
				$select->MoveNext();
            }
        }
	}

    public function insertNatural() {
        if (!$this->_checkToken()) return false;
        extract($_POST);

        $db = new person_model();
        $db->BeginTrans();
        $natureperson = '1';
        $idtheme = '1';
        $dtcreate = date('Y-m-d H:i:s');
        $status = 'A';
		
		// Only type 3 (authentication by HD) that can be encrypted password
		if ($logintype == 3) {
			$password = md5($password);
		}	
		
        $ins = $db->insertPerson($logintype, $typeuser, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $phone, $branch, $mobile, $login, $password, $time_value, $overtime, $location,$changePassInsert);
        if ($ins) {
            $idperson = $ins;
        } else {
            $db->RollbackTrans();  
            return false;
        }

        $checkst = $db->checkStreet($address);
        if (!$checkst->fields) {
            $insSt = $db->insertStreet($typestreet, $address);
            if ($insSt) {
                $idstreet = $db->maxStreet();
            } else {
                $db->RollbackTrans();  
                return false;
            }
        } else {
            $idstreet = $checkst->fields['idstreet'];
        }
        $checknb = $db->checkNeighborhood($neighborhood);
        if (!$checknb->fields) {
            $insNb = $db->insertNeighborhood($city, $neighborhood);
            if ($insNb) {
                $idneighborhood = $db->maxNeighborhood();
            } else {
                $db->RollbackTrans();  
                return false;
            }
        } else {
            $idneighborhood = $checknb->fields['idneighborhood'];
        }
        $typeaddress = '2';
        $insData = $db->insertAdressData($idperson, $city, $idneighborhood, $idstreet, $typeaddress, $number, $complement, $zipcode);
        if (!$insData) {
            $db->RollbackTrans();  
            return false;
        }
        if($dtbirth){
            $dtbirth = $this->formatSaveDate($dtbirth);
        }
        $insNatural = $db->insertNaturalData($idperson, $cpf, $dtbirth, $gender);
        if (!$insNatural) {
            $db->RollbackTrans();  
            return false;
        }
        $depart = $db->insertInDepartment($idperson, $department);
        if (!$depart) {
            $db->RollbackTrans();  
            return false;
        }

        // Since 2016-05-09 - Rogério Albandes
        if(!empty($_POST['persontypes'])){
            foreach ($_POST['persontypes'] as $idtypeperson) {
                $insPersonTypes = $db->insertPersonTypes($idperson,$idtypeperson);
                if(!$insPersonTypes) {
                    $db->RollbackTrans();
                    return false;
                }
            }
        }
        //

        $db->CommitTrans(); 
        echo "OK";
    }

    public function insertJuridical() {
        if (!$this->_checkToken()) return false;
        extract($_POST);

        $db = new person_model();
        $logintype = '3';
        $natureperson = '2';
        $idtheme = '1';
		$dtcreate = date('Y-m-d H:i:s');		
        $status = 'A';
        $vip = 'N';
		$db->BeginTrans();
        $ins = $db->insertPerson($logintype, $typeuser, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $phone, $branch, $fax);
        if ($ins) {
            $idperson = $ins;
        } else {
			$db->RollbackTrans();  
            return false;
        }
		
        $checkst = $db->checkStreet($address);
        if (!$checkst->fields) {
            $insSt = $db->insertStreet($typestreet, $address);
            if ($insSt) {
                $idstreet = $db->maxStreet();
            } else {
				$db->RollbackTrans();  
                return false;
            }
        } else {
            $idstreet = $checkst->fields['idstreet'];
        }
        $checknb = $db->checkNeighborhood($neighborhood);
        if (!$checknb->fields) {
            $insNb = $db->insertNeighborhood($city, $neighborhood);
            if ($insNb) {
                $idneighborhood = $db->maxNeighborhood();
            } else {
				$db->RollbackTrans();  
                return false;
            }
        } else {
            $idneighborhood = $checknb->fields['idneighborhood'];
        }
        $typeaddress = '3';
        $insData = $db->insertAdressData($idperson, $city, $idneighborhood, $idstreet, $typeaddress, $number, $complement, $zipcode);
        if (!$insData) {
			$db->RollbackTrans();  
            return false;
        }
		
        $depart = $db->insertDepartment($idperson, $department);
        if (!$depart) {
			$db->RollbackTrans();  
            return false;
        }
        $insJuridical = $db->insertJuridicalData($idperson, $cnpj, $contact, $observation);
        if (!$insJuridical) {
			$db->RollbackTrans();  
            return false;
        }
        
		$db->CommitTrans(); 
        echo "OK";
        
    }

    public function checklogin() {
        extract($_POST);

        $db = new person_model();
        $check = $db->checkLogin($login);
        if ($check->fields) {
            return false;
        } else {
            echo "OK";
        }
    }

    public function editNatural() {
        print_r($_POST);
        exit ;
        if (!$this->_checkToken()) return false;
        extract($_POST);

        $db = new person_model();
		$db->BeginTrans();
		
		if($currentLoginType != 3 && $logintype == 3){
			$currentPassword = $db->getCurrentPassword($id);
			$changePass = $db->changePassword($id, md5($currentPassword));
			if(!$changePass){
				$db->RollbackTrans();
				return false;
			}
		}
        
        $updt = $db->updatePerson($id, $logintype, $typeuser, $name, $email, $vip, $phone, $branch, $mobile, $location, $time_value, $overtime);
        if (!$updt) {        
        	$db->RollbackTrans();
            return false;
        }
		
        $checkst = $db->checkStreet($address);
        if (!$checkst->fields) {
            $insSt = $db->insertStreet($typestreet, $address);
            if ($insSt) {
                $idstreet = $db->maxStreet();
            } else {
            	$db->RollbackTrans();
                return false;
            }
        } else {
            $idstreet = $checkst->fields['idstreet'];
        }
		
        $checknb = $db->checkNeighborhood($neighborhood);
        if (!$checknb->fields) {
            $insNb = $db->insertNeighborhood($city, $neighborhood);
            if ($insNb) {
                $idneighborhood = $db->maxNeighborhood();
            } else {
            	$db->RollbackTrans();
                return false;
            }
        } else {
            $idneighborhood = $checknb->fields['idneighborhood'];
        }
		
        $insData = $db->updateAdressData($id, $city, $idneighborhood, $idstreet, $number, $complement, $zipcode);
        if (!$insData) {
        	$db->RollbackTrans();
            return false;
        }
        if($dtbirth){
            $dtbirth = $this->formatSaveDate($dtbirth);
        }
        $insNatural = $db->updateNaturalData($id, $cpf, $dtbirth, $gender);
        if (!$insNatural) {
            $db->RollbackTrans();
            return false;
        }

        $depart = $db->updatePersonDepartment($id, $department);

        if (!$depart) {
            $db->RollbackTrans();
            return false;
        }

        // Since 2016-05-09 - Rogério Albandes
        $del = $db->delPersonTypes($id) ;
        if (!$del) {
            $db->RollbackTrans();
            return false;
        } else {
            foreach ($_POST['persontypes'] as $idtypeperson) {
                $insPersonTypes = $db->insertPersonTypes($id, $idtypeperson);
                if (!$insPersonTypes) {
                    $db->RollbackTrans();
                    return false;
                }
            }
        }
        //
		$db->CommitTrans();
        echo "OK";
    }
	
	public function editNaturalUser() {
        if (!$this->_checkToken()) return false;
        extract($_POST);
		$id = $_SESSION['SES_COD_USUARIO'];
		$db = new person_model();
        if (!$this->_checkToken()) return false;
        $updt = $db->updatePersonUser($id,$name, $email, $phone, $branch, $mobile, $location);
        $i = 0;
        if ($updt) {
            $i++;
        } else {
            return false;
        }
		
        $checkst = $db->checkStreet($address);
        if (!$checkst->fields) {
            $insSt = $db->insertStreet($typestreet, $address);
            if ($insSt) {
                $idstreet = $db->maxStreet();
            } else {
                return false;
            }
        } else {
            $idstreet = $checkst->fields['idstreet'];
        }
        $checknb = $db->checkNeighborhood($neighborhood);
        if (!$checknb->fields) {
            $insNb = $db->insertNeighborhood($city, $neighborhood);
            if ($insNb) {
                $idneighborhood = $db->maxNeighborhood();
            } else {
                return false;
            }
        } else {
            $idneighborhood = $checknb->fields['idneighborhood'];
        }
        $insData = $db->updateAdressData($id, $city, $idneighborhood, $idstreet, $number, $complement, $zipcode);
        if ($insData) {
            $i++;
        } else {
            return false;
        }
        $insNatural = $db->updateNaturalData($id, $cpf, $dtbirth, $gender);
        if ($insNatural) {
            $i++;
        } else {
            return false;
        }
       
        if ($i == 3) {
            echo "OK";
        } else {
            return false;
        }
    }
	
    public function editJuridical()
    {

        if (!$this->_checkToken()) return false;
        extract($_POST);

        $db = new person_model();
        $db->BeginTrans();
        $updt = $db->updateCompany($id, $typeuser, $name, $email, $phone, $branch, $fax);
        if (!$updt){ 
            $db->RollbackTrans(); 
            return false;
        }

        $checkst = $db->checkStreet($address);
        if (!$checkst->fields) {
            $insSt = $db->insertStreet($typestreet, $address);
            if ($insSt) {
                $idstreet = $db->maxStreet();
            } else {
                $db->RollbackTrans(); 
                return false;
            }
        } else {
            $idstreet = $checkst->fields['idstreet'];
        }
        $checknb = $db->checkNeighborhood($neighborhood);
        if (!$checknb->fields) {
            $insNb = $db->insertNeighborhood($city, $neighborhood);
            if ($insNb) {
                $idneighborhood = $db->maxNeighborhood();
            } else {
                $db->RollbackTrans(); 
                return false;
            }
        } else {
            $idneighborhood = $checknb->fields['idneighborhood'];
        }
		
        $insData = $db->updateAdressData($id, $city, $idneighborhood, $idstreet, $number, $complement, $zipcode);
        if (!$insData) {
            $db->RollbackTrans(); 
            return false;
        }

		$chkJuridical = $db->checkJuridicalData($id);
		if($chkJuridical->fields['idperson']){
			$insJuridical = $db->updateJuridicalData($id, $cnpj, $contact, $observation);
	        if (!$insJuridical){
                $db->RollbackTrans(); 
	            return false;
	        }
		}else{
			$insJuridical = $db->insertJuridicalData($id, $cnpj, $contact, $observation);
			if (!$insJuridical) {
	            $db->RollbackTrans(); 
	            return false;
	        }
		}
        $db->CommitTrans();
        echo "OK";
    }

    public function insertState() {
        if (!$this->_checkToken()) return false;
        extract($_POST);

        $db = new person_model();
        $ins = $db->insertState($country, $abbr, $name);
        if ($ins) {
            $max = $db->selectMaxState();
            echo $max;
        } else {
            return false;
        }
    }

    public function insertCity() {
        extract($_POST);
        if (!$this->_checkToken()) return false;
        $db = new person_model();
        $ins = $db->insertCity($state, $name);
        if ($ins) {
            $max = $db->selectMaxCity();
            echo $max;
        } else {
            return false;
        }
    }

    public function manageperm() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $smarty->assign('id', $id);
        $smarty->display('gridpersonpermisson.tpl.html');
    }

    public function permjson() {
        $smarty = $this->retornaSmarty();
        $theme_default = $this->getConfig('theme');
        $idperson = $this->getParam('idperson');
        $langVars = $smarty->get_config_vars();
        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];

        if (!$sortorder)
            $sortorder = 'asc';


        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);

        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];



        $where = "";
        if ($query) {
            switch ($qtype) {
                case 'tbp.name':
                    $where = "and  $qtype LIKE '$query%' ";
                    break;
                default:
                    break;
            }
        }
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";

        $bd = new programs_model();

        $rsProgram = $bd->selectProgram($where, $order, $limit);

        $qcount = $bd->countProgram($where);
        $total = $qcount->fields['total'];

        $db = new permissions_model();		
        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsProgram->EOF) {
            $idprogram = $rsProgram->fields['idprogram'];
			$defPerms = $db->getDefaultPerms($idprogram);
			$defP = array();
			while (!$defPerms->EOF) {
				$defP[$defPerms->fields['idaccesstype']] = $defPerms->fields['idaccesstype'];			
				$defPerms->MoveNext();	
			}
            $checkperm = $db->getPermissionData($idprogram, $idperson);
            if (!$checkperm->fields) {
                $rows[] = array(
                    "id" => $rsProgram->fields['idprogram']."|".$idperson,
                    "cell" => array(
                        $langVars[$rsProgram->fields['smarty']],
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        "<button id='add' name='add' style='width: 100px; margin-top:-4px; *padding-botton: 5px; height=15px; font-size: 10px; font-family: Arial' onclick='addNew(" . $idprogram . "," . $idperson . "); return false;'><img src='" . path . "/app/themes/" . $theme_default . "/images/add.png' height='10px' width='10px' /></button>"
                    )
                );
            } else {
                while (!$checkperm->EOF) {
                	$disabled = "";
                    $idaccesstype = $checkperm->fields['idaccesstype'];
                    if ($idaccesstype == 1) {
                        $type = 1;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $accesscheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $accesscheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\"/>";
                            }
                        } else {
                            $accesscheck = '-';
                        }
                    }
                    if ($idaccesstype == 2) {
                        $type = 2;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $newcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $newcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" />";
                            }
                        } else {
                            $newcheck = '-';
                        }
                    }
                    if ($idaccesstype == 3) {
                        $type = 3;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $editcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $editcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" />";
                            }
                        } else {
                            $editcheck = '-';
                        }
                    }
                    if ($idaccesstype == 4) {
                        $type = 4;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $deletecheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $deletecheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" />";
                            }
                        } else {
                            $deletecheck = '-';
                        }
                    }
                    if ($idaccesstype == 5) {
                        $type = 5;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $exportcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $exportcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" />";
                            }
                        } else {
                            $exportcheck = '-';
                        }
                    }
                    if ($idaccesstype == 6) {
                        $type = 6;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $emailcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $emailcheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" />";
                            }
                        } else {
                            $emailcheck = '-';
                        }
                    }
                    if ($idaccesstype == 7) {
                        $type = 7;
                        $acc = $db->getPersonPermission($idprogram, $idperson, $type);
                        if ($acc->fields && $defP[$type]) {
                            if ($acc->fields['allow'] == 'Y') {
                                $smscheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" checked />";
                            } else {
                                $smscheck = "<input type='checkbox' name='$idprogram-$idperson-$type' id='$idprogram-$idperson-$type' onclick=\"editperm(this.name,'$idprogram','$idperson','$type');\" />";
                            }
                        } else {
                            $smscheck = '-';
                        }
                    }
                    $checkperm->MoveNext();
                }
                $rows[] = array(
                    "id" => $rsProgram->fields['idprogram'],
                    "cell" => array(
                        $langVars[$rsProgram->fields['smarty']],
                        $accesscheck,
                        $newcheck,
                        $editcheck,
                        $deletecheck,
                        $exportcheck,
                        $emailcheck,
                        $smscheck,
                        "<button id='remove' name='remove' style='width: 100px; margin-top:-4px; *padding-botton: 5px; height=15px; font-size: 10px; font-family: Arial' onclick='deletePerms(" . $idperson . "," . $idprogram . "); return false;'><img src='" . path . "/app/themes/" . $theme_default . "/images/delete.png' height='10px' width='10px' /></button>",
                    )
                );
            }
            $rsProgram->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function personPermForm() {
        $smarty = $this->retornaSmarty();
        $idprogram = $this->getParam('id');
        $idperson = $this->getParam('idperson');
		
		$bd = new permissions_model();
		$defPerms = $bd->getDefaultPerms($idprogram);
		while (!$defPerms->EOF) {
			$defP[$defPerms->fields['idaccesstype']] = $defPerms->fields['idaccesstype'];			
			$defPerms->MoveNext();	
		}
		
		$smarty->assign('defP', $defP);
        $smarty->assign('id', $idprogram);
        $smarty->assign('person', $idperson);
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display("modals/person/personpermission.tpl.html");
    }

    public function insertPermException() {
        if (!$this->_checkToken()) return false;
		$access = $_POST['opaccess'];
		$newop = $_POST['opnew'];
		$edit = $_POST['opedit'];
		$deleteop = $_POST['opdelete'];
		$exportop = $_POST['opexport'];
		$email = $_POST['opemail'];
		$sms = $_POST['opsms'];
		$person = $_POST['person'];
		$prog = $_POST['id'];
		
		if(!$access) $access = "N";
		if(!$newop) $newop = "N";
		if(!$edit) $edit = "N";
		if(!$deleteop) $deleteop = "N";
		if(!$exportop) $exportop = "N";
		if(!$email) $email = "N";
		if(!$sms) $sms = "N";		

        $bd = new permissions_model();
        $i = 0;
        $accessid = 1;
        $ins = $bd->insertPersonExceptions($accessid, $prog, $person, $access);
        if ($ins) {
            $i++;
        } else {
            return false;
        }
        $accessid = 2;
        $ins2 = $bd->insertPersonExceptions($accessid, $prog, $person, $newop);
        if ($ins2) {
            $i++;
        } else {
            return false;
        }
        $accessid = 3;
        $ins3 = $bd->insertPersonExceptions($accessid, $prog, $person, $edit);
        if ($ins3) {
            $i++;
        } else {
            return false;
        }
        $accessid = 4;
        $ins4 = $bd->insertPersonExceptions($accessid, $prog, $person, $deleteop);
        if ($ins4) {
            $i++;
        } else {
            return false;
        }
        $accessid = 5;
        $ins5 = $bd->insertPersonExceptions($accessid, $prog, $person, $exportop);
        if ($ins5) {
            $i++;
        } else {
            return false;
        }
        $accessid = 6;
        $ins6 = $bd->insertPersonExceptions($accessid, $prog, $person, $email);
        if ($ins6) {
            $i++;
        } else {
            return false;
        }
        $accessid = 7;
        $ins7 = $bd->insertPersonExceptions($accessid, $prog, $person, $sms);
        if ($ins7) {
            $i++;
        } else {
            return false;
        }
        if ($i == 7) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function grantpermission() {
        extract($_POST);

        $bd = new permissions_model();
        $grant = $bd->grantPermissionPerson($idprogram, $idperson, $type, $check);
        if ($grant) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function revokepermission() {
        extract($_POST);

        $bd = new permissions_model();
        $grant = $bd->revokePermissionPerson($idprogram, $idperson, $type, $check);
        if ($grant) {
            echo "OK";
        } else {
            return false;
        }
    }
	
	public function removeExceptionsModal() {
		$smarty = $this->retornaSmarty();
		$idprogram = $this->getParam('idprogram');
		$idperson = $this->getParam('idperson');
		
		$smarty->assign('idprogram', $idprogram);
		$smarty->assign('idperson', $idperson);
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/person/deletepermission.tpl.html');
	}
	
    public function removeExceptions() {
        if (!$this->_checkToken()) return false;
		$idprogram = $_POST['idprogram'];
		$idperson = $_POST['idperson'];
	
        $bd = new permissions_model();
        $grant = $bd->removeExceptions($idprogram, $idperson);
        if ($grant) {
            echo "OK";
        } else {
            return false;
        }
    }
	
	public function modalPassword() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/person/alterpassword.tpl.html');
    }
	
    public function changePassword() {
        if (!$this->_checkToken()) return false;
        $id = $_POST['id'];
		$password = $_POST['password'];
		$bd = new person_model();
		$logintype = $bd->getLoginType($id);
		$change_pass =  $_POST['changePass'];
        if(!$change_pass) $change_pass = 0;
		if ($logintype == 3) {
			$password = md5($password);
		}

        $change = $bd->changePassword($id, $password, $change_pass);
        if ($change) {
            echo "OK";
        } else {
            return false;
        }
    }
	
	public function modalPasswordUser() {
		$smarty = $this->retornaSmarty();
        $smarty->assign('token', $this->_makeToken()) ;
		$smarty->display('modals/person/alterpassworduser.tpl.html');
    }

	public function changePasswordUser() {
        if (!$this->_checkToken()) return false;
        $id = $_SESSION['SES_COD_USUARIO'];
		$password = $_POST['password'];
        $password = md5($password);
        $bd = new person_model();
		$logintype = $bd->getLoginType($id);
		$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();
		
		if ($logintype == 1) {
			$data = array("result" => 1, "msg" => $langVars['Lost_password_pop']);
		}elseif ($logintype == 2) {
			$data = array("result" => 1, "msg" => $langVars['Lost_password_ad']);
		}
		else{
        	$change = $bd->changePassword($id, $password);
	        if ($change) {
	            $data = array("result" => 1, "msg" => "OK");
	        } else {
	            $data = array("result" => 0, "msg" => "Error");
	        }
		}		
		echo json_encode($data);
    }
	
	public function modalAttendantGroup(){
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		
		$db = new person_model();
		$typeperson = $db->getIdTypePerson($id);
		if($typeperson != 3 && $typeperson != 1){
			echo "error";
		}else{
			
			$userGroups = $db->getPersonGroups($id);
			
	        while (!$userGroups->EOF) {
	            $listagroup[$userGroups->fields['idgroup']] =$userGroups->fields['name'];
				$groupsIds .= $userGroups->fields['idgroup'].","; 
	            $userGroups->MoveNext();
	        }
	        $smarty->assign('listagroup', $listagroup);
			
			$idsGrp = substr($groupsIds,0,-1);
			if($idsGrp)
				$groups = "AND tbg.idgroup NOT IN ($idsGrp)";
			else
				$groups = null;
			$bd = new groups_model();
	        $select = $bd->selectGroup("AND tbg.status ='A' $groups", "ORDER BY name ASC", NULL);
	        while (!$select->EOF) {
	            $campos[] = $select->fields['idgroup'];
	            $valores[] = $select->fields['name'];
	            $select->MoveNext();
	        }			
	        $smarty->assign('groupsids', $campos);
	        $smarty->assign('groupsvals', $valores);
			
			$smarty->assign('id', $id);
            $smarty->assign('token', $this->_makeToken()) ;
			$smarty->display('modals/person/attendantgroup.tpl.html');
		}
	}

	public function groupinsert(){
        if (!$this->_checkToken()) return false;
        extract($_POST);
        if(!$idgroup) return false;
        $bd = new permissions_model();
        $grant = $bd->groupPersonInsert($idgroup, $idperson);
        if ($grant){
            echo "OK";
        }
        else{
            return false;
        }
    }
	
    public function groupdelete(){
        if (!$this->_checkToken()) return false;
        extract($_POST);
        $bd = new permissions_model();
        $delete = $bd->groupPersonDelete($idgroup, $idperson);
        if ($delete){
            echo "OK";
        }
        else{
            return false;
        }
    }


    public function ajaxPersonCombo()
    {

        $DB = new person_model();
        $where =  "AND tbp.idtypeperson = 4" ;
        $sel = $DB->selectPersonForJson($where , "ORDER BY tbp.name ASC");

        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value='0'>-----</option>";
            exit();
        } else {
            $i = 0;
            echo "<option value='0'>". $_POST['message'] ."</option>";
            while (!$sel->EOF) {
                $idcostumer   = $sel->fields['idperson'];
                $name            = $sel->fields['name']  ;
                echo "<option value='$idcostumer' >$name</option>";
                $sel->MoveNext();
            }
        }
    }


}

?>
