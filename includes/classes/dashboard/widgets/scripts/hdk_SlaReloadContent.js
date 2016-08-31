	
$(function() {
	//window.dashboardDemo.widgets['hdk-grafico-sla'].contentElement.empty().html('<div id="hover"></div>');
	
	var opcoes = {
		series: {
			pie: { 
				show: true
			}
		},
		legend: {
            show: true,
			noColumns: 1,
			labelFormatter: function(label, series){
                return '<div style="font-size:8pt;text-align:left;padding:2px;color:'+series.color+';">'+label+'</div>';
            }
        },
		grid: {
			hoverable: true,
			clickable: true
		},
		colors: ["#edc240", "#cb4b4b","#afd8f8", "#4da74d", "#9440ed"]

	};

	$.getJSON("widgets/ajax/hdk_sla.php", function (data) {
		$.plot("#grafico-sla", data, opcoes);	
	});

	$("#grafico-sla").bind("plothover", pieHover);
	$("#grafico-sla").bind("plotclick", pieClick);

	function pieHover(event, pos, obj) 
	{
//		percent = parseFloat(obj.series.percent).toFixed(2);
//		$("#hover").html('<span style="font-size:8pt;text-align:left; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
		percent = parseFloat(obj.series.percent).toFixed(2);
		$("#hover").html('<span style="font-size:8pt;text-align:left; color: '+obj.series.color+'">'+obj.series.label+': '+pega_array(obj.series.data[0,0])+' ('+percent+'%)</span>');
		
	}

	function pieClick(event, pos, obj) 
	{
		percent = parseFloat(obj.series.percent).toFixed(2);
		alert(''+obj.series.label+': '+percent+'%');
	}

});	

function pega_array(aArray){
	return aArray[1];

}