<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\reasonDAO;
use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\modules\helpdezk\models\mysql\reasonModel;

class hdkReason extends Controller
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
     * en_us Renders reason's home screen
     * pt_br Renderiza a tela inicial do motivo de abertura
     *
     * @return void
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenReason();
		
		$this->view('helpdezk','reason',$params);
    }
    
    /**
     * makeScreenReason
     * 
     * en_us Makes the necessary parameters to render the program's screens
     * pt_br Cria os parâmetros necessários para renderizar as telas do programa
     *
     * @param  mixed $option Indicates the screen where the parameters will be made available
     * @param  mixed $obj    Data object for the registration edit screen
     * @return void
     */
    public function makeScreenReason($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);
       
        // -- Area --
        $params['cmbArea'] = $hdkSrc->_comboArea();         

        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboReasonFilters();
          $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
          $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte';//upload image modal
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        if($option=='upd'){
            $params['idreason'] = $obj->getIdReason();            
            $params['reason'] = $obj->getReason();
            $params['areaID'] = $obj->getIdArea();
            $params['typeID'] = $obj->getIdType();
            $params['itemID'] = $obj->getIdItem();
            $params['serviceID'] = $obj->getIdService();

            //-- Type --
            $params['cmbType']  = $hdkSrc->_comboType($obj->getIdArea());

            // -- Item --
            $params['cmbItem']  = $hdkSrc->_comboItem($obj->getIdType());

            // -- Service --
            $params['cmbService']   = $hdkSrc->_comboService($obj->getIdItem());            
        }
        
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
        $reasonDAO = new reasonDAO(); 

        $where = "";
        $group = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];           
                   
            switch($filterIndx){
                case "reason":
                    $filterIndx = "a.name";
                    break;
                case "service":
                    $filterIndx = "b.name";
                    break;
                case "item":
                    $filterIndx = "c.name";
                    break;
                case "type":
                    $filterIndx = "d.name";
                    break;    
                case "area":
                    $filterIndx = "e.name";
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
            $where .= " AND " . " (pipeLatinToUtf8(a.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(b.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(c.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(d.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(e.name) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "reason";
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countReason = $reasonDAO->countReason($where); 
        if($countReason['status']){
            $total_Records = $countReason['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $reason = $reasonDAO->queryReason($where,$group,$order,$limit);
        
        if($reason['status']){     
            $reasonObj = $reason['push']['object']->getGridList();     
            
            foreach($reasonObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                $data[] = array(
                    'id'            => $v['idreason'],
                    'reason'        => $v['reason'],
                    'area'          => $v['area'],
                    'type'          => $v['type'],
                    'item'          => $v['item'],
                    'service'       => $v['service'],
                    'status'        => $status_fmt,
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
     * comboReasonFilters
     * 
     * en_us Returns array with filters options to search data
     * pt_br Retorna array com opções de filtros para pesquisar dados
     *
     * @return array
     */
    public function comboReasonFilters(): array
    {
        $aRet = array(            
            array("id" => 'reason',"text"=>$this->translator->translate('Reason'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'area',"text"=>$this->translator->translate('Area'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'type',"text"=>$this->translator->translate('Type'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'item',"text"=>$this->translator->translate('Item'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'service',"text"=>$this->translator->translate('Service'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
        );
        
        return $aRet;
    }

    /**
     * formCreate
     * 
     * en_us Renders the reason add screen
     * pt_br Renderiza o template da tela de novo cadastro
     *
     * @return void
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenReason();

        $this->view('helpdezk','reason-create',$params);
    }

    /**
     * en_us Write the reason information to the DB
     *
     * pt_br Grava no BD as informações do reason
     */
    public function createReason()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $reasonDAO = new reasonDAO();
        $reasonModel = new reasonModel();
   
        $reasonModel->setReason(trim(strip_tags($_POST['reason'])))
                    ->setIdService($_POST['cmbService'])
                    ->setIdArea($_POST['cmbArea'])
                    ->setIdItem($_POST['cmbItem'])
                    ->setIdType($_POST['cmbType']);

        $ins = $reasonDAO->insertReason($reasonModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $reasonID = $ins['push']['object']->getIdReason();            

            $arraymodal = $reasonDAO->queryReason("AND idreason=$reasonID");        
            if($arraymodal['status']){     
                $modalObj = $arraymodal['push']['object']->getGridList();
                $service     = $modalObj[0]['service'];
                $item     = $modalObj[0]['item']; 
                $type     = $modalObj[0]['type']; 
                $area     = $modalObj[0]['area'];  
            } 
            
        }else{
            $st = false;
            $msg = $ins['push']['message'];
        }              
        
        $aRet = array(
            "success"               => $st,
            "id"                    => $reasonID,
            "reason"                => $reasonModel->getReason(),
            "area"                  => $area,
            "type"                  => $type,
            "item"                  => $item,
            "service"               => $service,
        );
        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     *
     * @param  mixed $reasonID
     * @return void
     */
    public function formUpdate($reasonID=null)
    {
        $reasonDAO = new reasonDAO();
        $reasonModel = new reasonModel();
        $reasonModel->setIdReason($reasonID); 
        
        $reasonUpd = $reasonDAO->getReason($reasonModel); 

        $params = $this->makeScreenReason('upd',$reasonUpd['push']['object']);
        $params['reasonID'] = $reasonID;
        $this->view('helpdezk','reason-update',$params);
    }
    
    /**
     * updateReason
     *
     * @return void
     */
    public function updateReason()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $reasonDAO = new reasonDAO();
        $reasonModel = new reasonModel();

        $reasonModel->setIdReason($_POST['reasonID'])
                    ->setreason(trim(strip_tags($_POST['reason'])))
                    ->setIdService($_POST['cmbService'])
                    ->setIdArea($_POST['cmbArea'])
                    ->setIdItem($_POST['cmbItem'])
                    ->setIdType($_POST['cmbType']);             
               
        $upd = $reasonDAO->updateReason($reasonModel);
        if($upd['status']){
            $st = true;
            $msg = "";
            $reasonID = $upd['push']['object']->getIdReason();
            
        }else{
            $st = false;
            $msg = $upd['push']['message'];
        }
           
       
        $aRet = array(
            "success"               => $st,
            "id"                    => $reasonID,
            "reason"                => $reasonModel->getReason(),
            "service"               => $reasonModel->getIdService(),
        );        

        echo json_encode($aRet);
    }

    /**
     * changeStatus
     * 
     * en_us Changes reason's status
     * pt_br Muda o status do motivo
     *
     * @return void
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $reasonDAO = new reasonDAO();
        $reasonModel = new reasonModel();

        //Setting up the model
        $reasonModel->setIdReason($_POST['reasonID'])
                           ->setstatus($_POST['newstatus']);
        
        $upd = $reasonDAO->updateStatus($reasonModel);
        if(!$upd['status']){
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
     * en_us Check if the reason has already been registered before
     * pt_br Verifica se o motivo já foi cadastrada anteriormente
     *
     * @return void
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $reasonDAO = new reasonDAO();

        $reason = strip_tags($_POST['reason']);
        $service = strip_tags($_POST['idservice']);

        $where = "AND b.idservice = '$service'  AND pipeLatinToUtf8(UPPER(a.name)) = UPPER('$reason')"; 
        $where .= (isset($_POST['reasonID'])) ? " AND idreason != {$_POST['reasonID']}" : "";
        

        $check =  $reasonDAO->queryReason($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('reason_exists_alert'));
        }else{
            echo json_encode(true);
        }

    }
}