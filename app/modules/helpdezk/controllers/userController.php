<?php

    session_start();
class user extends Controllers {

    public $database;

    public function __construct(){
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function index() {

        $this->validasessao();
		$idtypeperson = $_SESSION['SES_COD_TIPO'];

		if($idtypeperson != 2) {			
			$url = $this->getConfig("hdk_url").'helpdezk/operator';
			die("<script> location.href = '".$url."'; </script>");
		}
		
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        //
        $dbCommon = new common();
        $bd = new home_model();
		// Menu buttons
		$showAdmBtn = $bd->showAdmBtn($cod_usu, $idtypeperson);
		if($showAdmBtn->fields['total_typeperson'] > 0 || $showAdmBtn->fields['total_person'] > 0){
			$smarty->assign('showadmbutton', 1);
		}

        $smarty->assign('showDashboard', true);

        /*
        $totPerm = $bd->getNumberPermPersonModule($cod_usu, 4);
        if ($totPerm > 0) {
            $smarty->assign('showerpbutton', 1);
        }
        */

        $rsModules = $dbCommon->getExtraModulesPerson($cod_usu,$idtypeperson);
        $aModules = array();
        while (!$rsModules->EOF) {

            $aModules[] = array('idmodule' => $rsModules->fields['idmodule'],
                                'path' => $rsModules->fields['path'],
                                'class' => $rsModules->fields['class'],
                                'varsmarty' => $smarty->get_config_vars($rsModules->fields['smarty'])
                               );
            $rsModules->MoveNext();
        }


        $smarty->assign(modules, $aModules) ;
         //


		$change_pass = $bd->getChangePass($cod_usu);
        $usu_login = $bd->selectUserLogin($cod_usu);
        $bd2 = new person_model();
        $where = "and tbp.idperson = '$cod_usu'";
        $persinfo = $bd2->selectPersonData($cod_usu);
        $usu_name = $persinfo->fields['name'];
        $email = $persinfo->fields['email'];
        $company = $persinfo->fields['company'];
        $department = $persinfo->fields['department'];
		$phone = $persinfo->fields['phone_number'];
		$branch = $persinfo->fields['branch_number'];
		$cel = $persinfo->fields['cel_phone'];
        $db = new user_model();
		$newRequests = $db->geRequestsCount($cod_usu,1);
		$inProgress = $db->geRequestsCount($cod_usu,3);
		$waitingApproval = $db->geRequestsCount($cod_usu,4);
        if ($waitingApproval > 0) {
            $smarty->assign('blink', "style='color: #FF0000; text-decoration: blink;'");
            $smarty->assign('approvealert', "show");
        } else {
            $smarty->assign('approvealert', '');
            $smarty->assign('blink', "");
        }
		$finishedReq = $db->geRequestsCount($cod_usu,5);
		$rejectedReq = $db->geRequestsCount($cod_usu,6);
        $smarty->assign('nom_usuario', $usu_login);



        //
        $dbHome = new home_model();
        $google2fa = $dbHome->getConfigValue('SES_GOOGLE_2FA');
        if (empty($google2fa)) { // if don't exists in hdk_tbconfig [old versions before 1.02]
            $google2fa = 0 ;
        }

        //
        if ( $_SESSION['SES_GOOGLE_2FA'] ) {
            if($this->getConfig("license") == '200701006')
            {
                $dbIndex = new index_model();
                $iddepartment = $dbIndex->getIdPersonDepartment($usu_login);
                if($iddepartment == '314') {
                    $google2fa = true;
                } else {
                    $google2fa = false;
                }
            }
            else
            {
                $google2fa = true;
            }
        } else {
            $google2fa = false;
        }

        $smarty->assign('have2fa',$google2fa) ;
        if ($google2fa != 0) {
            if( strlen($persinfo->fields['token']) == 16 ) {
                $token = true;
            } else {
                $token = false;
            }
        } else {
            $token = false ;
        }

        $smarty->assign('haveToken',$token);
        //

        if ($lang_default == 'pt_BR') {
            $dayName = date("l");
            $monthName = date("F");
            $date = $langVars[$dayName] . ", " . date("d") . " de " . $langVars[$monthName] . " de " . date("Y");
        } else {
            $date = date("l") . ", " . date("F") . " " . date("j") . date("S") . ", " . date("Y");
        }
		
		$db = new logos_model();
        $headerlogo = $db->getHeaderLogo();
		
		$smarty->assign('total_warnings', $this->numNewWarnings());
		$smarty->assign('headerlogo', $headerlogo->fields['file_name']);
        $smarty->assign('headerheight', $headerlogo->fields['height']);
        $smarty->assign('headerwidth', $headerlogo->fields['width']);
        $smarty->assign('date2', $date);
        $smarty->assign('user_name', $usu_name);
        $smarty->assign('department', $department);
        $smarty->assign('newreq', $newRequests);
        $smarty->assign('inprogress', $inProgress);
        $smarty->assign('waitingapproval', $waitingApproval);
        $smarty->assign('finished', $finishedReq);
        $smarty->assign('rejected', $rejectedReq);
        $smarty->assign('company', $company);
        $smarty->assign('email', $email);
        $smarty->assign('userid', $cod_usu);
		$smarty->assign('phone', preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $phone));
		$smarty->assign('branch', $branch);
		$smarty->assign('cel', preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $cel));
		$smarty->assign('changepass', $change_pass);
		if(!$_SESSION['SES_TIME_SESSION'])
			$smarty->assign('timesession', 600);
		else
			$smarty->assign('timesession', $_SESSION['SES_TIME_SESSION']);
		
		$smarty->assign('grid_user', $_SESSION['SES_PERSONAL_USER_CONFIG']['grid_user']);
		$smarty->assign('grid_user_width', $_SESSION['SES_PERSONAL_USER_CONFIG']['grid_user_width']);

