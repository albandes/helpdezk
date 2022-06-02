<?php
/**
 * @file
 *    Defines the hello_rebekah widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_hello_rebekah() {
  // A widget object as per jquery.dashboard.js.
  return array(
    'title' => 'Hello Rebekah',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
    'settings' => true,
    'classes' => 'search highlight',
  );
}

// The widget settings handler.
function widget_hello_rebekah_settings() {
  // Get the saved settings.
  $c = $GLOBALS['_POST']['settings'] ? $GLOBALS['_POST']['settings']['counter'] : 0;

  // A widget settings object as per jquery.dashboard.js.
  return array(
    // Change the settings a bit so we can see that the new settings have been loaded client-side.
    'markup' => 'Some settings here: <input type="text" name="counter" value="' . ++$c . '" />',
    'initScript' => 'widgets/scripts/initSettings.js',
    'script' => 'widgets/scripts/settings.js',
  );
}
