<?php

use App\core\Controller;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;

use App\modules\helpdezk\dao\mysql\ticketDAO;
use App\modules\helpdezk\dao\mysql\ticketRulesDAO;

use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\helpdezk\models\mysql\ticketRulesModel;

class hdkTicket extends Controller
{           
    public function __construct()
    {
        parent::__construct();

        $this->appSrc->_sessionValidate();       
    }
    
    public function index($myTickets=null)
    {
        $params = $this->makeScreenHdkTicket('idx',null,$myTickets);

		$this->view('helpdezk','ticket',$params);
    }

    public function makeScreenHdkTicket($option='idx',$obj=null,$myTickets=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $hdkSrc->_makeNavHdk($params);

        // -- Companies --
        //$params['cmbCompany'] = $adminSrc->_comboCompany(); 
        
        if($option == 'idx'){
            if($myTickets){
                $params['typeUser'] = 2;
                $params['flgOperator'] = 1;
                $params['operatorAsUser'] = 1;
            }else{
                $params['typeUser'] = $_SESSION['SES_TYPE_PERSON'];
                $params['flgOperator'] = 0;
                $params['operatorAsUser'] = 0;
            }

            if($params['typeuser'] == 3){
                // -- View by Expiry date type --
                $params['cmbTypeExpireDate'] = $hdkSrc->_comboTypeExpireDate();

                // -- Type View --
                $params['cmbTipeView'] = $hdkSrc->_comboTypeView();
            }

            // -- Search action --
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts();
            $params['cmbFilters'] = $this->comboTicketFilters();
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }        
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte'; //subir imagem
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        if($option != 'idx'){
            $params['ticketAttMaxFiles'] = $this->appSrc->_getTicketAttMaxFiles(); 
            $params['ticketAcceptedFiles'] = $this->appSrc->_getAcceptedFiles();
            $params['noteAttMaxfiles'] = $this->appSrc->_getNoteAttMaxFiles(); 
            $params['noteAcceptedFiles'] = $this->appSrc->_getAcceptedFiles();
            $params['hdkMaxSize'] = substr($this->appSrc->_getTicketAttMaxFileSize(),0,-1);

            if($_SESSION['SES_TYPE_PERSON'] == 3){
                $params['owner'] = "";
            }else{
                $params['owner'] = $_SESSION['SES_NAME_PERSON'];
            }
        }

        if($option == 'add'){
            $params['equipment'] = isset($_SESSION['SES_IND_EQUIPMENT']) ? $_SESSION['SES_IND_EQUIPMENT'] : 1;
        }

        if($option=='upd'){
            $params['id'] = $obj->getIdDepartment();
            $params['department'] = $obj->getDepartment();
            $params['idcompany'] = $obj->getIdCompany();
        }
        
        return $params;
    }

    public function jsonGrid()
    {
        $ticketDAO = new ticketDAO();
        $hdkSrc = new hdkServices();

        $where = "";
        $group = "";

        $idStatus = $_POST['idStatus'];
        if($idStatus){
            if ($idStatus == 'ALL') {
                $where = '';
            } else {
                $where .= "AND b.idstatus_source = {$idStatus} ";
            }
        }else{
            if(!in_array($_SESSION['SES_TYPE_PERSON'],array(3,1))){
                $flgapvreq = $hdkSrc->_checkApproval(); // check if user have request to aprove
                if($flgapvreq > 0){
                    $where .= "AND b.idstatus_source = 4 ";
                }
            }
        }

        if($_ENV['LICENSE'] == '200701006'){
            $where .= "AND iditem <> 124 ";
        }

        $where .= "AND a.idperson_owner = {$_SESSION['SES_COD_USUARIO']}";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];           
                   
            switch($filterIndx){
                case "ticket":
                    $filterIndx = "a.name";
                    break;
                case "company":
                    $filterIndx = "b.name";
                    break;
                default:
                    $filterIndx = $filterIndx;
                break;
            }
            
            $where .=  " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

