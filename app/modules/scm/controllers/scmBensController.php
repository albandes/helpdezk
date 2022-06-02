<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmBens extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmBens ');

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('scm-bens-grid.tpl');
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
            $sidx ='numeropatrimonio';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'numeropatrimonio') $searchField = 'scm_tbbens.numeropatrimonio';
            if ( $_POST['searchField'] == 'descricao') $searchField = 'scm_tbbens.descricao';
            if ( $_POST['searchField'] == 'nomelocal') $searchField = 'scm_tblocal.nome';
            if ( $_POST['searchField'] == 'nomefornecedor') $searchField = 'tbperson.name';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumBens();

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

        $rsBens = $this->_getBens($where,$order,null,$limit);

        while (!$rsBens->EOF) {

            $status_fmt = ($rsBens->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'               => $rsBens->fields['idbens'],
                'numeropatrimonio' => $rsBens->fields['numeropatrimonio'],
                'descricao'        => $rsBens->fields['descricao'],
                'nomelocal'        => $rsBens->fields['nomelocal'],
                'datacomp'         => $rsBens->fields['datacomp'],
                'nomefornecedor'   => $rsBens->fields['nomefornecedor'],
                'status_fmt'       => $status_fmt,
                'status'           => $rsBens->fields['status']

            );
            $rsBens->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateBens()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenBens($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-bens-create.tpl');
    }

    public function formUpdateBens()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idBens = $this->getParam('idbens');
        $rsBens = $this->_getBens("where idbens = $idBens") ;

        $this->makeScreenBens($smarty,$rsBens,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idbens', $idBens);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-bens-update.tpl');

    }

    public function echoBens()
    {
        $smarty = $this->retornaSmarty();

        $idBens = $this->getParam('idbens');
        $rsBens = $this->_getBens("where idbens = $idBens") ;

        $this->makeScreenBens($smarty,$rsBens,'echo');
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-bens-echo.tpl');
    }

    function makeScreenBens($objSmarty,$rs,$oper)
    {
        // --- Descrição ---
        if ($oper == 'update') {
            if (empty($rs->fields['descricao']))
                $objSmarty->assign('plh_descricao','Informe a descrição do bem.');
            else
                $objSmarty->assign('descricao',$rs->fields['descricao']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_descricao','Informe a descrição do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('descricao',$rs->fields['descricao']);
        }

        // --- Número patrimônio ---
        if ($oper == 'update') {
            if (empty($rs->fields['numeropatrimonio']))
                $objSmarty->assign('plh_numeropatrimonio','Informe o número de patrimônio do bem.');
            else
                $objSmarty->assign('numeropatrimonio',$rs->fields['numeropatrimonio']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_numeropatrimonio','Informe o número de patrimônio do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('numeropatrimonio',$rs->fields['numeropatrimonio']);
        }

        // --- Marca ---
        if ($oper == 'update') {
            $idMarcaEnable = $rs->fields['idmarca'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomemarca',$rs->fields['nomemarca']);
        } else {
            $arrMarca = $this->_comboMarca($marca = 0);
            $objSmarty->assign('marcaids',  $arrMarca['ids']);
            $objSmarty->assign('marcavals',$arrMarca['values']);
            $objSmarty->assign('idmarca', $idMarcaEnable );
        }

        // --- Estado ---
        if ($oper == 'update') {
            $idEstadoEnable = $rs->fields['idestado'];
        } elseif ($oper == 'create') {
            $idEstadoEnable = 0;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomeestado',$rs->fields['nomeestado']);
        } else {
            $arrEstado = $this->_comboEstado($estado = 0);
            $result = [];
            foreach ($arrEstado['values'] as $key => $value){
                $result[] = utf8_encode($value);
            }
            $objSmarty->assign('estadoids',  $arrEstado['ids']);
            $objSmarty->assign('estadovals', $result);
            $objSmarty->assign('idestado', $idEstadoEnable );
        }

        // --- Local ---
        if ($oper == 'update') {
            $idLocalEnable = $rs->fields['idlocal'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomelocal',$rs->fields['nomelocal']);
        } else {
            $arrLocal = $this->_comboLocal($local = 0);
            $objSmarty->assign('localids',  $arrLocal['ids']);
            $objSmarty->assign('localvals',$arrLocal['values']);
            $objSmarty->assign('idlocal', $idLocalEnable );
        }

        // --- Grupo de Bens ---
        if ($oper == 'update') {
            $idGrupoDeBensEnable = $rs->fields['idgrupodebens'];
        } elseif ($oper == 'create') {
            $idGrupoDeBensEnable = 0;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomegrupodebens',$rs->fields['nomegrupodebens']);
        } else {
            $arrGrupoDeBens= $this->_comboGrupoDeBens($grupoDeBens = 0);
            $result = [];
            foreach ($arrGrupoDeBens['values'] as $key => $value){
                $result[] = utf8_encode($value);
            }
            $objSmarty->assign('grupodebensids',  $arrGrupoDeBens['ids']);
            $objSmarty->assign('grupodebensvals', $result);
            $objSmarty->assign('idgrupodebens', $idGrupoDeBensEnable );
        }

        // --- Data de Aquisição ---
        if ($oper == 'update') {
            if (empty($rs->fields['dataaquisicao']))
                $objSmarty->assign('plh_dataaquisicao','Informe a data de aquisição do bem.');
            else
                $objSmarty->assign('dataaquisicao',$rs->fields['dataaquisicao']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_dataaquisicao','Informe a data de aquisição do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('dataaquisicao',$rs->fields['dataaquisicao']);
        }

        // --- Fornecedor ---
        if ($oper == 'update') {
            $idFornecedorEnable = $rs->fields['idperson'];
        } elseif ($oper == 'create') {
            $idFornecedorEnable = 0;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomefornecedor',$rs->fields['nomefornecedor']);
        } else {
            $arrFornecedor= $this->_comboFornecedor();
            $result = [];
            foreach ($arrFornecedor['values'] as $key => $value){
                $result[] = $value;
            }
            $objSmarty->assign('personids',  $arrFornecedor['ids']);
            $objSmarty->assign('personvals', $result);
            $objSmarty->assign('idperson', $idFornecedorEnable );
        }

        // --- Doação ---
        if ($oper == 'update') {
            if (empty($rs->fields['doacao']))
                $objSmarty->assign('');
            else
                $objSmarty->assign('doacao',$rs->fields['doacao']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('doacao',$rs->fields['doacao']);
        }

        // --- NF Entrada ---
        if ($oper == 'update') {
            if (empty($rs->fields['nfentrada']))
                $objSmarty->assign('plh_nfentrada','Informe a nota fiscal de entrada do bem.');
            else
                $objSmarty->assign('nfentrada',$rs->fields['nfentrada']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nfentrada','Informe a nota fiscal de entrada do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nfentrada',$rs->fields['nfentrada']);
        }

        // --- Valor ---
        if ($oper == 'update') {
            if (empty($rs->fields['valor']))
                $objSmarty->assign('plh_valor','Informe o valor do bem.');
            else
                $objSmarty->assign('valor',$rs->fields['valor']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_valor','Informe o valor do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('valor',$rs->fields['valor']);
        }

        // --- Número de Série ---
        if ($oper == 'update') {
            if (empty($rs->fields['numeroserie']))
                $objSmarty->assign('plh_numeroserie','Informe o número de série bem.');
            else
                $objSmarty->assign('numeroserie',$rs->fields['numeroserie']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_numeroserie','Informe a nota fiscal de entrada do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('numeroserie',$rs->fields['numeroserie']);
        }

        // --- Data de Aquisição ---
        if ($oper == 'update') {
            if (empty($rs->fields['datagarantia']))
                $objSmarty->assign('plh_datagarantia','Informe a data de garantia do bem.');
            else
                $objSmarty->assign('datagarantia',$rs->fields['datagarantia']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_datagarantia','Informe a data de garantia do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('datagarantia',$rs->fields['datagarantia']);
        }

        // --- Quantidade ---
        if ($oper == 'update') {
            if (empty($rs->fields['quantidade']))
                $objSmarty->assign('plh_quantidade','Informe a quantidade do bem.');
            else
                $objSmarty->assign('quantidade',$rs->fields['quantidade']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_quantidade','Informe a quantidade do bem.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('quantidade',$rs->fields['quantidade']);
        }

        // --- Baixa ---
        if ($oper == 'update') {
            if (empty($rs->fields['baixa']))
                $objSmarty->assign('');
            else
                $objSmarty->assign('baixa',$rs->fields['baixa']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('baixa',$rs->fields['baixa']);
        }

    }

    function createBens()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('bens_model');
        $dbBens = new bens_model();

        $dbBens->BeginTrans();

        $idMarca       = null;
        $idEstado      = null;
        $idLocal       = null;
        $idGrupoDeBens = null;
        $idPerson      = null;

        if ($_POST['idmarca'] != 0){
            $idMarca = $_POST['idmarca'];
        }

        if ($_POST['idestado'] != 0){
            $idEstado = $_POST['idestado'];
        }

        if ($_POST['idlocal'] != 0){
            $idLocal = $_POST['idlocal'];
        }

        if ($_POST['idgrupodebens'] != 0){
            $idGrupoDeBens = $_POST['idgrupodebens'];
        }

        if ($_POST['idperson'] != 0){
            $idPerson = $_POST['idperson'];
        }

        $ret = $dbBens->insertBens($_POST['descricao'],$_POST['numeropatrimonio'],$idMarca,$idEstado,$idLocal,$idGrupoDeBens,$idPerson,$_POST['dataaquisicao'],$_POST['doacao'],$_POST['nfentrada'],$_POST['numeroserie'],$_POST['valor'],$_POST['datagarantia'],$_POST['quantidade'],$_POST['baixa']
        );

        if (!$ret) {
            $dbBens->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Bens  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idBens = $ret ;

        $aRet = array(
            "idbens" => $idBens,
            "descricao" => $_POST['descricao']
        );

        $dbBens->CommitTrans();

        echo json_encode($aRet);

    }

    function updateBens()
    {
        $idBens = $this->getParam('idbens');

        $this->loadModel('bens_model');
        $dbBens = new bens_model();

        $dbBens->BeginTrans();

        $idMarca       = null;
        $idEstado      = null;
        $idLocal       = null;
        $idGrupoDeBens = null;
        $idPerson      = null;

        if ($_POST['idmarca'] != 0){
            $idMarca = $_POST['idmarca'];
        }

        if ($_POST['idestado'] != 0){
            $idEstado = $_POST['idestado'];
        }

        if ($_POST['idlocal'] != 0){
            $idLocal = $_POST['idlocal'];
        }

        if ($_POST['idgrupodebens'] != 0){
            $idGrupoDeBens = $_POST['idgrupodebens'];
        }

        if ($_POST['idperson'] != 0){
            $idPerson = $_POST['idperson'];
        }

        $ret = $dbBens->updateBens($idBens,$_POST['descricao'],$_POST['numeropatrimonio'],$idMarca,$idEstado,$idLocal,$idGrupoDeBens,$idPerson,$_POST['dataaquisicao'],$_POST['doacao'],$_POST['nfentrada'],$_POST['numeroserie'],$_POST['valor'],$_POST['datagarantia'],$_POST['quantidade'],$_POST['baixa']);

        if (!$ret) {
            $dbBens->RollbackTrans();
            if($this->log)
                $this->logIt('Update Bens - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idbens" => $idBens,
            "status"   => 'OK'
        );

        $dbBens->CommitTrans();

        echo json_encode($aRet);


    }

    function statusBens()
    {
        $idbens = $this->getParam('idbens');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('bens_model');
        $dbBens = new bens_model();

        $ret = $dbBens->changeStatus($idbens,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Bens Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idbens" => $idbens,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function createMarca()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('marca_model');
        $dbMarca = new marca_model();

        $dbMarca->BeginTrans();

        $ret = $dbMarca->insertMarca($_POST['nome']);

        if (!$ret) {
            $dbMarca->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Marca  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idMarca = $ret ;

        $aRet = array(
            "idmarca" => $idMarca,
            "nome" => $_POST['nome']
        );

        $dbMarca->CommitTrans();

        echo json_encode($aRet);
    }

    function ajaxMarca()
    {
        echo $this->comboMarcaHtml();
    }

    public function comboMarcaHtml()
    {
        $arrType = $this->_comboMarca();
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    function createEstado()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('estado_model');
        $dbEstado = new estado_model();

        $dbEstado->BeginTrans();

        $ret = $dbEstado->insertEstado($_POST['nome']);

        if (!$ret) {
            $dbEstado->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Estado  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idEstado = $ret ;

        $aRet = array(
            "idestado" => $idEstado,
            "nome" => $_POST['nome']
        );

        $dbEstado->CommitTrans();

        echo json_encode($aRet);
    }

    function ajaxEstado()
    {
        echo $this->_comboEstadoHtml();
    }

    public function _comboEstadoHtml()
    {
        $arrType = $this->_comboEstado();
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
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

    function ajaxLocal()
    {
        echo $this->_comboLocalHtml();
    }

    public function _comboLocalHtml()
    {
        $arrType = $this->_comboLocal();
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    function buscaMarca()
    {
        $nome = $_POST['modal_marca_nome'];

        $this->loadModel('marca_model');
        $dbMarca = new marca_model();
        $ret = $dbMarca->getMarca("where nome = '".$nome."'");

        if (!$ret) {
            if($this->log)
            $this->logIt('Nome da marca - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idmarca']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }

    function buscaEstado()
    {
        $nome = $_POST['modal_estado_nome'];

        $this->loadModel('estado_model');
        $dbEstado = new estado_model();
        $ret = $dbEstado->getEstado("where nome = '".$nome."'");

        if (!$ret) {
            if($this->log)
                $this->logIt('Nome do estado - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idestado']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }

    function buscaLocal()
    {
        $nome = $_POST['modal_local_nome'];

        $this->loadModel('local_model');
        $dbLocal = new local_model();
        $ret = $dbLocal->getLocal("where nome = '".$nome."'");

        if (!$ret) {
            if($this->log)
                $this->logIt('Nome do local - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
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