/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {

  	$(window.dashboardDemo.widgets['mq-total-online'].contentElement).append('<div id="mq_total_online" style="width:430px;height:200px;font-size:small;"></div>');

	var options = {
		lines: { show: true , fill: true},
		points: { show: false },
		//xaxis: { tickDecimals: 0, tickSize: 1 },
		 xaxis: {
				mode: "time",
				timeformat: "%b",
				minTickSize: [1, "month"],
				twelveHourClock: true,
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
