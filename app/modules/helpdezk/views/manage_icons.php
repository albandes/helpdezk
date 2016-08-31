<?php
    session_start();
    $bd = new manageattachments_model();

    if (!(isset($_SESSION["SES_COD_ATTACHMENT"])) || ($_SESSION["SES_COD_ATTACHMENT"] == "")){
            $_SESSION["SES_COD_ATTACHMENT"] = "0";
    }

    //Verifica se for postado para excluir um arquivo.
    if ((isset($_POST["ACAO"])) && ($_POST["ACAO"] == "excluir") && (isset($_POST["COD_ANEXO"]))){
            $COD_ANEXO = $_POST["COD_ANEXO"];

            //Consulta o nome do ANEXO.			

            $rsAnexo = $bd->searchattname($COD_ANEXO);
            $ext = strrchr($rsAnexo->Fields("FILE_NAME"), ".");

            //Excluíndo do Banco

            $Result1 = $bd->delatt($COD_ANEXO);

            //apagando o arquivo.
            $arquivo = $document_root.$path_anexo.$COD_ANEXO.$ext;
            @unlink($arquivo);

    }
    if ((isset($_FILES["file"])) && ($_FILES["file"]["size"] > 0)){
        //$name=$_FILES["file"]["name"];

        $extensao = strrchr($_FILES["file"]["name"], ".");

        //Verifica se a extensão do arquivo é diferente de PHP ou ASP
        if ((strtolower($extensao) != 'php') && (strtolower($extensao) != 'asp')){

            $NOM_FILE = $_FILES["file"]["name"];
            //Inseri um registro no banco para armazenar o anexo;

            $Result1 = $bd->saveatt($NOM_FILE);


            //Consulta o código do Anexo.			
            $rsMax = $bd->maxatt();
            $COD_ATT = $rsMax->fields["COD"];
            $destino = DOCUMENT_ROOT.path."/app/uploads/helpdezk/attachments/".$COD_ATT.$extensao;
            //Faz o copia do anexo			
            if (!is_writeable(DOCUMENT_ROOT. path .'/app/uploads/helpdezk/attachments/')) {
                    //$str = '{$smarty.config.Write_permission}'.$document_root.$path_anexo;
                    $str = 'Sem permissão de escrita '.DOCUMENT_ROOT.'path."/app/uploads/helpdezk/attachments/';
                    $msg = '<center><h6>'.$str.'</h6><input type="button" class="btn_cadastrar" onclick="javascript:history.back(1)" value="{$smarty.config.Back_btn}"></center>';
                    //logit($str, '/".path."/logs/logupload.log');
                    die($msg);
            }

            if(!@copy($_FILES["file"]["tmp_name"],$destino)) {
                    $str = 'Erro ao mover o arquivo '.$_FILES["file"]["tmp_name"].' para '.$destino;
                    $msg  = '<center><h6>'.$str.'</h6><input type="button"  class="btn_cadastrar" onclick="javascript:history.back(1)" value="Voltar"></center>';
                    //logit($str, '/".path."/logs/logupload.log');					
                   die($msg);
            }
            //Armazena o código do Anexo na Seção.			
            $_SESSION["SES_COD_ATTACHMENT"] .= ",".$COD_ATT;
            
        }
    }

    $bd = new manageattachments_model();
    $rs = $bd->searchatt();
    
    
?>
<html>
<head
    <script src="/".path."/includes/classes/jquery/jquery-1.7.1.min.js"></script>    
        
    <link rel="stylesheet" href="/".path."/app/themes/".theme."/css3-buttons.css" type="text/css"  media="screen" />
    

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  
<style>
body{
    background-color: #eee;
}
button {
	height: 22px !important;
	cursor: pointer;
}
.texto-regular{
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #000;    
}
.btn_cadastrar {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    text-decoration: none;
    border: 1px solid #888888;
    background-color:#FFFFFF;	
    background-image:url(/".path."/app/themes/".theme."/images/btn_fundo.gif);
    cursor: pointer;
}
#div-input-file{
    background:url(/".path."/app/themes/".theme."/imagens/att.png) no-repeat 100% 0px;
    *background:url(/".path."/app/themes/".theme."/imagens/att.png) no-repeat 98% 1px;
    height:24px;
    width:168px;
    margin:0px;    
    margin-top:30px;
    *margin:0px;
}
#div-input-file #file{
    opacity: 0.0;
    -moz-opacity: 0.0;
    filter: alpha(opacity=00);
    font-size:18px;
    width:175px; 
    height:22px; 
    z-index:2; 
    position:absolute
}
#div-input-falso{    
    margin-top:-29px;
    *margin-top:-27px;
}
#div-input-falso #file-falso{
    width:130px;
    height:22px;
    font-size:18px;
    font-family: Verdana;
    position: absolute; 
    *margin-top:28px; 
    font-size: 11px; 
    z-index:1; 
}
.campo{
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    width:180px;
}
#submitat{
    height:22px; 
    width:170px; 
    margin-left:-2px; 
    *margin-left:0px;
    margin-top:25px;
    *margin-top:55px;
}
</style>
    

</head>
<body>

<form action="" method="post" enctype="multipart/form-data" name="formAt">
    
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="420"><table  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top" colspan="2">
          <div id="div-input-file">    
              <span>
              <input name="file" id="file" type="file" class="campo"  tabindex="-1" onchange="document.getElementById('file-falso').value = this.value;"/>
              <div id="div-input-falso"><input type="text" class="campo" name="file-falso"  id="file-falso" /></div>
              <span>
              <button type="submit" name="Submit"  id="Submitat" class="action"><span class="label" style="z-index:1; height:18px; line-height: 8px !important; margin-left:40px;">Anexar</span></button>
              </span>
          </div>   
          </td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
</body>
</html>
