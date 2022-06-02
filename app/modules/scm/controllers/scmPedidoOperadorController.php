<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmPedidoOperador extends scmCommon
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

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'suprimentos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        $this->programcontroller = 'scmPedidoOperador' ;
        $this->idprogram =  $this->getIdProgramByController($this->programcontroller);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

        $this->loadModel('status_model');
        $dbStatus = new status_model();
        $this->dbStatus = $dbStatus;

        $this->loadModel('pedidoaprovador_model');
        $dbPedidoAprovador = new pedidoaprovador_model();
        $this->dbPedidoAprovador = $dbPedidoAprovador;

        $this->saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';
        if($this->saveMode == "aws-s3"){
            $bucket = $this->getConfig('s3bucket_name');
            $this->cotacaoDir = 'scm/cotacoes/';
            $this->cotacaoUrl = "https://{$bucket}.s3.amazonaws.com/scm/cotacoes/";
        }elseif($this->saveMode == "disk"){
            if($this->_externalStorage) {
                $this->cotacaoDir = $this->_externalStoragePath.'/scm/cotacoes/';
                $this->cotacaoUrl = $this->_externalStorageUrl.'/scm/cotacoes/';
            } else {
                $this->cotacaoDir = $this->helpdezkPath.'/app/uploads/scm/cotacoes/';
                $this->cotacaoUrl = $this->getHelpdezkUrl().'/app/uploads/scm/cotacoes/';
            }
        }

        $this->accessExceptions = explode(',', $_SESSION['scm']['SCM_ACCESS_USER_EXCEPTIONS']);

        //$this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);

        $this->arrStatusClose = array(7,15); //Status de finalização do pedido
        $this->arrItemStAppr = array(7,15,16,17,19,23); //Status itens aprovados

        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();
        $this->dbPedidoOperador = $dbPedidoOperador;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $this->access($smarty,$this->idPerson,$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $arrProduto = $this->_comboProduto(null,null);
        $smarty->assign('produtoids',  $arrProduto['ids']);
        $smarty->assign('produtovals', $arrProduto['values']);
        $smarty->assign('produtodisables', $arrProduto['disables']);
        $smarty->assign('produtoopts', $arrProduto['options']);

        $this->_displayButtons($smarty,$permissions);
        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', '');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display('scm-maintenance.tpl');
        }else{
            if($permissions[0] == "Y"){
                $smarty->display('scm-pedidooperador-grid.tpl');
            }else{
                $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
                $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
            }
        }

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        $persontype = $this->_getSCMTypePerson("AND a.idperson = ".$this->idPerson);

        if($where == '') $where .= "WHERE ";
        else $where .= "AND ";

        $groupType = $this->_getUserGroupType($this->idPerson);

        switch ($groupType){
            case 1:
                $rsGroupPerson = $this->_getGrupoOperador("AND ghp.idperson = ".$this->idPerson);
                $arrTurma = array();
                $turmaw = '';
                while(!$rsGroupPerson->EOF){
                    $rsTurma = $this->_getTurmaGrupo($rsGroupPerson->fields['idgroup']);
                    while(!$rsTurma->EOF){
                        if(!in_array($rsTurma->fields['idturma'],$arrTurma)){
                            $turmaw .= $rsTurma->fields['idturma'].',';
                        }
                        $rsTurma->MoveNext();
                    }
                    $rsGroupPerson->MoveNext();
                }
                $turmaw = substr($turmaw,0,-1);
                break;
            case 2:
                $retUserGroup = $this->_getGrupoOperador("AND ghp.idperson = ".$this->idPerson);
                $groups = '';
                while(!$retUserGroup->EOF){
                    $groups .= $retUserGroup->fields['idgroup'].',';
                    $retUserGroup->MoveNext();
                }
                $groups = substr($groups,0,-1);
                if($groups != ''){
                    $wtmp = "AND a.idgroup IN ($groups)";
                    $usersGroup = $this->dbPedidoOperador->getUsersByGroup($wtmp);
                    $userList = '';
                    while(!$usersGroup->EOF){
                        $userList .= $usersGroup->fields['idperson'].',';
                        $usersGroup->MoveNext();
                    }
                    $userList = substr($userList,0,-1);
                }
                break;
            default:
                break;
        }

        if($turmaw != ''){
            //$where .= "pht.idturma IN ($turmaw) AND idstatus IN (1,20)";
            $where .= "pht.idturma IN ($turmaw)";
        }else{
            $where .= "(pht.idturma IS NULL OR (pht.idturma IS NOT NULL AND idstatus != 1)) ";
        }

        if($userList != ''){
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= "idperson IN ($userList)";
        }

        $idStatus = $_POST['idstatus'];
        if($idStatus && $idStatus != 'ALL') $where .= "AND idstatus = $idStatus";

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){

            switch ($_POST['searchField']){
                case 'id':
                    $searchField = 'vwp.idpedido';
                    break;
                case 'idperson':
                    $searchField = 'idperson';
                    break;
                case 'nomepessoa':
                    $searchField = 'nomepessoa';
                    break;
                case 'datapedido':
                    $searchField = "DATE_FORMAT(datapedido,'%d/%m/%Y')";
                    break;
                case 'dataentrega':
                    $searchField = "DATE_FORMAT(dataentrega,'%d/%m/%Y')";
                    break;
                case 'nomestatus':
                    $searchField = 'nomestatus';
                    break;
                default:
                    $searchField = 'motivo';
                    break;
            }

            if (empty($where))
                $oper = ' AND ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumPedidoOperadores($where);

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

        $rsPedidoOperador = $this->_getPedidoOperadorGrid($where ,$order,null,$limit);

        while (!$rsPedidoOperador->EOF) {
            $status_fmt = ($rsPedidoOperador->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

            $aColumns[] = array(
                'id'            => $rsPedidoOperador->fields['idpedido'],
                'nomepessoa'    => $rsPedidoOperador->fields['nomepessoa'],
                'nomestatus'    => $rsPedidoOperador->fields['nomestatus'],
                'idstatus'      => $rsPedidoOperador->fields['idstatus'],
                'datapedido'    => $rsPedidoOperador->fields['datapedido'],
                'dataentrega'   => $rsPedidoOperador->fields['dataentrega'],
                'foradoprazo'   => $rsPedidoOperador->fields['foradoprazo'],
                'motivo'        => $rsPedidoOperador->fields['motivo'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsPedidoOperador->fields['status'],
                'turma'         => $rsPedidoOperador->fields['nome']

            );
            $rsPedidoOperador->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formUpdatePedidoOperador()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();
        $this->access($smarty,$this->idPerson,$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $idPedidoOperador = $this->getParam('idpedidooperador');

        $rsPedidoOperador = $this->_getPedidoOperador("where idpedido = $idPedidoOperador") ;
        $rsItemPedidoOperador = $this->_getItemPedidoOperadorEcho("AND a.idpedido = $idPedidoOperador");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoOperador");

        $rsItemPedidoOperadorCotacao = $this->_getItemPedidoOperadorCotacaoEcho("$idPedidoOperador");
        $rsCotacao = [];
        $i = 0;
        $total = 0;
        $totalitens = 0;
        $totalfrete = 0;

        foreach ($rsItemPedidoOperadorCotacao as $key => $value){
            $rsCotacao[$value['iditempedido']][$i] = [
                'idcotacao'      => $value['idcotacao'],
                'arquivo'       => $value['arquivo'],
                'idperson'       => $value['idperson'],
                'valor_unitario' => $value['valor_unitario'],
                'valor_total'    => $value['valor_total'],
                'valor_frete'    => $value['valor_frete'],
                'flg_aprovado'   => $value['flg_aprovado'],
                'idproduto'   => $value['idproduto'],
                'flg_carrier'   => $value['flg_carrier']
            ];

            $totalitens += $value['valor_total'];
            $totalfrete += $value['valor_frete'];
            $i++;
        }

        $total = $totalitens + $totalfrete;

        $this->makeScreenPedidoOperador($smarty,$rsPedidoOperador,$rsItemPedidoOperador,$rsCotacao,$rsPedidoTurma,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('caminho', $this->cotacaoUrl);

        $smarty->assign('hidden_idpedidooperador', $idPedidoOperador);

        $smarty->assign('totalitens', number_format($totalitens,2,',',''));
        $smarty->assign('totalfrete', number_format($totalfrete,2,',',''));
        $smarty->assign('totalpedido', number_format($total,2,',',''));

        $rsTypeUser = $this->_getSCMTypePerson("AND a.idperson = ".$this->idPerson);

        if($rsTypeUser){$displaytype = '';}
        else{$displaytype = 'hidden';}
        $smarty->assign('flagdispplay',$displaytype);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', '');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display('scm-maintenance.tpl');
        }else{
            $smarty->display('scm-pedidooperador-update.tpl');
        }

    }

    public function echoPedidoOperador()
    {
        $smarty = $this->retornaSmarty();
        $this->access($smarty,$this->idPerson,$this->idprogram,$_SESSION['SES_TYPE_PERSON']);

        $idPedidoOperador = $this->getParam('idpedidooperador');
        $rsPedidoOperador = $this->_getPedidoOperador("where idpedido = $idPedidoOperador");
        $rsItemPedidoOperador = $this->_getItemPedidoOperadorEcho("AND a.idpedido = $idPedidoOperador");
        $rsItemPedidoOperadorCotacao = $this->_getItemPedidoOperadorCotacaoEcho("$idPedidoOperador");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoOperador");
        $rsCotacao = [];
        $i = 0;
        $total = 0;
        $totalitens = 0;
        $totalfrete = 0;

        foreach ($rsItemPedidoOperadorCotacao as $key => $value){
            $rsCotacao[$value['iditempedido']][$i] = [
                'idcotacao'      => $value['idcotacao'],
                'idperson'       => $value['idperson'],
                'nomefornecedor' => $value['nomefornecedor'],
                'valor_unitario' => $value['valor_unitario'],
                'valor_total'    => $value['valor_total'],
                'valor_frete'    => $value['valor_frete'],
                'flg_aprovado'   => $value['flg_aprovado'],
                'arquivo'        => $value['arquivo'],
                'idproduto'      => $value['idproduto']
            ];

            $totalitens += $value['valor_total'];
            $totalfrete += $value['valor_frete'];
            $i++;
        }

        $total = $totalitens + $totalfrete;
        $this->makeScreenPedidoOperador($smarty,$rsPedidoOperador,$rsItemPedidoOperador,$rsCotacao,$rsPedidoTurma,'echo');

        $smarty->assign('token', $this->_makeToken());
        $smarty->assign('caminho', $this->cotacaoUrl);
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('hidden_idpedidooperador', $idPedidoOperador);
        $smarty->assign('totalitens', number_format($totalitens,2,',',''));
        $smarty->assign('totalfrete', number_format($totalfrete,2,',',''));
        $smarty->assign('totalpedido', number_format($total,2,',',''));
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', '');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display('scm-maintenance.tpl');
        }else{
            $smarty->display('scm-pedidooperador-echo.tpl');
        }

    }

    function makeScreenPedidoOperador($objSmarty,$rs,$rsItem,$rsCotacao,$rsTurma,$oper)
    {

        // --- Solicitante ---
        $objSmarty->assign('personname',$rs->fields['nomepessoa']);

        // --- Array de produtos com as cotações ---
        $objSmarty->assign('rsCotacao',$rsCotacao);

        // --- Data Entrega ---
        if ($oper == 'update') {
            if (empty($rs->fields['dataentrega']))
                $objSmarty->assign('plh_dataentrega','Informe a data da entrega do pedido.');
            else
                $objSmarty->assign('dataentrega',$rs->fields['dataentrega']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_dataentrega','Informe a data da entrega do pedido.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('dataentrega',$rs->fields['dataentrega']);
        }

        // --- Fornecedor ---
        if ($oper == 'update') {
            $idFornecedorEnable = $rs->fields['idfornecedor'];
        } elseif ($oper == 'create') {
            $idStatusEnable = 1;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomefornecedor',$rs->fields['nomefornecedor']);
        } else {
            $arrFornecedor = $this->_comboFornecedor();
            $objSmarty->assign('fornecedorids',  $arrFornecedor['ids']);
            $objSmarty->assign('fornecedorvals', $arrFornecedor['values']);
            $objSmarty->assign('idfornecedor', $idFornecedorEnable );
        }

        // --- Status ---
        if ($oper == 'update') {
            $idStatusEnable = $rs->fields['idstatus'];
            $objSmarty->assign('nomestatus',$rs->fields['nomestatus']);
        } elseif ($oper == 'create') {
            $idStatusEnable = 1;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomestatus',$rs->fields['nomestatus']);
            $objSmarty->assign('motivocancelamento',$rs->fields['motivocancelamento']);
            $objSmarty->assign('idstatus', $rs->fields['idstatus']);
        } else {
            $arrStatus = $this->_comboStatus('form',1);
            $result = [];
            foreach ($arrStatus['values'] as $key => $value){
                $result[] = $value;
            }
            $objSmarty->assign('statusids',  $arrStatus['ids']);
            $objSmarty->assign('statusvals', $result);
            $objSmarty->assign('idstatus', $idStatusEnable );


        }

        // --- Conta Contabil ---
        if ($oper == 'update') {
            $idContacontabilEnable = $rs->fields['idcontacontabil'];
            $objSmarty->assign('codigonomecontacontabil',$rs->fields['codigonomecontacontabil']);
        } elseif ($oper == 'create') {
            $idStatusEnable = 1;
        }

        if ($oper == 'echo') {
            $objSmarty->assign('codigonomecontacontabil',$rs->fields['codigonomecontacontabil']);
        } else {
            $arrContacontabil = $this->_comboContaContabilAprovador();
            $objSmarty->assign('contacontabilids',  $arrContacontabil['ids']);
            $objSmarty->assign('contacontabilvals', $arrContacontabil['values']);
            $objSmarty->assign('idcontacontabil', $idContacontabilEnable );
        }

        // --- Centro de Custo ---
        if ($oper == 'update') {
            $idCentrodecustoEnable = $rs->fields['idcentrocusto'];
            $objSmarty->assign('codigonomecentrodecusto',$rs->fields['codigonomecentrodecusto']);
        } elseif ($oper == 'create') {
            $idCentrodecustoEnable = 1;
        }

        if ($oper == 'echo') {
            $objSmarty->assign('codigonomecentrodecusto',$rs->fields['codigonomecentrodecusto']);
        } else {
            $arrCentroCusto = $this->_comboCentroCustoAprovador();
            $objSmarty->assign('centrodecustoids',  $arrCentroCusto['ids']);
            $objSmarty->assign('centrodecustovals', $arrCentroCusto['values']);
            $objSmarty->assign('idcentrodecusto', $idCentrodecustoEnable );
        }

        // --- Motivo Compra ---
        if ($oper == 'update') {
            if (empty($rs->fields['motivo']))
                $objSmarty->assign('plh_motivo','Informe o motivo da compra.');
            else
                $objSmarty->assign('motivo',$rs->fields['motivo']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_motivo','Informe o motivo da compra.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('motivo',$rs->fields['motivo']);
        }

        //Status do item
        $arrStatus = $this->_comboStatus('form',2);
        $result = [];
        foreach ($arrStatus['values'] as $key => $value){
            $result[] = $value;
        }
        $objSmarty->assign('statusitensids',  $arrStatus['ids']);
        $objSmarty->assign('statusitensvals', $result);

        // --- Produto ---
        if ($oper == 'update') {
            $arrProduto = $this->_comboProduto(null,null);
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            $objSmarty->assign('produtodisables', $arrProduto['disables']);
            $objSmarty->assign('produtoopts', $arrProduto['options']);

            while (!$rsItem->EOF) {

                $arrItens[] = array(
                    'iditempedido'      => $rsItem->fields['iditempedido'],
                    'idproduto'         => $rsItem->fields['idproduto'],
                    'nome'              => $rsItem->fields['nome'],
                    'unidade'           => $rsItem->fields['unidade'],
                    'quantidade'        => $rsItem->fields['quantidade'],
                    'idstatus'          => $rsItem->fields['idstatus'],
                    'nomestatusitem'    => $rsItem->fields['nomestatusitem'],
                    'disponibilidade'   => ($rsItem->fields['estoque_atual'] > 0 && $rsItem->fields['quantidade'] <= $rsItem->fields['estoque_atual']) ? 'Dispon&iacute;vel em estoque' : 'N&atilde;o dispon&iacute;vel',
                    'lblType'           => ($rsItem->fields['estoque_atual'] > 0 && $rsItem->fields['quantidade'] <= $rsItem->fields['estoque_atual']) ? 'text-navy' : 'text-danger'
                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $arrItens);
        } elseif ($oper == 'create') {
            $idProdutoEnable = 1;
            $arrProduto = $this->_comboProduto();
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            $objSmarty->assign('idproduto', $idProdutoEnable );
        } elseif ($oper == 'echo') {

            while (!$rsItem->EOF) {
                $arrItens[] = array(
                    'iditempedido'    => $rsItem->fields['iditempedido'],
                    'idproduto'       => $rsItem->fields['idproduto'],
                    'nome'            => $rsItem->fields['nome'],
                    'unidade'         => $rsItem->fields['unidade'],
                    'quantidade'      => $this->_scmformatNumber($rsItem->fields['quantidade']),
                    'idstatus'        => $rsItem->fields['idstatus'],
                    'nomestatusitem'  => $rsItem->fields['nomestatusitem'],
                    'disponibilidade'   => ($rsItem->fields['estoque_atual'] > 0 && $rsItem->fields['quantidade'] <= $rsItem->fields['estoque_atual']) ? 'Dispon&iacute;vel em estoque' : 'N&atilde;o dispon&iacute;vel',
                    'lblType'           => ($rsItem->fields['estoque_atual'] > 0 && $rsItem->fields['quantidade'] <= $rsItem->fields['estoque_atual']) ? 'text-navy' : 'text-danger'
                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $arrItens);
        }

        // --- Turma ---
        if($rsTurma->RecordCount() > 0){
            $objSmarty->assign('flagdisplay', '' );
            $objSmarty->assign('turmadesc', $rsTurma->fields['abrev'] );
            $objSmarty->assign('aula', $rs->fields['aula']);
        }else{
            $objSmarty->assign('flagdisplay', 'hidden' );
        }

        //-- Display dos Combos de Centro de Custo e Conta Contábil --
        $flagGCompra = $this->_checkOperadorGroupCompra($this->idPerson);

        if($flagGCompra == 1){
            $objSmarty->assign('flgGC', '0' );
            $objSmarty->assign('flagCC', 'hidden' );
        }else{
            $objSmarty->assign('flgGC', '1' );
            $objSmarty->assign('flagCC', '' );
        }

        // Notes
        //
        $lineNotes = $this->_makePedidoNotesScreen($rs->fields['idpedido']);
        $objSmarty->assign('notes', $lineNotes);
    }

    function updatePedidoOperador()
    {
        $nomeArquivo = "";

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $idstatus = $_POST['idstatus'];
        //comentado para verificar motivo de colocar com o id 4
        //if ($idstatus == 1){
            //$idstatus = 4;
        //}

        $idPedidoOperador = $this->getParam('idpedidooperador');
        $iditempedidos = $_POST['iditempedidos'];
        $iditempedidos1 = $_POST['iditempedidos'];
        $idcotacoes = '';
        $motivorejeita = $_POST['motivorejeicao'];
        $flgGC = $_POST['flgGC'];

        if(isset($_POST['idcotacoes'])){
            $idcotacoes =$_POST['idcotacoes'];
        }

        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();

        $dataTmp = $dbPedidoOperador->getPedidoOperador("WHERE idpedido = ".$idPedidoOperador);
        if($dataTmp->fields['idstatus'] == $idstatus){$sendMail = "N";}
        else{$sendMail = "S";}

        $dbPedidoOperador->BeginTrans();

        $flagGCompra = $this->_checkOperadorGroupCompra($this->idPerson);

        if($flagGCompra == 1){$ret = $dbPedidoOperador->updatePedidoOperador($idPedidoOperador,$_POST['dataentrega'],addslashes($_POST['motivo']),$idstatus);}
        else{$ret = $dbPedidoOperador->updatePedidoOperador($idPedidoOperador,$_POST['dataentrega'],addslashes($_POST['motivo']),$idstatus,$_POST['idcontacontabil']);}

        if (!$ret) {
            $dbPedidoOperador->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        switch($idstatus){
            case 9:
            case 21:
                $retrejeita = $dbPedidoOperador->updateMotivo($idPedidoOperador, $motivorejeita);

                if (!$retrejeita) {
                    $dbPedidoOperador->RollbackTrans();
                    if ($this->log)
                        $this->logIt('Change Pedido Status - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                    return false;

                }
                break;

            case 10:
            case 14:
                $retlog = $dbPedidoOperador->insertApprovalLog($idPedidoOperador,$this->idPerson,$idstatus);
                if (!$retlog) {
                    $dbPedidoOperador->RollbackTrans();
                    if ($this->log)
                        $this->logIt('Insert Approval Log - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                    return false;

                }
                break;
            
            case 13:
                $rsaprovador = $dbPedidoOperador->getIdAprovador('WHERE idcontacontabil = '.$_POST['idcontacontabil']);
                if($sendMail == "S"){
                    $retins = $this->dbPedidoAprovador->insertInCharge($idPedidoOperador,$rsaprovador->fields['idperson'],'1','N');
                    if (!$retins) {
                        $dbPedidoOperador->RollbackTrans();
                        if ($this->log)
                            $this->logIt('Insert In Charge Log - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                        return false;

                    }
                }
                break;

            default:
                break;

        }

        if($flgGC == '1'){
            foreach ($iditempedidos1 as $key => $value){

                $retTMP = $this->_getItemPedidoOperadorEcho("AND a.iditempedido = {$value}");
                if (!$retTMP) {
                    $dbPedidoOperador->RollbackTrans();
                    if($this->log)
                        $this->logIt("Can't get request's item data. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                $updItem = $dbPedidoOperador->updateItemPedidoOperador($value,$_POST['produtos'][$value],$_POST['quantidades'][$value]);
                if (!$updItem['success']) {
                    $dbPedidoOperador->RollbackTrans();
                    if($this->log)
                        $this->logIt("Can't update request's item data.\n Message: {$updItem['message']}\n User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                $insLog = $dbPedidoOperador->insertItemLog($value,$retTMP->fields['idproduto'],$retTMP->fields['quantidade'],$_POST['produtos'][$value],$_POST['quantidades'][$value],$_SESSION['SES_COD_USUARIO']);
                if(!$insLog['success']){
                    if ($this->log)
                        $this->logIt("Can't insert log.\n Msg: {$insLog['message']}.\n User: " . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                }
            }

        }

        foreach ($iditempedidos1 as $key => $value){
            $itemStatus = $_POST['idstatusitens'][$value];

            if(in_array($idstatus,$this->arrStatusClose) && in_array($itemStatus,$this->arrItemStAppr)){
                $itemStatus = $idstatus;
                
                $retUpdStock = $this->updateStock($value);
                if (!$retUpdStock) {
                    $dbPedidoOperador->RollbackTrans();
                    return false;
                }
            }

            $ret = $dbPedidoOperador->updateItemPedidoOperadorStatus($value,$itemStatus);
            if (!$ret) {
                $dbPedidoOperador->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
        }

        foreach ($iditempedidos as $key => $value) {

            foreach ($_POST['fornecedores'][$value] as $chave => $valor) {
                if($_POST['valoresunitarios'][$value][$chave] != '' and $_POST['valorestotais'][$value][$chave] != '') {
                    $vfrete = $_POST['valoresfrete'][$value][$chave] != '' ?$_POST['valoresfrete'][$value][$chave] : '0';
                    if($_POST['idcotacao'][$value][$chave] == '' ) {
                        $ret = $dbPedidoOperador->insertPedidoOperadorCotacao($value, $_POST['fornecedores'][$value][$chave], $_POST['valoresunitarios'][$value][$chave], $_POST['valorestotais'][$value][$chave],$vfrete,$_POST['flagcarrier'][$value][$chave]);
                    }else{
                        $ret = $dbPedidoOperador->updatePedidoOperadorCotacao($_POST['idcotacao'][$value][$chave], $_POST['fornecedores'][$value][$chave], $_POST['valoresunitarios'][$value][$chave], $_POST['valorestotais'][$value][$chave],$vfrete,$_POST['flagcarrier'][$value][$chave]);
                    }
                    if (!$ret) {
                        $dbPedidoOperador->RollbackTrans();
                        if ($this->log)
                            $this->logIt('Update Pedido  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                        return false;
                    }

                    if (!empty($_FILES['arquivos']['name'][$value][$chave])) {
                        if($this->saveMode == "aws-s3"){
                            $aws = $this->getAwsS3Client();
                            $arrayRet = $aws->removeFile($this->cotacaoDir . $ret . '.pdf');
                            if(!$arrayRet['success']) {
                                if($this->log)
                                    $this->logIt('I could not remove the file: '.$ret . '.pdf'.' from S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                            }
                        }elseif($this->saveMode == "disk"){
                            if (file_exists($this->cotacaoDir . $ret . '.pdf')) {
                                @unlink($this->cotacaoDir . $ret . '.pdf');
                            }
                        }                        

                        $fileName = $_FILES['arquivos']['name'][$value][$chave];
                        $tempFile = $_FILES['arquivos']['tmp_name'][$value][$chave];
                        $extension = strrchr($fileName, ".");
                        $targetPath = $this->helpdezkPath . $this->cotacaoDir;

                        $nomeArquivo = $ret . $extension;

                        $targetFile = $this->cotacaoDir . $ret . $extension;

                        if($this->saveMode == "aws-s3"){
                            $aws = $this->getAwsS3Client();

                            $arrayRet = $aws->copyToBucket($tempFile,$targetFile);
                            
                            if($arrayRet['success']) {
                                if($this->log)
                                    $this->logIt("Save temp attachment file " . $nomeArquivo . ' - program: '.$this->program ,7,'general',__LINE__);   
                            } else {
                                if($this->log)
                                    $this->logIt('I could not save the temp file: '.$nomeArquivo.' in S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                            }
                        }elseif($this->saveMode == "disk"){
                            if(!is_dir($this->cotacaoDir)) {
                                mkdir ($this->cotacaoDir, 0777 ); // criar o diretorio
                            }
                            if (move_uploaded_file($tempFile, $targetFile)) {
                                if ($this->log) {
                                    $this->logIt("Save cotação: # " . $nomeArquivo . ' - File: ' . $targetFile . ' - program: ' . $this->program, 7, 'general', __LINE__);
                                }
                            } else {
                                if ($this->log) {
                                    $this->logIt("Can't save cotação: # " . $nomeArquivo . ' - File: ' . $targetFile . ' - program: ' . $this->program, 3, 'general', __LINE__);
                                }
                            }
                        }

                    }
                    $ret1 = $dbPedidoOperador->updatePedidoOperadorCotacaoNomeArquivo($ret, $nomeArquivo);
                    if (!$ret1) {
                        $dbPedidoOperador->RollbackTrans();
                        if ($this->log) {
                            $this->logIt('Nome do arquivo: ' . $nomeArquivo . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                        }
                        return false;
                    }
                }
            }

        }

        if($sendMail == "S"){
            $rsStatusP = $this->dbStatus->getStatus("AND a.idstatus = $idstatus");
            $this->_savePedidoNote($this->idPerson,$idPedidoOperador,'<p><strong>Status do Pedido de Compra:</strong> '.$rsStatusP->fields['nome'].'</p>','1');
        }

        $aRet = array(
            "idpedidooperador" => $idPedidoOperador,
            "status" => 'OK',
            "sendmail" => $sendMail
        );

        $dbPedidoOperador->CommitTrans();

        echo json_encode($aRet);

    }

    function statusPedidoOperador()
    {
        $idpedidooperador = $this->getParam('idpedidooperador');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();

        $ret = $dbPedidoOperador->changeStatus($idpedidooperador,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Pedido Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idpedidooperador" => $idpedidooperador,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function ajaxProduto()
    {
        echo $this->comboProdutoHtml();
    }

    function ajaxFornecedor()
    {
        echo $this->_comboFornecedorHtml();
    }

    function ajaxCentroCusto()
    {
        echo $this->_comboCentroCustoHtml();
    }

    function ajaxContaContabil()
    {
        echo $this->_comboContaContabilHtml($_POST['centrodecustoId'],$_POST['pedidoId']);
    }

    public function comboProdutoHtml()
    {
        $arrType = $this->_comboProduto();
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

    public function _comboFornecedorHtml()
    {
        $arrType = $this->_comboFornecedor();
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

    public function _comboCentroCustoHtml()
    {
        $arrType = $this->_comboCentroCusto();
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

    public function _comboContaContabilHtml($centrodecustoId,$pedidoId)
    {
        if($pedidoId){
            $rsPedidoOperador = $this->_getPedidoOperador("where idpedido = $pedidoId") ;
            $iddefault = $rsPedidoOperador->fields['idcontacontabil'];
        }else{$iddefault = 1;}

        $arrType = $this->_comboContaContabilAprovador('and b.idcentrocusto = ' . $centrodecustoId);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {

            if ($arrType['ids'][$indexKey] == $iddefault) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function makeReport()
    {
        $idpedidooperador = $_POST['idpedidooperador'];
        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $rsPedidoOperador = $dbPedidoOperador->getRequestDataImprimir("WHERE scm_tbpedido.idpedido = $idpedidooperador");

        //permissão para download do relatório
        $idperson = $_SESSION['SES_COD_USUARIO'];

        $pdf = $this->returnHtml2pdf();

        /*
         *  Variables
         */
        //Parâmetros para o cabeçalho
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() ); //Logo
        $leftMargin   = 10; //variável para margem à esquerda
        $this->SetPdfTitle(html_entity_decode(utf8_decode('Pedido de Compra'),ENT_QUOTES, "ISO8859-1")); //Titulo //Titulo
        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ; //numeração página
        $this->SetPdfleftMargin($leftMargin);
        //Parâmetros para a Fonte a ser utilizado no relatório
        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);

        $pdf->AliasNbPages();
        //$this->SetPdfHeaderData($a_HeaderData);

        $pdf->AddPage(); //Cria a página no arquivo pdf

        $pdf = $this->ReportPdfHeader($pdf); //Insere o cabeçalho  relatório

        $totalPedido = 0;
        $controlaTipo = 0;
        $controlaPedido = 0;
        $tituloItens = array(
            array('title'=>'Itens','cellWidth'=>60,'cellHeight'=>4,'titleAlign'=>'C'),
            array('title'=>'Qtd','cellWidth'=>19,'cellHeight'=>4,'titleAlign'=>'C'),
            array('title'=>'V. Unit','cellWidth'=>20,'cellHeight'=>4,'titleAlign'=>'C'),
            array('title'=>'V. Total','cellWidth'=>20,'cellHeight'=>4,'titleAlign'=>'C'),
            array('title'=>'Status','cellWidth'=>60,'cellHeight'=>4,'titleAlign'=>'C')
        );
        foreach ($rsPedidoOperador as $key => $value) {

            $CelHeight = 4;

            $id = $value['idpedido'];
            $tituloPedido = array(array('title'=>html_entity_decode(utf8_decode("N° Pedido $id"), ENT_QUOTES, "ISO8859-1"),'cellWidth'=>179,'cellHeight'=>4,'titleAlign'=>'C'));

            if ($controlaTipo != $id) {
                if ($controlaTipo != 0) {
                    $pdf->Cell($leftMargin);
                    $pdf->Cell(18, $CelHeight * 2, '', 0, 1, 'L', 0);
                }
                $controlaTipo = $id;
                $controlaPedido = 0;

                $pdf->Cell($leftMargin);
                $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
                $this->makePdfLineBlur($pdf, $tituloPedido);

                $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
                $pdf->SetFont('Arial', '', 8);
            }

            if ($controlaPedido != $value['idpedido']) {

                $controlaPedido = $value['idpedido'];

                $pdf->Cell($leftMargin);
                $pdf->Cell(15, $CelHeight, html_entity_decode('Solicitante', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(124, $CelHeight, utf8_decode($value['nomepessoa']), 0, 0, 'L', 0);

                $pdf->Cell(15, $CelHeight, html_entity_decode('Data/Hora', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(25, $CelHeight, date("d/m/Y H:i"), 0, 1, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(23, $CelHeight, html_entity_decode(utf8_decode('Centro de Custo'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(67, $CelHeight, utf8_decode($value['codigonomecentrodecusto']), 0, 0, 'L', 0);

                $pdf->Cell(23, $CelHeight, html_entity_decode(utf8_decode('Conta Contábil'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(66, $CelHeight, utf8_decode($value['codigonomecontacontabil']), 0, 1, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(18, $CelHeight, html_entity_decode('Data entrega', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);

                if ($value['idturma']) {
                    $pdf->Cell(18, $CelHeight, utf8_decode($value['dataentrega']), 0, 0, 'L', 0);
                    $pdf->Cell(95, $CelHeight, '', 0, 0, 'L', 0);
                    $pdf->Cell(18, $CelHeight, html_entity_decode('Turma', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(18, $CelHeight, utf8_decode($value['nometurma']), 0, 1, 'L', 0);
                }else{
                    $pdf->Cell(18, $CelHeight, utf8_decode($value['dataentrega']), 0, 1, 'L', 0);
                }

                $pdf->Cell($leftMargin);
                $pdf->Cell(21, $CelHeight, html_entity_decode('Motivo Compra', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->MultiCell(158, $CelHeight, utf8_decode($value['motivo']), 0, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(23, $CelHeight, html_entity_decode('Status do Pedido', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(23, $CelHeight, utf8_decode($value['nomestatus']), 0, 1, 'L', 0);
                $pdf->Ln(4);

                $pdf->Cell($leftMargin);
                $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
                $this->makePdfLineBlur($pdf, $tituloItens);
                $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
                $pdf->SetFont('Arial', '', 8);
            }

            $rsCotacao = $this->_getItemPedidoOperadorCotacaoEcho($value['idpedido']." AND scm_tbcotacao.iditempedido = ".$value['iditempedido']);

            if($rsCotacao->RecordCount() > 0){$totalPedido += $rsCotacao->fields['valor_total'];}

            $pdf->Cell($leftMargin);
            $pdf->Cell(60, $CelHeight, utf8_decode($value['nomeproduto']), 0, 0, 'L', 0);
            $pdf->Cell(19, $CelHeight, utf8_decode($this->_scmformatNumber($value['quantidadeproduto'])), 0, 0, 'C', 0);
            $pdf->Cell(20, $CelHeight, utf8_decode(number_format($rsCotacao->fields['valor_unitario'],2,',','.')), 0, 0, 'C', 0);
            $pdf->Cell(20, $CelHeight, utf8_decode(number_format($rsCotacao->fields['valor_total'],2,',','.')), 0, 0, 'C', 0);
            $pdf->Cell(60, $CelHeight, utf8_decode($value['nomestatusitem']), 0, 1, 'C', 0);

        }

        $this->makePdfLine($pdf,$leftMargin,197);

        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $pdf->Cell($leftMargin);
        $pdf->Cell(99, $CelHeight, html_entity_decode('TOTAL', ENT_QUOTES, "ISO8859-1"), 0, 0, 'R', 0);
        $pdf->Cell(20, $CelHeight, utf8_decode(number_format($totalPedido,2,',','.')), 0, 0, 'C', 0);
        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        $pdf->Ln();
        $this->makePdfLine($pdf,$leftMargin,197);

        //Parâmetros para salvar o arquivo
        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf"; //nome do arquivo
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ; //caminho onde é salvo o arquivo
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ; //link para visualização em nova aba/janela

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {//validação

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                $this->logIt("Make report request # ". $rsPedidoOperador->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }


        $pdf->Output($fileNameWrite,'F'); //a biblioteca cria o arquivo
        echo $fileNameUrl; //retorno para a função javascript


    }

    public function sendEmail()
    {
        $this->loadModel('fornecedor_model');
        $dbPedidoItem = new fornecedor_model();
        $itens = $_POST['itens'];


        $itens = (explode(',', $_POST['itens']));
        $result = [];
        foreach ($itens as $key => $value) {
            $where = "WHERE idperson = " . $_POST['fornecedor'];

            $ret = $dbPedidoItem->getFornecedor($where);
            if (!$ret) {
                if ($this->log)
                    $this->logIt('Change Pedido Status - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }


            $result[] = array(
                'iditempedido' => $key,
                'nomefornecedor' => $ret->fields['name'],
                'email' => $ret->fields['email'],
                'quantidade' => $_POST['quantidade'][$value],
                'nomeproduto' => $_POST['nome'][$value],

            );
        }
        //foreach ($result as $key => $value){
            //array com todos os itens para envio de email
            print_r($result);
        //}

        //$ret = $this->_sendNotification('','email',$code_request);

    }

    public function modalDeliveryTicket()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $idpedido = $_POST['idpedido'];

        $rsPedidoCompra = $this->_getPedidoCompra("WHERE idpedido = $idpedido") ;
        $rsItemPedidoCompra = $this->_getItemPedidoOperadorEcho("AND a.idpedido = $idpedido");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idpedido");

        if($rsPedidoTurma->RecordCount() > 0){$flagDisplay = 'S';}
        else{$flagDisplay = 'N';}

        list($dataTMP,$timeTMP) = explode(' ',$rsPedidoCompra->fields['dataentrega']);
        list($anoTMP,$mesTMP,$diaTMP) = explode('-',$dataTMP);
        $dtEntregaFinal = $diaTMP.'/'.$mesTMP.'/'.$anoTMP;

        $listItem = '';
        while(!$rsItemPedidoCompra->EOF){
            if($rsItemPedidoCompra->fields['idstatus'] == 6 || $rsItemPedidoCompra->fields['idstatus'] == 7 || $rsItemPedidoCompra->fields['idstatus'] == 19){
                $listItem .= "<div class='form-group'>
                                <label class='col-sm-2 control-label'><input type='checkbox' id='itensdelivery' name='itensdelivery[]' value='".$rsItemPedidoCompra->fields['iditempedido']."'></label>
                                <div class='col-sm-10'>
                                    <input type='text' class='form-control input-sm' value='".$rsItemPedidoCompra->fields['nome']." / ".str_replace('.',',',$rsItemPedidoCompra->fields['quantidade'])."' readonly />
                                </div>
                           </div>";
            }
            $rsItemPedidoCompra->MoveNext();
        }

        $aRet = array(
            "idpedidocompra" => $idpedido,
            "token" => $token,
            "dataentrega" => $dtEntregaFinal,
            "displayturma" => $flagDisplay,
            "turma" => $rsPedidoTurma->fields['abrev'],
            "statuspedido" => $rsPedidoCompra->fields['nomestatus'],
            "motivopedido" => $rsPedidoCompra->fields['motivo'],
            "ccusto" => $rsPedidoCompra->fields['nomecentrodecusto'],
            "ccontabil" => $rsPedidoCompra->fields['nomecontacontabil'],
            "itens" => $listItem
        );

        echo json_encode($aRet);

    }

    public function printDeliveryTicket()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();
        $dbPedidoOperador->BeginTrans();

        $idpedido = $_POST['idpedidodelivery'];
        $arrItem = $_POST['itensdelivery'];

        $rsPedidoCompra = $this->_getPedidoCompra("WHERE idpedido = $idpedido") ;
        $rsItemPedidoCompra = $this->_getItemPedidoOperadorEcho("AND a.idpedido = $idpedido AND a.idstatus != 9");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idpedido");

        if($rsPedidoTurma->RecordCount() > 0){$flagDisplay = 'S';}
        else{$flagDisplay = 'N';}

        list($dataTMP,$timeTMP) = explode(' ',$rsPedidoCompra->fields['dataentrega']);
        list($anoTMP,$mesTMP,$diaTMP) = explode('-',$dataTMP);
        $dtEntregaFinal = $diaTMP.'/'.$mesTMP.'/'.$anoTMP;

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        require_once DOCUMENT_ROOT . path . '/includes/classes/cupomMaker/cupomMaker.php';

        $cupom = new cupomMaker();

        $txt_info = array();
        $txt_itens = array();
        $txt_rodape = array();

        $txt_info[] = $cupom->addEspacos('Pedido #: '.$idpedido, 40, 'F');
        $txt_info[] = $cupom->addEspacos('Solicitado por: ', 40, 'F');
        $txt_info[] = $cupom->addEspacos($rsPedidoCompra->fields['nomepessoa'], 40, 'F');
        if($rsPedidoTurma->RecordCount() > 0){$txt_info[] = 'Turma: '.$rsPedidoTurma->fields['abrev'];}
        $txt_info[] = ' ';

        $txt_itens[] = array('ITEM', 'Qtd');
        $listItem = '';
        while(!$rsItemPedidoCompra->EOF){
            if(in_array($rsItemPedidoCompra->fields['iditempedido'],$arrItem)){
                $txt_itens[] = array($rsItemPedidoCompra->fields['nome'], $rsItemPedidoCompra->fields['quantidade']);

                /*$ret = $dbPedidoOperador->updateItemPedidoOperadorStatus($rsItemPedidoCompra->fields['iditempedido'],7);
                if (!$ret) {
                    $dbPedidoOperador->RollbackTrans();
                    if($this->log)
                        $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }*/
            }
            $rsItemPedidoCompra->MoveNext();
        }

        $txt_rodape[] = '________________________________________';
        $txt_rodape[] = ' Assinatura ';
        $txt_rodape[] = ' ';
        $txt_rodape[] = 'Nome: __________________________________';
        $txt_rodape[] = ' ';
        $txt_rodape[] = 'Data: __________________________________';
        $txt_rodape[] = ' '; // força pular uma linha

        $cabecalho = $cupom->centraliza('Comprovante de Entrega');

        foreach ($txt_itens as $item) {
            $itens[] = $cupom->addEspacos($item[0], 35, 'F') . $cupom->addEspacos($item[1], 5, 'F');
        }

        $txt = $cabecalho
            . "\r\n\r\n\r\n"
            . implode("\r\n", $txt_info)
            . "\r\n"
            . implode("\r\n", $itens)
            . "\r\n\r\n"
            . implode("\r\n", $txt_rodape)
            . "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";

        //Parâmetros para salvar o arquivo
        $filename = $idpedido.".txt"; //nome do arquivo
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ; //caminho onde é salvo o arquivo
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ; //link para visualização em nova aba/janela

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {//validação

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                $this->logIt("Make report request # ". $rsPedidoOperador->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }

        // cria o arquivo
        $_file = fopen($fileNameWrite,"w");
        fwrite($_file,$txt);
        fclose($_file);

        $dbPedidoOperador->CommitTrans();

        echo $fileNameUrl;

    }

    function changeStatusDelivery()
    {
        $idpedido = $_POST['idpedido'];
        $statusCount = 0;

        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();

        $rsItemPedidoCompra = $this->_getItemPedidoOperadorEcho("AND a.idpedido = $idpedido");
        $rowCount = $rsItemPedidoCompra->RecordCount();

        while(!$rsItemPedidoCompra->EOF){
            if($rsItemPedidoCompra->fields['idstatus'] == 7){
                $statusCount++;
            }
            $rsItemPedidoCompra->MoveNext();
        }

        if($statusCount == $rowCount){

            $ret = $dbPedidoOperador->changeStatus($idpedido,7);

            if (!$ret) {
                if($this->log)
                    $this->logIt('Change Pedido Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $aRet = array(
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    public function savePedidoNote()
    {
        $idPerson        = $_SESSION['SES_COD_USUARIO'];
        $idpedido     = $_POST['idpedido'];
        $noteContent     =  addslashes($_POST['noteContent']);
        $displayType     =  addslashes($_POST['displayType']);

        $ins = $this->_savePedidoNote($idPerson,$idpedido,$noteContent,$displayType);

        if($ins){
            $lineNotes = $this->_makePedidoNotesScreen($idpedido);

            $aRet = array(
                "status" => "OK",
                "addednotes" => $lineNotes
            );
        }else{
            $aRet = array(
                "status" => "NO"
            );
        }

        echo json_encode($aRet);


    }

    public function checkEdit()
    {
        $idpedido = $_POST['idpedido'];
        $rsPedidoCompra = $this->_getPedidoCompra("WHERE idpedido = $idpedido") ;

        $deadLine =  $this->_getExpireDate($rsPedidoCompra->fields['datapedido'],2);
        $deadLine = date("Y-m-d", strtotime($deadLine));
        $today = date("Y-m-d");
        //echo $this->_getUserGroupRole($this->idPerson);
        $retFlag = $this->_getUserGroupType($this->idPerson);
        //echo $today .' - '. $deadLine;
        if($retFlag == 1){
            if(($today <= $deadLine) && $rsPedidoCompra->fields['idstatus'] == 1){
                $flag = 1;
            }else{
                $flag = 0;
            }
        }else{
            $flag = 1;
        }

        $aRet = array("status" => $flag);

        echo json_encode($aRet);

    }

    public function updateStock($idtem)
    {
        $rs = $this->dbPedidoOperador->getItemPedidoOperadorEcho("AND a.iditempedido = $idtem");
        if(!$rs){
            if($this->log)
                $this->logIt('Can\'t get Item Pedido data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ret = $this->_updateStock($rs->fields['idproduto'],$rs->fields['quantidade'],2);

        if(!$ret){
            if($this->log)
                $this->logIt('Can\'t Update Stock - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        return true;

    }

    public function formExchange()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();
        $this->access($smarty,$this->idPerson,$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $idPedidoOperador = $this->getParam('idpedidooperador');

        $rsPedidoOperador = $this->_getPedidoOperador("where idpedido = $idPedidoOperador") ;
        $rsItemPedidoOperador = $this->_getItemPedidoOperadorEcho("AND a.idpedido = $idPedidoOperador");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoOperador");

        $rsItemPedidoOperadorCotacao = $this->_getItemPedidoOperadorCotacaoEcho("$idPedidoOperador");
        $rsCotacao = [];
        $i = 0;
        $total = 0;
        $totalitens = 0;
        $totalfrete = 0;

        foreach ($rsItemPedidoOperadorCotacao as $key => $value){
            $rsCotacao[$value['iditempedido']][$i] = [
                'idcotacao'      => $value['idcotacao'],
                'arquivo'       => $value['arquivo'],
                'idperson'       => $value['idperson'],
                'valor_unitario' => $value['valor_unitario'],
                'valor_total'    => $value['valor_total'],
                'valor_frete'    => $value['valor_frete'],
                'flg_aprovado'   => $value['flg_aprovado'],
                'idproduto'   => $value['idproduto'],
                'flg_carrier'   => $value['flg_carrier']
            ];

            $totalitens += $value['valor_total'];
            $totalfrete += $value['valor_frete'];
            $i++;
        }

        $total = $totalitens + $totalfrete;

        $this->makeScreenPedidoOperador($smarty,$rsPedidoOperador,$rsItemPedidoOperador,$rsCotacao,$rsPedidoTurma,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('caminho', $this->cotacaoUrl);

        $smarty->assign('hidden_idpedidooperador', $idPedidoOperador);

        $smarty->assign('totalitens', number_format($totalitens,2,',',''));
        $smarty->assign('totalfrete', number_format($totalfrete,2,',',''));
        $smarty->assign('totalpedido', number_format($total,2,',',''));

        $rsTypeUser = $this->_getSCMTypePerson("AND a.idperson = ".$this->idPerson);

        if($rsTypeUser){$displaytype = '';}
        else{$displaytype = 'hidden';}
        $smarty->assign('flagdispplay',$displaytype);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', '');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display('scm-maintenance.tpl');
        }else{
            $smarty->display('scm-pedidooperador-exchange.tpl');
        }

    }

    public function saveExchange(){
        //echo "",print_r($_POST,true),"\n";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idpedido = $_POST['idpedidooperador'];
        $iditempedidos = $_POST['iditempedidos'];
        $produtos = $_POST['produtos'];
        $quantidades = $_POST['quantidades'];

        $flgUpd = 0;
        $i = 1;
        foreach($iditempedidos as $key=>$val){
            
            $itemTmp = $this->_getItemPedidoOperadorEcho("AND a.iditempedido = {$val}");
            if(!$itemTmp){
                if($this->log)
                    $this->logIt("Can't get order item data. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            
            if($produtos[$val] != $itemTmp->fields['idproduto']){
                //First, update the stock of the previous product
                $updItemTmp = $this->_updateStock($itemTmp->fields['idproduto'],$itemTmp->fields['quantidade'],1);
                if(!$updItemTmp){
                    if($this->log)
                        $this->logIt("Can't update stock - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Updates the request item with the new product
                $retExc = $this->dbPedidoOperador->updateItemPedidoOperador($val,$produtos[$val],$quantidades[$val]);
                if(!$retExc['success']){
                    if($this->log)
                        $this->logIt("Can't update request item - {$retExc['success']} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }

                //Lastly, updates the new product stock
                $updItemTmp = $this->_updateStock($produtos[$val],$quantidades[$val],2);
                if(!$updItemTmp){
                    if($this->log)
                        $this->logIt("Can't update item exchanged stock - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                
                $flgUpd = 1;
            }

            $itenList["item_{$i}"] = "ID: {$val} - productID: {$produtos[$val]} - qt: {$quantidades[$val]}";
            $i++;
        }

        $msg = $flgUpd == 1 ? $this->getLanguageWord('item_exchanged') : $this->getLanguageWord('no_item_exchanged');
        $alerttype = $flgUpd == 1 ? "success" : "warning";

        $aRet = array(
            "success" => true,
            "message" => $msg,
            "alerttype" => $alerttype,
        );

        if($flgUpd == 1){
            $params = array(
                "data2log"   => array('scm_tbpedido','*',"WHERE idpedido = $idpedido"), //dados para inserir no tblog
                "adddata" => $itenList, //dados adicionais para inserir no tblog
                "programID"   => $this->idprogram,
                "userID"      => $_SESSION['SES_COD_USUARIO'],
                "tag"       => 'exchange-product'
            );

            $this->makeLog($params);
        }

        echo json_encode($aRet);

    }


}