<?php

use App\core\Controller;

//DAO
use App\modules\admin\dao\mysql\moduleDAO;

//Models
use App\modules\admin\models\mysql\moduleModel;

//services
use App\modules\admin\src\adminServices;
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

        if($option=='upd'){
            $params['cityID'] = $obj->getIdCity();
            $params['stateID'] = $obj->getIdState();
            $params['cityName'] = $obj->getName();
            $params['foundationDate'] = $this->appSrc->_formatDate($obj->getDtFoundation());
            $params['isDefault'] = $obj->getIsDefault();
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

            switch($filterIndx){
                case "city_uf": //Search for the acronym or the full name of the state
                    $where .= "AND (" . $this->appSrc->_formatGridOperation($filterOp,"b.name",$filterValue) ." OR ". $this->appSrc->_formatGridOperation($filterOp,"b.abbr",$filterValue) . ")" ;
                    break;
                case "dtfoundation": //Search for city's foundation date
                    $filterValue = $this->appSrc->_formatSaveDate($filterValue);
                    $where .= "AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
                    break;
                case "city_name": //Search for city's name
                    $filterIndx = "a.name";
                    $where .= "AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
                    break;
            }            
            
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " AND (a.name LIKE '%{$quickValue}%' OR dtfoundation LIKE '".$this->appSrc->_formatSaveDate($quickValue)."')";
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
                    'default_val'   => $v['defaultmodule']
    
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
        $params = $this->makeScreenModules();
        
        $this->view('admin','modules-create',$params);
    }

    /**
     * en_us Renders the module's update screen
     *
     * pt_br Renderiza o template da tela de atualização do cadastro
     */
    public function formUpdate($idCity=null)
    {
        $moduleDAO = new moduleDAO();
        $cityMod = new cityModel();
        $cityMod->setIdCity($idCity);

        $cityUpd = $cityDao->getCity($cityMod);

        $params = $this->makeScreenCity('upd',$cityUpd['push']['object']);
        $params['cityID'] = $idCity;
      
        $this->view('exp','city-update',$params);
    }

    /**
     * en_us Write the city information to the DB
     *
     * pt_br Grava no BD as informações da cidade
     */
    public function createCity()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $cityDao = new cityDAO();
        $cityMod = new cityModel();        

        //Setting up the model
        $cityMod->setIdState($_POST['cmbUF'])
                ->setName(trim($_POST['cityName']))
                ->setDtFoundation($this->appSrc->_formatSaveDate($_POST['foundationDate']))
                ->setIsDefault($_POST['cityDefault']);        

        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files
            
            $cityMod->setAttachments($aAttachs);
        }
                
        
        $ins = $cityDao->insertCity($cityMod);
        if($ins['status']){
            $st = true;
            $msg = "";
            $cityID = $ins['push']['object']->getIdCity();
            $cityDescription = $ins['push']['object']->getName();
            $cityFoundation = $this->appSrc->_formatDate($ins['push']['object']->getDtFoundation());
            
            // link attachments to the city
            if($aSize > 0){
                $insAttachs = $this->linkCityAttachments($ins['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                    $cityID = "";
                    $cityDescription = "";
                    $cityFoundation = "";
                }
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $cityID = "";
            $cityDescription = "";
            $cityFoundation = "";
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "idcity" => $cityID,
            "description" => $cityDescription,
            "dtfoundation"  => $cityFoundation
        );       

        echo json_encode($aRet);
    }

    /**
     * en_us Update the city information to the DB
     *
     * pt_br Atualiza no BD as informações da cidade
     */
    public function updateCity()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $cityDao = new cityDAO();
        $cityMod = new cityModel();        

        //Setting up the model
        $cityMod->setIdCity($_POST['cityID'])
                ->setIdState($_POST['cmbUF'])
                ->setName(trim($_POST['cityName']))
                ->setDtFoundation($this->appSrc->_formatSaveDate($_POST['foundationDate']))
                ->setIsDefault($_POST['cityDefault']);        

        if(isset($_POST["attachments"])){
            $aAttachs = $_POST["attachments"]; // Attachments
            $aSize = count($aAttachs); // count attachs files
            
            $cityMod->setAttachments($aAttachs);
        }
        
        $upd = $cityDao->updateCity($cityMod);
        if($upd['status']){
            $st = true;
            $msg = "";
            $cityID = $upd['push']['object']->getIdCity();
            
            // link attachments to the city
            if($aSize > 0){
                $insAttachs = $this->linkCityAttachments($upd['push']['object']);
                
                if(!$insAttachs['success']){
                    $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    $st = false;
                    $msg = $insAttachs['message'];
                    $cityID = "";
                }
            }
        }else{
            $st = false;
            $msg = $upd['push']['message'];
            $cityID = "";
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "idcity" => $cityID
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

        $cityDao = new cityDAO();
        $cityMod = new cityModel();        

        //Setting up the model
        $cityMod->setIdCity($_POST['cityID'])
                ->setStatus($_POST['newstatus']);
        
        $upd = $cityDao->updateStatus($cityMod);
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
    function deleteCity()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $cityDao = new cityDAO();
        $cityMod = new cityModel();        

        //Setting up the model
        $cityMod->setIdCity($_POST['cityID']);

        // First delete image linked with city
        $delImage = $cityDao->deleteCityImageByCity($cityMod);
        if(!$delImage['status']){
            return false;
        }

        // Delete city registration
        $del = $cityDao->deleteCity($cityMod);
		if(!$del['status']){
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Check if the city has already been registered before
     *
     * pt_br Verifica se a cidade já foi cadastrada anteriormente
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $cityDao = new cityDAO();

        $stateID = $_POST['uf'];
        $name = strip_tags($_POST['cityName']);

        $where = "AND a.name = '$name' AND a.idstate = $stateID";
        $where .= (isset($_POST['cityID'])) ? " AND idcity != {$_POST['cityID']}" : "";

        $check =  $cityDao->queryCities($where);
        if(!$check['status']){
            return false;
        }

        $checkObj = $check['push']['object']->getGridList();
        
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('city_already_registered'));
        }else{
            echo json_encode(true);
        }

    }

    /**
     * en_us Uploads the file in the directory
     *
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
                    $this->logger->info("Module's logo saved", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logger->error("Error trying save module's logo: {$fileName}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
                    
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = new awsServices();
                
                $arrayRet = $aws->_copyToBucket($tempFile,$this->imgDir.$fileName);
                
                if($arrayRet['success']) {
                    $this->logger->info("Save temp attachment file {$fileName}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    $this->logger->error("I could not save the temp file: {$fileName} in S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }             

            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
        }

        exit;
    }

    /**
     * en_us Loads the file linked with the city in the dropzone of the update screen
     *
     * pt_br Carrega o arquivo vinculado à cidade no dropzone da tela de atualização
     */
    function loadImage()
    {
        $cityDao = new cityDAO();
        $cityMod = new cityModel();
        $cityMod->setIdCity($_POST['cityID']);

        $imgList = $cityDao->fetchCityImage($cityMod);
        
        if(!$imgList['status']) {
            return false;
        }
        
        $imgListObj = $imgList['push']['object']->getAttachments();
        $aImage = [];
        
        if(!empty($imgListObj)){
            foreach ($imgListObj as $key => $value){
                if($this->saveMode == "aws-s3"){
                    $size = strlen(file_get_contents($this->imgBucket.$value['fileuploaded']));
                }else{
                    $size = filesize($this->imgDir.$value['fileuploaded']);
                }            
                
                $aImage[] = array(
                    'idimage'   => $value['idimage'],
                    'idcity'    => $value['idcity'], 
                    'filename'      => $value['filename'],
                    'fmtname'   => $value['fileuploaded'],
                    'size'      => $size,
                    'url'       => $this->imgBucket
                );
            }
        }
        
        echo json_encode($aImage);
    }

    /**
     * en_us Removes the file linked with the city
     *
     * pt_br Deleta o arquivo vinculado à cidade
     */
    function removeImage()
    {
        $cityDao = new cityDAO();
        $cityMod = new cityModel();        

        //Setting up the model
        $cityMod->setIdImage($_POST['idimage']);
        $filename = $_POST['filename'];

        $del = $cityDao->deleteCityImage($cityMod);
        if(!$del['status']){
            return false;
        }

        if($this->saveMode == 'disk') {
            unlink($this->imgDir.$filename);
            $msg = true;
        }elseif($this->saveMode == 'aws-s3'){           
            $aws = new awsServices();
            $arrayRet = $aws->_removeFile("{$this->imgDir}{$filename}");
            if($arrayRet['success']) {
                $msg = true;
            } else {
                $this->logger->error("I could not remove the image file: {$filename} from S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                $msg = false;
            }
        }

        $aRet = array(
            "success" => $msg,
        );

        echo json_encode($aRet);
    }
    
    /**
     * Link City to uploaded files
     *
     * @param  cityModel $cityModel
     * @return array
     */
    public function linkCityAttachments(cityModel $cityModel): array
    {
        $cityDao = new cityDAO();
        $aAttachs = $cityModel->getAttachments();
        
        foreach($aAttachs as $key=>$fileName){
            $cityModel->setFileName($fileName);

            $ins = $cityDao->insertCityImage($cityModel);

            if(!$ins['status']) {
                return array("success"=>false,"message"=>"Can't link file {$fileName} to city # {$cityModel->getIdCity()}");
            }

            $extension = strrchr($fileName, ".");
            $imageID = $ins['push']['object']->getIdImage();
            $newFile = $imageID.$extension;
            $ins['push']['object']->setNewFileName($newFile);

            if($this->saveMode == 'disk'){
                $targetOld = $this->imgDir.$fileName;
                $targetNew =  $this->imgDir.$newFile;
                if(!rename($targetOld,$targetNew)){
                    $del = $cityDao->deleteCityImage($ins['push']['object']);
                    if(!$del['status']) {
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to city # {$cityID}");
                    }
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to city #{$productID}");
                }
                
            }elseif($this->saveMode == 'aws-s3'){
                $aws = new awsServices();
                $arrayRet = $aws->_renameFile("{$this->imgDir}{$fileName}","{$this->imgDir}{$newFile}");
                
                if($arrayRet['success']) {
                    $this->logger->info("Rename city image file {$fileName} to {$newFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                } else {
                    $del = $cityDao->deleteCityImage($ins['push']['object']);
                    if(!$del['status']) {
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to city # {$cityID}");
                    }

                    $this->logger->error("I could not rename city image file {$fileName} to {$newFile} in S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
            
            }

            $upd = $cityDao->updateCityImageName($ins['push']['object']);
            if(!$upd['status']){
                return array("success"=>false,"message"=>"Can't update link file {$fileName} to city {$cityID}");
            }

        }

        return array("success"=>true,"message"=>"");

    }


}