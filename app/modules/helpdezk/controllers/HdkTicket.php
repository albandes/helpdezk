<?php

use App\core\Controller;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\src\awsServices;

use App\modules\helpdezk\dao\mysql\ticketDAO;
use App\modules\helpdezk\dao\mysql\ticketRulesDAO;
use App\modules\helpdezk\dao\mysql\evaluationDAO;

use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\helpdezk\models\mysql\ticketRulesModel;
use App\modules\helpdezk\models\mysql\evaluationModel;

use Mpdf\Mpdf;

class hdkTicket extends Controller
{
    /**
     * @var string
     */
    protected $saveMode;

    /**
     * @var string
     */
    protected $ticketStoragePath;

    /**
     * @var string
     */
    protected $noteStoragePath;

    /**
     * @var string
     */
    protected $fileBucket;

    /**
     * @var string
     */
    protected $pdfTmp;

    /**
     * @var string
     */
    protected $tmp;

    /**
     * @var string
     */
    protected $downloadDir;

    /**
     * @var string
     */
    protected $bucketUrl;

    public function __construct()
    {
        parent::__construct();

        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';
        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->ticketStoragePath = 'helpdezk/attachments/';
            $this->noteStoragePath = 'helpdezk/noteattachments/';
            $this->bucketUrl = "https://{$bucket}.s3.amazonaws.com/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']){
                $modDir = $this->appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/helpdezk/');
                $this->ticketStoragePath = $this->appSrc->_setFolder($modDir.'attachments/');
                $this->noteStoragePath = $this->appSrc->_setFolder($modDir.'noteattachments/');
                $this->bucketUrl = "{$_ENV['EXTERNAL_STORAGE_URL']}/";
            }else{
                $storageDir = $this->appSrc->_setFolder($this->appSrc->_getHelpdezkPath().'/storage/');
                $upDir = $this->appSrc->_setFolder($storageDir.'uploads/');
                $modDir = $this->appSrc->_setFolder($upDir.'helpdezk/');
                $this->ticketStoragePath = $this->appSrc->_setFolder($modDir.'attachments/');
                $this->noteStoragePath = $this->appSrc->_setFolder($modDir.'noteattachments/');
                $this->bucketUrl = "{$_ENV['HDK_URL']}/";                
            }
        }

        // -- pdf directory
        $storageDir = $this->appSrc->_setFolder($this->appSrc->_getHelpdezkPath().'/storage/');
        $downDir = $this->appSrc->_setFolder($storageDir.'downloads/');
        $this->pdfTmp = $this->appSrc->_setFolder($downDir.'pdfTmp/'); // temporary directory
        $this->tmp = $this->appSrc->_setFolder($downDir.'tmp/'); // temporary directory
        $this->downloadDir = "{$_ENV['HDK_URL']}/storage/downloads/tmp/"; // url to download pdf

        // set program permissions
        $this->programId = $this->appSrc->_getKernelProgramIdByName(__CLASS__);
        $this->appSrc->_saveProgramAccess($_SESSION['SES_COD_USUARIO'],$this->programId);

    }
    
    public function index($myTickets=null)
    {
        $this->appSrc->_sessionValidate();

        $params = $this->makeScreenHdkTicket('idx',null,$myTickets);

		$this->view('helpdezk','ticket',$params);
    }
    
    /**
     * makeScreenHdkTicket
     *
     * @param  mixed $option        Indicates on which screen the parameters will be used - (idx = index [grid] | add = new record | upd = update record)
     * @param  mixed $obj           Object with ticket's data
     * @param  mixed $myTickets     Indicates whether the viewing screen is for an attendant as a user  
     * @return void
     */
    public function makeScreenHdkTicket($option='idx',$obj=null,$myTickets=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $hdkSrc->_makeNavHdk($params);

        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        if($myTickets){
            $params['typeUser'] = 2;
            $params['flgOperator'] = 1;
            $params['operatorAsUser'] = 1;
        }else{
            $params['typeUser'] = $_SESSION['SES_TYPE_PERSON'];
            $params['flgOperator'] = 0;
            $params['operatorAsUser'] = 0;
        }
        // -- Datepicker settings -- 
        $params = $this->appSrc->_datepickerSettings($params);

        if($option == 'idx'){
            if($params['typeUser'] == 3){ // attendant
                // -- View by Expiry date type --
                $params['cmbTypeExpireDate'] = $hdkSrc->_comboTypeExpireDate();

                // -- Type View --
                $params['cmbViewType'] = $hdkSrc->_comboTypeView();
            }

            // -- Search action --
            $params['cmbFilters'] = ($params['typeUser'] == 2) ? $this->comboUserTicketFilters() : $this->comboAttendantTicketFilters();
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
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
            $params['equipment'] = isset($_SESSION['hdk']['SES_IND_EQUIPMENT']) ? $_SESSION['hdk']['SES_IND_EQUIPMENT'] : 0;
        }

        if($option == 'add'){            
            if($_SESSION['SES_TYPE_PERSON'] == 3){
                $params['owner'] = "";
                $params['cmbUser'] = $hdkSrc->_comboUsers();
                $params['ownerID'] = $_SESSION['SES_COD_USUARIO'];
                $params['cmbSource'] = $hdkSrc->_comboSource();
                $params['sourceID'] = 1; //SET HELPDEZK AS DEFAULT
                $params['cmbAttendanceType'] = $hdkSrc->_comboAttendanceType();
                $params['attendanceTypeID'] = 1;
                $params['timer'] = ($_SESSION['hdk']['SES_IND_TIMER_OPENING'] == 1) ? 1 : 0;

                $params['cmbAssumeGroups'] = $hdkSrc->_comboAttendantGroups($_SESSION['SES_COD_USUARIO']);
                $params['showGroupTrack'] = 1;
            }else{
                $params['owner'] = $_SESSION['SES_NAME_PERSON'];
            }

            if(isset($_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS']) && $_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS']){
                $params['showextrafields'] = 1;
            }
        }

        if($option=='upd'){
            $params['owner'] = $obj->getOwner();
            $params['ownerID'] = $obj->getIdOwner();
            $params['sourceName'] = $obj->getSource();
            $params['creatorName'] = $obj->getCreator();
            $params['department'] = $obj->getDepartment();
            $params['formatedTicketCode'] = $hdkSrc->_formatTicketCode($obj->getTicketCode());

            $obj->setNoteIsOpen(0)
                ->setIdUser($_SESSION['SES_COD_USUARIO']);
            
            if($params['typeUser'] == 3){ // attendant's view
                $params['cmbArea'] = $hdkSrc->_comboArea();
                $params['areaID'] = $obj->getIdArea();
                $params['cmbType'] = $hdkSrc->_comboType($obj->getIdArea());
                $params['typeID'] = $obj->getIdType();
                $params['cmbItem'] = $hdkSrc->_comboItem($obj->getIdType());
                $params['itemID'] = $obj->getIdItem();
                $params['cmbService'] = $hdkSrc->_comboService($obj->getIdItem());
                $params['serviceID'] = $obj->getIdService();
                $aReason = $hdkSrc->_comboReason($obj->getIdService());
                $aReason = (count($aReason) <= 0) ? array(array("id"=>"NR","text"=> $this->translator->translate('Reason_no_registered'))) : $aReason;
                $params['cmbReason'] = $aReason;
                $params['reasonID'] = empty($obj->getReason()) ? "NR": $obj->getIdReason();
                $params['cmbPriority'] = $hdkSrc->_comboPriority();
                $params['priorityID'] = $obj->getIdPriority();
                $params['attendanceTypeID'] = $obj->getIdAttendanceWay();
                $params['cmbAttendanceType'] = $hdkSrc->_comboAttendanceType();
                $params['attendanceTypeID'] = $obj->getIdAttendanceWay();
                $params['cmbHourType'] = $hdkSrc->_comboHourType();
                $params['hourTypeDefault'] = 1;
                $params['cmbNoteVisibility'] = $hdkSrc->_comboNoteType();
                $params['noteTypeDefault'] = 1;
                $params['deadlineExtensionNumber'] = $obj->getExtensionsNumber();

                $params['checkedAssume'] =  ($_SESSION['hdk']['SES_SHARE_VIEW'] == 1) ? true : false;
                $params['cmbAssumeGroups'] = $hdkSrc->_comboAttendantGroups($_SESSION['SES_COD_USUARIO']);
                
            }else{
                $params['areaName'] = $obj->getArea();
                $params['typeName'] = $obj->getType();
                $params['itemName'] = $obj->getItem();
                $params['serviceName'] = $obj->getService();
                $params['reason'] = empty($obj->getReason()) ? $this->translator->translate('Reason_no_registered'): $obj->getReason();
                $params['priorityName'] = $obj->getPriority();
                $params['attendanceType'] = $obj->getAttendanceWay();
                
                if($params['typeUser'] == 2 && $obj->getNoteIsOpen() == 1){
                    $updTicketFlag = $ticketDAO->updateTicketFlag($obj);
                    if($updTicketFlag['status']){
                        $this->logger->info("Ticket's opening flag updated", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    }
                }
            }
            
            $updNoteFlag = $ticketDAO->updateNoteFlag($obj);
            if($updNoteFlag['status']){
                $this->logger->info("Note's view flag updated. Ticket # {$obj->getTicketCode()}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }

            $params['status'] = $obj->getStatus();
            $params['statusID'] = $obj->getIdStatus();
            $params['statusColor'] = str_replace('#','',$obj->getColor());
            $params['openingDate'] = $this->appSrc->_formatDateHour($obj->getEntryDate());
            $params['inChargeName'] = $obj->getInCharge();
            $params['attendanceDeadline'] = $this->appSrc->_formatDateHour($obj->getExpireDate());
            $params['currentDeadlineDate'] = $this->appSrc->_formatDate($obj->getExpireDate());
            $params['currentDeadlineTime'] = $this->appSrc->_formatHour($obj->getExpireDate());
            $retAuxAttendant = $this->makeAuxAttendantScreen($obj->getTicketCode(),true);
            $params['auxiliaryAttendantList'] = $retAuxAttendant['auxAttendantLine'];
            $params['inChargeID'] = $obj->getIdInCharge();
            $params['inChargeType'] = $obj->getInChargeType();
            $params['requireTaskTime'] = $_SESSION['hdk']['SES_IND_ENTER_TIME'];
            $params['allowEmptyNote'] = (isset($_SESSION['hdk']['SES_EMPTY_NOTE'])) ? $_SESSION['hdk']['SES_EMPTY_NOTE'] : 0;
            
            if(isset($_SESSION['hdk']['SES_IND_EQUIPMENT']) && $_SESSION['hdk']['SES_IND_EQUIPMENT'] == 1){
                $params['equipmentTag'] = $obj->getLabel();
                $params['equipmentOsNumber'] = $obj->getOsNumber();
                $params['equipmentTag'] = $obj->getStatus();
            }

            $extensionNumberLimit = $_SESSION['hdk']['SES_QT_PRORROGATION']; // deadline extension change button
            if (!isset($extensionNumberLimit) || is_null($extensionNumberLimit)) {
                $params['showBtnDeadlineChange'] = 1;
            } else{
                if ($extensionNumberLimit == 0) {
                    $params['showBtnDeadlineChange'] = 0;
                } elseif ($obj->getExtensionsNumber() < $extensionNumberLimit) {
                    $params['showBtnDeadlineChange'] = 1;
                } else {
                    $params['showBtnDeadlineChange'] = 0;
                }  
            }          

            $params['ticketSubject'] = $obj->getSubject();
            $params['ticketDescription'] = $obj->getDescription();

            // attachments
            $retAttachments = $hdkSrc->_getTicketAttachments($obj->getTicketCode());
            $params['hasAttachment'] = $retAttachments['hasAttachment'];
            $params['attach_files'] = $retAttachments['attachmentHtml'];

            $params = $this->makeViewTicketBtns($params,$obj->getTicketCode(),$obj->getIdStatus(),$obj->getIdInCharge());
            $params['notes'] = $this->makeNotesScreen($obj->getTicketCode(),$obj->getIdStatus(),$params['typeUser'],$params['ownerID']);
            $params['showInsertNote'] = (in_array($params['statusID'],array(2,3)) || ($params['statusID'] == 1 && $params['ownerID'] == $_SESSION['SES_COD_USUARIO'])) ? 1 : 0;

            $params['momentFormat'] = ($_ENV['DEFAULT_LANG'] == 'en_us') ? "MM/DD/YYYY" : "DD/MM/YYYY";

            if(isset($_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS']) && $_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS'] == 1){
                $params = $this->makeTicketExtraFieldScreen($params,$obj->getTicketCode());
            }
            
        }
        //echo "<pre>",print_r($params,true),"</pre>";
        return $params;
    }
    
    /**
     * en_us Returns tickets recorded to show in the grid - user view
     * pt_br Retorna os tickets gravados para mostrar no grid - tela do usuário
     *
     * @return void
     */
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
                case "ticketCode":
                    $filterIndx = "a.code_request";
                    break;
                case "openingDate":
                    $filterIndx = "a.entry_date";
                    break;
                case "subject":
                    $filterIndx = "a.subject";
                    break;
                case "deadline":
                    $filterIndx = "a.expire_date";
                    break;
                case "inCharge":
                    $filterIndx = "resp.name";
                    break;
                case "status":
                    $filterIndx = "b.user_view";
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
            $where .= " AND " . " (pipeLatinToUtf8(a.code_request) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(a.entry_date) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(a.expire_date) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(resp.name) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(a.subject) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(b.user_view) LIKE '%{$quickValue}%')";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "code_request";

        switch($sortIndx){
            case "ticketCode":
                $sortIndx = "code_request";
                break;
            case "entryDate":
                $sortIndx = "entry_date";
                break;
            case "expiryDate":
                $sortIndx = "expire_date";
                break;
            case "inCharge":
                $sortIndx = "in_charge";
                break;
            case "star":
                $sortIndx = "flag_opened";
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
    
    /**
     * en_us Returns tickets recorded to show in the grid - attendant view
     * pt_br Retorna os tickets gravados para mostrar no grid - tela do atendente
     *
     * @return void
     */
    public function jsonGridAttendant()
    {
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $where = "";
        $idStatus = $_POST['idStatus'];

        // -- search by deadline date type
        $deadlineType = (isset($_POST['deadlineType'])) ? $_POST['deadlineType'] : 0;

        switch ($deadlineType) {
            case 1:
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."req.expire_date >= now()";
                break;
            case 2:
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."DATE(req.expire_date) >= DATE(now())";
                break;
            case 3:
                $idStatus = 3;
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."req.expire_date <= now()";
                break;
            case 4:
                $idStatus = 1;
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."req.expire_date <= now()";
                break;
            default:
                $where .= '';
                break;
        }

        // -- ticket's status
        if($idStatus){
            if ($idStatus == 'ALL') {
                $where = '';
            } else {
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."stat.idstatus_source = {$idStatus} ";
            }
        }else{
            $where .= ((empty($where)) ? "WHERE " : "AND ") ."stat.idstatus_source = 1 ";
        }

        if($_ENV['LICENSE'] == '200701006'){
            $where .= ((empty($where)) ? "WHERE " : "AND ") ."`req`.`iditem` <> 124 ";
        }
       
        if(isset($_SESSION['SES_PERSON_GROUPS'])){
            $ticketModel->setIdGroupList($_SESSION['SES_PERSON_GROUPS']);
        
            // search attendant's group real id
            $retGroups = $ticketDAO->fetchAttendantGroupRealID($ticketModel);
            if(!$retGroups['status']){
                $this->logger->error("Can't get attendant's group real id", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }
            
            $attendantGroups = ($retGroups['status'] && count($retGroups['push']['object']->getGroupRealIDList()) > 0) ? array_column($retGroups['push']['object']->getGroupRealIDList(),"idperson"): array();
            $attendantGroups = (count($attendantGroups) > 0) ? implode(",",$attendantGroups) : "";
        }else{
            $attendantGroups = "";
        }

        $viewType = (!isset($_POST['viewType'])) ? 1 : $_POST['viewType'];

        switch ($viewType) {
            case 2:
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."((inch.ind_in_charge = 1
										AND inch.id_in_charge IN({$_SESSION['SES_COD_USUARIO']}))
										OR (inch.ind_operator_aux = 1
											AND inch.id_in_charge = {$_SESSION['SES_COD_USUARIO']})
										OR (inch.id_in_charge IN({$_SESSION['SES_COD_USUARIO']} )
											and inch.ind_track = 1))";
                break;
            case 3:
                $where .= ((empty($where)) ? "WHERE " : "AND ") .	"((inch.ind_in_charge = 1 AND inch.id_in_charge IN($attendantGroups))
								OR (inch.id_in_charge in($attendantGroups)
											AND inch.ind_track = 1))";
                break;
            default:
                $cond = (!empty($_SESSION['SES_COD_USUARIO'])) ? "{$_SESSION['SES_COD_USUARIO']},$attendantGroups" : $_SESSION['SES_COD_USUARIO'];
                $where .= ((empty($where)) ? "WHERE " : "AND ") ."((inch.ind_in_charge = 1
                            AND inch.id_in_charge IN($cond))
                            OR (inch.ind_operator_aux = 1
                                AND inch.id_in_charge = {$_SESSION['SES_COD_USUARIO']})
                            OR (inch.id_in_charge IN($cond)
                                AND inch.ind_track = 1)) ";
                break;
        }
        
        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];           
                   
            switch($filterIndx){
                case "ticketCode":
                    $filterIndx = "req.code_request";
                    break;
                case "openingDate":
                    $filterIndx = "req.entry_date";
                    break;
                case "company":
                    $filterIndx = "comp.name";
                    break;
                case "owner":
                    $filterIndx = "pers.name";
                    break;
                case "subject":
                    $filterIndx = "req.subject";
                    break;
                case "description":
                    $filterIndx = "req.description";
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
            $where .= " AND " . " (pipeLatinToUtf8(req.code_request) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(req.entry_date) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(comp.name) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(pers.name) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(req.subject) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(stat.name) LIKE '%{$quickValue}%')";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "ticketCode";

        switch($sortIndx){
            case "ticketCodeLink":
                $sortIndx = "req.code_request";
                break;
            case "entryDate":
                $sortIndx = "entry_date";
                break;
            case "expiryDate":
                $sortIndx = "expire_date";
                break;
            case "owner":
                $sortIndx = "personname";
                break;
            case "inCharge":
                $sortIndx = "in_charge";
                break;
            case "attachments":
                $sortIndx = "total_attachs";
                break;
            case "star":
                $sortIndx = "flag_opened";
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
        $countTicket = $ticketDAO->countAttendantTickets($where); 
        if($countTicket['status']){
            $total_Records = $countTicket['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
         
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $ticket = $ticketDAO->queryAttendantTickets($where,$group,$order,$limit);
        
        if($ticket['status']){     
            $ticketObj = $ticket['push']['object']->getGridList();     
            
            foreach($ticketObj as $k=>$v) {
                $star = ($v['flag_opened'] == 1 && $v['status'] != 1) ? '<i class="fa fa-star" />' : '';
                $expire = $this->highlightDeadline($v['expire_date'],$v['idstatus_source']);
                $lblTotalAttachs = ($v['total_attachs'] > 0) ? "<span class='label label-primary'>".$v['total_attachs']."</span>" : " ";
                $ticketCodeLink = $this->makeLinkCode($v['id_in_charge'],$v['typeincharge'],$_SESSION['SES_COD_USUARIO'],$v['ind_track'],$v['code_request']);

                $data[] = array(
                    'star'          => $star,
                    'attachments'   => $lblTotalAttachs,
                    'ticketCode'    => $v['code_request'],
                    'ticketCodeLink'=> $ticketCodeLink,
                    'entryDate'     => $this->appSrc->_formatDateHour($v['entry_date']),
                    'company'       => $v['company'],
                    'owner'         => $v['personname'],
                    'type'          => $v['type'],
                    'item'          => $v['item'],
                    'service'       => $v['service'],
                    'subject'       => $v['subject'],
                    'expiryDate'    => $expire,
                    'inCharge'      => $v['in_charge'],
                    'status'        => "<span style='color:{$v['status_color']}'>{$v['status']}</span>",
                    'priority'      => "<span style='color:{$v['priority_color']}'>{$v['priority']}</span>"
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
     * en_us Makes array with search filters - user view
     * pt_br Crea array com os filtros de pesquisa - tela do usuário
     *
     * @return array
     */
    public function comboUserTicketFilters(): array
    {
        $aRet = array(
            array("id" => 'ticketCode',"text"=>"Nº","searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'openingDate',"text"=>$this->translator->translate('Grid_opening_date'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'subject',"text"=>$this->translator->translate('Grid_subject'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'deadline',"text"=>$this->translator->translate('Grid_expire_date'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'inCharge',"text"=>$this->translator->translate('Grid_incharge'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'status',"text"=>$this->translator->translate('status'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }
    
    /**
     * en_us Makes array with search filters - attendant view
     * pt_br Crea array com os filtros de pesquisa - tela do atendente
     *
     * @return array
     */
    public function comboAttendantTicketFilters(): array
    {
        $aRet = array(
            array("id" => 'ticketCode',"text"=>"Nº","searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'openingDate',"text"=>$this->translator->translate('Grid_opening_date'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'company',"text"=>$this->translator->translate('Company'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'owner',"text"=>$this->translator->translate('From'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'subject',"text"=>$this->translator->translate('Grid_subject'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'description',"text"=>$this->translator->translate('Description'),"searchOpt"=>array('eq', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the add ticket screen template
     *
     * pt_br Renderiza o template da tela de novo ticket
     */
    public function newTicket()
    {
        $this->appSrc->_sessionValidate();

        /** 
         * The Ticket is not registered as a regular program, as it is not in the menu - it is part of the helpdezk core,
         * so only the test is done to check if it is a user or operator
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
            $minTelephoneTime = number_format($_POST["openTime"], "2", ".", ",");
            $minAttendanceTime = (int) $_POST["openTime"];
            
            $ownerID    = $_POST["ownerID"];
            $creatorID  = $_SESSION["SES_COD_USUARIO"];
            $wayID      = $_POST["attendanceTypeID"];
            $sourceID	= $_POST["sourceID"];
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
        $statusID = (!$totalRules['status'] || $totalRules['push']['object']->getTotalRows() == 0) ? 1 : 58;

        $ticketDate = (!isset($_POST['ticketDate']) || empty($_POST['ticketDate'])) ? date("Y-m-d") : $this->appSrc->_formatSaveDate($_POST['ticketDate']);
        $ticketHour = (!isset($_POST['ticketTime']) || empty($_POST['ticketTime'])) ? date("H:i") : $_POST['ticketTime'];
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
            $ticketModel->setApprovalList(array());

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
        
        // -- add extra fields to model
        if($_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS'] && (isset($_POST['extraFields']) && count($_POST['extraFields']) > 0)){
            $ticketModel->setExtraFieldList($_POST['extraFields']);
        }
        
        $ins = $ticketDAO->saveTicket($ticketModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $ticketCode = $ins['push']['object']->getTicketCode();

            $retInCharge = $ticketDAO->getInChargeByTicketCode($ins['push']['object']);
            $inChargeName = ($retInCharge['status']) ? $retInCharge['push']['object']->getInCharge() : "";
            
            $expiryDate = $this->appSrc->_formatDateHour($expireDate);
            
            // link attachments to the ticket
            if($aSize > 0){
                $insAttachs = $this->linkTicketAttachments($ins['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                    $ticketCode = "";
                    $inChargeName = "";
                    $expiryDate = "";
                }
            }else{
                $this->logger->info("Ticket # {$ticketCode} was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $ticketCode = "";
            $inChargeName = "";
            $expiryDate = "";
            $this->logger->error("Unable to create a ticket. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "new-ticket-user",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "inChargeName" => $inChargeName,
            "expiryDate" => $expiryDate
        );       

        echo json_encode($aRet);
    }
    
    /**
     * en_us Renders the ticket view screen template
     *
     * pt_br Renderiza o template da tela de visualização ticket
     *
     * @param  mixed $ticketCode    Ticket code
     * @param  mixed $urlToken      URL's token to access ticket from email
     * @param  mixed $myTicket      Indicates if an attendant is the owner of ticket
     * @return void
     */
    public function viewTicket($ticketCode=null,$urlToken=null,$myTicket=null)
    {
        //-- set session by token
        if (!is_null($urlToken) && $urlToken > 0){
            $hdkSrc = new hdkServices();
            if (!$hdkSrc->_tokenAuthentication($ticketCode,$urlToken)) {
                $this->appSrc->_accessDenied();
            }
        }

        $this->appSrc->_sessionValidate();
        
        if(!in_array($_SESSION['SES_TYPE_PERSON'],array(2,3)))
            $this->appSrc->_accessDenied();
        

        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode); 
        
        $ticket = $ticketDAO->getTicket($ticketModel); 

        $params = $this->makeScreenHdkTicket('upd',$ticket['push']['object'],$myTicket);
        $params['ticketCode'] = $ticketCode;
      
        $this->view('helpdezk','ticket-update',$params);
    }
    
    /**
     * en_us Updates the ticket with the status canceled
     * pt_br Atualiza o ticket com o status cancelado
     *
     * @return json
     */
    public function cancelTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $note = "<p><b>" . $this->translator->translate('Request_canceled') . "</b></p>";
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus(11)
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime);

        $ret = $ticketDAO->saveCancelTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Ticket # {$ticketCode} was canceled successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Unable to cancel ticket# {$ticketCode}. Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "new-ticket-user",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Record the evaluation of the request attendance
     * pt_br Registrar a avaliação do atendimento da solicitação
     *
     * @return json
     */
    public function evaluateTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $evaluationDAO = new evaluationDAO();
        $evaluationModel = new evaluationModel();

        $ticketCode = $_POST['ticketCode'];
        $isApproved = $_POST['approve'];
        $noteDateTime = date("Y-m-d H:i:s");
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        //Setups status, reopen ticket and notes
        switch($isApproved){
            case "A":
                $statusID = 5;
                $reopened = 1;
                $note = "<p><b>" . $this->translator->translate('Request_closed') . "</b></p>";

                // -- notes --        
                $aNote = array(
                    array(
                        "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                        "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" => 0,
                        "ipAddress" => $ipAddress, "callback" => 0
                    )
                );

                break;

            case "N":
                $statusID = 3;
                $reopened = 1;
                $note = "<p><b><span style='color: #FF0000;'>" . $this->translator->translate('Request_not_approve') . "</span></b></p>
                         <p><strong>" . $this->translator->translate('Reason') . "</strong>: ". nl2br($_POST['observation']) ."</p>";

                // -- notes --        
                $aNote = array(
                    array(
                        "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                        "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" => 0,
                        "ipAddress" => $ipAddress, "callback" => 0
                    )
                );

                break;
            
            case "O":
                $statusID = 5;
                $reopened = 0;
                $note = "<p><b>" . $this->translator->translate('Request_closed') . "</b></p>
                         <p><strong>" . $this->translator->translate('Reason') . "</strong>: ". nl2br($_POST['observation']) ."</p>";

                // -- notes --        
                $aNote = array(
                    array(
                        "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                        "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" => 0,
                        "ipAddress" => $ipAddress, "callback" => 0
                    )
                );

                break;

        }        

        //Setting up the model
        $evaluationModel->setTicketCode($ticketCode)
                        ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                        ->setIdStatus($statusID)
                        ->setNoteList($aNote)
                        ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                        ->setLogDate($noteDateTime)
                        ->setAnswerList($_POST['question'])
                        ->setIsApproved($isApproved);

        $ret = $evaluationDAO->saveTicketEvaluation($evaluationModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Evaluate ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying save evaluation, ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "evaluate-ticket",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates the ticket with the status reopened
     * pt_br Atualiza o ticket com o status reaberto
     *
     * @return json
     */
    public function reopenTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $note = "<p><b><span style='color: #FF0000;'>" . $this->translator->translate('Request_reopened') . "</span></b></p>";
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus(1)
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime)
                    ->setIsReopened("1");

        $ret = $ticketDAO->saveReopenTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Reopen ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying reopen ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "reopen-ticket",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Checks if the user has requests to approve
     * 
     * pt_br Verifica se o usuário tem solicitações para aprovar
     *
     * @return void
     */
    public function checkApproval()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_checkApproval();
    }
    
    /**
     * en_us Returns area's combo options in html
     * pt_br Retorno as opções do combo área em html
     *
     * @return string
     */
    public function ajaxArea()
    {
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboAreaHtml();
    }
    
    /**
     * en_us Checks the visualization or not of the default parameters of the combos
     * pt_br Verifica a visualização ou não dos parâmetros padrão dos combos
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

    /**
     * en_us Uploads the file in the directory
     *
     * pt_br Carrega o arquivo no diretório
     */
    function saveTicketAttachments()
    {
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");

            if($this->saveMode == 'disk'){
                $targetFile =  $this->ticketStoragePath.$fileName;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logger->info("Ticket's attachment saved. {$targetFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logger->error("Error trying save Ticket's attachment: {$fileName}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
                    
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = new awsServices();
                
                $arrayRet = $aws->_copyToBucket($tempFile,$this->ticketStoragePath.$fileName);
                
                if($arrayRet['success']) {
                    $this->logger->info("Save temp attachment file {$fileName}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    $this->logger->error("I could not save the temp file: {$fileName} in S3 bucket !! Error: {$arrayRet['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }             

            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
        }
        
        exit;
    }
    
    /**
     * Link Ticket to uploaded files
     *
     * @param  ticketModel $ticketModel
     * @return array
     */
    public function linkTicketAttachments(ticketModel $ticketModel): array
    {
        $ticketDAO = new ticketDAO();
        $aAttachs = $ticketModel->getAttachments();
        
        foreach($aAttachs as $key=>$fileName){
            $ticketModel->setFileName($fileName);

            $ins = $ticketDAO->insertTicketAttachment($ticketModel);
            if(!$ins['status']) {
                $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}");
            }

            $extension = strrchr($fileName, ".");
            $attachmentID = $ins['push']['object']->getIdAttachment();
            $newFile = $attachmentID.$extension;
            $ins['push']['object']->setNewFileName($newFile);

            if($attachmentID <= 0){
                $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: Attacchment Id not found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}");
            }

            if($this->saveMode == 'disk'){
                $targetOld = $this->ticketStoragePath.$fileName;
                $targetNew =  $this->ticketStoragePath.$newFile;
                if(!rename($targetOld,$targetNew)){
                    $del = $ticketDAO->deleteTicketAttachment($ins['push']['object']);
                    if(!$del['status']) {
                        $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: {$del['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getRequestCode()}");
                    }
                    $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()} in disk!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket #{$ticketModel->getRequestCode()}");
                }
                
            }elseif($this->saveMode == 'aws-s3'){
                $aws = new awsServices();
                $arrayRet = $aws->_renameFile("{$this->ticketStoragePath}{$fileName}","{$this->ticketStoragePath}{$newFile}");
                
                if($arrayRet['success']) {
                    $this->logger->info("Rename ticket attachment file {$fileName} to {$newFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                } else {
                    $del = $ticketDAO->deleteTicketAttachment($ins['push']['object']);
                    if(!$del['status']) {
                        $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: {$del['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getRequestCode()}");
                    }

                    $this->logger->error("I could not rename ticket attachment file {$fileName} to {$newFile} in S3 bucket !! Error: {$arrayRet['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}");
                }
            
            }
        }

        return array("success"=>true,"message"=>"");
    }
    
    /**
     * en_us Setups ticket's view screen buttons
     * pt_br Configura os botões da tela de visualização da solicitação
     *
     * @param  mixed $params        Array with others parameters
     * @param  mixed $ticketCode    Ticket code
     * @param  mixed $idStatus      Ticket status
     * @param  mixed $inChargeID    In charge Id
     * @return array $params
     */
    function makeViewTicketBtns($params,$ticketCode,$idStatus,$inChargeID)
    {
        $hdkSrc = new hdkServices();
        $awsSrc = new awsServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $evaluationDAO = new evaluationDAO();
        $evaluationModel = new evaluationModel();
        
        $ticketModel->setIdStatus($idStatus);

        if($idStatus == 2){
            $switchStatusID = 2;
        }else{
            $ret = $ticketDAO->getIdStatusSource($ticketModel);
            if(!$ret['status']){
                return false;
            }
            $switchStatusID = $ret['push']['object']->getIdStatusSource();
        }

        if($params['typeUser'] == 2){
            switch($switchStatusID){
                case "1": //NEW
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 1;
                    $params['displayevaluate'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;

                case "2": //REPASSED
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayevaluate'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;

                case "3": //ON ATTENDANCE
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayevaluate'] = 0;
                    $params['displaynote'] = 1;
                    $params['displayprint'] = 1;
                    break;

                case "4": //WAITING FOR APP

                    $q = 0;
                    if ($_SESSION['hdk']['SES_EVALUATE'] == 1) {
                        $eval = "";
                        
                        $retQuestions = $evaluationDAO->fetchQuestions($evaluationModel);
                        if($retQuestions['status']){
                            $questions = $retQuestions['push']['object']->getQuestionList();

                            foreach($questions as $key=>$val) {
                                $idQuestion = $val['idquestion'];
                                $question = $val['question'];
                                $retQuestions['push']['object']->setIdQuestion($idQuestion);
    
                                $eval .= "<div class='row white-bg g-2 mt-2 questionsLine d-none'>                        
                                        <div class='row col-sm-12 text-start'>
                                            <label for='question-{$idQuestion}' class='hdk-label col-form-label control-label'>{$question}</label>
                                        </div>";
                                $eval .= '<div class="row col-sm-12">';
                                
                                $retAnswers = $evaluationDAO->fetchAnswers($retQuestions['push']['object']);
                                if($retAnswers['status']){
                                    $answers = $retAnswers['push']['object']->getAnswerList();

                                    $sel = 0;
                                    $chk = 0;
                                    foreach($answers as $k=>$v) {
                                        if($v['checked'] == 1){
                                            $checked = "checked='checked'";
                                            $chk = 1;
                                        } else {
                                            if(count($v) == $sel+1 && $chk == 0){
                                                $checked = "checked='checked'";
                                            }else{
                                                $checked = "";
                                            }
                                        }

                                        $idanswer   = $v['idevaluation'];
                                        $answer     = $v['name'];

                                        if($this->saveMode == 'aws-s3'){
                                            $retLogo = $awsSrc->_getFile("icons/{$v['icon_name']}");
                                            $ico = $retLogo['fileUrl'];
                                        }else{
                                            if ($this->_externalStorage) {
                                                $ico = $_ENV['EXTERNAL_STORAGE_URL'] .'/icons/'. $v['icon_name'];
                                            } else {
                                                $ico = $_ENV['HDK_URL'].'/storage/uploads/icons/'. $v['icon_name'];
                                            }
                                        }
        
                                        $name = 'question-' . $idQuestion ;
                                        $eval .= "<div class='row g-1 ms-1 radio i-checks'>
                                                    <label> 
                                                        <input type='radio' name='question[{$idQuestion}]' id='{$name}' value='{$idanswer}' {$checked} required> 
                                                        <i></i>&nbsp;&nbsp;<img src='$ico' height='14' />&nbsp;&nbsp;{$answer}
                                                    </label>
                                                </div>";
                                    }
                                }

                                $q++;
                                $eval .= "</div>
                                        </div>";
                            }
                        }

                        $params['questions'] = $eval;
                    }
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    if ($_SESSION['hdk']['SES_EVALUATE'] == 1 && $q != 0) {
                        $params['displayevaluate'] = 1;
                        $params['displayprint'] = 1;
                        //$params['numQuest'] = $q);
                    } else {
                        $params['displayevaluate'] = 0;
                        $params['displayprint'] = 1;
                        $params['evaluationform'] = '';
                    }
                    $params['displaynote'] = 0;
                    break;

                case "5": //FINISHED
                    if($_SESSION['hdk']['SES_IND_REOPEN_USER'] == 1)
                        $params['displayreopen'] = 1;
                    else
                        $params['displayreopen']= 0;
                    $params['displaycancel'] = 0;
                    $params['displayevaluate'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;

                case "6": //REJECTED
                    $params['displayreopen']= 0;
                    $params['displaycancel'] = 0;
                    $params['displayevaluate'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;

                default:
                    $params['displayreopen']= 0;
                    $params['displaycancel'] = 0;
                    $params['displayevaluate'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
            }
        }else{
            $attendantID = $_SESSION['SES_COD_USUARIO'];
            $ticketModel->setTicketCode($ticketCode)
                        ->setIdGroupList($_SESSION['SES_PERSON_GROUPS']);
            
            // search attendant's group real id
            $retGroups = $ticketDAO->fetchAttendantGroupRealID($ticketModel);
            if(!$retGroups['status']){
                $this->logger->error("Can't get attendant's group real id", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }

            $attendantGroups = ($retGroups['status'] && count($retGroups['push']['object']->getGroupRealIDList()) > 0) ? array_column($retGroups['push']['object']->getGroupRealIDList(),"idperson"): array();
            
            // search approval rules
            $retTicketApprover = $ticketDAO->fetchTicketApprover($ticketModel);
            if(!$retTicketApprover['status']){
                $this->logger->error("Can't get ticket's aprrovers", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }

            $ticketApprovers = $retTicketApprover['push']['object']->getTicketApproversList();
            if(count($ticketApprovers) > 0){
                foreach($ticketApprovers as $k=>$v){
                    if($v['idperson'] == $_SESSION['SES_COD_USUARIO'] && $v['order'] == 1){
                        $switchStatusID = "app1";
                    }elseif($v['idperson'] == $_SESSION['SES_COD_USUARIO'] && $v['order'] > 1){
                        $switchStatusID = "app2";
                    }
                }
            }
           
            // search auxiliary attendants
            $retAuxiliaryAttendant = $hdkSrc->_comboAuxiliaryAttendant($ticketCode,true);
            if(!$retAuxiliaryAttendant && !is_array($retAuxiliaryAttendant)){
                $this->logger->error("Can't get ticket's auxiliary attendants. ticket # {$ticketCode}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }

            $auxiliaryAttendants = ($retAuxiliaryAttendant && count($retAuxiliaryAttendant) > 0) ? array_column($retAuxiliaryAttendant,"id"): array();
            
            switch($switchStatusID){
                case "app1":
                    $params['displaychanges'] = 0;
                    $params['displayassume'] = 0;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 0;
                    $params['displayreject'] = 0;
                    $params['displayclose'] = 0;
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 1;
                    $params['displayreturn'] = 0;
                    $params['displayreprove'] = 1;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
                case "app2":
                    $params['displaychanges'] = 0;
                    $params['displayassume'] = 0;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 0;
                    $params['displayreject'] = 0;
                    $params['displayclose'] = 0;
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 1;
                    $params['displayreturn'] = 1;
                    $params['displayreprove'] = 1;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
                case "1": //NEW
                    $params['displaychanges'] = 1;
                    $params['displayassume'] = 1;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 1;
                    $params['displayreject'] = 1;
                    $params['displayclose'] = 0;
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 0;
                    $params['displayreturn'] = 0;
                    $params['displayreprove'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
                case "2": //REPASSED
                    if(in_array($inChargeID, $attendantGroups) || $inChargeID == $attendantID){
                        //I am in charge of this request
                        $params['displaychanges'] = 1;
                        $params['displayassume'] = 1;
                        $params['displayopaux'] = 0;
                        $params['displayrepass'] = 1;
                        $params['displayreject'] = 1;
                        $params['displayclose'] = 0;
                        $params['displayreopen'] = 0;
                        $params['displaycancel'] = 0;
                        $params['displayaux'] = 0;
                        $params['displayapprove'] = 0;
                        $params['displayreturn'] = 0;
                        $params['displayreprove'] = 0;
                        $params['displaynote'] = 0;
                        $params['displayprint'] = 1;
                    }
                    else{
                        //I am not in charge of this request
                        $params['displaychanges'] = 0;
                        if ($_SESSION['hdk']['SES_IND_ASSUME_OTHER'] == 1) {
                            $params['displayassume'] = 1;
                        }else{
                            $params['displayassume'] = 0;
                        }
                        $params['displayopaux'] = 0;
                        $params['displayrepass'] = 0;
                        $params['displayreject'] = 0;
                        $params['displayclose'] = 0;
                        $params['displayreopen'] = 0;
                        $params['displaycancel'] = 0;
                        $params['displayaux'] = 0;
                        $params['displayapprove'] = 0;
                        $params['displayreturn'] = 0;
                        $params['displayreprove'] = 0;
                        $params['displaynote'] = 0;
                        $params['displayprint'] = 1;
                    }
                    break;
                case "3"://ON ATTENDANCE
                    if(in_array($inChargeID, $attendantGroups) || $inChargeID == $attendantID){
                        //I am in charge of this request
                        $params['displaychanges'] = 1;
                        $params['displayassume'] = 0;
                        $params['displayopaux'] = 1;
                        $params['displayrepass'] = 1;
                        $params['displayreject'] = 0;
                        $params['displayclose'] = 1;
                        $params['displayreopen'] = 0;
                        $params['displaycancel'] = 0;
                        $params['displayaux'] = 0;
                        $params['displayapprove'] = 0;
                        $params['displayreturn'] = 0;
                        $params['displayreprove'] = 0;
                        $params['displaynote'] = 1;
                        $params['displayprint'] = 1;
                    }
                    else{
                        //I am not in charge of this request
                        $params['displaychanges'] = 0;
                        if ($_SESSION['hdk']['SES_IND_ASSUME_OTHER'] == 1 && !in_array($attendantID, $auxiliaryAttendants)) {
                            $params['displayassume'] = 1;
                        }else{
                            $params['displayassume'] = 0;
                        }
                        $params['displayopaux'] = (in_array($attendantID, $auxiliaryAttendants) ? '1' : '0');
                        $params['displayrepass'] = 0;
                        $params['displayreject'] = 0;
                        $params['displayclose'] = ((isset($_SESSION['hdk']['SES_AUX_OPERATOR_CLOSE_TICKET']) && $_SESSION['hdk']['SES_AUX_OPERATOR_CLOSE_TICKET'] == 1) && in_array($attendantID, $auxiliaryAttendants)) ? '1' : '0';
                        $params['displayreopen'] = 0;
                        $params['displaycancel'] = 0;
                        $params['displayaux'] = 0;
                        $params['displayapprove'] = 0;
                        $params['displayreturn'] = 0;
                        $params['displayreprove'] = 0;
                        if(in_array($attendantID, $auxiliaryAttendants)){
                            $params['displaynote'] = 1;
                        }else{
                            $params['displaynote'] = 0;
                        }
                        $params['displayprint'] = 1;
                    }
                    break;
                case "4":
                    //WAITING FOR APP
                    $params['displaychanges'] = 0;
                    $params['displayassume'] = 0;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 0;
                    $params['displayreject'] = 0;
                    $params['displayclose'] = 0;
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 0;
                    $params['displayreturn'] = 0;
                    $params['displayreprove'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
                case "5":
                    //FINISHED
                    $params['displaychanges'] = 0;
                    $params['displayassume'] = 0;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 0;
                    $params['displayreject'] = 0;
                    $params['displayclose'] = 0;
                    if ($_SESSION['hdk']['SES_IND_REOPEN_USER'] == '0')
                        $params['displayreopen'] = 0;
                    else
                        $params['displayreopen'] = 1;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 0;
                    $params['displayreturn'] = 0;
                    $params['displayreprove'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
                case "6":
                    //REJECTED
                    $params['displaychanges'] = 0;
                    $params['displayassume'] = 0;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 0;
                    $params['displayreject'] = 0;
                    $params['displayclose'] = 0;
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 0;
                    $params['displayreturn'] = 0;
                    $params['displayreprove'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
                default:
                    $params['displaychanges'] = 0;
                    $params['displayassume'] = 0;
                    $params['displayopaux'] = 0;
                    $params['displayrepass'] = 0;
                    $params['displayreject'] = 0;
                    $params['displayclose'] = 0;
                    $params['displayreopen'] = 0;
                    $params['displaycancel'] = 0;
                    $params['displayaux'] = 0;
                    $params['displayapprove'] = 0;
                    $params['displayreturn'] = 0;
                    $params['displayreprove'] = 0;
                    $params['displaynote'] = 0;
                    $params['displayprint'] = 1;
                    break;
            }

            // -- Trello
            $params['displaytrello'] = 1;
        }

        $params['hidden_idstatus'] = $switchStatusID;

        return $params;
    }
    
    /**
     * en_us List ticket's notes in html table
     * pt_br Lista os apontamentos da solicitação na tabela html
     *
     * @param  mixed $ticketCode    Ticket code
     * @param  mixed $idStatus      Ticket's status
     * @param  mixed $personType    User type
     * @param  mixed $ownerID       Ticket's owner id
     * @return string               Notes html table
     */
    function makeNotesScreen($ticketCode,$idStatus,$personType,$ownerID): string
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode);

        // Notes
        $retNotes = $ticketDAO->fetchTicketNotes($ticketModel);
        if(!$retNotes['status']){
            return "";
        }

        $lineNotes = '';
        $notes = $retNotes['push']['object']->getNoteList();
        if(count($notes) <= 0)
            return "";

        foreach($notes as $key=>$val){
            $idNote = $val['idnote'];

            if($idStatus == 3){
                if ($val['idtype'] != '3' && $_SESSION['hdk']['SES_IND_DELETE_NOTE'] == '1' && $_SESSION['SES_COD_USUARIO'] == $val['idperson'] && $val['flag_opened'] != '0'){
                    $iconDel = '<button type="button" class="btn btn-danger btn-xs" href="<a href="javascript:;" onclick="deleteNote('.$idNote.','.$ticketCode.','.$personType.');"><span class="fa fa-trash-alt"></span></button>';
                } else {
                    $iconDel = "";
                }
            } else {
                $iconDel = "";
            }

            if ($val['callback']) {
                // CALLBACK
                $iconNote = ' <i class="fa fa-cogs "></i>';
            } elseif ($val['idtype'] == '1' && $val['idperson'] == $ownerID) {
                // User
                $iconNote = ' <i class="fa fa-user "></i>';
            } elseif($val['idtype'] == '1'){
                // Operator
                $iconNote = ' <i class="fa fa-users "></i>';
            } else {
                $iconNote = ' <i class="fa fa-cogs "></i>';
            }

            $retNotes['push']['object']->setIdNote($idNote);
            $retNoteAttachments = $ticketDAO->fetchNoteAttachments($retNotes['push']['object']);
            if(!$retNoteAttachments['status']){
                $iconFile  = "&nbsp";
            }

            $notesAttachments = $retNoteAttachments['push']['object']->getNoteAttachmentsList();
            if(count($notesAttachments) <= 0){
                $iconFile  = "&nbsp";
            }else{
                $iconFile = '';
                foreach($notesAttachments as $k=>$v){
                    $idNoteAttach = $v['idnote_attachments'];
                    $tooltip = $v['filename'];
                    $iconFile .= '<button type="button" class="btn btn-default btn-sm" id="'.$idNoteAttach.'" onclick="download('.$idNoteAttach.',\'note\')" data-toggle="tooltip" data-placement="right" title="'.$tooltip.'"><span class="fa fa-file-alt"></span></button>&nbsp;';
                }
            }

            $noteTitle  = $this->appSrc->_formatDate($val['entry_date']) . " [" . $val['name'] . "] <br>";
            $note =  $val['description'] ;

            if($personType == 3){
                $lineNotes .=   '
                        <div class="timeline-item">
                            <div class="row">
                                <div class="col-sm-1 date">
                                    '.$iconNote.'
                                    <br/>
                                </div>
                                <div class="col-sm-9 content">
                                    <p class=""><h3>'.$noteTitle.'</h3></p>
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
                        </div>';
            }else{
                if($val['idtype'] != '2'){
                    $lineNotes .=   '
                    <div class="timeline-item">
                        <div class="row">
                            <div class="col-sm-1 date">
                                '.$iconNote.'
                                <br/>
                            </div>
                            <div class="col-sm-9 content">
                                <p class=""><h3>'.$noteTitle.'</h3></p>
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
                    </div>';
                }
            }
        }

        return $lineNotes;
    }
    
    /**
     * en_us Update ticket's deadline
     * pt_br Atualiza o prazo de atendimento da solicitação
     *
     * @return json
     */
    public function changeDeadline()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $newDeadlineDate =  $_POST['newDeadlineDate'];
        $newDeadlineTime = $_POST['newDeadlineTime'];
        $reason = $_POST['reason'];
        $extensionNumberLimit = $_POST['deadlineExtensionNumber'] + 1;

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setExpireDate($this->appSrc->_formatSaveDateTime("{$newDeadlineDate} {$newDeadlineTime}"))
                    ->setReason($reason)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setExtensionsNumber($extensionNumberLimit);
        
        $ret = $ticketDAO->saveChangeTicketDeadline($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $newDeadline = "{$newDeadlineDate} {$newDeadlineTime}";
            $this->logger->info("Change deadline ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $newDeadlineDate =  "";
            $newDeadlineTime = "";
            $extensionNumberLimit = "";
            $this->logger->error("Error trying change deadline, ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "newDeadline" => $newDeadline,
            "newDeadlineDate" => $newDeadlineDate,
            "newDeadlineTime" => $newDeadlineTime,
            "newExtensionsNumber" => $extensionNumberLimit
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Update ticket's deadline
     * pt_br Atualiza o prazo de atendimento da solicitação
     *
     * @return json
     */
    public function saveTicketChanges()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $typeID = $_POST['typeID'];
        $itemID = $_POST['itemID'];
        $serviceID = $_POST['serviceID'];
        $reasonID = (!isset($_POST["reasonID"]) || in_array($_POST["reasonID"],array("X","NR"))) ? 0 : $_POST["reasonID"];
        $priorityID = $_POST['priorityID'];
        $attendanceTypeID = (!isset($_POST["attendanceTypeID"]) || empty($_POST['attendanceTypeID'])) ? 0 : $_POST["attendanceTypeID"];

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdType($typeID)
                    ->setIdItem($itemID)
                    ->setIdService($serviceID)
                    ->setIdReason($reasonID)
                    ->setIdPriority($priorityID)
                    ->setIdAttendanceWay($attendanceTypeID);
        
        $ret = $ticketDAO->updateTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $newDeadline = "{$newDeadlineDate} {$newDeadlineTime}";
            $this->logger->info("Change deadline ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $newDeadlineDate =  "";
            $newDeadlineTime = "";
            $extensionNumberLimit = "";
            $this->logger->error("Error trying change deadline, ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success" => false,//$st,
            "message" => $msg
        );
        return false;
        echo json_encode($aRet);
    }

    /**
     * en_us Updates in charge of the ticket
     * pt_br Atualiza o responsável pela solicitação
     *
     * @return json
     */
    public function assumeTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $groupKeepView = $_POST['groupKeepView'];
        $inChargeID = $_POST['inChargeID'];
        $inChargeType = $_POST['inChargeType'];
        $groupAssumeID = $_POST['groupAssumeID'];
        $entryDate = substr($this->appSrc->_formatSaveDateHour($_POST['ticketEntryDate']),0,-3);
        $note = "<p><b>" . $this->translator->translate('Request_assumed') . "</b></p>";
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        // -- in charge --
        $aInCharge = array(
            array("id"=> $_SESSION['SES_COD_USUARIO'],"type" => "P","isInCharge" => 1,"isRepassed" => 'N',"isTrack"=>0) //user who is assuming the ticket
        );
        
        if($groupKeepView == 1){// add track to group
            if($inChargeType == "P"){
                $bus = array("id"=> $groupAssumeID,"type" => "G","isInCharge" => 0,"isRepassed" => 'N',"isTrack"=>1);
            }elseif($inChargeType == "G"){
                $bus = array("id"=> $inChargeID,"type" => "G","isInCharge" => 0,"isRepassed" => 'N',"isTrack"=>1);
            }
            array_push($aInCharge,$bus);
        }

        // -- ticket's times --
        $minOpening = $hdkSrc->_dateDiff($entryDate,date("Y-m-d H:i"));
        $aTimes = array(
            array("field"=>"MIN_OPENING_TIME","value"=>$minOpening)
        );

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus(3) // in attendance
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime)
                    ->setInChargeList($aInCharge)
                    ->setTimesList($aTimes);

        $ret = $ticketDAO->saveAssumeTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Assume ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying assume ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "operator-assume",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates group/attendant/partner list to repass
     * pt_br Atualiza a lista de grupos/atendentes/parceiros para repasse
     *
     * @return json
     */
    public function ajaxRepassList()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_comboRepassListHtml($_POST['repassType']);
    }

    /**
     * en_us Updates group/attendant/partner selected abilities list 
     * pt_br Atualiza a lista de habilidades dos grupos/atendentes/parceiros selecionados
     *
     * @return json
     */
    public function ajaxAbilitiesList()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_abilitiesListHtml($_POST['repassType'],$_POST['repassID']);
    }

    /**
     * en_us Updates group/attendant/partner selected abilities list 
     * pt_br Atualiza a lista de habilidades dos grupos/atendentes/parceiros selecionados
     *
     * @return json
     */
    public function ajaxGroupsList()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        echo $hdkSrc->_groupsListHtml($_POST['repassType'],$_POST['repassID']);
    }
    
    /**
     * en_us Repass ticket
     * pt_br Repassa a solicitação
     *
     * @return json 
     */
    public function repassTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $repassType = $_POST['repassType'];
        $repassID = $_POST['repassID'];
        $trackType = $_POST['trackType'];
        $trackGroupID = $_POST['trackGroupID'];
        $inChargeID = $_POST['inChargeID'];
        
        $repassName = $hdkSrc->_getRepassName($repassType,$repassID);
        $note = strtolower("{$this->translator->translate('to')} " . (($repassType == 'group') ? $this->translator->translate('group') : $this->translator->translate('Operator')));
        $note = "<p><b>{$this->translator->translate('Request_repassed')} {$note} {$repassName}</b></p>";
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        $aInCharge = array();

        // -- track --
        switch($trackType){
            case "G": // Redirect ticket, but the group continues to follow
                if($trackGroupID == 0){
                    $bus = array("id"=> $inChargeID,"type" => "G","isInCharge" => 0,"isRepassed" => 'Y',"isTrack"=>1);
                }else{
                    $bus = array("id"=> $trackGroupID,"type" => "G","isInCharge" => 0,"isRepassed" => 'Y',"isTrack"=>1);
                }
                break;
            case "P": // Redirect ticket and continue following
                $bus = array("id"=> $_SESSION['SES_COD_USUARIO'],"type" => "P","isInCharge" => 0,"isRepassed" => 'Y',"isTrack"=>1);
                break;
            case "N": // Do not follow

                break;
        }
        array_push($aInCharge,$bus);

        // -- new in charge --
        $newInCharge = array(
            "id"=> $repassID,
            "type" => ($repassType == 'group') ? "G": "P",
            "isInCharge" => 1,
            "isRepassed" => 'Y',
            "isTrack"=>0);
        array_push($aInCharge,$newInCharge);
        
        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus(2) // repassed
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime)
                    ->setInChargeList($aInCharge);
                    
        $ret = $ticketDAO->saveRepassTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Repass ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying repass ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "forward-ticket",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }
    
    /**
     * en_us Formats auxiliary attendants data to show in modal
     * pt_br Formata os dados dos atendentes auxiliares para mostrar em modal
     *
     * @return json
     */
    public function modalAuxAttendant()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $ticketCode = $_POST['ticketCode'];

        $ret = $this->makeAuxAttendantScreen($ticketCode);

        echo json_encode($ret);
    }
    
    /**
     * en_us Formats auxiliary attendants data in HTML
     * pt_br Formata os dados dos atendentes auxiliares em HTML
     *
     * @param  mixed $ticketCode
     * @return array
     */
    public function makeAuxAttendantScreen($ticketCode)
    {
        $hdkSrc = new hdkServices();

        $aCmbAttendants = $hdkSrc->_comboAuxiliaryAttendant($ticketCode);
        $select = '<option></option>';

        foreach ( $aCmbAttendants as $key => $val ) {
            $select .= "<option value='{$val['id']}'>{$val['text']}</option>";
        }

        $aAuxAttendants = $hdkSrc->_comboAuxiliaryAttendant($ticketCode,true);
        $tBody = '';
        $auxAttendantLine = '';

        foreach ( $aAuxAttendants as $key => $val ) {
            $tBody .= "<tr>
                        <td>{$val['text']}
                            <input type='hidden' class='hdkAuxOpe' name='hdkAuxOpe[]' id='hdkAuxOpe_{$val['id']}' value='{$val['id']}'>
                        </td>
                        <td>
                            <a href='javascript:;' onclick='removeAuxAttendant(this)' class='btn btn-danger btn-sm'><i class='fa fa-user-times'></i></a>
                        </td>
                    </tr>";
            $auxAttendantLine .= "<div class='row'>{$val['text']}</div>";
        }

        $aRet = array(
            "cmbAttendants" => $select,
            "auxAttendantList" => $tBody,
            "auxAttendantLine" => $auxAttendantLine
        );

        return $aRet;

    }
    
    /**
     * en_us Link auxiliary attendant to ticket
     * pt_br Vincula um atendente auxiliar à solicitação
     *
     * @return json
     */
    public function insertAuxiliaryAttendant()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $attendantID = $_POST['attendantID'];
        $attendantName = $hdkSrc->_getRepassName("operator",$attendantID);
    
        $note = "<p><b>{$this->translator->translate('hdk_aux_operator_added')}:</b> {$attendantName}</p>";
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        $aInCharge = array(
            array("id"=> $attendantID,"type" => "P","isInCharge" => 0,"isRepassed" => 'N',"isTrack"=>0,"isOperatorAux"=>1)
        );

        $ticketModel->setTicketCode($ticketCode)
                    ->setInChargeList($aInCharge)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setNoteList($aNote);
        
        $ret = $ticketDAO->insertAuxiliaryAttendant($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Auxiliary attendant inserted ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying insert auxiliary attendant ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $retScreen = $this->makeAuxAttendantScreen($ticketCode);
        $notesScreen = $this->makeNotesScreen($ticketCode,$_POST['statusID'],$_POST['flagNote'],$_POST['ownerID']);

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "add-aux-operator",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "auxAttendantsData" => $retScreen,
            "notesAdded" => $notesScreen
        );

        echo json_encode($aRet);
    }
    
    /**
     * en_us Remove auxiliary attendant from ticket
     * pt_br Exclui o atendente auxiliar da solicitação
     *
     * @return json
     */
    public function removeAuxiliaryAttendant()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $attendantID = $_POST['attendantID'];

        $ticketModel->setTicketCode($ticketCode)
                    ->setIdInCharge($attendantID);
        
        $ret = $ticketDAO->deleteAuxiliaryAttendant($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Auxiliary attendant removed, ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying remove auxiliary attendant, ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $retScreen = $this->makeAuxAttendantScreen($ticketCode);

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "auxAttendantsData" => $retScreen
        );

        echo json_encode($aRet);
    } 
        
    /**
     * en_us Saves ticket's note
     * pt_br 
     *
     * @return json
     */
    public function saveNote()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $note = addslashes($_POST['noteContent']);

        if(isset($_POST["attachments"])){
            $aAttachs 	= $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files
            
            $ticketModel->setAttachments($aAttachs);
        }
        
        
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $noteDateTime = date("Y-m-d H:i:s");

        if($_POST['flagNote'] == 2){
            
            $aNote = array(
                array(
                    "public"=> 1,"type" => 1,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                    "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                    "ipAddress" => $ipAddress, "callback" => 0
                )
            );
        }else{
            $aNote = array(
                array(
                    "public"=> 1,"type" => $_POST['typeNote'],"note" => $note, "date" => $noteDateTime, "totalMinutes" => $_POST['totalMinutes'],
                    "startHour" => $_POST['executionStarted'],"finishHour" => $_POST['executionFinished'], "executionDate" => $this->appSrc->_formatSaveDateHour($_POST['executionDate']), 
                    "hourType" =>$_POST['typeHour'], "ipAddress" => $ipAddress, "callback" => $_POST['callback']
                )
            );
        }

        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setNoteList($aNote);
        
        $ins = $ticketDAO->saveTicketNote($ticketModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Note added, ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            // link attachments to the ticket
            if($aSize > 0){
                $insAttachs = $this->linkNoteAttachments($ins['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                }
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $this->logger->error("Error trying add note, ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $retScreen = $this->makeNotesScreen($ticketCode,$_POST['statusID'],$_POST['flagNote'],$_POST['ownerID']);

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            if($_POST['flagNote'] == 3){ // Note created by operator
                $transaction = 'user-note' ;
            } elseif ($_POST['flagNote'] == 2) { // Note created by user
                $transaction = 'operator-note';
            }

            $aParam = array(
                'transaction' => $transaction,
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "notesAdded" => $retScreen
        );
        
        echo json_encode($aRet);
    }

    /**
     * en_us Uploads the file in the directory
     * pt_br Carrega o arquivo no diretório
     * 
     * @return json
     */
    function saveNoteAttachments()
    {
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");

            if($this->saveMode == 'disk'){
                $targetFile =  $this->noteStoragePath.$fileName;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logger->info("Note's attachment saved. {$targetFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logger->error("Error trying save note's attachment: {$fileName}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
                    
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = new awsServices();
                
                $arrayRet = $aws->_copyToBucket($tempFile,$this->noteStoragePath.$fileName);
                
                if($arrayRet['success']) {
                    $this->logger->info("Save temp attachment file {$fileName}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    $this->logger->error("I could not save the temp file: {$fileName} in S3 bucket !! Error: {$arrayRet['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }             

            }

        }else{
            $this->logger->error("Error: {$_FILES['file']['error']}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
        }
        
        exit;
    }
    
    /**
     * en_us Links note to attachment
     * pt_br Vincula o apontamento ao anexo
     *
     * @param  ticketModel $ticketModel
     * @return array
     */
    public function linkNoteAttachments(ticketModel $ticketModel): array
    {
        $ticketDAO = new ticketDAO();
        $aAttachs = $ticketModel->getAttachments();
        
        foreach($aAttachs as $key=>$fileName){
            $ticketModel->setFileName($fileName);

            $ins = $ticketDAO->insertNoteAttachment($ticketModel); //inserts into DB
            if(!$ins['status'] && $ins['push']['object']->getIdAttachment() <= 0) {
                $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}");
            }
            
            $extension = strrchr($fileName, ".");
            $attachmentID = $ins['push']['object']->getIdAttachment();
            $newFile = $attachmentID.$extension;
            $ins['push']['object']->setNewFileName($newFile);

            if($attachmentID <= 0){
                $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: Attachment Id not found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}. Attachment Id not found");
            }
            
            if($this->saveMode == 'disk'){
                $targetOld = $this->noteStoragePath.$fileName;
                $targetNew =  $this->noteStoragePath.$newFile;

                if(!rename($targetOld,$targetNew)){
                    $del = $ticketDAO->removeNoteAttachment($ins['push']['object']);// remove attachment from DB
                    if(!$del['status']) {
                        $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: {$del['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}");
                    }
                    $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()} in disk!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to ticket #{$ticketModel->getTicketCode()}");
                }
                
            }elseif($this->saveMode == 'aws-s3'){
                $aws = new awsServices();
                $arrayRet = $aws->_renameFile("{$this->noteStoragePath}{$fileName}","{$this->noteStoragePath}{$newFile}");
                $this->logger->info("Check rename file", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Ret'=>$arrayRet]);
                if($arrayRet['success']) {
                    $this->logger->info("Rename note attachment file {$fileName} to {$newFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                } else {
                    $del = $ticketDAO->removeNoteAttachment($ins['push']['object']);// remove attachment from DB
                    if(!$del['status']) {
                        $this->logger->error("Can't link file {$fileName} to ticket # {$ticketModel->getTicketCode()}! Error: {$del['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to note of ticket # {$ticketModel->getTicketCode()}");
                    }

                    $this->logger->error("I could not rename note attachment file {$fileName} to {$newFile} in S3 bucket !! Error: {$arrayRet['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
            
            }
        }

        return array("success"=>true,"message"=>"");
    }

    /**
     * en_us Updates in charge of the ticket
     * pt_br Atualiza o responsável pela solicitação
     *
     * @return json
     */
    public function closeTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $entryDate = substr($this->appSrc->_formatSaveDateHour($_POST['ticketEntryDate']),0,-3);
        
        $noteDateTime = date("Y-m-d H:i:s");

        if ($_SESSION['hdk']['SES_APROVE'] == 1) {
            $statusID = 4;
            $note = "<p><b>" . $this->translator->translate('Request_waiting_approval') . "</b></p>";
        } else {
            $statusID = 5;
            $note = "<p><b>" . $this->translator->translate('Request_closed') . "</b></p>";
        }

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus($statusID)
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime);

        // -- ticket's times --
        $retAssume = $ticketDAO->getAssumedDate($ticketModel);
        $assumeDate = ($retAssume['status'] && !empty($retAssume['push']['object']->getAssumeDate())) ? $retAssume['push']['object']->getAssumeDate() : date("Y-m-d H:i");
        $retExpended = $ticketDAO->getExpendedDate($ticketModel);
        $minExpended = ($retExpended['status'] && !empty($retExpended['push']['object']->getMinExpendedTime())) ? $retExpended['push']['object']->getMinExpendedTime() : 0;
        $minClosure = $hdkSrc->_dateDiff($entryDate,date("Y-m-d H:i"));
        $minAttendance = $hdkSrc->_dateDiff($assumeDate,date("Y-m-d H:i"));
        $aTimes = array(
            array("field"=>"MIN_CLOSURE_TIME","value"=>$minClosure),
            array("field"=>"MIN_ATTENDANCE_TIME","value"=>$minAttendance),
            array("field"=>"MIN_EXPENDED_TIME","value"=>$minExpended)
        );

        $ticketModel->setTimesList($aTimes);        
        
        $ret = $ticketDAO->saveCloseTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Close ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying close ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "finish-ticket",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );
        
        echo json_encode($aRet);
    }

    /**
     * en_us Saves ticket's reject
     * pt_br Atualiza o responsável pela solicitação
     *
     * @return json
     */
    public function rejectTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketCode = $_POST['ticketCode'];
        $inChargeID = $_POST['inChargeID'];
        $inChargeType = $_POST['inChargeType'];
        $reason = $_POST['rejectReason'];

        $note = "<p><b>" . $this->translator->translate('Request_rejected') . "</b></p>" . $reason;
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        // -- in charge --
        $aInCharge = array(
            array("id"=> $_SESSION['SES_COD_USUARIO'],"type" => "P","isInCharge" => 1,"isRepassed" => 'N',"isTrack"=>0) //user who is rejecting the ticket
        );

        //Setting up the model
        $ticketModel->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus(3) // in attendance
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime)
                    ->setInChargeList($aInCharge);

        $ret = $ticketDAO->saveRejectTicket($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Reject ticket # {$ticketCode} - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying reject ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "operator-reject",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Write the ticket information to the DB
     *
     * pt_br Grava no BD as informações da solicitação
     */
    public function saveOpenRepassTicket()
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

        $minTelephoneTime = number_format($_POST["openTime"], "2", ".", ",");
        $minAttendanceTime = (int) $_POST["openTime"];
        
        $ownerID    = $_POST["ownerID"];
        $creatorID  = $_SESSION["SES_COD_USUARIO"];
        $wayID      = $_POST["attendanceTypeID"];
        $sourceID	= $_POST["sourceID"];
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

        $repassType = $_POST['repassType'];
        $repassID = $_POST['repassID'];
        $trackType = $_POST['trackType'];
        $trackGroupID = $_POST['trackGroupID'];
        
        $note = "<p><b>" . $this->translator->translate('Request_opened') . "</b></p>";
        $repassName = $hdkSrc->_getRepassName($repassType,$repassID);
        $note2 = strtolower("{$this->translator->translate('to')} " . (($repassType == 'group') ? $this->translator->translate('group') : $this->translator->translate('Operator')));
        $note2 = "<p><b>{$this->translator->translate('Request_repassed')} {$note2} {$repassName}</b></p>";
        $noteDateTime = date("Y-m-d H:i:s");

        $statusID = 2;

        $ticketDate = (!isset($_POST['ticketDate']) || empty($_POST['ticketDate'])) ? date("Y-m-d") : $this->appSrc->_formatSaveDate($_POST['ticketDate']);
        $ticketHour = (!isset($_POST['ticketTime']) || empty($_POST['ticketTime'])) ? date("H:i") : $_POST['ticketTime'];
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

        $aInCharge =  array();
        // -- track --
        switch($trackType){
            case "G": // Redirect ticket, but the group continues to follow
                $bus = array("id"=> $trackGroupID,"type" => "G","isInCharge" => 0,"isRepassed" => 'Y',"isTrack"=>1);
                array_push($aInCharge,$bus);
                break;
            case "P": // Redirect ticket and continue following
                $bus = array("id"=> $_SESSION['SES_COD_USUARIO'],"type" => "P","isInCharge" => 0,"isRepassed" => 'Y',"isTrack"=>1);
                array_push($aInCharge,$bus);
                break;
            case "N": // Do not follow

                break;
        }

        // -- new in charge --
        $newInCharge = array(
            "id"=> $repassID,
            "type" => ($repassType == 'group') ? "G": "P",
            "isInCharge" => 1,
            "isRepassed" => 'Y',
            "isTrack"=>0);
        array_push($aInCharge,$newInCharge);

        $ticketModel->setInChargeList($aInCharge);

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        // -- opening note
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );
        // -- solution
        if($solution && $solution != '<p><br></p>'){
            $solutionNote = "<p><b>" .$this->translator->translate('Solution') . "</b></p>". $solution;
            $bus = array(
                "public"=> 1,"type" => 3,"note" => $solutionNote, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            );

            array_push($aNote,$bus);
        }
        // -- repass note
        $bus2 = array(
            "public"=> 1,"type" => 3,"note" => $note2, "date" => $noteDateTime, "totalMinutes" => 0,
            "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
            "ipAddress" => $ipAddress, "callback" => 0
        );
        array_push($aNote,$bus2);

        $ticketModel->setNoteList($aNote);
        
        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files
            
            $ticketModel->setAttachments($aAttachs);
        }
        
        // -- add extra fields to model
        if($_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS'] && (isset($_POST['extraFields']) && count($_POST['extraFields']) > 0)){
            $ticketModel->setExtraFieldList($_POST['extraFields']);
        }
        
        $ins = $ticketDAO->saveOpenRepassTicket($ticketModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $ticketCode = $ins['push']['object']->getTicketCode();

            $retInCharge = $ticketDAO->getInChargeByTicketCode($ins['push']['object']);
            $inChargeName = ($retInCharge['status']) ? $retInCharge['push']['object']->getInCharge() : "";
            
            $expiryDate = $this->appSrc->_formatDateHour($expireDate);
            
            // link attachments to the ticket
            if($aSize > 0){
                $insAttachs = $this->linkTicketAttachments($ins['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                    $ticketCode = "";
                    $inChargeName = "";
                    $expiryDate = "";
                }else{
                    $this->logger->info("Ticket # {$ticketCode} was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                }
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $ticketCode = "";
            $inChargeName = "";
            $expiryDate = "";
            $this->logger->error("Unable to create a ticket. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "new-ticket-user",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "inChargeName" => $inChargeName,
            "expiryDate" => $expiryDate
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Write the ticket information to the DB
     *
     * pt_br Grava no BD as informações da solicitação
     */
    public function saveOpenFinishTicket()
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

        $minTelephoneTime = number_format($_POST["openTime"], "2", ".", ",");
        $minAttendanceTime = (int) $_POST["openTime"];
        
        $ownerID    = $_POST["ownerID"];
        $creatorID  = $_SESSION["SES_COD_USUARIO"];
        $wayID      = $_POST["attendanceTypeID"];
        $sourceID	= $_POST["sourceID"];
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

        $statusID = 5;

        $ticketDate = (!isset($_POST['ticketDate']) || empty($_POST['ticketDate'])) ? date("Y-m-d") : $this->appSrc->_formatSaveDate($_POST['ticketDate']);
        $ticketHour = (!isset($_POST['ticketTime']) || empty($_POST['ticketTime'])) ? date("H:i") : $_POST['ticketTime'];
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
        
        // -- in charge --
        $aInCharge = array(
            array("id"=> $retGroup['push']['object']->getIdServiceGroup(),"type" => "G","isInCharge" => 0,"isRepassed" => 'N'),
            array("id"=> $_SESSION['SES_COD_USUARIO'],"type" => "P","isInCharge" => 1,"isRepassed" => 'N')
        );
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
            $solutionNote = "<p><b>{$this->translator->translate('Request_closed')}</b></p><p><b>{$this->translator->translate('Solution')}</b></p>". $solution;
            $bus = array(
                "public"=> 1,"type" => 3,"note" => $solutionNote, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            );            
        }else{
            $solutionNote = "<p><b>{$this->translator->translate('Request_closed')}</b></p>";
            $bus = array(
                "public"=> 1,"type" => 3,"note" => $solutionNote, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            );
        }
        array_push($aNote,$bus);

        $ticketModel->setNoteList($aNote);
        
        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files
            
            $ticketModel->setAttachments($aAttachs);
        }
        
        // -- add extra fields to model
        if($_SESSION['hdk']['SES_SHOW_TICKET_EXTRA_FIELDS'] && (isset($_POST['extraFields']) && count($_POST['extraFields']) > 0)){
            $ticketModel->setExtraFieldList($_POST['extraFields']);
        }
        
        $ins = $ticketDAO->saveOpenFinishTicket($ticketModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $ticketCode = $ins['push']['object']->getTicketCode();

            $retInCharge = $ticketDAO->getInChargeByTicketCode($ins['push']['object']);
            $inChargeName = ($retInCharge['status']) ? $retInCharge['push']['object']->getInCharge() : "";
            
            $expiryDate = $this->appSrc->_formatDateHour($expireDate);
            
            // link attachments to the ticket
            if($aSize > 0){
                $insAttachs = $this->linkTicketAttachments($ins['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                    $ticketCode = "";
                    $inChargeName = "";
                    $expiryDate = "";
                }
            }else{
                $this->logger->info("Ticket # {$ticketCode} was created successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $ticketCode = "";
            $inChargeName = "";
            $expiryDate = "";
            $this->logger->error("Unable to create a ticket. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => "new-ticket-user",
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode,
            "inChargeName" => $inChargeName,
            "expiryDate" => $expiryDate
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Make the ticket .pdf file
     * pt_br Faz o arquivo .pdf da solicitação
     *
     * @return void
     */
    public function makeReport()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $awsSrc = new awsServices();
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketModel->setTicketCode($_POST['ticketCode']);

        if(!$this->appSrc->_checkExtensionLoaded('gd'))
            return false;
        
        if(!$this->appSrc->_checkFunctionExists('gd_info'))
            return false;

        $ret = $ticketDAO->getTicket($ticketModel);
        if(!$ret['status'])
            return false;

        $ticketData = $ret['push']['object'];
        
        $retLogo = $awsSrc->_getFile("logos/default/reports.png");
        $logo = $retLogo['fileUrl'];
        
        $pdf = new Mpdf(['mode' => 'c',
                         'margin_left' => 10,
                         'margin_right' => 10,
                         'margin_top' => 20,
                         'margin_bottom' => 10,
                         'margin_header' => 5,
                         'margin_footer' => 5,
                         'tempDir' => $this->pdfTmp]);

        $header = "<table width='100%' style='border-bottom: 1px solid #000000; vertical-align: bottom; font-family: Arial; font-size: 10pt;'>
                        <tr>
                            <td width='20%' align='left'><img src='{$logo}' width='200px' /></td>
                            <td width='60%' align='center'><strong>{$this->translator->translate('Request')}</strong></td>
                            <td width='20%' align='right' style='font-size: 8pt;'>{$this->translator->translate('PDF_Page')} {PAGENO}/[PAGETOTAL]</td>
                        </tr>
                    </table>";
        
        $pdf->SetHTMLHeader($header);
        $pdf->AliasNbPages("[PAGETOTAL]");
        $pdf->AddPage();

        $body = "<table width='100%' style='border-bottom: 1px solid #000000; vertical-align: bottom; font-family: Arial; font-size: 8pt;'>
                    <tr>
                        <td colspan='4' align='center' style='background:#c8dcff'><strong>{$this->translator->translate('Request')}</strong></td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Number')}</strong>:</td>
                        <td width='30%' align='left'>".substr($ticketData->getTicketCode(),0,4) . "/" . substr($ticketData->getTicketCode(),4,2) . "-" . substr($ticketData->getTicketCode(),6)."</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Opened_by')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getCreator()}</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Request_owner')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getOwner()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Source')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getSource()}</td>
                    </tr>";

        if($_SESSION['SES_REQUEST_SHOW_PHONE'] == 1){
            $body .= "<tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Company')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getCompany()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Phone')}</strong>:</td>
                        <td width='30%' align='left'>".preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $ticketData->getCreatorPhone())."</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'>&nbsp;</td>
                        <td width='30%' align='left'>&nbsp;</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Branch')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getCreatorBranch()}</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'>&nbsp;</td>
                        <td width='30%' align='left'>&nbsp;</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Mobile_phone')}</strong>:</td>
                        <td width='30%' align='left'>".preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $ticketData->getCreatorMobile())."</td>
                    </tr>
                    <tr>
                        <td colspan='4' align='center'>&nbsp;</td>
                    </tr>
                </table>";
        }else{
            $body .= "<tr>
                    <td width='20%' align='right'><strong>{$this->translator->translate('Company')}</strong>:</td>
                    <td width='30%' align='left'>{$ticketData->getCompany()}</td>
                        <td width='20%' align='right'>&nbsp;</td>
                        <td width='30%' align='left'>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan='4' align='center'>&nbsp;</td>
                    </tr>
                </table>";
        }

        $body .= "<table width='100%' style='border-bottom: 1px solid #000000; vertical-align: bottom; font-family: Arial; font-size: 8pt;'>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Department')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getDepartment()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('status')}</strong>:</td>
                        <td width='30%' align='left' style='color:{$ticketData->getColor()}'>{$ticketData->getStatus()}</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Area')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getArea()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Opening_date')}</strong>:</td>
                        <td width='30%' align='left'>".$this->appSrc->_formatDateHour($ticketData->getEntryDate())."</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('type')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getType()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Priority')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getPriority()}</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Item')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getItem()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Att_way')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getAttendanceWay()}</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Service')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getService()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Var_incharge')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getInCharge()}</td>
                    </tr>
                    <tr>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Reason')}</strong>:</td>
                        <td width='30%' align='left'>{$ticketData->getReason()}</td>
                        <td width='20%' align='right'><strong>{$this->translator->translate('Expire_date')}</strong>:</td>
                        <td width='30%' align='left'>".$this->appSrc->_formatDateHour($ticketData->getExpireDate())."</td>
                    </tr>
                    <tr>
                        <td colspan='4' align='center'>&nbsp;</td>
                    </tr>
                </table>";
        
        $body .= "<table width='100%' style='border-bottom: 1px solid #000000; vertical-align: bottom; font-family: Arial; font-size: 8pt;'>
                <tr>
                    <td width='20%' align='right'><strong>{$this->translator->translate('Subject')}</strong>:</td>
                    <td width='80%' align='left'>{$ticketData->getSubject()}</td>
                </tr>
                <tr>
                    <td width='20%' align='right'><strong>{$this->translator->translate('Description')}</strong>:</td>
                    <td width='80%' align='left'>{$ticketData->getDescription()}</td>
                </tr>
                <tr>
                    <td colspan='4' align='center'>&nbsp;</td>
                </tr>
            </table>";
        // -- notes
        $retNotes = $ticketDAO->fetchTicketNotes($ticketModel);
        if($retNotes['status']){
            $aNotes = $retNotes['push']['object']->getNoteList();
            $body .= "<br><br>
                <table width='100%' style='vertical-align: bottom; font-family: Arial; font-size: 8pt;'>
                    <tr>
                        <td colspan='4' align='center' style='background:#c8dcff'><strong>{$this->translator->translate('Added_notes')}</strong></td>
                    </tr>";

            foreach($aNotes as $key=>$val){
                $body .= "<tr>
                            <td width='100%' style='border-bottom: 1px solid #000000;'><br>
                                <p style='margin-left:60px;'>".$this->appSrc->_formatDateHour($val['entry_date'])." [{$val['name']}]</p><br>
                                <p style='margin-left:60px;'>".preg_replace("/<br\W*?\/>/", "<br><br>", $val['description'])."</p>
                                <p>&nbsp;</p>
                            </td>
                        </tr>";
            }

            $body .= "</table>";
        }

        $pdf->WriteHTML($body);

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->tmp . $filename ;
        $fileNameUrl   = $this->downloadDir . $filename ;

        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;
    }
    
    /**
     * en_us Highlight attendance deadline
     * pt_br Destaca o prazo de atendimento
     *
     * @param  mixed $deadlineDate
     * @param  mixed $statusID
     * @return string
     */
    public function highlightDeadline($deadlineDate, $statusID) {

        $deadlineTMP = strtotime($deadlineDate);
        $nowTMP = strtotime(date('Y-m-d H:i:s'));
        $newDeadlineDate = strtotime(date("Y-m-d",strtotime($deadlineDate)));
        $newNow = strtotime(date('Y-m-d'));

        if($deadlineTMP >= $nowTMP){
            //near deadline
            $color_exp = "#000000";
        }elseif($date_exp == $newNow){
            //deadline today
            $color_exp = "#0000FF";
        }elseif($deadlineTMP <= $nowTMP && $statusID == 3){
            //expired deadline
            $color_exp = "#FF0000";
        }elseif($deadlineTMP <= $nowTMP && $statusID == 1){
            //expired deadline not assumed
            $color_exp = "#990000";
        }
        return "<span style='color:" . $color_exp . ";'>{$this->appSrc->_formatDateHour($deadlineDate)}</span>";
    }
    
    /**
     * en_us Highlight ticket's code
     * pt_br Destaca o número da solicitação
     *
     * @param  mixed $inChargeID
     * @param  mixed $inChargeType
     * @param  mixed $attendantID
     * @param  mixed $isTrack
     * @param  mixed $ticketCode
     * @return void
     */
    public function highlightTicketCode($inChargeID, $inChargeType, $attendantID, $isTrack, $ticketCode) {
		if($isTrack == 1 && $inChargeID != $attendantID && $inChargeType == "P"){
			// -- I am following
			$ret = "<span style='color: #808080; border-bottom:1px solid #808080; font-weight:bold;' title='{$this->translator->translate('tlt_span_track_me')}' > {$ticketCode} </span>";
		}elseif($isTrack == 1 && $inChargeType == "G"){
			// -- Group is following
			$ret = "<span style='color: #000000; border-bottom:1px solid #000000; font-weight:bold;' title='{$this->translator->translate('tlt_span_track_group')}' > {$ticketCode} </span>";
		}elseif($inChargeID == $attendantID && $inChargeType == "P"){
			// -- My ticket
			$ret = "<span style='color: #DF6300; border-bottom:1px solid #DF6300; font-weight:bold;' title='{$this->translator->translate('tlt_span_my')}' > {$ticketCode} </span>";
		}elseif($isTrack == 0 && $inChargeType == "G"){
			// -- My group
			$ret = "<span style='color: #0012DF; border-bottom:1px solid #0012DF; font-weight:bold;' title='{$this->translator->translate('tlt_span_group')}' > {$ticketCode} </span>";
		}else{
			$ret = "<span style='color: #000000; border-bottom:1px solid #000000; font-weight:bold;' title='{$this->translator->translate('tlt_span_track_group')}' > {$ticketCode} </span>";
		}
        		
        return $ret;
    }
    
    /**
     * en_us Makes ticket's code link
     * pt_br Faz o link do número da solicitação
     *
     * @param  mixed $inChargeID
     * @param  mixed $inChargeType
     * @param  mixed $attendantID
     * @param  mixed $isTrack
     * @param  mixed $ticketCode
     * @return string
     */
    public function makeLinkCode($inChargeID,$inChargeType,$attendantID,$isTrack,$ticketCode)
    {
        return "<a href='{$_ENV['HDK_URL']}/helpdezk/hdkTicket/viewTicket/{$ticketCode}' style=' text-decoration: none !important;'>{$this->highlightTicketCode($inChargeID,$inChargeType,$attendantID,$isTrack,$ticketCode)}</a>";
    }
    
    /**
     * en_us Download the attachment
     * pt_br Baixa o anexo
     *
     * @param  mixed $fileId    Attachment Id
     * @param  mixed $fileType  Attachment type: [request = Ticket's attachment. note =  Note's attachment] 
     * @return void
     */
    public function downloadFile($fileId=null,$fileType=null)
    {
        $awsSrc = new awsServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketModel->setIdAttachment($fileId)
                    ->setAttachmentType($fileType);

        $ret = $ticketDAO->getAttachment($ticketModel);
        if(!$ret['status']){
            return false;
        }

        $name = $ret['push']['object']->getFileName();
        $ext = strrchr($name, '.');

        switch ($fileType) {
            case 'note':
                if($this->saveMode == 'aws-s3') {
                    $retUrl = $awsSrc->_getFile("{$this->noteStoragePath}{$fileId}{$ext}");
                    $url = $retUrl['fileUrl'];
                    
                    if(!file_put_contents("{$this->tmp}{$fileId}{$ext}",file_get_contents($url))) {
                        $this->logger->error("Can\'t save S3 temp file {$fileId}{$ext}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    }

                    $fileName = "{$this->tmp}{$fileId}{$ext}" ;
                } else {
                    if($this->_externalStorage) {
                        $fileName = "{$this->noteStoragePath}{$fileId}{$ext}";
                    } else {
                        $fileName = "{$this->noteStoragePath}{$fileId}{$ext}";
                    }
                }
                
                break;

                case 'request':
                if($this->saveMode == 'aws-s3') {
                    $retUrl = $awsSrc->_getFile("{$this->ticketStoragePath}{$fileId}{$ext}");
                    $url = $retUrl['fileUrl'];
                    
                    if(!file_put_contents("{$this->tmp}{$fileId}{$ext}",file_get_contents($url))) {
                        $this->logger->error("Can\'t save S3 temp file {$fileId}{$ext}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    }

                    $fileName = "{$this->tmp}{$fileId}{$ext}";
                } else {
                    if($this->_externalStorage) {
                        $fileName = "{$this->ticketStoragePath}{$fileId}{$ext}";
                    } else {
                        $fileName = "{$this->ticketStoragePath}{$fileId}{$ext}";
                    }
                }

                break;
        }

        // required for IE
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // get the file mime type 
        $mime =  mime_content_type($fileName);
        if (empty($mime))
            $mime = 'application/force-download';

        header('Pragma: public');   // required
        header('Expires: 0');       // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate ('D, d M Y H:i:s', filemtime ($fileName)) . ' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '. filesize($fileName));  
        header('Connection: close');
        // push it out
        readfile($fileName);    
        // delete the file
        if($this->saveMode == 'aws-s3') 
            unlink($fileName);
        
        exit();
    }
    
    /**
     * en_us Delete the selected note
     * pt_br Remove o apontamento selecionado
     *
     * @return json
     */
    public function deleteNote()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $noteId = $_POST['noteId'];

        //Setting up the model
        $ticketModel->setIdNote($noteId);

        $ret = $ticketDAO->deleteNote($ticketModel);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Delete note # {$noteId} ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            //-- delete note's attachment from storage
            $aAttachments = $ret['push']['object']->getNoteAttachmentsList();
            if(count($aAttachments) > 0){

                foreach($aAttachments as $key=>$val){
                    $idAttach = $val['idnote_attachments'];
                    $extension = strrchr($val['filename'], ".");
                    
                    if($this->saveMode == 'disk'){
                        if(!unlink("{$this->noteStoragePath}{$idAttach}{$extension}")) {
                            $this->logger->error("I could not remove note's attachment file: {$idAttach}{$extension} from disk !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        } else {
                            $this->logger->info("Remove note's attachment file: {$idAttach}{$extension}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }
                            
                    }elseif($this->saveMode == "aws-s3"){
                        
                        $aws = new awsServices();
                        
                        $awsRet = $aws->_removeFile("{$this->noteStoragePath}{$idAttach}{$extension}");
                        
                        if($awsRet['success']) {
                            $this->logger->info("Remove note's attachment file: {$idAttach}{$extension}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);     
                        } else {
                            $this->logger->error("I could not remove note's attachment file: {$idAttach}{$extension} from S3 bucket !! Error: {$awsRet['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }
                    }
                }
            }
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying delete note # {$noteId} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $retScreen = $this->makeNotesScreen($_POST['ticketCode'],$_POST['statusID'],$_POST['flagNote'],$_POST['ownerID']);

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "noteId" => $noteId,
            "notesAdded" => $retScreen
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Updates group/attendant/partner list to repass
     * pt_br Atualiza a lista de grupos/atendentes/parceiros para repasse
     *
     * @return json
     */
    public function ajaxExtraFields()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $serviceId = $_POST['serviceId'];
        $html = "";

        //Setting up the model
        $ticketModel->setIdService($serviceId);

        $ret = $ticketDAO->fetchExtraFieldsByService($ticketModel);
        if($ret['status']){
            $aExtras = $ret['push']['object']->getExtraFieldList();
            if(count($aExtras) > 0){
                $st = true;
                $msg = "";

                foreach($aExtras as $k=>$v){
                    $html .= "<div class='row g-2 mb-2'>
                                    <div class='col-sm-3 text-end'>
                                        <label for='extraField_{$v['idextra_field']}' class='hdk-label col-form-label text-end'>{$this->translator->translate($v['lang_key_name'])}:</label>
                                    </div>
                                    <div class='col-sm-8'>";
                
                    switch($v['type']){
                        case 'input':
                            $html .= "<input type='text' id='extraField_{$v['idextra_field']}' name='extraField_{$v['idextra_field']}' class='form-control extra-field'>";
                            break;
                        case 'select':
                            $html .= "<select class='form-control m-b extra-field' id='extraField_{$v['idextra_field']}' name='extraField_{$v['idextra_field']}'>
                                        <option value='0'>{$this->translator->translate('Select')}</option>";
    
                            $aOptions = explode(",",$v['combo_options']);
                            if(is_array($aOptions) && count($aOptions) > 0){
                                foreach ($aOptions as $key=>$value) {
                                    $html .= "<option value='".$value."'>".$value."</option>";
                                }
                            }
    
                            $html .= "</select>";
                            break;
                    }                
                    
                    $html .= "</div></div>";
                }

                $this->logger->info("Extra fields list was made successfully ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }else{
                $st = false;
                $msg = "";
                $this->logger->info("Extra fields not found ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying get extra fields - Error: {$ret['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        echo $html;
    }

    /**
     * en_us Setups ticket's view screen buttons
     * pt_br Configura os botões da tela de visualização da solicitação
     *
     * @param  mixed $params        Array with others parameters
     * @param  mixed $ticketCode    Ticket code
     * @param  mixed $idStatus      Ticket status
     * @param  mixed $inChargeID    In charge Id
     * @return array $params
     */
    function makeTicketExtraFieldScreen($params,$ticketCode)
    {
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($ticketCode);
        
        $retExtraFields = $ticketDAO->fetchTicketExtraFields($ticketModel);
    
        if($retExtraFields['status'] && count($retExtraFields['push']['object']->getExtraFieldList()) > 0){
            $aExtraFields = $retExtraFields['push']['object']->getExtraFieldList();
            foreach($aExtraFields as $k=>$v){
                $html .= "<div class='row g-2 mb-1'>
                            <div class='col-sm-2 text-end'>
                                <label for='extraField_{$v['idextra_field']}' class='hdk-label col-form-label text-end'>{$this->translator->translate($v['lang_key_name'])}:</label>
                            </div>
                            <div class='col-sm-9 ms-1'>
                                <input type='text' readonly id='extraField_{$v['idextra_field']}' name='extraField_{$v['idextra_field']}' class='form-control-plaintext' value='{$v['field_value']}' >
                            </div>
                        </div>";
            }

            $params['hasExtraFields'] = 1;             // Show extra fields
            $params['extraFieldsHtml'] = $html;
        }else{
            $params['hasExtraFields'] = 0;             // Hide extra fields
        }

        return $params;
    }

    /**
     * en_us Saves ticket's approval
     * pt_br Grava a aprovação do ticket
     *
     * @return json
     */
    public function approveTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketDTO = new ticketModel();
        $hdkRulesDAO = new ticketRulesDAO();
        $hdkRulesDTO = new ticketRulesModel();

        $ticketCode = $_POST['ticketCode'];
        $reason = addslashes($_POST['reason']);
        $extensionNumberLimit = (isset($_POST['deadlineExtensionNumber'])) ? $_POST['deadlineExtensionNumber'] : 0;

        //Setting up the model
        $hdkRulesDTO->setTicketCode($ticketCode);
        
        $retNum = $hdkRulesDAO->getTotalApprovals($hdkRulesDTO);
        if(!$retNum['status']){
            $this->logger->error("Error getting total approvals, ticket # {$ticketCode}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retNum['push']['message']]);
            echo json_encode(array("success" => false,"message" => $this->translator->translate('generic_error_msg'),"ticketCode" => $ticketCode));
            exit;
        }

        if($retNum['push']['object']->getTotalRows() > 1)
            $note = "<p>".$this->translator->translate('Request_app_rep_next')."</p><p><strong>".$this->translator->translate('Justification').":</strong></p>".$reason;
        else
            $note = "<p>".$this->translator->translate('Request_app_rep_care')."</p><p><strong>".$this->translator->translate('Justification').":</strong></p>".$reason;
        
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        $retRecalc = $hdkRulesDAO->getRecalculate($hdkRulesDTO);
        if(!$retRecalc['status']){
            $this->logger->error("Error getting recalculate, ticket # {$ticketCode}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retRecalc['push']['message']]);
            echo json_encode(array("success" => false,"message" => $this->translator->translate('generic_error_msg'),"ticketCode" => $ticketCode));
            exit;
        }

        if($retRecalc['push']['object']->getIsRecalculate() == 1){
            $ticketDateHour = date("Y-m-d H:i");
            $expireDate = $hdkSrc->_getTicketExpireDate($ticketDateHour,$retRecalc['push']['object']->getPriorityId(),$retRecalc['push']['object']->getServiceId());
            if($expireDate)
                $ticketDTO->setExpireDate($expireDate);
        }

        //Setting up the model
        $ticketDTO->setTicketCode($ticketCode)
                    ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                    ->setIdStatus(59) // approved
                    ->setNoteList($aNote)
                    ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                    ->setLogDate($noteDateTime)
                    ->setExtensionsNumber($extensionNumberLimit);
        
        $ret = $ticketDAO->saveTicketApproval($ticketDTO);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Ticket # {$ticketCode} was approved successfully - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying approve ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => $ret['push']['object']->getEmailTransaction(),
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Records the return to the previous stage of ticket approval
     * pt_br Grava o retorno à fase anterior da aprovação do ticket
     *
     * @return json
     */
    public function returnTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketDTO = new ticketModel();
        $hdkRulesDAO = new ticketRulesDAO();
        $hdkRulesDTO = new ticketRulesModel();

        $ticketCode = $_POST['ticketCode'];
        $reason = addslashes($_POST['reason']);
        $note = "<p>".$this->translator->translate('Request_rejected_app_final')."</p><p><strong>".$this->translator->translate('Justification').":</strong></p>".$reason;
        
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        //Setting up the model
        $hdkRulesDTO->setTicketCode($ticketCode);
        
        $retLastApprover = $hdkRulesDAO->getLastApprover($hdkRulesDTO); // get previous approver
        if(!$retLastApprover['status']){
            $this->logger->error("Error getting last approver, ticket # {$ticketCode}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retLastApprover['push']['message']]);
            echo json_encode(array("success" => false,"message" => $this->translator->translate('generic_error_msg'),"ticketCode" => $ticketCode));
            exit;
        }

        //Setting up the model
        $ticketDTO->setTicketCode($ticketCode)
                  ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                  ->setNoteList($aNote)
                  ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                  ->setLogDate($noteDateTime)
                  ->setApproverId($retLastApprover['push']['object']->getIdPerson())
                  ->setApproverOrder($retLastApprover['push']['object']->getOrder());
        
        $ret = $ticketDAO->saveApprovalReturn($ticketDTO);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Ticket # {$ticketCode} was returned successfully - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying return ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => 'approve',
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Records ticket's disapproval
     * pt_br Grava a reprovação do ticket
     *
     * @return json
     */
    public function repproveTicket()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkSrc = new hdkServices();
        $ticketDAO = new ticketDAO();
        $ticketDTO = new ticketModel();
        $hdkRulesDAO = new ticketRulesDAO();
        $hdkRulesDTO = new ticketRulesModel();

        $ticketCode = $_POST['ticketCode'];
        $reason = addslashes($_POST['reason']);
        $note = "<p>".$this->translator->translate('Request_rejected_app_final')."</p><p><strong>".$this->translator->translate('Justification').":</strong></p>".$reason;
        
        $noteDateTime = date("Y-m-d H:i:s");

        // -- notes --
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $aNote = array(
            array(
                "public"=> 1,"type" => 3,"note" => $note, "date" => $noteDateTime, "totalMinutes" => 0,
                "startHour" => 0,"finishHour" => 0, "executionDate" => '0000-00-00 00:00:00', "hourType" =>0,
                "ipAddress" => $ipAddress, "callback" => 0
            )
        );

        //Setting up the model
        $ticketDTO->setTicketCode($ticketCode)
                  ->setIdCreator($_SESSION['SES_COD_USUARIO'])
                  ->setIdStatus(60) // disapproved
                  ->setNoteList($aNote)
                  ->setIdUserLog($_SESSION['SES_COD_USUARIO'])
                  ->setLogDate($noteDateTime);
        
        $ret = $ticketDAO->saveTicketDisapproval($ticketDTO);
        if($ret['status']){
            $st = true;
            $msg = "";
            $this->logger->info("Ticket # {$ticketCode} was disapproved successfully - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ret['push']['message'];
            $this->logger->error("Error trying return ticket # {$ticketCode} - Message: {$msg}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' && $st) {
            $aParam = array(
                'transaction' => 'operator-reject',
                'code_request' => $ticketCode,
                'media' => 'email'
            ) ;

            $hdkSrc->_sendNotification($aParam);
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "ticketCode" => $ticketCode
        );

        echo json_encode($aRet);
    }
}