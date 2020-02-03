
$(function() {
    
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
		colors: ["#E1BF00", "#D50000","#033696", "#EA7500", "#4da74d"]
		//colors: ["#edc240", "#cb4b4b","#afd8f8", "#4da74d", "#9440ed"]

	};
	
	
	$.getJSON("widgets/ajax/mq_online_motivo.php", function (dados) {
		
		$.plot("#grafico-ao-motivo", dados, opcoes);	
		
	});

	$("#grafico-ao-motivo").bind("plothover", pieHover);
	$("#grafico-ao-motivo").bind("plotclick", pieClick);

	function pieHover(event, pos, obj) 
	{
		percent = parseFloat(obj.series.percent).toFixed(2);
		$("#hover_mq_ao_motivo").html('<span style="font-size:8pt;text-align:left; color: '+obj.series.color+'">'+obj.series.label+': '+pega_array(obj.series.data[0,0])+' ('+percent+'%)</span>');
	}

	function pieClick(event, pos, obj) 
	{
		percent = parseFloat(obj.series.percent).toFixed(2);
		//alert(obj.series.toSource());
		alert(obj.series.data.toSource());
		str = obj.series.data[0,0];
		// str.substr(str.search(/,/i)+1)
		
		mostra = obj.series.data[0,0] ;
		alert(pega_array(obj.series.data[0,0]));
		alert(''+obj.series.label+': '+percent+'%');
	}
 

});

function pega_array(aArray){
	return aArray[1];

}