         //Search with params sended from quick search input
         if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
         {
             $quickValue = trim($_POST['quickValue']);
             $quickValue = str_replace(" ","%",$quickValue);
             $where .= " AND " . " (pipeLatinToUtf8(a.name) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(b.name) LIKE '%{$quickValue}%')";
         }

         //sort options
         $pq_sort = json_decode($_POST['pq_sort']);
         $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "code_request";
         
         $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
         $order = "ORDER BY {$sortIndx} {$sortDir}";
         
         $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
     
         $pq_rPP = $_POST["pq_rpp"];
         
         //Count records
         $countTicket = $ticketDAO->countTickets($where); 
         if($countTicket['status']){
             $total_Records = $countTicket['push']['object']->getTotalRows();
         }else{
             $total_Records = 0;
         }
         
         $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
         $limit = "LIMIT {$skip},$pq_rPP";
 
         $ticket = $ticketDAO->queryTickets($where,$group,$order,$limit);
        
        if($ticket['status']){     
            $ticketObj = $ticket['push']['object']->getGridList();     
            
            foreach($ticketObj as $k=>$v) {
                $star = ($v['flag_opened'] == 1 && $v['status'] != 1) ? '<i class="fa fa-star" />' : '';
                $expire = ($_SESSION['hdk']['SES_HIDE_GRID_PERIOD'] == 0)
                        ? $v['expire_date']
                        : (($v['status'] == 1) ? $this->translator->translate('Not_available_yet') : $this->appSrc->_formatDateHour($v['expire_date']));
                $statusLbl = strlen($v['statusview']) > 25 ? $this->appSrc->_reduceText($v['statusview'],25) : $v['statusview'];

                $data[] = array(
                    'star'          => $star,
                    'ticketCode'    => $v['code_request'],
                    'entryDate'     => $this->appSrc->_formatDateHour($v['entry_date']),
                    'subject'       => $v['subject'],
                    'expiryDate'    => $expire,
                    'inCharge'      => $v['in_charge'],
                    'status'        => "<span style='color:{$v['color_status']}'>{$v['statusview']}</span>"
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

    public function jsonGridAttendant()
    {
        $ticketDAO = new ticketDAO();
        $hdkSrc = new hdkServices();

        $where = "";
        $group = "";

        $idStatus = $_POST['idStatus'];
        if($idStatus){
            if ($idStatus == 'ALL') {
                $where = '';
            } else {
                $where .= "AND b.idstatus_source = {$idStatus} ";
            }
        }else{
            if(!in_array($_SESSION['SES_TYPE_PERSON'],array(3,1))){
                $flgapvreq = $hdkSrc->_checkApproval(); // check if user have request to aprove
                if($flgapvreq > 0){
                    $where .= "AND b.idstatus_source = 4 ";
                }
            }
        }

        if($_ENV['LICENSE'] == '200701006'){
            $where .= "AND iditem <> 124 ";
        }

        $where .= "AND a.idperson_owner = {$_SESSION['SES_COD_USUARIO']}";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];           
                   
            switch($filterIndx){
                case "ticket":
                    $filterIndx = "a.name";
                    break;
                case "company":
                    $filterIndx = "b.name";
                    break;
                default:
                    $filterIndx = $filterIndx;
                break;
            }
            
            $where .=  " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

         //Search with params sended from quick search input
         if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
         {
             $quickValue = trim($_POST['quickValue']);
             $quickValue = str_replace(" ","%",$quickValue);
             $where .= " AND " . " (pipeLatinToUtf8(a.name) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(b.name) LIKE '%{$quickValue}%')";
         }

         //sort options
         $pq_sort = json_decode($_POST['pq_sort']);
         $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "code_request";
         
         $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
         $order = "ORDER BY {$sortIndx} {$sortDir}";
         
         $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
     
         $pq_rPP = $_POST["pq_rpp"];
         
         //Count records
         $countTicket = $ticketDAO->countTickets($where); 
         if($countTicket['status']){
             $total_Records = $countTicket['push']['object']->getTotalRows();
         }else{
             $total_Records = 0;
         }
         
         $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
         $limit = "LIMIT {$skip},$pq_rPP";
 
         $ticket = $ticketDAO->queryTickets($where,$group,$order,$limit);
        
        if($ticket['status']){     
            $ticketObj = $ticket['push']['object']->getGridList();     
            
            foreach($ticketObj as $k=>$v) {
                $star = ($v['flag_opened'] == 1 && $v['status'] != 1) ? '<i class="fa fa-star" />' : '';
                $expire = ($_SESSION['hdk']['SES_HIDE_GRID_PERIOD'] == 0)
                        ? $v['expire_date']
                        : (($v['status'] == 1) ? $this->translator->translate('Not_available_yet') : $this->appSrc->_formatDateHour($v['expire_date']));
                $statusLbl = strlen($v['statusview']) > 25 ? $this->appSrc->_reduceText($v['statusview'],25) : $v['statusview'];

                $data[] = array(
                    'star'          => $star,
                    'ticketCode'    => $v['code_request'],
                    'entryDate'     => $this->appSrc->_formatDateHour($v['entry_date']),
                    'subject'       => $v['subject'],
                    'expiryDate'    => $expire,
                    'inCharge'      => $v['in_charge'],
                    'status'        => "<span style='color:{$v['color_status']}'>{$v['statusview']}</span>"
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

    public function comboTicketFilters(): array
    {
        $aRet = array(
            array("id" => 'department',"text"=>$this->translator->translate('Department')),
            array("id" => 'company',"text"=>$this->translator->translate('Company')),
        );
        
        return $aRet;
    }

    /*
     * en_us Renders the department add ticket screen
     *
     * pt_br Renderiza o template da tela de novo ticket
     */
    public function newTicket()
    {
        /** 
         * The Ticket is not registered as a regular program, as it is not in the menu - it is part of the helpdezk core,
         * so only the test is done to check if it is a user or operator
         *
         */
        if(!in_array($_SESSION['SES_TYPE_PERSON'],array(2,3)))
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenHdkTicket('add');

        $this->view('helpdezk','ticket-create',$params);
    }

    /**
     * en_us Write the ticket information to the DB
     *
     * pt_br Grava no BD as informações da solicitação
     */  

    public function saveTicket()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();

        if($_SESSION['SES_TYPE_PERSON'] == 3){
            $minTelephoneTime = number_format($_POST["open_time"], "2", ".", ",");
            $minAttendanceTime = (int) $_POST["open_time"];
            
            $ownerID    = $_POST["requesterID"];
            $creatorID  = $_SESSION["SES_COD_USUARIO"];
            $wayID      = $_POST["way"];
            $sourceID	= $_POST["source"];
            $solution   = str_replace("'", "`", $_POST["solution"]);

            //if telephone
            if ($sourceID == 2){
                $minTelephoneTime = $minTelephoneTime;
                $minExpendedTime = $minAttendanceTime;
            }else{
                $minTelephoneTime = 0;
                $minExpendedTime = 0;
            }
            $ticketModel->setMinExpendedTime($minTelephoneTime);
            $ticketModel->setMinTelephoneTime($minExpendedTime);
        }else{
            $ownerID    = $_SESSION["SES_COD_USUARIO"];
            $creatorID  = $_SESSION["SES_COD_USUARIO"];
            $wayID      = 1;
            $sourceID	= 1;
        }

        // --- Equipment ---
        $equipmentSerialNumber	= trim(strip_tags($_POST["serialNumber"]));
        $equipmentOS 	= trim(strip_tags($_POST["osNumber"]));
        $equipmentTag 	= trim(strip_tags($_POST["tag"]));

        $areaID = $_POST['area'];
        $typeID = $_POST['type'];
        $itemID = $_POST['item'];
        $serviceID = $_POST['service'];
        $reasonID = (!isset($_POST["reason"]) || in_array($_POST["reason"],array("X","NR"))) ? 0 : $_POST["reason"];
        $subject = trim(strip_tags(str_replace("'", "`", $_POST["subject"])));
        $description = str_replace("'", "`", $_POST["description"]);
        $note = "<p><b>" . $this->translator->translate('Request_opened') . "</b></p>";
        $noteDateTime = date("Y-m-d H:i:s");

        $ticketRulesModel->setItemId($itemID)
                         ->setServiceId($serviceID);
        $totalRules = $ticketRulesDAO->getTotalRules($ticketRulesModel);
        $statusID = (!$totalRules['status'] || $totalRules['push']['object']->getTotalRows() == 0) ? 1 : 2;

        $ticketDate = (!isset($_POST['date']) || empty($_POST['date'])) ? date("Y-m-d") : $this->appSrc->_formatSaveDate($_POST['date']);
        $ticketHour = (!isset($_POST['time']) || empty($_POST['time'])) ? date("H:i") : $_POST['time'];
        $ticketDateHour = "{$ticketDate} {$ticketHour}";
        
        //Setting up the model
        $ticketModel->setTicketCode($this->appSrc->_createRequestCode('hdk'))
                    ->setIdSource($sourceID)
                    ->setIdAttendanceWay($wayID)
                    ->setIdOwner($ownerID)
                    ->setIdCreator($creatorID)
                    ->setIdArea($areaID)
                    ->setIdType($typeID)
                    ->setIdItem($itemID)
                    ->setIdService($serviceID)
                    ->setIdReason($reasonID)
                    ->setSubject($subject)
                    ->setDescription($description)
                    ->setIdStatus($statusID)
                    ->setEntryDate($ticketDateHour)
                    ->setUserType($_SESSION['SES_TYPE_PERSON'])
                    ->setSerialNumber($equipmentSerialNumber)
                    ->setOsNumber($equipmentOS)
                    ->setLabel($equipmentTag)
                    ->setIdCompany($_SESSION['SES_COD_EMPRESA'])
                    ->setIdUserLog($ownerID)
                    ->setLogDate($noteDateTime);
        
        $priorityID = $hdkSrc->_getPriorityId($ownerID,$serviceID);
        if($priorityID)
            $ticketModel->setIdPriority($priorityID);
        
        $expireDate = $hdkSrc->_getTicketExpireDate($ticketDateHour,$priorityID,$serviceID);
        if($expireDate)
            $ticketModel->setExpireDate($expireDate);
        
        $retGroup = $ticketDAO->getServiceGroup($ticketModel);
        if(!$retGroup['status'])
            return false;
        
        if($totalRules['status'] && $totalRules['push']['object']->getTotalRows() > 0){
            $retRules = $ticketRulesDAO->fetchRules($totalRules['push']['object']);
            if(!$retRules['status'])
                return false;
            
            $aApproval = $retRules['push']['object']->getGridList(); 
            $ticketModel->setApprovalList($aApproval);
            foreach($aApproval as $k=>$v){
                if($v['order'] == 1)
                    $approverID = $v['idperson'];
            }
            
            $idGroupRepass = $hdkSrc->_getIdGroupOnlyRepass($retGroup['push']['object']->getIdServiceGroup());

            // -- in charge --
            $aInCharge = array(
                array("id"=> $idGroupRepass,"type" => "G","isInCharge" => 0,"isRepassed" => 'N'),
                array("id"=> $approverID,"type" => "P","isInCharge" => 1,"isRepassed" => 'N')
            );
        }else{
            $idGroupRepass = $hdkSrc->_getIdGroupOnlyRepass($retGroup['push']['object']->getIdServiceGroup());
            // -- in charge --
            $aInCharge = array(
                array("id"=> $idGroupRepass,"type" => "G","isInCharge" => 1,"isRepassed" => 'N')
            );
        }
        $ticketModel->setInChargeList($aInCharge);

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        if($solution && $solution != '<p><br></p>'){
            $solutionNote = "<p><b>" .$this->translator->translate('Solution') . "</b></p>". $solution;
            $bus = array(
                "public"=> 1,"type" => 3,"note" => $solutionNote, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            );

            array_push($aNote,$bus);
        }

        $ticketModel->setNoteList($aNote);
        
        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files
            
            $ticketModel->setAttachments($aAttachs);
        }                
        
        $ins = $ticketDAO->saveTicket($ticketModel);
        /*if($ins['status']){
            $st = true;
            $msg = "";
            $ticketCode = $ins['push']['object']->getRequestCode();
            
            // link attachments to the ticket
            if($aSize > 0){
                $insAttachs = $this->linkTicketAttachments($ins['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                    $ticketCode = "";
                }
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $ticketCode = "";
        } */     
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "inChargeName" => $retDPO['dpoName']
        );       

        echo json_encode($aRet);
    }

    public function formUpdate($departmentID=null)
    {
        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();
        $departmentModel->setIdDepartment($departmentID); 
        
        $departmentUpd = $departmentDAO->getDepartment($departmentModel); 

        $params = $this->makeScreenDepartment('upd',$departmentUpd['push']['object']);
        $params['departmentID'] = $departmentID;
      
        $this->view('helpdezk','department-update',$params);
    }

    public function updateDepartment()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();

        $departmentModel->setIdDepartment($_POST['departmentID'])
                            ->setDepartment(strip_tags(trim($_POST['department'])))
                            ->setIdCompany($_POST['cmbCompany']);       
           
               
        $upd = $departmentDAO->updateDepartment($departmentModel);
        if($upd['status']){
            $st = true;
            $msg = "";
            $departmentID = $upd['push']['object']->getIdDepartment();
            
        }else{
            $st = false;
            $msg = $upd['push']['message'];
        }
           
       
        $aRet = array(
            "success"               => $st,
            "id"                    => $departmentID,
            "department"            => $departmentModel->getDepartment(),
            "company"               => $departmentModel->getIdCompany(),
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Changes department's status
     *
     * pt_br Muda o status do Departamento
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();

        //Setting up the model
        $departmentModel->setIdDepartment($_POST['departmentID'])
                           ->setstatus($_POST['newstatus']);
        
        $upd = $departmentDAO->updateStatus($departmentModel);
        if(!$upd['status']){
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Remove the department from the DB
     *
     * pt_br Remove o Department do BD
     */

    function deleteDepartment()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();
        
        //Setting up the model       
        $departmentModel->setIdDepartment($_POST['departmentID']);

        $del = $departmentDAO->deleteDepartment($departmentModel);
        if(!$del['status']){
            return false;
        } 
        $aRet = array(
            "success"   => true,
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the department has already been registered before
     *
     * pt_br Verifica se o Department já foi cadastrada anteriormente
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $departmentDAO = new departmentDAO();

        $department = strip_tags($_POST['department']);
        $company = strip_tags($_POST['idperson']);

        $where = "AND a.idperson = '$company'  AND pipeLatinToUtf8(UPPER(a.name)) = UPPER('$department')"; 
        $where .= (isset($_POST['departmentID'])) ? " AND iddepartment != {$_POST['departmentID']}" : "";
        

        $check =  $departmentDAO->queryDepartment($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('Department_exists'));
        }else{
            echo json_encode(true);
        }

    }
    
    /**
     * Checks if the user has requests to approve
     *
     * @return void
     */
    public function checkApproval()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_checkApproval();
    }
    
    /**
     * Returns area combo options in html
     *
     * @return void
     */
    public function ajaxArea()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboAreaHtml();
    }
    
    /**
     * Checks the visualization or not of the default parameters of the combos
     *
     * @return void
     */
    public function showDefaults()
    {
        if (isset($_SESSION['hdk']['TKT_DONT_SHOW_DEFAULT']) && $_SESSION['hdk']['TKT_DONT_SHOW_DEFAULT'] == 1) {
            echo 'NO';
        } else {
            echo 'YES';
        }
    }

    public function ajaxTypes()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboTypeHtml($_POST['areaID']);
    }

    public function ajaxItens()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboItemHtml($_POST['typeID']);
    }

    public function ajaxServices()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboServiceHtml($_POST['itemID']);
    }

    public function ajaxReasons()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboReasonHtml($_POST['serviceID']);
    }
}