<?php
session_start();
$bd = new manageattachments_model();
$path_default = $this->getConfig("path_default");
if (substr($path_default, 0, 1) != '/') {
    $path_default = '/' . $path_default;
}
define('path', $path_default);
$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}
define('DOCUMENT_ROOT', $document_root);
define('theme',$this->getConfig("theme"));

require_once(SMARTY . 'Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->compile_dir = "system/templates_c/";
$lang_default = $this->getConfig("lang");
$smarty->config_load(DOCUMENT_ROOT . path . '/app/lang/' . $lang_default . '.txt', $lang_default);
$smarty->assign('lang', $lang_default);
$smarty->assign('pagetitle', $this->getConfig("page_title"));
$langVars = $smarty->get_config_vars();
$langVars2 = $smarty->get_template_vars();

if(path == '/..'){
   if($custom_attach_path){
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
   }
   else{
        $path_attach = DOCUMENT_ROOT . "/app/uploads/helpdezk/attachments/";
   }
}
else{
   if($custom_attach_path){
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
   }
   else{
        $path_attach = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/attachments/";
   }
}

if (!(isset($_SESSION["SES_COD_ATTACHMENT"])) || ($_SESSION["SES_COD_ATTACHMENT"] == "")) {
    $_SESSION["SES_COD_ATTACHMENT"] = "0";
}

//Verifica se for postado para excluir um arquivo.
if ((isset($_POST["ACAO"])) && ($_POST["ACAO"] == "excluir") && (isset($_POST["COD_ANEXO"]))) {
    $COD_ANEXO = $_POST["COD_ANEXO"];

    //Consulta o nome do ANEXO.			

    $rsAnexo = $bd->searchattname($COD_ANEXO);
    $ext = strrchr($rsAnexo->Fields("FILE_NAME"), ".");

    //Excluíndo do Banco

    $Result1 = $bd->delatt($COD_ANEXO);

    //apagando o arquivo.
    $arquivo = $path_attach . $COD_ANEXO . $ext;
    @unlink($arquivo);
}
if ((isset($_FILES["file"])) && ($_FILES["file"]["size"] > 0)) {
    //$name=$_FILES["file"]["name"];

    $extensao = strrchr($_FILES["file"]["name"], ".");

    //Verifica se a extensão do arquivo é diferente de PHP ou ASP
    if ((strtolower($extensao) != 'php') && (strtolower($extensao) != 'asp')) {

        $NOM_FILE = $_FILES["file"]["name"];
        //Inseri um registro no banco para armazenar o anexo;

        $Result1 = $bd->saveatt($NOM_FILE);


        //Consulta o código do Anexo.			
        $rsMax = $bd->maxatt();
        $COD_ATT = $rsMax->fields["cod"];
        $destino = $path_attach . $COD_ATT . $extensao;
        //Faz o copia do anexo			
        if (!is_writeable($path_attach)) {
            //$str = '{$smarty.config.Write_permission}'.$document_root.$path_anexo;
            $str = 'Sem permissão de escrita ' . $path_attach;
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
        //Armazena o código do Anexo na Seção.			
        $_SESSION["SES_COD_ATTACHMENT"] .= "," . $COD_ATT;
    }
}

$bd = new manageattachments_model();
$rs = $bd->searchatt();
?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href='<?php echo path; ?>/includes/css/structure.css' />
        <link rel='stylesheet' type='text/css' href='<?php echo path; ?>/app/themes/<?php echo theme; ?>/style.css' />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>


    </head>
    <body id="upload-anexo">
        <form action="" method="post" enctype="multipart/form-data" name="formAt">    
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="420"><table  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top" colspan="2">
                                    <div id="div-input-file">    
                                        <span>
                                            <input name="file" id="file" type="file" style="height:19px; cursor:text;" tabindex="-1" onchange="document.getElementById('file-falso').value = this.value;"/>
                                            <div id="div-input-falso"><input type="text" class="campo" name="file-falso"  id="file-falso" /></div>              
                                        </span>
                                    </div>   
                                    <button type="submit" name="Submit"  id="Submitat" class="gradiente">
                                        <span class="label"><?php echo $langVars['Attach']; ?></span>
                                    </button>
                                </td>
                            </tr>
                            <tr >
                                <td  height="20"colspan="2"><span class="titulo alignLeft ml5"><? echo $langVars['Attached_files']; ?></span></td>          
                            </tr>
                            <tr>
                                <td>
                                    <select name="COD_ANEXO" class="campo_apont ml5" size="3" style="width:170px; height:45px;" tabindex="-1">
                                        <?php while (!$rs->EOF) { ?>
                                            <option value="<?php echo $rs->fields['idrequest_attachment'] ?>"><?php echo $rs->fields['file_name'] ?></option>
                                            <?php $rs->MoveNext();
                                        } ?>
                                    </select></td>
                                <td valign="top"><input name="ACAO" type="hidden" id="ACAO3">
                                    <button type="submit" name="Submit3" class="action gradiente btnRemoverFile"  onClick="document.formAt.ACAO.value='excluir'" style="width:60px; margin-left:1px; margin-top:-0.5px;"><span class="label"><?php echo $langVars['Remove']; ?></span></button>
                                </td>
                            </tr>
                        </table></td>
                </tr>
            </table>
        </form>
    </body>
</html>
