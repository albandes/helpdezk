<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\warningDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;

use App\modules\helpdezk\models\mysql\warningModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;

use App\modules\admin\src\adminServices;
use App\modules\main\src\mainServices;
use App\modules\helpdezk\src\hdkServices;

class hdkWarning extends Controller
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

        $params = $this->makeScreenWarning();
		
		$this->view('helpdezk','warning',$params);
    }
    
    /**
     * makeScreenWarning
     *
     * @param  mixed $option
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenWarning($option='idx',$obj=null)
    {
        $admSrc = new adminServices();
        $mainSrc = new mainServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $admSrc->_makeNavAdm($params);
        
        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboWarningFilters();
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
            $params['cmbTopic'] = $this->comboTopic();

            // -- Datepicker settings -- 
            $params = $this->appSrc->_datepickerSettings($params);

            $params['momentFormat'] = ($_ENV['DEFAULT_LANG'] == 'en_us') ? "MM/DD/YYYY" : "DD/MM/YYYY";

            // -- Showin dropdown list --
            $params['cmbShowIn'] = $this->comboShowIn();

            // -- Show groups checkboxes --
            $params['groupList'] = $this->groupsCheckList();

            // -- Show companies checkboxes --
            $params['companyList'] = $this->companiesCheckList();
        }
        
        if($option=='upd'){
            $params['warningId'] = $obj->getWarningId();
            $params['selectedTopic'] = $obj->getTopicId();
            $params['title'] = $obj->getWarningTitle();
            $params['description'] = $obj->getWarningDescription();
            $params['startDate'] = $this->appSrc->_formatDate($obj->getStartDate());
            $params['startTime'] = $this->appSrc->_formatHour($obj->getStartDate());
            $params['flgUntilClosed'] = (empty($obj->getEndDate()) || $obj->getEndDate() == '0000-00-00 00:00:00') ? true : false;
            $params['endDate'] = ($params['flgUntilClosed']) ? "" : $this->appSrc->_formatDate($obj->getEndDate());
            $params['endTime'] = ($params['flgUntilClosed']) ? "" : $this->appSrc->_formatHour($obj->getEndDate());
            $params['flgSendAlert'] = (empty($obj->getFlagSendEmail()) || $obj->getFlagSendEmail() == 'N') ? false : true;
            $params['selectedShowIn'] = $obj->getShowIn();
        }

        if($option == 'topicIdx'){
            $params['cmbFilters'] = $this->comboTopicFilters();
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';

            // -- Show groups checkboxes --
            $params['groupList'] = $this->groupsCheckList();

            // -- Show companies checkboxes --
            $params['companyList'] = $this->companiesCheckList();
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
        $warningDAO = new warningDAO();

        $where = "";
        $group = "";
        
        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch($filterIndx){
                case 'topic':
                    $filterIndx = 'b.title';
                    break;
                case 'title':
                    $filterIndx = 'a.title';
                    break;
                default:
                    $filterIndx = $filterIndx;
                    break;
            }

            $where .=  " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " AND (pipeLatinToUtf8(a.title) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(b.title) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "title";

        switch($sortIndx){
            case 'topic':
                $sortIndx = 'title_topic';
                break;
            case 'title':
                $sortIndx = 'title_warning';
                break;
            case 'createddate':
                $sortIndx = 'dtcreate';
                break;
            case 'startdate':
                $sortIndx = 'dtstart';
                break;
            case 'enddate':
                $sortIndx = 'dtend';
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
        $countGroup = $warningDAO->countWarnings($where); 
        if($countGroup['status']){
            $total_Records = $countGroup['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $retGroup = $warningDAO->queryWarnings($where,$group,$order,$limit);
        
        if($retGroup['status']){     
            $aGroups = $retGroup['push']['object']->getGridList();     
            
            foreach($aGroups as $k=>$v) {
                switch ($v['showin']) {
                    case '1':
                        $showIn = "Home";
                        break;
                    case '2':
                        $showIn = "Login";
                        break;
                    case '3':
                        $showIn = $this->translator->translate("lbl_both");
                        break;
                }

                $data[] = array(
                    'idmessage'     => $v['idmessage'],
                    'topic'         => $v['title_topic'],
                    'title'         => $v['title_warning'],
                    'createddate'   => $this->appSrc->_formatDate($v['dtcreate']),
                    'startdate'     => $this->appSrc->_formatDate($v['dtstart']),
                    'enddate'      => ($v['dtend'] == '0000-00-00 00:00:00' || is_null($v['dtend']) || empty($v['dtend'])) ? $this->translator->translate("until_closed") : $this->appSrc->_formatDate($v['dtend']),
                    'showin'        => $showIn
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
     * comboWarningFilters
     * 
     * en_us Renders the warning add screen
     * pt_br Renderiza a tela de novo cadastro de status
     *
     * @return array
     */
    public function comboWarningFilters(): array
    {
        $aRet = array(            
            array("id" => 'topic',"text"=>$this->translator->translate('Topic'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),            
            array("id" => 'title',"text"=>$this->translator->translate('Title'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }

    /**
     * formCreate
     * 
     * en_us Renders the warning add screen
     * pt_br Renderiza a tela de novo cadastro de aviso
     *
     * @return void
     */
    public function formCreate()
    {
        // blocks if the user does not have permission to add a new register
        if($this->aPermissions[2] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenWarning('add');
        
        $this->view('helpdezk','warning-create',$params);
    }

    /**
     * createWarning
     * 
     * en_us Write the warning into the DB
     * pt_br Grava no BD as informações do aviso
     *
     * @return void
     */
    public function createWarning()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();

        $endDate = (!isset($_POST['flag-until-closed'])) ? $this->appSrc->_formatSaveDateTime("{$_POST['end-date']} {$_POST['end-time']}") : "0000-00-00 00:00:00";

        $warningDTO->setTopicId(trim(strip_tags($_POST['cmbTopic'])))
                   ->setUserId($_SESSION['SES_COD_USUARIO'])
                   ->setWarningTitle(trim(strip_tags($_POST['warning-title'])))
                   ->setWarningDescription(trim(strip_tags($_POST['warning-description'])))
                   ->setStartDate($this->appSrc->_formatSaveDateTime("{$_POST['start-date']} {$_POST['start-time']}"))
                   ->setEndDate($endDate)
                   ->setFlagSendEmail((isset($_POST['flag-send-alert'])) ? 'Y' : 'N')
                   ->setShowIn(trim(strip_tags($_POST['cmbShowIn'])))
                   ->setFlagEmailSent(0);
                   
        $ins = $warningDAO->insertWarning($warningDTO);
        if($ins['status']){
            $this->logger->info("Warning's data saved successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $warningId = $ins['push']['object']->getWarningId();            
        }else{
            $this->logger->error("Can't save warning's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $warningId = "";
        }   
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "warningId" => $warningId
        );

        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     * 
     * en_us Renders warning's update screen
     * pt_br Renderiza o template da atualização do cadastro de aviso
     *
     * @param  mixed $groupId
     * @return void
     */
    public function formUpdate($warningId=null)
    {
        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();
        $warningDTO->setWarningId($warningId);
        
        $ret = $warningDAO->getWarning($warningDTO);
        
        $params = $this->makeScreenWarning('upd',$ret['push']['object']);
        
        $this->view('helpdezk','warning-update',$params);
    }
    
    /**
     * updateWarning
     * 
     * en_us Updates the warning information in the DB
     * pt_br Atualiza no BD as informações do aviso
     *
     * @return void
     */
    public function updateWarning()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();
        
        $endDate = (!isset($_POST['flag-until-closed'])) ? $this->appSrc->_formatSaveDateTime("{$_POST['end-date']} {$_POST['end-time']}") : "0000-00-00 00:00:00";

        $warningDTO->setTopicId(trim(strip_tags($_POST['cmbTopic'])))
                   ->setUserId($_SESSION['SES_COD_USUARIO'])
                   ->setWarningTitle(trim(strip_tags($_POST['warning-title'])))
                   ->setWarningDescription(trim(strip_tags($_POST['warning-description'])))
                   ->setStartDate($this->appSrc->_formatSaveDateTime("{$_POST['start-date']} {$_POST['start-time']}"))
                   ->setEndDate($endDate)
                   ->setFlagSendEmail((isset($_POST['flag-send-alert'])) ? 'Y' : 'N')
                   ->setShowIn(trim(strip_tags($_POST['cmbShowIn'])))
                   ->setWarningId($_POST['warningId']);
        
        $upd = $warningDAO->updateWarning($warningDTO);
        if($upd['status']){
            $this->logger->info("Warning # {$upd['push']['object']->getWarningId()} data was updated successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
        }else{
            $this->logger->error("Can't update warning # {$warningDTO->getWarningId()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $upd['push']['message'];
        }           
       
        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );        

        echo json_encode($aRet);
    }
    
    /**
     * topics
     *
     * @return void
     */
    public function topics()
    {
        // blocks if the user does not have permission to access
        if($this->aPermissions[1] != "Y")
            $this->appSrc->_accessDenied();

        $params = $this->makeScreenWarning('topicIdx');
		
		$this->view('helpdezk','warning-topics',$params);
    }

    /**
     * jsonGrid
     * 
     * en_us Returns topics list to display in grid
     * pt_br Retorna lista de tópicos para exibir no grid
     *
     * @return void
     */
    public function jsonTopicGrid()
    {
        $warningDAO = new warningDAO();

        $where = "";
        $group = "";
        
        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch($filterIndx){
                case 'topic':
                    $filterIndx = 'title';
                    break;
                default:
                    $filterIndx = $filterIndx;
                    break;
            }

            $where .=  (empty($where) ? "WHERE " : " AND ") . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= (empty($where) ? "WHERE " : " AND ") . "(pipeLatinToUtf8(title) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "title";

        switch($sortIndx){
            case 'validity':
                $sortIndx = 'default_display';
                break;
            case 'flagsendemail':
                $sortIndx = 'fl_emailsent';
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
        $countTopic = $warningDAO->countTopics($where); 
        if($countTopic['status']){
            $total_Records = $countTopic['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $retTopic = $warningDAO->queryTopics($where,$group,$order,$limit);
        
        if($retTopic['status']){     
            $aTopics = $retTopic['push']['object']->getGridList();     
            
            foreach($aTopics as $k=>$v) {
                $defaultDisplay = trim($v['default_display']);
                if(!empty($defaultDisplay)){
                    $timeTmp = substr($defaultDisplay,0,-1);
                    switch (substr($defaultDisplay,-1)) {
                        case 'D':
                            $validity = $timeTmp / 86400;
                            $validity .= ($validity > 1) ? " {$this->translator->translate("Days")}" : " {$this->translator->translate("Day")}";
                            break;
                        case 'H':
                            $validity = $timeTmp / 3600;
                            $validity .= ($validity > 1) ? " {$this->translator->translate("Hours")}" : " {$this->translator->translate("Hour")}";
                            break;
                    }
                }else{
                    $validity = $this->translator->translate("until_closed");
                }

                $data[] = array(
                    'idtopic'       => $v['idtopic'],
                    'title'         => $v['title'],
                    'validity'      => $validity,
                    'flagsendemail' => ($v['fl_emailsent'] == "Y") ? '<span class="label label-info">&check;</span>' : ''
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
     * comboTopicFilters
     * 
     * en_us Renders the warning add screen
     * pt_br Renderiza a tela de novo cadastro de status
     *
     * @return array
     */
    public function comboTopicFilters(): array
    {
        $aRet = array(            
            array("id" => 'topic',"text"=>$this->translator->translate('Topic'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
        );
        
        return $aRet;
    }
        
    /**
     * createTopic
     * 
     * en_us Write the topic data into the DB
     * pt_br Grava no BD as informações do tópico
     *
     * @return void
     */
    public function createTopic()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();

        $flagValidity = (!isset($_POST['modal-validity'])) ? 1 : $_POST['modal-validity'];
        switch($flagValidity){
            case 1:
                $validity = "";
                break;
            case 2:
                $validity = (trim(strip_tags($_POST['modal-validity-hours'])) * 3600) . "H";
                break;
            case 3:
                $validity = (trim(strip_tags($_POST['modal-validity-days'])) * 86400) . "D";
                break;
        }
        
        $warningDTO->setTopicTitle(trim(strip_tags($_POST['modal-topic-name'])))
                   ->setTopicValidity($validity)
                   ->setTopicFlagSendEmail((!isset($_POST['modal-topic-send-email'])) ? 'N' : 'Y');
                   
        if($_POST['topic-available-group'] == 2 && isset($_POST['checkGroups'])){
            $warningDTO->setTopicGroups($_POST['checkGroups']);
        }
        
        if($_POST['topic-available-company'] == 2 && isset($_POST['checkCompanies'])){
            $warningDTO->setTopicCompanies($_POST['checkCompanies']);
        }
        
        $ins = $warningDAO->saveTopic($warningDTO);
        if($ins['status']){
            $this->logger->info("Topic's data saved successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $topicId = $ins['push']['object']->getTopicId();            
        }else{
            $this->logger->error("Can't save topic's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $topicId = "";
        }   
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
            "topicId"   => $topicId
        );

        echo json_encode($aRet);
    }
    
    /**
     * topicFormUpdate
     * 
     * en_us Renders topic's update screen
     * pt_br Renderiza a tela do cadastro de tópico
     *
     * @return void
     */
    public function topicFormUpdate()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();

        $warningDTO->setTopicId(trim(strip_tags($_POST['topicId'])));
        $ret =  $warningDAO->getTopic($warningDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get topic's data. Topic # {$_POST['topicId']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            $st = false;
            $topicTitle = "";
            $topicValidityType = "";
            $topicValidity = "";
            $topicFlgSendEmail = "";
            $topicCompanyList = "";
            $topicGroupList = "";
        }else{
            $this->logger->info("Topic's data got successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $topicTitle = $ret['push']['object']->getTopicTitle();

            switch (substr($ret['push']['object']->getTopicValidity(),-1)) {
                case 'D':
                    $topicValidityType = 3;
                    $topicValidity = (substr($ret['push']['object']->getTopicValidity(),0,-1)) / 86400;
                    break;
                case 'H':
                    $topicValidityType = 2;
                    $topicValidity = (substr($ret['push']['object']->getTopicValidity(),0,-1)) / 3600;
                    break;
                default:
                    $topicValidityType = 1;
                    $topicValidity = "";
                    break;
            }
            
            $topicFlgSendEmail = $ret['push']['object']->getTopicFlagSendEmail();
            $topicCompanyList = $ret['push']['object']->getTopicCompanies();
            $topicGroupList = $ret['push']['object']->getTopicGroups();
        }

        $aRet = array(
            "success"           => true,
            "topicTitle"        => $topicTitle,
            "topicValidityType" => $topicValidityType,
            "topicValidity"     => $topicValidity,
            "topicFlgSendEmail" => $topicFlgSendEmail,
            "topicCompanyList"  => $topicCompanyList,
            "topicGroupList"    => $topicGroupList
        );

        echo json_encode($aRet);
    }
    
    /**
     * updateTopic
     * 
     * en_us Updates the topic data into the DB
     * pt_br Atualiza no BD as informações do tópico
     *
     * @return void
     */
    public function updateTopic()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();

        $flagValidity = (!isset($_POST['modal-validity'])) ? 1 : $_POST['modal-validity'];
        switch($flagValidity){
            case 1:
                $validity = "";
                break;
            case 2:
                $validity = (trim(strip_tags($_POST['modal-validity-hours'])) * 3600) . "H";
                break;
            case 3:
                $validity = (trim(strip_tags($_POST['modal-validity-days'])) * 86400) . "D";
                break;
        }
        
        $warningDTO->setTopicTitle(trim(strip_tags($_POST['modal-topic-name'])))
                   ->setTopicValidity($validity)
                   ->setTopicFlagSendEmail((!isset($_POST['modal-topic-send-email'])) ? 'N' : 'Y')
                   ->setTopicId(trim(strip_tags($_POST['modal-topic-id'])));
                   
        if($_POST['topic-available-group'] == 2 && isset($_POST['checkGroups'])){
            $warningDTO->setTopicGroups($_POST['checkGroups']);
        }
        
        if($_POST['topic-available-company'] == 2 && isset($_POST['checkCompanies'])){
            $warningDTO->setTopicCompanies($_POST['checkCompanies']);
        }
        
        $ins = $warningDAO->saveUpdateTopic($warningDTO);
        if($ins['status']){
            $this->logger->info("Topic's data updated successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $topicId = $ins['push']['object']->getTopicId();            
        }else{
            $this->logger->error("Can't update topic's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $topicId = "";
        }   
        
        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
            "topicId"   => $topicId
        );

        echo json_encode($aRet);
    }

    /**
     * comboTopic
     * 
     * en_us Returns an array with topics data for dropdown list
     * pt_br Retorna um array com dados dos tópicos para lista suspensa
     *
     * @return void
     */
    function comboTopic()
    {
        $warningDAO = new warningDAO();
        $aRet = array();

        $ret = $warningDAO->queryTopics(null,null,"ORDER BY title");
        if(!$ret['status']){
            $this->logger->error("Can't get topics data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
        }else{
            $this->logger->info("Topics data got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $aTopics = $ret['push']['object']->getGridList();

            foreach($aTopics as $key=>$val){
                $bus = array(
                    "id" => $val['idtopic'],
                    "text" => $val['title']
                );

                array_push($aRet,$bus);
            }
        }

        return $aRet;
    }

    /**
     * comboShowIn
     * 
     * en_us Returns an array with places data for dropdown list
     * pt_br Retorna um array com dados dos locais para lista suspensa
     *
     * @return void
     */
    function comboShowIn()
    {
        $aRet = array(
            array("id"=>1,"text"=>"Home"),
            array("id"=>2,"text"=>"Login"),
            array("id"=>3,"text"=>$this->translator->translate('lbl_both'))
        );

        return $aRet;
    }
        
    /**
     * groupsCheckList
     * 
     * en_us Returns string with groups checkboxes
     * pt_br Retorna string com checkboxes de grupos
     *
     * @return string
     */
    public function groupsCheckList(): string
    {
        $admSrc = new adminServices();

        $ret = $admSrc->_comboGroup();
        
        $checkbox = '';
        $a = sizeof($ret);
        $rowsCol = round($a / 2);
        $i = 1;
        
        if($a > 0){
            $this->logger->info("Groups data got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            foreach ( $ret as $key=>$val ) {
    
                if($i == 1 || $i == ($rowsCol + 1)){$checkbox .= "<div class='col-sm-6'>";}

                $checkbox .= "<div class='mb-1'>
                                <input type='checkbox' class='i-checks' id='checkGroups-{$val['id']}' name='checkGroups[]' value='{$val['id']}'>&nbsp;&nbsp;<small class='col-form-label'> {$val['text']} </small>
                            </div>";
                
                if($i == $rowsCol || $i == $a ){$checkbox .= "</div>";}

                $i++;
            }

        }else{
            $this->logger->info("Groups data not found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        return $checkbox;
    }
    
    /**
     * companiesCheckList
     * 
     * en_us Returns string with companies checkboxes
     * pt_br Retorna string com checkboxes de empresas
     *
     * @return string
     */
    public function companiesCheckList(): string
    {
        $admSrc = new adminServices();

        $ret = $admSrc->_comboCompany();
        
        $checkbox = '';
        $a = sizeof($ret);
        $rowsCol = round($a / 2);
        $i = 1;
        
        if($a > 0){
            $this->logger->info("Groups data got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            foreach ( $ret as $key=>$val ) {
    
                if($i == 1 || $i == ($rowsCol + 1)){$checkbox .= "<div class='col-sm-6'>";}

                $checkbox .= "<div class='mb-1'>
                                <input type='checkbox' class='i-checks' id='checkCompanies-{$val['id']}' name='checkCompanies[]' value='{$val['id']}'>&nbsp;&nbsp;<small class='col-form-label'> {$val['text']} </small>
                            </div>";
                
                if($i == $rowsCol || $i == $a ){$checkbox .= "</div>";}

                $i++;
            }

        }else{
            $this->logger->info("Groups data not found", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        return $checkbox;
    }

    /**
     * ajaxTopics
     * 
     * en_us Returns topics list in HTML to reload combo
     * pt_br Retorna a lista de tópicos em HTML para recarregar o combo
     *
     * @return void
     */
    function ajaxTopics()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $html = "";

        $ret = $this->comboTopic();        
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