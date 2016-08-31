
	$(function() {
		$(window.dashboardDemo.widgets['cms-analytics-acessos'].contentElement).append('<div id="analytics_acessos" style="width:430px;height:150px;font-size:small;"></div><div id="mostra_tempo" style="font-size:11px;"></div>');
		AtualizaAnalytics();
	});

	function AtualizaAnalytics()
	{
		var d = new Date();
		$('#mostra_tempo').empty().html('Atualizado a cada 15 minutos. &#218;ltima atualiza&ccedil;&atilde;o: '+d.getHours()+'h'+d.getMinutes()+'min'); 
		
		var t = setTimeout("AtualizaAnalytics()",900000); // Loop de 15 minutos
		
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
			colors: [ "#afd8f8", "#4da74d", "#9440ed", "#ff0000"]		
		};		
					
		var myDataSets;
				
		$.getJSON("widgets/ajax/cms_analytics_acessos.php", function (data) {
			myDataSets = data;
			$.plot("#analytics_acessos", myDataSets, options);	
		});	  			
	}



