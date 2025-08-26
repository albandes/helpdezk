<?php

use App\core\Controller;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\main\dao\mysql\externalappDAO;
use App\modules\main\dao\mysql\usersettingsDAO;
use App\modules\main\dao\mysql\signatureDAO;

use App\modules\admin\models\mysql\loginModel;
use App\modules\admin\models\mysql\personModel;
use App\modules\main\models\mysql\externalappModel;
use App\modules\main\models\mysql\externalappfieldModel;
use App\modules\main\models\mysql\usersettingsModel;
use App\modules\main\models\mysql\signatureModel;

use App\modules\main\src\mainServices;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeWithLogoProvider;
use App\src\mfaServices;

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
                   ->setIdLocale($_POST['modal-cmblocale'])
                   ->setIdTheme($_POST['modal-cmbcolor-theme'])
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
    
    /**
     * en_us Makes a html string to reload search options dropdown list
     * pt_br Cria uma string html para recarregar o combo de ações de pesquisa
     *
     * @return string
     */
    public function reloadSearchOptions()
    {
        $searchOpts = trim(strip_tags($_POST['searchOpts']));
        $searchOpts = explode(",",$searchOpts);
        $optionDefault = (isset($_POST['optionDefault']) && !empty($_POST['optionDefault'])) ? $_POST['optionDefault'] : 'cn';

        $ret = $this->appSrc->_comboFilterOpts($searchOpts);
        
        $select = "";
        foreach ($ret as $k=>$v) {
            $selected = ($v['id'] == $optionDefault) ? "selected" : "";
            $select .= "<option value='{$v['id']}' {$selected}>{$v['text']}</option>";
        }

        echo $select;
    }    

	public function closeBrowser()
    {
        if(!empty($_SESSION['SES_CURRENT_PROGRAM_ACCESS'])){
            $permissionDAO = new permissionDAO();
            $permissionDTO = new permissionModel();
            $permissionDTO->setOldProgramAccessId($_SESSION['SES_CURRENT_PROGRAM_ACCESS']);

            $ret = $permissionDAO->closeProgramAccess($permissionDTO);
            if(!$ret['status']){
                $this->applogger->error("Can't update program's access detail. Program ID: {$_SESSION['SES_CURRENT_PROGRAM_ID']}. Program's access ID: {$permissionDTO->getOldProgramAccessId()}",['Class' => __CLASS__, 'Method' => __METHOD__, 'Error' => $ret['push']['message']]);
            }else{
                $this->applogger->info("Program's access detail was updated successfully. Program ID: {$_SESSION['SES_CURRENT_PROGRAM_ID']}. Program's access ID: {$permissionDTO->getOldProgramAccessId()}",['Class' => __CLASS__, 'Method' => __METHOD__]);
            }
        }
        echo "ok";
    }
    
    /**
     * getLoginType
     *
     * en_us Returns the user's login type ID
     * pt_br Retorna o ID do tipo de login do usuário
     *
     * @return void
     */
    public function getLoginType()
    {        
        $loginDAO = new loginDAO();
        $loginDTO = new loginModel();        
        $loginDTO->setIdPerson($_POST['userId']);
        
        $retLoginType = $loginDAO->getLoginTypeByUserId($loginDTO);
        
        if(!$retLoginType['status']){
            $this->logger->error("Can't get login type. User ID: {$_POST['userId']}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'Error' => $retLoginType['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
            $loginTypeId = "";
        }else{
            $this->logger->info("Login type got successfully. User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            
            $loginTypeId = $retLoginType['push']['object']->getLoginType();
            if($loginTypeId > 0){
                $st = true;
                $msg = "";                
            }else{
                $st = false;
                $msg = $this->translator->translate('generic_error_msg');
            }
        }

        echo json_encode(array('success'=>$st,'message'=>$msg,'loginTypeId'=>$loginTypeId));
    }
    
    /**
     * checkUserPass
     *
     * en_us Checks if the password is the same as the one registered in the DB
     * pt_br Verifica se a senha é igual à registrada no BD
     *
     * @return void
     */
    public function checkUserPass()
    {
        $personDAO = new personDAO();
        $personDTO = new personModel();
        
        $personDTO->setIdPerson($_POST['personId'])
                  ->setPassword(trim(strip_tags($_POST['modal-new-user-password'])));

        $check =  $personDAO->checkUserPass($personDTO);
        if(!$check['status']){
            $this->logger->error("Can't check user password. User ID: {$_POST['personId']}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'Error' => $check['push']['message']]);
            return false;
        }

        $this->logger->info("User password check successfully. User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);        
        if($check['push']['object']->getUserPasswordExist() > 0){
            echo json_encode($this->translator->translate('Alert_not_match_new_pass'));
        }else{
            echo json_encode(true);
        }
    }
    
    /**
     * changeUserPassword
     *
     * en_us Change the user's password in DB
     * pt_br Altera a senha do usuário no BD
     *
     * @return void
     */
    public function changeUserPassword()
    {
        $personDAO = new personDAO();
        $personDTO = new personModel();

        //Setting up the model
        $personDTO->setIdPerson($_POST['personId'])
                  ->setPassword(trim(strip_tags($_POST['newPassword'])))
                  ->setChangePasswordFlag(0);
        
        $upd = $personDAO->updatePassword($personDTO);
        if(!$upd['status']){
            $this->logger->error("Could not save the new password. User ID: {$_POST['personId']}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("The new password was saved successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
        }

        $aRet = array(
            "success" => $st,
            "message" => $msg
        );

        echo json_encode($aRet);
    }
    
    /**
     * saveAuthenticatorSecret
     *
     * en_us Saves the 2FA secret to the database.
     * pt_br Grava em BD o valor do secret para a autenticação em dois fatores.
     *
     * @return void
     */
    public function saveAuthenticatorSecret(){
        $secret = $_POST['secret'];
        $idperson = $_SESSION['SES_COD_USUARIO'];
        $mfaServices = new mfaServices();

        $check = $mfaServices->checkAuthCode($_POST['code'], $_POST['secret']);
        if(!$check['isValid']){
            $this->logger->error("Error saving authenticator", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            echo json_encode(['success' => false, 'message' => $this->translator->translate('invalid_authenticator_code')]);
            return;
        }

        $signatureDAO = new \App\modules\main\dao\mysql\signatureDAO();
        $aret = $signatureDAO->saveAuthenticatorSecret($idperson, $secret);
        if(!$aret['status']){
            $this->logger->error("Error saving authenticator secret", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            echo json_encode(['success' => false, 'message' => $aret['push']['message']]);
            return;
        }
        
        echo json_encode(['success' => true, 'message' => $this->translator->translate('secret_saved')]);
    }
    
    /**
     * isTwoFactorSetupRequired
     *
     * en_us Checks if the user needs to set up 2FA
     * pt_br Verifica se o usuário precisa configurar a 2FA.
     *
     * @return void
     */
    public function isTwoFactorSetupRequired()
    {        
        $signatureDAO = new signatureDAO();
        $signatureDTO = new signatureModel();        
        $signatureDTO->setIdPerson($_POST['userId']);
        
        $retSecret = $signatureDAO->getUser2FASecret($signatureDTO);
        
        if(!$retSecret['status']){
            $this->logger->error("Can't get user's 2FA secret. User ID: {$_POST['userId']}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'Error' => $retSecret['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
            $qrcode = "";
            $secret = "";
        }else{
            $this->logger->debug("User's 2FA secret got successfully. User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            $st = true;
            
            $secret = $retSecret['push']['object']->getUserSecret2FA();
            if(!is_null($secret)){
                $needsSetup = false;
                $msg = "{$this->translator->translate('no_need_2FA_setup')}";
                $qrcode = "";
                $secret = "";
            }else{
                $needsSetup = true;
                $msg = "";

                $qrcodeProvider = new EndroidQrCodeWithLogoProvider();
                $tfa = new TwoFactorAuth($_SESSION['SES_APP_NAME_INTEGRATIONS'],6,30,'sha512',$qrcodeProvider);
                $secret = $tfa->createSecret();
                $qrcode = $tfa->getQRCodeImageAsDataUri($_SESSION['SES_LOGIN_PERSON'],$secret);
            }
        }

        echo json_encode(array('success'=>$st,'message'=>$msg,'needsSetup'=>$needsSetup,'qrCode'=>$qrcode,'secret'=>$secret));
    }
}