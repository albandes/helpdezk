<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
*  Common methods - Supply Module
*/

if(class_exists('Controllers')) {
   class DynamicscmCommon extends Controllers {}
} elseif(class_exists('cronController')) {
   class DynamicscmCommon extends cronController {}
} elseif(class_exists('apiController')) {
   class DynamicscmCommon extends apiController {}
}


class scmCommon extends DynamicscmCommon  {


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

        $this->loadModel('baixaproduto_model');
        $this->dbBaixa = new baixaproduto_model();

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

        if(!$this->isCli()){
            $this->_serverApi = $this->_getServerApi();
        }
        
        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('admin/tracker_model');
        $dbTracker = new tracker_model();
        $this->dbTracker = $dbTracker;

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->modulename = 'suprimentos' ;
            $this->idmodule = $this->getIdModule($this->modulename) ;
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        $this->modulename = 'suprimentos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        $this->saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $this->getConfig('s3bucket_name');
            $this->imgDir = "https://{$bucket}.s3.amazonaws.com/scm/produtos/";
        }else{
            if($this->_externalStorage) {
                $this->imgDir = $this->_externalStoragePath.'/scm/produtos/';
            } else {
                $this->imgDir = $this->helpdezkPath.'/app/uploads/scm/produtos/';
            }
        }

    }

    public function _makeNavScm($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idPerson,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        //$smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);
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
        if(!$where){
            $fieldsID[] = 0;
            $values[]   = 'Escolha um Fornecedor';
        }

        $rs = $dbFornecedor->getFornecedor($where);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['fantasy_name'] ? $rs->fields['fantasy_name'] : $rs->fields['name'];
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

    public function _comboProduto($relatorio = null,$where=null,$order=null)
    {
        $this->loadModel('produto_model');
        $dbProduto = new produto_model();
        if($relatorio == '0'){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }

        if(!$order)
            $order = "ORDER BY scm_tbproduto.nome";


        $rs = ($where && $where !='') ? $dbProduto->getProduto($where,$order) : $dbProduto->getProduto(null,$order);
        while (!$rs->EOF) {
            
            if($rs->fields['status'] != "A")
                $disablesID[] =  $rs->fields['idproduto'];
            
            $fieldsID[] = $rs->fields['idproduto'];
            $values[]   = $rs->fields['nome'] . ' - ' . $rs->fields['unidade'];
            $options[$rs->fields['idproduto']] = $rs->fields['nome'] . ' - ' . $rs->fields['unidade'];
            
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['disables'] = $disablesID;
        $arrRet['options'] = $options;

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
                if ($midia == 'email') {
                    if ($_SESSION['scm']['SEND_EMAILS'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'approve';
                        $messagePart = 'Approve request # ';
                    }
                }
                break;
            case 'new-scmrequest-user':
                if ($midia == 'email') {
                    if ($_SESSION['scm']['SEND_EMAILS'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'record';
                        $messagePart = 'Insert request # ';
                    }
                }
                break;
            case 'remove-scmrequest-user':
                if ($midia == 'email') {
                    if ($_SESSION['scm']['SEND_EMAILS'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'delete';
                        $messagePart = 'Delete request # ';
                    }
                }
                break;
            case 'addnote-operator':
                if ($midia == 'email') {
                    if ($_SESSION['scm']['SEND_EMAILS'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'addnote-operator';
                        $messagePart = 'Add note request # ';
                    }
                }
                break;
            case 'repass-request':
                if ($midia == 'email') {
                    if ($_SESSION['scm']['SEND_EMAILS'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'repass-request';
                        $messagePart = 'Repass request # ';
                    }
                }
                break;
            case 'addnote-user':
                if ($midia == 'email') {
                    if ($_SESSION['scm']['SEND_EMAILS'] == '1') {
                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }
                        $messageTo   = 'addnote-user';
                        $messagePart = 'Add note request # ';
                    }
                }
                break;

            default:
                return false;
        }

        if ($midia == 'email') {
            if ($cron) {
                $retCron = $this->dbPedidoCompra->saveEmailCron($this->idmodule,$code_request, $messageTo );
                if(!$retCron['success']){
                    if($this->log)
                        $this->logIt($retCron['message'],3,'general');
                }else{
                    if($this->log)
                        $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail by cron' ,6,'general');
                }
                
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
            $fieldsID = array();
            foreach ($a as $item){
                $where = $this->_getConditionTurma($item);

                $idturma =  $this->dbTurma->getTurmaData($where);
                $idfieldturma = $idturma->fields['idcurso'].'|'.$idturma->fields['serie'].'|'.$idturma->fields['idturma'];

                if(!in_array($idfieldturma,$fieldsID)){
                    $fieldsID[] = $idfieldturma;
                    $values[]   = $item['NoAbrevTurma'];
                }

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
                        $idgroup[] = $rsgroup->fields['idgroup'];

                        //verifica se o usuário pertence ao um grupo de lab. química
                        $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                        while(!$rsusergroup->EOF){
                            $idgroup[] = $rsusergroup->fields['idgroup'];
                            $rsusergroup->MoveNext();
                        }

                        break;
                    default:
                        $idgroup[] = 15; //id do grupo de compras - quando a solicitação não foi solicitada por professor

                        //verifica se o usuário pertence ao um grupo de lab. química
                        $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                        while(!$rsusergroup->EOF){
                            $idgroup[] = $rsusergroup->fields['idgroup'];
                            $rsusergroup->MoveNext();
                        }

                        break;
                }

                foreach ($idgroup as $group){
                    $sentTo .= ($sentTo == '') ? $this->setSendTo($dbEmailConfig,$group,'G') : ';'.$this->setSendTo($dbEmailConfig,$group,'G');
                }

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

                        //verifica se o usuário pertence ao um grupo de lab. química
                        $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                        if($rsusergroup->RecordCount() > 0){
                            $sentTo .= ";" . $this->setSendTo($dbEmailConfig,25,'G');
                        }

                        $sentTo .= ";" . $this->setSendTo($dbEmailConfig,$rsReqData->fields['idperson'],'P');
                        break;
                    case 13:
                        $rsaprovador = $this->dbPedidoOperador->getIdAprovador('WHERE idcontacontabil = '.$rsReqData->fields['idcontacontabil']);
                        $sentTo = $this->setSendTo($dbEmailConfig,$rsaprovador->fields['idperson'],'P');
                        $sentTo .= ";" . $this->setSendTo($dbEmailConfig,$rsReqData->fields['idperson'],'P');

                        //verifica se o usuário pertence ao um grupo de lab. química
                        $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                        if($rsusergroup->RecordCount() > 0){
                            $sentTo .= ";" . $this->setSendTo($dbEmailConfig,25,'G');
                        }

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

                //verifica se o usuário pertence ao um grupo de lab. química
                $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                if($rsusergroup->RecordCount() > 0){
                    $sentTo .= ";" . $this->setSendTo($dbEmailConfig,25,'G');
                }
                
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

            case 'addnote-user':
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

                $userRole = $this->_getUserRole($rsReqData->fields['loginpessoa']);

                switch ($userRole){
                    case 1:
                        $rsgroup = $this->dbPedidoCompra->getIdGroup('WHERE idserie = '. $rsPedidoTurma->fields['idserie']);
                        $idgroup[] = $rsgroup->fields['idgroup'];
                        $idgroup[] = 15;

                        //verifica se o usuário pertence ao um grupo de lab. química
                        $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                        while(!$rsusergroup->EOF){
                            $idgroup[] = $rsusergroup->fields['idgroup'];
                            $rsusergroup->MoveNext();
                        }

                        break;
                    default:
                        $idgroup[] = 15; //id do grupo de compras - quando a solicitação não foi solicitada por professor

                        //verifica se o usuário pertence ao um grupo de lab. química
                        $rsusergroup = $this->_getGrupoOperador("AND ghp.idperson = '".$rsReqData->fields['idperson']."' AND ghp.idgroup IN (25)");
                        while(!$rsusergroup->EOF){
                            $idgroup[] = $rsusergroup->fields['idgroup'];
                            $rsusergroup->MoveNext();
                        }

                        break;
                }

                foreach ($idgroup as $group){
                    $sentTo .= ($sentTo == '') ? $this->setSendTo($dbEmailConfig,$group,'G') : ';'.$this->setSendTo($dbEmailConfig,$group,'G');
                }
                echo 'note-user';
                break;

        }

        $customHeader = 'X-scmRequest: '. $REQUEST;

        $msgLog = "request # ".$REQUEST." - Operation: ".$operation;
        $msgLog2 = "request # ".$REQUEST;

        $params = array("subject"       => $subject,
                        "contents"      => $contents,
                        "address"       => $sentTo,
                        "attachment"    => array(),
                        "idmodule"      => $this->idmodule,
                        "tracker"       => $this->tracker,
                        "msg"           => $msgLog,
                        "msg2"          => $msgLog2,
                        "customHeader"  => $customHeader,
                        "code_request"  => $REQUEST);


        $done = $this->sendEmailDefault($params);

        if (!$done) {
            return false ;
        } else {
            return true ;
        }

    }

    function setSendTo($dbEmailConfig,$idgroup,$type)
    {
        $sentTo = '';

        if ($type == 'G') {
            $where = "AND grp.idgroup = $idgroup";

            //traz o email do responsável do Lab. Química
            if($idgroup == 25){
                $retgroup = $dbEmailConfig->getGroupOperators("AND a.idgroup = $idgroup");
                if (!$retgroup) {
                    if($this->log)
                        $this->logIt('Error to Get Group Operators '.$idgroup.' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                $teachers = '';
                while(!$retgroup->EOF){
                    if($this->_isTeacherInGroup($retgroup->fields['login']) == 'true'){
                        $teachers .= $retgroup->fields['idperson'].',';
                    }
                    $retgroup->MoveNext();
                }
                $teachers = substr($teachers,0,-1);
                $where .= " AND pergrp.idperson NOT IN($teachers)";
            }

            $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($where);

            while (!$grpEmails->EOF) {
                if (!$sentTo) {
                    $sentTo = $grpEmails->fields['email'];
                    //$this->logIt("Entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
                } else {
                    $sentTo .= ";" . $grpEmails->fields['email'];
                    //$this->logIt("Entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
                }
                $grpEmails->MoveNext();
            }
        } else {
            $userEmail = $dbEmailConfig->getUserEmail($idgroup);
            $sentTo = $userEmail->fields['email'];
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
            $table.= "<tr><td>".$itens->fields['nome']."</td>";
            $table.= "<td class='alignright'>".$this->_scmformatNumber($itens->fields['quantidade'])."</td></tr>";
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

    /*
     * ID role
     *
     * 1 - Professor
     * 2 - Coordenação
     * 3 - Setor Compras
     * 4 - Outros Usuários
     *
     */
    public function _getUserRole($user)
    {
        $arrGroupCoord = explode(',',$_SESSION['scm']['SCM_COORDGROUPS']);

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
        if(!$typeperson){
            $retGroup = $this->_getUserGroupType($this->idPerson);
            $retTeacher = $this->_isTeacherInGroup($_SESSION['SES_LOGIN_PERSON']);

            if($retGroup == 2 && $retTeacher == 'false'){
                $where .= "";
            }else{
                $where .= "AND nt.user_visibility = '1'";
            }
        }

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
            $noteTitle  = $this->formatDate($notes->fields['entry_date']) . " [" . $this->getPersonName($notes->fields['idperson']) . "] <br>";
            $note =  $notes->fields['description'] ;

            $table.= "<tr><td colspan='2'><strong>".$noteTitle."</strong><td></tr>";
            $table.= "<tr><td colspan='2'>".$note."</td></tr>";
            $notes->MoveNext();
        }
        return $table;
    }

    public function _makeProdutoGallery()
    {
        $pics = $this->_getImagemProduto("WHERE idproduto = ". $_POST['idproduto']);

        $content = "";
        $i = 0;
        while (!$pics->EOF) {
            if($this->saveMode == "aws-s3"){
                $picsrc = $this->imgDir .$pics->fields['nome'];
            }else{
                if($this->_externalStorage) {
                    $picsrc = $this->_externalStorageUrl.'/scm/produtos/'.$pics->fields['nome'];
                } else {
                    $picsrc = $this->helpdezkUrl . $this->imgDir .$pics->fields['nome'];
                }
            }

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

    public function _makeArrayTracker($sentTo)
    {
        $jaExiste = array();
		$aRet = array();
        if (preg_match("/;/", $sentTo)) {
            $email_destino = explode(";", $sentTo);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
					if (empty($email_destino[$i])) 
						continue;
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $jaExiste[] = $email_destino[$i];
						array_push($aRet,$email_destino[$i]);
                    }
                }
            } else {
				array_push($aRet,$email_destino);
            }
        } else {
			array_push($aRet,$sentTo);
        }
		return $aRet;
    }

    public function _isEmailDone($objmail,$params){
        $done = $objmail->Send();
        if (!$done) {
            if($this->log AND $_SESSION['EM_FAILURE_LOG'] == '1') {
                $objmail->SMTPDebug = 5;
                $objmail->Send();
                $this->logIt("Error send email, scm_request # " . $params['request'] . ' - Operation: ' . $params['operation']. ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, scm_request # " . $params['request'] . ' - Error Info:: ' . $objmail->ErrorInfo . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, request # " . $params['request'] . ' - Variables: HOST: '.$params['mail_host'].'  DOMAIN: '.$params['mail_domain'].'  AUTH: '.$params['mail_auth'].' PORT: '.$params['mail_port'].' USER: '.$params['mail_username'].' PASS: '.$params['mail_password'].'  SENDER: '.$params['mail_sender'].' - program: ' . $this->program, 7, 'email', __LINE__);
            }
            $error_send = true ;
        } else {
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logIt("Email Succesfully Sent, scm_request # ". $params['request']  . ', operation: '. $params['operation'] ,6,'email');
            }
            $error_send = false ;
        }

        return $error_send;

    }

    public function _getConditionTurma($item){
        /*
         * A seguir cria a condição para os pedidos serem visualizadores pela coordenação certa
         * CoCurso =>   1 - Ensino Fundamental      2 - Ensino Médio        3 - Educação Infantil
         * CoDisciplina => 48 - Activity    32 - Ed. Física (Ed. Inf.)      6 - Ed. Física (Fundamental)
        */

        $where = "WHERE courselegacy = {$item['CoCurso']} AND serielegacy = {$item['Serie']} AND disciplinalegacy = {$item['CoDisciplina']}";
        $rs = $this->dbTurma->getTurmaCondition($where);
        if (!$rs){
            if ($this->log)
                $this->logIt('Can\'t get Turma condition - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        if($rs->RecordCount() > 0 ){
            $condition = "AND a.idserie = {$rs->fields['idserie']} AND a.numero = {$item['CoTurmaLegado']}";
        }else{
            $condition = "AND idlegado = ".$item['CoTurma'];
        }

        return $condition;

    }

    public function _scmformatNumber($value){
        $Temp = stristr($value, '.');
        if($Temp == '.00'){$result = strstr($value, '.', true);}
        else{$result = number_format($value,2,',','.');}

        return $result;
    }

    public function _getRequesterByGroup($idgroup,$status=null,$all=null)
    {
        $rs = $this->dbPedidoOperador->getRequesterByGroup($idgroup,$status);

        if(isset($all)){
            $fieldsID[] = 0;
            $values[]   = 'TODOS';
        }

        while(!$rs->EOF){
            $fieldsID[] = $rs->fields['idpesron'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _getExpireDate($startDate = null, $days = null, $fullday = true){

        if(!isset($startDate)){$startDate = date("Y-m-d H:i:s");}

        if(!$days){
            $days_sum = "+0 day";
        }elseif($days > 0 or $days == 1){
            $days_sum = "+".$days." day";
        }else{
            $days_sum = "+".$days." days";
        }

        $data_sum = date("Y-m-d H:i:s",strtotime($startDate." ".$days_sum));

        $this->loadModel('helpdezk/expiredate_model');
        $db = new expiredate_model();

        $date_holy_start = date("Y-m-d",strtotime($startDate)); // Separate only the inicial date to check for holidays in the period
        $date_holy_end = date("Y-m-d",strtotime($data_sum)); //Separate only the final date to check for holidays in the period

        $rsNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy_start,$date_holy_end); // Verifies the quantity of holidays in the period
        if(!$rsNationalDaysHoliday)
            return false;

        if(isset($idcompany)){
            $rsCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy_start,$date_holy_end,$idcompany); // Verifies the quantity of company�s holidays in the period
            if(!$rsCompanyDaysHoliday)
                return false;
            $sum_days_holidays = $rsNationalDaysHoliday->fields['num_holiday'] + $rsCompanyDaysHoliday->fields['num_holiday'];
        }else{
            $sum_days_holidays = $rsNationalDaysHoliday->fields['num_holiday'];
        }

        // Add holidays
        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$sum_days_holidays." days"));

        // Working days
        $rsBusinessDays = $db->getBusinessDays();
        if(!$rsBusinessDays)
            return false;

        while (!$rsBusinessDays->EOF) {
            $businessDay[$rsBusinessDays->fields['num_day_week']] = array(
                "begin_morning" 	=> $rsBusinessDays->fields['begin_morning'],
                "end_morning" 		=> $rsBusinessDays->fields['end_morning'],
                "begin_afternoon" 	=> $rsBusinessDays->fields['begin_afternoon'],
                "end_afternoon" 	=> $rsBusinessDays->fields['end_afternoon']
            );
            $rsBusinessDays->MoveNext();
        }

        $date_check_start = date("Y-m-d",strtotime($startDate));
        $date_check_end = date("Y-m-d",strtotime($data_sum));
        $addNotBussinesDay = 0;

        // Non-working days
        while (strtotime($date_check_start) <= strtotime($date_check_end)) {
            $numWeek = date('w',strtotime($date_check_start));
            if (!array_key_exists($numWeek, $businessDay)) {
                $addNotBussinesDay++;
            }
            $date_check_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_check_start)));
        }

        $data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$addNotBussinesDay." days")); // Add non-working days
        $data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
        if(!$fullday){
            $data_sum = $this->checkValidBusinessHour($data_check_bd,$businessDay); // Verify if the time is the interval of service
        }

        // If you change the day, check to see if it is a working day
        if(strtotime(date("Y-m-d",strtotime($data_check_bd))) != strtotime(date("Y-m-d",strtotime($data_sum)))){
            $data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
            return $data_check_bd;
        }else{
            return $data_sum;
        }

    }

    private function checkValidBusinessDay($date,$businessDay,$idcompany = null){

        $this->loadModel('expiredate_model');
        $db = new expiredate_model();

        $numWeek = date('w',strtotime($date));

        $i = 0;
        while($i == 0){
            while (!array_key_exists($numWeek, $businessDay)) {
                $date = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));
                $numWeek = date('w',strtotime($date));
            }
            $date_holy = date("Y-m-d",strtotime($date));

            $rsNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy,$date_holy);
            if(!$rsNationalDaysHoliday)
                return false;

            if(isset($idcompany)){
                $rsCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy,$date_holy,$idcompany);
                if(!$rsCompanyDaysHoliday){
                    $db->RollbackTrans();
                    return false;
                }
                $daysHoly = $rsNationalDaysHoliday->fields['num_holiday'] + $rsCompanyDaysHoliday->fields['num_holiday'];
            }else{
                $daysHoly = $rsNationalDaysHoliday->fields['num_holiday'];
            }

            if($daysHoly > 0){
                $date = date("Y-m-d H:i:s",strtotime($date." +".$daysHoly." days"));
                $numWeek = date('w',strtotime($date));
            }else{
                $i = 1;
            }
        }
        return $date;
    }

    private function checkValidBusinessHour($date,$businessDay){
        $i = 0;
        while($i == 0){
            $numWeek = date('w',strtotime($date));
            $hour = strtotime(date('H:i:s',strtotime($date)));
            $begin_morning = strtotime($businessDay[$numWeek]['begin_morning']);
            $end_morning = strtotime($businessDay[$numWeek]['end_morning']);
            $begin_afternoon = strtotime($businessDay[$numWeek]['begin_afternoon']);
            $end_afternoon = strtotime($businessDay[$numWeek]['end_afternoon']);
            if($hour >= $begin_morning && $hour <= $end_morning){
                $i = 1;
            }
            else if($hour >= $begin_afternoon && $hour <= $end_afternoon){
                $i = 1;
            }
            else{
                $date = date ("Y-m-d H:i:s", strtotime("+1 hour", strtotime($date)));
                $i = 0;
            }
        }
        return $date;
    }

    public function _isTeacher($user)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/intrauser/check/'.$user.'/1',false,$ctx);
        //echo $this->_serverApi.'/api/src/public/intrauser/check/'.$user.'/1';

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados da Intranet - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $a = $response['result'];
            //echo "<pre>"; print_r($a); "</pre>";
            foreach ($a as $item){
                if($item['CoTipoPessoa'] == 3){
                    $arrRet = $this->_checkTeacherCondition($user);
                }
                else{
                    $arrRet = array("status" => 'ok',
                        "msg" => '');
                }
            }
        }else{return false;}

        return $arrRet;
    }

    public function _getMsgError($idmodule,$errcode)
    {
        $where = "AND a.idmodule = $idmodule AND `code` = '$errcode'";
        $ret = $this->dbPedidoOperador->getMsgError($where);
        $msg = '['.strtoupper($ret->fields['code_fmt']).'] '.$this->getLanguageWord($ret->fields['smarty']);
        return $msg;
    }

    public function _checkTeacherCondition($user)
    {
        $ret = $this->_getTurmaPerson($user);
        if(sizeof($ret) == 0){
            $retGroup = $this->_getGrupoOperador("AND ghp.idperson = ".$_SESSION['SES_COD_USUARIO']);
            if($retGroup->RecordCount() > 0){
                $status = 'ok';
                $msg = '';
            }else{
                $status = 'Error';
                $msg = $this->_getMsgError($this->idmodule,'0001');
            }

        }else{
            $status = 'ok';
            $msg = '';
        }

        $arrRet = array("status" => $status,
            "msg" => $msg);

        return $arrRet;
    }

    /**
     * Returna o tipo de Grupo do usuário
     *
     * @param  int  $user   id do usuário
     * @return int      Returns o id do tipo de grupo
     *
     *  ID's
     *  1 - Coordenações do Ensino
     *  2 - Lab. Química
     *  3 - Compras
     *
     */
    public function _getUserGroupType($user)
    {
        $arrGroupCoord = explode(',',$_SESSION['scm']['SCM_COORDGROUPS']);
        $arrGroupLab = explode(',',$_SESSION['scm']['SCM_LABQUIMGROUPS']);

        $retUserGroup = $this->_getGrupoOperador("AND ghp.idperson = '".$user."'");
        $arrGroup = array();
        while(!$retUserGroup->EOF){
            if(!in_array($retUserGroup->fields['idgroup'],$arrGroup)){array_push($arrGroup,$retUserGroup->fields['idgroup']);}
            $retUserGroup->MoveNext();
        }

        if(count(array_intersect($arrGroup, $arrGroupCoord)) > 0){$idtype = 1;}
        elseif((count(array_intersect($arrGroup, $arrGroupLab))) > 0){$idtype = 2;}
        else{$idtype = 3;}


        return $idtype;
    }

    public function _isTeacherInGroup($user)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/intrauser/check/'.$user.'/1',false,$ctx);
        //echo $this->_serverApi.'/api/src/public/intrauser/check/'.$user.'/1';

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('Nao retornou dados da Intranet - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $a = $response['result'];
            //echo "<pre>"; print_r($a); "</pre>";
            foreach ($a as $item){
                if($item['CoTipoPessoa'] == 3){
                    return 'true';
                }
                else{
                    return 'false';
                }
            }
        }else{return false;}
    }

    public function _getTurmaReplica($user,$idturma)
    {
        $retTurma = $this->_getCourseSerieForm($idturma);
        if (!$retTurma){
            if ($this->log)
                $this->logIt('Can\'t get Turma data - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $ret = $this->_getAPIData("/api/src/public/turmabyserieprofessor/$user/{$retTurma['courseID']}/{$retTurma['serieID']}/{$retTurma['formID']}");

        if (!$ret){
            if ($this->log)
                $this->logIt('No data return - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        return $ret;
    }

    /*
     *
     */
    public function _getAPIData($route){
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.$route,false,$ctx);

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt('No data returned - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $arrRet = $response['result'];

        }else{$arrRet = array();}

        return $arrRet;
    }

    public function _getCourseSerieForm($idform){
        $idform = explode('|',$idform);

        $rsDePara = $this->dbTurma->getTurmaDePara("WHERE idturma = {$idform[2]}");
        if (!$rsDePara){
            if ($this->log)
                $this->logIt('Can\'t get Turma data - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        if($rsDePara->RecordCount() > 0){
            $arrRet['courseID'] = $rsDePara->fields['courselegacy'];
            $arrRet['serieID'] = $rsDePara->fields['serielegacy'];
            $arrRet['formID'] = $rsDePara->fields['formlegacy'];
        }else{
            $rsTurma = $this->dbTurma->getTurma("WHERE idturma = {$idform[2]}");
            if (!$rsTurma){
                if ($this->log)
                    $this->logIt('Can\'t get Turma data - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $arrRet['courseID'] = $idform[0];
            $arrRet['serieID'] = $idform[1];
            $arrRet['formID'] = $rsTurma->fields['numero'];

        }

        return $arrRet;
    }

    public function _getPersonsByDepartment($where = null, $order = null , $group = null , $limit = null){

        $rs = $this->dbPedidoCompra->getPersonsByDepartment($where, $order, $group, $limit);

        if(is_array($rs) && isset($rs['status'])){
            if($this->log)
                $this->logIt($rs['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
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

    public function _displayButtons($smarty,$permissions)
    {
        (isset($permissions[1]) && $permissions[1] == "Y") ? $smarty->assign('display_btn_add', '') : $smarty->assign('display_btn_add', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_edit', '') : $smarty->assign('display_btn_edit', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_enable', '') : $smarty->assign('display_btn_enable', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_disable', '') : $smarty->assign('display_btn_disable', 'hide');
        (isset($permissions[3]) && $permissions[3] == "Y") ? $smarty->assign('display_btn_delete', '') : $smarty->assign('display_btn_delete', 'hide');
        (isset($permissions[4]) && $permissions[4] == "Y") ? $smarty->assign('display_btn_export', '') : $smarty->assign('display_btn_export', 'hide');
        (isset($permissions[5]) && $permissions[5] == "Y") ? $smarty->assign('display_btn_email', '') : $smarty->assign('display_btn_email', 'hide');
        (isset($permissions[6]) && $permissions[6] == "Y") ? $smarty->assign('display_btn_sms', '') : $smarty->assign('display_btn_sms', 'hide');
    }

    public function _comboDestinoBaixa($where=null,$order=null,$limit=null,$group=null)
    {
        $rs = $this->dbBaixa->getDestinoBaixa($where,$order,$limit,$group);
        if(!$rs['success']){
            if($this->log)
            $this->logIt("{$rs['message']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['iddestinobaixa'];
            $values[]   = $rs['data']->fields['nome'];
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    function _ajaxQuantityConf()
    {
        $ret = $this->dbProduto->getProduto("WHERE idproduto = {$_POST['produtoID']}");
        if (!$ret) {
            if($this->log)
                $this->logIt('Can\'t get stock data - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $flgStep = (in_array($ret->fields['idunidade'],array(3,4,5))) ? true : false;            

        $aRet = array(
            "stock" => $ret->fields['estoque_atual'],
            "flgstep" => $flgStep
        );
        echo json_encode($aRet);
    }

    function isCli()
    {
        if ( defined('STDIN') )
        {
            return true;
        }

        if ( php_sapi_name() === 'cli' )
        {
            return true;
        }

        if ( array_key_exists('SHELL', $_ENV) ) {
            return true;
        }

        if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
        {
            return true;
        }

        if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
        {
            return true;
        }

        return false;
    }

}