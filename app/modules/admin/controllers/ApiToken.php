<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\apiTokenDAO;
use App\modules\admin\models\mysql\apiTokenModel;

use App\modules\admin\src\adminServices;
//use \DateTime;

class apiToken extends Controller
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
     * en_us Renders the apiToken home screen template
     *
     * pt_br Renderiza o template da tela de home de apiToken
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenApiToken();
		
		$this->view('admin','apiToken',$params);
    }

    /**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
    public function makeScreenApiToken($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- combo data --
        $params['cmbValidity'] = $this->comboValidity();

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;        
        
        // -- Search action --
        if($option=='idx'){
            $params['cmbFilters'] = $this->comboApiTokenFilters();
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }

        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalError'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-error.latte';

        if($option=='upd'){
            $params['idtoken'] = $obj->getIdApiToken();
            $params['app'] = $obj->getApp();
            $params['company'] = $obj->getCompany(); 
            $params['email'] = $obj->getEmail();
            $params['numberValidity'] = '2';
            $params['validity'] = 'Y';      
        }elseif($option=='add'){
            $params['numberValidity'] = '2';
            $params['validity'] = 'Y';
        }
      
        return $params;
    }

    public function jsonGrid()
    {
        $apiTokenDao = new apiTokenDAO(); 

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
                case "app":
                    $filterIndx = "app";
                    break;
                case "company":
                    $filterIndx = "company";
                    break;
                case "email":
                    $filterIndx = "email";
                    break;
                default:
                    $filterIndx = $filterIndx;
                break;
            }
            
            $where .= (empty($where) ? "WHERE " : " AND ") . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " WHERE " . " (pipeLatinToUtf8(app) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(company) LIKE '%{$quickValue}%' OR pipeLatinToUtf8(email) LIKE '%{$quickValue}%')";
        }

       //sort options
       $pq_sort = json_decode($_POST['pq_sort']);
       $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "`app`";
       
       $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
       $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countApiToken = $apiTokenDao->countApiToken($where,$group); 
        if($countApiToken['status']){
            $total_Records = $countApiToken['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $apiToken = $apiTokenDao->queryApiToken($where,$group,$order,$limit);
        
        if($apiToken['status']){     
            $apiTokenObj = $apiToken['push']['object']->getGridList();

            foreach($apiTokenObj as $k=>$v) {

                $data[] = array(
                    'id'                => $v['idtoken'],
                    'app'               => strip_tags($v['app']),
                    'company'           => strip_tags($v['company']),
                    'email'             => $v['email'],
                    'token'             => $v['token'],
                    'validity'          => $v['validity']    
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
     * Returns an array with ID and name of filters
     *
     * @return array
     */
    public function comboApiTokenFilters(): array
    {
        $aRet = array(
            array("id" => 'app',"text"=>$this->translator->translate('name_app'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')), // equal
            array("id" => 'company',"text"=>$this->translator->translate('Company'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'email',"text"=>$this->translator->translate('email'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    public function comboValidity(): array
    {

        $aDay = array(
            array("id" => 'D',"text"=>$this->translator->translate('days')),
            array("id" => 'M',"text"=>$this->translator->translate('month')),
            array("id" => 'Y',"text"=>$this->translator->translate('year')),
        );
        
        return $aDay;
    }

    /*
     * en_us Renders the apiToken add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenApiToken('add');

        $this->view('admin','apiToken-create',$params);
    }

    /**
     * en_us Write the ApiToken information to the DB
     *
     * pt_br Grava no BD as informações do Token API
     */
    public function createApiToken()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $apiTokenDAO = new apiTokenDAO();
        $apiTokenModel = new apiTokenModel();
       
        switch($_POST['cmbValidity']){
            case 'D':
                $timeCondition = "DAYS";
                break;
            case 'M':
                $timeCondition = "MONTHS";
                break;
            case 'D':
                $timeCondition = "YEARS";
                break;  
        }
        $expirationTime = "{$_POST['numberValidity']} {$timeCondition}";
        $expiredAt = (new DateTime())->modify("+{$expirationTime}")
                                    ->format('Y-m-d H:i:s');
        $expiredAtTS = (new DateTime())->modify("+{$expirationTime}")
                                    ->format('U');

         $apiTokenModel->setApp(strip_tags(trim($_POST['app'])))
                            ->setCompany(strip_tags(trim($_POST['company'])))
                            ->setEmail($_POST['email'])
                            ->setApiToken("")
                            ->setValidity($expiredAtTS);  

        $ins = $apiTokenDAO->insertApiToken($apiTokenModel);
        if($ins['status']){
            $apiTokenID = $ins['push']['object']->getIdApiToken();
            $payload = array(
                'id' => $ins['push']['object']->getIdApiToken(),
                'app' => $ins['push']['object']->getApp(),
                'company' => $ins['push']['object']->getCompany(),
                'email' => $ins['push']['object']->getEmail(),
                'expiredAt' => $expiredAt,
                'expiredAtTS' => $expiredAtTS
            );
            
            $ret = $this->appSrc->_makeJWT($payload);          
            if(!$ret){
                $this->logger->error("JWT not returned", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = false;
            }else{
                $ins['push']['object']->setApiToken($ret);
                $up = $apiTokenDAO->updateToken($ins['push']['object']);
                if($up['status']){
                    $st = true;
                    $msg = "";
                    $msg = $up['push']['message'];                                                          
                    $apiToken = "";
                }else{
                    $st = false;
                    $msg = $up['push']['message'];
                } 
            }            
            
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $apiTokenID = "";
        }     
                              
        $aRet = array(
            "success"               => $st,
            "id"                    => $apiTokenID,
            "app"                   => $apiTokenModel->getApp(),
            "company"               => $apiTokenModel->getCompany(),
            "email"                 => $apiTokenModel->getEmail(),
            "ApiToken"              => $apiTokenModel->getApiToken(),
            "validity"              => $apiTokenModel->getValidity()
        );
        echo json_encode($aRet);
    }

    public function formUpdate($apiTokenID=null)
    {  
        // blocks if the user does not have permission to edit
        if($this->aPermissions[3] != "Y")
            $this->appSrc->_accessDenied();
            
        $apiTokenDAO = new apiTokenDAO();
        $apiTokenModel = new apiTokenModel();
        $apiTokenModel->setIdApiToken($apiTokenID); 

        $apiTokenUpd = $apiTokenDAO->getApiToken($apiTokenModel);
        $params = $this->makeScreenApiToken('upd',$apiTokenUpd['push']['object']);
        $params['apiTokenID'] = $apiTokenID;
      
        $this->view('admin','apiToken-update',$params);
    }

    public function updateApiToken()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $apiTokenDAO = new apiTokenDAO();
        $apiTokenModel = new apiTokenModel();
    
        switch($_POST['cmbValidity']){
            case 'D':
                $timeCondition = "DAYS";
                break;
            case 'M':
                $timeCondition = "MONTHS";
                break;
            case 'D':
                $timeCondition = "YEARS";
                break;  
        }
        $expirationTime = "{$_POST['numberValidity']} {$timeCondition}";
        $expiredAt = (new DateTime())->modify("+{$expirationTime}")
                                    ->format('Y-m-d H:i:s');
        $expiredAtTS = (new DateTime())->modify("+{$expirationTime}")
                                    ->format('U');

         $apiTokenModel->setIdApiToken($_POST['apiTokenID'])
                            ->setApp(strip_tags(trim($_POST['app'])))
                            ->setCompany(strip_tags(trim($_POST['company'])))                            
                            ->setEmail($_POST['email'])
                            ->setApiToken("")
                            ->setValidity($expiredAtTS);  
        $upd = $apiTokenDAO->updateApiToken($apiTokenModel);
        if($upd['status']){
            $apiTokenID = $upd['push']['object']->getIdApiToken();            
            $payload = array(
                'id' => $upd['push']['object']->getIdApiToken(),
                'app' => $upd['push']['object']->getApp(),
                'company' => $upd['push']['object']->getCompany(),
                'email' => $upd['push']['object']->getEmail(),
                'expiredAt' => $expiredAt,
                'expiredAtTS' => $expiredAtTS
            );
            $ret = $this->appSrc->_makeJWT($payload);          
            if(!$ret){
                $this->logger->error("JWT not returned", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $st = false;
            }
            else{
                $upd['push']['object']->setApiToken($ret);
                $up = $apiTokenDAO->updateToken($upd['push']['object']);
                if($up['status']){
                    $st = true;
                    $msg = $up['push']['message'];                    
                    $apiToken = "";                   
                   
                }else{
                    $st = false;
                    $msg = $up['push']['message']; 
                } 
            }            
            
        }else{
            $st = false;
            $msg = $upd['push']['message'];
            $apiTokenID = "";
        }     
                              
        $aRet = array(
            "success"               => $st,
            "id"                    => $apiTokenID,
            "app"                   => $apiTokenModel->getApp(),
            "company"               => $apiTokenModel->getCompany(),
            "email"                 => $apiTokenModel->getEmail(),
            "ApiToken"              => $apiTokenModel->getApiToken(),
            "validity"              => $apiTokenModel->getValidity()
        );
        echo json_encode($aRet);
    }

    /**
     * en_us Remove the apiToken from the DB
     *
     * pt_br Remove a Area de Token APP do BD
     */
    function deleteApiToken()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        $apiTokenDAO = new apiTokenDAO();
        $apiTokenModel = new apiTokenModel();
        
        //Setting up the model       
        $apiTokenModel->setIdApiToken($_POST['apiTokenID']);

        $del = $apiTokenDAO->deleteApiToken($apiTokenModel);
        if(!$del['status']){
            return false;
        }
        $aRet = array(
            "success"   => true,
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Check if the app has already been registered before
     *
     * pt_br Verifica se o aplicativo já foi cadastrado anteriormente
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $apiTokenDAO = new apiTokenDAO();

        $appName = strip_tags(trim($_POST['app']));
        $companyName = strip_tags(trim($_POST['companyName']));

        $where = "WHERE pipeLatinToUtf8(UPPER(company)) = UPPER('$companyName') AND pipeLatinToUtf8(UPPER(app)) = UPPER('$appName')"; 
        $where .= (isset($_POST['apiTokenID'])) ? " AND idtoken != {$_POST['apiTokenID']}" : "";        

        $check =  $apiTokenDAO->queryApiToken($where);
        if(!$check['status']){ 
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('app_already_registred'));
        }else{
            echo json_encode(true);
        }

    }

}