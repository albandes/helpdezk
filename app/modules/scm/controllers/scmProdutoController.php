<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmProduto extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmProduto');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

        $this->loadModel('produto_model');
        $this->dbProduto = new produto_model();

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
        if($permissions[0] == "Y"){
            $smarty->display('scm-produto-grid.tpl');
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
            if ( $_POST['searchField'] == 'nome') $searchField = 'scm_tbproduto.nome';
            if ( $_POST['searchField'] == 'descricao') $searchField = 'descricao';
            if ( $_POST['searchField'] == 'unidade') $searchField = 'unidade';
            if ( $_POST['searchField'] == 'estoque_inicial') $searchField = 'estoque_inicial';
            if ( $_POST['searchField'] == 'estoque_minimo') $searchField = 'estoque_minimo';
            if ( $_POST['searchField'] == 'codigo_barras') $searchField = 'codigo_barras';
            if ( $_POST['searchField'] == 'estoque_atual') $searchField = 'estoque_atual';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumProdutos();

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

        $rsProduto = $this->_getProduto($where,$order,null,$limit);

        while (!$rsProduto->EOF) {

            $status_fmt = ($rsProduto->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'                => $rsProduto->fields['idproduto'],
                'nome'              => $rsProduto->fields['nome'],
                'descricao'         => $rsProduto->fields['descricao'],
                'unidade'           => $rsProduto->fields['unidade'],
                'estoque_inicial'   => $rsProduto->fields['estoque_inicial'],
                'estoque_atual'     => $rsProduto->fields['estoque_atual'],
                'estoque_minimo'    => $rsProduto->fields['estoque_minimo'],
                'codigo_barras'     => $rsProduto->fields['codigo_barras'],
                'status_fmt'        => $status_fmt,
                'status'            => $rsProduto->fields['status']

            );
            $rsProduto->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateProduto()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenProduto($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-produto-create.tpl');
    }

    public function formUpdateProduto()
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $idProduto = $this->getParam('idproduto');
        $rsProduto = $this->_getProduto("where idproduto = $idProduto") ;

        $rsImagem = $this->_getImagemProduto("where idproduto = $idProduto") ;
        $smarty->assign('rsImagem',  $rsImagem);
        $smarty->assign('caminho', $this->getHelpdezkUrl(). $this->imgDir);
        
        if($this->saveMode == "aws-s3"){
            $smarty->assign('urlPath', $this->imgDir);
        }else{
            $smarty->assign('urlPath', $this->getHelpdezkUrl()."/app/uploads/scm/produtos/");
        }

        $this->makeScreenProduto($smarty,$rsProduto,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idproduto', $idProduto);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-produto-update.tpl');

    }

    public function echoProduto()
    {
        $smarty = $this->retornaSmarty();

        $idProduto = $this->getParam('idproduto');
        $rsProduto = $this->_getProduto("where idproduto = $idProduto") ;

        $rsUnidade = $this->_getUnidade("where idunidade = " . $rsProduto->fields['idunidade']) ;
        $rsImagem = $this->makeProdutoGallery($idProduto) ;
        $smarty->assign('galleryProduto',  $rsImagem);

        $this->makeScreenProduto($smarty,$rsProduto,'echo');
        $smarty->assign('unidade',  $rsUnidade->fields['nome']);
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-produto-echo.tpl');

    }

    function makeScreenProduto($objSmarty,$rs,$oper)
    {

        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('plh_nome','Informe o nome do produto.');
            else
                $objSmarty->assign('nome',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o nome do produto.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nome',$rs->fields['nome']);
        }

        // --- Descrição ---
        if ($oper == 'update') {
            if (empty($rs->fields['descricao']))
                $objSmarty->assign('plh_descricao','Informe a descrição do produto.');
            else
                $objSmarty->assign('descricao',$rs->fields['descricao']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_descricao','Informe a descrição do produto.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('descricao',$rs->fields['descricao']);
        }

        // --- Unidade ---
        if ($oper == 'update') {
            $idUnidadeEnable = $rs->fields['idunidade'];
        } elseif ($oper == 'create') {
            $idUnidadeEnable = 1;
        }
        if ($oper == 'echo') {
            $objSmarty->assign('idunidade',$rs->fields['idunidade']);
        } else {
            $arrUnidade = $this->_comboUnidade();
            $objSmarty->assign('unidadeids',  $arrUnidade['ids']);
            $objSmarty->assign('unidadevals', $arrUnidade['values']);
            $objSmarty->assign('idunidade', $idUnidadeEnable );
        }

        // --- Estoque Inicial ---
        if ($oper == 'update') {
            if (empty($rs->fields['estoque_inicial']))
                $objSmarty->assign('plh_estoque_inicial','Informe o estoque inicial do produto.');
            else
                $objSmarty->assign('estoque_inicial',$rs->fields['estoque_inicial']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_estoque_inicial','Informe o estoque inicial do produto..');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('estoque_inicial',$rs->fields['estoque_inicial']);
        }

        // --- Estoque Inicial ---
        if ($oper == 'update') {
            if (empty($rs->fields['estoque_atual']))
                $objSmarty->assign('plh_estoque_atual','Informe o estoque inicial do produto.');
            else
                $objSmarty->assign('estoque_atual',$rs->fields['estoque_atual']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_estoque_atual','Informe o estoque inicial do produto..');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('estoque_atual',$rs->fields['estoque_atual']);
        }

        // --- Estoque Mínimo ---
        if ($oper == 'update') {
            if (empty($rs->fields['estoque_minimo']))
                $objSmarty->assign('plh_estoque_minimo','Informe o estoque mínimo do produto.');
            else
                $objSmarty->assign('estoque_minimo',$rs->fields['estoque_minimo']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_estoque_minimo','Informe o estoque mínimo do produto.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('estoque_minimo',$rs->fields['estoque_minimo']);
        }

        // --- Código de barras ---
        if ($oper == 'update') {
            if (empty($rs->fields['codigo_barras']))
                $objSmarty->assign('plh_codigo_barras','Informe o código de barras do produto.');
            else
                $objSmarty->assign('codigo_barras',$rs->fields['codigo_barras']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_codigo_barras','Informe o código de barras do produto.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('codigo_barras',$rs->fields['codigo_barras']);
        }

    }

    function salvaFoto()
    {
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");

            $saveMode = " ";
            if($this->_s3bucketStorage) {
                $saveMode = "aws-s3";
            } else {
                $saveMode = 'disk';
            }

            if($saveMode == 'disk') {

                if($this->_externalStorage) {
                    $targetPath = $this->_externalStoragePath . '/scm/produtos/' ;
                } else {
                    $targetPath = $this->helpdezkPath . $this->imgDir;
                }
    
                if(!is_dir($targetPath)) {
                    $this->logIt('Directory: '. $targetPath.' does not exists, I will try to create it. - program: '.$this->program ,7,'general',__LINE__);
                    if (!mkdir ($targetPath, 0777 )) {
                        $this->logIt('I could not create the directory: '.$targetPath.' - program: '.$this->program ,3,'general',__LINE__);
                        echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_create')}: {$targetPath}"));
                        exit;
                    }
                }
    
                if (!is_writable($targetPath)) {
                    $this->logIt('Directory: '. $targetPath.' Is not writable, I will try to make it writable - program: '.$this->program ,7,'general',__LINE__);
                    if (!chmod($targetPath,0777)){
                        $this->logIt('Directory: '.$targetPath.' Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                        echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_writable')}: {$targetPath}"));
                        exit;
                    }
                }
    
                $targetFile =  $targetPath.$fileName;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logIt('Save product photo - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logIt('Can\'t save product photo #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }
                    
            } else if ($saveMode == "aws-s3") {
                
                $aws = $this->getAwsS3Client();

                $arrayRet = $aws->copyToBucket($tempFile,'scm/produtos/'.$fileName);
                
                if($arrayRet['success']) {
                    if($this->log)
                        $this->logIt("Save temp attachment file " . $fileName . ' - program: '.$this->program ,7,'general',__LINE__);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    if($this->log)
                        $this->logIt('I could not save the temp file: '.$fileName.' in S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }             

            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
        }
        exit;
    }

    function createProduto()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files

        $this->loadModel('produto_model');
        $dbProduto = new produto_model();
        

        $dbProduto->BeginTrans();

        $ret = $dbProduto->insertProduto(addslashes(trim($_POST['nome'])) ,addslashes(trim($_POST['descricao'])),$_POST['idunidade'],$_POST['estoque_inicial'],$_POST['estoque_inicial'],$_POST['estoque_minimo'],$_POST['codigo_barras']);

        if (!$ret) {
            $dbProduto->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Produto  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }else{
            $idProduto = $ret ;
            $logStock = $dbProduto->insertProductLog($idProduto,0,$_POST['estoque_inicial'],$_SESSION['SES_COD_USUARIO'],'add');
            if(!$logStock['success']){
                if ($this->log)
                    $this->logIt("Can't insert log. Msg: {$logStock['message']}.\n User: " . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            }

            // link attachments to the request
            if($aSize > 0){
                $retAttachs = $this->linkProductAttachments($idProduto,$aAttachs,$dbProduto);
                
                if(!$retAttachs['success']){
                    if($this->log)
                        $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                    $dbProduto->RollbackTrans();
                    return false;
                }
            }
        }

        $aRet = array(
            "idproduto" => $idProduto,
            "nome" => $_POST['nome']
        );

        $dbProduto->CommitTrans();

        echo json_encode($aRet);

    }

    function updateProduto()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idProduto = $_POST['idproduto'];
        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files


        $this->loadModel('produto_model');
        $dbProduto = new produto_model();

        $dbProduto->BeginTrans();

        $retStock = $dbProduto->getProduto("WHERE idproduto = $idProduto");
        if (!$retStock) {
            if($this->log)
                $this->logIt('Get Produto data - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $stockTMP = !$retStock->fields['estoque_atual'] ? 0 : $retStock->fields['estoque_atual'];
        $iniStock = (isset($_POST['estoque_inicial'])) ? $_POST['estoque_inicial'] : NULL;

        /*if(!$retStock->fields['estoque_atual'] || $retStock->fields['estoque_atual'] <= 0){$ret = $dbProduto->updateProduto($idProduto,addslashes(trim($_POST['nome'])),addslashes(trim($_POST['descricao'])),$_POST['idunidade'],$_POST['estoque_inicial'],$_POST['estoque_inicial'],$_POST['estoque_minimo'],$_POST['codigo_barras']);}
        else{$ret = $dbProduto->updateProduto($idProduto,addslashes(trim($_POST['nome'])) ,addslashes(trim($_POST['descricao'])),$_POST['idunidade'],$_POST['estoque_inicial'],'',$_POST['estoque_minimo'],$_POST['codigo_barras']);}*/

        $ret = $dbProduto->updateProduto($idProduto,addslashes(trim($_POST['nome'])),addslashes(trim($_POST['descricao'])),$_POST['idunidade'],$iniStock,$iniStock,$_POST['estoque_minimo'],$_POST['codigo_barras']);

        if (!$ret) {
            $dbProduto->RollbackTrans();
            if($this->log)
                $this->logIt('Update Produto - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }else{
            $logIniStock = !$iniStock ? 0 : $iniStock;
            $logStock = $dbProduto->insertProductLog($idProduto,$stockTMP,$logIniStock,$_SESSION['SES_COD_USUARIO'],'upd');

            if(!$logStock['success']){
                if ($this->log)
                    $this->logIt("Can't insert log. Msg: {$logStock['message']}.\n User: " . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            }

        }

        // link attachments to the request
        if($aSize > 0){
            $retAttachs = $this->linkProductAttachments($idProduto,$aAttachs,$dbProduto);
            
            if(!$retAttachs['success']){
                if($this->log)
                    $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                $dbProduto->RollbackTrans();
                return false;
            }
        }

        $aRet = array(
            "idproduto" => $idProduto,
            "status"   => 'OK'
        );

        $dbProduto->CommitTrans();

        echo json_encode($aRet);


    }

    function statusProduto()
    {
        $idProduto = $this->getParam('idproduto');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('produto_model');
        $dbProduto = new produto_model();

        $ret = $dbProduto->changeStatus($idProduto,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Produto Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idproduto" => $idProduto,
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    function createUnidade()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->loadModel('unidade_model');
        $dbUnidade = new unidade_model();

        $dbUnidade->BeginTrans();

        $ret = $dbUnidade->insertUnidade($_POST['nome']);

        if (!$ret) {
            $dbUnidade->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Unidade  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idUnidade = $ret ;

        $aRet = array(
            "idunidade" => $idUnidade,
            "nome" => $_POST['nome']
        );

        $dbUnidade->CommitTrans();

        echo json_encode($aRet);
    }

    function ajaxUnidade()
    {
        echo $this->comboUnidadeHtml();
    }

    public function comboUnidadeHtml()
    {
        $arrType = $this->_comboUnidade();
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

    function buscaUnidade()
    {

        $nome = $_POST['modal_unidade_nome'];

        $this->loadModel('unidade_model');
        $dbUnidade = new unidade_model();
        $ret = $dbUnidade->getUnidade("where nome = '".$nome."'");

        if (!$ret) {
            if($this->log)
                $this->logIt('Nome da unidade - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idunidade']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }

    function buscaImagem()
    {
        
        $idProduto = $_POST['idproduto'];
        $this->loadModel('produto_model');
        $dbUnidade = new produto_model();
        $ret = $dbUnidade->getImagemProduto(" where idproduto = ".$idProduto);

        if (!$ret) {
            if($this->log)
                $this->logIt('Nome da imagem - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $resultimagens = [];
        $targetPath = $this->imgDir ;
        foreach ($ret as $key => $value){
            if($this->saveMode == "aws-s3"){
                $size = strlen(file_get_contents($targetPath.$value['nome']));
            }else{
                $size = filesize($targetPath.$value['nome']);
            }            
            
            $resultimagens[] = array(
                'idimagem'      => $value['idimagem'],
                'idproduto'     => $value['idproduto'], 
                'nome'          => $value['nome'],
                'size'          => $size
            );
        }
        echo json_encode($resultimagens);
    }

    function removeImage()
    {
        $idimage = $_POST['idimage'];
        $filename = $_POST['filename'];

        $this->loadModel('produto_model');
        $dbUnidade = new produto_model();
        $ret = $dbUnidade->deleteImagem($idimage);

        if (!$ret) {
            if($this->log)
                $this->logIt('Remove Image - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($this->saveMode == 'disk') {
            unlink($this->imgDir.$filename);
            $msg = 'OK';
        }else if ($this->saveMode == 'aws-s3'){           
            $aws = $this->getAwsS3Client();
            $arrayRet = $aws->removeFile("scm/produtos/{$filename}");
            if($arrayRet['success']) {
                $msg = 'OK';
            } else {
                if($this->log)
                    $this->logIt('I could not remove the product image file: '.$filename.' from S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                $msg = 'error';
            }
        }

        $aRet = array(
            "status" => $msg,
        );
        echo json_encode($aRet);
    }

    function checkProduto()
    {
        $name_product = addslashes($_POST['nome']);
        $idunidade = $_POST['idunidade'];
        $barcode = $_POST['codigo_barras'];
        $id = $_POST['idproduto'];

        $where = "WHERE scm_tbproduto.nome LIKE '%".$name_product."%' AND scm_tbproduto.idunidade = ".$idunidade." AND codigo_barras = '".$barcode."'";
        if(isset($id)) {$where .= " AND idproduto != $id";}

        $ret = $this->_getProduto($where);

        if($ret->RecordCount() > 0){$status = 'NO';}
        else{$status = 'OK';}

        $aRet = array(
            "status" => $status
        );

        echo json_encode($aRet);
    }

    public function makeProdutoGallery($idproduto)
    {
        $pics = $this->_getImagemProduto("WHERE idproduto = ". $idproduto);

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
            $content.= "<div class='item text-center $flagAtive'> 
                            <div>
                                <img src='".$picsrc."' style='max-width:200px; max-heigth:200px;' alt='item".$i."'>
                            </div>                        
                        <!--<div class='carousel-caption'>
                            <h3>Heading 3</h3>
                            <p>Slide 0  description.</p>
                        </div>-->
                    </div>";
            $i++;
            $pics->MoveNext();
        }

        return $content;
    }

    public function linkProductAttachments($productID,$aAttachs,$db)
    {
        $saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';
        
        if($saveMode == 'disk') {
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/scm/produtos/' ;
            } else {
                $targetPath = $this->helpdezkPath . $this->imgDir;
            }
        }

        foreach($aAttachs as $key=>$fileName){
            $ret = $db->insereImagemProduto($productID,$fileName);

            if (!$ret) {
                if($this->log)
                    $this->logIt('Can\'t insert product image.  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't link file {$fileName} to product {$productID}");
            }

            $extension = strrchr($fileName, ".");
            $newFile = $ret.$extension;

            if($saveMode == 'disk') {
                $targetOld = $targetPath.$fileName;
                $targetNew =  $targetPath.$ret.$extension;
                if(!rename($targetOld,$targetNew)){
                    $delAtt = $db->deleteImagem($ret);
                    if (!$delAtt) {
                        if($this->log)
                            $this->logIt('Can\'t delete product image into DB - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    }
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to product {$productID}");
                }
                
            } else if ($saveMode == 'aws-s3') {
                $aws = $this->getAwsS3Client();
                $arrayRet = $aws->renameFile("scm/produtos/{$fileName}","scm/produtos/{$newFile}");
                if($arrayRet['success']) {
                    if($this->log)
                        $this->logIt("Rename product image file {$fileName} to {$newFile} - program: {$this->program} ",7,'general',__LINE__);
                } else {
                    if($this->log)
                        $this->logIt('I could not save the product image file: '.$fileName.' in S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                    return json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }
            
            }

            $ret1 = $db->updateImagemProduto($ret,$newFile);
            if (!$ret1) {
                if($this->log)
                    $this->logIt('Can\'t update file name  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't update link file {$fileName} to product {$productID}");
            }

        }

        return array("success"=>true,"message"=>"");

    }

}