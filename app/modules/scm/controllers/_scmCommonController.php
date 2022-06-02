<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Supply Chain Management Module
 */


class scmCommon extends Controllers  {


    public static $_logStatus;

    public function __construct()
    {

        parent::__construct();

        /**
         * Aqui é onde vai o model mais utilizado , ou os mais utilizados
         */
        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();
        $this->dbCentroCusto = $dbCentroCusto;
        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();
        $this->dbContaContabil = $dbContaContabil;
        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();
        $this->dbFornecedor = $dbFornecedor;
        $this->loadModel('produto_model');
        $dbProduto = new produto_model();
        $this->dbProduto = $dbProduto;
        $this->loadModel('unidade_model');
        $dbUnidade = new unidade_model();
        $this->dbUnidade = $dbUnidade;
        $this->loadModel('pedidocompra_model');
        $dbPedidoCompra = new pedidocompra_model();
        $this->dbPedidoCompra = $dbPedidoCompra;
        $this->loadModel('pedidooperador_model');
        $dbPedidoOperador = new pedidooperador_model();
        $this->dbPedidoOperador = $dbPedidoOperador;
        $this->loadModel('status_model');
        $dbStatus = new status_model();
        $this->dbStatus = $dbStatus;
        $this->loadModel('pedidoaprovador_model');
        $dbPedidoAprovador = new pedidoaprovador_model();
        $this->dbPedidoAprovador = $dbPedidoAprovador;
        $this->loadModel('local_model');
        $dbLocal = new local_model();
        $this->dbLocal = $dbLocal;
        $this->loadModel('grupodebens_model');
        $dbGrupoDeBens = new grupodebens_model();
        $this->dbGrupoDeBens = $dbGrupoDeBens;
        $this->loadModel('bens_model');
        $dbBens = new bens_model();
        $this->dbBens = $dbBens;
        $this->loadModel('marca_model');
        $dbMarca = new marca_model();
        $this->dbMarca = $dbMarca;
        $this->loadModel('estado_model');
        $dbEstado = new estado_model();
        $this->dbEstado = $dbEstado;
        $this->loadModel('transportadora_model');
        $dbTransportadora = new transportadora_model();
        $this->dbTransportadora = $dbTransportadora;
        $this->loadModel('entradaproduto_model');
        $dbEntradaProduto = new entradaproduto_model();
        $this->dbEntradaProduto = $dbEntradaProduto;
        $this->loadModel('acdturma_model');
        $dbTurma = new acdturma_model();
        $this->dbTurma = $dbTurma;

        /**
         * Aqui pega a empresa padrão para setar no idPerson
         */
        $this->_companyDefault = $this->_getCompanyDefault();

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $objSyslog->SetFacility(18);
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
        }

