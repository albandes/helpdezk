<?php
session_start();
//Connection statement
require_once('../../includes/config.php');
require_once('../../includes/valida_sessao.php');
require_once('../../Connections/conexao.php');
require_once('../../includes/funcao.php');
require_once("../../includes/lang_comum/$_idioma_/comum.inc");
require_once("lang/$_idioma_/cadastrar_widget.inc");


/**
 * Atendentes nao alteram seu modo de login
 * @since 20091218
 */ 
 
/* 
if ($_SESSION['SES_COD_TIPO'] == 4) { 
	$tipoLogin = " ,TIP_LOGIN = '".$_POST["TIP_LOGIN"]."' ";
	$disButton = "";
} else {
	$tipoLogin = "";
    $disButton = 'disabled="disabled"';
}

*/

if (isset($_POST['COD_USU'])){

	$IND_USUARIO_VIP      = 0;
	if (isset($_POST["IND_USUARIO_VIP"]))
		$IND_USUARIO_VIP      = $_POST["IND_USUARIO_VIP"];
		
	$IND_ATIVO      = 0;
	if (isset($_POST["IND_ATIVO"]))
		$IND_ATIVO      = $_POST["IND_ATIVO"];

	$VAL_HORA       = "NULL";
	if ($_POST["VAL_HORA"] != ""){
		$VAL_HORA = str_replace(",",".",$_POST["VAL_HORA"]);
		$VAL_HORA = (float) $VAL_HORA;
	}
	
	$VAL_HORA_EXTRA = "NULL";	
	if ($_POST["VAL_HORA_EXTRA"] != ""){
		$VAL_HORA_EXTRA = str_replace(",",".",$_POST["VAL_HORA_EXTRA"]);
		$VAL_HORA_EXTRA = (float) $VAL_HORA_EXTRA;
	}

			
// se o usuário for 2 ou 4 (atendente ou adm), verifica qual é o tipo da empresa a ser cadastrada
// para ele
if ($_POST["COD_TIPO"] == 2 || $_POST["COD_TIPO"] == 4){ 
	$SQL = "select emp.COD_TIPO
			from hdk_empresa emp
			where emp.COD_EMPRESA = ".$_POST["COD_EMPRESA"];
	
	$rsTipoEmpresa = $conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());
	$TipoEmpresa = $rsTipoEmpresa->Fields("COD_TIPO");

	// se o tipo for 1 conta quantos usuários adm ou atendente existem 
	// em empresas desse tipo
	if ($TipoEmpresa == 1){
		$SQL = "select count(usu.COD_USUARIO) as QTD_USU
				from hdk_usuario usu, hdk_empresa emp
				where
					emp.COD_EMPRESA = usu.COD_EMPRESA and
					emp.COD_TIPO = 1 and
					usu.COD_TIPO in(2,4)";
	}
	// se a empresa for 2, verifica qtos usuarios terceiros existem
	else if ($TipoEmpresa == 2) {		
		$SQL = "select count(usu.COD_USUARIO) as QTD_USU
				from hdk_usuario usu, hdk_empresa emp
				where
					emp.COD_EMPRESA = usu.COD_EMPRESA and
					emp.COD_TIPO = 2 and
					usu.COD_TIPO in(2,4) and
					IND_ATIVO = 1";
	}
	//echo $SQL;return false;
	$rsQtdUsu = $conexao->Execute($SQL) or die(ErrorMsg());
	$QtdUsu = $rsQtdUsu->Fields("QTD_USU");

	//echo $QtdUsu . " - ". licenca_op;return false;
	if (licenca_op && $QtdUsu >= licenca_op){
		if ($TipoEmpresa == 1){
			?>
			<script language="javascript">
				alert("<?php echo "{$l_ate["msg"]["nr_limite_licenca"]} ".licenca_op?>");
				window.close();				
			</script>
			<?
			return false;
		}else if ($TipoEmpresa == 2 && $QtdUsu > licenca_op){
			$IND_ATIVO = 0;
		}
	}
	
} //	if ($_POST["COD_TIPO"] == 2 || $_POST["COD_TIPO"] == 4){

