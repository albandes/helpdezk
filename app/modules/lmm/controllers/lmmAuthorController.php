<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmAuthor extends lmmCommon {
    
    public function __construct()
     {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmAuthor');
        
        $this->loadModel('author_model');
        $this->dbAuthor = new author_model();

        $this->loadModel('titles_model');
        $this->dbTitles = new titles_model();

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
        $this->_makeNavlmm($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lmm-author-grid.tpl');
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
            $sidx ='name';
        if(!$sord)
            $sord ='ASC';
            
        if ($_POST['_search'] == 'true'){
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbAuthor->getAuthor($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Author. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

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

        $rsBrand =  $this->dbAuthor->getAuthor($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
            $status_fmt = ($rsBrand['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsBrand['data']->fields['idauthor'],
                'author'         => $rsBrand['data']->fields['name'],
                'cutter'         => $rsBrand['data']->fields['cutter'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsBrand['data']->fields['status']

            );
            $rsBrand['data']->MoveNext();
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

        $this->makeScreenAuthor($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmauthor-create.tpl');
    }


    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbAuthor->getAuthor("WHERE idauthor = $lmmID");

        $this->makeScreenAuthor($smarty,$rsBrand['data'],'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('idauthor', $lmmID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmauthor-update.tpl');

    }


    public function viewAuthor()
    {
        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbAuthor->getAuthor("WHERE idauthor = $lmmID");

        $this->makeScreenAuthor($smarty,$rsBrand['data'],'echo');

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmauthor-view.tpl');

    }


    function makeScreenAuthor($objSmarty,$rs,$oper)
    {

        // --- Name ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_author','Informe o nome.');
            else
                $objSmarty->assign('author',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_author','Informe o Nome.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('author',$rs->fields['name']);
        }

         // --- Cutter ---
         if ($oper == 'update') {
            if (empty($rs->fields['cutter']))
                $objSmarty->assign('plh_cutter','Informe o codigo cutter do autor.');
            else
                $objSmarty->assign('cutter',$rs->fields['cutter']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_cutter','Informe o codigo cutter do autor.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('cutter',$rs->fields['cutter']);
        }

        // --- Name ---
        if ($oper == 'update') {
            $objSmarty->assign('checkdefault',$rs->fields['default'] == 1 ? "checked=checked" : "");                
        }

    }



    function createAuthor()
    {
         $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $author = trim($_POST['author']);
        $cutter = trim($_POST['cutter']);
        $status = isset($_POST['citystatus']) ? 0 : 1;

        $this->dbAuthor->BeginTrans();

        $ret = $this->dbAuthor->insertAuthor($author,$cutter,$status);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbAuthor->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbAuthor->CommitTrans();

        echo json_encode($aRet);

    }
    

    function updateAuthor()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $lmmID = $_POST['idauthor'];
        $author = trim($_POST['author']);
        $cutter = trim($_POST['cutter']);

        $this->dbAuthor->BeginTrans();

        $ret = $this->dbAuthor->updateAuthor($lmmID,$author,$cutter);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbAuthor->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbAuthor->CommitTrans();

        echo json_encode($aRet);

    }


    function deleteAuthor()
    {
        $this->protectFormInput();
        
        $lmmID = $_POST['idauthor_modal'];

        $this->dbAuthor->BeginTrans();

        $ret = $this->dbAuthor->deleteAuthor($lmmID);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbAuthor->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $lmmID
        );

        $this->dbAuthor->CommitTrans();

        echo json_encode($aRet);

    }


    function existAuthor(){

        $author = $_POST['author'];

        $where = "WHERE  name = '$author'";

        $check =  $this->dbAuthor->getAuthor($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get author. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function existAuthorUp()
    {
        
        $author =trim($_POST['author']);
        $where = "WHERE  name = '$author'";
        $where .= (isset($_POST['idauthor'])) ? "AND idauthor!= {$_POST['idauthor']}" : "";
       

        $check =  $this->dbAuthor->getAuthor($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get author. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode($this->getLanguageWord('Alert_failure'));

        } else {

            echo json_encode(true);

        }

    }

    function statusAuthor()
    {
        $lmmID = $_POST['authorID'];
        $newStatus = $_POST['newstatus'];

        $ret = $this->dbAuthor->changeStatus($lmmID,$newStatus);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update author status. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "success" => true,
            "BrandID" => $lmmID
        );

        echo json_encode($aRet);

    }

    function checksAuthor(){

        $lmmID = $_POST['authorID'];

        $where = "AND  a.idauthor = $lmmID ";

        $check =  $this->dbTitles->getAuthor($where);
        if (!$check['success']) {
            if($this->log)
                $this->logIt("Can't get titles. {$check['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if ($check['data']->RecordCount() > 0) {
            
            echo json_encode(array('success'=>true));

        } else {

            echo json_encode(array('success'=>false));

        }

    }

    }