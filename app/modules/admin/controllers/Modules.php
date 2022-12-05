<?php

use App\core\Controller;

//DAO
use App\modules\admin\dao\mysql\moduleDAO;

//Models
use App\modules\admin\models\mysql\moduleModel;

//services
use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\src\awsServices;


class Modules extends Controller
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
    
    public function __construct()
    {
        parent::__construct();

		$this->appSrc->_sessionValidate();

        //
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
        $params = $this->makeScreenModules();
		
		$this->view('admin','modules',$params);
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
    public function makeScreenModules($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $mainSrc = new mainServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();
        
        // -- Search action --
        if($option=='idx'){
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts();
            $params['cmbFilters'] = $this->comboModulesFilters();
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte';

        if($option != 'idx'){
            $params['cmbModules'] = $adminSrc->_comboModules();
            $params['cmbLocales'] = $mainSrc->_comboLocale();
            $params['modalAddVocabulary'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-add-vocabulary.latte';
        }

        if($option=='upd'){
            $params['moduleName'] = $obj->getName();
            $params['modulePath'] = $obj->getPath();
            $params['moduleKeyName'] = $obj->getLanguageKeyName();
            $params['isDefault'] = (!empty($obj->getIsDefault())) ? 1 : 0;
            $params['moduleSelected'] = $obj->getIdModule();

            if(!empty($obj->getHeaderLogo())){
                if($this->saveMode == "aws-s3"){
                    $awsSrc = new awsServices();
                    $retS3 = $awsSrc->_getFile("{$this->imgDir}{$obj->getHeaderLogo()}");
                    $params['showLogo'] = !$retS3['success'] ? false : true;
                    $params['moduleLogoUrl'] = !$retS3['success'] ? "" : $retS3['fileUrl'];
                }else{
                    $fileSize = filesize($this->imgDir.$obj->getHeaderLogo()); 
                    $params['showLogo'] = ($fileSize <= 0) ? false : true;
                    $params['moduleLogoUrl'] = "{$this->imgBucket}{$obj->getHeaderLogo()}";
                }
            }else{
                $params['showLogo'] = false;
                $params['moduleLogoUrl'] = "";
            }
        }
        
        return $params;
    }
    
    /**
     * en_us Returns cities data to grid
	 * 
	 * pt_br Retorna os dados das cidades para o grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $moduleDAO = new moduleDAO(); 

        $where = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            $where .= ((empty($where)) ? "WHERE " : " AND ") . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);          
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= ((empty($where)) ? "WHERE " : " AND ") . "(`name` LIKE '%{$quickValue}%' OR `path` LIKE '%{$quickValue}%')";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " status = 'A' ";
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        switch($sortIndx){
            case "default":
                $sortIndx = "defaultmodule";
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
        $countModules = $moduleDAO->countModules($where); 
        if($countModules['status']){
            $total_Records = $countModules['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $modules = $moduleDAO->queryModules($where,$group,$order,$limit);
        
        if($modules['status']){     
            $modulesObj = $modules['push']['object']->getGridList();     
            
            foreach($modulesObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
                $default_fmt = ($v['defaultmodule'] == "YES" ) ? '<span class="label label-info">&check;</span>' : '';

                $data[] = array(
                    'idmodule'      => $v['idmodule'],
                    'name'          => $v['name'],//utf8_decode($v['holiday_description']),
                    'status'        => $status_fmt,
                    'status_val'    => $v['status'],
                    'default'       => $default_fmt,
                    'default_val'   => $v['defaultmodule'],
                    'module_path'   => $v['path'],
                    'lang_key'      => $v['smarty']  
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
    public function comboModulesFilters(): array
    {
        $aRet = array(
            array("id" => 'name',"text"=>$this->translator->translate('Name'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the module's add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        $params = $this->makeScreenModules('add');
        
        $this->view('admin','modules-create',$params);
    }

    /**
     * en_us Write the module information to the DB
     * pt_br Grava no BD as informações do módulo
     */
    public function createModule()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $moduleDao = new moduleDAO();
        $moduleMod = new moduleModel();

        $isDefault = (isset($_POST['moduleDefault'])) ? "YES" : "";

        //Setting up the model
        $moduleMod->setName(trim(strip_tags($_POST['moduleName'])))
                  ->setPath(trim(strip_tags($_POST['modulePath'])))
                  ->setTablePrefix(trim(strip_tags($_POST['modulePath'])))
                  ->setLanguageKeyName(trim(strip_tags($_POST['moduleKeyName'])))
                  ->setIsDefault($isDefault)
                  ->setRestrictionList((isset($_POST['moduleRestrictIp'])) ? $_POST["ipNumber"] : array());// if module has restrictions

        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // logo
            $moduleMod->setHeaderLogo($aAttachs[0]);
        }else{
            $moduleMod->setHeaderLogo("");
        }
        
        $ins = $moduleDao->saveModule($moduleMod);
        if($ins['status']){
            $st = true;
            $msg = "";
            $moduleID = $ins['push']['object']->getIdModule();
            $moduleName = $ins['push']['object']->getName();
            $modulePath = $ins['push']['object']->getPath();

            if(!$this->appSrc->_setFolder($this->appSrc->_getHelpdezkPath()."/app/modules/{$modulePath}/")){
                $this->logger->error("Could not create module dir", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }

            $this->logger->info("Module was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $moduleID = "";
            $moduleName = "";
            $modulePath = "";

            $this->logger->error("Could not save module. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "moduleId" => $moduleID,
            "moduleName" => $moduleName,
            "modulePath"  => $moduleName
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Renders the module's update screen
     *
     * pt_br Renderiza o template da tela de atualização do cadastro
     */
    public function formUpdate($idModule=null)
    {
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();
        $moduleModel->setIdModule($idModule);

        $moduleUpd = $moduleDAO->getModule($moduleModel);

        $params = $this->makeScreenModules('upd',$moduleUpd['push']['object']);
        $params['moduleId'] = $idModule;

        $retRestriction =  $moduleDAO->fetchModuleRestrictions($moduleUpd['push']['object']);
        if(!$retRestriction['status']){
            $params['hasRestriction'] = 0;
            $params['aRestriction'] = array();
            $params['aSize'] = 0;

        }else{
            $params['aRestriction'] = $retRestriction['push']['object']->getRestrictionList();
            $params['aSize'] = sizeof($params['aRestriction']);
            $params['hasRestriction'] = ($params['aSize'] > 0) ? 1 : 0;
        }
      
        $this->view('admin','modules-update',$params);
    }

    /**
     * en_us Update the city information to the DB
     *
     * pt_br Atualiza no BD as informações da cidade
     */
    public function updateModule()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $moduleDao = new moduleDAO();
        $moduleMod = new moduleModel();

        $isDefault = (isset($_POST['moduleDefault'])) ? "YES" : "";

        //Setting up the model
        $moduleMod->setIdModule(trim(strip_tags($_POST['moduleId'])))
                  ->setName(trim(strip_tags($_POST['moduleName'])))
                  ->setLanguageKeyName(trim(strip_tags($_POST['moduleKeyName'])))
                  ->setIsDefault($isDefault)
                  ->setRestrictionList((isset($_POST['moduleRestrictIp'])) ? $_POST["ipNumber"] : array());// if module has restrictions

        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // logo
            $moduleMod->setHeaderLogo($aAttachs[0]);
        }else{
            $moduleMod->setHeaderLogo("");
        }
        
        $upd = $moduleDao->saveUpdateModule($moduleMod);
        if($upd['status']){
            $st = true;
            $msg = "";
            $moduleId = $upd['push']['object']->getIdModule();

            $this->logger->info("Module was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $upd['push']['message'];
            $moduleId = "";

            $this->logger->error("Could not update module. Error: {$upd['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "moduleId" => $moduleId
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
        
        $moduleDAO = new moduleDAO();
        $moduleMod = new moduleModel();        

        //Setting up the model
        $moduleMod->setIdModule($_POST['moduleId'])
                  ->setIsDefault($_POST['isDefault'])
                  ->setStatus($_POST['newstatus']);
        
        $upd = $moduleDAO->changeModuleStatus($moduleMod);
        if(!$upd['status']){
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Remove the city from the DB
     *
     * pt_br Remove a cidade do BD
     */
    function deleteModule()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();        

        //Setting up the model
        $moduleModel->setIdModule($_POST['moduleId'])
                    ->setTablePrefix($_POST['path']);

        // Delete city registration
        $del = $moduleDAO->saveDeleteModule($moduleModel);
		if(!$del['status']){
            $st = false;
            $msg = $this->translator->translate("generic_error_msg");
            $this->logger->error("Could not remove module # {$_POST['moduleId']}. Error: {$del['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = true;
            $msg = "";
            $this->logger->info("Module # {$_POST['moduleId']} was removed successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Check if the module has already been registered before
     * pt_br Verifica se o módulo já foi cadastrado anteriormente
     */
    function checkModule(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $moduleDao = new moduleDAO();

        $name = strip_tags($_POST['moduleName']);

        $where = "WHERE name = '{$name}'";
        $where .= (isset($_POST['moduleId'])) ? " AND idmodule != {$_POST['moduleId']}" : "";

        $check =  $moduleDao->queryModules($where);
        if(!$check['status']){
            return false;
        }

        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('module_already_exists'));
        }else{
            echo json_encode(true);
        }

    }

    /**
     * en_us Check if the module path has already been registered before
     * pt_br Verifica se o caminho do módulo já foi cadastrado anteriormente
     */
    function checkModulePath(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $moduleDao = new moduleDAO();
        $aReserved = array('hdk','helpdezk','admin','adm','main');

        $path = strip_tags($_POST['modulePath']);

        $where = "WHERE `path` = '{$path}'";
        $where .= (isset($_POST['moduleId'])) ? " AND idmodule != {$_POST['moduleId']}" : "";

        if(in_array($path,$aReserved)){
            echo json_encode($this->translator->translate('module_path_exists'));
            exit;
        }

        $check =  $moduleDao->queryModules($where);
        if(!$check['status']){
            return false;
        }

        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('module_path_exists'));
        }else{
            echo json_encode(true);
        }

    }

    /**
     * en_us Uploads the file in the directory
     * pt_br Carrega o arquivo no diretório
     */
    function saveLogo()
    {
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");

            if($this->saveMode == 'disk') {
                $targetFile =  $this->imgDir.$fileName;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logger->info("Module's logo saved. {$targetFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logger->error("Error trying save module's logo: {$fileName}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }        
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = new awsServices();
                $arrayRet = $aws->_copyToBucket($tempFile,"{$this->imgDir}{$fileName}");
                
                if($arrayRet['success']) {
                    $this->logger->info("Save temp attachment file {$fileName}. {$this->imgDir}{$fileName}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    $this->logger->error("Could not save the temp file: {$fileName} in S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
            }

        }else{
            $this->logger->error("Error trying save module's logo. Error: {$_FILES['file']['error']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
        }

        exit;
    }

}