<?php
session_start();
//Connection statement
require_once('../../includes/config.php');
require_once('../../includes/valida_sessao.php');
require_once('../../Connections/conexao.php');
require_once('../../includes/funcao.php');
require_once("../../includes/lang_comum/$_idioma_/comum.inc");
require_once("lang/$_idioma_/atendente_widget.inc");
//error_reporting(E_ALL);
/**
 * Impede que um atendente/usuario altere seu dashboard
 * @since 20090810
 */
 
 //print_r($_SESSION);
 //print_r($_GET);
 if ($_SESSION['SES_COD_TIPO'] < 2) {
	echo "<h1>P&aacute;gina permitida apenas para atendentes e administradores</h1>";
	 exit(1);
 }

if (isset($_GET["COD_USUARIO"]))
	$COD_USUARIO = $_GET["COD_USUARIO"];

if (isset($_GET["OPCAO"])){ // ------------- EXCLUSÃO -----------------
	$ID_WIDGET = $_GET["idwidget"];
	$conexao->StartTrans();
	$SQL = "
			DELETE
			FROM dsh_tbwidget_has_dsh_tbwidgetusuario
			WHERE dsh_tbwidget_idwidget = ".$_GET["idwidget"]."
				 AND dsh_tbwidgetusuario_idwidgetusuario = (select
															   idwidgetusuario
															from dsh_tbwidgetusuario
															where idusuario = ".$COD_USUARIO.")	
			";												
	$sql_result = $conexao->Execute($SQL);
	if(!$sql_result){
		mensagemjs("Não foi possível excluir a relação entre o atendente e o dashboard", $SQL);
	}
	// -- Acertar o campo widgets
	$sql =  "
			select
			   nome
			from dsh_tbwidget
			where idwidget = $ID_WIDGET
			" ;
	$rs = $conexao->Execute($sql) ;
	if (!$rs) {$conexao->Failtrans(); $conexao->CompleteTrans(); mensagemjs("Erro: " . $sql ); die(); }

	$wnome = $rs->Fields("nome");
		
	$sql = 	"
			select
			   widgets
			from dsh_tbwidgetusuario
			where idusuario = ".$_SESSION['SES_COD_USUARIO']."
			" ;
	$rs = $conexao->Execute($sql) or die($conexao->ErrorMsg());
	if (!$rs) {$conexao->Failtrans(); $conexao->CompleteTrans(); mensagemjs("Erro: " . $sql ); die(); }
	
	$widgets = unserialize(stripslashes($rs->Fields("widgets")));	
	$novo = AcertaArray($widgets,$wnome);

	$SQL =  "
			update 
				dsh_tbwidgetusuario
			set 
				widgets = '".serialize($novo)."'
			where 
				idusuario = ".$COD_USUARIO."
			";
	$rs = $conexao->Execute($SQL) ;
	if (!$rs) {	$conexao->Failtrans(); $conexao->CompleteTrans(); mensagemjs("Erro: " . $SQL ); die(); }
	
	$conexao->CompleteTrans();
	

}
if (isset($_POST["idwidget"]) AND $_POST["idwidget"] != -1)
{
	$COD_USUARIO = $_POST["COD_USUARIO"];
	$ID_WIDGET = $_POST["idwidget"];

	if ($ID_WIDGET == 0) {
		/*
		Fazer a gravação de todos 
		*/
	} else { 
		
		$query	= 	"
					select
                        idwidgetusuario
                    from dsh_tbwidgetusuario
                    where idusuario = ".$COD_USUARIO."		
					";
		$rs_tbwidgetusuario = $conexao->Execute($query);
		
		if 	($rs_tbwidgetusuario->RecordCount() == 0) 
		{
			
			$sql =  "
				select
				   nome
				from dsh_tbwidget
				where idwidget = $ID_WIDGET
				" ;
			$rs = $conexao->Execute($sql) ;
			if (!$rs) {
				mensagemjs("Erro: " . $SQL );	die();	
			}
			
			$wnome = $rs->Fields("nome");
			$widgets = array();
			$widgets[0][$wnome] = 0;
			$widgets[1] = array();
			$widgets[2] = array();
			
			$SQL = "INSERT  INTO dsh_tbwidgetusuario (idusuario,widgets)
					VALUES (
					".$COD_USUARIO.",
					'".serialize($widgets)."'
					)";
			
			$conexao->StartTrans();		
			$rs = $conexao->Execute($SQL);
			if (!$rs) {
				$conexao->Failtrans();
				$conexao->CompleteTrans();
				mensagemjs("Erro: " . $SQL );	die();	
			}
			$idwidgetusuario = $conexao->Insert_ID() ;
		} 
		else
		{
			$idwidgetusuario = $rs_tbwidgetusuario->Fields("idwidgetusuario");
			
			$conexao->StartTrans();	
			
			$sql =  "
				select
				   nome
				from dsh_tbwidget
				where idwidget = $ID_WIDGET
				" ;
			$rs = $conexao->Execute($sql) ;
			if (!$rs) {	$conexao->Failtrans(); $conexao->CompleteTrans(); mensagemjs("Erro: " . $sql ); die(); }
		
			$wnome = $rs->Fields("nome");
		
			$sql = 	"
				select
				   widgets
				from dsh_tbwidgetusuario
				where idusuario = ".$_SESSION['SES_COD_USUARIO']."
				" ;
			$rs = $conexao->Execute($sql) or die($conexao->ErrorMsg());
			
			$widgets = unserialize(stripslashes($rs->Fields("widgets")));
			$widgets[0][$wnome] = 0;	
			
			$SQL =  "
					update 
						dsh_tbwidgetusuario
					set 
						widgets = '".serialize($widgets)."'
					where 
						idusuario = ".$COD_USUARIO."
					";
			$rs = $conexao->Execute($SQL) ;
			if (!$rs) {	$conexao->Failtrans(); $conexao->CompleteTrans(); mensagemjs("Erro: " . $SQL ); die(); }
			
		}	
			
		$SQL = "INSERT INTO dsh_tbwidget_has_dsh_tbwidgetusuario (dsh_tbwidget_idwidget, dsh_tbwidgetusuario_idwidgetusuario)
				VALUES (
				".$ID_WIDGET."
				,".$idwidgetusuario."
				)";
		$rs = $conexao->Execute($SQL) ;
		if (!$rs) {	$conexao->Failtrans(); $conexao->CompleteTrans(); mensagemjs("Erro: " . $SQL ); die(); }
		

		$conexao->completeTrans();	
	}	
}

