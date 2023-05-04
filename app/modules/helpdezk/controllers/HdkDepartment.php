<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\departmentDAO;
use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\modules\helpdezk\models\mysql\departmentModel;

class hdkDepartment extends Controller
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
    
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenDepartment();
		
		$this->view('helpdezk','department',$params);
    }

    public function makeScreenDepartment($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Companies --
        $params['cmbCompany'] = $adminSrc->_comboCompany();
        
        // -- Search action --
        if($option=='idx'){
            $params['cmbFilters'] = $this->comboDepartmentFilters();
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

        if($option=='upd'){
            $params['id'] = $obj->getIdDepartment();
            $params['department'] = $obj->getDepartment();
            $params['idcompany'] = $obj->getIdCompany();
        }
        
        return $params;
    }

    public function jsonGrid()
    {
        $departmentDAO = new departmentDAO(); 

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
                case "department":
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

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= " AND  a.status = 'A' ";
        }

         //sort options
         $pq_sort = json_decode($_POST['pq_sort']);
         $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "a.name";
         
         $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
         $order = "ORDER BY {$sortIndx} {$sortDir}";
         
         $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
     
         $pq_rPP = $_POST["pq_rpp"];
         
         //Count records
         $countDepartment = $departmentDAO->countDepartment($where); 
         if($countDepartment['status']){
             $total_Records = $countDepartment['push']['object']->getTotalRows();
         }else{
             $total_Records = 0;
         }
         
         $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
         $limit = "LIMIT {$skip},$pq_rPP";
 
         $department = $departmentDAO->queryDepartment($where,$group,$order,$limit);
        
        if($department['status']){     
            $departmentObj = $department['push']['object']->getGridList();     
            
            foreach($departmentObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
               

                $data[] = array(
                    'id'            => $v['iddepartment'],
                    'department'    => strip_tags($v['department']),
                    'idCompany'     => $v['idcompany'],
                    'company'       => strip_tags($v['company']),
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

    public function comboDepartmentFilters(): array
    {
        $aRet = array(
            array("id" => 'department',"text"=>$this->translator->translate('Department'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'company',"text"=>$this->translator->translate('Company'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the new record screen
     * pt_br Renderiza a tela de novo cadastro
     *
     * @return void
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenDepartment();

        $this->view('helpdezk','department-create',$params);
    }

    /**
     * en_us Write the department information to the DB
     * pt_br Grava no BD as informações do departamento
     *
     * @return void
     */
    public function createDepartment()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();
   
        $departmentModel->setDepartment(trim(strip_tags($_POST['department'])))
                        ->setIdCompany($_POST['cmbCompany']);

        $ins = $departmentDAO->insertDepartment($departmentModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $departmentID = $ins['push']['object']->getIdDepartment();

            $arraymodal = $departmentDAO->queryDepartment("AND iddepartment=$departmentID");        
            if($arraymodal['status']){     
                $modalObj = $arraymodal['push']['object']->getGridList();
                $company     = $modalObj[0]['company']; 
            } 
            
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $departmentID = "";
        }   
              
        
        $aRet = array(
            "success"               => $st,
            "id"                    => $departmentID,
            "department"            => $departmentModel->getDepartment(),
            "company"               => $company,
        );
        echo json_encode($aRet);
    }
    
    /**
     * en_us Renders register's edit screen
     * pt_br Renderiza a tela da edição do cadastro
     *
     * @param  mixed $departmentID
     * @return void
     */
    public function formUpdate($departmentID=null)
    {
        // blocks if the user does not have permission to edit
        if($this->aPermissions[3] != "Y")
            $this->appSrc->_accessDenied();
            
        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();
        $departmentModel->setIdDepartment($departmentID); 
        
        $departmentUpd = $departmentDAO->getDepartment($departmentModel); 

        $params = $this->makeScreenDepartment('upd',$departmentUpd['push']['object']);
        $params['departmentID'] = $departmentID;
      
        $this->view('helpdezk','department-update',$params);
    }
    
    /**
     * en_us Update the department information to the DB
     * pt_br Atualiza no BD as informações do departamento
     *
     * @return void
     */
    public function updateDepartment()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $departmentDAO = new departmentDAO();
        $departmentModel = new departmentModel();

        $departmentModel->setIdDepartment($_POST['departmentID'])
                        ->setDepartment(trim(strip_tags($_POST['department'])))
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
     * pt_br Altera o status do Departamento
     *
     * @return void
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
     * pt_br Remove o Department do BD
     *
     * @return void
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
     * pt_br Verifica se o Department já foi cadastrada anteriormente
     *
     * @return void
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $departmentDAO = new departmentDAO();

        $department = trim(strip_tags($_POST['department']));
        $department = addslashes($_POST['department']);
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
}