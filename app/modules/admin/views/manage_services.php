<?php
error_reporting(1);
include 'includes/config/config.php';
session_start();

if (substr($config['path_default'], 0, 1) != '/') {
    $config['path_default'] = '/' . $config['path_default'];
}
define('path', $config['path_default']);
$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}
define('DOCUMENT_ROOT', $document_root);
define('theme', $config['theme']);

require_once(SMARTY . 'Smarty.class.php');

$smarty = new Smarty;
$smarty->debugging = false;
$smarty->compile_dir = "system/templates_c/";
$smarty->config_load(DOCUMENT_ROOT . path . '/app/lang/' . $config['lang'] . '.txt', $config['lang']);
$smarty->assign('lang', $config['lang']);
$smarty->assign('pagetitle', $config['page_title']);
$langVars = $smarty->get_config_vars();

$langVars2 = $smarty->get_template_vars();

if (path == '/..') {
    if ($custom_attach_path) {
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
    } else {
        $path_attach = DOCUMENT_ROOT . "/app/uploads/helpdezk/services/";
    }
} else {
    if ($custom_attach_path) {
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
    } else {
        $path_attach = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/services/";
    }
}



if (!(isset($_SESSION["SES_COD_ATTACHMENT"])) || ($_SESSION["SES_COD_ATTACHMENT"] == "")) {
    $_SESSION["SES_COD_ATTACHMENT"] = "0";
}


