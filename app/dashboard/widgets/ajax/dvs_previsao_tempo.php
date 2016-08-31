<?php
session_start();
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');


require('../../classes/metar/phpweather.php');
require '../../classes/metar/pir.php';
require '../classes/gapi/gapi.class.php';


// Pegar do banco 
$sql =	"
	select 
		campo1
	from 
		dsh_tbwidget
	where 
		nome = 'srv-banda'
		";
$rs = $conexao->Execute($sql);
$url = 	$rs->fields['campo1'] ; 

	$SQL = 	"
			select
			   a.pasta
			from dsh_tbtema a,
			   dsh_tbtema_has_hdk_usuario b
			where b.hdk_usuario_COD_USUARIO = ".$_SESSION['SES_COD_USUARIO']."
			   and a.idtema = b.dsh_tbtema_idtema
			";

	$rsTema = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
	
    $tema = $rsTema->Fields("pasta");

	if ($tema=="mq") {	
		$skin = "laranja";
	} elseif($tema="helpezk") {
		$skin = "azul";
	}	

$icao="SBPK";
$weather = new phpweather();
$weather->set_icao($icao);
$data = $weather->decode_metar();

require(PHPWEATHER_BASE_DIR . "/output/pw_images.php");
$icons = new pw_images($weather);


date_default_timezone_set('America/Sao_Paulo');

  
/*
$html= 	"
<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
  <tr>
    <td style=\"padding-right:3px\"><iframe src='http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=362&SKIN=".$skin."' scrolling='no' frameborder='0' width=120 height='170' marginheight='0' marginwidth='0'></iframe></td>
    <td style=\"padding-right:3px\"><iframe src='http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=363&SKIN=".$skin."' scrolling='no' frameborder='0' width=120 height='170' marginheight='0' marginwidth='0'></iframe></td>
    <td><iframe src='http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=572&SKIN=".$skin."' scrolling='no' frameborder='0' width=120 height='170' marginheight='0' marginwidth='0'></iframe></td>
  </tr>
</table>
		";
*/

$html = "
		<style>

		#previsao{
			border: solid 1px #DDD;
			height: 75px;
			width: 120px;
		}

		#previsao img{
			float: left;
			width: 50%;
			height: 50%;
		}

		#previsao .info_img{
			font: normal 11px Arial, Verdana;
			display: block;
			color: orange;
			text-align: left;
		}

		#previsao .cidade{
			font: normal 11px Arial, Verdana;
			display: block;
			text-align: center;
		}

		#info_previsao{
			display: block;
			font: normal 11px Arial, Verdana;
			margin: 5px;
		}   

		</style>

		";

if ( isset($data['rel_humidity']) )
{
	$umidade = ceil($data['rel_humidity']) . " %";
}
else
{
	$umidade = "N Inf.";
}		
$html .= "		
		<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
		  <tr>
			<td style=\"padding-right:3px\"><iframe src='http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=362&SKIN=".$skin."' scrolling='no' frameborder='0' width=120 height='170' marginheight='0' marginwidth='0'></iframe></td>
			<td style=\"padding-right:3px\"><iframe src='http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=363&SKIN=".$skin."' scrolling='no' frameborder='0' width=120 height='170' marginheight='0' marginwidth='0'></iframe></td>
			<td valign= \"top\">
			
			<iframe style=\"margin: 0 0 5px -10px\" src=\"http://monitor.ntp.br/horacerta/button.php\" frameborder=\"0\" scrolling=\"no\" height=\"90\" width=\"120\" marginheight=\"0px\" allowtransparency=\"true\"></iframe></iframe>
			

			<div id=\"previsao\">
				<label class=\"cidade\">Pelotas / RS</label>
				<img src=\"/modulos/dashboard/classes/metar/". $icons->get_sky_image()." \" />
				<label class=\"info_img\">". $data['temperature']['temp_c'] ." &#186;C</label>
				<label class=\"info_img\">". $umidade ." </label>
				<label class=\"info_img\">".  ceil(($data['wind']['meters_per_second'] * 3.6)) ."km/h</label>
				<span id=\"info_previsao\">".  date("d/m/Y : H:i:s", $data['time'])  ."</span>				
				
			</div>

	


			
			</td>
		  </tr>
		</table>
		";

$output['texto'] =  "<div style=\"padding-top: 5px;\"><div id=\"centraliza\">".$html."</div>";

echo json_encode($output);


?>