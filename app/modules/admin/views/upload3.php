<html>
    <head

        <link rel="stylesheet" href="/<?php echo $path_default; ?>/app/themes/<?php echo $theme_default; ?>/css3-buttons.css" type="text/css"  media="screen" />

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>

        <style>
            body,form {
            	margin:0;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
            }
           
        </style>
<?php
if (!extension_loaded('gd') || !function_exists('gd_info')) {
    die("PHP GD library is NOT installed on your web server");
}
include 'includes/config/config2.php';
session_start();
$bd = new logos_model();

$path_default = $config['theme']; 
if (substr($path_default, 0, 1) != '/') {
    $path_default = '/' . $path_default;
}
define('path', $path_default);
$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}
define('DOCUMENT_ROOT', $document_root);
define('theme',$config['theme']);

require_once(SMARTY . 'Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->compile_dir = "system/templates_c/";
$smarty->config_load(DOCUMENT_ROOT . path . '/app/lang/' . $config['lang'] . '.txt', $config['lang']);
$smarty->assign('lang', $config['lang']);
$smarty->assign('pagetitle', $config['page_title']);
$langVars = $smarty->get_config_vars();
$langVars2 = $smarty->get_template_vars();

if(path == '/..'){
   $path_attach = DOCUMENT_ROOT . "/app/uploads/logos/"; 
}
else{
    $path_attach = DOCUMENT_ROOT . path . "/app/uploads/logos/";
}

if ((isset($_FILES["file"])) && ($_FILES["file"]["size"] > 0)) {
    //$name=$_FILES["file"]["name"];

    $extensao = strrchr($_FILES["file"]["name"], ".");

    //Verifica se a extensão do arquivo é diferente de PHP ou ASP
    if ((strtolower($extensao) != 'php') && (strtolower($extensao) != 'asp')) {

        $NOM_FILE = "reports_".$_FILES["file"]["name"];
        //Inseri um registro no banco para armazenar o anexo;
        
        list($realwidth, $realheight, $type, $attr) = getimagesize($_FILES["file"]["tmp_name"]);
        $height = '40';
        $width = ($height * $realwidth) / $realheight; 
        $where = 'reports';
        $upload = $bd->upload($NOM_FILE, $height, round($width), $where);

        $destino = $path_attach . $NOM_FILE;
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
        include('includes/classes/SimpleImage/SimpleImage.php');
        $image = new SimpleImage();
        $image->load($destino);
        $image->resize($width,$height);
        $image->save($destino);

        die("OK");
    }
}
?>
    </head>
    <body>
        <form action="" method="post" enctype="multipart/form-data" name="formAt">
			<input name="file" id="file" type="file" class="campo"  tabindex="-1" onchange="document.formAt.submit();"/>
        </form>
    </body>
</html>