//Seleciona os widgets que estão disponíveis para cadastro 
$SQL = "
		select
		   k.idwidget,
		   k.descricao
		from dsh_tbwidget k
		where k.idwidget not in(SELECT
								   a.idwidget
								FROM dsh_tbwidget a,
								   dsh_tbwidget_has_dsh_tbwidgetusuario b,
								   dsh_tbwidgetusuario c
								WHERE a.idwidget = b.dsh_tbwidget_idwidget
									 and b.dsh_tbwidgetusuario_idwidgetusuario = c.idwidgetusuario
									 AND c.idusuario = ".$COD_USUARIO.")
		order by k.descricao							 
		";
$rsDisponivel = $conexao->Execute($SQL) ;
if(!$rsDisponivel) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $conexao->ErrorMsg() . '<br> SQL: ' . $SQL);

//SELECIONA OS WIDGETS QUE O ATENDENTE POSSUI CADASTRADOS 
$query 	=	"
			SELECT
				a.idwidget,
				a.descricao
			FROM dsh_tbwidget a,
				dsh_tbwidget_has_dsh_tbwidgetusuario b,
				dsh_tbwidgetusuario c
			WHERE a.idwidget = b.dsh_tbwidget_idwidget
				AND b.dsh_tbwidgetusuario_idwidgetusuario = c.idwidgetusuario
				AND c.idusuario = ".$COD_USUARIO."
			";
$rsWidgets = $conexao->Execute($query) or die($conexao->ErrorMsg());			

function AcertaArray($widgets,$nome) {
	$novo = array();
	for($i=0;$i<3;$i++) {
		foreach ($widgets[$i] as $key=>$val) 
		{
			if($key != $nome) {
				$novo[$i][$key] = $val;
			}	
			
		}
	}
	return $novo;	
}  
?>
<html>
<head>
<link href="../../style.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body bgcolor="#E5E5E5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="00">
<form name="form1" method="post" action="atendente_widgets.php">
  <table width="100%"  border="0" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
      <td height="22" class="txtLabel"><?=$l_atg["titulo"]?>
        <input name="COD_USUARIO" type="hidden" id="COD_USUARIO" value="<? echo $COD_USUARIO ?>"></td>
    </tr>
  </table>
  <table  border="0" cellspacing="0" cellpadding="0">
    <tr>
	
      <td><select name="idwidget" class="campo" id="COD_GRUPO"  style="width:200px">
		  <?php 
		if ($rsDisponivel->RecordCount() > 0) {
			echo '<option value="-1">:: Selecione uma opção ::</option>';
			// echo '<option value="0">Todos</option>';
		} 	
		while (!$rsDisponivel->EOF) { ?>
          <option value="<? echo $rsDisponivel->Fields("idwidget") ?>"><? echo $rsDisponivel->Fields("descricao") ?></option>
          <? $rsDisponivel->MoveNext(); 
		} ?>
        </select> </td>
      <td><input name="Submit2" type="submit" class="btn_verde" value="<?=$l_atg["btn_adicionar"]?>" onClick="if (document.form1.COD_GRUPO.options.value == ''){ return false; } "></td>
    </tr>

  </table>
  <table width="100%"  border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
    <? while (!$rsWidgets->EOF) { ?>
    <tr bgcolor="#E7E7E7">
      <td width="20"><a href="?idwidget=<? echo $rsWidgets->Fields("idwidget") ?>&COD_USUARIO=<? echo $COD_USUARIO ?>&OPCAO=excluir"><img src="../../images/btn_excluir.gif" width="18" height="18" border="0"></a></td>
      <td class="texto11"><? echo $rsWidgets->Fields("descricao") ?></td>
    </tr>
    <? $rsWidgets->MoveNext(); } ?>
  </table>
</form>
</body>
</html>