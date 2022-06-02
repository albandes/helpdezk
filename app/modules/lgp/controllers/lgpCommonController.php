<?php
    
require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

if(class_exists('Controllers')) {
    class DynamicLgpCommon extends Controllers {}
} elseif(class_exists('cronController')) {
    class DynamicLgpCommon extends cronController {}
} elseif(class_exists('apiController')) {
    class DynamicLgpCommon extends apiController {}
}

class lgpCommon extends DynamicLgpCommon {

    public static $_logStatus;

    public function __construct(){

        parent::__construct();

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
                $this->_logFacility = $objSyslog->setLogFacility();
        }

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        //
        $this->modulename = 'LGPD';

        $id = $this->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->loadModel('holdertype_model');
        $this->dbHolderType = new holdertype_model();

        $this->loadModel('lgpdatamapping_model');
        $this->dbDataMapping = new lgpdatamapping_model();

        $this->loadModel('admin/person_model');
        $this->dbPerson = new person_model();

        $this->loadModel('dporequest_model');
        $this->dbDPORequest = new dporequest_model();

        $this->loadModel('lgpemailconfig_model');
        $this->dbEmailConfig = new lgpemailconfig_model();

        $retDPOID = $this->_getDPOID();
        $this->DPOID = $retDPOID['success'] ? $retDPOID['id'] : false;
        $this->DPOEmail = $retDPOID['success'] ? $retDPOID['email'] : false;

    }

    public function _makeNavLgp($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idPerson,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        $smarty->assign('listMenu_1',$listRecords);

        // Set Header Logo
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogos']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);

    }

    public function _comboHolderType($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getHolderType($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idtipotitular'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboType($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getType($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idtipodado'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboPurpose($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getPurpose($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idfinalidade'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboFormat($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getFormat($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idformatocoleta'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboCollectForm($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getCollectForm($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idformacoleta'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboLegalGround($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getLegalGround($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idbaselegal'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboStorage($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getStorage($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idarmazenamento'];
            $values[]   = $v['nome'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboPerson($where=null,$order=null,$group=null,$limit=null,$addType=null)
    {
        
        $rs = $this->dbDataMapping->getPersonAccess($where,$order,$group,$limit,$addType);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $aGroup = array();
        
        foreach($rs['data'] as $k=>$v){
            $idx = $v['type'] == 'G' ? $this->getLanguageWord('pgr_lgpgroup') : $this->getLanguageWord('lbl_people');
            
            $aGroup[$idx]["{$v['idperson']}|{$v['type']}"] = $v['name'];
            $fieldsID[] = $v['idperson'];
            $values[]   = $v['name'];
        }

        $arrRet['opts'] = $aGroup;
        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboSharedWith($where=null,$order=null,$group=null,$limit=null)
    {
        $rs = $this->dbDataMapping->getSharedWith($where,$order,$group,$limit);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        foreach($rs['data'] as $k=>$v){
            //echo "{$v}<br>";
            $fieldsID[] = $v['idperson'];
            $values[]   = $v['name'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _setFolder($path)
    {
        if(!is_dir($path)) {
            $this->logIt('Directory: '. $path.' does not exists, I will try to create it. - program: '.$this->program ,6,'general',__LINE__);
            if (!mkdir ($path, 0777 )) {
                $this->logIt('I could not create the directory: '.$path.' - program: '.$this->program ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_create')}: {$path}");
            }

        }

        if (!is_writable($path)) {
            $this->logIt('Directory: '. $path.' Is not writable, I will try to make it writable - program: '.$this->program ,6,'general',__LINE__);
            if (!chmod($path,0777)){
                $this->logIt('Directory: '.$path.'Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_writable')}: {$path}");
            }
        }

        return array("success"=>true,"path"=>$path);
    }

    public function _comboCompanies()
    {
        $rs = $this->dbPerson->getErpCompanies("WHERE idtypeperson IN (4) AND status = 'A'","ORDER BY name ASC");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcompany'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _getNoteAttMaxFiles()
    {
        if (version_compare($this->helpdezkVersionNumber, '1.0.1', '>' )) {
            return 5;
        } else {
            return 1;
        }
    }

    public function _getNoteAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    public function _getTicketAttMaxFiles()
    {
        if (version_compare($this->helpdezkVersionNumber, '1.0.1', '>' )) {
            return 10;
        } else {
            return 1;
        }
    }

    public function _getTicketAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    public function _getTicketAttMaxFileSize()
    {
        return ini_get(upload_max_filesize);
    }

    public function _comboRequester($where=null,$order=null,$limit=null,$group=null)
    {
        $rs = $this->dbDPORequest->getRequester($where,$order,$limit,$group);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idrequester'];
            $values[]   = $v['name'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _inchargeName($code_request)
    {
        $rsInCharge = $this->dbDPORequest->getInCharge($code_request);
        if(!$rsInCharge['success']){
            if($this->log)
                $this->logIt("Can't get in charge ticket # {$code_request} - {$rsInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        return $rsInCharge['data'][0]['name'];
    }

    public function  _sendNotification($arrayParam)
    {

        $transaction    = $arrayParam['transaction'] ;
        $midia          = $arrayParam['media'] ;
        $code_request   = $arrayParam['code_request'] ;

        if ($midia == 'email'){
            $cron = false;
            $smtp = false;
        }

        $this->logIt(__FUNCTION__ .' - entrou : ' . $code_request . ' - ' . $transaction . ' - ' . $midia ,7,'general');

        switch($transaction){
            // Send email to the attendant, or group of attendants, when a request is forwarded
            case 'forward-ticket':
                if ($midia == 'email') {
                    if ($midia == 'email') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'forward-ticket';
                        $messagePart = 'Pass the request # ';
                    }
                }
                break;

            // Sends notification to user when the request receives a note, created by operator
            case 'user-note' :
                if ($midia == 'email') {
                    if ( $_SESSION['EM_BY_CRON'] == '1') {
                        $cron = true;
                    } else {
                        $smtp =  true;
                    }
                    $messageTo   = 'user-note';
                    $messagePart = 'Add note in request # ';
                }
                break ;

            // Sends notification to operator when the request receives a note, created by user
            case 'operator-note':
                if ($midia == 'email') {
                    if ( $_SESSION['EM_BY_CRON'] == '1') {
                        $cron = true;
                    } else {
                        $smtp =  true;
                    }
                    $messageTo   = 'operator-note';
                    $messagePart = 'Add note in request # ';
                }
                break;

            // Send notification to the attendant, or group of attendants, when a request is reopened by user
            case 'reopen-ticket':
                if ($midia == 'email') {
                    if ( $_SESSION['EM_BY_CRON'] == '1') {
                        $cron = true;
                    } else {
                        $smtp =  true;
                    }

                    $messageTo   = 'reopen-ticket';
                    $messagePart = 'Reopen request # ';
                }
                break;

            // Send notification to the attendant, or group of attendants, when a request is evaluated by user
            case 'evaluate-ticket':
                if($midia == 'email'){
                    if ( $_SESSION['EM_BY_CRON'] == '1') {
                        $cron = true ;
                    } else {
                        $smtp = true;
                    }

                    $messageTo   = 'evaluate-ticket';
                    $messagePart = 'Evaluate request # ';
                }
                break;

            // Sends notification to user, when a request is closed by attendant.
            case 'finish-ticket':
                if($midia == 'email'){
                    if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                        $cron = true;
                    } else {
                        $smtp = true;
                    }

                    $messageTo   = 'finish-ticket';
                    $messagePart = 'Closed request # ';
                }
                break;

            // Sends notification to the user when a request is assumed
            case 'operator-assume':
                if($midia == 'email'){
                    if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                        $cron = true;
                     } else {
                         $smtp = true;
                     }

                     $messageTo   = 'operator-assume';
                     $messagePart = 'Assumed request # ';
                }
                break;

            // Sends notification to the user when a request is rejected
            case 'operator-reject':
                if($midia == 'email'){
                    if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                        $cron = true;
                    } else {
                        $smtp = true;
                    }
                    $messageTo   = 'operator-reject';
                    $messagePart = 'Rejected request # ';
                }
                break;

            // Sends a notification to the operator or group of operators when a request is opened
            case 'new-ticket-user':
                if($midia == 'email'){
                    if ( $_SESSION['EM_BY_CRON'] == '1' ) {
                        $cron = true;
                    } else {
                        $smtp = true;
                    }
                    $messageTo   = 'new-ticket-user';
                    $messagePart = 'Inserted request # ';
                }
                break;
            
            default:
                return false;
        }


        if ($midia == 'email') {
            if ($cron) {
                $retCron = $this->dbDPORequest->saveEmailCron($this->idmodule,$code_request,$transaction );
                if(!$retCron['success']){
                    if($this->log)
                        $this->logIt($retCron['message'],3,'general');
                }else{
                    if($this->log)
                        $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail by cron' ,6,'general');
                }
            } elseif($smtp){

                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail' ,6,'general');

                $this->_sendEmail($messageTo , $code_request);
            }

        }

        return true ;
    }

    public function _comboRepassListHtml($type,$where=null,$order=null,$limit=null,$group=null)
    {

        $aRepass = $this->_comboRepassUsers($type,$where,$order,$limit,$group);
        $select = '';
        foreach ($aRepass['ids'] as $indexKey => $indexValue ){
            if ($aRepass['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$aRepass['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function _comboRepassUsers($type,$where=null,$order=null,$limit=null,$group=null)
    {
        switch($type){
            case "group":
                $rs = $this->dbDPORequest->getRepassGroups($where,$order,$limit,$group);
                break;
            default:
                $rs = $this->dbDPORequest->getRepassUsers($where,$order,$limit,$group);
                break;
        }

        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idperson'];
            $values[]   = strip_tags($v['name']);
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $fieldsID[0];

        return $arrRet;
    }

    public function _editRequest($codeRequest)
    {
        return substr($codeRequest,0,4).'-'.substr($codeRequest,4,2).'.'.substr($codeRequest,6,6);
    }

    public function _makeTicketAttachList($ticketCode)
    {
        $ret = $this->dbDPORequest->getTicketAttachs("WHERE code_request = {$ticketCode}");

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("{$ret['message']}\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if (count($ret['data']) > 0) {
            $hasAttach = 1;
            foreach($ret['data'] as $key=>$val) {
                $filename = $val['file_name'];
                $ext = strrchr($filename, '.');
                $idAttach = $val['idrequest_attachment'];
                $attach[$filename] = "<button type='button' class='btn btn-default btn-xs' id='{$idAttach}' onclick='download({$idAttach},\"request\")'><span class='fa fa-file-alt'></span>&nbsp;{$filename}</button>";
            }
            
            $attach = implode(" ", $attach);
        } else {
            $hasAttach = 0;
        }
        
        return array(
            "hasAttach" => $hasAttach,
            "attach" => $attach
        );
    }

    public function _comboOperatorGroups($personID)
    {
        $rs = $this->dbDPORequest->getOperatorGroups($personID);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idperson'];
            $values[]   = strip_tags($v['name']);
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboTypeNote($where=null,$order=null,$limit=null,$group=null)
    {
        $rs = $this->dbDPORequest->getTypeNote($where,$order,$limit,$group);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("{$rs['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idtypenote'];
            $values[]   = $this->getLanguageWord($v['description']);
        }
        
        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        
        return $arrRet;
    }

    public function _saveNote($aParam)
    {
        
        $idPerson        = $_SESSION['SES_COD_USUARIO'];
        $codeRequest     = $aParam['code_request'];
        $noteContent     = $aParam['notecontent'];

        $public     = $aParam['public'];
        $typeNote   = $aParam['typenote'];
        $flgopen = $aParam['flgopen'] ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ins = $this->dbDPORequest->insertNote($codeRequest,$idPerson,$noteContent,$this->databaseNow,$public,$typeNote,$flgopen);
        if(!$ins['success']){
            if($this->log)
                $this->logIt("Can't insert note ticket # {$codeRequest} - {$ins['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        $idNote = $ins['id'];

        return $idNote;

    }

    public function _sendEmail($operation,$code_request,$reason=NULL) {

        if (!isset($operation)) {
            if($this->log)
                $this->logIt("Email code not provided !!! - Program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        $sentTo = "";
        $arrAttach = array();

        // Common data
        $entry_date = " DATE_FORMAT(a.dtentry, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_entry_date" ;
        $rsReqData = $this->dbDPORequest->getTickets($entry_date,"WHERE a.code_request = '$code_request'");
        if(!$rsReqData['success']){
            if($this->log)
                $this->logIt("Can't get ticket data, # {$code_request}. - {$rsReqData['message']} - Program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        $REQUEST        = $code_request;
        $SUBJECT        = $rsReqData['data'][0]['subject'];
        $REQUESTER      = $rsReqData['data'][0]['owner_name'];
        $RECORD         = $this->formatDate($rsReqData['data'][0]['dtentry']);
        $DESCRIPTION    = $rsReqData['data'][0]['description'];
        $INCHARGE       = $rsReqData['data'][0]['in_charge_name'];
        //$LINK_OPERATOR  = $this->makeLinkOperator($code_request);
        //$LINK_USER      = $this->makeLinkUser($code_request);
        // Notes
        $table          = $this->makeNotesTable($code_request);
        $NT_OPERATOR    = $table;

        switch ($operation) {

            //Sends a email to the data holder and DPO when a request is opened
            case "new-ticket-user":
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("NEW_REQUEST_OPERATOR_MAIL");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($code_request);
                $sentTo .= (!$sentTo) ? $rsReqData['data'][0]['owner_email'] : ";{$rsReqData['data'][0]['owner_email']}";

                break;

            // Sends email to the user when a request is assumed
            case 'operator-assume':
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("NEW_ASSUMED_MAIL");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                if($rsReqData['data'][0]['idstatus'] == 3 && $rsReqData['data'][0]['id_in_charge'] != $this->DPOID){
                    $sentTo = $this->setSendTo($code_request," OR ind_track = 1");
                    $sentTo .= (!$sentTo) ? "{$this->DPOEmail}" : ";{$this->DPOEmail}";
                    $public = true;
                }else{
                    $sentTo = $rsReqData['data'][0]['owner_email'];
                    $public = false;
                }

                $date = date('Y-m-d H:i');
                $ASSUME = $this->formatDate($date);
                $NT_USER = $this->makeNotesTable($code_request,$public);

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                break;

            // Sends email to the user, when a request is closed by the attendant.
            case 'finish-ticket':
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("FINISH_MAIL");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                $sentTo = $rsReqData['data'][0]['owner_email'];
                //$typeuser = $reqEmail->fields['idtypeperson'];

                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);

                /*$this->loadModel('evaluation_model');
                $ev = new evaluation_model();
                $tk = $ev->getToken($code_request);
                $token = $tk->fields['token'];
                if($token)
                    $LINK_EVALUATE =  $this->helpdezkUrl."/helpdezk/evaluate/index/token/".$token;*/

                $table = $this->makeNotesTable($code_request,false);
                $NT_USER = $table;

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                break;

            case 'operator-reject':
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("REJECTED_MAIL");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                $sentTo = $rsReqData['data'][0]['owner_email'];

                $typeuser = $reqEmail->fields['idtypeperson'];

                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                //$LINK_OPERATOR = $this->makeLinkOperator($code_request);

                /*if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);*/

                $NT_USER = $this->makeNotesTableUser($code_request);

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                break;

             // Sends email to user when the request receives a note
            case 'user-note' :
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("USER_NEW_NOTE_MAIL");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                //
                $FINISH_DATE = $this->formatDate(date('Y-m-d H:i'));
                /*$LINK_OPERATOR = $this->makeLinkOperator($code_request);

                $typeuser = $reqEmail->fields['idtypeperson'];

                if($typeuser == 2)
                    $LINK_USER = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);*/
                
                $table = $this->makeNotesTableUser($code_request);
                $NT_USER = $table;

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $rsReqData['data'][0]['owner_email'];

                break;

            // Sends email to operator when the request receives a note
            case 'operator-note' :
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("OPERATOR_NEW_NOTE");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                //$reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                //$typeuser = $reqEmail->fields['idtypeperson'];

                $FINISH_DATE = $this->formatDate(date('Y-m-d H:i'));
                /*$LINK_OPERATOR = $this->makeLinkOperator($code_request);
                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);*/

                $table = $this->makeNotesTable($code_request);
                $NT_USER = $table;

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($code_request);
    

                break;

            // Send email to the attendant, or group of attendants, when a request is reopened by user
            case 'reopen-ticket':

                $templateId = $dbEmailConfig->getEmailIdBySession("REQUEST_REOPENED");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }

                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

                break;

            // Send email to the attendant, or group of attendants, when a request is evaluated by user
            case "evaluate-ticket":

                $templateId = $dbEmailConfig->getEmailIdBySession("EM_EVALUATED");

                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }

                }

                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

                break;

            // Send email to the attendant, or group of attendants, when a request is forwarded
            case "forward-ticket":
                $rsTemplate = $this->dbEmailConfig->getTemplateBySession("REPASS_REQUEST_OPERATOR_MAIL");
                if(!$rsTemplate['success']){
                    if($this->log)
                        $this->logIt("Can't get template data,  ticket # {$code_request}. - {$rsTemplate['message']} - Program: {$this->program} - Method: ". __METHOD__ ,7,'general',__LINE__);
                }

                $contents = str_replace('"', "'",$rsTemplate['data'][0]['template_body']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate['data'][0]['template_name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($code_request," OR ind_track = 1");
                
                break;

            case "approve":
                $templateId = $dbEmailConfig->getEmailIdBySession("SES_REQUEST_APPROVE");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                //$bdop = new operatorview_model();
                $reqdata = $this->dbTicket->getRequestData("WHERE code_request = $code_request");

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;

            // Sends email to the user when a request is rejected
            case "operator_reject":
                $templateId = $dbEmailConfig->getEmailIdBySession("SES_MAIL_OPERATOR_REJECT");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($_SESSION['hdk']['SES_MAIL_OPERATOR_REJECT_ID']);
                while (!$grpEmails->EOF) {
                    if (!$sentTo) {
                        $sentTo = $grpEmails->Fields('email');
                    } else {
                        $sentTo .= ";" . $grpEmails->Fields('email');
                    }
                    $grpEmails->MoveNext();
                }

                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;

        }


        $customHeader = 'X-lgpRequest: '. $REQUEST;

        $msgLog = "request # ".$REQUEST." - Operation: ".$operation;
        $msgLog2 = "request # ".$REQUEST;

        $params = array("subject"       => $subject,
                        "contents"      => $contents,
                        "address"       => $sentTo,
                        "attachment"    => $arrAttach,
                        "idmodule"      => $this->idmodule,
                        "tracker"       => $this->tracker,
                        "msg"           => $msgLog,
                        "msg2"          => $msgLog2,
                        "customHeader"  => $customHeader,
                        "code_request"  => $REQUEST);


        $done = $this->sendEmailDefault($params);

        if (!$done) {
            return false ;
        } else {
            return true ;
        }

    }

    /**
     * Set recipient's e-mail address to send the e-mail
     *
     * @param  mixed  $code_request Ticket ID
     * @param  string $flgTrack     SQL statement to get all those who follow up on the request
     * @return void
     */
    function setSendTo($code_request,$flgTrack=null)
    {
        $sentTo = '';

        $rsInCharge = $this->dbEmailConfig->getInCharge($code_request,$flgTrack);
        if(!$rsInCharge['success']){
            if($this->log)
                $this->logIt("Can't get in charge data,  ticket # {$code_request}. - {$rsInCharge['message']} - Program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        foreach($rsInCharge['data'] as $key=>$val){
            
            $inchType = $val['type'];
            $inchid = $val['id_in_charge'];
    
            if ($inchType == 'G') {                
                $grpEmails = $this->dbEmailConfig->getEmailsfromGroupOperators($inchid);
                if(!$grpEmails['success']){
                    if($this->log)
                        $this->logIt("Can't get in charge data [group],  ticket # {$code_request}. - {$grpEmails['message']} - Program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
                }
                //echo "",print_r($grpEmails,true), "\n";
                foreach($grpEmails['data'] as $k=>$v){
                    $sentTo .= (!$sentTo) ? $v['email'] : ";{$v['email']}";
                }
            } else {
                $userEmail = $this->dbEmailConfig->getUserEmail($inchid);
                if(!$userEmail['success']){
                    if($this->log)
                        $this->logIt("Can't get in charge data,  ticket # {$code_request}. - {$userEmail['message']} - Program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
                }

                $sentTo .= (!$sentTo) ? $userEmail['data'][0]['email'] : ";{$userEmail['data'][0]['email']}";      
            }
        }

        

        return $sentTo ;
    }

    public function makeNotesTable($code_request,$public=true)
    {
        $rsNotes = $this->dbDPORequest->getTicketNotes($code_request);
        if(!$rsNotes['success']){
            if($this->log)
                $this->logIt("{$rsNotes['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
        foreach($rsNotes['data'] as $key=>$val) {
            if($public){
                $table.= "<tr><td height=28><font size=2 face=arial>";
                $table.= $this->formatDate($val['dtentry']) . " [" . $val["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($val["description"],"<p>"));
                $table.= "</font><br></td></tr>";
            }else{
                if($val['idtypenote'] != '2'){
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($val['dtentry']) . " [" . $val["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($val["description"],"<p>"));
                    $table.= "</font><br></td></tr>";
                }
            }
        }
        $table.= "</table>";
        
        return $table;
    }

    public function _getDPOID(){

        $ret = $this->dbDPORequest->getDPOID();
        if(!$ret['success']) {
            if($this->log)
                $this->logIt("Can't get DPO ID. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"id"=>"");
        }

        return array("success"=>true,"id"=>$ret['data'][0]['idperson'],"email"=>$ret['data'][0]['email']);

    }

    public function makeNotesTableUser($code_request)
    {
        $rsNotes = $this->dbDPORequest->getTicketNotesUser($code_request);
        if(!$rsNotes['success']){
            if($this->log)
                $this->logIt("{$rsNotes['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
        foreach($rsNotes['data'] as $key=>$val) {
            $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($val['dtentry']) . " [" . $val["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($val["description"],"<p>"));
                    $table.= "</font><br></td></tr>";
        }
        $table.= "</table>";
        
        return $table;
    }

}










