<?php

use App\core\Controller;

//DAO
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\moduleDAO;

//Models
use App\modules\admin\models\mysql\featureModel;
use App\modules\admin\models\mysql\moduleModel;

//services
use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\src\awsServices;


class Features extends Controller
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
     * en_us Renders the holidays home screen template
     *
     * pt_br Renderiza o template da tela de home de feriados
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenFeature();
		
		$this->view('admin','feature',$params);
    }

    /**
     * en_us Configure program screens
	 * pt_br Configura as telas do programa
     *
     * @param  string $option Indicates the type of screen (idx = index, add = new, upd = update)
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenFeature($option='idx',$obj=null)
    {
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
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

        // -- Module's combo --
        $params['cmbModules'] = $adminSrc->_comboModules();
        $params['moduleSelected'] = 1;

        // -- POP's combo --
        $params['cmbPopTypes'] = $adminSrc->_comboPopServer();

        // -- LDAP's combo --
        $params['cmbLdapTypes'] = $adminSrc->_comboLdapServer();

        // -- Default country combo --
        $params['cmbDefCountry'] = $adminSrc->_comboCountries();

        // -- Default country combo --
        $params['cmbFeatureTypes'] = $adminSrc->_comboFieldType();

        if($option == 'idx'){
            //Email settings
            $featureDTO->setSettingCatId(5);
            $retEmail = $featureDAO->fetchConfigsByCategory($featureDTO);
            if(!$retEmail['status']){
                $this->logger->error("Can't get email settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error'=>$retEmail['push']['message']]);
            }else{
                $this->logger->info("Email settings got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                $emSettings = $retEmail['push']['object']->getSettingsList();
                $params['emTitle'] = $emSettings['EM_TITLE'];
                $params['emHost'] = $emSettings['EM_HOSTNAME'];
                $params['emDomain'] = $emSettings['EM_DOMAIN'];
                $params['emUser'] = $emSettings['EM_USER'];
                $params['emPassword'] = $emSettings['EM_PASSWORD'];
                $params['emSender'] = $emSettings['EM_SENDER'];
                $params['emPort'] = $emSettings['EM_PORT'];
                $params['emAuth'] = ($emSettings['EM_AUTH'] == 1) ? 'checked' : '';
                $params['emSuccessLog'] = ($emSettings['EM_SUCCESS_LOG'] == 1) ? 'checked' : '';
                $params['emFailLog'] = ($emSettings['EM_FAILURE_LOG'] == 1) ? 'checked' : '';
                $params['emailTracker'] = ($emSettings['TRACKER_STATUS'] == 1) ? 'checked' : '';
                $params['emByCon'] = ($emSettings['EM_BY_CRON'] == 1) ? 'checked' : '';
            }

            $retEmailTpl = $featureDAO->fetchEmailTempSettings($featureDTO);
            if(!$retEmailTpl['status']){
                $this->logger->error("Can't get email header and footer settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error'=>$retEmailTpl['push']['message']]);
            }else{
                $this->logger->info("Header and footer settings got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                $tempSettings = $retEmailTpl['push']['object']->getSettingsList();
                $params['emHeader'] = $tempSettings['EM_HEADER'];
                $params['emFooter'] = $tempSettings['EM_FOOTER'];
            }

            //POP settings
            $featureDTO->setSettingCatId(12);
            $retPop = $featureDAO->fetchConfigsByCategory($featureDTO);
            if(!$retPop['status']){
                $this->logger->error("Can't get POP settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error'=>$retPop['push']['message']]);
            }else{
                $this->logger->info("POP settings got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                $popSettings = $retPop['push']['object']->getSettingsList();
                $params['popHost'] = $popSettings['POP_HOST'];
                $params['popTypeSelected'] = $popSettings['POP_TYPE'];
                $params['popPort'] = $popSettings['POP_PORT'];
                $params['popDomain'] = $popSettings['POP_DOMAIN'];
            }

            //LDAP settings
            $featureDTO->setSettingCatId(13);
            $retLdap = $featureDAO->fetchConfigsByCategory($featureDTO);
            if(!$retLdap['status']){
                $this->logger->error("Can't get LDAP settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error'=>$retLdap['push']['message']]);
            }else{
                $this->logger->info("LDAP settings got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                $ldapSettings = $retLdap['push']['object']->getSettingsList();
                $params['ldapTypeSelected'] = $ldapSettings['SES_LDAP_AD'];
                $params['ldapServer'] = $ldapSettings['SES_LDAP_SERVER'];
                $params['ldapDistName'] = $ldapSettings['SES_LDAP_DN'];
                $params['ldapDomain'] = $ldapSettings['SES_LDAP_DOMAIN'];
                $params['ldapField'] = $ldapSettings['SES_LDAP_FIELD'];
            }

            //Maintenance settings
            $params['maintenanceFlag'] = ($emSettings['SES_MAINTENANCE'] == 1) ? 'checked' : '';
            $params['maintenanceMsg'] = $emSettings['SES_MAINTENANCE_MSG'];

            //Other settings
            $featureDTO->setSettingCatId(1);
            $retMisc = $featureDAO->fetchConfigsByCategory($featureDTO);
            if(!$retMisc['status']){
                $this->logger->error("Can't get other settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error'=>$retMisc['push']['message']]);
            }else{
                $this->logger->info("Other settings got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                $miscSettings = $retMisc['push']['object']->getSettingsList();
                $params['misc2FAuth'] = ($miscSettings['SES_GOOGLE_2FA'] == 1) ? 'checked' : '';
                $params['miscCountrySelected'] = $emSettings['COUNTRY_DEFAULT'];
                $params['miscTimeSession'] = $miscSettings['SES_TIME_SESSION'];
            }
        }
        
        return $params;
    }
    
    /**
     * saveEmailChanges
     * 
     * en_us Writes email settings changes in DB
     * pt_br Grava as alterações das configurações de e-mail no BD
     *
     * @return void
     */
    public function saveEmailChanges()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aSettings =  array(
            'EM_HOSTNAME' => trim(strip_tags($_POST['emailHost'])),
            'EM_DOMAIN' => trim(strip_tags($_POST['emailDomain'])),
            'EM_USER' => trim(strip_tags($_POST['emailUser'])),
            'EM_PASSWORD' => trim(strip_tags($_POST['emailPassword'])),
            'EM_SENDER' => trim(strip_tags($_POST['emailSender'])),
            'EM_AUTH' => (isset($_POST['emailAuth'])) ? 1 : 0,
            'EM_HEADER' => $_POST['mailHeader'],
            'EM_TITLE' => trim(strip_tags($_POST['emailTitle'])),
            'EM_PORT' => trim(strip_tags($_POST['emailPort'])),
            'EM_FOOTER' => $_POST['mailFooter'],
            'EM_SUCCESS_LOG' => (isset($_POST['emailSuccessLog'])) ? 1 : 0,
            'EM_FAILURE_LOG' => (isset($_POST['emailFailLog'])) ? 1 : 0,
            'TRACKER_STATUS' => (isset($_POST['emailTracker'])) ? 1 : 0,
            'EM_BY_CRON' => (isset($_POST['emailByCron'])) ? 1 : 0
        );

        //Setting up the model
        $featureDTO->setSettingsList($aSettings);
        
        $ins = $featureDAO->saveSettingsChanges($featureDTO);
        if($ins['status']){
            $st = true;
            $msg = "";

            $this->logger->info("Email settings was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];

            $this->logger->error("Could not save email settings. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * savePopChanges
     * 
     * en_us Writes POP settings changes in DB
     * pt_br Grava as alterações das configurações POP no BD
     *
     * @return void
     */
    public function savePopChanges()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aSettings =  array(
            'POP_HOST' => trim(strip_tags($_POST['popHost'])),
            'POP_PORT' => trim(strip_tags($_POST['popPort'])),
            'POP_DOMAIN' => trim(strip_tags($_POST['popDomain'])),
            'POP_TYPE' => trim(strip_tags($_POST['cmbPopType']))
        );

        //Setting up the model
        $featureDTO->setSettingsList($aSettings);
        
        $ins = $featureDAO->saveSettingsChanges($featureDTO);
        if($ins['status']){
            $st = true;
            $msg = "";

            $this->logger->info("POP settings was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];

            $this->logger->error("Could not save POP settings. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * saveLdapChanges
     * 
     * en_us Writes LDAP/AD settings changes in DB
     * pt_br Grava as alterações das configurações LDAP/AD no BD
     *
     * @return void
     */
    public function saveLdapChanges()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aSettings =  array(
            'SES_LDAP_AD' => trim(strip_tags($_POST['cmbLdapType'])),
            'SES_LDAP_SERVER' => trim(strip_tags($_POST['ldapServer'])),
            'SES_LDAP_DN' => trim(strip_tags($_POST['ldapDistName'])),
            'SES_LDAP_DOMAIN' => trim(strip_tags($_POST['ldapDomain'])),
            'SES_LDAP_FIELD' => trim(strip_tags($_POST['ldapField']))
        );

        //Setting up the model
        $featureDTO->setSettingsList($aSettings);
        
        $ins = $featureDAO->saveSettingsChanges($featureDTO);
        if($ins['status']){
            $st = true;
            $msg = "";

            $this->logger->info("LDAP/AD settings was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];

            $this->logger->error("Could not save LDAP/AD settings. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * saveMaintenanceChanges
     * 
     * en_us Writes maintenance settings changes in DB
     * pt_br Grava as alterações das configurações de manutenção no BD
     *
     * @return void
     */
    public function saveMaintenanceChanges()
    {
        if(!$this->appSrc->_checkToken()){
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aSettings =  array(
            'SES_MAINTENANCE' => (isset($_POST['maintenanceFlag'])) ? 1 : 0,
            'SES_MAINTENANCE_MSG' => $_POST['maintenanceMsg']
        );

        //Setting up the model
        $featureDTO->setSettingsList($aSettings);
        
        $ins = $featureDAO->saveSettingsChanges($featureDTO);
        if($ins['status']){
            $st = true;
            $msg = "";

            $this->logger->info("Maintenance settings was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];

            $this->logger->error("Could not save maintenance settings. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * saveMiscChanges
     * 
     * en_us Writes other settings changes in DB
     * pt_br Grava as alterações de outras configurações no BD
     *
     * @return void
     */
    public function saveMiscChanges()
    {
        if(!$this->appSrc->_checkToken()){
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aSettings =  array(
            'SES_GOOGLE_2FA' => (isset($_POST['misc2FAuth'])) ? 1 : 0,
            'COUNTRY_DEFAULT' => $_POST['cmbDefCountry'],
            'SES_TIME_SESSION' => empty(trim(strip_tags($_POST['miscTimeSession']))) ? 600 : trim(strip_tags($_POST['miscTimeSession']))
        );

        //Setting up the model
        $featureDTO->setSettingsList($aSettings);
        
        $ins = $featureDAO->saveSettingsChanges($featureDTO);
        if($ins['status']){
            $st = true;
            $msg = "";

            $this->logger->info("Other settings was saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $ins['push']['message'];

            $this->logger->error("Could not save other settings. Error: {$ins['push']['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * loadModuleConfs
     * 
     * en_us Returns module's settings
     * pt_br Retorna as configurações do módulo
     *
     * @return void
     */
    public function loadModuleConfs()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        $moduleDAO = new moduleDAO();
        $moduleDTO = new moduleModel();

        //get module's info
        $moduleDTO->setIdModule($_POST['idmodule']);
        $retMod = $moduleDAO->getModule($moduleDTO);
        if(!$retMod['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retMod['push']['message']]);
            
            echo "<div class='row col-sm-11 alert alert-danger ms-5'><div class='row g-2 text-center'>{$this->translator->translate('generic_error_msg')}</div></div>";
            exit;
        }

        $prefix = $retMod['push']['object']->getTablePrefix();
        $catTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig_category";
        $confTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig";
        
        //check if table exists
        $featureDTO->setTableName($confTable);
        $check = $featureDAO->tableExists($featureDTO);
        if(!$check['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            
            echo "<div class='row col-sm-11 alert alert-danger ms-5'><div class='row g-2 text-center'>{$this->translator->translate('generic_error_msg')}</div></div>";
            exit;
        }
        
        if(!$check['push']['object']->getExistTable()){
            $this->logger->info("{$confTable} not exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            echo "<div class='row col-sm-11 alert alert-warning ms-5'><div class='row g-2 text-center'>{$this->translator->translate('No_result')}</div></div>";
            exit;
        }

        //get settings
        $featureDTO->setTableName($catTable);
        $retCat = $featureDAO->fetchModuleSettingCategories($featureDTO);
        if(!$retCat['status']){
            $this->logger->error("Can't get module's settings categories", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retCat['push']['message']]);
            
            echo "<div class='row col-sm-11 alert alert-danger ms-5'><div class='row g-2 text-center'>{$this->translator->translate('generic_error_msg')}</div></div>";
            exit;
        }

        $aCategories = $retCat['push']['object']->getSettingsCatList();
        if(count($aCategories) <= 0){
            $this->logger->info("No categories found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            echo "<div class='row col-sm-11 alert alert-warning ms-5'><div class='row g-2 text-center'>{$this->translator->translate('No_result')}</div></div>";
            exit;
        }

        $html = "";
        foreach($aCategories as $key=>$val){
            $featureDTO->setSettingCatId($val['idconfigcategory'])
                       ->setTableName($confTable);

            $retSettings = $featureDAO->fetchModuleSettings($featureDTO);
            if(!$retSettings['status']){
                $this->logger->error("Can't get module's settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retSettings['push']['message']]);
                
                echo "<div class='row col-sm-11 alert alert-danger ms-5'><div class='row g-2 text-center'>{$this->translator->translate('generic_error_msg')}</div></div>";
                exit;
            }

            $aSettings = $retSettings['push']['object']->getSettingsList();
            if(count($aSettings) <= 0){
                $this->logger->info("No settings found. Module: {$_POST['idmodule']}. Category: {$val['idconfigcategory']} ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                continue;
            }

            $catName = (empty(trim($val['smarty']))) ? $val['name'] : $this->translator->translate($val['smarty']);
            $html .= "<form method='post' id='module-feature-form' name='module-feature-form' class='mb-3'>
                    <div class='card card-primary'>
                        <div class='card-header'>
                            <i class='fa fa-cog'></i>  {$catName}
                        </div>
                        <div class='card-body'>";
            
            foreach($aSettings as $k=>$v){
                switch($v['field_type']){
                    case 'input':
                        $settingField = "<div class='row text-center'>
                                            <div class='col-sm-4'>&nbsp;</div>
                                            <div class='col-sm-4 text-center'>
                                                <input type='text' value='{$v['value']}' class='text-center form-control form-control-sm changeConfigValue' id='{$prefix}_{$v['idconfig']}' data-id='{$prefix}{$v['idconfig']}' />
                                            </div>
                                            <div class='col-sm-4'>&nbsp;</div>
                                         </div>";
                        break;
                    case 'checkbox':
                        $checked = $v['value'] == 1 ? "checked" : "";

                        $settingField = "<div class='row text-center'>
                                            <div class='col-sm-4'>&nbsp;</div>
                                            <div class='col-sm-4 text-center'>
                                                <input type='checkbox' id='{$prefix}_{$v['value']}' {$checked} class='changeConfigStatus i-Checks' value='{$prefix}_{$v['idconfig']}' />
                                            </div>
                                            <div class='col-sm-4'>&nbsp;</div>
                                        </div>";
                        break;
                    default:
                        $settingField = "<div class='row col-sm-10 text-center ms-3'>
                                            <input type='text' value='{$v['value']}' class='text-center form-control form-control-sm changeConfigValue' id='{$prefix}_{$v['idconfig']}' data-id='{$prefix}{$v['idconfig']}' />
                                        </div>";
                        break;

                }


                $settingName = (empty(trim($v['smarty']))) ? $v['name'] : $this->translator->translate($v['smarty']);
                $removeIcon = (isset($v['allowremove']) && $v['allowremove'] == 'Y') ? '<a href="javascript:;" class="btn btn-danger btn-sm tooltip-buttons removeConfig" data-toggle="tooltip" data-placement="top" title="'.$this->translator->translate('Feature_remove').'" data-id="'.$prefix.'_'.$v['idconfig'].'"><i class="fa fa-trash-alt"></i></a>' : "";
                $html .= "<div class='row g-2 mt-1'>  
                            <div class='col-sm-4'>{$settingField}</div>
                            <div class='col-sm-7'>{$settingName}</div>
                            <div class='col-sm-1'>{$removeIcon}</div>
                          </div>";
            }

            $html .= "</div>
                    </div>
                    </form>";
        }

        echo $html;
    }
    
    /**
     * updateConfig
     * 
     * en_us Updates module's setting
     * pt_br Atualiza a configuração do módulo
     *
     * @return void
     */
    public function updateConfig()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aId = explode("_",$_POST['id']);

        $featureDTO->setTableName("{$aId[0]}_tbconfig")
                   ->setSettingId($aId[1])
                   ->setSettingValue($_POST['newVal']);

        $upd = $featureDAO->updateSettingValueById($featureDTO);
        if($upd['status']){
            $st = true;
            $msg = "";

            $this->logger->info("Setting value was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $upd['push']['message'];

            $this->logger->error("Could not update setting value", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * removeConfig
     * 
     * en_us Removes module's setting
     * pt_br Remove a configuração do módulo
     *
     * @return void
     */
    public function removeConfig()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        $aId = explode("_",$_POST['id']);

        $featureDTO->setTableName("{$aId[0]}_tbconfig")
                   ->setSettingId($aId[1]);

        $del = $featureDAO->deleteSetting($featureDTO);
        if($del['status']){
            $st = true;
            $msg = "";

            $this->logger->info("Setting was removed successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }else{
            $st = false;
            $msg = $del['push']['message'];

            $this->logger->error("Could not remove setting", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $del['push']['message']]);
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg
        );       

        echo json_encode($aRet);
    }
    
    /**
     * modalNewFeature
     * 
     * en_us Returns categories options for new feature
     * pt_br Retorna opções de categorias para a nova configuração
     *
     * @return void
     */
    public function modalNewFeature()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        $moduleDAO = new moduleDAO();
        $moduleDTO = new moduleModel();

        //get module's info
        $moduleDTO->setIdModule($_POST['moduleId']);
        $retMod = $moduleDAO->getModule($moduleDTO);
        if(!$retMod['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retMod['push']['message']]);
            return false;
        }

        $catTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig_category";
        
        //get settings
        $featureDTO->setTableName($catTable);
        $retCat = $featureDAO->fetchModuleSettingCategories($featureDTO);
        if(!$retCat['status']){
            $this->logger->error("Can't get module's settings categories", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retCat['push']['message']]);
            return false;
        }

        $aCategories = $retCat['push']['object']->getSettingsCatList();
        //echo "",print_r($aCategories),"\n";
        if(count($aCategories) <= 0){
            $this->logger->info("No categories found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            $st = false;
            $msg = "";
        }else{
            $this->logger->info("Categories got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            $st = true;
            $msg = "";

            foreach($aCategories as $k=>$v){
                $catOptions .= "<option value='{$v['idconfigcategory']}'>{$v['name']}</option>";
            }

        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "catOptions" => $catOptions
        );       

        echo json_encode($aRet);
    }
    
    /**
     * checkCategory
     * 
     * en_us Cheks if category exists in DB
     * pt_br Verifica se a categoria existe no BD
     *
     * @return void
     */
    public function checkCategory()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        $moduleDAO = new moduleDAO();
        $moduleDTO = new moduleModel();

        //get module's info
        $moduleDTO->setIdModule($_POST['moduleId']);
        $retMod = $moduleDAO->getModule($moduleDTO);
        if(!$retMod['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retMod['push']['message']]);
            return false;
        }

        $catTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig_category";
        $featureDTO->setTableName($catTable)
                   ->setSettingCatName(trim(strip_tags($_POST['new-cat-name'])));
                   
        //check if table exists in DB
        $check = $featureDAO->getFeatureCategoryByName($featureDTO);
        if(!$check['status']){
            $this->logger->error("Can't check category", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }
        
        $featureCatId = $check['push']['object']->getSettingCatId();
        if (!empty($featureCatId) && !is_null($featureCatId) && $featureCatId > 0) {
            echo json_encode($this->translator->translate('Value_exists'));
        } else {
            echo json_encode(true);
        }
    }
    
    /**
     * saveNewcategory
     * 
     * en_us Saves feature's new category in DB
     * pt_br Grava a nova categoria de configuração no BD
     *
     * @return void
     */
    public function saveNewCategory()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        $moduleDAO = new moduleDAO();
        $moduleDTO = new moduleModel();

        //get module's info
        $moduleDTO->setIdModule($_POST['new-cat-module-id']);
        $retMod = $moduleDAO->getModule($moduleDTO);
        if(!$retMod['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retMod['push']['message']]);
            return false;
        }

        $catTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig_category";
        $featureDTO->setTableName($catTable);

        //check if table exists in DB
        $check = $featureDAO->tableExists($featureDTO);
        if(!$check['status']){
            $this->logger->error("Can't check if table exist", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }

        if(!$check['push']['object']->getExistTable()){
            $this->logger->info("{$catTable} not exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDTO->setSettingCatName(trim(strip_tags($_POST['new-cat-name'])))
                   ->setSettingCatLangKey(trim(strip_tags($_POST['new-cat-keyname'])))
                   ->setSettingCatFlgSetup('Y');
        
        
        $ins = $featureDAO->insertNewCategory($featureDTO);
        if(!$ins['status']){
            $this->logger->error("Can't save feature's new category", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_msg_error');
            $featureCatId = 0;
        }else{
            $this->logger->info("Feature's new category saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            $st = true;
            $msg = "";
            $featureCatId = $ins['push']['object']->getSettingCatId();
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "featureCatId" => $featureCatId
        );       

        echo json_encode($aRet);
    }
    
    /**
     * checkField
     * 
     * en_us Cheks if field value exists in DB
     * pt_br Verifica se o valor do campo existe no BD
     *
     * @return void
     */
    public function checkField()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        $moduleDAO = new moduleDAO();
        $moduleDTO = new moduleModel();

        //get module's info
        $moduleDTO->setIdModule($_POST['moduleId']);
        $retMod = $moduleDAO->getModule($moduleDTO);
        if(!$retMod['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retMod['push']['message']]);
            return false;
        }

        $catTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig";
        $fieldName = trim(strip_tags($_POST['fieldName']));
        $fieldValue = ($fieldName == 'name') ? trim(strip_tags($_POST['new-feature-name'])) : trim(strip_tags($_POST['new-feature-session']));
        
        $featureDTO->setTableName($catTable)
                   ->setFieldName($fieldName)
                   ->setFieldValue($fieldValue);
                   
        //check if table exists in DB
        $check = $featureDAO->getFeatureIdByField($featureDTO);
        if(!$check['status']){
            $this->logger->error("Can't check field value", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }
        
        $featureId = $check['push']['object']->getSettingId();
        if (!empty($featureId) && !is_null($featureId) && $featureId > 0) {
            echo json_encode($this->translator->translate('Value_exists'));
        } else {
            echo json_encode(true);
        }
    }
    
    /**
     * saveNewFeature
     * 
     * en_us Saves new feature in DB
     * pt_br Grava a nova configuração no BD
     *
     * @return void
     */
    public function saveNewFeature()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        $moduleDAO = new moduleDAO();
        $moduleDTO = new moduleModel();
        //echo "olaaaa";
        //get module's info
        $moduleDTO->setIdModule($_POST['moduleId']);
        $retMod = $moduleDAO->getModule($moduleDTO);
        if(!$retMod['status']){
            $this->logger->error("Can't get module's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $retMod['push']['message']]);
            return false;
        }
        
        $catTable = "{$retMod['push']['object']->getTablePrefix()}_tbconfig";
        $featureDTO->setTableName($catTable);
        
        //check if table exists in DB
        $check = $featureDAO->tableExists($featureDTO);
        if(!$check['status']){
            $this->logger->error("Can't check if table exist", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }

        if(!$check['push']['object']->getExistTable()){
            $this->logger->info("{$catTable} not exists", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $sessionName = str_replace(' ','_',trim(strip_tags($_POST['new-feature-session'])));
        $langKey = str_replace(' ','_',trim(strip_tags($_POST['new-feature-lang-key'])));
        $fieldType = ($_POST['cmbFeatureType'] == 'X') ? '' : $_POST['cmbFeatureType'];
        $value = ($fieldType == "checkbox")
                 ? isset($_POST['new-feature-value-check']) ? "1" : "0"
                 : trim(strip_tags($_POST['new-feature-value-input']));
        $flagDefault = isset($_POST['new-feature-default']) ? 'N' : 'Y';
        
        $featureDTO->setSettingName(trim(strip_tags($_POST['new-feature-name'])))
                   ->setSettingDescription(trim(strip_tags($_POST['new-feature-description'])))
                   ->setSettingCatId($_POST['cmbFeatureCat'])
                   ->setSessionName($sessionName)
                   ->setSettingLangKey($langKey)
                   ->setFieldType($fieldType)
                   ->setSettingValue($value)
                   ->setFlagDefault($flagDefault);        
        
        $ins = $featureDAO->insertNewFeature($featureDTO);
        if(!$ins['status']){
            $this->logger->error("Can't save feature's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
            $featureId = 0;
        }else{
            $this->logger->info("Feature's new category saved successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            $st = true;
            $msg = "";
            $featureId = $ins['push']['object']->getSettingId();
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "featureId" => $featureId
        );       

        echo json_encode($aRet);
    }
}