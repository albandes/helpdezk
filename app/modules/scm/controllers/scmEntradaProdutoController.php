<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmEntradaProduto extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmEntradaProduto');

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
            $smarty->display('scm-entradaproduto-grid.tpl');
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
            $sidx ='datacadastro';
        if(!$sord)
            $sord ='DESC';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'id') $searchField = 'identradaproduto';
            if ( $_POST['searchField'] == 'numeropedido') $searchField = 'numeropedido';
            if ( $_POST['searchField'] == 'numeronotafiscal') $searchField = 'numeronotafiscal';

            if ( $_POST['searchField'] == 'datacadastro'){
                $searchField = 'DATE(datacadastro)';
                $_POST['searchString'] = substr($this->formatSaveDate($_POST['searchString']),1,-1);
            }

            if ( $_POST['searchField'] == 'dtnotafiscal'){
                $searchField = 'dtnotafiscal';
                $_POST['searchString'] = substr($this->formatSaveDate($_POST['searchString']),1,-1);
            }

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            
            if ( $_POST['searchField'] == 'nomefornecedor'){
                $where .= "{$oper} (".$this->getJqGridOperation($_POST['searchOper'],'b.name',$_POST['searchString'])." OR ".$this->getJqGridOperation($_POST['searchOper'],'b.fantasy_name',$_POST['searchString']).")";
            }else{
                $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
            }
            

        }

        $count = $this->_getNumEntradaProdutos($where);

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

        $rsEntradaProduto = $this->_getEntradaProduto($where ,$order,null,$limit);

        while (!$rsEntradaProduto->EOF) {
            $dtnf_fmt = $rsEntradaProduto->fields['dtnotafiscal'] ? $this->formatDate($rsEntradaProduto->fields['dtnotafiscal']) : '00/00/0000';
            $tipo_fmt = $rsEntradaProduto->fields['tipo'] == 'C' ? 'Compra' : 'Lista de Materiais';

            $aColumns[] = array(
                'id'               => $rsEntradaProduto->fields['identradaproduto'],
                'dtcadastro'       => $this->formatDateHour($rsEntradaProduto->fields['datacadastro']),
                'tipo'             => $tipo_fmt,           
                'numeropedido'     => $rsEntradaProduto->fields['numeropedido'],
                'nomefornecedor'   => $rsEntradaProduto->fields['nomefornecedor'],
                'numeronotafiscal' => $rsEntradaProduto->fields['numeronotafiscal'],
                'dtnotafiscal'     => $dtnf_fmt
            );
            
            $rsEntradaProduto->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsEntradaProduto->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateEntradaProduto()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenEntradaProduto($smarty,'', '', 'create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-entradaproduto-create.tpl');
    }

    public function formUpadateEntradaProduto()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idEntradaProduto = $this->getParam('identradaproduto');

        $rsEntradaProduto = $this->_getEntradaProduto("WHERE identradaproduto = $idEntradaProduto") ;
        $rsItemEntradaProduto = $this->_getItemEntradaProduto("identradaproduto = $idEntradaProduto");

        $this->makeScreenEntradaProduto($smarty,$rsEntradaProduto,$rsItemEntradaProduto,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('identradaproduto', $idEntradaProduto);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-entradaproduto-update.tpl');

    }

    public function echoEntradaProduto()
    {
        $smarty = $this->retornaSmarty();

        $idEntradaProduto = $this->getParam('identradaproduto');
        $rsEntradaProduto = $this->_getEntradaProduto("WHERE identradaproduto = $idEntradaProduto") ;
        $rsItemEntradaProduto = $this->_getEchoItemEntradaProduto($idEntradaProduto);

        $this->makeScreenEntradaProduto($smarty,$rsEntradaProduto,$rsItemEntradaProduto,'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-entradaproduto-echo.tpl');

    }

    function makeScreenEntradaProduto($objSmarty,$rs,$rsItem,$oper)
    {

        // --- Tipo ---
        if ($oper == 'update') {
            if (!empty($rs->fields['tipo'])){
               $objSmarty->assign('tipo',$rs->fields['tipo']);
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('tipo',$rs->fields['tipo'] == 'C' ? 'Compra': 'Lista de Materiais');
        }

        $objSmarty->assign('displayLine',$rs->fields['tipo'] == 'C' ? '': 'hide');

        // --- N° do pedido ---
        if ($oper == 'update') {
            if (empty($rs->fields['numeropedido']))
                $objSmarty->assign('plh_motivo','Informe o número do pedido.');
            else
                $objSmarty->assign('numeropedido',$rs->fields['numeropedido']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_numeropedido','Informe o número do pedido.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('numeropedido',$rs->fields['numeropedido']);
        }

        // --- N° nota fiscal ---
        if ($oper == 'update') {
            if (empty($rs->fields['numeronotafiscal']))
                $objSmarty->assign('plh_numeronotafiscal','Informe o número da nota fiscal.');
            else
                $objSmarty->assign('numeronotafiscal',$rs->fields['numeronotafiscal']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_numeronotafiscal','Informe o número da nota fiscal.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('numeronotafiscal',$rs->fields['numeronotafiscal']);
        }
        // --- Data nota fiscal ---
        if ($oper == 'update') {
            if (empty($rs->fields['dtnotafiscal']))
                $objSmarty->assign('plh_dtnotafiscal','Informe a Data da nota fiscal.');
            else
                $objSmarty->assign('dtnotafiscal',$this->formatDate($rs->fields['dtnotafiscal']));
        } elseif ($oper == 'echo') {
            $objSmarty->assign('dtnotafiscal',$this->formatDate($rs->fields['dtnotafiscal']));
        }
        // --- N° valor total ---
        if ($oper == 'update') {
            if (empty($rs->fields['valortotal']))
                $objSmarty->assign('plh_valorestotais','Informe o valor Total dos Itens.');
            else
                $objSmarty->assign('valorestotais',$rs->fields['valortotal']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_valorestotais','Informe o valor Total dos Itens.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('valorestotais',$rs->fields['valortotal']);
        }
        // --- valor total nota ---
        if ($oper == 'update') {
            if (empty($rs->fields['valornota']))
                $objSmarty->assign('plh_valornota','Informe o valor da nota fiscal.');
            else
                $objSmarty->assign('valornota',$rs->fields['valornota']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_valornota','Informe o valor da nota fiscal.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('valornota',$rs->fields['valornota']);
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

        // --- Produto ---
        if ($oper == 'update') {
            $arrProduto = $this->_comboProduto();
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            while (!$rsItem->EOF) {

                $arrItens[] = array(
                    'iditementradaproduto' => $rsItem->fields['iditementradaproduto'],
                    'valor'                => $rsItem->fields['valor'],
                    'quantidade'           => $rsItem->fields['quantidade'],
                    'idproduto'            => $rsItem->fields['idproduto']

                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $rsItem);
        } elseif ($oper == 'create') {
            $idProdutoEnable = 1;
            $arrProduto = $this->_comboProduto();
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            $objSmarty->assign('idproduto', $idProdutoEnable );
        } elseif ($oper == 'echo') {
                 while (!$rsItem->EOF) {

                     $arrItens[] = array(
                        'iditementradaproduto' => $rsItem->fields['iditementradaproduto'],
                        'valor'                => $rsItem->fields['valor'],
                        'nome'                 => $rsItem->fields['nome'],
                        'quantidade'           => $rsItem->fields['quantidade'],
                        'idproduto'            => $rsItem->fields['idproduto']

                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $arrItens);
        }
    }

    function createEntradaProduto()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('entradaproduto_model');
        $dbEntradaProduto = new entradaproduto_model();

        $dtNF = $this->formatSaveDate($_POST['dtnotafiscal']);

        $dbEntradaProduto->BeginTrans();

        $ret = $dbEntradaProduto->insertEntradaProduto($_POST['idfornecedor'],$_POST['tipo'],$_POST['numeropedido'],$_POST['numeronotafiscal'],$_POST['valorestotais'],$_POST['valorestotaisnotafiscal'],$dtNF);

        if (!$ret) {
            if($this->log)
                $this->logIt('Insert Entrada Produto  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            $dbEntradaProduto->RollbackTrans();            
            return false;
        }

        foreach ($_POST['produtos'] as $key => $value) {
            $retItem = $dbEntradaProduto->insertItemEntradaProduto($ret,$_POST['produtos'][$key],$_POST['quantidades'][$key],$_POST['valores'][$key]);
            if (!$retItem) {
                if($this->log)
                    $this->logIt('Insert Item Entrada Produto  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                $dbEntradaProduto->RollbackTrans();
                return false;
            }

            $retUpdStock = $dbEntradaProduto->updateStock($_POST['produtos'][$key],$_POST['quantidades'][$key],1);
            if (!$retUpdStock) {
                if($this->log)
                    $this->logIt('Insert Item Entrada Produto  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                $dbEntradaProduto->RollbackTrans();
                return false;
            }
        }

        $idEntradaProduto = $ret ;

        $aRet = array(
            "identradaproduto" => $idEntradaProduto,
            "numeropedido" => $_POST['numeropedido']
        );

        $dbEntradaProduto->CommitTrans();

        echo json_encode($aRet);

    }

    function updateEntradaProduto()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idEntradaProduto = $this->getParam('identradaproduto');
        $dtNF = $this->formatSaveDate($_POST['dtnotafiscal']);

        $this->loadModel('entradaproduto_model');
        $dbEntradaPedido = new entradaproduto_model();

        $dbEntradaPedido->BeginTrans();

        $ret = $dbEntradaPedido->updateEntradaPedido($idEntradaProduto,$_POST['idfornecedor'],$_POST['numeropedido'],$_POST['numeronotafiscal'],$_POST['valorestotais'],$_POST['valorestotaisnotafiscal'],$dtNF);
        if (!$ret) {
            $dbEntradaPedido->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $rsItemEntradaProduto = $this->_getItemEntradaProduto("identradaproduto = $idEntradaProduto");

        while(!$rsItemEntradaProduto->EOF){
            $retDelStock = $dbEntradaPedido->updateStock($rsItemEntradaProduto->fields['idproduto'],$rsItemEntradaProduto->fields['quantidade'],2);
            if (!$retDelStock) {
                $dbEntradaPedido->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Item Entrada Produto  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            $rsItemEntradaProduto->MoveNext();
        }

        $ret = $dbEntradaPedido->deleteAllItemEntradaPedido($idEntradaProduto);
        if (!$ret) {
            $dbEntradaPedido->RollbackTrans();
            if($this->log)
                $this->logIt('Update Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $produtos = $_POST['produtos'];

        foreach ($produtos as $key => $value) {
            $retItem = $dbEntradaPedido->insertItemEntradaProduto($idEntradaProduto,$_POST['produtos'][$key],$_POST['quantidades'][$key],$_POST['valores'][$key]);
            if (!$retItem) {
                $dbEntradaPedido->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Item Pedido  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $retUpdStock = $dbEntradaPedido->updateStock($_POST['produtos'][$key],$_POST['quantidades'][$key],1);
            if (!$retUpdStock) {
                $dbEntradaPedido->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Item Entrada Produto  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $aRet = array(
            "idpedidocompra" => $idEntradaProduto,
            "status"   => 'OK'
        );

        $dbEntradaPedido->CommitTrans();

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

    function ajaxProduto()
    {
        echo $this->comboProdutoHtml();
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

    public function modalRemoveEntrada()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $identrada = $_POST['identrada'];

        $aRet = array(
            "identrada" => $identrada,
            "token" => $token
        );

        echo json_encode($aRet);

    }

    function removeEntrada()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $identrada = $_POST['identrada'];

        $this->loadModel('entradaproduto_model');
        $dbEntrada = new entradaproduto_model();

        $dbEntrada->BeginTrans();

        $rsItemList = $this->_getItemEntradaProduto("identradaproduto = $identrada");
        while(!$rsItemList->EOF){
            $retDelStock = $dbEntrada->updateStock($rsItemList->fields['idproduto'],$rsItemList->fields['quantidade'],2);
            if (!$retDelStock) {
                $dbEntrada->RollbackTrans();
                if($this->log)
                    $this->logIt('Update Stock  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
            $rsItemList->MoveNext();
        }

        $ret = $dbEntrada->deleteAllItemEntradaPedido($identrada);
        if (!$ret) {
            $dbEntrada->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Itens Entrada  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retDel = $dbEntrada->deleteEntradaProduto($identrada);

        if (!$retDel) {
            $dbEntrada->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Entrada Produto - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $dbEntrada->CommitTrans();

        $aRet = array(
            "status" => "OK"
        );

        echo json_encode($aRet);

    }


}