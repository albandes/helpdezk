<?php

/**
 
 demo.html 
 <script language="javascript" type="text/javascript" src="widgets/classes/jquery.datatables/jquery.dataTables.js"></script>
 
 
 **/

// The get widget handler.
function widget_solicitacoes() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => utf8_encode('Novas Solicitações'),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'reloadContentScript' => 'widgets/scripts/SolicitacoesReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
