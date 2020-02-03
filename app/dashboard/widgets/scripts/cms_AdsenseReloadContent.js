/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.
  
  	
	
	busca_adsense();


});


	function busca_adsense()
	{
		// -- aqui !!!
		
		var t = setTimeout("busca_adsense()",300000); // Loop
		window.dashboardDemo.widgets['cms-adsense'].contentElement.empty().append('<img src="images/throbber.gif" />');
		$.ajax({
			url: "widgets/ajax/adsense.php",
			cache: false,
			dataType: "json",
			success: function(retorno) {
				window.dashboardDemo.widgets['cms-adsense'].contentElement.empty().append(retorno.texto);				
			}
		});	
		

	}		
