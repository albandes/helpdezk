<?php

class apiController extends apiSystem {

    public function __construct()
    {

        parent::__construct();

        $this->_log = true ;
        $this->_logFile  = $this->getApiLog();

    }

    // New
    public function _getLastInsertId($model,$table,$key){
        $dbModel = new $model;
        return $dbModel->TableMaxID($table, $key);
    }

    // New
    public function _saveNoteAttachment($idNote,$fileName)
    {
        $dbTicket = new ticket_model();
        $idNoteAttachment = $dbTicket->saveNoteAttachment($idNote,$fileName);
        return $idNoteAttachment;
    }

    // New
    public function _saveTicketAttachment($codeRequest,$fileName)
    {
        $dbTicket = new ticket_model();
        $idTicketAttchment = $dbTicket->saveTicketAtt($codeRequest,$fileName);

        return $idTicketAttchment;
    }

    // New
    public function _inchargeName($code_request)
    {
        $dbTicket = new ticket_model();
        $rsInCharge = $dbTicket->getInCharge($code_request);
        return $rsInCharge->fields['name'];
    }

    protected function _view($nome, $vars=NULL) {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $langVars2 = $smarty->get_template_vars();
        
        if (is_array($vars) && count($vars) > 0) {
            extract($vars, EXTR_PREFIX_ALL, 'view');
        }
        require_once(VIEWS . $nome);
    }

    function _getUserByToken($token)
    {
        $db = new index_model();
        $idperson = $db->getUserIdByToken($token);
        //die($idperson);
        if($idperson)
            return $idperson;
        else
            return false;
    }

    function _getLoginByToken($token)
    {
        $db = new index_model();
        $login = $db->getLoginByToken($token);
        if($login)
            return $login;
        else
            return false;
    }

    // OK

    public function _editRequest($codeRequest)
    {
        return substr($codeRequest,0,4).'-'.substr($codeRequest,4,2).'.'.substr($codeRequest,6,6);
    }

    public function _getPersonData($where = '', $order = '', $limit = '')
    {
        $dbPerson = new person_model();
        return $dbPerson->selectPerson();
    }

    public function _getPersonName($idPerson)
    {
        $db = new person_model();
        return $db->selectPersonName($idPerson);
    }

    public function _getQuestions()
    {
        $db = new ticket_model();
        return $db->getQuestions();
    }

    public function _getAnswers($idQuestion)
    {
        $db = new ticket_model();
        return $db->getAnswers($idQuestion);
    }

    // --- New Routes --
    public function _getVocabulary($aQuery)
    {
        $locale     = $aQuery['locale'];
        $fields     = $aQuery['fields'];
        $arrayWord  = $aQuery['wordList'];

        $query = '';

        if ($fields == 'all') {
            $where = "b.name = '{$locale}' AND a.idlocale=b.idlocale";
        } elseif ($fields == 'wildcard') {
            $first = true ;
            foreach ($arrayWord['vocabulary'] as $row) {
                if ($first) {
                    $query .= ' LIKE ' . "'".$row['smarty_tag']."'";
                    $first = false ;
                    continue;
                }
                $query .= ' OR a.key_name LIKE ' . "'".$row['smarty_tag']."'";
            }
            $where = "( a.key_name {$query} ) AND b.name = '{$locale}' AND a.idlocale=b.idlocale";
        } elseif ($fields == 'object') {
            foreach ($arrayWord['vocabulary'] as $row) {
                $query .= "'".$row['smarty_tag']."', ";
            }
            $trimQuery=  rtrim($query, ", "); // Remove the last character only if it's comma
            $where = "a.key_name IN ({$trimQuery}) AND b.name = '{$locale}' AND a.idlocale=b.idlocale";
        }

        $dbVocabulary = new vocabulary_model();

        $rsVocabulary = $dbVocabulary->selectVocabulary($where);

        while (!$rsVocabulary->EOF) {
            $result[] = array(
                "key_name" => $rsVocabulary->fields['key_name'],
                "key_value" => utf8_decode($rsVocabulary->fields['key_value'])
            );
            $rsVocabulary->MoveNext();
        }

        $data = array(
            'locale'  => $locale,
            'records' => $result
        );

        return $data;

    }

    public function _getArea()
    {
        $dbTicket = new ticket_model();
        $select = $dbTicket->selectArea();
        while (!$select->EOF) {
            $result[] = array(
                "id" => $select->fields['idarea'],
                "name" => $select->fields['name'],
                "default" => $select->fields['default']
            );
            $select->MoveNext();
        }

        return $result;
    }

