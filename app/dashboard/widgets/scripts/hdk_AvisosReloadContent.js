
$(function() {
	/**
	 * 
	 * http://www.jankoatwarpspeed.com/post/2009/07/20/Expand-table-rows-with-jQuery-jExpand-plugin.aspx 
	 * 
	 */

	
	var e = window.dashboardDemo.widgets['hdk-avisos'].element;
        $("#hdk_avisos tr:odd").addClass("odd");
        $("#hdk_avisos tr:not(.odd)").hide();
        $("#hdk_avisos tr:first-child").show();
            
        $("#hdk_avisos tr.odd").click(function(){
            $(this).next("tr").toggle();
            $(this).find(".arrow").toggleClass("up");
        });			
		
	$.getJSON("widgets/ajax/hdk_avisos.php", function (data) {
		$(window.dashboardDemo.widgets['hdk-avisos'].contentElement).append(data.tabela);	
	});
	
	
	//$("#grafico-solicitacoes-mensal").text('AQUI');
	//$(window.dashboardDemo.widgets['hdk-grafico-solicitacoes-mensal'].contentElement).append('TESTE');
});
