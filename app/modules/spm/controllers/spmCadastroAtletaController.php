<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/spm/controllers/spmCommonController.php');

class spmCadastroAtleta extends spmCommon
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

        $this->modulename = 'sportsmedicine' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,'sportmedicine');
        $this->_makeNavSpm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('spm-atleta-grid.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        $idCondicao = $_POST['idcondicao'];
        if ($idCondicao) {
            if ($idCondicao == 'ALL')
                $where = '';
            else
                $where .= " WHERE idcondicao = $idCondicao ";
        }

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
            if ( $_POST['searchField'] == 'apelido') $searchField = 'apelido';
            if ( $_POST['searchField'] == 'nome') $searchField = 'nome';
            if ( $_POST['searchField'] == 'condicao') $searchField = 'condicao';
            if ( $_POST['searchField'] == 'posicao') $searchField = 'posicao';
            if ( $_POST['searchField'] == 'departamento') $searchField = 'departamento';
            if ( $_POST['searchField'] == 'dtcreate') $searchField = 'dtcreate';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumAtletas();

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

        $rsAtleta = $this->_getAtleta($where,$order,null,$limit);

        while (!$rsAtleta->EOF) {
            //$star = ($rsTicket->fields['flag_opened'] == 1 && $rsTicket->fields['status'] != 1) ? '<i class="fa fa-star" />' : '';
            switch($rsAtleta->fields['idcondicao']){
                case 1:
                    $star = '<span class="label label-success">Normal</span>';
                    break;
                case 2:
                    $star = '<span class="label">Preventiva</span>';
                    break;
                case 3:
                    $star = '<span class="label label-warning">Terapeutica</span>';
                    break;
                case 4:
                    $star = '<span class="label label-info">Retreinamento</span>';
                    break;
                case 5:
                    $star = '<span class="label label-danger">Cirurgia</span>';
                    break;

            }

            $status_fmt = ($rsAtleta->fields['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
            $aColumns[] = array(
                'star'          => $star ,
                'id'            => $rsAtleta->fields['idperson'],
                'apelido'       => $rsAtleta->fields['apelido'],
                'nome'          => $rsAtleta->fields['nome'],
                'condicao'      => $rsAtleta->fields['condicao'],
                'cor'           => $rsAtleta->fields['cor'],
                'posicao'       => $rsAtleta->fields['posicao'],
                'departamento'  => $rsAtleta->fields['departamento'],
                'dtcreate'      => $rsAtleta->fields['dtcreate'],
                'status_fmt'    => $status_fmt,
                'status'        => $rsAtleta->fields['status']

            );
            $rsAtleta->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsAtleta->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateAtleta()
    {
        $smarty = $this->retornaSmarty();

        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$this->idPerson);

        if ($imgFormat) {
            $imgPhoto = $this->idPerson.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }
        $smarty->assign('person_photo', $this->getHelpdezkUrl().'/app/uploads/photos/' . $imgPhoto);

        $this->makeScreenAtleta($smarty,'','create');


        $this->makeNavVariables($smarty,'sportmedicine');
        $this->_makeNavSpm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('spm-atleta-create.tpl');
    }

    public function formUpdateAtleta()
    {
        $smarty = $this->retornaSmarty();

        $idPerson = $this->getParam('idperson');
        $rsPerson = $this->_getAtleta("where idperson = $idPerson") ;

        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$idPerson);

        if ($imgFormat) {
            $imgPhoto = $idPerson.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $smarty->assign('foto', $this->getHelpdezkUrl().'/app/uploads/photos/' . $imgPhoto);

        $this->makeScreenAtleta($smarty,$rsPerson,'update');

        $smarty->assign('hidden_idperson', $idPerson);

        $this->makeNavVariables($smarty,'sportmedicine');
        $this->_makeNavSpm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('spm-atleta-update.tpl');

    }

    function makeScreenAtleta($objSmarty,$rs,$oper)
    {

        // --- Apelido ---
        if ($oper == 'update') {
            if (empty($rs->fields['apelido']))
                $objSmarty->assign('plh_apelido','Informe o apelido do atleta.');
            else
                $objSmarty->assign('apelido',$rs->fields['apelido']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_apelido','Informe o apelido do atleta.');
        }

        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['nome']))
                $objSmarty->assign('plh_nome','Informe o nome do atleta.');
            else
                $objSmarty->assign('nome',$rs->fields['nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o nome do atleta.');
        }

        // --- Posicao ---
        if ($oper == 'update') {
            $idPosicaoEnable = $rs->fields['idposicao'];
        } elseif ($oper == 'create') {
            $idPosicaoEnable = 1;
        }
        $arrPosicao = $this->_comboAtletaPosicao();
        $objSmarty->assign('posicaoids',  $arrPosicao['ids']);
        $objSmarty->assign('posicaovals', $arrPosicao['values']);
        $objSmarty->assign('idposicao', $idPosicaoEnable );

        // --- Condicao ---
        if ($oper == 'update') {
            $idCondicaoEnable = $rs->fields['idcondicao'];
        } elseif ($oper == 'create') {
            $idCondicaoEnable = 1;
        }
        $arrCondicao = $this->_comboAtletaCondicao();
        $objSmarty->assign('condicaoids',  $arrCondicao['ids']);
        $objSmarty->assign('condicaovals', $arrCondicao['values']);
        $objSmarty->assign('idcondicao', $idCondicaoEnable );

        // --- Departamento ---
        if ($oper == 'update') {
            $idDepartamentoEnable = $rs->fields['iddepartamento'];
        } elseif ($oper == 'create') {
            $idDepartamentoEnable = 1;
        }
        $arrDepartamento = $this->_comboAtletaDepartamento();
        $objSmarty->assign('departamentoids',  $arrDepartamento['ids']);
        $objSmarty->assign('departamentovals', $arrDepartamento['values']);
        $objSmarty->assign('iddepartamento', $idDepartamentoEnable );

        // --- Cpf ---
        if ($oper == 'update') {
            if (empty($rs->fields['ssn_cpf']))
                $objSmarty->assign('plh_cpf','Informe o cpf.');
            else
                $objSmarty->assign('cpf',$rs->fields['cpf_fmt']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_cpf','Informe o cpf.');
        }

        // --- Nascimento ---
        if ($oper == 'update') {
            if ($rs->fields['dtbirth'] == '0000-00-00')
                $objSmarty->assign('plh_dtnasc','Informe a data.');
            else
                $objSmarty->assign('dtnasc',$rs->fields['dtbirth_fmt']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_dtnasc','Informe a data.');
        }

        // --- Email ---
        if ($oper == 'update') {
            if (empty($rs->fields['email']))
                $objSmarty->assign('plh_email','Informe o e-mail.');
            else
                $objSmarty->assign('email',$rs->fields['email']);

        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_email','Informe o e-mail.');
        }

        // --- Telefone ---
        if ($oper == 'update') {
            if (empty($rs->fields['phone_number']))
                $objSmarty->assign('plh_telefone','Informe o telefone.');
            else
                $objSmarty->assign('telefone',$rs->fields['phone_number']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_telefone','Informe o telefone.');
        }

        // --- Celular ---
        if ($oper == 'update') {
            if (empty($rs->fields['cel_phone']))
                $objSmarty->assign('plh_celular','Informe o celular.');
            else
                $objSmarty->assign('celular',$rs->fields['cel_phone']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_celular','Informe o celular.');
        }

        /* -- Endereco -- */

        // --- Country ---
        if ($oper == 'update') {
            $idCountryEnable = $rs->fields['idcountry'];
        } elseif ($oper == 'create') {
            $idCountryEnable = $this->getIdCountryDefault();
        }
        $arrCountry = $this->comboCountries();
        $objSmarty->assign('countryids',  $arrCountry['ids']);
        $objSmarty->assign('countryvals', $arrCountry['values']);
        $objSmarty->assign('idcountry', $idCountryEnable  );
        // --- State ---
        if ($oper == 'update') {
            $idStateEnable = $rs->fields['idstate'];
        } elseif ($oper == 'create') {
            $idStateEnable = $this->getIdStateDefault();
        }
        $arrCountry = $this->comboStates($idCountryEnable);
        $objSmarty->assign('stateids',  $arrCountry['ids']);
        $objSmarty->assign('statevals', $arrCountry['values']);
        $objSmarty->assign('idstate',   $idStateEnable);
        // --- City ---
        if ($oper == 'update') {
            $idCityEnable = $rs->fields['idcity'];
        } elseif ($oper == 'create') {
            $idCityEnable = $this->getIdCityDefault($idStateEnable);
        }
        $arrCity = $this->comboCity($idStateEnable);
        $objSmarty->assign('cityids',  $arrCity['ids']);
        $objSmarty->assign('cityvals', $arrCity['values']);
        $objSmarty->assign('idcity',   $idCityEnable);
        // --- Neighborhood ---
        if ($oper == 'update'){
            $idNeighborhoodEnable = $rs->fields['idneighborhood'];
            $arrNeighborhood = $this->comboNeighborhood($rs->fields['idcity']);
        } elseif ($oper == 'create') {
            $idNeighborhoodEnable = 1;
            $arrNeighborhood = $this->comboNeighborhood($idCityEnable);
        }

        $objSmarty->assign('neighborhoodids',  $arrNeighborhood['ids']);
        $objSmarty->assign('neighborhoodvals', $arrNeighborhood['values']);
        $objSmarty->assign('idneighborhood',   $idNeighborhoodEnable);
        // --- Cep ---
        if (empty($rs->fields['zipcode']))
            $objSmarty->assign('plh_cep','Informe o cep.');
        else
            $objSmarty->assign('cep',$rs->fields['zipcode']);
        // --- Type Street ---
        if ($oper == 'update') {
            $idTypeStreetEnable = $rs->fields['idtypestreet'];
        } elseif ($oper == 'create') {
            $idTypeStreetEnable = '';
        }
        $arrTypestreet = $this->comboTypeStreet();
        $objSmarty->assign('typestreetids',  $arrTypestreet['ids']);
        $objSmarty->assign('typestreetvals', $arrTypestreet['values']);
        $objSmarty->assign('idtypestreet', $idTypeStreetEnable  );
        // --- Logradouro ---
        if ($oper == 'update') {
            if (empty($rs->fields['logradouro_nome']))
                $objSmarty->assign('plh_logradouro','Informe o logradouro.');
            else
                $objSmarty->assign('logradouro',$rs->fields['logradouro_nome']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_logradouro','Informe o logradouro.');
        }
        // --- Number ---
        if ($oper == 'update') {
            if (!empty($rs->fields['number']))
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

    function salvaFoto()
    {
        $idPerson = $_POST['idperson'];
        $this->logIt('Insert Atleta  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        if (!empty($_FILES)) {
            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            $targetPath = $this->helpdezkPath . '/app/uploads/photos/' ;

            //$idAtt = $this->dbTicket->saveTicketAtt($code_request,$fileName);

            $targetFile =  $targetPath.$idPerson.$extension;

            if (move_uploaded_file($tempFile,$targetFile)){
                if($this->log)
                    $this->logIt("Save person photo: # ". $idPerson . ' - File: '.$targetFile.' - program: '.$this->program ,7,'general',__LINE__);
                return 'OK';
            } else {
                if($this->log)
                    $this->logIt("Can't save person photo: # ". $idPerson . ' - File: '.$targetFile.' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

        }
    }

    function createAtleta()
    {
        $this->loadModel('atleta_model');
        $dbAtleta = new atleta_model();

        $login = $this->makeLogin($_POST['nome']);

        $dbAtleta->BeginTrans();

        $ret = $dbAtleta->insertAtleta(3,2,1,1,$_POST['nome'],$login,$_POST['email'],'NULL','A','N',$_POST['telefone'],'NULL',$_POST['celular'],0,0,0,1,$_POST['condicao'],$_POST['departamento'],$_POST['posicao'],$_POST['apelido']);
        if (!$ret) {
            $dbAtleta->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Atleta  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idPerson = $ret ;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $idTypeAddress = 2;
        if(!$_POST['bairro'])
            $idBairro = 1;
        else
            $idBairro = $_POST['bairro'];
        $retAddress = $dbPerson->insertAddress($idPerson,$_POST['cidade'],$idBairro,$idTypeAddress,$_POST['numero'],$_POST['complemento'],$_POST['cep'],$_POST['tipologra'],$_POST['endereco'] );
        if (!$retAddress) {
            $dbAtleta->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Address  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($_POST['dtnasc']){
            $dtNasc = $this->formatSaveDate($_POST['dtnasc']);
        }


        $retNat = $dbPerson->insertNaturalData($idPerson,$_POST['cpf'],$dtNasc,'M');
        if (!$retNat) {
            $dbAtleta->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Natural Data  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idperson" => $idPerson,
            "login" => $login,
            "apelido" => $_POST['apelido']
        );

        echo json_encode($aRet);

    }

    function updateAtleta()
    {
        $idPerson = $this->getParam('idperson');

        $this->loadModel('atleta_model');
        $dbAtleta = new atleta_model();

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();

        $dbAtleta->BeginTrans();
        $dbPerson->BeginTrans();

        $ret = $dbAtleta->updateAtleta($idPerson,$_POST['nome'],$_POST['email'],$_POST['telefone'],$_POST['celular'],$_POST['condicao'],$_POST['departamento'],$_POST['posicao'],$_POST['apelido']);
        if (!$ret) {
            $dbAtleta->RollbackTrans();
            if($this->log)
                $this->logIt('Update Atleta  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ret = $dbPerson->updateAdressData($idPerson,$_POST['cidade'],$_POST['bairro'],$_POST['numero'],$_POST['complemento'],$_POST['cep'],$_POST['tipologra'],$_POST['endereco']);
        if (!$ret) {
            $dbAtleta->RollbackTrans();
            if($this->log)
                $this->logIt('Update Atleta  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($_POST['dtnasc'])
            $dtNasc = $this->formatSaveDate($_POST['dtnasc']);

        $ret = $dbPerson->updateNaturalData($idPerson,$_POST['cpf'],$dtNasc,'M');
        if (!$ret) {
            $dbAtleta->RollbackTrans();
            if($this->log)
                $this->logIt('Update Atleta  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idperson" => $idPerson,
            "status"   => 'OK'
        );

        $dbAtleta->CommitTrans();
        $dbPerson->CommitTrans();

        echo json_encode($aRet);


    }

    function statusAtleta()
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


}