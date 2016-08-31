/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the fullscreen event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.fullscreenScript is executed everytime the widget goes into full screen.
  
  setTimeout(function() {
    window.dashboardDemo.widgets['hello-world'].fullscreen.element.append('<p>Another <strong>paragraph</strong> for the full screen page</p>');
  }, 500);
});