if (isset($_FILES['ARQUIVO'])) {
            //Checa se o arquivo foi enviado e sem erros
            if (!isset($_FILES['ARQUIVO']) || $_FILES['ARQUIVO']['error'] != 0) {
                hddie($langVars['Manage_fail_import_file'].'<!--' . $_FILES['ARQUIVO']['error'] . '-->');
            }

            // Inicializa as variáveis necessárias
            $areas = $tipos = $itens = $servicos = $grupos = $prioridades = array();

            // Para cada nível, cria um array com aquilo que já está no banco
			$DB = new services_model();
			

			$tmpAreas = $DB->selectAreas();
            foreach ($tmpAreas as $dados) {
                $areas[trim($dados['name'])] = $dados['idarea'];
            }
			$areas = array_map('clean_encode', $areas);
			
            $tmpTipos = $DB->selectAreaType();
            foreach ($tmpTipos as $dados) {
                $tipos[$dados['idarea']][$dados['name']] = $dados['idtype'];
            }

            $tmpItens = $DB->selectTypeItem();
            foreach ($tmpItens as $dados) {
                $itens[$dados['idtype']][$dados['name']] = $dados['iditem'];
            }

            $tmpServicos = $DB->selectItemService();
            foreach ($tmpServicos as $dados) {
                $servicos[$dados['iditem']][$dados['name']] = $dados['idservice'];
            }


            // Recupera todos os grupos e o grupo de 1º nível
            //$grupos = array_flip(get_grupos());
            $dbg = new groups_model();
        	$rs = $dbg->selectGroup();
            while (!$rs->EOF) {
            	$grupos[str_replace(" ", "", $rs->fields['name'])] = $rs->fields['idgroup'];
	            $rs->MoveNext();
	        }


            // Recupera todas as prioridades e a prioridade padrão
            $allPrio = $DB->selectPriorityData();

            foreach ($allPrio as $prio) {
                $prioridades[$prio['name']] = $prio['idpriority'];
                $tempoPrioridades[$prio['idpriority']] = array('DIAS' => $prio['limit_days'], 'HORAS' => $prio['limit_hours']);
                if ($prio['def'] == 1) {
                    $prioridadePadrao = $prio['idpriority'];
                }
            }

            // Libera um pouco da mem ...
            unset($tmpAreas, $tmpTipos, $tmpItens, $tmpServicos);

            //  Move o arquivo informado pra evitar que seja removido do tmp...
            $csvFile = $path_attach . "/ATIS.csv";
            if (!@move_uploaded_file($_FILES['ARQUIVO']["tmp_name"], $csvFile)) {
                die($langVars['Manage_fail_move_file']);
            }
            // Inicia a transaçao
            $DB->BeginTrans();

            // Abre o arquivo e inicia o contador
            if (!@$arquivo = fopen($csvFile, "r")) {
                die($langVars['Manage_fail_open_file_in']."$csvFile.".$langVars['Manage_fail_open_file_per']);
            }
            $i = 0;
            while (!feof($arquivo)) {
                $i++;
                $linha = fgets($arquivo, 4096);

                if (strlen($linha) == 0) {
                    continue; // Empty line....
                }

                $dados = split(";", $linha);
                $numCols = count($dados);
                if ($numCols != 9 AND $numCols != 10) {
                    $DB->RollbackTrans();
					$error = str_replace("%", $i, $langVars['Error_Number_columns'] ); 
                    die($error . '<hr>');
                }

				// Clean and encode the array
				$dados = array_map('clean_encode', $dados);
                // Cadastra ou recupera a Area
				echo "<pre>";

                if (!array_key_exists($dados[0], $areas)) {
					$name = $dados[0];
					$rs = $DB->selectAreaFromName(trim($name));
					if ($rs->RecordCount() == 0) {
						$rs = $DB->areaInsert($dados[0]) ;
						if($rs){
							//$codArea = $DB->InsertID() ;
                            $codArea = $DB->TableMaxID('hdk_tbcore_area','idarea');
							echo $langVars['Manage_service_area'] . $codArea . '<br>';
						} else {
							$DB->RollbackTrans();
							die($langVars['Manage_service_area_fail'] . $dados[0] . $langVars['Manage_service_inf_line'] . $i . $langVars['Manage_service_imp_canceled']);
						}
					} 
                } else {
                    $codArea = $areas[$dados[0]];
                    echo $langVars['Manage_service_using_area'] . $codArea . '<br>';
                }

                // Cadastra ou recupera o Tipo
                if (!isset($tipos[$codArea]) || !array_key_exists($dados[1], $tipos[$codArea])) {
					$ins = $DB->typeInsert($dados[1], 0, 'A', '1', $codArea) ;
                    if ($ins) {
						//$idType =  $DB->InsertID() ;
                        $idType = $DB->TableMaxID('hdk_tbcore_type','idtype');
						$tipos[$codArea][$dados[1]] = $idType;
						echo $langVars['Manage_service_type'] . $idType . '<br>';
                    } else {
                        $DB->RollbackTrans();
                        die($langVars['Manage_service_type_fail'] . $dados[1] . $langVars['Manage_service_inf_line'] . $i . $langVars['Manage_service_imp_canceled']);
                    }
                } else {
					
                    $idType = $tipos[$codArea][$dados[1]];
                    echo $langVars['Manage_service_using_type'] . $idType . '<br>';
                }
				
                // Cadastra ou recupera o Item
                if (!isset($itens[$idType]) || !array_key_exists($dados[2], $itens[$idType])) {
					$ins = $DB->insertItem($dados[2], 0, 'A', 0, $idType) ;
                    if (!$ins) {
                        $DB->RollbackTrans();
                        die($langVars['Manage_service_item_fail'] . $dados[2] . $langVars['Manage_service_inf_line'] . $i . $langVars['Manage_service_imp_canceled']);
                    }
					//$codItem =  $DB->InsertID();
                    $codItem = $DB->TableMaxID('hdk_tbcore_item','iditem');

                    $itens[$idType][$dados[2]] = $codItem;
                    echo $langVars['Manage_service_item'] . $codItem . '<br>';					
                } else {
                    $codItem = $itens[$idType][$dados[2]];
                    echo $langVars['Manage_service_using_item'] . $codItem . '<br>';
                }
				
                // Verifica o grupo, se não existir, usa o de 1º nível
                if (!array_key_exists(str_replace(" ", "", $dados[4]), $grupos)) {
			        $db_grp = new groups_model();
					$db_per = new person_model();
					// Teste da empresa
					$idcostumer = $db_per->selectPersonFromName($dados[8]);
					if (!$idcostumer) {
						$DB->RollbackTrans();
						$error = str_replace("%", $dados[8], $langVars['Manage_service_company_fail'] );						
						die($error);
					}
					// Cadastro o nome do grupo na tabela pessoa
					$idperson = $db_per->insertPerson('3', '6', '1', '1', $dados[4], NULL, NULL, 'A', 'N', NULL, NULL, NULL);
					if(!$idperson)
					{
                        $DB->RollbackTrans();
                        die($langVars['Manage_service_group_fail'] . $dados[4] . $langVars['Manage_service_inf_line'] . $i . $langVars['Manage_service_imp_canceled']);
					
					}
					$level = 2;
					$rsGrp = $db_grp->insertGroup($idperson,$level,$idcostumer, 'N');
                    if (!$rsGrp) {
                        $DB->RollbackTrans();
                        die($langVars['Manage_service_group_fail2'] . $dados[4] . $langVars['Manage_service_inf_line'] . $i . $langVars['Manage_service_imp_canceled']);
                    }
					//$codGrupo =  $db_grp->InsertID() ;
                    $codGrupo = $DB->TableMaxID('hdk_tbgroup','idgroup');
                    $grupos[str_replace(" ", "", $dados[4])] = $codGrupo;
                    echo $langVars['Manage_service_group_register'] . $codGrupo . '<br>';
                } else {
                    $codGrupo = $grupos[str_replace(" ", "", $dados[4])];
                    echo $langVars['Manage_service_group_using'] . $codGrupo . '<br>';
                }
				
                // Verifica a prioridade, se não existir usa a padrão
                if (!array_key_exists($dados[5], $prioridades)) {
                    $codPrioridade = $prioridadePadrao;
                    $msgs[$i][] = $langVars['Service'] . $dados[3] . $langVars['Manage_service_default_pri'] . $dados[5] . $langVars['Manage_service_pri_no_exist'];
                } else {
                    $codPrioridade = $prioridades[$dados[5]];
                }
				
                

				
				$name = $dados[3] ;
				$whereCheck = "WHERE name = '$name' and iditem = $codItem ";
				$rs = $DB->selectService($whereCheck);
				if(!$rs) {
					$DB->RollbackTrans();
					die($langVars['Manage_service_fail_code'] . $dados[3] . $langVars['Manage_service_on_line'] . $i . $langVars['Manage_service_imp_canceled']);
				}

				if ($rs->RecordCount() > 0) {
                    echo $langVars['Service'] . $dados[3] . '" ('.$langVars['Manage_service_line'].' '. $i . ') '.$langVars['Manage_service_already_registered'].'<br>';
                    //apenas lê o já cadastrado (pode haver uma aprovação sendo criada agora...
					$idservice = $rs->fields['idservice'] ;
                } else {
                    //maioria dos caso deve entrar aqui
					if (!is_numeric($dados[6]))
					{
						die($langVars['Manage_service_column_6'] . $dados[6] . $langVars['Manage_service_on_line'] . $i . $langVars['Manage_service_imp_canceled']);
					}

					$a_Time = define_service_time($dados[7]);

					$ins = $DB->serviceInsert($dados[3], 0, 'A', 0 , $codItem, $codPrioridade ,$a_Time[1] ,$dados[6] , $a_Time[0]);
                    if (!$ins) {
                        $DB->RollbackTrans();
                        die($langVars['Manage_service_fail'] . $dados[3] . $langVars['Manage_service_inf_on_line'] . $i . $langVars['Manage_service_imp_canceled']);
                    } else {
                        echo $langVars['Manage_service_register_service'] .'"'. $dados[3] . '" ('.$langVars['Manage_service_line'] . $i . ') -> '.$langVars['PDF_code'].': '. $DB->InsertID()  . '<br>';
					}
					//$idservice = $DB->InsertID();
                    $idservice = $DB->TableMaxID('hdk_tbcore_service','idservice');
				}
				
				/*
				 * vincula serviço com o grupo de atendimento 
				 */
				$ins = $DB->serviceGroupInsert($idservice,$codGrupo);
				if (!$ins) {
					$DB->RollbackTrans();
					die($langVars['Manage_service_fail_rel'] . $idservice  . $langVars['Manage_service_and_group'] . $codGrupo . $langVars['Manage_service_imp_canceled']);
				} 								
				
				// Se o layout for de 9 colunas, teremos o nome do aprovador
				if (isset($dados[9]) AND !empty($dados[9])) 
				{
					$aOperator = explode("|",$dados[9]);
					$db_per = new person_model();
					
					// Preciso testar todos os atendentes, pois irei deletar na tabela hdk_tbapproval_rule
					foreach($aOperator as $key => $val) 
					{
						$rs = $db_per->selectPerson("AND tbp.name = '$val' and tbtp.idtypeperson = 3");
						if ($rs->RecordCount() == 0) {
							die($val . $langVars['Manage_service_not_registered'] . $i );
						} 
					}
					// DELETAR
					
					$db_rules = new requestrules_model();
					$db_rules->BeginTrans();
					
					$rs_rules = $db_rules->deleteUsersApprove($codItem, $idservice);
                    if (!$rs_rules) {
                        $DB->RollbackTrans();
						$db_rules->RollbackTrans();
                        die('Falha ao excluir ma tabela hdk_tbapproval_rule. Linha ' . $i . $langVars['Manage_service_imp_canceled']);
                    }					
					$j=1;
					// Incluir na tabela hdk_tbapproval_rule
					foreach($aOperator as $key => $val) 
					{
						$rs = $db_per->selectPerson("AND tbp.name = '$val' ");
						$ins = $db_rules->insertUsersApprove($codItem, $idservice, $rs->fields['idperson'], $j, 0);
						if (!$ins) {
						    $DB->RollbackTrans();
							$db_rules->RollbackTrans();
							die('Erro ao gravar na tabela hdk_tbapproval_rule. Atendente '.$val.'Linha ' . $i );
						} else {
							echo 'Cadastrado aprovador "' . $val . '" ( '. $langVars['Manage_service_line'] . $i . '), '.$langVars['Manage_service_in_service'] . $dados[3]. $langVars['Mange_service_order'] ." ". $j  . '<br>';		
						}						
						$j++;
					}
					$db_rules->CommitTrans();
				}                
                echo '---<br>'. $langVars['Manage_service_finalized_line'] . $i . '<br>---<br>	';
            }

            $DB->CommitTrans();
            echo $lang['Manage_service_completed'];
            echo '<div style="width:100%; text-align:center"><a href="javascript:window.print()">Imprimir</a></div>';
            exit;
        }
		function clean_encode($str) {
			return utf8_encode(trim(addslashes($str)));
		}   
  		
		function define_service_time($str)
		{		
			$str = strtoupper($str);
			$time = preg_match("/[H-M]/", $str);

			if (strpos($str, 'H') === false) {
				if (strpos($str, 'M') === false) {
					die($langVars['Manage_service_not_identify_priority'] . $i . '<br>'); 
				} else {
					$pos = strpos($str, 'M') ;
				}
			} else {
				$pos = strpos($str, 'H') ;
			}
			$ind_hours_minutes = substr($str, -1);  
			$hours_attendance = substr($str, 0,$pos);
            if(!$hours_attendance) $hours_attendance = 0;
			return array($hours_attendance,  $ind_hours_minutes);
		}
		
?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href='<?php echo path; ?>/app/themes/<?php echo theme; ?>/style.css' />
        <script>
            function validaArquivoCSV() {
                arquivo = document.formSe.ARQUIVO.value.toLowerCase();
                tipo = arquivo.split(".");
                if (tipo[1] != "csv") {
                    alert("<?php echo $langVars['Alert_wrong_extension_csv']?>");
                    return false;
                } else
                    return true;
            }
                       
        </script>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    </head>
    <body id="upload-anexo" bgcolor="black">
        <form action="importservices/import/" accept-charset=utf-8 method="post" enctype="multipart/form-data" name="formSe" onSubmit="return validaArquivoCSV();" target="_oframe">    
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td> 
                    	<input name="ARQUIVO" id="ARQUIVO" type="file"/>                                                      
                        <input name="Submit" type="submit" class="btnOrange tp1" id="Submit" value="<?=$langVars['Import']?>"/>                                
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
