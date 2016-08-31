<?php
class requestInsertOperator extends Controllers {
	
	public $database;
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
		$this->database = $this->getConfig('db_connect');
	}
	
	public function index() {
		$smarty = $this->retornaSmarty();
		
		$idperson = $_SESSION['SES_COD_USUARIO']; //user id
		$usertype = $_SESSION['SES_TYPE_PERSON']; //user type
		
		$rm = new requestinsert_model();
		$hm = new home_model();
		$login = $hm->selectUserLogin($idperson);
				
		$_SESSION['SES_COD_ATTACHMENT'] = ""; // ????		
		
		
		$areadefault = $rm->getDefaultArea();
		if($areadefault){
			$smarty->assign('area_default', $areadefault);	
		}			
		else {
			$smarty->assign('area_default', 0);
		}
		
		if ($_SESSION['SES_IND_TIMER_OPENING'] == 1)
			$smarty->assign('timer', 1); //Start countdown
        else
        	$smarty->assign('timer', 0); //Don't start countdown
        
				
		$select = $rm->selectSource();
        while (!$select->EOF) {
            $campos[] = $select->fields['idsource'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }


        $smarty->assign('sourceids', $campos);
        $smarty->assign('sourcevals', $valores);
		$smarty->assign('source_default', 1); //SET HELPDEZK AS DEFAULT
        $campos = '';
        $valores = '';
		
		$dm = new department_model();
		$id_department = $dm->getIdDepartment($idperson);
		
		$select = $rm->selectArea();
		while (!$select->EOF) {
          	
            /************************* AESA *************************/
			//ARRAY DE ÁREAS BLOQUEADAS AESA
			$areas_cead = array(
							       	 92, //CEAD - Núcleo de Atendimento ao Pólo
        							 95, //CSC - Contas a Pagar - Repasse
        							 94, //CSC - Backoffice - Financeiro
        							 93, //Secretaria Acadêmica - CEAD
        							 98, //Pós Graduação - Interativa
        							 99, //Financeiro - Bolsas - CEAD
        							 102, //Marketing Comercial - CEAD
        							 104 //PAI - Programa de Avaliação - CEAD							       	 
							 );
			
			//ARRAY DE ÁREAS PERMITIDAS PARA TODAS EMPRESAS
			$areas_allow_all = array(
										105 //Secretaria Certificação Pós-Graduação - EAD								
								);
 
			//ARRAY DE EMPRESAS QUE NÃO TERÃO ACESSOS A ESTAS ÁREAS
			$empresas_block = array(
									3,4,5,6,7,8,9,10,11,12,14,15,16,17,18,19,
									20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,
									36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,
									52,53,54,55,56,57,58,59,60,61,62,66,67,68,69,71,
									72,73,74,76,77,78,79,84,85,86,87,88,89,90,91,92,
									93,94,95,96,97,98,99,100,101,102,103,107
								);
			//DEPARTAMENTOS QUE TERÃO ACESSOS A ESSAS ÁREAS
			$departments_allow = array(
									   228,282,309,361,375,386,390,520,521,539,593,602,
									   606,612,613,614,637,638,640,642,648,650,652,774,
									   779,808,812,839,855,862,880,883,889,903,917,940,
									   944,946,949,957,974,976,1006,1013,1014,1028,1029,
									   1035,1040,1043,1058,1086,1087,1094,1107,1112,1115,
									   1116,1144,1159,1160,1182,1183,1195,1201,1205,1219,
									   1225,1239,1240,1277,1278,1279,1280,1281,1282,1283,
									   1284,1285
								);
			/************************* //AESA *************************/

			if($_SESSION['SES_LICENSE'] == 200801011 && $_SESSION['SES_COD_EMPRESA'] == 106){ //SE FOR AESA E EMPRESA "Interativa - CEAD"
        		if(in_array($select->fields['idarea'],$areas_cead) || in_array($select->fields['idarea'],$areas_allow_all) ){
        			$campos[] = $select->fields['idarea'];
            		$valores[] = $select->fields['name'];
        		}
				//$smarty->assign('area_default', 92);
			}elseif($_SESSION['SES_LICENSE'] == 200801011){ //SE FOR AESA
				if(in_array($select->fields['idarea'],$areas_cead) ){					
					/*
					 *  Se não está no array de empresas bloqueadas
					 * 	ou se está no array de departamentos permitidos
					 *	mostrará as áreas do array $areas_cead
					 */
					 if(!in_array($_SESSION['SES_COD_EMPRESA'],$empresas_block) || in_array($id_department,$departments_allow) || in_array($select->fields['idarea'],$areas_allow_all)){
        				$campos[] = $select->fields['idarea'];
            			$valores[] = $select->fields['name'];
        			}					
        		}else{
        			$campos[] = $select->fields['idarea'];
            		$valores[] = $select->fields['name'];
        		}				
			}
			else{
        		$campos[] = $select->fields['idarea'];
            	$valores[] = $select->fields['name'];
        	}
			$select->MoveNext();
        }

        $smarty->assign('areaids', $campos);
        $smarty->assign('areavals', $valores);
        $campos = '';
        $valores = '';
		
		$select = $rm->selectWay();
        while (!$select->EOF) {
            $campos3[] = $select->fields['idattendanceway'];
            $valores3[] = $select->fields['way'];
            $select->MoveNext();
        }
        $smarty->assign('wayids', $campos3);
        $smarty->assign('wayvals', $valores3);
        $smarty->assign('waydefault', 1);
 		
 		$smarty->assign('field_reason', 1);
        $smarty->assign('field_att_way', 1);
		$smarty->assign('select_value_way', '');
		
		if($_SESSION['SES_LICENSE'] == 200801011){
			$smarty->assign('field_reason', 0);
            $smarty->assign('field_att_way', 0);
        }		
		if($_SESSION['SES_LICENSE'] == 200701008){
			$smarty->assign('select_value_way', 'NULL');
			$smarty->assign('waydefault', 'NULL');
        }
		
        $smarty->assign('id_person', $_SESSION['SES_COD_USUARIO']);
        $smarty->assign('id_company', $_SESSION['SES_COD_EMPRESA']); 
		$smarty->assign('name_person', $_SESSION['SES_NAME_PERSON']);
        $smarty->assign('login_person', $login);
        
		if($_SESSION['SES_IND_EQUIPMENT'] == 1)
			$smarty->assign('equipment', 1);
		else
			$smarty->assign('equipment', 0);		
		
        $smarty->assign('time',  date("H:i") );
		
		$config['date'] = '%I:%M %p';
		$config['time'] = '%H:%M:%S';
		$smarty->assign('config', $config);
        $sysdate = date('d/m/Y',strtotime('now'));

        $smarty->assign('sysdate',$sysdate);
		
    	$usertype = $_SESSION['SES_TYPE_PERSON'];
		if($usertype == 2)
			$smarty->display("modais/requestInsertUser.tpl.html");
		else 
			$smarty->display("modais/requestInsertOperator.tpl.html"); 
    }
	
	public function saverequest() {
        $smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
        $debug = false;
    	
		//SAVE STEP IN LOG
        $file = fopen($document_root . 'logs/criando_sol.log', 'ab');
        if ($file) {
            $msg = "\r\n\r\n### Criando nova solici	tacao em ".date("Y-m-d H:i:s"). " - usuario:". $_SESSION['SES_COD_USUARIO'];
            fwrite($file, $msg);
        }

		//CHECK IF EXIST IDPERSON
        if (isset($_POST["idperson"])) {
        	
            $MIN_TELEPHONE_TIME = number_format($_POST["open_time"], "2", ".", ",");
			$MIN_ATTENDANCE_TIME = (int) $_POST["open_time"];
            
			//CREATE THE CODE REQUEST
			$CODE_REQUEST = $this->createRequestCode();
            
			//GET THE POST VARS
            $COD_PERSON  		= $_POST["idperson"];
			$COD_PERSON_AUTHOR 	= $_SESSION["SES_COD_USUARIO"];
            $COD_COMPANY 		= $_POST["idjuridical"];
			// -- Equipment --------------------------
            $NUM_SERIAL	= $_POST["serial_number"];
			$NUM_OS 	= $_POST["os_number"];
            $NUM_TAG 	= $_POST["tag"];
			// ---------------------------------------
            $COD_TYPE 		= $_POST["type"];
            $COD_SERVICE 	= $_POST["service"];
            $COD_ITEM 		= $_POST["item"];
            $COD_WAY 		= $_POST["way"];
            $NOM_SUBJECT 	= str_replace("'", "`", $_POST["subject"]);
            $DESCRIPTION 	= str_replace("'", "`", $_POST["description"]);
			$SOLUTION	 	= str_replace("'", "`", $_POST["solution"]);
            $COD_STATUS 	= 1;
			$SOURCE 		= $_POST['source']; // === $COD_ORIGEM
			$REASON 		= $_POST['reason'];
			$DATE	 		= $_POST['date'];
			$TIME 			= $_POST['time'];
			
			if(!$REASON) $REASON = "NULL";
			
			//if telephone
			if ($SOURCE == 2){
				$MIN_TELEPHONE_TIME = $MIN_TELEPHONE_TIME;
				$MIN_EXPENDED_TIME = $MIN_ATTENDANCE_TIME;
			}else{
				$MIN_TELEPHONE_TIME = 0;
				$MIN_EXPENDED_TIME = 0;
			}
			
			$dbrr =  new requestrules_model();
			$rules = $dbrr->getRule($COD_ITEM, $COD_SERVICE);
			$numRules = $rules->RecordCount();




			//IF HAVE APPROVER SET STATUS AS REPASSED
			if($numRules > 0){
				$COD_STATUS = 2;
			}
            // VERIFICA SE O USUARIO EH VIP
            $db = new requestinsert_model();

            $db->BeginTrans();

		
            $rsUsuarioVip = $db->checksVipUser($COD_PERSON);
 			if ($file) {
                $msg = "\r\nVerifica se o usuario é vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // verifica se ha alguma prioridade marcada como VIP
            $rsPrioridadeVip = $db->checksVipPriority();
            if ($file) {
                $msg = "\r\nVerifica prioridades marcadas como vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                 if ($file) {
                    $msg = "\r\nSe for vip e tiver marcada, pega ela (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $COD_PRIORITY = $rsPrioridadeVip->fields["idpriority"];
            }             
            
            else {
                /// Busca a prioridade no servico
                if ($file) {
                    $msg = "\r\nVai buscar a prioridade do serviço (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rsService = $db->getServPriority($COD_SERVICE);

                $COD_PRIORITY = $rsService->fields["idpriority"];


                // se nao tiver prioridade no servico, pega a prioridade padrao do cadastro de prioridade...
                if (!$COD_PRIORITY) {
                	if ($file) {
                        $msg = "\r\nVai buscar a prioridade padrao (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORITY = $rsPrioridade->fields["idpriority"];
                }
            }


            if (!$_POST["date"]) {
            	if($this->database == 'oci8po') $DAT_CADASTRO = date("d/m/Y");
            	elseif($this->database == 'mysqlt') $DAT_CADASTRO = date("Y-m-d");
            } else {
            	if($this->database == 'oci8po') $DAT_CADASTRO = $_POST["date"];
            	elseif($this->database == 'mysqlt') {
            		$DAT_CADASTRO = $this->formatSaveDate($_POST["date"]);
					$DAT_CADASTRO = str_replace("'", "", $DAT_CADASTRO);	
            	}
            }
            if (!$_POST["time"]) {
                $HOR_CADASTRO = date("H:i"); 
            } else {
            	if($this->database == 'oci8po') $HOR_CADASTRO = $_POST["time"];
            	elseif($this->database == 'mysqlt') $HOR_CADASTRO = $this->formatSaveHour($_POST["time"]);
            }
		
            if($this->database == 'oci8po') {
            	$START_DATE = $this->formatSaveDateHour($DAT_CADASTRO." ".$HOR_CADASTRO);
            	$expDate = $this->cData($START_DATE);
            }
            elseif($this->database == 'mysqlt'){
            	$START_DATE = $DAT_CADASTRO." ".$HOR_CADASTRO;
            	$expDate = $START_DATE;
            } 
            

            if ($file) {
	            $msg = "\r\nVai rodar a função para fazer a data de venc. (".__LINE__.") - ";
	            fwrite($file, $msg);
	        }

            //$v_dt = ($this->database == 'oci8po') ? $this->cData($START_DATE) : $START_DATE;
            //($file) ? fwrite($file, "\r\n $v_dt (".__LINE__.") - ") : die("fwrite");

            $EXPIRE_DATE = $this->getExpireDate($expDate, $COD_PRIORITY, $COD_SERVICE);            
            if($file) 
                fwrite($file, "\r\n $EXPIRE_DATE (".__LINE__.") - ");

            //$EXPIRE_DATE = ($this->database == 'oci8po') ? $this->cData($EXPIRE_DATE) : $EXPIRE_DATE;
			if($this->database == 'oci8po'){
				$EXPIRE_DATE = $this->cData($EXPIRE_DATE);
			}
			
            if($file) 
                fwrite($file, "\r\n $EXPIRE_DATE (".__LINE__.") - ");

            if ($file) {
                $msg = "\r\n*Vai inserir a solicitacao no banco (".__LINE__.") -\r\n";
                $msg .= $db->insertRequest2($COD_PERSON_AUTHOR, 
                							$SOURCE, 
                							$START_DATE, 
                							$COD_TYPE, 
                							$COD_ITEM, 
                							$COD_SERVICE, 
                							$REASON,                 							
                							$COD_WAY,                 							
                							$NOM_SUBJECT, 
                							$DESCRIPTION, 
                							$NUM_OS,                 							
                							$COD_PRIORITY,                							 
                							$NUM_TAG, 
                							$NUM_SERIAL, 
                							$COD_COMPANY, 
                							$EXPIRE_DATE, 
                							$COD_PERSON, 
                							$COD_STATUS, 
                							$CODE_REQUEST);
                fwrite($file, $msg);
            }
			
            $rs = $db->insertRequest(	$COD_PERSON_AUTHOR, 
            							$SOURCE, 
            							$START_DATE, 
            							$COD_TYPE, 
            							$COD_ITEM, 
            							$COD_SERVICE, 
            							$REASON,                 							
            							$COD_WAY,                 							
            							$NOM_SUBJECT, 
            							$DESCRIPTION, 
            							$NUM_OS,                 							
            							$COD_PRIORITY,                							 
            							$NUM_TAG, 
            							$NUM_SERIAL, 
            							$COD_COMPANY, 
            							$EXPIRE_DATE, 
            							$COD_PERSON, 
            							$COD_STATUS, 
            							$CODE_REQUEST);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
				
				
            if ($file) {
                $msg = "\r\n*Vai buscar o grupo de atendimento do serviço (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $grp = $db->getServiceGroup($COD_SERVICE);
            if(!$grp){
            	$db->RollbackTrans();
                return false;
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
					$values .= "(".$rules->fields['idapproval'].",". $CODE_REQUEST .",". $rules->fields['order'] .",". $rules->fields['idperson'] .",". $rules->fields['fl_recalculate'] .")";		            
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
							$rs2 = $db->insertRequestCharge($CODE_REQUEST, $grp2, 'G', '0');
						else
							$rs2 = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '0');
						
					}else{//REGRA PARA CASO SEM APROVADOR E GRUPO SOMENTE REPASSADOR
						$rs2 = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '0');	
					}					
					
					$rs = $db->insertRequestCharge($CODE_REQUEST, $APROVADOR, 'P', '1');
					
					if(!$rs || !$rs2){
						$db->RollbackTrans();
		                return false;
		            }
                }
			}else{
				$grp_model = new groups_model();				
				$onlyRep = $grp_model->checkGroupOnlyRepass($grp);
				
				if($onlyRep->fields['repass_only'] == "Y"){//REGRA PARA CASO ESTE GRUPO SEJA SOMENTE REPASSADOR
					$newidgroup = $grp_model->getNewGroupOnlyRepass($grp,$_SESSION['SES_COD_EMPRESA']);
					$grp2 = $newidgroup->fields['idperson'];
					if($grp2)
						$rs = $db->insertRequestCharge($CODE_REQUEST, $grp2, 'G', '1');
					else
						$rs = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '1');
					
		            if(!$rs){
		            	$db->RollbackTrans();
		                return false;
		            }
				}else{//REGRA PARA CASO SEM APROVADOR E GRUPO SOMENTE REPASSADOR
					$rs = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '1');
		            if(!$rs){
		            	$db->RollbackTrans();
		                return false;
		            }	
				}
			}     
           
            if ($file) {
                $msg = "\r\n*Vai inserir os tempos na request_times (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			$tm = $db->insertRequestTimesNew($CODE_REQUEST, 0, 0, $MIN_EXPENDED_TIME, $MIN_TELEPHONE_TIME, 0);
            if(!$tm){
            	$db->RollbackTrans();
                return false;
            }

            if ($file) {
                $msg = "\r\n*Vai criar a data na hdk_tbrequest_times (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $dr = $db->insertRequestDate($CODE_REQUEST);
            if(!$dr){
                $db->RollbackTrans();
                return false;
            }
			            
            //insere na tabela de controle a a alteração de status feita e qual usuario fez com a data do acontecimento
            if ($file) {
                $msg = "\r\n*Vai inserir na resquest_log (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $rs = $db->insertRequestLog($CODE_REQUEST, date("Y-m-d H:i:s"), $COD_STATUS, $COD_PERSON);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
			
            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_SESSION["SES_COD_ATTACHMENT"]) {
                $COD_ANEXO = explode(",", $_SESSION["SES_COD_ATTACHMENT"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluรญndo o cรณdigo da solicitaรงรฃo  nos anexos.     
                    if ($file) {
                        $msg = "\r\n*Vai atualizar o codigo da solicitacao na tabela de anexos (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }                           
                    $Result1 = $db->updateRequestAttach($CODE_REQUEST, $COD_ANEXO[$i]);
                    if(!$Result1){
                    	$db->RollbackTrans();
                       	return false;
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
            $con = $db->insertNote($CODE_REQUEST, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');
            if(!$con){
            	$db->RollbackTrans();
                return false;
            }


			
			if($SOLUTION){				
				//Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
	            if ($this->database == 'oci8po') {
	        		$data = "sysdate";
				}
				else
				{
	            	$data = "now()";
	            }
	            $DES_APONTAMENTO = "<p><b>" . $langVars['Solution'] . "</b></p>". $SOLUTION;
	            if ($file) {
	                $msg = "\r\n*Vai inserir o apontamento de solução (".__LINE__.") - ";
	                fwrite($file, $msg);
	            }  
	            $con = $db->insertNote($CODE_REQUEST, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", "3", "0", "0", "0", "0", $_SERVER['REMOTE_ADDR'], 'NULL');
	            if(!$con){
	            	$db->RollbackTrans();
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
	                    $this->sendEmail('approve', $CODE_REQUEST);
	                    
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
                        if ($debug){
	                        $this->sendEmail('record', $CODE_REQUEST);
                        }

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
				$db->CommitTrans();
                echo $CODE_REQUEST;
            } else {
                return false;
            }
        }//fim do cadastro
    }

    public function cData( $data )
    {
        $data = explode(" ",$data);
        if ( ! strstr( $data[0], '/' ) )
        {
            // $data está no formato ISO (yyyy-mm-dd) e deve ser convertida
            // para dd/mm/yyyy
            sscanf( $data[0], '%d-%d-%d', $y, $m, $d );
            return sprintf( '%d/%d/%d', $d, $m, $y )." ".$data[1];
        }
        else
        {
            // $data está no formato brasileiro e deve ser convertida para ISO
            sscanf( $data[0], '%d/%d/%d', $d, $m, $y );
            return sprintf( '%d-%d-%d', $y, $m, $d )." ".$data[1];
        }

        return false;
    }


    public function saverequestuser() {
        $smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
    	
		//SAVE STEP IN LOG
        $file = fopen($document_root . 'logs/ticket_detail.log', 'ab');
        if ($file) {
            $msg = "\r\n\r\n### Criando nova solicitacao em ".date("Y-m-d H:i:s"). " - usuario:". $_SESSION['SES_COD_USUARIO'];
            fwrite($file, $msg);
        }
        
		//CHECK IF EXIST IDPERSON
        if (isset($_POST["idperson"])) {           
			//CREATE THE CODE REQUEST
			$CODE_REQUEST = $this->createRequestCode();            
			//GET THE POST VARS
            //$COD_PERSON  		= $_POST["idperson"];
			$COD_PERSON 	= $_SESSION["SES_COD_USUARIO"];
            $COD_COMPANY 	= $_SESSION['SES_COD_EMPRESA'];
			// -- Equipment --------------------------
            $NUM_SERIAL	= $_POST["serial_number"];
			$NUM_OS 	= $_POST["os_number"];
            $NUM_TAG 	= $_POST["tag"];
			// ---------------------------------------
            $COD_TYPE 		= $_POST["type"];
            $COD_SERVICE 	= $_POST["service"];
            $COD_ITEM 		= $_POST["item"];
            $COD_WAY 		= 1;
            $NOM_SUBJECT 	= str_replace("'", "`", $_POST["subject"]);
            $DESCRIPTION 	= str_replace("'", "`", $_POST["description"]);
            $COD_STATUS 	= 1;
			$SOURCE 		= 1;
			$REASON 		= "NULL";
			
			$dbrr =  new requestrules_model();
			$rules = $dbrr->getRule($COD_ITEM, $COD_SERVICE);
			$numRules = $rules->RecordCount();
			//IF HAVE APPROVER SET STATUS AS REPASSED
			if($numRules > 0){
				$COD_STATUS = 2;
			}
			
            // VERIFICA SE O USUARIO EH VIP
            $db = new requestinsert_model();
            $db->BeginTrans();
		
            $rsUsuarioVip = $db->checksVipUser($COD_PERSON);
 			if ($file) {
                $msg = "\r\nVerifica se o usuario é vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // verifica se ha alguma prioridade marcada como VIP
            $rsPrioridadeVip = $db->checksVipPriority();
            if ($file) {
                $msg = "\r\nVerifica prioridades marcadas como vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                 if ($file) {
                    $msg = "\r\nSe for vip e tiver marcada, pega ela (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $COD_PRIORITY = $rsPrioridadeVip->fields["idpriority"];
            }             
            
            else {
                /// Busca a prioridade no servico
                if ($file) {
                    $msg = "\r\nVai buscar a prioridade do serviço (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rsService = $db->getServPriority($COD_SERVICE);
                $COD_PRIORITY = $rsService->fields['idpriority'];

                // se nao tiver prioridade no servico, pega a prioridade padrao do cadastro de prioridade...
                if (!$COD_PRIORITY) {
                	if ($file) {
                        $msg = "\r\nVai buscar a prioridade padrao (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORITY = $rsPrioridade->fields["idpriority"];
                }
            }


            $HOR_CADASTRO = date("H:i"); 
        
            if($this->database == 'oci8po') {
                $DAT_CADASTRO = date("d/m/Y");
                $START_DATE = $this->formatSaveDateHour($DAT_CADASTRO." ".$HOR_CADASTRO);
                $expDate = $this->cData($START_DATE);
            }
            elseif($this->database == 'mysqlt'){
                $DAT_CADASTRO = date("Y-m-d");
                $START_DATE = $DAT_CADASTRO." ".$HOR_CADASTRO;
                $expDate = $START_DATE;
            } 
			
            if ($file) {
	            $msg = "\r\nVai rodar a função para fazer a data de venc. (".__LINE__.") - ";
	            fwrite($file, $msg);
	        }

            $EXPIRE_DATE = $this->getExpireDate($expDate, $COD_PRIORITY, $COD_SERVICE);
            if($this->database == 'oci8po'){
                $EXPIRE_DATE = $this->cData($EXPIRE_DATE);
            }


            if ($file) {
                $msg = "\r\n*Vai inserir a solicitacao no banco (".__LINE__.") -\r\n";
                $msg .= $db->insertRequest2($COD_PERSON, 
                							$SOURCE, 
                							$START_DATE, 
                							$COD_TYPE, 
                							$COD_ITEM, 
                							$COD_SERVICE, 
                							$REASON,                 							
                							$COD_WAY,                 							
                							$NOM_SUBJECT, 
                							$DESCRIPTION, 
                							$NUM_OS,                 							
                							$COD_PRIORITY,                							 
                							$NUM_TAG, 
                							$NUM_SERIAL, 
                							$COD_COMPANY, 
                							$EXPIRE_DATE, 
                							$COD_PERSON, 
                							$COD_STATUS, 
                							$CODE_REQUEST);
                fwrite($file, $msg);
            }

            if ( $this->getConfig('license') == '201601001')
            {
                //Key
                $key =  'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282';
                //To Encrypt:
                $encrypted = $this->mc_encrypt($NOM_SUBJECT, $key);
                $NOM_SUBJECT = $encrypted ;
                $DESCRIPTION = $this->mc_encrypt($DESCRIPTION, $key);

            }

            $rs = $db->insertRequest(	$COD_PERSON, 
            							$SOURCE, 
            							$START_DATE, 
            							$COD_TYPE, 
            							$COD_ITEM, 
            							$COD_SERVICE, 
            							$REASON,                 							
            							$COD_WAY,                 							
            							$NOM_SUBJECT, 
            							$DESCRIPTION, 
            							$NUM_OS,                 							
            							$COD_PRIORITY,                							 
            							$NUM_TAG, 
            							$NUM_SERIAL, 
            							$COD_COMPANY, 
            							$EXPIRE_DATE, 
            							$COD_PERSON, 
            							$COD_STATUS, 
            							$CODE_REQUEST);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
				


            if ($file) {
                $msg = "\r\n*Vai buscar o grupo de atendimento do serviço (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $grp = $db->getServiceGroup($COD_SERVICE);
            if(!$grp){
            	$db->RollbackTrans();
                return false;
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
					$values .= "(".$rules->fields['idapproval'].",". $CODE_REQUEST .",". $rules->fields['order'] .",". $rules->fields['idperson'] .",". $rules->fields['fl_recalculate'] .")";		            
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
							$rs2 = $db->insertRequestCharge($CODE_REQUEST, $grp2, 'G', '0');
						else
							$rs2 = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '0');
						
					}else{//REGRA PARA CASO SEM APROVADOR E GRUPO SOMENTE REPASSADOR
						$rs2 = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '0');	
					}					
					
					$rs = $db->insertRequestCharge($CODE_REQUEST, $APROVADOR, 'P', '1');
					
					if(!$rs || !$rs2){
						$db->RollbackTrans();
		                return false;
		            }
                }
			}else{
				$grp_model = new groups_model();
				$onlyRep = $grp_model->checkGroupOnlyRepass($grp);
				
				if($onlyRep->fields['repass_only'] == "Y"){//REGRA PARA CASO ESTE GRUPO SEJA SOMENTE REPASSADOR
					$newidgroup = $grp_model->getNewGroupOnlyRepass($grp,$_SESSION['SES_COD_EMPRESA']);
					$grp2 = $newidgroup->fields['idperson'];
					if($grp2)
						$rs = $db->insertRequestCharge($CODE_REQUEST, $grp2, 'G', '1');
					else
						$rs = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '1');
					
		            if(!$rs){
		            	$db->RollbackTrans();
		                return false;
		            }
				}else{//REGRA PARA CASO SEM APROVADOR E GRUPO SOMENTE REPASSADOR
					$rs = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '1');
		            if(!$rs){
		            	$db->RollbackTrans();
		                return false;
		            }	
				}
			}     
           	
		   
            if ($file) {
                $msg = "\r\n*Vai inserir os tempos na request_times (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			$tm = $db->insertRequestTimesNew($CODE_REQUEST);
            if(!$tm){
            	$db->RollbackTrans();
                return false;
            }

            if ($file) {
                $msg = "\r\n*Vai criar a data na hdk_tbrequest_times (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $dr = $db->insertRequestDate($CODE_REQUEST);
            if(!$dr){
                $db->RollbackTrans();
                return false;
            }
			            
            //insere na tabela de controle a a alteração de status feita e qual usuario fez com a data do acontecimento
            if ($file) {
                $msg = "\r\n*Vai inserir na resquest_log (".__LINE__.") - ";
                fwrite($file, $msg);
            }
            $rs = $db->insertRequestLog($CODE_REQUEST, date("Y-m-d H:i:s"), $COD_STATUS, $COD_PERSON);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }

            $db2 = new operatorview_model;
            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_SESSION["SES_COD_ATTACHMENT"]) {
                $COD_ANEXO = explode(",", $_SESSION["SES_COD_ATTACHMENT"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluรญndo o cรณdigo da solicitaรงรฃo  nos anexos.     
                    if ($file) {
                        $msg = "\r\n*Vai atualizar o codigo da solicitacao na tabela de anexos (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }                           
                    $Result1 = $db->updateRequestAttach($CODE_REQUEST, $COD_ANEXO[$i]);
                    if(!$Result1){
                    	$db->RollbackTrans();
                       	return false;
                    }
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
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
            $con = $db->insertNote($CODE_REQUEST, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');
            if(!$con){
                return false;
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
	                    $this->sendEmail('approve', $CODE_REQUEST);
	                    
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
	                    $this->sendEmail('record', $CODE_REQUEST);
	                    
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
				$db->CommitTrans();
                echo $CODE_REQUEST;
            } else {
                return false;
            }
        }//fim do cadastro
    }



	
	public function finishrequest() {
        $smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
        
		//CHECK IF EXIST IDPERSON
        if (isset($_POST["idperson"])) {
            $MIN_TEMPO_TELEFONE = number_format($_POST["open_time"], "2", ".", ",");
            
			//CREATE THE CODE REQUEST
			$CODE_REQUEST = $this->createRequestCode();
            
			//GET THE POST VARS
            $COD_PERSON  		= $_POST["idperson"];
			$COD_PERSON_AUTHOR 	= $_SESSION["SES_COD_USUARIO"];
            $COD_COMPANY 		= $_POST["idjuridical"];
			// -- Equipment --------------------------
            $NUM_SERIAL	= $_POST["serial_number"];
			$NUM_OS 	= $_POST["os_number"];
            $NUM_TAG 	= $_POST["tag"];
			// ---------------------------------------
            $COD_TYPE 		= $_POST["type"];
            $COD_SERVICE 	= $_POST["service"];
            $COD_ITEM 		= $_POST["item"];
            $COD_WAY 		= $_POST["way"];
            $NOM_SUBJECT 	= str_replace("'", "`", $_POST["subject"]);
            $DESCRIPTION 	= str_replace("'", "`", $_POST["description"]);
			$SOLUTION	 	= str_replace("'", "`", $_POST["solution"]);
            $COD_STATUS 	= 5;
			$SOURCE 		= $_POST['source']; // === $COD_ORIGEM
			$REASON 		= $_POST['reason'];
			$DATE	 		= $_POST['date'];
			$TIME 			= $_POST['time'];
			if(!$REASON) $REASON = "NULL";
			
			// VERIFICA SE O USUARIO EH VIP
            $db = new requestinsert_model();
            $db->BeginTrans();
		
            $rsUsuarioVip = $db->checksVipUser($COD_PERSON);
 			if ($file) {
                $msg = "\r\nVerifica se o usuario é vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // verifica se ha alguma prioridade marcada como VIP
            $rsPrioridadeVip = $db->checksVipPriority();
            if ($file) {
                $msg = "\r\nVerifica prioridades marcadas como vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                if ($file) {
                    $msg = "\r\nSe for vip e tiver marcada, pega ela (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $COD_PRIORITY = $rsPrioridadeVip->fields["idpriority"];
            }             
            
            else {
                /// Busca a prioridade no servico
                if ($file) {
                    $msg = "\r\nVai buscar a prioridade do serviço (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rsService = $db->getServPriority($COD_SERVICE);
                $COD_PRIORITY = $rsService->fields['idpriority'];

                // se nao tiver prioridade no servico, pega a prioridade padrao do cadastro de prioridade...
                if (!$COD_PRIORITY) {
                	if ($file) {
                        $msg = "\r\nVai buscar a prioridade padrao (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORITY = $rsPrioridade->fields["idpriority"];
                }
            }

            if (!$_POST["date"]) {
                if($this->database == 'oci8po') $DAT_CADASTRO = date("d/m/Y");
                elseif($this->database == 'mysqlt') $DAT_CADASTRO = date("Y-m-d");
            } else {
                if($this->database == 'oci8po') $DAT_CADASTRO = $_POST["date"];
                elseif($this->database == 'mysqlt') {
                    $DAT_CADASTRO = $this->formatSaveDate($_POST["date"]);
                    $DAT_CADASTRO = str_replace("'", "", $DAT_CADASTRO);    
                }
            }
            if (!$_POST["time"]) {
                $HOR_CADASTRO = date("H:i"); 
            } else {
                if($this->database == 'oci8po') $HOR_CADASTRO = $_POST["time"];
                elseif($this->database == 'mysqlt') $HOR_CADASTRO = $this->formatSaveHour($_POST["time"]);
            }
        
            if($this->database == 'oci8po') {
                $START_DATE = $this->formatSaveDateHour($DAT_CADASTRO." ".$HOR_CADASTRO);
                $expDate = $this->cData($START_DATE);
            }
            elseif($this->database == 'mysqlt'){
                $START_DATE = $DAT_CADASTRO." ".$HOR_CADASTRO;
                $expDate = $START_DATE;
            } 

            if ($file) {
                $msg = "\r\nVai rodar a função para fazer a data de venc. (".__LINE__.") - ";
                fwrite($file, $msg);
            }

            $EXPIRE_DATE = $this->getExpireDate($expDate, $COD_PRIORITY, $COD_SERVICE);            
            if ($file) {
                $msg = "\r\n $EXPIRE_DATE (".__LINE__.") - ";
                fwrite($file, $msg);
            }

            if($this->database == 'oci8po'){
                $EXPIRE_DATE = $this->cData($EXPIRE_DATE);
            }

            

            $rs = $db->insertRequest(	$COD_PERSON_AUTHOR, 
            							$SOURCE, 
            							$START_DATE, 
            							$COD_TYPE, 
            							$COD_ITEM, 
            							$COD_SERVICE, 
            							$REASON,                 							
            							$COD_WAY,                 							
            							$NOM_SUBJECT, 
            							$DESCRIPTION, 
            							$NUM_OS,                 							
            							$COD_PRIORITY,                							 
            							$NUM_TAG, 
            							$NUM_SERIAL, 
            							$COD_COMPANY, 
            							$EXPIRE_DATE, 
            							$COD_PERSON, 
            							$COD_STATUS, 
            							$CODE_REQUEST);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
				
            $grp = $db->getServiceGroup($COD_SERVICE);
            if(!$grp){
            	$db->RollbackTrans();
                return false;
            }		   
			
           	$rs = $db->insertRequestCharge($CODE_REQUEST, $grp, 'G', '1');
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
		   
            $tm = $db->insertRequestTimes($CODE_REQUEST, $MIN_TEMPO_TELEFONE, '0', '0');
            if(!$tm){
            	$db->RollbackTrans();
                return false;
            }
			
            $rs = $db->insertRequestLog($CODE_REQUEST, date("Y-m-d H:i:s"), $COD_STATUS, $COD_PERSON);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }

            $dr = $db->insertRequestDate($CODE_REQUEST);
            if(!$dr){
                $db->RollbackTrans();
                return false;
            }

            $db2 = new operatorview_model();
            $ud = $db2->updateDate($CODE_REQUEST, "finish_date");
            if(!$ud){
                $db2->RollbackTrans();
                return false;
            }

            if ($_SESSION["SES_COD_ATTACHMENT"]) {
                $COD_ANEXO = explode(",", $_SESSION["SES_COD_ATTACHMENT"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluรญndo o cรณdigo da solicitaรงรฃo  nos anexos.     
                    if ($file) {
                        $msg = "\r\n*Vai atualizar o codigo da solicitacao na tabela de anexos (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }                           
                    $Result1 = $db->updateRequestAttach($CODE_REQUEST, $COD_ANEXO[$i]);
                    if(!$Result1){
                    	$db->RollbackTrans();
                       	return false;
                    }
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            if ($this->database == 'oci8po') {
        		$data = "sysdate";
			}
			else
			{
            	$data = "now()";
            }
            $DES_APONTAMENTO = "<p><b>" . $langVars['Request_opened'] . "</b></p>";
            $con = $db->insertNote($CODE_REQUEST, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');
		    if(!$con){
                $db->RollbackTrans();
                return false;
            }
            			
			$person = $_SESSION['SES_COD_USUARIO'];
            $type = "P";
            $rep = 'N';
            $ind = '1';
			$db2->removeIncharge($CODE_REQUEST);
            $insInCharge = $db2->insertInCharge($CODE_REQUEST, $person, $type, $rep, $ind);
			$status = '5';
            $reopened = '0';
            $inslog = $db2->changeRequestStatus($status, $CODE_REQUEST, $person);
			$ipadress = $_SERVER['REMOTE_ADDR'];
            $callback = '0';
            $idtype = '3';
            $public = '1';
            $note = '<p><b>' . $langVars['Request_closed'] . '</b></p><p><b>' . $langVars['Solution'] . ':</b></p>'. $SOLUTION;
            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            if ($this->database == 'oci8po') {
        		$data = "sysdate";
			}
			else
			{
            	$data = "now()";
            }
			$insNote = $db2->insertNote($CODE_REQUEST, $person, $note, "$data", NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
            $changeStat = $db2->updateReqStatus($status, $CODE_REQUEST);
			
            //Zerando a variavel que armazena os anexos.
           	$_SESSION["SES_COD_ATTACHMENT"] = "";
           	
           	if ($rs) {
           		$db->CommitTrans();
                echo $CODE_REQUEST;
            } else {
                return false;
            }
        }//fim do cadastro
    }
	
	public function openrepassed() {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $debug = false;
		$repassto = $_POST['repassto'];
		$typerepass = $_POST['typerepass'];
		$viewrepass = $_POST['viewrepass'];
		
        if ($typerepass == 'operator') {
            $db = new person_model();
            $name = $db->selectPersonName($repassto);
            $typerepass = $langVars['to'] . " " . $langVars['Operator'];
            $type2 = "P";
        } 
        elseif ($typerepass == 'group') {
            $db = new groups_model();
            $name = $db->selectRepGroupData($repassto);
            $name = $name->fields['name'];
            $typerepass = $langVars['to'] . " " . $langVars['Group'];
            $type2 = "G";
            
        }  
		
        if (isset($repassto)) {
        	$MIN_TEMPO_TELEFONE = number_format($_POST["open_time"], "2", ".", ",");
            
			//CREATE THE CODE REQUEST
			$CODE_REQUEST = $this->createRequestCode();
            
			//GET THE POST VARS
            $COD_PERSON  		= $_POST["idperson"];
			$COD_PERSON_AUTHOR 	= $_SESSION["SES_COD_USUARIO"];
            $COD_COMPANY 		= $_POST["idjuridical"];
			// -- Equipment --------------------------
            $NUM_SERIAL	= $_POST["serial_number"];
			$NUM_OS 	= $_POST["os_number"];
            $NUM_TAG 	= $_POST["tag"];
			// ---------------------------------------
            $COD_TYPE 		= $_POST["type"];
            $COD_SERVICE 	= $_POST["service"];
            $COD_ITEM 		= $_POST["item"];
            $COD_WAY 		= $_POST["way"];
            $NOM_SUBJECT 	= str_replace("'", "`", $_POST["subject"]);
            $DESCRIPTION 	= str_replace("'", "`", $_POST["description"]);
			$SOLUTION	 	= str_replace("'", "`", $_POST["solution"]);
            $COD_STATUS 	= 2;
			$SOURCE 		= $_POST['source']; // === $COD_ORIGEM
			$REASON 		= $_POST['reason'];
			$DATE	 		= $_POST['date'];
			$TIME 			= $_POST['time'];
            if(!$REASON) $REASON = "NULL";
            // VERIFICA SE O USUARIO EH VIP
            $db = new requestinsert_model();
            $db->BeginTrans();
		
            $rsUsuarioVip = $db->checksVipUser($COD_PERSON);
 			if ($file) {
                $msg = "\r\nVerifica se o usuario é vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // verifica se ha alguma prioridade marcada como VIP
            $rsPrioridadeVip = $db->checksVipPriority();
            if ($file) {
                $msg = "\r\nVerifica prioridades marcadas como vip (".__LINE__.") - ";
                fwrite($file, $msg);
            }
			
            // Se o usuario for VIP e tiver prioridade marcada para VIP, pega essa
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                 if ($file) {
                    $msg = "\r\nSe for vip e tiver marcada, pega ela (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $COD_PRIORITY = $rsPrioridadeVip->fields["idpriority"];
            }             
            
            else {
                /// Busca a prioridade no servico
                if ($file) {
                    $msg = "\r\nVai buscar a prioridade do serviço (".__LINE__.") - ";
                    fwrite($file, $msg);
                }
                $rsService = $db->getServPriority($COD_SERVICE);
                $COD_PRIORITY = $rsService->fields['idpriority'];

                // se nao tiver prioridade no servico, pega a prioridade padrao do cadastro de prioridade...
                if (!$COD_PRIORITY) {
                	if ($file) {
                        $msg = "\r\nVai buscar a prioridade padrao (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORITY = $rsPrioridade->fields["idpriority"];
                }
            }





            if (!$_POST["date"]) {
                if($this->database == 'oci8po') $DAT_CADASTRO = date("d/m/Y");
                elseif($this->database == 'mysqlt') $DAT_CADASTRO = date("Y-m-d");
            } else {
                if($this->database == 'oci8po') $DAT_CADASTRO = $_POST["date"];
                elseif($this->database == 'mysqlt') {
                    $DAT_CADASTRO = $this->formatSaveDate($_POST["date"]);
                    $DAT_CADASTRO = str_replace("'", "", $DAT_CADASTRO);    
                }
            }
            if (!$_POST["time"]) {
                $HOR_CADASTRO = date("H:i"); 
            } else {
                if($this->database == 'oci8po') $HOR_CADASTRO = $_POST["time"];
                elseif($this->database == 'mysqlt') $HOR_CADASTRO = $this->formatSaveHour($_POST["time"]);
            }
        
            if($this->database == 'oci8po') {
                $START_DATE = $this->formatSaveDateHour($DAT_CADASTRO." ".$HOR_CADASTRO);
                $expDate = $this->cData($START_DATE);
            }
            elseif($this->database == 'mysqlt'){
                $START_DATE = $DAT_CADASTRO." ".$HOR_CADASTRO;
                $expDate = $START_DATE;
            } 

            if ($file) {
                $msg = "\r\nVai rodar a função para fazer a data de venc. (".__LINE__.") - ";
                fwrite($file, $msg);
            }

            $EXPIRE_DATE = $this->getExpireDate($expDate, $COD_PRIORITY, $COD_SERVICE);            
            if ($file) {
                $msg = "\r\n $EXPIRE_DATE (".__LINE__.") - ";
                fwrite($file, $msg);
            }

            if($this->database == 'oci8po'){
                $EXPIRE_DATE = $this->cData($EXPIRE_DATE);
            }
         			
			$rs = $db->insertRequest(	$COD_PERSON_AUTHOR, 
            							$SOURCE, 
            							$START_DATE, 
            							$COD_TYPE, 
            							$COD_ITEM, 
            							$COD_SERVICE, 
            							$REASON,                 							
            							$COD_WAY,                 							
            							$NOM_SUBJECT, 
            							$DESCRIPTION, 
            							$NUM_OS,                 							
            							$COD_PRIORITY,                							 
            							$NUM_TAG, 
            							$NUM_SERIAL, 
            							$COD_COMPANY, 
            							$EXPIRE_DATE, 
            							$COD_PERSON, 
            							$COD_STATUS, 
            							$CODE_REQUEST);
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
			
			switch($viewrepass){			
				case "P": //REPASSAR E SEGUIR ACOMPANHANDO
						$opmodel = new operatorview_model();
						$track = $opmodel->insertInCharge($CODE_REQUEST, $_SESSION['SES_COD_USUARIO'], "P", "Y", '0', '1');
						if(!$track){
							$db->RollbackTrans();
							return false;
						}
					break;
				case "N": //NAO ACOMPANHAR
					
					break;
			}
			
            $rs = $db->insertRequestCharge($CODE_REQUEST, $repassto, $type2, '1');
            if(!$rs){
            	$db->RollbackTrans();
                return false;
            }
            $tm = $db->insertRequestTimes($CODE_REQUEST, $MIN_TEMPO_TELEFONE, '0', '0');
            if(!$tm){
            	$db->RollbackTrans();
                return false;
            }

            $dr = $db->insertRequestDate($CODE_REQUEST);
            if(!$dr){
                $db->RollbackTrans();
                return false;
            }

            $db2 = new operatorview_model();
            $ud = $db2->updateDate($CODE_REQUEST, "forwarded_date");
            if(!$ud){
                $db2->RollbackTrans();
                return false;
            }

           
            //insere na tabela de controle a a alteração de status feita e qual usuario fez com a data do acontecimento
            $rs = $db->insertRequestLog($CODE_REQUEST, date("Y-m-d H:i:s"), $COD_STATUS, $COD_PERSON);
            if(!$rs){
                return false;
            }
			
            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_SESSION["SES_COD_ATTACHMENT"]) {
                $COD_ANEXO = explode(",", $_SESSION["SES_COD_ATTACHMENT"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluรญndo o cรณdigo da solicitaรงรฃo  nos anexos.     
                    if ($file) {
                        $msg = "\r\n*Vai atualizar o codigo da solicitacao na tabela de anexos (".__LINE__.") - ";
                        fwrite($file, $msg);
                    }                           
                    $Result1 = $db->updateRequestAttach($CODE_REQUEST, $COD_ANEXO[$i]);
                    if(!$Result1){
                    	$db->RollbackTrans();
                       	return false;
                    }
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            if ($this->database == 'oci8po') {
        		$data = "sysdate";
			}
			else
			{
            	$data = "now()";
            }
            $DES_APONTAMENTO = "<p><b>" . $langVars['Request_opened'] . "</b></p>";
            $con = $db->insertNote($CODE_REQUEST, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, "$data", '3', '0', '0', '0', '0', $_SERVER['REMOTE_ADDR'], 'null');
            if(!$con){
            	$db->RollbackTrans();
                return false;
            }
			
			if($SOLUTION){				
				//Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
	            if ($this->database == 'oci8po') {
	        		$data = "sysdate";
				}
				else
				{
	            	$data = "now()";
	            }
	            $DES_APONTAMENTO = "<p><b>" . $langVars['Solution'] . "</b></p>". $SOLUTION;
	            if ($file) {
	                $msg = "\r\n*Vai inserir o apontamento de solução (".__LINE__.") - ";
	                fwrite($file, $msg);
	            }  
	            $con = $db->insertNote($CODE_REQUEST, $COD_PERSON_AUTHOR, $DES_APONTAMENTO, "$data", "3", "0", "0", "0", "0", $_SERVER['REMOTE_ADDR'], 'NULL');
							
	            if(!$con){
	                return false;
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
	       	$note = "<p><b>" . $langVars['Request_repassed'] . strtolower($typerepass) . " " . $name . "</b></p>";			
			$insNote = $db->insertNote($CODE_REQUEST, $COD_PERSON_AUTHOR, $note, "$data", "3", "0", "0", "0", "0", $_SERVER['REMOTE_ADDR'], 'NULL');
	        if (!$insNote) {
	            $db->RollbackTrans();
				return false;
	        }
			
            //Zerando a variavel que armazena os anexos.
            $_SESSION["SES_COD_ATTACHMENT"] = "";
            
            if ($rs) {
                if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['NEW_REQUEST_OPERATOR_MAIL'] == '1') {
                    if ($debug){
                        $this->sendEmail('record', $CODE_REQUEST);
                    }
                }
				$db->CommitTrans();
                echo $CODE_REQUEST;
            } else {
                return false;
            }
        }else{
        	return false;
        }//fim do cadastro
    }
			
	private function getExpireDate($START_DATE = null, $COD_PRIORITY = null, $COD_SERVICE = null){		
		if(!isset($START_DATE)){$START_DATE = date("Y-m-d H:i:s");}
		
		$db = new expiredate_model();
		$db->BeginTrans();
		
		//SE TEM CODIGO DO SERVIÇO
		if(isset($COD_SERVICE)){
			$idcompany = $db->getIdCustumerByService($COD_SERVICE);
			
			$getExpireDateService = $db->getExpireDateService($COD_SERVICE);
			if(!$getExpireDateService){
				$db->RollbackTrans();
				return false;
			}
			$days = $getExpireDateService->fields['days_attendance']; //NUM DE DIAS DO PRAZO
			$time = $getExpireDateService->fields['hours_attendance']; //QUANTIDADE DE TEMPO
			$type_time = $getExpireDateService->fields['ind_hours_minutes']; //TIPO DO TEMPO H = HORAS | M = MINUTOS
			
			if($days > 0){
				$days_sum = "+".$days." days";
			}			
			if($time > 0){
				if($type_time == "H"){
					$time_sum = "+".$time." hour";
				}
				elseif($type_time == "M"){
					$time_sum = "+".$time." minutes";
				}
			}
		}
		
		//SE TEM O CODIGO DA PRIORIDADE E O TEMPO E DIAS DO SERVIÇO FOREM 0
		if(isset($COD_PRIORITY) && $time == 0 && $days == 0){
			$getExpireDatePriority = $db->getExpireDatePriority($COD_PRIORITY);
			if(!$getExpireDatePriority){
				$db->RollbackTrans();
				return false;
			}
			$days = $getExpireDatePriority->fields['limit_days']; //NUM DE DIAS DO PRAZO
			$time = $getExpireDatePriority->fields['limit_hours']; //QUANTIDADE DE TEMPO
			
			if($days > 0){
				$days_sum = "+".$days." days";
			}			
			if($time > 0){
				$time_sum = "+".$time." hour";
			}
		}
		
		//SE O TEMPO E O DIA CONTINUAREM 0 MESMO DEPOIS DE BUSCAR NO SERVIÇO E NA PRIORIDADE DEFINIDO 1 COMO PADRÃO
		if($time == 0 && $days == 0){
			$days_sum = "+0 day";
			$time_sum = "+0 hour";
			return $START_DATE;
		}
		
		//SOMA O TEMPO DE ATENDIMENTO DETERMINADO PELO SERVIÇO OU PRIORIDADE OU PADRÃO
		$data_sum = date("Y-m-d H:i:s",strtotime($START_DATE." ".$days_sum." ".$time_sum));

		$date_holy_start = date("Y-m-d",strtotime($START_DATE)); //SEPARA SOMENTE A DATA INICIAL PARA VERIFICAR SE HÁ FERIADO NO PERÍODO
		$date_holy_end = date("Y-m-d",strtotime($data_sum)); //SEPARA SOMENTE A DATA FINAL PARA VERIFICAR SE HÁ FERIADO NO PERÍODO		
				
		$getNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy_start,$date_holy_end); //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
		if(!$getNationalDaysHoliday){
			$db->RollbackTrans();
			return false;
		}

		if(isset($idcompany)){
			$getCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy_start,$date_holy_end,$idcompany); //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
			if(!$getCompanyDaysHoliday){
				$db->RollbackTrans();
				return false;
			}			
			$sum_days_holidays = $getNationalDaysHoliday->fields['num_holiday'] + $getCompanyDaysHoliday->fields['num_holiday'];			
		}else{
			$sum_days_holidays = $getNationalDaysHoliday->fields['num_holiday'];
		}		
				
		//PRAZO COM O ACRÉSCIMO DE FERIADOS
		$data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$sum_days_holidays." days"));

		//GERA O ARRAY DE DIAS ÚTIS DA EMPRESA
		$getBusinessDays = $db->getBusinessDays();
		if(!$getBusinessDays){
			$db->RollbackTrans();
			return false;
		}		
        while (!$getBusinessDays->EOF) {
            $businessDay[$getBusinessDays->fields['num_day_week']] = array(
																		"begin_morning" 	=> $getBusinessDays->fields['begin_morning'],
																		"end_morning" 		=> $getBusinessDays->fields['end_morning'],
																		"begin_afternoon" 	=> $getBusinessDays->fields['begin_afternoon'],
																		"end_afternoon" 	=> $getBusinessDays->fields['end_afternoon']
																	);
            $getBusinessDays->MoveNext();
        }		
		
		$date_check_start = date("Y-m-d",strtotime($START_DATE));
		$date_check_end = date("Y-m-d",strtotime($data_sum));
		$addNotBussinesDay = 0;
		//PEGA A QUANDIDADE DE DIAS NÃO UTEIS
		while (strtotime($date_check_start) <= strtotime($date_check_end)) {			
			$numWeek = date('w',strtotime($date_check_start));
			if (!array_key_exists($numWeek, $businessDay)) {
			    $addNotBussinesDay++;
			}
			$date_check_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_check_start)));
		}
		$data_sum = date("Y-m-d H:i:s",strtotime($data_sum." +".$addNotBussinesDay." days")); //PRAZO SOMADO COM OS DIAS NÃO UTEIS
		$data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany); //VALIDA SE O DIA É UM DIA ÚTIL E NAO É FERIADO
		$data_sum = $this->checkValidBusinessHour($data_check_bd,$businessDay); //VALIDA SE A HORA ESTÁ NO INTERVALO DE ATNDIMENTO
		//CASO MUDE O DIA COM O ACRÉSCIMO DA HORA SERÁ CHECADO NOVAMENTE SE O DIA É VALIDO
		if(strtotime(date("Y-m-d",strtotime($data_check_bd))) != strtotime(date("Y-m-d",strtotime($data_sum)))){
			$data_check_bd = $this->checkValidBusinessDay($data_sum,$businessDay,$idcompany);
			return $data_check_bd;
		}else{
			return $data_sum;
		}
				
	}
	
	private function checkValidBusinessDay($date,$businessDay,$idcompany = null){
		$db = new expiredate_model();
		$numWeek = date('w',strtotime($date));
		$i = 0;
		while($i == 0){			
			while (!array_key_exists($numWeek, $businessDay)) {
				$date = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));
				$numWeek = date('w',strtotime($date));
			}		
			$date_holy = date("Y-m-d",strtotime($date));
			
			$getNationalDaysHoliday = $db->getNationalDaysHoliday($date_holy,$date_holy); //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
			if(!$getNationalDaysHoliday){
				$db->RollbackTrans();
				return false;
			}
			
			if(isset($idcompany)){
				$getCompanyDaysHoliday = $db->getCompanyDaysHoliday($date_holy,$date_holy,$idcompany); //VERIFICA A QUANTIDADE DE FERIADOS NO PERÍODO
				if(!$getCompanyDaysHoliday){
					$db->RollbackTrans();
					return false;
				}			
				$daysHoly = $getNationalDaysHoliday->fields['num_holiday'] + $getCompanyDaysHoliday->fields['num_holiday'];			
			}else{
				$daysHoly = $getNationalDaysHoliday->fields['num_holiday'];
			}
			
			if($daysHoly > 0){
				//die("aaa");
				$date = date("Y-m-d H:i:s",strtotime($date." +".$daysHoly." days"));
				$numWeek = date('w',strtotime($date));
			}else{
				$i = 1;
			}		
		}
		return $date;
	}
	
	private function checkValidBusinessHour($date,$businessDay){
		$i = 0;
		while($i == 0){
			$numWeek = date('w',strtotime($date));
			$hour = strtotime(date('H:i:s',strtotime($date)));
			$begin_morning = strtotime($businessDay[$numWeek]['begin_morning']);
			$end_morning = strtotime($businessDay[$numWeek]['end_morning']);
			$begin_afternoon = strtotime($businessDay[$numWeek]['begin_afternoon']);
			$end_afternoon = strtotime($businessDay[$numWeek]['end_afternoon']);			
			if($hour >= $begin_morning && $hour <= $end_morning){
				$i = 1;
			}
			else if($hour >= $begin_afternoon && $hour <= $end_afternoon){
				$i = 1;
			}
			else{
				$date = date ("Y-m-d H:i:s", strtotime("+1 hour", strtotime($date)));
				$i = 0;
			}
		}		
		return $date;
	}
	
	private function createRequestCode(){
		$db = new requestinsert_model();
		$db->BeginTrans();
        //GET THE COD_REQUEST AND COD_MONTH THE CURRENT MONTH, IF THERE TURNS BLANK A NEW COUNT WILL BE SET FORTH
        $rsCode = $db->getCode();
		if(!$rsCode){
			$db->RollbackTrans();
			return false;
		}
		//COUNT MONTH CODE
        $rsCountCode = $db->countGetCode();
		if(!$rsCountCode){
			$db->RollbackTrans();
			return false;
		}		
		//IF HAVE CODE REQUEST
        if ($rsCountCode->fields['total']) {
            $COD_REQUEST = $rsCode->fields["cod_request"];
			//WILL INCREASE THE CODE OF REQUEST
            $rsIncressCode = $db->increaseCode($COD_REQUEST);
			if(!$rsIncressCode){
				$db->RollbackTrans();
				return false;
			}
        } 
        else { //IF NOT HAVE CODE REQUEST WILL CREATE A NEW 
            $COD_REQUEST = 1;
            $rsCreateCode = $db->createCode($COD_REQUEST);
			if(!$rsCreateCode){
				$db->RollbackTrans();
				return false;
			}
        }
        //CREATES THE FINAL CODE
        while (strlen($COD_REQUEST) < 6) {
            $COD_REQUEST = "0" . $COD_REQUEST;
        }
        $COD_REQUEST = date("Ym") . $COD_REQUEST;		
		$db->CommitTrans();
		return $COD_REQUEST;
	}
	
	public function modalReturnRequest(){
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
        $smarty->display('modais/newrequestcreated.tpl.html');
    }

	public function groupList() {
        $db = new operatorview_model();
		if($_POST['filter'])
			$where = "AND name LIKE '%".$_POST['filter']."%' ";
		else {
			$where = null;
		}	
        $repgroups = $db->getRepassGroups($where);
        while (!$repgroups->EOF) {        	
			$groups[] = array(
								"level" => $repgroups->fields['level'],
								"id" 	=> $repgroups->fields['idperson'],
								"name"	=> $repgroups->fields['name']
								);
            $repgroups->MoveNext();
        }
        echo json_encode($groups);
    }
	
	public function operatorList() {
        $bd = new operatorview_model();
        if($_POST['filter'])
			$where = "AND name LIKE '%".$_POST['filter']."%' ";
		else {
			$where = null;
		}	
        $ret = $bd->getRepassOperators($where);
        while (!$ret->EOF) {
			$operators[] = array(
							"id" 	=> $ret->fields['idperson'],
							"name"	=> $ret->fields['name']
							);
            $ret->MoveNext();
        }
        echo json_encode($operators);
    }
	
	public function abilitiesList() {		
		extract($_POST);
        $db = new operatorview_model();
        if ($type == 'group') {
            $ret = $db->getAbilityGroup($rep);
            if ($ret->fields) {
                while (!$ret->EOF) {
                    $abilities[] = array("service" => $ret->fields['service']);
                    $ret->MoveNext();
                }
            } 
        }
		elseif ($type == 'operator') {
            $ret = $db->getAbilityOperator($rep);
            if ($ret->fields) {
                while (!$ret->EOF) {
                	$abilities[] = array("service" => $ret->fields['service']);
                    $ret->MoveNext();
                }
            }
        }		
		echo json_encode($abilities);
    }

	public function groupsList() {       
        extract($_POST);		
        $db = new operatorview_model();
        if ($type == 'group') {
            $ret = $db->getGroupOperators($rep);
            
            if ($ret->fields) {
                while (!$ret->EOF) {
                	$groups[] = array("name" => $ret->fields['name']);
                    $ret->MoveNext();
                }
            } 
        } elseif ($type == 'operator') {
            $ret = $db->getOperatorGroups($rep);
            if ($ret->fields) {
                while (!$ret->EOF) {
                	$groups[] = array("name" => $ret->fields['pername']);
                    $ret->MoveNext();
                }                    
            }
        }        
		echo json_encode($groups);
    }

}
