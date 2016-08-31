<?php

class Warnings extends Controllers {

    public $database;

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
        $this->database = $this->getConfig('db_connect');
	}
	
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("warnings/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('warnings.tpl.html');
    }

    public function json() {
    	$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();
		
        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];

        if (!$sortorder)
            $sortorder = 'asc';


        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);

        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];
		$tipwarning = $_POST['tipwarning'];
        $where = "";
        if ($query) {
            switch ($qtype) {
                case 'name':
                    $where .= "AND  $qtype LIKE '$query%' ";
                    break;
                default:
                    $where .= "";
                    break;
            }
        }
        $database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$now = "NOW()";
			$dateendativo = "= '0000-00-00 00:00:00'";
			$dateendinativo = "!= '0000-00-00 00:00:00'";
		}elseif ($database == 'oci8po') {
			$now = "SYSDATE";
			$dateendativo = "IS NULL";
			$dateendinativo = "IS NOT NULL";
		}
		
		switch ($tipwarning) {
            case "100":
                $where .= "AND (a.dtend > $now OR a.dtend $dateendativo)";
                break;
			case "101":
                $where .= "AND (a.dtend < $now AND a.dtend $dateendinativo)";
                break;
			case "102":
                $where .= "";
                break;
            default:
                $where .= "AND (a.dtend > $now OR a.dtend $dateendativo)";
                break;
        }			
		
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";



        $bd = new warning_model();
        $rsWarning = $bd->selectWarning($where, $order, $limit);
        if ($database == 'mysqlt') {
			$rstotal = $this->found_rows();
        	$total = $rstotal->fields['found_rows'];
		} elseif ($database == 'oci8po') {
			$total = $rsWarning->rowcount();
			if(!$total) $total = 0;
		}


        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsWarning->EOF) {
            if ($database == 'oci8po'){
                $dtEnd = $rsWarning->fields['dtend'];
                if($dtEnd == "" || !$dtEnd)
                    $dtEnd = $langVars['Until_closed'];
            }else{
                $dtEnd = $this->formatDateHour($rsWarning->fields['dtend']);
                if($dtEnd == "00/00/0000 00:00" || !$dtEnd)
                    $dtEnd = $langVars['Until_closed'];
            }

			
			switch ($rsWarning->fields['showin']) {
				case '1':
					$showin = "Home";
					break;
				case '2':
					$showin = "Login";
					break;
				case '3':
					$showin = $langVars['Both'];
					break;
			}
            if ($database == 'oci8po'){
            $rows[] = array(
                "id" => $rsWarning->fields['idmessage'],
                "cell" => array(
                    $rsWarning->fields['title_topic'],
                    $rsWarning->fields['title_warning'],
                    $rsWarning->fields['dtcreate'],
                   $rsWarning->fields['dtstart'],
                    $dtEnd,
                    $showin
                )
            );

        }else{
            $rows[] = array(
                "id" => $rsWarning->fields['idmessage'],
                "cell" => array(
                    $rsWarning->fields['title_topic'],
                    $rsWarning->fields['title_warning'],
                    $this->formatDate($rsWarning->fields['dtcreate']),
                    $this->formatDateHour($rsWarning->fields['dtstart']),
                    $dtEnd,
                    $showin
                )
            );

        }
            $rsWarning->MoveNext();
        }

        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function inserttopicmodal() {
		$smarty = $this->retornaSmarty();		
		$bd = new groups_model();
        $rs = $bd->selectGroup();		
		while (!$rs->EOF) {			
			$group[] = array(
                "id" => $rs->fields['idgroup'],
                "name" => $rs->fields['name'] 
            );
			$rs->MoveNext();        
		}		
		$smarty->assign('groups', $group);
		$smarty->assign('groupslength', count($group)-1);
		$select = $bd->selectCorporations();
        while (!$select->EOF) {
        	$company[] = array(
                "id" => $select->fields['idperson'],
                "name" => $select->fields['name'] 
            );
            $select->MoveNext();
        }
        $smarty->assign('company', $company);
		$smarty->assign('companylength', count($company)-1);
		$smarty->display('modais/warnings/insert_topic.tpl.html');
	}
    
	public function topicInsert(){
		
		$title = $_POST['txtTitle'];		
		switch ($_POST['validity']) {
			case 1:
				$validity = ' ';
				break;
			case 2:
				$hours = $_POST['txtHours'];
				$validity = $hours * 3600;
				$validity .= 'H';
				break;
			case 3:
				$days = $_POST['txtDays'];
				$validity = $days * 86400;
				$validity .= 'D';
				break;
			default:
				$validity = ' ';
				break;
		}
		
		if(!$_POST['chkSendEmail']){
			$_POST['chkSendEmail'] = "N";
		}
		
		$data = array(
					'title' => $title,
					'default_display' => $validity,
					'fl_emailsent'	=> $_POST['chkSendEmail']
					);
		$warning_model = new warning_model();
		$warning_model->BeginTrans();
		$insert_warning = $warning_model->insertTopic($data);
		
		if($insert_warning){
			if($_POST['avaibleOperator'] == 2 || $_POST['avaibleUser'] == 2){
				//$id_topic = $warning_model->InsertID();
				$id_topic = $warning_model->TableMaxID('bbd_topic','idtopic');
				
				if($_POST['avaibleOperator'] == 2){
					foreach($_POST['selectGroup'] as $group_id){
						$data = array('idtopic' => $id_topic, 'idgroup' => $group_id);
						$insertGroup = $warning_model->insertTopicGroup($data);
						if(!$insertGroup){
							$warning_model->RollbackTrans();
							return false;
						}
					}
				}
				if($_POST['avaibleUser'] == 2){
					foreach($_POST['selectCompany'] as $company_id){
						$data = array('idtopic' => $id_topic, 'idcompany' => $company_id);
						$insertCompany = $warning_model->insertTopicCompany($data);
						if(!$insertCompany){
							$warning_model->RollbackTrans();
							return false;
						}	
					}
				}				
				$warning_model->CommitTrans();
				echo "OK";				
			}else{
				$warning_model->CommitTrans();
				echo "OK";
			}
		}else{
			$warning_model->RollbackTrans();
			return false;
		}
	}
    
    
    public function modalseetopics()
    {
        $smarty = $this->retornaSmarty();
		$db = new warning_model();
		$select = $db->selectTopics();
        while (!$select->EOF) {
        	$topics[] = array(
                "id" => $select->fields['idtopic'],
                "title" => $select->fields['title'] 
            );
            $select->MoveNext();
        }
		
		$smarty->assign('topics', $topics);
		$smarty->display('modais/warnings/see_topics.tpl.html');
    }
    
    public function modalEditTopic(){
    	$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$db = new warning_model();
		
		$getCompany = $db->getTopicCompany($id);
		$thisCompany = array();
		while (!$getCompany->EOF) {			
			$thisCompany[] = $getCompany->fields['idcompany'];
			$getCompany->MoveNext();
		}

		$getGroup = $db->getTopicGroup($id);
		$thisGroup = array();
		while (!$getGroup->EOF) {			
			$thisGroup[] = $getGroup->fields['idgroup'];
			$getGroup->MoveNext();
		}
		
		
		$bd = new groups_model();
        $rs = $bd->selectGroup();
		$thisGroupCount = count($thisGroup);
		while (!$rs->EOF) {
			if($thisGroupCount > 0){
        		if(in_array($rs->fields['idgroup'],$thisGroup)){
        			$checked = "Y";
        		}else{
        			$checked = "N";
        		}
        	}else{
        		$checked = "N";
        	}
					
			$group[] = array(
                "id" => $rs->fields['idgroup'],
                "name" => $rs->fields['name'],
                "checked" => $checked, 
            );
			$rs->MoveNext();
		}	
		
		$smarty->assign('groupscount', $thisGroupCount);
		$smarty->assign('groups', $group);
		$smarty->assign('groupslength', count($group)-1);
		
		
		$select = $bd->selectCorporations();
		$thisCompanyCount = count($thisCompany);
        while (!$select->EOF) {
        	if($thisCompanyCount > 0){
        		if(in_array($select->fields['idperson'],$thisCompany)){
        			$checked = "Y";
        		}else{
        			$checked = "N";
        		}
        	}else{
        		$checked = "N";
        	}
			
        	$company[] = array(
                "id" => $select->fields['idperson'],
                "name" => $select->fields['name'],
                "checked" => $checked,
            );
            $select->MoveNext();
        }
		
		$smarty->assign('companycount', $thisCompanyCount);
        $smarty->assign('company', $company);
		$smarty->assign('companylength', count($company)-1);
		
		$select = $db->selectTopic($id);
		$def_dis = $select->fields['default_display'];
		if($def_dis){			
			$type = substr($def_dis, -1);
			switch ($type) {
				case 'D':
					$tempo = substr($def_dis, 0, -1);
					$tempo_days = $tempo / 86400;
					$smarty->assign('temp', $tempo_days);
					break;
				case 'H':
					$tempo = substr($def_dis, 0, -1);
					$tempo_hour = $tempo / 3600;
					$smarty->assign('temp', $tempo_hour);
					break;
			}
			$smarty->assign('type', $type);
		}else{
			$smarty->assign('type', '');
		}
				
		$smarty->assign('id', $id);
		$smarty->assign('title', $select->fields['title']);
		$smarty->assign('email', $select->fields['fl_emailsent']);		
		$smarty->display('modais/warnings/edit_topic.tpl.html');
    }
    
    public function topicEdit(){
		$id_topic = $_POST['id_topic'];
		
		$title = $_POST['txtTitle'];
		switch ($_POST['validity']) {
			case 1:
				$validity = null;
				break;
			case 2:
				$hours = $_POST['txtHours'];
				$validity = $hours * 3600;
				$validity .= 'H';
				break;
			case 3:
				$days = $_POST['txtDays'];
				$validity = $days * 86400;
				$validity .= 'D';
				break;
			default:
				$validity = null;
				break;
		}
		
		if(!$_POST['chkSendEmail']){
			$_POST['chkSendEmail'] = "N";
		}
		
		$data = array(
					'title' => $title,
					'default_display' => $validity,
					'fl_emailsent'	=> $_POST['chkSendEmail']
					);
		$warning_model = new warning_model();
		$warning_model->BeginTrans();
		$insert_warning = $warning_model->updateTopic($data, $id_topic);
		
		if($insert_warning){
			$clearGroup = $warning_model->clearTopicGroup($id_topic);
			if(!$clearGroup){
				$warning_model->RollbackTrans();
				return false;
			}
			$clearCompany = $warning_model->clearTopicCompany($id_topic);
			if(!$clearCompany){
				$warning_model->RollbackTrans();
				return false;
			}
			
			if($_POST['avaibleOperator'] == 2 || $_POST['avaibleUser'] == 2){
								
				if($_POST['avaibleOperator'] == 2){
					foreach($_POST['selectGroup'] as $group_id){
						$data = array('idtopic' => $id_topic, 'idgroup' => $group_id);
						$insertGroup = $warning_model->insertTopicGroup($data);
						if(!$insertGroup){
							$warning_model->RollbackTrans();
							return false;
						}
					}
				}
				if($_POST['avaibleUser'] == 2){
					foreach($_POST['selectCompany'] as $company_id){
						$data = array('idtopic' => $id_topic, 'idcompany' => $company_id);
						$insertCompany = $warning_model->insertTopicCompany($data);
						if(!$insertCompany){
							$warning_model->RollbackTrans();
							return false;
						}	
					}
				}				
				$warning_model->CommitTrans();
				echo "OK";				
			}else{
				$warning_model->CommitTrans();
				echo "OK";
			}
		}else{
			$warning_model->RollbackTrans();
			return false;
		}
	}

	public function getTopicInfo() {
        $bd = new warning_model();
        $id = $this->getParam('idtopic');		
        $rs = $bd->selectTopic($id);
		$dfDisplay = $rs->fields['default_display'];
		if($dfDisplay){
			$lastLetter = substr($dfDisplay, -1);
			$hours = $_POST['txtHours'];
			
			if($lastLetter == "D"){
				$days = substr($dfDisplay,0,-1) / 86400;
				$date = strtotime("+$days days");
			}elseif($lastLetter == "H"){
				$hours = substr($dfDisplay,0,-1) / 3600;
				$date = strtotime("+$hours hours");
			}			
		}else{
			$date = strtotime("now");
		}
		
		$date_now = strtotime("now");
		if($this->getConfig('lang') == 'pt_BR') {
			$dt_format = "d/m/Y";
		}else{
			$dt_format = "m/d/Y";
		}
		
		$output = array(
						"title" => $rs->fields['title'],
						"date" => date($dt_format, $date),
						"time" => date("H:i", $date),
						"date_now" => date($dt_format, $date_now),
						"time_now" => date("H:i", $date_now),
						"let" => $lastLetter,
						"fl_emailsent" => $rs->fields['fl_emailsent'],
						"lang" => $lang_default,
						"total" => $rs->fields['total']
					);
		
		echo json_encode($output);
    }
        
    
    public function insertwarningmodal() {
		$smarty = $this->retornaSmarty();
		$warning_model = new warning_model();
		
		$select = $warning_model->selectTopics();
		
		while (!$select->EOF) {
            $campos[] = $select->fields['idtopic'];
            $valores[] = $select->fields['title'];
            $select->MoveNext();
        }
        $smarty->assign('topicids', $campos);
        $smarty->assign('topicvals', $valores);
        $smarty->assign('date', date("d/m/Y"));
		$smarty->assign('time', date("H:i"));
		
		
		$smarty->display('modais/warnings/insert_warning.tpl.html');
	}
	
    public function warningInsert() {
    	
		$idTopic 		= $_POST['cmbTopic'];
		$title 			= $_POST['txtTitle'];
		$description 	= $_POST['txtDescription'];
		$initDate 		= $_POST['validDate'];
		$initHour 		= $_POST['validHour'];
		$endDate 		= $_POST['validEndDate'];
		$endHour 		= $_POST['validEndHour'];
		$untilClosed 	= $_POST['chkUntilClosed'];
		$sendEmail 		= $_POST['chkSendEmail'];
		$show 			= $_POST['chkShowAlert'];
		$user 			= $_SESSION['SES_COD_USUARIO'];

        if ($this->database == 'oci8po') {
            $dtStart = " to_date('".$initDate." ".$initHour."','DD/MM/YYYY HH24:MI') ";
        }
        else
        {
            $dtStart = $this->formatSaveDateHour($initDate." ".$initHour);
        }

		
        
		if(!$sendEmail) $sendEmail = "N";
		if(!$show) $show = "1";
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$now = "NOW()";
			if($untilClosed == "S"){//Até ser encerrado
        		$dtEnd = "'0000-00-00 00:00:00'";
	        }else{
	        	$dtEnd = $this->formatSaveDateHour($endDate." ".$endHour);
	        }
		}elseif ($database == 'oci8po') {
			$now = "SYSDATE";
			if($untilClosed == "S"){//Até ser encerrado
        		$dtEnd = "NULL";
	        }else{
	        	$dtEnd = "  to_date('".$endDate." ".$initHour."','DD/MM/YYYY HH24:MI') ";
	        }	
		}

		$data = array(
					"idtopic" 		=> $idTopic,
					"idperson" 		=> $user,
					"title" 		=> "'".$title."'",
					"description" 	=> "'".$description."'",
					"dtcreate" 		=> $now,
					"dtstart" 		=> $dtStart,
					"dtend" 		=> $dtEnd,
					"sendemail"		=> "'".$sendEmail."'",
					"showin"		=> $show,
                    "emailsent"		=> 0
		);
		
		$db = new warning_model();
		$res = $db->insertWarning($data);
        
		if($res) echo "ok";
		else return false;
		
   	}

	public function modalEditWarning()
	{
		$smarty = $this->retornaSmarty();
		$database = $this->getConfig('db_connect');
        $id = $this->getParam('id');
        $db = new warning_model();
		$where = "AND a.idmessage = $id";
        $ret = $db->selectWarning($where);
      
		$select = $db->selectTopics();
		while (!$select->EOF) {
            $campos[] = $select->fields['idtopic'];
            $valores[] = $select->fields['title'];
            $select->MoveNext();
        }		
		
        $smarty->assign('topicids', $campos);
        $smarty->assign('topicvals', $valores);
        $smarty->assign('idtopic', $ret->fields['idtopic']);
		$smarty->assign('title_warning', $ret->fields['title_warning']);
		$smarty->assign('description', $ret->fields['description']);

		if ($database == 'mysqlt') {
			$smarty->assign('datestart', $this->formatDate($ret->fields['dtstart']));
			$smarty->assign('timestart', $this->formatHour($ret->fields['dtstart']));
		}elseif ($database == 'oci8po') {
			$dthourstart = explode(" ", $ret->fields['dtstart']);
			$smarty->assign('datestart', $dthourstart[0]);
			$smarty->assign('timestart', $dthourstart[1]);
		}	
		
		$smarty->assign('total', $ret->fields['total']);
		
		if($ret->fields['dtend'] == "0000-00-00 00:00:00" || !$ret->fields['dtend']){
			$smarty->assign('dateend', '');
			$smarty->assign('timeend', '');
			$smarty->assign('until', 'S');
		}else{
			if ($database == 'mysqlt') {
				$smarty->assign('dateend', $this->formatDate($ret->fields['dtend']));
				$smarty->assign('timeend', $this->formatHour($ret->fields['dtend']));
				$smarty->assign('until', 'N');
			}elseif ($database == 'oci8po') {
				$dthourend = explode(" ", $ret->fields['dtend']);
				$smarty->assign('dateend', $dthourend[0]);
				$smarty->assign('timeend', $dthourend[1]);
				$smarty->assign('until', 'N');
			}	
		}
		
		$smarty->assign('sendemail', $ret->fields['sendemail']);
		$smarty->assign('showin', $ret->fields['showin']);		
		$smarty->assign('id', $id);
        $smarty->display('modais/warnings/edit_warning.tpl.html');
	}

	public function warningEdit() {
    	
		$idTopic 		= $_POST['cmbTopicEdit'];
		$title 			= $_POST['txtTitleEdit'];
		$description 	= $_POST['txtDescriptionEdit'];
		$initDate 		= $_POST['validDateEdit'];
		$initHour 		= $_POST['validHourEdit'];
		$endDate 		= $_POST['validEndDateEdit'];
		$endHour 		= $_POST['validEndHourEdit'];
		$untilClosed 	= $_POST['chkUntilClosedEdit'];
		$sendEmail 		= $_POST['chkSendEmailEdit'];
		$show 			= $_POST['chkShowAlertEdit'];
		$id				= $_POST['id'];


		$database = $this->getConfig('db_connect');
        if ($database == 'oci8po') {
            $dtStart = "  to_date('".$initDate." ".$initHour."','DD/MM/YYYY HH24:MI') ";
        }else{
            $dtStart = $this->formatSaveDateHour($initDate." ".$initHour);
        }


		if ($database == 'mysqlt') {
			if($untilClosed == "S"){//Até ser encerrado
        		$dtEnd = "'0000-00-00 00:00:00'";
	        }else{
	        	$dtEnd = $this->formatSaveDateHour($endDate." ".$endHour);
	        }
		}elseif ($database == 'oci8po') {
			if($untilClosed == "S"){//Até ser encerrado
        		$dtEnd = "NULL";
	        }else{
	        	$dtEnd = "  to_date('".$endDate." ".$endHour."','DD/MM/YYYY HH24:MI') ";
	        }	
		}
		
		
		if(!$sendEmail) $sendEmail = "N";
		if(!$show) $show = "1";
		
		$data = array(
					"idtopic" 		=> $idTopic,
					"title" 		=> "'".$title."'",
					"description" 	=> "'".$description."'",
					"dtstart" 		=> $dtStart,
					"dtend" 		=> $dtEnd,
					"sendemail"		=> "'".$sendEmail."'",
					"showin"		=> $show
		);
		
		
				
		$db = new warning_model();
		$res = $db->updateWarning($data,$id);
        
		if($res) echo "ok";
		else return false;
   	}

    
    public function delete() {
        $id = substr($_POST['items'], 0, -1);
        $bd = new modules_model();
        $del = $bd->deleteModule("idmodule in ($id)");
    }

    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new modules_model();
        $ret = $bd->selectModuleData($id);
        $smarty->assign('id', $id);
        $smarty->assign('name', $ret->fields['name']);
        $smarty->display('modais/modules/modulesedit.tpl.html');
    }

    public function edit() {
        $id = $_POST['id_module'];
        $name = $_POST['nameEdit'];
		
		if(!$id || !$name) return false;
		
        $db = new modules_model();
        $updt = $db->updateModule($id, $name);
        if ($updt) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/modules/modulesdisable.tpl.html');
	}
	
    public function deactivate() {
        $id = $_POST['id'];
		if(!$id || !id == 1) return false;
        $bd = new modules_model();
        $dea = $bd->moduleDeactivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function activatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/modules/modulesactive.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new modules_model();
        $dea = $bd->moduleActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
}
?>