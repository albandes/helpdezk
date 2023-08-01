<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\hdkStatusDAO;
use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\modules\helpdezk\models\mysql\hdkStatusModel;

class hdkStatus extends Controller
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

        $params = $this->makeScreenHdkStatus();
		
		$this->view('helpdezk','hdkstatus',$params);
    }
    
    /**
     * makeScreenHdkStatus
     *
     * @param  mixed $option
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenHdkStatus($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);
        
        // -- Area --
        $params['cmbArea'] = $hdkSrc->_comboArea();         

        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboHdkStatusFilters();
          $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
          $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte'; //subir imagem
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        if($option != 'idx'){
            // -- Status group combo --
            $params['cmbStatusGroup'] = $hdkSrc->_comboStatusSource();
        }
        
        if($option=='upd'){
            $params['hdkStatusId'] = $obj->getStatusId();            
            $params['hdkStatusName'] = $obj->getName();
            $params['userView'] = $obj->getRequesterView();
            $params['hdkStatusColor'] = $obj->getColor();
            $params['statusSourceId'] = $obj->getStatusSourceId();
            $params['stopTimeFlag'] = $obj->getStopSlaFlag();            
        }
        //echo "<br><pre>",print_r($params,true),"</pre>";
        return $params;
       
    }
    
    /**
     * jsonGrid
     * 
     * en_us Returns status list to display in grid
     * pt_br Retorna lista de status para exibir no grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $hdkStatusDAO = new hdkStatusDAO(); 

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
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " (pipeLatinToUtf8(name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(user_view) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " status = 'A' ";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "`name`";
        
        switch($sortIndx){
            case "name":
                $sortIndx = "`name`";
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
        $countStatus = $hdkStatusDAO->countHdkStatus($where); 
        if($countStatus['status']){
            $total_Records = $countStatus['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $status = $hdkStatusDAO->queryHdkStatus($where,$group,$order,$limit);
        
        if($status['status']){     
            $statusObj = $status['push']['object']->getGridList();     
            
            foreach($statusObj as $k=>$v) {
                $statusFmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
                $coloFmt = "<span style='background-color:{$v['color']}; height:10px; width:10px; border:0px solid #000;'>&nbsp;&nbsp;&nbsp;&nbsp;</span>";

                $data[] = array(
                    'idstatus'      => $v['idstatus'],
                    'name'          => $v['name'],
                    'user_view'     => $v['user_view'],
                    'color'         => $coloFmt,
                    'color_val'     => $v['color'],
                    'status'        => $statusFmt,
                    'status_val'    => $v['status']                    
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
     * comboHdkStatusFilters
     * 
     * en_us Renders the status add screen
     * pt_br Renderiza o template da tela de novo cadastro de status
     *
     * @return array
     */
    public function comboHdkStatusFilters(): array
    {
        $aRet = array(            
            array("id" => 'name',"text"=>$this->translator->translate('Name'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'user_view',"text"=>$this->translator->translate('user_exhibition'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
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

        $params = $this->makeScreenHdkStatus('add');

        $this->view('helpdezk','hdkstatus-create',$params);
    }

    /**
     * createStatus
     * 
     * en_us Write the status information to the DB
     * pt_br Grava no BD as informações do status
     *
     * @return void
     */
    public function createStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatusDTO = new hdkStatusModel();
   
        $hdkStatusDTO->setName(trim(strip_tags($_POST['hdkStatusName'])))
                     ->setRequesterView(trim(strip_tags($_POST['userView'])))
                     ->setColor(trim(strip_tags($_POST['hdkStatusColor'])))
                     ->setStatusSourceId($_POST['cmbStatusGroup'])
                     ->setStopSlaFlag((isset($_POST['stopTimeFlag'])) ? 1 : 0);

        $ins = $hdkStatusDAO->insertHdkStatus($hdkStatusDTO);
        if($ins['status']){
            $this->logger->info("Status data save successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $hdkStatusId = $ins['push']['object']->getStatusId();
            $hdkStatusName = $ins['push']['object']->getName();
            $userView = $ins['push']['object']->getRequesterView();
            $hdkStatusColor = $ins['push']['object']->getColor();
            
        }else{
            $this->logger->error("Can't save status data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $hdkStatusId = "";
            $hdkStatusName = "";
            $userView = "";
            $hdkStatusColor = "";
        }   
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "hdkStatusId"    => $hdkStatusId,
            "hdkStatusName"  => $hdkStatusName,
            "userView"  => $userView,
            "hdkStatusColor"  => $hdkStatusColor
        );

        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     * 
     * en_us Renders the status update screen
     * pt_br Renderiza o template da atualização do cadastro de status
     *
     * @param  mixed $hdkStatusId
     * @return void
     */
    public function formUpdate($hdkStatusId=null)
    {
        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatusDTO = new hdkStatusModel();
        $hdkStatusDTO->setStatusId($hdkStatusId); 
        
        $ret = $hdkStatusDAO->getHdkStatus($hdkStatusDTO); 

        $params = $this->makeScreenHdkStatus('upd',$ret['push']['object']);
        
        $this->view('helpdezk','hdkstatus-update',$params);
    }
    
    /**
     * updateStatus
     * 
     * en_us Updates the status information in the DB
     * pt_br Atualiza no BD as informações do status
     *
     * @return void
     */
    public function updateStatus()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatusDTO = new hdkStatusModel();

        $hdkStatusDTO->setStatusId($_POST['hdkStatusId'])
                     ->setName(trim(strip_tags($_POST['hdkStatusName'])))
                     ->setRequesterView(trim(strip_tags($_POST['userView'])))
                     ->setColor(trim(strip_tags($_POST['hdkStatusColor'])))
                     ->setStatusSourceId($_POST['cmbStatusGroup'])
                     ->setStopSlaFlag((isset($_POST['stopTimeFlag'])) ? 1 : 0);             
        
        $upd = $hdkStatusDAO->updateHdkStatus($hdkStatusDTO);
        if($upd['status']){
            $this->logger->info("Status # {$upd['push']['object']->getStatusId()} data update successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $hdkStatusId = $upd['push']['object']->getStatusId();
            
        }else{
            $this->logger->error("Can't update status # {$upd['push']['object']->getStatusId()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $upd['push']['message'];
            $hdkStatusId = "";
        }           
       
        $aRet = array(
            "success"       => $st,
            "hdkStatusId"    => $hdkStatusId
        );        

        echo json_encode($aRet);
    }
    
    /**
     * changeStatus
     * 
     * en_us Changes status state
     * pt_br Muda o estado do status
     *
     * @return void
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatusDTO = new hdkStatusModel();

        //Setting up the model
        $hdkStatusDTO->setStatusId($_POST['hdkStatusId'])
                     ->setStatus($_POST['newStatus']);
        
        $upd = $hdkStatusDAO->updateStatusState($hdkStatusDTO);
        if(!$upd['status']){
            $this->logger->error("Can't update hdkStatus # {$upd['push']['object']->getsetStatusId()} status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);
    }

    /**
     * checkExist
     * 
     * en_us Check if the status has already been registered before
     * pt_br Verifica se o status já foi cadastrado anteriormente
     *
     * @return void
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatus = trim(strip_tags($_POST['hdkStatusName']));

        $where = "WHERE pipeLatinToUtf8(`name`) = pipeLatinToUtf8('$hdkStatus')"; 
        $where .= (isset($_POST['hdkStatusId'])) ? " AND idstatus != {$_POST['hdkStatusId']}" : "";        

        $check =  $hdkStatusDAO->queryHdkStatus($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('status_already_exists'));
        }else{
            echo json_encode(true);
        }

    }
    
    /**
     * checkExistUserView
     * 
     * en_us Check if the display for user has already been registered before
     * pt_br Verifica se o nome da exibição para usuário  já foi cadastrado anteriormente
     *
     * @return void
     */
    function checkExistUserView(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $hdkStatusDAO = new hdkStatusDAO();
        $userView = trim(strip_tags($_POST['userView']));

        $where = "WHERE pipeLatinToUtf8(user_view) = pipeLatinToUtf8('$userView')"; 
        $where .= (isset($_POST['hdkStatusId'])) ? " AND idstatus != {$_POST['hdkStatusId']}" : "";        

        $check =  $hdkStatusDAO->queryHdkStatus($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('status_user_view_already_exists'));
        }else{
            echo json_encode(true);
        }

    }
    
    /**
     * checkDelete
     * 
     * en_us Checks if the priority register is enabled to delete
     * pt_br Verifica se o cadastro de prioridade está habilitado para excluir
     *
     * @return void
     */
    function checkDelete()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatusDTO = new hdkStatusModel();

        //Setting up the model
        $hdkStatusDTO->setStatusId($_POST['hdkStatusId']);
        
        $ret = $hdkStatusDAO->fetchHdkStatusLink($hdkStatusDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get status # {$ret['push']['object']->getStatusId()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Status # {$ret['push']['object']->getStatusId()} data got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);           

            if(count($ret['push']['object']->getLinkList()) > 0){
                $st = false;
                $msg = $this->translator->translate('status_not_delete');
            }else{
                $st = true;
                $msg = "";
            }
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg
        );

        echo json_encode($aRet);
    }
    
    /**
     * deleteStatus
     * 
     * en_us Removes status data from DB
     * pt_br Deleta os dados do status do BD
     *
     * @return void
     */
    function deleteStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $hdkStatusDAO = new hdkStatusDAO();
        $hdkStatusDTO = new hdkStatusModel();        

        //Setting up the model
        $hdkStatusDTO->setStatusId($_POST['hdkStatusId']);
        
        // Delete hdkStatus registration
        $del = $hdkStatusDAO->deleteHdkStatus($hdkStatusDTO);
		if(!$del['status']){
            $st = false;
            $msg = $this->translator->translate("generic_error_msg");
            $this->logger->error("Could not remove status # {$_POST['hdkStatusId']}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $del['push']['message']]);
        }else{
            $st = true;
            $msg = "";
            $this->logger->info("status # {$_POST['hdkStatusId']} was removed successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);

    }
}