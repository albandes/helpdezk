/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.


  	$(window.dashboardDemo.widgets['twitter'].contentElement).append('<div id="twitter"></div>');

				$("#twitter").getTwitter({
					userName: "futeboldaqui",
					numTweets: 5,
					loaderText: "Lendo tweets...",
					slideIn: true,
					slideDuration: 750,
					showHeading: true,
					headingText: "Ultimos Tweets",
					showProfileLink: true,
					showTimestamp: true
				});

	//$("#placeholder").html("inicio sem click");
	//window.dashboardDemo.widgets['twitter'].contentElement.html('html');


});
