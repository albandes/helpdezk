<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpDataMapping extends lgpCommon
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

        $this->modulename = 'LGPD' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpDataMapping');

        $this->loadModel('lgpdatamapping_model');
        $this->dbDataMapping = new lgpdatamapping_model();


    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $smarty->assign('token', $this->_makeToken());
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $smarty->display('lgp-data-mapping.tpl');
        
    }

    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $smarty = $this->retornaSmarty();

        $where = '';
        
        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='a.nome';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            switch($_POST['searchField']){
                case 'finalidade':
                    $searchField = 'm.nome';
                    break;
                case 'formato':
                    $searchField = 'o.nome';
                    break;
                case 'forma':
                    $searchField = 'n.nome';
                    break;
                case 'base':
                    $searchField = 'l.nome';
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }
            
            $where .= ($where != '' ? ' AND ' : 'WHERE ') . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
        }

        $count = $this->dbDataMapping->getDataMapping($where);
        if(!$count['success']){
            if($this->log)
                $this->logIt("Can't record data.\n".$count['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $totalRows = count($count['data']);

        if($totalRows > 0 && $rows > 0) {
            $total_pages = ceil($totalRows/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsDataMapping = $this->dbDataMapping->getDataMapping($where,$order,$limit);
        if(!$rsDataMapping['success']){
            if($this->log)
                $this->logIt("Can't record data.\n".$rsDataMapping['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        foreach($rsDataMapping['data'] as $k=>$v){
            $aColumns[] = array(
                'id'            => $v['iddado'],
                'tipotitular'   => $v['tipotitular'],
                'dado'          => strip_tags($v['nome']),
                'tipo'          => $v['tipo'],
                'finalidade'    => $v['finalidade'],
                'formato'       => $v['formato'],
                'formacoleta'   => $v['forma'],
                'baselegal'     => $v['base'],
                'compartilha'   => $v['compartilhado'] == 'S' ? $this->getLanguageWord("Yes") : $this->getLanguageWord("No")
            );

        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreate()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $smarty->assign('token', $this->_makeToken());
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->makeScreenDataMapping($smarty,'','add');

        $smarty->display('lgp-data-mapping-add.tpl');
    }

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $typePerson = $this->dbDataMapping->getLgpTypePerson("LGP_personhasaccess"); //get type person ID of person who access data mapped
        if(!$typePerson['success']){
            if($this->log)
                $this->logIt("Can't get lgpd person has access data.\n".$typePerson['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }
        
        $accID = count($typePerson['data']) > 0 ? ",{$typePerson['data'][0]['idtypeperson']}" : "";

        $iddado = $this->getParam('iddado');
        $rsDado = $this->dbDataMapping->getDataMappingEdit("WHERE a.iddado = {$iddado}",null,null,$accID);

        $this->makeScreenDataMapping($smarty,$rsDado['data'],'update');

        $smarty->assign('token', $this->_makeToken());
        $smarty->assign('iddado', $iddado);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lgp-data-mapping-upd.tpl');

    }

    public function formView()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();
        
        $typePerson = $this->dbDataMapping->getLgpTypePerson("LGP_personhasaccess"); //get type person ID of person who access data mapped
        if(!$typePerson['success']){
            if($this->log)
                $this->logIt("Can't get lgpd person has access data.\n".$typePerson['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }
        
        $accID = count($typePerson['data']) > 0 ? ",{$typePerson['data'][0]['idtypeperson']}" : "";
        $iddado = $this->getParam('iddado');
        $rsDado = $this->dbDataMapping->getDataMappingEdit("WHERE a.iddado = {$iddado}",null,null,$accID);

        $this->makeScreenDataMapping($smarty,$rsDado['data'],'echo');

        $smarty->assign('token', $this->_makeToken());
        $smarty->assign('iddado', $iddado);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lgp-data-mapping-view.tpl');

    }
    
    /**
     * makeScreenDataMapping
     * 
     * Formatação de campos dos formulários Novo, Editar, Visualizar
     *
     * @param  mixed $objSmarty Objeto do smarty template engine
     * @param  mixed $rs Record set do cadastro selecionado (Edição e Visualização)
     * @param  mixed $oper Ação do formulário (add: Novo / update: Editar )
     * @return void
     */
    function makeScreenDataMapping($objSmarty,$rs,$oper)
    {
        // --- Descrição ---
        if ($oper != 'add') {
            $objSmarty->assign('descricao',strip_tags($rs[0]['nome']));
        }

        // --- Compartilhado ---
        if ($oper == 'update') {
            $objSmarty->assign('ischecked',($rs[0]['compartilhado'] == 'S' ? 'checked' : ''));
        }else if($oper == 'echo'){
            $objSmarty->assign('ischecked',($rs[0]['compartilhado'] == 'S' ? $this->getLanguageWord('Yes') : $this->getLanguageWord('No')));
        }
        
        // --- Tipo titular ---
        $aTipoTitular = $this->_comboHolderType("WHERE `status` = 'A'","ORDER BY nome");
        
        if ($oper == 'update') {
            $idholdertype = $rs[0]['idtipotitular'];
        } elseif ($oper == 'add') {
            $idholdertype = '';
        }

        if ($oper == 'echo') {
            $objSmarty->assign('txtHolderType',$rs[0]['tipotitular']);
        } else {
            $objSmarty->assign('holdertypeids',  $aTipoTitular['ids']);
            $objSmarty->assign('holdertypevals', $aTipoTitular['values']);
            $objSmarty->assign('idholdertype', $idholdertype);
        }

        // --- Tipo dado ---
        $aTipoDado = $this->_comboType("WHERE `status` = 'A'","ORDER BY nome");
        
        if ($oper == 'update') {
            $idTipoDado = $rs[0]['idtipodado'];
        } elseif ($oper == 'add') {
            $idTipoDado = '';
        }

        if ($oper == 'echo') {
            $objSmarty->assign('txtType',$rs[0]['tipo']);
        } else {
            $objSmarty->assign('typeids',  $aTipoDado['ids']);
            $objSmarty->assign('typevals', $aTipoDado['values']);
            $objSmarty->assign('idtype', $idTipoDado);
        }

        // --- Finalidade ---
        $aFinalidade = $this->_comboPurpose("WHERE `status` = 'A'","ORDER BY nome");
        if ($oper == 'update') {
            $idPurpose = explode(',',$rs[0]['finalidadeids']);
        } elseif ($oper == 'add') {
            $idPurpose = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtPurpose',str_replace(',','<br>',$rs[0]['finalidade']));
        } else {
            $objSmarty->assign('purposegroupsids',  $aFinalidade['ids']);
            $objSmarty->assign('purposegroupsvals', $aFinalidade['values']);
            $objSmarty->assign('idpurposegroups', $idPurpose  );
        }

        // --- Formato Coleta ---
        $aFormato = $this->_comboFormat("WHERE `status` = 'A'","ORDER BY nome");
        if ($oper == 'update') {
            $idFormato = explode(',',$rs[0]['formatoids']);
        } elseif ($oper == 'add') {
            $idFormato = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtFormat',str_replace(',','<br>',$rs[0]['formato']));
        } else {
            $objSmarty->assign('formatgroupsids',  $aFormato['ids']);
            $objSmarty->assign('formatgroupsvals', $aFormato['values']);
            $objSmarty->assign('idformatgroups', $idFormato);
        }

        // --- Forma Coleta ---
        $aForma = $this->_comboCollectForm("WHERE `status` = 'A'","ORDER BY nome");
        if ($oper == 'update') {
            $idForma = explode(',',$rs[0]['formaids']);
        } elseif ($oper == 'add') {
            $idForma = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtCollectForm',str_replace(',','<br>',$rs[0]['forma']));
        } else {
            $objSmarty->assign('collectformatsids',  $aForma['ids']);
            $objSmarty->assign('collectformatsvals', $aForma['values']);
            $objSmarty->assign('idcollectformats', $idForma);
        }

        // --- Base Legal ---
        $aBase = $this->_comboLegalGround("WHERE `status` = 'A'","ORDER BY nome");
        if ($oper == 'update') {
            $idBase = explode(',',$rs[0]['baseids']);
        } elseif ($oper == 'add') {
            $idBase = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtLegalGround',str_replace(',','<br>',$rs[0]['base']));
        } else {
            $objSmarty->assign('legalgroundsids',  $aBase['ids']);
            $objSmarty->assign('legalgroundsvals', $aBase['values']);
            $objSmarty->assign('idlegalgrounds', $idBase);
        }

        // --- Armazenamento ---
        $aArmazena = $this->_comboStorage("WHERE `status` = 'A'","ORDER BY nome");
        if ($oper == 'update') {
            $idArmazena = explode(',',$rs[0]['armazenamentoids']);
        } elseif ($oper == 'add') {
            $idArmazena = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtStorage',str_replace(',','<br>',$rs[0]['armazenamento']));
        } else {
            $objSmarty->assign('storageids',  $aArmazena['ids']);
            $objSmarty->assign('storagevals', $aArmazena['values']);
            $objSmarty->assign('idstorage', $idArmazena);
        }

        // --- Quem acessa ---
        $typePerson = $this->dbDataMapping->getLgpTypePerson("LGP_personhasaccess");
        if(!$typePerson['success']){
            if($this->log)
                $this->logIt("Can't get lgpd person has access data.\n".$typePerson['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }
        $accID = count($typePerson['data']) > 0 ? ",{$typePerson['data'][0]['idtypeperson']}" : "";

        $aPerson = $this->_comboPerson("WHERE `status` = 'A'","ORDER BY `name`",null,null,$accID);
        if ($oper == 'update') {
            $idPerson = explode(',',$rs[0]['personaccids']);
        } elseif ($oper == 'add') {
            $idPerson = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtWhoAccesses',str_replace(',','<br>',$rs[0]['personacc']));
        } else {
            $objSmarty->assign('personaccessesopts',  $aPerson['opts']);
            //$objSmarty->assign('personaccessesids',  $aPerson['ids']);
            //$objSmarty->assign('personaccessesvals', $aPerson['values']);
            $objSmarty->assign('idpersonaccesses', $idPerson);
        }

        // --- Com quem é compartilhado ---
        $typeOperator = $this->dbDataMapping->getLgpTypePerson("LGP_operator");
        if(!$typeOperator['success']){
            if($this->log)
                $this->logIt("Can't get lgpd operator data.\n".$typeOperator['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        $where = count($typeOperator['data']) > 0 ? "WHERE idtypeperson = {$typeOperator['data'][0]['idtypeperson']} AND status = 'A'" : null;

        $aOperador = $this->_comboSharedWith($where,"ORDER BY `name`");
        if ($oper == 'update' || $oper == 'echo') {
            if($rs[0]['compartilhado'] == 'S'){
                $objSmarty->assign('flgdisplay', "");
                $idOperador = explode(',',$rs[0]['operadorids']);
            }else{
                $objSmarty->assign('flgdisplay', "hide");
                $idOperador = array();
            }            
        } elseif ($oper == 'add') {
            $idOperador = array();
        }
        
        if ($oper == 'echo') {
            $objSmarty->assign('txtWith',str_replace(',','<br>',$rs[0]['operador']));
        } else {
            $objSmarty->assign('sharedwhithids',  $aOperador['ids']);
            $objSmarty->assign('sharedwhithvals', $aOperador['values']);
            $objSmarty->assign('idsharedwhith', $idOperador);
        }

    }
    
    /**
     * createDataMap
     *
     * @return void
     */
    function createDataMap()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $holderTypeID = $_POST['cmbHolderType'];
        $dataName = strip_tags($_POST['description']);
        $typeID = $_POST['cmbType'];
        $shared = isset($_POST['datashared']) ? $_POST['datashared'] : 'N';
        $params = array(
            "purpose" => $_POST['purposegroups'],
            "format" => $_POST['formatgroups'],
            "collect" => $_POST['collectformats'],
            "legalground" => $_POST['legalgrounds'],
            "storage" => $_POST['storage'],
            "personaccesses" => $_POST['personaccesses'],
            "shared" => $shared,
            "with" => isset($_POST['sharedwhith']) ? $_POST['sharedwhith'] : null
        );
        
        $ins = $this->dbDataMapping->insertDataMap($holderTypeID,$dataName,$typeID,$shared);
        if(!$ins['success']){
            if($this->log)
                $this->logIt("Can't record data.\n".$ins['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $dataID = $ins['id'];
        //echo "",print_r($_POST,true),"\n";
        $ret = $this->processDataComplement($params,$dataID);
        if(!$ret){
            return false;
        }

        $aRet = array(
            "success"   => true,
            "iddado"    => $dataID
        );

        echo json_encode($aRet);

    }

    function updateDataMap()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $dataMapID = $_POST['iddado'];
        $holderTypeID = $_POST['cmbHolderType'];
        $dataName = strip_tags($_POST['description']);
        $typeID = $_POST['cmbType'];
        $shared = isset($_POST['datashared']) ? $_POST['datashared'] : 'N';
        $params = array(
            "purpose" => $_POST['purposegroups'],
            "format" => $_POST['formatgroups'],
            "collect" => $_POST['collectformats'],
            "legalground" => $_POST['legalgrounds'],
            "storage" => $_POST['storage'],
            "personaccesses" => $_POST['personaccesses'],
            "shared" => $shared,
            "with" => isset($_POST['sharedwhith']) ? $_POST['sharedwhith'] : null
        );        
        
        $ins = $this->dbDataMapping->updateDataMap($dataMapID,$holderTypeID,$dataName,$typeID,$shared);
        if(!$ins['success']){
            if($this->log)
                $this->logIt("Can't update data.\n".$ins['message']."\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $ret = $this->processDataComplement($params,$dataMapID,'upd');
        if(!$ret){
            return false;
        }
        //echo "",print_r($_POST,true),"\n";
        
        $aRet = array(
            "success"   => true,
            "iddado"    => $dataID
        );

        echo json_encode($aRet);
    }

    function processDataComplement($params,$dataID,$op='add')
    {
        // --- Deleta os vínculos do dado mapeado com a finalidade(s), formato(s) coleta, forma(s) de coleta, etc ---
        if ($op == 'upd') {
            $ret = $this->deleteDataComplement($dataID);
            if(!$ret){
                return false;
            }
        }

        // --- Inserir os dados complementares ---
        foreach($params['purpose'] as $k=>$v){
            $insPurpose = $this->dbDataMapping->insertDataPurpose($dataID,$v);
            if(!$insPurpose['success']){
                if($this->log)
                    $this->logIt("Can't record data purpose. ".$insPurpose['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach($params['format'] as $k=>$v){
            $insFormat = $this->dbDataMapping->insertDataFormat($dataID,$v);
            if(!$insFormat['success']){
                if($this->log)
                    $this->logIt("Can't record data collect format.\n{$insFormat['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach($params['collect'] as $k=>$v){
            $insCollect = $this->dbDataMapping->insertDataCollectForm($dataID,$v);
            if(!$insCollect['success']){
                if($this->log)
                    $this->logIt("Can't record data collect form.\n{$insCollect['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach($params['legalground'] as $k=>$v){
            $insLegalGround = $this->dbDataMapping->insertDataLegalGround($dataID,$v);
            if(!$insLegalGround['success']){
                if($this->log)
                    $this->logIt("Can't record data legal ground.\n{$insLegalGround['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach($params['storage'] as $k=>$v){
            $insStorage = $this->dbDataMapping->insertDataStorage($dataID,$v);
            if(!$insStorage['success']){
                if($this->log)
                    $this->logIt("Can't record data storage.\n{$insStorage['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        foreach($params['personaccesses'] as $k=>$v){
            $aPerson = explode('|',$v);
            $insPerson = $this->dbDataMapping->insertDataPerson($dataID,$aPerson[0],$aPerson[1]);
            if(!$insPerson['success']){
                if($this->log)
                    $this->logIt("Can't record data person accesses. {$insPerson['message']} Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        if($params['shared'] == 'S'){
            foreach($params['with'] as $k=>$v){
                $insWith = $this->dbDataMapping->insertSharedWith($dataID,$v);
                if(!$insWith['success']){
                    if($this->log)
                        $this->logIt("Can't record with whom share data.\n{$insWith['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }
        }

        return true;

    }

    function deleteDataComplement($dataID)
    {
        $delPurpose = $this->dbDataMapping->deleteDataBind($dataID,"lgp_tbdado_has_finalidade");
        if(!$delPurpose['success']){
            if($this->log)
                $this->logIt("Can't delete data purpose. ".$delPurpose['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delFormat = $this->dbDataMapping->deleteDataBind($dataID,'lgp_tbdado_has_formatocoleta');
        if(!$delFormat['success']){
            if($this->log)
                $this->logIt("Can't delete data format. ".$delFormat['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delCollect = $this->dbDataMapping->deleteDataBind($dataID,'lgp_tbdado_has_formacoleta');
        if(!$delCollect['success']){
            if($this->log)
                $this->logIt("Can't delete data collection form. ".$delCollect['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delLegalGround = $this->dbDataMapping->deleteDataBind($dataID,'lgp_tbdado_has_baselegal');
        if(!$delLegalGround['success']){
            if($this->log)
                $this->logIt("Can't delete data legal ground. ".$delLegalGround['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delStorage = $this->dbDataMapping->deleteDataBind($dataID,'lgp_tbdado_has_armazenamento');
        if(!$delStorage['success']){
            if($this->log)
                $this->logIt("Can't delete data storage. ".$delStorage['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delPerson = $this->dbDataMapping->deleteDataBind($dataID,'lgp_tbdado_has_person');
        if(!$delPerson['success']){
            if($this->log)
                $this->logIt("Can't delete data person accesses. ".$delPerson['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $delWith = $this->dbDataMapping->deleteDataBind($dataID,'lgp_tbdado_has_operador');
        if(!$delWith['success']){
            if($this->log)
                $this->logIt("Can't delete data shared with whom. ".$delWith['message']." Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        return true;
    }

}