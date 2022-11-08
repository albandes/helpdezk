<?

$user  = 'root' ;
$senha = '';
$banco = 'hd';
$host  = 'localhost';

// Primeiro dia do mes seguinte ao que deve ser iniciada a geraçào da estatística [dd/mm/aaaa]
$datainicial = "01/03/2009";

// Primeiro dia do mes que deve ser terminada a geraçào da estatística [dd/mm/aaaa]
$datafinal   = "01/10/2010";


/* ----------------------------------------------------- */
require("../../adodb/adodb.inc.php");

$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco");   

$aDtInicial = explode("/", $datainicial);
$aDtFinal   = explode("/", $datafinal);

$mesinicial = date("m", mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] + $i, $aDtInicial[2]) );	
$anoinicial = date("Y", mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] + $i, $aDtInicial[2]) );	

$mesfinal = date("m", mktime(0, 0, 0, $aDtFinal[1],$aDtFinal[0] + $i, $aDtFinal[2]) );	
$anofinal = date("Y", mktime(0, 0, 0, $aDtFinal[1],$aDtFinal[0] + $i, $aDtFinal[2]) );	



print "Processando ...<br><br>";

$aMeses = array();
$total = 0;
$primeiro = true ;

$datateste = $datainicial ;
for (; ; ) 
{

	if ($primeiro) 
	{
		$primeiro = false;
	}
	else 
	{
		$aDtTeste = explode("/", $datateste);
		$datateste = date('d/m/Y',mktime(0,0,0,$aDtTeste[1] + 1,$aDtTeste[0],$aDtTeste[2])) ;
		
	}
	if ($datateste == date('d/m/Y',mktime(0,0,0,$aDtFinal[1] + 2,$aDtFinal[0],$aDtFinal[2])) ) 
	{
		break;
	}	

	$datagrava = $aDataGrava = explode("/", $datateste);
	$datagrava = $aDataGrava[2] . "-" .$aDataGrava[1] ."-". $aDataGrava[0];
	print $datateste . "<br>";
	// print $datagrava . "<br>";
	$sql =	"
			insert into dsh_tbestatatendentemensal (idusuario,total,anomes)
				SELECT
					a.COD_USUARIO,
					ifnull((select
								count(f.cod_solicitacao)
								from hdk_solicitacao_grupo f,
								hdk_solicitacao h
							where f.COD_ANALISTA = a.COD_USUARIO
							and concat(year(substr(h.DAT_ABERTURA,1,8)),month(substr(h.DAT_ABERTURA,1,8))) = concat(year('$datagrava'- interval 1 month),month('$datagrava'- interval 1 month))
							and h.COD_SOLICITACAO = f.COD_SOLICITACAO
							and h.cod_status = h.cod_status
							and f.cod_ANALISTA <> 0
							group by f.cod_ANALISTA),0) as total ,

					if(LENGTH(month('$datagrava'- interval 1 month))<2,
						concat(year('$datagrava'- interval 1 month),'0',month('$datagrava'- interval 1 month)),
						concat(year('$datagrava'- interval 1 month),month('$datagrava'- interval 1 month))) as anomes

			from hdk_usuario a
			";	
	$rs = $db->Execute($sql);
	if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);			

	$sql = 	"
			insert into dsh_tbestatgrupomensal (idgrupo,total,anomes)
			SELECT
			a.COD_GRUPO,
			ifnull((select
			count(f.cod_solicitacao)
			from hdk_solicitacao_grupo f,
			hdk_solicitacao h
			where f.COD_GRUPO = a.COD_GRUPO
			and concat(year(substr(h.DAT_ABERTURA,1,8)),month(substr(h.DAT_ABERTURA,1,8))) = concat(year('$datagrava'- interval 1 month),month('$datagrava'- interval 1 month))
			and h.COD_SOLICITACAO = f.COD_SOLICITACAO
			and h.cod_status = h.cod_status
			and f.cod_grupo <> 0
			group by f.cod_grupo),0) as total ,
			if(LENGTH(month('$datagrava'- interval 1 month))<2,
			concat(year('$datagrava'- interval 1 month),'0',month('$datagrava'- interval 1 month)),
			concat(year('$datagrava'- interval 1 month),month('$datagrava'- interval 1 month))) as anomes
			from hdk_grupo a
			";
	$rs = $db->Execute($sql);
	if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);			
			
}








?>
