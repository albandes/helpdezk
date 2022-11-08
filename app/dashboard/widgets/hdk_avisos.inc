<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_hdk_avisos() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => utf8_encode('Avisos'),
    'content' => '',
    'classes' => 'search',
	'initScript' => 'widgets/scripts/hdk_AvisosReloadContent.js',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'reloadContentScript' => 'widgets/scripts/hdk_AvisosReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
