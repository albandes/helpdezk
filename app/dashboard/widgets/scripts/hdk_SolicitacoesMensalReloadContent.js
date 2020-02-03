/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.


  	//$(window.dashboardDemo.widgets['grafico-solicitacoes-mensal'].contentElement).append('<div id="grafico-solicitacoes-mensal" style="width:430px;height:200px;font-size:small;"></div>');
	
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
			
	$.getJSON("widgets/ajax/hdk_grafico_solicitacoes_mensal.php", function (data) {
		myDataSets = data;
		$.plot("#grafico-solicitacoes-mensal", myDataSets, options);	
	});
	
	
	//$("#grafico-solicitacoes-mensal").text('AQUI');
	//$(window.dashboardDemo.widgets['hdk-grafico-solicitacoes-mensal'].contentElement).append('TESTE');
});
