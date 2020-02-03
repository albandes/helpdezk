<?php
session_start();
//Connection statement
require_once('../../includes/config.php');
require_once('../../includes/valida_sessao.php');
require_once('../../Connections/conexao.php');
require_once('../../includes/funcao.php');
require_once("../../includes/lang_comum/$_idioma_/comum.inc");
require_once("lang/$_idioma_/atendente_temas.inc");

/**
 * Impede que um atendente/usuario altere seu tema
 * @since 20090810
 */
 if ($_SESSION['SES_COD_TIPO'] < 2) {
	echo "<h1>P&aacute;gina permitida apenas para atendentes e administradores</h1>";
	 exit(1);
 }

if (isset($_GET["COD_USUARIO"]))
	$COD_USUARIO = $_GET["COD_USUARIO"];

if (isset($_POST["idtema"]) AND $_POST["idtema"] != -1){
	$COD_USUARIO = $_POST["COD_USUARIO"];
	
	$ID_TEMA = $_POST["idtema"];
	$SQL = "SELECT * FROM dsh_tbtema_has_hdk_usuario WHERE hdk_usuario_COD_USUARIO = ".$COD_USUARIO;

	$rsQuery = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
	if($rsQuery->RecordCount() > 0){
	
		$SQL = "UPDATE dsh_tbtema_has_hdk_usuario SET dsh_tbtema_idtema = $ID_TEMA WHERE hdk_usuario_COD_USUARIO = $COD_USUARIO";
		
		$sql_result = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
		
		if (!$sql_result) {
				
				mensagemjs("Não foi possível alterar o tema");
			}
	}

}

//SELECIONANDO OS TEMAS DISPONÍVEIS

$SQL = 	"
		select
		   a.idtema,
		   a.nome,
		   a.pasta
		from dsh_tbtema a
		where a.idtema not in(select
								 dsh_tbtema_idtema
							  from dsh_tbtema_has_hdk_usuario
							  where hdk_usuario_COD_USUARIO = $COD_USUARIO)
		";

$rsTema = $conexao->Execute($SQL) or die($conexao->ErrorMsg());


?>
<html>
<head>
<link href="../../style.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body bgcolor="#E5E5E5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="00">
<form name="form1" method="post" action="atendente_temas.php">
  <table width="100%"  border="0" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
      <td height="22" class="txtLabel">Tema:
        <input name="COD_USUARIO" type="hidden" id="COD_USUARIO" value="<? echo $COD_USUARIO ?>"></td>
    </tr>
  </table>
  <table  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><select name="idtema" class="campo" id="COD_GRUPO"  style="width:200px">
		  <?php 
		if ($rsTema->RecordCount() > 0) {
			echo '<option value="-1">Escolha um tema</option>';
		} 	
			while (!$rsTema->EOF) { ?>
          <option value="<? echo $rsTema->Fields("idtema") ?>"><? echo $rsTema->Fields("nome") ?></option>
          <? $rsTema->MoveNext(); } ?>
        </select> </td>
      <td><input name="Submit2" type="submit" class="btn_verde" value="<?=$l_atg["btn_adicionar"]?>" onClick="if (document.form1.COD_GRUPO.options.value == ''){ return false; } "></td>
    </tr>
  </table>
</form>
</body>
</html>