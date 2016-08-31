/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.reloadContentScript is executed when a widget's reloadContent() method is invoked.
  // reloadContent() is not used internally, so this will only every be called if an external script invokes the method.
  if (!window.newsContentReloadCount) {
    window.newsContentReloadCount = 1;
  }
  window.dashboardDemo.widgets['news'].contentElement.empty().append('Some newer news.  Count: ' + window.newsContentReloadCount++);
});
