<?php



// The get widget handler.
function widget_hdk_solicitacoes() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => utf8_encode('Novas Solicitações'),
    'content' => '<div id="dt_example"><div id="container"><div id="dynamic"><table cellpadding="0" cellspacing="0" border="0" class="display" id="example"><thead><tr><th width="5px"></th><th >Abertura</th><th width="240px">Assunto</th><th>link</th><th>vencimento</th></tr></thead><tbody></tbody><tfoot><tr><th ></th><th>Abertura</th><th>Assunto</th></tr></tfoot></table></div></div></div>',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/hdk_SolicitacoesReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/hdk_SolicitacoesReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
