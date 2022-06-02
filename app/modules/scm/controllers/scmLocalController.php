<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmLocal extends scmCommon
{
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

        $this->idPerson = $this->_companyDefault;

        $this->modulename = 'suprimentos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('scmLocal');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('scm-local-grid.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='nome';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'nome') $searchField = 'nome';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumLocais();

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
        //

        $rsLocal = $this->_getLocal($where,$order,null,$limit);

        while (!$rsLocal->EOF) {
            $status_fmt = ($rsLocal->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsLocal->fields['idlocal'],
                'nome'          => $rsLocal->fields['nome'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsLocal->fields['status']

            );
            $rsLocal->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateLocal()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenLocal($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-local-create.tpl');
    }

    public function formUpdateLocal()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idLocal = $this->getParam('idlocal');
        $rsLocal = $this->_getLocal("where idlocal = $idLocal") ;

        $this->makeScreenLocal($smarty,$rsLocal,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idlocal', $idLocal);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-local-update.tpl');

    }

    public function echoLocal()
    {
        $smarty = $this->retornaSmarty();

        $idLocal = $this->getParam('idlocal');
        $rsLocal = $this->_getLocal("where idlocal = $idLocal") ;

        $this->makeScreenLocal($smarty,$rsLocal,'echo');
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-local-echo.tpl');
    }

    function makeScreenLocal($objSmarty,$rs,$oper)
    {

        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('plh_nome','Informe o local.');
            else
                $objSmarty->assign('nome',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o local.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nome',$rs->fields['nome']);
        }

    }

    function createLocal()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('local_model');
        $dbLocal = new local_model();

        $dbLocal->BeginTrans();

        $ret = $dbLocal->insertLocal($_POST['nome']);

        if (!$ret) {
            $dbLocal->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Local  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idLocal = $ret ;

        $aRet = array(
            "idlocal" => $idLocal,
            "nome" => $_POST['nome']
        );

        $dbLocal->CommitTrans();

        echo json_encode($aRet);

    }

    function updateLocal()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idLocal = $this->getParam('idlocal');

        $this->loadModel('local_model');
        $dbLocal = new local_model();

        $dbLocal->BeginTrans();

        $ret = $dbLocal->updateLocal($idLocal,$_POST['nome']);
        if (!$ret) {
            $dbLocal->RollbackTrans();
            if($this->log)
                $this->logIt('Update Local - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idlocal" => $idLocal,
            "status"   => 'OK'
        );

        $dbLocal->CommitTrans();

        echo json_encode($aRet);


    }

    function statusLocal()
    {
        $idlocal = $this->getParam('idlocal');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('local_model');
        $dbLocal = new local_model();

        $ret = $dbLocal->changeStatus($idlocal,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Local Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idlocal" => $idlocal,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function buscaNomeLocal()
    {
        $nomeLocal = $_POST['nome'];
        $idlocal = $_REQUEST['idlocal'];

        $this->loadModel('local_model');
        $dbLocal = new local_model();

        if ($idlocal != '') {
            $ret = $dbLocal->getLocal("where nome = '".$nomeLocal."' AND idlocal != ".$idlocal . " ");
        } else {
            $ret = $dbLocal->getLocal("where nome = '".$nomeLocal."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('CPF - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idlocal']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }

}