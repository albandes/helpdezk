<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmPedidoCompra extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmPedidoCompra');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

        $this->loadModel('acdturma_model');
        $dbTurma = new acdturma_model();
        $this->dbTurma = $dbTurma;

        $this->loadModel('pedidocompra_model');
        $dbPedidoCompra = new pedidocompra_model();
        $this->dbPedidoCompra = $dbPedidoCompra;

        $this->arrGroupException = array(11,12,13,14,15,19);
        $this->accessExceptions = explode(',', $_SESSION['scm']['SCM_ACCESS_USER_EXCEPTIONS']);

        $this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);

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
                $smarty->display('scm-pedidocompra-grid.tpl');
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

        $where = "WHERE (idperson = {$this->idPerson} OR idpersoncreator = {$this->idPerson})";

        $idStatus = $_POST['idstatus'];
        if ($idStatus) {
            $where .= " AND idstatus = $idStatus";
        }

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='idpedido';
        if(!$sord)
            $sord ='desc';

        if ($_POST['_search'] == 'true'){

            switch ($_POST['searchField']){
                case 'idpedido':
                    $searchField = 'idpedido';
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

        $count = $this->_getNumPedidoCompras($where);

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

        $rsPedidoCompra = $this->_getPedidoCompra($where ,$order,null,$limit);

        while (!$rsPedidoCompra->EOF) {
            $status_fmt = ($rsPedidoCompra->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';

            $aColumns[] = array(
                'id'            => $rsPedidoCompra->fields['idpedido'],
                'nomepessoa'    => $rsPedidoCompra->fields['nomepessoa'],
                'nomestatus'    => $rsPedidoCompra->fields['nomestatus'],
                'idstatus'      => $rsPedidoCompra->fields['idstatus'],
                'datapedido'    => $rsPedidoCompra->fields['datapedido'],
                'dataentrega'   => $rsPedidoCompra->fields['dataentrega'],
                'foradoprazo'   => $rsPedidoCompra->fields['foradoprazo'],
                'motivo'        => $rsPedidoCompra->fields['motivo'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsPedidoCompra->fields['status']

            );
            $rsPedidoCompra->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreatePedidoCompra()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenPedidoCompra($smarty,'', '', '','create');

        $smarty->assign('token', $this->_makeToken()) ;
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
            $smarty->display('scm-pedidocompra-create.tpl');
        }

    }

    public function formUpdatePedidoCompra()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idPedidoCompra = $this->getParam('idpedidocompra');

        $rsPedidoCompra = $this->_getPedidoCompra("where idpedido = $idPedidoCompra") ;
        $rsItemPedidoCompra = $this->_getItemPedidoCompra("where idpedido = $idPedidoCompra");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoCompra");

        $this->makeScreenPedidoCompra($smarty,$rsPedidoCompra,$rsItemPedidoCompra, $rsPedidoTurma,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idpedidocompra', $idPedidoCompra);

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
            $smarty->display('scm-pedidocompra-update.tpl');
        }

    }

    public function echoPedidoCompra()
    {
        $smarty = $this->retornaSmarty();

        $idPedidoCompra = $this->getParam('idpedidocompra');
        $rsPedidoCompra = $this->_getPedidoCompra("where idpedido = $idPedidoCompra");
        $rsItemPedidoCompra = $this->_getItemPedidoCompraEcho("$idPedidoCompra");
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $idPedidoCompra");

        $this->makeScreenPedidoCompra($smarty,$rsPedidoCompra,$rsItemPedidoCompra,$rsPedidoTurma,'echo');

        $smarty->assign('hidden_idpedidocompra', $idPedidoCompra);
        $smarty->assign('token', $this->_makeToken()) ;
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
            $smarty->display('scm-pedidocompra-echo.tpl');
        }

    }

    function makeScreenPedidoCompra($objSmarty,$rs,$rsItem,$rsTurma,$oper)
    {
        //$arrDepartProf = array(313,314);
        //$arrGroupCoord = array(11,12,13,14,19);

        if ($oper == 'echo') {
            $objSmarty->assign('motivocancelamento',$rs->fields['motivocancelamento']);
            $objSmarty->assign('idstatus', $rs->fields['idstatus']);
        }

        // --- Data Entrega ---
        $dateDefault = date("Y-m-d", mktime (0, 0, 0, date("m"), date("d")+10, date("Y")));
        $dtDefaultF = $this->_getDefaultDataEntrega($dateDefault);
        if ($oper == 'update') {
            if (empty($rs->fields['dataentrega']) or $rs->fields['dataentrega'] == '0000-00-00 00:00:00'){
                $objSmarty->assign('plh_dataentrega',$dtDefaultF);
                $objSmarty->assign('dataentrega',$dtDefaultF);
            }
            else{
                list($dataTMP,$timeTMP) = explode(' ',$rs->fields['dataentrega']);
                list($anoTMP,$mesTMP,$diaTMP) = explode('-',$dataTMP);
                $dtEntregaFinal = $diaTMP.'/'.$mesTMP.'/'.$anoTMP;
                $objSmarty->assign('dataentrega',$dtEntregaFinal);
            }
        } elseif ($oper == 'create') {
            $dtDefaultF = $this->_getDefaultDataEntrega($dateDefault);
            $objSmarty->assign('plh_dataentrega',$dtDefaultF);
            $objSmarty->assign('dataentrega',$dtDefaultF);
        } elseif ($oper == 'echo') {
            list($dataTMP,$timeTMP) = explode(' ',$rs->fields['dataentrega']);
            list($anoTMP,$mesTMP,$diaTMP) = explode('-',$dataTMP);
            $dtEntregaFinal = $diaTMP.'/'.$mesTMP.'/'.$anoTMP;
            $objSmarty->assign('dataentrega',$dtEntregaFinal);
        }

        // --- Status ---
        if ($oper == 'update') {
            $objSmarty->assign('nomestatus',$rs->fields['nomestatus']);
            $resultids[0] = 1;
            $resultvals[0] = 'Solicitado';
            $resultids[1] = 9;
            $resultvals[1] = 'Cancelado';
            $objSmarty->assign('statusids', $resultids);
            $objSmarty->assign('statusvals', $resultvals);
            $objSmarty->assign('idstatus', $rs->fields['idstatus']);
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nomestatus',$rs->fields['nomestatus']);
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

        // --- Produto ---
        if ($oper == 'update') {
            $arrProduto = $this->_comboProduto(null,null);
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            $objSmarty->assign('produtodisables', $arrProduto['disables']);
            $objSmarty->assign('produtoopts', $arrProduto['options']);
            while (!$rsItem->EOF) {

                $arrItens[] = array(
                    'idpedido'       => $rsItem->fields['idpedido'],
                    'idproduto'      => $rsItem->fields['idproduto'],
                    'quantidade'     => $rsItem->fields['quantidade']

                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $rsItem);
        } elseif ($oper == 'create') {
            $idProdutoEnable = 1;
            $arrProduto = $this->_comboProduto(null,"WHERE scm_tbproduto.status = 'A'");
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            $objSmarty->assign('idproduto', $idProdutoEnable );
        } elseif ($oper == 'echo') {

            while (!$rsItem->EOF) {

                $arrItens[] = array(
                    'idproduto'         => $rsItem->fields['idproduto'],
                    'nome'              => $rsItem->fields['nome'],
                    'quantidade'        => $rsItem->fields['quantidade'],
                    'disponibilidade'   => ($rsItem->fields['estoque_atual'] > 0 && $rsItem->fields['quantidade'] <= $rsItem->fields['estoque_atual']) ? 'Dispon&iacute;vel em estoque' : 'N&atilde;o dispon&iacute;vel',
                    'lblType'           => ($rsItem->fields['estoque_atual'] > 0 && $rsItem->fields['quantidade'] <= $rsItem->fields['estoque_atual']) ? 'text-navy' : 'text-danger'


                );
                $rsItem->MoveNext();
            }

            $objSmarty->assign('arrItens', $arrItens);
        }

        // --- Turma ---
        $userRole = $this->_getUserRole($_SESSION['SES_LOGIN_PERSON']);
        $objSmarty->assign('iduserrole', $userRole);

        switch ($userRole){
            case 1:
                $arrTurma = $this->_getTurmaPerson($_SESSION['SES_LOGIN_PERSON']);
                $objSmarty->assign('flagdisplay', '' );
                $objSmarty->assign('flagselturma', 'hidden' );
                break;
            case 2:
                $retUserGroup = $this->_getGrupoOperador("AND ghp.idperson = ".$this->idPerson);
                $groups = '';
                while(!$retUserGroup->EOF){
                    $groups .= $retUserGroup->fields['idgroup'].',';
                    $retUserGroup->MoveNext();
                }
                $groups = substr($groups,0,-1);
                $arrTurma = $this->_getTurmaList('G',$groups);

                switch ($oper){
                    case 'update':
                        if($rsTurma->RecordCount() > 0){
                            $objSmarty->assign('flagdisplay', '' );
                            $objSmarty->assign('flagselturma', '' );
                            $objSmarty->assign('flagchecked', 'checked="checked"' );
                        }else{
                            $objSmarty->assign('flagdisplay', 'hidden' );
                            $objSmarty->assign('flagselturma', '' );
                            $objSmarty->assign('flagchecked', '' );
                        }
                        break;
                    case 'echo':
                        if($rsTurma->RecordCount() > 0){
                            $objSmarty->assign('flagdisplay', '' );
                        }else{
                            $objSmarty->assign('flagdisplay', 'hidden' );
                        }
                        break;
                    default:
                        $objSmarty->assign('flagdisplay', 'hidden' );
                        $objSmarty->assign('flagselturma', '' );
                        break;
                }
                break;
            case 3:
                $arrTurma = $this->_getTurmaList('P',$this->idPerson);
                switch ($oper){
                    case 'update':
                        if($rsTurma->RecordCount() > 0){
                            $objSmarty->assign('flagdisplay', '' );
                            $objSmarty->assign('flagselturma', '' );
                            $objSmarty->assign('flagchecked', 'checked="checked"' );
                        }else{
                            $objSmarty->assign('flagdisplay', 'hidden' );
                            $objSmarty->assign('flagselturma', '' );
                        }
                        break;
                    case 'echo':
                        if($rsTurma->RecordCount() > 0){
                            $objSmarty->assign('flagdisplay', '' );
                        }else{
                            $objSmarty->assign('flagdisplay', 'hidden' );
                        }
                        break;
                    default:
                        $objSmarty->assign('flagdisplay', 'hidden' );
                        $objSmarty->assign('flagselturma', '' );
                        break;
                }

                break;
            default:
                $objSmarty->assign('flagdisplay', 'hidden' );
                $objSmarty->assign('flagselturma', 'hidden' );
                break;
        }

        if ($oper == 'update' or $oper == 'echo') {
            $idTurmaEnable = $rsTurma->fields['idcurso'].'|'.$rsTurma->fields['serie'].'|'.$rsTurma->fields['idturma'];
        } elseif ($oper == 'create') {
            $idTurmaEnable = '';
        }

        $objSmarty->assign('turmaids',  $arrTurma['ids']);
        $objSmarty->assign('turmavals', $arrTurma['values']);
        $objSmarty->assign('idturma', $idTurmaEnable);

        // --- Allow User open supply request for another ---
        $allowOpenOther = $this->dbPedidoCompra->getAllowOpenOther("WHERE idperson = {$_SESSION['SES_COD_USUARIO']}");

        if(is_array($allowOpenOther) && isset($allowOpenOther['status'])){
            if($this->log)
                $this->logIt($allowOpenOther['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
        }else{
            if($allowOpenOther->RecordCount() > 0){
                if ($oper == 'update' or $oper == 'echo') {
                    $idOwner = $rs->fields['idperson'];
                    $objSmarty->assign('ownerName', $rs->fields['nomepessoa']);
                } elseif ($oper == 'create') {
                    $idOwner = $_SESSION['SES_COD_USUARIO'];
                }

                $where = "AND b.status = 'A'";

                $rsDepartment = $this->dbPedidoCompra->getPersonDepartment("AND a.idperson = ".$_SESSION['SES_COD_USUARIO']);
                if (!$rsDepartment) {
                    if($this->log)
                        $this->logIt('Can\'t get Requester\'s departments   - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                }

                $departmentIds = '';
                while(!$rsDepartment->EOF){
                    $departmentIds .= $rsDepartment->fields['iddepartment'].',';
                    $rsDepartment->MoveNext();
                }
                $departmentIds = substr($departmentIds,0,-1);

                $where .= $departmentIds != '' ? " AND a.iddepartment IN({$departmentIds}) ": '';
                $order = "ORDER BY b.`name`";

                $arrOwner = $this->_getPersonsByDepartment($where,$order);

                $objSmarty->assign('ownerids',  $arrOwner['ids']);
                $objSmarty->assign('ownervals', $arrOwner['values']);
                $objSmarty->assign('idowner', $idOwner);
                $objSmarty->assign('displayOwner', '');
            }else{
                if ($oper == 'update' or $oper == 'echo') {
                    $idOwner = $rs->fields['idperson'];
                    $objSmarty->assign('ownerName', $rs->fields['nomepessoa']);
                } elseif ($oper == 'create') {
                    $idOwner = $_SESSION['SES_COD_USUARIO'];
                }
                $objSmarty->assign('ownerids',  array($_SESSION['SES_COD_USUARIO']));
                $objSmarty->assign('ownervals', array($_SESSION['SES_NAME_PERSON']));
                $objSmarty->assign('idowner', $idOwner);
                $objSmarty->assign('displayOwner', 'hide');
            }

        }

        // Aula
        if ($oper == 'update' or $oper == 'echo') {
            $objSmarty->assign('aula', $rs->fields['aula']);
        }
    }

    function createPedidoCompra()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idowner = $_POST['cmbOwner'];
        $idcreator = $this->idPerson;
        $aula = addslashes($_POST['txtAula']);
        $motivo = addslashes($_POST['motivo']);
        list($dayTmp,$monthTMP,$yearTMP) = explode('/',$_POST['dataentrega']);
        $dtEntrega = $yearTMP."-".$monthTMP."-".$dayTmp;

        $this->loadModel('pedidocompra_model');
        $dbPedidoCompra = new pedidocompra_model();

        $dbPedidoCompra->BeginTrans();

        $ret = $dbPedidoCompra->insertPedidoCompra($idowner,$dtEntrega,$motivo,$aula,$idcreator);

        if (!$ret) {
            $dbPedidoCompra->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $userRole = $this->_getUserRole($_SESSION['SES_LOGIN_PERSON']);

        switch ($userRole){
            case 1:
                list($idcurso,$serie,$idturma) = explode('|',$_POST['cmbTurma']);
                $retTurma = $dbPedidoCompra->insertPedidoTurma($ret,$idturma);
                if (!$retTurma) {
                    $dbPedidoCompra->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert Turma Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                break;
            case 2:
                if(isset($_POST['flagturma'])){
                    $updStatus = $dbPedidoCompra->changeStatus($ret,11);
                    if (!$updStatus) {
                        $dbPedidoCompra->RollbackTrans();
                        if($this->log)
                            $this->logIt('Update Status Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                    list($idcurso,$serie,$idturma) = explode('|',$_POST['cmbTurma']);
                    $retTurma = $dbPedidoCompra->insertPedidoTurma($ret,$idturma);
                    if (!$retTurma) {
                        $dbPedidoCompra->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Turma Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
                break;
            case 3:
                $updStatus = $dbPedidoCompra->changeStatus($ret,2);
                if(isset($_POST['flagturma'])){
                    list($idcurso,$serie,$idturma) = explode('|',$_POST['cmbTurma']);
                    $retTurma = $dbPedidoCompra->insertPedidoTurma($ret,$idturma);
                    if (!$retTurma) {
                        $dbPedidoCompra->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Turma Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
                break;
        }

        foreach ($_POST['produtos'] as $key => $value) {
            $retItem = $dbPedidoCompra->insertItemPedidoCompra($ret,$_POST['produtos'][$key],$_POST['quantidades'][$key]);
            if (!$retItem) {
                $dbPedidoCompra->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $idPedidoCompra = $ret ;

        $this->_savePedidoNote($idowner,$idPedidoCompra,'<p><strong>Pedido de Compra cadastrado</strong></p>','1');

        $aRet = array(
            "idpedidocompra" => $idPedidoCompra,
            "motivo" => $_POST['motivo']
        );

        $dbPedidoCompra->CommitTrans();

        if(isset($_POST['flagReplicar'])){
            $retRep = $this->replicateRequest($idPedidoCompra,$_POST['turmareplicar']);
            if(!$retRep){
                $aRet['replicateMsg'] = $this->getLanguageWord('SCM_Alert_replicate_failure');
                if($this->log)
                    $this->logIt('Can\'t Replicate Supply Request  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            }
        }

        echo json_encode($aRet);

    }

    function updatePedidoCompra()
    {

        if ($this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idPedidoCompra = $this->getParam('idpedidocompra');
        $iditempedido = $_POST['iditempedido'];

        list($dayTmp,$monthTMP,$yearTMP) = explode('/',$_POST['dataentrega']);
        $dtEntrega = $yearTMP."-".$monthTMP."-".$dayTmp;

        $this->loadModel('pedidocompra_model');
        $dbPedidoCompra = new pedidocompra_model();

        $dbPedidoCompra->BeginTrans();

        $ret = $dbPedidoCompra->updatePedidoCompra($idPedidoCompra,$dtEntrega,$_POST['motivo'],$_POST['idstatus']);
        if (!$ret) {
            $dbPedidoCompra->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $rsTurma = $dbPedidoCompra->getPedidoTurma("AND a.idpedido = ".$idPedidoCompra);

        if($rsTurma->RecordCount()>0){
            $retTurma = $dbPedidoCompra->deleteTurmaPedidoCompra($idPedidoCompra);

            if (!$retTurma) {
                if($this->log)
                    $this->logIt('Update Pedido - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $userRole = $this->_getUserRole($_SESSION['SES_LOGIN_PERSON']);

        switch ($userRole){
            case 1:
                list($idcurso,$serie,$idturma) = explode('|',$_POST['cmbTurma']);
                $retTurma = $dbPedidoCompra->insertPedidoTurma($idPedidoCompra,$idturma);
                if (!$retTurma) {
                    $dbPedidoCompra->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert Turma Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                break;
            case 2:
                if(isset($_POST['flagturma'])){
                    list($idcurso,$serie,$idturma) = explode('|',$_POST['cmbTurma']);
                    $retTurma = $dbPedidoCompra->insertPedidoTurma($idPedidoCompra,$idturma);
                    if (!$retTurma) {
                        $dbPedidoCompra->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Turma Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
                break;
            case 3:
                if(isset($_POST['flagturma'])){
                    list($idcurso,$serie,$idturma) = explode('|',$_POST['cmbTurma']);
                    $retTurma = $dbPedidoCompra->insertPedidoTurma($idPedidoCompra,$idturma);
                    if (!$retTurma) {
                        $dbPedidoCompra->RollbackTrans();
                        if($this->log)
                            $this->logIt('Insert Turma Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
                break;
        }

        $ret = $dbPedidoCompra->deleteAllItemPedidoCompra($idPedidoCompra);
        if (!$ret) {
            $dbPedidoCompra->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach ($_POST['produtos'] as $key => $value) {
            $retItem = $dbPedidoCompra->insertItemPedidoCompra($idPedidoCompra,$_POST['produtos'][$key],$_POST['quantidades'][$key]);
            if (!$retItem) {
                $dbPedidoCompra->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $aRet = array(
            "idpedidocompra" => $idPedidoCompra,
            "status"   => 'OK'
        );

        $dbPedidoCompra->CommitTrans();

        echo json_encode($aRet);


    }

    function statusPedidoCompra()
    {
        $idpedidocompra = $this->getParam('idpedidocompra');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('pedidocompra_model');
        $dbPedidoCompra = new pedidocompra_model();

        $ret = $dbPedidoCompra->changeStatus($idpedidocompra,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Pedido Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idpedidocompra" => $idpedidocompra,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function removePedido()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idpedidocompra = $_POST['idpedido'];

        $this->loadModel('pedidocompra_model');
        $dbPedidoCompra = new pedidocompra_model();

        $retItens = $dbPedidoCompra->deleteAllItemPedidoCompra($idpedidocompra);

        if (!$retItens) {
            if($this->log)
                $this->logIt('Delete Itens Pedido - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $rsTurma = $dbPedidoCompra->getPedidoTurma("AND a.idpedido = ".$idpedidocompra);

        if($rsTurma->RecordCount()>0){
            $retTurma = $dbPedidoCompra->deleteTurmaPedidoCompra($idpedidocompra);

            if (!$retTurma) {
                if($this->log)
                    $this->logIt('Change Pedido Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $ret = $dbPedidoCompra->deletePedidoCompra($idpedidocompra);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Pedido Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function ajaxProduto()
    {
        echo $this->comboProdutoHtml();
    }

    public function comboProdutoHtml()
    {
        $arrType = $this->_comboProduto(null,"WHERE scm_tbproduto.status = 'A'");
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

    public function sendNotification()
    {
        if(!empty($_POST['transaction']))
            $transaction = $_POST['transaction'];
        if(!empty($_POST['code_request']))
            $code_request = $_POST['code_request'];

        $ret = $this->_sendNotification($transaction,'email',$code_request);
        if($ret)
            echo 'OK';
        else
            echo 'ERROR';
    }

    public function modalRemovePedido()
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
            $listItem .= "<div class='form-group'>
                                <label class='col-sm-2 control-label'>Item / Quantidade:</label>
                                <div class='col-sm-7'>
                                    <input type='text' class='form-control input-sm' value='".$rsItemPedidoCompra->fields['nome']." / ".str_replace('.',',',$rsItemPedidoCompra->fields['quantidade'])."' readonly />
                                </div>
                           </div>";
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
            "itens" => $listItem
        );

        echo json_encode($aRet);

    }

    public function checkUser()
    {
        $ret = $this->_isTeacher($_SESSION['SES_LOGIN_PERSON']);

        if (!$ret){
            if ($this->log)
                $this->logIt('Erro no retorno da funcao _isTeacher  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        echo json_encode($ret);

    }

    function ajaxReplicateLbl()
    {
        $idturma = $_POST['idturma'];
        $user = $_SESSION['SES_LOGIN_PERSON'];
        $arrTurma = explode('|',$idturma);

        $rsTurma = $this->dbTurma->getTurma("WHERE idturma = {$arrTurma[2]}");
        if (!$rsTurma){
            if ($this->log)
                $this->logIt('Can\'t return data - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $ret = $this->_getTurmaReplica($user,$idturma);

        if (!$ret){
            if ($this->log)
                $this->logIt('Error to return _getTurmaReplica  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        if(sizeof($ret) > 0){
            foreach ($ret as $key=>$value){
                $retTurma = $this->dbTurma->getTurma("WHERE idserie = {$rsTurma->fields['idserie']} AND idlegado = {$value['CoTurma']}");
                if (!$retTurma){
                    if ($this->log)
                        $this->logIt('Error to get Turma data  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                    return false;
                }

                $bus = array (
                    "id" => $retTurma->fields['idturma'],
                    "name" => $retTurma->fields['abrev']
                );

                $aRet[] = $bus;

            }
        }else{ $aRet = array();}

        echo json_encode($aRet);
    }

    function replicateRequest($idpedido,$turmaIDs)
    {
        $rs = $this->_getPedidoCompra("WHERE idpedido = $idpedido") ;

        foreach ($turmaIDs as $key => $value) {
            $rsItens = $this->dbPedidoCompra->getItemPedidoCompra("WHERE idpedido = $idpedido");
            $this->dbPedidoCompra->BeginTrans();

            $ret = $this->dbPedidoCompra->insertPedidoCompra($rs->fields['idperson'],$rs->fields['dataentrega'],$rs->fields['motivo'],$rs->fields['aula'],$rs->fields['idpersoncreator']);

            if (!$ret) {
                $this->dbPedidoCompra->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Replicate Pedido   - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $retTurma = $this->dbPedidoCompra->insertPedidoTurma($ret,$value);
            if (!$retTurma) {
                $this->dbPedidoCompra->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Turma Replicate Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            while(!$rsItens->EOF) {
                $retItem = $this->dbPedidoCompra->insertItemPedidoCompra($ret,$rsItens->fields['idproduto'],$rsItens->fields['quantidade']);
                if (!$retItem) {
                    $this->dbPedidoCompra->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert Item Replicate Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                $rsItens->MoveNext();
            }

            $idPedidoCompra = $ret ;

            $this->_savePedidoNote($rs->fields['idperson'],$idPedidoCompra,'<p><strong>Pedido de Compra cadastrado</strong></p>','1');

            $this->_sendNotification('new-scmrequest-user','email',$ret);

            $this->dbPedidoCompra->CommitTrans();
        }

        return true;//array("status" => "OK");
        
    }

    function checkProductAvailability()
    {
        $ret = $this->dbPedidoCompra->checkProductStock("WHERE idproduto = {$_POST['productID']}");
        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't get product data.\n {$ret['message']}\nUser: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            echo json_encode(array("success"=>false, "message" => 'Erro ao realizar consulta'));
            exit;
        }

        if($ret['data']->RecordCount() > 0){
            $lbl = ($ret['data']->fields['estoque_atual'] > 0 && $_POST['quantity'] <= $ret['data']->fields['estoque_atual']) ? 'Dispon&iacute;vel em estoque' : 'N&atilde;o dispon&iacute;vel';
            $txtType = ($ret['data']->fields['estoque_atual'] > 0 && $_POST['quantity'] <= $ret['data']->fields['estoque_atual']) ? 'text-navy' : 'text-danger';
            echo json_encode(array("success"=>true, "message" => $lbl, "txtType"=> $txtType));
        }
        else{
            echo json_encode(array("success"=>false, "message" => 'Erro ao realizar consulta'));
        }

    }
}