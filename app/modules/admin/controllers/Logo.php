<?php

use App\core\Controller;

//DAO
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\logoDAO;

//Models
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\logoModel;

//services
use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\src\awsServices;


class Logo extends Controller
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
     * en_us Renders the holidays home screen template
     *
     * pt_br Renderiza o template da tela de home de feriados
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenLogo();
		
		$this->view('admin','logo',$params);
    }

    /**
     * en_us Configure program screens
	 * pt_br Configura as telas do programa
     *
     * @param  string $option Indicates the type of screen (idx = index, add = new, upd = update)
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenLogo($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $mainSrc = new mainServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';

        $headerLogo = $this->appSrc->_getSystemLogo('header');
        $params['headerLogo'] = $headerLogo['image'];
        $params['headerHeight'] = $headerLogo['height'];
        $params['headerWidth'] = $headerLogo['width'];

        $loginLogo = $this->appSrc->_getSystemLogo('login');
        $params['loginLogo'] = $loginLogo['image'];
        $params['loginHeight'] = $loginLogo['height'];
        $params['loginWidth'] = $loginLogo['width'];

        $reportLogo = $this->appSrc->_getSystemLogo('reports');
        $params['reportLogo'] = $reportLogo['image'];
        $params['reportHeight'] = $reportLogo['height'];
        $params['reportWidth'] = $reportLogo['width'];
        
        return $params;
    }
        
    /**
     * saveLogo
     * 
     * en_us Uploads the file into directory
	 * pt_br Carrega o arquivo no diretório
     *
     * @return void
     */
    public function saveLogo()
    {
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $aFileData = getimagesize($tempFile);
            $aFileName = explode($extension,$fileName);
            
            //rename file
            $uploadFile = $this->appSrc->_clearAccent(trim($_POST['type']));
            $uploadFile = $uploadFile.$extension;
            
            //checks if resize image dimentions
            switch($_POST['type']){
                case 'header':
                    $width = ($aFileData[1] > 35) ?  round((35 * $aFileData[0]) / $aFileData[1]) : 114;
                    $height = 35;
                    break;
                case 'login':
                    $width = ($aFileData[1] > 70) ? round((70 * $aFileData[0]) / $aFileData[1]) : 227;
                    $height = 70;
                    break;
                case 'reports':
                    $width = ($aFileData[1] > 40) ? round((40 * $aFileData[0]) / $aFileData[1]) : 130;
                    $height = 40;
                    break;
            }
            
            if($this->saveMode == 'disk') {
                $targetFile =  $this->imgDir.$uploadFile;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logger->info("{$POST['type']}'s logo saved. {$targetFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    
                    $this->saveLogoData($_POST['type'],$uploadFile,$width,$height);//save into DB

                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logger->error("Error trying save {$POST['type']}'s logo: {$uploadFile}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }        
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = new awsServices();
                $arrayRet = $aws->_copyToBucket($tempFile,"{$this->imgDir}{$uploadFile}");
                
                if($arrayRet['success']) {
                    $this->logger->info("Save temp attachment file {$uploadFile}. {$this->imgDir}{$uploadFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    $this->saveLogoData($_POST['type'],$uploadFile,$width,$height);//save into DB

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    $this->logger->error("Could not save the temp file: {$uploadFile} in S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
                }
            }

        }else{
            $this->logger->error("Error trying save {$POST['type']}'s logo. Error: {$_FILES['file']['error']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            echo json_encode(array("success"=>false,"message"=>"{$this->translator->translate('Alert_failure')}"));
        }

        exit;
    }
    
    /**
     * saveLogoData
     * 
     * en_us Saves file's data into DB
	 * pt_br Grava os dados do arquivo no BD
     *
     * @param  mixed $type
     * @param  mixed $fileName
     * @return bool
     */
    public function saveLogoData($type,$fileName,$width,$height): bool
    {
        $logoDAO = new logoDAO();
        $logoDTO = new logoModel();
        
        $logoDTO->setFileName($fileName)
                ->setName($type)
                ->setWidth($width)
                ->setHeight($height);

        $ret = $logoDAO->updateLogo($logoDTO);
        if(!$ret['status']){
            $this->logger->error("Could not save the file {$fileName} data in DB", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            return false;
        }else{
            $this->logger->info("File {$fileName} data was saved successfully in DB.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }
    }
    
    /**
     * getLogo
     * 
     * en_us Returns new logo's element
	 * pt_br Retorna o elemento do novo logotipo
     *
     * @return bool
     */
    public function getLogo(): bool
    {
        $logoData = $this->appSrc->_getSystemLogo($_POST['type']);
        
        echo "<img src='{$logoData['image']}' height='{$logoData['height']}px' width='{$logoData['width']}px'/>";
        exit;
    }
}