
$(function() {
	
	$.ajax({
		url: "widgets/ajax/dvs_previsao_tempo.php",
		cache: false,
		dataType: "json",
		success: function(retorno) {
			window.dashboardDemo.widgets['dvs-previsao-tempo'].contentElement.empty().append(retorno.texto);				
		}
	});	


});


		
