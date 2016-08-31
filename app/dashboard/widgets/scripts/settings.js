/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the settings event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.setting.script is executed when a widget's reloadContent() method is invoked.
  var e = window.dashboardDemo.widgets['hello-rebekah'].element;
  var m = $('<div>Messages can be added anywhere</div>').prependTo(e);
  setTimeout(function() {
    m.fadeOut(1000);
    m = $('<p>Even outside the columns...</p>').insertBefore(window.dashboardDemo.element);
    setTimeout(function() {
      m.fadeOut(1000);
      m = $('<li>...and outside the box!</li>').insertAfter(e).fadeOut(2000);
    }, 1000);
  }, 1000);
});
