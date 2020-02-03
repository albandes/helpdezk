/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the initFullscreen event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.initFullscreenScript is executed when a widget's fullscreen display has initialized.
  window.dashboardDemo.widgets['hello-world'].fullscreen.element.append('<p>More <strong>content</strong> for the full screen page</p>');
});
