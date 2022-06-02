<?php


function widget_mq_grafico_coletora() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => utf8_encode('Frequência - Coletoras  '),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_Grafico_Coletora_ReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_Grafico_Coletora_ReloadContent.js'
  );
}
