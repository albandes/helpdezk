/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.
  
  	
	window.dashboardDemo.widgets['on-line'].contentElement.empty().append('<img src="images/throbber.gif" />');
	on_line();


});


	function on_line()
	{
		
		var t = setTimeout("on_line()",60000); // Loop

		$.ajax({
			url: "widgets/ajax/online.php",
			cache: false,
			dataType: "json",
			success: function(retorno) {
				window.dashboardDemo.widgets['on-line'].contentElement.empty().append(retorno.texto);				
			}
		});	
		

	}		
