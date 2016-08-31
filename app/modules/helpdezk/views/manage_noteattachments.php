<html>
    <head>
        <script type="text/javascript" src="<?php echo path; ?>/includes/classes/jquery/jquery-1.7.1.min.js"></script>
        <link rel='stylesheet' type='text/css' href='<?php echo path; ?>/app/themes/<?php echo theme; ?>/style.css' />
<?php
unset($COD_NOTE_ATT);

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
$theme_default = $this->getConfig("theme");
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
        $path_attach = DOCUMENT_ROOT . "/app/uploads/helpdezk/noteattachments/";
   }
}
else{
   if($custom_attach_path){
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
   }
   else{
        $path_attach = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/noteattachments/";
   }
}


if ((isset($_FILES["noteattch"])) && ($_FILES["noteattch"]["size"] > 0)) {
    //$name=$_FILES["file"]["name"];

    $extensao = strrchr($_FILES["noteattch"]["name"], ".");

    //Verifica se a extensão do arquivo é diferente de PHP ou ASP
    if ((strtolower($extensao) != 'php') && (strtolower($extensao) != 'asp')) {

        $NOM_FILE = $_FILES["noteattch"]["name"];
        //Inseri um registro no banco para armazenar o anexo;

        $Result1 = $bd->savenoteatt($NOM_FILE);


        //Consulta o código do Anexo.			
        $rsMax = $bd->maxnoteatt();
        $COD_NOTE_ATT = $rsMax->fields["cod"];
        
        $destino = $path_attach . $COD_NOTE_ATT . $extensao;
        //Faz o copia do anexo			
        if (!is_writeable($path_attach)) {
            //$str = '{$smarty.config.Write_permission}'.$document_root.$path_anexo;
            $str = 'Sem permissão de escrita ' . $path_attach;
            $msg = '<center><h6>' . $str . '</h6><input type="button" class="btn_cadastrar" onclick="javascript:history.back(1)" value="{$smarty.config.Back_btn}"></center>';
            //logit($str, '/".path."/logs/logupload.log');
            die($msg);
        }

        if (!@copy($_FILES["noteattch"]["tmp_name"], $destino)) {
            $str = 'Erro ao mover o arquivo ' . $_FILES["noteattach"]["tmp_name"] . ' para ' . $destino;
            $msg = '<center><h6>' . $str . '</h6><input type="button"  class="btn_cadastrar" onclick="javascript:history.back(1)" value="Voltar"></center>';
            //logit($str, '/".path."/logs/logupload.log');					
            die($msg);
        }
        //Armazena o código do Anexo na Seção.
       
        unset($inicio);
       $final = "<div style='line-height:4px; margin-left:6px; height:12px;'><b>$NOM_FILE <img src='$path_default/app/themes/$theme_default/images/excluir.jpg' border='0' onclick=document.getElementById('formNAt').submit(); style='cursor:pointer; margin-top:4px;  position:relative;'/></b></div>";
    }
}else{
    if(isset($_POST['destino'])){
        //se nao usou o anexo no apont, remove do disco para nao ficar perdido
        unlink($_POST['destino']);
    }
    $inicio=true;
    unset($final);}
    
?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body id="upload-anexo">
        <form action="" method="post" enctype="multipart/form-data" name="formNAt"  accept-charset=utf-8  id="formNAt">    
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="820"><table  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top" colspan="2">
                                    <?php if(isset($inicio)){ ?>
                                    <div id="div-input-file" >    
                                        <span>
                                            <input name="noteattch" id="file" type="file" style="height:19px; cursor:text;" tabindex="-1" onchange="document.getElementById('file-falso').value = this.value; document.getElementById('formNAt').submit(); "/>
                                            <div id="div-input-falso"><input type="text" class="campo" name="file-falso"  id="file-falso"/></div>              
                                        </span>
                                    </div>
                                    <?php } ?>
                                    <div id='final' >
                                        <?php echo $final ?>
                                    </div>
                                </td>
                                
                            </tr>
                        </table></td>
                </tr>
            </table>
            <div id="esc"></div>
            <input  type="hidden" name="COD_NOTE_ATT2" id="COD_NOTE_ATT2" value="<?php echo $COD_NOTE_ATT; ?>"/>            
            <input  type="hidden" name="destino" id="destino" value="<?php echo $destino; ?>"/>
        </form>
    </body>
</html>
