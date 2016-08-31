<?php
session_start();

class requestInsert extends Controllers {

    public function index() {
        //error_reporting(E_ALL);
        session_start();
        $this->validasessao();
        //estanciando o a conexao com o model para as execucoes dessa funcao
        $db = new requestinsert_model();
        $usertype = $_SESSION['SES_TYPE_PERSON'];
        
        //definicoes do smarty 
        $smarty = $this->retornaSmarty();
        //verificamos o tipo de usuario para definir no select da origem qual o que deve vir marcado
        //se a var de sessao type user é igual a 2(user)
        //$smarty->assign('source_default',1);

        $_SESSION['SES_COD_ATTACHMENT'] = "";

        //setamos a variavel com o nome da pessoa logada como autor da solicitacao
        $smarty->assign('person', $_SESSION['SES_NAME_PERSON']);

        //buscamos a area default para o grupo de atendimento desta pessoa
        //obs. por enquanto somente a dafault do banco, depois faremos as regras para filtrar os detalhes
        $smarty->assign('area_default', 1);

        if ($_SESSION['SES_IND_TIMER_OPENING'] == 1) {
            $smarty->assign('timer', 1);
        } else {
            $smarty->assign('timer', 0);
        }

        $select = $db->selectSource();
        while (!$select->EOF) {
            $campos[] = $select->fields['idsource'];
            $valores[] = "--";
            $select->MoveNext();
        }
        $smarty->assign('sourceids', $campos);
        $smarty->assign('sourcevals', $valores);
        $select2 = $db->selectArea();
        while (!$select2->EOF) {
            $campos2[] = $select2->fields['idarea'];
            $valores2[] = $select2->fields['name'];
            $select2->MoveNext();
        }
        $smarty->assign('areaids', $campos2);
        $smarty->assign('areavals', $valores2);
        $select3 = $db->selectWay();
        while (!$select3->EOF) {
            $campos3[] = $select3->fields['idattendanceway'];
            $valores3[] = $select3->fields['way'];
            $select3->MoveNext();
        }
        
        
        $smarty->assign('wayids', $campos3);
        $smarty->assign('wayvals', $valores3);
        $smarty->assign('waydefault', 1);
        //setamos o id do usuario
        $smarty->assign('SES_COD_USUARIO', $_SESSION['SES_COD_USUARIO']);
        //setamos o id da empresa a qual o usuario logado faz parte
        $smarty->assign('SES_COD_JURIDICAL', $_SESSION['SES_COD_EMPRESA']);
        $smarty->display('operator.tpl.html');
    }

