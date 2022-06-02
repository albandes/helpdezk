<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Analytics Report</title>
	<!--[if IE]><script language="javascript" type="text/javascript" src="flot/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="classes/flot/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="classes/flot/jquery.flot.js"></script>
	<link rel="stylesheet" type="text/css" href="css/adsense.css" />
	<script language="javascript" type="text/javascript" >
	
	$(document).ready(function(){
		busca_adsense();
	});
	
	function busca_adsense()
	{
		
		var t = setTimeout("busca_adsense()",15000); // Loop

		/*
		Não pude usar $.getJSON pois o IE faz cache, ai usei .ajax, pois ai dá para setar
		para não fazer cache
		*/					
		
		$.ajax({
			url: "ajax/adsense.php",
			cache: false,
			dataType: "json",
			success: function(data) {
				
				$('#tab_adsense').html(data.texto) ;	
			}
		});	

	}		
	</script>	
</head>

<body>
	
<!-- ### ADSENSE ### -->	
<div id="tab_adsense"> </div>
	
	
 </body>
</html>
