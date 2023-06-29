<?php

use App\core\Controller;
use App\modules\admin\dao\mysql\userTypeDAO;
use App\modules\admin\src\adminServices;
use App\modules\admin\models\mysql\userTypeModel;

class userType extends Controller
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

        $params = $this->makeScreenUserType();
		
		$this->view('admin','userType',$params);
    }

    public function makeScreenUserType($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

       
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboUserTypeFilters();
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
            $params['id'] = $obj->getIdUserType();
            $params['userType'] = $obj->getUserType();
            $params['permissionGroup'] = $obj->getPermissionGroup();
            $params['langKeyName'] = $obj->getLangKeyName();
        }
        
        return $params;
    }

    public function jsonGrid()
    {
        $userTypeDAO = new userTypeDAO(); 

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
                case "userType":
                    $filterIndx = "name";
                    break;
                case "permissionGroup":
                    $filterIndx = "permissionGroup";
                    break;
                default:
                    $filterIndx = $filterIndx;
                break;
            }
            
            $where .=  " WHERE " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

         //Search with params sended from quick search input
         if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
         {
             $quickValue = trim($_POST['quickValue']);
             $quickValue = str_replace(" ","%",$quickValue);
             $where .= " WHERE " . " (pipeLatinToUtf8(name) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(permissiongroup) LIKE '%{$quickValue}%')";
         }

         //sort options
         $pq_sort = json_decode($_POST['pq_sort']);
         $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";

         switch($sortIndx){
            case "userType":
                $sortIndx = "name"; 
                break;
            default:
                $sortIndx = "{$sortIndx}";
                break;
        }
         
         $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
         $order = "ORDER BY {$sortIndx} {$sortDir}";
         
         $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
     
         $pq_rPP = $_POST["pq_rpp"];
         
         //Count records
         $countUserType = $userTypeDAO->countUserType($where); 
         if($countUserType['status']){
             $total_Records = $countUserType['push']['object']->getTotalRows();
         }else{
             $total_Records = 0;
         }
         
         $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
         $limit = "LIMIT {$skip},$pq_rPP";
 
         $userType = $userTypeDAO->queryUserType($where,$group,$order,$limit);
        
        if($userType['status']){     
            $userTypeObj = $userType['push']['object']->getGridList();     
            
            foreach($userTypeObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
                $permissionGroup_fmt = ($v['permissiongroup'] == 'Y' ) ? '<span class="label label-info">Y</span>' : '<span class="label label-danger">N</span>';
               

                $data[] = array(
                    'id'                        => $v['idtypeperson'],
                    'userType'                  => strip_tags($v['name']),
                    'permissionGroup'           => $permissionGroup_fmt,
                    'permissionGroup_val'       => ($v['permissiongroup'] == 'Y' ? 'Sim' :'Não'),
                    'langKeyName'               => $v['lang_key_name'],
                    'status'                    => $status_fmt,
                    'status_val'                => $v['status']   
                    
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

    public function comboUserTypeFilters(): array
    {
        $aRet = array(
            array("id" => 'userType',"text"=>$this->translator->translate('userType'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'permissionGroup',"text"=>$this->translator->translate('permissionGroup'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
        );
        
        return $aRet;
    }

    /*
     * en_us Renders the userType add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenUserType();

        $this->view('admin','userType-create',$params);
    }

    /**
     * en_us Write the userType information to the DB
     *
     * pt_br Grava no BD as informações do userType
     */  

    public function createUserType()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $userTypeDAO = new userTypeDAO();
        $userTypeModel = new userTypeModel();

        $permissionGroup = isset($_POST['permissionGroup']) ? 'Y' : 'N';
   
        $userTypeModel->setUserType(strip_tags(trim($_POST['userType'])))
                           ->setPermissionGroup($permissionGroup)
                           ->setLangKeyName(strip_tags(trim($_POST['langKeyName'])));
                          

        $ins = $userTypeDAO->insertUserType($userTypeModel);
        if($ins['status']){
            $st = true;
            $msg = "";
            $userTypeID = $ins['push']['object']->getUserType(); 
            
        }else{
            $st = false;
            $msg = $ins['push']['message'];
        }   
              
        
        $aRet = array(
            "success"                   => $st,
            "id"                        => $userTypeID,
            "userType"                  => $userTypeModel->getUserType(),
            "permissionGroup"           => $permissionGroup =='Y' ? 'Sim' : 'Não',
            "langKeyName"               => $userTypeModel->getLangKeyName(),
        );
        echo json_encode($aRet);
    }

    public function formUpdate($userTypeID=null)
    {
        // blocks if the user does not have permission to edit
        if($this->aPermissions[3] != "Y")
            $this->appSrc->_accessDenied();
            
        $userTypeDAO = new userTypeDAO();
        $userTypeModel = new userTypeModel();
        $userTypeModel->setIdUserType($userTypeID); 
        
        $userTypeUpd = $userTypeDAO->getUserType($userTypeModel); 

        $params = $this->makeScreenUserType('upd',$userTypeUpd['push']['object']);
        $params['userTypeID'] = $userTypeID;
      
        $this->view('admin','userType-update',$params);
    }

    public function updateUserType()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $userTypeDAO = new userTypeDAO();
        $userTypeModel = new userTypeModel();
        
        $permissionGroup = isset($_POST['permissionGroup']) ? 'Y' : 'N';
   
        $userTypeModel->setIdUserType($_POST['userTypeID'])
                            ->setUserType(strip_tags(trim($_POST['userType'])))
                            ->setPermissionGroup($permissionGroup)
                            ->setLangKeyName(strip_tags(trim($_POST['langKeyName'])));
                           
        $upd = $userTypeDAO->updateUserType($userTypeModel);
        if($upd['status']){
            $st = true;
            $msg = "";
            $userTypeID = $upd['push']['object']->getIdUserType();
            
        }else{
            $st = false;
            $msg = $upd['push']['message'];
        }
           
       
        $aRet = array(
            "success"               => $st,
            "id"                    => $userTypeID,
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Changes userType's status
     *
     * pt_br Muda o status do tipo de usuário
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $userTypeDAO = new userTypeDAO();
        $userTypeModel = new userTypeModel();

        //Setting up the model
        $userTypeModel->setIdUserType($_POST['userTypeID'])
                           ->setstatus($_POST['newstatus']);
        
        $upd = $userTypeDAO->updateStatus($userTypeModel);
        if(!$upd['status']){
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Remove the userTypeModel from the DB
     *
     * pt_br Remove o Tipo de usuário do BD
     */

    function deleteUserType()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        $userTypeDAO = new userTypeDAO();
        $userTypeModel = new userTypeModel();
        
        //Setting up the model       
        $userTypeModel->setIdUserType($_POST['userTypeID']);

        $del = $userTypeDAO->deleteUserType($userTypeModel);
        if(!$del['status']){
            return false;
        } 
        $aRet = array(
            "success"   => true,
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the userType has already been registered before
     *
     * pt_br Verifica se o Tipo de usuário já foi cadastrado anteriormente
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $userTypeDAO = new userTypeDAO();

        $userType = strip_tags($_POST['userType']);

        $where = "WHERE pipeLatinToUtf8(UPPER(name)) = UPPER('$userType')"; 
        $where .= (isset($_POST['userTypeID'])) ? " AND idtypeperson != {$_POST['userTypeID']}" : "";
        

        $check =  $userTypeDAO->queryUserType($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('userType_exists_alert'));
        }else{
            echo json_encode(true);
        }

    }
}