<?php
require_once(HELPDEZK_PATH . '/app/modules/hur/controllers/hurCommonController.php');

class hurRole extends hurCommon {
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('hurRole');
        
        $this->loadModel('hurrole_model');
        $this->dbRole = new hurrole_model();

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('hur-role-grid.tpl');


    }

    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='nome';
        if(!$sord)
            $sord ='ASC';
            
        if ($_POST['_search'] == 'true'){

            //echo "{$_POST['searchOper']} || {$_POST['searchField']} || {$_POST['searchString']}"; die();

            if($_POST['searchField'] == "rolename"){

                $search_field = "a.description";

            }else if($_POST['searchField'] == "areaname"){

                $search_field = "b.description";

            }

            $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);
        }

        $rsCount = $this->dbRole->getRole($where);
        //print_r($rsCount['data']); die();
        $count = $rsCount['data']->RecordCount();

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";

        $rsRole =  $this->dbRole->getRole($where, $order,$limit);

        //print_r($rsRole); die();

        while (!$rsRole['data']->EOF) {
            $status_fmt = ($rsRole['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'                => $rsRole['data']->fields['idrole'],
                'idarea'            => $rsRole['data']->fields['idarea'],
                'rolename'          => $rsRole['data']->fields['rolename'],
                'areaname'          => $rsRole['data']->fields['areaname'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsRole['data']->fields['status']

            );
            $rsRole['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    
    }

    //FUNCIONALIDADES ESPECÍFICAS DESSE PROGRAMA
    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenRole($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('hurrole-create.tpl');
    }

    public function formUpdate()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $roleID = $this->getParam('roleID');
        $rsRole = $this->dbRole->getRole("AND idrole = $roleID");

        $this->makeScreenRole($smarty,$rsRole['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_roleID', $roleID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('hurrole-update.tpl');

    }
    public function viewRole()
    {
        $smarty = $this->retornaSmarty();

        $roleID = $this->getParam('roleID');
        $rsRole = $this->dbRole->getRole("AND idrole = $roleID");

        $this->makeScreenRole($smarty,$rsRole['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('hurrole-view.tpl');

    }


    function makeScreenRole($objSmarty,$rs,$oper)
    {

        // --- Name ---
        if ($oper == 'update') {

            //The area combo item is pre-selected when the update form is loaded
            $objSmarty->assign('idarea',$rs->fields['idarea']);

            if (empty($rs->fields['rolename']))

                $objSmarty->assign('plh_nome','Novo');

            else

                $objSmarty->assign('roleName',$rs->fields['rolename']);

            //Assign do combo "Area"
            $areas = $this->_comboArea();
            $objSmarty->assign('areaIds', $areas['ids']);
            $objSmarty->assign('areaVals', $areas['values']);

        } elseif ($oper == 'create') {

            //Se o campo cargo estiver vazio
            if (empty($rs->fields['rolename']))
                //smarty plh_course recebe "Informe o curso da turma"
                $objSmarty->assign('plh_nome','Informe o nome do cargo');

            //Assign do combo "Area"
            $areas = $this->_comboArea();
            $objSmarty->assign('areaIds', $areas['ids']);
            $objSmarty->assign('areaVals', $areas['values']);
            $objSmarty->assign('idarea', "X");

        } elseif ($oper == 'echo') {

            $objSmarty->assign('areaName',$rs->fields['areaname']);
            $objSmarty->assign('roleName',$rs->fields['rolename']);

        }

    }

    function createRole()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $roleName = strip_tags(trim($_POST['roleName']));
        $idArea = strip_tags(trim($_POST['cmbRoleArea']));

        $this->dbRole->BeginTrans();

        $ret = $this->dbRole->insertRole($idArea, $roleName);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Role data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbRole->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "roleID" => $ret['id']
        );

        $this->dbRole->CommitTrans();

        echo json_encode($aRet);

    }

    function updateRole()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $roleID = strip_tags($_POST['roleID']);
        $areaID = strip_tags($_POST['cmbRoleArea']);
        $roleName = strip_tags($_POST['roleName']);

        $this->dbRole->BeginTrans();

        $ret = $this->dbRole->updateRole($roleID,$areaID,$roleName);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Role data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbRole->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "roleID" => $roleID
        );

        $this->dbRole->CommitTrans();

        echo json_encode($aRet);

    }

    public function statusRole(){

        $roleID = $_POST['roleID'];
        $newStatus = $_POST['newStatus'];

        $ret = $this->dbRole->statusRole($roleID,$newStatus);

        //print_r($ret); die();

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Role status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "roleID" => $roleID
        );

        echo json_encode($aRet);
    }

    //FAZER VALIDAÇÃO
    public function existRole() {
        $this->protectFormInput();

        $roleName = $_POST['roleName'];
        //$areaID = $_POST['areaID'];
        $roleID = $_POST['roleID'];

        //echo $areaID;

        //Validação do create
        $where = "AND a.description = '$roleName'"; //AND a.idarea = $areaID

        //Validação do Update
        $where .= isset($_POST['roleID']) ? " AND idrole != {$_POST['roleID']}" : "";

        $check = $this->dbRole->getRole($where);
        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord("hur_roleexist"));

        } else {
            echo json_encode(true);
        }
    }



}