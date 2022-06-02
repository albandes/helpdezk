<?php
require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdKnowledgeArea extends acdCommon {
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
        $this->idprogram =  $this->getIdProgramByController('acdKnowledgeArea');
        
        $this->loadModel('acdknowledgearea_model');
        $this->dbKnowledgearea = new acdknowledgearea_model();

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
        $this->_makeNavAcd($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('acd-knowledgearea-grid.tpl');


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
            $sidx ='descricao';
        if(!$sord)
            $sord ='ASC';
            
        if ($_POST['_search'] == 'true'){
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbKnowledgearea->getKnowledgearea($where);
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

        $rsArea =  $this->dbKnowledgearea->getKnowledgearea($where,$order,$limit);

        while (!$rsArea['data']->EOF) {
            $status_fmt = ($rsArea['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsArea['data']->fields['idareaconhecimento'],
                'descricao'          => $rsArea['data']->fields['descricao'],
                'descricaoabrev'          => $rsArea['data']->fields['descricaoabrev'],
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

    //CONTAINERS DAS OPERAÇÕES CREATE, UPDATE VIEW //TABELA: acd_tbareaconhecimento

    /*Métodos para renderização dos templates*/
    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenKnowledgearea($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdknowledgearea-create.tpl');
    }

    public function formUpdate()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $acdID = $this->getParam('acdID');
        $rsArea = $this->dbKnowledgearea->getKnowledgearea("WHERE idareaconhecimento = $acdID");

        $this->makeScreenKnowledgearea($smarty,$rsArea['data'],'update');

        $smarty->assign('token', $token);

        $smarty->assign('hidden_areaID', $acdID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdknowledgearea-update.tpl');

    }

    public function viewKnowledgearea()
    {
        $smarty = $this->retornaSmarty();

        $acdID = $this->getParam('acdID');
        $rsArea = $this->dbKnowledgearea->getKnowledgearea("WHERE idareaconhecimento = $acdID"); 

        $this->makeScreenKnowledgearea($smarty,$rsArea['data'],'echo'); 

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdknowledgearea-view.tpl');

    }

    /*-----------------------------------------------------------------------*/

    /* Métodos para processamento das ações */
    function makeScreenKnowledgearea($objSmarty,$rs,$oper)
    {

        // --- Name e abreviação
        if ($oper == 'update') {
            //Se o campo descricao estiver vazio
            if (empty($rs->fields['descricao']))
                //smarty plh_nome recebe "Informe o nome"
                $objSmarty->assign('plh_informe_nome','Informe o Nome.');
            else
                $objSmarty->assign('area_name',$rs->fields['descricao']);

            //Se o campo descricaoabrev estiver vazio
            if (empty($rs->fields['descricaoabrev']))
                //smarty plh_abrev recebe "informe a abreviação"
                $objSmarty->assign('plh_abrev','Informe a abreviação');
            else
                $objSmarty->assign('area_abrev',$rs->fields['descricaoabrev']);

        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o Nome.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('acdName',$rs->fields['descricao']);
            $objSmarty->assign('abvName',$rs->fields['descricaoabrev']);
        }

    }

    function createKnowledgearea()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $Nome = trim($_POST['areaDesc']);
        $Descabrev = trim($_POST['areaDescAbrev']);

        $this->dbKnowledgearea->BeginTrans();

        $ret = $this->dbKnowledgearea->insertKnowledgearea($Nome, $Descabrev);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Area data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbKnowledgearea->RollbackTrans();
            return false;
        }

    
        $aRet = array(
            "success" => true,
            "areaID" => $ret['id']
        );

        $this->dbKnowledgearea->CommitTrans();

        echo json_encode($aRet);

    }

    function updateKnowledgearea()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $acdID = $_POST['areaID']; 
        $Nome = trim($_POST['areaDesc']);
        $Descabrev = trim($_POST['areaDescAbrev']);

        $this->dbKnowledgearea->BeginTrans();

        $ret = $this->dbKnowledgearea->updateKnowledgearea($acdID,$Nome,$Descabrev);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Area data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbKnowledgearea->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "areaID" => $areaID
        );

        $this->dbKnowledgearea->CommitTrans();

        echo json_encode($aRet);

    }

    public function statusKnowledge(){

        $acdID = $_POST['acdID'];
        $newStatus = $_POST['newStatus'];

        $ret = $this->dbKnowledgearea->statusKnowledge($acdID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Area status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "areaID" => $acdID
        );

        echo json_encode($aRet);
    }

    public function existArea() {
        $this->protectFormInput();

        $areaName = $_POST['areaDesc'];
        $areaAbrev = $_POST['areaDescAbrev'];

        if(isset($_POST['areaDesc'])){

            $where = "WHERE `descricao` LIKE '$areaName'";

            $msg = "acd_exists";

        }else if(isset($_POST['areaDescAbrev'])){

            $where = "WHERE `descricaoabrev` LIKE '$areaAbrev'";

            $msg = "acd_abbrevexists";

        }

        $where .= isset($_POST['areaID']) ? " AND idareaconhecimento != {$_POST['areaID']}" : "";

        $check = $this->dbKnowledgearea->getKnowledgearea($where);
        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord($msg));

        } else {
            echo json_encode(true);
        }
    }


}