    public function _getType($idArea)
    {
        $db = new ticket_model();
        return $db->selectType($idArea);
    }

    public function _getItem($idType)
    {
        $db = new ticket_model();
        return $db->selectItem($idType);
    }

    public function _getItemCount($idType)
    {
        $db = new requestinsert_model();
        $rs = $db->selectItem($idType);
        return $rs->RecordCount();
    }

    public function _getService($idItem)
    {
        $db = new ticket_model();
        return $db->selectservice($idItem);
    }

    public function _getReason($idService)
    {
        $db = new ticket_model();
        return $db->selectReason($idService);
    }

    public function _getServiceCount($idItem)
    {
        $db = new requestinsert_model();
        $rs = $db->selectService($idItem);
        return $rs->RecordCount();
    }

    public function _getRequest($aParam)
    {

        $dbTicket = new ticket_model();
        $idPerson = $aParam['idPerson'] ;

        $idStatus  = (empty($aParam['idStatus']) ? "ALL" : $aParam['idStatus']);
        $page = (empty($aParam['page']) ? 1 : $aParam['page']);
        $limit = (empty($aParam['limit']) ? 25 : $aParam['limit']);

        $sord  = (empty($aParam['sortOrder']) ? 25 : $aParam['sortOrder']);
        $sidx  = (empty($aParam['sortName']) ? "a.entry_date" : $aParam['sortName']);

        $where = ($idStatus == 'ALL' ? " " : " AND b.idstatus_source IN (" . $idStatus . ") ");

        $count = $dbTicket->getNumberRequests($where,$idPerson);
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $limit*$page - $limit;
        if($start <0) $start = 0;

        $entry_date = " DATE_FORMAT(a.entry_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as fmt_entry_date" ;
        $expire_date = " DATE_FORMAT(a.expire_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as expire_date, a.expire_date AS fmt_expire_date" ;

        $rsTicket = $dbTicket->getRequest($entry_date, $expire_date, $where, $sidx,$sord,$start,$limit,$idPerson);

        while (!$rsTicket->EOF) {
            $rows[] = array(
                'code_request' => $rsTicket->fields['code_request'],
                'status_id'       =>  $rsTicket->fields['status'],
                'status_name'       =>  $rsTicket->fields['statusview'],
                'status_color' =>  $rsTicket->fields['color_status'],
                'entry_date'   => $rsTicket->fields['entry_date'],
                'subject'      => $rsTicket->fields['subject'],
                'expire_date'  => $rsTicket->fields['expire_date'],
                'in_charge'    => $rsTicket->fields['in_charge']
            );
            $rsTicket->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total_pages' => $total_pages,
            'records_per_page' => $limit,
            'total_records' => $count,
            'records' => $rows
        );

        return $data;
    }

    public function _getRequestData($idRequest)
    {
        $db = new ticket_model();
        return $db->getRequestData('where code_request = ' . $idRequest);
    }


    public function _getRequestNotes($idRequest)
    {
        $db = new ticket_model();
        return $db->getRequestNotes($idRequest);
    }

    public function _getRequestAttachment($code_request)
    {
        $db = new ticket_model();
        return $db->selectAttach($code_request);
    }

    public function _getRequestNoteAttachment($idNote)
    {
        $db = new ticket_model();
        return $db->getNoteAttachments($idNote);
    }

    public function _getTicketFile($id,$type)
    {
        $db = new ticket_model();
        return $db->getTicketFile($id,$type);
    }

    public function _getRequestsCountByStatus($idPerson,$idStatus)
    {
        $db = new user_model();
        return $db->getRequestsCountByStatus($idPerson,$idStatus);

    }

    public function _getNewRequestsCount($idPerson)
    {
        $db = new user_model();
        return $db->getNewRequestsCount($idPerson);

    }

    public function _getInProgressRequestsCount($idPerson)
    {
        $db = new user_model();
        return $db->getInProgressRequestsCount($idPerson );
    }

    public function _getWaitingApprovalRequestsCount($idPerson)
    {
        $db = new user_model();
        return $db->getWaitingApprovalRequestsCount($idPerson );
    }

    public function _getFinishedRequestsCount($idPerson)
    {
        $db = new user_model();
        return $db->getFinishedRequestsCount($idPerson );
    }

    public function _getRejectedRequestsCount($idPerson)
    {
        $db = new user_model();
        return $db->getRejectedRequestsCount($idPerson );
    }

    public function _getStatusColor()
    {
        $db = new user_model();
        return $db->getStatusColor();
    }

