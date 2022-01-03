<?php

use App\core\Controller;

use App\modules\main\dao\mysql\externalappDAO;
use App\modules\main\dao\mysql\usersettingsDAO;

use App\modules\main\models\mysql\externalappModel;
use App\modules\main\models\mysql\usersettingsModel;
use App\modules\main\models\mysql\externalappfieldModel;

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
     * @uses $_POST['modal-trello-key'] directly
     * @uses $_POST['modal-trello-token'] directly
     * @uses $_POST['modal-pushover-key'] directly
     * @uses $_POST['modal-pushover-token'] directly
     *
     * @since December 29, 2019
     *
     * @return array [
     *                  'success'       => true|false,
     *                  'message'       => Error or success message
     *                  'id'            => Record ID saved in database
     *                 ]
     */
    public function saveUserSettings()
    {
        
        $userSetDAO = new usersettingsDAO();
        $userSetMod = new usersettingsModel();
        $extAppMod = new externalappModel(); 
        
        $userSetMod->setUserID($_SESSION['SES_COD_USUARIO'])
                   ->setIdlocale($_POST['modal-cmblocale'])
                   ->setIdtheme($_POST['modal-cmbcolor-theme'])
                   ->setDisplayGrid(isset($_POST['modal-display-grid']) ? 'Y' : 'N');
        
        $retUserSet = $userSetDAO->getUserSettingsByUser($userSetMod);
        
        if(!$retUserSet['status']){
            return false;
        }else{
            if($retUserSet['push']['object']->getUserSettingID() != 0){
                $op = $userSetDAO->updateUserSettings($retUserSet['push']['object']);
            }else{
                $op = $userSetDAO->insertUserSettings($userSetMod);
            }

            if(!$op['status']){
                return false;
            }
        }
        
        if(isset($_POST['modal-trello-key']) || isset($_POST['modal-trello-token'])){
            
            $extAppMod->setUserID($_SESSION['SES_COD_USUARIO'])
                      ->setAppName("Trello")
                      ->setSettingsList(array(
                                                array( 'field' => 'key', 'value' => trim($_POST['modal-trello-key'])) , 
                                                array( 'field' => 'token','value' => trim($_POST['modal-trello-token']))
                                            ));
            
            $retTrello = $this->insertExternalSettings($extAppMod);

            if(!$retTrello['status']){
                return false;
            }
        }

        if(isset($_POST['modal-pushover-key']) || isset($_POST['modal-pushover-token'])){
            $extAppMod->setUserID($_SESSION['SES_COD_USUARIO'])
                      ->setAppName("Pushover")
                      ->setSettingsList(array(
                                                array( 'field' => 'key', 'value' => trim($_POST['modal-pushover-key'])) , 
                                                array( 'field' => 'token','value' => trim($_POST['modal-pushover-token']))
                                            ));
                                            
            $retTrello = $this->insertExternalSettings($extAppMod);

            if(!$retTrello['status']){
                return false;
            }
        }

        echo json_encode(array('success'=>true));

    }

    public function insertExternalSettings($externalappModel)
    {
        
        $extAppDAO = new externalappDAO();
        $extAppFieldMod = new externalappfieldModel();
        
        // Get external app ID
        $retExtApp = $extAppDAO->getExternalAppByName($externalappModel);
        if(!$retExtApp['status']){
            $st = false;
            $msg = $retExtApp['push']['message'];
        }elseif($retExtApp['push']['object']->getIdExternalApp() <= 0){
            $st = true;
            $msg = $this->translator->translate('no_external_app');
        }else{
            // Check if the user has external app settings
            $checkUserExtApp = $extAppDAO->getExtAppSettingByUser($retExtApp['push']['object']);
            if(!$checkUserExtApp['status']){
                $st = false;
                $msg = $checkUserExtApp['push']['message'];
            }else{ 
                if($checkUserExtApp['push']['object']->getIdExternalSetting() <= 0){
                    $insUserApp = $extAppDAO->insertUserExternalApp($retExtApp['push']['object']);
                    if(!$insUserApp['status']){
                        $externalSettingsID = 0;
                    }else{
                        $externalSettingsID = $insUserApp['push']['object']->getIdExternalSetting();
                    }
                }else{
                    $externalSettingsID = $checkUserExtApp['push']['object']->getIdExternalSetting();
                }                

                if($externalSettingsID > 0){
                    $extAppFieldMod->setIdExternalSetting($externalSettingsID);

                    foreach ($externalappModel->getSettingsList() as $row){
                        $extAppFieldMod->setFieldName($row['field'])
                                       ->setFieldValue($row['value']);

                        $checkField = $extAppDAO->getExtAppFieldByName($extAppFieldMod);
                        if(!$checkField['status']){
                            $st = false;
                            $msg = $checkField['push']['message'];
                        }else{
                            if($checkField['push']['object']->getIdExternalField() <= 0){
                                $insField = $extAppDAO->insertExternalAppField($extAppFieldMod);
                                if(!$insField['status']){
                                    $st = false;
                                    $msg = $insField['push']['message'];
                                }else{
                                    $st = true;
                                    $msg = "";
                                }
                            }else{
                                $extAppFieldMod;
                                $updField = $extAppDAO->updateExternalAppField($checkField['push']['object']);
                                if(!$updField['status']){
                                    $st = false;
                                    $msg = $updField['push']['message'];
                                }else{
                                    $st = true;
                                    $msg = "";
                                }
                            }
                        }
                    }
                }
            }

        }

        return array('status'=>$st,'message'=>$msg);

    }

}