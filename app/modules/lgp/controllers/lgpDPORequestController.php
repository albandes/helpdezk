<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpDPORequest extends lgpCommon
{
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


        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'LGPD' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpDPORequest');

        // Set attachments storage local
        $this->saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';
        if($this->saveMode == "aws-s3"){
            $this->requestStoragePath = 'lgp/requestattachs/' ;
            $this->noteStoragePath = 'lgp/noteattachs/' ;
        }elseif($this->saveMode == "disk"){
            if($this->_externalStorage) {
                $this->requestStoragePath = $this->_externalStoragePath.'/lgp/requestattachs/' ;
                $this->noteStoragePath = $this->_externalStoragePath.'/lgp/noteattachs/' ;
            } else {
                $moduleDir = $this->_setFolder($this->helpdezkPath.'/app/uploads/lgp/');
                if(!$moduleDir['success']) {
                    $this->logIt("{$moduleDir['message']}. Program: {$this->program}" ,3,'general',__LINE__);
                }
            
                $targetDir = $this->_setFolder($moduleDir['path'] . "requestattachs/");    
                if(!$targetDir['success']) {
                    $this->logIt("{$targetDir['message']}. Program: {$this->program}" ,7,'general',__LINE__);
                }
                $this->requestStoragePath = isset($targetDir['path']) ? $targetDir['path'] : "";

                $noteDir = $this->_setFolder($moduleDir['path'] . "noteattachs/");    
                if(!$noteDir['success']) {
                    $this->logIt("{$noteDir['message']}. Program: {$this->program}" ,7,'general',__LINE__);
                }
                $this->noteStoragePath = isset($noteDir['path']) ? $noteDir['path'] : "";
            }
        }

        $this->databaseNow = ($this->database == 'oci8po') ? 'sysdate' : 'NOW()' ;

        $this->loadModel('dporequest_model');
        $this->dbDPORequest = new dporequest_model();

        $this->loadModel('lpgreport_model');
        $this->dbReport = new lgpreport_model();
    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $smarty->assign('token', $this->_makeToken());
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('lgp-dporequest-grid.tpl');
    }

    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $smarty = $this->retornaSmarty();

        $where = '';
        $entry_date = " DATE_FORMAT(a.dtentry, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_entry_date" ;

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='a.dtentry';
        if(!$sord)
            $sord ='DESC';

        if ($_POST['_search'] == 'true'){
            $arrSearch = array('.','-','/','_');
            $arrReplace = array('','','','');

            switch($_POST['searchField']){
                case 'a.code_request':
                    $searchField = $_POST['searchField'];
                    $_POST['searchString'] = str_replace($arrSearch,$arrReplace,$_POST['searchString']);
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }
            
            $where .= ($where != '' ? ' AND ' : 'WHERE ') . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
        }

        if(isset($_POST['txtSearch'])){
            $txtSearch = str_replace(' ','%',trim($_POST['txtSearch']));
            $paramW = "(b.name LIKE '%{$txtSearch}%' OR b.cpf LIKE '{$txtSearch}' OR b.email LIKE '{$txtSearch}') ";
            $where .= ($where != '' ? ' AND ' : 'WHERE ') . $paramW;
        }

        $retDPOID = $this->getDPOID(); //Check if the user is the DPO
        if($retDPOID['success'] && $retDPOID['id'] != $_SESSION['SES_COD_USUARIO']){
            $rsGroups = $this->dbReport->getPersonGroups("AND d.idperson = {$_SESSION['SES_COD_USUARIO']} ");
            if (!$rsGroups['success']) {
                if($this->log)
                    $this->logIt("{$rsGroups['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $groupIDs = "";
            foreach($rsGroups['data'] as $key=>$val){
                $groupIDs .= "{$val[idperson]},";
            }

            $groupIDs = substr($groupIDs,0,-1);
            $wGroups = ($groupIDs && $groupIDs =! '') ? " OR (d.id_in_charge IN ({$groupIDs}) AND (d.ind_in_charge = 1 OR d.ind_track = 1))" : "";
            //If the user isn't a DPO, get the tickets where he or his group(s) are in charge
            $where .= ($where != '' ? ' AND ' : 'WHERE ') . "((d.id_in_charge = {$_SESSION['SES_COD_USUARIO']} AND (d.ind_in_charge = 1 OR d.ind_track = 1))$wGroups)";
        }

        $count = $this->dbDPORequest->getTickets($entry_date,$where);
        if(!$count['success']){
            if($this->log)
                $this->logIt("Can't get row's total - {$count['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        
        $totalRows = count($count['data']);
        
        if($totalRows > 0 && $rows > 0) {
            $total_pages = ceil($totalRows/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsTickets = $this->dbDPORequest->getTickets($entry_date,$where,$order,$limit);
        if(!$rsTickets['success']){
            if($this->log)
                $this->logIt("Can't get tickets data - {$rsTickets['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        
        foreach($rsTickets['data'] as $key=>$val){
            
            $aColumns[] = array(
                'id'            => $val['idrequest'],
                'ticket_code'   => $val['code_request'],
                'owner'         => $val['owner_name'],
                'subject'       => strip_tags($val['subject']),
                'dtopen'        => $val['dtentry'],
                'status'        => "<span style='color:{$val['status_color']}'>{$val['status_name']}</span>"
            );
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $totalRows,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function newTicket(){
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $smarty->assign('token', $this->_makeToken());
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        $smarty->assign('ticketattmaxfiles', $this->_getTicketAttMaxFiles()); 
        $smarty->assign('ticketacceptedfiles', $this->_getTicketAcceptedFiles());
        $smarty->assign('hdkMaxSize', substr($this->_getTicketAttMaxFileSize(),0,-1) );

        $smarty->assign('summernote_version', $this->summernote);

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->makeScreenDPORequest($smarty,'','add');

        $smarty->display('lgp-dporequest-add.tpl');
    }

    public function viewTicket()
    {
        $smarty = $this->retornaSmarty();

        $ticketID = $this->getParam('ticketCode');
        $entry_date = " DATE_FORMAT(a.dtentry, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_entry_date" ;

        $rsTicket = $this->dbDPORequest->getTickets($entry_date,"WHERE a.code_request = '$ticketID'");

        $this->makeScreenDPORequest($smarty,$rsTicket['data'],'update');
        $smarty->assign('token', $this->_makeToken()) ;

        $retAttach = $this->_makeTicketAttachList($ticketID);
        if($retAttach){
            $smarty->assign('hasattach', $retAttach['hasAttach']);
            $smarty->assign('attach', $retAttach['attach']);
        }       

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('request_code', $this->_editRequest($ticketID));
        $smarty->assign('hidden_coderequest', $ticketID);

        $smarty->assign('ticketattmaxfiles', $this->_getTicketAttMaxFiles()); 
        $smarty->assign('ticketacceptedfiles', $this->_getTicketAcceptedFiles());
        $smarty->assign('noteattmaxfiles', $this->_getNoteAttMaxFiles()); 
        $smarty->assign('noteacceptedfiles', $this->_getNoteAcceptedFiles());
        $smarty->assign('hdkMaxSize', substr($this->_getTicketAttMaxFileSize(),0,-1) );

        $smarty->assign('summernote_version', $this->summernote);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lgp-dporequest-view.tpl');

    }

    /**
     * Formatação de campos dos formulários Nova Solicitação, Visualizar
     *
     * @param  mixed $objSmarty Objeto do smarty template engine
     * @param  mixed $rs Record set do cadastro selecionado (Visualização)
     * @param  mixed $oper Ação do formulário (add: Novo / update: Editar )
     * @return void
     */
    function makeScreenDPORequest($objSmarty,$rs,$oper){
        
        // --- Solicitante ---
        if ($oper == 'add') {
            $aRequester = $this->_comboRequester();
            $objSmarty->assign('requesterids',  $aRequester['ids']);
            $objSmarty->assign('requestervals', $aRequester['values']);
        }elseif ($oper == 'update' || $oper == 'echo') {
            $owner = !empty($rs[0]['owner_name']) ? $rs[0]['owner_name'] : "";
            $objSmarty->assign('owner',$owner);
        }

        // --- Status ---
        if ($oper == 'update' || $oper == 'echo') {
            if($_SESSION['SES_COD_USUARIO'] == $rs[0]['idowner'] && $_SESSION['SES_COD_USUARIO'] != $rs[0]['id_in_charge']){
                $status = "<span style='color:{$rs[0]['status_color']}'>{$rs[0]['user_view']}</span>";
            }else{
                $status = "<span style='color:{$rs[0]['status_color']}'>{$rs[0]['status_name']}</span>";
            }
            
            $objSmarty->assign('status',$status);
        }

        // --- Opening Date ---
        if ($oper == 'update' || $oper == 'echo') {
            $openingdate = !empty($rs[0]['fmt_entry_date']) ? $rs[0]['fmt_entry_date'] : "";
            $objSmarty->assign('openingdate',$openingdate);
        }

        // --- In charge ---
        if ($oper == 'update' || $oper == 'echo') {
            $in_charge = !empty($rs[0]['in_charge_name']) ? $rs[0]['in_charge_name'] : "";
            $objSmarty->assign('in_charge',$in_charge);
        }

        // --- Subject ---
        if ($oper == 'update' || $oper == 'echo') {
            $subject = !empty($rs[0]['subject']) ? $rs[0]['subject'] : "";
            $objSmarty->assign('subject',$subject);
        }

        // --- Description ---
        if ($oper == 'update' || $oper == 'echo') {
            $description = !empty($rs[0]['description']) ? $rs[0]['description'] : "";
            $objSmarty->assign('description',$description);
        }

        if ($oper == 'update') {
           // --- Actions buttons ---
            if($_SESSION['SES_COD_USUARIO'] == $rs[0]['idowner'] && $_SESSION['SES_COD_USUARIO'] != $rs[0]['id_in_charge']){
                $userType = 2;
            }else{
                $userType = 1;
            }
            $this->makeViewTicketBtns($objSmarty,$userType,$rs[0]['code_request'],$rs[0]['idstatus']);

            // --- Notes ---
            $lineNotes = $this->makeNotesScreen($rs[0]['code_request']);
            $objSmarty->assign('notes', $lineNotes);

            if($oper == 'update'){
                $objSmarty->assign('incharge', $rs[0]['id_in_charge']);
                $objSmarty->assign('typeincharge', $rs[0]['in_charge_type']);
            }
            
            // --- Operator groups ---
            if($_SESSION['SES_COD_USUARIO'] != $rs[0]['idowner'] && $_SESSION['SES_COD_USUARIO'] == $rs[0]['id_in_charge']){
                $arrOpeGrp = $this->_comboOperatorGroups($_SESSION['SES_COD_USUARIO']);
                $objSmarty->assign('grpids', $arrOpeGrp['ids']);
                $objSmarty->assign('grpvals', $arrOpeGrp['values']);
            }

            $retDPOID = $this->getDPOID();
            if($retDPOID['success'] && $retDPOID['id'] == $_SESSION['SES_COD_USUARIO']){
                $objSmarty->assign('isdpo', '1');
                $objSmarty->assign('displayViewGroup', 0);
            }else{
                $objSmarty->assign('isdpo', '0');
                $objSmarty->assign('displayViewGroup', 1);
            }
            
            // --- Note's type ---
            $aTypeNote = $this->_comboTypeNote("WHERE available = 1");
            $objSmarty->assign('typenoteids',  $aTypeNote['ids']);
            $objSmarty->assign('typenotevals', $aTypeNote['values']);
            $objSmarty->assign('idtypenote', "2");

        }

    }

    public function saveAttachments(){
        $this->protectFormInput();
                
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {
            
            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");            
            
            if($this->saveMode == 'disk'){

                $targetFile =  $this->requestStoragePath.$fileName;

                if($this->log)
                    $this->logIt("Save attachment in request # ". $code_request . ' - File: '.$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
                if (move_uploaded_file($tempFile,$targetFile)){
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }
                    
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = $this->getAwsS3Client();

                $arrayRet = $aws->copyToBucket($tempFile,$this->requestStoragePath.$fileName);
                
                if($arrayRet['success']) {
                    if($this->log)
                        $this->logIt("Save temp attachment file " . $fileName . ' - program: '.$this->program ,7,'general',__LINE__);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    if($this->log)
                        $this->logIt('I could not save the temp file: '.$fileName.' in S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }  

            }

        }else{

            echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));

        }

        exit;

    }

    public function saveTicket(){
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //CREATE THE CODE REQUEST
        $code_request = $this->createRequestCode();

        $requesterID = $_POST['requesterID'];
        $subject 	 = str_replace("'", "`", $_POST["subject"]);
        $subject 	 = strip_tags($subject);
        $description = str_replace("'", "`", $_POST["description"]);
        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files
        
        $statusID = 1;

        $rs = $this->dbDPORequest->insertRequest($code_request,$subject,$description,$requesterID,$_SESSION['SES_COD_USUARIO'],$statusID);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Insert ticket # {$code_request}. - {$rs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        $retInCharge = $this->getDPOID();
        if(!$retInCharge['success']){
            return false;
        }
        
        $insInCharge = $this->dbDPORequest->insertRequestCharge($code_request, $retInCharge['id'], 'P', '1');
        if(!$insInCharge['success']){
            if($this->log)
                $this->logIt("Can't insert in charge ticket # {$code_request}. - {$insInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        // link attachments to the request
        if($aSize > 0){
            $retAttachs = $this->linkTicketAttachments($code_request,$aAttachs);
            if(!$retAttachs['success']){
                if($this->log)
                    $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }
        }        

        $description = "<p><b>" . $langVars['Request_opened'] . "</b></p>";

        $public     = 1;
        $typeNote   = 3;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$description,$this->databaseNow,$public,$typeNote);
        if(!$ret['success']){
            if($this->log)
                $this->logIt("Can't insert note ticket # {$code_request} - {$ret['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "coderequest" => $code_request,
            "incharge" => $this->_inchargeName($code_request)
        );

        echo json_encode($aRet);

    }

    public function checkRequester() {
        $this->protectFormInput();

        $requesterName = strip_tags($_POST['requesterName']);
        $requesterCPF = $this->replaceMaskChar(strip_tags($_POST['cpf']));

        $where = "WHERE `name` = '{$requesterName}' AND cpf = '{$requesterCPF}'"; //echo "{$where}\n";

        $check = $this->dbDPORequest->getRequester($where);
        if(!$check['success']){
            if($this->log)
                $this->logIt("Can't get requester data. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            echo json_encode($this->getLanguageWord("generic_error_msg"));
            exit;
        }

        if (count($check['data']) > 0) {            
            echo json_encode($this->getLanguageWord("Alert_record_exist"));
        } else {
            echo json_encode(true);
        }
    }

    public function replaceMaskChar($word){

        $arr1 = array("(", ")", " ", ".", "-");

        $arr2 = array("", "", "", "", "");

        return strip_tags(str_replace($arr1, $arr2, $word));

    }

    function checkCPF()
    {
        $valuecheck = $this->replaceMaskChar(strip_tags($_POST['requesterCPF']));
        $where = "WHERE cpf = '$valuecheck'"; //echo $where;

        $valid = $this->validateCPF($valuecheck);

        if(!$valid){
            echo json_encode($this->getLanguageWord("Alert_invalid_cpf"));
        }else{
            $ret = $this->dbDPORequest->getRequester($where);
            if(!$ret['success']){
                if($this->log)
                    $this->logIt("Can't get requester data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                echo json_encode($this->getLanguageWord("generic_error_msg"));
                exit;
            }

            if(count($ret['data']) > 0){
                echo json_encode($this->getLanguageWord("cpf_exists"));
            }else{
                echo json_encode(true);
            }
        }
    }

    function validateCPF($cpf)
    {
        $arrInvalid = array('00000000000','11111111111','22222222222','33333333333','44444444444','55555555555','66666666666','77777777777','88888888888','99999999999');

        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

        if(strlen($cpf) != 11 || in_array($cpf,$arrInvalid)){ return FALSE;}
        else{
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    public function saveRequester() {
        $this->protectFormInput();
        
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        //echo "",print_r($_POST,true),"\n";
        $requesterName = strip_tags($_POST['requesterName']);
        $requesterCPF = $this->replaceMaskChar(strip_tags($_POST['requesterCPF']));
        $requesterEmail = strip_tags($_POST['requesterEmail']);

        $ret = $this->dbDPORequest->insertRequester($requesterName,$requesterCPF,$requesterEmail);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert requester data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;

        }

        $aRet = array(
            "success" => true,
            "requesterID" => $ret['id']
        );

        echo json_encode($aRet);
    }

    public function ajaxRequester()
    {
        echo $this->comboRequesterHtml($_POST['selectedID']);
    }

    public function comboRequesterHtml($selectedID)
    {
        $aRequester = $this->_comboRequester();
        $select = '';
        
        foreach ($aRequester['ids'] as $indexKey => $indexValue ) {
            $default = $indexValue == $selectedID ? 'selected="selected"' : '';
            $select .= "<option value='$indexValue' $default>".$aRequester['values'][$indexKey]."</option>";
        }

        return $select;
    }

    private function createRequestCode(){

        $rsCode = $this->dbDPORequest->getCode();
        if(!$rsCode['success']) {
            if($this->log)
                $this->logIt("Can't get request code data. {$rsCode['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        // Count month code
        $rsCountCode = $this->dbDPORequest->countGetCode();
        if(!$rsCountCode['success']) {
            if($this->log)
                $this->logIt("Can't get count month code data. {$rsCountCode['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        // If have code request
        if ($rsCountCode['data'][0]['total'] > 0) {
            $code_request = $rsCode['data'][0]["cod_request"];
            // Will increase the code of request
            $rsIncreaseCode = $this->dbDPORequest->increaseCode($code_request);
            if(!$rsIncreaseCode['success']) {
                if($this->log)
                    $this->logIt("Can't increase request code data. {$rsIncreaseCode['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }
        else {
            //If not have code request will create a new
            $code_request = 1;
            $rsCreateCode = $this->dbDPORequest->createCode($code_request);
            if(!$rsCreateCode['success']) {
                if($this->log)
                    $this->logIt("Can't create request code data. {$rsCreateCode['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $code_request = str_pad($code_request, 6, '0', STR_PAD_LEFT);
        $code_request = date("Ym") . $code_request;
        return $code_request;
    }
    
    private function getDPOID(){

        $ret = $this->dbDPORequest->getDPOID();
        if(!$ret['success']) {
            if($this->log)
                $this->logIt("Can't get DPO ID. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return array("success"=>false,"id"=>"");
        }

        return array("success"=>true,"id"=>$ret['data'][0]['idperson']);

    }

    public function linkTicketAttachments($code_request,$aAttachs)
    {

        foreach($aAttachs as $key=>$fileName){
            $retAttID = $this->dbDPORequest->saveTicketAtt($code_request,$fileName);
            if (!$retAttID['success']) {
                if($this->log)
                    $this->logIt("Can't save attachment into DB - {$retAttID['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to request {$code_request}");
            }
            
            $idAtt = $retAttID['id'];
            $extension = strrchr($fileName, ".");

            if($this->saveMode == 'disk'){
                $targetOld = $this->requestStoragePath.$fileName;
                $targetNew =  $this->requestStoragePath.$idAtt.$extension;
                if(!rename($targetOld,$targetNew)){
                    $delAtt = $this->dbDPORequest->deleteTicketAtt($idAtt);
                    if (!$delAtt['success']) {
                        if($this->log)
                            $this->logIt("Can't delete attachment into DB - {$delAtt['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                    }
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to request {$code_request}");
                }
                
            }elseif($this->saveMode == 'aws-s3'){
                $aws = $this->getAwsS3Client();
                $newFile = $idAtt.$extension;
                $arrayRet = $aws->renameFile("{$this->requestStoragePath}{$fileName}","{$this->requestStoragePath}{$newFile}");
                if($arrayRet['success']) {
                    if($this->log)
                        $this->logIt("Rename attachment file {$fileName} to {$newFile} - program: {$this->program} ",7,'general',__LINE__);
                } else {
                    if($this->log)
                        $this->logIt('I could not save the attchment file: '.$fileName.' in S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }
            
            }

        }
        
        return array("success"=>true,"message"=>"");

    }

    public function sendNotification(){

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

    public function ajaxRepassList()
    {
        $this->protectFormInput();
        $where = $_POST['typerep'] == 'group' ? "AND a.status = 'A'" : "AND status = 'A'";
        echo $this->_comboRepassListHtml($_POST['typerep'],$where,'ORDER BY `name` ASC');
    }

    public function openRepassedTicket()
    {
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }    

        $repassto = $_POST['repassto'];
		$typerepass = $_POST['typerepass'];
        $viewrepass = $_POST['viewrepass'];
        
        if ($typerepass == 'operator') {
            $name = $this->dbDPORequest->getRepassUsers("AND idperson = {$repassto}");
            $name = $name['data'][0]['name'];
            $typerepass = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        } 
        elseif ($typerepass == 'group') {
            $name = $this->dbDPORequest->getRepassGroups("AND a.idgroup = {$repassto}");
            $name = $name['data'][0]['name'];
            $typerepass = $langVars['to'] . " " . $langVars['Group'];
            $type2 = "G";
            
        }

        if (isset($repassto)) {
            //CREATE THE CODE REQUEST
            $code_request = $this->createRequestCode();

            $requesterID = $_POST['requesterID'];
            $subject 	 = str_replace("'", "`", $_POST["subject"]);
            $description = str_replace("'", "`", $_POST["description"]);
            $aAttachs 	= $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files

            $authorID = $_SESSION["SES_COD_USUARIO"];           
            $statusID = 2;

            $rs = $this->dbDPORequest->insertRequest($code_request,$subject,$description,$requesterID,$authorID,$statusID);
            if(!$rs['success']){
                if($this->log)
                    $this->logIt("Insert ticket # {$code_request}. - {$rs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }

            switch($viewrepass){			
				case "P": //REPASSAR E SEGUIR ACOMPANHANDO (Pessoa)
                    $track =$this->dbDPORequest->insertInCharge($code_request, $_SESSION['SES_COD_USUARIO'], "P", '0', "Y", '1');
                    if(!$track['success']){
                        if($this->log)
                            $this->logIt("Can't insert track ticket # {$code_request}. - {$track['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                        return false;
                    }
                    break;
                case "G": //REPASSAR E SEGUIR ACOMPANHANDO (Grupo)
                    $track =$this->dbTicket->insertInCharge($code_request, $_POST['viewgroup'], "G", '0', "Y", '1');
                    if(!$track['success']){
                        if($this->log)
                            $this->logIt("Can't insert track ticket # {$code_request}. - {$track['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                        return false;
                    }
					break;
				case "N": //NAO ACOMPANHAR					
					break;
            }

            $insInCharge = $this->dbDPORequest->insertRequestCharge($code_request, $repassto, $type2, '1');
            if(!$insInCharge['success']){
                if($this->log)
                    $this->logIt("Can't insert in charge ticket # {$code_request}. - {$insInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }

            // link attachments to the request
            if($aSize > 0){
                $retAttachs = $this->linkTicketAttachments($code_request,$aAttachs);
                if(!$retAttachs['success']){
                    if($this->log)
                        $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                    return false;
                }
            }

            $description = "<p><b>" . $langVars['Request_opened'] . "</b></p>";
            $public     = 1;
            $typeNote   = 3;
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            
            $ret = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$description,$this->databaseNow,$public,$typeNote);
            if(!$ret['success']){
                if($this->log)
                    $this->logIt("Can't insert note ticket # {$code_request} - {$ret['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }

            $descriptionRepass = "<p><b>" . $langVars['Request_repassed'] . strtolower($typerepass) . " " . $name . "</b></p>";
            $retNote = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$descriptionRepass,$this->databaseNow,$public,$typeNote);
            if(!$retNote['success']){
                if($this->log)
                    $this->logIt("Can't insert note[repassed] ticket # {$code_request} - {$retNote['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }

            $aRet = array(
                "coderequest" => $code_request,
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

    function makeViewTicketBtns($smarty,$userType,$ticketCode,$idstatus)
    {
        if($userType == 2){
            if($idstatus == 2){
                $idswitch_status = 2;
            }else{
                $rs = $this->dbDPORequest->getIdStatusSource($idstatus);
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("{$rs['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                }
                $idswitch_status = $rs['data'][0]['idstatus_source'];
            }

            switch($idswitch_status){
                case "1": //NEW
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'1');
                    $smarty->assign('displaynote',		'0'); 
                    $smarty->assign('displayprint',     '1');
                    break;

                case "2": //REPASSED
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;

                case "3": //ON ATTENDANCE
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displaynote',		'1');
                    $smarty->assign('displayprint',     '1');
                    break;

                case "5": //FINISHED
                    if($_SESSION['lgp']['SES_IND_REOPEN_USER'] == 1)
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
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;

                default:
                    $smarty->assign('displayreopen',	'0');
                    $smarty->assign('displaycancel',  	'0');
                    $smarty->assign('displaynote',		'0');
                    $smarty->assign('displayprint',     '1');
                    break;
            }
        }else{
            $idperson = $_SESSION['SES_COD_USUARIO'];
            $entry_date = " DATE_FORMAT(a.dtentry, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_entry_date" ;
            $where      = "WHERE a.code_request = {$ticketCode} AND d.ind_in_charge = 1";

            $rsTicket = $this->dbDPORequest->getTickets($entry_date,$where);
            if(!$rsTicket['success']){
                if($this->log)
                    $this->logIt("Can't get ticket data. {$rsTicket['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            }
            
            $incharge   = $rsTicket['data'][0]['id_in_charge'];

            if($idstatus == 2){
                $idswitch_status = 2;
            }else{
                $rs = $this->dbDPORequest->getIdStatusSource($idstatus);
                if(!$rs['success']){
                    if($this->log)
                        $this->logIt("{$rs['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                }
                $idswitch_status = $rs['data'][0]['idstatus_source'];
            }

            $myGroupsIdPerson = $this->dbDPORequest->getIdPersonGroup($idperson);
            if(!$myGroupsIdPerson['success']){
                if($this->log)
                    $this->logIt("{$myGroupsIdPerson['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            }else{
                foreach($myGroupsIdPerson['data'] as $key=>$val) {
                    $myGroupsIdPersonArr[] = $val['idperson'];
                }
            }

            switch($idswitch_status){
                case "1": //NEW
                    $smarty->assign('displayassume',  '1');
                    $smarty->assign('displayrepass',  '1');
                    $smarty->assign('displayreject',  '1');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "2": //REPASSED                    
                    if(in_array($incharge, $myGroupsIdPersonArr) || $incharge == $idperson){
                        //SOU RESPONSÁVEL POR ESTA SOL
                        $smarty->assign('displayassume',  '1');
                        $smarty->assign('displayrepass',  '1');
                        $smarty->assign('displayreject',  '1');
                        $smarty->assign('displayclose',   '0');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displaynote', 	  '0');
                        $smarty->assign('displayprint',   '1');
                    }
                    else{
                        //NÃO SOU RESPONSÁVEL POR ESTA SOL
                        if ($_SESSION['lgp']['SES_IND_ASSUME_OTHER'] == 1) {
                            $smarty->assign('displayassume',  '1');
                        }else{
                            $smarty->assign('displayassume',  '0');
                        }
                        $smarty->assign('displayrepass',  '0');
                        $smarty->assign('displayreject',  '0');
                        $smarty->assign('displayclose',   '0');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displaynote', 	  '0');
                        $smarty->assign('displayprint',   '1');
                    }
                    break;
                case "3"://ON ATTENDANCE
                    if(in_array($incharge, $myGroupsIdPersonArr) || $incharge == $idperson){
                        //SOU RESPONSÁVEL POR ESTA SOL
                        $smarty->assign('displayassume',  '0');
                        $smarty->assign('displayrepass',  '1');
                        $smarty->assign('displayreject',  '0');
                        $smarty->assign('displayclose',   '1');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displaynote', 	  '1');
                        $smarty->assign('displayprint',   '1');
                    }
                    else{
                        //NÃO SOU RESPONSÁVEL POR ESTA SOL
                        if ($_SESSION['lgp']['SES_IND_ASSUME_OTHER'] == 1) {
                            $smarty->assign('displayassume',  '1');
                        }else{
                            $smarty->assign('displayassume',  '0');
                        }
                        $smarty->assign('displaysavechange','1');
                        $smarty->assign('displayrepass',  '0');
                        $smarty->assign('displayreject',  '0');
                        $smarty->assign('displayclose',   '0');
                        $smarty->assign('displayreopen',  '0');
                        $smarty->assign('displaycancel',  '0');
                        $smarty->assign('displaynote',    '0');
                        $smarty->assign('displayprint',   '1');
                    }
                    break;
                case "5":
                    //FINISHED
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    if ($_SESSION['lgp']['SES_IND_REOPEN_USER'] == '0')
                        $smarty->assign('displayreopen',  '0');
                    else
                        $smarty->assign('displayreopen',  '1');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                case "6":
                    //REJECTED
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
                default:
                    $smarty->assign('displayassume',  '0');
                    $smarty->assign('displayrepass',  '0');
                    $smarty->assign('displayreject',  '0');
                    $smarty->assign('displayclose',   '0');
                    $smarty->assign('displayreopen',  '0');
                    $smarty->assign('displaycancel',  '0');
                    $smarty->assign('displaynote', 	  '0');
                    $smarty->assign('displayprint',   '1');
                    break;
            }
        }

        $smarty->assign('hidden_idstatus',$idswitch_status);

    }

    function makeNotesScreen($code_request)
    {

        // Attendance data
        $entry_date = " DATE_FORMAT(a.dtentry, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_entry_date" ;
        $where = "WHERE a.code_request = '{$code_request}'";
        $rsTicket = $this->dbDPORequest->getTickets($entry_date,$where);
        if(!$rsTicket['success']){
            if($this->log)
                $this->logIt("{$rsTicket['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $idstatus = $rsTicket['data'][0]['idstatus'];
        
        // Notes
        $typeperson = $_SESSION['SES_TYPE_PERSON'];
        $rsNotes = $this->dbDPORequest->getTicketNotes($code_request);
        if(!$rsNotes['success']){
            if($this->log)
                $this->logIt("{$rsNotes['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $lineNotes = '';

        foreach($rsNotes['data'] as $key=>$val){
            $idNote = $val['idnote'];

            if($idstatus == 3){
                if ($val['idtypenote'] != '3' && $_SESSION['lgp']['SES_IND_DELETE_NOTE'] == '1' && $_SESSION['SES_COD_USUARIO'] == $val['idperson'] && $val['flag_opened'] != '0'){
                    $iconDel = '<button type="button" class="btn btn-danger btn-xs" href="<a href="javascript:;" onclick="deleteNote('.$idNote.','.$code_request.','.$typeperson.');"><span class="fa fa-trash-alt"></span></button>';
                } else {
                    $iconDel = "";
                }
            } else {
                $iconDel = "";
            }

            if ($val['idtypenote'] == '1' && $val['idperson'] == $rsTicket['data'][0]['idowner']) {
                // User
                $iconNote = ' <i class="fa fa-user "></i>';
            } elseif($val['idtypenote'] == '1'){
                // Operator
                $iconNote = ' <i class="fa fa-users "></i>';
            } else {
                $iconNote = ' <i class="fa fa-cogs "></i>';
            }

            $rsNoteAttach = $this->dbDPORequest->getNoteAttachments($idNote);
            if(!$rsNoteAttach['success']){
                if($this->log)
                    $this->logIt("Can't get note attachments. {$rsNoteAttach['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            if (count($rsNoteAttach['data']) > 0) {
                $iconFile = '';
                foreach($rsNoteAttach['data'] as $k=>$v){
                    $idNoteAttach = $v['idnote_attachments'] ;
                    $tooltip = $v['filename']; 
                    $iconFile .= '<button type="button" class="btn btn-default btn-xs" id="'.$idNoteAttach.'" onclick="download('.$idNoteAttach.',\'note\')" data-toggle="tooltip" data-container="body" data-placement="right" data-original-title="'.$tooltip.'"><span class="fa fa-file-alt"></span></button>&nbsp;';
                }
            } else {
                $iconFile  = "&nbsp";
            }

            $noteTitle  = $this->formatDateHour($val['dtentry']) . " [" . $this->getPersonName($val['idperson']) . "] <br>";
            $note =  $val['description'] ;

            if($_SESSION['SES_COD_USUARIO'] != $rsTicket['data'][0]['idowner'] || ($_SESSION['SES_COD_USUARIO'] == $rsTicket['data'][0]['idowner']  && $val['idtypenote'] != '2')){
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

        return $lineNotes;
    }

    public function assumeTicket()
    {

        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $is_dpo = $_POST['isdpo'];
        $code_request = $_POST['code_request'];
        $grpview = $_POST['grpview'];
        $groupAssume = $_POST['groupAssume'];
        $incharge = $_POST['incharge'];
        $typeincharge = $_POST['typeincharge'];

        $idstatus = '3'; //EM ATENDIMENTO
        $idperson = $_SESSION['SES_COD_USUARIO']; //ID DO USUARIO QUE ESTÁ ASSUMINDO

        $description = "<p><b>" . $langVars['Request_assumed'] . "</b></p>";

        $public     = 1;
        $typeNote   = 3;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ret = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$description,$this->databaseNow,$public,$typeNote);
        if(!$ret['success']){
            if($this->log)
                $this->logIt("Can't insert note ticket # {$code_request} - {$ret['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        if($is_dpo != '1' && $grpview == 1){//ADICIONAR TRACK PARA O GRUPO
            if($typeincharge == "P"){
                $track = $this->dbDPORequest->insertInCharge($code_request, $groupAssume, "G", '0', 'N', '1');
            }elseif($typeincharge == "G"){
                $track = $this->dbDPORequest->insertInCharge($code_request, $incharge, "G", '0', 'N', '1');
            }
            if(!$track['success']){
                if($this->log)
                    $this->logIt("Can't insert track ticket # {$code_request}. - {$track['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }
        }

        $type = "P"; //TIPO PESSOA
        $rep = 'N'; //NÃO É REPASS
        $ind = '1'; //RESPONSAVEL ATUAL
        $removeInCharge = $this->dbDPORequest->removeIncharge($code_request); //Remove request's responsible before add new one
        if (!$removeInCharge['success']) {
            if($this->log)
                $this->logIt("Can't remove in charge ticket # {$code_request}. - {$removeInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $insInCharge = $this->dbDPORequest->insertInCharge($code_request, $idperson, $type, $ind, $rep, '0'); //Add new responsible
        if(!$insInCharge['success']){
            if($this->log)
                $this->logIt("Can't insert in charge ticket # {$code_request}. - {$insInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $changeStatus = $this->dbDPORequest->updateTicketStatus($idstatus, $code_request); //Update request status
        if(!$changeStatus['success']){
            if($this->log)
                $this->logIt("Can't update status, ticket # {$code_request}. - {$changeStatus['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrayParam = array('transaction' => 'operator-assume',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        $aRet = array("success" => true);
        echo json_encode($aRet);

    }

    public function repassTicket()
    {

        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $type = $_POST['type'];
        $repassto = $_POST['repassto'];
        $code_request = $_POST['code_request'];
        $view = $_POST['view'];
        $idgrouptrack = $_POST['idgrouptrack'];
        $incharge = $_POST['incharge'];

        if ($type == 'operator') {
            $name = $this->dbDPORequest->getRepassUsers("AND idperson = {$repassto}");
            $name = $name['data'][0]['name'];
            $type = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        }elseif ($type == 'group') {
            $name = $this->dbDPORequest->getRepassGroups("AND a.idperson = {$repassto}");
            $name = $name['data'][0]['name'];
            $type = $langVars['to'] . " " . $langVars['Group'];
            $type2 = "G";
        }else{
            return false;
        }

        $status = '2'; // Redirected ticket
        $reopened = '0';
        $rep = 'Y';

        switch($view){
            case "G": // Redirect ticket, but the group continues to follow
                if($idgrouptrack == 0){
                    $track = $this->dbDPORequest->insertInCharge($code_request, $incharge, "G", '0', $rep,  '1');
                    if(!$track['success']){
                        if($this->log)
                            $this->logIt("Can't insert track ticket # {$code_request}. - {$track['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }else{
                    $track = $this->dbDPORequest->insertInCharge($code_request, $idgrouptrack, "G", '0', $rep, '1');
                    if(!$track['success']){
                        if($this->log)
                            $this->logIt("Can't insert track ticket # {$code_request}. - {$track['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
                break;
            case "P": // Redirect ticket and continue following
                $track = $this->dbDPORequest->insertInCharge($code_request, $_SESSION['SES_COD_USUARIO'], "P", '0', $rep, '1');
                if(!$track['success']){
                    if($this->log)
                        $this->logIt("Can't insert track ticket # {$code_request}. - {$track['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                break;
            case "N": // Do not follow
                break;
        }

        //Insert Note
        $description = "<p><b>" . $langVars['Request_repassed'] . strtolower($type) . " " . $name . "</b></p>";
        
        $public     = 1;
        $typeNote   = 3;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $retNote = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$description,$this->databaseNow,$public,$typeNote);
        if(!$retNote['success']){
            if($this->log)
                $this->logIt("Can't insert note[repassed] ticket # {$code_request} - {$retNote['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //Set all ind_in_charge with 0 (zero) (Remove all responsible)
        $rmincharge = $this->dbDPORequest->removeIncharge($code_request);
        if (!$rmincharge['success']) {
            if($this->log)
                $this->logIt("Can't remove in charge ticket # {$code_request}. - {$rmincharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //Add new responsible
		$insInCharge = $this->dbDPORequest->insertInCharge($code_request, $repassto, $type2, 1, $rep);
        if(!$insInCharge['success']){
            if($this->log)
                $this->logIt("Can't insert in charge ticket # {$code_request}. - {$insInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //Change Request Status - Repassed
        $changeStat = $this->dbDPORequest->updateTicketStatus($status,$code_request);
        if(!$changeStat['success']){
            if($this->log)
                $this->logIt("Can't update status, ticket # {$code_request}. - {$changeStat['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrayParam = array('transaction' => 'forward-ticket',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        $aRet = array("success" => true);
        echo json_encode($aRet);

    }

    public function rejectTicket()
    {

        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];
        $incharge = $_POST['incharge'];
        $typeincharge = $_POST['typeincharge'];
        $rejectreason = $_POST['rejectreason'];

        $statusID = 5;
        $idperson = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';

        $description = "<p><b>" . $langVars['Request_rejected'] . "</b></p>".$rejectreason;

        $serviceVal = 'NULL';
        $public     = 1;
        $typeNote   = 3;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $retNote = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$description,$this->databaseNow,$public,$typeNote);
        if(!$retNote['success']){
            if($this->log)
                $this->logIt("Can't insert note[rejected] ticket # {$code_request} - {$retNote['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $changeStat = $this->dbDPORequest->updateTicketStatus($statusID,$code_request);
        if(!$changeStat['success']){
            if($this->log)
                $this->logIt("Can't update status, ticket # {$code_request}. - {$changeStat['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $type = "P"; //TIPO PESSOA
        $rep = 'N'; //NÃO É REPASS
        $ind = '1'; //RESPONSAVEL ATUAL
        $removeInCharge = $this->dbDPORequest->removeIncharge($code_request);
        if (!$removeInCharge['success']) {
            if($this->log)
                $this->logIt("Can't remove in charge ticket # {$code_request}. - {$removeInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        //Add new responsible
        $insInCharge = $this->dbDPORequest->insertInCharge($code_request, $idperson, $type, $ind, $rep, '0');
        if(!$insInCharge['success']){
            if($this->log)
                $this->logIt("Can't insert in charge ticket # {$code_request}. - {$insInCharge['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrayParam = array('transaction' => 'operator-reject',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $email = $this->_sendNotification($arrayParam);

        $aRet = array("success" => true);
        echo json_encode($aRet);

    }

    public function finishTicket()
    {

        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $code_request = $_POST['code_request'];

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';
        $statusID = 4;
        $description = '<p><b>' . $langVars['Request_closed'] . '</b></p>';

        $public     = 1;
        $typeNote   = 3;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $retNote = $this->dbDPORequest->insertNote($code_request,$_SESSION["SES_COD_USUARIO"],$description,$this->databaseNow,$public,$typeNote);
        if(!$retNote['success']){
            if($this->log)
                $this->logIt("Can't insert note[rejected] ticket # {$code_request} - {$retNote['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $changeStat = $this->dbDPORequest->updateTicketStatus($statusID,$code_request);
        if(!$changeStat['success']){
            if($this->log)
                $this->logIt("Can't update status, ticket # {$code_request}. - {$changeStat['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrayParam = array('transaction' => 'finish-ticket',
                            'code_request' => $code_request,
                            'media' => 'email') ;

        $this->_sendNotification($arrayParam);

        $aRet = array("success" => true);
        echo json_encode($aRet);

    }

    public function saveNoteAttach()
    {

        $this->protectFormInput();

        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {
            
            $fileName   = $_FILES['file']['name'];
            $tempFile   = $_FILES['file']['tmp_name'];
            $extension  = strrchr($fileName, ".");

            if($this->saveMode == 'disk') {

                $targetFile =  $this->noteStoragePath.$fileName;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logIt('Add attachment #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logIt('Error attachment #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }

            }elseif($this->saveMode == "aws-s3") {

                $aws = $this->getAwsS3Client();
                $arrayRet = $aws->copyToBucket($tempFile,$this->noteStoragePath.$fileName);
                
                if($arrayRet['success']) {
                    if($this->log)
                        $this->logIt("Save temp attachment file " . $fileName . ' - program: '.$this->program ,7,'general',__LINE__);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    if($this->log)
                        $this->logIt('I could not save the temp file: '.$fileName.' in S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }  

            }    

        }else{   

            echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));        

        }

        exit;

    }

    public function saveNote()
    {
        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $codeRequest     = $_POST['code_request'];
        $noteContent     =  addslashes($_POST['noteContent']);

        $public     = 1;
        $typeNote   = ($_POST['flagNote'] == 2) ? 1 : $_POST['typeNote'];

        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files

        $aParam = array();
        $aParam['code_request'] = $codeRequest;
        $aParam['notecontent'] = $noteContent;
        $aParam['public'] = $public;
        $aParam['typenote'] = $typeNote;
        $aParam['flgopen'] = 1;

        $idNoteInsert = $this->_saveNote($aParam);
        if(!$idNoteInsert){
            if($this->log)
                $this->logIt("Can't add note in request # {$codeRequest} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        } else {
            if($this->log)
                $this->logIt("Add note in request # {$codeRequest} - User: {$_SESSION['SES_LOGIN_PERSON']}",6,'general');
            
            // link attachments to the request
            if($aSize > 0){
                $retAttachs = $this->linkNoteAttachments($idNoteInsert,$aAttachs);
                if(!$retAttachs['success']){
                    if($this->log)
                        $this->logIt("Can't link note to attachment. {$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }

            if($_SESSION['lgp']['SEND_EMAILS'] == '1'){

                if($_POST['flagNote'] == 3){ // Note created by operator
                    $transaction = $typeNote != 2 ? 'user-note' : 'operator-note';
                }elseif($_POST['flagNote'] == 2){ // Note created by user
                    $transaction = 'operator-note';
                }else{
                    echo $idNoteInsert;
                    exit;
                }

                $arrayParam = array(
                    'transaction' => $transaction,
                    'code_request' => $codeRequest,
                    'media' => 'email'
                );
                
                $this->_sendNotification($arrayParam);
            }            

            echo $idNoteInsert;
        }

    }

    public function linkNoteAttachments($idNote,$aAttachs)
    {

        foreach($aAttachs as $key=>$fileName){
            
            $retAttID = $this->dbDPORequest->saveNoteAttachment($idNote,$fileName);
            if (!$retAttID['success']) {
                if($this->log)
                    $this->logIt("Can't save attachment into DB - {$retAttID['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to note {$idNote}");
            }
            
            $idAtt = $retAttID['id'];
            $extension = strrchr($fileName, ".");

            if($this->saveMode == 'disk') {
                $targetOld = $this->noteStoragePath.$fileName;
                $targetNew =  $this->noteStoragePath.$idAtt.$extension;

                if(!rename($targetOld,$targetNew)){
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to note {$idNote}");
                }
            }elseif($this->saveMode == 'aws-s3') {
                $aws = $this->getAwsS3Client();
                $newFile = $idAtt.$extension;
                $arrayRet = $aws->renameFile("{$this->noteStoragePath}{$fileName}","{$this->noteStoragePath}{$newFile}");
                if($arrayRet['success']) {
                    if($this->log)
                        $this->logIt("Rename attachment file {$fileName} to {$newFile} - program: {$this->program} ",7,'general',__LINE__);
                } else {
                    if($this->log)
                        $this->logIt("I could not save the attachment file: {$fileName} in S3 bucket !! - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }            
            }
        }        
        
        return array("success"=>true,"message"=>"");

    }

    public function ajaxNotes()
    {
        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $code_request = $_POST['code_request'];
        $lineNotes = $this->makeNotesScreen($code_request);
        echo $lineNotes;
    }

    public function downloadFile(){

        $filename = $this->getParam('id');
        $type = $this->getParam('type');
        $file = $this->dbDPORequest->getTicketFile($filename,$type);
        if(!$file['success']){
            if($this->log)
                $this->logIt("Can't get attachments. {$file['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $name = $file['data'][0]['file_name'];
        $ext = strrchr($name, '.');

        switch ($type) {
            case 'note':
                if($this->saveMode == 'aws-s3') {
                    $bucket = $this->getConfig('s3bucket_name');
                    $url = "https://{$bucket}.s3.amazonaws.com/{$this->noteStoragePath}{$filename}{$ext}" ;
                    if( ! file_put_contents( $this->helpdezkPath . "/app/uploads/tmp/{$filename}{$ext}" ,file_get_contents($url))) {
                        if($this->log)
                            $this->logIt("Can\'t save S3 temp file {$filename}{$ext} - program: ".$this->program ,3,'general',__LINE__);
                    }
                    $file_name = $this->helpdezkPath . "/app/uploads/tmp/{$filename}{$ext}" ;
                } else {
                    if($this->_externalStorage) {
                        $file_name = $this->noteStoragePath . $filename . $ext;
                    } else {
                        $file_name = $this->noteStoragePath . $filename . $ext ;
                    }
                }
                
                break;

                case 'request':
                if($this->saveMode == 'aws-s3') {
                    $bucket = $this->getConfig('s3bucket_name');
                    $url = "https://{$bucket}.s3.amazonaws.com/{$this->requestStoragePath}{$filename}{$ext}" ;
                    if( ! file_put_contents( $this->helpdezkPath . "/app/uploads/tmp/{$filename}{$ext}" ,file_get_contents($url))) {
                        if($this->log)
                            $this->logIt("Can\'t save S3 temp file {$filename}{$ext} - program: ".$this->program ,3,'general',__LINE__);
                    }
                    $file_name = $this->helpdezkPath . "/app/uploads/tmp/{$filename}{$ext}" ;
                } else {
                    if($this->_externalStorage) {
                        $file_name = $this->requestStoragePath . $filename . $ext ;
                    } else {
                        $file_name = $this->requestStoragePath . $filename . $ext ;
                    }
                }

                break;
        }

        // required for IE
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // get the file mime type 
        $mime =  mime_content_type($filename . $ext) ;
        if (empty($mime))
            $mime = 'application/force-download';

        header('Pragma: public');   // required
        header('Expires: 0');       // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate ('D, d M Y H:i:s', filemtime ($file_name)) . ' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '. filesize($file_name));  
        header('Connection: close');
        // push it out
        readfile($file_name);    
        // delete tem file
        if($this->saveMode == 'aws-s3') 
            unlink($file_name);
        
        exit();
    }

    public function makeReport()
    {

        $this->protectFormInput();

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $code_request = $_POST['code_request'];
        $entry_date = " DATE_FORMAT(a.dtentry, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_entry_date" ;

        $rsTicket = $this->dbDPORequest->getTickets($entry_date,"WHERE a.code_request = '$code_request'");
        if(!$rsTicket['success']){
            if($this->log)
                $this->logIt("Can't get ticket data. Ticket # {$code_request}. {$rsTicket['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $idowner  = $rsTicket['data'][0]['idowner'];

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
        $pdf->Cell(33,$CelHeight,substr($rsTicket['data'][0]['code_request'],0,4) . "/" . substr($rsTicket['data'][0]['code_request'],4,2) . "-" . substr($rsTicket['data'][0]['code_request'],6),0,0,'L',0);
        $pdf->Cell(40);
        $pdf->Cell(30,$CelHeight,html_entity_decode($langVars['available_note_holder'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(60,$CelHeight,utf8_decode($rsTicket['data'][0]['owner_name']),0,1,'L',0);

        // -- Status --
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['status'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R');
        $pdf->Cell(15,$CelHeight,utf8_decode($rsTicket['data'][0]['status_name']),0,0,'L',0);
        $pdf->Cell(40);
        $pdf->Cell(30,$CelHeight,"",0,0,'R');
        $pdf->Cell(60,$CelHeight,"",0,1,'L',0);

        $this->makePdfLine($pdf,$leftMargin,197);

        // -- Subject and description
        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Subject'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->Cell(145,$CelHeight,utf8_decode($rsTicket['data'][0]['subject']),0,1,'L',0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(22,$CelHeight,html_entity_decode($langVars['Description'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
        $pdf->SetleftMargin($leftMargin + 30);
        $description = ltrim(html_entity_decode(utf8_decode($rsTicket['data'][0]['description']),ENT_QUOTES, "ISO8859-1"));
        $pdf->Cell(145,$CelHeight,$pdf->WriteHTML($description),0,1,'L',0);

        $pdf->SetLeftMargin($leftMargin);
        $this->makePdfLine($pdf,$leftMargin,197);


        $pdf->Ln(1);
        $pdf->SetLeftMargin($leftMargin);


        // Notes
        $rsNotes = $this->dbDPORequest->getTicketNotes($code_request);
        if(!$rsNotes['success']){
            if($this->log)
                $this->logIt("Can't get notes data. Ticket # {$code_request}. {$rsNotes['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if (count($rsNotes['data']) != 0) {
            $pdf->Ln(6);
            $pdf->Cell($leftMargin);
            $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
            $titleNotes = array(array('title'=>html_entity_decode($langVars['Added_notes'],ENT_QUOTES, "ISO8859-1"),'cellWidth'=>179,'cellHeight'=>$CelHeight,'titleAlign'=>'C'));
            $this->makePdfLineBlur($pdf,$titleNotes);
            $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        }

        foreach($rsNotes['data'] as $key=>$val){
            $pdf->Ln(2);
            $pdf->Cell($leftMargin);
            $pdf->Cell(30,$CelHeight,$this->formatDateHour($val['dtentry']) . " [ " . utf8_decode($val['name']) . " ] " ,0,1,'L');
            $pdf->SetLeftMargin($leftMargin + 15);
            $description = ltrim(html_entity_decode(utf8_decode($val['description']),ENT_QUOTES, "ISO8859-1"));
            $description = preg_replace("/<br\W*?\/>/", "<br><br>", $description);
            $pdf->Cell(0,$CelHeight,$pdf->WriteHTML($description),0,1,'C');
            $pdf->Ln(1);
            $pdf->SetLeftMargin($leftMargin);
            $this->makePdfLine($pdf,$leftMargin,197);
        }

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_dir($this->helpdezkPath . '/app/downloads/tmp/')) {
            $this->logIt('Target Directory: '. $this->helpdezkPath . '/app/downloads/tmp/' .' does not exists, I will try to create it. - program: '.$this->program ,6,'general',__LINE__);
            if (!mkdir ($this->helpdezkPath . '/app/downloads/tmp/', 0777 )) {
                $this->logIt('I could not create the directory: '. $this->helpdezkPath . '/app/downloads/tmp/' .' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
        }

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {
            if($this->log)
                $this->logIt("Target Directory: ".$this->helpdezkPath . '/app/downloads/tmp/'.' is not writable - program: '.$this->program ,3,'general',__LINE__);

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) ){
                $this->logIt("Make report request # ". $rsTicket->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/tmp/' . ' is not writable ' ,3,'general',__LINE__);
                return false;
            }
        }

        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;



    }

}