    public function _getStatus($where = '', $order = '', $limit = '')
    {
        $db = new status_model();
        return $db->getStatusWithCount($where, $order, $limit);
    }

    public function _insertNote($codeRequest,$idPerson,$note,$date,$minutes,$startHour,$finishHour,$execDate,$hourType,$serviceVal,$public,$idtype,$ipAddress,$callBack,$flgOpen,$idanexo)
    {
        $dbTicket = new ticket_model();
        return $dbTicket->insertNote($codeRequest,$idPerson,$note,$date,$minutes,$startHour,$finishHour,$execDate,$hourType,$serviceVal,$public,$idtype,$ipAddress,$callBack, $flgOpen,$idanexo);
    }

    public function _isRequest($code)
    {
        $db = new apiModel();
        return $db->isRequest($code);
    }

    public function _getRequestStatus($code){

        $db = new operatorview_model();
        return $db->getResquestStatus($code);
    }

    public function _cancelRequest($code, $idStatus, $idPerson)
    {
        $db = new user_model();
        $db->BeginTrans();
        $log = $db->updateLog($code, $idStatus, $idPerson);
        if (!$log) {
            $db->RollbackTrans();
            return false;
        }

        $cancel = $db->cancelRequest($code, $idStatus);
        if (!$cancel) {
            $db->RollbackTrans();
            return false;
        }

        $dbOperator     = new operatorview_model();
        $ipAddress      = $this->_getIpAddress();
        $callback       = '0';
        $idtype         = '3';
        $public         = '1';
        $note           = "<p><b><span style=\"color: #FF0000;\">".$this->_getLangVar('Text_cancel_request')."</span></b></p>";
        $date           = 'NOW()';

        $insNote = $dbOperator->insertNote($code,$idPerson,$note,$date,NULL,NULL,NULL,NULL,NULL,NULL,$public,$idtype,$ipAddress,$callback,'NULL');
        if (!$insNote) {
            $dbOperator->RollbackTrans();
            $db->RollbackTrans();
            return false;
        }
        $db->CommitTrans();
        $dbOperator->CommitTrans();

        return true;
    }

    public function _reopenRequest($code,$idStatus,$idPerson)
    {
        $db = new operatorview_model();
        $db->BeginTrans();

        $reopened   = '1';
        $inslog     = $db->changeRequestStatus($idStatus, $code, $idPerson);
        if (!$inslog) {
            $db->RollbackTrans();
            return false;
        }
        $callback   = '0';
        $idtype     = '3';
        $public     = '1';
        $note       = "<p><b><span style=\"color: #FF0000;\">".$this->_getLangVar('Text_reopen_request')."</span></b></p>";
        $date       = 'now()';
        $insNote    = $db->insertNote($code,$idPerson,$note,$date,NULL,NULL,NULL,NULL,NULL,NULL,$public,$idtype,$this->_getIpAddress(),$callback,'NULL');
        if (!$insNote) {
            $db->RollbackTrans();
            return false;
        }
        $changeStat = $db->updateReqStatus($idStatus, $code);
        if (!$changeStat) {
            $db->RollbackTrans();
            return false;
        }
        $db->CommitTrans();
        return true ;
    }
/*
    public function _getEvaluationQuestions()
    {
        $db = new operatorview_model();
        return $db->getEvaluationQuestions();
    }
*/
    public function _isLoged($token)
    {

        $idPerson = $this->_getUserByToken($token);

        if(!$idPerson) {
            return false ;
        } else {
            return $idPerson ;
        }

    }

    // Since May 24, 2017
    public function _isLastRecord ($rs)
    {
        return ($rs->currentRow()+1  == $rs->rowCount()) ?  true :  false;
    }

    public function _checkEvaluation($evals){
        $dbEval = new evaluation_model();
        $ids = '('.implode(',', $evals). ')';
        $ret = $dbEval->selectEvaluation("WHERE idevaluation IN $ids",'','');
        if ($ret->RecordCount() != count($evals) ){

            return true;
        }  else {
            return false;
        }
    }

