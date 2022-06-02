<?php
require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdSubject extends acdCommon {

    /**
     * Create an model instance, check session time
     * Calls the common constructor method
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
        $this->idprogram =  $this->getIdProgramByController('acdSubject');
        
        $this->loadModel('acdsubject_model');
        $this->dbSubject = new acdsubject_model();

    }

        
    /**
     * Index
     * Processing of basic smarty templates
     *
     * @return void
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
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

        $smarty->display('acd-subject-grid.tpl');
        
    }

    /**
     * jsonGrid
     * Grid template processing
     *
     * @return json some grid data in an array
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
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

            //echo $_POST['searchOper'],$_POST['searchField'],$_POST['searchString']; die();

            if($_POST['searchField'] == "nome"){

                $search_field = $_POST['searchField'];

            }else if($_POST['searchField'] == "idareaconhecimento"){

                $search_field = "b.descricao";

            }
            
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$search_field,$_POST['searchString']);
        }

        $rsCount = $this->dbSubject->getSubject($where);
        //print_r($rsCount); die();
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

        $rsSub =  $this->dbSubject->getSubject($where,$order,$limit);

        while (!$rsSub['data']->EOF) {
            $status_fmt = ($rsSub['data']->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsSub['data']->fields['iddisciplina'],
                'nome'          => $rsSub['data']->fields['nome'],
                'sigla'          => $rsSub['data']->fields['sigla'],
                'idareaconhecimento'          => $rsSub['data']->fields['area'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsSub['data']->fields['status']
            );
            $rsSub['data']->MoveNext();
        }

        //data to form the grid
        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        //Sending data to Ajax
        echo json_encode($data);

    }
    
    /**
     * formCreate
     * Processing of the insertion / registration form template
     *
     * @return void
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

        $rsSub = $this->dbSubject->getSubject();

        $this->makeScreenSubject($smarty,$arrKeyVal,'create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdsubject-create.tpl');
    }
    
    /**
     * formUpdate
     * Processing of the update form template
     *
     * @return void
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function formUpdate()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $subID = $this->getParam('subID');
        $rsSub = $this->dbSubject->getSubject("WHERE iddisciplina = $subID");

        $this->makeScreenSubject($smarty,$rsSub['data'],'update');

        $smarty->assign('token', $token);

        $smarty->assign('hidden_subID', $subID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdsubject-update.tpl');

    }
    
    /**
     * viewSubject
     * Processing of the view form template
     *
     * @return void
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function viewSubject()
    {

        $smarty = $this->retornaSmarty();
        $subID = $this->getParam('subID');
        
        $rsSub = $this->dbSubject->getSubject("WHERE iddisciplina = $subID"); 

        //print_r($rsSub); die();

        $this->makeScreenSubject($smarty,$rsSub['data'],'echo'); 

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('acdsubject-view.tpl');

    }

        
    /**
     * makeScreenSubject
     * Is called by the previous methods (formCreate, formUpdate and viewSubject), receiving some data of them
     * This method assigns values ​​to smarty variables
     * 
     * @param  object $objSmarty
     * @param  array $rs
     * @param  string $oper
     * 
     * @return void
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    function makeScreenSubject($objSmarty,$rs,$oper)
    {

        // --- Placeholder or input value
        if ($oper == 'update') {

            //The course combo item is pre-selected when the update form is loaded
            $objSmarty->assign('idArea',$rs->fields['idareaconhecimento']);

            if (empty($rs->fields['nome']))
                //smarty plh_nome recebe "Informe o nome"
                $objSmarty->assign('plh_nome','Informe o nome');
            else
                $objSmarty->assign('subject_name',$rs->fields['nome']);

            if (empty($rs->fields['sigla']))
                //smarty plh_nome recebe "Informe o nome"
                $objSmarty->assign('plh_sigla','Informe a sigla');
            else
                $objSmarty->assign('subject_abrev',$rs->fields['sigla']);

            //Assign do combo "area"
            $areas = $this->_comboArea();
            $objSmarty->assign('areasIds', $areas['ids']);
            $objSmarty->assign('areasVals',$areas['values']);

        } elseif ($oper == 'create') {

            if (empty($rs->fields['nome']))
                //smarty plh_nome recebe "Informe o nome"
                $objSmarty->assign('plh_nome','Informe o nome');
            else
                $objSmarty->assign('subject_name',$rs->fields['nome']);

            if (empty($rs->fields['sigla']))
                //smarty plh_nome recebe "Informe o nome"
                $objSmarty->assign('plh_sigla','Informe a sigla');
            else
                $objSmarty->assign('subject_abrev',$rs->fields['sigla']);

            //Assign do combo "area"
            $areas = $this->_comboArea();
            $objSmarty->assign('areasIds', $areas['ids']);
            $objSmarty->assign('areasVals', $areas['values']);
            
        } elseif ($oper == 'echo') {
            //Assign var smarty do template view

            $objSmarty->assign('subjectName',$rs->fields['nome']);
            $objSmarty->assign('subjectSigla',$rs->fields['sigla']);
            $objSmarty->assign('subjectArea',$rs->fields['area']);
        }

    }

        
    /**
     * createSubject
     * Method receives, from Ajax, the data of the insertion / registration form
     * This method communicates with the model to insert the data
     * 
     * @return mixed returns a confirmation array or false
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    function createSubject()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        // Serialized form data
        $nome = strip_tags(trim($_POST['subjectName']));
        $sigla = strip_tags(trim($_POST['subjectAbrev']));
        $idarea = strip_tags(trim($_POST['cmbSubArea']));

        $this->dbSubject->BeginTrans();

        $ret = $this->dbSubject->insertSubject($nome,$idarea, $sigla);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert subject data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbSubject->RollbackTrans();
            return false;
        }

    
        $aRet = array(
            "success" => true,
            "subID" => $ret['id']
        );

        $this->dbSubject->CommitTrans();

        //Sending data to Ajax
        echo json_encode($aRet);

    }
    
    /**
     * updateSubject
     * Method receives, from Ajax, the data of the update form
     * This method communicates with the model to update the data
     * 
     * @return mixed returns a confirmation response array, with the record id changed, or false
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    function updateSubject()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        // Serialized form data
        $subID = strip_tags($_POST['subID']); 
        $nome = strip_tags(trim($_POST['subjectName']));
        $sigla = strip_tags(trim($_POST['subjectAbrev']));
        $area = strip_tags(trim($_POST['cmbSubArea']));

        $this->dbSubject->BeginTrans();

        $ret = $this->dbSubject->updateSubject($subID,$nome,$sigla,$area);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Subject data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbSubject->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "subID" => $subID
        );

        $this->dbSubject->CommitTrans();

        //Sending data to Ajax
        echo json_encode($aRet);

    }

    public function statusSubject(){

        $subjectID = $_POST['subID'];
        $newStatus = $_POST['newStatus'];

        //echo $subjectID, $newStatus; 

        $ret = $this->dbSubject->statusSubject($subjectID,$newStatus);

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

        
    /**
     * existSubject
     * This method is used to check if a given data already exists, being called from the jquery validate function
     * It serves to prevent the registration of the same data
     * 
     * @return json returns a message confirming the previous existence of the data, or false
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function existSubject() {
        $this->protectFormInput();

        //data sent via ajax, using the validate function
        $Sname = $_POST['subjectName'];
        $Sabrev = $_POST['subjectAbrev'];
        //$subID = $_POST['subID'];

        if(isset($_POST['subjectName'])){

            $where = "WHERE `nome` LIKE '{$Sname}'";

            $msg = "acd_exists";

        }else if(isset($_POST['subjectAbrev'])){

            $where = "WHERE `sigla` LIKE '{$Sabrev}'";

            $msg = "acd_abbrevexists";
            
        }

        $where .= isset($_POST['subID']) ? " AND iddisciplina <> {$_POST['subID']}" : "";
        

        $check = $this->dbSubject->getSubject($where);
        if ($check['data']->RecordCount() > 0) {
            //Send message that it already exists
            echo json_encode($this->getLanguageWord($msg));
        } else {
            //Is sent true if it does not exist
            echo json_encode(true);
        }
    }


}
