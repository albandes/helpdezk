<?php


function widget_mq_impressoes_xerox_printers() {
  
  return array(
    'title' => utf8_encode('C�pias e Impress�es '),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_ImpressoesXeroxPrintersReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_ImpressoesXeroxPrintersReloadContent.js'
  );
}

