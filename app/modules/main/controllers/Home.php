<?php

use App\core\Controller;

use App\modules\main\dao\mysql\externalappDAO;

use App\modules\main\models\mysql\externalappModel;

use App\modules\main\src\mainServices;

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
		
		session_start();
		$this->appSrc->_sessionValidate();
        
        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->imgBucket = "https://{$bucket}.s3.amazonaws.com/photos/";
            $this->imgDir = "photos/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']) {
                $this->imgDir = $this->appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/photos/');
                $this->imgBucket = $_ENV['EXTERNAL_STORAGE_URL'].'photos/';
            } else {
                $storageDir = $this->appSrc->_setFolder($this->appSrc->_getHelpdezkPath().'/storage/');
                $upDir = $this->appSrc->_setFolder($storageDir.'uploads/');
                $this->imgDir = $this->appSrc->_setFolder($upDir.'photos/');
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
        $params = $this->appSrc->_getDefaultParams();
		
        return $params;
    }

	public function logout()
    {
        $this->appSrc->_sessionDestroy();
        header('Location:' . $_ENV['HDK_URL'] . '/admin/login');
    }

	public function lockscreen()
    {
		$params = $this->makeScreenMainHome();
        
        if($this->saveMode == 'disk') {
            $imgFormat = $this->appSrc->_getImageFileFormat($this->imgDir.$_SESSION['SES_COD_USUARIO']);
        }elseif($this->saveMode == "aws-s3"){
            $imgFormat = $this->appSrc->_getImageFileFormat($this->imgBucket.$_SESSION['SES_COD_USUARIO']);
        }
        
        if ($imgFormat) {
            $imgPhoto = $_SESSION['SES_COD_USUARIO'].'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $params['person_login'] = $_SESSION['SES_LOGIN_PERSON'];
        $params['login'] = $_ENV['HDK_URL'] . '/admin/login';
        $params['person_photo'] = $this->imgBucket . $imgPhoto;

		$this->appSrc->_sessionDestroy();
		$this->view('main','lockscreen',$params);

    }

    public function translateLabel()
    {
        $label = trim($_POST['label']);

        echo json_encode($this->translator->translate($label));
    }

    /**
     * en_us Returns on screen the list of color themes
     *
     * pt_br Retorna em tela a lista de temas de cores
     */
    public function ajaxComboThemes()
    {
        $mainSrc = new mainServices();
        $retCmbTheme = $mainSrc->_comboTheme();

        $select = '';
        
        if(count($retCmbTheme) <= 0){
            $select .= "<option value='X'> - {$this->translator->translate('no_themes_registered')} - </option>";
        }else{
            $select .= "<option></option>";
            foreach ($retCmbTheme as $key=>$value) {
                $select .= "<option value='{$value['id']}'>{$value['text']}</option>";
            }
        }
        
        echo $select;
    }

    /**
     * en_us Returns on screen the list of color themes
     *
     * pt_br Retorna em tela a lista de temas de cores
     */
    public function ajaxComboLocales()
    {
        $mainSrc = new mainServices();
        $retCmbLocale = $mainSrc->_comboLocale();

        $select = '';
        
        if(count($retCmbLocale) <= 0){
            $select .= "<option value='X'> - {$this->translator->translate('no_locales_registered')} - </option>";
        }else{
            $select .= "<option></option>";
            foreach ($retCmbLocale as $key=>$value) {
                $select .= "<option value='{$value['id']}'>{$value['text']}</option>";
            }
        }
        
        echo $select;
    }

    /**
     * Save user's settings (Theme, language, external APIs configurations,etc)
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @uses $_POST['idperson'] directly
     * @uses $_POST['trellokey'] directly
     * @uses $_POST['trellotoken'] directly
     * @uses $_POST['pushoverkey'] directly
     * @uses $_POST['pushovertoken'] directly
     *
     * @since December 29, 2019
     *
     * * @return array [
     *                  'success'       => true|false,
     *                  'message'       => Error or success message
     *                  'id'            => Record ID saved in database
     *                 ]
     */
    public function saveUserSettings()
    {
        //use this on display user's settings modal
        /*if(!$this->dbUserConfig->existApiConfigTables()) {
            echo json_encode(array('sucess' => false,'message' => 'There are no external APIs Configuration Tables !','id' => ''));
            exit;
        }*/
        $extAppDAO = new externalappDAO();
        $extAppMod = new externalappModel();
        $extAppMod->setAppName("Trello");
        $retExtApp = $extAppDAO->getExternalAppByName($extAppMod);
        echo "<pre>", print_r($retExtApp,true), "</pre>";
        /*$idPerson      = $_POST['idperson'];
        $trelloKey     = $_POST['trellokey'];
        $trelloToken   = $_POST['trellotoken'];
        $pushoverKey   = $_POST['pushoverkey'];
        $pushoverToken = $_POST['pushovertoken'];

        $this->dbUserConfig->BeginTrans();

        // Trello
        $arrayParam = array( array( 'field' => 'key', 'value' => $trelloKey) , array( 'field' => 'token','value' => $trelloToken) );
        $arrayReturn = $this->dbUserConfig->insertExternalSettings(50,$idPerson);

        if (!$arrayReturn['success']) {
            echo json_encode($arrayReturn);
            //$this->dbUserConfig->RoolbackTrans();
            exit;
        } else {
            $idexternalsettings = $arrayReturn['id'] ;
            foreach ($arrayParam as $row) {
                $arrayReturn = $this->dbUserConfig->insertExternalField($idexternalsettings,$row['field'],$row['value']);
                if (!$arrayReturn['success']) {
                    echo json_encode($arrayReturn);
                    //$this->dbUserConfig->RollbackTrans();
                    exit;
                }
            }

        }

        $arrayParam = array();

        // Pushover
        $arrayParam = array( array( 'field' => 'key','value' => $pushoverKey) , array( 'field' => 'token', 'value' => $pushoverToken) );
        $arrayReturn = $this->dbUserConfig->insertExternalSettings(51,$idPerson);

        if (!$arrayReturn['success']) {
            echo json_encode($arrayReturn);
            //$this->dbUserConfig->RoolbackTrans();
            exit;
        } else {
            $idexternalsettings = $arrayReturn['id'] ;
            foreach ($arrayParam as $row) {
                $arrayReturn = $this->dbUserConfig->insertExternalField($idexternalsettings,$row['field'],$row['value']);
                if (!$arrayReturn['success']) {
                    echo json_encode($arrayReturn);
                    //$this->dbUserConfig->RoolbackTrans();
                    exit;
                }
            }

        }
        //
        $this->dbUserConfig->CommitTrans();

        echo json_encode($arrayReturn);*/

    }

}