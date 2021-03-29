<?php

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkTicket extends hdkCommon {
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();


        if (!empty($this->getParam('token'))) {
            if (!$this->_tokenAuthentication($this->getParam('id'),$this->getParam('token'))) {
                $this->accessDenied();
            }
        }

        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;

        $this->loadModel('ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->loadModel('groups_model');
        $dbGroup = new groups_model();
        $this->dbGroup = $dbGroup;

        $this->loadModel('evaluation_model');
        $dbEvaluation = new evaluation_model();
        $this->dbEvaluation = $dbEvaluation;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        /*
         * The Ticket is not registered as a regular program, as it is not in the menu - it is part of the helpdezk core,
         * so only the test is done to check if it is a user or operator
         */
        if($_SESSION['SES_TYPE_PERSON'] != 2 and $_SESSION['SES_TYPE_PERSON'] != 3)
            $this->accessDenied();

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHdk($smarty);

        $arrTypeExpDate = $this->_comboTypeExpireDate();
        $smarty->assign('typeexpdateids',  $arrTypeExpDate['ids']);
        $smarty->assign('typeexpdatevals', $arrTypeExpDate['values']);
        $smarty->assign('idtypeexpdate', $arrTypeExpDate['ids'][0]);

        $arrTypeView = $this->_comboTypeView();
        $smarty->assign('typeviewids',  $arrTypeView['ids']);
        $smarty->assign('typeviewvals', $arrTypeView['values']);
        $smarty->assign('idtypeview', $arrTypeView['ids'][0]);

        if($this->getParam('mytickets')){
            $smarty->assign('typeuser',2);
            $smarty->assign('flgoperator',1);
            $smarty->assign('operatorAsUser',1);
        }else{
            $smarty->assign('typeuser',$_SESSION['SES_TYPE_PERSON']);
            $smarty->assign('flgoperator',0);
            $smarty->assign('operatorAsUser',0);
        }

        if(($_SESSION['SES_TYPE_PERSON'] == 3 || $_SESSION['SES_TYPE_PERSON'] == 1) && !$this->getParam('mytickets')){
            $sord = isset($_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols'])
                ? $_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols']
                : $_SESSION['hdk']['SES_ORDER_ASC'] == 1 ? 'asc' : 'desc';

            $sidx = isset($_SESSION['SES_PERSONAL_USER_CONFIG']['orderfield'])
                ? $_SESSION['SES_PERSONAL_USER_CONFIG']['orderfield']
                : 'a.expire_date';
        }else{
            $sidx = 'a.entry_date';
            $sord = 'desc';
        }
        $smarty->assign('sidx',$sidx);
        $smarty->assign('sord',$sord);

        $autoRefresh = ($_SESSION['SES_TYPE_PERSON'] == 3 || $_SESSION['SES_TYPE_PERSON'] == 1)
                        ?  $_SESSION['hdk']['SES_REFRESH_OPERATOR_GRID'] ?  ($_SESSION['hdk']['SES_REFRESH_OPERATOR_GRID'] * 1000) : 0
                        : 0;
        $smarty->assign('autorefreshgrid', $autoRefresh);

        $smarty->assign('hidden_login',$_SESSION['SES_LOGIN_PERSON']) ; // Demo Version
        $smarty->assign('demoversion', $this->demoVersion);             // Demo version

        $smarty->display('ticket.tpl');

    }

    public function newTicket()
    {
        $this->validasessao();

        /*
         * The Ticket is not registered as a regular program, as it is not in the menu - it is part of the helpdezk core,
         * so only the test is done to check if it is a user or operator
         *
         */
        if($_SESSION['SES_TYPE_PERSON'] != 2 and $_SESSION['SES_TYPE_PERSON'] != 3)
            $this->accessDenied();

        $smarty = $this->retornaSmarty();

        $langVars = $this->getLangVars($smarty);

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHdk($smarty);

        $idPerson   = $_SESSION['SES_COD_USUARIO']; // user id
        $typePerson = $_SESSION['SES_TYPE_PERSON']; //user type

        if ($_SESSION['hdk']['SES_IND_TIMER_OPENING'] == 1)
            $smarty->assign('timer', 1); //Start countdown
        else
            $smarty->assign('timer', 0); //Don't start countdown

        if($_SESSION['SES_IND_EQUIPMENT'] == 1)
            $smarty->assign('equipment', 1);
        else
            $smarty->assign('equipment',1);

        $smarty->assign('id_person', $_SESSION['SES_COD_USUARIO']);
        $smarty->assign('id_company', $_SESSION['SES_COD_EMPRESA']);

        $arrArea = $this->_comboArea();
        $idAreaDefault = $this->_getIdAreaDefault();
        $smarty->assign('areaids',  $arrArea['ids']);
        $smarty->assign('areavals', $arrArea['values']);
        $smarty->assign('idarea', $idAreaDefault);

        $smarty->assign('ticketattmaxfiles', $this->_getTicketAttMaxFiles());
        $smarty->assign('ticketacceptedfiles', $this->_getTicketAcceptedFiles());
        $smarty->assign('hdkMaxSize', substr($this->_getTicketAttMaxFileSize(),0,-1) );

        $smarty->assign('summernote_version', $this->summernote);
        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');

        $smarty->assign('hidden_login',$_SESSION['SES_LOGIN_PERSON']) ; // Demo Version
        $smarty->assign('demoversion', $this->demoVersion);             // Demo version

        if($typePerson == 3){
            $arrRequestUser = $this->_comboRequestUser();
            $smarty->assign('userids',  $arrRequestUser['ids']);
            $smarty->assign('uservals', $arrRequestUser['values']);
            $smarty->assign('iduser', $idPerson);

            $arrSource = $this->_comboSource();
            $smarty->assign('sourceids', $arrSource['ids']);
            $smarty->assign('sourcevals', $arrSource['values']);
            $smarty->assign('sourcedefault', 1); //SET HELPDEZK AS DEFAULT

            $arrWay = $this->_comboWay();
            $smarty->assign('wayids',  $arrWay['ids']);
            $smarty->assign('wayvals', $arrWay['values']);
            $smarty->assign('waydefault', 1);

            $smarty->assign('field_att_way', 1);
            $smarty->assign('timedefault',  date("H:i") );
            $sysdate = date('d/m/Y',strtotime('now'));
            $smarty->assign('datedefault',$sysdate);


            $smarty->display('new_ticket-operator.tpl');
        }else{            
            $smarty->assign('owner', $_SESSION['SES_NAME_PERSON']);
            $smarty->display('new_ticket.tpl');
        }
        

    }

    public function saveTicket()
    {
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $this->loadModel('ticketrules_model');
        $dbRules = new ticketrules_model();

        //CREATE THE CODE REQUEST
        $code_request = $this->createRequestCode();

        $typePerson = $_SESSION['SES_TYPE_PERSON'];

        if($typePerson == 3){
            $minTelephoneTime = number_format($_POST["open_time"], "2", ".", ",");
            $minAttendanceTime = (int) $_POST["open_time"];
            
            $idPerson  		= $_POST["idrequester"];
            $idPersonAuthor = $_SESSION["SES_COD_USUARIO"];
            $idWay 		    = $_POST["way"];
            $idSource	= $_POST["source"];
            $solution = str_replace("'", "`", $_POST["solution"]);

            //if telephone
            if ($idSource == 2){
                $minTelephoneTime = $minTelephoneTime;
                $minExpendedTime = $minAttendanceTime;
            }else{
                $minTelephoneTime = 0;
                $minExpendedTime = 0;
            }
        }else{
            $idPerson 	= $_SESSION["SES_COD_USUARIO"];
            $idPersonAuthor = $_SESSION["SES_COD_USUARIO"];
            $idWay 		 = 1;
            $idSource	= 1;
        }

        
        $idCompany 	= $_SESSION['SES_COD_EMPRESA'];

        // -- Equipment --------------------------
        $numberSerial	= $_POST["serial_number"];
        $numberOS 	= $_POST["os_number"];
        $numberTag 	= $_POST["tag"];

        $idType 	= $_POST["type"];
        $idService 	= $_POST["service"];
        $idItem		= $_POST["item"];
        if(empty($_POST['reason']))
            $idReason 		= "NULL";
        else
            $idReason = $_POST['reason'];
        
        $subject 	 = str_replace("'", "`", $_POST["subject"]);
        $description = str_replace("'", "`", $_POST["description"]);
        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files
        
        $idStatus 	= 1;

        $rsRules = $dbRules->getRule($idItem,$idService);
        if ($rsRules->RecordCount() > 0 )  // If have approval rule, put the status of the ticket as repassed (2)
            $idStatus = 2;

        if ( $this->checkVipUser($idPerson) == 1 &&  $this->checkVipPriority() == 1) {
            $idPriority = $this->getVipPriority();
        } else {
            $idPriority = $this->getServicePriority($idService);
        }

        $insertHour = !$_POST['time'] ? date("H:i") : $_POST['time'];
        if($this->database == 'oci8po') {
            $insertDate = !$_POST['date'] ? date("d/m/Y") : $_POST['date'];
            $startDate = $this->formatSaveDateHour($insertDate." ".$insertHour);
            $startDate = $this->oracleDate($startDate);
        }
        elseif($this->isMysql($this->database)){
            $insertDate = !$_POST['date'] ? date("Y-m-d") : str_replace("'", "", $this->formatSaveDate($_POST['date']));
            $startDate = $insertDate." ".$insertHour;
        }

        $expireDate = $this->getExpireDate($startDate, $idPriority, $idService);

        $this->dbTicket->BeginTrans();
        $rs = $this->dbTicket->insertRequest($idPersonAuthor,$idSource,$startDate,$idType,$idItem,$idService,$idReason,$idWay,$subject,$description,$numberOS,$idPriority,$numberTag,$numberSerial,$idCompany,$expireDate,$idPerson,$idStatus,$code_request);
        if(!$rs){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $idGroup = $this->dbTicket->getServiceGroup($idService);
        if (!$idGroup){
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $numRules = $rsRules->RecordCount();

        $this->loadModel('groups_model');
        $dbGroup = new groups_model();
        if ($numRules > 0) { // If have one approver for this service
            $count = 1;
            $values = '';
            while (!$rsRules->EOF) {
                if($rsRules->fields['order'] == 1)
                    $idPersonApprover = $rsRules->fields['idperson'];
                $values .= "(".$rsRules->fields['idapproval'].",". $code_request .",". $rsRules->fields['order'] .",". $rsRules->fields['idperson'] .",". $rsRules->fields['fl_recalculate'] .")";
                if($numRules != $count)
                    $values .= ",";
                $count++;
                $rsRules->MoveNext();
            }

            $ret = $dbRules->insertApproval($values);

            if ($ret) {

                $onlyRep = $dbGroup->checkGroupOnlyRepass($idGroup);

                if($onlyRep->fields['repass_only'] == "Y"){
                    $rsNewGroup = $dbGroup->getNewGroupOnlyRepass($idGroup,$_SESSION['SES_COD_EMPRESA']);
                    $idGroup_2 = $rsNewGroup->fields['idperson'];
                    if($idGroup_2)
                        $rs2 = $this->dbTicket->insertRequestCharge($code_request, $idGroup_2, 'G', '0');
                    else
                        $rs2 = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '0');

                } else{
                    $rs2 = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '0');
                }

                $rs = $this->dbTicket->insertRequestCharge($code_request, $idPersonApprover, 'P', '1');

                if(!$rs || !$rs2){
                    if($this->log)
                        $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false;
                }
            } else {
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }
        } else {

            $rsOnlyRep = $dbGroup->checkGroupOnlyRepass($idGroup);

            if($rsOnlyRep->fields['repass_only'] == "Y"){

                $rsNewIdGroup = $dbGroup->getNewGroupOnlyRepass($idGroup,$_SESSION['SES_COD_EMPRESA']);
                $idGroup_2 = $rsNewIdGroup->fields['idperson'];

                if($idGroup_2)
                    $rs = $this->dbTicket->insertRequestCharge($code_request, $idGroup_2, 'G', '1');
                else
                    $rs = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');

                if(!$rs){
                    if($this->log)
                        $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false;
                }

            } else{

                $rs = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');

                if(!$rs){
                    $this->dbTicket->RollbackTrans();
                    if($this->log)
                        $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    return false;
                }
            }
        }

        if($typePerson == 3){
            $ret = $this->dbTicket->insertRequestTimesNew($code_request,0,0,$minExpendedTime,$minTelephoneTime,0);
        }else{
            $ret = $this->dbTicket->insertRequestTimesNew($code_request);
        }
        
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $ret = $this->dbTicket->insertRequestDate($code_request);
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        // Inserts in the control table the status change made and which user did and the date of the event
        $ret = $this->dbTicket->insertRequestLog($code_request,date("Y-m-d H:i:s"),$idStatus,$idPerson);
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        // link attachments to the request
        if($aSize > 0){
            $retAttachs = $this->linkTicketAttachments($code_request,$aAttachs);
            if(!$retAttachs['success']){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }
        }        

        $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
        $description = "<p><b>" . $langVars['Request_opened'] . "</b></p>";

        $serviceVal = 'NULL';
        $public     = 1;
        $typeNote   = 3;
        $callback   = 0;
        $execDate   = '0000-00-00 00:00:00';

        $totalminutes   = 0 ;
        $starthour      = 0;
        $finishour      = 0;
        $hourtype       = 0 ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        if($solution && $solution != '<p><br></p>'){
            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
            $description = "<p><b>" . $langVars['Solution'] . "</b></p>". $solution;

            $con = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, 3, $ipAddress, $callback, 'NULL' );
            if(!$con){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }				
        }

        $this->dbTicket->CommitTrans();

        $aRet = array(
                    "coderequest" => $code_request,
                    "expire" => $this->formatDateHour($expireDate),
                    "incharge" => $this->_inchargeName($code_request)
        );

        echo json_encode($aRet);

    }

    public function json()
    {
        $this->protectFormInput();

        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        $where = '';

        $idStatus = $_POST['idstatus'];
        if ($idStatus) {
            if ($idStatus == 'ALL') {
                $where = '';
            } else {
                    $where .= " AND b.idstatus_source =" . $idStatus . " ";
            }
        } else {
            if(!in_array($_SESSION['SES_TYPE_PERSON'],array(3,1))){
                $flgapvreq = $this->_checkapproval(); // check if user have request to aprove
                if($flgapvreq > 0){
                    $where .= " AND b.idstatus_source = 4 ";
                }
            }

        }

        if ($this->getConfig('license') == '200701006') {
            $where .= " AND iditem <> 124";
        }

        // create the query.
        $page = $_POST['page'];
        $limit = $_POST['rows'];
        $sidx = $_POST['sidx'];
        $sord = $_POST['sord'];

        if(!$sidx)
            $sidx ='code_request';
        if(!$sord)
            $sord ='desc';

        if ($_POST['_search'] == 'true'){
            /*
            searchField	code_request
            searchOper	ge
            searchString	2017
            */
            $arrSearch = array('.','-','/','_');
            $arrReplace = array('','','','');

            if ( $_POST['searchField'] == 'code_request'){
                $searchField = 'a.code_request';
                $_POST['searchString'] = str_replace($arrSearch,$arrReplace,$_POST['searchString']);
            }
            if ( $_POST['searchField'] == 'entry_date') $searchField = "DATE_FORMAT(a.entry_date,'%d/%m/%Y')";
            if ( $_POST['searchField'] == 'subject') $searchField = 'a.subject';
            if ( $_POST['searchField'] == 'expire_date') $searchField = 'a.expire_date';
            if ( $_POST['searchField'] == 'in_charge') $searchField = 'resp.name';
            if ( $_POST['searchField'] == 'status') $searchField = 'b.user_view';

            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }


        $count = $this->dbTicket->getNumberRequests($where,$cod_usu);
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $limit*$page - $limit;
        if($start <0) $start = 0;
        //


        if ($this->database == 'oci8po') {
            $entry_date = " to_char(a.entry_date,'DD/MM/YYYY HH24:MI') fmt_entry_date " ;
            $expire_date = " to_char(a.expire_date,'DD/MM/YYYY HH24:MI')expire_date , a.expire_date  AS fmt_expire_date" ;
        }
        else
        {
            $entry_date = " DATE_FORMAT(a.entry_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as fmt_entry_date" ;
            $expire_date = " DATE_FORMAT(a.expire_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as expire_date, a.expire_date AS fmt_expire_date" ;
        }

        $rsTicket = $this->dbTicket->getRequest($entry_date, $expire_date, $where, $sidx,$sord,$start,$limit,$cod_usu);
        while (!$rsTicket->EOF) {
            $star = ($rsTicket->fields['flag_opened'] == 1 && $rsTicket->fields['status'] != 1) ? '<i class="fa fa-star" />' : '';
            $expire = $_SESSION['hdk']['SES_HIDE_GRID_PERIOD'] == 0
                        ? $rsTicket->fields['expire_date']
                        : $rsTicket->fields['status'] == 1 ? $this->getLanguageWord('Not_available_yet') : $rsTicket->fields['expire_date'];

            $rows[] = array(
                'star' => $star ,
                'code_request' => $rsTicket->fields['code_request'],
                'entry_date' => $rsTicket->fields['entry_date'],
                'subject' => $rsTicket->fields['subject'],
                'expire_date' => $expire,
                'in_charge' => $rsTicket->fields['in_charge'],
                'statusview' => '<span style="color:'.$rsTicket->fields['color_status'].'">'.$rsTicket->fields['statusview'].'</span>'

            );
            $rsTicket->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
                        'records' => $rsTicket->RecordCount(),
                        'rows' => $rows
        );

        echo json_encode($data);
    }

    public function viewrequest()
    {

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $this->makeNavVariables($smarty);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHdk($smarty);

        $where = 'WHERE code_request = '.$this->getParam('id');
        $rsTicket = $this->dbTicket->getRequestData($where);

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $typeperson = $_SESSION['SES_TYPE_PERSON'];
        $idowner  = $rsTicket->fields['idperson_owner'];
        $flgOpeAsUser = $this->getParam('myticket') ? $this->getParam('myticket') : 0;

        if($typeperson == 2)
            if($idperson != $idowner) die($langVars['Access_denied']);

        $coderequest    = $rsTicket->fields['code_request'];
        $namecreator    = $rsTicket->fields['name_creator'];
        $owner          = $rsTicket->fields['personname'];
        $department     = $rsTicket->fields['department'];
        $source         = $rsTicket->fields['source'];
        $prorrogs       = $rsTicket->fields['extensions_number'];
        $incharge       = $rsTicket->fields['id_in_charge'];
        $inchargename   = $rsTicket->fields['in_charge'];
        $status         = $rsTicket->fields['status'];

        $iddepartment   = $rsTicket->fields['iddepartment'];
        $company        = $rsTicket->fields['company'];
        $entrydate      = $this->formatDateHour($rsTicket->fields['entry_date']);
        $expiredate      = $this->formatDateHour($rsTicket->fields['expire_date']);

        $idarea         = $rsTicket->fields['idarea'];
        $idtype         = $rsTicket->fields['idtype'];
        $iditem         = $rsTicket->fields['iditem'];
        $os             = $rsTicket->fields['os_number'];
        $serial         = $rsTicket->fields['serial_number'];
        $label          = $rsTicket->fields['label'];
        $idstatus       = $rsTicket->fields['idstatus'];
        $idservice      = $rsTicket->fields['idservice'];
        $idpriority     = $rsTicket->fields['idpriority'];
        $idreason       = $rsTicket->fields['idreason'];
        $idway          = $rsTicket->fields['idattendance_way'];

        $subject        = $rsTicket->fields['subject'];
        $description    = $rsTicket->fields['description'];

        if($typeperson == 2 && $rsTicket->fields['flag_opened'] == 1) $this->dbTicket->updateFlag($coderequest, 0);
        $this->dbTicket->updateFlagNote($coderequest, 0, $idperson);

        $arrArea = $this->_comboArea();
        $smarty->assign('areaids',  $arrArea['ids']);
        $smarty->assign('areavals', $arrArea['values']);

        $arrType = $this->_comboType($idarea);
        $smarty->assign('typeids',  $arrType['ids']);
        $smarty->assign('typevals', $arrType['values']);

        $arrItem = $this->_comboItem($idtype);
        $smarty->assign('itemids',  $arrItem['ids']);
        $smarty->assign('itemvals', $arrItem['values']);

        $arrService = $this->_comboService($iditem);
        $smarty->assign('serviceids',  $arrService['ids']);
        $smarty->assign('servicevals', $arrService['values']);

        $arrPriority = $this->_comboPriority();
        $smarty->assign('priorityids',  $arrPriority['ids']);
        $smarty->assign('priorityvals', $arrPriority['values']);

        $arrReason = $this->_comboReason($idservice);
        $smarty->assign('reasonids',  $arrReason['ids']);
        $smarty->assign('reasonvals', $arrReason['values']);

        $arrWay = $this->_comboWay();
        $smarty->assign('wayids',  $arrWay['ids']);
        $smarty->assign('wayvals', $arrWay['values']);

        $arrTypeNote = $this->_comboTypeNote();
        $smarty->assign('typenoteids',  $arrTypeNote['ids']);
        $smarty->assign('typenotevals', $arrTypeNote['values']);

        $arrTypeHour = $this->_comboTypeHour();
        $smarty->assign('typehourids',  $arrTypeHour['ids']);
        $smarty->assign('typehourvals', $arrTypeHour['values']);

        // Attach
        $rsAttach = $this->dbTicket->selectAttach($coderequest);
        if ($rsAttach->fields) {
            $hasAttach = 1;
            while (!$rsAttach->EOF) {
                $filename = $rsAttach->fields['file_name'];
                $ext = strrchr($filename, '.');
                $idAttach = $rsAttach->fields['idrequest_attachment'];
                $attach[$filename] = '<button type="button" class="btn btn-default btn-xs" id="'.$idAttach.'" onclick="download('.$idAttach.',\'request\')"><span class="fa fa-file-alt"></span>  &nbsp;'.$filename.'</button>';
                $rsAttach->MoveNext();
            }
            $attach = implode(" ", $attach);
        } else {
            $hasAttach = 0;
        }

        $smarty->assign('hasattach', $hasAttach);
        $smarty->assign('attach_files', $attach);
        //

        // Notes
        //
        $lineNotes = $this->makeNotesScreen($this->getParam('id'));
        $smarty->assign('notes', $lineNotes);
        //
        // Buttons
        if($typeperson == 3 && $idowner == $idperson && $flgOpeAsUser == 1){
            $typepersontmp = 2;
        }else{
            $typepersontmp = $typeperson;
        }
        $this->makeViewTicketButtons($smarty,$typepersontmp,$this->getParam('id'),$idstatus);
        //

        $qtprorrogation = $_SESSION['hdk']['SES_QT_PRORROGATION'];
        if ($qtprorrogation == NULL) {
            $smarty->assign('show_btn_change_expire', 1);
        } else {
            if ($qtprorrogation == 0) {
                $smarty->assign('show_btn_change_expire', 0);
            } else {
                if ($prorrogs < $qtprorrogation) {
                    $smarty->assign('show_btn_change_expire', 1);
                } else {
                    $smarty->assign('show_btn_change_expire', 0);
                }
            }
        }
        if ($_SESSION['hdk']['SES_SHARE_VIEW'] == 1) {
            $checkedassume = 'checked="checked"';
        } else {
            $checkedassume = '';
        }
        $smarty->assign('displayViewGroup', 1);
        $obrigatorytime = $_SESSION['hdk']['SES_IND_ENTER_TIME'];
        $emptynote = $_SESSION['hdk']['SES_EMPTY_NOTE'];
        if(!$emptynote) $emptynote = 0;

        $arrOpeGrp = $this->_comboOperatorGroups($idperson);
        $smarty->assign('grpids', $arrOpeGrp['ids']);
        $smarty->assign('grpvals', $arrOpeGrp['values']);

        $now = $this->formatDate(date("Ymd"));

        $smarty->assign('email', $email);
        $smarty->assign('now', $now);
        $smarty->assign('idperson', $idperson);
        $smarty->assign('emptynote', $emptynote);
        $smarty->assign('request_code', $this->_editRequest($coderequest));
        $smarty->assign('hidden_coderequest', $coderequest);
        $smarty->assign('hidden_idperson', $idperson);
        $smarty->assign('owner', $owner);
        $smarty->assign('author', $namecreator);
        $smarty->assign('department', $department);
        $smarty->assign('idstatus', $idstatus);
        $smarty->assign('status', $status);
        $smarty->assign('prorrogation', '');
        $smarty->assign('source', $source);
        $smarty->assign('entry', $entrydate);
        $smarty->assign('company', $company);
        $smarty->assign('idarea', $idarea);
        $smarty->assign('idtype', $idtype);
        $smarty->assign('iditem', $iditem);
        $smarty->assign('idservice', $idservice);
        $smarty->assign('idway', $idway);
        $smarty->assign('idreason', $idreason);
        $smarty->assign('idpriority', $idpriority);
        $smarty->assign('expire_date', $expiredate);
        $smarty->assign('mod_expire_date', $this->formatDate($rsTicket->fields['expire_date']));
        $smarty->assign('mod_expire_hour', $this->formatHour($rsTicket->fields['expire_date']));
        $smarty->assign('checkedassume', $checkedassume);
        $smarty->assign('obrigatorytime', $obrigatorytime);
        $smarty->assign('incharge', $incharge);
        $smarty->assign('inchargename', $inchargename);
        $smarty->assign('os', $os);
        $smarty->assign('serial_num', $serial);
        $smarty->assign('subject', $subject);
        $smarty->assign('description', $description);
        $smarty->assign('typeincharge', $rsTicket->fields['typeincharge']);

        $smarty->assign('hidden_coderequest',$coderequest);

        $smarty->assign('noteattmaxfiles', $this->_getNoteAttMaxFiles());
        $smarty->assign('noteacceptedfiles', $this->_getNoteAcceptedFiles());
        $smarty->assign('ticketattmaxfiles', $this->_getTicketAttMaxFiles());
        $smarty->assign('summernote_version', $this->summernote);

        $arrAuxOpe = $this->_comboAuxOperators($coderequest,'in');
        foreach ( $arrAuxOpe['ids'] as $indexKey => $indexValue ) {
            $aux[] = $arrAuxOpe['values'][$indexKey];
        }

        $flgauxopelist = (count($aux) > 0) ? '' : 'hide';
        $smarty->assign('usersaux', $aux);
        $smarty->assign('flgauxopelist', $flgauxopelist);
        $smarty->assign('hdkMaxSize', substr($this->_getTicketAttMaxFileSize(),0,-1) );

        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');

        $smarty->assign('hidden_login',$_SESSION['SES_LOGIN_PERSON']) ; // Demo Version
        $smarty->assign('demoversion', $this->demoVersion);             // Demo version

        if($typeperson == 3){
            $smarty->assign('flgoperator',1);
        }else{
            $smarty->assign('flgoperator',0);
        }

        /*if($typeperson == 3){
            $myGroupsIdPerson = $this->dbTicket->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
            while (!$myGroupsIdPerson->EOF) {
                $myGroupsIdPersonArr[] = $myGroupsIdPerson->fields['idperson'];
                $myGroupsIdPerson->MoveNext();
            }
        }*/

        if($typeperson == 3 && $flgOpeAsUser != 1){
            $smarty->display('viewticket_operator.tpl');
        }else{$smarty->display('viewticket_user.tpl');}

    }

    public function downloadFile(){

        $filename = $this->getParam('id');
        $type = $this->getParam('type');
        $file = $this->dbTicket->getTicketFile($filename,$type);

        $name = $file;
        $ext = strrchr($name, '.');

        switch ($type) {
            case 'note':
                if($this->_externalStorage) {
                    $file_name = $this->_externalStoragePath . '/helpdezk/noteattachments/' . $filename . $ext;
                } else {
                    $file_name = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/' . $filename . $ext ;
                }
                $pathDownload =  "/app/uploads/helpdezk/noteattachments/";
                break;
            case 'request':
                if($this->_externalStorage) {
                    $file_name = $this->_externalStoragePath . '/helpdezk/attachments/' . $filename . $ext ;
                } else {
                    $file_name = $this->helpdezkPath . '/app/uploads/helpdezk/attachments/' . $filename . $ext ;
                }
                break;
        }



        //$file_name = $this->helpdezkPath . $pathDownload . $filename . $ext;

        // required for IE
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // get the file mime type using the file extension
        switch(strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'pdf': $mime = 'application/pdf'; break;
            case 'zip': $mime = 'application/zip'; break;
            case 'jpeg':
            case 'jpg': $mime = 'image/jpg'; break;
            default: $mime = 'application/force-download';
        }
        header('Pragma: public');   // required
        header('Expires: 0');       // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: '.$mime);
        header('Content-Disposition: attachment; filename="'.basename($name).'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($file_name));  // provide file size
        header('Connection: close');
        readfile($file_name);    // push it out
        exit();
    }

    public function evaluateTicket()
    {

        $this->protectFormInput();

        $person     = $_SESSION['SES_COD_USUARIO'];
        $ipadress   = $_SERVER['REMOTE_ADDR'];
        $code       = $_POST['coderequest'];

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $this->loadModel('evaluation_model');
        $dbEvaluation = new evaluation_model();

        //Get the Evaluate Token
        $rsTokenTmp = $dbEvaluation->getToken($code);
        if (!$rsTokenTmp) {
            if($this->log)
                $this->logIt("Get Evaluate token request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $tokenTmp = $rsTokenTmp->fields['token'];

        //Delete the Evaluate Token
        $rmToken = $dbEvaluation->removeTokenByCode($code);
        if(!$rmToken){
            if($this->log)
                $this->logIt("Delete Evaluate token request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);

            return false;
        }

        $this->dbTicket->BeginTrans();

        switch ($_POST['approve']) {

            case 'A':
                $status = '5';
                $reopened = '0';
                $retLog = $this->dbTicket->changeRequestStatus($status,$code,$person);
                if (!$retLog) {
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }
                $callback = '0';
                $idtype   = '3';
                $public   = '1';
                $note     = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
                $retInsNote = $this->dbTicket->insertNote($code, $person, $note, $this->databaseNow, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
                if (!$retInsNote) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $retChangeStat = $this->dbTicket->updateReqStatus($status, $code);
                if (!$retChangeStat) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $retClearEval = $this->dbTicket->clearEvaluation($code);
                if (!$retClearEval) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $rsQuestions = $this->dbTicket->getQuestions();
                $numQuestions = $rsQuestions->RecordCount();

                for($i = 1; $i <= $numQuestions-1; $i++){
                    $idAnswer = $_POST['question-'.$i];
                    $retInsEval = $this->dbTicket->insertEvaluation($idAnswer, $code, $this->databaseNow);

                    if (!$retInsEval) {
                        if($this->log)
                            $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        $this->dbTicket->RollbackTrans();

                        $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                        if(!$iToken){
                            if($this->log)
                                $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        }

                        return false;
                    }
                }

                $retUpdateDate = $this->dbTicket->updateDate($code, "approval_date");
                if(!$retUpdateDate){
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                break;

            case 'N':
                $status   = '3';
                $reopened = '1';
                $retLog = $this->dbTicket->changeRequestStatus($status,$code,$person);
                if (!$retLog) {
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $callback = '0';
                $idtype = '3';
                $public = '1';
                $note = "<p><b><span style=\"color: #FF0000;\">" . $langVars['Request_not_approve'] . "</span></b></p>";
                $note .= "<p><strong>" . $langVars['Reason'] . ":</strong> " . nl2br($_POST['observation']) . "</p>";
                $retInsNote = $this->dbTicket->insertNote($code, $person, $note, $this->databaseNow, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
                if (!$retInsNote) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $retChangeStat = $this->dbTicket->updateReqStatus($status, $code);
                if (!$retChangeStat) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                break;

            case 'O':
                $status = '5';
                $reopened = '0';
                $retLog = $this->dbTicket->changeRequestStatus($status,$code,$person);
                if (!$retLog) {
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $callback = '0';
                $idtype = '3';
                $public = '1';
                $note = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
                $note .= "<p><strong>" . $langVars['Observation'] . ":</strong> " . nl2br($_POST['observation']) . "</p>";
                $retInsNote = $this->dbTicket->insertNote($code, $person, $note, $this->databaseNow, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
                if (!$retInsNote) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $retChangeStat = $this->dbTicket->updateReqStatus($status, $code);
                if (!$retChangeStat) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $retClearEval = $this->dbTicket->clearEvaluation($code);
                if (!$retClearEval) {
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                $rsQuestions = $this->dbTicket->getQuestions();
                $numQuestions = $rsQuestions->RecordCount();

                for($i = 1; $i <= $numQuestions-1; $i++){
                    $idAnswer = $_POST['question-'.$i];
                    $retInsEval = $this->dbTicket->insertEvaluation($idAnswer, $code, $this->databaseNow);

                    if (!$retInsEval) {
                        if($this->log)
                            $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        $this->dbTicket->RollbackTrans();

                        $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                        if(!$iToken){
                            if($this->log)
                                $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        }

                        return false;
                    }
                }

                $retUpdateDate = $this->dbTicket->updateDate($code, "approval_date");
                if(!$retUpdateDate){
                    if($this->log)
                        $this->logIt("Evaluate request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();

                    $iToken = $this->dbEvaluation->insertTokenOnEvaluateError($code,$tokenTmp);
                    if(!$iToken){
                        if($this->log)
                            $this->logIt("Insert Eval Token on error -  ticket # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }

                    return false;
                }

                if($this->log)
                    $this->logIt("Evaluate request # ". $code . ' - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');

                break;

        }



        if($this->log)
            $this->logIt("Evaluate request # ". $code . ' - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');

        $this->dbTicket->CommitTrans();

        $arrayParam = array( 'transaction' => 'evaluate-ticket',
                             'code_request' => $code,
                             'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        die ("OK");

    }

    public function cancelTicket()
    {

        $this->protectFormInput();

        $person = $_SESSION['SES_COD_USUARIO'];
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $code = $_POST['coderequest'];

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $this->dbTicket->BeginTrans();

        $status = '11';
        $retLog = $this->dbTicket->changeRequestStatus($status,$code,$person);
        if (!$retLog) {
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $retCancel = $this->dbTicket->cancelRequest($code,$status);
        if (!$retCancel) {
            if($this->log)
                $this->logIt("Cancel request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $callback = '0';
        $idtype   = '3';
        $public   = '1';
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $note     = '<p><b>' . $langVars['Request_canceled'] . '</b></p>';
        $retInsNote = $this->dbTicket->insertNote($code, $person, $note, $this->databaseNow, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
        if (!$retInsNote) {
            if($this->log)
                $this->logIt("Cancel request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }
        if($this->log)
            $this->logIt("Cancel request # ". $code . ' - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');

        $this->dbTicket->CommitTrans();

        die ("OK");
    }

    public function reopenTicket()
    {

        $this->protectFormInput();

        $person     = $_SESSION['SES_COD_USUARIO'];
        $ipadress   = $_SERVER['REMOTE_ADDR'];
        $code       = $_POST['coderequest'];

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $this->dbTicket->BeginTrans();

        $status = '1';
        $reopened = '1';
        $retLoog = $this->dbTicket->changeRequestStatus($status, $code, $person);
        if (!$retLoog) {
            if($this->log)
                $this->logIt("Reopen request request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $callback = '0';
        $idtype = '3';
        $public = '1';
        $note = "<p><b><span style=\"color: #FF0000;\">" . $langVars['Request_reopened'] . "</span></b></p>";
        $retInsNote = $this->dbTicket->insertNote($code, $person, $note, $this->databaseNow, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
        if (!$retInsNote) {
            if($this->log)
                $this->logIt("Reopen request request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $retChangeStat = $this->dbTicket->updateReqStatus($status, $code);
        if (!$retChangeStat) {
            if($this->log)
                $this->logIt("Reopen request request # ". $code . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        if($this->log)
            $this->logIt("Reopen request # ". $code . ' - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');

        $this->dbTicket->CommitTrans();

        $arrayParam = array('transaction' => 'reopen-ticket',
                            'code_request' => $code,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        die ("OK");

    }

    public function deleteNote()
    {

        $this->protectFormInput();

        $idNote = $_POST['idnote'];

        // Get Attachments
        $rsNoteAttach = $this->dbTicket->getNoteAttachments($idNote);

        $this->dbTicket->beginTrans();

        $rsDelNote = $this->dbTicket->deleteNote($idNote);

        if(!$rsDelNote){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Delete note # ". $idNote . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        if ($rsNoteAttach->RecordCount() == 0) {
            if($this->log)
                $this->logIt("Delete note # ". $idNote . ', without attachment - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');
            $this->dbTicket->commitTrans();
            echo 'OK';
            return;
        }

        while(!$rsNoteAttach->EOF) {
            $idAttach = $rsNoteAttach->fields['idnote_attachments'];
            $exp = explode(".",$rsNoteAttach->fields['filename']);
            $ext = $exp[count($exp)-1];

            if($this->_externalStorage) {
                $fileAttach = $this->_externalStoragePath . "/helpdezk/noteattachments/$idAttach.$ext" ;
            } else {
                $fileAttach = $this->helpdezkPath . "/app/uploads/helpdezk/noteattachments/$idAttach.$ext";
            }

            //$fileAttach = $this->helpdezkPath . "/app/uploads/helpdezk/noteattachments/$idAttach.$ext";

            if(!unlink($fileAttach)){
                if($this->log)
                    $this->logIt("Delete note # ". $idNote . ', do not remove attachment '.$fileAttach.' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }
            $rsNoteAttach->MoveNext();
        }

        $this->dbTicket->CommitTrans();
        if($this->log)
            $this->logIt("Delete note # ". $idNote . ',  with '.$rsNoteAttach->RecordCount().' attachment(s) - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');
        echo 'OK';
    }

    public function makeReport()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $code_request = $_POST['code_request'];

        $where = 'WHERE code_request = '.$code_request;
        $rsTicket = $this->dbTicket->getRequestData($where);

        $idperson = $_SESSION['SES_COD_USUARIO'];

        $idowner  = $rsTicket->fields['idperson_owner'];

        if($typeperson == 2)
            if($idperson != $idowner) die ($langVars['Access_denied']);

        // class FPDF with extention to parsehtml
        $pdf = $this->returnHtml2pdf();

        /*
         *  Variables
         */
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() );
        $leftMargin   = 10;
        $this->SetPdfTitle(html_entity_decode($langVars['Request'],ENT_QUOTES, "ISO8859-1"));
        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ;
        $this->SetPdfleftMargin($leftMargin);

        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);


        $pdf->AliasNbPages();
        //$this->SetPdfHeaderData($a_HeaderData);

        $pdf->AddPage();

        $pdf = $this->ReportPdfHeader($pdf);

        //$pdf = $this->ReportPdfCabec($pdf) ;

        $CelHeight = 4;

        $pdf->Cell($leftMargin);

        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $title = array(array('title'=>html_entity_decode($langVars['Request'],ENT_QUOTES, "ISO8859-1"),'cellWidth'=>179,'cellHeight'=>$CelHeight,'titleAlign'=>'C'));
        $this->makePdfLineBlur($pdf,$title);
        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        $pdf->SetFont('Arial','',8);

        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Number'],ENT_QUOTES, "ISO8859-1") . ":",0,0,'R',0);
        $pdf->Cell(33,$CelHeight,substr($rsTicket->fields['code_request'],0,4) . "/" . substr($rsTicket->fields['code_request'],4,2) . "-" . substr($rsTicket->fields['code_request'],6),0,0,'L',0);
        $pdf->Cell(40);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Opened_by'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket->fields['personname']),0,1,'L',0);
        //--
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Request_owner'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(25,$CelHeight,utf8_decode($rsTicket->fields['personname']),0,0,'L',0);
        $pdf->Cell(48);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Source'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket->fields['source']),0,1,'L',0);

        if($_SESSION['SES_REQUEST_SHOW_PHONE'] == 1) {
            $pdf->Cell($leftMargin);
            $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Company'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
            $pdf->Cell(25,$CelHeight,utf8_decode($rsTicket->fields['company']),0,0,'L',0);
            $pdf->Cell(48);
            $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Phone'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
            $pdf->Cell(60,$CelHeight,preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $rsTicket->fields['phone_number']),0,1,'L',0);
            $pdf->Cell($leftMargin);
            $pdf->Cell(95);
            $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Branch'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
            $pdf->Cell(60,$CelHeight,$rsTicket->fields['branch_number'],0,1,'L',0);
            $pdf->Cell($leftMargin);
            $pdf->Cell(95);
            $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Mobile_phone'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
            $pdf->Cell(60,$CelHeight,preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $rsTicket->fields['cel_phone']),0,1,'L',0);
        }else{
            $pdf->Cell($leftMargin);
            $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Company'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
            $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['company']),0,1,'L',0);
            $pdf->Cell(48);
        }

        $this->makePdfLine($pdf,$leftMargin,197);

        // -- Department and Status --
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Department'],ENT_QUOTES, "ISO8859-1") .':',0,'L');
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['department']),0,0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['status'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket->fields['status']),0,1,'L',0);
        // -- Area and Opening Date
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Area'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['area']),0,0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Opening_date'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
        $pdf->Cell(60,$CelHeight,$this->formatDateHour($rsTicket->fields['entry_date']),0,1,'L',0);
        // -- Type and Priority
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['type'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['type']),0,0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Priority'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket->fields['priority']),0,1,'L',0);
        // -- Item and Attendance Way
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Item'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['item']),0,0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Att_way'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket->fields['way_name']),0,1,'L',0);
        // -- Service and In Charge
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Service'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['service']),0,0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Var_incharge'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket->fields['in_charge']),0,1,'L',0);
        // -- Reason and Expire date
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Reason'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket->fields['reason']),0,0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['Expire_date'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
        $pdf->Cell(60,$CelHeight,$this->formatDateHour($rsTicket->fields['expire_date']),0,1,'L',0);

        $this->makePdfLine($pdf,$leftMargin,197);

        // -- Subject and description
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Subject'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(0,$CelHeight,utf8_decode($rsTicket->fields['subject']),0,1,'L',0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Description'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->SetleftMargin($leftMargin + 30);
        $description = ltrim(html_entity_decode(utf8_decode($rsTicket->fields['description']),ENT_QUOTES, "ISO8859-1"));
        $pdf->Cell(0,$CelHeight,$pdf->WriteHTML($description),0,1,'L',0);

        $pdf->SetLeftMargin($leftMargin);
        $this->makePdfLine($pdf,$leftMargin,197);


        $pdf->Ln(1);
        $pdf->SetLeftMargin($leftMargin);


        // Notes
        $rsNotes = $this->dbTicket->getRequestNotes($code_request);
        if ($rsNotes->RecordCount() != 0) {
            $pdf->Ln(6);
            $pdf->Cell($leftMargin);
            $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
            $titleNotes = array(array('title'=>html_entity_decode($langVars['Added_notes'],ENT_QUOTES, "ISO8859-1"),'cellWidth'=>179,'cellHeight'=>$CelHeight,'titleAlign'=>'C'));
            $this->makePdfLineBlur($pdf,$titleNotes);
            $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        }

        while(!$rsNotes->EOF){
            $pdf->Ln(2);
            $pdf->Cell($leftMargin);
            $pdf->Cell(30,$CelHeight,$this->formatDateHour($rsNotes->fields['entry_date']) . " [ " . utf8_decode($rsNotes->fields['name']) . " ] " ,0,1,'L');
            $pdf->SetLeftMargin($leftMargin + 15);
            $description = ltrim(html_entity_decode(utf8_decode($rsNotes->fields['description']),ENT_QUOTES, "ISO8859-1"));
            $description = preg_replace("/<br\W*?\/>/", "<br><br>", $description);
            $pdf->Cell(0,$CelHeight,$pdf->WriteHTML($description),0,1,'C');
            $pdf->Ln(1);
            $pdf->SetLeftMargin($leftMargin);
            $this->makePdfLine($pdf,$leftMargin,197);

            $rsNotes->MoveNext();
        }

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/tmp/'. $filename ;
        $fileNameUrl   = $this->helpdezkUrl . '/app/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/tmp/')) {

            if( !chmod($this->helpdezkPath . '/app/tmp/', 0777) )
                $this->logIt("Make report request # ". $rsTicket->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }


        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;



    }

    public function saveNote()
    {
        $this->protectFormInput();

        $codeRequest     = $_POST['code_request'];
        $noteContent     =  addslashes($_POST['noteContent']);

        if($_POST['flagNote'] == 2){
            $serviceVal = 'NULL';
            $public     = 1;
            $typeNote   = 1;
            $callback   = 0;
            $execDate   = '0000-00-00 00:00:00';

            $totalminutes   = 0 ;
            $starthour      = 0;
            $finishour      = 0;
            $hourtype       = 0 ;
        }else{
            $serviceVal = 'NULL';
            $public     = 1;
            $typeNote   = $_POST['typeNote'];
            $callback   = $_POST['callback'];
            $execDate   = str_replace("'", "", $this->formatSaveDate($_POST['execDate']));

            $totalminutes   = $_POST['totalminutes'] ;
            $starthour      = $_POST['starthour'];
            $finishour      = $_POST['finishour'];
            $hourtype       = $_POST['hourtype'] ;
        }

        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files

        $aParam = array();
        $aParam['code_request'] = $codeRequest;
        $aParam['notecontent'] = $noteContent;
        $aParam['serviceval'] = $serviceVal;
        $aParam['public'] = $public;
        $aParam['typenote'] = $typeNote;
        $aParam['callback'] = $callback;
        $aParam['execdate'] = $execDate;

        $aParam['totalminutes'] = $totalminutes;
        $aParam['starthour'] = $starthour;
        $aParam['finishhour'] = $finishour;

        $aParam['hourtype'] = $hourtype;
        $aParam['flgopen'] = 1;

        $idNoteInsert = $this->_saveNote($aParam);
        if(!$idNoteInsert){
            if($this->log)
                $this->logIt("Add note in request # ". $codeRequest . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        } else {
            if($this->log)
                $this->logIt("Add note in request # ". $codeRequest . ' - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');
            
            // link attachments to the request
            if($aSize > 0){
                $retAttachs = $this->linkNoteAttachments($idNoteInsert,$aAttachs);
                if(!$retAttachs['success']){
                    if($this->log)
                        $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                    return false;
                }
            }

            if ($_SESSION['hdk']['SEND_EMAILS'] == '1') {

                if($_POST['flagNote'] == 3){ // Note created by operator
                    $transaction = 'user-note' ;
                } elseif ($_POST['flagNote'] == 2) { // Note created by user
                    $transaction = 'operator-note';
                } else {
                    echo $idNoteInsert;
                    exit;
                }

                $arrayParam = array('transaction' => $transaction,
                    'code_request' => $codeRequest,
                    'media' => 'email') ;
            }

            $ret = $this->_sendNotification($arrayParam);

            echo $idNoteInsert;
        }

    }

    public function saveNoteAttach()
    {

        $this->protectFormInput();

        $idNote = $_POST['idNote'];

        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {
            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/helpdezk/noteattachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/';
            }

            if(!is_dir($targetPath)) {
                $this->logIt('Directory: '. $targetPath.' does not exists, I will try to create it. - program: '.$this->program ,7,'general',__LINE__);
                if (!mkdir ($targetPath, 0777 )) {
                    $this->logIt('I could not create the directory: '.$targetPath.' - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_create')}: {$targetPath}"));
                    exit;
                }
            }

            if (!is_writable($targetPath)) {
                $this->logIt('Directory: '. $targetPath.' Is not writable, I will try to make it writable - program: '.$this->program ,7,'general',__LINE__);
                if (!chmod($targetPath,0777)){
                    $this->logIt('Directory: '.$targetPath.' Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_writable')}: {$targetPath}"));
                    exit;
                }
            }

            //$idNoteAttachments = $this->dbTicket->saveNoteAttachment($idNote,$fileName);

            $targetFile =  $targetPath.$fileName;

            if (move_uploaded_file($tempFile,$targetFile)){
                $this->logIt('Add attachment #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);
                echo json_encode(array("success"=>true,"message"=>""));
            } else {
                $this->logIt('Error attachment #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
        }
        exit;

    }

    public function saveTicketAttachments()
    {
        $this->protectFormInput();

        $code_request = $_POST['coderequest'];

        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {
            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/helpdezk/attachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/helpdezk/attachments/';
            }

            if(!is_dir($targetPath)) {
                $this->logIt('Directory: '. $targetPath.' does not exists, I will try to create it. - program: '.$this->program ,7,'general',__LINE__);
                if (!mkdir ($targetPath, 0777 )) {
                    $this->logIt('I could not create the directory: '.$targetPath.' - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_create')}: {$targetPath}"));
                    exit;
                }
            }

            if (!is_writable($targetPath)) {
                $this->logIt('Directory: '. $targetPath.' Is not writable, I will try to make it writable - program: '.$this->program ,7,'general',__LINE__);
                if (!chmod($targetPath,0777)){
                    $this->logIt('Directory: '.$targetPath.' Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_writable')}: {$targetPath}"));
                    exit;
                }
            }

            $targetFile =  $targetPath.$fileName;
            $this->logIt("Save attachment in request # ". $code_request . ' - File: '.$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
            if (move_uploaded_file($tempFile,$targetFile)){
                //return 'OK';
                echo json_encode(array("success"=>true,"message"=>""));
            } else {
                //return false;
                echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
        }
        exit;
    }

    public function sendNotification()
    {

        $this->protectFormInput();

        if(!empty($_POST['transaction']))
            $transaction = $_POST['transaction'];
        if(!empty($_POST['code_request']))
            $code_request = $_POST['code_request'];
        if(!empty($_POST['has_attachment']))
            $hasAttachment = $_POST['has_attachment'];

        $arrayParam = array('transaction' => $transaction,
                            'code_request' => $code_request,
                            'hasAttachment' => $hasAttachment,
                            'media' => 'email') ;
        $ret = $this->_sendNotification($arrayParam);
        if($ret)
            echo 'OK';
        else
            echo 'ERROR';
    }

    function makeNotesScreen($code_request)
    {

        // Ticket data
        $where = 'WHERE code_request = '.$code_request;
        $rsTicket = $this->dbTicket->getRequestData($where);
        $idstatus = $rsTicket->fields['idstatus'];
        // Notes
        $typeperson = $_SESSION['SES_TYPE_PERSON'];
        $rsNotes = $this->dbTicket->getRequestNotes($code_request);
        $lineNotes = '';

        while(!$rsNotes->EOF){
            $idNote = $rsNotes->fields['idnote'];

            if($idstatus == 3){
                if ($rsNotes->fields['idtype'] != '3' && $_SESSION['hdk']['SES_IND_DELETE_NOTE'] == '1' && $_SESSION['SES_COD_USUARIO'] == $rsNotes->fields['idperson'] && $rsNotes->fields['flag_opened'] != '0'){
                    $iconDel = '<button type="button" class="btn btn-danger btn-xs" href="<a href="javascript:;" onclick="deleteNote('.$idNote.','.$code_request.','.$typeperson.');"><span class="fa fa-trash-alt"></span></button>';
                } else {
                    $iconDel = "";
                }
            } else {
                $iconDel = "";
            }

            if ($rsNotes->fields['callback']) {
                // CALLBACK
                $iconNote = ' <i class="fa fa-cogs "></i>';
            } elseif ($rsNotes->fields['idtype'] == '1' && $rsNotes->fields['idperson'] == $rsTicket->fields['idperson_owner']) {
                // User
                $iconNote = ' <i class="fa fa-user "></i>';
            } elseif($rsNotes->fields['idtype'] == '1'){
                // Operator
                $iconNote = ' <i class="fa fa-users "></i>';
            } else {
                $iconNote = ' <i class="fa fa-cogs "></i>';
            }

            $rsNoteAttach = $this->dbTicket->getNoteAttachments($idNote);

            if ($rsNoteAttach->RecordCount() > 0) {
                $iconFile = '';
                while(!$rsNoteAttach->EOF){
                    $idNoteAttach = $rsNoteAttach->fields['idnote_attachments'] ;
                    $tooltip = $this->dbTicket->getTicketFile($idNoteAttach,'note');
                    $iconFile .= '<button type="button" class="btn btn-default btn-xs" id="'.$idNoteAttach.'" onclick="download('.$idNoteAttach.',\'note\')" data-toggle="tooltip" data-container="body" data-placement="right" data-original-title="'.$tooltip.'"><span class="fa fa-file-alt"></span></button>&nbsp;';
                    $rsNoteAttach->MoveNext();
                }
            } else {
                $iconFile  = "&nbsp";
            }

            //$note  = $this->formatDateHour($rsNotes->fields['entry_date']) . " [" . $this->getPersonName($rsNotes->fields['idperson']) . "] <br>";
            //$note .=  $rsNotes->fields['description'] ;
            $noteTitle  = $this->formatDateHour($rsNotes->fields['entry_date']) . " [" . $this->getPersonName($rsNotes->fields['idperson']) . "] <br>";
            $note =  $rsNotes->fields['description'] ;

            if($typeperson == 3){
                $lineNotes .=   '
                    <div id="ticket_notes" class="row wrapper  white-bg ">
                        <div class="timeline-item">
                            <div class="row">
                                <div class="col-xs-3 date">
                                    '.$iconNote.'

                                    <br/>
                                </div>
                                <div class="col-xs-9 content">
                                    <p class="m-b-xs"><h3>'.$noteTitle.'</h3></p>
                                    <p></p>
                                    <p>
                                     '.$iconDel.'
                                     '.$note.'
                                    </p>
                                    <p>
                                    '.$iconFile.'
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                                ';
            }else{
                if($rsNotes->fields['idtype'] != '2'){
                    $lineNotes .=   '
                    <div id="ticket_notes" class="row wrapper  white-bg ">
                        <div class="timeline-item">
                            <div class="row">
                                <div class="col-xs-3 date">
                                    '.$iconNote.'

                                    <br/>
                                </div>
                                <div class="col-xs-9 content">
                                    <p class="m-b-xs"><h3>'.$noteTitle.'</h3></p>
                                    <p></p>
                                    <p>
                                     '.$iconDel.'
                                     '.$note.'
                                    </p>
                                    <p>
                                    '.$iconFile.'
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                                ';
                }
            }

            $rsNotes->MoveNext();
        }

        return $lineNotes;
    }

    public function ajaxNotes()
    {
        $this->protectFormInput();

        $code_request = $_POST['code_request'];
        $lineNotes = $this->makeNotesScreen($code_request);
        echo $lineNotes;
    }

    public function showDefaults()
    {
        if (isset($_SESSION['hdk']['TKT_DONT_SHOW_DEFAULT']) && $_SESSION['hdk']['TKT_DONT_SHOW_DEFAULT'] == 1) {
            echo 'NO';
        } else {
            echo 'YES';
        }
    }

    public function areaDefault()
    {
        // Since December 26, 2019
        if (isset($_SESSION['hdk']['TKT_DONT_SHOW_DEFAULT']) && $_SESSION['hdk']['TKT_DONT_SHOW_DEFAULT'] == 1) {
            echo 'NO';
        } else {
            $idArea = $this->_getIdAreaDefault();
            if ($idArea > 0)
                //echo $this->_comboTypeHtml($idArea);
                echo 'YES';
            else
                echo 'NO';
        }

    }

    public function ajaxTypeWithAreaDefault()
    {
        $idArea = $this->_getIdAreaDefault();
        echo $this->_comboTypeHtml($idArea);
    }

    public function ajaxArea()
    {
        echo $this->_comboAreaHtml();
    }

    public function ajaxTypes()
    {
        echo $this->_comboTypeHtml($_POST['areaId']);
    }

    public function ajaxItens()
    {
        echo $this->_comboItemHtml($_POST['typeId']);
    }

    public function ajaxServices()
    {
        echo $this->_comboServiceHtml($_POST['itemId']);
    }

    public function ajaxReasons()
    {
        echo $this->_comboReasonHtml($_POST['serviceId']);
    }

    private function createRequestCode(){
        $this->dbTicket->BeginTrans();

        $rsCode = $this->dbTicket->getCode();
        if(!$rsCode){
            $this->dbTicket->RollbackTrans();
            return false;
        }
        // Count month code
        $rsCountCode = $this->dbTicket->countGetCode();
        if(!$rsCountCode){
            $this->dbTicket->RollbackTrans();
            return false;
        }
        // If have code request
        if ($rsCountCode->fields['total']) {
            $code_request = $rsCode->fields["cod_request"];
            // Will increase the code of request
            $rsIncressCode = $this->dbTicket->increaseCode($code_request);
            if(!$rsIncressCode){
                $this->dbTicket->RollbackTrans();
                return false;
            }
        }
        else {
            //If not have code request will create a new
            $code_request = 1;
            $rsCreateCode = $this->dbTicket->createCode($code_request);
            if(!$rsCreateCode){
                $this->dbTicket->RollbackTrans();
                return false;
            }
        }

        $code_request = str_pad($code_request, 6, '0', STR_PAD_LEFT);
        $code_request = date("Ym") . $code_request;
        $this->dbTicket->CommitTrans();
        return $code_request;
    }

    private function getExpireDate($startDate = null, $idPriority = null, $idService = null){

        if(!isset($startDate)){$startDate = date("Y-m-d H:i:s");}

        $this->loadModel('expiredate_model');
        $db = new expiredate_model();

        // If have service id
        if(isset($idService)){
            $idcompany = $db->getIdCustumerByService($idService);

            $rsExpireDateService = $db->getExpireDateService($idService);
            if(!$rsExpireDateService){
                return false;
            }

            $days = $rsExpireDateService->fields['days_attendance'];
            $time = $rsExpireDateService->fields['hours_attendance'];
            $type_time = $rsExpireDateService->fields['ind_hours_minutes'];

            if($days > 0){
                $days_sum = "+".$days." days";
            }
            if($time > 0){
                if($type_time == "H"){
                    $time_sum = "+".$time." hour";
                }
                elseif($type_time == "M"){
                    $time_sum = "+".$time." minutes";
                }
            }
        }

        // If have priority id and time and days are zero
        if(isset($idPriority) && $time == 0 && $days == 0){
            $rsExpireDatePriority = $db->getExpireDatePriority($idPriority);
            if(!$rsExpireDatePriority){
                $db->RollbackTrans();
                return false;
            }
            $days = $rsExpireDatePriority->fields['limit_days'];
            $time = $rsExpireDatePriority->fields['limit_hours'];

            if($days > 0){
                $days_sum = "+".$days." days";
            }
            if($time > 0){
                $time_sum = "+".$time." hour";
            }
        }

        if($time == 0 && $days == 0){
            $days_sum = "+0 day";
            $time_sum = "+0 hour";
            return $startDate;
        }

        $data_sum = date("Y-m-d H:i:s",strtotime($startDate." ".$days_sum." ".$time_sum));

        $date_holy_start = date("Y-m-d",strtotime($startDate)); // Separate only the inicial date to check for holidays in the period
        $date_holy_end = date("Y-m-d",strtotime($data_sum)); //Separate only the final date to check for holidays in the period

        $rsNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy_start,$date_holy_end); // Verifies the quantity of holidays in the period
        if(!$rsNationalDaysHoliday)
            return false;


        if(isset($idcompany)){
            $rsCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy_start,$date_holy_end,$idcompany); // Verifies the quantity of company�s holidays in the period
            if(!$rsCompanyDaysHoliday)
                return false;
            $sum_days_holidays = $rsNationalDaysHoliday->fields['num_holiday'] + $rsCompanyDaysHoliday->fields['num_holiday'];
        }else{
            $sum_days_holidays = $rsNationalDaysHoliday->fields['num_holiday'];
        }

        // Add holidays
        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$sum_days_holidays." days"));

        // Working days
        $rsBusinessDays = $db->getBusinessDays();
        if(!$rsBusinessDays)
            return false;

        while (!$rsBusinessDays->EOF) {
            $businessDay[$rsBusinessDays->fields['num_day_week']] = array(
                "begin_morning" 	=> $rsBusinessDays->fields['begin_morning'],
                "end_morning" 		=> $rsBusinessDays->fields['end_morning'],
                "begin_afternoon" 	=> $rsBusinessDays->fields['begin_afternoon'],
                "end_afternoon" 	=> $rsBusinessDays->fields['end_afternoon']
            );
            $rsBusinessDays->MoveNext();
        }

        $date_check_start = date("Y-m-d",strtotime($startDate));
        $date_check_end = date("Y-m-d",strtotime($data_sum));
        $addNotBussinesDay = 0;

        // Non-working days
        while (strtotime($date_check_start) <= strtotime($date_check_end)) {
            $numWeek = date('w',strtotime($date_check_start));
            if (!array_key_exists($numWeek, $businessDay)) {
                $addNotBussinesDay++;
            }
            $date_check_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_check_start)));
        }

        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$addNotBussinesDay." days")); // Add non-working days
        $data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
        $data_sum = $this->checkValidBusinessHour($data_check_bd,$businessDay); // Verify if the time is the interval of service

        // If you change the day, check to see if it is a working day
        if(strtotime(date("Y-m-d",strtotime($data_check_bd))) != strtotime(date("Y-m-d",strtotime($data_sum)))){
            $data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
            return $data_check_bd;
        }else{
            return $data_sum;
        }

    }

    private function checkValidBusinessDay($date,$businessDay,$idcompany = null){

        $this->loadModel('expiredate_model');
        $db = new expiredate_model();

        $numWeek = date('w',strtotime($date));

        $i = 0;
        while($i == 0){
            while (!array_key_exists($numWeek, $businessDay)) {
                $date = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));
                $numWeek = date('w',strtotime($date));
            }
            $date_holy = date("Y-m-d",strtotime($date));

            $rsNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy,$date_holy);
            if(!$rsNationalDaysHoliday)
                return false;

            if(isset($idcompany)){
                $rsCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy,$date_holy,$idcompany);
                if(!$rsCompanyDaysHoliday){
                    $db->RollbackTrans();
                    return false;
                }
                $daysHoly = $rsNationalDaysHoliday->fields['num_holiday'] + $rsCompanyDaysHoliday->fields['num_holiday'];
            }else{
                $daysHoly = $rsNationalDaysHoliday->fields['num_holiday'];
            }

            if($daysHoly > 0){
                $date = date("Y-m-d H:i:s",strtotime($date." +".$daysHoly." days"));
                $numWeek = date('w',strtotime($date));
            }else{
                $i = 1;
            }
        }
        return $date;
    }

    private function checkValidBusinessHour($date,$businessDay){
        $i = 0;
        while($i == 0){
            $numWeek = date('w',strtotime($date));
            $hour = strtotime(date('H:i:s',strtotime($date)));
            $begin_morning = strtotime($businessDay[$numWeek]['begin_morning']);
            $end_morning = strtotime($businessDay[$numWeek]['end_morning']);
            $begin_afternoon = strtotime($businessDay[$numWeek]['begin_afternoon']);
            $end_afternoon = strtotime($businessDay[$numWeek]['end_afternoon']);
            if($hour >= $begin_morning && $hour <= $end_morning){
                $i = 1;
            }
            else if($hour >= $begin_afternoon && $hour <= $end_afternoon){
                $i = 1;
            }
            else{
                $date = date ("Y-m-d H:i:s", strtotime("+1 hour", strtotime($date)));
                $i = 0;
            }
        }
        return $date;
    }

    function checkVipUser($idPerson)
    {
        $rsVipuser = $this->dbTicket->checksVipUser($idPerson);
        if ($rsVipuser->fields['rec_count'] > 0)
            return true;
        else
            return false;

    }

    function checkVipPriority()
    {
        $rsVipPriority = $this->dbTicket->checksVipPriority();
        if ($rsVipPriority->fields['rec_count'] > 0)
            return true;
        else
            return false;

    }

    function getVipPriority()
    {
        $rsVipPriority = $this->dbTicket->getVipPriority();
        return $rsVipPriority->fields['idpriority'];
    }

    function getServicePriority($idService)
    {
        $rsServicePrio = $this->dbTicket->getServPriority($idService);

        $idPriority = $rsServicePrio->fields['idpriority'];

        if(!$idPriority){
            $rsDefault = $this->dbTicket->getDefaultPriority();
            $idPriority = $rsDefault->fields['idpriority'];
        }

        return $idPriority;
    }

    public function ajaxRepassList()
    {
        $this->protectFormInput();

        echo $this->_comboRepassListHtml($_POST['typerep']);
    }

    public function ajaxAbilitiesList()
    {
        $this->protectFormInput();

        echo $this->_abilitiesListHtml($_POST['type'],$_POST['rep']);
    }

    public function ajaxgroupsList()
    {
        $this->protectFormInput();

        echo $this->_groupsListHtml($_POST['type'],$_POST['rep']);
    }

    public function openRepassedTicket()
    {
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);
        //die($_POST);
        $this->loadModel('ticketrules_model');
        $dbRules = new ticketrules_model();

        $repassto = $_POST['repassto'];
		$typerepass = $_POST['typerepass'];
        $viewrepass = $_POST['viewrepass'];
        
        if ($typerepass == 'operator') {
            $name = $this->dbPerson->selectPersonName($repassto);
            $typerepass = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        } 
        elseif ($typerepass == 'group') {
            $name = $this->dbGroup->selectRepGroupData($repassto);
            $name = $name->fields['name'];
            $typerepass = $langVars['to'] . " " . $langVars['Group'];
            $type2 = "G";
            
        }

        if (isset($repassto)) {
            //CREATE THE CODE REQUEST
            $code_request = $this->createRequestCode();

            $minTelephoneTime = number_format($_POST["open_time"], "2", ".", ",");
            $minAttendanceTime = (int) $_POST["open_time"];
            
            $idPerson  		= $_POST["idrequester"];
            $idPersonAuthor = $_SESSION["SES_COD_USUARIO"];
            $idWay 		    = $_POST["way"];
            $idSource	= $_POST["source"];
            $solution = str_replace("'", "`", $_POST["solution"]);

            //if telephone
            if ($idSource == 2){
                $minTelephoneTime = $minTelephoneTime;
                $minExpendedTime = $minAttendanceTime;
            }else{
                $minTelephoneTime = 0;
                $minExpendedTime = 0;
            }
           
            $idCompany 	= $_SESSION['SES_COD_EMPRESA'];

            // -- Equipment --------------------------
            $numberSerial	= $_POST["serial_number"];
            $numberOS 	= $_POST["os_number"];
            $numberTag 	= $_POST["tag"];

            $idType 	= $_POST["type"];
            $idService 	= $_POST["service"];
            $idItem		= $_POST["item"];
            if(empty($_POST['reason']))
                $idReason 		= "NULL";
            else
                $idReason = $_POST['reason'];
            
            $subject 	 = str_replace("'", "`", $_POST["subject"]);
            $description = str_replace("'", "`", $_POST["description"]);
            $aAttachs 	= $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files

            $idStatus 	= 2;

            if ( $this->checkVipUser($idPerson) == 1 &&  $this->checkVipPriority() == 1) {
                $idPriority = $this->getVipPriority();
            } else {
                $idPriority = $this->getServicePriority($idService);
            }

            $insertHour = !$_POST['time'] ? date("H:i") : $_POST['time'];
            if($this->database == 'oci8po') {
                $insertDate = !$_POST['date'] ? date("d/m/Y") : $_POST['date'];
                $startDate = $this->formatSaveDateHour($insertDate." ".$insertHour);
                $startDate = $this->oracleDate($startDate);
            }
            elseif($this->isMysql($this->database)){
                $insertDate = !$_POST['date'] ? date("Y-m-d") : str_replace("'", "", $this->formatSaveDate($_POST['date']));
                $startDate = $insertDate." ".$insertHour;
            }

            $expireDate = $this->getExpireDate($startDate, $idPriority, $idService);

            $this->dbTicket->BeginTrans();
            $rs = $this->dbTicket->insertRequest($idPersonAuthor,$idSource,$startDate,$idType,$idItem,$idService,$idReason,$idWay,$subject,$description,$numberOS,$idPriority,$numberTag,$numberSerial,$idCompany,$expireDate,$idPerson,$idStatus,$code_request);
            if(!$rs){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            switch($viewrepass){			
				case "P": //REPASSAR E SEGUIR ACOMPANHANDO
                    $track =$this->dbTicket->insertInCharge($code_request, $_SESSION['SES_COD_USUARIO'], "P", '0', "Y", '1');
                    if(!$track){
                        if($this->log)
                            $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        $this->dbTicket->RollbackTrans();
                        return false;
                    }
					break;
				case "N": //NAO ACOMPANHAR
					
					break;
            }
            
            $rs = $this->dbTicket->insertRequestCharge($code_request, $repassto, $type2, '1');

            if(!$rs){
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }

            $ret = $this->dbTicket->insertRequestTimesNew($code_request,0,0,$minExpendedTime,$minTelephoneTime,0);
            
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $ret = $this->dbTicket->insertRequestDate($code_request);
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $ret = $this->dbTicket->updateDate($code_request,"forwarded_date");
                if(!$ret){
                    $this->dbTicket->RollbackTrans();
                    if($this->log)
                        $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    return false;
                }

            // Inserts in the control table the status change made and which user did and the date of the event
            $ret = $this->dbTicket->insertRequestLog($code_request,date("Y-m-d H:i:s"),$idStatus,$idPerson);
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            // link attachments to the request
            if($aSize > 0){
                $retAttachs = $this->linkTicketAttachments($code_request,$aAttachs);
                if(!$retAttachs['success']){
                    $this->dbTicket->RollbackTrans();
                    if($this->log)
                        $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                    return false;
                }
            }

            $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
            $description = "<p><b>" . $langVars['Request_opened'] . "</b></p>";

            $serviceVal = 'NULL';
            $public     = 1;
            $typeNote   = 3;
            $callback   = 0;
            $execDate   = '0000-00-00 00:00:00';

            $totalminutes   = 0 ;
            $starthour      = 0;
            $finishour      = 0;
            $hourtype       = 0 ;

            $ipAddress = $_SERVER['REMOTE_ADDR'];

            $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            if($solution && $solution != '<p><br></p>'){
                //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
                $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
                $description = "<p><b>" . $langVars['Solution'] . "</b></p>". $solution;

                $con = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, 3, $ipAddress, $callback, 'NULL' );
                if(!$con){
                    $this->dbTicket->RollbackTrans();
                    if($this->log)
                        $this->logIt("Insert note[solution] ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    return false;
                }				
            }

            $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
            $description = "<p><b>" . $langVars['Request_repassed'] . strtolower($typerepass) . " " . $name . "</b></p>";

            $con = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, 3, $ipAddress, $callback, 'NULL' );
            if(!$con){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert note[repassed] ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }


            $this->dbTicket->CommitTrans();

            $aRet = array(
                        "coderequest" => $code_request,
                        "expire" => $this->formatDateHour($expireDate),
                        "incharge" => $this->_inchargeName($code_request)
            );

            echo json_encode($aRet);
        }

        

    }

    public function openFinishTicket()
    {
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);
        //die($_POST);
        $this->loadModel('ticketrules_model');
        $dbRules = new ticketrules_model();

        if (isset($_POST["idrequester"])) {
            //CREATE THE CODE REQUEST
            $code_request = $this->createRequestCode();

            $minTelephoneTime = number_format($_POST["open_time"], "2", ".", ",");
            $minAttendanceTime = (int) $_POST["open_time"];
            
            $idPerson  		= $_POST["idrequester"];
            $idPersonAuthor = $_SESSION["SES_COD_USUARIO"];
            $idWay 		    = $_POST["way"];
            $idSource	= $_POST["source"];
            $solution = str_replace("'", "`", $_POST["solution"]);

            //if telephone
            if ($idSource == 2){
                $minTelephoneTime = $minTelephoneTime;
                $minExpendedTime = $minAttendanceTime;
            }else{
                $minTelephoneTime = 0;
                $minExpendedTime = 0;
            }
           
            $idCompany 	= $_SESSION['SES_COD_EMPRESA'];

            // -- Equipment --------------------------
            $numberSerial	= $_POST["serial_number"];
            $numberOS 	= $_POST["os_number"];
            $numberTag 	= $_POST["tag"];

            $idType 	= $_POST["type"];
            $idService 	= $_POST["service"];
            $idItem		= $_POST["item"];
            if(empty($_POST['reason']))
                $idReason 		= "NULL";
            else
                $idReason = $_POST['reason'];
            
            $subject 	 = str_replace("'", "`", $_POST["subject"]);
            $description = str_replace("'", "`", $_POST["description"]);
            $aAttachs 	= $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files

            $idStatus 	= 5;

            if ( $this->checkVipUser($idPerson) == 1 &&  $this->checkVipPriority() == 1) {
                $idPriority = $this->getVipPriority();
            } else {
                $idPriority = $this->getServicePriority($idService);
            }

            $insertHour = !$_POST['time'] ? date("H:i") : $_POST['time'];
            if($this->database == 'oci8po') {
                $insertDate = !$_POST['date'] ? date("d/m/Y") : $_POST['date'];
                $startDate = $this->formatSaveDateHour($insertDate." ".$insertHour);
                $startDate = $this->oracleDate($startDate);
            }
            elseif($this->isMysql($this->database)){
                $insertDate = !$_POST['date'] ? date("Y-m-d") : str_replace("'", "", $this->formatSaveDate($_POST['date']));
                $startDate = $insertDate." ".$insertHour;
            }

            $expireDate = $this->getExpireDate($startDate, $idPriority, $idService);

            $this->dbTicket->BeginTrans();
            $rs = $this->dbTicket->insertRequest($idPersonAuthor,$idSource,$startDate,$idType,$idItem,$idService,$idReason,$idWay,$subject,$description,$numberOS,$idPriority,$numberTag,$numberSerial,$idCompany,$expireDate,$idPerson,$idStatus,$code_request);
            if(!$rs){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $idGroup = $this->dbTicket->getServiceGroup($idService);
            if (!$idGroup){
                $this->dbTicket->RollbackTrans();
                return false;
            }

            $rs = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');

            if(!$rs){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $ret = $this->dbTicket->insertRequestTimesNew($code_request,0,0,$minExpendedTime,$minTelephoneTime,0);
            
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $ret = $this->dbTicket->insertRequestDate($code_request);
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $ret = $this->dbTicket->updateDate($code_request,"finish_date");
                if(!$ret){
                    $this->dbTicket->RollbackTrans();
                    if($this->log)
                        $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    return false;
                }

            // Inserts in the control table the status change made and which user did and the date of the event
            $ret = $this->dbTicket->insertRequestLog($code_request,date("Y-m-d H:i:s"),$idStatus,$idPerson);
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            // link attachments to the request
            if($aSize > 0){
                $retAttachs = $this->linkTicketAttachments($code_request,$aAttachs);
                if(!$retAttachs['success']){
                    $this->dbTicket->RollbackTrans();
                    if($this->log)
                        $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                    return false;
                }
            }

            $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
            $description = "<p><b>" . $langVars['Request_opened'] . "</b></p>";

            $serviceVal = 'NULL';
            $public     = 1;
            $typeNote   = 3;
            $callback   = 0;
            $execDate   = '0000-00-00 00:00:00';

            $totalminutes   = 0 ;
            $starthour      = 0;
            $finishour      = 0;
            $hourtype       = 0 ;

            $ipAddress = $_SERVER['REMOTE_ADDR'];

            $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
            if(!$ret){
                $this->dbTicket->RollbackTrans();
                if($this->log)
                    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

            $person = $_SESSION['SES_COD_USUARIO'];
            $type = "P";
            $rep = 'N';
            $ind = '1';
			$this->dbTicket->removeIncharge($code_request);
            $insInCharge = $this->dbTicket->insertInCharge($code_request, $person, $type, $ind, $rep);
			$status = '5';
            $reopened = '0';
            $inslog = $this->dbTicket->changeRequestStatus($status, $code_request, $person);
			$ipadress = $_SERVER['REMOTE_ADDR'];
            $callback = '0';
            $idtype = '3';
            $public = '1';

            $note = '<p><b>' . $langVars['Request_closed'] . '</b></p><p><b>' . $langVars['Solution'] . ':</b></p>'. $solution;

            if($solution  && $solution != '<p><br></p>'){
                //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
                $description = "<p><b>" . $langVars['Request_closed'] . "</b></p><p><b>" . $langVars['Solution'] . "</b></p>". $solution;				
            }else{
                $description = "<p><b>" . $langVars['Request_closed'] . "</b></p>";
            }

            $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

            $con = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, 3, $ipAddress, $callback, 'NULL' );
            if(!$con){
                $this->dbTicket->RollbackTrans();
                return false;
            }

            $this->dbTicket->CommitTrans();

            $aRet = array(
                        "coderequest" => $code_request,
                        "expire" => $this->formatDateHour($expireDate),
                        "incharge" => $this->_inchargeName($code_request)
            );

            echo json_encode($aRet);
        }

        

    }

    function makeViewTicketButtons($smarty,$typeperson,$idticket,$idstatus)
    {
        if($typeperson == 2){
            if($idstatus == 2){
                $idswitch_status = 2;
            }else{
                $idswitch_status = $this->dbTicket->getIdStatusSource($idstatus);
            }

            switch($idswitch_status){
                case "1": //NEW
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'1');
                    $smarty->assign('displayevaluate',  '0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;

                case "2": //REPASSED
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displayevaluate',  '0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;

                case "3": //ON ATTENDANCE
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displayevaluate',  '0');
                    $smarty->assign('displaynote',		'1');
                    $smarty->assign('displayprint',     '1');
                    break;

                case "4": //WAITING FOR APP

                    $q = 0;
                    if ($_SESSION['hdk']['SES_EVALUATE'] == 1) {
                        $eval = "";
                        $rsQuestions = $this->dbTicket->getQuestions();
                        while (!$rsQuestions->EOF) {
                            $idquestion = $rsQuestions->fields['idquestion'];
                            $question = $rsQuestions->fields['question'];

                            $eval .= '<label class="col-sm-12 control-label">'.$question.'</label><br>';
                            $eval .= '<div class="col-sm-12">';
                            $rsAnswers = $this->dbTicket->getAnswers($idquestion);
                            $sel = 0;
                            $chk = 0;
                            while (!$rsAnswers->EOF) {
                                if($rsAnswers->fields['checked'] == 1){
                                    $checked = "checked='checked'";
                                    $chk = 1;}
                                else {
                                    if(count($rsAnswers->fields) == $sel+1 && $chk == 0){
                                        $checked = "checked='checked'";
                                    }else{
                                        $checked = "";
                                    }
                                }
                                $idanswer   = $rsAnswers->fields['idevaluation'];
                                $answer     = $rsAnswers->fields['name'];
                                if ($this->_externalStorage) {
                                    $ico        = $this->_externalUrl .'/app/uploads/icons/'. $rsAnswers->fields['icon_name'];
                                } else {
                                    $ico        = $this->helpdezkUrl.'/app/uploads/icons/'. $rsAnswers->fields['icon_name'];
                                }

                                $name = 'question-' . $idquestion ;
                                $eval       .=   "<div class='radio i-checks'><label> <input type='radio' name='$name' value='$idanswer' $checked required> <i></i>&nbsp;<img src='$ico' height='14' />&nbsp;$answer</label></div>";
                                $rsAnswers->MoveNext();
                            }
                            $q++;
                            $eval .= '</div>';
                            $rsQuestions->MoveNext();
                        }
                        $smarty->assign('questions', $eval);
                    }
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    if ($_SESSION['hdk']['SES_EVALUATE'] == 1 && $q != 0) {
                        $smarty->assign('displayevaluate',  '1');
                        $smarty->assign('displayprint',     '1');
                        //$smarty->assign('numQuest',  $q);
                    } else {
                        $smarty->assign('displayevaluate',  '0');
                        $smarty->assign('displayprint',     '1');
                        $smarty->assign('evaluationform', 	 '');
                    }
                    $smarty->assign('displaynote',		'0');
                    break;

                case "5": //FINISHED
                    if($_SESSION['hdk']['SES_IND_REOPEN_USER'] == 1)
                        $smarty->assign('displayreopen',	'1');
                    else
                        $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displayevaluate',  '0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;

                case "6": //REJECTED
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displayevaluate',  '0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;

                default:
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displayevaluate',  '0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;
            }
        }else{
            $idperson = $_SESSION['SES_COD_USUARIO'];

            $where = 'WHERE code_request = '.$idticket;
            $rsTicket = $this->dbTicket->getRequestData($where);
            $incharge       = $rsTicket->fields['id_in_charge'];

            $arrAuxOpe = $this->_comboAuxOperators($idticket,'in');
            foreach ( $arrAuxOpe['ids'] as $indexKey => $indexValue ) {
                $arrayAux[] = $indexValue;
            }

            $rules = $this->dbTicket->checkApprovalBt($idticket);
            $approving = $rules->RecordCount();

            if($approving){
                if($rules->fields['idperson'] == $_SESSION['SES_COD_USUARIO'] && $rules->fields['order'] == 1){
                    $idswitch_status = "app1";
                }elseif($rules->fields['idperson'] == $_SESSION['SES_COD_USUARIO'] && $rules->fields['order'] > 1){
                    $idswitch_status = "app2";
                }
                /*
                $lastapp = $dbrr->getLastApprovalBt($id);
                while (!$lastapp->EOF) {
                    $lastapplist[] = $lastapp->fields['idperson'];
                      $lastapp->MoveNext();
                }
                if(!in_array($idperson, $lastapplist) && sizeof($lastapplist) > 0) {
                    $idswitch_status = "app2";
                }			*/
            }elseif($idstatus == 2){
                $idswitch_status = 2;
            }else{
                $idswitch_status = $this->dbTicket->getIdStatusSource($idstatus);
            }

            switch($idswitch_status){
                case "app1":
                    $smarty->assign('displaychanges', '0');
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '1');
                    $smarty->assign('displayreturn',  '0');
                    $smarty->assign('displayreprove', '1');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "app2":
                    $smarty->assign('displaychanges', '0');
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '1');
                    $smarty->assign('displayreturn',  '1');
                    $smarty->assign('displayreprove', '1');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "1": //NEW
                    $smarty->assign('displaychanges', '1');
                    $smarty->assign('displayassume',  '1');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '1');
                    $smarty->assign('displayreject',  '1');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '0');
                    $smarty->assign('displayreturn',  '0');
                    $smarty->assign('displayreprove', '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "2": //REPASSED
                    $myGroupsIdPerson = $this->dbTicket->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
                    while (!$myGroupsIdPerson->EOF) {
                        $myGroupsIdPersonArr[] = $myGroupsIdPerson->fields['idperson'];
                        $myGroupsIdPerson->MoveNext();
                    }
                    if(in_array($incharge, $myGroupsIdPersonArr) || $incharge == $idperson){
                        //SOU RESPONSÁVEL POR ESTA SOL
                        $smarty->assign('displaychanges', '1');
                        $smarty->assign('displayassume',  '1');
                        $smarty->assign('displayopaux',   '0');
                        $smarty->assign('displayrepass',  '1');
                        $smarty->assign('displayreject',  '1');
                        $smarty->assign('displayclose',   '0');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displayaux', 	  '0');
                        $smarty->assign('displayapprove', '0');
                        $smarty->assign('displayreturn',  '0');
                        $smarty->assign('displayreprove', '0');
                        $smarty->assign('displaynote', 	  '0');
                        $smarty->assign('displayprint',   '1');
                    }
                    else{
                        //NÃO SOU RESPONSÁVEL POR ESTA SOL
                        $smarty->assign('displaychanges', '0');
                        if ($_SESSION['hdk']['SES_IND_ASSUME_OTHER'] == 1) {
                            $smarty->assign('displayassume',  '1');
                        }else{
                            $smarty->assign('displayassume',  '0');
                        }
                        $smarty->assign('displayopaux',   '0');
                        $smarty->assign('displayrepass',  '0');
                        $smarty->assign('displayreject',  '0');
                        $smarty->assign('displayclose',   '0');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displayaux', 	  '0');
                        $smarty->assign('displayapprove', '0');
                        $smarty->assign('displayreturn',  '0');
                        $smarty->assign('displayreprove', '0');
                        $smarty->assign('displaynote', 	  '0');
                        $smarty->assign('displayprint',   '1');
                    }
                    break;
                case "3"://ON ATTENDANCE
                    $myGroupsIdPerson = $this->dbTicket->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
                    while (!$myGroupsIdPerson->EOF) {
                        $myGroupsIdPersonArr[] = $myGroupsIdPerson->fields['idperson'];
                        $myGroupsIdPerson->MoveNext();
                    }
                    if(in_array($incharge, $myGroupsIdPersonArr) || $incharge == $idperson){
                        //SOU RESPONSÁVEL POR ESTA SOL
                        $smarty->assign('displaychanges', '1');
                        $smarty->assign('displayassume',  '0');
                        $smarty->assign('displayopaux',   '1');
                        $smarty->assign('displayrepass',  '1');
                        $smarty->assign('displayreject',  '0');
                        $smarty->assign('displayclose',   '1');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displayaux', 	  '0');
                        $smarty->assign('displayapprove', '0');
                        $smarty->assign('displayreturn',  '0');
                        $smarty->assign('displayreprove', '0');
                        $smarty->assign('displaynote', 	  '1');
                        $smarty->assign('displayprint',   '1');
                    }
                    else{
                        //NÃO SOU RESPONSÁVEL POR ESTA SOL
                        $smarty->assign('displaychanges', '0');
                        if ($_SESSION['hdk']['SES_IND_ASSUME_OTHER'] == 1) {
                            $smarty->assign('displayassume',  '1');
                        }else{
                            $smarty->assign('displayassume',  '0');
                        }
                        $smarty->assign('displayopaux',   '0');
                        $smarty->assign('displayrepass',  '0');
                        $smarty->assign('displayreject',  '0');
                        $smarty->assign('displayclose',   '0');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displayaux', 	  '0');
                        $smarty->assign('displayapprove', '0');
                        $smarty->assign('displayreturn',  '0');
                        $smarty->assign('displayreprove', '0');
                        if(in_array($idperson, $arrayAux)){
                            $smarty->assign('displaynote','1');
                        }else{
                            $smarty->assign('displaynote','0');
                        }
                        $smarty->assign('displayprint',   '1');
                    }
                    break;
                case "4":
                    //WAITING FOR APP
                    $smarty->assign('displaychanges', '0');
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '0');
                    $smarty->assign('displayreturn',  '0');
                    $smarty->assign('displayreprove', '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "5":
                    //FINISHED
                    $smarty->assign('displaychanges', '0');
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    if ($_SESSION['hdk']['SES_IND_REOPEN_USER'] == '0')
                        $smarty->assign('displayreopen',  '0');
                    else
                        $smarty->assign('displayreopen',  '1');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '0');
                    $smarty->assign('displayreturn',  '0');
                    $smarty->assign('displayreprove', '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "6":
                    //REJECTED
                    $smarty->assign('displaychanges', '0');
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '0');
                    $smarty->assign('displayreturn',  '0');
                    $smarty->assign('displayreprove', '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                default:
                    $smarty->assign('displaychanges', '0');
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayopaux',   '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displayaux', 	  '0');
                    $smarty->assign('displayapprove', '0');
                    $smarty->assign('displayreturn',  '0');
                    $smarty->assign('displayreprove', '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
            }

            // Trello
            $smarty->assign('displaytrello',   '1');
        }

        $smarty->assign('hidden_idstatus',$idswitch_status);

    }

    public function saveChangesTicket()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request       = $_POST['coderequest'];
        $type       = $_POST['cmbType'];
        $item       = $_POST['item'];
        $service    = $_POST['service'];
        $reason     = $_POST['reason'] == 0 ? 'NULL' : $_POST['reason'];
        $way        = $_POST['way'] == 0 ? 'NULL' : $_POST['way'];
        $priority   = $_POST['priority'];


        $this->dbTicket->BeginTrans();
        $rs = $this->dbTicket->updateRequest($code_request, $type, $item , $service, $reason, $way, $priority);
        if(!$rs){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Update ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbTicket->CommitTrans();

        echo "OK";

    }

    public function assumeTicket()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];
        $grpview = $_POST['grpview'];
        $groupAssume = $_POST['groupAssume'];
        $incharge = $_POST['incharge'];
        $typeincharge = $_POST['typeincharge'];

        $idstatus = '3'; //EM ATENDIMENTO
        $idperson = $_SESSION['SES_COD_USUARIO']; //ID DO USUARIO QUE ESTÁ ASSUMINDO
        $this->dbTicket->BeginTrans();

        $inslog = $this->dbTicket->changeRequestStatus($idstatus, $code_request, $idperson); //SALVA NO LOG A MUDANÇA DE STATUS Q VAI FAZER
        if (!$inslog) {
            if($this->log)
                $this->logIt("Insert Log - Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
        $description = "<p><b>" . $langVars['Request_assumed'] . "</b></p>";

        $serviceVal = 'NULL';
        $public     = 1;
        $typeNote   = 3;
        $callback   = 0;
        $execDate   = '0000-00-00 00:00:00';

        $totalminutes   = 0 ;
        $starthour      = 0;
        $finishour      = 0;
        $hourtype       = 0 ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $date, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Note - Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        if($grpview == 1){//ADICIONAR TRACK PARA O GRUPO
            if($typeincharge == "P"){
                $track = $this->dbTicket->insertInCharge($code_request, $groupAssume, "G", '0', 'N', '1');
            }elseif($typeincharge == "G"){
                $track = $this->dbTicket->insertInCharge($code_request, $incharge, "G", '0', 'N', '1');
            }
            if(!$track){
                if($this->log)
                    $this->logIt("Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }
        }

        $type = "P"; //TIPO PESSOA
        $rep = 'N'; //NÃO É REPASS
        $ind = '1'; //RESPONSAVEL ATUAL
        $removeInCharge = $this->dbTicket->removeIncharge($code_request); //Remove request's responsible before add new one
        if (!$removeInCharge) {
            if($this->log)
                $this->logIt("Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $insInCharge = $this->dbTicket->insertInCharge($code_request, $idperson, $type, $ind, $rep, '0'); //Add new responsible
        if (!$insInCharge) {
            if($this->log)
                $this->logIt("Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $changeStat = $this->dbTicket->updateReqStatus($idstatus, $code_request); //Update request status
        if (!$changeStat) {
            if($this->log)
                $this->logIt("Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $getEntryDate = $this->dbTicket->getEntryDate($code_request);
        $MIN_OPENING_TIME = $this->_dif_date($getEntryDate,date("Y-m-d H:i"));
        $data = array("MIN_OPENING_TIME" => $MIN_OPENING_TIME);
        $uptimes = $this->dbTicket->updateRequestTimes($code_request, $data);

        if (!$uptimes) {
            if($this->log)
                $this->logIt("Assume ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $this->dbTicket->CommitTrans();

        $arrayParam = array('transaction' => 'operator-assume',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        $aRet = array("status" => "OK");
        echo json_encode($aRet);

    }

    public function modalAuxOperator()
    {
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request       = $_POST['code_request'];

        $aRet = $this->makeAuxOperatorScreen($code_request);

        echo json_encode($aRet);

    }

    public function makeAuxOperatorScreen($code_request)
    {

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $arrOpe = $this->_comboAuxOperators($code_request,'not');
        $select = '';

        foreach ( $arrOpe['ids'] as $indexKey => $indexValue ) {
            if ($arrOpe['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }

            $select .= "<option value='$indexValue' $default>".$arrOpe['values'][$indexKey]."</option>";
        }

        $arrOpe2 = $this->_comboAuxOperators($code_request,'in');
        $tbody = '';
        $auxopeline = '';

        foreach ( $arrOpe2['ids'] as $indexKey => $indexValue ) {
            $tbody .= "<tr><td>".$arrOpe2['values'][$indexKey]."<input type='hidden' class='hdkAuxOpe' name='hdkAuxOpe[]' id='hdkAuxOpe_".$indexValue."' value='".$indexValue."'></td><td><a href='javascript:;' onclick='removeAuxOpe(this)' class='btn btn-danger'><i class='fa fa-user-times'></i></a></td></tr>";
            $auxopeline .= "<div>".$arrOpe2['values'][$indexKey]."</div>";
        }

        $aRet = array(
            "cmblist" => $select,
            "tablelist" => $tbody,
            "auxopelist" => $auxopeline
        );

        return $aRet;

    }

    public function insertAuxOperator()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];
        $idauxoperator = $_POST['auxopeid'];

        $this->dbTicket->BeginTrans();
        $rs = $this->dbTicket->insertOperatorAux($code_request,$idauxoperator);
        if(!$rs){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Aux Operator ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbTicket->CommitTrans();

        $aRet = $this->makeAuxOperatorScreen($code_request);

        echo json_encode($aRet);

    }

    public function deleteAuxOperator()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];
        $idauxoperator = $_POST['auxopeid'];

        $this->dbTicket->BeginTrans();
        $rs = $this->dbTicket->deleteOperatorAux($code_request,$idauxoperator);
        if(!$rs){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Delete Aux Operator ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbTicket->CommitTrans();

        $aRet = $this->makeAuxOperatorScreen($code_request);

        echo json_encode($aRet);

    }

    public function repassTicket()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $type = $_POST['type'];
        $repassto = $_POST['repassto'];
        $code_request = $_POST['code_request'];
        $view = $_POST['view'];
        $idgrouptrack = $_POST['idgrouptrack'];
        $incharge = $_POST['incharge'];

        if ($type == 'operator') {
            $name = $this->dbPerson->selectPersonName($repassto);
            $type = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        }elseif ($type == 'group') {
            $name = $this->dbGroup->selectRepGroupData($repassto);
            $name = $name->fields['name'];
            $type = $langVars['to'] . " " . $langVars['Group'];
            $type2 = "G";
        }else{
            return false;
        }

        $status = '2'; // Redirected ticket
        $reopened = '0';
        $rep = 'Y';
        $this->dbTicket->BeginTrans();

        switch($view){
            case "G": // Redirect ticket, but the group continues to follow
                if($idgrouptrack == 0){
                    $track = $this->dbTicket->insertInCharge($code_request, $incharge, "G", '0', $rep,  '1');
                    if(!$track){
                        if($this->log)
                            $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        $this->dbTicket->RollbackTrans();
                        return false;
                    }
                }else{
                    $track = $this->dbTicket->insertInCharge($code_request, $idgrouptrack, "G", '0', $rep, '1');
                    if(!$track){
                        if($this->log)
                            $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                        $this->dbTicket->RollbackTrans();
                        return false;
                    }
                }
                break;
            case "P": // Redirect ticket and continue following
                $track = $this->dbTicket->insertInCharge($code_request, $_SESSION['SES_COD_USUARIO'], "P", '0', $rep, '1');
                if(!$track){
                    if($this->log)
                        $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false;
                }
                break;
            case "N": // Do not follow

                break;
        }

        $inslog = $this->dbTicket->changeRequestStatus($status, $code_request, $_SESSION['SES_COD_USUARIO'], $reopened); //insere log
        if (!$inslog) {
            if($this->log)
                $this->logIt("Change Status / Log - Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }
        
        //Insert Note
        $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
        $description = "<p><b>" . $langVars['Request_repassed'] . strtolower($type) . " " . $name . "</b></p>";
        
        $serviceVal = 'NULL';
        $public     = 1;
        $typeNote   = 3;
        $callback   = 0;
        $execDate   = '0000-00-00 00:00:00';

        $totalminutes   = 0 ;
        $starthour      = 0;
        $finishour      = 0;
        $hourtype       = 0 ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        //Insert into hdk_tbrequest_repassed - log
        $noteid = $this->dbTicket->getRepassNote($code_request);
        $insrep = $this->dbTicket->insertRepassRequest($date, $code_request, $noteid);
        if (!$insrep) {
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        //Set all ind_in_charge with 0 (zero) (Remove all responsible)
        $rmincharge = $this->dbTicket->removeIncharge($code_request);
        if (!$rmincharge) {
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        //Add new responsible
		$insInCharge = $this->dbTicket->insertInCharge($code_request, $repassto, $type2, 1, $rep);
        if (!$insInCharge) {
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        //Change Request Status - Repassed
        $changeStat = $this->dbTicket->updateReqStatus($status, $code_request);
        if (!$changeStat) {
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        //Save Repass Date
        $ud = $this->dbTicket->updateDate($code_request, "forwarded_date");
        if(!$ud){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Repass ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $this->dbTicket->CommitTrans();

        $arrayParam = array('transaction' => 'forward-ticket',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        $aRet = array("status" => "OK");
        echo json_encode($aRet);

    }

    public function rejectTicket()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];
        $incharge = $_POST['incharge'];
        $typeincharge = $_POST['typeincharge'];
        $rejectreason = $_POST['rejectreason'];

        $idstatus = '6';
        $idperson = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';

        $this->dbTicket->BeginTrans();

        $inslog = $this->dbTicket->changeRequestStatus($idstatus, $code_request, $idperson);
        if (!$inslog) {
            if($this->log)
                $this->logIt("Insert Log - Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
        $description = "<p><b>" . $langVars['Request_rejected'] . "</b></p>".$rejectreason;

        $serviceVal = 'NULL';
        $public     = 1;
        $typeNote   = 3;
        $callback   = 0;
        $execDate   = '0000-00-00 00:00:00';

        $totalminutes   = 0 ;
        $starthour      = 0;
        $finishour      = 0;
        $hourtype       = 0 ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $date, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Note - Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $changeStat = $this->dbTicket->updateReqStatus($idstatus, $code_request); //Update request status
        if (!$changeStat) {
            if($this->log)
                $this->logIt("Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $type = "P"; //TIPO PESSOA
        $rep = 'N'; //NÃO É REPASS
        $ind = '1'; //RESPONSAVEL ATUAL
        $removeInCharge = $this->dbTicket->removeIncharge($code_request); //Remove request's responsible before add new one
        if (!$removeInCharge) {
            if($this->log)
                $this->logIt("Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $insInCharge = $this->dbTicket->insertInCharge($code_request, $idperson, $type, $ind, $rep, '0'); //Add new responsible
        if (!$insInCharge) {
            if($this->log)
                $this->logIt("Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $ud = $this->dbTicket->updateDate($code_request, "rejection_date");
        if(!$ud){
            if($this->log)
                $this->logIt("Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $this->dbTicket->CommitTrans();

        $arrayParam = array('transaction' => 'operator-reject',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $email = $this->_sendNotification($arrayParam);



        //  pipetodo [albandes] : Need to review
        /*
        if($_SESSION['hdk']['SES_MAIL_OPERATOR_REJECT'] && $typeincharge == "G" && $_SESSION['hdk']['SEND_EMAILS'] == '1'){
            $_SESSION['hdk']['SES_MAIL_OPERATOR_REJECT_ID'] = $incharge;
            //$email = $this->_sendEmail('operator_reject', $code_request, $rejectreason);
            $email = $this->_sendNotification('operator-reject','email', $code_request, $rejectreason);
        }
        */
        $aRet = array("status" => "OK");
        echo json_encode($aRet);

    }

    public function finishTicket()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';

        $this->dbTicket->BeginTrans();

        $ud = $this->dbTicket->updateDate($code_request, "finish_date");
        if(!$ud){
            if($this->log)
                $this->logIt("Finish ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        if ($_SESSION['hdk']['SES_APROVE'] == 1) {
            $idstatus = '4';
            $description = '<p><b>' . $langVars['Request_waiting_approval'] . '</b></p>';

            $iToken = $this->dbEvaluation->insertToken($code_request);
            if(!$iToken){
                if($this->log)
                    $this->logIt("Insert Eval Token - Finish ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }
        } else {
            $idstatus = '5';
            $description = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
            $ud = $this->dbTicket->updateDate($code_request, "approval_date");
            if(!$ud){
                if($this->log)
                    $this->logIt("Finish ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }
        }

        $inslog = $this->dbTicket->changeRequestStatus($idstatus, $code_request, $idperson);
        if (!$inslog) {
            if($this->log)
                $this->logIt("Insert Log - Finish ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $date = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $serviceVal = 'NULL';
        $public     = 1;
        $typeNote   = 3;
        $callback   = 0;
        $execDate   = '0000-00-00 00:00:00';

        $totalminutes   = 0 ;
        $starthour      = 0;
        $finishour      = 0;
        $hourtype       = 0 ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbTicket->insertNote($code_request, $_SESSION["SES_COD_USUARIO"], $description, $date, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
        if(!$ret){
            $this->dbTicket->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Note - Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $changeStat = $this->dbTicket->updateReqStatus($idstatus, $code_request); //Update request status
        if (!$changeStat) {
            if($this->log)
                $this->logIt("Reject ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $getEntryDate = $this->dbTicket->getEntryDate($code_request);
        $getAssumedDate = $this->dbTicket->getAssumedDate($code_request);
        $MIN_EXPENDED_TIME = $this->dbTicket->getExpendedTime($code_request);
        $MIN_CLOSURE_TIME = $this->_dif_date($getEntryDate,date("Y-m-d H:i"));
        $MIN_ATTENDANCE_TIME = $this->_dif_date($getAssumedDate,date("Y-m-d H:i"));
        $data = array(
            "MIN_CLOSURE_TIME" => $MIN_CLOSURE_TIME,
            "MIN_ATTENDANCE_TIME" => $MIN_ATTENDANCE_TIME,
            "MIN_EXPENDED_TIME"	=> $MIN_EXPENDED_TIME
        );

        $uptimes = $this->dbTicket->updateRequestTimes($code_request, $data);
        if (!$uptimes) {
            if($this->log)
                $this->logIt("Finish ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $this->dbTicket->CommitTrans();

        $arrayParam = array('transaction' => 'finish-ticket',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        $aRet = array("status" => "OK");
        echo json_encode($aRet);

    }

    public function changeExpireDate()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];
        $dateChangeExpire = str_replace("'", "", $this->formatSaveDate($_POST['dateChangeExpire']));
        $timeChangeExpire = (!$_POST['timeChangeExpire'] || $_POST['timeChangeExpire'] == '') ? date("H:i:s") : $_POST['timeChangeExpire'];
        $reason = $_POST['reason'];

        $idperson = $_SESSION['SES_COD_USUARIO'];

        $this->dbTicket->BeginTrans();
        $extNumber = $this->dbTicket->getExtNumber($code_request);
        $extNumber = $extNumber + 1;
        $date = $dateChangeExpire." ".$timeChangeExpire;

        $upd =  $this->dbTicket->saveExtension($code_request, $extNumber, $date);
        if (!$upd) {
            if($this->log)
                $this->logIt("Change Expire Date ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $updCh = $this->dbTicket->insertChangeExpireDate($code_request, $reason, $idperson);
        if (!$updCh) {
            if($this->log)
                $this->logIt("Change Expire Date ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $this->dbTicket->CommitTrans();

        $where = 'WHERE code_request = '.$code_request;
        $rsTicket = $this->dbTicket->getRequestData($where);
        $newDate = $this->formatDateHour($rsTicket->fields['expire_date']);
        $newexpire_date = $this->formatDate($rsTicket->fields['expire_date']);
        $newexpire_hour = $this->formatHour($rsTicket->fields['expire_date']);

        $aRet = array("status" => "OK","newdate"=> $newDate,"newmod_date"=>$newexpire_date,"newmod_hour"=>$newexpire_hour);
        echo json_encode($aRet);
    }

    public function saveNewAttWay()
    {

        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $txtattway = $_POST['txtattway'];

        $this->dbTicket->BeginTrans();
        $ret = $this->dbTicket->insertWay($txtattway);

        if (!$ret) {
            if($this->log)
                $this->logIt('Insert New Attendance Way - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            $this->dbTicket->RollbackTrans();
            return false;
        }

        $this->dbTicket->CommitTrans();

        $aRet = array("status" => "OK");
        echo json_encode($aRet);
    }

    public function ajaxAttWay(){
        echo $this->_comboAttWayHtml();
    }

    public function jsonAtt()
    {

        $this->protectFormInput();

        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        $where = '';

        $idStatus = $_POST['idstatus'];
        $typeexpdate = !$_POST['typeexpdate'] ? 0 : $_POST['typeexpdate'];

        switch ($typeexpdate) {
            case 1:
                if ($this->database  == 'oci8po') {
                    $wheredata = " AND a.expire_date >= sysdate";
                }else{
                    //$wheredata = " AND date(a.expire_date) < date(now())";
                    $wheredata = " AND a.expire_date >= now()";
                }
                break;
            case 2:
                if ($this->database  == 'oci8po') {
                    $wheredata = " AND a.expire_date = sysdate";
                }else{
                    $wheredata = " AND date(a.expire_date) = date(now())";
                }
                break;
            case 3:
                $idStatus = 3;
                if ($this->database  == 'oci8po') {
                    $wheredata = " AND a.expire_date <= sysdate";
                }else{
                    //$wheredata = " AND date(a.expire_date) < date(now())";
                    $wheredata = " AND a.expire_date <= now()";
                }
                break;
            case 4:
                $idStatus = 1;
                if ($this->database  == 'oci8po') {
                    $wheredata = " AND a.expire_date <= sysdate";
                }else{
                    //$wheredata = " AND date(a.expire_date) < date(now())";
                    $wheredata = " AND a.expire_date <= now()";
                }
                break;
            default:
                $wheredata = '';
                break;
        }

        if ($idStatus) {
            if ($idStatus == 'ALL') {
                $where = '';
            } else {
                $where .= " AND b.idstatus_source =" . $idStatus . " ";
            }
        } else {
            $where .= " AND b.idstatus_source = 1 ";

        }

        if ($this->getConfig('license') == '200701006') {
            $where .= " AND a.iditem <> 124";
        }

        $idPersonGroups = '';
        if($_SESSION['SES_PERSON_GROUPS']){
            $rsIdPersonGroups = $this->dbTicket->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
            while (!$rsIdPersonGroups->EOF) {
                $idPersonGroups .=  $rsIdPersonGroups->fields['idperson'].",";
                $rsIdPersonGroups->MoveNext();
            }
            $idPersonGroups = substr($idPersonGroups,0,-1);
        }

        $typeview = !$_POST['typeview'] ? 1 : $_POST['typeview'];

        switch ($typeview) {
            case 2:
                $wheretip = "((inch.ind_in_charge = 1
										AND inch.id_in_charge IN($cod_usu ))
										OR (inch.ind_operator_aux = 1
											AND inch.id_in_charge = $cod_usu)
										OR (inch.id_in_charge IN($cod_usu )
											and inch.ind_track = 1))";
                break;
            case 3:
                $wheretip = 	"
								((inch.ind_in_charge = 1 AND inch.id_in_charge IN($idPersonGroups))
								OR (inch.id_in_charge in($idPersonGroups)
											AND inch.ind_track = 1))
								";
                break;
            default:
                $cond = ($idPersonGroups != '') ? $cod_usu.','.$idPersonGroups : $cod_usu;
                $wheretip = " ((inch.ind_in_charge = 1
                            AND inch.id_in_charge IN($cond))
                            OR (inch.ind_operator_aux = 1
                                AND inch.id_in_charge = $cod_usu)
                            OR (inch.id_in_charge IN($cond)
                                AND inch.ind_track = 1)) ";
                break;
        }

        // create the query.
        $page = $_POST['page'];
        $limit = $_POST['rows'];
        $sidx = $_POST['sidx'];
        $sord = $_POST['sord'];

        if(!$sidx)
            $sidx ='code_request';
        /*if(!$sord)
            $sord ='desc';*/
        if(!$sord)
            $sord ='desc';

        if ($_POST['_search'] == 'true'){
            $arrSearch = array('.','-','/','_');
            $arrReplace = array('','','','');

            switch ($_POST['searchField']){
                case 'code_request':
                    $searchField = 'a.code_request';
                    $_POST['searchString'] = str_replace($arrSearch,$arrReplace,$_POST['searchString']);
                    break;
                case 'entry_date':
                    $searchField = "DATE_FORMAT(a.entry_date,'%d/%m/%Y')";
                    break;
                case 'subject':
                    $searchField = 'a.subject';
                    break;
                case 'own.name':
                    $searchField = "own.name";
                    break;
                case 'company':
                    $searchField = "comp.name";
                    break;
                default:
                    $searchField = "a.description";
                    break;
            }

            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }


        $count = $this->dbTicket->getNumberRequestsAtt($cod_usu, $wheredata, $where, $wheretip);
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $limit*$page - $limit;
        if($start <0) $start = 0;
        //


        if ($this->database == 'oci8po') {
            $entry_date = " to_char(a.entry_date,'DD/MM/YYYY HH24:MI') fmt_entry_date " ;
            $expire_date = " to_char(a.expire_date,'DD/MM/YYYY HH24:MI')expire_date , a.expire_date  AS fmt_expire_date" ;
        }
        else
        {
            $entry_date = " DATE_FORMAT(a.entry_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as fmt_entry_date" ;
            $expire_date = " DATE_FORMAT(a.expire_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as expire_date, a.expire_date AS fmt_expire_date" ;
        }

        $rsTicket = $this->dbTicket->getRequests($cod_usu, $entry_date, $expire_date, $wheredata, $where, $wheretip, $start, $limit, $sidx, $sord);
        while (!$rsTicket->EOF) {
            $star = ($rsTicket->fields['flag_opened'] == 1 && $rsTicket->fields['status'] != 1) ? '<i class="fa fa-star" />' : ' ';
            $iattch = ($rsTicket->fields['totatt'] > 0) ? "<span class='label label-primary'>".$rsTicket->fields['totatt']."</span>" : " ";
            $linkcode = $this->makeLinkCode($rsTicket->fields['id_in_charge'], $rsTicket->fields['type_in_charge'], $cod_usu, $rsTicket->fields['ind_track'], $rsTicket->fields['code_request']);

            $rows[] = array(
                'star' => $star,
                'attch' => $iattch,
                'code_request_view' => $linkcode,
                'entry_date' => $rsTicket->fields['entry_date_order'],
                'company' => $rsTicket->fields['company'],
                'owner' => $rsTicket->fields['personname'],
                'type' => $rsTicket->fields['type'],
                'item' => $rsTicket->fields['item'],
                'service' => $rsTicket->fields['service'],
                'subject' => $rsTicket->fields['subject'],
                'expire_date' => $this->highlightExpireDate($rsTicket->fields['expire_date_order'],$rsTicket->fields['expire_date'],$rsTicket->fields['idstatus_source']) ,
                'in_charge' => $rsTicket->fields['in_charge'],
                'statusview' => '<span style="color:'.$rsTicket->fields['s_color'].'">'.$rsTicket->fields['statusview'].'</span>',
                'priority' => '<span style="color:'.$rsTicket->fields['p_color'].'">'.$rsTicket->fields['priority'].'</span>',
                'code_request' => $rsTicket->fields['code_request'],
                'description' => ''
            );
            $rsTicket->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsTicket->RecordCount(),
            'rows' => $rows
        );

        echo json_encode($data);
    }

    function highlightCodeRequest($idincharge, $type_in_charge, $cod_user, $ind_track,$code_request) {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);
		
		if($ind_track == 1 && $idincharge != $cod_user && $type_in_charge == "P"){
			//EU ESTOU ACOMPANHANDO
			$ret = "<span style='color: #808080; border-bottom:1px solid #808080; font-weight:bold;' title='" . $langVars['tlt_span_track_me'] . "' > " . $code_request . " </span>";
		}
		elseif($ind_track == 1 && $type_in_charge == "G"){
			//GRUPO ESTA ACOMPANHANDO
			$ret = "<span style='color: #000000; border-bottom:1px solid #000000; font-weight:bold;' title='" . $langVars['tlt_span_track_group'] . "' > " . $code_request . " </span>";
		}
		elseif($idincharge == $cod_user && $type_in_charge == "P"){
			//MINHA
			$ret = "<span style='color: #DF6300; border-bottom:1px solid #DF6300; font-weight:bold;' title='" . $langVars['tlt_span_my'] . "' > " . $code_request . " </span>";
		}elseif($ind_track == 0 && $type_in_charge == "G"){
			//MEU GRUPO
			$ret = "<span style='color: #0012DF; border-bottom:1px solid #0012DF; font-weight:bold;' title='" . $langVars['tlt_span_group'] . "' > " . $code_request . " </span>";
		}else{
			$ret = "<span style='color: #000000; border-bottom:1px solid #000000; font-weight:bold;' title='" . $langVars['tlt_span_track_group'] . "' > " . $code_request . " </span>";
		}		
        return $ret;
    }

    function highlightExpireDate($expiredate, $fmtexpiredate, $idstatus) {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $datetime_exp = strtotime($expiredate);
        $datetime_now = strtotime(date('Y-m-d H:i:s'));
        $date_exp = strtotime(date("Y-m-d",strtotime($expiredate)));
        $date_now = strtotime(date('Y-m-d'));

        if($datetime_exp >= $datetime_now){
            //vencendo
            $color_exp = "#000000";
        }elseif($date_exp == $date_now){
            //Vencendo Hj
            $color_exp = "#0000FF";
        }elseif($datetime_exp <= $datetime_now && $idstatus == 3){
            //Vencidas
            $color_exp = "#FF0000";
        }elseif($datetime_exp <= $datetime_now && $idstatus == 1){
            //vencidas não assumidas
            $color_exp = "#990000";
        }
        return "<span style='color:" . $color_exp . ";'>" . $fmtexpiredate . "</span>";;
    }

    function checkapproval()
    {
        echo $this->_checkapproval();
    }

    public function makeLinkCode($id_in_charge,$type_in_charge,$iduser,$ind_track,$code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."'>".$this->highlightCodeRequest($id_in_charge,$type_in_charge,$iduser,$ind_track,$code_request)."</a>";
    }

    public function linkTicketAttachments($code_request,$aAttachs)
    {
        foreach($aAttachs as $key=>$val){
            $extension = strrchr($val, ".");
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/helpdezk/attachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/helpdezk/attachments/';
            }
            $targetOld = $targetPath.$val;

            $idAtt = $this->dbTicket->saveTicketAtt($code_request,$val);
            if (!$idAtt) {
                if($this->log)
                    $this->logIt('Can\'t save attachment into DB - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't link file {$val} to request {$code_request}");
            }

            $targetNew =  $targetPath.$idAtt.$extension;

            if(!rename($targetOld,$targetNew)){
                $delAtt = $this->dbTicket->deleteTicketAtt($idAtt);
                if (!$delAtt) {
                    if($this->log)
                        $this->logIt('Can\'t delete attachment into DB - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                }
                return array("success"=>false,"message"=>"Can't link file {$val} to request {$code_request}");
            }
        }
        return array("success"=>true,"message"=>"");

    }


    public function linkNoteAttachments($idNote,$aAttachs)
    {
        foreach($aAttachs as $key=>$val){
            $extension = strrchr($val, ".");
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/helpdezk/noteattachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/';
            }
            $targetOld = $targetPath.$val;

            $idAtt = $this->dbTicket->saveNoteAttachment($idNote,$val);
            if (!$idAtt) {
                if($this->log)
                    $this->logIt('Can\'t save attachment into DB - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't link file {$val} to request {$idNote}");
            }

            $targetNew =  $targetPath.$idAtt.$extension;

            if(!rename($targetOld,$targetNew)){
                return array("success"=>false,"message"=>"Can't link file {$val} to request {$idNote}");
            }
        }
        return array("success"=>true,"message"=>"");

    }


}


?>
