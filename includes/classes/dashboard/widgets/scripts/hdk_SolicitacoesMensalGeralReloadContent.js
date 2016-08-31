

$(function() {
	
	var options = {
		lines: { show: true ,fill: true},
		points: { show: false },
		xaxis: {
			mode: "time",
			timeformat: "%b",
			minTickSize: [1, "month"],
			monthNames: ["jan", "fev", "mar", "abr", "mai", "jun", "jul", "ago", "set", "out", "nov", "dez"],
			ticks: 12
		},
		
		grid: { hoverable: true, 
				clickable: true, 
				backgroundColor: { 
									colors: ["#999", "#BBB"] 
								 } 
		},
		
		legend: {
				show: true,
				noColumns: 3,
				position: 'nw', //"ne" or "nw" or "se" or "sw"
				backgroundOpacity: 0.3
				}
			
	};		
				
	var myDataSets;
			
	$.getJSON("widgets/ajax/hdk_grafico_solicitacoes_mensal_geral.php", function (data) {
		myDataSets = data;
		$.plot("#grafico-solicitacoes-mensal-geral", myDataSets, options);	
	});
	
	
	//$("#grafico-solicitacoes-mensal").text('AQUI');
	//$(window.dashboardDemo.widgets['hdk-grafico-solicitacoes-mensal'].contentElement).append('TESTE');
});
