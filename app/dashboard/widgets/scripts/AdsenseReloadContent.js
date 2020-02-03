/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.
  
  	
	window.dashboardDemo.widgets['hello-bevan'].contentElement.empty().append('<img src="images/throbber.gif" />');
	busca_adsense();


});


	function busca_adsense()
	{
		// -- aqui !!!
		
		var t = setTimeout("busca_adsense()",300000); // Loop

		$.ajax({
			url: "widgets/ajax/adsense.php",
			cache: false,
			dataType: "json",
			success: function(retorno) {
				window.dashboardDemo.widgets['hello-bevan'].contentElement.empty().append(retorno.texto);				
			}
		});	
		

	}		
