<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\hdkEmailFeatureDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;

use App\modules\helpdezk\models\mysql\hdkEmailFeatureModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;

use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\modules\helpdezk\src\hdkServices;

class hdkEmailConfig extends Controller
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
     * index
     *
     * @return void
     */
    public function index()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenEmailConfig();
		
		$this->view('helpdezk','hdk-email-config',$params);
    }
    
    /**
     * makeScreenEmailConfig
     *
     * @param  mixed $option
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenEmailConfig($option='idx',$obj=null)
    {
        $admSrc = new adminServices();
        $mainSrc = new mainServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $admSrc->_makeNavAdm($params);
        
        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboHdkRequestEmailFilters();
          $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
          $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte'; //upload image
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        if($option != 'idx'){
            // -- Locales dropdown list --
            $params['cmbLocale'] = $mainSrc->_comboLocale();
            $params['cmbSetting'] = $this->comboEmailSetting();
        }

        if($option == 'add') {
            // -- Default country combo --
            $params['cmbFeatureTypes'] = $admSrc->_comboFieldType();
        }
        
        if($option=='upd'){
            $params['templateId'] = $obj->getIdEmailTemplate();
            $params['featureId'] = $obj->getFeatureId();
            $params['settingName'] = (!empty($obj->getSettingName())) ? $obj->getSettingName() : $this->translator->translate($obj->getSettingKeyLang());
            $params['localeId'] = $obj->getIdLocale();
            $params['subject'] = $obj->getSubject();
            $params['body'] = $obj->getBody();
        }
        
        return $params;
    }
    
    /**
     * jsonGrid
     * 
     * en_us Returns groups list to display in grid
     * pt_br Retorna lista de grupos para exibir no grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $emailConfigDAO = new hdkEmailFeatureDAO(); 

        $where = "";
        $group = "";
        
        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            $where .=  " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " AND (pipeLatinToUtf8(name) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= " AND  `status` = 'A' ";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countGroup = $emailConfigDAO->countHdkEmailFeature($where); 
        if($countGroup['status']){
            $total_Records = $countGroup['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $retGroup = $emailConfigDAO->queryHdkEmailFeature($where,$group,$order,$limit);
        
        if($retGroup['status']){     
            $aGroups = $retGroup['push']['object']->getGridList();     
            
            foreach($aGroups as $k=>$v) {
                $fmtStatus = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                $data[] = array(
                    'idconfig'      => $v['idconfig'],
                    'name'          => (!is_null($v['smarty']) && !empty($v['smarty'])) ? $this->translator->translate($v['smarty']) : $v['name'],
                    'status'        => $fmtStatus,
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
     * comboHdkRequestEmailFilters
     * 
     * en_us Renders the status add screen
     * pt_br Renderiza o template da tela de novo cadastro de status
     *
     * @return array
     */
    public function comboHdkRequestEmailFilters(): array
    {
        $aRet = array(            
            array("id" => 'area',"text"=>$this->translator->translate('Area'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),            
            array("id" => 'type',"text"=>$this->translator->translate('Type'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),            
            array("id" => 'item',"text"=>$this->translator->translate('Item'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),            
            array("id" => 'service',"text"=>$this->translator->translate('Service'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'user',"text"=>$this->translator->translate('email'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    /**
     * formCreate
     * 
     * en_us Renders the status add screen
     * pt_br Renderiza o template da tela de novo cadastro de status
     *
     * @return void
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenEmailConfig('add');

        $this->view('helpdezk','hdk-email-config-create',$params);
    }

    /**
     * createEmailConfig
     * 
     * en_us Write the email config template into the DB
     * pt_br Grava no BD as informações do template de email
     *
     * @return void
     */
    public function createEmailConfig()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $emailConfigDAO = new hdkEmailFeatureDAO();
        $emailConfigDTO = new hdkEmailFeatureModel();

        $featureId = trim(strip_tags($_POST['cmbSetting']));
        $localeId = trim(strip_tags($_POST['cmbLocale']));
        $subject = trim(strip_tags($_POST['template-subject']));
        $body = trim($_POST['template-body']);

        $emailConfigDTO->setFeatureId($featureId)
                       ->setIdLocale($localeId)
                       ->setSubject($subject)
                       ->setBody($body);
                        
        $ins = $emailConfigDAO->saveEmailConfig($emailConfigDTO);
        if($ins['status']){
            $this->logger->info("Email config template save successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $emailConfigId = $ins['push']['object']->getIdEmailTemplate();            
        }else{
            $this->logger->error("Can't save mail config template.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $emailConfigId = "";
        }   
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "emailConfigId" => $emailConfigId,
            "subject"       => $subject
        );

        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     * 
     * en_us Renders the requests by email settings update screen
     * pt_br Renderiza o template da atualização do cadastro de configurações para solicitações por e-mail
     *
     * @param  mixed $groupId
     * @return void
     */
    public function formUpdate($settingId=null)
    {
        $emailConfigDAO = new hdkEmailFeatureDAO();
        $emailConfigDTO = new hdkEmailFeatureModel();
        $emailConfigDTO->setFeatureId($settingId);
        
        $ret = $emailConfigDAO->getEmailTemplateBySettingId($emailConfigDTO);
        
        $params = $this->makeScreenEmailConfig('upd',$ret['push']['object']);
        
        $this->view('helpdezk','hdk-email-config-update',$params);
    }
    
    /**
     * updateEmailConfig
     * 
     * en_us Updates the group information in the DB
     * pt_br Atualiza no BD as informações do grupo
     *
     * @return void
     */
    public function updateEmailConfig()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $emailConfigDAO = new hdkEmailFeatureDAO();
        $emailConfigDTO = new hdkEmailFeatureModel();
        
        $localeId = trim(strip_tags($_POST['cmbLocale']));
        $subject = trim(strip_tags($_POST['template-subject']));
        $body = trim($_POST['template-body']);

        $emailConfigDTO->setIdEmailTemplate($_POST['templateId'])
                       ->setIdLocale($localeId)
                       ->setSubject($subject)
                       ->setBody($body);
        
        $upd = $emailConfigDAO->updateEmailConfig($emailConfigDTO);
        if($upd['status']){
            $this->logger->info("Email config template # {$upd['push']['object']->getIdEmailTemplate()} data was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $emailConfigId = $upd['push']['object']->getIdEmailTemplate();
        }else{
            $this->logger->error("Can't update email config template # {$emailConfigDTO->getIdEmailTemplate()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $upd['push']['message'];
            $emailConfigId = "";
        }           
       
        $aRet = array(
            "success"           => $st,
            "emailConfigId"    => $emailConfigId
        );        

        echo json_encode($aRet);
    }
    
    /**
     * checkExistLangKey
     * 
     * en_us Check if the email template for selected notification and language has already been registered before
     * pt_br Verifica se o template de e-mail para a notificação e idioma selecionados já foi cadastrado anteriormente
     *
     * @return void
     */
    public function checkExistLangKey()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $emailConfigDAO = new hdkEmailFeatureDAO();

        $localeId = strip_tags($_POST['cmbLocale']);
        $featureId = strip_tags($_POST['featureId']);

        $where = " AND c.idlocale = {$localeId} AND b.idconfig = {$featureId}"; 
        $where .= (isset($_POST['templateId'])) ? " AND b.idtemplate != {$_POST['templateId']}" : "";        

        $check =  $emailConfigDAO->queryEmailTemplate($where);
        if(!$check['status']){
            $this->logger->error("Can't check if template exists.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('email_notification_lang'));
        }else{
            echo json_encode(true);
        }
    }
    
    /**
     * checkExist
     * 
     * en_us Check if the email template has already been registered before
     * pt_br Verifica se o template de e-mail já foi cadastrado anteriormente
     *
     * @return void
     */
    public function checkExist()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $emailConfigDAO = new hdkEmailFeatureDAO();

        $subject = trim(strip_tags($_POST['template-subject']));
        $subject = addslashes($subject);
        $localeId = strip_tags($_POST['localeId']);
        $featureId = strip_tags($_POST['featureId']);

        $where = " AND pipeLatinToUtf8(c.name) = pipeLatinToUtf8('{$subject}') AND c.idlocale = {$localeId} AND b.idconfig = {$featureId}"; 
        $where .= (isset($_POST['templateId'])) ? " AND b.idtemplate != {$_POST['templateId']}" : "";        

        $check =  $emailConfigDAO->queryEmailTemplate($where);
        if(!$check['status']){
            $this->logger->error("Can't check if template exists.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }
        
        $checkObj = $check['push']['object']->getGridList();
        if(count($checkObj) > 0){
            echo json_encode($this->translator->translate('emailTemplate_exists'));
        }else{
            echo json_encode(true);
        }
    }
    
    /**
     * comboEmailSetting
     * 
     * en_us Returns an array with notification data for dropdown list
     * pt_br Retorna um array com dados de notificação para lista suspensa
     *
     * @return void
     */
    public function comboEmailSetting()
    {
        $emailConfigDAO = new hdkEmailFeatureDAO();
        $aRet =  array();

        $where = " AND idconfig NOT IN (SELECT idconfig FROM hdk_tbconfig_has_template)";
        $order = "ORDER BY `name`";

        $ret =  $emailConfigDAO->queryHdkEmailFeature(null,null,$order);
        if(!$ret['status']){
            $this->logger->error("Can't get email settings list.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            return false;
        }
        
        $aSettings = $ret['push']['object']->getGridList();
        foreach($aSettings as $key=>$val){
            $bus =  array(
                "id" => $val['idconfig'],
                "text" => (!is_null($val['smarty']) && !empty($val['smarty'])) ? $this->translator->translate($val['smarty']) : $val['name']
            );
            array_push($aRet,$bus);
        }
        
        return $aRet;
    }

    /**
     * changeStatus
     * 
     * en_us Changes email config status
     * pt_br Muda o status da configuração do e-mail
     *
     * @return void
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $emailConfigDAO = new hdkEmailFeatureDAO();
        $emailConfigDTO = new hdkEmailFeatureModel();

        //Setting up the model
        $emailConfigDTO->setFeatureId($_POST['featureId'])
                       ->setStatus($_POST['newStatus']);
        
        $upd = $emailConfigDAO->updateEmailConfigStatus($emailConfigDTO);
        if(!$upd['status']){
            $this->logger->error("Can't update email config # {$emailConfigDTO->getFeatureId()} status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);
    }
    
    /**
     * ajaxEmailSettings
     * 
     * en_us Reloads options for email settings dropdown list
     * pt_br Recarrega opções para o combo de configurações de e-mail
     *
     * @return void
     */
    function ajaxEmailSettings()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $html = "";
        $ret = $this->comboEmailSetting();
        if(!$ret){
            $st = false;
        }else{
            $st = true;

            foreach($ret as $key=>$val){
                $selected = ($val['id'] == $_POST['selectedId']) ? "selected" : "";
                $html .= "<option value='{$val['id']}' {$selected}>{$val['text']}</option>";
            }
        }

        $aRet = array(
            "success" => $st,
            "data"    => $html
        );

        echo json_encode($aRet);
    }
}