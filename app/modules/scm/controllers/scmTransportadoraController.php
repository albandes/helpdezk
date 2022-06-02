<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmTransportadora extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmTransportadora');

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
            $smarty->display('scm-transportadora-grid.tpl');
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
            if ( $_POST['searchField'] == 'name') $searchField = 'a.name';

            if (empty($where))
                $oper = ' AND ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumGridTransportadoras();

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

        $rsTransportadora = $this->_getGridTransportadora($where,$order,null,$limit);

        while (!$rsTransportadora->EOF) {
            $status_fmt = ($rsTransportadora->fields['status'] == 'A') ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'id'           => $rsTransportadora->fields['idperson'],
                'tipo'         => $this->getLanguageWord($rsTransportadora->fields['tipo']),
                'name'         => $rsTransportadora->fields['name'],
                'phone_number' => $rsTransportadora->fields['phone_number'],
                'email'        => $rsTransportadora->fields['email'],
                'status_fmt'   => $status_fmt,
                'status'       => $rsTransportadora->fields['status']

            );
            $rsTransportadora->MoveNext();

        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateTransportadora()
    {

        $smarty = $this->retornaSmarty();

        $this->makeScreenTransportadora($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-transportadora-create.tpl');
    }

    public function formUpdateTransportadora()
    {

        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        $rsPerson = $this->_getTransportadoraUpdateEcho("tbperson.idperson = $idPerson") ;

        $this->makeScreenTransportadora($smarty,$rsPerson,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idperson', $idPerson);

        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-transportadora-update.tpl');

    }

    public function echoTransportadora()
    {

        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        $rsPerson = $this->_getTransportadoraUpdateEcho("tbperson.idperson = $idPerson") ;

        $this->makeScreenTransportadora($smarty,$rsPerson,'echo');
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('scm-transportadora-echo.tpl');
    }

    function makeScreenTransportadora($objSmarty,$rs,$oper)
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
                $objSmarty->assign('plh_nome','Informe o nome da transportadora.');
            else
                $objSmarty->assign('nomefisico',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o nome da transportadora.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('nomefisico',$rs->fields['name']);
        }

        // --- Razão Social ---
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_razaosocial','Informe a razão social da transportadora.');
            else
                $objSmarty->assign('razaosocial',$rs->fields['name']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_razaosocial','Informe o nome da transportadora.');
        } elseif ($oper == 'echo') {
            $objSmarty->assign('razaosocial',$rs->fields['name']);
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

        echo $this->makeJsonUtf8Compat($aRet);
    }

    function createTransportadora()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $this->loadModel('transportadora_model');
        $dbTransportadora = new transportadora_model();

        $login = $this->makeLogin($_SESSION['SES_LOGIN_PERSON']);

        $dbTransportadora->BeginTrans();

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


        $ret = $dbTransportadora->insertTransportadora(3,18,$idNaturePerson,1,$nome,$email,$cel_phone,$phone_number,'N');
        if (!$ret) {
            $dbTransportadora->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Transportadora - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idPerson = $ret ;

        $dbTransportadora->CommitTrans();

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
            $this->loadModel('transportadora_model');
            $dbTransportadora = new transportadora_model();


            $dbTransportadora->BeginTrans();

            $retJur = $dbTransportadora->insertJuridicalData($idPerson, $_POST['ein_cnpj'], $_POST['iestadual']);
            if (!$retJur) {
                $dbTransportadora->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Transportadora  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $idPerson = $ret;

            $dbTransportadora->CommitTrans();

        }

        if($_POST['tipo'] == 1) {
            $this->loadModel('admin/person_model');
            $dbPerson = new person_model();

            $dbPerson->BeginTrans();
            $retNat = $dbPerson->insertNaturalData($idPerson, $_POST['cpf'], $_POST['rg'], $dtbirth = "''", 'M');
            if (!$retNat) {
                $dbPerson->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Natural Data  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }
            $dbPerson->CommitTrans();
        }



        $aRet = array(
            "idperson"           => $idPerson,
            "nometransportadora" => $nome,
            "status"             => 'OK'
        );

        echo json_encode($aRet);

    }

    function updateTransportadora()
    {

        $idPerson = $this->getParam('idperson');

        $this->loadModel('transportadora_model');
        $dbTransportadora = new transportadora_model();

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $dbTransportadora->BeginTrans();
        if($_POST['tipo'] == 1){
            $nomepessoa = $_POST['nomefisico'];
            $email = $_POST['emailfisica'];

        }else{
            $nomepessoa = $_POST['razaosocial'];
            $email = $_POST['emailjuridica'];

        }

        $phone_number = $_POST['phone_number'];
        $cel_phone = $_POST['cel_phone'];


        $ret = $dbTransportadora->updateTransportadora($idPerson,$nomepessoa,$email,$cel_phone,$phone_number);

        if (!$ret) {
            $dbTransportadora->RollbackTrans();
            if($this->log)
                $this->logIt('Update Transportadora  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $dbTransportadora->CommitTrans();

        $dbPerson->BeginTrans();

        $cep = (empty($_POST['cep']) ? '' : $_POST['cep']);
        $idCidade = (empty($_POST['cidade']) ? '' : $_POST['cidade']);

        $ret = $dbPerson->updateAdressData($idPerson,$idCidade,$_POST['bairro'],$_POST['numero'],$_POST['complemento'],$cep,$_POST['tipologra'],$_POST['endereco']);
        if (!$ret) {
            $dbPerson->RollbackTrans();
            if($this->log)
                $this->logIt('Update Transportadora - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $dbPerson->CommitTrans();


        if($_POST['tipo'] == 2) {
            $this->loadModel('transportadora_model');
            $dbTransportadora = new transportadora_model();

            $dbTransportadora->BeginTrans();

            $retJur = $dbTransportadora->updateJuridicalData($idPerson, $_POST['ein_cnpj'], $_POST['iestadual']);
            if (!$retJur) {
                $dbTransportadora->RollbackTrans();
                if ($this->log)
                    $this->logIt('Update Transportadora  - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }


            $dbTransportadora->CommitTrans();

        }
        if($_POST['tipo'] == 1) {
            $this->loadModel('transportadora_model');
            $dbTransportadora = new transportadora_model();


            $dbTransportadora->BeginTrans();

            $retJur = $dbTransportadora->updateNaturalData($idPerson, $_POST['ssn_cpf'], $_POST['rg']);
            if (!$retJur) {
                $dbTransportadora->RollbackTrans();
                if ($this->log)
                    $this->logIt('Insert Transportadora - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            $dbTransportadora->CommitTrans();

        }

        $aRet = array(
            "idperson" => $idPerson,
            "status"   => 'OK'
        );


        echo json_encode($aRet);

    }

    function statusTransportadora()
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

    function buscacnpj()
    {

        $ein_cnpj = $_POST['ein_cnpj'];
        $idperson = $_REQUEST['idperson'];

        $this->loadModel('transportadora_model');
        $dbTransportadora = new transportadora_model();

        if ($idperson != '') {
            $ret = $dbTransportadora->getCnpj("where ein_cnpj = '".$ein_cnpj."' AND idperson != ".$idperson . " ");
        } else {
            $ret = $dbTransportadora->getCnpj("where ein_cnpj = '".$ein_cnpj."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('CNPJ - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idjuridicalperson']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }

    function buscacpf()
    {

        $cpf = $_POST['cpf'];
        $idperson = $_REQUEST['idperson'];

        $this->loadModel('transportadora_model');
        $dbTransportadora = new transportadora_model();

        if ($idperson != '') {
            $ret = $dbTransportadora->getCpf("where ssn_cpf = '".$cpf."' AND idperson != ".$idperson . " ");
        } else {
            $ret = $dbTransportadora->getCpf("where ssn_cpf = '".$cpf."'");
        }

        if (!$ret) {
            if($this->log)
                $this->logIt('CPF - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->fields['idnaturalperson']){
            echo 'false';
            exit;
        }

        echo 'true';
        exit;
    }
}