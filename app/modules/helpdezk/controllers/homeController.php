<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');
require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/pipeDateTime.php');

class home extends hdkCommon {
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        //
        $this->modulename = 'helpdezk' ;
        //

        $id = $this->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;

        $this->loadModel('ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->loadModel('admin/userconfig_model');
        $this->dbUserConfig = new userconfig_model();

    }

    public function index()
    {
        $cod_usu = $_SESSION['SES_COD_USUARIO'];

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);

        $this->_makeNavHdk($smarty);

        $this->makeDash($smarty);

        $this->makeMessages($smarty);

        $smarty->assign('jquery_version', $this->jquery);

        // -- navbar

        $smarty->assign('lnk_ticket',$this->helpdezkUrl . '/helpdezk/hdkTicket/index');

        $smarty->assign('lnk_newticket',$this->helpdezkUrl . '/helpdezk/hdkTicket/newTicket');

        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');
        //echo "<pre>"; print_r($_SESSION); echo "</pre>";
        $arrTypeExpDate = $this->_comboTypeExpireDate();
        $smarty->assign('typeexpdateids',  $arrTypeExpDate['ids']);
        $smarty->assign('typeexpdatevals', $arrTypeExpDate['values']);
        $smarty->assign('idtypeexpdate', $arrTypeExpDate['ids'][0]);

        $arrTypeView = $this->_comboTypeView();
        $smarty->assign('typeviewids',  $arrTypeView['ids']);
        $smarty->assign('typeviewvals', $arrTypeView['values']);
        $smarty->assign('idtypeview', $arrTypeView['ids'][0]);

        $smarty->assign('typeuser',$_SESSION['SES_TYPE_PERSON']);


        if($_SESSION['SES_TYPE_PERSON'] == 3 || $_SESSION['SES_TYPE_PERSON'] == 1){
            //Set Order to columns of Attendant's Grid
            $sord = isset($_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols'])
                ? $_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols']
                : $_SESSION['hdk']['SES_ORDER_ASC'] == 1 ? 'asc' : 'desc';
            $smarty->assign('sord',$sord);

            //Set Auto Refresh Attendant's Grid
            $autoRefresh = $_SESSION['hdk']['SES_REFRESH_OPERATOR_GRID']
                            ?  ($_SESSION['hdk']['SES_REFRESH_OPERATOR_GRID'] * 1000) : 0;
            $smarty->assign('autorefreshgrid', $autoRefresh);

            $smarty->display('hdk-operator.tpl');
        }
        else{$smarty->display('hdk-main.tpl');}



    }

