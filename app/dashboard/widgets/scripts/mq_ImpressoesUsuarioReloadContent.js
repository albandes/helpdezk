
	$(function() {
		$(window.dashboardDemo.widgets['mq-impressoes-usuario'].contentElement).append('<div id="impressoes-usuario" style="width:430px;height:150px;font-size:small;"></div><div id="tempo_imp_usuarios" style="font-size:11px;"></div>');
		AtualizaImpressoesUsuario();
	});

	function AtualizaImpressoesUsuario()
	{
		var d = new Date();
		$('#tempo_imp_usuarios').empty().html('Atualizado a cada 5 minutos. &#218;ltima atualiza&ccedil;&atilde;o: '+d.getHours()+'h'+d.getMinutes()+'min'); 
		
		var t = setTimeout("AtualizaImpressoesUsuario()",300000); // Loop de 5 minutos
		
		var options = {
			lines: { show: true },
			points: { show: false },
			 xaxis: {
					mode: "time",
					timeformat: "%d",
					minTickSize: [1, "day"],
					twelveHourClock: true,
					ticks: 31
					},
			grid: { hoverable: true, clickable: true },
			legend: {
					show: true,
					noColumns: 3,
					position: 'nw', //"ne" or "nw" or "se" or "sw"
					backgroundOpacity: 0.3
					},
			colors: ["#edc240", "#cb4b4b","#afd8f8", "#4da74d", "#9440ed"]
		};		
					
		var myDataSets;
		
		$.getJSON("widgets/ajax/mq_impressoes_usuario.php", function (data) {
			myDataSets = data;
			$.plot("#impressoes-usuario", myDataSets, options);	
		});	  			
	}