    //fim definicoes smarty
    //funcao para carregar os tipos
    public function type() {
        $area = $_POST['area'];
        $db = new requestinsert_model();
        $sel = $db->selectType($area);
        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value='0'>------</option>";
            exit();
        } else {
            $i = 0;
            while (!$sel->EOF) {
                $campos[] = $sel->fields['idtype'];
                $valores[] = $sel->fields['name'];
                $selected[] = $sel->fields['selected'];
                if ($selected[$i] == '1') {
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

    //funcao para carregar os itens
    public function item() {
        $type = $_POST['type'];
        $db = new requestinsert_model();
        $sel = $db->selectItem($type);
        $count = $sel->RecordCount();
        if ($count == 0) {
            echo "<option value='0'>Não há items para esse tipo!</option>";
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
            echo "<option value='0'>-------</option>";
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
            echo "<option value='0'>----------</option>";
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

    public function saverequest() {
        session_start();
        $this->validasessao();
        $_SESSION["SES_COD_ATTACHMENT"] = "";

        if (isset($_POST["idperson"])) {
            $MIN_TEMPO_TELEFONE = number_format($_POST["open_time"], "2", ".", ",");
            $TEMPO_ATENDIMENTO = (int) $_POST["open_time"];

			/**
			 ** If it is set to the request code be formatted as MOUNTH and YEAR, creates the code at this moment.
			 **/
            if ($_SESSION["SES_IND_CODIGO_ANOMES"]) {
                //GERANDO O CÓDIGO DA SOLICITACAO
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
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
                //Montando o Código Final
                while (strlen($COD_SOLICITACAO) < 6) {
                    $COD_SOLICITACAO = "0" . $COD_SOLICITACAO;
                }
                $COD_SOLICITACAO = date("Ym") . $COD_SOLICITACAO;
                $CAMPO_COD_SOLICITACAO = "code_request,";
            }

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


            //$NUM_OS			= $_POST["NUM_OS"];
            if (!$NUM_OS) {
                $NUM_OS = 0;
            }

            
			/**
			 ** When it is user, the default source is already HelpDezk, unless it be at the chat.
			 **/
            if (isset($_POST["source"])) {
                $COD_ORIGEM = $_POST["source"];
            } else {
                if (isset($_POST['chatid']) && $_POST['chatid'] != 0) {
                    $COD_ORIGEM = 100; //coloca como chat
                } else {
                    $COD_ORIGEM = 1;
                }
            }
			
			/**
			 ** If the source is the phone and NOT the user, it counts service time by the phone
			 **/
            if ($COD_ORIGEM == 2 && $_SESSION["SES_COD_TIPO"] != 1) {
                $MIN_TEMPO_TELEFONE = $MIN_TEMPO_TELEFONE;
                $MIN_TEMPO_CONSUMIDO = $TEMPO_ATENDIMENTO;
            } else {
                $MIN_TEMPO_TELEFONE = 0;
                $MIN_TEMPO_CONSUMIDO = 0;
            }
            
			/**
			 ** When it is user it is not informed the status
			 **/
            if (isset($_POST["status"])) {
                $COD_STATUS = $_POST["status"];
            } else {
                $COD_STATUS = 1;
            }
            $NOM_ANALISTA_AUTOR = "";
            $COD_ANALISTA_AUTOR = "NULL";
            if ($COD_USUARIO != $_SESSION["SES_COD_USUARIO"]) {
                $NOM_ANALISTA_AUTOR = $_SESSION["SES_NOM_USUARIO"];
                $COD_ANALISTA_AUTOR = $_SESSION["SES_COD_USUARIO"];
            }

            // pode ter sido passado um código de atendente reponsavel, nesse casso
            if (isset($COD_USUARIO_ANALISTA)) {

                $rs = $db->getAnalyst($COD_USUARIO_ANALISTA);


                if (!$rs->EOF) {
                    $COD_ANALISTA_AUTOR = $COD_USUARIO_ANALISTA;
                    $NOM_ANALISTA_AUTOR = $rs->fields["name"];
                }
            }

            
			/**
			 ** Check if the user is VIP
			 **/
            $rsUsuarioVip = $db->checksVipUser($COD_USUARIO);
            
			/**
			 ** Check if there is any priority marked as VIP
			 **/
            $rsPrioridadeVip = $db->checksVipPriority();
            
			/**
			 ** If the user is VIP and has priority marked for VIP, take this one
			 **/
            if ($rsUsuarioVip->fields['rec_count'] == 1 && $rsPrioridadeVip->fields['rec_count'] == 1) {
                $COD_PRIORIDADE = $rsPrioridadeVip->fields["idpriority"];
            } else {
				/**
				 ** Search priority in the service
				 **/
                $rsService = $db->getServPriority($COD_SERVICO);
                $COD_PRIORIDADE = $rsService->fields['idpriority'];
				/**
				 ** If there is no priority in the service, uses default priority.
				 **/
                if (!$COD_PRIORIDADE) {
					/**
					 ** Check the default priority when opening requests
					 **/
                    $rsPrioridade = $db->getDefaultPriority();
                    $COD_PRIORIDADE = $rsPrioridade->fields["idpriority"];
                }
            }
			/**
			 ** Sending the service code, the time must be calculated based on it.
			 ** If an opening date is informed, we will used this one to calculated the term.
			 **/
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

			/**
			 ** If the status is 4,5, it is ending the request (telephone service)
			 **/
            if ($COD_STATUS == "4" || $COD_STATUS == "5") {
				/**
				 ** If the source it is not by phone, register the times.
				 **/
                if ($COD_ORIGEM != 2) {
                    $MIN_TEMPO_ABERTURA = $TEMPO_ATENDIMENTO;
                    $MIN_TEMPO_ENCERRAMENTO = $TEMPO_ATENDIMENTO;
                    $MIN_TEMPO_ATENDIMENTO = $TEMPO_ATENDIMENTO;
                } else {
					/**
					 ** If it is by phone, do not register this times. The register time will be at the phone at MIN_TIME_PHONE.
					 **/
                    $MIN_TEMPO_ABERTURA = 0;
                    $MIN_TEMPO_ENCERRAMENTO = 0;
                    $MIN_TEMPO_ATENDIMENTO = 0;
                }

                $rs = $db->insertRequestTimes($COD_SOLICITCAO, $MIN_TEMPO_ABERTURA, $MIN_TEMPO_ENCERRAMENTO, $MIN_TEMPO_ATENDIMENTO);
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
                $rs = $db->insertRequestDates($COD_SOLICITCAO, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
            }//fim status encerrada

            if ($_POST["reason"] > 0) {
                $COD_MOTIVO = $_POST["reason"];
            } else {
                $COD_MOTIVO = NULL;
            }
            $AUX = split("/", $DAT_CADASTRO);
            $DAT_CADASTRO = $AUX[2] . $AUX[1] . $AUX[0];
            $AUX = split(":", $HOR_CADASTRO);
            $DAT_CADASTRO .= $AUX[0] . $AUX[1] . "00";

            if (isset($_POST['chatid'])) {
                $chatid = $_POST['chatid'];
            } else {
                $chatid = 0;
            }


            $db = new requestinsert_model();

            //$db->StartTrans(); 

            $rs = $db->insertRequest($COD_ANALISTA_AUTOR, $COD_ORIGEM, $DAT_CADASTRO, $COD_TIPO, $COD_ITEM, $COD_SERVICO, $COD_MOTIVO, $COD_TIPO_ATENDIMENTO, $NOM_ASSUNTO, $DES_SOLICITACAO, $NUM_OS, $COD_PRIORIDADE, $NUM_ETIQUETA, $NUM_SERIE, $COD_EMPRESA, $DAT_VENCIMENTO_ATENDIMENTO, $COD_USUARIO, $CAMPO_COD_SOLICITACAO, $COD_SOLICITACAO . ',');
            if (!$rs) {
                die('Error cod.:' . __LINE__);
            }
            ###############
            ///Vamos  descobrir o código auto increment da solicitação
            /// se não estivermos usando o formato ANOMES. Caso em que já teremos o $COD_SOLICITACAO AQUI
            if (!isset($COD_SOLICITACAO)) {
                $rs = $db->lastCode();
                $COD_SOLICITACAO = $rs->fields["code_request"];
            }
            if (!$_SESSION["SES_IND_CODIGO_ANOMES"]) {
                $rs = $db->lastCode();
                $COD_SOLICITACAO = $rs->fields["code_request"];
            }

            $rs = $db->insertRequestLog($COD_SOLICITACAO, date("Y-m-d H:i:s"), $COD_STATUS, $COD_USUARIO);
            if (!$rs) {
                die('Error cod.:' . __LINE__);
            }
			/**
			 ** If the request is already close when opening it, 
			 ** add the group and the attendence as responsable.
			 **/
            if ($COD_STATUS == "4" || $COD_STATUS == "5") { //encerrada ou aguardando aprovação
				/**
				 ** Consulting for search group if the user
				 **/
                $rsGrupo = $db->getGroupUser($_SESSION['SES_COD_USUARIO']);

                $COD_GRUPO = "0";

                if (!$rsGrupo->EOF) {
                    //Inserindo um registro para o Grupo
                    $COD_GRUPO = $rsGrupo->fields["idgroup"];
                    //Incluindo a relação da Solicitação com o Grupo do atendente
                    $rs = $db->insertRequestGroup($COD_SOLICITACAO, $COD_GRUPO);
                    if (!$rs) {
                        die('Error cod.:' . __LINE__);
                    }
                }
				/**
				 ** Updating the table of Requests to inform of wich group belongs the request.
				 **/
                $rs = $db->updateRequestGroup($COD_GRUPO, $COD_SOLICITACAO);
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
                //INcluíndo a Analista como atentende principal
                $rs = $db->insertRequestCharge($COD_SOLICITACAO, $COD_GRUPO, 'G');
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
            }
			/**
			 ** If it is select to route to a group.
			 **/
            if (isset($_POST["IND_REPASSAR"]) && $_POST["IND_REPASSAR"] == "grupo") {
				/**
				 ** If it depends on approval, it will be passed forward to the responsible.
				 **/
                $responsabilidade = (isset($projetokilling) || isset($APROVADOR)) ? 0 : 1;
				/**
				 ** Including the Request lists with Group to passed
				 **/ 
                $rs = $db->insertRequestCharge($COD_SOLICITACAO, $_POST["COD_GRUPO_REPASSE"], 'G');
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
				/* --- */
                acompanhar(); //verifica se deve continuar acompanhando
				/* --- */
            }
            
			/**
			 ** If it was selected to route for an Attendance
			 **/
            elseif (isset($_POST["IND_REPASSAR"]) && $_POST["IND_REPASSAR"] == "analista") {
				/**
				 ** If it depends on approval, it will be passed forward to the responsible
				 **/
                $responsabilidade = (isset($projetokilling) || isset($APROVADOR)) ? 0 : 1;
				/**
				 ** Including the listing of requests with the analyst transfer
				 **/
                $rs = $db->insertRequestCharge($COD_SOLICITACAO, $_POST["COD_ANALISTA_REPASSE"], 'O');
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
				/**
				 ** Consults the group that the analyst belongs to update at the request
				 **/
                $rsGrupoResp = $db->checkGroup($_POST["COD_ANALISTA_REPASSE"]);
				/**
				 ** If the attendance has not a group, uses the first level group.
				 **/
                $rsGrupo = $db->getGroupFirstLevel();
                $grupoDePrimeiroNivel = $rsGrupo->fields['idgroup'];

                if ($rsGrupoResp->EOF) {
					/**
					 ** I changed the “1” original from here to the result of the query above, 
					 ** because the group of the first level wiil not always be code 1
					 **/
                    $COD_GRUPO = $grupoDePrimeiroNivel; 
                } else {
                    $COD_GRUPO = $rsGrupoResp->fields['idgroup'];
                }
				/**
				 ** Updating the table of Requests to inform of wich group belongs the request
				 **/
                $rs = $db->updateRequestGroup($COD_GRUPO, $COD_SOLICITACAO);
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
                $rs = $db->insertRequestCharge($COD_SOLICITACAO, $COD_GRUPO, 'G');
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
			
                acompanhar(); 

            } else {
                if ($COD_STATUS != "5") {
                    $IND_TRIAGEM = "1";
					/**
					 ** If it is working at automatic screening.
					 **/
                    //Se estiver trabalhando no modo triagem automática
                    if ($_SESSION["SES_IND_TRIAGEM"] == "1") {

                        $codGrupoSol = $db->getGrupoResponsavel($COD_SERVICO);
						/**
						 ** Request responsibility
						 ** If the call has already been closed when opening it, we define the attendence as the responsable..
						 ** In this case, we set the group of attendance of the item only as a group with viewing possibility,
						 ** but not as responsible for the call However,
						 ** When it is a Killing project, or item/service needs approval, do not link
						 ** with a group here, because it will be linked with the responsable attendence for the approval.
						 **/	
                        $responsabilidade = ($COD_STATUS == 4) ? 0 : 1;
						/**
						 ** We will record also a flag 1 in the column ORIG_RESP indicating to,
						 ** if exists, when the approvals ended identify the original responsible, as it is a call can
						 ** be passed to somebody when it is open and if need
						 ** approval we will need to know for who it was passed originally
						 ** @since 2008-09-30
						 **/


						/**
						 ** Included the request listing with Attendance Group
						 **/
                        $rs = $db->insertRequestGroupResp($COD_SOLICITACAO, $codGrupoSol, $responsabilidade, '1');
                        if (!$rs) {
                            die('Error cod.:' . __LINE__);
                        }
                        $IND_TRIAGEM = "0";

						/**
						 ** Updating the table of Requests to inform of wich group belongs the request.
						 **/
                        $rs = $db->updateRequestGroup($codGrupoSol, $COD_SOLICITACAO);
                        if (!$rs) {
                            die('Error cod.:' . __LINE__);
                        }
                        $rs = $db->insertRequestCharge($COD_SOLICITACAO, $codGrupoSol, 'G');
                        if (!$rs) {
                            die('Error cod.:' . __LINE__);
                        }
                    }

					/**
					 ** Included the request for the group ai level 1 when it is not automativ screenign
					 **/	
                    if ($_SESSION["SES_IND_TRIAGEM"] == "0" || $IND_TRIAGEM == "1") {
						/**
						 ** Responsibility. The case here is the same shown above when it is doing the screening
						 ** adding the item group. Here, the group will be visualized will be the first level.
						 **/
                        $responsabilidade = ($COD_STATUS == 4 ) ? 0 : 1;

						/**
						 ** Selecting the code of the first group (level 1).
						 **/
                        $rsGrupo = $db->getGroupFirstLevel();
						
						/**
						 ** Including the relation of the Request with the Group of Attendance.
						 **/
                        $rs = $db->insertRequestGroupResp($COD_SOLICITACAO, $rsGrupo->fields["idgroup"], $responsabilidade, '1');
                        if (!$rs) {
                            die('Error cod.:' . __LINE__);
                        }
                        $rs = $db->insertRequestCharge($COD_SOLICITACAO, $rsGrupo->fields["idgroup"], 'G');
                        if (!$rs) {
                            die('Error cod.:' . __LINE__);
                        }

						/**
						 ** Updating the Request table to inform which group belongs the request
						 **/
                        $rs = $db->updateRequestGroup($rsGrupo->fields["idgroup"], $COD_SOLICITACAO);
                        if (!$rs) {
                            die('Error cod.:' . __LINE__);
                        }
                    }
					// End of the verification of the automatic screening
                }
            }

            /*             * *********** APROVAÇÕES ******************* */
            if (isset($APROVADOR)) {
                // É uma aprovação, mas não do tipo projetos e sim baseada na comnição item/serviço
                atualiza_responsavel($COD_SOLICITACAO, $APROVADOR, 'atd');
                // inclui o relacionamento da aprovação com a regra
                $regrasAprovacao = get_regras_aprovacao($COD_ITEM, $COD_SERVICO);
                $iOrdem = 1; //ordem em que deve-se dar a aprovacao
                $flRecalc = get_flRecalcular_prazo($COD_ITEM, $COD_SERVICO) ? 1 : 0; //recalcular tempo atenimento ao concluir aprovações?

                foreach ($regrasAprovacao as $codRegra => $codAprovador) {
                    $values[] = "($codRegra, $COD_SOLICITACAO, $iOrdem, $codAprovador, $flRecalc)";
                    $iOrdem++;
                }


                $xon = $db->insertApproval($values);
                if (!$con) {
                    die('Error cod.:' . __LINE__);
                }
            }

            /*             * *********** FIM APROVAÇÕES ******************* */

            //Controlando os anexos.
            //Verifica se existe anexos.
            if ($_POST["COD_ANEXO"] != '') {
                /**
                 * Removido o select max() abaxaixo (ate porque ja temos o cod)
                 * para evitar que os anexos sejam relacionados a solicitacao errada
                 * e alinha do update mais abaixo
                 * @since 20090729
                 */
                //Faz a consulta para buscar o código da solicitação.			
                //$SQL = "SELECT max(COD_SOLICITACAO) as COD FROM hdk_solicitacao";
                //$rsMax = $conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());

                $COD_ANEXO = split(",", $_POST["COD_ANEXO"]);
                for ($i = 0; $i < count($COD_ANEXO); $i++) {
                    //Incluíndo o código da solicitação  nos anexos.                                
                    $Result1 = $db->updateRequestAttach($COD_SOLICITACAO, $COD_ANEXO[$i]);
                    if (!$Result1) {
                        die('Error cod.:' . __LINE__);
                    }
                }
            }

            //Inserindo Apontamento se A solicitação for feita por Administrador 
            //Se tiver apontamento sempre registrar
            //Se for origem telefone tb sempre registra
            if ((isset($_POST["DES_APONTAMENTO"]) && strlen($_POST["DES_APONTAMENTO"]) > 2) || $COD_ORIGEM == 2) {

                //Se for aberta como nova
                // ########################### CONFIGURAÇÕES ESPECÍFICAS DO CLIENTE DATADROME ###########################
                // Mudando o status de 10 para 3 da solicitação para o cliente Datadrome. Pediram para os chamados repassados sejam assumidos automaticamente pelos atendentes.///
                if ($COD_STATUS == 1 || $COD_STATUS == 10) {
                    $COD_TIPO_APONTAMENTO = 3;
                }
                //se for aberta como encerrada
                else if ($COD_STATUS == 4 || $COD_STATUS == 5) {
                    $COD_TIPO_APONTAMENTO = 1;
                }
                //Se a origam for Telefone
                if ($COD_ORIGEM == 2 && strlen($_POST["DES_APONTAMENTO"]) < 2) {
                    $DES_APONTAMENTO = "Atendimento por Telefone ";
                } elseif ($COD_ORIGEM == 2 && strlen($_POST["DES_APONTAMENTO"]) > 2) {
                    $DES_APONTAMENTO = "Atendimento por Telefone: " . $_POST["DES_APONTAMENTO"];
                } else {
                    $DES_APONTAMENTO = $_POST["DES_APONTAMENTO"];
                }
                if ($COD_ORIGEM == 2) {
                    $MIN_TEMPO_ABERTURA = $MIN_TEMPO_TELEFONE;
                } else {
                    $MIN_TEMPO_ABERTURA = $TEMPO_ATENDIMENTO;
                }
                $HOR_INICIAL = mktime(date("H"), date("i") - $MIN_TEMPO_ABERTURA, date("s"), date("m"), date("d"), date("Y"));
                $HOR_INICIAL = strftime("%H", $HOR_INICIAL) . ":" . strftime("%M", $HOR_INICIAL);

                if ($HOR_INICIAL == ":")
                    $HOR_INICIAL = "";
                $HOR_FINAL = date("H:i");
####################
                //Se já está encerrando o chamado (cod_tipo_apontamento == 1)
                //precisamos informar o tipo de hora, se extra ou normal, que não estava sendo informado, pois,
                //sem ele não podemos fazer relatório de custos por tempo consumido	


                if ($COD_TIPO_APONTAMENTO == 1) {
                    $tipo_hora = (hora_extra_hd(date('w'), date("H:i") . ':00')) ? 2 : 1;
                }
####################
                $data = date("Y-m-d H:i:s");
                $SQL = "INSERT INTO hdk_apontamento 
				   (COD_SOLICITACAO
				   ,COD_USUARIO
				   ,DES_APONTAMENTO
				   ,DAT_CADASTRO
				   ,COD_TIPO
				   ,NUM_MINUTO
				   ,HOR_INICIAL
				   ,HOR_FINAL
				   ,DAT_EXECUCAO
				   ,NUM_IP_ACESSO
				   ,TIP_HORA)
					VALUES (
					" . $COD_SOLICITACAO . "
					," . $_SESSION["SES_COD_USUARIO"] . "
					,'$DES_APONTAMENTO'
					,to_date($data,'YYYYmmddHH24MISS')
					,$COD_TIPO_APONTAMENTO
					,$MIN_TEMPO_ABERTURA
					,'$HOR_INICIAL'
					,'$HOR_FINAL'
					,to_date($data,'YYYYmmddHH24MISS')
					,'" . $_SERVER['REMOTE_ADDR'] . "'";

                if ($COD_TIPO_APONTAMENTO == 1) {
                    $tipohora = $tipo_hora; //informa o tipo de hora se já está encerrando
                } else {
                    $tipohora = null; //deixa como estava
                }
                $con = $db->insertNote($COD_SOLICITACAO, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, $data, $COD_TIPO_APONTAMENTO, $MIN_TEMPO_ABERTURA, $HOR_INICIAL, $HOR_INICIAL, $data, $_SERVER['REMOTE_ADDR'], $tipohora);
                if (!$con) {
                    die('Error cod.:' . __LINE__);
                }
            }

            //Se for acesso pelo usuario Inserir Apontamento indicando o IP USADO
            else {
                $data = date("Y-m-d H:i:s");
                $DES_APONTAMENTO = "Abertura de Solicitação";
                $con = $db->insertNote($COD_SOLICITACAO, $_SESSION["SES_COD_USUARIO"], $DES_APONTAMENTO, $data, '3', 'null', 'null', 'null', 'null', $_SERVER['REMOTE_ADDR'], 'null');
                if (!$con) {
                    die('Error cod.:' . __LINE__);
                }
            }

            ###########coment email
            #
        #	if($COD_STATUS==10){
            #
        #	$COD_EMAIL="repassar";
            #
	#	require_once("../email/email.php");
            #	} else{
            #	if($COD_STATUS==5){
            #		$COD_EMAIL="encerrar";
            #		require_once("../email/email.php");
            #	} 
            #	else {
            #		//Manda email para os Atendentes de uma nova solicitação
            #		 */
            #			$COD_EMAIL = 'registrar_aprovacao';				
            #		} elseif (isset($APROVADOR)) { //é aprovacao de uma solicitação normal, faremos um template a parte...
            #			$COD_EMAIL = 'registrar_aprovacao_solicitacao';				
            #		}else { 
            #			//Fica com o código default
            #			$COD_EMAIL="registrar";
            #		}	
            #		require_once("../email/email.php");
            #	}
            #}
            //Zerando a variável que armazena os anexos.
            $_SESSION["SES_COD_ATTACHMENT"] = "";

            if (isset($_POST['chatid']) and $_POST['chatid'] != 0) {
                /* Deivisson 10/05/11
                  //Escreve a msg de que o chamado foi aberto nas janelas de chat do usuário e do atendente.
                  $goto = ('usuario/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
                  $str  = 'solicitacao:'.$url_helpdesk.$goto;
                  $msg  = char_to_html($str);
                  if ($_SESSION['SES_COD_USUARIO']) {
                  $sql  = 'INSERT INTO hcl_chat(chatid, operatorid, timestamp, message, x, operator) VALUES';
                  $sql .= '('.$_POST['chatid'].', '.$_SESSION['SES_COD_USUARIO'].', unix_timestamp(), "'.$msg.'", "o", "1")';
                  } else {
                  $sql  = 'INSERT INTO hcl_chat(chatid, operatorid, timestamp, message, x, guest) VALUES';
                  $sql .= '('.$_POST['chatid'].', '.$_SESSION['SES_COD_USUARIO'].', unix_timestamp(), "'.$msg.'", "g", "1")';
                  }
                  $sql_result = $conexao->Execute($sql);
                  if (!$sql_result) {
                  //die($sql.' <br>'.$conexao->ErrorMsg());
                  }
                  //Inclui na sessão o código da solicitação aberta, para que ao final do chat se faça a cópia do log
                  $_SESSION['hcl_'.$_POST['chatid']]['solicitacoes'][] = $COD_SOLICITACAO;
                 */

                //  PARTE DO CHAT SERA UM MODULO PAGO, NAO FOI FEITO AGORA, DEVERA SER ADAPTADO PARA MVC E CRIADO OS MODELS E CONTROLERS RESPECTIVOS
                /*
                  if(isset($_POST['apont'])){
                  //insere apontamento com a conversa do chat
                  $SQL_CHAT="select concat(tname,': ') as name, tmessage from chatmessage where threadid=$chatid and ikind in (1,2)";

                  $rschat=$conexao->Execute($SQL_CHAT) or die("<b>$SQL_CHAT</b><br>".$conexao->ErrorMsg()."<br>".__LINE__);

                  $DES_APONTAMENTO ="<b>Atendimento por Chat</b><br>";

                  while(!$rschat->EOF){
                  $DES_APONTAMENTO .=$rschat->Fields('name').$rschat->Fields('tmessage').".<br>";
                  $rschat->MoveNext();
                  }
                  $SQL = "INSERT INTO hdk_apontamento
                  (COD_SOLICITACAO
                  ,COD_USUARIO
                  ,DES_APONTAMENTO
                  ,DAT_CADASTRO
                  ,COD_TIPO
                  ,NUM_IP_ACESSO)
                  VALUES (
                  ".$COD_SOLICITACAO."
                  ,".$_SESSION["SES_COD_USUARIO"]."
                  ,'$DES_APONTAMENTO'
                  ,".date("YmdHi00")."
                  ,3
                  ,'".$_SERVER['REMOTE_ADDR']."')";
                  $conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg()."<br>".__LINE__);
                  } */
            }
            ?>


            <script>
                //window.opener.location.reload();
                alert('<?php echo $COD_SOLICITACAO ?>');
                //window.location = 'solicita_nova_gerada.php?COD_SOLICITACAO=<?php echo $COD_SOLICITACAO ?>&chatid=<?php echo $chatid; ?>&COD_USUARIO=<?php echo $COD_USUARIO; ?>';		
            </script>
            <?php
        }//fim do cadastro
    }

    #######################################################################################################################################################
    ############################################################################ HELPERS ##################################################################
    #######################################################################################################################################################

    public function getDiasUteis() {

        // Armazena o horário de trabalho para cada dia útil da semana
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
            $Feriados[] = $rsFeriados->fields['HOLIDAY_DATE'];
            $rsFeriados->MoveNext();
        }
        return $Feriados;
    }

    public function getDataVcto($DAT_INICIAL, $COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO = false) {

        /*         * ******************************************************************************************************
          A logica da função é recebe um prazo e a data inicial.
          Ao longo da função, a data inicial vai sendo acrescida até se transformar na data de vencimento
          a mesma medida em que a data vai sendo acrescida, o prazo vai diminuindo até zerar
          Assim, enquanto houver um valor no prazo, vai processando...
         * ****************************************************************************************************** */
        $GLOBALS["DiasUteis"] = $this->getDiasUteis();
        $GLOBALS["Feriados"] = $this->getFeriados();
        // a DAT_INICIAL chega no formato AAAAMMDDHHMM ano mes dia hora minuto e ï¿½ convertida pra timestamp
        $DAT_INICIAL = $this->converteTimeStamp($DAT_INICIAL);


        list($PRAZO_EM_DIAS, $PRAZO_EM_MINUTOS) = $this->getPrazo($COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO); // pega o prazo em dias e/ou horas e transforma em min
        // Verifica se a data inicial nï¿½o cai num feriado ou dia nï¿½o-ï¿½til. Se cair, busca o próximo dia vï¿½lido
        $DAT_INICIAL = $this->pulaDias($DAT_INICIAL, $PRAZO_EM_DIAS);

        // pega o horï¿½rio de trabalho data de vencimento. $HOR passa a ser um array com todas as horas e minutos limites de trabalho
        $HOR = $this->getHorarioTrabalho($DAT_INICIAL);

        $processaTarde = true;
        while ($PRAZO_EM_MINUTOS > 0) {
            // armazena o horï¿½rio final de trabalho no perï¿½odo matutino
            $FIN_MANHA = $this->converteTimeStamp(strftime("%Y%m%d", $DAT_INICIAL) . $HOR["HOR_FIN_MANHA"] . $HOR["MIN_FIN_MANHA"]);

            //if (strftime("%H%M", $DAT_INICIAL) < $HOR["HOR_FIN_MANHA"].$HOR["MIN_FIN_MANHA"]){
            // se a hora de abertura for menor do que a hora final de trabalho da manhï¿½, tem um tempo pra resolver jï¿½ de manhï¿½
            if (dateDiff("n", $DAT_INICIAL, $FIN_MANHA) > 0) {
                // TEMPO_MANHA armazena quanto tempo tem pra resolver atï¿½ o final da manhï¿½
                $TEMPO_MANHA = $this->dateDiff("n", $DAT_INICIAL, $FIN_MANHA);

                // se tiver tempo suficiente pra resolver de manhï¿½, ou seja, o tempo disponï¿½vel ï¿½ maior que o prazo
                if ($TEMPO_MANHA >= $PRAZO_EM_MINUTOS) {
                    // a data do vencimento passa a ser a data de abertura acrescida do prazo
                    $DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL) + $PRAZO_EM_MINUTOS, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));
                    $PRAZO_EM_MINUTOS = 0;
                } else {
                    // se nï¿½o puder ser resolvido sï¿½ de manhï¿½, a data ï¿½ acrescida do tempo que tem-se de manhï¿½
                    $DAT_INICIAL = mktime(extHora($DAT_INICIAL), extMin($DAT_INICIAL) + $TEMPO_MANHA, 0, extMes($DAT_INICIAL), extDia($DAT_INICIAL), extAno($DAT_INICIAL));
                    // esse tempo que foi acrescido ï¿½ data, ï¿½ retirado do prazo
                    $PRAZO_EM_MINUTOS -= $TEMPO_MANHA;
                }
            }

            // apï¿½s verificar o perï¿½odo da manhï¿½, se ainda tiver prazo pra fazer...
            if ($PRAZO_EM_MINUTOS > 0) {
                // armazena o perï¿½odo inicial e final da tarde
                $INI_TARDE = $this->converteTimeStamp(strftime("%Y%m%d", $DAT_INICIAL) . $HOR["HOR_INI_TARDE"] . $HOR["MIN_INI_TARDE"]);
                $FIN_TARDE = $this->converteTimeStamp(strftime("%Y%m%d", $DAT_INICIAL) . $HOR["HOR_FIN_TARDE"] . $HOR["MIN_FIN_TARDE"]);
                // quanto tempo (em minutos) tem pra fazer ï¿½ tarde
                $TEMPO_TARDE = dateDiff("n", $INI_TARDE, $FIN_TARDE);

                // se a data inicial form maior que o inï¿½cio da tarde, tï¿½ tranquilo, ï¿½ sï¿½ comeï¿½ar
                //if (strftime("%H%M", $DAT_INICIAL) > strftime("%H%M", $INI_TARDE)){
                if (dateDiff("n", $INI_TARDE, $DAT_INICIAL) > 0) {
                    // se a solcitaï¿½ï¿½o foi aberta depois do expediente, comeï¿½a no outro dia
                    if (strftime("%H%M", $DAT_INICIAL) > strftime("%H%M", $FIN_TARDE)) {
                        // acresencta um dia na data inicial					
                        $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL), 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL) + 1, $this->extAno($DAT_INICIAL));
                        // se o dia seguinte (acresentado acima) for feriado ou dia nï¿½o-ï¿½til, pula
                        $DAT_INICIAL = $this->pulaDias($DAT_INICIAL, 0);
                        $HOR = $this->getHorarioTrabalho($DAT_INICIAL); // pega o horï¿½rio de trabalho nova data
                        // o inï¿½cio passa a ser o horï¿½rio inicial de trabalho do dia seguinte (jï¿½ calculado)
                        $DAT_INICIAL = mktime($HOR["HOR_INI_MANHA"], $HOR["MIN_INI_MANHA"], 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                        // a variï¿½vel abaixo serve para, logo abaixo, nï¿½o ser processada a tarde, caso pule pro outro dia
                        $processaTarde = false;
                    } else { // se tem tempo ï¿½ tarde pra resolver, calcula quanto tempo tem					
                        $TEMPO_TARDE = dateDiff("n", $DAT_INICIAL, $FIN_TARDE);
                    }
                } else if ($TEMPO_TARDE) { // se a data nï¿½o ï¿½ maior que o inï¿½cio da tarde, ï¿½ por que caiu no intervalo do meio dia
                    $TEMPO_INTERVALO = dateDiff("n", $DAT_INICIAL, $INI_TARDE);
                    $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL) + $TEMPO_INTERVALO, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                    $TEMPO_TARDE = dateDiff("n", $DAT_INICIAL, $FIN_TARDE);
                }

                // se a data caiu dentro do perï¿½odo da tarde....
                if ($processaTarde) {
                    // se o tempo que se tem pra resolver ï¿½ tarde ï¿½ maior do que meu prazo, tï¿½ tranquilo...
                    if ($TEMPO_TARDE >= $PRAZO_EM_MINUTOS) {
                        // a data inicial ï¿½ acrescida de quantos minutos eu tenho pra resolver, gerando a data finall
                        $DAT_INICIAL = mktime($this->extHora($DAT_INICIAL), $this->extMin($DAT_INICIAL) + $PRAZO_EM_MINUTOS, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL), $this->extAno($DAT_INICIAL));
                        $PRAZO_EM_MINUTOS = 0;
                    } else {// se o tempo que eu tenho pra resolver ï¿½ tarde, nï¿½o basta....
                        // desconta todo o tempo que eu tenho ï¿½ tarde do tempo que eu tenho pra resolver	
                        $PRAZO_EM_MINUTOS -= $TEMPO_TARDE;
                        // pual pro dia seguinte					
                        $DAT_INICIAL = mktime(0, 0, 0, $this->extMes($DAT_INICIAL), $this->extDia($DAT_INICIAL) + 1, $this->extAno($DAT_INICIAL));
                        $DAT_INICIAL = $this->pulaDias($DAT_INICIAL, 0); // verifica se o dia seguinte nï¿½o ï¿½ feriado nem dia nï¿½o-ï¿½til
                        $HOR = $this->getHorarioTrabalho($DAT_INICIAL); // pega o horï¿½rio de trabalho data de vencimento
                        // a data inicial passa a ser o perï¿½odo inicial de trabalho do dia do vencimento
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

    public function getPrazo($COD_PATRIMONIO, $COD_ITEM, $COD_PRIORIDADE, $COD_SERVICO = false) {

        // se tiver COD_PATRIMONIO e este patrimônio tiver tempo, pega pelo tempo do patrimônio
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

        // só vai chegar aki se não tiver código do patrimônio OU se o patrimônio não tiver nem hora nem dia
        // se vier patrimônio e este tiver OU hora OU dia, pega esses valores e já dão o return na função, parando por ali.
        // seleciona, ou o tempo do Item, ou o tempo da Prioridade
        ############ ATENÇÃO #############
        /**
         * Os dados de tempo de atendimento e de prioridade agora são resgatados a partir do serviço.
         * Como esta função deverá ser reescrita na nova versão, apenas alterei a query e mantive todo os resto
         * quando for reescrita essas informações deverão ser obtidas através de um objeto Servico.
         * @since 2008-11-12
         */
        $db2 = new services_model();
        if (isset($COD_SERVICO)) {
            $where = "WHERE idservice = " . $COD_SERVICO;
        } else {
            ///programas que ainda não foram alterados podem manter-se usando o código do item
            $where = "WHERE iditem = " . $COD_ITEM;
        }
        $rsItem = $db2->selectService($where);

        // se tiver tanto a qtd de dias de atendimento ou a qtd de horas...

        $db3 = new priority_model();
        if ($rsItem->fields["days_attendance"] || $rsItem->fields["hours_attendance"]) {
            $NUM_DIA_ATENDIMENTO = $rsItem->fields["days_attendance"];
            $NUM_HORA_ATENDIMENTO = $rsItem->fields["hours_attendance"];
        }
        // se não houver nem qtd de dia nem de horas, porém houver um uma prioridade cadastrada para o ITEM...
        else if ($rsItem->fields['idpriority']) {
            $where = "where idpriorety = " . $rsItem->fields['idpriority'];
            $rsPrior = selectPriority($where);
            $NUM_DIA_ATENDIMENTO = $rsPrior->fields["days_attendance"];
            $NUM_HORA_ATENDIMENTO = $rsPrior->fields["hours_attendance"];
        } else { // se não houver tempo de atendimento nem prioridade do item, pega o tempo do cadastro de prioridade
            $where = "where idpriority = " . $COD_PRIORIDADE;
            $rsPrior2 = selectPriority($where);
            // Se nï¿½o tiver registro, ou nï¿½o tiver nem dia nem hora, zera...
            if ($rsPrior2->EOF || (!$rsPrior2->fields["days_attendance"] && !$rsPrior2->fields["hours_attendance"])) {
                $NUM_DIA_ATENDIMENTO = 0;
                $NUM_HORA_ATENDIMENTO = 0;
            } else {
                $NUM_DIA_ATENDIMENTO = $rsPrior2->fields["days_attendance"];
                $NUM_HORA_ATENDIMENTO = $rsPrior2->fields["hours_attendance"];
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

                // enquanto a data for feriado ou nï¿½o for dia util, incrementa a data sem considerar a 
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

    ///////////////// F I M   D A   F U N ï¿½ ï¿½ O   d a t e D i f f  //////////////////////////////

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

    function atualiza_responsavel($COD_SOLICITACAO, $COD_RESP, $GRUPO_OU_ATD = 'atd') {

        $envolvidos = get_envolvidos_atendimento($COD_SOLICITACAO);


        $db = new requestinsert_model();
        $sql_result = $db->updateRequestGroupInd($COD_SOLICITACAO);

        // Se a responsabilidade será passada a um atendente
        if ($GRUPO_OU_ATD == 'atd') {
            if (array_key_exists($COD_RESP, $envolvidos['atendentes'])) {
                // já está lá apenas certifica-se de que serï¿½ o responsï¿½vel

                $sql_result = $db->updateRequestGroupInd1($COD_RESP, $COD_SOLICITACAO);
            } else { //se não inclui o atendente como responsável
                $sql_result = $db->insertRequestGroupPersonResp($COD_SOLICITACAO, $COD_RESP, '1');
                if (!$sql_result) {
                    die('Error cod.:' . __LINE__);
                }
            }
        } else { //resp. serï¿½ passada a um grupo
            if (array_key_exists($COD_RESP, $envolvidos['grupos'])) {
                $sql_result = $db->updateRequestGroupInd2($COD_RESP, $COD_SOLICITACAO);
            } else {

                $rs = $db->insertRequestGroupResp($COD_SOLICITACAO, $COD_RESP, '1', '0');
                if (!$rs) {
                    die('Error cod.:' . __LINE__);
                }
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

    function get_envolvidos_atendimento($COD_SOLICITACAO) {

        $grupos = get_grupos();
        $users = get_users();

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

    function get_grupos($match = NULL) {

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

    function get_users($COD_TIPO = "ALL") {
        //o array que retornaremos
        $users_do_tipo = array();

        //Se nï¿½o especificou tipo ou colocaou "ALL"
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

        //recupera todos os usuï¿½rios do tipo

        $sql_result = $db->getPersonFromType($COD_TIPO);
        $empresas = get_empresas();
        while ($usuarios = $sql_result->fields) {

            foreach ($usuarios as $col => $valor) {
                $users_do_tipo[$sql_result->fields["idperson"]][$col] = $valor;
            }
            if (isset($empresas[$sql_result->fields["idjuridical"]])) {
                $users_do_tipo[$sql_result->fields["idperson"]]['juridical'] = $empresas[$sql_result->fields["idjuridical"]];
            }
            if ($users_do_tipo[$sql_result->fields["idperson"]]['idtypeperson'] != 1) { //se nï¿½o for um usuï¿½rio pega os grupos de atendimento
                $users_do_tipo[$sql_result->fields["idperson"]]['GRUPOS_ATENDIMENTO'] = array();
                $db = new requestinsert_model($sql_result->fields["idperson"]);
                $sql_result_grupos = $db->getGroupsAttendance();
                if (!$sql_result_grupos) { //o atendente nï¿½o tem grupo
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

    function get_empresas() {
        $db = new person_model;
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

        if ($sql_result_count->fields['total'] == 0) { //sem regras para essa combinação Item/serviço
            return (int) 0;
        }

        return (int) $sql_result->fields['fl_recalculate'];
    }

}
?>