    /*function makePersonData($smarty)
    {
        $cod_usu = $_SESSION['SES_COD_USUARIO'];

        $rsPerson = $this->getPersonById($cod_usu);

        $address = $rsPerson->fields['street']. ' ' . $rsPerson->fields['number'];
        if (!empty($rsPerson->fields['complement']))
            $address .= ' /'.$rsPerson->fields['complement'];

        $smarty->assign('user_name', $rsPerson->fields['name']);
        $smarty->assign('user_department', $rsPerson->fields['department']);
        $smarty->assign('user_company', $rsPerson->fields['company']);
        $smarty->assign('user_city', $rsPerson->fields['city']);

        $smarty->assign('user_number', $rsPerson->fields['number']);
        $smarty->assign('user_street', $rsPerson->fields['street']);
        $smarty->assign('user_typestreet', $rsPerson->fields['typestreet']);
        $smarty->assign('user_complement', $rsPerson->fields['complement']);
        $smarty->assign('user_city', $rsPerson->fields['city']);
        $smarty->assign('user_state', $rsPerson->fields['state_abbr']);
        $zip = $this->formatMask($rsPerson->fields['zipcode'],$this->getConfig('zip_mask'));
        $smarty->assign('user_zip', $zip);
        $phone = $this->formatMask($rsPerson->fields['telephone'],$this->getConfig('phone_mask'));
        $smarty->assign('user_phone',$phone);
        $cellphone = $this->formatMask($rsPerson->fields['cellphone'],$this->getConfig('cellphone_mask'));
        $smarty->assign('user_cellphone',$cellphone);

        // Update user data - Screen
        $personType = $rsPerson->fields['idtypeperson'];
        $aScreenAccess = $this->getArrayScreenFields(2,$personType,'persondata_form');

        $smarty->assign('login',$_SESSION['SES_LOGIN_PERSON']);
        $smarty->assign('id_person',$_SESSION['SES_COD_USUARIO']);

        // --- Person Name ---
        $smarty->assign('person_name_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_name') ? '' : 'disabled') ) ;

        if (empty($rsPerson->fields['name']))
            $smarty->assign('placeholder_name',$langVars['Placeholder_name']);
        else
            $smarty->assign('person_name',$rsPerson->fields('name'));

        // --- SSN (USA) or CPF (Brazil) ---
        $smarty->assign('ssn_cpf_disabled',($this->getScreenFieldEnable($aScreenAccess,'ssn_cpf') ? '' : 'disabled') ) ;

        if (empty($rsPerson->fields['ssn_cpf']))
            $smarty->assign('placeholder_ssn_cpf', $langVars['Placeholder_cpf']);
        $ssnCpf = '';
        $smarty->assign('ssn_cpf', $rsPerson->fields['ssn_cpf']);

        // --- Gender ---
        $smarty->assign('person_gender_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_gender') ? '' : 'disabled') ) ;
        $arrGender = $this->_comboGender();
        $smarty->assign('genderids',  $arrGender['ids']);
        $smarty->assign('gendervals', $arrGender['values']);
        $smarty->assign('idgender',   $rsPerson->fields['gender']);

        // --- Email ---
        $smarty->assign('person_email_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_email') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['email']))
            $smarty->assign('placeholder_email',$langVars['Placeholder_email']);
        else
            $smarty->assign('person_email',$rsPerson->fields['email']);

        // --- Date Birthday ---
        $smarty->assign('person_dtbirth_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_dtbirth') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['dtbirth']) or $rsPerson->fields['dtbirth'] == '0000-00-00')
            $smarty->assign('placeholder_dtbirth',$this->getConfig('date_placeholder'));
        else
            $smarty->assign('person_dtbirth',$this->formatDate($rsPerson->fields['dtbirth']));

        // --- Phone Number ---
        $smarty->assign('person_phone_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_phone') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['telephone']))
            $smarty->assign('placeholder_phone',$langVars['Placeholder_phone']);
        else
            $smarty->assign('person_phone',$rsPerson->fields['telephone']);

        // --- Branch Number ---
        $smarty->assign('person_branch_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_branch') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['branch_number']))
            $smarty->assign('person_branch','');
        else
            $smarty->assign('person_branch',$rsPerson->fields['branch_number']);

        // --- Cellphone Number ---
        $smarty->assign('person_cellphone_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_cellphone') ? '' : 'disabled') ) ;
        if (empty($rsPerson->fields['cellphone']))
            $smarty->assign('placeholder_cellphone',$langVars['Placeholder_cellphone']);
        else
            $smarty->assign('person_cellphone',$rsPerson->fields['cellphone']);

        // --- Country ---
        $smarty->assign('person_country_disabled',($this->getScreenFieldEnable($aScreenAccess,'person_country') ? '' : 'disabled') ) ;
        if ($rsPerson->fields['idcountry'] <= 1)
            $idCountryEnable = $this->getIdCountryDefault();
        else
            $idCountryEnable = $rsPerson->fields['idcountry'];
        $arrCountry = $this->comboCountries();
        $smarty->assign('countryids',  $arrCountry['ids']);
        $smarty->assign('countryvals', $arrCountry['values']);
        $smarty->assign('idcountry', $idCountryEnable  );

        // --- State ---
        if ($rsPerson->fields['idstate'] <= 1)
            $idStateEnable = $this->getIdStateDefault();
        else
            $idStateEnable = $rsPerson->fields['idstate'];
        $arrCountry = $this->comboStates($idCountryEnable);
        $smarty->assign('stateids',  $arrCountry['ids']);
        $smarty->assign('statevals', $arrCountry['values']);
        $smarty->assign('idstate',   $idStateEnable);

        // --- City ---
        if ($rsPerson->fields['idcity'] <= 1)
            $idCityEnable = 1;
        else
            $idCityEnable = $rsPerson->fields['idcity'];
        $arrCity = $this->comboCity($idStateEnable);
        $smarty->assign('cityids',  $arrCity['ids']);
        $smarty->assign('cityvals', $arrCity['values']);
        $smarty->assign('idcity',   $idCityEnable);

        // --- Zipcode ---
        if (empty($rsPerson->fields['zipcode']))
            $smarty->assign('placeholder_zipcode',$langVars['Placeholder_zipcode']);
        else
            $smarty->assign('person_zipcode',$rsPerson->fields['zipcode']);

        // --- Neighborhood ---
        if ($rsPerson->fields['idneighborhood'] <= 1)
            $idNeighborhoodEnable = 1;
        else
            $idNeighborhoodEnable = $rsPerson->fields['idneighborhood'];
        $arrNeighborhood = $this->comboNeighborhood($idCityEnable);
        $smarty->assign('neighborhoodids',  $arrNeighborhood['ids']);
        $smarty->assign('neighborhoodvals', $arrNeighborhood['values']);
        $smarty->assign('idneighborhood',   $idNeighborhoodEnable);

        // --- Type Street ---
        if ($rsPerson->fields['idtypestreet'] == 'Choose')
            $idTypeStreetEnable = '';
        else
            $idTypeStreetEnable = $rsPerson->fields['idtypestreet'];
        $arrTypestreet = $this->comboTypeStreet();
        $smarty->assign('typestreetids',  $arrTypestreet['ids']);
        $smarty->assign('typestreetvals', $arrTypestreet['values']);
        $smarty->assign('idtypestreet', $idTypeStreetEnable  );

        // --- Address ---
        if ($rsPerson->fields['street'] == 'Choose')
            $smarty->assign('placeholder_address',$langVars['Placeholder_address']);
        else
            $smarty->assign('person_address',$rsPerson->fields['street']);

        // --- Number ---
        $smarty->assign('person_number',$rsPerson->fields['number']);

        // --- Complement ---
        $smarty->assign('person_complement',$rsPerson->fields['complement']);

    }*/

