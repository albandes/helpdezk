
$(function() {


  	$(window.dashboardDemo.widgets['cms-grafico-acessos'].contentElement).append('<div id="grafico_acessos" style="width:430px;height:150px;font-size:small;"></div>');

	var options = {
		lines: { show: true },
		points: { show: false },
		 xaxis: {
				mode: "time",
				timeformat: "%H",
				minTickSize: [1, "hour"],
				twelveHourClock: true,
				ticks: 25
		  		},
		grid: { hoverable: true, clickable: true },
		legend: {
				show: true,
				noColumns: 3,
				position: 'nw', //"ne" or "nw" or "se" or "sw"
				backgroundOpacity: 0.3
				}
	};		
				
	var myDataSets;
			
	$.getJSON("widgets/ajax/cms_grafico_acessos.php", function (data) {
		myDataSets = data;
		$.plot("#grafico_acessos", myDataSets, options);	
	});	  			
 


});
