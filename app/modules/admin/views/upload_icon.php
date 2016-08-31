<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	
		<style>
			* {margin: 0; padding: 0;}
	        body{background:#EEEEEE;}
			.ok{
				font-weight: bold;
				color: #0FD053;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin-top: 2px;
			}
			.rmv{
				font-weight: bold;
				color: #FF2222;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin-top: 2px;
				text-decoration: none;
			}
			input{
				background: #FFFFFF \0/;
				font-size: 11px \0/;
				border: solid 1px #888888 \0/;
				height: 18px \0/;
			}
		</style>

	</head>
	<body>
		<?php session_start();
		$bd = new evaluation_model();
		
		unset($_SESSION['ICON']);
		
		if (!(isset($_SESSION["SES_COD_ATTACHMENT"])) || ($_SESSION["SES_COD_ATTACHMENT"] == "")) {
			$_SESSION["SES_COD_ATTACHMENT"] = "0";
		}
		
		//Verifica se for postado para excluir um arquivo.
		/*if ((isset($_POST["ACAO"])) && ($_POST["ACAO"] == "excluir") && (isset($_POST["COD_ANEXO"]))) {
			$COD_ANEXO = $_POST["COD_ANEXO"];
		
			//Consulta o nome do ANEXO.
		
			$rsAnexo = $bd -> searchattname($COD_ANEXO);
			$ext = strrchr($rsAnexo -> Fields("FILE_NAME"), ".");
		
			//Excluíndo do Banco
		
			$Result1 = $bd -> delatt($COD_ANEXO);
		
			//apagando o arquivo.
			$arquivo = $document_root . $path_anexo . $COD_ANEXO . $ext;
			@unlink($arquivo);
		
		}
		*/
		
		if ((isset($_FILES["file"])) && ($_FILES["file"]["size"] > 0)) {
			//$name=$_FILES["file"]["name"];
		
			$extensao = strtolower(strrchr($_FILES["file"]["name"], "."));
		
			//Verifica se a extensão do arquivo é diferente de PHP ou ASP
			if (($extensao != 'php') && ($extensao != 'asp')) {
		
				$NOM_FILE = $_FILES["file"]["name"];
				//Inseri um registro no banco para armazenar o anexo;
		
				//$Result1 = $bd -> saveatt($NOM_FILE);
		
				//Consulta o código do Anexo.
				//$rsMax = $bd -> maxatt();
				//$COD_ATT = $rsMax -> fields["COD"];
				
				
				if($extensao != ".gif" && $extensao != ".jpg" && $extensao != ".jpeg" && $extensao != ".png"){
					die($langVars['Invalid_extension'] . " <a href='javascript:;' title='".$langVars['Back_btn']."' onclick='window.location = window.location.href;' class='rmv' />".$langVars['Back_btn']."</a>");
				}
				
				$destino = DOCUMENT_ROOT . path . "/app/uploads/icons/" . $NOM_FILE;
				
				$smarty = $this->retornaSmarty();
				$langVars = $smarty->get_config_vars();
				
				if(file_exists($destino)){
					die($langVars['File_exist'] . " <a href='javascript:;' title='".$langVars['Back_btn']."' onclick='window.location = window.location.href;' class='rmv' />".$langVars['Back_btn']."</a>");
				}
				//Faz o copia do anexo
				if (!is_writeable(DOCUMENT_ROOT . path."/app/uploads/helpdezk/attachments/")) {
					//$str = '{$smarty.config.Write_permission}'.$document_root.$path_anexo;
					$str = 'Sem permissão de escrita ' . DOCUMENT_ROOT . path . '/app/themes/".theme."/images/';
					$msg = '<center><h6>' . $str . '</h6><input type="button" class="btn_cadastrar" onclick="javascript:history.back(1)" value="{$smarty.config.Back_btn}"></center>';
					//logit($str, '/".path."/logs/logupload.log');
					die($msg);		
				}
				if (!@copy($_FILES["file"]["tmp_name"], $destino)) {
					$str = 'Erro ao mover o arquivo ' . $_FILES["file"]["tmp_name"] . ' para ' . $destino;
					$msg = '<center><h6>' . $str . '</h6><input type="button"  class="btn_cadastrar" onclick="javascript:history.back(1)" value="Voltar"></center>';
					//logit($str, '/".path."/logs/logupload.log');
					die($msg);
				}
				
				include('includes/classes/SimpleImage/SimpleImage.php');
		        $image = new SimpleImage();
		        $image->load($destino);
		        $image->resizeToHeight(17);
		        $image->save($destino);
				//Armazena o código do Anexo na Seção.
				$_SESSION["ICON"] = $NOM_FILE;
				die("<span style='font-family: Arial; font-size: 12px;'><b>".$NOM_FILE."</b></span> <a href='javascript:;' title='Delete' onclick='window.location = window.location.href;' class='rmv' />X</a>");
		
			}
		}
		
		$bd = new evaluation_model();
		$rs = $bd -> searchatt();
		?>

		<form action="" method="post" enctype="multipart/form-data" name="formAt">
			<input name="file" id="file" type="file" class="campo"  tabindex="-1" onchange="document.formAt.submit();"/>
		</form>
	</body>
</html>
