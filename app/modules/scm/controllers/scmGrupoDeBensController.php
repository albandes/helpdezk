<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmGrupoDeBens extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmGrupoDeBens');

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
            $smarty->display('scm-grupodebens-grid.tpl');
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
            $sidx ='descricao';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'descricao') $searchField = 'descricao';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumGrupoDeBens();

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

        $rsGrupoDeBens = $this->_getGrupoDeBens($where,$order,null,$limit);

        while (!$rsGrupoDeBens->EOF) {

            $status_fmt = ($rsGrupoDeBens->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'            => $rsGrupoDeBens->fields['idgrupodebens'],
                'descricao'     => $rsGrupoDeBens->fields['descricao'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsGrupoDeBens->fields['status']

            );
            $rsGrupoDeBens->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsGrupoDeBens->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateGrupoDeBens()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenGrupoDeBens($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-grupodebens-create.tpl');
    }

    public function formUpdateGrupoDeBens()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idGrupoDeBens = $this->getParam('idgrupodebens');
        $rsGrupoDeBens = $this->_getGrupoDeBens("where idgrupodebens = $idGrupoDeBens") ;

        $this->makeScreenGrupoDeBens($smarty,$rsGrupoDeBens,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idgrupodebens', $idGrupoDeBens);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-grupodebens-update.tpl');

    }

    public function echoGrupoDeBens()
    {
        $smarty = $this->retornaSmarty();

        $idGrupoDeBens = $this->getParam('idgrupodebens');
        $rsGrupoDeBens = $this->_getGrupoDeBens("where idgrupodebens = $idGrupoDeBens") ;

        $this->makeScreenGrupoDeBens($smarty,$rsGrupoDeBens,'echo');
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-grupodebens-echo.tpl');
    }

    function makeScreenGrupoDeBens($objSmarty,$rs,$oper)
    {

        // --- Descrição ---
        if ($oper == 'update') {
            if (empty($rs->fields['descricao']))
                $objSmarty->assign('plh_descricao','Informe a descrição do grupo de bens.');
            else
                $objSmarty->assign('descricao',$rs->fields['descricao']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_descricao','Informe a descrição do grupo de bens.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('descricao',$rs->fields['descricao']);
        }

        // --- Depreciação ---
        if ($oper == 'update') {
            if (empty($rs->fields['depreciacao']))
                $objSmarty->assign('');
            else
                $objSmarty->assign('depreciacao',$rs->fields['depreciacao']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('depreciacao',$rs->fields['depreciacao']);
        }

        // --- Depreciação porcentagem ---
        if ($oper == 'update') {
            if (empty($rs->fields['depreciacaoporcentagem']))
                $objSmarty->assign('plh_depreciacaoporcentagem','Informe a porcentagem de depreciação do bem.');
            else
                $objSmarty->assign('depreciacaoporcentagem',$rs->fields['depreciacaoporcentagem']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_depreciacaoporcentagem','Informe a porcentagem de depreciação do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('depreciacaoporcentagem',$rs->fields['depreciacaoporcentagem']);
        }

        // --- Depreciação ---
        if ($oper == 'update') {
            $idDepreciacaoContaEnable = $rs->fields['iddepreciacaoconta'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('codigonomedepreciacaoconta',$rs->fields['codigonomedepreciacaoconta']);
        } else {
            $arrContaContabil = $this->_comboContaContabil($grupoDeBens = 0);
            $objSmarty->assign('depreciacacontaids',  $arrContaContabil['ids']);
            $objSmarty->assign('depreciacacontavals', $arrContaContabil['values']);
            $objSmarty->assign('iddepreciacaoconta', $idDepreciacaoContaEnable );
        }

        // --- Depreciação Acumulada ---
        if ($oper == 'update') {
            $idDepreciacaoAcumuladaContaEnable = $rs->fields['iddepreciacaoacumuladaconta'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('codigonomedepreciacaoacumuladaconta',$rs->fields['codigonomedepreciacaoacumuladaconta']);
        } else {
            $arrContaContabil = $this->_comboContaContabil($grupoDeBens = 0);
            $objSmarty->assign('depreciacaoacumuladacontaids',  $arrContaContabil['ids']);
            $objSmarty->assign('depreciacaoacumuladacontavals', $arrContaContabil['values']);
            $objSmarty->assign('iddepreciacaoacumuladaconta', $idDepreciacaoAcumuladaContaEnable );
        }

        // --- Bens ---
        if ($oper == 'update') {
            $idDepreciacaoBensContaEnable = $rs->fields['iddepreciacaobensconta'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('codigonomedepreciacaobensconta',$rs->fields['codigonomedepreciacaobensconta']);
        } else {
            $arrContaContabil = $this->_comboContaContabil($grupoDeBens = 0);
            $objSmarty->assign('depreciacaobenscontaids',  $arrContaContabil['ids']);
            $objSmarty->assign('depreciacaobenscontavals', $arrContaContabil['values']);
            $objSmarty->assign('iddepreciacaobensconta', $idDepreciacaoBensContaEnable );
        }

        // --- Custo da baixa ---
        if ($oper == 'update') {
            $idDepreciacaoCustoDaBaixaEnable = $rs->fields['iddepreciacaocustodabaixa'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('codigonomedepreciacaocustodabaixa',$rs->fields['codigonomedepreciacaocustodabaixa']);
        } else {
            $arrContaContabil = $this->_comboContaContabil($grupoDeBens = 0);
            $objSmarty->assign('depreciacaocustodabaixaids',  $arrContaContabil['ids']);
            $objSmarty->assign('depreciacaocustodabaixavals', $arrContaContabil['values']);
            $objSmarty->assign('iddepreciacaocustodabaixa', $idDepreciacaoCustoDaBaixaEnable );
        }

    }

    function createGrupoDeBens()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('grupodebens_model');
        $dbGrupoDeBens = new grupodebens_model();

        $dbGrupoDeBens->BeginTrans();

        $idDepreciacaoConta          = 'NULL';
        $idDepreciacaoAcumuladaConta = 'NULL';
        $idDepreciacaoBensConta      = 'NULL';
        $idDepreciacaoCustoDaBaixa   = 'NULL';

        if ($_POST['iddepreciacaoconta'] != 0){
            $idDepreciacaoConta = $_POST['iddepreciacaoconta'];
        }

        if ($_POST['iddepreciacaoacumuladaconta'] != 0){
            $idDepreciacaoAcumuladaConta = $_POST['iddepreciacaoacumuladaconta'];
        }

        if ($_POST['iddepreciacaobensconta'] != 0){
            $idDepreciacaoBensConta = $_POST['iddepreciacaobensconta'];
        }

        if ($_POST['iddepreciacaocustodabaixa'] != 0){
            $idDepreciacaoCustoDaBaixa = $_POST['iddepreciacaocustodabaixa'];
        }

        $ret = $dbGrupoDeBens->insertGrupodeBens($_POST['descricao'], $_POST['depreciacao'], $_POST['depreciacaoporcentagem'], $idDepreciacaoConta, $idDepreciacaoAcumuladaConta, $idDepreciacaoBensConta, $idDepreciacaoCustoDaBaixa);

        if (!$ret) {
            $dbGrupoDeBens->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Grupo de Bens  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idGrupoDeBens = $ret ;

        $aRet = array(
            "idgrupodebens" => $idGrupoDeBens,
            "descricao" => $_POST['descricao']
        );

        $dbGrupoDeBens->CommitTrans();

        echo json_encode($aRet);

    }

    function updateGrupoDeBens()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idGrupoDeBens = $this->getParam('idgrupodebens');

        $this->loadModel('grupodebens_model');
        $dbGrupoDeBens = new grupodebens_model();

        $dbGrupoDeBens->BeginTrans();

        $idDepreciacaoConta          = 'NULL';
        $idDepreciacaoAcumuladaConta = 'NULL';
        $idDepreciacaoBensConta      = 'NULL';
        $idDepreciacaoCustoDaBaixa   = 'NULL';

        if ($_POST['iddepreciacaoconta'] != 0){
            $idDepreciacaoConta = $_POST['iddepreciacaoconta'];
        }

        if ($_POST['iddepreciacaoacumuladaconta'] != 0){
            $idDepreciacaoAcumuladaConta = $_POST['iddepreciacaoacumuladaconta'];
        }

        if ($_POST['iddepreciacaobensconta'] != 0){
            $idDepreciacaoBensConta = $_POST['iddepreciacaobensconta'];
        }

        if ($_POST['iddepreciacaocustodabaixa'] != 0){
            $idDepreciacaoCustoDaBaixa = $_POST['iddepreciacaocustodabaixa'];
        }

        $ret = $dbGrupoDeBens->updateGrupoDeBens($idGrupoDeBens, $_POST['descricao'], $_POST['depreciacao'], $_POST['depreciacaoporcentagem'], $idDepreciacaoConta, $idDepreciacaoAcumuladaConta, $idDepreciacaoBensConta, $idDepreciacaoCustoDaBaixa);

        if (!$ret) {
            $dbGrupoDeBens->RollbackTrans();
            if($this->log)
                $this->logIt('Update Grupo de Bens - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idgrupodebens" => $idGrupoDeBens,
            "status"   => 'OK'
        );

        $dbGrupoDeBens->CommitTrans();

        echo json_encode($aRet);


    }

    function statusGrupoDeBens()
    {
        $idGrupoDeBens = $this->getParam('idgrupodebens');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('grupodebens_model');
        $dbGrupoDeBens = new grupodebens_model();

        $ret = $dbGrupoDeBens->changeStatus($idGrupoDeBens,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Grupo de Bens Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idgrupodebens" => $idGrupoDeBens,
            "status"        => "OK"
        );

        echo json_encode($aRet);

    }


}