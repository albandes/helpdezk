<?php

//error_reporting(E_ALL);

class operator extends Controllers {
	
    public $database;

	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
        $this->database = $this->getConfig('db_connect');
	}
	
    public function index() {
        $this->validasessao();
		$idtypeperson = $_SESSION['SES_COD_TIPO'];
		if($idtypeperson == 2) {
			$url = $this->getConfig('hdk_url').'helpdezk/user';
			die("<script> location.href = '".$url."'; </script>");
		}
        $smarty = $this->retornaSmarty();
        $cod_usu = $_SESSION['SES_COD_USUARIO'];

        $dbCommon = new common();
        $bd = new home_model();

        $usu_name = $bd->selectUserLogin($cod_usu);

        $db = new logos_model();
        $headerlogo = $db->getHeaderLogo();

        // Buttons
        $showAdmBtn = $bd->showAdmBtn($cod_usu, $idtypeperson);
        if($showAdmBtn->fields['total_typeperson'] > 0 || $showAdmBtn->fields['total_person'] > 0){
            $smarty->assign('showadmbutton', 1);
        }

        $smarty->assign('showDashboard', true);
        $rsModules = $dbCommon->getExtraModulesPerson($cod_usu);
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
			
        $usertype = $_SESSION['SES_TYPE_PERSON'];
		$smarty->assign('total_warnings', $this->numNewWarnings());
        $smarty->assign('headerlogo', $headerlogo->fields['file_name']);
        $smarty->assign('headerheight', $headerlogo->fields['height']);
        $smarty->assign('headerwidth', $headerlogo->fields['width']);
        $smarty->assign('nom_usuario', $usu_name);
        $smarty->assign('userid', $cod_usu);
        $smarty->assign('usertype', $usertype);
		$smarty->assign('changepass', $change_pass);
		$smarty->assign('displayEquipment', $_SESSION['SES_IND_EQUIPMENT']);
		if(!$_SESSION['SES_TIME_SESSION'])
			$smarty->assign('timesession', 600);
		else
			$smarty->assign('timesession', $_SESSION['SES_TIME_SESSION']);
		
		if($_SESSION['SES_REFRESH_OPERATOR_GRID'])
			$smarty->assign('auto_refresh_grid_operator', $_SESSION['SES_REFRESH_OPERATOR_GRID']*1000);
		else
			$smarty->assign('auto_refresh_grid_operator', 0);
		
		$smarty->assign('grid_operator', $_SESSION['SES_PERSONAL_USER_CONFIG']['grid_operator']);
		$smarty->assign('grid_operator_width', $_SESSION['SES_PERSONAL_USER_CONFIG']['grid_operator_width']);
		$smarty->assign('grid_user', $_SESSION['SES_PERSONAL_USER_CONFIG']['grid_user']);
		$smarty->assign('grid_user_width', $_SESSION['SES_PERSONAL_USER_CONFIG']['grid_user_width']);
		if(!isset($_SESSION['SES_PERSONAL_USER_CONFIG']['orderfield']))
			$smarty->assign('sortname', 'a.expire_date');
		else
			$smarty->assign('sortname', $_SESSION['SES_PERSONAL_USER_CONFIG']['orderfield'] );
		
		if(isset($_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols'])){
    		$smarty->assign('sortorder', $_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols'] );
    	}else{
    		$order_mode = $_SESSION['SES_ORDER_ASC'];
	        if ($order_mode == 1)
				$smarty->assign('sortorder', 'asc');
	        else
	            $smarty->assign('sortorder', 'desc');
    	}

        //
        $dbHome = new home_model();
        $dbPerson = new person_model();

        $google2fa = $dbHome->getConfigValue('SES_GOOGLE_2FA');
        if (empty($google2fa)) { // if don't exists in hdk_tbconfig [old versions before 1.02]
            $google2fa = false ;
        }

        if($this->getConfig("license") == '200701006')
        {
                $google2fa = false;
        }

        $smarty->assign('have2fa',$google2fa) ;

        if ($google2fa) {
            $where    = "and tbp.idperson = '$cod_usu'";
            $persinfo = $dbPerson->selectPersonData($cod_usu);
            if( strlen($persinfo->fields['token']) == 16 ) {
                $token = true;
            } else {
                $token = false;
            }
        } else {
            $token = 0 ;
        }

        $smarty->assign('haveToken',$token);
        //

        $smarty->assign('license', $_SESSION['SES_LICENSE']);
        $smarty->display('operator.tpl.html');
    }

	public function numNewWarnings(){
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$idcompany = $_SESSION['SES_COD_EMPRESA'];
        $bd = new warning_model();		

		if ($this->database == 'oci8po'){
            $rsWarning = $bd->selectWarning("AND (a.dtend > SYSDATE AND a.dtstart <= SYSDATE OR a.dtend is null) AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))");
            $total = $rsWarning->RecordCount();
        }else{
            $rsWarning = $bd->selectWarning("AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND (a.idmessage NOT IN(SELECT idmessage FROM bbd_tbread WHERE idperson = $idperson))");
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
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

    //fim definicoes smarty
    //funcao para carregar os tipos
    public function type() {
        $area = $_POST['area'];
        $db = new requestinsert_model();
        $sel = $db->selectType($area);
        $count = $sel->RecordCount();
        $idperson = $_SESSION["SES_COD_USUARIO"];
        if ($count == 0) {
            echo "<option value=''>-----</option>";
            exit();
        } else {
            $i = 0;
            while (!$sel->EOF) {
                $idtype = $sel->fields['idtype'];
                $name = $sel->fields['name'];
                $selected = $sel->fields['selected'];

                /************************* KILLING *************************/
                $type_block_killing = array(84); //TIPOS BLOQUEADOS PARA TODOS USUARIOS
                $person_access = array(555,557,560,1); //PESSOAS QUE TÊM ACESSO AOS TIPOS BLOQUEADOS
                if($_SESSION['SES_LICENSE'] == 200701008 && !in_array($idperson,$person_access) && in_array($idtype, $type_block_killing)){
                    $sel->MoveNext();
                    continue;
                }
                /************************* //KILLING *************************/

                if ($selected == '1') {
                    $selec = "selected='selected'";
                } else {
                    $selec = '';
                }
                echo "<option value='$idtype' $selec >$name</option>";
                $sel->MoveNext();
            }
        }
    }

    //funcao para carregar os itens
    public function item() {
        $type = $_POST['type'];
        $db = new requestinsert_model();
        $sel = $db->selectItem($type);
        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value=''>-----</option>";
            exit();
        } else {
            $i = 0;
            while (!$sel->EOF) {
                $campos[] = $sel->fields['iditem'];
                $valores[] = $sel->fields['name'];
                $selected[] = $sel->fields['selected'];
                if ($selected[$i] == 1) {
                    $selec = "selected='selected'";
                } else {
                    $selec = '';
                }
                echo "<option value='$campos[$i]' $selec >$valores[$i]</option>";
                $i++;
                $sel->MoveNext();
            }
        }
    }

    //funcao para carregar os servicos
    public function service() {
        $item = $_POST['item'];
        $db = new requestinsert_model();
        $sel = $db->selectService($item);
        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value=''>-----</option>";
            exit();
        } else {
            $i = 0;
            while (!$sel->EOF) {
                $campos[] = $sel->fields['idservice'];
                $valores[] = $sel->fields['name'];
                $selected[] = $sel->fields['selected'];
                if ($selected[$i] == 1) {
                    $selec = "selected='selected'";
                } else {
                    $selec = '';
                }
                echo "<option value='$campos[$i]' $selec >$valores[$i]</option>";
                $i++;
                $sel->MoveNext();
            }
        }
    }

    public function reason() {
        $type = $_POST['service'];
        $db = new requestinsert_model();
        $sel = $db->selectReason($type);
        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value=''>-----</option>";
            exit();
        } else {
            $i = 0;
            while (!$sel->EOF) {
                $campos[] = $sel->fields['idreason'];
                $valores[] = $sel->fields['reason'];
                echo "<option value='$campos[$i]'>$valores[$i]</option>";
                $i++;
                $sel->MoveNext();
            }
        }
    }
    //função para cadastrar a solicitação criada no banco
    public function saverequest() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
        $_SESSION["SES_COD_ATTACHMENT"] = "";
        $file = fopen($document_root . 'logs/criando_sol.log', 'ab');
        if ($file) {
            $msg = "\r\n\r\n### Criando nova solicitacao em ".date("Y-m-d H:i:s"). " - usuario:". $_SESSION['SES_COD_USUARIO'];
            fwrite($file, $msg);
        }
        
        if (isset($_POST["idperson"])) {
            $MIN_TEMPO_TELEFONE = number_format($_POST["open_time"], "2", ".", ",");
            // Se estiver configurado para que o codigo da solicitacao seja com formato
            // ANO E MES (ANOMES), gera o codigo neste momento
            if ($_SESSION["SES_IND_CODIGO_ANOMES"]) {
                //GERANDO O CODIGO DA SOLICITACAO
                $db = new requestinsert_model();
                
                 if ($file) {
                    $msg = "\r\nVai testar o código do mês (".__LINE__.") -  ";
                    fwrite($file, $msg);
                }
                //pega o COD_REQUEST e COD_MONTH do mes atual, se não existe vira em branco e uma nova contagem sera criada adiante
                $rsCodigo = $db->getCode();                
                

                 if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
                if ($file) {
                    $msg = "\r\nVai contar o codigo do mês (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rsCountCodigo = $db->countGetCode();
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
                if ($rsCountCodigo->fields['total']) {
                    $COD_SOLICITACAO = $rsCodigo->fields["COD_REQUEST"];
                    if ($file) {
                        $msg = "\r\nVai aumentar o codigo do mês (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rs = $db->increaseCode($COD_SOLICITACAO);
                    if ($file) {
                        $msg = "PASS";
                        fwrite($file, $msg);
                    }
                } else {
                    $COD_SOLICITACAO = 1;
                    if ($file) {
                        $msg = "\r\nVai criar o codigo do mês (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rs = $db->createCode($COD_SOLICITACAO);
                    if ($file) {
                        $msg = "PASS";
                        fwrite($file, $msg);
                    }
                }

                //Montando o Codigo Final
                while (strlen($COD_SOLICITACAO) < 6) {
                    $COD_SOLICITACAO = "0" . $COD_SOLICITACAO;
                }
                $COD_SOLICITACAO = date("Ym") . $COD_SOLICITACAO;
                $CAMPO_COD_SOLICITACAO = "code_request,";
                if ($file) {
                        $msg = "\r\n---Solicitacao tera o código ".$COD_SOLICITACAO ." (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
            }
            //pega as variveis submetidas do form
			
            $COD_USUARIO = $_POST["idperson"];
            $COD_EMPRESA = $_POST["idjuridical"];
            //$COD_PATRIMONIO = $_POST["idproperty"];
            //$NOM_PATRIMONIO = $_POST["property"];
			// -- Equipment --------------------------
            $NUM_SERIE 		= $_POST["serial_number"];
			$NUM_OS 		= $_POST["os_number"];
            $NUM_ETIQUETA 	= $_POST["tag"];
			// ---------------------------------------
            $COD_TIPO = $_POST["type"];
            $COD_SERVICO = $_POST["service"];
            $COD_ITEM = $_POST["item"];
            $COD_TIPO_ATENDIMENTO = $_POST["way"];
            $NOM_ASSUNTO = str_replace("'", "`", $_POST["subject"]);
            $DES_SOLICITACAO = str_replace("'", "`", $_POST["description"]);
            $COD_STATUS = $_POST["status"];
/*
            if (!$NUM_OS) {
                $NUM_OS = 0;
            }
*/	
            //Quando eh usuario, a origem padao eh รฉ pelo HelpDEZk, a menos que seja o chat
            if (isset($_POST["source"])) {
                $COD_ORIGEM = $_POST["source"];
            } else {
                if (isset($_POST['chatid']) && $_POST['chatid'] != 0) {
                    $COD_ORIGEM = 100; //coloca como chat
                } else {
                    $COD_ORIGEM = 1;
                }
            }
			
			$dbrr =  new requestrules_model();
			$rules = $dbrr->getRule($COD_ITEM, $COD_SERVICO);
			$numRules = $rules->RecordCount();
			//Se houver aprovador o status fica como repassado.
			if($numRules > 0){
				$COD_STATUS = 2;
			}else{
				 // se a origem for por telefone e NaO for usuario, conta o tempo de atendimento por TELEFONE            
	            //Quando eh usuario nao eh informado o Status
	            if (isset($_POST["status"])) {
	                $COD_STATUS = $_POST["status"];
	            } else {
	                $COD_STATUS = 1;
	            }
			}
			
           
            $NOM_ANALISTA_AUTOR = "";
            $COD_ANALISTA_AUTOR = "NULL";
            if ($COD_USUARIO != $_SESSION["SES_COD_USUARIO"]) {
                $NOM_ANALISTA_AUTOR = $_SESSION['SES_NAME_PERSON'];
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            } else {
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            }

            // pode ter sido passado um codigo de atendente reponsavel, nesse casso
            if (isset($COD_USUARIO_ANALISTA)) {
                if ($file) {
                    $msg = "\r\nSe passou um responsavel, pega os dados dele (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rs = $db->getAnalyst($COD_USUARIO_ANALISTA);
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }

                if (!$rs->EOF) {
                    $COD_ANALISTA_AUTOR = $COD_USUARIO_ANALISTA;
                    $NOM_ANALISTA_AUTOR = $rs->fields["name"];
                }
            }

            // VERIFICA SE O USUARIO EH VIP
            if ($file) {
                    $msg = "\r\nVerifica se o usuario é vip (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
            $rsUsuarioVip = $db->checksVipUser($COD_USUARIO);
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
            // verifica se ha alguma prioridade marcada como VIP
                if ($file) {
                    $msg = "\r\nVerifica prioridades marcadas como vip (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
            $rsPrioridadeVip = $db->checksVipPriority();
             if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                 if ($file) {
                    $msg = "\r\nSe for vip e tiver marcada, pega ela (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $COD_PRIORIDADE = $rsPrioridadeVip->fields["idpriority"];
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
            } else {
                /// Busca a prioridade no servico
                if ($file) {
                    $msg = "\r\nVai buscar a prioridade do serviço (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rsService = $db->getServPriority($COD_SERVICO);
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
                $COD_PRIORIDADE = $rsService->fields['idpriority'];

                // se nao tiver prioridade no servico, pega a prioridade padrao...
                if (!$COD_PRIORIDADE) {
                    // Consulta a prioridade padrao na abertura de solicitacoes
                     if ($file) {
                        $msg = "\r\nVai buscar a prioridade padrao (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORIDADE = $rsPrioridade->fields["idpriority"];
                    if ($file) {
                        $msg = "PASS";
                        fwrite($file, $msg);
                     }
                }
            }

            // Estamos passando o codigo do servico, o tempo de atendimento deverao ser calculado com base nele..                
            // Se uma data de abertura for informada, usaremos ela para calcular o prazo
            if (!$_POST["date"]) {
                $DAT_CADASTRO = date("d/m/Y");
                $datCalcPrazo = date('Ymd');
            } else {
                $DAT_CADASTRO = $_POST["date"];
                $ptDAT_CADASTRO = explode('/', $_POST["date"]);
                $datCalcPrazo = $ptDAT_CADASTRO[2] . $ptDAT_CADASTRO[1] . $ptDAT_CADASTRO[0];
            }

            if (!$_POST["time"]) {
                $HOR_CADASTRO = date("H:i");
                $datCalcPrazo .= date('Hi');
            } else {
                $HOR_CADASTRO = $_POST["time"];
                $datCalcPrazo .= str_replace(':', '', $_POST["time"]);
            }
            if ($file) {
                        $msg = "\r\nVai rodar a função para fazer a data de venc. (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
            $DAT_VENCIMENTO_ATENDIMENTO = $this->getDataVcto($datCalcPrazo, $COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO);
            if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
            $db = new requestinsert_model();

            $AUX = explode("/", $DAT_CADASTRO);
            $DAT_CADASTRO = $AUX[2] . "-" . $AUX[1] . "-" . $AUX[0];
            $AUX = explode(":", $HOR_CADASTRO);
            $DAT_CADASTRO .= " " . $AUX[0] . ":" . $AUX[1] . ":00";

            $COD_MOTIVO = $_POST['reason'];
            $COD_TIPO_ATENDIMENTO = $_POST['way'];
            if ($file) {
                        $msg = "\r\n*Vai inserir a solicitacao no banco (".__LINE__.") -\r\n";
                        $msg .= $db->insertRequest2($COD_ANALISTA_AUTOR, $COD_ORIGEM, $DAT_CADASTRO, $COD_TIPO, $COD_ITEM, $COD_SERVICO, $COD_MOTIVO, $COD_TIPO_ATENDIMENTO, $NOM_ASSUNTO, $DES_SOLICITACAO, $NUM_OS, $COD_PRIORIDADE, $NUM_ETIQUETA, $NUM_SERIE, $COD_EMPRESA, $DAT_VENCIMENTO_ATENDIMENTO, $COD_USUARIO, $COD_STATUS, $CAMPO_COD_SOLICITACAO, $COD_SOLICITACAO . ',');
            
                        fwrite($file, $msg);
                    }
            $rs = $db->insertRequest($COD_ANALISTA_AUTOR, $COD_ORIGEM, $DAT_CADASTRO, $COD_TIPO, $COD_ITEM, $COD_SERVICO, $COD_MOTIVO, $COD_TIPO_ATENDIMENTO, $NOM_ASSUNTO, $DES_SOLICITACAO, $NUM_OS, $COD_PRIORIDADE, $NUM_ETIQUETA, $NUM_SERIE, $COD_EMPRESA, $DAT_VENCIMENTO_ATENDIMENTO, $COD_USUARIO, $COD_STATUS, $CAMPO_COD_SOLICITACAO, $COD_SOLICITACAO . ',');
            if(!$rs){
                return false;
            }
            if ($file) {
                $msg = "\r\nPASS";
                fwrite($file, $msg);
            }
            if ($file) {
                $msg = "\r\n*Vai buscar o grupo de atendimento do serviço (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $grp = $db->getServiceGroup($COD_SERVICO);
            //$rs = $db->updateRequest_in_Group($grp, $COD_SOLICITACAO);
            if(!$grp){
                return false;
            }
            if ($file) {
                $msg = "PASS";
                fwrite($file, $msg);
            }
             if ($file) {
                $msg = "\r\n*Vai inserir o grupo responsavel an in_charge (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			 
			
			
           
			if ($numRules > 0) { //REGRAS PARA CASO TENHA APROVADOR PARA ESTE SERVIÇO
				$dbrr->BeginTrans();
				$count = 1;
		        while (!$rules->EOF) {		        		
		        	if($rules->fields['order'] == 1) $APROVADOR = $rules->fields['idperson'];
					
					$values .= "(".$rules->fields['idapproval'].",". $COD_SOLICITACAO .",". $rules->fields['order'] .",". $rules->fields['idperson'] .",". $rules->fields['fl_recalculate'] .")";
		            
					if($numRules != $count) $values .= ",";
					$count++;
		            $rules->MoveNext();
		        }
		        $con = $dbrr->insertApproval($values);
                if ($con) {
                    $grp_model = new groups_model();				
					$onlyRep = $grp_model->checkGroupOnlyRepass($grp);
					
					if($onlyRep->fields['repass_only'] == "Y"){//REGRA PARA CASO ESTE GRUPO SEJA SOMENTE REPASSADOR
						$newidgroup = $grp_model->getNewGroupOnlyRepass($grp,$_SESSION['SES_COD_EMPRESA']);
						$grp2 = $newidgroup->fields['idperson'];
						if($grp2)
							$rs2 = $db->insertRequestCharge($COD_SOLICITACAO, $grp2, 'G', '0');
						else
							$rs2 = $db->insertRequestCharge($COD_SOLICITACAO, $grp, 'G', '0');
						
					}else{//REGRA PARA CASO SEM APROVADOR E GRUPO SOMENTE REPASSADOR
						$rs2 = $db->insertRequestCharge($COD_SOLICITACAO, $grp, 'G', '0');	
					}					
					
					$rs = $db->insertRequestCharge($COD_SOLICITACAO, $APROVADOR, 'P', '1');
					
					if(!$rs || !$rs2){
						$dbrr->RollbackTrans();
		                return false;
		            }else{
		            	$dbrr->CommitTrans();
		            }
                }
				
				
			}else{
				$grp_model = new groups_model();				
				$onlyRep = $grp_model->checkGroupOnlyRepass($grp);
				
				if($onlyRep->fields['repass_only'] == "Y"){//REGRA PARA CASO ESTE GRUPO SEJA SOMENTE REPASSADOR
					$newidgroup = $grp_model->getNewGroupOnlyRepass($grp,$_SESSION['SES_COD_EMPRESA']);
					$grp2 = $newidgroup->fields['idperson'];
					if($grp2)
						$rs = $db->insertRequestCharge($COD_SOLICITACAO, $grp2, 'G', '1');
					else
						$rs = $db->insertRequestCharge($COD_SOLICITACAO, $grp, 'G', '1');
					
		            if(!$rs){
		                return false;
		            }
				}else{//REGRA PARA CASO SEM APROVADOR E GRUPO SOMENTE REPASSADOR
					$rs = $db->insertRequestCharge($COD_SOLICITACAO, $grp, 'G', '1');
		            if(!$rs){
		                return false;
		            }	
				}
			}     
            if ($file) {
                $msg = "PASS";
                fwrite($file, $msg);
            }
            if ($file) {
                $msg = "\r\n*Vai inserir os tempos na request_times (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $tm = $db->insertRequestTimes($COD_SOLICITACAO, $MIN_TEMPO_TELEFONE, '0', '0');
            if(!$tm){
                return false;
            }
            if ($file) {
                $msg = "PASS";
                fwrite($file, $msg);
            }
            ///Vamos  descobrir o codigo auto increment da solicitacao
            /// se nao estivermos usando o formato ANOMES. Caso em que ja teremos o $COD_SOLICITACAO AQUI
            if (!isset($COD_SOLICITACAO)) {
                if ($file) {
                    $msg = "\r\n*Vai buscar o ultimo codigo da request (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rs = $db->lastCode();
                if(!$rs){
                   return false;
                }
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
                $COD_SOLICITACAO = $rs->fields["code_request"];
            }
            if (!$_SESSION["SES_IND_CODIGO_ANOMES"]) {
                if ($file) {
                    $msg = "\r\n*Vai buscar o ultimo codigo da request (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rs = $db->lastCode();
                if(!$rs){
                    return false;
                }
                if ($file) {
                    $msg = "PASS";
                    fwrite($file, $msg);
                }
                $COD_SOLICITACAO = $rs->fields["code_request"];
            }
            //insere na tabela de controle a a alteração de status feita e qual usuario fez com a data do acontecimento
            if ($file) {
                $msg = "\r\n*Vai inserir na resquest_log (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $rs = $db->insertRequestLog($COD_SOLICITACAO, date("Y-m-d H:i:s"), $COD_STATUS, $COD_USUARIO);
            if(!$rs){
                return false;
            }
            if ($file) {
                $msg = "PASS";
                fwrite($file, $msg);
            }
            $db2 = new operatorview_model;
            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_POST["COD_ANEXO"] != '') {

                $COD_ANEXO = explode(",", $_POST["COD_ANEXO"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluรญndo o cรณdigo da solicitaรงรฃo  nos anexos.     
                    if ($file) {
                        $msg = "\r\n*Vai atualizar o codigo da solicitacao na tabela de anexos (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }                           
                    $Result1 = $db->updateRequestAttach($COD_SOLICITACAO, $COD_ANEXO[$i]);
                    if(!$Result1){
                       return false;
                    }
                    if ($file) {
                        $msg = "PASS";
                        fwrite($file, $msg);
                    }
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            if ($this->database == 'oci8po') {
                $data = "sysdate";
            }
            else
            {
                $data = "now()";
            }

            $DES_APONTAMENTO = "<p><b>" . $langVars['Request_opened'] . "</b></p>";
            if ($file) {
                $msg = "\r\n*Vai inserir o apontamento de solicitacao aberta (".__LINE__.") - ";
                fwrite($file, $msg);
            }  
            $con = $db->insertNote($COD_SOLICITACAO, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');
            if(!$con){
                return false;
            }
            
			
			if($_POST['solution']){
                if ($this->database == 'oci8po') {
                    $data = "sysdate";
                }
                else
                {
                    $data = "now()";
                }
	            $DES_APONTAMENTO = "<p><b>" . $langVars['Solution'] . "</b></p>". $_POST['solution'];
	            if ($file) {
	                $msg = "\r\n*Vai inserir o apontamento de solução (".__LINE__.") - ";
	                fwrite($file, $msg);
	            }  
	            $con = $db->insertNote($COD_SOLICITACAO, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", "3", "0", "0", "0", "0", $_SERVER['REMOTE_ADDR'], 'NULL');
							
	            if(!$con){
	                return false;
	            }				
			}		
			
            if ($file) {
                $msg = "PASS";
                fwrite($file, $msg);
            }
            //Zerando a variavel que armazena os anexos.
            $_SESSION["SES_COD_ATTACHMENT"] = "";
            
            if ($rs) {
            	
				if($numRules > 0){
					if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['SES_REQUEST_APPROVE'] == '1') {
	                    if ($file) {
	                        $msg = "\r\n*Vai rodar a função de enviar email (".__LINE__.") - ";
	                        fwrite($file, $msg);
	                    } 
	                    $this->sendEmail('approve', $COD_SOLICITACAO);
	                    
	                    if ($file) {
	                        $msg = "PASS";
	                        fwrite($file, $msg);
	                    }
	                }		
				}else{
					if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['NEW_REQUEST_OPERATOR_MAIL'] == '1') {
	                    if ($file) {
	                        $msg = "\r\n*Vai rodar a função de enviar email (".__LINE__.") - ";
	                        fwrite($file, $msg);
	                    } 
	                    $this->sendEmail('record', $COD_SOLICITACAO);
	                    
	                    if ($file) {
	                        $msg = "PASS";
	                        fwrite($file, $msg);
	                    }
	                }
				}
				
                
                if ($file) {
                    $msg = "\r\n###Fim da criação de solicitação (".__LINE__.") ### ";
                    fwrite($file, $msg);                        
                    fclose($file);
                } 
                echo $COD_SOLICITACAO;
            } else {
                return false;
            }
        }//fim do cadastro
    }

    //fim da funcao saverequest
    #######################################################################################################################################################
    ############################################################################ HELPERS ##################################################################
    #######################################################################################################################################################

    public function getDiasUteis() {

        // Armazena o horรกrio de trabalho para cada dia รบtil da semana
        $db = new helpers_model();

        $rsUtil = $db->getDiasUteisM();
        $DiasUteis = array();
        while (!$rsUtil->EOF) {
            $INI_MANHA = $this->sepHoraMin($this->addZero($rsUtil->fields['begin_morning']));
            $FIN_MANHA = $this->sepHoraMin($this->addZero($rsUtil->fields['end_morning']));
            $INI_TARDE = $this->sepHoraMin($this->addZero($rsUtil->fields['begin_afternoon']));
            $FIN_TARDE = $this->sepHoraMin($this->addZero($rsUtil->fields['end_afternoon']));

            $DiasUteis[$rsUtil->fields['num_day_week']] = array(
                "DIA_SEMANA" => $rsUtil->fields['num_day_week'],
                "HOR_INI_MANHA" => $INI_MANHA[0],
                "MIN_INI_MANHA" => $INI_MANHA[1],
                "HOR_FIN_MANHA" => $FIN_MANHA[0],
                "MIN_FIN_MANHA" => $FIN_MANHA[1],
                "HOR_INI_TARDE" => $INI_TARDE[0],
                "MIN_INI_TARDE" => $INI_TARDE[1],
                "HOR_FIN_TARDE" => $FIN_TARDE[0],
                "MIN_FIN_TARDE" => $FIN_TARDE[1]);
            $rsUtil->MoveNext();
        }
        return $DiasUteis;
    }

    public function getFeriados() {
        // seleciona os feriados
        $db = new holidays_model();
        $where = "where holiday_date >='" . date('Y-m-d') . "'";
        $rsFeriados = $db->selectHoliday($where);
        $Feriados = array();
        while (!$rsFeriados->EOF) {
            $feriado = $rsFeriados->fields['HOLIDAY_DATE'];
            $feriado = str_replace('-', '', $feriado);
            $Feriados[] = $feriado;
            $rsFeriados->MoveNext();
        }
        return $Feriados;
    }

    public function getDataVcto($DAT_INICIAL, $COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO = false) {

        /*         * ******************************************************************************************************
          A logica da funรงรฃo รฉ recebe um prazo e a data inicial.
          Ao longo da funรงรฃo, a data inicial vai sendo acrescida atรฉ se transformar na data de vencimento
          a mesma medida em que a data vai sendo acrescida, o prazo vai diminuindo atรฉ zerar
          Assim, enquanto houver um valor no prazo, vai processando...
         * ****************************************************************************************************** */
        $GLOBALS["DiasUteis"] = $this->getDiasUteis();
        $GLOBALS["Feriados"] = $this->getFeriados();
        // a DAT_INICIAL chega no formato AAAAMMDDHHMM ano mes dia hora minuto e รฏยฟยฝ convertida pra timestamp
        $DAT_INICIAL = $this->converteTimeStamp($DAT_INICIAL);


        list($PRAZO_EM_DIAS, $PRAZO_EM_MINUTOS) = $this->getPrazo($COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO); // pega o prazo em dias e/ou horas e transforma em min
        // Verifica se a data inicial nรฏยฟยฝo cai num feriado ou dia nรฏยฟยฝo-รฏยฟยฝtil. Se cair, busca o prรณximo dia vรฏยฟยฝlido
        //echo $DAT_INICIAL."<BR/>";
        $DAT_INICIAL = $this->pulaDias($DAT_INICIAL, $PRAZO_EM_DIAS);
        //echo $DAT_INICIAL;
        // pega o horรฏยฟยฝrio de trabalho data de vencimento. $HOR passa a ser um array com todas as horas e minutos limites de trabalho
        $HOR = $this->getHorarioTrabalho($DAT_INICIAL);

        $processaTarde = true;
        while ($PRAZO_EM_MINUTOS > 0) {
            // armazena o horรฏยฟยฝrio final de trabalho no perรฏยฟยฝodo matutino
            $FIN_MANHA = $this->converteTimeStamp(strftime("%Y%m%d", $DAT_INICIAL) . $HOR["HOR_FIN_MANHA"] . $HOR["MIN_FIN_MANHA"]);

            //if (strftime("%H%M", $DAT_INICIAL) < $HOR["HOR_FIN_MANHA"].$HOR["MIN_FIN_MANHA"]){
            // se a hora de abertura for menor do que a hora final de trabalho da manhรฏยฟยฝ, tem um tempo pra resolver jรฏยฟยฝ de manhรฏยฟยฝ
            if ($this->dateDiff("n", $DAT_INICIAL, $FIN_MANHA) > 0) {
                // TEMPO_MANHA armazena quanto tempo tem pra resolver atรฏยฟยฝ o final da manhรฏยฟยฝ
                $TEMPO_MANHA = $this->dateDiff("n", $DAT_INICIAL, $FIN_MANHA);

                // se tiver tempo suficiente pra resolver de manhรฏยฟยฝ, ou seja, o tempo disponรฏยฟยฝvel รฏยฟยฝ maior que o prazo
                if ($TEMPO_MANHA >= $PRAZO_EM_MINUTOS) {
                    // a data do vencimento passa a ser a data de abertura acrescida do prazo
                    $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL) + $PRAZO_EM_MINUTOS, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                    $PRAZO_EM_MINUTOS = 0;
                } else {
                    // se nรฏยฟยฝo puder ser resolvido sรฏยฟยฝ de manhรฏยฟยฝ, a data รฏยฟยฝ acrescida do tempo que tem-se de manhรฏยฟยฝ
                    $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL) + $TEMPO_MANHA, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                    // esse tempo que foi acrescido รฏยฟยฝ data, รฏยฟยฝ retirado do prazo
                    $PRAZO_EM_MINUTOS -= $TEMPO_MANHA;
                }
            }

            // apรฏยฟยฝs verificar o perรฏยฟยฝodo da manhรฏยฟยฝ, se ainda tiver prazo pra fazer...
            if ($PRAZO_EM_MINUTOS > 0) {
                // armazena o perรฏยฟยฝodo inicial e final da tarde
                $INI_TARDE = $this->converteTimeStamp(strftime("%Y%m%d", $DAT_INICIAL) . $HOR["HOR_INI_TARDE"] . $HOR["MIN_INI_TARDE"]);
                $FIN_TARDE = $this->converteTimeStamp(strftime("%Y%m%d", $DAT_INICIAL) . $HOR["HOR_FIN_TARDE"] . $HOR["MIN_FIN_TARDE"]);
                // quanto tempo (em minutos) tem pra fazer รฏยฟยฝ tarde
                $TEMPO_TARDE = $this->dateDiff("n", $INI_TARDE, $FIN_TARDE);

                // se a data inicial form maior que o inรฏยฟยฝcio da tarde, tรฏยฟยฝ tranquilo, รฏยฟยฝ sรฏยฟยฝ comeรฏยฟยฝar
                //if (strftime("%H%M", $DAT_INICIAL) > strftime("%H%M", $INI_TARDE)){
                if ($this->dateDiff("n", $INI_TARDE, $DAT_INICIAL) > 0) {
                    // se a solcitaรฏยฟยฝรฏยฟยฝo foi aberta depois do expediente, comeรฏยฟยฝa no outro dia
                    if (strftime("%H%M", $DAT_INICIAL) > strftime("%H%M", $FIN_TARDE)) {
                        // acresencta um dia na data inicial					
                        $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL), 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL) + 1, $this->extAno($DAT_INICIAL));
                        // se o dia seguinte (acresentado acima) for feriado ou dia nรฏยฟยฝo-รฏยฟยฝtil, pula
                        $DAT_INICIAL = $this->pulaDias($DAT_INICIAL, 0);
                        $HOR = $this->getHorarioTrabalho($DAT_INICIAL); // pega o horรฏยฟยฝrio de trabalho nova data
                        // o inรฏยฟยฝcio passa a ser o horรฏยฟยฝrio inicial de trabalho do dia seguinte (jรฏยฟยฝ calculado)
                        $DAT_INICIAL = mktime($HOR["HOR_INI_MANHA"], $HOR["MIN_INI_MANHA"], 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                        // a variรฏยฟยฝvel abaixo serve para, logo abaixo, nรฏยฟยฝo ser processada a tarde, caso pule pro outro dia
                        $processaTarde = false;
                    } else { // se tem tempo รฏยฟยฝ tarde pra resolver, calcula quanto tempo tem					
                        $TEMPO_TARDE = $this->dateDiff("n", $DAT_INICIAL, $FIN_TARDE);
                    }
                } else if ($TEMPO_TARDE) { // se a data nรฏยฟยฝo รฏยฟยฝ maior que o inรฏยฟยฝcio da tarde, รฏยฟยฝ por que caiu no intervalo do meio dia
                    $TEMPO_INTERVALO = $this->dateDiff("n", $DAT_INICIAL, $INI_TARDE);
                    $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL) + $TEMPO_INTERVALO, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                    $TEMPO_TARDE = $this->dateDiff("n", $DAT_INICIAL, $FIN_TARDE);
                }

                // se a data caiu dentro do perรฏยฟยฝodo da tarde....
                if ($processaTarde) {
                    // se o tempo que se tem pra resolver รฏยฟยฝ tarde รฏยฟยฝ maior do que meu prazo, tรฏยฟยฝ tranquilo...
                    if ($TEMPO_TARDE >= $PRAZO_EM_MINUTOS) {
                        // a data inicial รฏยฟยฝ acrescida de quantos minutos eu tenho pra resolver, gerando a data finall
                        $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL) + $PRAZO_EM_MINUTOS, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                        $PRAZO_EM_MINUTOS = 0;
                    } else {// se o tempo que eu tenho pra resolver รฏยฟยฝ tarde, nรฏยฟยฝo basta....
                        // desconta todo o tempo que eu tenho รฏยฟยฝ tarde do tempo que eu tenho pra resolver	
                        $PRAZO_EM_MINUTOS -= $TEMPO_TARDE;
                        // pual pro dia seguinte					
                        $DAT_INICIAL = mktime(0, 0, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL) + 1, $this->extAno($DAT_INICIAL));
                        $DAT_INICIAL = $this->pulaDias($DAT_INICIAL, 0); // verifica se o dia seguinte nรฏยฟยฝo รฏยฟยฝ feriado nem dia nรฏยฟยฝo-รฏยฟยฝtil
                        $HOR = $this->getHorarioTrabalho($DAT_INICIAL); // pega o horรฏยฟยฝrio de trabalho data de vencimento
                        // a data inicial passa a ser o perรฏยฟยฝodo inicial de trabalho do dia do vencimento
                        $DAT_INICIAL = mktime($HOR["HOR_INI_MANHA"], $HOR["MIN_INI_MANHA"], 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                    }
                }
            }
            $processaTarde = true;
        }

        $DAT_VENCIMENTO = date("YmdHi00", $DAT_INICIAL);

        return $DAT_VENCIMENTO;
    }

    public function addZero($HORA) {
        if (strlen($HORA) == 3) {
            return "0" . $HORA;
        } elseif (strlen($HORA) == 4) {
            return $HORA;
        } else {
            return 0;
        }
    }

    public function sepHoraMin($HORA) {
        $retorno[] = substr($HORA, 0, 2);
        $retorno[] = substr($HORA, 2, 2);
        return $retorno;
    }

    public function getPrazo($COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO = NULL) {

        // se tiver COD_PATRIMONIO e este patrimรดnio tiver tempo, pega pelo tempo do patrimรดnio
        if ($COD_PATRIMONIO) {
            $db = new property_model();
            $where = "where idproperty = " . $COD_PATRIMONIO;
            $rsPatrimonio = selectProperty($where);
            $rsCountPatrimonio = selectCountProperty($where);
            $rsPatrimonioRows = $rsCountPatrimonio->fields['total'];
            if ($rsPatrimonioRows && ($rsPatrimonio->fields["days_attendance"] || $rsPatrimonio->fields["hours_attendance"])) {
                $NUM_DIA_ATENDIMENTO = $rsPatrimonio->fields["days_attendance"] ? $rsPatrimonio->fields["days_attendance"] : 0;
                $NUM_HORA_ATENDIMENTO = $rsPatrimonio->fields["hours_attendance"] ? $rsPatrimonio->fields["hours_attendance"] : 0;
//			$retorno = ($NUM_DIA_ATENDIMENTO * 24 * 60) + ($NUM_HORA_ATENDIMENTO * 60);
                $retorno[] = $NUM_DIA_ATENDIMENTO ? $NUM_DIA_ATENDIMENTO : 0;
                $retorno[] = $NUM_HORA_ATENDIMENTO * 60;
                return $retorno;
                return false;
            }

            $db2->close();
        }

        // sรณ vai chegar aki se nรฃo tiver cรณdigo do patrimรดnio OU se o patrimรดnio nรฃo tiver nem hora nem dia
        // se vier patrimรดnio e este tiver OU hora OU dia, pega esses valores e jรก dรฃo o return na funรงรฃo, parando por ali.
        // seleciona, ou o tempo do Item, ou o tempo da Prioridade
        ############ ATENรรO #############
        /**
         * Os dados de tempo de atendimento e de prioridade agora sรฃo resgatados a partir do serviรงo.
         * Como esta funรงรฃo deverรก ser reescrita na nova versรฃo, apenas alterei a query e mantive todo os resto
         * quando for reescrita essas informaรงรตes deverรฃo ser obtidas atravรฉs de um objeto Servico.
         * @since 2008-11-12
         */
        $db2 = new services_model();
        if (isset($COD_SERVICO)) {
            $where = "WHERE idservice = " . $COD_SERVICO;
        } else {
            ///programas que ainda nรฃo foram alterados podem manter-se usando o cรณdigo do item
            $where = "WHERE iditem = " . $COD_ITEM;
        }
        $rsItem = $db2->selectService($where);

        // se tiver tanto a qtd de dias de atendimento ou a qtd de horas...

        $db3 = new priority_model();
        if ($rsItem->fields["days_attendance"] > 0 || $rsItem->fields["hours_attendance"] > 0) {
            $NUM_DIA_ATENDIMENTO = $rsItem->fields["days_attendance"];
            $NUM_HORA_ATENDIMENTO = $rsItem->fields["hours_attendance"];
        }
        // se nรฃo houver nem qtd de dia nem de horas, porรฉm houver um uma prioridade cadastrada para o ITEM...
        else if ($rsItem->fields['idpriority']) {
            $where = "where idpriority = " . $rsItem->fields['idpriority'];
            $rsPrior = $db3->selectPriority($where);
            $NUM_DIA_ATENDIMENTO = $rsPrior->fields["limit_days"];
            $NUM_HORA_ATENDIMENTO = $rsPrior->fields["limit_hours"];
        } else { // se nรฃo houver tempo de atendimento nem prioridade do item, pega o tempo do cadastro de prioridade
            $where = "where idpriority = " . $COD_PRIORIDADE;
            $rsPrior2 = $db3->selectPriority($where);
            // Se nรฏยฟยฝo tiver registro, ou nรฏยฟยฝo tiver nem dia nem hora, zera...
            if ($rsPrior2->EOF || (!$rsPrior2->fields["limit_days"] && !$rsPrior2->fields["limit_hours"])) {
                $NUM_DIA_ATENDIMENTO = 0;
                $NUM_HORA_ATENDIMENTO = 0;
            } else {
                $NUM_DIA_ATENDIMENTO = $rsPrior2->fields["limit_days"];
                $NUM_HORA_ATENDIMENTO = $rsPrior2->fields["limit_hours"];
            }
        }

        //$db3->Close();
        //$retorno = ($NUM_DIA_ATENDIMENTO * 24 * 60) + ($NUM_HORA_ATENDIMENTO * 60);
        $retorno[] = $NUM_DIA_ATENDIMENTO ? $NUM_DIA_ATENDIMENTO : 0;
        $retorno[] = $NUM_HORA_ATENDIMENTO * 60;
        //$db->close();
        return $retorno;
    }

    public function pulaDias($DAT_INICIAL, $PRAZO_EM_DIAS) {
        global $DiasUteis, $Feriados;
        // o vencimento parte da data inicial
        $DAT_VENCIMENTO = strftime("%Y%m%d", $DAT_INICIAL);
        $HORA = $this->extHoraMin($DAT_INICIAL);

        if ($PRAZO_EM_DIAS) {
            $CONT = 0;
            while ($CONT < $PRAZO_EM_DIAS) {
                // para cada dia de prazo, incrementa um dia na data inicial
                $DATA = mktime(0, 0, 0, substr($DAT_VENCIMENTO, 4, 2), substr($DAT_VENCIMENTO, 6, 2) + 1, substr($DAT_VENCIMENTO, 0, 4));
                $DAT_VENCIMENTO = substr(strftime("%Y%m%d", $DATA), 0, 8);

                // enquanto a data for feriado ou nรฏยฟยฝo for dia util, incrementa a data sem considerar a 
                // qtd de dias de prazo ($cont)
                while (!in_array(date('w', $DATA), array_keys($DiasUteis)) || in_array($DAT_VENCIMENTO, $Feriados)) {
                    $DATA = mktime(0, 0, 0, substr($DAT_VENCIMENTO, 4, 2), substr($DAT_VENCIMENTO, 6, 2) + 1, substr($DAT_VENCIMENTO, 0, 4));
                    $DAT_VENCIMENTO = substr(strftime("%Y%m%d", $DATA), 0, 8);
                }
                $CONT++;
            }
        } else {
            $x = "";
            foreach ($DiasUteis as $key => $valor) {
                $x .= $key . "-";
            }
            $y = "";
            foreach ($Feriados as $key => $valor) {
                $y .= $valor . "-";
            }

            while (!in_array(date('w', $DAT_INICIAL), array_keys($DiasUteis)) || in_array($DAT_VENCIMENTO, $Feriados)) {
                $DAT_INICIAL = mktime(0, 0, 0, substr($DAT_VENCIMENTO, 4, 2), substr($DAT_VENCIMENTO, 6, 2) + 1, substr($DAT_VENCIMENTO, 0, 4));
                $DAT_VENCIMENTO = substr(strftime("%Y%m%d", $DAT_INICIAL), 0, 8);
            }
        }
        return $this->converteTimeStamp($DAT_VENCIMENTO . $HORA);
    }

    public function getHorarioTrabalho($DATA) {
        global $DiasUteis;
        return $DiasUteis[date('w', $DATA)];
    }

    public function converteTimeStamp($DATA) {
        return mktime(substr($DATA, -4, 2), substr($DATA, -2, 2), 0, substr($DATA, 4, 2), substr($DATA, 6, 2), substr($DATA, 0, 4));
    }

    public function dateDiff($interval, $datefrom, $dateto, $using_timestamps = true) {
        /*
          $interval can be:
          yyyy - Number of full years
          q - Number of full quarters
          m - Number of full months
          y - Difference between day numbers
          (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
          d - Number of full days
          w - Number of full weekdays
          ww - Number of full weeks
          h - Number of full hours
          n - Number of full minutes
          s - Number of full seconds (default)
         */
        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }
        $difference = $dateto - $datefrom; // Difference in seconds
        switch ($interval) {
            case 'yyyy': // Number of full years
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters
                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;

            case 'y': // Difference between day numbers
                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;

            case "d": // Number of full days
                $datediff = floor($difference / 86400);
                break;

            case "w": // Number of full weekdays
                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case "ww": // Number of full weeks
                $datediff = floor($difference / 604800);
                break;

            case "h": // Number of full hours
                $datediff = floor($difference / 3600);
                break;

            case "n": // Number of full minutes
                $datediff = floor($difference / 60);
                break;

            default: // Number of full seconds (default)
                $datediff = $difference;
                break;
        }
        return $datediff;
    }

    ///////////////// F I M   D A   F U N รฏยฟยฝ รฏยฟยฝ O   d a t e D i f f  //////////////////////////////

    public function extHora($DATA) {
        return date("H", $DATA);
    }

    public function extMin($DATA) {
        return date("i", $DATA);
    }

    public function extDia($DATA) {
        return date("d", $DATA);
    }

    public function extMes($DATA) {
        return date("m", $DATA);
    }

    public function extAno($DATA) {
        return date("Y", $DATA);
    }

    public function extHoraMin($DATA) {
        return date("Hi", $DATA);
    }

    function evaluate() {
        session_start();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        extract($_POST);
        $date = date("Y-m-d H:i:s");

        $bd = new operatorview_model();
        $ins = $bd->insertEvaluation($answer, $code, $date);
        if ($ins) {
            if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['EM_EVALUATED']) {
                $this->sendEmail('afterevaluate', $code);
            }
            echo "OK";
        } else {
            return false;
        }
    }

    public function atualiza_responsavel($COD_SOLICITACAO, $COD_RESP, $GRUPO_OU_ATD = 'atd') {

        $envolvidos = $this->get_envolvidos_atendimento($COD_SOLICITACAO);


        $db = new requestinsert_model();
        $sql_result = $db->updateRequestGroupInd($COD_SOLICITACAO);

        // Se a responsabilidade sera passada a um atendente
        if ($GRUPO_OU_ATD == 'atd') {
            if (array_key_exists($COD_RESP, $envolvidos['atendentes'])) {
                // ja estao la apenas certifica-se de que serao o responsavel

                $sql_result = $db->updateRequestGroupInd1($COD_RESP, $COD_SOLICITACAO);
            } else { //se nao inclui o atendente como responsรกvel
                $sql_result = $db->insertRequestGroupPersonResp($COD_SOLICITACAO, $COD_RESP, '1');
            }
        } else { //resp. serao passada a um grupo
            if (array_key_exists($COD_RESP, $envolvidos['grupos'])) {
                $sql_result = $db->updateRequestGroupInd2($COD_RESP, $COD_SOLICITACAO);
            } else {

                $rs = $db->insertRequestGroupResp($COD_SOLICITACAO, $COD_RESP, '1', '0');
            }
        }

        // Coloca o status como repassada
        $rs = $db->insertRequestLog($COD_SOLICITACAO, date("Y-m-d H:i:s"), '10', $COD_USUARIO);
        if (!$rs) {
            die('Error cod.:' . __LINE__);
        }


        // Envia o e-mail avisando da responsabilidade
        $GLOBALS['COD_EMAIL'] = $COD_EMAIL = "registrar";

        return true;
    }

//function

    public function get_envolvidos_atendimento($COD_SOLICITACAO) {

        $grupos = $this->get_grupos();
        $users = $this->get_users();

        $retorno['atendentes'] = array();
        $retorno['grupos'] = array();

        $db = new requestinsert_model();
        $sql_result = $db->getAnalista($COD_SOLICITACAO);

        while ($tupla = $sql_result->fields) {
            if (!empty($tupla['idperson'])) {
                $retorno['atendentes'][$tupla['idperson']] = $users[$tupla['idperson']];
            } else {
                $retorno['grupos'][$tupla['idgroup']] = get_grupo_atendimento($tupla['idgroup']);
            }
            $sql_result->MoveNext();
        }

        return $retorno;
    }

//function

    public function get_grupos($match = NULL) {

        if (isset($match)) {
            $match = "WHERE $match ";
        } else {
            $match = false;
        }

        $db = new requestinsert_model();
        $sql_result = $db->getGrupos($match);

        if (!$sql_result) {
            return array();
        }
        while ($tupla = $sql_result->fields) {
            $retorno[$tupla["idgroup"]] = $tupla["name"];
            $sql_result->MoveNext();
        }
        return $retorno;
    }

//function

    public function get_users($COD_TIPO = "ALL") {
        //o array que retornaremos
        $users_do_tipo = array();

        //Se nรฏยฟยฝo especificou tipo ou colocaou "ALL"
        if ($COD_TIPO == "ALL") {
            $db = new person_model();


            $sql_result = $db->getTypePerson();
            $COD_TIPO = array();
            while (!$sql_result->EOF) {

                array_push($COD_TIPO, $sql_result->fields["idtypeperson"]);
                $sql_result->MoveNext();
            }
        }//if

        if (is_array($COD_TIPO))
            $COD_TIPO = join($COD_TIPO, ",");

        //recupera todos os usuรฏยฟยฝrios do tipo

        $sql_result = $db->getPersonFromType($COD_TIPO);
        $empresas = $this->get_empresas();
        while ($usuarios = $sql_result->fields) {

            foreach ($usuarios as $col => $valor) {
                $users_do_tipo[$sql_result->fields["idperson"]][$col] = $valor;
            }
            if (isset($empresas[$sql_result->fields["idjuridical"]])) {
                $users_do_tipo[$sql_result->fields["idperson"]]['juridical'] = $empresas[$sql_result->fields["idjuridical"]];
            }
            if ($users_do_tipo[$sql_result->fields["idperson"]]['idtypeperson'] != 1) { //se nรฏยฟยฝo for um usuรฏยฟยฝrio pega os grupos de atendimento
                $users_do_tipo[$sql_result->fields["idperson"]]['GRUPOS_ATENDIMENTO'] = array();
                $db = new requestinsert_model($sql_result->fields["idperson"]);
                $sql_result_grupos = $db->getGroupsAttendance();
                if (!$sql_result_grupos) { //o atendente nรฏยฟยฝo tem grupo
                    $dbg = new groups_model();
                    $g1 = $dbg->getGroupFirstLevel();
                    array_push($users_do_tipo[$sql_result->fields["idperson"]]['GRUPOS_ATENDIMENTO'], $g1['idgroup']);
                } else {
                    while ($_grupo = $sql_result_grupos->fields['idgroup']) {
                        $users_do_tipo[$sql_result->fields["idperson"]]['GRUPOS_ATENDIMENTO'][$_grupo] = $_grupo;
                        $sql_result_grupos->MoveNext();
                    }
                }
            }
            $sql_result->MoveNext();
        }//while		
        return $users_do_tipo;
    }

//function

    public function get_empresas() {
        $db = new person_model();
        $sql_result = $db->getJuridical();
        if (!$sql_result) {
            return array();
        }

        while ($tupla = $sql_result->fields) {
            $retorno[$tupla["idjuridical"]] = $tupla["name"];
            $sql_result->MoveNext();
        }//while

        return $retorno;
    }

//function

    function get_regras_aprovacao($COD_ITEM, $COD_SERVICO) {

        $db = new requestinsert_model();
        $sql_result = selectApprovalRules($COD_ITEM, $COD_SERVICO);
        $sql_result_count = countApprovalRules($COD_ITEM, $COD_SERVICO);

        if (!$sql_result AND !empty($erro)) {
            //echo $erro;
            return false;
        }

        if ($sql_result_count->fields['total'] == 0) {
            return array();
        }

        while ($tupla = $sql_result->fields) {
            $retorno[$tupla['idapproval']] = $tupla['idperson'];
            $sql_result->MoveNext();
        }
        return $retorno;
    }

    function get_flRecalcular_prazo($codItem, $codServico) {

        $db = new requestinsert_model();
        $sql_result = $db->getRecalcularPrazo($codItem, $codServico);
        $sql_result_count = $db->countRecalcularPrazo($codItem, $codServico);

        if ($sql_result_count->fields['total'] == 0) { //sem regras para essa combinaรงรฃo Item/serviรงo
            return (int) 0;
        }

        return (int) $sql_result->fields['fl_recalculate'];
    }

    public function json() {
        $this->validasessao();
        //error_reporting(E_ALL);
        $date_format = $this->getConfig('date_format');
		$hour_format = $this->getConfig('hour_format');
        
        $SES_COD_USUARIO = isset($_GET['SES_COD_USUARIO']) ? (int) $_GET['SES_COD_USUARIO'] : 0;

        $cod_user = $_SESSION["SES_COD_USUARIO"];

        $cod_group_user = explode(',', $_SESSION['SES_PERSON_GROUPS']);

        $COD_STATUS = $_POST['COD_STATUS'];
        $DatVencimento = $_POST['datvencimento'];


        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];
		
        if ($_POST['sortorder'] != 'undefined') {
            $sortorder = $_POST['sortorder'];
			$_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols'] = $sortorder;
        }else{
        	if($_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols']){
        		$sortorder = $_SESSION['SES_PERSONAL_USER_CONFIG']['ordercols'];
        	}else{
        		$order_mode = $_SESSION['SES_ORDER_ASC'];
		        if ($order_mode == 1)
		            $sortorder = 'asc';
		        else
		            $sortorder = 'desc';
        	}			
        }


        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;


        $start = (($page - 1) * $rp);
        
        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $sortname = $_POST['sortname'];
		$_SESSION['SES_PERSONAL_USER_CONFIG']['orderfield'] = $sortname;
		$wheredata = "";
        if ($DatVencimento == 1 or $DatVencimento == 2 or $DatVencimento == 3 or $DatVencimento == 4) { //gambiarra enquanto nao resolve o problema dos combobox no grlexigrid
            if ($DatVencimento == "1")
                $OPERADOR = " >= ";

            if ($DatVencimento == "3") {
                $OPERADOR = " <= ";
                $COD_STATUS = "3";
            }
            if ($DatVencimento == "4") {
                $COD_STATUS = "1";
                $OPERADOR = " <= ";
            }


            if ($DatVencimento <> "0") {
                if ($DatVencimento == "2")
                    if ($this->database  == 'oci8po') {
                        $wheredata = " AND a.expire_date = sysdate";
                    }else{
                        $wheredata = " AND date(a.expire_date) = date(now())";
                    }
                else{
                    if ($this->database  == 'oci8po') {
                        $wheredata = " AND a.expire_date $OPERADOR sysdate";
                    }else{
					   //$wheredata = " AND date(a.expire_date) < date(now())";
                        $wheredata = " AND a.expire_date $OPERADOR now()";
                    }
				}	
            }
			
			
        }

        $bd = new operatorview_model();

        //pega os iperson referente aos grupos cadastrados na tabla tbperson

        $rsIdPersonGroups = $bd->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
		$test = true;
        while (!$rsIdPersonGroups->EOF) {
			if ($test) {
				$idPersonGroups =  $rsIdPersonGroups->fields['idperson'];
				$test = false;
			} else	{ 
				$idPersonGroups .=  ",". $rsIdPersonGroups->fields['idperson'];
			}
            $rsIdPersonGroups->MoveNext();
        }
		/*
        $rsIdPersonOthersGroups = $bd->getIdPersonOthersGroup($_SESSION['SES_PERSON_GROUPS']);
        while (!$rsIdPersonOthersGroups->EOF) {
            $idPersonOthersGroups .= ',' . $rsIdPersonOthersGroups->fields['idperson'];
            $rsIdPersonOthersGroups->MoveNext();
        }
		*/
		
        if ($COD_STATUS) {
            if ($COD_STATUS > 0) {
				$status = "AND b.idstatus_source = ".$COD_STATUS." " ; 
            } else if ($COD_STATUS == 0) {
                $status = '';
            }
        }

        $TipConsulta = $_POST['tipconsulta'];
//die("tip:".$TipConsulta);
        switch ($TipConsulta) {
             case '100':
				$wheretip = 	"
								((c.ind_in_charge = 1
										and c.id_in_charge in($cod_user , $idPersonGroups))
										or (c.ind_operator_aux = 1
											and c.id_in_charge = $cod_user)
										or (c.id_in_charge in($cod_user , $idPersonGroups)
											and c.ind_track = 1))				
								";
                break;
            case '101':
				$wheretip = 	"
								((c.ind_in_charge = 1
										and c.id_in_charge in($cod_user ))
										or (c.ind_operator_aux = 1
											and c.id_in_charge = $cod_user)
										or (c.id_in_charge in($cod_user )
											and c.ind_track = 1))				
								";
                break;
            case '102':
				$wheretip = 	"
								((c.ind_in_charge = 1 and c.id_in_charge in($idPersonGroups))
								or (c.id_in_charge in($idPersonGroups)
											and c.ind_track = 1))
								";
					
                break;
            default:
                $wheretip = "";
                break;
        }		
        $where = "";
        if ($query) {
            switch ($qtype) {
                case 'date_request':
                    if($this->getConfig('date_format') == '%d/%m/%Y') {
                        $query = trim($query);
                        if (strlen($query) > 10 ) {
                            $dtend = substr($query, -10);
                            $dttemp = explode("/", $dtend);
                            $dtend = $dttemp[2].'-'.$dttemp[1].'-'.$dttemp[0];
                            $dtstart = substr($query, 0,10);
                            $dttemp = explode("/", $dtstart);
                            $dtstart = $dttemp[2].'-'.$dttemp[1].'-'.$dttemp[0];
                            $where = " AND date(a.entry_date) BETWEEN '$dtstart'  AND '$dtend' ";
                        } else {
                            $dttemp = explode("/", $query);
                            $dtsearch = $dttemp[2].'-'.$dttemp[1].'-'.$dttemp[0];
                            $where = " AND date(a.entry_date) = '$dtsearch' ";
                        }

                    }
                    break;
                case 'code_request':
                    $query = trim($query);
                    $where = " AND a.$qtype = '$query'";
                    break;
                case 'subject':
                    $where = " AND a.$qtype LIKE '%$query%' ";
                    break;
                case 'description':
                    if ($this->database  == 'oci8po') {
                        $where = " AND upper(a.$qtype) LIKE upper('%$query%') ";
                    }
                    else
                    {
                        $where = " AND CONVERT(a.$qtype USING latin1) LIKE '%$query%' ";
                    }
                    break;
				case 'name':
					$where = " AND own.$qtype LIKE '%$query%' ";
				
				default:
					$where = " AND $qtype LIKE '%$query%' ";
            }
            //$status = '';
        }

        if ($this->database == 'oci8po') {
          $entry_date  = " to_char(a.entry_date,'DD/MM/YYYY HH24:MI') entry_date " ;
          $expire_date = " to_char(a.expire_date,'DD/MM/YYYY HH24:MI') expire_date , a.expire_date  AS expire_date_color" ;
        }
        else
        {
		  $entry_date  = " DATE_FORMAT(a.entry_date, '".$date_format." ".$hour_format."') as entry_date" ;
		  $expire_date = " DATE_FORMAT(a.expire_date, '".$date_format." ".$hour_format."') as expire_date, a.expire_date AS expire_date_color" ;
        }

		
        $rsSolicitacao = $bd->getRequests($cod_user, $entry_date, $expire_date, $status, $wheredata, $where, $wheretip, $limit, $sortname, $sortorder);

        $rstotal = "";
        $total = "";

        if ($this->database == 'oci8po'){
            //$total = $rsSolicitacao->RecordCount();

            $total = $bd->getOracleNumberRequests($cod_user, $entry_date, $expire_date, $status, $wheredata, $where, $wheretip);

        }else{
            $rstotal = $this->found_rows();
            $total = $rstotal->fields['found_rows'];
        }

        $data['page'] = $page;
        $data['total'] = $total;

        $COD_SOL_ANTERIOR = 0;
        while (!$rsSolicitacao->EOF) {
            if (isset($rsSolicitacao->fields['grp_in_charge'])) {
                $grp_in_charge = $rsSolicitacao->fields['grp_in_charge'];
            } else {
                $grp_in_charge = 0;
            }

            $code_request = $rsSolicitacao->fields['code_request'];
            $orderexec = $bd->getOrder($code_request, $cod_user);
            
            if ($rsSolicitacao->fields['totatt']) {
                $attach = "<img src='" . path . "/app/themes/" . theme . "/images/ico_anexos.gif'>";
            } else {
                $attach = '';
            }

            /*
             * Problema que por enquanto surgiu apenas no cliente 201001012.
             * Ao criar solicitacoes por email, agumas vezes surgia um caracter estranho no Subject da solicitacao
             * que criava problema no json_encode [Function json_encode does not support ISO-8859-1 encoded data],
             * fazendo com que nao retornasse nada e o grid nao era montado.
             * Usando a opcao JSON_PARTIAL_OUTPUT_ON_ERROR no json_encode, o subject ficava em branco e o grid era
             * montado sem ele.
             * A solucao definitiva veio deste artigo:
             * http://www.pabloviquez.com/2009/07/json-iso-8859-1-and-utf-8-%E2%80%93-part2/
             *
             * Foi necessario alterar tambem o attendanceRequest.js
             */
            if($_SESSION['SES_LICENSE'] == 201001012){
                $linksubject = base64_encode($rsSolicitacao->fields['subject']) ;
            } else {
                $linksubject = "<a href='#/operator/viewrequest/id/" . $rsSolicitacao->fields['code_request'] . "' class='subject' >" . $rsSolicitacao->fields['subject'] . "</a>";
            }


            $idincharge = $rsSolicitacao->fields['id_in_charge'];
            $type_in_charge = $rsSolicitacao->fields['type_in_charge'];
            $linkcode = "<a href='#/operator/viewrequest/id/" . $rsSolicitacao->fields['code_request'] . "' style='text-decoration:none;'>" . $this->pintasolicitacao($idincharge, $type_in_charge, $cod_user, $rsSolicitacao->fields['ind_track'], $rsSolicitacao->fields['code_request']) . "</a>";

			$statuswithcolor = "<span style='color:" . $rsSolicitacao->fields['s_color'] . ";'>" . $rsSolicitacao->fields['status'] . "</span>";
            $priowithcolor = "<span style='color:" . $rsSolicitacao->fields['p_color'] . "; font-weight: bold;'>" . $rsSolicitacao->fields['priority'] . "</span>";

			/*
				1 = vencendo
				a.expire_date >= now()
				
				2 = Vencendo Hj
				date(a.expire_date) = date(now()
				
				3 = Vencidas - status 3
				a.expire_date <= now()
				
				4 = vencidas não assumidas - status 1
				a.expire_date <= now() 
			*/

			//die (strtotime($rsSolicitacao->fields['expire_date_color']) . " ---- ". $rsSolicitacao->fields['expire_date_color']); 
			$datetime_exp = strtotime($rsSolicitacao->fields['expire_date_color']);
			$datetime_now = strtotime(date('Y-m-d H:i:s'));
			$date_exp = strtotime(date("Y-m-d",strtotime($rsSolicitacao->fields['expire_date_color'])));
			$date_now = strtotime(date('Y-m-d'));
			$idstatus = $rsSolicitacao->fields['idstatus_source'];

			if($datetime_exp >= $datetime_now){
				//vencendo
				$color_exp = "#000000";
			}elseif($date_exp == $date_now){
				//Vencendo Hj
				$color_exp = "#0000FF";
			}elseif($datetime_exp <= $datetime_now && $idstatus == 3){
				//Vencidas
				$color_exp = "#FF0000";
			}elseif($datetime_exp <= $datetime_now && $idstatus == 1){
				//vencidas não assumidas
				$color_exp = "#990000";
			}

			$req_expire_date = "<span style='color:" . $color_exp . ";'>" . $rsSolicitacao->fields['expire_date'] . "</span>";
			//die(strtotime(date('Y-m-d H:i:s')) . '---------' .strtotime($rsSolicitacao->fields['expire_date_color']));

            $textbox = "<input value='" . $orderexec . "' type='text' class='requestorder' name='" . $rsSolicitacao->fields['code_request'] . "' id='" . $rsSolicitacao->fields['code_request'] . "' height='5px' size='1' onchange='changeOrder($code_request, $cod_user);'/>";
            $rows[] = array(
                "id" => $rsSolicitacao->fields['code_request'],
                "cell" => array(
                    $textbox
                    , $attach
                    , $linkcode
                    , $rsSolicitacao->fields['entry_date']
                    , $rsSolicitacao->fields['company']
                    , $rsSolicitacao->fields['personname']
                    , $rsSolicitacao->fields['type']
                    , $rsSolicitacao->fields['item']
                    , $rsSolicitacao->fields['service']
                    , $linksubject
                    , $statuswithcolor
                    , $rsSolicitacao->fields['in_charge']
                    , $priowithcolor
                    , $req_expire_date
                    , 'SES_COD_USUARIO'
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

        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            echo json_encode($data,  JSON_PARTIAL_OUTPUT_ON_ERROR );
        } else {
            echo json_encode($data);
        }



    }

    function pintasolicitacao($idincharge, $type_in_charge, $cod_user, $ind_track,$code_request) {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
		
		if($ind_track == 1 && $idincharge != $cod_user && $type_in_charge == "P"){
			//EU ESTOU ACOMPANHANDO
			$ret = "<span style='color: #808080; border-bottom:1px solid #808080; font-weight:bold;' title='" . $langVars['tlt_span_track_me'] . "' > " . $code_request . " </span>";
		}
		elseif($ind_track == 1 && $type_in_charge == "G"){
			//GRUPO ESTA ACOMPANHANDO
			$ret = "<span style='color: #000000; border-bottom:1px solid #000000; font-weight:bold;' title='" . $langVars['tlt_span_track_group'] . "' > " . $code_request . " </span>";
		}
		elseif($idincharge == $cod_user && $type_in_charge == "P"){
			//MINHA
			$ret = "<span style='color: #DF6300; border-bottom:1px solid #DF6300; font-weight:bold;' title='" . $langVars['tlt_span_my'] . "' > " . $code_request . " </span>";
		}elseif($ind_track == 0 && $type_in_charge == "G"){
			//MEU GRUPO
			$ret = "<span style='color: #0012DF; border-bottom:1px solid #0012DF; font-weight:bold;' title='" . $langVars['tlt_span_group'] . "' > " . $code_request . " </span>";
		}else{
			$ret = "<span style='color: #000000; border-bottom:1px solid #000000; font-weight:bold;' title='" . $langVars['tlt_span_track_group'] . "' > " . $code_request . " </span>";
		}		
        return $ret;
    }


    public function insertexorder() {
        extract($_POST);

        $bd = new operatorview_model();
        $ins = $bd->insertExecutionOrder($code, $value, $person);
        if ($ins) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function deleteexorder() {
        extract($_POST);

        $bd = new operatorview_model();
        $ins = $bd->deleteExecutionOrder($code, $value, $person);
        if ($ins) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function updateexorder() {
        extract($_POST);

        $bd = new operatorview_model();
        $ins = $bd->updateExecutionOrder($code, $value, $person);
        if ($ins) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function checkOrder() {
        extract($_POST);

        $bd = new operatorview_model();
        $check = $bd->checkOrder($code, $value, $person);
        if ($check->fields) {
            echo $check;
        } else {
            return false;
        }
    }
	
	public function queryrequest(){
		$this->validasessao();
        $smarty = $this->retornaSmarty();
		$typeperson = $_SESSION['SES_TYPE_PERSON'];
		$smarty->assign('typeperson', $typeperson);
		$smarty->display('queryrequest.tpl.html');
	}
	
	public function queryviewrequest() {
        $this->validasessao();
		$hdk_url = $this->getConfig('hdk_url');
		$hour_format  = $this->getConfig('hour_format');
		$path_default = $this->getConfig('path_default');
		$lang_default = $this->getConfig('lang');
		
		if($_SESSION['SES_COD_TIPO'] == 2) {			
			$url = $hdk_url.'helpdezk/user#/user/viewrequest/id/'.$id;
			die("<script> location.href = '".$url."'; </script>");
		}
		
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $id = $this->getParam('id');
        $bd = new operatorview_model();
        $req = $bd->getRequestData($id);
		if(!$req->fields['code_request']){
			echo "<p class='mb10 ml20'><strong>".$langVars['No_result']."</strong></p>";
			return false;
		}
		$typeperson = $_SESSION['SES_TYPE_PERSON'];
		if($typeperson != 1 && $typeperson != 3){
			echo "<p class='mb10 ml20'><strong>".$langVars['Alert_no_permission']."</strong></p>";
			return false;
		}
		
        $idperson = $_SESSION['SES_COD_USUARIO'];
		$emptynote = $_SESSION['SES_EMPTY_NOTE'];
		if(!$emptynote) $emptynote = 0;
        $namecreator = $bd->getNameCreator($req->fields['idperson_creator'], $id);
        $owner = $req->fields['personname'];
        $department = $req->fields['department'];
        $source = $req->fields['source'];
        $status = $req->fields['status'];
        $iddepartment = $req->fields['iddepartment'];
        $company = $bd->getCompanyName($iddepartment);

        $entry_time = $bd->getTime($req->fields['entry_date'], $hour_format);
        $entry_date = $this->formatDate($req->fields['entry_date']);

        $idarea = $req->fields['idarea'];
        $idtype = $req->fields['idtype'];
        $iditem = $req->fields['iditem'];
        $prorrogs = $req->fields['extensions_number'];
        $incharge = $req->fields['id_in_charge'];
        $inchargename = $req->fields['in_charge'];
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
            $subject = $this->mc_decrypt($req->fields['subject'], $key) ;
            $description = $this->mc_decrypt($req->fields['description'], $key) ;

        } else {
            $subject = $req->fields['subject'];
            $description = $req->fields['description'];
        }

        //$subject = $req->fields['subject'];
        //$description = $req->fields['description'];

        $expire_date = $req->fields['expire_date'];
        $expire_date2 = $this->formatDate($expire_date);
        if ($_SESSION['SES_SHARE_VIEW'] == 1) {
            $checkedassume = 'checked';
        } else {
            $checkedassume = '';
        }
        $qtprorrogation = $_SESSION['SES_QT_PRORROGATION'];
        if ($qtprorrogation == NULL) {
            $smarty->assign('show_btn_change_expire', 1);
        } else {
            if ($qtprorrogation == 0) {
                $smarty->assign('show_btn_change_expire', 0);
            } else {
                if ($prorrogs < $qtprorrogation) {
                    $smarty->assign('show_btn_change_expire', 1);
                } else {
                    $smarty->assign('show_btn_change_expire', 0);
                }
            }
        }
        
        $reopen = $_SESSION['SES_IND_REOPEN'];
        $obrigatorytime = $_SESSION['SES_IND_ENTER_TIME'];
        $now = date('Ymd');
        $now = $this->formatDate($now);
        $entrydate = $entry_date . " " . $entry_time;
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
		
		
		$opvm = new operatorview_model();
		$operatorgroups = $opvm->getOperatorGroups($idperson);
		while (!$operatorgroups->EOF) {
            $camposopg[] = $operatorgroups->fields['idpergroup'];
            $valoresopg[] = $operatorgroups->fields['pername'];
            $operatorgroups->MoveNext();
        }
		$smarty->assign('grpids', $camposopg);
        $smarty->assign('grpvals', $valoresopg);
		
              		
        $selectattach = $bd->selectAttach($id);
        if ($selectattach->fields) {
            $hasattach = "";
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
            $hasattach = "style='display: none;'";
        }
	    $typeperson = $_SESSION['SES_TYPE_PERSON'];
        $notes = $bd->getRequestNotes($id);
        $notetable = "<table border='0' cellpadding='0' cellspacing='0' class='notetable'>";
		$notetable .= "
				<colgroup>
					<col width='40'/>
					<col width='40'/>
					<col />
				</colgroup>
		";
        while (!$notes->EOF) {
            $notetable.= "<tr>";
            $idnote = $notes->fields['idnote'];
            
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
			//OLNLY OPERATOR
			elseif($notes->fields['idtype'] == '2'){
				$ico_note = "<img src='" . path . "/app/themes/" . theme . "/images/atendimento_close_25.png' alt='" . $langVars['Operator'] . "' />";
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
                    $attachicon =        "<a href='javascript:;' onclick=\"openDownloadPopUP('" . $custom_attach_path . "', '" . $notes->fields['idnote_attachment'] . $ext . "','$filename');\" class='file' name='" . $path_default . $custom_attach_path . $filename . "'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='24px' width='24px' title='" . $langVars['Attachment'] . "'></a>";
				}
				else {
					$attachicon ="<a href='downloads/getFile2/id/". $notes->fields['idnote_attachment'] ."/type/note' class='file'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='24px' width='24px' title='" . $langVars['Attachment'] . "'></a>";
                }
                
            } else {
                $attachicon  = "";
            }
            $notedescription = $notes->fields['description'];
            $notedate = $notes->fields['entry_date'];
            $notenameper = $notes->fields['idperson'];
            $notenameper = $bd->getUserName($notenameper);
            $ipadress = $notes->fields['ip_adress'];
            $hour_type = $notes->fields['hour_type'];
            $notetime = $bd->getTime($notedate, $hour_format);
            $minutes = "";
            $min = (int) $notes->fields['minutes'];
            $offset = $notes->fields['minutes'] - $min;
            $seg = number_format($offset * 60, 0, '.', ',');
            if ($min) {
                $minutes = $min . " min ";
            }
            if ($seg) {
                $minutes.= $seg . " s";
            }
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
		$now = date('Ymd');
        $now = $this->formatDate($now);
        $email = $req->fields['email'];
        $smarty->assign('expiry_view', "");
        $smarty->assign('expiry', "0");
        $smarty->assign('email', $email);
        $smarty->assign('now', $now);
        $smarty->assign('idperson', $idperson);
		$smarty->assign('emptynote', $emptynote);		
        $smarty->assign('notetable', $notetable);
        $smarty->assign('request_code', $id);
        $smarty->assign('owner', $owner);
        $smarty->assign('department', $department);
        $smarty->assign('checkedassume', $checkedassume);
        $smarty->assign('obrigatorytime', $obrigatorytime);
        $smarty->assign('idstatus', $idstatus);
        $smarty->assign('status', $status);
        $smarty->assign('source', $source);
        $smarty->assign('entry', $entrydate);
        $smarty->assign('expire_date', $expire_date2);
        $smarty->assign('expire_hour', $expire_hour);
        $smarty->assign('company', $company);
        $smarty->assign('idarea', $idarea);
        $smarty->assign('idtype', $idtype);
        $smarty->assign('iditem', $iditem);
        $smarty->assign('idservice', $idservice);
        $smarty->assign('idway', $idway);
        $smarty->assign('idreason', $idreason);
        $smarty->assign('idpriority', $idpriority);
        $smarty->assign('incharge', $incharge);
        $smarty->assign('inchargename', $inchargename);
        $smarty->assign('subject', $subject);
        $smarty->assign('description', $description);
		$smarty->assign('typeincharge', $req->fields['typeincharge']);
		$smarty->assign('typeperson', $typeperson);

		if($_SESSION['SES_IND_EQUIPMENT'] == 1) {
			$smarty->assign('have_equipment',	'1');
			$smarty->assign('os_number', $os);
			$smarty->assign('serial_num', $serial);
			$smarty->assign('label', $label);
		}
		
        $smarty->assign('hasattach', $hasattach);
        $smarty->assign('attach1', $attach);
        $db = new operatorview_model();
        $repgroups = $db->getRepassGroups();
        $replist = "<select name='replist' id='replist' size='10' style='width: 350px; background-color: #eee;' onclick='getAbilities();'>";
        while (!$repgroups->EOF) {
            $replist.="<option value=" . $repgroups->fields['idperson'] . ">(" . $repgroups->fields['level'] . ") " . $repgroups->fields['name'] . "</option>";
            $repgroups->MoveNext();
        }
        $replist.="</select>";
        $smarty->assign('repgrouplist', $replist);
        $smarty->assign('creator', $namecreator);
		
		$data = new person_model();
		$rs = $data->getOperatorAuxCombo($id,'in');
        while (!$rs->EOF) {
			$aux[] = $rs->fields['name']; 
            $rs->MoveNext();
        }
		
		$dbrr =  new requestrules_model();
		$rules = $dbrr->checkApprovalBt($id);
		$approving = $rules->RecordCount();
		
		$smarty->assign('approving', $approving);
		$smarty->assign('usersaux', $aux);
		$smarty->assign('numusersaux', count($aux));
        $smarty->display('queryviewrequest.tpl.html');
    }
	

    public function viewrequest() {
        $hdk_url = $this->getConfig('hdk_url');
		$hour_format  = $this->getConfig('hour_format');
		$path_default = $this->getConfig('path_default');
		$lang_default = $this->getConfig('lang');		
		
        $this->validasessao();
		$id = $this->getParam('id');
		
		if($_SESSION['SES_COD_TIPO'] == 2) {			
			$url = $hdk_url.'helpdezk/user#/user/viewrequest/id/'.$id;
			die("<script> location.href = '".$url."'; </script>");
		}
		
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $bd = new operatorview_model();
        $req = $bd->getRequestData($id);
        $idperson = $_SESSION['SES_COD_USUARIO'];
		$emptynote = $_SESSION['SES_EMPTY_NOTE'];
		if(!$emptynote) $emptynote = 0;
        //$namecreator = $bd->getNameCreator($req->fields['idperson_creator'], $id);
		$namecreator = $req->fields['name_creator'];
        $owner = $req->fields['personname'];
		$phone_number = $req->fields['phone_number'];
		$cel_phone = $req->fields['cel_phone'];
		$branch_number = $req->fields['branch_number'];
        $department = $req->fields['department'];
        $source = $req->fields['source'];
        $status = $req->fields['status'];
        $iddepartment = $req->fields['iddepartment'];
        $company = $bd->getCompanyName($iddepartment);
        $entry_time = $bd->getTime($req->fields['entry_date'], $hour_format);
        $entry_date = $this->formatDate($req->fields['entry_date']);
        $idarea = $req->fields['idarea'];
        $idtype = $req->fields['idtype'];
        $iditem = $req->fields['iditem'];
        $prorrogs = $req->fields['extensions_number'];
        $incharge = $req->fields['id_in_charge'];
        $inchargename = $req->fields['in_charge'];
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
            $subject = $this->mc_decrypt($req->fields['subject'], $key) ;
            $description = $this->mc_decrypt($req->fields['description'], $key) ;

        } else {
            $subject = $req->fields['subject'];
            $description = $req->fields['description'];
        }
        //$subject = $req->fields['subject'];
        //$description = $req->fields['description'];


        $expire_date = $req->fields['expire_date'];
        $expire_date2 = $this->formatDate($expire_date);
        if ($_SESSION['SES_SHARE_VIEW'] == 1) {
            $checkedassume = 'checked="checked"';
        } else {
            $checkedassume = '';
        }
        $qtprorrogation = $_SESSION['SES_QT_PRORROGATION'];
        if ($qtprorrogation == NULL) {
            $smarty->assign('show_btn_change_expire', 1);
        } else {
            if ($qtprorrogation == 0) {
                $smarty->assign('show_btn_change_expire', 0);
            } else {
                if ($prorrogs < $qtprorrogation) {
                    $smarty->assign('show_btn_change_expire', 1);
                } else {
                    $smarty->assign('show_btn_change_expire', 0);
                }
            }
        }
        $reopen = $_SESSION['SES_IND_REOPEN'];
        $obrigatorytime = $_SESSION['SES_IND_ENTER_TIME'];
        if ($this->database == 'oci8po') {
            $now = date('d/m/Y');
            $now = $this->formatDate($now);
        }
        else
        {
            $now = date('Ymd');
            $now = $this->formatDate($now);
        }


        $entrydate = $entry_date . " " . $entry_time;
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
		
		
		$opvm = new operatorview_model();
		$operatorgroups = $opvm->getOperatorGroups($idperson);
		while (!$operatorgroups->EOF) {
            $camposopg[] = $operatorgroups->fields['idpergroup'];
            $valoresopg[] = $operatorgroups->fields['pername'];
            $operatorgroups->MoveNext();
        }
		$smarty->assign('grpids', $camposopg);
        $smarty->assign('grpvals', $valoresopg);
		
              		
        $selectattach = $bd->selectAttach($id);
        if ($selectattach->fields) {
            $hasattach = "";
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
            $hasattach = "style='display: none;'";
        }
;        $typeperson = $_SESSION['SES_TYPE_PERSON'];

		$idstatus_source = $bd->getIdStatusSource($idstatus);
		$status_source = $idstatus_source->fields['idstatus_source'];

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
            $notetable.= "<tr>";
            $idnote = $notes->fields['idnote'];

            
			if($status_source == 3){
	            if ($notes->fields['idtype'] != '3' && $_SESSION['SES_TYPE_PERSON'] != '2' && $_SESSION['SES_IND_DELETE_NOTE'] == '1' && $_SESSION['SES_COD_USUARIO'] == $notes->fields['idperson']) 
	            {
	                $ico_del = "<a href='javascript:;' onclick=\"deleteNote('$idnote', '$id', '$typeperson');\"><img src='" . path . "/app/themes/" . theme . "/images/delete_new.png' height='15px' width='15px' title='" . $langVars['Delete'] . "'></a>";
	            } else {
	                $ico_del = "";
	            }
			}else{
				$ico_del = "";
			}


			/*
			  
			 if ($rsApontamento->Fields("IND_CALLBACK")){
				?><img src="<?=$path_abs;?>images/ico_callback.gif" alt="<?=$l_sdt["alt"]["apont_do_usuario"]?>" vspace="4"> <?
			// se foi cadastro por usuï¿½rio	
			}elseif ($rsApontamento->Fields("COD_TIPO") ==1 && $rsApontamento->Fields("COD_USUARIO") == $rsSolicitacao->Fields("COD_USUARIO")) {  
				?> <img src="<?=$path_abs;?>images/icones/ico_apont_usuarios.gif" alt="<?=$l_sdt["alt"]["apont_do_usuario"]?>" vspace="4"><? 
			 // se for realizado por atendente
			} elseif ($rsApontamento->Fields("COD_TIPO") == 1) { 
				$qtdApontAtend++; ?>
				<img src="<?=$path_abs;?>images/icones/ico_apont_atendentes.gif" alt="<?=$l_sdt["alt"]["apont_do_atendente"]?>" vspace="4"> <? 
			 // se for visivel sï¿½ para atendnete 
			} elseif ($rsApontamento->Fields("COD_TIPO") == 2) { 
				$qtdApontAtend++; ?>
				<img src="<?=$path_abs;?>images/icones/ico_apont_atendentes_cadeado.gif" alt="<?=$l_sdt["alt"]["apont_visivel_atend"]?>" vspace="4"> <? 
			//se for de sistema 
			} else{
				?> <img src="<?=$path_abs;?>images/icones/ico_apont_system.gif" vspace="4" alt="<?=$l_sdt["alt"]["apont_reg_sistema"]?>"><? 
			} ?>	 
			
			*/
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
			//OLNLY OPERATOR
			elseif($notes->fields['idtype'] == '2'){
				$ico_note = "<img src='" . path . "/app/themes/" . theme . "/images/atendimento_close_25.png' alt='" . $langVars['Operator'] . "' />";
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
                    $attachicon =        "<a href='javascript:;' onclick=\"openDownloadPopUP('" . $custom_attach_path . "', '" . $notes->fields['idnote_attachment'] . $ext . "','$filename');\" class='file' name='" . $path_default . $custom_attach_path . $filename . "'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='24px' width='24px' title='" . $langVars['Attachment'] . "'></a>";
				}
				else {
					$attachicon ="<a href='downloads/getFile2/id/". $notes->fields['idnote_attachment'] ."/type/note' class='file'><img src='" . path . "/app/themes/" . theme . "/images/floppy.png' height='24px' width='24px' title='" . $langVars['Attachment'] . "'></a>";
                }
                
            } else {
                $attachicon  = "";
            }
            $notedescription = $notes->fields['description'];
            $notedate = $notes->fields['entry_date'];
            $notenameper = $notes->fields['idperson'];
            $notenameper = $bd->getUserName($notenameper);
            $ipadress = $notes->fields['ip_adress'];
            $hour_type = $notes->fields['hour_type'];
            $notetime = $bd->getTime($notedate, $hour_format);
            $minutes = "";
            $min = (int) $notes->fields['minutes'];
            $offset = $notes->fields['minutes'] - $min;
            $seg = number_format($offset * 60, 0, '.', ',');
            if ($min) {
                $minutes = $min . " min ";
            }
            if ($seg) {
                $minutes.= $seg . " s";
            }
            $notetable.= "<td align='center' valign='middle'> $ico_del </td>";
            $notetable.= "<td align='center' valign='middle'> $attachicon </td>";
            $notetable.= "<td align='center' valign='middle'> $ico_note</td>";

            if ($notes->fields['minutes'] != 0) {
                $timeexp = "<strong>".$langVars['Time_exp'].": </strong>" . $this->formatDate($notedate) . " " . $notes->fields['start_hour'] . " - " . $notes->fields['finish_hour'] . " (" . $notes->fields['diferenca'] . ")";
                if ($hour_type==2) {
                    $timeexpnote = "<span class='block'>".$timeexp." - ".$langVars['Extra']." </span>";
                } else {
                    $timeexpnote = "<span class='block'>".$timeexp."</span>";
                }

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
            $notes->MoveNext();
        }
        $notetable.= "</table>";


        if ($lang_default == 'en_US') {
            $hour_format = "%h:%i";
        }
        $expire_hour = $bd->getTime($expire_date, $hour_format);

        if ($lang_default == 'pt_BR') {
            $smarty->assign('hour_label', '');
        } else {
            $hour_format2 = "%p";
            $hour_label = $bd->getTime($expire_date, $hour_format2);
            $smarty->assign('hour_label', $hour_label);
        }
        $email = $req->fields['email'];
         
        $smarty->assign('hour_format', $hour_format);
        $smarty->assign('expiry', "0");
        $smarty->assign('email', $email);
        $smarty->assign('now', $now);
        $smarty->assign('idperson', $idperson);
		$smarty->assign('emptynote', $emptynote);
        $smarty->assign('notetable', $notetable);
        $smarty->assign('request_code', $id);
        $smarty->assign('owner', $owner);
		if($_SESSION['SES_REQUEST_SHOW_PHONE'] == 1) {
			$smarty->assign('show_phone',	'1');
			$smarty->assign('phone_number', preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $phone_number));
			$smarty->assign('branch_number', $branch_number);
			$smarty->assign('cel_phone', preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $cel_phone));
		}
        $smarty->assign('department', $department);
        $smarty->assign('checkedassume', $checkedassume);
        $smarty->assign('obrigatorytime', $obrigatorytime);
        $smarty->assign('idstatus', $idstatus);
        $smarty->assign('status', $status);
        $smarty->assign('source', $source);
        $smarty->assign('entry', $entrydate);
        $smarty->assign('expire_date', $expire_date2);
        $smarty->assign('expire_hour', $expire_hour);
        $smarty->assign('company', $company);
        $smarty->assign('idarea', $idarea);
        $smarty->assign('idtype', $idtype);
        $smarty->assign('iditem', $iditem);
        $smarty->assign('idservice', $idservice);
        $smarty->assign('idway', $idway);
        $smarty->assign('idreason', $idreason);
        $smarty->assign('idpriority', $idpriority);
        $smarty->assign('incharge', $incharge);
        $smarty->assign('inchargename', $inchargename);
        //$smarty->assign('os', $os);
        //$smarty->assign('serial_num', $serial);
        $smarty->assign('subject', $subject);
        $smarty->assign('description', $description);
		$smarty->assign('typeincharge', $req->fields['typeincharge']);
		$smarty->assign('idstatus_source', $status_source);
		if($_SESSION['SES_IND_EQUIPMENT'] == 1) {
			$smarty->assign('have_equipment',	'1');
			$smarty->assign('os_number', $os);
			$smarty->assign('serial_number', $serial);
			$smarty->assign('label', $label);
		}

		$data = new person_model();
		$rs = $data->getOperatorAuxCombo($id,'in');

        while (!$rs->EOF) {
			$aux[] = $rs->fields['name'];
			$arrayAux[] = $rs->fields['idperson'];
            $rs->MoveNext();
        }
		$smarty->assign('usersaux', $aux);
		$smarty->assign('numusersaux', count($aux));



		if($status_source == 3){
			
			$status_model = new status_model();
			$getStatus = $status_model->selectStatus("WHERE idstatus_source = 3"); 
			while (!$getStatus->EOF) {
	            $camposstatus[] = $getStatus->fields['idstatus'];
	            $valorstatus[] = $getStatus->fields['name'];
	            $getStatus->MoveNext();
	        }
			
			//PEGAR OS STATUS PAR ALTERAÇÃO
			
			$smarty->assign('stids', $camposstatus);
	        $smarty->assign('stvals', $valorstatus);
		
		
		}

		$dbrr =  new requestrules_model();
		$rules = $dbrr->checkApprovalBt($id);
		$approving = $rules->RecordCount();

					
			if($approving){
				if($rules->fields['idperson'] == $_SESSION['SES_COD_USUARIO'] && $rules->fields['order'] == 1){					
					$idswitch_status = "app1";
				}elseif($rules->fields['idperson'] == $_SESSION['SES_COD_USUARIO'] && $rules->fields['order'] > 1){
					$idswitch_status = "app2";
				}
				/*
				$lastapp = $dbrr->getLastApprovalBt($id);
				while (!$lastapp->EOF) {
		            $lastapplist[] = $lastapp->fields['idperson'];
		          	$lastapp->MoveNext();
		        }				
				if(!in_array($idperson, $lastapplist) && sizeof($lastapplist) > 0) {
					$idswitch_status = "app2";
				}			*/	
			}elseif($idstatus == 2){
				$idswitch_status = 2;
			}else{
				$idswitch_status = $status_source;	
			}			
			
			switch($idswitch_status){
				case "app1":
					$smarty->assign('displaychanges', '0');
					$smarty->assign('displayassume',  '0');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '0');
					$smarty->assign('displayreject',  '0');
					$smarty->assign('displayclose',   '0');
					$smarty->assign('displayreopen',  '0');
					$smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '1');
					$smarty->assign('displayreturn',  '0');
					$smarty->assign('displayreprove', '1');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
				
				case "app2":
					$smarty->assign('displaychanges', '0');
					$smarty->assign('displayassume',  '0');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '0');
					$smarty->assign('displayreject',  '0');
					$smarty->assign('displayclose',   '0');
					$smarty->assign('displayreopen',  '0');
					$smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '1');
					$smarty->assign('displayreturn',  '1');
					$smarty->assign('displayreprove', '1');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
				
				case "1": //NEW				
					$smarty->assign('displaychanges', '1');
					$smarty->assign('displayassume',  '1');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '1');
					$smarty->assign('displayreject',  '1');
					$smarty->assign('displayclose',   '0');
					$smarty->assign('displayreopen',  '0');
					$smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '0');
					$smarty->assign('displayreturn',  '0');
					$smarty->assign('displayreprove', '0');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
				case "2": //REPASSED					
					$myGroupsIdPerson = $bd->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
					while (!$myGroupsIdPerson->EOF) {			           
			            $myGroupsIdPersonArr[] = $myGroupsIdPerson->fields['idperson'];
						$myGroupsIdPerson->MoveNext();
					}
					if(in_array($incharge, $myGroupsIdPersonArr) || $incharge == $idperson){
						//SOU RESPONSÁVEL POR ESTA SOL						
						$smarty->assign('displaychanges', '1');
						$smarty->assign('displayassume',  '1');
						$smarty->assign('displayopaux',   '0');
						$smarty->assign('displayrepass',  '1');
						$smarty->assign('displayreject',  '1');
						$smarty->assign('displayclose',   '0');
						$smarty->assign('displayreopen',  '0');
						$smarty->assign('displaycancel',  '0');
						$smarty->assign('displayaux', 	  '0');
						$smarty->assign('displayapprove', '0');
						$smarty->assign('displayreturn',  '0');
						$smarty->assign('displayreprove', '0');
						$smarty->assign('displaynote', 	  '0');
						$smarty->assign('displayprint',   '1');
					}	
					else{
						//NÃO SOU RESPONSÁVEL POR ESTA SOL
						$smarty->assign('displaychanges', '0');
						if ($_SESSION['SES_IND_ASSUME_OTHER'] == 1) {
							$smarty->assign('displayassume',  '1');
						}else{
							$smarty->assign('displayassume',  '0');
						}
						$smarty->assign('displayopaux',   '0');
						$smarty->assign('displayrepass',  '0');
						$smarty->assign('displayreject',  '0');
						$smarty->assign('displayclose',   '0');
						$smarty->assign('displayreopen',  '0');
						$smarty->assign('displaycancel',  '0');
						$smarty->assign('displayaux', 	  '0');
						$smarty->assign('displayapprove', '0');
						$smarty->assign('displayreturn',  '0');
						$smarty->assign('displayreprove', '0');
						$smarty->assign('displaynote', 	  '0');
						$smarty->assign('displayprint',   '1');
					}					
					break;
				case "3"://ON ATTENDANCE
					$myGroupsIdPerson = $bd->getIdPersonGroup($_SESSION['SES_PERSON_GROUPS']);
					while (!$myGroupsIdPerson->EOF) {			           
			            $myGroupsIdPersonArr[] = $myGroupsIdPerson->fields['idperson'];
						$myGroupsIdPerson->MoveNext();
					}
					if(in_array($incharge, $myGroupsIdPersonArr) || $incharge == $idperson){
						//SOU RESPONSÁVEL POR ESTA SOL			
						$smarty->assign('displaychanges', '1');
						$smarty->assign('displayassume',  '0');
						$smarty->assign('displayopaux',   '1');
						$smarty->assign('displayrepass',  '1');
						$smarty->assign('displayreject',  '0');
						$smarty->assign('displayclose',   '1');
						$smarty->assign('displayreopen',  '0');
						$smarty->assign('displaycancel',  '0');
						$smarty->assign('displayaux', 	  '0');
						$smarty->assign('displayapprove', '0');
						$smarty->assign('displayreturn',  '0');
						$smarty->assign('displayreprove', '0');
						$smarty->assign('displaynote', 	  '1');
						$smarty->assign('displayprint',   '1');
					}	
					else{
						//NÃO SOU RESPONSÁVEL POR ESTA SOL
						$smarty->assign('displaychanges', '0');
						if ($_SESSION['SES_IND_ASSUME_OTHER'] == 1) {
							$smarty->assign('displayassume',  '1');
						}else{
							$smarty->assign('displayassume',  '0');
						}
						$smarty->assign('displayopaux',   '0');
						$smarty->assign('displayrepass',  '0');
						$smarty->assign('displayreject',  '0');
						$smarty->assign('displayclose',   '0');
						$smarty->assign('displayreopen',  '0');
						$smarty->assign('displaycancel',  '0');
						$smarty->assign('displayaux', 	  '0');
						$smarty->assign('displayapprove', '0');
						$smarty->assign('displayreturn',  '0');
						$smarty->assign('displayreprove', '0');
						if(in_array($idperson, $arrayAux)){
							$smarty->assign('displaynote','1');
						}else{
							$smarty->assign('displaynote','0');	
						}
						$smarty->assign('displayprint',   '1');
					}
					break;
				case "4":
					//WAITING FOR APP
					$smarty->assign('displaychanges', '0');
					$smarty->assign('displayassume',  '0');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '0');
					$smarty->assign('displayreject',  '0');
					$smarty->assign('displayclose',   '0');
					$smarty->assign('displayreopen',  '0');
					$smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '0');
					$smarty->assign('displayreturn',  '0');
					$smarty->assign('displayreprove', '0');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
				case "5":
					//FINISHED
					$smarty->assign('displaychanges', '0');
					$smarty->assign('displayassume',  '0');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '0');
					$smarty->assign('displayreject',  '0');
					$smarty->assign('displayclose',   '0');
					if ($reopen == '0')
	                    $smarty->assign('displayreopen',  '0');
	                else
	                    $smarty->assign('displayreopen',  '1');
	                $smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '0');
					$smarty->assign('displayreturn',  '0');
					$smarty->assign('displayreprove', '0');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
				case "6":
					//REJECTED
					$smarty->assign('displaychanges', '0');
					$smarty->assign('displayassume',  '0');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '0');
					$smarty->assign('displayreject',  '0');
					$smarty->assign('displayclose',   '0');
					$smarty->assign('displayreopen',  '0');
					$smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '0');
					$smarty->assign('displayreturn',  '0');
					$smarty->assign('displayreprove', '0');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
				default:
					$smarty->assign('displaychanges', '0');
					$smarty->assign('displayassume',  '0');
					$smarty->assign('displayopaux',   '0');
					$smarty->assign('displayrepass',  '0');
					$smarty->assign('displayreject',  '0');
					$smarty->assign('displayclose',   '0');
					$smarty->assign('displayreopen',  '0');
					$smarty->assign('displaycancel',  '0');
					$smarty->assign('displayaux', 	  '0');
					$smarty->assign('displayapprove', '0');
					$smarty->assign('displayreturn',  '0');
					$smarty->assign('displayreprove', '0');
					$smarty->assign('displaynote', 	  '0');
					$smarty->assign('displayprint',   '1');
					break;
			}
				
       
        $smarty->assign('hasattach', $hasattach);
        $smarty->assign('attach1', $attach);
        $db = new operatorview_model();
        $repgroups = $db->getRepassGroups();
        $replist = "<select name='replist' id='replist' size='10' style='width: 350px; background-color: #eee;' onclick='getAbilities();'>";
        while (!$repgroups->EOF) {
            $replist.="<option value=" . $repgroups->fields['idperson'] . ">(" . $repgroups->fields['level'] . ") " . $repgroups->fields['name'] . "</option>";
            $repgroups->MoveNext();
        }
        $replist.="</select>";
        $smarty->assign('repgrouplist', $replist);
        $smarty->assign('creator', $namecreator);
		
		
		if($_SESSION['SES_REQUEST_ADDINFO']){
			$db = new addinfo_model();
			$getAddInfo = $db->getRequestAddInfos($id); 
			
			$smarty->assign('addinfoname', $getAddInfo->fields['name']);
		}
		
		$smarty->assign('SES_REQUEST_ADDINFO', $_SESSION['SES_REQUEST_ADDINFO']);
		
		$smarty->display('viewrequest.tpl.html');
    }

	public function updateStatusRequest(){
		$this->validasessao();
		
		$code_request = $_POST['code_request'];
		$idstatus = $_POST['idstatus'];
		$person = $_SESSION['SES_COD_USUARIO'];
        $way = $_POST['way'];

		$db = new operatorview_model();
		$db->BeginTrans();
		
        //OBRIGAR USUÁRIO KILLING INFORMAR FORNECEDOR AO ESCOLHER STATUS "AGUARDANDO FORNCEDOR"
        if($_SESSION['SES_LICENSE'] == 200701008){            
            if($idstatus == 50 && $way){
                $updWay = $db->updateWay($code_request, $way);
                if (!$updWay) {
                    $db->RollbackTrans();
                    echo json_encode(array("success" => 0,"msg"=> "aqui"));
                    return false;
                }
            }elseif($idstatus == 50 && !$way){
                 $return = array(
                            "success" => 0,
                            "msg"   => "Selecione um fornecedor."
                        );
                echo json_encode($return);
                return;
            }
        }
        $inslog = $db->changeRequestStatus($idstatus, $code_request, $person); //SALVA NO LOG A MUDANÇA DE STATUS Q VAI FAZER
        if (!$inslog) {
        	$db->RollbackTrans();
            echo json_encode(array("success" => 0));
        	return false;
        }		
		
		$upStatus = $db->updateRequestStatus($idstatus,$code_request);
		if(!$upStatus){
			$db->RollbackTrans();
            echo json_encode(array("success" => 0));
            return false;
		}	
        
        $db->CommitTrans();
        echo json_encode(array("success" => 1));
	}
	
	public function approveRequest(){
		$this->validasessao();
		$smarty = $this->retornaSmarty();
		$dbrr =  new requestrules_model();
		$db = new operatorview_model();
		$langVars = $smarty->get_config_vars();
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$JUSTF = strip_tags($_POST["justApproval"]);
		$code = $_POST['code'];
		
		$num_approve = $dbrr->checkNumApp($code);

		if($num_approve->fields['num_approve'] > 1)
			$note = "<p>".$langVars['Request_app_rep_next']."</p><p><strong>".$langVars['Justification'].":</strong> $JUSTF</p>";
		else
			$note = "<p>".$langVars['Request_app_rep_care']."</p><p><strong>".$langVars['Justification'].":</strong> $JUSTF</p>";
        $date = "now()";
        $ipadress = $_SERVER['REMOTE_ADDR'];
        
		$db->BeginTrans();
        $ins = $db->insertNote($code, $idperson, $note, $date, 0, 0, 0, $date, 1, null, 1, 3, $ipadress, 0, 'NULL');
        if($ins){
			$id_note = $db->insertNoteLastID();
			$upidnote = $dbrr->updateApprovalNote($id_note, $idperson, $code);
		   	if(!$upidnote){
				$dbrr->RollbackTrans();
				return false;
		   	}
			
			$app = $dbrr->checkApproval($code);
			$nextapp = $app->RecordCount();
		   	
			$db->removeIncharge($code);
			
			$db_reqinsert = new requestinsert_model();
			
			if($nextapp){//Tem mais aprovdor
				$APROVADOR = $app->fields['idperson'];
				
				$rs = $db_reqinsert->insertRequestCharge($code, $APROVADOR, 'P', '1');
				
				if($rs){
					$this->sendEmail('approve', $code);
					$dbrr->CommitTrans();
					echo "OK";
				}else{
					$dbrr->RollbackTrans();
					return false;
				}
				
			}else{//Não tem mais aprovador, passar sol para o respectivo responsavel
				$resp = $dbrr->getRespOriginal($code);
				
				$idperson = $resp->fields['id_in_charge'];
				$type = $resp->fields['type'];
				if($resp){										
					$rs = $db_reqinsert->insertRequestCharge($code, $idperson, $type, '1');
					if($rs){
						
						$recal = $dbrr->getRecalculate($code);
						if($recal->fields['recalculate']){
							$DAT_CADASTRO = date("d/m/Y");
							$datCalcPrazo = date('Ymd');
							$HOR_CADASTRO = date("H:i");
							$datCalcPrazo .= date('Hi');
							$DAT_VENCIMENTO_ATENDIMENTO = $this->getDataVcto($datCalcPrazo, 0, $recal->fields['iditem'], $recal->fields['idpriority'], $recal->fields['idservice']);
							$extNumber = $db->getExtNumber($code);
							$upd = $db->saveExtension($code, $extNumber, $DAT_VENCIMENTO_ATENDIMENTO);
							if($upd){
								$this->sendEmail('record', $code);
								$dbrr->CommitTrans();
								echo "OK";
							}else{
								$dbrr->RollbackTrans();
								return false;
							}
						}else{
							$this->sendEmail('record', $code);
							$dbrr->CommitTrans();
							echo "OK";
						}
					}else{
						$dbrr->RollbackTrans();
						return false;
					}					
				}else{
					$dbrr->RollbackTrans();
					return false;
				}				
			}
        }
	}

	public function reproveRequest(){
		$this->validasessao();
		$smarty = $this->retornaSmarty();
		$dbrr =  new requestrules_model();
		$db = new operatorview_model();
		$langVars = $smarty->get_config_vars();
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$JUSTF = strip_tags($_POST["justApproval"]);
		$code = $_POST['code'];

		$note = "<p>".$langVars['Request_rejected']."</p><p><strong>".$langVars['Justification'].":</strong> $JUSTF</p>";
        
        $date = "now()";
        $ipadress = $_SERVER['REMOTE_ADDR'];
        
		$db->BeginTrans();
        $ins = $db->insertNote($code, $idperson, $note, $date, 0, 0, 0, $date, 1, null, 1, 3, $ipadress, 0, 'NULL');
        if($ins){
				
			$id_note = $db->insertNoteLastID();
			$upidnote = $dbrr->updateReproveNote($id_note, $idperson, $code);
		   	if(!$upidnote){
				$dbrr->RollbackTrans();
				return false;
		   	}else{
		   		$changeStat = $db->updateReqStatus(6, $code);
				if(!$changeStat){
					$dbrr->RollbackTrans();
					return false;
				}else{
					$this->sendEmail('reject', $code, $note);
					$dbrr->CommitTrans();
					echo "OK";
				}					
		   	}
			
        }
	}

	public function returnRequest(){
		$this->validasessao();
		$smarty = $this->retornaSmarty();
		$dbrr =  new requestrules_model();
		$db = new operatorview_model();
		$langVars = $smarty->get_config_vars();
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$JUSTF = strip_tags($_POST["justApproval"]);
		$code = $_POST['code'];

        $note = "<p>".$langVars['Request_rejected_app']."</p><p><strong>".$langVars['Justification'].":</strong> $JUSTF</p>";
        $date = "now()";
        $ipadress = $_SERVER['REMOTE_ADDR'];
        
		$db->BeginTrans();		
		$lastApp = $dbrr->getLastApproval($code);
		$idlastapp = $lastApp->fields['idperson'];
		$orderlastapp = $lastApp->fields['order'];
		
		if(isset($idlastapp)){
	        $ins = $db->insertNote($code, $idperson, $note, $date, 0, 0, 0, $date, 1, null, 1, 3, $ipadress, 0, 'NULL');
	        if($ins){				
				$updReturn = $dbrr->updateReturnApp($code, $idlastapp, $orderlastapp);
				if($updReturn){
					$db->removeIncharge($code);			
					$db_reqinsert = new requestinsert_model();
					$rs = $db_reqinsert->insertRequestCharge($code, $idlastapp, 'P', '1');						
					if($rs){
						$this->sendEmail('approve', $code);
						$db->CommitTrans();
						echo "OK";
					}else{
						$db->RollbackTrans();
						return false;
					}
				}else{
					$db->RollbackTrans();
					return false;
				}
	        }
		}
	}
	
    public function addnote() {
        $this->validasessao();
        $typeperson = $_SESSION['SES_TYPE_PERSON'];
        
        extract($_POST);

        $bd = new operatorview_model();
        $date = $this->formatSaveDateHour($execdate." ".$starthour);		
        $serviceval = 'NULL';
        $public = '1';
        if(!$typenote) $typenote = '1';
        $ipadress = $_SERVER['REMOTE_ADDR'];
        if($_POST['callback'])
        	$callback = $_POST['callback'];
		else
			$callback = '0';
        $ipadress = $_SERVER['REMOTE_ADDR'];
		$idperson = $_SESSION['SES_COD_USUARIO'];


        if($idanexo < 1){
            $idanexo = 'NULL';
        }
        $note = str_replace("'", "\"", $note);
		$note = addslashes($note);

        $ins = $bd->insertNote($code, $idperson, $note, $date, $totalminutes, $starthour, $finishour, $execdate, $hourtype, $serviceval, $public, $typenote, $ipadress, $callback, $idanexo);

        if(!$ins){
            return false;
        }
        if ($ins) {

            if ($typeperson != 2) {
            	if($typenote == 1){
            		$bd->updateFlag($code, 1);
	                if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['OPERATOR_NEW_NOTE'] == '1') {
	                    $this->sendEmail('user_note', $code);
	                }
				}
            }
            echo "OK";
        } else {
            return false;
        }
    }

    public function assume() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        extract($_POST);

        $bd = new operatorview_model();
		$bd->BeginTrans();		
        $status = '3'; //EM ATENDIMENTO
        $person = $_SESSION['SES_COD_USUARIO']; //ID DO USUARIO QUE ESTÁ ASSUMINDO
        
        $inslog = $bd->changeRequestStatus($status, $code, $person); //SALVA NO LOG A MUDANÇA DE STATUS Q VAI FAZER
        if (!$inslog) {
        	$bd->RollbackTrans();
        	return false;
        }
		
        $ipadress = $_SERVER['REMOTE_ADDR']; //IP DO ATENDENTE
        $callback = '0';
        $idtype = '3'; //APONTAMENTO DE SISTEMA
        $public = '1';
        $note = '<p><b>' . $langVars['Request_assumed'] . '</b></p>'; //TEXTO APONTAMENTO

        if ($this->database == 'oci8po') {
            $date = 'sysdate'; //PEGAR DATA ATUAL
        }
        else
        {
            $date = 'now()'; //PEGAR DATA ATUAL
        }

        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'null');
        if (!$insNote) {
        	$bd->RollbackTrans();
        	return false;
        }

		if($grpview == 1){//ADICIONAR TRACK PARA O GRUPO
			if($typeincharge == "P"){
				$trackGroup = $bd->insertInCharge($code, $groupAssume, 'G', 'N', '0', '1');
			}elseif($typeincharge == "G"){
				$trackGroup = $bd->insertInCharge($code, $incharge, 'G', 'N', '0', '1');
			}
			if (!$trackGroup) {
	        	$bd->RollbackTrans();
	        	return false;
	        }
		}		
		
        $type = "P"; //TIPO PESSOA
        $rep = 'N'; //NÃO É REPASS
        $ind = '1'; //RESPONSAVEL ATUAL
        $removeInCharge = $bd->removeIncharge($code); //RETIRA O RESPONSÁVEL DA SOLICITAÇÃO ANTES DE ADICIONAR O NOVO
        if (!$removeInCharge) {
        	$bd->RollbackTrans();
        	return false;
        }

        $insInCharge = $bd->insertInCharge($code, $person, $type, $rep, $ind, '0'); //ADICIONA O NOVO RESPONSÁVEL
        if (!$insInCharge) {
        	$bd->RollbackTrans();
        	return false;
        }

        $changeStat = $bd->updateReqStatus($status, $code); //ATUALIZA O STATUS DA SOLICITAÇÃO
        if (!$changeStat) {
        	$bd->RollbackTrans();
        	return false;
        }
		
		$getEntryDate = $bd->getEntryDate($code);
		$MIN_OPENING_TIME = $this->dif_data($getEntryDate,date("Y-m-d H:i"));
		$data = array("MIN_OPENING_TIME" => $MIN_OPENING_TIME);
		$uptimes = $bd->updateTime($code, $data);

		if (!$uptimes) {
        	$bd->RollbackTrans();
        	return false;
        }

        /*
        $ud = $bd->updateDate($code, "assume_date");
        if(!$ud){
            $bd->RollbackTrans();
            return false;
        }
        */
	    if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['NEW_ASSUMED_MAIL'] == '1') {
	        $this->sendEmail('assume', $code);
	    }
		$bd->CommitTrans();
	    echo "OK";
    }

	public function modalWay()
	{
        $smarty = $this->retornaSmarty();
		$smarty->display('modais/way.tpl.html');
	}
	
	public function modalSaveWay()
	{
        $way = $_POST['txtWay'];
        $bd = new requestinsert_model();
		$bd->BeginTrans();
        $ret = $bd->insertWay($way);
        if ($ret) {

            if ($this->database == 'oci8po') {
                $bd->CommitTrans();
            }
            else
            {
                $insertid = $bd->InsertID();
                if($insertid){
                    $bd->CommitTrans();
                    echo $insertid;
                }else{
                    $bd->RollbackTrans();
                    return false;
                }
            }

        } else {			
        	$bd->RollbackTrans();
            return false;
        }
        echo "ok";

	}
	
	public function operatorauxmodal()
	{
		error_reporting(1);
		$data = new person_model();
        $sel = $data->getOperatorAuxCombo($this->getParam('code'),'not');
 		while (!$sel->EOF) {
			$ids[]  = $sel->fields['idperson'];
			$names[] = $sel->fields['name'];
			$sel->MoveNext();
		}
		
		$rs = $data->getOperatorAuxCombo($this->getParam('code'),'in');
        while (!$rs->EOF) {
			$mylist[$rs->fields['idperson']] = $rs->fields['name'];
            $rs->MoveNext();
        }
		
        $smarty = $this->retornaSmarty();
		$smarty->assign("mylist", $mylist);
		$code = $this->getParam('code');
		$smarty->assign('operatorids', $ids);
		$smarty->assign('operatorsvals', $names);
		$smarty->assign('code_request', $this->getParam('code'));
		$smarty->display('modais/operatoraux.tpl.html');
	}


	public function jsonOperatorAux() 
	{
		/**
		 * O atendente que est� sendo inserido pode ter sido  o mesmo que repassou o chamado para o atendente respons�vel 
		 * e tamb�m � poss�vel que durante o repasse ele tenha marcado a op��o "Desejo que eu continue visualizando", 
		 * ent�o ele pode estar na tabela  "hdk_request_in_charge". � preciso fazer uma checagem extra e se esse for o caso, 
		 * dar um UPDATE na tabela ao inv�s de um INSERT.
		 *
		 * The operator being inserted may have been the same as relayed the call to the operator responsible 
		 * and it is also possible that during the transfer he has checked the "I want to continue browsing", 
		 * then he may be on the table "hdk_request_in_charge". You need to make an extra check and if that's the case, 
		 * give an UPDATE on the table instead of an INSERT.		 
		 **/
		// insertOperatorAux
		$db = new operatorview_model();
        $ret = $db->insertOperatorAux($_POST['code_request'],$_POST['id_person']);
		
		$data = new person_model();
		$rs = $data->getOperatorAuxCombo($_POST['code_request'],'in');
		$i = 0;
      	$resul = array();
        while (!$rs->EOF) {
            $resul[$i]['name'] = $rs->fields['name'];
			$resul[$i]['idperson'] = $rs->fields['idperson'];
			$i++;
            $rs->MoveNext();
        }
		echo json_encode($resul);
    }

    public function deleteOpeAux() 
	{
		$db = new operatorview_model();
        $ret = $db->deleteOperatorAux($_POST['code_request'],$_POST['id_person']);
    }
	
    public function comboOpeAux() 
	{
		$data = new person_model();
        $sel = $data->getOperatorAuxCombo($_POST['code_request'],'not');
 		while (!$sel->EOF) {
			echo "<option value='".$sel->fields['idperson']."'>".$sel->fields['name']."</option>";
			$sel->MoveNext();
		}
	}

    public function lstOperatorAux() 
	{
		$data = new person_model();
		$rs = $data->getOperatorAuxCombo($_POST['code_request'],'in');
		$i = 0;
      	$resul = array();
        while (!$rs->EOF) {
			$resul[$i]['name'] = $rs->fields['name'];
			$resul[$i]['idperson'] = $rs->fields['idperson'];
			$i++;
            $rs->MoveNext();
        }
		echo json_encode($resul);
    }
	
	public function closerequestmodal(){
		$smarty = $this->retornaSmarty();
		$code = $this->getParam('code');
		$idperson = $this->getParam('idpserson');
		
		if($_SESSION['SES_REQUEST_ADDINFO']){
			$db = new addinfo_model();
			$getAddInfo = $db->getAddInfos(); 
			while (!$getAddInfo->EOF) {
	            $value[] = $getAddInfo->fields['idaddinfo'];
	            $field[] = $getAddInfo->fields['name'];
	            $getAddInfo->MoveNext();
	        }		
			$smarty->assign('addinfofield', $field);
	        $smarty->assign('addinfovalue', $value);
		}
		$smarty->assign('SES_REQUEST_ADDINFO', $_SESSION['SES_REQUEST_ADDINFO']);
		$smarty->assign('code', $code);
		$smarty->display('modais/encerrar.tpl.html');
	}

    public function closerequest() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $code = $_POST['code'];

        $bd = new operatorview_model();
		$bd->BeginTrans();

        $ud = $bd->updateDate($code, "finish_date");
        if(!$ud){
            $bd->RollbackTrans();
            return false;
        }

        if ($_SESSION['SES_APROVE'] == 1) {
            $status = '4';
            $note = '<p><b>' . $langVars['Request_waiting_approval'] . '</b></p>';
			$ev = new evaluation_model();
			$iToken = $ev->insertToken($code);
			if(!$iToken){
				$bd->RollbackTrans();
            	return false;
			}
        } else {
            $status = '5';
            $note = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
            $ud = $bd->updateDate($code, "approval_date");
            if(!$ud){
                $bd->RollbackTrans();
                return false;
            }
        }
        $person = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';
        $inslog = $bd->changeRequestStatus($status, $code, $person);
        if (!$inslog) {
            $bd->RollbackTrans();
            return false;
        }
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $callback = '0';
        $idtype = '3';
        $public = '1';
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
		
		$getEntryDate = $bd->getEntryDate($code);
		$getAssumedDate = $bd->getAssumedDate($code);
		$MIN_EXPENDED_TIME = $bd->getExpendedTime($code);
		$MIN_CLOSURE_TIME = $this->dif_data($getEntryDate,date("Y-m-d H:i"));
		$MIN_ATTENDANCE_TIME = $this->dif_data($getAssumedDate,date("Y-m-d H:i"));
		$data = array(
					"MIN_CLOSURE_TIME" => $MIN_CLOSURE_TIME,
					"MIN_ATTENDANCE_TIME" => $MIN_ATTENDANCE_TIME,
					"MIN_EXPENDED_TIME"	=> $MIN_EXPENDED_TIME
					);
		$uptimes = $bd->updateTime($code, $data);
		if (!$uptimes) {
        	$bd->RollbackTrans();
        	return false;
        }		
		
		if($_POST['addInfo'] != 0){
			$addinf = new addinfo_model();
			$clearaddinf = $addinf->clearRequestAddInfos($code);
			if(!$clearaddinf){
				$bd->RollbackTrans();
        		return false;
			}
			
			$sendaddinf = $addinf->setRequestAddInfo($code, $_POST['addInfo']);
			if(!$sendaddinf){
				$bd->RollbackTrans();
        		return false;
			}			
		}		
		
		$bd->CommitTrans();
		
        if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['FINISH_MAIL'] == '1') {
            $this->sendEmail('close', $code);
        }		
        echo "OK";       
    }

    public function reopenrequest() {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $this->validasessao();
        extract($_POST);
		
        $bd = new operatorview_model();
		$bd->BeginTrans();

        $status = '1';
        $person = $_SESSION['SES_COD_USUARIO'];
        $reopened = '1';
        $inslog = $bd->changeRequestStatus($status, $code, $person);
        if (!$inslog) {
            $bd->RollbackTrans();
            return false;
        }
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $callback = '0';
        $idtype = '3';
        $public = '1';
        $note = "<p><b><span style=\"color: #FF0000;\">" . $langVars['Request_reopened'] . "</span></b></p>";
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
        
        if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['REQUEST_REOPENED'] == '1') {
            $this->sendEmail('reopen', $code);
        }
		$bd->CommitTrans();
        echo "OK";
    }

    //função para repassar a solicitação na sua criação
    public function openrepassed() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        
        extract($_POST);
        $_SESSION["SES_COD_ATTACHMENT"] = "";

        
        if ($typerepass == 'operator') {
            $db = new person_model();
            $name = $db->selectPersonName($repassto);
            $typerepass = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        } else {
            if ($typerepass == 'group') {
                $db = new groups_model();
                $name = $db->selectRepGroupData($repassto);
                $name = $name->fields['name'];
                $typerepass = $langVars['to'] . " " . $langVars['Group'];
                $type2 = "G";    
                $bdg = new operatorview_model();
                //pega os iperson referente aos grupos cadastrados na tabla tbperson
                //$rsIdPersonGroups = $bdg->getIdPersonGroup($repassto);
                //$repassto = $rsIdPersonGroups->fields['idperson'];
                
            } else {
                $db = new person_model();
                $name = $db->selectPersonName($repassto);
                $typerepass = $langVars['to'] . " " . $langVars['Operator'];
                $type2 = "P";
            }
        }
        
        $status = '2';
        $person = $_SESSION['SES_COD_USUARIO'];
        

        if (isset($repassto)) {
            $MIN_TEMPO_TELEFONE = number_format($_POST["open_time"], "2", ".", ",");
            // Se estiver configurado para que o codigo da solicitacao seja com formato
            // ANO E MES (ANOMES), gera o codigo neste momento
            if ($_SESSION["SES_IND_CODIGO_ANOMES"]) {
                //GERANDO O CODIGO DA SOLICITACAO
                $db = new requestinsert_model();
                $rsCodigo = $db->getCode();
//                if(!$rsCodigo){
//                    return false;
//                }
                
                $rsCountCodigo = $db->countGetCode();
//                if(!$rsCountCodigo){
//                    return false;
//                }
                if ($rsCountCodigo->fields['total']) {
                    $COD_SOLICITACAO = $rsCodigo->fields["cod_request"];
                    $rs = $db->increaseCode($COD_SOLICITACAO);
//                    if(!$rs){
//                        return false;
//                    }
                } else {
                    $COD_SOLICITACAO = 1;
                    $rs = $db->createCode($COD_SOLICITACAO);
//                    if(!$rs){
//                        return false;
//                    }
                }

                //Montando o Codigo Final
                while (strlen($COD_SOLICITACAO) < 6) {
                    $COD_SOLICITACAO = "0" . $COD_SOLICITACAO;
                }
                $COD_SOLICITACAO = date("Ym") . $COD_SOLICITACAO;
                $CAMPO_COD_SOLICITACAO = "code_request,";
            }
            //pega as variveis submetidas do form
            $COD_USUARIO = $_POST["idperson"];
            $COD_EMPRESA = $_POST["idjuridical"];
            $COD_PATRIMONIO = $_POST["idproperty"];
            $NOM_PATRIMONIO = $_POST["property"];
            $NUM_SERIE = $_POST["serial_number"];
            $NUM_ETIQUETA = $_POST["tag"];
            $COD_TIPO = $_POST["type"];
            $COD_SERVICO = $_POST["service"];
            $COD_ITEM = $_POST["item"];
            $COD_TIPO_ATENDIMENTO = $_POST["way"];
            $NOM_ASSUNTO = str_replace("'", "`", $_POST["subject"]);
            $DES_SOLICITACAO = str_replace("'", "`", $_POST["description"]);
            $COD_STATUS = 2;

            if (!$NUM_OS) {
                $NUM_OS = 0;
            }

            //Quando eh usuario, a origem padao eh รฉ pelo HelpDEZk, a menos que seja o chat
            if (isset($_POST["source"])) {
                $COD_ORIGEM = $_POST["source"];
            } else {
                if (isset($_POST['chatid']) && $_POST['chatid'] != 0) {
                    $COD_ORIGEM = 100; //coloca como chat
                } else {
                    $COD_ORIGEM = 1;
                }
            }
            
            $NOM_ANALISTA_AUTOR = "";
            $COD_ANALISTA_AUTOR = "NULL";
            if ($COD_USUARIO != $_SESSION["SES_COD_USUARIO"]) {
                $NOM_ANALISTA_AUTOR = $_SESSION['SES_NAME_PERSON'];
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            } else {
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            }

            // pode ter sido passado um codigo de atendente reponsavel, nesse casso
            if (isset($COD_USUARIO_ANALISTA)) {
                $rs = $db->getAnalyst($COD_USUARIO_ANALISTA);
//                if(!$rs){
//                    return false;
//                }

                if (!$rs->EOF) {
                    $COD_ANALISTA_AUTOR = $COD_USUARIO_ANALISTA;
                    $NOM_ANALISTA_AUTOR = $rs->fields["name"];
                }
            }

            // VERIFICA SE O USUARIO EH VIP
            $rsUsuarioVip = $db->checksVipUser($COD_USUARIO);
//            if(!$rsUsuarioVipo){
//                return false;
//            }
            // verifica se ha alguma prioridade marcada como VIP
            $rsPrioridadeVip = $db->checksVipPriority();
//            if(!$rsPrioridadeVip){
//                return false;
//            }
            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                $COD_PRIORIDADE = $rsPrioridadeVip->fields["idpriority"];
            } else {
                /// Busca a prioridade no servico
                $rsService = $db->getServPriority($COD_SERVICO);
//                if(!$rsService){
//                    return false;
//                }
                $COD_PRIORIDADE = $rsService->fields['idpriority'];

                // se nao tiver prioridade no servico, pega a prioridade padrao...
                if (!$COD_PRIORIDADE) {
                    // Consulta a prioridade padrao na abertura de solicitacoes
                    $rsPrioridade = $db->getDefaultPriority();
//                    if(!$rsPrioridade){
//                        return false;
//                    }
                    $COD_PRIORIDADE = $rsPrioridade->fields["idpriority"];
                }
            }

            // Estamos passando o codigo do servico, o tempo de atendimento deverao ser calculado com base nele..                
            // Se uma data de abertura for informada, usaremos ela para calcular o prazo
            if (!$_POST["date"]) {
                $DAT_CADASTRO = date("d/m/Y");
                $datCalcPrazo = date('Ymd');
            } else {
                $DAT_CADASTRO = $_POST["date"];
                $ptDAT_CADASTRO = explode('/', $_POST["date"]);
                $datCalcPrazo = $ptDAT_CADASTRO[2] . $ptDAT_CADASTRO[1] . $ptDAT_CADASTRO[0];
            }

            if (!$_POST["time"]) {
                $HOR_CADASTRO = date("H:i");
                $datCalcPrazo .= date('Hi');
            } else {
                $HOR_CADASTRO = $_POST["time"];
                $datCalcPrazo .= str_replace(':', '', $_POST["time"]);
            }

            $DAT_VENCIMENTO_ATENDIMENTO = $this->getDataVcto($datCalcPrazo, $COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO);

            $db = new requestinsert_model();

            $AUX = explode("/", $DAT_CADASTRO);
            $DAT_CADASTRO = $AUX[2] . "-" . $AUX[1] . "-" . $AUX[0];
            $AUX = explode(":", $HOR_CADASTRO);

            $COD_MOTIVO = $_POST['reason'];
            $COD_TIPO_ATENDIMENTO = $_POST['way'];

            $rs = $db->insertRequest($COD_ANALISTA_AUTOR, $COD_ORIGEM, $DAT_CADASTRO, $COD_TIPO, $COD_ITEM, $COD_SERVICO, $COD_MOTIVO, $COD_TIPO_ATENDIMENTO, $NOM_ASSUNTO, $DES_SOLICITACAO, $NUM_OS, $COD_PRIORIDADE, $NUM_ETIQUETA, $NUM_SERIE, $COD_EMPRESA, $DAT_VENCIMENTO_ATENDIMENTO, $COD_USUARIO, $COD_STATUS, $CAMPO_COD_SOLICITACAO, $COD_SOLICITACAO . ',');
            if(!$rs){
                return false;
            }
            
			switch($viewrepass){			
				case "P": //REPASSAR E SEGUIR ACOMPANHANDO
						$opmodel = new operatorview_model();
						$track = $opmodel->insertInCharge($COD_SOLICITACAO, $_SESSION['SES_COD_USUARIO'], "P", "Y", '0', '1');
						if(!$track){
							$db->RollbackTrans();
							return false;
						}
					break;
				case "N": //NAO ACOMPANHAR
					
					break;
			}
			
            $rs = $db->insertRequestCharge($COD_SOLICITACAO, $repassto, $type2, '1');
            if(!$rs){
                return false;
            }
            $tm = $db->insertRequestTimes($COD_SOLICITACAO, $MIN_TEMPO_TELEFONE, '0', '0');
            if(!$tm){
                return false;
            }
            ///Vamos  descobrir o codigo auto increment da solicitacao
            /// se nao estivermos usando o formato ANOMES. Caso em que ja teremos o $COD_SOLICITACAO AQUI
            if (!isset($COD_SOLICITACAO)) {
                $rs = $db->lastCode();
                if(!$rs){
                   return false;
                }
                $COD_SOLICITACAO = $rs->fields["code_request"];
            }
            if (!$_SESSION["SES_IND_CODIGO_ANOMES"]) {
                $rs = $db->lastCode();
                if(!$rs){
                    return false;
                }
                $COD_SOLICITACAO = $rs->fields["code_request"];
            }
            //insere na tabela de controle a a alteração de status feita e qual usuario fez com a data do acontecimento
            $rs = $db->insertRequestLog($COD_SOLICITACAO, date("Y-m-d H:i:s"), $COD_STATUS, $COD_USUARIO);
            if(!$rs){
                return false;
            }
			
            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_POST["COD_ANEXO"] != '') {

                $COD_ANEXO = explode(",", $_POST["COD_ANEXO"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluรญndo o cรณdigo da solicitaรงรฃo  nos anexos.                                
                    $Result1 = $db->updateRequestAttach($COD_SOLICITACAO, $COD_ANEXO[$i]);
                    if(!$Result1){
                       return false;
                    }
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            $data = "now()";
            $DES_APONTAMENTO = "<p><b>" . $langVars['Request_opened'] . "</b></p>";
            $con = $db->insertNote($COD_SOLICITACAO, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');
            if(!$con){
                return false;
            }
            //Zerando a variavel que armazena os anexos.
            $_SESSION["SES_COD_ATTACHMENT"] = "";
            
            
            
            if ($rs) {
                if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['NEW_REQUEST_OPERATOR_MAIL'] == '1') {                    
                    $this->sendEmail('record', $COD_SOLICITACAO);
                }
                echo $COD_SOLICITACAO;
            } else {
                return false;
            }
        }//fim do cadastro
    }

    //fim da funcao saverequest

    public function rejectrequest() {
        session_start();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $this->validasessao();
        extract($_POST);
		
        $bd = new operatorview_model();
		$bd->BeginTrans();				
		
        $status = '6';
        $person = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';
        $inslog = $bd->changeRequestStatus($status, $code, $person);
        if (!$inslog) {
            $bd->RollbackTrans();
            return false;
        }
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $callback = '0';
        $idtype = '3';
        $public = '1';
        $note = '<p><b>' . $langVars['Request_rejected'] . '</b></p>';
        $note.= '<p>' . $reason . '</p>';
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
		
        $type = "P"; //TIPO PESSOA
        $rep = 'N'; //NÃO É REPASS
        $ind = '1'; //RESPONSAVEL ATUAL
        $removeInCharge = $bd->removeIncharge($code); //RETIRA O RESPONSÁVEL DA SOLICITAÇÃO ANTES DE ADICIONAR O NOVO
        if (!$removeInCharge) {
        	$bd->RollbackTrans();
        	return false;
        }
        
        $insInCharge = $bd->insertInCharge($code, $person, $type, $rep, $ind, '0'); //ADICIONA O NOVO RESPONSÁVEL
        if (!$insInCharge) {
        	$bd->RollbackTrans();
        	return false;
        }

        $ud = $bd->updateDate($code, "rejection_date");
        if(!$ud){
            $bd->RollbackTrans();
            return false;
        }
		
		$bd->CommitTrans();
		
        if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['REJECTED_MAIL'] == '1') {
            $email = $this->sendEmail('reject', $code, $reason);
        }
		
		if($_SESSION['SES_MAIL_OPERATOR_REJECT'] && $typeincharge == "G" && $_SESSION['SEND_EMAILS'] == '1'){
			$_SESSION['SES_MAIL_OPERATOR_REJECT_ID'] = $incharge;
			$email = $this->sendEmail('operator_reject', $code, $reason);
		}
		
        echo "OK";
        
    }

	/**
	* Method to pass requests
	*
	* @access public
	* @return boolean
	*/	
    public function repassRequest() {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();

        extract($_POST);
		
        if ($type == 'operator') {
            $db = new person_model();
			$db->BeginTrans();
            $name = $db->selectPersonName($repassto);
            $type = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        } 
        elseif ($type == 'group') {
            $db = new groups_model();
			$db->BeginTrans();
            $name = $db->selectRepGroupData($repassto);
            $name = $name->fields['name'];
            $type = $langVars['to'] . " " . $langVars['Group'];
            $type2 = "G";
        }else{
        	return false;
        }
		
		
		$bd = new operatorview_model();
        $status = '2'; //REPASSADO
        $person = $_SESSION['SES_COD_USUARIO'];
        $reopened = '0';		
		$idgrouptrack = $_POST['idgrouptrack'];
		$rep = 'Y';
		
		switch($_POST['view']){
			case "G": //REPASSAR E GRUPO SEGUIR ACOMPANHANDO
				if($idgrouptrack == 0){
					$track = $bd->insertInCharge($code_request, $incharge, "G", $rep, '0', '1');
					if(!$track){
						$db->RollbackTrans();
						return false;
					}
				}else{
					$track = $bd->insertInCharge($code_request, $idgrouptrack, "G", $rep, '0', '1');
					if(!$track){
						$db->RollbackTrans();
						return false;
					}
				}					
				break;
			case "P": //REPASSAR E SEGUIR ACOMPANHANDO
					$track = $bd->insertInCharge($code_request, $person, "P", $rep, '0', '1');
					if(!$track){
						$db->RollbackTrans();
						return false;
					}
				break;
			case "N": //NAO ACOMPANHAR
				
				break;
		}
		
		/*
		 LOG
		*/		
        $inslog = $bd->changeRequestStatus($status, $code_request, $person, $reopened); //insere log
        if (!$inslog) {
            $db->RollbackTrans();
			return false;
        } 
		/*
		 INSERE NOTIFICAÇÃO
		*/	
		
        $ipadress = $_SERVER['REMOTE_ADDR'];
        $callback = '0';
        $idtype = '3';
        $public = '1';
        $note = "<p><b>" . $langVars['Request_repassed'] . strtolower($type) . " " . $name . "</b></p>";
        if ($this->database == 'oci8po') {
            $date = "sysdate";
        }
        else
        {
            $date = "now()";
        }
        $insNote = $bd->insertNote($code_request, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback,'NULL');
        if (!$insNote) {
            $db->RollbackTrans();
			return false;
        }
		
		/*
		 INSERE NA hdk_tbrequest_repassed A PRINCIPIO SOMANTE PARA REGISTRO
		 */
        $noteid = $bd->getRepassNote($code_request);
        $insrep = $bd->insertRepassRequest($date, $code_request, $noteid);
        if (!$insrep) {
            $db->RollbackTrans();
			return false;
        }
		
		/*
		 COLOCA TODOS ind_in_charge PARA 0 (RETIRA TODOS RESPONSAVEIS)
		*/
        $rmincharge = $bd->removeIncharge($code_request);
        if (!$rmincharge) {
            $db->RollbackTrans();
			return false;
        }
        /*
		 ADICIONA O NOVO RESPONSÁVEL
		*/		
        $insInCharge = $bd->insertInCharge($code_request, $repassto, $type2, $rep, '1');
        if (!$insInCharge) {
            $db->RollbackTrans();
			return false;
        }
		
		/*
		 MUDA STATUS DA SOLICITAÇÃO PARA REPASSADO
		*/
        $changeStat = $bd->updateReqStatus($status, $code_request);
        if (!$changeStat) {
            $db->RollbackTrans();
			return false;
        }

        /*
          SALVA DATA DE REPASSE
        */
        
        $ud = $bd->updateDate($code_request, "forwarded_date");
        if(!$ud){
            $bd->RollbackTrans();
            return false;
        }
        	
    	$bd->CommitTrans();
    	if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['REPASS_REQUEST_OPERATOR_MAIL'] == 1) {                    
            $this->sendEmail('repass', $code_request);
        }
        echo "OK";
        
    }

    public function editrequest() {
        $this->validasessao();
        extract($_POST);
        $bd = new operatorview_model();     
        $upd = $bd->updateRequest($code, $type, $item , $service, $reason, $way, $priority);
        if ($upd) {
            echo "ok";
        } else {
            return false;
        }
    }        

    public function reports() {
        $smarty = $this->retornaSmarty();
        $smarty->display("reports.tpl.html");
    }

    public function operatorrep() {
        $this->validasessao();
        $bd = new operatorview_model();        
        if($_POST['filter'])
			$where = "AND name LIKE '%".$_POST['filter']."%' ";
		else {
			$where = null;
		}	
        $ret = $bd->getRepassOperators($where);		
        $replist = "<select name='replist' id='replist' size='10' style='width: 350px; background-color: #eee;' onclick='getAbilities();'>";
        while (!$ret->EOF) {
            $replist.="<option value=" . $ret->fields['idperson'] . ">" . $ret->fields['name'] . "</option>";
            $ret->MoveNext();
        }
        $replist.="</select>";
        echo $replist;
    }

    public function grouprep() {
        $this->validasessao();
        $db = new operatorview_model();
		if($_POST['filter'])
			$where = "AND name LIKE '%".$_POST['filter']."%' ";
		else {
			$where = null;
		}	
        $repgroups = $db->getRepassGroups($where);
        $replist = "<select name='replist' id='replist' size='10' style='width: 350px; background-color: #eee;' onclick='getAbilities();'>";
        while (!$repgroups->EOF) {
            $replist.="<option value=" . $repgroups->fields['idperson'] . ">(" . $repgroups->fields['level'] . ") " . $repgroups->fields['name'] . "</option>";
            $repgroups->MoveNext();
        }
        $replist.="</select>";
        echo $replist;
    }

    public function partrep() {
        $this->validasessao();
        $db = new operatorview_model();
        $ret = $db->getRepassPartners();
        $replist = "<select name='replist' id='replist' size='10' style='width: 350px; background-color: #eee;' onclick='getAbilities();'>";
        while (!$ret->EOF) {
            $replist.="<option value=" . $ret->fields['idperson'] . ">" . $ret->fields['name'] . "</option>";
            $ret->MoveNext();
        }
        $replist.="</select>";
        echo $replist;
    }

    public function abilitieslist() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        extract($_POST);

        $langVars = $smarty->get_config_vars();
        $db = new operatorview_model();
        if ($type == 'group') {
            $ret = $db->getAbilityGroup($rep);
            $replist = "<table id='abtable' border='1px' cellpadding='5px' cellspacing='0' style='background: #eee; width: 248px;'>";
            $replist.= "<tr style='text-align: center; font-size: 12px;'><td><b>" . $langVars['Related_abilities'] . "</b></td></tr>";
            if ($ret->fields) {
                while (!$ret->EOF) {
                    $replist.="<tr style='font-size: 12px;'><td>" . $ret->fields['service'] . "</tr></td>";
                    $ret->MoveNext();
                }
                $replist.="</table>";
            } else {
                $replist.="<tr style='text-align: center; font-size: 12px;'><td>" . $langVars['No_abilities'] . "</tr></td>";
            }
            echo $replist;
        } else {
            if ($type == 'operator') {
                $ret = $db->getAbilityOperator($rep);
                $replist = "<table id='abtable' border='1px' cellpadding='5px' cellspacing='0' style='background: #eee; width: 248px;'>";
                $replist.= "<tr style='text-align: center; font-size: 12px;'><td><b>" . $langVars['Related_abilities'] . "</b></td></tr>";
                if ($ret->fields) {
                    while (!$ret->EOF) {
                        $replist.="<tr style='font-size: 12px;'><td>" . $ret->fields['service'] . "</tr></td>";
                        $ret->MoveNext();
                    }
                    $replist.="</table>";
                } else {
                    $replist.="<tr style='text-align: center; font-size: 12px;'><td>" . $langVars['No_abilities'] . "</tr></td>";
                }
                echo $replist;
            } else {
                $ret = $db->getAbilityPartners($rep);
                $replist = "<table id='abtable' border='1px' cellpadding='5px' cellspacing='0' style='background: #eee; width: 248px;'>";
                $replist.= "<tr style='text-align: center; font-size: 12px;'><td><b>" . $langVars['Related_abilities'] . "</b></td></tr>";
                if ($ret->fields) {
                    while (!$ret->EOF) {
                        $replist.="<tr style='font-size: 12px;'><td>" . $ret->fields['service'] . "</tr></td>";
                        $ret->MoveNext();
                    }
                    $replist.="</table>";
                } else {
                    $replist.="<tr style='text-align: center; font-size: 12px;'><td>" . $langVars['No_abilities'] . "</tr></td>";
                }
                echo $replist;
            }
        }
    }

    public function grouplist() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        extract($_POST);

        $langVars = $smarty->get_config_vars();
        $db = new operatorview_model();
        if ($type == 'group') {
            $ret = $db->getGroupOperators($rep);
            $replist = "<table id='abtable' border='1px' cellpadding='5px' cellspacing='0' style='background: #eee; width: 248px;'>";
            $replist.= "<tr style='text-align: center; font-size: 12px;'><td><b>" . $langVars['Group_operators'] . "</b></td></tr>";
            if ($ret->fields) {
                while (!$ret->EOF) {
                    $replist.="<tr style='font-size: 12px;'><td>" . $ret->fields['name'] . "</tr></td>";
                    $ret->MoveNext();
                }
                $replist.="</table>";
            } else {
                $replist.="<tr style='text-align: center; font-size: 12px;'><td>" . $langVars['No_data'] . "</tr></td>";
            }
            echo $replist;
        } else {
            if ($type == 'operator') {
                $ret = $db->getOperatorGroups($rep);
                $replist = "<table id='abtable' border='1px' cellpadding='5px' cellspacing='0' style='background: #eee; width: 248px;'>";
                $replist.= "<tr style='text-align: center; font-size: 12px;'><td><b>" . $langVars['Operator_groups'] . "</b></td></tr>";
                if ($ret->fields) {
                    while (!$ret->EOF) {
                        $replist.="<tr style='font-size: 12px;'><td>" . $ret->fields['pername'] . "</tr></td>";
                        $ret->MoveNext();
                    }
                    $replist.="</table>";
                } else {
                    $replist.="<tr style='text-align: center; font-size: 12px;'><td>" . $langVars['No_data'] . "</tr></td>";
                }
                echo $replist;
            } else {
                echo "replist";
            }
        }
    }

    public function downloadtable() {
		$path_default = $this->getConfig('path_default');
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $bd = new downloads_model();
        $rs = $bd->selectCategories();
        $lista = "<ol>";
        while (!$rs->EOF) {
            $down = $bd->getDownloadFromCategory($rs->fields['iddownloadcategory']);
            if ($down->fields) {
                $lista.="<li class='download-table'><h2><span>" . $rs->fields['category'] . "</span></h2><div id='download-specs'>";
                while (!$down->EOF) {
                    $iddownload = $down->fields['iddownload'];
                    $date = $this->formatDate($down->fields['date']);
                    $instruction = $down->fields['instruction'];
                    $filename = $down->fields['file_name'];
                    $downfilename = $down->fields['download_file_name'];
                    if ($instruction == '') {
                        $lista.= "<div class='ind-spec'><a href='javascript:;' onclick=\"openDownloadPopUP('" . $path_default . "-app-uploads-files-','$filename','$downfilename');\" class='file' name='" . path . "/app/uploads/files/" . $filename . "'><div style='margin-top: 2px; float: left;'><img src='" . path . "/app/themes/" . theme . "/images/folder.png' width='30px' height='30px'></div><div style='margin-left: 2px; height:50px; overflow: hidden; width: auto;'>" . $down->fields['name'] . " - " . $down->fields['version_description'] . "</a> - " . $date . " <BR/><b>" . $langVars['Description'] . "</b>: " . $down->fields['description'] . "</div><div style='margin-top: 0px; margin-left: -50px; left:50%; position:relative; margin-bottom: 5px; width: 100px;'><button id='noinstructions' name='noinstructions' class='btn_cadastrar_disabled'>" . $langVars['No_instructions'] . "</button></div></div>";
                    } else {
                        $lista.= "<div class='ind-spec'><a href='javascript:;' onclick=\"openDownloadPopUP('" . $path_default . "-app-uploads-files-','$filename','$downfilename');\" class='file' name='" . path . "/app/uploads/files/" . $filename . "'><div style='margin-top: 2px; float: left;'><img src='" . path . "/app/themes/" . theme . "/images/folder.png' width='30px' height='30px'></div><div style='margin-left: 2px; height:50px; overflow: hidden; width: auto;'>" . $down->fields['name'] . " - " . $down->fields['version_description'] . "</a> - " . $date . " <BR/><b>" . $langVars['Description'] . "</b>: " . $down->fields['description'] . "</div><div style='margin-top: 0px; margin-left: -50px; left:50%; position:relative;  margin-bottom: 5px; width: 100px;'><button id='instructions' name='instructions' class='btn_cadastrar_down' onclick=\"showinstruction('" . $iddownload . "'); return false;\">" . $langVars['Show_instructions'] . "</button></div></div>";
                    }

                    $down->MoveNext();
                }

                $lista.="</div></li>";
            }
            $rs->MoveNext();
        }
        $lista.="</ol>";
        $smarty->assign('list', $lista);
        $smarty->display('downloadtable.tpl.html');
    }

    public function calcmin() {
        //error_reporting(E_ALL);
        extract($_POST);
        if($start == '' || $finish ==''){
            return false;
        }
        $split[2] = explode(':', $start);
        $split[1] = explode(':', $finish);
        
        $hour1 = $split[1][0];
        $minute1 = $split[1][1];
        $second1 = $split[1][2];

        $hour2 = $split[2][0];
        $minute2 = $split[2][1];
        $second2 = $split[2][2];

        $total_minutes = ((($hour1 * 60) - ($hour2 * 60)) + ($minute1 - $minute2) + (($second1 / 60) - ($second2 / 60)));
        echo number_format($total_minutes, 2);
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

    public function changeExpireDate() {
        $this->validasessao();
        extract($_POST);
        $idperson = $_SESSION['SES_COD_USUARIO'];
        $db = new operatorview_model();
        $db->BeginTrans();
        $extNumber = $db->getExtNumber($codeRequestExpire);
        $extNumber = $extNumber + 1;
        if ($this->database == 'oci8po') {
            $date = $this->formatSaveDate($dateChangeExpire." ".$timeChangeExpire);
        }
        else{
            $date = $this->formatSaveDateHour($dateChangeExpire." ".$timeChangeExpire);
        }
        $upd = $db->saveExtension($codeRequestExpire, $extNumber, $date);
        if (!$upd) {
            $db->RollbackTrans();
            return false;
        }
        $updCh = $db->insertChangeExpireDate($codeRequestExpire, $reasonChangeExpire, $idperson);
        if (!$updCh) {
            $db->RollbackTrans();
            return false;
        }             
        $db->CommitTrans();
        echo "ok";
    }

    public function finishrequest() {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $this->validasessao();

        $_SESSION["SES_COD_ATTACHMENT"] = "";


        if (isset($_POST["idperson"])) {
            $MIN_TEMPO_TELEFONE = number_format($_POST["open_time"], "2", ".", ",");
            // Se estiver configurado para que o codigo da solicitacao seja com formato
            // ANO E MES (ANOMES), gera o codigo neste momento
            if ($_SESSION["SES_IND_CODIGO_ANOMES"]) {
                //GERANDO O CODIGO DA SOLICITACAO
                $db = new requestinsert_model();
                $rsCodigo = $db->getCode();
                $rsCountCodigo = $db->countGetCode();

                if ($rsCountCodigo->fields['total']) {
                    $COD_SOLICITACAO = $rsCodigo->fields["COD_REQUEST"];
                    $rs = $db->increaseCode($COD_SOLICITACAO);
                } else {
                    $COD_SOLICITACAO = 1;
                    $rs = $db->createCode($COD_SOLICITACAO);
                }

                //Montando o Codigo Final
                while (strlen($COD_SOLICITACAO) < 6) {
                    $COD_SOLICITACAO = "0" . $COD_SOLICITACAO;
                }
                $COD_SOLICITACAO = date("Ym") . $COD_SOLICITACAO;
                $CAMPO_COD_SOLICITACAO = "code_request,";
            }
            //pega as variveis submetidas do form
            $COD_USUARIO = $_POST["idperson"];
            $COD_EMPRESA = $_POST["idjuridical"];
            $COD_PATRIMONIO = $_POST["idproperty"];
            $NOM_PATRIMONIO = $_POST["property"];
            $NUM_SERIE = $_POST["serial_number"];
            $NUM_ETIQUETA = $_POST["tag"];
            $COD_TIPO = $_POST["type"];
            $COD_SERVICO = $_POST["service"];
            $COD_ITEM = $_POST["item"];
            $COD_TIPO_ATENDIMENTO = $_POST["way"];
            $NOM_ASSUNTO = str_replace("'", "`", $_POST["subject"]);
            $DES_SOLICITACAO = str_replace("'", "`", $_POST["description"]);
			$SOLUTION = str_replace("'", "`", $_POST["DES_APONTAMENTO"]);
            $COD_STATUS = $_POST["status"];

            if (!$NUM_OS) {
                $NUM_OS = 0;
            }

            //Quando eh usuario, a origem padao eh รฉ pelo HelpDEZk, a menos que seja o chat
            if (isset($_POST["source"])) {
                $COD_ORIGEM = $_POST["source"];
            } else {
                if (isset($_POST['chatid']) && $_POST['chatid'] != 0) {
                    $COD_ORIGEM = 100; //coloca como chat
                } else {
                    $COD_ORIGEM = 1;
                }
            }
            // se a origem for por telefone e NaO for usuario, conta o tempo de atendimento por TELEFONE            
            //Quando eh usuario nao eh informado o Status
            if (isset($_POST["status"])) {
                $COD_STATUS = $_POST["status"];
            } else {
                $COD_STATUS = 1;
            }
            $NOM_ANALISTA_AUTOR = "";
            $COD_ANALISTA_AUTOR = "NULL";
            if ($COD_USUARIO != $_SESSION["SES_COD_USUARIO"]) {
                $NOM_ANALISTA_AUTOR = $_SESSION['SES_NAME_PERSON'];
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            } else {
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            }

            // pode ter sido passado um codigo de atendente reponsavel, nesse casso
            if (isset($COD_USUARIO_ANALISTA)) {
                $rs = $db->getAnalyst($COD_USUARIO_ANALISTA);

                if (!$rs->EOF) {
                    $COD_ANALISTA_AUTOR = $COD_USUARIO_ANALISTA;
                    $NOM_ANALISTA_AUTOR = $rs->fields["name"];
                }
            }

            // VERIFICA SE O USUARIO EH VIP
            $rsUsuarioVip = $db->checksVipUser($COD_USUARIO);

            // verifica se ha alguma prioridade marcada como VIP
            $rsPrioridadeVip = $db->checksVipPriority();

            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                $COD_PRIORIDADE = $rsPrioridadeVip->fields["idpriority"];
            } else {
                /// Busca a prioridade no servico
                $rsService = $db->getServPriority($COD_SERVICO);
                $COD_PRIORIDADE = $rsService->fields['idpriority'];

                // se nao tiver prioridade no servico, pega a prioridade padrao...
                if (!$COD_PRIORIDADE) {
                    // Consulta a prioridade padrao na abertura de solicitacoes
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORIDADE = $rsPrioridade->fields["idpriority"];
                }
            }

            // Estamos passando o codigo do servico, o tempo de atendimento deverao ser calculado com base nele..                
            // Se uma data de abertura for informada, usaremos ela para calcular o prazo
            if (!$_POST["date"]) {
                $DAT_CADASTRO = date("d/m/Y");
                $datCalcPrazo = date('Ymd');
            } else {
                $DAT_CADASTRO = $_POST["date"];
                $ptDAT_CADASTRO = explode('/', $_POST["date"]);
                $datCalcPrazo = $ptDAT_CADASTRO[2] . $ptDAT_CADASTRO[1] . $ptDAT_CADASTRO[0];
            }

            if (!$_POST["time"]) {
                $HOR_CADASTRO = date("H:i");
                $datCalcPrazo .= date('Hi');
            } else {
                $HOR_CADASTRO = $_POST["time"];
                $datCalcPrazo .= str_replace(':', '', $_POST["time"]);
            }

            $DAT_VENCIMENTO_ATENDIMENTO = $this->getDataVcto($datCalcPrazo, $COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO);

            $db = new requestinsert_model();

            $AUX = explode("/", $DAT_CADASTRO);
            $DAT_CADASTRO = $AUX[2] . "-" . $AUX[1] . "-" . $AUX[0];
            $AUX = explode(":", $HOR_CADASTRO);
            $DAT_CADASTRO .= " " . $AUX[0] . ":" . $AUX[1] . ":00";

            $COD_MOTIVO = $_POST['reason'];
            $COD_TIPO_ATENDIMENTO = $_POST['way'];

            $rs = $db->insertRequest($COD_ANALISTA_AUTOR, $COD_ORIGEM, $DAT_CADASTRO, $COD_TIPO, $COD_ITEM, $COD_SERVICO, $COD_MOTIVO, $COD_TIPO_ATENDIMENTO, $NOM_ASSUNTO, $DES_SOLICITACAO, $NUM_OS, $COD_PRIORIDADE, $NUM_ETIQUETA, $NUM_SERIE, $COD_EMPRESA, $DAT_VENCIMENTO_ATENDIMENTO, $COD_USUARIO, $COD_STATUS, $CAMPO_COD_SOLICITACAO, $COD_SOLICITACAO . ',');

            $grp = $db->getServiceGroup($COD_SERVICO);
            //$rs = $db->updateRequest_in_Group($grp, $COD_SOLICITACAO);

            $rs = $db->insertRequestCharge($COD_SOLICITACAO, $grp, 'G', '1');
            $tm = $db->insertRequestTimes($COD_SOLICITACAO, $MIN_TEMPO_TELEFONE, '0', '0');

            //insere na tabela de controle a a alteração de status feita e qual usuario fez com a data do acontecimento
            $rs = $db->insertRequestLog($COD_SOLICITACAO, date("Y-m-d H:i:s"), $COD_STATUS, $COD_USUARIO);

            $db2 = new operatorview_model;

            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_POST["COD_ANEXO"] != '') {

                $COD_ANEXO = explode(",", $_POST["COD_ANEXO"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluindo o codigo da solicitacao  nos anexos.                                
                    $Result1 = $db->updateRequestAttach($COD_SOLICITACAO, $COD_ANEXO[$i]);
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            if ($this->database == 'oci8po') {
                $data = "sysdate";
            }
            else
            {
                $data = "now()";
            }
            $DES_APONTAMENTO = "<p><b>" . $langVars['Request_opened'] . "</b></p>";
            $con = $db->insertNote($COD_SOLICITACAO, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');

            //Zerando a variavel que armazena os anexos.
            $_SESSION["SES_COD_ATTACHMENT"] = "";

            $person = $_SESSION['SES_COD_USUARIO'];
            $type = "P";
            $rep = 'N';
            $ind = '1';
            $db2->removeIncharge($COD_SOLICITACAO);
            $insInCharge = $db2->insertInCharge($COD_SOLICITACAO, $person, $type, $rep, $ind);
            $status = '5';
            $reopened = '0';
            $inslog = $db2->changeRequestStatus($status, $COD_SOLICITACAO, $person);
            $ipadress = $_SERVER['REMOTE_ADDR'];
            $callback = '0';
            $idtype = '3';
            $public = '1';
            $note = '<p><b>' . $langVars['Request_closed'] . '</b></p><p><b>' . $langVars['Solution'] . ':</b></p>'. $SOLUTION;
            if ($this->database == 'oci8po') {
                $data = "sysdate";
            }
            else
            {
                $data = "now()";
            }
            $insNote = $db2->insertNote($COD_SOLICITACAO, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
            $changeStat = $db2->updateReqStatus($status, $COD_SOLICITACAO);

            if ($rs) {
                echo $COD_SOLICITACAO;
            } else {
                return false;
            }
        }
    }

    public function dashboard() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $smarty->display('dashboardindex.tpl.html');
    }


    public function newrequestcreated(){
        $code = $this->getParam('code');
        $smarty = $this->retornaSmarty();
        
		$bd = new operatorview_model();
        $req = $bd->getRequestData($code);
        $idperson = $_SESSION['SES_COD_USUARIO'];
		$usertype = $_SESSION['SES_TYPE_PERSON'];
		$idowner = $req->fields['idperson_owner'];
		
		if($idowner == $idperson || $usertype == 2)
			$smarty->assign('typelink', 1);	
		
        $smarty->assign('code', $code);
        $smarty->display('newrequestcreated.tpl.html');
    }

    public function managenoteattachments(){
         unset($final);
         unlink($destino);
         $this->view('manage_noteattachments.php');
    }
	
	public function modalGoogleCalendar(){
		error_reporting(1);
		// Set path to Zend GData
		
		$path_default = $this->getConfig('path_default');
		
		if (substr($path_default, 0, 1) != '/') {
			$path_default = '/' . $path_default;
		}
		$document_root = $_SERVER['DOCUMENT_ROOT'];
		if ($path_default == '/..') {
			if (substr($document_root, -1) != '/') {
				$document_root = $document_root . '/';
			}
			$lib = $document_root ;
		} else {
			if (substr($path_default, -1) != '/') {
				$path_default = $path_default . '/';
			}			
			$lib = $document_root . $path_default ;
		}
		// Zend GData
		$clientLibraryPath = $lib. 'includes/classes/';
		$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		Zend_Loader::loadClass('Zend_Gdata_Extension_When');
		Zend_Loader::loadClass('Zend_Gdata_Extension_Who');
		Zend_Loader::loadClass('Zend_Gdata_Extension_Reminder');
		// Helpdezk 
		$code =  $this->getParam('code');
		$option = $_POST['GoogleType'];
		// Smarty 
		$smarty = $this->retornaSmarty();
		
		switch ($option) {
			case 'Send':
				
				$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; 

				try
				{
					$client = Zend_Gdata_ClientLogin::getHttpClient($_POST['GoogleLogin'], $_POST['GooglePassword'], $service);
					$service = new Zend_Gdata_Calendar($client);
				}
				catch(Exception $e)
				{
					$aError = array ("error" => $e->getMessage());
					echo json_encode($aError);
					exit;
				}		

				// Create a new entry using the calendar service's magic factory method
				$event= $service->newEventEntry();
				// Populate the event with the desired information
				// Note that each attribute is crated as an instance of a matching class
				$event->title = $service->newTitle($_POST['subject']);
				$event->where = array($service->newWhere($_POST['where']));
				$event->content =  $service->newContent($_POST['description']);
				// Set the date using RFC 3339 format.
				$startDate = $this->formatSaveDate($_POST['startdate']);
				$startTime = $_POST['starthour'];
				$endDate = $this->formatSaveDate($_POST['enddate']);
				$endTime   = $_POST['endhour'];
				$tzOffset = substr(date('O'),0,3); //$tzOffset = "-03";
				$when = $service->newWhen();
				$when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
				$when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";

				// Apply the when property to an event
				$event->when = array($when);

				// Invites 
				if (is_array($_POST['emailGuest'])) {
					foreach ($_POST['emailGuest'] as $key => $mail) {
						$who = $service->newWho(); 
						$who->setEmail($mail); 
						$status = new Zend_Gdata_Extension_AttendeeStatus();
						$status->setValue("http://schemas.google.com/g/2005#event.accepted");
						$who->setAttendeeStatus($status);
						$whoArray[] = $who;
					}	
					if (is_array($whoArray)) { 
						$event->setWho($whoArray); 
					}
				}
					
				// Create a new reminder object.
				if($_POST['reminder']) {
					$reminder = $service->newReminder();
					$reminder->method = $_POST['reminder'];
					if($_POST['rem_type'] == 'min') {
						$reminder->minutes = $_POST['time'];
					} elseif($_POST['rem_type'] == 'hou') {
						$reminder->minutes = $_POST['time'];
					} elseif($_POST['rem_type'] == 'day') {
						$reminder->minutes = $_POST['time'];
					}	
					// Apply the reminder to an existing event's when property
					$when = $event->when[0];
					$when->reminders = array($reminder);
				}	
				
				// Visibility
				if ($_POST['selPrivacy'] == 'Pri') {
					$event->visibility = $service->newVisibility("http://schemas.google.com/g/2005#event.private");
				} elseif ($_POST['selPrivacy'] == 'Pub') {	
					$event->visibility = $service->newVisibility("http://schemas.google.com/g/2005#event.public");
				} elseif ($_POST['selPrivacy'] == 'Def') {	
					$event->visibility = $service->newVisibility("http://schemas.google.com/g/2005#event.default");	
				}

				// Send notification		
				if ($_POST['notification']) {
					$sendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications();
					$sendEventNotifications->setValue( true );
					$event->setSendEventNotifications( $sendEventNotifications );
				}	
				
				// Upload the event to the calendar server
				$newEvent = $service->insertEvent($event, $_POST['cal_options']);	

				// insert event
				if($newEvent) {
					// event id
					$event_id = $newEvent->id->text;
					$aSent = array ("success" => 1, "message" => $event_id);
					echo json_encode($aSent);
				} else {
					$aError = array ("success" => 0, "message" => $e->getMessage());
					echo json_encode($aError);
				}
			break;

			case 'Auth':
				
				if (!extension_loaded('openssl')) {
					$langVars = $smarty->get_config_vars();
					$aError = array ("success" => false, "message" => $langVars['Google_Openssl_Error']);
					echo json_encode($aError);				
					exit ;
				}
				
				$db = new operatorview_model();
				$rs  = $db->getPersonPlus($_SESSION['SES_COD_USUARIO'],1) ;

				if ($rs->RecordCount() == 0) {
					if( $_POST['credentials'] ) {
						$rep = $db->insertPersonPlus($_SESSION['SES_COD_USUARIO'], $_POST['login'], $_POST['password'], 1) ;
						if(!$rep){
							$aError = array ("success" => false, "message" => $db->ErrorMsg());
							echo json_encode($aError);
							exit;
						}
					}	
				} else {
					if (($_POST['login'] != $rs->fields['login']) or ($_POST['password'] != $rs->fields['password']) ) {
						$rep = $db->updatePersonPlus($rs->fields['idpersonplus'], $_POST['login'], $_POST['password'], 1) ;
						if(!$rep){
							$aError = array ("success" => false, "message" => $db->ErrorMsg());
							echo json_encode($aError);
							exit;
						}						
					}
				}
				
				$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; 

				try
				{
					$client = Zend_Gdata_ClientLogin::getHttpClient($_POST['login'], $_POST['password'], $service);
					$service = new Zend_Gdata_Calendar($client);
				}
				catch(Exception $e)
				{
					$aError = array ("success" => false, "message" => $e->getMessage());
					echo json_encode($aError);
					exit;
				}	

				try {
					$listFeed= $service->getCalendarListFeed();
				} catch (Zend_Gdata_App_Exception $e) {
					$aError = array ("success" => false, "message" => $e->getMessage());
					echo json_encode($aError);				
					exit;
				}				

				$aCalendar = array();
				foreach ($listFeed as $calendar) {
					$aTemp = array (
									"calendar_title" => $calendar->title->text,
									"calendar_id"    => $calendar->content->src
									);
					array_push($aCalendar,$aTemp)	;
				}
				$aError = array ("success" => true, "message" => $aCalendar);
				//echo json_encode($aCalendar);
				echo json_encode($aError);	
				
			break;
			default:
				$db = new operatorview_model();
				$req = $db->getRequestData($this->getParam('code'));

				$rs  = $db->getPersonPlus($_SESSION['SES_COD_USUARIO'],1) ;
				$rs->RecordCount();
				if ($rs->RecordCount() == 0) {	
					$login    = "";
					$password = "";		
					$langVars = $smarty->get_config_vars();
					$smarty->assign('remember',"<div id=\"remember\"><li><ul><li class=\"info\"><label for=\"credentials\">". $langVars['Google_Credentials']." :</label></li><li class=\"field\"><input type=\"checkbox\" id=\"credentials\" name=\"credentials\" value=\"1\">". $langVars['Google_Save_Credentials']."</li></ul></li></div>");
				} else {
					$login    = $rs->fields['login'];
					$password = $rs->fields['password'];		
				}	
				
				$smarty->assign('subject', $req->fields['subject']);
				$smarty->assign('login', $login);
				$smarty->assign('password', $password);
				$smarty->assign('code', $code);
				$smarty->assign('calendar', "<img src='" . path . "/app/themes/" . theme . "/images/ico_calendario.gif' width='20' height='15' class='calendar' data-prev='expdate' align='absmiddle' id='f_date' style='cursor: pointer; ' title='" . $langVars['Choose_date'] . "'/><a href='javascript:;' class='btnOrange tp1 min' id='saveDate'>".$langVars['Save_term']."</a>");
				$smarty->display('modais/googlecalendar.tpl.html');
			break;
		}

		exit;
		
	}

	public function dif_data($start, $end){
		$StartDate = getdate(strtotime($start)); 
		$EndDate = getdate(strtotime($end)); 
		$Dif = ($EndDate[0] - $StartDate[0]) / 60;
		return number_format($Dif, 0, '', ''); 
	}


}

?>
