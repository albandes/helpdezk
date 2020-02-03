<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_on_line() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => utf8_encode('Audiência'),
    'content' => 'On line',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'reloadContentScript' => 'widgets/scripts/OnLineReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
