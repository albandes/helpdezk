/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the init event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {


  // widget.initScript is executed when a widget has initialized.
  /*
   
	$.getJSON("widgets/ajax/init.php", function (ret) {
				
		var aWidgets=eval(ret.texto);
		for (x in aWidgets)
		{
			var sWid = aWidgets[x] ;
			window.dashboardDemo.widgets[''+aWidgets[x]+''].reloadContent();
		}
	});
	*/
	window.dashboardDemo.widgets['hdk-avisos'].reloadContent();
	/*		
	window.dashboardDemo.widgets['hdk-grafico-solicitacoes-mensal'].reloadContent();
	window.dashboardDemo.widgets['mq-total-online'].reloadContent();
	window.dashboardDemo.widgets['hdk-solicitacoes'].reloadContent();
	window.dashboardDemo.widgets['hdk-avisos'].reloadContent();
	window.dashboardDemo.widgets['cms-grafico-acessos'].reloadContent();
	window.dashboardDemo.widgets['cms-adsense'].reloadContent();
	window.dashboardDemo.widgets['cms-online'].reloadContent();
	window.dashboardDemo.widgets['srv-banda'].reloadContent();
	window.dashboardDemo.widgets['hdk-grafico-sla'].reloadContent();
	window.dashboardDemo.widgets['mq-online-motivo'].reloadContent();
	*/
	


});
