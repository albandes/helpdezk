<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/helpdezk/controllers/hdkCommonController.php');

class hdkWarning extends hdkCommon
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

        $this->modulename = 'helpdezk' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);

        $this->loadModel('admin/warning_model');
        $dbWarning = new warning_model();
        $this->dbWarning = $dbWarning;

        $this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty);
        $this->makeNavAdmin($smarty);

        $smarty->display('warning.tpl');

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
            $sidx ='b.title';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'b.title') $searchField = 'b.title';
            if ( $_POST['searchField'] == 'a.title') $searchField = 'a.title';
            if ( $_POST['searchField'] == 'a.dtcreate') $searchField = 'a.dtcreate';
            if ( $_POST['searchField'] == 'a.dtstart') $searchField = 'a.dtstart';
            if ( $_POST['searchField'] == 'a.dtend') $searchField = 'a.dtend';

            $where .= 'AND ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbWarning->getTotalWarning($where);

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

        $rsWarning = $this->dbWarning->selectWarning($where,$order,$limit);

        while (!$rsWarning->EOF) {
            switch ($rsWarning->fields['showin']) {
                case '1':
                    $showin = "Home";
                    break;
                case '2':
                    $showin = "Login";
                    break;
                case '3':
                    $showin = "Ambos";
                    break;
            }

            /*if($rsWarning->fields['dtend'] == "0000-00-00 00:00:00" || !$rsWarning->fields['dtend']) $dtEnd = 'Até ser encerrado';
            else*/ $dtEnd = $rsWarning->fields['dtend'];

            $aColumns[] = array(
                'idmessage'     => $rsWarning->fields['idmessage'],
                'topico'        => $rsWarning->fields['title_topic'],
                'titulo'        => $rsWarning->fields['title_warning'],
                'dtcreate'      => $rsWarning->fields['dtcreate'],
                'dtstart'       => $rsWarning->fields['dtstart'],
                'dtend'         => $dtEnd,
                'showin'        => $showin

            );
            $rsWarning->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsWarning->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateWarning()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $this->makeScreenWarning($smarty,'','create');

        $smarty->assign('token', $token) ;

        $this->makeNavVariables($smarty);
        $this->makeNavAdmin($smarty);
        $smarty->display('warning-create.tpl');
    }

    public function formUpdateWarning()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $smarty = $this->retornaSmarty();

        $idWarning = $this->getParam('idwarning');
        $rsWarning =  $this->dbWarning->selectWarning("AND idmessage = $idWarning") ;

        $this->makeScreenWarning($smarty,$rsWarning,'update');

        $smarty->assign('hidden_idwarning', $idWarning);
        $smarty->assign('token', $token) ;

        $this->makeNavVariables($smarty);
        $this->_makeNavHdk($smarty);
        $smarty->display('warning-update.tpl');

    }

    function makeScreenWarning($objSmarty,$rs,$oper)
    {

        // --- Tópico ---
        if ($oper == 'update') {
            $idTopicEnable = $rs->fields['idtopic'];
        } elseif ($oper == 'create') {
            $idTopicEnable = 1;
        }
        $arrTopic = $this->comboTopic();
        $objSmarty->assign('topicids',  $arrTopic['ids']);
        $objSmarty->assign('topicvals', $arrTopic['values']);
        $objSmarty->assign('idtopic', $idTopicEnable );

        // --- Título ---
        if ($oper == 'update') {
            if (empty($rs->fields['title_warning']))
                $objSmarty->assign('plh_title','Informe o título do aviso.');
            else
                $objSmarty->assign('title_warning',$rs->fields['title_warning']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_title','Informe o título do aviso.');
        }

        // --- Descrição ---
        if ($oper == 'update') {
            if (empty($rs->fields['description']))
                $objSmarty->assign('plh_description','Informe a descrição do aviso.');
            else
                $objSmarty->assign('description',$rs->fields['description']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_description','Informe a descrição do aviso.');
        }

        // --- Data inicio aviso ---
        if ($oper == 'update') {
            if ($rs->fields['dtstart'] == '0000-00-00 00:00:00'){
                $objSmarty->assign('plh_dtstart','Informe a data.');
                $objSmarty->assign('plh_timestart','Informe o horário.');
            }else{
                list($starttmp,$timetmp) = explode(' ',$rs->fields['dtstart']);
                list($yeartmp,$monthtmp,$daytmp) = explode('-',$starttmp);
                $startfmt = $daytmp.'/'.$monthtmp.'/'.$yeartmp;
                $objSmarty->assign('dtstart',$startfmt);
                $objSmarty->assign('timestart',$timetmp);
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_dtstart',date('d/m/Y
            '));
            $objSmarty->assign('plh_timestart',date('H:i'));
        }

        // --- Data encerramento aviso ---
        if ($oper == 'update') {
            if ($rs->fields['dtend'] == '0000-00-00 00:00:00'){
                $objSmarty->assign('flagUntil','S');
                $objSmarty->assign('checkedUntil','checked=checked');
            }else{
                list($endtmp,$endtmtmp) = explode(' ',$rs->fields['dtend']);
                list($yeartmp,$monthtmp,$daytmp) = explode('-',$endtmp);
                $endtfmt = $daytmp.'/'.$monthtmp.'/'.$yeartmp;
                $objSmarty->assign('dtend',$endtfmt);
                $objSmarty->assign('timeend',$endtmtmp);
                $objSmarty->assign('flagUntil','N');
                $objSmarty->assign('checkedUntil','');
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('flagUntil','S');
        }

        // --- Envia Email ---
        if ($oper == 'update') {
            if ($rs->fields['sendemail'] == 'N'){
                $objSmarty->assign('checkedSend','');
            }else{
                $objSmarty->assign('checkedSend','checked=checked');
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('checkedSend','');
        }

        // --- Mostrar em ---
        if ($oper == 'update') {
            $idShowinEnable = $rs->fields['showin'];
        } elseif ($oper == 'create') {
            $idShowinEnable = 1;
        }
        $arrShowin = $this->comboShowIn();
        $objSmarty->assign('showinids',  $arrShowin['ids']);
        $objSmarty->assign('showinvals', $arrShowin['values']);
        $objSmarty->assign('idshowin', $idShowinEnable );


    }

    function createWarning()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        } 

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $database = $this->getConfig('db_connect');

        if ($database == 'oci8po') {
            $dtStart = $this->formatSaveDateHour($_POST['dtstart']." ".$_POST['timestart']);
            $dtStart = $this->oracleDate($dtStart);
        }else {
            $dtStart = "'".str_replace("'", "",$this->formatSaveDate($_POST['dtstart']))." ".$_POST['timestart']."'";
        }

        if ($database == 'mysqli') {
            $now = "NOW()";
            if($_POST['warningend'] == "S"){//Até ser encerrado
                $dtEnd = "'0000-00-00 00:00:00'";
            }else{
                $dtEnd = "'".str_replace("'", "",$this->formatSaveDate($_POST['dtend']))." ".$_POST['timeend']."'";
            }
        }elseif ($database == 'oci8po') {
            $now = "SYSDATE";
            if($_POST['warningend'] == "S"){//Até ser encerrado
                $dtEnd = "NULL";
            }else{
                $dtEnd = $this->formatSaveDateHour($_POST['dtend']." ".$_POST['timeend']);
                $dtEnd = $this->oracleDate($dtEnd);
            }
        }

        $data = array(
            "idtopic" 		=> $_POST['topic'],
            "idperson" 		=> $idperson,
            "title" 		=> "'".addslashes($_POST['title'])."'",
            "description" 	=> "'".addslashes($_POST['description'])."'",
            "dtcreate" 		=> $now,
            "dtstart" 		=> $dtStart,
            "dtend" 		=> $dtEnd,
            "sendemail"		=> "'".$_POST['sendemailconf']."'",
            "showin"		=> $_POST['showin'],
            "emailsent"		=> 0
        );

        $this->dbWarning->BeginTrans();

        $ret = $this->dbWarning->insertWarning($data);

        if (!$ret) {
            $this->dbWarning->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Warning  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status" => 'OK'
        );

        $this->dbWarning->CommitTrans();
        echo json_encode($aRet);

    }

    function updateWarning()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $idWarning = $this->getParam('idwarning');
        $database = $this->getConfig('db_connect');

        if ($database == 'oci8po') {
            $dtStart = $this->formatSaveDateHour($_POST['dtstart']." ".$_POST['timestart']);
            $dtStart = $this->oracleDate($dtStart);
        }else {
            $dtStart = "'".str_replace("'", "",$this->formatSaveDate($_POST['dtstart']))." ".$_POST['timestart']."'";
        }

        if ($database == 'mysqli') {
            $now = "NOW()";
            if($_POST['warningend'] == "S"){//Até ser encerrado
                $dtEnd = "'0000-00-00 00:00:00'";
            }else{
                $dtEnd = "'".str_replace("'", "",$this->formatSaveDate($_POST['dtend']))." ".$_POST['timeend']."'";
            }
        }elseif ($database == 'oci8po') {
            $now = "SYSDATE";
            if($_POST['warningend'] == "S"){//Até ser encerrado
                $dtEnd = "NULL";
            }else{
                $dtEnd = $this->formatSaveDateHour($_POST['dtend']." ".$_POST['timeend']);
                $dtEnd = $this->oracleDate($dtEnd);
            }
        }

        $data = array(
            "idtopic" 		=> $_POST['topic'],
            "title" 		=> "'".$_POST['title']."'",
            "description" 	=> "'".$_POST['description']."'",
            "dtstart" 		=> $dtStart,
            "dtend" 		=> $dtEnd,
            "sendemail"		=> "'".$_POST['sendemailconf']."'",
            "showin"		=> $_POST['showin']
        );

        $this->dbWarning->BeginTrans();

        $ret = $this->dbWarning->updateWarning($data,$idWarning);

        if (!$ret) {
            $this->dbWarning->RollbackTrans();
            if($this->log)
                $this->logIt('Update Warning  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status"   => 'OK'
        );

        $this->dbWarning->CommitTrans();
        echo json_encode($aRet);


    }

    function ajaxOperatorGroup()
    {

        echo $this->checklistHtml("Groups",$_POST['topicId']);
    }

    function ajaxCorporation()
    {
        echo $this->checklistHtml("Corporations",$_POST['topicId']);
    }
	
	function createTopic()
    {
        $title = $_POST['modal_topic_title'];
        switch ($_POST['validity']) {
            case 1:
                $validity = '';
                break;
            case 2:
                $hours = $_POST['hoursValidity'];
                $validity = $hours * 3600;
                $validity .= 'H';
                break;
            case 3:
                $days = $_POST['daysValidity'];
                $validity = $days * 86400;
                $validity .= 'D';
                break;
            default:
                $validity = '';
                break;
        }

        if(!$_POST['send-email-topic']){
			$_POST['send-email-topic'] = "N";
		}

        $data = array(
					'title' => addslashes($title),
					'default_display' => $validity,
					'fl_emailsent'	=> $_POST['send-email-topic']
					);

        $this->dbWarning->BeginTrans();
		$insert_topic = $this->dbWarning->insertTopic($data);

		if($insert_topic){
            //$id_topic = $warning_model->InsertID();
            $id_topic = $this->dbWarning->TableMaxID('bbd_topic','idtopic');
			if($_POST['availableOperatorNew'] == 2 || $_POST['availableUserNew'] == 2){

				if($_POST['availableOperatorNew'] == 2){
					foreach($_POST['checkGroups'] as $group_id){
						$data = array('idtopic' => $id_topic, 'idgroup' => $group_id);
						$insertGroup = $this->dbWarning->insertTopicGroup($data);
						if(!$insertGroup){
                            $this->dbWarning->RollbackTrans();
                            if($this->log)
                                $this->logIt('Insert Topic Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
							return false;
						}
					}
				}
				if($_POST['availableUserNew'] == 2){
					foreach($_POST['checkCorporations'] as $company_id){
						$data = array('idtopic' => $id_topic, 'idcompany' => $company_id);
						$insertCompany = $this->dbWarning->insertTopicCompany($data);
						if(!$insertCompany){
                            $this->dbWarning->RollbackTrans();
                            if($this->log)
                                $this->logIt('Insert Topic Company - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
							return false;
						}
					}
				}
                $this->dbWarning->CommitTrans();
			}else{
                $this->dbWarning->CommitTrans();
			}
		}else{
            $this->dbWarning->RollbackTrans();
            if($this->log)
                $this->logIt('Insert Topic  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
		}

        $aRet = array(
            "idtopic" => $id_topic
        );

        echo json_encode($aRet);
    }
	
	function updateTopic()
    {
        $id_topic = $_POST['idtopic'];
		$title = $_POST['modal_topic_title_upd'];
        switch ($_POST['validity_upd']) {
            case 1:
                $validity = '';
                break;
            case 2:
                $hours = $_POST['hoursValidity_upd'];
                $validity = $hours * 3600;
                $validity .= 'H';
                break;
            case 3:
                $days = $_POST['daysValidity_upd'];
                $validity = $days * 86400;
                $validity .= 'D';
                break;
            default:
                $validity = '';
                break;
        }

        if(!$_POST['send-email-topic_upd']){
			$_POST['send-email-topic_upd'] = "N";
		}

        $data = array(
					'title' => addslashes($title),
					'default_display' => $validity,
					'fl_emailsent'	=> $_POST['send-email-topic_upd']
					);

        $this->dbWarning->BeginTrans();
		$update_topic = $this->dbWarning->updateTopic($data,$id_topic);

		if($update_topic){
            $clearGroup = $this->dbWarning->clearTopicGroup($id_topic);
			if(!$clearGroup){
				$this->dbWarning->RollbackTrans();
				return false;
			}
			$clearCompany = $this->dbWarning->clearTopicCompany($id_topic);
			if(!$clearCompany){
				$this->dbWarning->RollbackTrans();
				return false;
			}
			
			if($_POST['availableOperator'] == 2 || $_POST['availableUser'] == 2){

				if($_POST['availableOperator'] == 2){
					foreach($_POST['checkGroups'] as $group_id){
						$data = array('idtopic' => $id_topic, 'idgroup' => $group_id);
						$insertGroup = $this->dbWarning->insertTopicGroup($data);
						if(!$insertGroup){
                            $this->dbWarning->RollbackTrans();
                            if($this->log)
                                $this->logIt('Update Topic Group - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
							return false;
						}
					}
				}
				if($_POST['availableUser'] == 2){
					foreach($_POST['checkCorporations'] as $company_id){
						$data = array('idtopic' => $id_topic, 'idcompany' => $company_id);
						$insertCompany = $this->dbWarning->insertTopicCompany($data);
						if(!$insertCompany){
                            $this->dbWarning->RollbackTrans();
                            if($this->log)
                                $this->logIt('Update Topic Company - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
							return false;
						}
					}
				}
                $this->dbWarning->CommitTrans();
			}else{
                $this->dbWarning->CommitTrans();
			}
		}else{
            $this->dbWarning->RollbackTrans();
            if($this->log)
                $this->logIt('Update Topic  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
		}

        $aRet = array(
            "status" => "OK"
        );

        echo json_encode($aRet);
    }

    function ajaxTopic()
    {
        echo $this->selectHtml('Topic');
    }

    public function formListTopic()
    {
        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty);
        $this->_makeNavHdk($smarty);
        $smarty->display('warning-topic-grid.tpl');
    }

    public function jsonTopicGrid()
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
            $sidx ='title';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'title') $searchField = 'title';

            $where .= 'WHERE ' . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbWarning->getTotalTopic($where);

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

        $rsTopic = $this->dbWarning->getTopic($where,$order,$limit);

        while (!$rsTopic->EOF) {
            $def_dis = $rsTopic->fields['default_display'];
            if($def_dis && $def_dis != ' '){
                $type = substr($def_dis, -1);
                switch ($type) {
                    case 'D':
                        $tempo = substr($def_dis, 0, -1);
                        $tempo_days = $tempo / 86400;
                        $temp = $tempo_days;
                        break;
                    case 'H':
                        $tempo = substr($def_dis, 0, -1);
                        $tempo_hour = $tempo / 3600;
                        $temp = $tempo_hour;
                        break;
                }
            }else{
                $temp = '';
            }

            $aColumns[] = array(
                'idtopic'       => $rsTopic->fields['idtopic'],
                'topico'        => $rsTopic->fields['title'],
                'validade'      => $temp,
                'enviaemail'    => $rsTopic->fields['fl_emailsent']

            );
            $rsTopic->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsTopic->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

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

    public function comboTopic()
    {
        $rs = $this->dbWarning->getTopic();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idtopic'];
            $values[]   = $rs->fields['title'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function comboShowIn()
    {
        for($i=1;$i<=3;$i++) {
            $fieldsID[] = $i;
            switch ($i){
                case 1:
                    $values[]   = 'Home';
                    break;
                case 2:
                    $values[]   = 'Login';
                    break;
                default:
                    $values[]   = 'Ambos';
                    break;
            }

        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function checklistHtml($type,$idtopic=null)
    {
        $methodName = "checklist{$type}";
        $arrType = $this->$methodName();
        $checkbox = '';
        $a = sizeof($arrType['ids']);
        $rowsCol = round($a / 2);
        $i = 1;

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if($idtopic){
                $methodDb = "getTopic{$type}";
                $rsCheck = $this->dbWarning->$methodDb($idtopic,$indexValue);
                if($rsCheck->RecordCount() > 0){$checked = 'checked=checked';}
                else{$checked = '';}
            }else{$checked = '';}

            if($i == 1 || $i == ($rowsCol + 1)){$checkbox .= "<div class='col-sm-6'>";}
            $checkbox .= "<div class='checkbox i-checks'><label><input type='checkbox' name='check{$type}[]' value='$indexValue' id='".$indexValue."_Insert{$type}' {$checked}> <small> ".$arrType['values'][$indexKey]." </small></label></div>";
            if($i == $rowsCol || $i == $a ){$checkbox .= "</div>";}
            $i++;
        }
        return $checkbox;
    }

    public function checklistGroups()
    {
        $rs = $this->dbWarning->getGroups();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idgroup'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function checklistCorporations()
    {
        $this->loadModel('groups_model');
        $dbGroups = new groups_model();

        $rs = $dbGroups->selectCorporations();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function selectHtml($type)
    {
        $methodName = "combo{$type}";
        $arrType = $this->$methodName();
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

    function ajaxTopicInfo()
    {
        $where = "WHERE idtopic = ".$_POST['topicId'];

        $rs = $this->dbWarning->getTopic($where);
		
		$def_dis = $rs->fields['default_display'];
		if($def_dis){			
			$type = substr($def_dis, -1);
			switch ($type) {
				case 'D':
					$tempo = substr($def_dis, 0, -1);
					$tempo_tmp = $tempo / 86400;
					break;
				case 'H':
					$tempo = substr($def_dis, 0, -1);
					$tempo_tmp = $tempo / 3600;
					break;
				default:
					$type = 'P';
					$tempo_tmp = '';
					break;
			}
		}else{
			$type = 'P';
			$tempo_tmp = '';
		}
		
		$countGroup = $this->dbWarning->getCountTopicGroup($_POST['topicId']);
		if($countGroup > 0){ $avalGroup = 2; } else { $avalGroup = 1; }
		
		$countCorp = $this->dbWarning->getCountTopicCorp($_POST['topicId']);
		if($countCorp > 0){ $avalCorp = 2; } else { $avalCorp = 1; }
		
		
        
		$aRet = array(
            "title" 		=> $rs->fields['title'],
            "type"   		=> $type,
			"timedef" 		=> $tempo_tmp,
			"avalGroup"		=> $avalGroup,
			"avalCorp"		=> $avalCorp,
			"fl_emailsent"	=> $rs->fields['fl_emailsent']
        );

        echo json_encode($aRet);
    }

}