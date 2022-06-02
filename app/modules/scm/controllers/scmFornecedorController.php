<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmFornecedor extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmFornecedor');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

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
            $smarty->display('scm-fornecedor-grid.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

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
            if ( $_POST['searchField'] == 'idperson') $searchField = 'a.idperson';
            if ( $_POST['searchField'] == 'name') $searchField = 'a.name';
            if ( $_POST['searchField'] == 'fantasy_name') $searchField = 'a.fantasy_name';

            if (empty($where))
                $oper = ' AND ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumGridFornecedores($where);

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

        $rsFornecedor = $this->_getGridFornecedor($where,$order,null,$limit);

        while (!$rsFornecedor->EOF) {
            $status_fmt = ($rsFornecedor->fields['status'] == 'A') ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id' => $rsFornecedor->fields['idperson'],
                'tipo' => $this->getLanguageWord($rsFornecedor->fields['tipo']) ,
                'name' => $rsFornecedor->fields['name'],
                'fantasy_name' => $rsFornecedor->fields['fantasy_name'],
                'phone_number' => $rsFornecedor->fields['phone_number'],
                'email' => $rsFornecedor->fields['email'],
                'status_fmt' => $status_fmt,
                'status' => $rsFornecedor->fields['status']

            );
            $rsFornecedor->MoveNext();

        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateFornecedor()
    {

        $smarty = $this->retornaSmarty();

        $this->makeScreenFornecedor($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-fornecedor-create.tpl');
    }

    public function formUpdateFornecedor()
    {

        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        $rsPerson = $this->_getFornecedorUpdateEcho("tbperson.idperson = $idPerson") ;

        $this->makeScreenFornecedor($smarty,$rsPerson,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idperson', $idPerson);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-fornecedor-update.tpl');

    }

    public function echoFornecedor()
    {

        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        $rsPerson = $this->_getFornecedorUpdateEcho("tbperson.idperson = $idPerson") ;

        $this->makeScreenFornecedor($smarty,$rsPerson,'echo');
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-fornecedor-echo.tpl');
    }

    function makeScreenFornecedor($objSmarty,$rs,$oper)
    {

        if(!empty($rs->fields['ein_cnpj'])){
            $objSmarty->assign('tipojuridico','checked');
        }else{
            $objSmarty->assign('tipojuridico','');
        }
        if(!empty($rs->fields['ssn_cpf'])){
            $objSmarty->assign('tipofisico','checked');
        }else{
            $objSmarty->assign('tipofisico','');
        }


        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_nome','Informe o Nome do fornecedor.');
            else
                $objSmarty->assign('nomefisico',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o Nome do fornecedor.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nomefisico',$rs->fields['name']);
        }

        // --- Razão Social ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_razaosocial','Informe a razão social do fornecedor.');
            else
                $objSmarty->assign('razaosocial',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_razaosocial','Informe o nome do fornecedor.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('razaosocial',$rs->fields['name']);
        }

        // --- Nome Fantasia ---
        if ($oper == 'update') {
            if (empty($rs->fields['nomefantasia']))
                $objSmarty->assign('plh_nomefantasia','Informe o Nome de Fantasia do fornecedor.');
            else
                $objSmarty->assign('nomefantasia',$rs->fields['nomefantasia']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nomefantasia','Informe o Nome de Fantasia do fornecedor.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nomefantasia',$rs->fields['nomefantasia']);
        }

        // --- CNPJ ---
        if ($oper == 'update') {
            if (empty($rs->fields['ein_cnpj'])){
                $objSmarty->assign('plh_ein_cnpj','Informe o CNPJ.');
            }else
                $objSmarty->assign('ein_cnpj',$rs->fields['ein_cnpj']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_ein_cnpj','Informe o CNPJ.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('ein_cnpj',$rs->fields['ein_cnpj']);
        }

        // --- Inscrição Estadual ---
        if ($oper == 'update') {
            if (empty($rs->fields['iestadual']))
                $objSmarty->assign('plh_iestadual','Inscrição estadual.');
            else
                $objSmarty->assign('iestadual',$rs->fields['iestadual']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_iestadual','Inscrição estadual.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('iestadual',$rs->fields['iestadual']);
        }

        // --- RG ---
        if ($oper == 'update') {
            if (empty($rs->fields['rg']))
                $objSmarty->assign('plh_rg','Informe o rg.');
            else
                $objSmarty->assign('rg',$rs->fields['rg']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_rg','Informe o rg.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('rg',$rs->fields['rg']);
        }


        // --- CPF ---
        if ($oper == 'update') {
            if (empty($rs->fields['ssn_cpf']))
                $objSmarty->assign('plh_cpf','Informe o cpf.');
            else {
                $objSmarty->assign('cpf', $rs->fields['ssn_cpf']);
           }
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_cpf','Informe o cpf.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('cpf',$rs->fields['ssn_cpf']);
        }


        // --- Email físico ---
        if ($oper == 'update') {
            if (empty($rs->fields['email']))
                $objSmarty->assign('plh_email','Informe o e-mail.');
            else
                $objSmarty->assign('emailfisica',$rs->fields['email']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_email','Informe o e-mail.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('emailfisica',$rs->fields['email']);
        }

        // --- Email ---
        if ($oper == 'update') {
            if (empty($rs->fields['email']))
                $objSmarty->assign('plh_email','Informe o e-mail.');
            else
                $objSmarty->assign('emailjuridica',$rs->fields['email']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_email','Informe o e-mail.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('emailjuridica',$rs->fields['email']);
        }

        //         --- Telefone ---
        if ($oper == 'update') {
            if (empty($rs->fields['phone_number']))
                $objSmarty->assign('plh_telefone','Informe o telefone.');
            else
                $objSmarty->assign('phone_number',$rs->fields['phone_number']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_telefone','Informe o telefone.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('phone_number',$rs->fields['phone_number']);
        }

        // --- Celular ---
        if ($oper == 'update') {
            if (empty($rs->fields['cel_phone']))
                $objSmarty->assign('plh_celular','Informe o celular.');
            else
                $objSmarty->assign('cel_phone',$rs->fields['cel_phone']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_celular','Informe o celular.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('cel_phone',$rs->fields['cel_phone']);
        }

        /* -- Endereco -- */

        // --- Country ---
        if ($oper == 'update') {
            $idCountryEnable = $rs->fields['idcountry'];
        } elseif ($oper == 'create') {
            $idCountryEnable = $this->getIdCountryDefault();
        }
        if ($oper == 'echo') {
            $objSmarty->assign('pais',$rs->fields['printablename']);
        } else {
            $arrCountry = $this->comboCountries();
            $objSmarty->assign('countryids',  $arrCountry['ids']);
            $objSmarty->assign('countryvals', $arrCountry['values']);
            $objSmarty->assign('idcountry', $idCountryEnable  );
        }
        // --- State ---
        if ($oper == 'update') {
            $idStateEnable = $rs->fields['idstate'];
        } elseif ($oper == 'create') {
            $idStateEnable = $this->getIdStateDefault();
        }
        if ($oper == 'echo') {
            $objSmarty->assign('estado',$rs->fields['estado']);
        } else {
            $arrCountry = $this->comboStates($idCountryEnable);
            $objSmarty->assign('stateids',  $arrCountry['ids']);
            $objSmarty->assign('statevals', $arrCountry['values']);
            $objSmarty->assign('idstate',   $idStateEnable);
        }
        // --- City ---
        if ($oper == 'update') {
            $idCityEnable = $rs->fields['idcity'];
        } elseif ($oper == 'create') {
            $idCityEnable = $this->getIdCityDefault($idStateEnable);
        }
        if ($oper == 'echo') {
            $objSmarty->assign('cidade', utf8_encode($rs->fields['cidade']));
        } else {
            $arrCity = $this->comboCity($idStateEnable);
            $objSmarty->assign('cityids',  $arrCity['ids']);
            $objSmarty->assign('cityvals', $arrCity['values']);
            $objSmarty->assign('idcity',   $idCityEnable);
        }

        // --- Neighborhood ---
        if ($oper == 'update'){
            $idNeighborhoodEnable = $rs->fields['idneighborhood'];
        } elseif ($oper == 'create') {
            $arrNeighborhood = $this->getIdNeighborhoodDefault($idCityEnable);
        }
        if ($oper == 'echo') {
            $objSmarty->assign('bairro', $rs->fields['bairro']);
        } else {
            $arrNeighborhood = $this->comboNeighborhood($idCityEnable);
            $objSmarty->assign('neighborhoodids',  $arrNeighborhood['ids']);
            $objSmarty->assign('neighborhoodvals', $arrNeighborhood['values']);
            $objSmarty->assign('idneighborhood',   $idNeighborhoodEnable);
        }
        // --- Cep ---
        if ($oper == 'update' or $oper == 'create' ) {
            if (empty($rs->fields['zipcode']))
                $objSmarty->assign('plh_cep', 'Informe o cep.');
            else
                $objSmarty->assign('cep', $rs->fields['zipcode']);
        } elseif ($oper == 'echo'){
            $objSmarty->assign('cep', $rs->fields['zipcode']);
        }
        // --- Type Street ---
        if ($oper == 'update') {
            $idTypeStreetEnable = $rs->fields['idtypestreet'];
        } elseif ($oper == 'create') {
            $idTypeStreetEnable = '';
        }
        if ($oper == 'echo') {
            $objSmarty->assign('tipologradouro', $rs->fields['tipologradouro']);
        } else {
            $arrTypestreet = $this->comboTypeStreet();
            $objSmarty->assign('typestreetids',  $arrTypestreet['ids']);
            $objSmarty->assign('typestreetvals', $arrTypestreet['values']);
            $objSmarty->assign('idtypestreet', $idTypeStreetEnable  );
        }
        // --- Logradouro ---
        if ($oper == 'update') {
            if (empty($rs->fields['logradouro_nome']))
                $objSmarty->assign('plh_logradouro','Informe o logradouro.');
            else
                $objSmarty->assign('logradouro',$rs->fields['logradouro_nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_logradouro','Informe o logradouro.');
        }  elseif ($oper == 'echo'){
            $objSmarty->assign('logradouro',$rs->fields['logradouro_nome']);
        }

        // --- Number ---
        if ($oper == 'update') {
            if (!empty($rs->fields['number']))
                $objSmarty->assign('numero',$rs->fields['number']);
        }  elseif ($oper == 'echo'){
            $objSmarty->assign('numero',$rs->fields['number']);
        }

        // --- Complemento ---
        if ($oper == 'update') {
            if ($oper == 'update') {
                if (empty($rs->fields['complement']))
                    $objSmarty->assign('plh_complemento','Informe o complemento.');
                else
                    $objSmarty->assign('complemento',$rs->fields['complement']);
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_complemento','Informe o complemento.');
        }  elseif ($oper == 'echo'){
            $objSmarty->assign('complemento',$rs->fields['complement']);
        }

        /* -- Fim endereco -- */

    }

    function ajaxStates()
    {
        echo $this->comboStatesHtml($_POST['countryId']);
    }

    function ajaxCities()
    {
        echo $this->comboCitesHtml($_POST['stateId']);
    }

    function ajaxNeighborhood()
    {
        echo $this->comboNeighborhoodHtml($_POST['cityId']);
    }

    function completeStreet()
    {
        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $aRet = array();

        $where = "WHERE `name` LIKE  '%". $this->getParam('search')."%'";
        $group = 'GROUP BY NAME';
        $order = 'ORDER BY NAME ASC';


        $rs = $dbPerson->getStreet($where,$group,$order);

        while (!$rs->EOF) {
            array_push($aRet,$rs->fields['name']);
            $rs->MoveNext();
        }
         //$array = array_map('htmlentities',$aRet);
        //$json = html_entity_decode(json_encode($array));
        //$json = json_encode($aRet);
        echo $this->makeJsonUtf8Compat($aRet);
    }

    function createFornecedor()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();

        $login = $this->makeLogin($_SESSION['SES_LOGIN_PERSON']);

        $dbFornecedor->BeginTrans();

        if($_POST['tipo'] == 1){
             $nome = $_POST['nomefisico'];
             $email = $_POST['emailfisica'];
             $idNaturePerson = 1;
        }else{
            $nome = $_POST['razaosocial'];
            $email = $_POST['emailjuridica'];
            $idNaturePerson = 2;
        }
        $cel_phone = $_POST['cel_phone'];
        $phone_number = $_POST['phone_number'];

        $this->logIt('cel_phone: '.$cel_phone.' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);


        $ret = $dbFornecedor->insertFornecedor(3,17,$idNaturePerson,1,$nome,$email,$cel_phone,$phone_number,'N');
        if (!$ret) {
            $dbFornecedor->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Fornecedor  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idPerson = $ret ;

        $dbFornecedor->CommitTrans();

        $this->loadModel('admin/person_model');


        $dbPerson = new person_model();
        $dbPerson->BeginTrans();
        $idTypeAddress = 2;
        if(!$_POST['bairro'])
            $idBairro = 1;
        else
            $idBairro = $_POST['bairro'];

        $retAddress = $dbPerson->insertAddress($idPerson,$_POST['cidade'],$idBairro,$idTypeAddress,$_POST['numero'],$_POST['complemento'],$_POST['cep'],$_POST['tipologra'],$_POST['endereco'] );
        if (!$retAddress) {
            $dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $dbPerson->CommitTrans();

        if($_POST['tipo'] == 2) {
            $ein_cnpj = $this->formatCPFCNPJBD('J',$_POST['ein_cnpj']);

             $this->dbFornecedor->BeginTrans();

            $retJur = $this->dbFornecedor->insertJuridicalData($idPerson, $_POST['nomefantasia'], $ein_cnpj, $_POST['iestadual'],'','');
            if (!$retJur) {
                $dbFornecedor->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Fornecedor  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $this->dbFornecedor->CommitTrans();

        }

        if($_POST['tipo'] == 1) {
            $cpf = $this->formatCPFCNPJBD('F',$_POST['cpf']);

            $this->dbFornecedor->BeginTrans();

            $retNat = $this->dbFornecedor->insertNaturalData($idPerson, $_POST['nomefantasia'], $cpf, $_POST['rg'], '', '', '', '', 'M');
            if (!$retNat) {
                $dbPerson->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Natural Data  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $this->dbFornecedor->CommitTrans();
        }


        $aRet = array(
            "idperson"          => $idPerson,
            "nomefornecedor"    => $nome,
            "status"            => 'OK'
        );

        echo json_encode($aRet);

    }

    function updateFornecedor()
    {

        $idPerson = $this->getParam('idperson');

        $persontmp = $this->_getFornecedor("WHERE idperson = $idPerson");

        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $dbFornecedor->BeginTrans();
        if($_POST['tipo'] == 1){
            $nomepessoa = $_POST['nomefisico'];
            $email = $_POST['emailfisica'];

        }else{
            $nomepessoa = $_POST['razaosocial'];
            $email = $_POST['emailjuridica'];

        }

        $phone_number = $_POST['phone_number'];
        $cel_phone = $_POST['cel_phone'];


        $ret = $dbFornecedor->updateFornecedor($idPerson,$nomepessoa,$_POST['tipo'],$email,$cel_phone,$phone_number);

        if (!$ret) {
            $dbFornecedor->RollbackTrans();
            if($this->log)
                $this->logIt('Update Fornecedor  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        //$dbFornecedor->CommitTrans();

        $dbPerson->BeginTrans();

        $cep = (empty($_POST['cep']) ? '' : $_POST['cep']);
        $idCidade = (empty($_POST['cidade']) ? '' : $_POST['cidade']);

        $ret = $dbPerson->updateAdressData($idPerson,$idCidade,$_POST['bairro'],$_POST['numero'],$_POST['complemento'],$cep,$_POST['tipologra'],$_POST['endereco']);
        if (!$ret) {
            $dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Update Fornecedor  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        //$dbPerson->CommitTrans();

        if($persontmp->fields['idnatureperson'] != $_POST['tipo']){
            //$this->dbFornecedor->BeginTrans();

            $retDel = $dbFornecedor->deleteNatureData($idPerson, $_POST['tipo']);

            if (!$retDel) {
                $dbFornecedor->RollbackTrans();
                $dbPerson->RollbackTrans();
                if ($this->log)
                    $this->logIt('Delete Nature Fornecedor  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            //$this->dbFornecedor->CommitTrans();

        }

        if($_POST['tipo'] == 2) {
            $ein_cnpj = $this->formatCPFCNPJBD('J',$_POST['ein_cnpj']);

            //$this->dbFornecedor->BeginTrans();

            $retJur = $dbFornecedor->insertJuridicalData($idPerson, $_POST['nomefantasia'], $ein_cnpj, $_POST['iestadual'],'','');
            if (!$retJur) {
                $dbFornecedor->RollbackTrans();
                $dbPerson->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Fornecedor  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            //$this->dbFornecedor->CommitTrans();

        }

        if($_POST['tipo'] == 1) {
            $cpf = $this->formatCPFCNPJBD('F',$_POST['ssn_cpf']);

            //$this->dbFornecedor->BeginTrans();

            $retNat = $dbFornecedor->insertNaturalData($idPerson, $_POST['nomefantasia'], $cpf, $_POST['rg'], '', '', '', '', 'M');
            if (!$retNat) {
                $dbPerson->RollbackTrans();
                $dbPerson->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Natural Data  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            //$this->dbFornecedor->CommitTrans();
        }

        $dbFornecedor->CommitTrans();
        $dbPerson->CommitTrans();



        $aRet = array(
            "idperson" => $idPerson,
            "status"   => 'OK'
        );


        echo json_encode($aRet);


    }

    function statusFornecedor()
    {
        $idPerson = $this->getParam('idperson');
        $newStatus = $_POST['newstatus'];
        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $ret = $dbPerson->changeStatus($idPerson,$newStatus);

        if (!$ret) {
            if($this->log)
                $this->logIt('Change Person Status - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idperson" => $idPerson,
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }

    function createBairro()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCity = $_POST['cidade'];
        $nameNeighborhood = utf8_decode($_POST['bairro']);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $idNeighborhood = $dbPerson->insertNeighborhood($idCity,$nameNeighborhood);

        $aRet = array(
            "idbairro" => $idNeighborhood
        );

        echo json_encode($aRet);
    }

    function createEstado()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCountry = $_POST['pais'];

        $nameState = utf8_decode($_POST['estado']);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $idState = $dbPerson->insertState($idCountry,$nameState);

        $aRet = array(
            "idestado" => $idState
        );

        echo json_encode($aRet);
    }

    function createCidade()
    {
        if (!$this->_checkToken()) {
            if ($this->log)
                $this->logIt('Error Token - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $idState = $_POST['estado'];
        $nameCity = utf8_decode($_POST['cidade']);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $idCity = $dbPerson->insertCity($idState, $nameCity);

        $aRet = array(
            "idcidade" => $idCity
        );

        echo json_encode($aRet);
    }

    function createLogradouro()
    {
        if (!$this->_checkToken()) {
            if ($this->log)
                $this->logIt('Error Token - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $nameLogradouro = utf8_decode($_POST['nome']);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $idtypestreet = $dbPerson->insertLogradouro($nameLogradouro);

        $aRet = array(
            "idlogradouro" => $idtypestreet
        );

        echo json_encode($aRet);
    }

    function ajaxLogradouro()
    {
        echo $this->comboLogradouroHtml();
    }

    public function comboLogradouroHtml()
    {
        $arrType = $this->_comboLogradouro();
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

    function buscacnpj($cnpj,$idperson)
    {
        $cnpj = str_pad(preg_replace('/[^0-9]/', '', $cnpj), 14, '0', STR_PAD_LEFT);

        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();

        if ($idperson != '') {
            $ret = $dbFornecedor->getCnpj("AND a.ein_cnpj = '".$cnpj."' AND a.idperson != ".$idperson . " ");
        } else {
            $ret = $dbFornecedor->getCnpj("AND a.ein_cnpj = '".$cnpj."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('CNPJ - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->RecordCount() > 0){
            $aRet = array(
                "idjuridicalperson" => $ret->fields['idjuridicalperson'],
                "idperson" => $ret->fields['idperson'],
                "name" => $ret->fields['name']
            );
        }else{
            $aRet = array();
        }

        return $aRet;
    }

    function buscacpf($cpf,$idperson)
    {
        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

        $this->loadModel('fornecedor_model');
        $dbFornecedor = new fornecedor_model();

        if ($idperson != '') {
            $ret = $dbFornecedor->getCpf("AND a.ssn_cpf = '".$cpf."' AND a.idperson != ".$idperson . " ");
        } else {
            $ret = $dbFornecedor->getCpf("AND a.ssn_cpf = '".$cpf."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('CPF - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        //echo "<pre>"; print_r($ret); echo "</pre>";
        if($ret->RecordCount() > 0){
            $aRet = array(
                "idnaturalperson" => $ret->fields['idnaturalperson'],
                "idperson" => $ret->fields['idperson'],
                "name" => $ret->fields['name']
            );
        }else{
            $aRet = array();
        }

        return $aRet;
    }

    function checkCPFCNPJ()
    {
        $typecheck = $_POST['typecheck'];
        $idperson = $_POST['idperson'];
        $valuecheck = $_POST['valuecheck'];

        if($typecheck == 'F'){$valid = $this->validateCPF($valuecheck); $msg = "Digite um CPF válido.";}
        else{$valid = $this->validateCNPJ($valuecheck); $msg = "Digite um CNPJ válido.";}

        if(!$valid){
            $aRet = array(
                "status" => false,
                "message" => $msg
            );
        }else{
            if($typecheck == 'F'){$ret = $this->buscacpf($valuecheck,$idperson); $msg = "O CPF informado já está cadastrado.";}
            else{$ret = $this->buscacnpj($valuecheck,$idperson); $msg = "O CNPJ informado já está cadastrado.";}

            if(sizeof($ret)> 0){
                $aRet = array(
                    "status" => false,
                    "idfornecedor" => $ret['idperson'],
                    "namefornecedor" => $ret['name'],
                    "message" => $msg
                );
            }else{
                $aRet = array(
                    "status" => true
                );
            }
        }

        echo json_encode($aRet);
    }

    function validateCPF($cpf)
    {
        $arrInvalid = array('00000000000','11111111111','22222222222','33333333333','44444444444','55555555555','66666666666','77777777777','88888888888','99999999999');

        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

        if(strlen($cpf) != 11 || in_array($cpf,$arrInvalid)){ return FALSE;}
        else{
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    function validateCNPJ($cnpj)
    {
        $arrInvalid = array('00000000000000','11111111111111','22222222222222','33333333333333','44444444444444','55555555555555','66666666666666','77777777777777','88888888888888','99999999999999');

        $cnpj = str_pad(preg_replace('/[^0-9]/', '', $cnpj), 14, '0', STR_PAD_LEFT);

        if(strlen($cnpj) != 14 || in_array($cnpj,$arrInvalid)){ return FALSE;}
        else{
            $j = 5;
            $k = 6;
            $soma1 = "";
            $soma2 = "";

            for ($i = 0; $i < 13; $i++) {

                $j = $j == 1 ? 9 : $j;
                $k = $k == 1 ? 9 : $k;

                $soma2 += ($cnpj{$i} * $k);

                if ($i < 12) {
                    $soma1 += ($cnpj{$i} * $j);
                }

                $k--;
                $j--;

            }

            $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
            $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;


            return (($cnpj{12} == $digito1) and ($cnpj{13} == $digito2));
            //return TRUE;
        }
    }

    function formatCPFCNPJBD($type,$formvalue)
    {
        if($type == 'J'){$result = str_pad(preg_replace('/[^0-9]/', '', $formvalue), 14, '0', STR_PAD_LEFT);}
        else{$result = str_pad(preg_replace('/[^0-9]/', '', $formvalue), 11, '0', STR_PAD_LEFT);}

        return $result;
    }

    function isvalidCPFCNPJ()
    {
        $typecheck = $_POST['typecheck'];
        $idperson = $_POST['idperson'];

        if($typecheck == 'F'){$valuecheck = $_POST['cpf'];}
        else{$valuecheck = $_POST['ein_cnpj'];}

        if($typecheck == 'F'){$valid = $this->validateCPF($valuecheck); $msg = "CPF inv&aacute;lido";}
        else{$valid = $this->validateCNPJ($valuecheck); $msg = "CNPJ inv&aacute;lido";}

        if(!$valid){
            echo json_encode($msg);
        }else{
            if($typecheck == 'F'){$ret = $this->buscacpf($valuecheck,$idperson); $msg1 = "O CPF informado j&aacute; est&aacute; cadastrado.";}
            else{$ret = $this->buscacnpj($valuecheck,$idperson); $msg1 = "O CNPJ informado j&aacute; est&aacute; cadastrado.";}

            if(sizeof($ret) > 0){
                echo json_encode($msg1);
            }else{
                echo json_encode(true);
            }
        }
    }

}