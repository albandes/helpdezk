<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\hdkRequestEmailDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;

use App\modules\helpdezk\models\mysql\hdkRequestEmailModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;

class hdkRequestEmail extends Controller
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
     * index
     *
     * @return void
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenHdkRequestEmail();
		
		$this->view('helpdezk','hdk-request-email',$params);
    }
    
    /**
     * makeScreenHdkRequestEmail
     *
     * @param  mixed $option
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenHdkRequestEmail($option='idx',$obj=null)
    {
        $admSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $admSrc->_makeNavAdm($params);
        
        // -- Area --
        $params['cmbArea'] = $hdkSrc->_comboArea();         

        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboHdkRequestEmailFilters();
          $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
          $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte'; //upload image
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        if($option != 'idx'){
            // -- Companies dropdown list --
            $params['cmbCompany'] = $admSrc->_comboCompany();

            // -- Server type dropdown list --
            $params['cmbServerType'] = $hdkSrc->_comboServerType();

            // -- Area dropdown list --
            $params['cmbArea'] = $hdkSrc->_comboArea();

            // -- Login layout list --
            $params['cmbLoginLayout'] = $hdkSrc->_comboLoginLayout();
        }
        
        if($option=='upd'){
            $params['requestEmailId'] = $obj->getRequestEmailId();
            $params['serverUrl'] = $obj->getServerUrl();
            $params['serverTypeId'] = $obj->getServerType();
            $params['serverPort'] = $obj->getServerPort();
            $params['userEmail'] = $obj->getUser();
            $params['userPassword'] = $obj->getPassword();
            $params['createUser'] = $obj->getAddUserFlag();
            $params['deleteEmail'] = $obj->getDelFromServerFlag();
            $params['serviceId'] = $obj->getServiceId();
            $params['filterSender'] = $obj->getFilterFrom();
            $params['filterSubject'] = $obj->getFilterSubject();
            $params['loginLayout'] = $obj->getLoginLayout();
            $params['insertNote'] = $obj->getResponseNoteFlag();
            $params['areaId'] = $obj->getAreaId();
            $params['typeId'] = $obj->getTypeId();
            $params['itemId'] = $obj->getItemId();
            $params['companyId'] = $obj->getCompanyId();
            $params['departmentId'] = $obj->getDepartmentId();

            // -- Type dropdown list --
            $params['cmbType'] = $hdkSrc->_comboType($obj->getAreaId());

            // -- Item dropdown list --
            $params['cmbItem'] = $hdkSrc->_comboItem($obj->getTypeId());

            // -- Service dropdown list --
            $params['cmbService'] = $hdkSrc->_comboService($obj->getItemId());

            // -- Service dropdown list --
            $params['cmbDepartment'] = $admSrc->_comboDepartment($obj->getCompanyId());
        }
        
        return $params;
    }
    
    /**
     * jsonGrid
     * 
     * en_us Returns groups list to display in grid
     * pt_br Retorna lista de grupos para exibir no grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $requestEmailDAO = new hdkRequestEmailDAO(); 

        $where = "";
        $group = "";
        
        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            $where .=  ((empty($where)) ? "WHERE " : " AND ") . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= ((empty($where)) ? "WHERE" : " AND") . "  (pipeLatinToUtf8(serverurl) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(servertype) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(user) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        /* if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= " AND  tbg.status = 'A' ";
        } */

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "`serverUrl`";
        
        switch($sortIndx){
            case "serverUrl":
                $sortIndx = "`serverurl`";
                break;
            case "serverType":
                $sortIndx = "`servertype`";
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
        $countGroup = $requestEmailDAO->countHdkRequestEmails($where); 
        if($countGroup['status']){
            $total_Records = $countGroup['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $retGroup = $requestEmailDAO->queryHdkRequestEmails($where,$group,$order,$limit);
        
        if($retGroup['status']){     
            $aGroups = $retGroup['push']['object']->getGridList();     
            
            foreach($aGroups as $k=>$v) {

                $data[] = array(
                    'idgetemail'    => $v['idgetemail'],
                    'serverUrl'     => $v['serverurl'],
                    'serverType'    => $v['servertype'],
                    'user'          => $v['user']                    
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
     * comboHdkRequestEmailFilters
     * 
     * en_us Renders the status add screen
     * pt_br Renderiza o template da tela de novo cadastro de status
     *
     * @return array
     */
    public function comboHdkRequestEmailFilters(): array
    {
        $aRet = array(            
            array("id" => 'serverurl',"text"=>$this->translator->translate('Server'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'user',"text"=>$this->translator->translate('email'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    /**
     * formCreate
     * 
     * en_us Renders the status add screen
     * pt_br Renderiza o template da tela de novo cadastro de status
     *
     * @return void
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenHdkRequestEmail('add');

        $this->view('helpdezk','hdk-request-email-create',$params);
    }

    /**
     * createRequestEmail
     * 
     * en_us Write the request by email information to the DB
     * pt_br Grava no BD as informações da solicitação por email
     *
     * @return void
     */
    public function createRequestEmail()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $requestEmailDAO = new hdkRequestEmailDAO();
        $requestEmailDTO = new hdkRequestEmailModel();

        $serviceId = trim(strip_tags($_POST['cmbService']));
        $departmentId = trim(strip_tags($_POST['cmbDepartment']));
        $loginLayout = trim(strip_tags($_POST['cmbLoginLayout']));

        $check = $ins = $requestEmailDAO->queryHdkRequestEmails("WHERE idservice = {$serviceId}");
        if(!$check['status']){
            $this->logger->error("Can't check if request by email exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            echo json_encode(array("success"=>false,"message"=>$this->translator->translate('generic_error_msg'),"requestEmailId"=>"","serverUrl"=>"","serverType"=>""));
            exit;
        }

        if(count($check['push']['object']->getGridList()) > 0){
            $this->logger->info("Request by email setting already exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false,"message"=>$this->translator->translate('request_email_exists'),"requestEmailId"=>"","serverUrl"=>"","serverType"=>""));
            exit;
        }
   
        $requestEmailDTO->setServerUrl(trim(strip_tags($_POST['serverUrl'])))
                        ->setServerType(trim(strip_tags($_POST['cmbServerType'])))
                        ->setServerPort(trim(strip_tags($_POST['serverPort'])))
                        ->setUser(trim(strip_tags($_POST['userEmail'])))
                        ->setPassword(trim(strip_tags($_POST['userPassword'])))
                        ->setServiceId($serviceId)
                        ->setFilterFrom(trim(strip_tags($_POST['filterSender'])))
                        ->setFilterSubject(trim(strip_tags($_POST['filterSubject'])))
                        ->setAddUserFlag((isset($_POST['createUser'])) ? 1 : 0)
                        ->setDepartmentId((!empty($departmentId)) ? $departmentId : 0)
                        ->setLoginLayout((!empty($loginLayout)) ? $loginLayout : "E")
                        ->setDelFromServerFlag((isset($_POST['deleteEmail'])) ? 1 : 0)
                        ->setResponseNoteFlag((isset($_POST['insertNote'])) ? "1" : "0");
                        
        $ins = $requestEmailDAO->saveRequestEmail($requestEmailDTO);
        if($ins['status']){
            $this->logger->info("Request by email data save successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $requestEmailId = $ins['push']['object']->getRequestEmailId();
            $serverUrl = $ins['push']['object']->getServerUrl();
            $serverType = $ins['push']['object']->getServerType();
            
        }else{
            $this->logger->error("Can't save request by email data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $requestEmailId = "";
            $serverUrl = "";
            $serverType = "";
        }   
        
        $aRet = array(
            "success"           => $st,
            "message"           => $msg,
            "requestEmailId"    => $requestEmailId,
            "serverUrl"         => $serverUrl,
            "serverType"        => $serverType
        );

        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     * 
     * en_us Renders the requests by email settings update screen
     * pt_br Renderiza o template da atualização do cadastro de configurações para solicitações por e-mail
     *
     * @param  mixed $groupId
     * @return void
     */
    public function formUpdate($requestEmailId=null)
    {
        $requestEmailDAO = new hdkRequestEmailDAO();
        $requestEmailDTO = new hdkRequestEmailModel();
        $requestEmailDTO->setRequestEmailId($requestEmailId); 
        
        $ret = $requestEmailDAO->getRequestEmail($requestEmailDTO); 
        
        $params = $this->makeScreenHdkRequestEmail('upd',$ret['push']['object']);
        
        $this->view('helpdezk','hdk-request-email-update',$params);
    }
    
    /**
     * updateRequestEmail
     * 
     * en_us Updates the group information in the DB
     * pt_br Atualiza no BD as informações do grupo
     *
     * @return void
     */
    public function updateRequestEmail()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $requestEmailDAO = new hdkRequestEmailDAO();
        $requestEmailDTO = new hdkRequestEmailModel();

        $serviceId = trim(strip_tags($_POST['cmbService']));
        $departmentId = trim(strip_tags($_POST['cmbDepartment']));
        $loginLayout = trim(strip_tags($_POST['cmbLoginLayout']));

        $check = $ins = $requestEmailDAO->queryHdkRequestEmails("WHERE idservice = {$serviceId} AND idgetemail != {$_POST['requestEmailId']}");
        if(!$check['status']){
            $this->logger->error("Can't check if request by email exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            echo json_encode(array("success"=>false,"message"=>$this->translator->translate('generic_error_msg'),"requestEmailId"=>"","serverUrl"=>"","serverType"=>""));
            exit;
        }

        if(count($check['push']['object']->getGridList()) > 0){
            $this->logger->info("Request by email setting already exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false,"message"=>$this->translator->translate('request_email_exists'),"requestEmailId"=>"","serverUrl"=>"","serverType"=>""));
            exit;
        }
   
        $requestEmailDTO->setRequestEmailId(trim(strip_tags($_POST['requestEmailId'])))
                        ->setServerUrl(trim(strip_tags($_POST['serverUrl'])))
                        ->setServerType(trim(strip_tags($_POST['cmbServerType'])))
                        ->setServerPort(trim(strip_tags($_POST['serverPort'])))
                        ->setUser(trim(strip_tags($_POST['userEmail'])))
                        ->setPassword(trim(strip_tags($_POST['userPassword'])))
                        ->setServiceId($serviceId)
                        ->setFilterFrom(trim(strip_tags($_POST['filterSender'])))
                        ->setFilterSubject(trim(strip_tags($_POST['filterSubject'])))
                        ->setAddUserFlag((isset($_POST['createUser'])) ? 1 : 0)
                        ->setDepartmentId((!empty($departmentId)) ? $departmentId : 0)
                        ->setLoginLayout((!empty($loginLayout)) ? $loginLayout : "E")
                        ->setDelFromServerFlag((isset($_POST['deleteEmail'])) ? 1 : 0)
                        ->setResponseNoteFlag((isset($_POST['insertNote'])) ? "1" : "0");   
        
        $upd = $requestEmailDAO->saveUpdateRequestEmail($requestEmailDTO);
        if($upd['status']){
            $this->logger->info("Request by email settings # {$upd['push']['object']->getRequestEmailId()} data update successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $requestEmailId = $upd['push']['object']->getRequestEmailId();
        }else{
            $this->logger->error("Can't update request by email setting # {$upd['push']['object']->getRequestEmailId()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $upd['push']['message'];
            $requestEmailId = "";
        }           
       
        $aRet = array(
            "success"           => $st,
            "requestEmailId"    => $requestEmailId
        );        

        echo json_encode($aRet);
    }
    
    /**
     * deleteRequestEmail
     * 
     * en_us Removes requests by email setting data from DB
     * pt_br Deleta os dados da configuração de solicitações por email do BD
     *
     * @return void
     */
    function deleteRequestEmail()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $requestEmailDAO = new hdkRequestEmailDAO();
        $requestEmailDTO = new hdkRequestEmailModel();

        //Setting up the model
        $requestEmailDTO->setRequestEmailId($_POST['requestEmailId']);

        // Delete request by email registration
        $del = $requestEmailDAO->saveDeleteRequestEmail($requestEmailDTO);
		if(!$del['status']){
            $st = false;
            $msg = $this->translator->translate("generic_error_msg");
            $this->logger->error("Could not remove request by email setting # {$_POST['requestEmailId']}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $del['push']['message']]);
        }else{
            $st = true;
            $msg = "";
            $this->logger->info("Request by email setting # {$_POST['requestEmailId']} was removed successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);

    }
}