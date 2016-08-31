/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.


  	$(window.dashboardDemo.widgets['grafico-acessos'].contentElement).append('<div id="grafico_acessos" style="width:430px;height:200px;font-size:small;"></div>');

	var options = {
		lines: { show: true },
		points: { show: false },
		//xaxis: { tickDecimals: 0, tickSize: 1 },
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
			
	$.getJSON("widgets/ajax/grafico_acessos.php", function (data) {
		myDataSets = data;
		$.plot("#grafico_acessos", myDataSets, options);	
	});	  			
 

	//$("#placeholder").html("inicio sem click");
	//window.dashboardDemo.widgets['twitter'].contentElement.html('html');


});
