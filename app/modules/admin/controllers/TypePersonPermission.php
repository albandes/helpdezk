<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\dao\mysql\programDAO;
use App\modules\admin\dao\mysql\permissionDAO;
use App\modules\admin\dao\mysql\userTypeDAO;

use App\modules\admin\models\mysql\personModel;
use App\modules\admin\models\mysql\programModel;
use App\modules\admin\models\mysql\permissionModel;
use App\modules\admin\models\mysql\userTypeModel;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\src\cpfServices;

class TypePersonPermission extends Controller
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

        $params = $this->makeScreenTypePersonPerms();
		
		$this->view('admin','type-person-permission',$params);
    }

    /**
	 *  en_us Configure program screens
	 *  pt_br Configura as telas do programa
     * 
	 */
    public function makeScreenTypePersonPerms($option='idx',$obj=null)
    {
        $adminSrc = new adminServices();
        $hdkSrc = new hdkServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();

        // -- User's permissions --
        $params['aPermissions'] = $this->aPermissions;

        // -- Search action --
        if($option == 'idx'){
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts();
            $params['cmbFilters'] = $this->comboUserTypePermFilters();
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }

        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalError'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-error.latte';

        /* if($option=='permission'){
            echo "olaaaa";
        } */
      
        return $params;
    }

    /**
     * en_us Returns programs data to grid
	 * pt_br Retorna os dados dos programas para o grid
     *
     * @return void
     */
    public function jsonGrid()
    {
        $permissionDAO = new permissionDAO(); 

        $where = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];

            switch($filterIndx){                
                case "name":
                    $where .= ((empty($where)) ? "WHERE (" : " AND (") . $this->appSrc->_formatGridOperation($filterOp,"tbp.name",$filterValue) ."OR " . $this->appSrc->_formatGridOperation($filterOp,"pvoc.key_value",$filterValue) . ")";
                    break;
                
                case "module": 
                    $where .= ((empty($where)) ? "WHERE (" : " AND (") . $this->appSrc->_formatGridOperation($filterOp,"tbm.name",$filterValue) ."OR " . $this->appSrc->_formatGridOperation($filterOp,"mvoc.key_value",$filterValue) . ")";
                    break;
            }

                      
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= ((empty($where)) ? "WHERE " : " AND ") . "((tbp.name LIKE '%{$quickValue}%' OR pvoc.key_value LIKE '%{$quickValue}%') OR (tbm.name LIKE '%{$quickValue}%' OR mvoc.key_value LIKE '%{$quickValue}%'))";
        }

        if(!isset($_POST["allRecords"]) || (isset($_POST["allRecords"]) && $_POST["allRecords"] != "true"))
        {
            $where .= ((empty($where)) ? "WHERE " : " AND ") . " tbp.status = 'A' ";
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        switch($sortIndx){
            case "name":
                $sortIndx = "name_fmt";
                break;
            case "module":
                $sortIndx = "module_fmt";
                break;
            case "category":
                $sortIndx = "category_fmt";
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
        $countProgram = $permissionDAO->countPermissions($where);
        if($countProgram['status']){
            $total_Records = $countProgram['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $programs = $permissionDAO->queryPermissions($where,$group,$order,$limit);
       
        if($programs['status']){     
            $programsObj = $programs['push']['object']->getGridList();     
            
            foreach($programsObj as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
                $flgNew = ($v['allow']) ? "<span class='label label-info'>{$this->translator->translate('New')}</span>" : "";

                $data[] = array(
                    'idprogram'     => $v['idprogram'],
                    'name'          => $v['name_fmt'],
                    'status'        => $status_fmt,
                    'status_val'    => $v['status'],
                    'controller'    => $v['controller'],
                    'module'        => $v['module_fmt'],
                    'category'      => $v['category_fmt'],
                    'flagNew'       => $flgNew
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
    public function comboUserTypePermFilters(): array
    {
        $aRet = array(
            array("id" => 'name',"text"=>$this->translator->translate('Name')),
            array("id" => 'module',"text"=>$this->translator->translate('Module'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders manage permissions screen
     * pt_br Renderiza a tela de gerenciamento de permissiões
     */
    public function managePermissions($programId=null)
    {
        $programDAO = new programDAO();
        $programModel = new programModel();
        $programModel->setProgramId($programId);

        $ret = $programDAO->getProgram($programModel);
        
        $params = $this->makeScreenTypePersonPerms('permission',$ret['push']['object']);
        $params['programId'] = $programId;
        $params['lblProgramName'] = $ret['push']['object']->getName();
      
        $this->view('admin','type-person-manage-permission',$params);
    }
    
    /**
     * en_us Returns person's permissions to display in grid
     * pt_br Retorna as permissões da pessoa para exibir no grid
     *
     * @return void
     */
    public function jsonGridPermissions()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $userTypeDAO = new userTypeDAO(); 

        $programId = $_POST['programId'];
        $where = "";
        $group = "";
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= " WHERE (pipeLatinToUtf8(a.name) LIKE pipeLatinToUtf8('%{$quickValue}%') OR pipeLatinToUtf8(b.key_value) LIKE pipeLatinToUtf8('%{$quickValue}%'))";
        }

        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = isset($pq_sort[0]->dataIndx) ? $pq_sort[0]->dataIndx : "name";
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";

        switch($sortIndx){
            case "name":
                $sortIndx = "name_fmt"; 
                break;
            default:
                $sortIndx = "{$sortIndx}";
                break;
        }

        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countUserTypes = $userTypeDAO->countUserType($where); 
        if($countUserTypes['status']){
            $total_Records = $countUserTypes['push']['object']->getTotalRows();
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $userTypes = $userTypeDAO->queryUserType($where,$group,$order,$limit);
        
        if($userTypes['status']){
            $aUserTypes = $userTypes['push']['object']->getGridList();

            foreach($aUserTypes as $k=>$v) {
                $retPermissions = $this->makePermissionOptions($v['idtypeperson'],$programId);

                $data[] = array(
                    'idtypeperson'  => $v['idtypeperson'],
                    'name'          => (!is_null($v['name_fmt']) && !empty($v['name_fmt'])) ? $v['name_fmt'] : $v['name'],
                    'access'        => ($retPermissions['success']) ? $retPermissions['access'] : "",
                    'new'           => ($retPermissions['success']) ? $retPermissions['new'] : "",
                    'edit'          => ($retPermissions['success']) ? $retPermissions['edit'] : "",
                    'delete'        => ($retPermissions['success']) ? $retPermissions['delete'] : "",
                    'export'        => ($retPermissions['success']) ? $retPermissions['export'] : "",
                    'email'         => ($retPermissions['success']) ? $retPermissions['email'] : "",
                    'sms'           => ($retPermissions['success']) ? $retPermissions['sms'] : "",
                    'idprogram'      => $$programId  
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
     * en_us Make a list of permission by program in HTML
     * pt_br Cria uma lista de permissões por programa em HTML
     *
     * @param  int $programId
     * @return array
     */
    public function makePermissionOptions($userTypeId,$programId): array
    {
        $permissionDAO = new permissionDAO();
        $permissionModel = new permissionModel();
        $permissionModel->setProgramId($programId)
                        ->setPersonTypeId($userTypeId);

        $ret = $permissionDAO->fetchDefaultPermissionsByProgram($permissionModel);
        if(!$ret['status']){
            $this->logger->error("Could not get default permissions", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return array("success"=>false);
        }
        $aDefPermissions = $ret['push']['object']->getDefaultPermissionList();

        foreach($aDefPermissions as $k=>$v){
            $aDefPerm[$v['idaccesstype']] = $v['idaccesstype'];
        }

        for($accessType = 1;$accessType <=7;$accessType++){
            $ret['push']['object']->setAccessTypeId($accessType);
            
            $retUserPermission = $permissionDAO->getUserTypePermission($ret['push']['object']);
            if(!$retUserPermission['status']){
                $this->logger->error("Could not get user's permissions", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return array("success"=>false);
            }

            $allow = $retUserPermission['push']['object']->getAllow();
            switch ($accessType) {
                case 1 :
                    $disabled = (!$aDefPerm[1]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $access = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
                case 2 :
                    $disabled = (!$aDefPerm[2]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $new = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
                case 3 :
                    $disabled = (!$aDefPerm[3]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $edit = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
                case 4 :
                    $disabled = (!$aDefPerm[4]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $delete = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
                case 5 :
                    $disabled = (!$aDefPerm[5]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $export = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
                case 6 :
                    $disabled = (!$aDefPerm[6]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $email = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
                case 7 :
                    $disabled = (!$aDefPerm[7]) ? "disabled='disabled'" : "";
                    $checked = ($allow == 'Y') ? "checked='checked'" : "";
                    $sms = "<input type='checkbox' $disabled $checked id='{$accessType}-{$userTypeId}-{$programId}' name='{$accessType}-{$userTypeId}-{$programId}' onchange='grantPermission(this.id,{$programId},{$accessType},{$userTypeId});'>";
                    break;
            }
        }

        $aRet = array(
            "success" => true,
            "access"        => $access,
            "new"           => $new,
            "edit"          => $edit,
            "delete"        => $delete,
            "export"        => $export,
            "email"         => $email,
            "sms"           => $sms,
        );

        return $aRet;
    }

    /**
     * en_us Changes person's status
     * pt_br Muda o status da pessoa
     *
     * @return void
     */
    function grantPermission()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $permissionDAO = new permissionDAO();
        $permissionModel = new permissionModel();

        //Setting up the model
        $permissionModel->setProgramId($_POST['programId'])
                        ->setPersonTypeId($_POST['userTypeId'])
                        ->setAccessTypeId($_POST['accessTypeId'])
                        ->setAllow($_POST['allow']);
        
        $upd = $permissionDAO->grantUserTypePermission($permissionModel);
        if(!$upd['status']){
            $this->logger->error("Could not grant permission.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $upd['push']['message']]);
            return false;
        }

        $logMsg = ($_POST['allow'] == 'Y') ? "granted" : "removed";
        $this->logger->info("Permission for program # {$_POST['programId']} and user type # {$_POST['userTypeId']} was $logMsg.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    /**
     * en_us Shows the attendant's groups
     * pt_br Mostra os grupos do atendente
     *
     * @return void
     */
    function modalAttendantGroups()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ret = $this->makeAttendantGroupHtml($_POST['personId']);
        $st = ($ret) ?  true : false;
        $html = ($ret) ?  $ret : "";

        $aRet = array(
            "success" => true,
            "html"    => $html
        );

        echo json_encode($aRet);
    }

    /**
     * en_us Shows the attendant's groups
     * pt_br Mostra os grupos do atendente
     *
     * @return string
     */
    public function makeAttendantGroupHtml($personId)
    {
        $personDAO = new personDAO();
        $personModel = new personModel();

        //Setting up the model
        $personModel->setIdPerson($personId);
        
        $ret = $personDAO->fetchAttendantGroups($personModel);
        if(!$ret['status']){
            return false;
        }

        $aGroups =  $ret['push']['object']->getPersonGroupsList();
        $html = "";

        if(count($aGroups) > 0){
            foreach($aGroups as $k=>$v){
                $html .= "<tr>
                            <td>{$v['company_name']}</td>
                            <td>
                                {$v['name']}
                                <input type='hidden' name='admAttGrps[]' id='admAttGrps_{$v['idgroup']}' value='{$v['idgroup']}'>
                            </td>
                            <td><a href='javascript:;' onclick='removeGroup({$_POST['personId']},{$v['idgroup']})' class='btn btn-danger'><i class='fa fa-times'></i></a></td>
                        </tr>"; 
            }
        }

        return $html;
    }

    


}