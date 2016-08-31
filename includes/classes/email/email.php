<?php
if($COD_EMAIL == "enviar_alertas_reload"){ //estaremos dentro do diretório includes/reload
	require_once("../config.php");
	require_once("../../Connections/conexao.php");
	require_once("../../includes/lang_comum/$_idioma_/comum.inc");
}
else{
	require("../includes/config.php");
	require_once("../Connections/conexao.php");
	require_once("../includes/lang_comum/$_idioma_/comum.inc");
}

if (defined('NAO_ENVIAR_EMAILS') AND NAO_ENVIAR_EMAILS === TRUE) {
	return true;
} 

	require("lang/$_idioma_/email.inc");

	if (!isset($COD_EMAIL)){
		echo $l_eml["prob_no_email"];
		print("Código do email não informado");
		return false;
		
	}
$destinatario = "";
	//## ENVIA E-MAIL PARA O GRUPO AO REGISTRAR UMA SOLICITACAO ##===
	if ($COD_EMAIL == "registrar" || $COD_EMAIL == "registrar_aprovacao" || $COD_EMAIL == "registrar_retorno_aprovacao"){

		//Verifica se a permissão para enviar e-mail ao assumir solicitação<br>
		//está vingente
		/****
		 * Uma alteração para a Killing permitirá que esses e-mails sejam enviados independentemente
		 * da flag, quando se tratar da aprovação de projetos - pois eles só usarão a noticação nesses casos
		 * @since 2008-02-27
		 **/
		if ($_SESSION["SES_IND_MAIL_REGISTRAR"]  == "1" || $COD_EMAIL == "registrar_aprovacao" || $COD_EMAIL == "registrar_retorno_aprovacao"){			
			if ($COD_EMAIL == "registrar_aprovacao") {
				$COD_REGISTRAR = "51"; // Template é diferente pra aprov. de projetos
			} elseif ($COD_EMAIL == "registrar_retorno_aprovacao") {
				$COD_REGISTRAR = "52"; // Template é diferente pra aprov. de projetos ao retornar a fase anterior
			} else {
				$COD_REGISTRAR = "16"; // Esse é o padrão
			}
			
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_REGISTRAR ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			//echo $rsTemplate->Fields("NOM_CONFIG");
			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");
			
			//Consulta o grupo responsável pela solicitação
			$SQL = "select COD_GRUPO, COD_ANALISTA
					 from hdk_solicitacao_grupo 
					 where COD_SOLICITACAO=".$COD_SOLICITACAO."
					 AND IND_RESPONSAVEL = 1";
							
			$rsSolGrup = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			$COD_GRUPO = $rsSolGrup->Fields('COD_GRUPO');
			$COD_ANALISTA = $rsSolGrup->Fields('COD_ANALISTA');

			//Se a solicitação estiver sendo passada para um grupo
			if ($COD_GRUPO){
				//Consulta os emails dos atendentes que atendem apenas a empresa do usuario
				$SQL =" SELECT 
						usuario.DES_EMAIL , 
						count(usuarioempresa.COD_EMPRESA) AS TOTAL 
						from hdk_usuario usuario, 
						hdk_usuario_grupo usuariogrupo,
						hdk_usuario_empresa usuarioempresa
						where 
						usuario.COD_USUARIO = usuariogrupo.COD_USUARIO 
						AND usuario.COD_USUARIO = usuarioempresa.COD_USUARIO
						AND usuarioempresa.COD_EMPRESA = ". $COD_EMPRESA."
						AND usuario.IND_ATIVO = 1 
						AND usuario.DES_EMAIL IS NOT NULL
						AND usuario.DES_EMAIL != '' 
						AND usuariogrupo.COD_GRUPO= ". $COD_GRUPO ."
						GROUP BY 
						usuario.DES_EMAIL "; 								
					
				$rsDest = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
				
				/**
				 * Evita que sejam excluídos da lista os atendentes restritos a empresa
				 * @since 2009-07-01	
				 */
				if (empty($destinatario)) {
					$destinatario = "";
				}
				
				while(!$rsDest->EOF){
					if (!$destinatario){
						$destinatario = $rsDest->Fields('DES_EMAIL');
					}else{
						$destinatario .= ";".$rsDest->Fields('DES_EMAIL');
					}
					$rsDest->MoveNext();
				}
				
				$rsDest->Close(); 
				
			   // Consulta os atendentes que atendente todas as empresas	
				$SQL =" SELECT usuario.DES_EMAIL, 
						count(usuarioempresa.COD_EMPRESA)  TOTAL 
						from 
						hdk_usuario usuario
						LEFT JOIN 
						hdk_usuario_empresa usuarioempresa
						ON (usuario.COD_USUARIO = usuarioempresa.COD_USUARIO
						AND usuarioempresa.COD_EMPRESA = ". $COD_EMPRESA ."),
						hdk_usuario_grupo usuariogrupo 
						where 
						usuario.COD_USUARIO = usuariogrupo.COD_USUARIO 
						AND usuario.IND_ATIVO = 1 
						AND usuario.DES_EMAIL is NOT Null 
						AND usuario.DES_EMAIL !='' 
						AND usuariogrupo.COD_GRUPO=".$COD_GRUPO ."
						GROUP BY 
						usuario.DES_EMAIL, 
						usuarioempresa.COD_EMPRESA 
						HAVING
						count(usuarioempresa.COD_EMPRESA) = 0";
			
				$rsDest = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
				
				/**
				 * Evita que sejam excluídos da lista os atendentes restritos a empresa
				 * @since 2009-07-01	
				 */
				if (empty($destinatario)) {
					$destinatario = "";
				}

				while(!$rsDest->EOF){
					if (!$destinatario){
						$destinatario = $rsDest->Fields('DES_EMAIL');
					}else{
						$destinatario .= ";".$rsDest->Fields('DES_EMAIL');
					}
					$rsDest->MoveNext();
				}
				
				$rsDest->Close();  
				
		
						
			}
			//Se estiver sendo passada apenas para 1 atendente
			else{
				$SQL = "select usu.DES_EMAIL from hdk_usuario usu where COD_USUARIO=".$COD_ANALISTA;
				$rsDest = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
				$destinatario = $rsDest->Fields('DES_EMAIL');
			}		
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			
			//$conteudo .= "<BR><BR>".$url;			
		
		}	
	}		
	
	
	//## ENVIA E-MAIL PARA O USUARIO AO ATENDENTE ASSUMIR UMA SOLICITACAO ##===
	if ($COD_EMAIL == "assumir"){
		//Verifica se a permissão para enviar e-mail ao assumir solicitação<br>
		//está vingente

		if ($_SESSION["SES_IND_MAIL_ABRIR"]  == "1"){

			//Consulta no Banco, a template a ser utilizada.
			$COD_ASSUMIR = "1";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_ASSUMIR ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());

			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");

			$goto    = ('usuario/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';
	
			//Lozalizando o e-mail do usuário solicitantes
			$SQL = "SELECT usuario.DES_EMAIL
					FROM 
					 hdk_usuario usuario
					,hdk_solicitacao solicitacao
					WHERE
					usuario.COD_USUARIO = solicitacao.COD_USUARIO
					AND solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO;
			$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());			

			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			$destinatario = $rsMail->Fields("DES_EMAIL");
			//$conteudo .= "<BR><BR>".$url;

			
		}	
	}
	
	//## ENVIA E-MAIL PARA O USUARIO AO ATENDENTE ENCERRAR UMA SOLICITACAO ##===
	if ($COD_EMAIL == "encerrar"){
		if ($_SESSION["SES_IND_MAIL_ENCERRAR"]  == "1"){
			//Consulta no Banco, a template a ser utilizada.
			$COD_ASSUMIR = "2";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_ASSUMIR ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());

			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");			

			$goto    = ('usuario/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">';
			if ($aprovar=="true"){
				$url .= $l_eml["link_solicitacao"];
			} else {
				$url .= $l_eml["link_aprovar"];
			}
			$url .= '</a>';
			
			//Lozalizando o e-mail do usuário solicitantes
			$SQL = "SELECT usuario.DES_EMAIL 
					FROM 
					 hdk_usuario usuario
					,hdk_solicitacao solicitacao
					WHERE
					usuario.COD_USUARIO = solicitacao.COD_USUARIO
					AND solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO;
			$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			$destinatario = $rsMail->Fields("DES_EMAIL");
			//$conteudo .= "<BR><BR>".$url;
			
		
		}		
	}	
	
	//## ENVIA E-MAIL PARA O USUARIO AO ATENDENTE REJEITAR UMA SOLICITACAO ##===
	if ($COD_EMAIL == "rejeitar"){
		if ($_SESSION["SES_IND_MAIL_REJEITAR"]  == "1"){
			//Consulta no Banco, a template a ser utilizada.
			$COD_REJEITAR = "3";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_REJEITAR ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());

			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");		


			$motivo  = "<u>".$l_eml["lb_motivo_rejeicao"]."</u> ".$DES_MOTIVO;
			$goto    = ('usuario/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';
	
			//Lozalizando o e-mail do usuário solicitantes
			$SQL = "SELECT usuario.* 
					FROM 
					 hdk_usuario usuario
					,hdk_solicitacao solicitacao
					WHERE
					usuario.COD_USUARIO = solicitacao.COD_USUARIO
					AND solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO;
			$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
				
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			$destinatario = $rsMail->Fields("DES_EMAIL");
			//$conteudo .= "<BR>".$motivo;
		}
	}
	
	//## ENVIA E-MAIL PARA O ATENDENTE AO USUARIO APROVAR UMA SOLICITACAO ##===
	if ($COD_EMAIL == "aprovar"){

		if ($_SESSION["SES_IND_MAIL_APROVAR"]  == "1"){
			//Consulta no Banco, a template a ser utilizada.
			$COD_APROVAR = "5";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_APROVAR ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());

			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");		

			$goto    = ('atendimento/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';

			//Lozalizando o e-mail do usuário solicitantes
			$SQL = "SELECT usuario.DES_EMAIL
					FROM 
					 hdk_usuario usuario
					,hdk_solicitacao_grupo solicitacao
					WHERE
					usuario.COD_USUARIO = solicitacao.COD_ANALISTA
					AND solicitacao.IND_RESPONSAVEL = 1
					AND solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO;
			$rsMail = $conexao->Execute($SQL) or die(__FILE__ . '::' . __LINE__ . '<br>' . $SQL . '<BR>' . $conexao->ErrorMsg());
				
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			$destinatario = $rsMail->Fields("DES_EMAIL");
//			$conteudo .= "<BR><BR>".$url;
		}
	}
	
	//## ENVIA E-MAIL PARA O ATENDENTE AO USUARIO NAO APROVAR UMA SOLICITACAO ##===
	if ($COD_EMAIL == "naoaprovar"){

		if ($_SESSION["SES_IND_MAIL_NAO_APROVAR"]  == "1"){
			//Consulta no Banco, a template a ser utilizada.
			$COD_APROVAR = "4";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_APROVAR ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());

			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");		

			$goto    = ('atendimento/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';

			//Lozalizando o e-mail do usuário solicitantes
			$SQL = "SELECT usuario.DES_EMAIL
					FROM 
					 hdk_usuario usuario
					,hdk_solicitacao_grupo solicitacao
					WHERE
					usuario.COD_USUARIO = solicitacao.COD_ANALISTA
					AND solicitacao.IND_RESPONSAVEL = 1
					AND solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO;
			$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
				
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			$destinatario = $rsMail->Fields("DES_EMAIL");
		//	$conteudo .= "<BR><BR>".$url;

		}
	}
	
	//## ENVIA E-MAIL DE APONTAMENTOS INCLUSOS NA SOLICITACAO PARA OS USUARIOS ##===
	if ($COD_EMAIL == "apontamento_usuario")
	{
                /**
                 *  Removida a verificação da configuraçõa da sessao 
                 * Visto que o checkbox está sempre disponível na tela independente de estar ou nõa 
                 * habilitada essa conf, resolvi deixar o envio sendo determinado apenass pelo checkbox;
                 * A conf. agora só indica se, por padrão, o checkbox vem ou não marcado. Mas a definição
                 * de enviar ou não vem do checkbox em sí e não da conf.
                 *
                 * @since 2008-08-14
                 */
	/**************************************
        	if ($_SESSION["SES_IND_MAIL_APONT_USUARIO"]  == "1")
		{
        ***************/
			//Consulta no Banco, a template a ser utilizada.
			$COD_APONTAMENTO = "13";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_APONTAMENTO ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
	
			require_once('../includes/solicitacao_detalhe.php');
			
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");		
	
			$goto    = ('usuario/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';
	
			//Lozalizando o e-mail do usuário solicitantes
			$SQL = "SELECT usuario.DES_EMAIL 
				FROM 
				 hdk_usuario usuario
				,hdk_solicitacao solicitacao
				WHERE
				usuario.COD_USUARIO = solicitacao.COD_USUARIO
				AND solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO;

			$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
										
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			$destinatario = $rsMail->Fields("DES_EMAIL");
	/**************************************
		}
        ***************/

	}
	
	//## ENVIA E-MAIL DE APONTAMENTOS INCLUSOS NA SOLICITACAO PARA OS ATENDENTES ##===
	if ($COD_EMAIL == "apontamento_atendente")
	{				   

		if ($_SESSION["SES_IND_MAIL_APONT_ATENDENTE"]  == "1")
		{				
		 
 
			//Consulta no Banco, a template a ser utilizada.
			$COD_APONTAMENTO = "43";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_APONTAMENTO ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
	
			require_once('../includes/solicitacao_detalhe.php');
			
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");		
	
			$goto    = ('atendimento/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
			$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';
	
			//Lozalizando o e-mail do atendentes responsaveis e auxiliares da solicitacao
			$SQL = "SELECT usuario.DES_EMAIL 
				FROM 				 
				hdk_solicitacao solicitacao			
				,hdk_solicitacao_grupo grupo
				,hdk_usuario usuario
				WHERE
				solicitacao.COD_SOLICITACAO = ".$COD_SOLICITACAO . "
				AND solicitacao.COD_SOLICITACAO = grupo.COD_SOLICITACAO
				AND (grupo.IND_RESPONSAVEL = 1  OR IND_ANALISTA_AUXILIAR = 1)
				AND grupo.COD_ANALISTA = usuario.COD_USUARIO
				"; 
			
			$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			$destinatario = "";
			while (!$rsMail->EOF){
				if ($rsMail->Fields("DES_EMAIL")){
					if (!$destinatario){
						$destinatario = $rsMail->Fields("DES_EMAIL");
					}else{
						$destinatario .= ";" . $rsMail->Fields("DES_EMAIL");
					}				
				}
				$rsMail->MoveNext();
			}													
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			//$conteudo .= "<BR><BR>".$url;
		} else {
			//não envia pois não está configurado para fazê-lo
		}
	}
	
	
	
	//## ENVIA E-MAIL PARA ATENDENTE AUX. AO SER INCLUSO EM UMA SOLICITACAO ##===
	/**
	 * Acabo de perceber que se um segudo analista auxiliar for inserido em momento posterior a um primeiro, 
	 * esse primeiro recebera o email de notificacao novamente...
	 * @todo resolver isso (criar uma flag de "ja notificado" e o que me ocorre agora...)
	 * @since 2009-07-01	
	 */
	if (empty($destinatario)) {
		$destinatario = "";
	}

	if ($COD_EMAIL == "apontamento_analista_auxiliar"){
		//Consulta no Banco, a template a ser utilizada.
		$COD_APONTAMENTO = "21";
		$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_APONTAMENTO ";
		$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());

		require_once('../includes/solicitacao_detalhe.php');
		
		$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
		$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
		eval ("\$conteudo = \"$conteudo\";");		

		$goto    = ('atendimento/solicita_detalhes.php?COD_SOLICITACAO='.$COD_SOLICITACAO);
		$url     = '<a href="'.$url_helpdesk.'index.php?url='.urlencode($goto).'">'.$l_eml["link_solicitacao"].'</a>';

		//Lozalizando o e-mail do usuário solicitantes
		$SQL = "SELECT
					usuario.DES_EMAIL
				FROM 
					hdk_usuario usuario
					,hdk_solicitacao_grupo solgrup
				WHERE
					solgrup.COD_ANALISTA = usuario.COD_USUARIO
					AND solgrup.IND_ANALISTA_AUXILIAR = 1
					AND solgrup.COD_ANALISTA <> ".$_SESSION["SES_COD_USUARIO"]."
					AND solgrup.COD_SOLICITACAO = ".$COD_SOLICITACAO;					
		$rsMail = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
		
		$destinatario = "";
		while (!$rsMail->EOF){
			if ($rsMail->Fields("DES_EMAIL")){
				if (!$destinatario){
					$destinatario = $rsMail->Fields("DES_EMAIL");
				}else{
					$destinatario .= ";" . $rsMail->Fields("DES_EMAIL");
				}				
			}
			$rsMail->MoveNext();
		}

		$assunto   = $rsTemplate->Fields("NOM_TITULO");
		eval ("\$assunto = \"$assunto\";");		
		//$conteudo .= "<BR><BR>".$url;
	}

	/**
	 * Quando for efetuado um download de email que se torne solicitação
	 */
	if ($COD_EMAIL == 'feedback_usuario') {

		$user = get_user_sol($COD_SOLICITACAO);
		$body_subject = get_body_subject($COD_EMAIL, array('COD_SOLICITACAO', 'nom_titulo'));
		$conteudo = $body_subject['body'];
		$assunto = $body_subject['subject'];		
		$destinatario = $user['DES_EMAIL'];
                
                print($destinatario . $assunto . $conteudo);
	}
		
	// AO INCLUIR UM ANALISTA RESPONSÁVEL

	if ($COD_EMAIL == "incluir_analista")
	{
		//Verifica se a permissão para enviar e-mail ao assumir solicitação<br>
		//está vingente

		if ($_SESSION["SES_IND_MAIL_INCLUIR_ANALISTA"]  == "1")
		{

			//Consulta no Banco, a template a ser utilizada.
			$COD_INCLUIR_ANALISTA = "17";
			$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $COD_INCLUIR_ANALISTA ";
			$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg()."<br>Linha ". __LINE__ ." no arquivo ". __FILE__);

			require_once('../includes/solicitacao_detalhe.php');
			$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
			$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
			eval ("\$conteudo = \"$conteudo\";");

			$SQL = "select DES_EMAIL from hdk_usuario where COD_USUARIO = ".$_GET['COD_ANALISTA'];	
			//echo $SQL;
			$rsAnalista = $conexao->Execute($SQL) or die ($conexao->ErrorMsg()."<br>Linha ". __LINE__ ." no arquivo ". __FILE__);
			
			$destinatario = $rsAnalista->Fields('DES_EMAIL');
			
			$assunto   = $rsTemplate->Fields("NOM_TITULO");
			eval ("\$assunto = \"$assunto\";");
			
			//$conteudo .= "<BR><BR>".$url;			
		}	
	}		


	/** 
	 * Envia email ao responsável por uma aprovação de solicitação
	 * @since 26-09-2008
	 */
	if ($COD_EMAIL == 'registrar_aprovacao_solicitacao') {
			// Lista de variáveis que não constam includes/solicitacao_detalhes.php, 
			// mas podem estar na template
			$vars_aparecem_no_email = array('COD_SOLICITACAO', 'nom_titulo');
			
			$body_subject = get_body_subject($COD_EMAIL, $vars_aparecem_no_email);
			$conteudo = $body_subject['body'];
			$assunto = $body_subject['subject'];		
			
			// será o retorno da get_user()
			$user = get_responsavel_solicitacao($COD_SOLICITACAO);
			$destinatario = $user['DES_EMAIL'];
	}

	if ($COD_EMAIL == "enviar_alertas_reload" AND strlen($destinatarios) > 0)
	{

		//Consulta no Banco, a template a ser utilizada. (utilizei o código 50 sem nehum motivo específico)
		$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = 50 ";
		$rsTemplate = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
		$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
		$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
		$assunto  = $rsTemplate->Fields("NOM_TITULO");

		require("class.phpmailer.php");			

		$marcar_como_enviado = array(); //checaremos esse array no reload.php para atualizar a flag de enviado no banco
		foreach($alertas_enviar as $alerta){

			foreach($alerta as $var => $val){				
				$$var = $val;
			}	

			eval ("\$assunto = \"$assunto\";");
			eval ("\$conteudo = \"$conteudo\";");

			// ENVIO DE EMAIL
			$mail = new phpmailer();
  		        $mail->SetLanguage('br', $document_root."email/language/");
                        $mail->From     = $mail_remetente; // variável tirada do arquivo config.php
			$mail->FromName = $nom_titulo;
			if ($mail_host) {
				$mail->Host = $mail_host; 
			}	
			$mail->Mailer   = $mail_metodo;
			$mail->SMTPAuth = $mail_auth;
			$mail->Username = $mail_username;
			$mail->Password = $mail_password; 
			$mail->Body     = $mail_cabecalho. $conteudo . $mail_rodape;
			$mail->AltBody  = "HTML";
			$mail->Subject  = $assunto;
			
			
			//$destinatarios vem assim:
			//email_usuario1,nome_usuario1;email_usuario2,nome_usuario2|email_atendente1,nome_atendente1;email_atendente2,nome_atendente2

			$usuAtd = explode("|", $destinatarios); //separa usuarios de atendentes

                        foreach($usuAtd as $listaDest) {
                            $dests = explode(";", $listaDest);//separa um atd do outro
                            foreach($dests as $dest) { //separa nome do email
                              $d = explode(",", $dest);   
                              if (!in_array($d[0], $in)) {
                                
                                $in[] = $d[0];
                                $mail->AddAddress($d[0], $d[1]); //adiciona a lista de remetentes
                              }
                            }
                        }

			$mail->Send();
			if(!$mail->IsError()){
				array_push($marcar_como_enviado, $COD_ALERTA);
			} else {
                           $file = fopen($document_root.'email/mail.log', 'ab');
          		   if ($file) {
          			  $msg  = date("Y-m-d H:i:s");
          			  $msg .= "|".$mail->ErrorInfo;
          			  $msg .= "|COD_EMAIL=$COD_EMAIL\r\n";
          			  fwrite($file, $msg);
          			  fclose($file);
          		   }
                        
                        }

                        unset($in);
		}// foreach alertas

	}//if alertas
	
	/**
	 *  Envia uma mensagem ao atendente quando uma 
         * de suas solicitaço~es tiver sido enviada com
         * restrições
         *
         * @since 2008-08-14
	 */
        if ($COD_EMAIL == 'notificar_aprovacao_restricao') {

          //Consulta no Banco, a template a ser utilizada.
          $TEMPLATE = "56";
          $sql = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $TEMPLATE ";
          $rsTemplate = $conexao->Execute($sql) or die(__FILE__.' #'.__LINE__.$conexao->ErrorMsg());

			// Pega o motivo da 
			$sql2 = "SELECT DES_APONTAMENTO 
				   FROM hdk_apontamento 
				   WHERE 
				   COD_SOLICITACAO = $COD_SOLICITACAO 
				   AND COD_TIPO = 3
				   AND DES_APONTAMENTO LIKE '%Motivo:%'";
			$rsApontamento = $conexao->Execute($sql2) or die (__FILE__.' #'.__LINE__.$conexao->ErrorMsg());
			$desc = $rsApontamento->fields('DES_APONTAMENTO');
			$RESTRICOES = substr($desc, strpos($desc, 'Motivo:') + 7);
		  
          require_once('../includes/solicitacao_detalhe.php');
          $conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
          $conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));
          eval ("\$conteudo = \"$conteudo\";");
		

          $sql = "select DES_EMAIL from hdk_usuario where COD_USUARIO = ".$COD_ANALISTA;
          $rsAnalista = $conexao->Execute($sql) or die (__FILE__.' #'.__LINE__.$conexao->ErrorMsg());
		  unset($sql);
          $destinatario = $rsAnalista->Fields('DES_EMAIL');

          $assunto   = $rsTemplate->Fields("NOM_TITULO");
		  
        }
		
	/**
	 * Envia alertas quando um chamado é repassado a um usuário, foi separado do código registrar
            * a pedido da datadrome pois o cliente não usava aquela notificação, mas tinha interesse nesta .
            *
	 * @since 2008-12-09
	 */
	if ($COD_EMAIL == 'registrar_repasse') {
	
		//Consulta no Banco, a template a ser utilizada.
		$TEMPLATE = "62";
		$SQL = "SELECT * FROM hdk_template_email WHERE COD_TEMPLATE = $TEMPLATE ";
		$rsTemplate = $conexao->Execute($SQL) or die(__FILE__.' #'.__LINE__.$conexao->ErrorMsg());

		require_once('../includes/solicitacao_detalhe.php');
		$conteudo = str_replace(chr(10),"<br>",$rsTemplate->Fields("DES_TEMPLATE"));
		$conteudo = str_replace('"',"'",$rsTemplate->Fields("DES_TEMPLATE"));

		eval ("\$conteudo = \"$conteudo\";");

		//Consulta o grupo responsável pela solicitação
		$SQL = "select COD_GRUPO, COD_ANALISTA
				 from hdk_solicitacao_grupo 
				 where COD_SOLICITACAO=".$COD_SOLICITACAO."
				 AND IND_RESPONSAVEL = 1";

		$rsSolGrup = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
		$COD_GRUPO = $rsSolGrup->Fields('COD_GRUPO');
		$COD_ANALISTA = $rsSolGrup->Fields('COD_ANALISTA');
		//Se a solicitação estiver sendo passada para um grupo
		if ($COD_GRUPO){
			//Consulta os emails dos atendentes que atendem apenas a empresa do usuario
			$SQL =" SELECT 
					usuario.DES_EMAIL , 
					count(usuarioempresa.COD_EMPRESA) AS TOTAL 
					from hdk_usuario usuario, 
					hdk_usuario_grupo usuariogrupo,
					hdk_usuario_empresa usuarioempresa
					where 
					usuario.COD_USUARIO = usuariogrupo.COD_USUARIO 
					AND usuario.COD_USUARIO = usuarioempresa.COD_USUARIO
					AND usuarioempresa.COD_EMPRESA = ". $COD_EMPRESA."
					AND usuario.IND_ATIVO = 1 
					AND usuario.DES_EMAIL IS NOT NULL
					AND usuario.DES_EMAIL != '' 
					AND usuariogrupo.COD_GRUPO= ". $COD_GRUPO ."
					GROUP BY 
					usuario.DES_EMAIL, 
					usuarioempresa.COD_EMPRESA "; 								
				
			$rsDest = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			
			$destinatario = "";
			while(!$rsDest->EOF){
				if (!$destinatario){
					$destinatario = $rsDest->Fields('DES_EMAIL');
				}else{
					$destinatario .= ";".$rsDest->Fields('DES_EMAIL');
				}
				$rsDest->MoveNext();
			}
			
			$rsDest->Close(); 
		
		   // Consulta os atendentes que atendente todas as empresas	
			$SQL =" SELECT usuario.DES_EMAIL, 
					count(usuarioempresa.COD_EMPRESA) AS TOTAL 
					from 
					(
					hdk_usuario usuario, 
					hdk_usuario_grupo usuariogrupo 
					)
					LEFT JOIN 
					hdk_usuario_empresa usuarioempresa
					ON usuario.COD_USUARIO = usuarioempresa.COD_USUARIO
					AND usuarioempresa.COD_EMPRESA = ". $COD_EMPRESA ."
					where 
					usuario.COD_USUARIO = usuariogrupo.COD_USUARIO 
					AND usuario.IND_ATIVO = 1 
					AND usuario.DES_EMAIL is NOT Null 
					AND usuario.DES_EMAIL !='' 
					AND usuariogrupo.COD_GRUPO=".$COD_GRUPO ."
					GROUP BY 
					usuario.DES_EMAIL, 
					usuarioempresa.COD_EMPRESA 
					HAVING
					TOTAL = 0";				
			$rsDest = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			
			/**
			 * Evita que sejam excluídos da lista os atendentes restritos a empresa
			 * @since 2009-07-01	
			 */
			if (empty($destinatario)) {
				$destinatario = "";
			}

			while(!$rsDest->EOF){
				if (!$destinatario){
					$destinatario = $rsDest->Fields('DES_EMAIL');
				}else{
					$destinatario .= ";".$rsDest->Fields('DES_EMAIL');
				}
				$rsDest->MoveNext();
			}
			
			$rsDest->Close();			
					
		}
		//Se estiver sendo passada apenas para 1 atendente
		else{
			$SQL = "select usu.DES_EMAIL from hdk_usuario usu where COD_USUARIO=".$COD_ANALISTA;
			$rsDest = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
			$destinatario = $rsDest->Fields('DES_EMAIL');
		}		
		$assunto   = $rsTemplate->Fields("NOM_TITULO");
		eval ("\$assunto = \"$assunto\";");
		
	}			  

	if($COD_EMAIL != "enviar_alertas_reload" AND strlen($destinatario) > 0){ //se for envio de alertas o envio já foi feito

		// ENVIO DE EMAIL
		require_once("class.phpmailer.php");
		$mail = new phpmailer();
		$mail->From     = $mail_remetente; // variável tirada do arquivo config.php
		$mail->FromName = $nom_titulo;
		if ($mail_host) $mail->Host = $mail_host;
		if (isset($mail_porta) AND !empty($mail_porta) ) {
  		    $mail->Port = $mail_porta;
		}
		$mail->Mailer   = $mail_metodo;
		$mail->SMTPAuth = $mail_auth;
		$mail->Username = $mail_username;
		$mail->Password = $mail_password;
		$mail->Body     = $mail_cabecalho. $conteudo . $mail_rodape;
		$mail->AltBody  = "HTML";
		$mail->Subject  = $assunto;
		$mail->Send();	
		//Verifica se há mais de 1 endereço de email no destinatario 
		$jaExiste = array();
		if (ereg(";", $destinatario))
		{
			$email_destino = split(";",  $destinatario); 
			if (is_array($email_destino))
			{
				for($i=0; $i< count($email_destino); $i++)
				{ 
					// Se o endereço de e-mail NÃO estiver no array, envia e-mail e coloca no array
					// Se já tiver no array, não envia novamente, evitando mails duplicados
					if (!in_array($email_destino[$i], $jaExiste))
					{
						$mail->AddAddress($email_destino[$i]);
						$jaExiste[] = $email_destino[$i];
						//echo $email_destino[$i] . "<br>";
					}					
				}
			}
			else
			{
				$mail->AddAddress($email_destino);	 		
			}
		}
		else 
		{ 
			$mail->AddAddress($destinatario);	
		}
	    $mail->SetLanguage('br', $document_root."email/language/");

	    $deu = $mail->Send();
	    if(!$deu) { //algum debug dos erros
		   //$mail->SMTPDebug = true;
		   $mail->Send();
		   $file = fopen($document_root.'email/mail.log', 'ab');
		   if ($file) {
			  $msg  = date("Y-m-d H:i:s");
			  $msg .= "|".$mail->ErrorInfo;
			  $msg .= "|COD_EMAIL=$COD_EMAIL|COD_SOL=$COD_SOLICITACAO\r\n";
			  fwrite($file, $msg);
			  fclose($file);
		   }	
	    } else {
			if (defined('LOG_SENT_MAIL') AND LOG_SENT_MAIL === TRUE){ //defina 'LOG_SENT_MAIL' como true, para criar log de emails enviados com sucesso
				$file = fopen($document_root.'email/mailsent.log', 'ab');
				if ($file) {
					$msg  = date("Y-m-d H:i:s");
					$msg .= '|' . $destinatario;
					$msg .= "|COD_EMAIL=$COD_EMAIL|COD_SOL=$COD_SOLICITACAO\r\n";
					fwrite($file, $msg);
					fclose($file);
				}	
			} 
	    }
	   
	}//if
