<?php

use App\core\Controller;

//DAO
use App\modules\admin\dao\mysql\vocabularyDAO;

//Models
use App\modules\admin\models\mysql\vocabularyModel;

//services
use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\src\awsServices;


class Vocabulary extends Controller
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
     * en_us Renders the vocabulary home screen template
     * pt_br Renderiza o template da tela de home do vocabulario
     * 
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();
        
        $params = $this->makeScreenVocabulary();
		
		$this->view('admin','vocabulary',$params);
    }

    /**
     * en_us Configure program screens
	 * pt_br Configura as telas do programa
     *
     * @param  string $option Indicates the type of screen (idx = index, add = new, upd = update)
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenVocabulary($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $mainSrc = new mainServices();
        $vocabularyDAO = new vocabularyDAO();
        $vocabularyModel = new vocabularyModel();

        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;
        
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
            $params['moduleSelected'] = $obj->getIdModule();
            $params['keyName'] = $obj->getKeyName();
            
            $ret = $vocabularyDAO->fetchVocabularyByName($obj);
            if(!$ret['status']){
                $this->logger->error("Could not get vocabulary.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
                $params['aVocabularies'] = array();
            }else{
                $this->logger->info("Vocabularies got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $params['aVocabularies'] = $ret['push']['object']->getGridList();
                $params['vocabulariesId'] = implode(',',array_column($ret['push']['object']->getGridList(),'idvocabulary'));
            }
        }
        
        return $params;
    }
    
    /**
     * en_us Returns vocabularies data to grid
	 * pt_br Retorna os dados dos vocabulários para o grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $vocabularyDAO = new vocabularyDAO(); 

        $where = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch($filterIndx){
                case "locale":
                    $filterIndx = "b.name";
                    break;
                case "module":
                    $filterIndx = "c.name";
                    break;
                default:
                    $filterIndx = $filterIndx;
                    break;
            }

            $where .= " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);          
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " AND " . "(pipeLatinToUtf8(c.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(b.name) LIKE pipeLatinToUtf8('%{$quickValue}%') 
                                  OR pipeLatinToUtf8(key_name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(key_value) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= " AND " . "a.status = 'A' ";
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "keyName";
        
        switch($sortIndx){
            case "localeName":
                $sortIndx = "locale_name";
                break;
            case "moduleName":
                $sortIndx = "module_name";
                break;
            case "keyName":
                $sortIndx = "key_name";
                break;
            case "keyValue":
                $sortIndx = "key_value";
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
        $countVocabularies = $vocabularyDAO->countVocabularies($where); 
        if($countVocabularies['status']){
            $total_Records = $countVocabularies['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $vocabularies = $vocabularyDAO->queryVocabularies($where,$group,$order,$limit);
        
        if($vocabularies['status']){     
            $vocabulariesObj = $vocabularies['push']['object']->getGridList();     
            
            foreach($vocabulariesObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                $data[] = array(
                    'idvocabulary'  => $v['idvocabulary'],
                    'localeName'    => $v['locale_name'],
                    'moduleName'    => $v['module_name'],
                    'keyName'       => $v['key_name'],
                    'keyValue'      => $v['key_value'],
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
     * en_us Returns an array with ID and name of filters
     * pt_br Retorna um array com ID e nome dos filtros
     *
     * @return array
     */
    public function comboModulesFilters(): array
    {
        $aRet = array(
            array("id" => 'locale',"text"=>$this->translator->translate('vocabulary_locale')),
            array("id" => 'module',"text"=>$this->translator->translate('Module')),
            array("id" => 'key_name',"text"=>$this->translator->translate('vocabulary_key_name')),
            array("id" => 'key_value',"text"=>$this->translator->translate('vocabulary_key_value'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the vocabulary's add screen
     * pt_br Renderiza o template da tela de novo cadastro
     * 
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();
        
        $params = $this->makeScreenVocabulary('add');
        
        $this->view('admin','vocabulary-create',$params);
    }

    /**
     * en_us Write the vocabulary information to the DB
     * pt_br Grava no BD as informações do vocabulario
     * 
     */
    public function createVocabulary()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $vocabularyDao = new vocabularyDAO();
        $vocabularyDTO = new vocabularyModel();

        //Setting up the model
        $vocabularyDTO->setIdModule((isset($_POST['cmbModule'])) ? $_POST['cmbModule'] : $_POST['modal-cmb-module'])
                      ->setKeyName(trim(strip_tags($_POST['keyName'])))
                      ->setLocaleList($_POST['localeID'])
                      ->setKeyValueList($_POST['keyValue']);
        
        $ins = $vocabularyDao->saveVocabulary($vocabularyDTO);
        if($ins['status']){
            $st = true;
            $msg = "";
            $vocabularyID = $ins['push']['object']->getIdVocabulary();
            $keyName = $ins['push']['object']->getKeyName();

            $this->logger->info("Vocabulary was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $vocabularyID = "";
            $keyName = "";

            $this->logger->error("Could not save vocabulary.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "vocabularyId" => $vocabularyID,
            "vocabularyName" => $keyName
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Renders register's update screen
     * pt_br Renderiza o template da tela de atualização do cadastro
     * 
     */
    public function formUpdate($vocabularyId=null)
    {
        // blocks if the user does not have permission to edit
        if($this->aPermissions[3] != "Y")
            $this->appSrc->_accessDenied();
        
        $vocabularyDAO = new vocabularyDAO();
        $vocabularyModel = new vocabularyModel();
        $vocabularyModel->setIdVocabulary($vocabularyId);

        $ret = $vocabularyDAO->getVocabularyById($vocabularyModel);
        
        $params = $this->makeScreenVocabulary('upd',$ret['push']['object']);
        $params['vocabularyId'] = $vocabularyId;

        $this->view('admin','vocabulary-update',$params);
    }

    /**
     * en_us Updates vocabulary's information in DB
     * pt_br Atualiza no BD as informações do vocabulário
     * 
     */
    public function updateVocabulary()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $vocabularyDao = new vocabularyDAO();
        $vocabularyDTO = new vocabularyModel();

        //Setting up the model
        $vocabularyDTO->setIdModule($_POST['cmbModule'])
                      ->setKeyName(trim(strip_tags($_POST['keyName'])))
                      ->setKeyValueList($_POST['keyValue'])
                      ->setLocaleList($_POST['localeID'])
                      ->setVocabularyIdList($_POST['vocabularyID']);
        
        $ins = $vocabularyDao->saveUpdateVocabulary($vocabularyDTO);
        if($ins['status']){
            $st = true;
            $msg = "";
            $vocabularyID = $ins['push']['object']->getIdVocabulary();

            $this->logger->info("Vocabulary was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $vocabularyID = "";

            $this->logger->error("Could not save vocabulary.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);
        }      
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "vocabularyId" => $vocabularyId
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Changes vocabulary's status
     * pt_br Muda o status do vaocabulário
     * 
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $vocabularyDAO = new vocabularyDAO();
        $vocabularyDTO = new vocabularyModel();        

        //Setting up the model
        $vocabularyDTO->setIdVocabulary($_POST['vocabularyId'])
                      ->setStatus($_POST['newStatus']);
        
        $upd = $vocabularyDAO->changeStatus($vocabularyDTO);
        if(!$upd['status']){
            $this->logger->error("Could not save vocabulary.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Check if the vocabulary key name has already been registered before
     * pt_br Verifica se o key name do vocabulário já foi cadastrado anteriormente
     * 
     */
    function checkKeyName(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $vocabularyDAO = new vocabularyDAO();

        $name = strip_tags($_POST['keyName']);
        $moduleId = $_POST['moduleId'];

        if(empty($moduleId) || is_null($moduleId)){
            echo json_encode($this->translator->translate('selecione'));
            exit;
        }

        $where = "AND key_name = '{$name}' AND a.idmodule = {$moduleId}";
        $where .= (isset($_POST['vocabulariesId'])) ? " AND idvocabulary NOT IN ({$_POST['vocabulariesId']})" : "";

        $check =  $vocabularyDAO->queryVocabularies($where);
        if(!$check['status']){
            return false;
        }

        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('vocabulary_key_exists'));
        }else{
            echo json_encode(true);
        }

    }
}