$COD_OPERADOR = 0;
if (isset($_POST["COD_OPERADOR"]) && $_POST["COD_OPERADOR"]){
	$COD_OPERADOR = $_POST["COD_OPERADOR"];
}


// verifica se não está sendo alterado com um cod_operador ou login já existente
$msg = "";
	if ($_POST["DES_LOGIN"]){
		$SQL =" SELECT COD_USUARIO FROM hdk_usuario WHERE 
				DES_LOGIN='" . $_POST["DES_LOGIN"] . "' and
				COD_USUARIO <> ". $_POST["COD_USU"];
		$rsUsuario = $conexao->Execute($SQL);		
		if(!$rsUsuario->EOF){
			$msg = $l_atc["login_ja_existe"];
		}
	} 
	if ($_POST["COD_OPERADOR"]){
		$SQL =" SELECT COD_USUARIO FROM hdk_usuario WHERE 
				COD_OPERADOR =".$_POST["COD_OPERADOR"]." and
				COD_USUARIO <> ". $_POST["COD_USU"];
		$rsUsuario = $conexao->Execute($SQL);
		if(!$rsUsuario->EOF){
			$msg = $l_atc["codOp_ja_existe"];
		}		
	}



if ($msg){
	?>
	<script type="text/javascript">
		alert("<?= $msg?>");
	</script>
	<?
}else{			
         /**
          *   array de codigos de usuários cujo username e senha 
          * nõa podem ser alterado por um usuário com licenca demo
          */
	if (!isset($nao_alterar_dados)) {
		$nao_alterar_dados = array();
	}
	if (!in_array($_POST["COD_USU"], $nao_alterar_dados)) {
           $extras = "DES_LOGIN  	= '{$_POST["DES_LOGIN"]}',
		      DES_SENHA   	= '{$_POST["DES_SENHA"]}', ";
        } else {
          $extras = '';
        }

	$conexao->StartTrans();

	/**
	 * Verifica se a empresa foi alterada para alterar também esse código nas solicitaões do usuário (não sei pq está repetido lá...)
	 * @since 20090215
	 */
	$uCod = (int)trim($_POST["COD_USU"]);
	$oldEmp = $conexao->GetOne('SELECT COD_EMPRESA FROM hdk_usuario WHERE COD_USUARIO = ' . $uCod);
	if (!$oldEmp OR $oldEmp == null) {
		hddie('Falha ao resgatar o código da empresa do usuário. ' . __FILE__ . '::' . __LINE__);
	}
	$newEmp = (int)trim($_POST["COD_EMPRESA"]);
	if ($oldEmp != $newEmp) {
		$sql = 'UPDATE hdk_solicitacao SET COD_EMPRESA = ' .  $newEmp . ' WHERE COD_USUARIO = ' . $uCod;
		if (!$conexao->Execute($sql) ) {
			hddie('Falha ao atualizar as código da empresa nas solicitações do usuário. ' . __FILE__ . '::' . __LINE__);
		} 				
	}


	$SQL = "Update hdk_usuario SET $extras
				 NOM_USUARIO  	= '".$_POST["NOM_USUARIO"]."'
				,DES_EMAIL   	= '".$_POST["DES_EMAIL"]."'
				,COD_TIPO  	 	= ".$_POST["COD_TIPO"]."
				,COD_EMPRESA 	= ".$_POST["COD_EMPRESA"]."
				,NUM_RAMAL		= '".$_POST["NUM_RAMAL"]."'
				,NUM_TELEFONE	= '".$_POST["NUM_TELEFONE"]."'
				,NUM_CELULAR	= '".$_POST["NUM_CELULAR"]."'
				,IND_USUARIO_VIP= ".$IND_USUARIO_VIP."
				,IND_ATIVO		= ".$IND_ATIVO."
				,COD_CENTRO_CUSTO = '".$_POST["COD_CENTRO_CUSTO"]."'
				,VAL_HORA = ".$VAL_HORA."
				,VAL_HORA_EXTRA = ".$VAL_HORA_EXTRA."
				,COD_PERMISSAO_GRUPO = ".$_POST["COD_PERMISSAO_GRUPO"]."
				,COD_OPERADOR = '".$COD_OPERADOR."'
				$tipoLogin
				WHERE COD_USUARIO =".$_POST["COD_USU"] ;


	$conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());
	
