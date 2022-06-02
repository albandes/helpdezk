<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmContaContabil extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmContaContabil');

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
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('scm-contacontabil-grid.tpl');
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
            if ( $_POST['searchField'] == 'centrocusto') $searchField = 'centrocusto';
            if ( $_POST['searchField'] == 'codigo') $searchField = 'codigo';
            if ( $_POST['searchField'] == 'nome') $searchField = 'nome';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumContaContabeis();

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

        $rsContaContabil = $this->_getContaContabil($where,$order,null,$limit);

        while (!$rsContaContabil->EOF) {

            $status_fmt = ($rsContaContabil->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsContaContabil->fields['idcontacontabil'],
                'centrocusto'   => $rsContaContabil->fields['centrocusto'],
                'codigo'        => $rsContaContabil->fields['codigo'],
                'nome'          => $rsContaContabil->fields['nome'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsContaContabil->fields['status']

            );
            $rsContaContabil->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsContaContabil->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateContaContabil()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenContaContabil($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-contacontabil-create.tpl');
    }

    public function formUpdateContaCOntabil()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idContaContabil = $this->getParam('idcontacontabil');
        $rsContaContabil = $this->_getContaContabil("where idcontacontabil = $idContaContabil") ;

        $this->makeScreenContaContabil($smarty,$rsContaContabil,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idcontacontabil', $idContaContabil);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-contacontabil-update.tpl');

    }

    public function echoContaContabil()
    {
        $smarty = $this->retornaSmarty();

        $idContaContabil = $this->getParam('idcontacontabil');
        $rsContaContabil = $this->_getContaContabil("where  idcontacontabil = $idContaContabil") ;
        $rsCentroCusto = $this->_getCentroCusto("where idcentrocusto = " . $rsContaContabil->fields['idcentrocusto']) ;

        $this->makeScreenContaContabil($smarty,$rsContaContabil,'echo');
        $smarty->assign('centrocusto',  $rsCentroCusto->fields['codigo']. ' - ' .$rsCentroCusto->fields['nome']);
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-contacontabil-echo.tpl');
    }

    function makeScreenContaContabil($objSmarty,$rs,$oper)
    {
        // --- Centro de Custo ---
        if ($oper == 'update') {
            $idCentroCustoEnable = $rs->fields['idcentrocusto'];
        } elseif ($oper == 'create') {
            $idCentroCustoEnable = 1;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('idcentrocusto',$rs->fields['idcentrocusto']);
        } else {
            $arrCentroCusto = $this->_comboCentroCusto();
            $objSmarty->assign('centrocustoids',  $arrCentroCusto['ids']);
            $objSmarty->assign('centrocustovals', $arrCentroCusto['values']);
            $objSmarty->assign('idcentrocusto', $idCentroCustoEnable );
        }

        // --- Código ---
        if ($oper == 'update') {
            if (empty($rs->fields['codigo']))
                $objSmarty->assign('plh_codigo','Informe o código da conta contábil.');
            else
                $objSmarty->assign('codigo',$rs->fields['codigo']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_codigo','Informe o código da conta contábil.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('codigo',$rs->fields['codigo']);
        }

        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('plh_nome','Informe o nome da conta contábil..');
            else
                $objSmarty->assign('nome',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o nome da conta contábil.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nome',$rs->fields['nome']);
        }

    }

    function createContaContabil()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();

        $dbContaContabil->BeginTrans();

        $ret = $dbContaContabil->insertContaContabil($_POST['idcentrocusto'],$_POST['nome'],$_POST['codigo']);

        if (!$ret) {
            $dbContaContabil->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Conta Contábil  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idContaContabil = $ret ;

        $aRet = array(
            "idcontacontabil" => $idContaContabil,
            "nome" => $_POST['nome']
        );

        $dbContaContabil->CommitTrans();

        echo json_encode($aRet);

    }

    function updateContaContabil()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idContaContabil = $this->getParam('idcontacontabil');

        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();

        $dbContaContabil->BeginTrans();

        $ret = $dbContaContabil->updateContaContabil($idContaContabil,$_POST['idcentrocusto'],$_POST['nome'],$_POST['codigo']);
        if (!$ret) {
            $dbContaContabil->RollbackTrans();
            if($this->log)
                $this->logIt('Update Conta Contábil - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idcontacontabil" => $idContaContabil,
            "status"   => 'OK'
        );

        $dbContaContabil->CommitTrans();

        echo json_encode($aRet);


    }

    function statusContaContabil()
    {
        $idContaContabil = $this->getParam('idcontacontabil');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();

        $ret = $dbContaContabil->changeStatus($idContaContabil,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Conta Contábil Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idcontacontabil" => $idContaContabil,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function buscaContaContabil()
    {

        $codigo = $_REQUEST['codigo'];
        $idcontacontabil = $_REQUEST['idcontacontabil'];

        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();

        if ($idcontacontabil != '') {
            $ret = $dbContaContabil->getContaContabil("where codigo = '".$codigo."' AND idcontacontabil != ".$idcontacontabil . " ");
        } else {
            $ret = $dbContaContabil->getContaContabil("where codigo = '".$codigo."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('Conta Contábil Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idcontacontabil']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }


}