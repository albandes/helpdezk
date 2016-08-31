<?php


function widget_mq_impressoes_xerox_printers() {
  
  return array(
    'title' => utf8_encode('Cópias e Impressões '),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_ImpressoesXeroxPrintersReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_ImpressoesXeroxPrintersReloadContent.js'
  );
}

