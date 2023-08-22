<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\priorityDAO;
use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\modules\helpdezk\models\mysql\priorityModel;

class hdkPriority extends Controller
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

        $params = $this->makeScreenPriority();
		
		$this->view('helpdezk','priority',$params);
    }
    
    /**
     * makeScreenPriority
     *
     * @param  mixed $option
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenPriority($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);
        
        // -- Area --
        $params['cmbArea'] = $hdkSrc->_comboArea();         

        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboPriorityFilters();
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
            $priorityDAO = new priorityDAO();
            $priorityDTO = new priorityModel();

            $ret = $priorityDAO->getLastOrder($priorityDTO);            
            if(!$ret['status']){
                $this->logger->error("Can't get last priority order", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
                $params['exibitionOrder'] = 1;
            }else{
                $params['exibitionOrder'] = ($option == 'add') ? ($ret['push']['object']->getOrder() + 1) : $ret['push']['object']->getOrder();
            }

            $retDefault = $priorityDAO->getDefaultPriorityId($priorityDTO);            
            if(!$retDefault['status']){
                $this->logger->error("Can't get default priority id", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retDefault['push']['message']]);
                $params['defaultId'] = 0;
            }else{
                $params['defaultId'] = $retDefault['push']['object']->getDefaultId();
            }
        }
        
        if($option=='upd'){
            $params['priorityId'] = $obj->getIdPriority();            
            $params['priorityName'] = $obj->getName();
            $params['priorityOrder'] = $obj->getOrder();
            $params['priorityColor'] = $obj->getColor();
            $params['priorityDefault'] = $obj->getDefault();
            $params['priorityVip'] = $obj->getVip();
            $params['limitDays'] = $obj->getLimitDays();
            $params['limitHours'] = $obj->getLimitHours();
            $params['curOrder'] = $obj->getOrder();
            
        }
        //echo "<br><pre>",print_r($params,true),"</pre>";
        return $params;
       
    }
    
    /**
     * jsonGrid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $priorityDAO = new priorityDAO(); 

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
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " pipeLatinToUtf8(name) LIKE pipeLatinToUtf8('%{$quickValue}%')";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " status = 'A' ";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "`order`";
        
        switch($sortIndx){
            case "order":
                $sortIndx = "`order`";
                break;
            case "default":
                $sortIndx = "`default`";
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
        $countpriority = $priorityDAO->countPriorities($where); 
        if($countpriority['status']){
            $total_Records = $countpriority['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $priority = $priorityDAO->queryPriorities($where,$group,$order,$limit);
        
        if($priority['status']){     
            $priorityObj = $priority['push']['object']->getGridList();     
            
            foreach($priorityObj as $k=>$v) {
                $statusFmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
                $coloFmt = "<span style='background-color:{$v['color']}; height:10px; width:10px; border:0px solid #000;'>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
                $default_fmt = ($v['default'] == 1 ) ? '<span class="label label-info">&check;</span>' : '';

                $data[] = array(
                    'idpriority'    => $v['idpriority'],
                    'name'          => $v['name'],
                    'order'         => $v['order'],
                    'color'         => $coloFmt,
                    'color_val'     => $v['color'],
                    'status'        => $statusFmt,
                    'status_val'    => $v['status'],
                    'default'       => $default_fmt,
                    'default_val'   => $v['default']
                    
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
     * comboPriorityFilters
     *
     * @return array
     */
    public function comboPriorityFilters(): array
    {
        $aRet = array(            
            array("id" => 'name',"text"=>$this->translator->translate('Name'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    /**
     * formCreate
     * 
     * en_us Renders the priority add screen
     * pt_br Renderiza o template da tela de novo cadastro de prioridade
     *
     * @return void
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenPriority('add');

        $this->view('helpdezk','priority-create',$params);
    }

    /**
     * createPriority
     * 
     * en_us Write the priority information to the DB
     * pt_br Grava no BD as informações do priority
     *
     * @return void
     */
    public function createPriority()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $priorityDAO = new priorityDAO();
        $priorityDTO = new priorityModel();
   
        $priorityDTO->setName(trim(strip_tags($_POST['priorityName'])))
                    ->setOrder(trim(strip_tags($_POST['exhibitionOrder'])))
                    ->setColor(trim(strip_tags($_POST['priorityColor'])))
                    ->setDefault((isset($_POST['priorityDefault'])) ? 1 : 0)
                    ->setVip((isset($_POST['priorityVip'])) ? 1 : 0)
                    ->setLimitDays(trim(strip_tags($_POST['limitDays'])))
                    ->setLimitHours(trim(strip_tags($_POST['limitHours'])));

        $ins = $priorityDAO->savePriority($priorityDTO);
        if($ins['status']){
            $this->logger->info("Priority data save successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $priorityId = $ins['push']['object']->getIdPriority();
            $priorityName = $ins['push']['object']->getName();
            $priorityOrder = $ins['push']['object']->getOrder();
            $priorityColor = $ins['push']['object']->getColor();
            
        }else{
            $this->logger->error("Can't save priority data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $priorityId = "";
            $priorityName = "";
            $priorityOrder = "";
            $priorityColor = "";
        }   
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "priorityId"    => $priorityId,
            "priorityName"  => $priorityName,
            "priorityOrder"  => $priorityOrder,
            "priorityColor"  => $priorityColor
        );

        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     * 
     * en_us Renders the priority update screen
     * pt_br Renderiza o template da atualização do cadastro de prioridade
     *
     * @param  mixed $priorityId
     * @return void
     */
    public function formUpdate($priorityId=null)
    {
        $priorityDAO = new priorityDAO();
        $priorityModel = new priorityModel();
        $priorityModel->setIdPriority($priorityId); 
        
        $ret = $priorityDAO->getPriority($priorityModel); 

        $params = $this->makeScreenPriority('upd',$ret['push']['object']);
        
        $this->view('helpdezk','priority-update',$params);
    }
    
    /**
     * updatePriority
     *
     * @return void
     */
    public function updatePriority()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $priorityDAO = new priorityDAO();
        $priorityDTO = new priorityModel();

        $priorityDTO->setIdPriority($_POST['priorityId'])
                    ->setName(trim(strip_tags($_POST['priorityName'])))
                    ->setOrder(trim(strip_tags($_POST['exhibitionOrder'])))
                    ->setColor(trim(strip_tags($_POST['priorityColor'])))
                    ->setDefault((isset($_POST['priorityDefault'])) ? 1 : 0)
                    ->setVip((isset($_POST['priorityVip'])) ? 1 : 0)
                    ->setLimitDays(trim(strip_tags($_POST['limitDays'])))
                    ->setLimitHours(trim(strip_tags($_POST['limitHours'])))
                    ->setDefaultId(trim(strip_tags($_POST['defaultId'])))
                    ->setOrderTmp($_POST['curOrder']);             
               
        $upd = $priorityDAO->saveUpdatePriority($priorityDTO);
        if($upd['status']){
            $this->logger->info("Priority # {$upd['push']['object']->getIdPriority()} data update successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $priorityId = $upd['push']['object']->getIdPriority();
            
        }else{
            $this->logger->error("Can't update priority # {$upd['push']['object']->getIdPriority()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $upd['push']['message'];
            $priorityId = "";
        }           
       
        $aRet = array(
            "success"       => $st,
            "priorityId"    => $priorityId
        );        

        echo json_encode($aRet);
    }
    
    /**
     * changeStatus
     * 
     * en_us Changes priority's status
     * pt_br Muda o status da prioridade
     *
     * @return void
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $priorityDAO = new priorityDAO();
        $priorityModel = new priorityModel();

        //Setting up the model
        $priorityModel->setIdPriority($_POST['priorityId'])
                      ->setStatus($_POST['newStatus'])
                      ->setDefault($_POST['isDefault']);
        
        $upd = $priorityDAO->saveNewStatus($priorityModel);
        if(!$upd['status']){
            $this->logger->error("Can't update priority # {$upd['push']['object']->getIdPriority()} status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
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
     * en_us Check if the priority has already been registered before
     * pt_br Verifica se o motivo já foi cadastrada anteriormente
     *
     * @return void
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $priorityDAO = new priorityDAO();
        $priority = trim(strip_tags($_POST['priorityName']));

        $where = "WHERE pipeLatinToUtf8(`name`) = pipeLatinToUtf8('$priority')"; 
        $where .= (isset($_POST['priorityId'])) ? " AND idpriority != {$_POST['priorityId']}" : "";        

        $check =  $priorityDAO->queryPriorities($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('priority_already_exists'));
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

        $priorityDAO = new priorityDAO();
        $priorityDTO = new priorityModel();

        //Setting up the model
        $priorityDTO->setIdPriority($_POST['priorityId']);
        
        $ret = $priorityDAO->fetchPriorityLink($priorityDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get priority # {$ret['push']['object']->getIdPriority()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Priority # {$ret['push']['object']->getIdPriority()} data got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);           

            if(count($ret['push']['object']->getLinkList()) > 0){
                $st = false;
                $msg = $this->translator->translate('priority_not_delete');
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
     * deletePriority
     * 
     * en_us Removes priority data from DB
     * pt_br Deleta os dados da prioridade do BD
     *
     * @return void
     */
    function deletePriority()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $priorityDAO = new priorityDAO();
        $priorityDTO = new priorityModel();        

        //Setting up the model
        $priorityDTO->setIdPriority($_POST['priorityId'])
                    ->setDefault($_POST['isDefault']);

        // Delete priority registration
        $del = $priorityDAO->saveDeletePriority($priorityDTO);
		if(!$del['status']){
            $st = false;
            $msg = $this->translator->translate("generic_error_msg");
            $this->logger->error("Could not remove priority # {$_POST['priorityId']}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $del['push']['message']]);
        }else{
            $st = true;
            $msg = "";
            $this->logger->info("Priority # {$_POST['priorityId']} was removed successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);

    }
}