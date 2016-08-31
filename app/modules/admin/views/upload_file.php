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
    	
    <?php
		session_start();
		error_reporting(0);
		unset($_SESSION['filename']);
		unset($_SESSION['tempname']);
		$bd = new downloads_model();
		if (!(isset($_SESSION["SES_COD_ATTACHMENT"])) || ($_SESSION["SES_COD_ATTACHMENT"] == "")) {
		    $_SESSION["SES_COD_ATTACHMENT"] = "0";
		}		
		
		if ((isset($_FILES["file"])) && ($_FILES["file"]["size"] > 0)) {
		    //$name=$_FILES["file"]["name"];
		    $extensao = strrchr($_FILES["file"]["name"], ".");
		
		    //Verifica se a extensão do arquivo é diferente de PHP ou ASP
		    if ((strtolower($extensao) != 'php') && (strtolower($extensao) != 'asp')) {
		
		        $NOM_FILE = $_FILES["file"]["name"];
		        //Inseri um registro no banco para armazenar o anexo;
		
		        $rsMax = $bd->maxfile();
		        $COD_ATT = $rsMax->fields["COD"];
		        if($COD_ATT == NULL){
		            $COD_ATT = 0;
		        }
				
				
				$id = $this->getParam('id');
				
				if($id != "null")
					$COD_ATT = $id;
				
				
		        $destino = DOCUMENT_ROOT . path . "/app/uploads/files/" . $COD_ATT . $extensao;
				$path = DOCUMENT_ROOT . path . "/app/uploads/files/";
				$dh = opendir($path); 
				while (false !== ($filename = readdir($dh))) { 
					if(is_file($path.$filename)){
						if(preg_replace('#(\.[^\.]*)$#','',$filename) == $COD_ATT){
							unlink($path.$filename); 
						}
					}
				}
				
		//		//Faz o copia do anexo
		        if (!is_writeable(DOCUMENT_ROOT . path . "/app/uploads/files/")) {
		            $str = 'Sem permissão de escrita ' . DOCUMENT_ROOT . path . '/app/themes/".theme."/images/';
		            $msg = '<center><h6>' . $str . '</h6><input type="button" class="btn_cadastrar" onclick="javascript:history.back(1)" value="{$smarty.config.Back_btn}"></center>';
		            //logit($str, '/".path."/logs/logupload.log');
		            die($msg);
		        }
		        if (!@copy($_FILES["file"]["tmp_name"], $destino)) {
		            $str = 'Erro ao mover o arquivo ' . $_FILES["file"]["tmp_name"] . ' para ' . $destino;
		            $msg = '<center><h6>' . $str . '</h6><input type="button"  class="btn_cadastrar" onclick="javascript:history.back(1)" value="{$smarty.config.Back_btn}"></center>';
		            //logit($str, '/".path."/logs/logupload.log');
		            die($msg);
		        }
				
				if($id != "null")
					$bd->updateFilename($_FILES["file"]["name"], $id, $COD_ATT . $extensao);
				
		        //Armazena o código do Anexo na Seção.
		        $_SESSION["filename"] = $COD_ATT . $extensao;
		        $_SESSION["tempname"] = $_FILES["file"]["name"];
				
				die("<span class='ok'>OK</span> - <a href='javascript:;' title='Delete' onclick='window.location = window.location.href;' class='rmv' />X</a>");
		    }
		}
	?>
    	
        <form action="" method="post" enctype="multipart/form-data" name="formAt">
			
			<input name="file" id="file" type="file" class="campo"  tabindex="-1" onchange="document.formAt.submit();"/>
        </form>
    </body>
</html>
