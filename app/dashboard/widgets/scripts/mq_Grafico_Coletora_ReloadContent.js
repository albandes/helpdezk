
$(function() {


  	$(window.dashboardDemo.widgets['mq-grafico-coletora'].contentElement).append('<div id="grafico_coletora" style="width:430px;height:150px;font-size:small;"></div>');

	var options = {
		lines: 	{ 
					show: true
				},
		points: { show: false },
		 xaxis: {
				mode: "time",
				timeformat: "%d",
				minTickSize: [1, "day"],
				twelveHourClock: true,
				ticks: 31
		  		},
		grid: 	{ 
				hoverable: true, 
				clickable: false, 
				borderWidth: 0
				},
		legend: {
				show: true,
				noColumns: 3,
				position: 'nw', //"ne" or "nw" or "se" or "sw"
				backgroundOpacity: 0.3
				},
		colors: [ "#ff0000","#afd8f8", "#4da74d", "#9440ed"]
		
	};		
				
	var myDataSets;
			
	$.getJSON("widgets/ajax/mq_grafico_coletora.php", function (data) {
		myDataSets = data;
		$.plot("#grafico_coletora", myDataSets, options);	
	});	  			

});

/*


  %h: hours
  %H: hours (left-padded with a zero)
  %M: minutes (left-padded with a zero)
  %S: seconds (left-padded with a zero)
  %d: day of month (1-31), use %0d for zero-padding
  %m: month (1-12), use %0m for zero-padding
  %y: year (four digits)
  %b: month name (customizable)
  %p: am/pm, additionally switches %h/%H to 12 hour instead of 24
  %P: AM/PM (uppercase version of %p)

  Note that for the time mode "tickSize" and "minTickSize" are a bit
special in that they are arrays on the form "[value, unit]" where unit
is one of "second", "minute", "hour", "day", "month" and "year". So
you can specify

  minTickSize: [1, "month"]


*/