#####
		//Altera a tabela de atendentes do chat, se ela existir
		if (in_array('hcl_operators', $conexao->MetaTables('TABLES'))) {
			if ($_POST['COD_TIPO'] == 1) { //se o atendente agora é um usuário
				$sql = "DELETE FROM hcl_assigns WHERE operatorid = ".$_POST['COD_USU'];
				$sql_result = $conexao->Execute($sql);
				if (!$sql_result) {
					$conexao->FailTrans();
					$conexao->CompleteTrans();
					mensagemjs("Falha ao excluir os relacionamentos do operator com os grupos de atendimento no chat");
					die;
				} 
				$sql = "DELETE FROM hcl_operators WHERE id = ".$_POST['COD_USU']; echo $sql;
				$sql_result = $conexao->Execute($sql);
				if (!$sql_result) {
					$conexao->FailTrans();
					$conexao->CompleteTrans();
					mensagemjs("Falha ao excluir o operador da tebela de atendentes do chat");
					die;
				} 				
			} else {
				$level = $_POST["COD_TIPO"] == "2" ? "1" : "0";
				$senha = md5($_POST['DES_SENHA']);
				$sql  = "UPDATE hcl_operators SET ";
				$sql .= "username = '".$_POST['DES_LOGIN']."', ";
				$sql .= "password = '$senha', ";
				$sql .= "lastname = '".$_POST['NOM_USUARIO']."', ";
				$sql .= "email = '".$_POST['DES_EMAIL']."', ";
				$sql .= "level = $level "; 
				$sql .= "WHERE id = ".$_POST['COD_USU']; 
				$sql_result = $conexao->Execute($sql);

				if(!$sql_result) {

					$conexao->FailTrans();
					$conexao->CompleteTrans();
					mensagemjs("Não foi possível atualizar os dados do atendente na tabela de atendentes do chat.<br>Motivo: ".$conexao->ErrorMsg());
					die;
				}			
			} 			
		}//if	
#####	
	
	
	$SQL = "DELETE FROM hdk_usuario_departamento WHERE COD_USUARIO = ".$COD_USU ;
	$conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());

		if ($_POST["COD_DEPARTAMENTO"] != ""){
			$SQL2 = "INSERT INTO hdk_usuario_departamento (
					COD_DEPARTAMENTO,  COD_USUARIO
					) VALUES ( 
					".$_POST["COD_DEPARTAMENTO"]."
					, ".$_POST["COD_USU"].")" ; // echo $SQL2;
		 $conexao->Execute($SQL2) or die($conexao->ErrorMsg());
		}		
				
	// se o atendente tiver nível de acesso de usuário, deleta seus grupos e empresas
	if ($_POST["COD_TIPO"] == 1) {
		$SQL = "DELETE FROM hdk_usuario_grupo WHERE COD_USUARIO =".$COD_USU ;
		$conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());

		$SQL = "DELETE FROM hdk_usuario_empresa WHERE COD_USUARIO =".$COD_USU ;
		$conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());
	}
	
	$conexao->CompleteTrans();
}				
?>

<script language="javascript">
	opener.location.reload();
	window.close();
</script>

