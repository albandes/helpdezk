<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmCentroCusto extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmCentroCusto');

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
            $smarty->display('scm-centrocusto-grid.tpl');
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
            if ( $_POST['searchField'] == 'codigo') $searchField = 'codigo';
            if ( $_POST['searchField'] == 'nome') $searchField = 'nome';
            if ( $_POST['searchField'] == 'tipo') $searchField = 'tipo';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumCentroCustos();

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

        $rsCentroCusto = $this->_getCentroCusto($where,$order,null,$limit);

                while (!$rsCentroCusto->EOF) {

            $tipo_fmt = ($rsCentroCusto->fields['tipo'] == 'C' ) ? '<span class="label label-success">Crédito</span>' : '<span class="label label-danger">Débito</span>';
            $status_fmt = ($rsCentroCusto->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsCentroCusto->fields['idcentrocusto'],
                'idperson'      => $rsCentroCusto->fields['idperson'],
                'codigo'        => $rsCentroCusto->fields['codigo'],
                'nome'          => $rsCentroCusto->fields['nome'],
                'tipo'          => $tipo_fmt,
                'status_fmt'    => $status_fmt,
                'status'        => $rsCentroCusto->fields['status']

            );
            $rsCentroCusto->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateCentroCusto()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenCentroCusto($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-centrocusto-create.tpl');
    }

    public function formUpdateCentroCusto()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idCentroCusto = $this->getParam('idcentrocusto');
        $rsCentroCusto = $this->_getCentroCusto("where idcentrocusto = $idCentroCusto") ;

        $this->makeScreenCentroCusto($smarty,$rsCentroCusto,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idcentrocusto', $idCentroCusto);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-centrocusto-update.tpl');

    }

    public function echoCentroCusto()
    {
        $smarty = $this->retornaSmarty();

        $idCentroCusto = $this->getParam('idcentrocusto');
        $rsCentroCusto = $this->_getCentroCusto("where idcentrocusto = $idCentroCusto") ;

        $this->makeScreenCentroCusto($smarty,$rsCentroCusto,'echo');
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-centrocusto-echo.tpl');
    }

    function makeScreenCentroCusto($objSmarty,$rs,$oper)
    {

        // --- Código ---
        if ($oper == 'update') {
            if (empty($rs->fields['codigo']))
                $objSmarty->assign('plh_codigo','Informe o código do centro de custo.');
            else
                $objSmarty->assign('codigo',$rs->fields['codigo']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_codigo','Informe o codigo do centro de custo.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('codigo',$rs->fields['codigo']);
        }

        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('plh_nome','Informe o nome do centro de custo.');
            else
                $objSmarty->assign('nome',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o nome do centro de custo.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nome',$rs->fields['nome']);
        }

        // --- Tipo ---
        if ($oper == 'update') {
            $idTipoEnable = $rs->fields['tipo'];
        } elseif ($oper == 'create') {
            $idTipoEnable = 'C';
        }
        if ($oper == 'echo') {
            $objSmarty->assign('tipo', $rs->fields['tipo']);
        } else {
            $arrDepartamento = $this->_comboCentroCustoTipo();
            $objSmarty->assign('tipoids',  $arrDepartamento['ids']);
            $objSmarty->assign('tipovals', $arrDepartamento['values']);
            $objSmarty->assign('idtipo', $idTipoEnable);
        }

    }

    function createCentroCusto()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();

        $dbCentroCusto->BeginTrans();

        $ret = $dbCentroCusto->insertCentroCusto($this->idPerson,$_POST['nome'],$_POST['tipo'],$_POST['codigo']);

        if (!$ret) {
            $dbCentroCusto->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Centro de Custo  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCentroCusto = $ret ;

        $aRet = array(
            "idcentrocusto" => $idCentroCusto,
            "nome" => $_POST['nome']
        );

        $dbCentroCusto->CommitTrans();

        echo json_encode($aRet);

    }

    function updateCentroCusto()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCentroCusto = $this->getParam('idcentrocusto');

        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();

        $dbCentroCusto->BeginTrans();

        $ret = $dbCentroCusto->updateCentroCusto($idCentroCusto,$_POST['nome'],$_POST['tipo'],$_POST['codigo']);
        if (!$ret) {
            $dbCentroCusto->RollbackTrans();
            if($this->log)
                $this->logIt('Update Centro de Custo - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idcentrocusto" => $idCentroCusto,
            "status"   => 'OK'
        );

        $dbCentroCusto->CommitTrans();

        echo json_encode($aRet);


    }

    function statusCentroCusto()
    {
        $idcentrocusto = $this->getParam('idcentrocusto');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();

        $ret = $dbCentroCusto->changeStatus($idcentrocusto,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Centro de Custo Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idcentrocusto" => $idcentrocusto,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }
    function buscaCentroCusto()
    {

        $codigo = $_REQUEST['codigo'];
        $idcentrocusto = $_REQUEST['idcentrocusto'];

        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();

        if ($idcentrocusto != '') {
            $ret = $dbCentroCusto->getCentroCusto("where codigo = '".$codigo."' AND idcentrocusto != ".$idcentrocusto . " ");
        } else {
            $ret = $dbCentroCusto->getCentroCusto("where codigo = '".$codigo."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('Centro de Custo Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idcentrocusto']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }


}