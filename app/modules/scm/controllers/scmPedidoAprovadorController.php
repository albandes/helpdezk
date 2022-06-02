<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmPedidoAprovador extends scmCommon
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

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('scmPedidoAprovador');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

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
        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', '');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display('scm-maintenance.tpl');
        }else{
            if($permissions[0] == "Y"){
                $smarty->display('scm-pedidoaprovador-grid.tpl');
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
        $rsCContabilPerson = $this->_getCContabilAprovador($this->idPerson);

        $arrCContabil = array();
        $ccontabilw = '';
        while(!$rsCContabilPerson->EOF){
            if(!in_array($rsCContabilPerson->fields['idcontacontabil'],$arrCContabil)){
                $ccontabilw .= $rsCContabilPerson->fields['idcontacontabil'].',';
            }
            $rsCContabilPerson->MoveNext();
        }

        $ccontabilw = substr($ccontabilw,0,-1);

        if($where == '') $where .= "WHERE ";
        else $where .= "AND ";

        if($ccontabilw) {$where .= "vwp.idcontacontabil IN ($ccontabilw) AND ";}

        $where .= "idstatus IN (4,13) AND id_in_charge = ".$this->idPerson." AND ind_in_charge = 1";

        $idStatus = $_POST['idstatus'];
        if($idStatus && $idStatus != 'ALL') $where .= "AND idstatus = $idStatus";

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='dataentrega';
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

        $count = $this->_getNumPedidoAprovadores($where);

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

        $rsPedidoAprovador = $this->dbPedidoAprovador->getPedidoAprovadorGrid($where ,$order,null,$limit);

        while (!$rsPedidoAprovador->EOF) {
            $status_fmt = ($rsPedidoAprovador->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

            $aColumns[] = array(
                'id'            => $rsPedidoAprovador->fields['idpedido'],
                'nomepessoa'    => $rsPedidoAprovador->fields['nomepessoa'],
                'nomestatus'    => $rsPedidoAprovador->fields['nomestatus'],
                'idstatus'      => $rsPedidoAprovador->fields['idstatus'],
                'datapedido'    => $rsPedidoAprovador->fields['datapedido'],
                'dataentrega'   => $rsPedidoAprovador->fields['dataentrega'],
                'foradoprazo'   => $rsPedidoAprovador->fields['foradoprazo'],
                'motivo'        => $rsPedidoAprovador->fields['motivo'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsPedidoAprovador->fields['status'],
                'turma'        => $rsPedidoAprovador->fields['nome']

            );
            $rsPedidoAprovador->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formUpdatePedidoAprovador()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idPedidoAprovador = $this->getParam('idpedidoaprovador');

        $rsPedidoAprovador = $this->_getPedidoAprovador("where idpedido = $idPedidoAprovador") ;
        $rsItemPedidoAprovador = $this->_getItemPedidoAprovador("AND a.idpedido = $idPedidoAprovador");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoAprovador");

        $rsItemPedidoAprovadorCotacao = $this->_getItemPedidoOperadorCotacaoEcho("$idPedidoAprovador");
        $rsCotacao = [];
        $i = 0;
        $total = 0;
        $totalitens = 0;
        $totalfrete = 0;

        foreach ($rsItemPedidoAprovadorCotacao as $key => $value){
            $rsCotacao[$value['iditempedido']][$i] = [
                'idcotacao'      => $value['idcotacao'],
                'idperson'       => $value['idperson'],
                'valor_unitario' => $value['valor_unitario'],
                'nomefornecedor' => $value['nomefornecedor'],
                'valor_total'    => $value['valor_total'],
                'valor_frete'    => $value['valor_frete'],
                'flg_aprovado'   => $value['flg_aprovado'],
                'arquivo'   => $value['arquivo'],
                'idproduto'   => $value['idproduto']
            ];

            $totalitens += $value['valor_total'];
            $totalfrete += $value['valor_frete'];
            $i++;
        }

        $total = $totalitens + $totalfrete;

        $this->makeScreenPedidoAprovador($smarty,$rsPedidoAprovador,$rsItemPedidoAprovador,$rsCotacao,$rsPedidoTurma,'update');

        $smarty->assign('token', $token) ;
        $smarty->assign('caminho', $this->cotacaoUrl);
        $smarty->assign('hidden_idpedidoaprovador', $idPedidoAprovador);

        $smarty->assign('totalitens', number_format($totalitens,2,',',''));
        $smarty->assign('totalfrete', number_format($totalfrete,2,',',''));
        $smarty->assign('totalpedido', number_format($total,2,',',''));

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
            $smarty->display('scm-pedidoaprovador-update.tpl');
        }

    }

    public function echoPedidoAprovador()
    {

        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();


        $idPedidoAprovador = $this->getParam('idpedidoaprovador');

        $rsPedidoAprovador = $this->_getPedidoAprovador("where idpedido = $idPedidoAprovador") ;
        $rsItemPedidoAprovador = $this->_getItemPedidoAprovador("AND a.idpedido = $idPedidoAprovador");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoAprovador");

        $rsItemPedidoAprovadorCotacao = $this->_getItemPedidoOperadorCotacaoEcho("$idPedidoAprovador");
        $rsCotacao = [];
        $i = 0;
        $total = 0;
        $totalitens = 0;
        $totalfrete = 0;

        foreach ($rsItemPedidoAprovadorCotacao as $key => $value){
            $rsCotacao[$value['iditempedido']][$i] = [
                'idcotacao'      => $value['idcotacao'],
                'idperson'       => $value['idperson'],
                'valor_unitario' => $value['valor_unitario'],
                'nomefornecedor' => $value['nomefornecedor'],
                'valor_total'    => $value['valor_total'],
                'valor_frete'    => $value['valor_frete'],
                'flg_aprovado'   => $value['flg_aprovado'],
                'arquivo'   => $value['arquivo'],
                'idproduto'   => $value['idproduto']
            ];

            $totalitens += $value['valor_total'];
            $totalfrete += $value['valor_frete'];
            $i++;
        }

        $total = $totalitens + $totalfrete;
        $this->makeScreenPedidoAprovador($smarty,$rsPedidoAprovador,$rsItemPedidoAprovador,$rsCotacao,$rsPedidoTurma,'echo');

        $smarty->assign('token', $token) ;
        $smarty->assign('caminho', $this->cotacaoUrl);
        $smarty->assign('hidden_idpedidoaprovador', $idPedidoAprovador);

        $smarty->assign('totalitens', number_format($totalitens,2,',',''));
        $smarty->assign('totalfrete', number_format($totalfrete,2,',',''));
        $smarty->assign('totalpedido', number_format($total,2,',',''));

        $smarty->assign('hidden_typeoperation', 'view');

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', '');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display('scm-maintenance.tpl');
        }else{
            $smarty->display('scm-pedidoaprovador-echo.tpl');
        }

    }

    function makeScreenPedidoAprovador($objSmarty,$rs,$rsItem,$rsCotacao,$rsTurma,$oper)
    {

        // --- Solicitante ---
        $objSmarty->assign('personname',$rs->fields['nomepessoa']);

        // --- Array de produtos com as cotações ---
        $objSmarty->assign('rsCotacao',$rsCotacao);

        //codigo + nome centro de custo
        $objSmarty->assign('codigonomecentrodecusto',$rs->fields['codigonomecentrodecusto']);

        //codigo + nome contacontabil
        $objSmarty->assign('codigonomecontacontabil',$rs->fields['codigonomecontacontabil']);

        // --- Data Entrega ---
        $objSmarty->assign('dataentrega',$rs->fields['dataentrega']);

        // --- Fornecedor ---
        if ($oper == 'update') {
            $idFornecedorEnable = $rs->fields['idfornecedor'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomefornecedor',$rs->fields['name']);
        } else {
            $arrFornecedor = $this->_comboFornecedor();
            $objSmarty->assign('fornecedorids',  $arrFornecedor['ids']);
            $objSmarty->assign('fornecedorvals', $arrFornecedor['values']);
            $objSmarty->assign('idfornecedor', $idFornecedorEnable );
        }

        // --- Status ---
        if ($oper == 'update') {
            $idStatusEnable = $rs->fields['idstatus'];
        }
        if ($oper == 'echo') {
            $objSmarty->assign('nomestatus',$rs->fields['nomestatus']);
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

        // --- Motivo Compra ---
        if ($oper == 'update') {
            if (empty($rs->fields['motivo']))
                $objSmarty->assign('plh_motivo','Informe o motivo da compra.');
            else
                $objSmarty->assign('motivo',$rs->fields['motivo']);
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
        $arrProduto = $this->_comboProduto();
        $objSmarty->assign('produtoids',  $arrProduto['ids']);
        $objSmarty->assign('produtovals', $arrProduto['values']);

        while (!$rsItem->EOF) {

            $arrItens[] = array(
                'iditempedido'    => $rsItem->fields['iditempedido'],
                'idpedido'       => $rsItem->fields['idpedido'],
                'idproduto'      => $rsItem->fields['idproduto'],
                'nome'            => $rsItem->fields['nome'],
                'unidade'       => $rsItem->fields['unidade'],
                'quantidade'     => $rsItem->fields['quantidade'],
                'idstatus'     => $rsItem->fields['idstatus'],
                'nomestatusitem' => $rsItem->fields['nomestatusitem']

            );
            $rsItem->MoveNext();
        }

        $objSmarty->assign('arrItens',$arrItens);

        // --- Turma ---
        if($rsTurma->RecordCount() > 0){
            $objSmarty->assign('flagdisplay', '' );
            $objSmarty->assign('turmadesc', $rsTurma->fields['abrev'] );
            $objSmarty->assign('aula', $rs->fields['aula']);
        }else{
            $objSmarty->assign('flagdisplay', 'hidden' );
        }

        // --- Repassar para ---
        $arrAprovador = $this->_comboAprovador();
        $objSmarty->assign('aprovatorids',  $arrAprovador['ids']);
        $objSmarty->assign('aprovatorvals', $arrAprovador['values']);
        $objSmarty->assign('idaprovator', $arrAprovador['ids'][0]);

        //-- Display do botão Repassar --
        /*$flagRepass = $this->_isInCharge($rs->fields['idpedido'],$this->idPerson);

        if($flagRepass == 1){$objSmarty->assign('flagRepass', 'hidden' );}
        else{$objSmarty->assign('flagRepass', '' );}*/

    }

    function updatePedidoAprovador()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idPedidoAprovador = $this->getParam('idpedidoaprovador');

        $this->loadModel('pedidoaprovador_model');
        $dbPedidoAprovador = new pedidoaprovador_model();

        $dataTmp = $this->_getPedidoOperador("WHERE idpedido = ".$idPedidoAprovador);
        if($dataTmp->fields['idstatus'] == $_POST['idstatus']){$sendMail = "N";}
        else{$sendMail = "S";}

        $dbPedidoAprovador->BeginTrans();

        $ret = $dbPedidoAprovador->updatePedidoAprovador($idPedidoAprovador,$_POST['idstatus']);
        if (!$ret) {
            $dbPedidoAprovador->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($_POST['idstatus'] == 9) {

            $retrejeita = $dbPedidoAprovador->updateMotivo($idPedidoAprovador, addslashes($_POST['motivorejeicao']));

            if (!$retrejeita) {
                $dbPedidoAprovador->RollbackTrans();
                if ($this->log)
                    $this->logIt('Change Pedido Status - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;

            }

        }elseif($_POST['idstatus'] == 10 or $_POST['idstatus'] == 14){
            $retlog = $dbPedidoAprovador->insertApprovalLog($idPedidoAprovador,$this->idPerson,$_POST['idstatus']);
            if (!$retlog) {
                $dbPedidoAprovador->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Approval Log - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;

            }
        }

        foreach ($_POST['idcotacao'] as $key => $value){
            $flg_aprovado = 0;
            $retItem = $dbPedidoAprovador->updateCotacaoAprovador($value,$flg_aprovado);
            if (!$retItem) {
                $dbPedidoAprovador->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach ($_POST['flg_aprovado'] as $key => $value){
            $flg_aprovado = 1;
            $retItem = $dbPedidoAprovador->updateCotacaoAprovador($value,$flg_aprovado);
            if (!$retItem) {
                $dbPedidoAprovador->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach ($_POST['idstatusitens'] as $key => $value) {
            $retItem = $dbPedidoAprovador->updateItemPedidoAprovador($key,$value);
            if (!$retItem) {
                $dbPedidoAprovador->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach ($_POST['quantidades'] as $key => $value){
            $retItemQt = $dbPedidoAprovador->updateItemQuantidade($key,$value);
            if (!$retItemQt) {
                $dbPedidoAprovador->RollbackTrans();
                if($this->log)
                    $this->logIt('Update quantidade do Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        if($sendMail == "S"){
            $rsStatusP = $this->dbStatus->getStatus("AND a.idstatus = ".$_POST['idstatus']);
            $this->_savePedidoNote($this->idPerson,$idPedidoAprovador,'<p><strong>Status do Pedido de Compra:</strong> '.$rsStatusP->fields['nome'].'</p>','1');
        }

        $aRet = array(
            "idpedidoaprovador" => $idPedidoAprovador,
            "status"   => 'OK',
            "sendmail" => $sendMail
        );

        $dbPedidoAprovador->CommitTrans();

        echo json_encode($aRet);


    }

    function statusPedidoAprovador()
    {
        $idpedidoaprovador = $this->getParam('idpedidoaprovador');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('pedidoaprovador_model');
        $dbPedidoAprovador = new pedidoaprovador_model();

        $ret = $dbPedidoAprovador->changeStatus($idpedidoaprovador,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Pedido Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idpedidoaprovador" => $idpedidoaprovador,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function ajaxPedidoRepass()
    {
        $idpedido = $_POST['idpedido'];

        $rsPedidoAprovador = $this->_getPedidoAprovador("WHERE idpedido = $idpedido") ;
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idpedido");
        $rsItemPedidoAprovador = $this->_getItemPedidoAprovador("AND a.idpedido = $idpedido");

        $deliverydate = date('d/m/Y', strtotime($rsPedidoAprovador->fields['dataentrega']));
        if($rsPedidoTurma->RecordCount() > 0){
            $flagdisplay = 'S';
            $turmadesc = $rsPedidoTurma->fields['abrev'];
        }else{
            $flagdisplay = 'N';
        }

        $itemtable = "";
        while(!$rsItemPedidoAprovador->EOF){
            $itemtable .= "<tr>
                            <td>".$rsItemPedidoAprovador->fields['nome']." - ".$rsItemPedidoAprovador->fields['unidade']."</td>
                            <td style='text-align: center'>".$rsItemPedidoAprovador->fields['quantidade']."</td>
                            <td style='text-align: center'>".$rsItemPedidoAprovador->fields['nomestatusitem']."</td>
                        </tr>";
            $rsItemPedidoAprovador->MoveNext();
        }

        $aRet = array(
            "idpedidoaprovador" => $idpedido,
            "author" => $rsPedidoAprovador->fields['nomepessoa'],
            "deliverydate" => $deliverydate,
            "flagdisplay" => $flagdisplay,
            "turma" => $turmadesc,
            "reason" => $rsPedidoAprovador->fields['motivo'],
            "costcenter" => $rsPedidoAprovador->fields['nomecentrodecusto'],
            "accountingacc" => $rsPedidoAprovador->fields['nomecontacontabil'],
            "status" => $rsPedidoAprovador->fields['nomestatus'],
            "itens" => $itemtable
        );

        echo json_encode($aRet);

    }

    function repassPedido()
    {

        $idpedido = $_POST['idpedido'];
        $idincharge = $_POST['idincharge'];

        $this->dbPedidoAprovador->BeginTrans();
        $ret = $this->dbPedidoAprovador->removeIncharge($idpedido);
        if (!$ret) {
            $this->dbPedidoAprovador->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ins = $this->dbPedidoAprovador->insertInCharge($idpedido,$idincharge,'1','Y');

        if (!$ret) {
            $this->dbPedidoAprovador->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idpedido" => $idpedido,
            "status"   => 'OK'
        );

        $this->dbPedidoAprovador->CommitTrans();

        echo json_encode($aRet);


    }

}