<? return false; 
  }
	  
	$COD_USUARIO = $_SESSION["SES_COD_USUARIO"];
	
	$SQL = "SELECT
		*
		, usuario.DES_EMAIL as DES_EMAIL_US 
		, usuario.COD_USUARIO as COD_USUARIO
		, usuario.COD_EMPRESA as EMPRESA
		, usuario.COD_PERMISSAO_GRUPO
	FROM  (
		  hdk_usuario_tipo as usuario_tipo
		, hdk_empresa as empresa
		, hdk_usuario as usuario 
		  )
		   LEFT JOIN hdk_usuario_departamento as usuario_departamento
			ON ( usuario_departamento.COD_USUARIO = usuario.COD_USUARIO ) 
			LEFT JOIN hdk_departamento as departamento
			ON 	( usuario_departamento.COD_DEPARTAMENTO = departamento.COD_DEPARTAMENTO)
			LEFT JOIN hdk_centro_custo cc
			ON (cc.COD_CENTRO_CUSTO = usuario.COD_CENTRO_CUSTO)
	WHERE 
		usuario.COD_TIPO = usuario_tipo.COD_TIPO
		AND empresa.COD_EMPRESA = usuario.COD_EMPRESA
		AND usuario.COD_USUARIO=".$COD_USUARIO;


$rsUsuario = $conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());

$EMPRESA = $rsUsuario->Fields('EMPRESA');

// Seleciona tipo de usuario
$SQL_TIPO = "Select * ,COD_TIPO  as TIPO from hdk_usuario_tipo order by NOM_TIPO ASC ";
$rsTipo = $conexao->Execute($SQL_TIPO) or die($conexao->ErrorMsg());
// end Recordset

// Seleciona departamentos
$SQL_DEPTNO = "Select *, COD_DEPARTAMENTO as DEPARTAMENTO  from hdk_departamento where COD_EMPRESA=".$EMPRESA." order by NOM_DEPARTAMENTO ASC";
$rsDeptno = $conexao->Execute($SQL_DEPTNO) or die($SQL_DEPTNO." ".$conexao->ErrorMsg());
// end Recordset

//Selecionando todas as empresas disponíveis
$SQL = "SELECT * FROM hdk_empresa ";
$rsEmpresa = $conexao->Execute($SQL) or die("<b>$SQL</b><br>".$conexao->ErrorMsg());

//Se for login não for de administrador não mostra todas as opções de ediçao
if($_SESSION["SES_COD_TIPO"]!=4) 
{ 
	$display="style='display:none'"; 
} else{ 
	$display=""; 
} 

// busca todos os grupos de permissões para popular o select
$SQL = "select ut.COD_TIPO, ut.NOM_TIPO, 
		pg.COD_GRUPO, 
		pg.NOM_GRUPO, 
		pg.TIP_GRUPO 
		from hdk_permissao_grupo pg, 
		hdk_usuario_tipo ut 
		where 
		pg.TIP_GRUPO = ut.COD_TIPO 
		order by 
		IND_SELECIONAR DESC,
		pg.NOM_GRUPO ASC";
$rsPermGrupo = $conexao->Execute($SQL) or die ($conexao->ErrorMsg());

?>
<html>
<head>
<title><? echo $nom_titulo; ?></title>
<link href="../../style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../includes/funcoes.js" type="text/JavaScript"></script>
<script language="JavaScript" type="text/JavaScript">
<!--


function verifica_form(){
	path = document.form1;
	if(path.NOM_USUARIO.value == ''){
		alert("<?=$l_ate["msg"]["informe_nm_usuario"]?>");
		path.NOM_USUARIO.focus();			
		return false;
	}
	if(path.DES_LOGIN.value == ''){
		alert("<?=$l_ate["msg"]["informe_login"]?>");
		path.DES_LOGIN.focus();			
		return false;
	}
	/* if(path.DES_SENHA.value == ''){
	alert("<?=$l_ate["msg"]["informe_senha"]?>");
	path.DES_SENHA.focus();			
	return false;
	}*/ 
	if(path.alterar.value == 'nao'){
		alert("<?=$l_ate["msg"]["login_ja_existe"]?>");
		path.DES_LOGIN.focus();			
		return false;
	}
/*	if((path.COD_PERMISSAO_GRUPO.value == '0') && (path.COD_TIPO.value != 4)){
		alert("<?=$l_ate["msg"]["defina_grupo_permissao"]?>");
		path.COD_PERMISSAO_GRUPO.focus();			
		return false;
	}
	
	if(path.DES_EMAIL.value == ''){
		alert("<?=$l_ate["msg"]["informe_email"]?>");
		path.DES_EMAIL.focus();			
		return false;
	}
	   if(path.COD_DEPARTAMENTO.value == ''){
	alert("<?=$l_ate["msg"]["informe_depto"]?>");
	path.COD_DEPARTAMENTO.focus();			
	return false;
	} */
}

