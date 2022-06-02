<?php

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmBaixaProduto extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmBaixaProduto');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

        $this->loadModel('baixaproduto_model');
        $this->dbBaixa = new baixaproduto_model();

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        $smarty->display('scm-baixaproduto-grid.tpl');

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
            $sidx ='dtcadastro';
        if(!$sord)
            $sord ='DESC';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'idbaixa') $searchField = 'idbaixa';
            if ( $_POST['searchField'] == 'tipo') $searchField = "IF(tipo = 'D','Doação','Descarte')";
            if ( $_POST['searchField'] == 'b.nome') $searchField = 'b.nome';

            if ( $_POST['searchField'] == 'dtcadastro'){
                $searchField = 'DATE(dtcadastro)';
                $_POST['searchString'] = substr($this->formatSaveDate($_POST['searchString']),1,-1);
            }

            $where .= ' AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $rsCount = $this->dbBaixa->getBaixas($where);
        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("{$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
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
        //

        $rsBaixa = $this->dbBaixa->getBaixas($where ,$order,$limit);
        if (!$rsBaixa['success']) {
            if($this->log)
                $this->logIt("{$rsBaixa['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rsBaixa['data']->EOF) {

            $aColumns[] = array(
                'id'            => $rsBaixa['data']->fields['idbaixa'],
                'dtcadastro'    => $this->formatDateHour($rsBaixa['data']->fields['dtcadastro']),
                'tipo'          => $rsBaixa['data']->fields['tipo_fmt'],           
                'responsavel'   => $rsBaixa['data']->fields['responsavel']
            );

            $rsBaixa['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeScreenBaixa($smarty,'', '', 'create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $smarty->display('scm-baixaproduto-create.tpl');
    }

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $baixaID = $this->getParam('idbaixaproduto');

        $rsBaixa = $this->dbBaixa->getBaixas("AND a.idbaixa = $baixaID");
        if (!$rsBaixa['success']) {
            if($this->log)
                $this->logIt("{$rsBaixa['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        $rsItemBaixa =  $this->dbBaixa->getItemBaixa("AND a.idbaixa = $baixaID");
        if (!$rsItemBaixa['success']) {
            if($this->log)
                $this->logIt("{$rsItemBaixa['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }
        
        $this->makeScreenBaixa($smarty,$rsBaixa['data'],$rsItemBaixa['data'],'update');
        
        $smarty->assign('token', $this->_makeToken()) ;

        $smarty->assign('idbaixa', $baixaID);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $smarty->display('scm-baixaproduto-update.tpl');

    }

    public function echoBaixa()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $baixaID = $this->getParam('idbaixa');

        $rsBaixa = $this->dbBaixa->getBaixas("AND a.idbaixa = $baixaID");
        if (!$rsBaixa['success']) {
            if($this->log)
                $this->logIt("{$rsBaixa['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        $rsItemBaixa =  $this->dbBaixa->getItemBaixa("AND a.idbaixa = $baixaID");
        if (!$rsItemBaixa['success']) {
            if($this->log)
                $this->logIt("{$rsItemBaixa['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        $this->makeScreenBaixa($smarty,$rsBaixa['data'],$rsItemBaixa['data'],'echo');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $smarty->display('scm-baixaproduto-echo.tpl');

    }

    function makeScreenBaixa($objSmarty,$rs,$rsItem,$oper)
    {
        
        // --- Tipo ---
        if ($oper == 'update') {
            if (!empty($rs->fields['tipo'])){
               $objSmarty->assign('tipo',$rs->fields['tipo']);
            }
        } elseif ($oper == 'echo') {
            $objSmarty->assign('tipo',$rs->fields['tipo_fmt']);
        }

        // --- Motivo ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('motivo',$rs->fields['motivo']);
        }

        // --- Destino ---
        if ($oper == 'update') {
            $idDestinoEnable = $rs->fields['iddestinobaixa'];
        } elseif ($oper == 'create') {
            $idDestinoEnable = 1;
        }

        if ($oper == 'echo') {
            $objSmarty->assign('destino',$rs->fields['destino']);
        } else {
            $arrBaixa= $this->_comboDestinoBaixa(null,"ORDER BY nome");
            $objSmarty->assign('destinationids',  $arrBaixa['ids']);
            $objSmarty->assign('destinationvals', $arrBaixa['values']);
            $objSmarty->assign('iddestination', $idDestinoEnable);
        }

        // --- Produto ---
        if ($oper == 'update') {
            
            $arrProduto = $this->_comboProduto();
            $objSmarty->assign('produtoids',  $arrProduto['ids']);
            $objSmarty->assign('produtovals', $arrProduto['values']);
            while (!$rsItem->EOF) {

                $arrItens[] = array(
                    'iditembaixa'   => $rsItem->fields['iditembaixa'],
                    'quantidade'    => $rsItem->fields['quantidade'],
                    'idproduto'     => $rsItem->fields['idproduto']

                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $rsItem);
        } elseif ($oper == 'echo') {
                 while (!$rsItem->EOF) {

                     $arrItens[] = array(
                        'iditementradaproduto' => $rsItem->fields['iditementradaproduto'],
                        'valor'                => $rsItem->fields['valor'],
                        'nome'                 => $rsItem->fields['nome_produto'],
                        'quantidade'           => $rsItem->fields['quantidade'],
                        'idproduto'            => $rsItem->fields['idproduto']

                );
                $rsItem->MoveNext();
            }
            $objSmarty->assign('arrItens', $arrItens);
        }
    }

    function createBaixa()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $tipo = $_POST['tipo'];
        $motivo = trim($_POST['motivo']);
        $destino = $_POST['cmbDestination'];

        $this->dbBaixa->BeginTrans();

        $ret = $this->dbBaixa->insertBaixa($tipo,$_SESSION['SES_COD_USUARIO'],$motivo,$destino);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("{$ret['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();            
            return false;
        }

        $insID = $ret['id'];
        $itemList = "";

        foreach ($_POST['produtos'] as $key => $value) {
            $retItem = $this->dbBaixa->insertItemBaixa($insID,$_POST['produtos'][$key],$_POST['quantidades'][$key]);
            if (!$retItem['success']) {
                if($this->log)
                    $this->logIt("{$retItem['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbBaixa->RollbackTrans();
                return false;
            }

            $retUpdStock = $this->dbBaixa->updateStock($_POST['produtos'][$key],$_POST['quantidades'][$key],2);
            if (!$retUpdStock['success']) {
                if($this->log)
                    $this->logIt("{$retUpdStock['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbBaixa->RollbackTrans();
                return false;
            }
            $itemList .= "{$_POST['produtos'][$key]},{$_POST['quantidades'][$key]}|";
        }

        $aRet = array(
            "success" => true,
            "baixaID" => $insID
        );

        $this->dbBaixa->CommitTrans();

        $params = array(
            "data2log"   => array('scm_tbbaixa','*',"WHERE idbaixa = $insID"), //dados para inserir no tblog
            "adddata" => array('itemvalues' => substr($itemList,0,-1)), //dados adicionais para inserir no tblog
            "programID"   => $this->idprogram,
            "userID"      => $_SESSION['SES_COD_USUARIO'],
            "tag"       => 'insert'
        );
        
        $this->makeLog($params);

        echo json_encode($aRet);

    }

    function updateBaixa()
    {

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $baixaID = $_POST['idbaixa'];
        $motivo = trim($_POST['motivo']);
        $destino = $_POST['cmbDestination'];
        $produtos = $_POST['produtos'];

        $this->dbBaixa->BeginTrans();

        $ret = $this->dbBaixa->updateBaixa($baixaID,$motivo,$destino);
        if (!$ret['success']) {
            if($this->log)
                $this->logIt("{$ret['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }
        
        $rsItemBaixaTMP = $this->dbBaixa->getItemBaixa("AND a.idbaixa = $baixaID");
        if (!$rsItemBaixaTMP['success']) {
            if($this->log)
                $this->logIt("{$rsItemBaixaTMP['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }
        
        while(!$rsItemBaixaTMP['data']->EOF){
            $retDelStock = $this->dbBaixa->updateStock($rsItemBaixaTMP['data']->fields['idproduto'],$rsItemBaixaTMP['data']->fields['quantidade'],1);
            if (!$retDelStock['success']) {
                if($this->log)
                    $this->logIt("{$retDelStock['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbBaixa->RollbackTrans();
                return false;
            }
            $rsItemBaixaTMP['data']->MoveNext();
        }
        
        $delAll = $this->dbBaixa->deleteAllItemBaixa($baixaID);
        if (!$delAll['success']) {
            if($this->log)
                $this->logIt("{$delAll['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }

        $itemList = "";
        foreach ($produtos as $key => $value) {
            $retItem = $this->dbBaixa->insertItemBaixa($baixaID,$_POST['produtos'][$key],$_POST['quantidades'][$key]);
            if (!$retItem['success']) {
                if($this->log)
                    $this->logIt("{$retItem['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbBaixa->RollbackTrans();
                return false;
            }

            $retUpdStock = $this->dbBaixa->updateStock($_POST['produtos'][$key],$_POST['quantidades'][$key],2);
            if (!$retUpdStock['success']) {
                if($this->log)
                    $this->logIt("{$retUpdStock['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbBaixa->RollbackTrans();
                return false;
            }

            $itemList .= "{$_POST['produtos'][$key]},{$_POST['quantidades'][$key]}|";
        }

        $aRet = array(
            "success"   => true,
            "baixaID"   => $baixaID
        );

        $this->dbBaixa->CommitTrans();

        $params = array(
            "data2log"   => array('scm_tbbaixa','*',"WHERE idbaixa = $baixaID"), //dados para inserir no tblog
            "adddata" => array('itemvalues' => substr($itemList,0,-1)), //dados adicionais para inserir no tblog
            "programID"   => $this->idprogram,
            "userID"      => $_SESSION['SES_COD_USUARIO'],
            "tag"       => 'update'
        );
        
        $this->makeLog($params);

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

    public function modalRemoveBaixa()
    {
        $token = $this->_makeToken();
        if($this->log)
            $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $idbaixa = $_POST['idbaixa'];

        $aRet = array(
            "idbaixa" => $idbaixa,
            "token" => $token
        );

        echo json_encode($aRet);

    }

    function removeBaixa()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $baixaID = $_POST['idbaixa'];

        $params = array(
            "data2log"   => array('scm_tbbaixa','*',"WHERE idbaixa = $baixaID"), //dados para inserir no tblog
            "programID"   => $this->idprogram,
            "userID"      => $_SESSION['SES_COD_USUARIO'],
            "tag"       => 'delete'
        );
        $this->makeLog($params);
        
        $this->dbBaixa->BeginTrans();

        $rsItemBaixaTMP = $this->dbBaixa->getItemBaixa("AND a.idbaixa = $baixaID");
        if (!$rsItemBaixaTMP['success']) {
            if($this->log)
                $this->logIt("{$rsItemBaixaTMP['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }
        
        while(!$rsItemBaixaTMP['data']->EOF){
            $retDelStock = $this->dbBaixa->updateStock($rsItemBaixaTMP['data']->fields['idproduto'],$rsItemBaixaTMP['data']->fields['quantidade'],1);
            if (!$retDelStock['success']) {
                if($this->log)
                    $this->logIt("{$retDelStock['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbBaixa->RollbackTrans();
                return false;
            }
            $rsItemBaixaTMP['data']->MoveNext();
        }
        
        $delAll = $this->dbBaixa->deleteAllItemBaixa($baixaID);
        if (!$delAll['success']) {
            if($this->log)
                $this->logIt("{$delAll['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }

        $retDel = $this->dbBaixa->deleteBaixa($baixaID);
        if(!$retDel['success']){
            if($this->log)
                $this->logIt("{$retDel['message']}  - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }
        
        $this->dbBaixa->CommitTrans();

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }

    function ajaxDestination()
    {
        $this->protectFormInput();
        $selectedID = $_POST['selectedID'];
        echo $this->comboDestinationHtml($selectedID);
    }

    public function comboDestinationHtml($selectedID)
    {
        $aDest = $this->_comboDestinoBaixa(null,"ORDER BY `nome`");

        foreach ($aDest['ids'] as $indexKey => $indexValue ) {
            $default = $indexValue == $selectedID ? 'selected=selected' : '';
            $select .= "<option value='$indexValue' $default>".$aDest['values'][$indexKey]."</option>";
        }
        return $select;
    }

    function addDestination()
    {
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $destName = trim($_POST['destName']);

        $this->dbBaixa->BeginTrans();

        $ret = $this->dbBaixa->insertDestination($destName);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert destination data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            $this->dbBaixa->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success" => true,
            "newDestID" => $ret['id']
        );

        $this->dbBaixa->CommitTrans();

        echo json_encode($aRet);

    }


}