/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the initSettings event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {
  // widget.settings.initScript is executed when a widget's settings display has initialized (but not when it reloads after saveSettings).
  $('#widget-hello-rebekah .widget-settings').submit(function() {
    alert('The news widget will now be reloaded');
    // Demonstrates usage of the reloadContent() method.
    window.dashboardDemo.widgets['news'].reloadContent();
    alert('Your settings would have been saved persistently if the server-side db layer were implemented');
  });
});