function atualizar(){
	path = document.all;
	if (path.DES_LOGIN.value != path.DES_LOGIN_OLD.value){
		path.carregardepto.src= 'atendente_checar.php?DES_LOGIN='+path.DES_LOGIN.value;	
	}
}

function ValidaNum(Campo) {
	var valid = "0123456789,";
	var ok = "yes";
	var temp;
	

	for (var i=0; i<Campo.value.length; i++) {
		temp = "" + Campo.value.substring(i, i+1);
		if (valid.indexOf(temp) == "-1") ok = "no";	
	}
	if (ok == "no") {
		//alert("Você só pode digitar números neste campo ou pontos. Não é permidido usar vírgulas.");
		Campo.value = '';
		Campo.focus();
   }
}

function Numeros(){
	if (((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) && event.keyCode != 9 && event.keyCode != 8){
		return false;
	}
}

// cria um array javascript com todas as permissoes sendo que cada item do array possui (COD_TIPO, COD_GRUPO e NOM_GRUPO)
arrPermissao = new Array();

<?
$i = 0;
$rsPermGrupo->MoveFirst();
while (!$rsPermGrupo->EOF) {
	
	echo "arrPermissao[".$i."] =  new Array(".$rsPermGrupo->Fields('COD_TIPO').",".$rsPermGrupo->Fields("COD_GRUPO").",'".$rsPermGrupo->Fields("NOM_GRUPO")."');";
	
	$i++;
	$rsPermGrupo->MoveNext();
}

$rsPermGrupo->MoveFirst();
?>

function carregaPermissao(COD_TIPO) {
	obj = document.form1.COD_PERMISSAO_GRUPO;
	
	// limpa valores do combo
	for (i=obj.options.length; i >= 0 ; i-- )
		obj.options[i] = null;

	// coloca primeiro valor
	//obj.options[obj.options.length] = new Option("-- Grupos --", 0);

	// monta combo de acordo com COD_TIPO
	for (i=0; i < arrPermissao.length; i++ ) {
		
		if ((arrPermissao[i][0] == COD_TIPO) || ((COD_TIPO == 4) && (arrPermissao[i][0] == 2)))
			obj.options[obj.options.length] = new Option(arrPermissao[i][2], arrPermissao[i][1], false);
	}
}




//-->
</script>
<style type="text/css">
<!--
.style1 {color: #000000}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.form1.NOM_USUARIO.focus()">
<form name="form1" method="post" action="" onSubmit="return verifica_form()">
  <table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="1" bgcolor="#666666">
    <tr>
      <td height="25" valign="top" bgcolor="<? echo $cor_fundo1 ?>" class="txtLabel"><table width="180"  border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td height="22" bgcolor="<? echo $cor_fundo2; ?>" class="linkMenuApont"><?=$l_ate["titulo"]?></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td valign="top" bgcolor="#E5E5E5" class="txtLabel"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="389" height="389" valign="top">
	  <table width="388" height="379"  border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
            <tr bgcolor="#E5E5E5">
<?php  if ($_SESSION['SES_COD_TIPO'] >= 2) : ?>
      <td width="597" valign="top">
	  	<span class="texto11">
			<iframe width="290" height="320" scrolling="yes" frameborder="0" src="atendente_widgets.php?COD_USUARIO=<? echo $COD_USUARIO ?>"></iframe><br>
			<iframe width="290" height="80" scrolling="no" frameborder="0" src="atendente_temas.php?COD_USUARIO=<? echo $COD_USUARIO ?>"></iframe><br>
		</span>
	  </td>
<?php endif;  ?>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
<iframe id="carregardepto" frameborder="0" width="0" height="0"></iframe>
</body>
</html>
