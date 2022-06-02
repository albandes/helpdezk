<?php
require_once(HELPDEZK_PATH . '/app/modules/hur/controllers/hurCommonController.php');

class hurArea extends hurCommon {
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
        $this->idprogram =  $this->getIdProgramByController('hurArea');
        
        $this->loadModel('hurarea_model');
        $this->dbArea = new hurarea_model();

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

        $smarty->display('hur-area-grid.tpl');


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
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbArea->getArea($where);
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

        $rsArea =  $this->dbArea->getArea($where,$order,$limit);

        while (!$rsArea['data']->EOF) {
            $status_fmt = ($rsArea['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsArea['data']->fields['idarea'],
                'description'          => $rsArea['data']->fields['description'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsArea['data']->fields['status']

            );
            $rsArea['data']->MoveNext();
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

        $this->makeScreenArea($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('hurarea-create.tpl');
    }

    public function formUpdate()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $areaID = $this->getParam('areaID');
        $rsArea = $this->dbArea->getArea("WHERE idarea = $areaID");

        $this->makeScreenArea($smarty,$rsArea['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_areaID', $areaID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('hurarea-update.tpl');

    }
    public function viewArea()
    {
        $smarty = $this->retornaSmarty();

        $areaID = $this->getParam('areaID');
        $rsArea = $this->dbArea->getArea("WHERE idarea = $areaID");

        $this->makeScreenArea($smarty,$rsArea['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('hurarea-view.tpl');

    }


    function makeScreenArea($objSmarty,$rs,$oper)
    {

        // --- Name ---
        if ($oper == 'update') {

            if (empty($rs->fields['description']))

                $objSmarty->assign('plh_nome','Nova área');

            else

                $objSmarty->assign('areaName',$rs->fields['description']);

        } elseif ($oper == 'create') {

            $objSmarty->assign('plh_nome','Informe o nome da área');

        } elseif ($oper == 'echo') {

            $objSmarty->assign('areaName',$rs->fields['description']);

        }

    }

    function createArea()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $areaName = strip_tags(trim($_POST['areaName']));

        $this->dbArea->BeginTrans();

        $ret = $this->dbArea->insertArea($areaName);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Area data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbArea->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "areaID" => $ret['id']
        );

        $this->dbArea->CommitTrans();

        echo json_encode($aRet);

    }

    function updateArea()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $areaID = strip_tags($_POST['areaID']);
        $areaName = strip_tags(trim($_POST['areaName']));

        $this->dbArea->BeginTrans();

        $ret = $this->dbArea->updateArea($areaID,$areaName);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Area data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbArea->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "areaID" => $areaID
        );

        $this->dbArea->CommitTrans();

        echo json_encode($aRet);

    }

    public function existArea() {
        $this->protectFormInput();

        $areaName = $_POST['areaName'];
        $areaID = $_POST['areaID'];

        //echo "ID: $areaID";
        //echo "Name: $areaName";

        if(isset($_POST['areaName'])){

            $where = "WHERE `description` LIKE '$areaName'";

            $msg = "hur_areaname";

        }

        //Validação do Update
        $where .= isset($_POST['areaID']) ? " AND idarea != {$_POST['areaID']}" : "";

        $check = $this->dbArea->getArea($where);
        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord($msg));

        } else {
            echo json_encode(true);
        }
    }

    public function statusArea(){

        $areaID = $_POST['areaID'];
        $newStatus = $_POST['newStatus'];

        //echo $areaID, $newStatus;

        $ret = $this->dbArea->statusArea($areaID,$newStatus);

        //print_r($ret); die();

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Area status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "areaID" => $areaID
        );

        echo json_encode($aRet);
    }



}