    public function _evaluateRequest($code,$idPerson,$arrEvaluation,$idStatus,$reopened,$callback,$idType,$public,$note,$date)
    {

        $db = new operatorview_model();
        $dbEval = new evaluation_model();

        $db->BeginTrans();
        $insNote = $db->insertNote($code, $idPerson, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idType, $this->_getIpAddress(), $callback, 'NULL');
        if (!$insNote) {
            $db->RollbackTrans();
            return false;
        }

        if (!$db->updateReqStatus($idStatus, $code)){
            $db->RollbackTrans();
            return false;
        }

        if (!$db->clearEvaluation($code)){
            $db->RollbackTrans();
            return false;
        }

        foreach( $arrEvaluation as $key => $idAnswer ){
            if(!$db->insertEvaluation($idAnswer,$code,$date)) {
                $db->RollbackTrans();
                return false;
            }
        }

        if (!$dbEval->removeTokenByCode($code)) {
            $db->RollbackTrans();
            return false;
        }

        $db->CommitTrans();
        return true ;
    }

    public function _saveRequest($arrRequest)
    {
        $dbTicket = new ticket_model();
        $dbRules = new ticketrules_model();
        $dbIndex = new index_model();
        $dbPerson = new person_model();

        $idArea = $arrRequest['area'] ;
        $idType = $arrRequest['type'];
        $idItem =$arrRequest['item'] ;
        $idService = $arrRequest['service'] ;

        $idReason = (empty($arrRequest['reason']) ? "NULL" : $arrRequest['reason']);
        $idWay    = (empty($arrRequest['way']) ? 1 : $arrRequest['way']);
        $idSource = (empty($arrRequest['source']) ? 1 : $arrRequest['source']);

        $subject = str_replace("'", "`",$arrRequest['subject']);
        $description = str_replace("'", "`",$arrRequest['description']);
        $idPerson = $arrRequest['idperson'];
        $numSerial = $arrRequest['num_serial'];
        $numOs = $arrRequest['num_os'];
        $numTag = $arrRequest['num_tag'];


        // Não está retornando nada - VER DEPOIS

        if($this->_log)
            $this->log('EM_BY_CRON: ' . $this->_getEspecificValueSession('EM_BY_CRON'), 'DEBUG', $this->_logFile);

        $rsIndex = $dbIndex->selectDataSession($idPerson);
        $idCompany = $rsIndex->fields['idjuridical'];


        $typePerson = $dbPerson->getIdTypePerson($idPerson);


        if($typePerson == 3){
            /*
             *  Operator - next version ---------------
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

            */
        }else{

            $idPersonAuthor = $idPerson;
        }

        //CREATE REQUEST CODE
        $codeRequest = $this->_createRequestCode();


        $rsRules = $dbRules->getRule($idItem, $idService);
        $numRules = $rsRules->RecordCount();

        // If have approver, set status as repassed, else set to New
        ($numRules > 0) ? $idStatus = 2 : $idStatus = 1;

        $dbTicket->BeginTrans();

        $idPriority = $this->_getPriority($idPerson, $idService);

        if ( $this->_checkVipUser($idPerson) == 1 &&  $this->_checkVipPriority() == 1) {
            $idPriority = $this->_getVipPriority();
        } else {
            $idPriority = $this->_getServicePriority($idService);
        }



        $insertDate = date("Y-m-d");
        $insertHour = date("H:i");
        $startDate = $insertDate." ".$insertHour;
        $expireDate = $this->_getExpireDate($startDate, $idPriority, $idService);

        $dbTicket->BeginTrans();
        $rs = $dbTicket->insertRequest($idPersonAuthor,$idSource,$startDate,$idType,$idItem,$idService,$idReason,$idWay,$subject,$description,$numOs,$idPriority,$numTag,$numSerial,$idCompany,$expireDate,$idPerson,$idStatus,$codeRequest);
        if(!$rs){
            $dbTicket->RollbackTrans();
            return false;
        }

        $idGroup = $dbTicket->getServiceGroup($idService);
        if(!$idGroup){
            $dbTicket->RollbackTrans();
            return false;
        }
        $dbGroup = new groups_model();

        if ($numRules > 0) { // If have one approver for this service
            $count = 1;
            $values = '';
            while (!$rsRules->EOF) {
                if($rsRules->fields['order'] == 1)
                    $idPersonApprover = $rsRules->fields['idperson'];
                $values .= "(".$rsRules->fields['idapproval'].",". $codeRequest .",". $rsRules->fields['order'] .",". $rsRules->fields['idperson'] .",". $rsRules->fields['fl_recalculate'] .")";
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
                        $rs2 = $dbTicket->insertRequestCharge($codeRequest, $idGroup_2, 'G', '0');
                    else
                        $rs2 = $dbTicket->insertRequestCharge($codeRequest, $idGroup, 'G', '0');

                } else{
                    $rs2 = $dbTicket->insertRequestCharge($codeRequest, $idGroup, 'G', '0');
                }

                $rs = $dbTicket->insertRequestCharge($codeRequest, $idPersonApprover, 'P', '1');

                if(!$rs || !$rs2){
                    //if($this->log)  $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $dbTicket->RollbackTrans();
                    return false;
                }
            } else {
                //if($this->log)  $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                $dbTicket->RollbackTrans();
                return false;
            }
        } else {

            $rsOnlyRep = $dbGroup->checkGroupOnlyRepass($idGroup);
            if($rsOnlyRep->fields['repass_only'] == "Y"){
                $rsNewIdGroup = $dbGroup->getNewGroupOnlyRepass($idGroup,$_SESSION['SES_COD_EMPRESA']);
                $idGroup_2 = $rsNewIdGroup->fields['idperson'];

                if($idGroup_2)
                    $rs = $dbTicket->insertRequestCharge($codeRequest, $idGroup_2, 'G', '1');
                else
                    $rs = $dbTicket->insertRequestCharge($codeRequest, $idGroup, 'G', '1');

                if(!$rs){
                    // if($this->log)   $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    $dbTicket->RollbackTrans();
                    return false;
                }

            } else{

                $rs = $dbTicket->insertRequestCharge($codeRequest, $idGroup, 'G', '1');

                if(!$rs){
                    $dbTicket->RollbackTrans();
                    //if($this->log)
                    //    $this->logIt("Insert ticket # ". $codeRequest . ' - User: '.$idPerson.' - program: '.$this->program ,3,'general',__LINE__);
                    return false;
                }
            }
        }



