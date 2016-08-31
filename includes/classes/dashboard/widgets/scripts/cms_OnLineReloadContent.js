
var tempo;
$(function() {
 
  	tempo=0;
	window.dashboardDemo.widgets['cms-online'].contentElement.empty().append('<img src="images/throbber.gif" />');
	//var tempo=0;
	on_line();





});


	function on_line()
	{
		
		var t = setTimeout("on_line()",60000); // Loop
	
		window.dashboardDemo.widgets['cms-online'].contentElement.empty().append('<img src="images/throbber.gif" />');
		
		tempo++;
		$('#conteudo').text(tempo);	
		$.ajax({
			url: "widgets/ajax/cms_online.php",
			cache: false,
			dataType: "json",
			success: function(retorno) {
				window.dashboardDemo.widgets['cms-online'].contentElement.empty().append(retorno.texto);	
				
					
			}
		});	
		

	}		

	function sleep(milliseconds) {
	  var start = new Date().getTime();
	  for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
		  break;
		}
	  }
	}

