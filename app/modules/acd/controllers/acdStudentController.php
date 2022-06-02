<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdStudent extends acdCommon
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

        $this->modulename = 'Academico' ;
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

        $this->makeNavVariables($smarty,'Academico');
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('acd-student-grid.tpl');

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

    public function formCreateStudent()
    {
        $smarty = $this->retornaSmarty();

        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$this->idPerson);

        if ($imgFormat) {
            $imgPhoto = $this->idPerson.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }
        $smarty->assign('person_photo', $this->getHelpdezkUrl().'/app/uploads/photos/' . $imgPhoto);

        $this->makeScreenStudent($smarty,'','create');


        $this->makeNavVariables($smarty,'Academico');
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('acd-student-create.tpl');
    }

    public function formUpdateStudent()
    {
        $smarty = $this->retornaSmarty();

        $this->loadModel('acdstudent_model');
        $dbStudent = new acdstudent_model();

        $idPerson = $this->getParam('idperson');
        $rsPerson = $dbStudent->getStudent("WHERE idaluno = $idPerson") ;

        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$idPerson);

        if ($imgFormat) {
            $imgPhoto = $idPerson.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $smarty->assign('foto', $this->getHelpdezkUrl().'/app/uploads/photos/' . $imgPhoto);

        $this->makeScreenStudent($smarty,$rsPerson,'update');

        $smarty->assign('hidden_idperson', $idPerson);

        $this->makeNavVariables($smarty,'Academico');
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('acd-student-update.tpl');

    }

    function makeScreenStudent($objSmarty,$rs,$oper)
    {

        // --- Nome ---
        if ($oper == 'update') {
            if (empty($rs->fields['name_aluno']))
                $objSmarty->assign('plh_nome','Informe o nome do aluno.');
            else
                $objSmarty->assign('nome',$rs->fields['name_aluno']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_nome','Informe o nome do aluno.');
        }

        // --- ID legado ---
        if ($oper == 'update') {
            if (empty($rs->fields['idaluno_legacy']))
                $objSmarty->assign('plh_idlegacy','Informe o ID legado.');
            else
                $objSmarty->assign('idlegacy',$rs->fields['idaluno_legacy']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_idlegacy','Informe o ID legado.');
        }

        // --- Matricula ---
        if ($oper == 'update') {
            if (empty($rs->fields['matricula']))
                $objSmarty->assign('plh_matricula','Informe a matrícula.');
            else
                $objSmarty->assign('matricula',$rs->fields['matricula']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_matricula','Informe a matrícula.');
        }



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

    function createStudent()
    {
        $this->loadModel('acdstudent_model');
        $dbStudent = new acdstudent_model();

        $dbStudent->BeginTrans();

        $ret = $dbStudent->insertStudent($_POST['nome'],$_POST['idlegacy'],$_POST['matricula']);
        if (!$ret) {
            $dbStudent->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Student  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $dbStudent->CommitTrans();
        $idPerson = $ret ;

        $aRet = array(
            "idperson" => $idPerson
        );

        echo json_encode($aRet);

    }

    function updateStudent()
    {
        $idPerson = $this->getParam('idperson');

        $this->loadModel('acdstudent_model');
        $dbStudent = new acdstudent_model();

        $dbStudent->BeginTrans();

        $ret = $dbStudent->updateStudent($idPerson,$_POST['nome'],$_POST['idlegacy'],$_POST['matricula']);
        if (!$ret) {
            $dbStudent->RollbackTrans();
            if($this->log)
                $this->logIt('Update Student  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idperson" => $idPerson,
            "status"   => 'OK'
        );

        $dbStudent->CommitTrans();

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