        //
        if($typePerson == 3){
            // $ret = $this->dbTicket->insertRequestTimesNew($codeRequest,0,0,$minExpendedTime,$minTelephoneTime,0);
        }else{
            $ret = $dbTicket->insertRequestTimesNew($codeRequest);
        }

        if(!$ret){
            $dbTicket->RollbackTrans();
            //if($this->log)                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $ret = $dbTicket->insertRequestDate($codeRequest);
        if(!$ret){
            $dbTicket->RollbackTrans();
            //if($this->log)                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $rs = $dbTicket->insertRequestLog($codeRequest, date("Y-m-d H:i:s"), $idStatus, $idPerson);
        if(!$rs){
            $dbTicket->RollbackTrans();
            // if($this->log)    $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);

            return false;
        }

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


        $note = "<p><b>".html_entity_decode($this->_getLangVar('Request_opened'), ENT_COMPAT, 'UTF-8')."</b></p>";

        $ret = $dbTicket->insertNote($codeRequest, $idPerson, $description, "NOW()", $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, 'NULL' );
        if(!$ret){
            $dbTicket->RollbackTrans();
            // if($this->log)                $this->logIt("Insert ticket # ". $code_request . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        /*
         * Quando for atendente
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
        *
        */


        $dbTicket->CommitTrans();

        $aReturn = array(
            "coderequest" => $codeRequest,
            "expire" => $this->formatDateHour($expireDate),
            "incharge" => $this->_inchargeName($codeRequest)
        );

        return $aReturn;

        die();

        /*
         *
         */
        if ($this->_getEspecificValueSession('SEND_EMAILS') == '1') {
            $cron =  $this->_getEspecificValueSession('EM_BY_CRON')  ;
            if($cron) $dbCron = new request_model();
            if ($cron) {
                $dbCron->saveEmailCron($codeRequest, 'record') ;
            } else {
                $hdkCommon = new hdkCommon();
                $hdkCommon->_sendEmail('record', $codeRequest);
            }
        }

        /*
        *
        */



    }

    private function _createRequestCode(){

        $dbTicket = new ticket_model();
        $dbTicket->BeginTrans();

        $rsCode = $dbTicket->getCode();
        if(!$rsCode){
            $dbTicket->RollbackTrans();
            return false;
        }
        // Count month code
        $rsCountCode = $dbTicket->countGetCode();
        if(!$rsCountCode){
            $dbTicket->RollbackTrans();
            return false;
        }
        // If have code request
        if ($rsCountCode->fields['total']) {
            $code_request = $rsCode->fields["cod_request"];
            // Will increase the code of request
            $rsIncressCode = $dbTicket->increaseCode($code_request);
            if(!$rsIncressCode){
                $dbTicket->RollbackTrans();
                return false;
            }
        }
        else {
            //If not have code request will create a new
            $code_request = 1;
            $rsCreateCode = $dbTicket->createCode($code_request);
            if(!$rsCreateCode){
                $dbTicket->RollbackTrans();
                return false;
            }
        }

        $code_request = str_pad($code_request, 6, '0', STR_PAD_LEFT);
        $code_request = date("Ym") . $code_request;
        $dbTicket->CommitTrans();
        return $code_request;
    }

    public function _assumeRequest($arrRequest)
    {
        $db = new operatorview_model();
        $db->BeginTrans();

        $idStatus    = '3';
        $idPerson    = $arrRequest['idperson'];
        $codeRequest = $arrRequest['code'];
        $grpview     = $arrRequest['grpview'];

        $inslog = $db->changeRequestStatus($idStatus, $codeRequest, $idPerson);
        if (!$inslog) {
            $db->RollbackTrans();
            return false;
        }

        $ipAddress  = $this->_getIpAddress();
        $callback   = '0';
        $public     = '1';
        $date       = 'now()';
        $idType     = '3'; // note
        $note       = '<p><b>' . $this->_getLangVar('Request_assumed') . '</b></p>'; // Text note

        $insNote = $db->insertNote($codeRequest, $idPerson, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idType, $ipAddress, $callback, 'null');
        if (!$insNote) {
            $db->RollbackTrans();
            return false;
        }

        // Track for group
        /*
        if($grpview == 1){
            if($typeIncharge == "P"){
                $trackGroup = $db->insertInCharge($codeRequest, $groupAssume, 'G', 'N', '0', '1');
            }elseif($typeincharge == "G"){
                $trackGroup = $bd->insertInCharge($code, $incharge, 'G', 'N', '0', '1');
            }
            if (!$trackGroup) {
                $bd->RollbackTrans();
                if($this->log)
                    $this->logit("[".date($this->getPrintDate())."]" . " File: " . __FILE__ . " - Assume request # ".$code." - Line: " . __LINE__ .' - Error inserInCharge' , $this->logfile);
                return false;
            }
        }
        */

        $type   = "P"; //TIPO PESSOA
        $rep    = 'N'; //NÃO É REPASS
        $ind    = '1'; //RESPONSAVEL ATUAL

        //RETIRA O RESPONSÁVEL DA SOLICITAÇÃO ANTES DE ADICIONAR O NOVO
        $removeInCharge = $db->removeIncharge($codeRequest);
        if (!$removeInCharge) {
            $db->RollbackTrans();
            return false;
        }

        //ADICIONA O NOVO RESPONSÁVEL
        $insInCharge = $db->insertInCharge($codeRequest, $idPerson, $type, $rep, $ind, '0');
        if (!$insInCharge) {
            $db->RollbackTrans();
            return false;
        }

        $changeStat = $db->updateReqStatus($idStatus, $codeRequest);
        if (!$changeStat) {
            $db->RollbackTrans();
            return false;
        }

        $getEntryDate = $db->getEntryDate($codeRequest);

        $MIN_OPENING_TIME = $this->_difDate($getEntryDate,date("Y-m-d H:i"));

        $data = array("MIN_OPENING_TIME" => $MIN_OPENING_TIME);
        $uptimes = $db->updateTime($codeRequest, $data);

        if (!$uptimes) {
            $db->RollbackTrans();
            return false;
        }

        /*
        $ud = $bd->updateDate($code, "assume_date");
        if(!$ud){
            $bd->RollbackTrans();
            return false;
        }
        */

        $cron =  $this->_getEspecificValueSession('EM_BY_CRON') ;
        if($cron) $dbCron = new request_model();

        if ($this->_getEspecificValueSession('SEND_EMAILS') == '1' && $this->_getEspecificValueSession('NEW_ASSUMED_MAIL')) {
            if ($cron) {
                $dbCron->saveEmailCron($codeRequest, 'assume') ;
            } else {
                $hdkCommon = new hdkCommon();
                $hdkCommon->_sendEmail('assume', $codeRequest);
            }
        }

        $db->CommitTrans();

        return true;

    }

    public function _getPriority($idPerson, $idService)
    {
        $dbTicket = new ticket_model();
        $rsUsuarioVip = $dbTicket->checksVipUser($idPerson);
        if ($rsUsuarioVip->fields['rec_count'] == 1) {
            $rsPrioridadeVip = $dbTicket->checksVipPriority();
            $idPriority = $rsPrioridadeVip->fields["idpriority"];
        } else {
            $rsService = $dbTicket->getServPriority($idService);
            $idPriority = $rsService->fields['idpriority'];
            if (!$idPriority) {
                $rsPrio = $dbTicket->getDefaultPriority();
                $idPriority   = $rsPrio->fields["idpriority"];
            }

        }
        if ($idPriority){
            return $idPriority;
        } else {
            return false ;
        }


    }

    function _checkVipUser($idPerson)
    {
        $dbTicket = new ticket_model();
        $rsVipuser = $dbTicket->checksVipUser($idPerson);
        if ($rsVipuser->fields['rec_count'] > 0)
            return true;
        else
            return false;

    }

    function _getVipPriority()
    {
        $dbTicket = new ticket_model();
        $rsVipPriority = $dbTicket->getVipPriority();
        return $rsVipPriority->fields['idpriority'];
    }

    function _getServicePriority($idService)
    {
        $dbTicket = new ticket_model();
        $rsServicePrio = $dbTicket->getServPriority($idService);
        $idPriority = $rsServicePrio->fields['idpriority'];
        if(!$idPriority){
            $rsDefault = $dbTicket->getDefaultPriority();
            $idPriority = $rsDefault->fields['idpriority'];
        }

        return $idPriority;
    }

    private function _getExpireDate($startDate = null, $idPriority = null, $idService = null){

        $days_sum = '';


        if(!isset($startDate)){$startDate = date("Y-m-d H:i:s");}

        $dbExpire = new expiredate_model();
        $dbExpire->BeginTrans();

        if(isset($idService)){
            $idcompany = $dbExpire->getIdCustumerByService($idService);

            $getExpireDateService = $dbExpire->getExpireDateService($idService);
            if(!$getExpireDateService){
                $dbExpire->RollbackTrans();
                return false;
            }
            $days = $getExpireDateService->fields['days_attendance']; //NUM DE DIAS DO PRAZO
            $time = $getExpireDateService->fields['hours_attendance']; //QUANTIDADE DE TEMPO
            $type_time = $getExpireDateService->fields['ind_hours_minutes']; //TIPO DO TEMPO H = HORAS | M = MINUTOS

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

        //SE TEM O CODIGO DA PRIORIDADE E O TEMPO E DIAS DO SERVIÇO FOREM 0
        if(isset($idPriority) && $time == 0 && $days == 0){
            $getExpireDatePriority = $dbExpire->getExpireDatePriority($idPriority);
            if(!$getExpireDatePriority){
                $dbExpire->RollbackTrans();
                return false;
            }
            $days = $getExpireDatePriority->fields['limit_days']; //NUM DE DIAS DO PRAZO
            $time = $getExpireDatePriority->fields['limit_hours']; //QUANTIDADE DE TEMPO

            if($days > 0){
                $days_sum = "+".$days." days";
            }
            if($time > 0){
                $time_sum = "+".$time." hour";
            }
        }

        //SE O TEMPO E O DIA CONTINUAREM 0 MESMO DEPOIS DE BUSCAR NO SERVIÇO E NA PRIORIDADE DEFINIDO 1 COMO PADRÃO
        if($time == 0 && $days == 0){
            $days_sum = "+0 day";
            $time_sum = "+0 hour";
            return $startDate;
        }

        //SOMA O TEMPO DE ATENDIMENTO DETERMINADO PELO SERVIÇO OU PRIORIDADE OU PADRÃO
        $data_sum = date("Y-m-d H:i:s",strtotime($startDate." ".$days_sum." ".$time_sum));

        $date_holy_start = date("Y-m-d",strtotime($startDate)); //SEPARA SOMENTE A DATA INICIAL PARA VERIFICAR SE HÁ FERIADO NO PERÍODO
        $date_holy_end = date("Y-m-d",strtotime($data_sum)); //SEPARA SOMENTE A DATA FINAL PARA VERIFICAR SE HÁ FERIADO NO PERÍODO

        //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
        $getNationalDaysHoliday = $dbExpire->getNationalDaysHoliday($date_holy_start,$date_holy_end);
        if(!$getNationalDaysHoliday){
            $dbExpire->RollbackTrans();
            return false;
        }

        if(isset($idcompany)){
            //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
            $getCompanyDaysHoliday = $dbExpire->getCompanyDaysHoliday($date_holy_start,$date_holy_end,$idcompany);
            if(!$getCompanyDaysHoliday){
                $dbExpire->RollbackTrans();
                return false;
            }
            $sum_days_holidays = $getNationalDaysHoliday->fields['num_holiday'] + $getCompanyDaysHoliday->fields['num_holiday'];
        }else{
            $sum_days_holidays = $getNationalDaysHoliday->fields['num_holiday'];
        }

        //PRAZO COM O ACRÉSCIMO DE FERIADOS
        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$sum_days_holidays." days"));

        //GERA O ARRAY DE DIAS UTEIS DA EMPRESA
        $getBusinessDays = $dbExpire->getBusinessDays();
        if(!$getBusinessDays){
            $dbExpire->RollbackTrans();
            return false;
        }
        while (!$getBusinessDays->EOF) {
            $businessDay[$getBusinessDays->fields['num_day_week']] = array(
                "begin_morning" 	=> $getBusinessDays->fields['begin_morning'],
                "end_morning" 		=> $getBusinessDays->fields['end_morning'],
                "begin_afternoon" 	=> $getBusinessDays->fields['begin_afternoon'],
                "end_afternoon" 	=> $getBusinessDays->fields['end_afternoon']
            );
            $getBusinessDays->MoveNext();
        }

        $date_check_start = date("Y-m-d",strtotime($startDate));
        $date_check_end = date("Y-m-d",strtotime($data_sum));
        $addNotBussinesDay = 0;

        //PEGA A QUANDIDADE DE DIAS NÃO UTEIS
        while (strtotime($date_check_start) <= strtotime($date_check_end)) {
            $numWeek = date('w',strtotime($date_check_start));
            if (!array_key_exists($numWeek, $businessDay)) {
                $addNotBussinesDay++;
            }
            $date_check_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_check_start)));
        }
        //PRAZO SOMADO COM OS DIAS NÃO UTEIS
        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$addNotBussinesDay." days"));
        //VALIDA SE O DIA É UM DIA ÚTIL E NAO É FERIADO
        $data_check_bd = $this->_checkValidBusinessDay($data_sum,$businessDay,$idcompany);
        //VALIDA SE A HORA ESTÁ NO INTERVALO DE ATENDIMENTO
        $data_sum = $this->_checkValidBusinessHour($data_check_bd,$businessDay);
        //CASO MUDE O DIA COM O ACRÉSCIMO DA HORA SERÁ CHECADO NOVAMENTE SE O DIA É VALIDO
        if(strtotime(date("Y-m-d",strtotime($data_check_bd))) != strtotime(date("Y-m-d",strtotime($data_sum)))){
            $data_check_bd = $this->_checkValidBusinessDay($data_sum,$businessDay,$idcompany);
            return $data_check_bd;
        }else{
            return $data_sum;
        }

    }

    private function _checkValidBusinessDay($date,$businessDay,$idcompany = null){
        $db = new expiredate_model();
        $numWeek = date('w',strtotime($date));
        $i = 0;
        while($i == 0){
            while (!array_key_exists($numWeek, $businessDay)) {
                $date = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));
                $numWeek = date('w',strtotime($date));
            }
            $date_holy = date("Y-m-d",strtotime($date));

            //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
            $getNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy,$date_holy);
            if(!$getNationalDaysHoliday){
                $db->RollbackTrans();
                return false;
            }

            if(isset($idcompany)){
                // Verifica a quiantidade de feriados específicos da empresa no período
                $getCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy,$date_holy,$idcompany);
                if(!$getCompanyDaysHoliday){
                    $db->RollbackTrans();
                    return false;
                }
                $daysHoly = $getNationalDaysHoliday->fields['num_holiday'] + $getCompanyDaysHoliday->fields['num_holiday'];
            }else{
                $daysHoly = $getNationalDaysHoliday->fields['num_holiday'];
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

    public function _checkValidBusinessHour($date,$businessDay)
    {
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

    public function _difDate($start, $end){
        $StartDate  = getdate(strtotime($start));
        $EndDate    = getdate(strtotime($end));
        $Dif        = ($EndDate[0] - $StartDate[0]) / 60;
        return number_format($Dif, 0, '', '');
    }

    public function _setDeploy($arrGit)
    {
        $db = new features_model();

        $ret = $db->setDeploy($arrGit['server'],$arrGit['state'],$arrGit['created_on']);
        return ($ret) ?  true :  false;
    }

    /**
     * Method to create random passwords
     *
     * @author Thiago Belem <contato@thiagobelem.net>
     *
     * @param integer $tamanho Size of the new password
     * @param boolean $maiusculas If it will have capital letters
     * @param boolean $numeros If it will have numbers
     * @param boolean $simbolos  If it will have symbols
     *
     * @return string A senha gerada
     */
    public function _generateRandomPassword($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;
        if ($simbolos) $caracteres .= $simb;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++)
        {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }
}

?>
