<?php

use App\core\Controller;

//DAO
use App\modules\admin\dao\mysql\programDAO;

//Models
use App\modules\admin\models\mysql\programModel;

//services
use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\src\awsServices;


class Program extends Controller
{
    /**
     * @var string
     */
    protected $saveMode;
    
    /**
     * @var string
     */
    protected $imgDir;

    /**
     * @var string
     */
    protected $imgBucket;

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
        
        // set files upload/download directories
        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->imgBucket = "https://{$bucket}.s3.amazonaws.com/logos/";
            $this->imgDir = "logos/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']) {
                $this->imgDir = $this->appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/logos/');
                $this->imgBucket = $_ENV['EXTERNAL_STORAGE_URL'].'logos/';
            } else {
                $storageDir = $this->appSrc->_setFolder($this->appSrc->_getHelpdezkPath().'/storage/');
                $upDir = $this->appSrc->_setFolder($storageDir.'uploads/');
                $this->imgDir = $this->appSrc->_setFolder($upDir.'logos/');
                $this->imgBucket = $_ENV['HDK_URL']."/storage/uploads/logos/";
            }
        }
        
    }

    /**
     * en_us Renders the holidays home screen template
     *
     * pt_br Renderiza o template da tela de home de feriados
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenProgram();
		
		$this->view('admin','program',$params);
    }

    /**
     * en_us Configure program screens
	 * 
	 * pt_br Configura as telas do programa
     *
     * @param  string $option Indicates the type of screen (idx = index, add = new, upd = update)
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenProgram($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $mainSrc = new mainServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;
        
        // -- Search action --
        if($option=='idx'){
            $params['cmbFilters'] = $this->comboProgramFilters();
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';

        if($option != 'idx'){
            $params['cmbModules'] = $adminSrc->_comboModules();
            $params['cmbLocales'] = $mainSrc->_comboLocale();
            $params['modalAddVocabulary'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-add-vocabulary.latte';
        }

        if($option=='upd'){
            $params['programName'] = $obj->getName();
            $params['programController'] = $obj->getController();
            $params['programKeyName'] = $obj->getLanguageKeyName();
            $params['moduleSelected'] = $obj->getModuleId();
            $params['categorySelected'] = $obj->getProgramCategoryId();
            $params['operationList'] = $obj->getOperationList();
        }
        
        return $params;
    }

    /**
     * Returns an array with ID and name of filters
     *
     * @return array
     */
    public function comboProgramFilters(): array
    {
        $aRet = array(
            array("id" => 'name',"text"=>$this->translator->translate('Name'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'module',"text"=>$this->translator->translate('Module'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }
    
    /**
     * en_us Returns cities data to grid
	 * pt_br Retorna os dados das cidades para o grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $programDAO = new programDAO(); 

        $where = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch($filterIndx){
                case "name":
                    $filterIndx = "tbp.name";
                    break;
                case "module":
                    $filterIndx = "tbm.name module";
                    break;
                default:
                    $filterIndx = $filterIndx;
                    break;
            }

            $where .= ((empty($where)) ? "WHERE " : " AND ") . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);          
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= ((empty($where)) ? "WHERE " : " AND ") . "((tbp.name LIKE '%{$quickValue}%' OR pvoc.key_value LIKE '%{$quickValue}%') OR (tbm.name LIKE '%{$quickValue}%' OR mvoc.key_value LIKE '%{$quickValue}%') OR (tbtp.name LIKE '%{$quickValue}%' OR pcvoc.key_value LIKE '%{$quickValue}%'))";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " tbp.status = 'A' ";
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        switch($sortIndx){
            case "name":
                $sortIndx = "name_fmt";
                break;
            case "module":
                $sortIndx = "module_fmt";
                break;
            case "category":
                $sortIndx = "category_fmt";
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
        $countProgram = $programDAO->countPrograms($where); 
        if($countProgram['status']){
            $total_Records = $countProgram['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $programs = $programDAO->queryPrograms($where,$group,$order,$limit);
        
        if($programs['status']){     
            $programsObj = $programs['push']['object']->getGridList();     
            
            foreach($programsObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                $data[] = array(
                    'idprogram'      => $v['idprogram'],
                    'name'          => $v['name_fmt'],
                    'status'        => $status_fmt,
                    'status_val'    => $v['status'],
                    'controller'    => $v['controller'],
                    'module'        => $v['module_fmt'],
                    'category'      => $v['category_fmt']  
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
     * en_us Renders the module's add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenProgram('add');
        
        $this->view('admin','program-create',$params);
    }

    /**
     * en_us Write the program information to the DB
     * pt_br Grava no BD as informações do programa
     */
    public function createProgram()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $programDao = new programDAO();
        $programMod = new programModel();
        
        $operationList = (isset($_POST['operations'])) ? $_POST["operations"] : array();
        array_push($operationList,1);//add access permission
        sort($operationList);
        
        //Setting up the model
        $programMod->setName(trim(strip_tags($_POST['programName'])))
                   ->setModuleId(trim(strip_tags($_POST['cmbModule'])))
                   ->setProgramCategoryId(trim(strip_tags($_POST['cmbCategory'])))
                   ->setController(trim(strip_tags($_POST['programController'])))
                   ->setLanguageKeyName(trim(strip_tags($_POST['programKeyName'])))
                   ->setOperationList($operationList);
                   
        $ins = $programDao->saveProgram($programMod);
        if($ins['status']){
            $st = true;
            $msg = "";
            $programID = $ins['push']['object']->getProgramId();
            $programName = $ins['push']['object']->getName();

            $this->logger->info("Program was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $programID = "";
            $programName = "";

            $this->logger->error("Could not save program. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "programId" => $programID,
            "programName" => $programName
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the module's update screen
     * pt_br Renderiza o template da tela de atualização do cadastro
     * 
     */
    public function formUpdate($programId=null)
    {
        // blocks if the user does not have permission to edit
        if($this->aPermissions[3] != "Y")
            $this->appSrc->_accessDenied();
            
        $programDAO = new programDAO();
        $programModel = new programModel();
        $programModel->setProgramId($programId);

        $ret = $programDAO->getProgram($programModel);

        $params = $this->makeScreenProgram('upd',$ret['push']['object']);
        $params['programId'] = $programId;
        
        $this->view('admin','program-update',$params);
    }

    /**
     * en_us Update the city information to the DB
     * pt_br Atualiza no BD as informações da cidade
     *
     */
    public function updateProgram()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $programDao = new programDAO();
        $programMod = new programModel();
        
        $operationList = (isset($_POST['operations'])) ? $_POST["operations"] : array();
        array_push($operationList,1);//add access permission
        sort($operationList);
        
        //Setting up the model
        $programMod->setName(trim(strip_tags($_POST['programName'])))
                   ->setModuleId(trim(strip_tags($_POST['cmbModule'])))
                   ->setProgramCategoryId(trim(strip_tags($_POST['cmbCategory'])))
                   ->setController(trim(strip_tags($_POST['programController'])))
                   ->setLanguageKeyName(trim(strip_tags($_POST['programKeyName'])))
                   ->setOperationList($operationList)
                   ->setProgramId($_POST['programId'])
                   ->setFlgChangeOperations((isset($_POST['changeOperations'])) ? true : false);
                   
        $upd = $programDao->saveUpdateProgram($programMod);
        if($upd['status']){
            $st = true;
            $msg = "";
            $programId = $upd['push']['object']->getProgramId();

            $this->logger->info("Program was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $upd['push']['message'];
            $programId = "";

            $this->logger->error("Could not update program.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "moduleId" => $programId
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Changes city's status
     *
     * pt_br Muda o status da cidade
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $programDAO = new programDAO();
        $programMod = new programModel();        

        //Setting up the model
        $programMod->setProgramId($_POST['programId'])
                   ->setStatus($_POST['newstatus']);
        
        $upd = $programDAO->changeProgramStatus($programMod);
        if(!$upd['status']){
            $this->logger->error("Could not update program's status.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
            return false;
        }

        $this->logger->info("Program's status was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Check if the program has already been registered before
     * pt_br Verifica se o programa já foi cadastrado anteriormente
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $programDao = new programDAO();

        $name = trim(strip_tags($_POST['programName']));


        $where = "WHERE pipeLatinToUtf8(tbp.name) = pipeLatinToUtf8('{$name}') AND tbp.idprogramcategory = {$_POST['categoryId']}";
        $where .= (isset($_POST['programId'])) ? " AND tbp.idprogram != {$_POST['programId']}" : "";

        $check =  $programDao->queryPrograms($where);
        if(!$check['status']){
            return false;
        }

        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('Alert_record_exist'));
        }else{
            echo json_encode(true);
        }

    }

    /**
     * en_us Returns modules list in HTML to reload combo
     * pt_br Retorna a lista de módulos em HTML para recarregar o combo
     *
     * @return string
     */
    function ajaxModule()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $adminSrc = new adminServices();
        echo $adminSrc->_comboModulesHtml($_POST['selectedID']);

    }

    /**
     * en_us Returns categories list in HTML to reload combo
     * pt_br Retorna a lista de categorias em HTML para recarregar o combo
     *
     * @return string
     */
    function ajaxCategory()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $adminSrc = new adminServices();
        echo $adminSrc->_comboCategoryHtml($_POST['moduleId'],$_POST['selectedID']);

    }

    /**
     * en_us Check if the category has already been registered before
     * pt_br Verifica se a categoria já foi cadastrada anteriormente
     */
    function checkCategory(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $programDao = new programDAO();

        $name = trim(strip_tags($_POST['modal-category-name']));

        $where = "WHERE pipeLatinToUtf8(name) = pipeLatinToUtf8('{$name}') AND idmodule = {$_POST['moduleId']}";
        $where .= (isset($_POST['categoryId'])) ? " AND idprogramcategory != {$_POST['categoryId']}" : "";

        $check =  $programDao->queryCategories($where);
        if(!$check['status']){
            return false;
        }

        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('Alert_record_exist'));
        }else{
            echo json_encode(true);
        }

    }

    /**
     * en_us Write the program's category information to the DB
     * pt_br Grava no BD as informações da categoria de programa
     */
    public function createCategory()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $programDao = new programDAO();
        $programMod = new programModel();
        
        //Setting up the model
        $programMod->setProgramCategory(trim(strip_tags($_POST['modal-category-name'])))
                   ->setModuleId(trim(strip_tags($_POST['moduleId'])))
                   ->setLanguageKeyName(trim(strip_tags($_POST['modal-category-keyname'])));
                   
        $ins = $programDao->insertCategory($programMod);
        if($ins['status']){
            $st = true;
            $msg = "";
            $categoryID = $ins['push']['object']->getProgramCategoryId();
            $categoryName = $ins['push']['object']->getProgramCategory();

            $this->logger->info("Program's category was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $categoryID = "";
            $categoryName = "";

            $this->logger->error("Could not save program's category.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'Error' => $ins['push']['message']]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "categoryId" => $categoryID,
            "categoryName" => $categoryName
        );       

        echo json_encode($aRet);
    }

}