    function makeMessages($smarty)
    {
        $license = $this->getConfig('license');
        $langVars = $this->getLangVars($smarty);

        $clTime = new pipeDateTime();

        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $rsMessages = $this->dbTicket->getNoteMessagesFromOperator($idPerson);

        $smarty->assign("num_messages", str_replace('$$',$rsMessages->RecordCount(),$langVars['Message_title']) );

        $i = 1;
        while(!$rsMessages->EOF) {
            if ( $license = '200701006' && $rsMessages->fields['iditem'] == 124) {
                $rsMessages->MoveNext();
                continue;
            };
            $aMessages[$i]['sender'] = $rsMessages->fields['name'];
            $aMessages[$i]['text'] = $rsMessages->fields['description'];
            $aMessages[$i]['datetime'] = $this->formatDateHour($rsMessages->fields['entry_date']);

            $aElapsed = $clTime->getSingleTime($rsMessages->fields['entry_date'], date('Y-m-d H:i:s'));
            if(isset($aElapsed['status'])){
                if($this->log)
                    $this->logIt($aElapsed['status'].': '. $aElapsed['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            }else{
                $idx = $aElapsed['sufix'];
                $aMessages[$i]['elapsed'] = $aElapsed['value'] . ' ' . $langVars[$idx]  ;
            }

            $url = $this->helpdezkUrl .'/helpdezk/hdkTicket/viewrequest/id/' . $rsMessages->fields['code_request'];
            $lnkTicket = '<a href="'.$url.'">'.$this->_editRequest($rsMessages->fields['code_request']).'</a>';
            //$aMessages[$i]['code_request'] =  $this->_editRequest($rsMessages->fields['code_request']);
            $aMessages[$i]['code_request'] =  $lnkTicket;
            $i++;
            $rsMessages->MoveNext();
        }

        $smarty->assign("messages", $aMessages);

    }

    function makeDash($smarty)
    {
        $langVars = $this->getLangVars($smarty);
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        /*
        $total = $this->dbHome->getTotalRequestsByPerson($idPerson);
        $newRequests = $this->dbHome->getRequestsCount($idPerson,1);
        $newRequestsPercent = ceil(($newRequests * 100)/$total);
        $InProgress  = $this->dbHome->getRequestsCount($idPerson,3);
        $InProgressPercent = ceil(($InProgress * 100)/$total);
        $waitingservice = $newRequests - $InProgress;
        $waitingservicePercent = ceil(($waitingservice * 100)/$total);
        $finished = $this->dbHome->getRequestsCount($idPerson,5);
        $finishedPercent = ceil(($finished * 100)/$total);
        $waitingApproval = $this->dbHome->getRequestsCount($idPerson,4);
        $waitingApprovalPercent = ceil(($waitingApproval * 100)/$total);
        $attended = $finished + $waitingApproval;
        $attendedPercent  =  ceil(($attended * 100)/$total);

        $smarty->assign('inprogress_requests', $InProgress);
        $smarty->assign('inprogress_requests_percent', $InProgressPercent);
        $smarty->assign('waiting_service_requests', $newRequests);
        $smarty->assign('waiting_service_requests_percent', $newRequestsPercent);
        $smarty->assign('attended_requests', $attended);
        $smarty->assign('attended_requests_percent', $attendedPercent);
        $smarty->assign('waiting_aprovall_requests', $waitingApproval);
        $smarty->assign('waiting_aprovall_requests_percent', $waitingApprovalPercent);
        $smarty->assign('approved_requests', $finished);
        $smarty->assign('approved_requests_percent', $finishedPercent);
        */





        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$idPerson);

        if ($imgFormat) {
            $imgPhoto = $idPerson.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $smarty->assign('person_photo', $this->getHelpdezkUrl().'/app/uploads/photos/' . $imgPhoto);

        $smarty->assign('total_requests', $total);


        /* Table */

        $clTime = new pipeDateTime();
        $license = $this->getConfig('license');

        $where = "WHERE (a.idstatus = 1 OR a.idstatus = 3) AND a.idperson_creator = $idPerson ";

        if ($license == '200701006') {
            $where .= " AND iditem <> 124";
        }

        $rsTicket =  $this->dbTicket->getTicketStats($this->langDefault,$where,'ORDER BY a.expire_date desc');
        $i = 1;
        while (!$rsTicket->EOF) {

            $mylist[$i]['subject'] = $this->_cutSubject($rsTicket->fields['subject'],55,' ... ');

            $mylist[$i]['ts_expire'] = $rsTicket->fields['ts_expire'];

            if($_SESSION['hdk']['SES_HIDE_DASH_PERIOD'] == 0){
                $arrRet = $this->setExpireDateStatusLbl($rsTicket,true,$clTime);
            }else{
                $arrRet = $this->setExpireDateStatusLbl($rsTicket,false,$clTime);
            }

            foreach($arrRet as $key =>$value){
                $mylist[$i][$key] = $value;
            }

            $mylist[$i]['code_request'] = $rsTicket->fields['code_request'];
            $mylist[$i]['code_request_fmt'] = $this->_editRequest($rsTicket->fields['code_request']);

            $i++;
            $rsTicket->MoveNext();
        }

        $smarty->assign("mylist", $mylist);
    }


    /**
     * Method to update user data.
     * This method is utilized from navigation bar, where user can update you data
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @uses $_POST['idperson'] directly
     * @uses $_POST['name'] directly
     * @uses $_POST['email'] directly
     * @uses $_POST['phone'] directly
     * @uses $_POST['branch'] directly
     * @uses $_POST['cellphone'] directly
     * @uses $_POST['cellphone'] directly
     * @uses $_POST['dtbirth'] directly
     * @uses $_POST['ssn'] directly
     * @uses $_POST['gender'] directly
     * @uses $_POST['city'])  directly
     * @uses $_POST['neighb'] directly
     * @uses $_POST['street'] directly
     * @uses $_POST['number'] directly
     * @uses $_POST['complement'] directly
     * @uses $_POST['zipcode'] directly
     * @uses $_POST['typestreet'] directly
     * @uses $_POST['street'] directly
     *
     * @since January 01, 2020
     *
     * @return string JSON {
     *                       "success": "true | false",
     *                       "message": "Error or success message",
     *                       "id":       "Record ID saved in database"
     *                     }
     *
     */
    function updateUserData()
    {

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $dbPerson->BeginTrans();

        $ret = $dbPerson->updatePersonUser($_POST['idperson'],$_POST['name'],$_POST['email'],$_POST['phone'],$_POST['branch'],$_POST['cellphone']);
        if (!$ret) {
            $dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Update user data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            echo json_encode(array('success' => false, 'message' => 'Can not update person data', 'id' => ''));
            exit;
        }


        $dtbirthday = $this->formatSaveDate($_POST['dtbirth']);

        $ret = $dbPerson->updateNaturalData($_POST['idperson'],$_POST['ssn'],$dtbirthday,$_POST['gender']);
        if (!$ret) {
            $dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Update user data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            echo json_encode(array('success' => false, 'message' => 'Can not update natural data', 'id' => ''));
            exit;
        }

        if (empty ($_POST['city']) or empty($_POST['neighb']) or empty($_POST['street'])){
            $dbPerson->CommitTrans();
            if($this->log)
                $this->logIt('Update user data. Incomplete Address: City or neighborhood or street empty  - User: '.$_SESSION['SES_LOGIN_PERSON'],6,'general');
            echo json_encode(array('success' => false, 'message' => 'Incomplete Address: City or neighborhood or street empty', 'id' => ''));
            exit;

        } else {
            $ret = $dbPerson->updateAdressData($_POST['idperson'],$_POST['city'],$_POST['neighb'],$_POST['number'],$_POST['complement'],$_POST['zipcode'],$_POST['typestreet'],$_POST['street']);
            if (!$ret) {
                $dbPerson->RollbackTrans();
                if($this->log)
                    $this->logIt('Update user data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                echo json_encode(array('success' => false, 'message' => 'Can not update adress data', 'id' => ''));
                exit;
            }

            $dbPerson->CommitTrans();
            if($this->log)
                $this->logIt('User update data  - User: '.$_SESSION['SES_LOGIN_PERSON'] ,6,'general');


        }

        echo json_encode(array('success' => true, 'message' => '', 'id' => ''));

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
        echo $this->comboNeighborhoodHtml($_POST['stateId']);

    }

    function completeStreet()
    {
        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $aRet = array();

        $where = "WHERE `name` LIKE  '%". $this->getParam('search')."%'";
        $group = 'GROUP BY NAME';
        $order = 'ORDER BY NAME ASC';


        $rs = $dbPerson->getStreet($where,$group,$order);

        while (!$rs->EOF) {
            array_push($aRet,$rs->fields['name']);
            $rs->MoveNext();
        }

        echo $this->makeJsonUtf8Compat($aRet);
    }

    function checkapproval()
    {
        echo $this->_checkapproval();
    }


    function savePhoto()
    {

        $char_search	= array("ã", "á", "à", "â", "é", "ê", "í", "õ", "ó", "ô", "ú", "ü", "ç", "ñ", "Ã", "Á", "À", "Â", "É", "Ê", "Í", "Õ", "Ó", "Ô", "Ú", "Ü", "Ç", "Ñ", "ª", "º", " ", ";", ",");
        $char_replace	= array("a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "u", "c", "n", "A", "A", "A", "A", "E", "E", "I", "O", "O", "O", "U", "U", "C", "N", "_", "_", "_", "_", "_");

        $iduser = $_POST['iduser'];

        if (!empty($_FILES)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $targetPath = $this->helpdezkPath . '/app/uploads/photos/' ;
            $fileName = str_replace($char_search, $char_replace, $fileName);

            $tmpFormat = $this->getImageFileFormat('/app/uploads/photos/'.$iduser);
            if($tmpFormat){
                unlink($targetPath.$iduser.'.'.$tmpFormat);

            }

            $targetFile = $targetPath.$iduser.$extension;

            if(!is_dir($targetPath)) {
                $this->logIt("Save user photo: # ". $iduser . ' - Directory: '. $targetPath.' does not exists, I will try to create it. - program: '.$this->program ,7,'general',__LINE__);
                if (!mkdir ($targetPath, 0777 )) {
                    $this->logIt("Can't save user photo: # ". $iduser . ' - I could not create the directory: '.$targetPath.' - program: '.$this->program ,3,'general',__LINE__);
                }

            }
            if (!is_writable($targetPath)) {
                $this->logIt("Save user photo: # ". $iduser . ' - Directory: '. $targetPath.' Is not writable, I will try to make it writable - program: '.$this->program ,7,'general',__LINE__);
                if (!chmod($targetPath,0777)){
                    $this->logIt("Can't save user photo: # ". $iduser . ' - Directory: '.$targetPath.' Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                }

            }
            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log){
                    $this->logIt("Save user photo: # ". $iduser . ' - File: '.$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
                }
            }else {
                if($this->log){
                    $this->logIt("Can't save user photo: # ". $iduser . ' - File: '.$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                }
                return false;
            }

        }

        echo "success";

    }


    /*
     * Make Expire date and status labels for requester dashboard
     */
    function setExpireDateStatusLbl($rs,$visibility,$clTime)
    {
        if($visibility){
            $arrRet['expire_date'] = $rs->fields['expire_date'];
            $aTime = $clTime->expireTime($rs->fields['seconds']);
            $arrRet['seconds'] = $aTime['time'] ;

            $arrRet['status'] = $aTime['status'] == 'ontime'
                ? '<div class="text-center col-sm-12"><span class="label label-success col-xs-12">'.$this->getLanguageWord('on_time').'</span></div>'
                : $aTime['status'] == 'overdue'
                    ? '<div class="text-center col-sm-12"><span class="label label-danger col-xs-12">'.$this->getLanguageWord('overdue').'</span></div>'
                    : '<div class="text-center col-sm-12"><span class="label label-warning col-xs-12">'.$this->getLanguageWord('Not_available_yet').'</span></div>';
        }else{
            if($rs->fields['idstatus'] == 1){
                $arrRet['expire_date'] = $this->getLanguageWord('Not_available_yet');
                $arrRet['seconds'] = '';
                $arrRet['status'] = '<div class="text-center col-sm-12"><span class="label label-warning col-xs-12">'.$this->getLanguageWord('Not_available_yet').'</span></div>';
            }else{
                $arrRet['expire_date'] = $rs->fields['expire_date'];
                $aTime = $clTime->expireTime($rs->fields['seconds']);
                $arrRet['seconds'] = $aTime['time'] ;
                if($aTime['status'] == 'ontime')
                    $arrRet['status'] = '<div class="text-center col-sm-12"><span class="label label-success col-xs-12">'.$this->getLanguageWord('on_time').'</span></div>';
                elseif ($aTime['status'] == 'overdue')
                    $arrRet['status'] = '<div class="text-center col-sm-12"><span class="label label-danger col-xs-12">'.$this->getLanguageWord('overdue').'</span></div>';

            }
        }

        return $arrRet;

    }

    public function checkUserPass() {

        $idperson = $_POST['personId'];
        $password = md5($_POST['userconf_password']);

        $ret = $this->dbHome->checkUserPass($idperson, $password);
        if (!$ret) {
            if($this->log)
                $this->logIt('Can\'t password data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($ret->fields) {
            echo json_encode($this->getLanguageWord('Alert_not_match_new_pass'));
        } else {
            echo json_encode(true);
        }

    }

    /**
     * Method to save External APIs configurations
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @uses $_POST['idperson'] directly
     * @uses $_POST['trellokey'] directly
     * @uses $_POST['trellotoken'] directly
     *
     * @since December 29, 2019
     *
     * * @return array [
     *                  'success'       => true| false,
     *                  'message'       => Error or success message
     *                  'id'            => Record ID saved in database
     *                 ]
     */
    public function saveConfigExternal()
    {

        if(!$this->dbUserConfig->existApiConfigTables()) {
            echo json_encode(array('sucess' => false,'message' => 'There are no external APIs Configuration Tables !','id' => ''));
            exit;
        }

        $idPerson    = $_POST['idperson'];
        $trelloKey   = $_POST['trellokey'];
        $trelloToken = $_POST['trellotoken'];

        $arrayParam = array(
            array( 'field' => 'key',
                'value' => $trelloKey)
        ,
            array( 'field' => 'token',
                'value' => $trelloToken)
        );

        $this->dbUserConfig->BeginTrans();

        $arrayReturn = $this->dbUserConfig->insertExternalSettings(50,$idPerson);

        if (!$arrayReturn['success']) {
            echo json_encode($arrayReturn);
            $this->dbUserConfig->RoolbackTrans();
            exit;
        } else {
            $idexternalsettings = $arrayReturn['id'] ;
            foreach ($arrayParam as $row) {
                $arrayReturn = $this->dbUserConfig->insertExternalField($idexternalsettings,$row['field'],$row['value']);
                if (!$arrayReturn['success']) {
                    echo json_encode($arrayReturn);
                    $this->dbUserConfig->RoolbackTrans();
                    exit;
                }
            }

        }

        $this->dbUserConfig->CommitTrans();

        echo json_encode($arrayReturn);

    }

    public function changeUserPassword() {

        $idperson = $_POST['idperson'];
        $password = md5($_POST['newpassword']);
        $changepass = 0;

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
}

?>
