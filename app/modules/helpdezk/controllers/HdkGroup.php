<?php

use App\core\Controller;
use App\modules\helpdezk\dao\mysql\groupDAO;
use App\modules\helpdezk\dao\mysql\hdkServiceDAO;

use App\modules\helpdezk\models\mysql\groupModel;
use App\modules\helpdezk\models\mysql\hdkServiceModel;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;

class hdkGroup extends Controller
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

        $params = $this->makeScreenHdkGroup();
		
		$this->view('helpdezk','group',$params);
    }
    
    /**
     * makeScreenHdkGroup
     *
     * @param  mixed $option
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenHdkGroup($option='idx',$obj=null)
    {
        $admSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $admSrc->_makeNavAdm($params);
        
        // -- Area --
        $params['cmbArea'] = $hdkSrc->_comboArea();         

        // -- Search action --
        if($option=='idx'){
          $params['cmbFilters'] = $this->comboHdkGroupFilters();
          $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts($params['cmbFilters'][0]['searchOpt']);
          $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte'; //subir imagem
        
        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        if($option == 'idx'){
            // -- Groups dropdown list --
            $params['cmbGroup'] = $admSrc->_comboGroup();

            // -- Area dropdown list --
            $params['cmbArea'] = $hdkSrc->_comboArea();
        }

        if($option != 'idx'){
            // -- Companies dropdown list --
            $params['cmbCompany'] = $admSrc->_comboCompany();
        }
        
        if($option=='upd'){
            $params['groupId'] = $obj->getIdGroup();
            $params['personId'] = $obj->getPersonId();
            $params['companySelected'] = $obj->getIdCompany();
            $params['groupName'] = $obj->getGroupName();
            $params['groupLevel'] = $obj->getGroupLevel();
            $params['onlyForward'] = $obj->getIsRepassOnly();
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
        $groupDAO = new groupDAO(); 

        $where = "";
        $group = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch ($filterIndx) {
                case 'name':
                    $filterIndx = "tbp.name";
                    break;
                
                case 'company':
                    $filterIndx = "tbp2.name";
                    break;

                case 'level':
                    $filterIndx = "tbg.level";
                    break;
            }

            $where .=  " AND " . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " AND  (pipeLatinToUtf8(tbp.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(tbp2.name) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= " AND  tbg.status = 'A' ";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "`name`";
        
        switch($sortIndx){
            case "name":
                $sortIndx = "`name`";
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
        $countGroup = $groupDAO->countGroups($where); 
        if($countGroup['status']){
            $total_Records = $countGroup['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $retGroup = $groupDAO->queryGroups($where,$group,$order,$limit);
        
        if($retGroup['status']){     
            $aGroups = $retGroup['push']['object']->getGridList();     
            
            foreach($aGroups as $k=>$v) {
                $statusFmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

                $data[] = array(
                    'idgroup'       => $v['idgroup'],
                    'name'          => $v['name'],
                    'level'         => $v['level'],
                    'company'       => $v['company'],
                    'status'        => $statusFmt,
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
     * comboHdkGroupFilters
     * 
     * en_us Renders the status add screen
     * pt_br Renderiza o template da tela de novo cadastro de status
     *
     * @return array
     */
    public function comboHdkGroupFilters(): array
    {
        $aRet = array(            
            array("id" => 'name',"text"=>$this->translator->translate('Name'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'company',"text"=>$this->translator->translate('Company'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en')),
            array("id" => 'level',"text"=>$this->translator->translate('Attend_level'),"searchOpt"=>array('eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'))
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

        $params = $this->makeScreenHdkGroup('add');

        $this->view('helpdezk','group-create',$params);
    }

    /**
     * createGroup
     * 
     * en_us Write the group information to the DB
     * pt_br Grava no BD as informações do grupo
     *
     * @return void
     */
    public function createGroup()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        } 
        
        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();
   
        $groupDTO->setIdCompany(trim(strip_tags($_POST['cmbCompany'])))
                 ->setGroupName(trim(strip_tags($_POST['groupName'])))
                 ->setGroupLevel(trim(strip_tags($_POST['groupLevel'])))
                 ->setIsRepassOnly((isset($_POST['onlyForward'])) ? "Y" : "N");

        $ins = $groupDAO->saveGroup($groupDTO);
        if($ins['status']){
            $this->logger->info("Group data save successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $groupId = $ins['push']['object']->getIdGroup();
            $groupName = $ins['push']['object']->getGroupName();
            $groupLevel = $ins['push']['object']->getGroupLevel();
            
        }else{
            $this->logger->error("Can't save group's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);

            $st = false;
            $msg = $ins['push']['message'];
            $groupId = "";
            $groupName = "";
            $groupLevel = "";
        }   
        
        $aRet = array(
            "success"       => $st,
            "message"       => $msg,
            "groupId"    => $groupId,
            "groupName"  => $groupName,
            "groupLevel"  => $groupLevel
        );

        echo json_encode($aRet);
    }
    
    /**
     * formUpdate
     * 
     * en_us Renders the group update screen
     * pt_br Renderiza o template da atualização do cadastro de grupo
     *
     * @param  mixed $groupId
     * @return void
     */
    public function formUpdate($groupId=null)
    {
        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();
        $groupDTO->setIdGroup($groupId); 
        
        $ret = $groupDAO->getGroup($groupDTO); 

        $params = $this->makeScreenHdkGroup('upd',$ret['push']['object']);
        
        $this->view('helpdezk','group-update',$params);
    }
    
    /**
     * updateGroup
     * 
     * en_us Updates the group information in the DB
     * pt_br Atualiza no BD as informações do grupo
     *
     * @return void
     */
    public function updateGroup()
    { 
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();

        $groupDTO->setIdGroup($_POST['groupId'])
                 ->setPersonId(trim(strip_tags($_POST['personId'])))
                 ->setIdCompany(trim(strip_tags($_POST['cmbCompany'])))
                 ->setGroupName(trim(strip_tags($_POST['groupName'])))
                 ->setGroupLevel(trim(strip_tags($_POST['groupLevel'])))
                 ->setIsRepassOnly((isset($_POST['onlyForward'])) ? "Y" : "N");             
        
        $upd = $groupDAO->saveUpdateGroup($groupDTO);
        if($upd['status']){
            $this->logger->info("Group # {$upd['push']['object']->getIdGroup()} data update successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = "";
            $groupId = $upd['push']['object']->getIdGroup();
        }else{
            $this->logger->error("Can't update group # {$upd['push']['object']->getIdGroup()} data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);

            $st = false;
            $msg = $upd['push']['message'];
            $groupId = "";
        }           
       
        $aRet = array(
            "success"       => $st,
            "groupId"    => $groupId
        );        

        echo json_encode($aRet);
    }
    
    /**
     * changeStatus
     * 
     * en_us Changes group state
     * pt_br Muda o estado do grupo
     *
     * @return void
     */
    function changeStatus()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();

        //Setting up the model
        $groupDTO->setIdGroup($_POST['groupId'])
                 ->setStatus($_POST['newStatus']);
        
        $upd = $groupDAO->updateGroupStatus($groupDTO);
        if(!$upd['status']){
            $this->logger->error("Can't update hdkGroup # {$upd['push']['object']->getsetStatusId()} status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);
    }

    /**
     * checkExist
     * 
     * en_us Check if the group has already been registered before
     * pt_br Verifica se o grupo já foi cadastrado anteriormente
     *
     * @return void
     */
    function checkExist(){
        
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $groupDAO = new groupDAO();
        $groupName = trim(strip_tags($_POST['groupName']));

        $where = " AND tbg.idcustomer = {$_POST['companyId']} AND pipeLatinToUtf8(tbp.name) = pipeLatinToUtf8('$groupName')"; 
        $where .= (isset($_POST['groupId'])) ? " AND tbg.idgroup != {$_POST['groupId']}" : "";        

        $check =  $groupDAO->queryGroups($where);
        if(!$check['status']){
            $this->logger->error("Can't check if group: {$groupName} exists. Company Id: {$_POST['companyId']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            return false;
        }
        
        $aCheck = $check['push']['object']->getGridList();
        
        if(count($aCheck) > 0){
            echo json_encode($this->translator->translate('group_already_exists'));
        }else{
            echo json_encode(true);
        }

    }
    
    /**
     * loadGroupAttendants
     * 
     * en_us Returns group's attendants list
     * pt_br Retorna a lista de atendentes do grupo
     *
     * @return void
     */
    public function loadGroupAttendants()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();

        $html = "";
        $groupId = trim(strip_tags($_POST['groupId']));

        if(empty($groupId) || $groupId <= 0){
            echo json_encode(array("success"=> false,"message"=>$this->translator->translate('Select_group'),"html"=>""));
            exit;
        }

        $groupDTO->setIdGroup($groupId);

        $ret = $groupDAO->fetchGroupUsers($groupDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get group # {$_POST['groupId']} attendants.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Group # {$_POST['groupId']} attendants got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $aAttendants = $ret['push']['object']->getGridList();
            if(sizeof($aAttendants) > 0){
                foreach($aAttendants as $key=>$val){
                    $html .= "<tr>
                                <td class='text-start' style='vertical-align:middle;'>
                                    {$val['operator_name']}
                                </td>
                                <td>
                                    <button type='button' class='btn btn-danger btn-remove' id='remove{$val['idperson']}' onclick='removeGroupAttendant(this.id,{$val['idperson']},{$groupId})'><i class='fa fa-user-times'></i></button>
                                </td>
                            </tr>";
                }
        
                $st = true;
                $msg = "";
            }else{
                $st = false;
                $msg = $this->translator->translate('group_no_attendant');
            }
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
            "html"      => $html
        );        

        echo json_encode($aRet);

    }
        
    /**
     * searchAttendant
     * 
     * en_us Returns attendants list found by keyword
     * pt_br Retorna a lista de atendentes encontrados pela palavra-chave
     *
     * @return void
     */
    function searchAttendant()
    {
        $hdkSrc = new hdkServices();
        
        echo json_encode($hdkSrc->_searchAttendant($_POST['keyword']));
    }
    
    /**
     * addGroupAttendant
     * 
     * en_us Links the attendant to the group
     * pt_br Vincula o atendente ao grupo
     *
     * @return void
     */
    public function addGroupAttendant()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();

        $groupId = trim(strip_tags($_POST['groupId']));
        $attendantId = trim(strip_tags($_POST['attendantId']));

        if(empty($groupId) || $groupId <= 0){
            echo json_encode(array("success"=> false,"message"=>$this->translator->translate('Select_group')));
            exit;
        }

        $groupDTO->setIdGroup($groupId)
                 ->setIdUser($attendantId);

        //checks if attendant is in group
        $check = $groupDAO->checkAttendantInGroup($groupDTO);
        if(!$check['status']){
            $this->logger->error("Can't check group # {$groupId}, attendant # {$attendantId}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $check['push']['message']]);
            echo json_encode(array("success"=> false,"message"=>$this->translator->translate('generic_error_msg')));
            exit;
        }else{
            $this->logger->info("Group # {$groupId}, attendant # {$attendantId} checked successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            if($check['push']['object']->getInGroupFlag() > 0){
                echo json_encode(array("success"=> false,"message"=>$this->translator->translate('user_in_group')));
                exit;
            }
        }
        
        //links attendant with group
        $ins = $groupDAO->insertGroupAttendant($groupDTO);
        if(!$ins['status']){
            $this->logger->error("Can't link group # {$groupId} with attendant # {$attendantId}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Group # {$groupId} and attendant # {$attendantId} linked successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = $this->translator->translate('attendant_linked_success');
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );        

        echo json_encode($aRet);

    }
    
    /**
     * removeGroupAttendant
     * 
     * en_us Remove links between attendant and group
     * pt_br /remove o víncula entre atendente e grupo
     *
     * @return void
     */
    public function removeGroupAttendant()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $groupDAO = new groupDAO();
        $groupDTO = new groupModel();

        $groupId = trim(strip_tags($_POST['groupId']));
        $attendantId = trim(strip_tags($_POST['attendantId']));

        if(empty($groupId) || $groupId <= 0){
            echo json_encode(array("success"=> false,"message"=>$this->translator->translate('Select_group')));
            exit;
        }

        $groupDTO->setIdGroup($groupId)
                 ->setIdUser($attendantId);
                 
        //links attendant with group
        $ins = $groupDAO->deleteGroupAttendant($groupDTO);
        if(!$ins['status']){
            $this->logger->error("Can't remove link group # {$groupId} with attendant # {$attendantId}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ins['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Link between group # {$groupId} and attendant # {$attendantId} removed successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $st = true;
            $msg = $this->translator->translate('attendant_removed_success');
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg
        );        

        echo json_encode($aRet);

    }

    /**
     * loadServiceGroups
     * 
     * en_us Returns service's groups list
     * pt_br Retorna a lista de grupos do serviço
     *
     * @return void
     */
    public function loadServiceGroups()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $serviceDAO = new hdkServiceDAO();
        $serviceDTO = new hdkServiceModel();

        $html = "";
        $serviceId = trim(strip_tags($_POST['serviceId']));

        $serviceDTO->setIdService($serviceId);

        $ret = $serviceDAO->fetchServiceGroup($serviceDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get service # {$serviceId} groups.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            $st = false;
            $msg = $this->translator->translate('generic_error_msg');
        }else{
            $this->logger->info("Service # {$serviceId} groups got successfully", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

            $aGroups = $ret['push']['object']->getGroupList();
            if(sizeof($aGroups) > 0){
                foreach($aGroups as $key=>$val){
                    $html .= "<li class='list-group-item'>({$val['level']}) {$val['group_name']}</li>";
                }
        
                $st = true;
                $msg = "";
            }else{
                $st = false;
                $msg = $this->translator->translate('service_no_group');
            }
        }

        $aRet = array(
            "success"   => $st,
            "message"   => $msg,
            "html"      => $html
        );        

        echo json_encode($aRet);
    }
}