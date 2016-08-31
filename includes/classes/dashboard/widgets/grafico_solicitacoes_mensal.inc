<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_grafico_solicitacoes_mensal() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => 'Solicitações Mensais',
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'reloadContentScript' => 'widgets/scripts/SolicitacoesMensaisReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
