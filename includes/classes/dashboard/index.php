<? 
session_start(); 


require_once('../../Connections/conexao.php');
require_once('../../includes/config.php');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <!-- Released under the GNU General Public License.  See LICENSE.txt. -->
  <head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <title>Helpdezk - Dashboard</title>

    <!-- Include jQuery.dashboard() and dependencies -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script> 
    <script type="text/javascript" src="jquery-ui-personalized-1.6rc6.min.js"></script>
	
	<?php 
	
	$SQL = 	"
			select
			   a.pasta
			from dsh_tbtema a,
			   dsh_tbtema_has_hdk_usuario b
			where b.hdk_usuario_COD_USUARIO = ".$_SESSION['SES_COD_USUARIO']."
			   and a.idtema = b.dsh_tbtema_idtema
			";

	$rsTema = $conexao->Execute($SQL) or die($conexao->ErrorMsg());
	
	if($rsTema->RecordCount() ==0 ) {
		$tema = "helpdezk";
	} else {	
		$tema = $rsTema->Fields("pasta");
	}	
	
	
	?>
	<script type="text/javascript" >
		var tema = "<?=$tema?>"
		var temaimg = "temas/"+tema+"/images/" ;
		$(document).ready(function() {
			//alert('tema: '+tema);
			primeiro = true ;
			AtualizaDashBoard();
			
		});

		function AtualizaDashBoard()
		{
			var d = new Date();
			$('#debuga').empty().html('Loop: '+d.getHours()+'h'+d.getMinutes()+'min'+d.getSeconds()+'seg'); 
			if (primeiro)
			{
				primeiro = false;
				hora = d.getHours();
			}
			else
			{
				if (hora != d.getHours() )
				{
					//$('#entrou').empty().html('Entrou no if: '+d.getHours()+'h'+d.getMinutes()+'min'+d.getSeconds()+'seg<br>minuto: '+minuto); 
					hora = d.getHours();
					mostra = d.getHours()+'h'+d.getMinutes()+'min'+d.getSeconds()+'seg';
					//alert('Vou dar reload: '+mostra );
					location.reload();
				}
			}	
			var t = setTimeout("AtualizaDashBoard()",300000); // Loop de 5 minutos
		}	
		
		
	</script>
	
	
    <script type="text/javascript" src="jquery.dashboard.js"></script>
	
	<div id="teste" ></div>    

	<!-- Adsense e Analytics -->	
	<link rel="stylesheet" type="text/css" href="widgets/css/adsense.css" />
	<link rel="stylesheet" type="text/css" href="widgets/css/analytics.css" />
	
	<!-- Flot -->
	<!--[if IE]><script language="javascript" type="text/javascript" src="widgets/classes/excanvas/excanvas.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="widgets/classes/jquery.flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="widgets/classes/jquery.flot/jquery.flot.pie.js"></script>
	
	<!-- Jquery Datatables -->
	<script language="javascript" type="text/javascript" src="widgets/classes/jquery.datatables/jquery.dataTables.js"></script>	
	<style type="text/css" title="currentStyle">
		@import "widgets/css/demo_page.css";
		@import "widgets/css/demo_table.css";
	</style>

	
	<script type="text/javascript">
		var SES_COD_USUARIO = <? echo $_SESSION["SES_COD_USUARIO"]; ?>;
	</script>
    <!-- Include the demo implementation's files -->
    <script type="text/javascript" src="demo.js"></script>
    <link rel="stylesheet" type="text/css" href="demo.css" />
	
  </head>
  <body>
  
	<? 
	if ($_GET['topo'] != "N")
	{
		require_once("../../topo.php"); 
	}	
	?>
	
    <div id="dashboard-demo">
      <!-- You can put anything you like here.  jQuery.dashboard() will remove it. -->
      You need javascript to use the dashboard.
    </div>
	
	
	
  </body>
</html>
