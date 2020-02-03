$(function() {

  	//$(window.dashboardDemo.widgets['mq-total-online'].contentElement).append('<div id="mq_total_online" style="width:430px;height:200px;font-size:small;"></div>');

	var options = {
		lines: { show: true , fill: true},
		points: { show: false },
		xaxis: {
				mode: "time",
				timeformat: "%b",
				minTickSize: [1, "month"],
				monthNames: ["jan", "fev", "mar", "abr", "mai", "jun", "jul", "ago", "set", "out", "nov", "dez"],
				ticks: 12
		  		},
		grid: { hoverable: true, clickable: true },
		
		legend: {
				show: true,
				noColumns: 1,
				position: 'nw', //"ne" or "nw" or "se" or "sw"
				backgroundOpacity: 0.2
				}
			
	};		
				
	var myDataSets;
			
	$.getJSON("widgets/ajax/mq_total_online.php", function (data) {
		myDataSets = data;
		$.plot("#mq_total_online", myDataSets, options);	
	});	  			
 

});
