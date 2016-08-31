<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_mq_total_online() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => 'Aulas Online',
    'content' => '<div style="padding-top: 5px;"><div id="mq_total_online" style="width:430px;height:130px;font-size:small;"></div>',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_TotalOnlineReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_TotalOnlineReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
