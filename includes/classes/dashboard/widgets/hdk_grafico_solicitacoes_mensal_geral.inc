<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_hdk_grafico_solicitacoes_mensal_geral() {
    
  return array(
    'title' => utf8_encode('Solicitações Mensal - Geral '),
    'content' => '<div style="padding-top: 5px;"><div id="grafico-solicitacoes-mensal-geral" style="width:430px;height:150px;font-size:small;text-align: center;"></div>',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/hdk_SolicitacoesMensalGeralReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/hdk_SolicitacoesMensalGeralReloadContent.js'
  );
}