        $smarty->assign('license', $_SESSION['SES_LICENSE']);
        $smarty->display('user.tpl.html');
    }
	
	public function numNewWarnings(){
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$idcompany = $_SESSION['SES_COD_EMPRESA'];
        $bd = new warning_model();
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $where = "AND (a.dtend > NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
        } elseif ($database == 'oci8po') {
            $where = "AND (a.dtend > SYSDATE OR a.dtend = '') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
        }
		$rsWarning = $bd->selectWarning($where);
		

        if ($database == 'mysqlt') {
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
        } elseif ($database == 'oci8po') {
            $total = $rsWarning->fields['rnum'];
            if(!$total) $total = 0;
        }

		while (!$rsWarning->EOF) {				
			if($_SESSION['SES_COD_TIPO'] == 2){//USER
				if($rsWarning->fields['total_company'] > 0){
					$checkCompany = $bd->checkCompany($rsWarning->fields['idtopic'], $idcompany);
					if($checkCompany->fields['check'] == 0){
						$total--;
						$rsWarning->MoveNext();
						continue;
					}
				}	
			}else{
				// by group				
				if($rsWarning->fields['total_group'] > 0){
					$checkGroup = $bd->checkGroup($rsWarning->fields['idtopic'], $_SESSION['SES_PERSON_GROUPS']);
					if($checkGroup->fields['check'] == 0){
						$total--;
						$rsWarning->MoveNext();
						continue;
					}
				}					
			}	
            $rsWarning->MoveNext();
        }		
		return $total;
	}

	public function numNewWarningsAjax(){
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$idcompany = $_SESSION['SES_COD_EMPRESA'];
        $bd = new warning_model();
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $where = "AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
        } elseif ($database == 'oci8po') {
            $where = "AND (a.dtend > SYSDATE AND a.dtstart <= SYSDATE OR a.dtend = '') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))";
        }
        $rsWarning = $bd->selectWarning($where);
		if ($database == 'mysqlt') {
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
        } elseif ($database == 'oci8po') {
            $total = $rsWarning->fields['rnum'];
            if(!$total) $total = 0;
        }
		while (!$rsWarning->EOF) {				
			if($_SESSION['SES_COD_TIPO'] == 2){//USER
				if($rsWarning->fields['total_company'] > 0){
					$checkCompany = $bd->checkCompany($rsWarning->fields['idtopic'], $idcompany);
					if($checkCompany->fields['check'] == 0){
						$total--;
						$rsWarning->MoveNext();
						continue;
					}
				}	
			}else{
				// by group				
				if($rsWarning->fields['total_group'] > 0){
					$checkGroup = $bd->checkGroup($rsWarning->fields['idtopic'], $_SESSION['SES_PERSON_GROUPS']);
					if($checkGroup->fields['check'] == 0){
						$total--;
						$rsWarning->MoveNext();
						continue;
					}
				}					
			}	
            $rsWarning->MoveNext();
        }		
		echo $total;
	}
	
    public function myrequest() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        $smarty->display('myrequest.tpl.html');
    }
	
	public function checkapproval(){
		$this->validasessao();
		$cod_usu = $_SESSION['SES_COD_USUARIO'];
		if($_SESSION['SES_OPEN_NEW_REQUEST']){
			$db = new user_model();
			
			if($_SESSION['SES_LICENSE'] == 201301014 && $_SESSION['SES_COD_EMPRESA'] == 93){ //SE FOR COINPEL E EMPRESA "SANEP"
        		$reqs = $db->getWaitingApprovalRequestsCountByDate($cod_usu);
        		$total = 0;
				while (!$reqs->EOF) {
					$dt_req = strtotime("+2 day", strtotime($reqs->fields['dt_approval']));
					$now = strtotime(date("Y-m-d H:i:s"));
					if($dt_req <= $now){
						$total++;
					}					
					$reqs->MoveNext();
        		}				
			}else{
		        $total = $db->getWaitingApprovalRequestsCount($cod_usu);
			}
			echo $total;
		
		}else{
			echo 0;
		}
	}

    public function json() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
		
        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        $COD_STATUS = $_POST['COD_STATUS'];
		
        if ($COD_STATUS != "undefined") {
            if ($COD_STATUS > 0) {
                $WHERESTATUS = " AND b.idstatus_source =" . $COD_STATUS . " ";
            }
        }
        elseif($this->getParam('status')){
			$WHERESTATUS = " AND b.idstatus_source =" . $this->getParam('status') . " ";
		}
		else {
            $WHERESTATUS = " ";
        }

        $TIP_VISUALIZACAO = isset($_POST['tipconsulta']) ? $_POST['tipconsulta'] : 0;

        $prog = "";
        $path = "";
		$sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];
        $page = $_POST['page'];
        $rp = $_POST['rp'];
		if(!$sortname)
			$sortname = "code_request";
        if (!$sortorder)
            $sortorder = 'desc';
        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);
        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $where = "";

        if ($query) {
            switch ($qtype) {
                case 'code_request':
                    $where = " AND a.$qtype = $query ";
                    break;
                case 'subject':
                    $where = " AND a.$qtype LIKE '%$query%' ";
                    break;
                case 'description':
                    $where = " AND a.$qtype LIKE '%$query%' ";
                    break;
                default:
                    $where = "";
                    break;
            }
        }


        if ($TIP_VISUALIZACAO == 2) {
            $sqltip = "tbderpartment_has_person dep,";
        }
       
        $bd = new user_model();
        if ($this->database == 'oci8po') {
            $entry_date = " to_char(a.entry_date,'DD/MM/YYYY HH24:MI') entry_date " ;
            $expire_date = " to_char(a.expire_date,'DD/MM/YYYY HH24:MI')expire_date , a.expire_date  AS expire_date_color" ;
        }
        else
        {
            $entry_date = " DATE_FORMAT(a.entry_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as entry_date" ;
            $expire_date = " DATE_FORMAT(a.expire_date, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') as expire_date, a.expire_date AS expire_date_color" ;
        }

        $rsSolicitacao = $bd->getRequest($entry_date, $expire_date,$filtrotip, $where, $WHERESTATUS, $sortname, $sortorder, $start, $rp, $status, $cod_usu);

        $rstotal = "";
        $total = "";

        if ($this->database == 'oci8po'){
            $total = $rsSolicitacao->RecordCount() ;
        }else{
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
        }

		
        //** SELECIONANDO OS DADOS PARA A LEGENDA! - sistema antigo, 
        $rsPrioridade = $bd->getPriority();
        $totalRows_rsPrioridade = $rsPrioridade->RecordCount();		
		
        $rsStatus = $bd->getStatus();
		
		/*	
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: text/x-json");
		*/
		
        $data['page'] = $page;
        $data['total'] = $total;
        $pathimg = "..";


        $COD_SOL_ANTERIOR = 0;
        while (!$rsSolicitacao->EOF) {

            if ($COD_SOL_ANTERIOR != $rsSolicitacao->fields['code_request']) {
                // Test to see if I put the star icon
                $row = $rsSolicitacao->fields;
                $icones = " ";
                if ($rsSolicitacao->fields['flag_opened'] == 1 && $rsSolicitacao->fields['status'] != 1) {
                    $icones .= "<img src='" . path . "/app/themes/" . theme . "/images/ico_estrela.gif' title='novo apontamento' width='14' height='14'>";
                }
            }
            // Tests whether the request has attachments!
			$totatt = $rsSolicitacao->fields['totatt'];
            if ($totatt > 0) {
                $anexo = "<img src='" . path . "/app/themes/" . theme . "/images/ico_anexos.gif' width='7' height='14' title='Total de anexos: " . $totatt . "'>";
            } else {
                $anexo = "";
            }
			
            //setando as variveis para o metodo pintasolicitacao
            if ($rsSolicitacao->fields['idgroup'])
                $cod_grupo = $rsSolicitacao->fields['idgroup'];
            else
                unset($cod_grupo);

            $cod_responsavel = $rsSolicitacao->fields['id_in_charge'];
            $texto = $rsSolicitacao->fields['code_request'];
            $var = $this->pintasolicitacao($rsSolicitacao->fields['idgroup'], $cod_responsavel, $rsSolicitacao->fields['owner'], $cod_grupo_usuario, $texto);
            $link = $this->montalink($prog, $path, $rsSolicitacao->fields['code_request'], $var, $TIP_VISUALIZACAO);
            $Solici = $link;

            //$link = $this->montalinkassunto($prog, $path, $rsSolicitacao->fields['code_request'], addslashes($rsSolicitacao->fields['subject']), $TIP_VISUALIZACAO);

            if ( $this->getConfig('license') == '201601001') {
                //Key
                $key =  'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282';

                // Test ticket status
                if ($rsSolicitacao->fields['status'] != 1){
                    $subject = $this->mc_decrypt($rsSolicitacao->fields['subject'], $key) ;
                } else {
                    $subject = "" ;
                }

            } else {
                $subject = $rsSolicitacao->fields['subject'] ;
            }

            $link = $this->montalinkassunto($prog, $path, $rsSolicitacao->fields['code_request'], addslashes($subject), $TIP_VISUALIZACAO);

            $Assunto = $link;

            $var = $this->pintadata($rsSolicitacao->fields['dat_vencimento_atendimento2'], $rsSolicitacao->fields['expire_date'], $rsSolicitacao->fields['status']);
            $Vencimento = $var;

            $var = $this->pintastatus($rsSolicitacao->fields['color_status'], $rsSolicitacao->fields['statusview']);
            $status = $var;

            $funcaotime = new operatorview_model();

            $stat = $rsSolicitacao->fields['status'];
            if ($_SESSION['SES_HIDE_PERIOD'] == 0) {
                $expire = $rsSolicitacao->fields['expire_date'] ;
            } else {
                if ($stat == 1) {
                    $expire = $langVars['Not_available_yet'];
                } else {
                    $expire = $rsSolicitacao->fields['expire_date'] ;
                }
            }
			
            $rows[] = array(
                "id" => $rsSolicitacao->fields['code_request'],
                "cell" => array(
                    $icones.$anexo
                    , $Solici
                    , $rsSolicitacao->fields['entry_date']
                    , $rsSolicitacao->fields['personname']
                    , $Assunto
                    , $expire
                    , $rsSolicitacao->fields['in_charge']
                    , $status
                )
            );
            $COD_SOL_ANTERIOR = $rsSolicitacao->fields['code_request'];
            $rsSolicitacao->MoveNext();
        }

        if ($total == 0) {
            $data['rows'] = 0;
        } else {
            $data['rows'] = $rows;
        }


        $data['params'] = $_POST;

        echo json_encode($data);

    }

    function montalink($prog, $path, $chave, $mostra, $TIP_VISUALIZACAO = null) {
        //$ret = "<a href='solicita_detalhes.php?COD_SOLICITACAO=" . $chave . "&TIP_VISUALIZACAO=" . $TIP_VISUALIZACAO . "' class='linhas'> " . $mostra . " </a>";
        $ret = "<a href='#/user/viewrequest/id/" . $chave . "' class='linhas'>" . $chave . "</a>";
        return $ret;
    }

    function montalinkassunto($prog, $path, $chave, $mostra, $TIP_VISUALIZACAO = null) {
        if($_SESSION['SES_LICENSE'] == 201001012){
            $ret = base64_encode($mostra) ;
        } else {
            $ret = "<a href='javascript:;' class='linhas' onclick=\"$('#content2').load('user/viewrequest/id/" . $chave . "')\">" . $mostra . "</a>";
        }

        return $ret;
    }

    function pintastatus($cor, $texto) {
        $ret = "<span style='color:" . $cor . ";' > " . $texto . " </span>";
        return $ret;
    }

    function pintadata($datateste, $dataformatada, $codstatus) {
        if ($datateste == date("Y-m-d H:i"))
            $ret = "<span style='color: #0000ff; ' ><b> " . $dataformatada . "</b> </span>";
        else if ($datateste <= date("Y-m-d H:i") and $codstatus == 1) //arrumar essa droga de data com segundos
            $ret = "<span style='color: #ff0000; ' ><b> " . $dataformatada . "</b> </span>";
        else if ($datateste <= date("Y-m-d H:i"))
            $ret = "<span style='color: #ff0000;' > " . $dataformatada . " </span>";
        else
            $ret = $dataformatada;
        return $ret;
    }

    function pintasolicitacao($cod_grupo, $cod_responsavel, $cod_usuario, $cod_grupo_usuario, $texto) {

        if ($cod_grupo && in_array($cod_grupo, $cod_grupo_usuario))
            $ret = "<span style='color: #0012DF; border-bottom: 1px solid #0012DF;' > " . $texto . " </span>";
        else if (!$cod_grupo && $cod_responsavel == $cod_usuario)
            $ret = "<span style='color: #DF6300; border-bottom: 1px solid #DF6300;' > " . $texto . " </span>";
        else
            $ret = "<span style='border-bottom: 1px solid black;' > " . $texto . "</span>";
        return $ret;
    }

    function responsavel($COD_SOLICITACAO) {
        $bd = new user_model();

        $rsResponsavel = $bd->getInCharge($COD_SOLICITACAO);

        if (!$rsResponsavel->EOF) {
            //se tiver um grupo respons?vel 
            if ($rsResponsavel->fields["idgroup"] > 0) {

                $rsGrupo = $bd->getGroup($rsResponsavel->fields["idgroup"]);
                return $rsGrupo->fields["name"];
                $rsGrupo->Close();
            }
            //Se tiver um atendente respons?vel viewreqere
            else {


                $rsAtendente = $bd->getUser($rsResponsavel->fields["id_in_charge"]);
                return $rsAtendente->fields["name"];
                $rsAtendente->Close();
            }
        } else {
            return "N?o associado";
        }
        $rsResponsavel->Close();
    }

    public function viewrequest() {
        $this->validasessao();
        session_start();
		
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $id = $this->getParam('id');
		
        $bd = new operatorview_model();
        $req = $bd->getRequestData($id);		
        $idperson = $_SESSION['SES_COD_USUARIO'];
		$idowner = $req->fields['idperson_owner'];
		
		if($_SESSION['SES_COD_TIPO'] != 2 && $idperson != $idowner) {			
			$url = $this->getConfig('hdk_url').'helpdezk/operator#/operator/viewrequest/id/'.$id;
			die("<script> location.href = '".$url."'; </script>");
		}
		
		//Negar acesso caso não seja ele o dono da solicitação
		if($idperson != $idowner) die($langVars['Access_denied']);
		
        //$namecreator = $bd->getNameCreator($req->fields['idperson_creator'], $id);
		$namecreator = $req->fields['name_creator'];
        $owner = $req->fields['personname'];
        $department = $req->fields['department'];
        $source = $req->fields['source'];
        $prorrogs = $req->fields['extensions_number'];
        $incharge = $req->fields['id_in_charge'];
        $inchargename = $req->fields['in_charge'];
        $status = $req->fields['status'];
        $iddepartment = $req->fields['iddepartment'];
        $company = $bd->getCompanyName($iddepartment);
        $entry_time = $bd->getTime($req->fields['entry_date'], $hour_format);
        $entry_date = $this->formatDate($req->fields['entry_date']);
        $idarea = $req->fields['idarea'];
        $idtype = $req->fields['idtype'];
        $iditem = $req->fields['iditem'];
        $os = $req->fields['os_number'];
        $serial = $req->fields['serial_number'];
        $label = $req->fields['label'];		
        $idstatus = $req->fields['idstatus'];
        $idservice = $req->fields['idservice'];
        $idpriority = $req->fields['idpriority'];
        $idreason = $req->fields['idreason'];
        $idway = $req->fields['idattendance_way'];

        if ( $this->getConfig('license') == '201601001') {
            //Key
            $key =  'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282';
            // Test ticket status
            if ($req->fields['idstatus'] != 1){
                $subject = $this->mc_decrypt($req->fields['subject'], $key) ;
                $description = $this->mc_decrypt($req->fields['description'], $key) ;
            } else {
                $subject = "Encryped field" ;
            }
        } else {
            $subject = $req->fields['subject'];
            $description = $req->fields['description'];
        }


        $entrydate = $entry_date . " " . $entry_time;
		
		if($req->fields['flag_opened'] == 1)
			$bd->updateFlag($id, 0);
		
        $db = new requestinsert_model();
        $select2 = $db->selectArea();
        while (!$select2->EOF) {
            $campos2[] = $select2->fields['idarea'];
            $valores2[] = $select2->fields['name'];
            $select2->MoveNext();
        }
        $smarty->assign('areaids', $campos2);
        $smarty->assign('areavals', $valores2);
        $select3 = $db->selectType($idarea);
        while (!$select3->EOF) {
            $campos3[] = $select3->fields['idtype'];
            $valores3[] = $select3->fields['name'];
            $select3->MoveNext();
        }
        $smarty->assign('typeids', $campos3);
        $smarty->assign('typevals', $valores3);
        $select4 = $db->selectItem($idtype);
        while (!$select4->EOF) {
            $campos4[] = $select4->fields['iditem'];
            $valores4[] = $select4->fields['name'];
            $select4->MoveNext();
        }
        $smarty->assign('itemids', $campos4);
        $smarty->assign('itemvals', $valores4);
        $select5 = $db->selectService($iditem);
        while (!$select5->EOF) {
            $campos5[] = $select5->fields['idservice'];
            $valores5[] = $select5->fields['name'];
            $select5->MoveNext();
        }
        $smarty->assign('serviceids', $campos5);
        $smarty->assign('servicevals', $valores5);
        $select6 = $db->selectPriorities();
        while (!$select6->EOF) {
            $campos6[] = $select6->fields['idpriority'];
            $valores6[] = $select6->fields['name'];
            $select6->MoveNext();
        }
        $smarty->assign('priorityids', $campos6);
        $smarty->assign('priorityvals', $valores6);
        $select7 = $db->selectReason($idtype);
        while (!$select7->EOF) {
            $campos7[] = $select7->fields['idreason'];
            $valores7[] = $select7->fields['reason'];
            $select7->MoveNext();
        }
        $smarty->assign('reasonids', $campos7);
        $smarty->assign('reasonvals', $valores7);
        $select8 = $db->selectWay();
        while (!$select8->EOF) {
            $campos8[] = $select8->fields['idattendanceway'];
            $valores8[] = $select8->fields['way'];
            $select8->MoveNext();
        }
        $smarty->assign('wayids', $campos8);
        $smarty->assign('wayvals', $valores8);
        $smarty->assign('expiry_view', "style='display: none;'");
        $smarty->assign('expiry', "1");
        $selectattach = $bd->selectAttach($id);
        if ($selectattach->fields) {
            $hasattach = 1;
            $countatt = $bd->countAttachs($id);
            while (!$selectattach->EOF) {
                $filename = $selectattach->fields['file_name'];
                $ext = strrchr($filename, '.');
                if ($custom_attach_path) {
                    $custom_attach_path = str_replace('/', '-', $custom_attach_path);
                    $attach[$filename] = "<a href='javascript:;' onclick=\"openDownloadPopUP('" . $custom_attach_path . "', '" . $selectattach->fields['idrequest_attachment'] . $ext . "','$filename');\" class='file' name='" . $path_default . $custom_attach_path . $filename . "'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='15px' width='15px' /><span class='icontext'>" . "  " . $filename . "</span></a>";
                } else {
					$attach[$filename] = "<a href='downloads/getFile2/id/". $selectattach->fields['idrequest_attachment'] ."/type/request' class='file'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='15px' width='15px' /><span class='icontext'>" . "  " . $filename . "</span></a>";
                }
                $selectattach->MoveNext();
            }
            $attach = implode(" ", $attach);
        } else {
            $hasattach = 0;
        }
        $typeperson = $_SESSION['SES_TYPE_PERSON'];
        $notes = $bd->getRequestNotes($id);
        $notetable = "<table border='0' cellpadding='0' cellspacing='0' class='notetable'>";
		$notetable .= "
				<colgroup>
					<col width='40'/>
					<col width='40'/>
					<col width='40'/>
					<col />
				</colgroup>
		";
        while (!$notes->EOF) {
        	
			if($notes->fields['idtype'] != '2'){
			
	            $notetable.= "<tr>";
	            $idnote = $notes->fields['idnote'];
				
				if($idstatus == 3){
		            if ($notes->fields['idtype'] != '3' && $_SESSION['SES_IND_DELETE_NOTE'] == '1' && $_SESSION['SES_COD_USUARIO'] == $notes->fields['idperson']) 
		            {
		                $ico_del = "<a href='javascript:;' onclick=\"deleteNote('$idnote', '$id', '$typeperson');\"><img src='" . path . "/app/themes/" . theme . "/images/delete_new.png' height='15px' width='15px' title='" . $langVars['Delete'] . "'></a>";
		            } else {
		                $ico_del = "";
		            }
				}else{
					$ico_del = "";
				}
	           
	            //CALLBACK
	            if ($notes->fields['callback']) {
	                $ico_note = "<img src='" . path . "/app/themes/" . theme . "/images/ico_callback.gif' alt='Callback' />";
	            } 
				//USER
	            elseif ($notes->fields['idtype'] == '1' && $notes->fields['idperson'] == $req->fields['idperson_owner']) {
					$ico_note = "<img src='" . path . "/app/themes/" . theme . "/images/user_25.png' alt='" . $langVars['User'] . "' />";
				}
				//OPERATOR
				elseif($notes->fields['idtype'] == '1'){
					$ico_note = "<img src='" . path . "/app/themes/" . theme . "/images/atendimento_25.png' alt='" . $langVars['Operator'] . "' />";
				}
				//SYSTEM
				else{
					$ico_note = "<img src='" . path . "/app/themes/" . theme . "/images/system_25.png' height='30px' width='30px' alt='" . $langVars['System'] . "' />";				
				}
				
	            if($notes->fields['idnote_attachment'] > 0){
	                $filename = $notes->fields['file_name'];
	                $ext = strrchr($filename, '.');
	                if ($custom_attach_path) {
	                    $custom_attach_path = str_replace('/', '-', $custom_attach_path);
	                    $attachicon = "<a href='javascript:;' onclick=\"openDownloadPopUP('" . $custom_attach_path . "', '" . $notes->fields['idnote_attachment'] . $ext . "','$filename');\" class='file' name='" . $this->getConfig('path_default') . $custom_attach_path . $filename . "'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='24px' width='24px' title='" . $langVars['Attachment'] . "'></a>";
					} else {
	                    $attachicon = "<a href='downloads/getFile2/id/". $notes->fields['idnote_attachment'] ."/type/note' class='file'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='24px' width='24px' title='" . $langVars['Attachment'] . "'></a>";
	                }
	                
	            } else {
	                $attachicon  = "";
	            }
	            $notedescription = $notes->fields['description'];
	            $notedate = $notes->fields['entry_date'];
	            $notenameper = $notes->fields['idperson'];
	            $notenameper = $bd->getUserName($notenameper);
	            $ipadress = $notes->fields['ip_adress'];
	            $notetime = $bd->getTime($notedate, $hour_format);
				
				$notetable.= "<td align='center' valign='middle'> $ico_del </td>";
	            $notetable.= "<td align='center' valign='middle'> $attachicon </td>";
	            $notetable.= "<td align='center' valign='middle'> $ico_note</td>";
	            
				
				if ($notes->fields['minutes'] != 0) {
					$timeexp = "<strong>".$langVars['Time_exp'].": </strong>" . $this->formatDate($notedate) . " " . $notes->fields['start_hour'] . " - " . $notes->fields['finish_hour'] . " (" . $notes->fields['diferenca'] . ")";				
					$timeexpnote = "<span class='block'>".$timeexp."</span>";
				}else{
					$timeexp = "";
					$timeexpnote = "";
				}
	            
	            $notetable.= "
	            <td>
		            <span class='block'><strong>" . $this->formatDate($notedate) . " " . $notetime . "</strong> [<i>" . $notenameper . "</i>]</span>
		            <span class='block'>" . $notedescription . "</span>
		            <span class='block'><strong>".$langVars['IP_adress'].":</strong> " . $ipadress . "</span>            
		            ".$timeexpnote."
	            </td>";
	            $notetable.= "</tr>";
			}
            $notes->MoveNext();
        }
        $notetable.= "</table>";
        if ($lang_default == 'en_US') {
            $hour_format = "%h:%i";
        }
        $expire_hour = $bd->getTime($expire_date, $hour_format);
        $hour_format2 = "%p";
        $hour_label = $bd->getTime($expire_date, $hour_format2);
        if ($lang_default == 'pt_BR') {
            $smarty->assign('hour_label', '');
        } else {
            $smarty->assign('hour_label', $hour_label);
        }
        $email = $req->fields['email'];
        $smarty->assign('email', $email);
        $smarty->assign('now', $now);
        $smarty->assign('idperson', $idperson);
        $smarty->assign('notetable', $notetable);
        $smarty->assign('request_code', $id);
        $smarty->assign('owner', $owner);
        $smarty->assign('department', $department);
        $smarty->assign('idstatus', $idstatus);
        $smarty->assign('status', $status);
        $smarty->assign('prorrogation', '');
        $smarty->assign('source', $source);
        $smarty->assign('entry', $entrydate);
        $smarty->assign('company', $company);
        $smarty->assign('idarea', $idarea);
        $smarty->assign('idtype', $idtype);
        $smarty->assign('iditem', $iditem);
        $smarty->assign('idservice', $idservice);
        $smarty->assign('idway', $idway);
        $smarty->assign('idreason', $idreason);
        $smarty->assign('idpriority', $idpriority);
        $smarty->assign('expire_date', $expire_date2);
        $smarty->assign('expire_hour', $expire_hour);
        $smarty->assign('checkedassume', $checkedassume);
        $smarty->assign('obrigatorytime', $obrigatorytime);
        $smarty->assign('incharge', $incharge);
        $smarty->assign('inchargename', $inchargename);
        $smarty->assign('os', $os);
        $smarty->assign('serial_num', $serial);
        $smarty->assign('subject', $subject);
        $smarty->assign('description', $description);
		if($_SESSION['SES_IND_EQUIPMENT'] == 1) {
			$smarty->assign('have_equipment',	'1');
			$smarty->assign('os_number', $os);
			$smarty->assign('serial_number', $serial);
			$smarty->assign('label', $label);
		}		
		if($idstatus == 2){
			$idswitch_status = 2;
		}else{
			$idstatus_source = $bd->getIdStatusSource($idstatus);
			$idswitch_status = $idstatus_source->fields['idstatus_source'];	
		}			
		
		switch($idswitch_status){				
			case "1": //NEW				
				$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'1');
				$smarty->assign('displayevaluate',  '0');
				$smarty->assign('displaynote',		'0');
				$smarty->assign('displayprint',     '1');
				break;
			case "2": //REPASSED					
				$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'0');
				$smarty->assign('displayevaluate',  '0');
				$smarty->assign('displaynote',		'0');
				$smarty->assign('displayprint',     '1');
				break;
			case "3": //ON ATTENDANCE
				$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'0');
				$smarty->assign('displayevaluate',  '0');
				$smarty->assign('displaynote',		'1');
				$smarty->assign('displayprint',     '1');
				break;
			case "4": //WAITING FOR APP				
				$q = 0;
		        if ($_SESSION['SES_EVALUATE'] == 1) {
		            $eval = "";
		            $questions = $bd->getQuestions();
		            while (!$questions->EOF) {
		                $idquestion = $questions->fields['idquestion'];
		                $question = $questions->fields['question'];
		                $eval.= "<p><strong>" . $question . "</strong></p><ul class='lstEval clearfix mtb10'>";
		                $answers = $bd->getAnswers($idquestion);
						$sel = 0;
						$chk = 0;
		                while (!$answers->EOF) {
		                	
		                	if($answers->fields['checked']==1){ $checked = "checked='checked'"; $chk = 1;}
							else {
								if(count($answers->fields) == $sel+1 && $chk == 0){
									$checked = "checked='checked'";
								}else{
									$checked = "";	
								}
							}
		                    $idanswer = $answers->fields['idevaluation'];
		                    $answer = $answers->fields['name'];
		                    $ico = $answers->fields['icon_name'];
		                    $eval.= "
		                    <li>
		                    	<label for='eval$idanswer'><input type='radio' $checked value='$idanswer' name='Answer$q' id='eval$idanswer'/>
		                    	<img src=" . path . "/app/uploads/icons/". $ico . " height='18' /> $answer</label>
		                    </li>";
							
		                    $sel++;
		                    $answers->MoveNext();
		                }
		                $eval.= "</ul>";
		                $q++;
		                $questions->MoveNext();
		            }
					$smarty->assign('evaluationform', $eval);
		        }
			
				$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'0');
				if ($_SESSION['SES_EVALUATE'] == 1 && $q != 0) {
                    $smarty->assign('displayevaluate',  '1');
					$smarty->assign('displayprint',     '0');
					$smarty->assign('numQuest',  $q);
                } else {
                    $smarty->assign('displayevaluate',  '0');
					$smarty->assign('displayprint',     '1');
					$smarty->assign('evaluationform', 	 '');
                }
				$smarty->assign('displaynote',		'0');
				break;
			case "5": //FINISHED
				if($_SESSION['SES_IND_REOPEN_USER'] == 1)
					$smarty->assign('displayreopen',	'1');
				else
					$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'0');
				$smarty->assign('displayevaluate',  '0');
				$smarty->assign('displaynote',		'0');
				$smarty->assign('displayprint',     '1');
				break;
			case "6": //REJECTED
				$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'0');
				$smarty->assign('displayevaluate',  '0');
				$smarty->assign('displaynote',		'0');
				$smarty->assign('displayprint',     '1');
				break;
			default:
				$smarty->assign('displayreopen',	'0');
				$smarty->assign('displaycancel',  	'0');
				$smarty->assign('displayevaluate',  '0');
				$smarty->assign('displaynote',		'0');
				$smarty->assign('displayprint',     '1');
				break;
		}

			
					
		$data = new person_model();
		$rs = $data->getOperatorAuxCombo($id,'in');
        while (!$rs->EOF) {
			$aux[] = $rs->fields['name']; 
            $rs->MoveNext();
        }
		$smarty->assign('usersaux', $aux);
		$smarty->assign('numusersaux', count($aux));

        $smarty->assign('hasattach', $hasattach);
        $smarty->assign('attach1', $attach);
        $smarty->assign('creator', $namecreator);
        $smarty->display('viewrequest_user.tpl.html');
    }

    public function cancelrequest() {
        $this->validasessao();
        extract($_POST);
		$smarty = $this->retornaSmarty();
        $db = new user_model();
        $status = '11';
        $del = $db->updateLog($code, $status, $person);
        $i = 0;
        if ($del) {
            $i++;
        } else {
            return false;
        }
        $del2 = $db->cancelRequest($code, $status);
        if ($del2) {
            $i++;
        } else {
            return false;
        }
        
        $langVars = $smarty->get_config_vars();
        $bd2 = new operatorview_model();
		$ipadress = $_SERVER['REMOTE_ADDR'];
        $callback = '0';
        $idtype = '3';
        $public = '1';
        $note = "<p><b><span style=\"color: #FF0000;\">" . $langVars['Request_canceled'] . "</span></b></p>";
        if ($this->database == 'oci8po') {
            $date = "sysdate";
        }
        else
        {
            $date = "now()";
        }
        $insNote = $bd2->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
        if ($insNote) {
            $i++;
        } else {
            return false;
        }
		

        if ($i == 3) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function addnote() {
        $this->validasessao();
        extract($_POST);
        $bd = new operatorview_model();
        if ($this->database == 'oci8po') {
            $date = "sysdate";
        }
        else
        {
            $date = "now()";
        }
        $serviceval = "NULL";
        $public = '1';
        $typenote = '1';
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $callback = '0';
		$idperson = $_SESSION['SES_COD_USUARIO'];
        $execdate = "0000-00-00 00:00:00";
        if($idanexo < 1){
            $idanexo = 'NULL';
        }
        $note = addslashes($note);
        $ins = $bd->insertNote($code, $idperson, $note, $date, 0, 0, 0, $execdate, 0, $serviceval, $public, $typenote, $ipadress, $callback, $idanexo);
        if(!$ins){
            return false;
        }
        if ($ins) {       
            if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['USER_NEW_NOTE_MAIL'] == '1') {
                $this->sendEmail('operator_note', $code);
            }
            echo "OK";
        } else {
            return false;
        }
    }
	
	public function evaluate(){
		session_start();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $this->validasessao();
		
		$ev = new evaluation_model();
		$bd = new operatorview_model();
		$bd->BeginTrans();
        $person = $_SESSION['SES_COD_USUARIO'];
		$ipadress = $_SERVER['REMOTE_ADDR'];
		$code = $_POST['code_request'];

		switch ($_POST['approve']) {
			case 'A':
				$status = '5';
		        $reopened = '0';
		        $inslog = $bd->changeRequestStatus($status, $code, $person);
		        if (!$inslog) {
		            $bd->RollbackTrans();
		            return false;
		        }
				
		        $callback = '0';
		        $idtype = '3';
		        $public = '1';
		        $note = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
                if ($this->database == 'oci8po') {
                    $date = "sysdate";
                }
                else
                {
                    $date = "now()";
                }
		        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
		        if (!$insNote) {
		        	$bd->RollbackTrans();
		            return false;
		        }
				
		        $changeStat = $bd->updateReqStatus($status, $code);
		        if (!$changeStat) {
					$bd->RollbackTrans();
		            return false;
		        }				
				
				$clearEval = $bd->clearEvaluation($code);
				if (!$clearEval) {
					$bd->RollbackTrans();
		            return false;
		        }

                if ($this->database == 'oci8po') {
                    $date = "sysdate";
                }
                else
                {
                    $date = "now()";
                }

				for($i = 0; $i <= $_POST['numQuest']-1; $i++){					
					$idAnswer = $_POST['Answer'.$i];
					$ins = $bd->insertEvaluation($idAnswer, $code, $date);
					if (!$ins) {
						$bd->RollbackTrans();
			            return false;
			        }
				}
				
				$rmToken = $ev->removeTokenByCode($code);
				if(!$rmToken){
					$bd->RollbackTrans();
			        return false;
				}

                $ud = $bd->updateDate($code, "approval_date");
                if(!$ud){
                    $bd->RollbackTrans();
                    return false;
                }
				
				/*if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['FINISH_MAIL'] == '1') {
	                $this->sendEmail('close', $code);
	            }*/

               
                if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['EM_EVALUATED']) {
                    $this->sendEmail('afterevaluate', $code);
                }
               

				
				
				$bd->CommitTrans();
	            echo "OK";
				
				break;
			
			case 'N':				
		        $status = '3';
		        $reopened = '1';
		        $inslog = $bd->changeRequestStatus($status, $code, $person);
		        if (!$inslog) {
		            $bd->RollbackTrans();
			        return false;
		        }
				
		        $callback = '0';
		        $idtype = '3';
		        $public = '1';
		        $note = "<p><b><span style=\"color: #FF0000;\">" . $langVars['Request_not_approve'] . "</span></b></p>";
				$note .= "<p><strong>" . $langVars['Reason'] . ":</strong> " . nl2br($_POST['approveobs']) . "</p>";
                if ($this->database == 'oci8po') {
                    $date = "sysdate";
                }
                else
                {
                    $date = "now()";
                }
		        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
		        if (!$insNote) {
		            $bd->RollbackTrans();
			        return false;
		        }
				
		        $changeStat = $bd->updateReqStatus($status, $code);
		        if (!$changeStat) {
		            $bd->RollbackTrans();
			        return false;
		        }
				
				$rmToken = $ev->removeTokenByCode($code);
				if(!$rmToken){
					$bd->RollbackTrans();
			        return false;
				}
               
                if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['REQUEST_REOPENED'] == '1') {
                    $this->sendEmail('reopen', $code);
                }

	            echo "OK";
				$bd->CommitTrans();
				break;
			
			case 'O':
				$status = '5';
		        $reopened = '0';
		        $inslog = $bd->changeRequestStatus($status, $code, $person);
		        if (!$inslog) {
		            $bd->RollbackTrans();
		            return false;
		        }


		        $callback = '0';
		        $idtype = '3';
		        $public = '1';
		        $note = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
				$note .= "<p><strong>" . $langVars['Observation'] . ":</strong> " . nl2br($_POST['approveobs']) . "</p>";
                if ($this->database == 'oci8po') {
                    $date = "sysdate";
                }
                else
                {
                    $date = "now()";
                }
		        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
		        if (!$insNote) {
		        	$bd->RollbackTrans();
		            return false;
		        }

		        $changeStat = $bd->updateReqStatus($status, $code);
		        if (!$changeStat) {
					$bd->RollbackTrans();
		            return false;
		        }

				$clearEval = $bd->clearEvaluation($code);
				if (!$clearEval) {
					$bd->RollbackTrans();
		            return false;
		        }

                if ($this->database == 'oci8po') {
                    $date = "sysdate";
                }
                else
                {
                    $date = "now()";
                }

				for($i = 0; $i <= $_POST['numQuest']-1; $i++){					
					$idAnswer = $_POST['Answer'.$i];
					$ins = $bd->insertEvaluation($idAnswer, $code, $date);
					if (!$ins) {
						$bd->RollbackTrans();
			            return false;
			        }
				}

				$rmToken = $ev->removeTokenByCode($code);
				if(!$rmToken){
					$bd->RollbackTrans();
			        return false;
				}

                $ud = $bd->updateDate($code, "approval_date");
                if(!$ud){
                    $bd->RollbackTrans();
                    return false;
                }
				
				if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['FINISH_MAIL'] == '1') {
	                $this->sendEmail('close', $code);
	            }
				
				if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['EM_EVALUATED']) {
	                $this->sendEmail('afterevaluate', $code);
	            }
                
				$bd->CommitTrans();
	            echo "OK";
				
				break;
			
			default:
				
				break;
		}		
	}

	public function deletenote() {
        $this->validasessao();
		$person = $_SESSION['SES_COD_USUARIO'];
        extract($_POST);
		$db = new operatorview_model();
		$db->BeginTrans();

		$check = $db->getNote($idnote);
		if(!$check){
			$db->RollbackTrans();
			return false;
		}		
		$idperson_note = $check->fields['idperson'];
		$idattach = $check->fields['idnote_attachment'];
        $file_name = $check->fields['file_name'];
		
		if($idperson_note == $person){
			
			$del = $db->deleteNote($idnote);
	        if ($del) {
	        	if($idattach){	        		
					$del_att = $db->deleteAttachNote($idattach);
					if(!$del_att){
						$db->RollbackTrans();
						return false;
					}else{
						$exp = explode(".",$file_name);
						$ext = $exp[count($exp)-1];
						
						$path = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/noteattachments/$idattach.$ext";
						if(unlink($path)){
							$db->CommitTrans();
		            		echo "ok";
						}else{
							echo $path;
							$db->RollbackTrans();
							return false;
						}
					}
				}else{
					$db->CommitTrans();
	            	echo "ok";
				}
			} else {
	            $db->RollbackTrans();
				return false;
	        }
		}else{
			$db->RollbackTrans();
			return false;
		}
        
    }



    public function dashboard() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        die('daqui');
        $smarty->display('dashboardindex.tpl.html');
    }



}

?>
