<?php

use App\core\Controller;
use App\src\appServices;
use App\src\localeServices;

class Home extends Controller
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
		
		$appSrc = new appServices();
        session_start();
		$appSrc->_sessionValidate();
        
        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->imgBucket = "https://{$bucket}.s3.amazonaws.com/photos/";
            $this->imgDir = "photos/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']) {
                $this->imgDir = $appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/photos/');
                $this->imgBucket = $_ENV['EXTERNAL_STORAGE_URL'].'photos/';
            } else {
                $storageDir = $appSrc->_setFolder($appSrc->_getHelpdezkPath().'/storage/');
                $upDir = $appSrc->_setFolder($storageDir.'uploads/');
                $this->imgDir = $appSrc->_setFolder($upDir.'photos/');
                $this->imgBucket = $_ENV['HDK_URL']."/storage/uploads/photos/";
            }
        }
        
    }

	/**
	 *  en_us Calls the method that renders the module's home template
	 * 
	 *  pt_br Chama o método que renderiza o template da home do módulo
	 */
	public function index()
	{
		$params = $this->makeScreenMainHome();
		
		$this->view('main','main',$params);
		
	}
	
	/**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
	public function makeScreenMainHome()
    {
        $appSrc = new appServices();
		$params = $appSrc->_getDefaultParams();
		
        return $params;
    }

	public function logout()
    {
        $appSrc = new appServices();
		$appSrc->_sessionDestroy();
        header('Location:' . $_ENV['HDK_URL'] . '/admin/login');
    }

	public function lockscreen()
    {
		$appSrc = new appServices();
        $params = $this->makeScreenMainHome();
        
        if($this->saveMode == 'disk') {
            $imgFormat = $appSrc->_getImageFileFormat($this->imgDir.$_SESSION['SES_COD_USUARIO']);
        }elseif($this->saveMode == "aws-s3"){
            $imgFormat = $appSrc->_getImageFileFormat($this->imgBucket.$_SESSION['SES_COD_USUARIO']);
        }
        echo $imgFormat;
        if ($imgFormat) {
            $imgPhoto = $_SESSION['SES_COD_USUARIO'].'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $params['person_login'] = $_SESSION['SES_LOGIN_PERSON'];
        $params['login'] = $_ENV['HDK_URL'] . '/admin/login';
        $params['person_photo'] = $this->imgBucket . $imgPhoto;

		$appSrc->_sessionDestroy();
		$this->view('main','lockscreen',$params);

    }

    public function translateLabel()
    {
        $translator = new localeServices();
        $label = trim($_POST['label']);

        echo json_encode($translator->translate($label));
    }

}