        $this->_serverApi = $this->_getServerApi();
        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->modulename = 'suprimentos' ;
            $this->idmodule = $this->getIdModule($this->modulename) ;
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

    }

    public function _makeNavScm($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuBycategory($idPerson,$this->idmodule,$this->idmodule);


        $smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
    }

    function _getServerApi()
    {

        $sessionVal = $_SESSION['scm']['server_api_dominio'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt('Url da API da Dominio sem valor - Variavel de sessao: $_SESSION[\'scm\'][\'server_api_dominio\']' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }

    }

    //funções centro de custo
    public function _getNumCentroCustos($where = null)
    {

        $rs = $this->dbCentroCusto->getCentroCusto($where);
        return $rs->RecordCount();

    }

    public function _getCentroCusto($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbCentroCusto->getCentroCusto($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboCentroCustoTipo()
    {

        $fieldsID[] = 'C';
        $values[]   = 'Crédito';

        $fieldsID[] = 'D';
        $values[]   = 'Débito';

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboCentroCusto($relatorio = null)
    {
        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();

        $rs = $dbCentroCusto->getCentroCusto('where status = true');
        if($relatorio == '0'){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcentrocusto'];
            $values[]   = $rs->fields['codigo'] . ' - ' . $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções contacontabil
    public function _getNumContaContabeis($where = null)
    {

        $rs = $this->dbContaContabil->getContaContabil($where);
        return $rs->RecordCount();

    }

    public function _getContaContabil($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbContaContabil->getContaContabil($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboContaContabil($where = null)
    {
        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha uma conta';
            $where = null;
        }
        $rs = $dbContaContabil->getContaContabil('where status = true ' . $where);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcontacontabil'];
            $values[]   = $rs->fields['codigo'] . ' - ' . $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções fornecedor
    public function _getNumGridFornecedores($where = null)
    {

        $rs = $this->dbFornecedor->getGridFornecedor($where);
        return $rs->RecordCount();

    }

    public function _getGridFornecedor($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbFornecedor->getGridFornecedor($where, $order , $group , $limit);
        return $rs;

    }

    public function _getNumFornecedores($where = null)
    {

        $rs = $this->dbFornecedor->getFornecedor($where);
        return $rs->RecordCount();

    }

    public function _getFornecedor($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbFornecedor->getFornecedor($where, $order , $group , $limit);
        return $rs;

    }

    public function _getFornecedorUpdateEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbFornecedor->getFornecedorUpdateEcho($where, $order , $group , $limit);
        return $rs;

    }

    //funções produto
    public function _getNumProdutos($where = null)
    {

        $rs = $this->dbProduto->getProduto($where);
        return $rs->RecordCount();

    }

    public function _getProduto($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbProduto->getProduto($where, $order , $group , $limit);
        return $rs;

    }

    public function _getImagemProduto($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbProduto->getImagemProduto($where, $order , $group , $limit);
        return $rs;

    }

    //funções unidade de medida do produto
    public function _getNumUnidades($where = null)
    {

        $rs = $this->dbUnidade->getUnidade($where);
        return $rs->RecordCount();

    }

    public function _getUnidade($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbUnidade->getUnidade($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboLogradouro()
    {
        $this->loadModel('fornecedor_model');
        $dbUnidade = new fornecedor_model();

        $rs = $dbUnidade->getlogradouro();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idtypestreet'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboUnidade()
    {
        $this->loadModel('unidade_model');
        $dbUnidade = new unidade_model();

        $rs = $dbUnidade->getUnidade();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idunidade'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboFornecedor($where = null)
    {
        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha um Fornecedor';
            $where = null;
        }

        $rs = $dbFornecedor->getFornecedor($where);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _getPerson($all = null, $where = null, $order = null , $group = null , $limit = null)
    {
        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();

        $rs = $dbFornecedor->getPerson($where,$group,$order,$limit);
        if(isset($all)){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    /**
     **/
    public function _comboStatus($type,$cmbtype)
    {
        $this->loadModel('status_model');
        $dbStatus = new status_model();
        $where = '';

        /*
            $persontype
            1 = Operador
                    Verifica se pertence ao grupo de compras. Se operador pertence disponibiliza todos os status.
                    Se operador não pertence disponibiliza os status
                        1 - 'Solicitado',
                        9 - 'Rejeitado',
                       11 - 'Aprovado pela Coordenação'
            2 = Aprovador
                    Disponibiliza os status
                        4 - 'Cotado - Aguardando aprovação',
                        9 - 'Rejeitado',
                       11 - 'Aprovado - Aguardando compra'
        */
        switch($type){
            case 'report': //filtros de relatório
                $fieldsID[] = 0;
                $values[]   = 'TODOS';
                break;
            case 'form': //tela de pedido
                $persontype = $this->_getSCMTypePerson("AND a.idperson = ".$this->idPerson);

                if($persontype == 1){
                    $retFlag = $this->_checkOperadorGroupCompra($this->idPerson);
                    if($retFlag == 1){$where = 'AND a.idstatus IN (1,9,11)';}
                    else{ $where = '';}
                }
                break;
        }

        /*
         *  $cmbtype
         *  1 : Combo Status Pedido
         *  2 : Combo Status Item
         * */
        if($cmbtype == 1){
            $where .= ' AND b.idtypestatus = 1';
        }else{
            $where .= ' AND b.idtypestatus = 2';
        }


        $rs = $dbStatus->getStatus($where,"","ORDER BY a.nome");

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idstatus'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções pedidos de compras
    public function _getNumPedidoCompras($where = null)
    {

        $rs = $this->dbPedidoCompra->getPedidoCompra($where);
        return $rs->RecordCount();

    }

    public function _getPedidoCompra($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoCompra->getPedidoCompra($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoCompraEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoCompra->getItemPedidoCompraEcho($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoCompra($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoCompra->getItemPedidoCompra($where, $order , $group , $limit);
        return $rs;

    }

    //funções pedidos de operadores
    public function _getNumPedidoOperadores($where = null)
    {

        $rs = $this->dbPedidoOperador->getPedidoOperadorGrid($where);
        return $rs->RecordCount();

    }

    public function _getPedidoOperador($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getPedidoOperador($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoOperadorEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getItemPedidoOperadorEcho($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoOperadorCotacaoEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getItemPedidoOperadorCotacaoEcho($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoOperador($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getItemPedidoOperador($where, $order , $group , $limit);
        return $rs;

    }

    //funções pedidos de aprovadores
    public function _getNumPedidoAprovadores($where = null)
    {

        $rs = $this->dbPedidoAprovador->getPedidoAprovadorGrid($where);
        return $rs->RecordCount();

    }

    public function _getPedidoAprovador($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoAprovador->getPedidoAprovador($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoAprovadorEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoAprovador->getItemPedidoAprovadorEcho($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemPedidoAprovador($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoAprovador->getItemPedidoAprovador($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboProduto($relatorio = null)
    {
        $this->loadModel('produto_model');
        $dbProduto = new produto_model();
        if($relatorio == '0'){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }
        $rs = $dbProduto->getProduto();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idproduto'];
            $values[]   = $rs->fields['nome'] . ' - ' . $rs->fields['unidade'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções local
    public function _getNumLocais($where = null)
    {
        $rs = $this->dbLocal->getLocal($where);
        return $rs->RecordCount();

    }

    public function _getLocal($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbLocal->getLocal($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboLocal($where = null)
    {
        $this->loadModel('local_model');
        $dbLocal = new local_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha um Local';
            $where = null;
        }
        $rs = $dbLocal->getLocal($where);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idlocal'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboLocalRelatorio($relatorio = null)
    {
        $this->loadModel('local_model');
        $dbLocal = new local_model();

        $rs = $dbLocal->getLocal();
        if($relatorio == '0'){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idlocal'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções grupos de bens
    public function _getNumGrupoDeBens($where = null)
    {
        $rs = $this->dbGrupoDeBens->getGrupoDeBens($where);
        return $rs->RecordCount();

    }

    public function _getGrupoDeBens($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbGrupoDeBens->getGrupoDeBens($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboGrupoDeBens($where = null)
    {
        $this->loadModel('grupodebens_model');
        $dbGrupoDeBens = new grupodebens_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha um Grupo';
            $where = null;
        }
        $rs = $dbGrupoDeBens->getGrupoDeBens($where);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idgrupodebens'];
            $values[]   = $rs->fields['descricao'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções bens
    public function _getNumBens($where = null)
    {
        $rs = $this->dbBens->getBens($where);
        return $rs->RecordCount();

    }

    public function _getBens($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbBens->getBens($where, $order , $group , $limit);
        return $rs;

    }

    //funções marca
    public function _getNumMarcas($where = null)
    {

        $rs = $this->dbMarca->getMarca($where);
        return $rs->RecordCount();

    }

    public function _getMarca($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbMarca->getMarca($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboMarca($where = null)
    {
        $this->loadModel('marca_model');
        $dbMarca = new marca_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha uma Marca';
            $where = null;
        }
        $rs = $dbMarca->getMarca($where);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idmarca'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    //funções estado
    public function _getNumEstados($where = null)
    {

        $rs = $this->dbEstado->getEstado($where);
        return $rs->RecordCount();

    }

    public function _getEstado($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbEstado->getEstado($where, $order , $group , $limit);
        return $rs;

    }

    public function _comboEstado($where = null)
    {
        $this->loadModel('estado_model');
        $dbEstado = new estado_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha um Estado';
            $where = null;
        }
        $rs = $dbEstado->getEstado($where);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idestado'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboEstadoRelatorio($relatorio = null)
    {
        $this->loadModel('estado_model');
        $dbEstado = new estado_model();

        $rs = $dbEstado->getEstado();
        if($relatorio == '0'){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idestado'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _sendNotification($transaction=null,$midia='email',$code_request=null)
    {
        if ($midia == 'email'){
            $cron = false;
            $smtp = false;
        }

        $this->logIt('entrou: ' . $code_request . ' - ' . $transaction . ' - ' . $midia ,7,'general');

        switch($transaction){

            case 'approve-scmrequest-operator':
                $smtp = true;
                $messageTo   = 'approve';
                $messagePart = 'Approve request # ';

                break;

            case 'reopen-ticket':
                if ($midia == 'email') {
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['REQUEST_REOPENED'] == '1' ) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }

                        $messageTo   = 'reopen';
                        $messagePart = 'Reopen request # ';
                    }
                }

                break;

            case 'evaluate-ticket':
                if($midia == 'email'){
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['EM_EVALUATED']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'afterevaluate';
                        $messagePart = 'Evaluate request # ';
                    }

                }

                break;

            case 'new-scmrequest-user':
                //deve ser configurado melhor no futuro
                /*if($midia == 'email'){
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['NEW_REQUEST_OPERATOR_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'record';
                        $messagePart = 'Insert request # ';
                    }

                }*/
                $smtp = true;
                $messageTo   = 'record';
                $messagePart = 'Insert request # ';
                break;

            case 'remove-scmrequest-user':
                //deve ser configurado melhor no futuro
                /*if($midia == 'email'){
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['NEW_REQUEST_OPERATOR_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'record';
                        $messagePart = 'Insert request # ';
                    }

                }*/
                $smtp = true;
                $messageTo   = 'delete';
                $messagePart = 'Delete request # ';
                break;

            case 'addnote-operator':
                //deve ser configurado melhor no futuro
                /*if($midia == 'email'){
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['NEW_REQUEST_OPERATOR_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'record';
                        $messagePart = 'Insert request # ';
                    }

                }*/
                $smtp = true;
                $messageTo   = 'addnote-operator';
                $messagePart = 'Add note request # ';
                break;

            case 'repass-request':
                //deve ser configurado melhor no futuro
                /*if($midia == 'email'){
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['NEW_REQUEST_OPERATOR_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'record';
                        $messagePart = 'Insert request # ';
                    }

                }*/
                $smtp = true;
                $messageTo   = 'repass-request';
                $messagePart = 'Repass request # ';
                break;

            default:
                return false;
        }

        if ($midia == 'email') {
            if ($cron) {
                $this->dbTicket->saveEmailCron($code_request, $messageTo );
                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail by cron' ,6,'general');
            } elseif($smtp){
                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail' ,6,'general');
                $this->_sendEmail($messageTo , $code_request);
            }

        }

        return true ;
    }

    public function _getCompanyDefault()
    {
        // Depois a gente melhora este método,
        // só para poder usar e não precisamos mexer no system .
        return 2;
    }

    //funções transportadora
    public function _getNumGridTransportadoras($where = null)
    {

        $rs = $this->dbTransportadora->getGridTransportadora($where);
        return $rs->RecordCount();

    }

    public function _getGridTransportadora($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbTransportadora->getGridTransportadora($where, $order , $group , $limit);
        return $rs;

    }

    public function _getNumTransportadoras($where = null)
    {

        $rs = $this->dbTransportadora->getTransportadora($where);
        return $rs->RecordCount();

    }

    public function _getTransportadora($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbTransportadora->getTransportadora($where, $order , $group , $limit);
        return $rs;

    }

    public function _getTransportadoraUpdateEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbTransportadora->getTransportadoraUpdateEcho($where, $order , $group , $limit);
        return $rs;

    }

    //funções entradas produtos
    public function _getNumEntradaProdutos($where = null)
    {

        $rs = $this->dbEntradaProduto->getEntradaProduto($where);
        return $rs->RecordCount();

    }

    public function _getEntradaProduto($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbEntradaProduto->getEntradaProduto($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemEntradaProdutoEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbEntradaProduto->getItemEntradaProdutoEcho($where, $order , $group , $limit);
        return $rs;

    }

    public function _getItemEntradaProduto($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbEntradaProduto->getItemEntradaProduto($where, $order , $group , $limit);
        return $rs;

    }
    
    public function _getEchoItemEntradaProduto($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbEntradaProduto->getEchoItemEntradaProduto($where, $order , $group , $limit);
        return $rs;

    }

    /**
     * Obter turmas do usuários
     *
     * @access public
     * @param string $user Login usuário
     * @return array
     */
    public function _getTurmaPerson($user)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/turmaperson/'.$user,false,$ctx);
        //echo $this->_serverApi.'/api/src/public/turmaperson/'.$user;

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $a = $response['result'];
            //echo "<pre>"; print_r($a); "</pre>";
            foreach ($a as $item){
                if($item['CoCurso'] == 3 && $item['Serie'] == 4 && $item['CoDisciplina'] == 48){
                    $where = "AND a.idserie = 22 AND a.numero =".$item['CoTurmaLegado'];
                }elseif($item['CoCurso'] == 3 && $item['Serie'] == 1 && $item['CoDisciplina'] == 48){
                    $where = "AND a.idserie = 19 AND a.numero =".$item['CoTurmaLegado'];
                }else{
                    $where = "AND idlegado = ".$item['CoTurma'];
                }

                $idturma =  $this->dbTurma->getTurmaData($where);
                $fieldsID[] = $idturma->fields['idcurso'].'|'.$idturma->fields['serie'].'|'.$idturma->fields['idturma'];
                $values[]   = $item['NoAbrevTurma'];
            }
            $arrRet['ids'] = $fieldsID;
            $arrRet['values'] = $values;
        }else{$arrRet = array();}


        return $arrRet;
    }

    public function _getPedidoTurma($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoCompra->getPedidoTurma($where, $order , $group , $limit);
        return $rs;

    }

    public function _sendEmail($operation, $code_request, $reason = NULL,$arrCota = NULL) {

        $mail = $this->returnPhpMailer();
        $this->loadModel('scmemailconfig_model');
        $dbEmailConfig = new scmemailconfig_model();

        if (!isset($operation)) {
            print("Email code not provided !!!");
            return false;
        }

        $sentTo = "";

        // Common data
        $rsReqData = $this->dbPedidoCompra->getPedidoCompra('WHERE idpedido = '. $code_request);
        $rsPedidoTurma = $this->_getPedidoTurma("AND idpedido = $code_request");

        $REQUEST = $code_request;
        $REQUESTER = $rsReqData->fields['nomepessoa'];
        $RECORD = $this->formatDate($rsReqData->fields['dataentrega']);
        $DESCRIPTION = $rsReqData->fields['motivo'];
        $REQUEST_STATUS = $rsReqData->fields['nomestatus'];

        // Notes
        $table = $this->makeItensTable($code_request);
        $NT_OPERATOR = $table;

        $this->loadModel('operatorview_model');
        //$bdop = new operatorview_model();

        switch ($operation) {
            // New request
            case "record":

                $templateId = $dbEmailConfig->getEmailIdBySession("SCM_NEW_REQUEST");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $userRole = $this->_getUserRole($rsReqData->fields['loginpessoa']);

                switch ($userRole){
                    case 1:
                        $rsgroup = $this->dbPedidoCompra->getIdGroup('WHERE idserie = '. $rsPedidoTurma->fields['idserie']);
                        $idgroup = $rsgroup->fields['idgroup'];
                        break;
                    default:
                        $idgroup = 15; //id do grupo de compras - quando a solicitação não foi solicitada por professor
                        break;
                }

                $sentTo = $this->setSendTo($dbEmailConfig,$idgroup,'G');

                break;

            case 'approve':
                $OPERATOR_NAME = utf8_decode($_SESSION['SES_NAME_PERSON']);
                $templateId = $dbEmailConfig->getEmailIdBySession("SCM_APPROVE_REQUEST");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                switch ($rsReqData->fields['idstatus']){
                    case 11:
                    case 10:
                    case 14:
                        $sentTo = $this->setSendTo($dbEmailConfig,15,'G');
                        $sentTo .= ";" . $this->setSendTo($dbEmailConfig,$rsReqData->fields['idperson'],'P');
                        break;
                    case 13:
                        $rsaprovador = $this->dbPedidoOperador->getIdAprovador('WHERE idcontacontabil = '.$rsReqData->fields['idcontacontabil']);
                        $sentTo = $this->setSendTo($dbEmailConfig,$rsaprovador->fields['idperson'],'P');
                        $sentTo .= ";" . $this->setSendTo($dbEmailConfig,$rsReqData->fields['idperson'],'P');
                        break;
                    default:
                        $sentTo = $this->setSendTo($dbEmailConfig,$rsReqData->fields['idperson'],'P');
                        break;
                }

                break;

            case 'delete':
                $REQUESTER = utf8_decode($_SESSION['SES_NAME_PERSON']);
                $templateId = $dbEmailConfig->getEmailIdBySession("SCM_DELETE_REQUEST");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $idgroup = 15; //id do grupo de compras - quando a solicitação não foi solicitada por professor
                $sentTo = $this->setSendTo($dbEmailConfig,$idgroup,'G');

                break;

            case 'addnote-operator':
                $OPERATOR_NAME = utf8_decode($_SESSION['SES_NAME_PERSON']);
                $tablenotes = $this->makePedidoNotesTable($code_request);
                $NOTES_LINE = $tablenotes;
                $templateId = $dbEmailConfig->getEmailIdBySession("SCM_ADD_NOTE");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$rsReqData->fields['idperson'],'P');

                break;

            case 'repass-request':
                $OPERATOR_NAME = utf8_decode($_SESSION['SES_NAME_PERSON']);
                $rsInCharge = $this->dbPedidoAprovador->getInCharge($code_request);
                $IN_CHARGE_NAME = $rsInCharge->fields['name'];

                $templateId = $dbEmailConfig->getEmailIdBySession("SCM_REPASS_REQUEST");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$rsInCharge->fields['id_in_charge'],'P');

                break;

            /*case 'close':
                $COD_CLOSE = $bd->getEmailIdBySession("FINISH_MAIL");
                //$COD_CLOSE = "2";
                $rsTemplate = $bd->getTemplateData($COD_CLOSE);

                //$bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $reqEmail = $bd->getRequesterEmail($code_request);
                $sentTo = $reqEmail->fields['email'];
                $typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);

                $this->loadModel('evaluation_model');
                $ev = new evaluation_model();
                $tk = $ev->getToken($code_request);
                $token = $tk->fields['token'];
                if($token)
                    $LINK_EVALUATE =  $hdk_url."helpdezk/evaluate/index/token/".$token;

                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $table = $this->makeNotesTable($code_request);
                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$sentTo = $rsMail->Fields("DES_EMAIL");

                break;

            case 'reject':
                $COD_REJECT = $bd->getEmailIdBySession("REJECTED_MAIL");
                //$COD_REJECT = "3";
                $rsTemplate = $bd->getTemplateData($COD_REJECT);

                //$bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $reqEmail = $bd->getRequesterEmail($code_request);
                $sentTo = $reqEmail->fields['email'];

                $typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $table = $this->makeNotesTable($code_request);
                $NT_USER = $table;

                //require_once('../includes/solicitacao_detalhe.php');
                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->Fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->Fields("description"));
                eval("\$conteudo = \"$conteudo\";");


                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;
                $goto = ('usuario/solicita_detalhes.php?COD_SOLICITACAO=' . $COD_SOLICITACAO);
                $url = '<a href="' . $url_helpdesk . 'index.php?url=' . urlencode($goto) . '">' . $l_eml["link_solicitacao"] . '</a>';

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
                //$sentTo = $rsMail->Fields("DES_EMAIL");

                break;

            case 'user_note' :

                $templateId = $dbEmailConfig->getEmailIdBySession("USER_NEW_NOTE_MAIL");

                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");


                //
                $FINISH_DATE = $this->formatDate(date('Y-m-d H:i'));
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                $reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                $typeuser = $reqEmail->fields['idtypeperson'];

                if($typeuser == 2)
                    $LINK_USER = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);


                $sentTo = $reqEmail->fields['email'];


                if($_SESSION['SES_ATTACHMENT_OPERATOR_NOTE']){
                    $rsAttachs = $this->dbTicket->getNoteAttchByCodeRequest($code_request);
                    if($rsAttachs) {
                        $att_path = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/' ;
                        while (!$rsAttachs->EOF) {
                            $ext = strrchr($rsAttachs['filename'], '.');
                            $attachment_dest = $att_path . $rsAttachs['idnote_attachments'] . $ext;

                            $mail->AddAttachment($attachment_dest, $rsAttachs->fields['filename']);

                            $rsAttachs->MoveNext();
                        }
                    }

                }

                break;

            case 'operator_note' :

                $templateId = $dbEmailConfig->getEmailIdBySession("USER_NEW_NOTE_MAIL"); // 13

                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }

                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";

                $FINISH_DATE = $this->formatDate(date('Y-m-d H:i'));
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                if($_SESSION['SES_ATTACHMENT_OPERATOR_NOTE'] == '1') {
                    $rsAttachs = $this->dbTicket->getNoteAttchByCodeRequest($code_request);
                    if ($rsAttachs->RecordCount() > 0) {
                        $att_path = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/';
                        while (!$rsAttachs->EOF) {
                            $ext = strrchr($rsAttachs->fields['filename'], '.');
                            $attachment_dest = $att_path . $rsAttachs->fields['idnote_attachments'] . $ext;
                            $this->logIt('Anexo: '. $attachment_dest,7,'email');
                            $mail->AddAttachment($attachment_dest, $rsAttachs->fields['filename']);
                            $rsAttachs->MoveNext();
                        }
                    }
                }

                $NT_USER = $this->setTableNotes($code_request);

                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

                break;

            case 'reopen':

                $templateId = $dbEmailConfig->getEmailIdBySession("REQUEST_REOPENED");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

                break;

            case "afterevaluate":

                $templateId = $dbEmailConfig->getEmailIdBySession("EM_EVALUATED");

                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }

                }

                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

                break;

            case "repass":
                $COD_RECORD = $bd->getEmailIdBySession("REPASS_REQUEST_OPERATOR_MAIL");

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                //$bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                $LINK_USER     = $this->makeLinkUser($code_request);

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$sentTo) {
                            $sentTo = $grpEmails->Fields('email');
                        } else {
                            $sentTo .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $sentTo = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");


                break;

            case "approve":
                $COD_RECORD = $bd->getEmailIdBySession("SES_REQUEST_APPROVE");

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                //$bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                $LINK_USER     = $this->makeLinkUser($code_request);

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$sentTo) {
                            $sentTo = $grpEmails->Fields('email');
                        } else {
                            $sentTo .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $sentTo = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");


                break;

            case "operator_reject":
                $COD_REJECT = $bd->getEmailIdBySession("SES_MAIL_OPERATOR_REJECT");

                $rsTemplate = $bd->getTemplateData($COD_REJECT);

                //$bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $grpEmails = $bd->getEmailsfromGroupOperators($_SESSION['SES_MAIL_OPERATOR_REJECT_ID']);
                while (!$grpEmails->EOF) {
                    if (!$sentTo) {
                        $sentTo = $grpEmails->Fields('email');
                    } else {
                        $sentTo .= ";" . $grpEmails->Fields('email');
                    }
                    $grpEmails->MoveNext();
                }

                $typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                $LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                $USER = $notes->fields["name"];
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->Fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->Fields("description"));
                eval("\$conteudo = \"$conteudo\";");

                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");

                break;*/

        }

        $dbCommon = new common();
        $emconfigs = $dbCommon->getEmailConfigs();
        $tempconfs = $dbCommon->getTempEmail();

        $mail_title     = 'no-reply';//$emconfigs['EM_TITLE'];
        $mail_method    = 'smtp';
        $mail_host      = $emconfigs['EM_HOSTNAME'];
        $mail_domain    = $emconfigs['EM_DOMAIN'];
        $mail_auth      = $emconfigs['EM_AUTH'];
        $mail_username  = $emconfigs['EM_USER'];
        $mail_password  = $emconfigs['EM_PASSWORD'];
        $mail_sender    = $emconfigs['EM_SENDER'];
        $mail_header    = '';//$tempconfs['EM_HEADER'];
        $mail_footer    = '';//$tempconfs['EM_FOOTER'];
        $mail_port      = $emconfigs['EM_PORT'];



        $mail->CharSet = 'utf-8';
        //ini_set('default_charset', 'UTF-8');
        $mail->addCustomHeader('X-scmRequest: '. $REQUEST);
        if ($this->getConfig('demo') == true) {
            $mail->addCustomHeader('X-hdkLicence:' . 'demo');
        } else {
            $mail->addCustomHeader('X-hdkLicence:' . $this->getConfig('license'));
        }

        $mail->From     = $mail_sender;
        $mail->FromName = $mail_title;
        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_port) AND !empty($mail_port)) {
            $mail->Port = $mail_port;
        }

        $mail->Mailer = $mail_method;
        $mail->SMTPAuth = $mail_auth;
        if (strpos($mail_username,'gmail') !== false) {
            $mail->SMTPSecure = "tls";
        }
        $mail->Username = $mail_username;
        $mail->Password = $mail_password;

        $mail->AltBody = "HTML";
        $mail->Subject = utf8_decode($subject);

        //$mail->SetFrom($mail_sender, $mail_title);
        //echo $subject.'<br>'.$contents;
        //Checks for more than 1 email address at recipient
        $this->makeSentTo($mail,$sentTo);

        // Tracker
        if($this->tracker) {
            $body = $mail_header . $contents . $mail_footer;
            $idEmail = $this->_saveTracker($this->idmodule,$mail_sender,$sentTo,addslashes($subject),addslashes($body),$REQUEST,$operation);
            if(!$idEmail) {
                $this->logIt("Error insert in tbtracker, request # " . $REQUEST . ' - Operation: ' . $operation . ' - program: ' . $this->program, 3, 'email', __LINE__);
            } else {

                $trackerID = '<img src="'.$this->helpdezkUrl.'/tracker/'.$this->modulename.'/'.$idEmail.'.png" height="1" width="1" />' ;
                $mail->Body = $mail_header . $contents . $mail_footer . $trackerID;
            }
        } else {
            $mail->Body = $mail_header . $contents . $mail_footer;
        }

        $mail->SetLanguage('br', $this->helpdezkPath . "/includes/classes/phpMailer/");

        $done = $mail->Send();

        if (!$done) {
            if($this->log AND $_SESSION['EM_FAILURE_LOG'] == '1') {
                $mail->SMTPDebug = 5;
                $mail->Send();
                $this->logIt("Error send email, scm_request # " . $REQUEST . ' - Operation: ' . $operation . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, scm_request # " . $REQUEST . ' - Error Info:: ' . $mail->ErrorInfo . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, request # " . $REQUEST . ' - Variables: HOST: '.$mail_host.'  DOMAIN: '.$mail_domain.'  AUTH: '.$mail_auth.' PORT: '.$mail_port.' USER: '.$mail_username.' PASS: '.$mail_password.'  SENDER: '.$mail_sender.' - program: ' . $this->program, 7, 'email', __LINE__);
            }
            return false ;
        } else {
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logIt("Email Succesfully Sent, scm_request # ". $REQUEST . ', operation: '. $operation ,6,'email');
            }
            return true ;
        }


    }

    function setSendTo($dbEmailConfig,$idgroup,$type)
    {
        $sentTo = '';

        if ($type == 'G') {
            //$this->logIt("Entrou G " . ' - program: ' . $this->program, 7, 'email', __LINE__);
            $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($idgroup);

            while (!$grpEmails->EOF) {
                if (!$sentTo) {
                    $sentTo = $grpEmails->fields['email'];
                    //$sentTo = 'valentin.acosta@marioquintana.com.br'; //para testes
                    //$this->logIt("Entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
                } else {
                    $sentTo .= ";" . $grpEmails->fields['email'];
                    //$sentTo = ';valentin.acosta@marioquintana.com.br'; //para testes
                    //$this->logIt("Entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
                }
                $grpEmails->MoveNext();
            }
        } else {
            //$this->logIt("NAO entrou G " . ' - program: ' . $this->program, 7, 'email', __LINE__);
            $userEmail = $dbEmailConfig->getUserEmail($idgroup);
            $sentTo = $userEmail->fields['email'];
            //$sentTo = 'valentin.acosta@marioquintana.com.br'; //para testes
            //$this->logIt("Nao entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
        }

        return $sentTo ;
    }

    public function makeSentTo($mail,$sentTo)
    {
        //$this->logIt('sentTo: ' . $sentTo,7,'email');
        $jaExiste = array();
        if (preg_match("/;/", $sentTo)) {
            //$this->logIt('Entrou',7,'email');
            $email_destino = explode(";", $sentTo);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    // If the e-mail address is NOT in the array, it sends e-mail and puts it in the array
                    // If the email already has the array, do not send again, avoiding duplicate emails
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $mail->AddAddress($email_destino[$i]);
                        $jaExiste[] = $email_destino[$i];
                    }
                }
            } else {
                //$this->logIt('Entrou ' . $email_destino,7,'email');
                $mail->AddAddress($email_destino);
            }
        } else {
            //$this->logIt('Nao Entrou ' . $sentTo,7,'email');
            $mail->AddAddress($sentTo);
        }
    }

    public function makeItensTable($code_request)
    {
        $itens = $this->_getItemPedidoCompraEcho("$code_request");

        $table = "";
        while (!$itens->EOF) {
            $table.= "<tr><td>".$itens->fields['nome']."<td>";
            $table.= "<td class='alignright'>".$itens->fields['quantidade']."</td></tr>";
            $itens->MoveNext();
        }
        return $table;
    }

    public function _getGrupoOperador($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getGrupoOperador($where, $order, $group, $limit);
        return $rs;

    }

    public function _getSCMTypePerson($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getSCMTypePerson($where, $order, $group, $limit);
        return $rs;

    }

    public function _getTurmaGrupo($idgroup)
    {

        $rs = $this->dbPedidoOperador->getTurmaGrupo($idgroup);
        return $rs;

    }

    public function _getPedidoOperadorGrid($where = null, $order = null , $group = null , $limit = null)
    {

        $rs = $this->dbPedidoOperador->getPedidoOperadorGrid($where, $order , $group , $limit);
        return $rs;

    }

    public function _checkOperadorGroupCompra($iduser)
    {

        $rs = $this->_getGrupoOperador("AND ghp.idperson = ".$iduser);
        $flagGCompra = 0;
        while(!$rs ->EOF){
            if($rs ->fields['idgroup'] != 15){$flagGCompra = 1;}
            $rs ->MoveNext();
        }
        return $flagGCompra;

    }

    public function _getCContabilAprovador($iduser)
    {

        $rs = $this->dbPedidoAprovador->getIdContaContabilAprovador('WHERE idperson = '.$iduser);
        return $rs;

    }

    public function _getHolidays()
    {
        $year = $_POST['cmbYear'];

        $this->loadModel('pedidocompra_model');
        $dbPedido = new pedidocompra_model();

        $where = "WHERE YEAR(holiday_date) = ".$year;
        $rs = $dbPedido->getHolidays($where);

        while (!$rs->EOF) {
            $arrRet[] = $rs->fields['holiday_br'];
            $rs->MoveNext();
        }

        $aRet = array(
            "dates" => $arrRet,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    public function _getDefaultDataEntrega($dateDefault){

        $this->loadModel('pedidocompra_model');
        $dbPedido = new pedidocompra_model();

        $yearTmp = substr($dateDefault, 0, 4);
        $monthTmp = substr($dateDefault, 5, 2);
        $dayTmp = substr($dateDefault, 8, 2);

        $dayWeek = date("w", mktime(0,0,0,$monthTmp,$dayTmp,$yearTmp));

        if($dayWeek >= 1 and $dayWeek <= 5){
            $where = "WHERE holiday_date = '".$dateDefault."'";
            $rs = $dbPedido->getHolidays($where);
            if($rs->RecordCount() > 0){
                $dateDefaultTmp = date("Y-m-d", mktime(0,0,0,$monthTmp,($dayTmp+1),$yearTmp));
                return $this->_getDefaultDataEntrega($dateDefaultTmp);
            }else{
                $dateDefaultFinal = $dayTmp."/".$monthTmp."/".$yearTmp;

                return $dateDefaultFinal;
            }
        }else{
            // soma um na data se cair no domingo
            if ($dayWeek == 0)	$dateDefaultTmp = date("Y-m-d", mktime(0,0,0,$monthTmp,($dayTmp+1),$yearTmp));
            // soma dois na data se cair no sabado
            if ($dayWeek == 6)	$dateDefaultTmp = date("Y-m-d", mktime(0,0,0,$monthTmp,($dayTmp+2),$yearTmp));

            return $this->_getDefaultDataEntrega($dateDefaultTmp);
        }

    }

    public function _getPersonDepartment($userid){

        $this->loadModel('pedidocompra_model');
        $dbPedido = new pedidocompra_model();

        $rs = $dbPedido->getPersonDepartment("AND a.idperson = ".$userid);

        return $rs;

    }

    public function _getTurmaList($type,$idperson)
    {


        if($type == 'G'){
            $rs = $this->dbPedidoOperador->getTurmaGrupo($idperson);
        }else{
            $this->loadModel('acdturma_model');
            $dbTurma = new acdturma_model();
            $rs = $dbTurma->getTurmaData('','ORDER BY b.idcurso, b.numero, abrev');
        }

        while(!$rs->EOF){
            $fieldsID[] = $rs->fields['idcurso'].'|'.$rs->fields['numero'].'|'.$rs->fields['idturma'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _getUserRole($user)
    {
        $arrGroupCoord = array(11,12,13,14,19);

        $retType = $this->_getSCMTypePerson("AND b.login = '".$user."'");
        if(!$retType){

            $ret = $this->_getTurmaPerson($user);

            if(sizeof($ret) > 0){
                $idrole = 1;
            }else{
                $idrole = 4;
            }
        }else{
            $retUserGroup = $this->_getGrupoOperador("AND p.login = '".$user."'");
            $flagCoord = 0;
            while(!$retUserGroup->EOF){
                if(in_array($retUserGroup->fields['idgroup'],$arrGroupCoord)){$flagCoord = 1;}
                $retUserGroup->MoveNext();
            }
            if($flagCoord == 1){$idrole = 2;}
            else{$idrole = 3;}
        }

        return $idrole;
    }

    public function _comboCentroCustoAprovador()
    {
        $this->loadModel('centrocusto_model');
        $dbCentroCusto = new centrocusto_model();

        $rs = $dbCentroCusto->getCentroCustoByUserId();

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcentrocusto'];
            $values[]   = $rs->fields['codigo'] . ' - ' . $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboContaContabilAprovador($where = null)
    {
        $this->loadModel('contacontabil_model');
        $dbContaContabil = new contacontabil_model();
        if($where == '0'){
            $fieldsID[] = 0;
            $values[]   = 'Escolha uma conta';
            $where = null;
        }
        $rs = $dbContaContabil->getContaContabilByUserId($where);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcontacontabil'];
            $values[]   = $rs->fields['codigo'] . ' - ' . $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _updateStock($idproduct,$quantity,$type){

        $upd = $this->dbProduto->updateStock($idproduct,$quantity,$type);

        if (!$upd) {
            if($this->log)
                $this->logIt('Update Stock  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        return true;
    }

    public function _savePedidoNote($idperson,$idpedido,$notecontent,$displayuser)
    {
        $ins = $this->dbPedidoOperador->insertPedidoNote($idpedido, $idperson, $notecontent, $this->databaseNow, $displayuser);
        if(!$ins){
            if($this->log)
                $this->logIt("Add note in pedido # ". $idpedido . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $idNoteInsert = $this->dbPedidoOperador->insertPedidoNoteLastID();

        if($this->log)
            $this->logIt("Add note in pedido # ". $idpedido . ' - User: ' . $_SESSION['SES_LOGIN_PERSON'],6,'general');

        return $idNoteInsert;


    }

    function _makePedidoNotesScreen($idpedido)
    {
        // Pedido data
        $rsPedido = $this->_getPedidoOperador("WHERE idpedido = $idpedido") ;
        $where = "AND idpedido = '$idpedido'";

        // Notes
        $typeperson = $this->_getSCMTypePerson("AND a.idperson = ".$this->idPerson);
        if(!$typeperson){$where .= "AND nt.user_visibility = '1'";}

        $rsNotes = $this->dbPedidoOperador->getPedidoNotes($where);
        $lineNotes = '';

        while(!$rsNotes->EOF){

            if ($rsNotes->fields['idperson'] == $rsPedido->fields['idperson']) {
                // User
                $iconNote = ' <i class="fa fa-user "></i>';
            } else {
                $iconNote = ' <i class="fa fa-cogs "></i>';
            }

            $noteTitle  = $this->formatDateHour($rsNotes->fields['entry_date']) . " [" . $this->getPersonName($rsNotes->fields['idperson']) . "] <br>";
            $note =  $rsNotes->fields['description'] ;

            $lineNotes .=   '
                <div id="pedido_notes" class="row wrapper  white-bg ">
                    <div class="timeline-item">
                        <div class="row">
                            <div class="col-sm-3 date">
                                '.$iconNote.'
                                <br/>
                            </div>
                            <div class="col-sm-9 content">
                                <p class="m-b-xs"><strong>'.$noteTitle.'</strong></p>
                                <p>
                                 '.$note.'
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                            ';
            $rsNotes->MoveNext();
        }

        return $lineNotes;
    }

    function _ajaxPedidoNotesScreen()
    {
        $idpedido = $_POST['idpedido'];
        $notes =  $this->_makePedidoNotesScreen($idpedido);

        $rsTypeUser = $this->_getSCMTypePerson("AND a.idperson = ".$this->idPerson);

        if($rsTypeUser){$displaytype = 'S';}
        else{$displaytype = 'N';}

        $aRet = array(
            "displaytype" => $displaytype,
            "notes" => $notes
        );

        echo json_encode($aRet);
    }

    public function makePedidoNotesTable($code_request)
    {
        $notes = $this->dbPedidoOperador->getPedidoNotes("AND nt.idpedido = $code_request");

        $table = "";
        while (!$notes->EOF) {
            $noteTitle  = $this->formatDateHour($notes->fields['entry_date']) . " [" . $this->getPersonName($notes->fields['idperson']) . "] <br>";
            $note =  $notes->fields['description'] ;

            $table.= "<tr><td colspan='2'><strong>".$noteTitle."</strong><td></tr>";
            $table.= "<tr><td colspan='2'>".$note."</td></tr>";
            $notes->MoveNext();
        }
        return $table;
    }

    public function makeProdutoGallery()
    {
        $pics = $this->_getImagemProduto("WHERE idproduto = ". $_POST['idproduto']);

        $content = "";
        $i = 0;
        while (!$pics->EOF) {
            $picsrc = $this->helpdezkUrl . '/app/uploads/photos/'.$pics->fields['nome'];

            $flagAtive = ($i == 0) ? 'active' : '';
            $content.= "<div class='item $flagAtive'> <img src='".$picsrc."' style='max-width:700px; max-heigth:700px;' alt='item".$i."'>
                        <!--<div class='carousel-caption'>
                            <h3>Heading 3</h3>
                            <p>Slide 0  description.</p>
                        </div>-->
                    </div>";
            $i++;
            $pics->MoveNext();
        }

        echo $content;
    }

    public function _comboAprovador()
    {
        $rs = $this->dbPedidoAprovador->getAprovador("AND a.idperson != ".$_SESSION['SES_COD_USUARIO']);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _isInCharge($idpedido,$idperson)
    {
        $rs = $this->dbPedidoAprovador->getInCharge($idpedido);

        if($rs->fields['id_in_charge'] == $idperson and $rs->fields['ind_repass'] == 'Y'){
            return 1;
        }else{
            return 0;
        }

    }

    function _saveTracker($idmodule,$mail_sender,$sentTo,$subject,$body)
    {
        $ret = $this->dbTracker->insertEmail($idmodule,$mail_sender,$sentTo,$subject,$body);
        if(!$ret) {
            return false;
        } else {
            return $ret;
        }

    }

}