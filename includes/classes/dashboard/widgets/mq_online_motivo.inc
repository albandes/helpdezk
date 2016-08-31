<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_mq_online_motivo() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => 'Aulas Online - Motivos ',
    'content' => '<div style="padding-top: 5px;"></div><div id="grafico-ao-motivo" style="width:400px;height:150px;font-size:small;text-align: center; "></div><div style="padding-top: 10px;" id="hover_mq_ao_motivo"></div>',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_MotivoOnlineReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_MotivoOnlineReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
