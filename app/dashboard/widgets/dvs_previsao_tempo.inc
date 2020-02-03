<?php


function widget_dvs_previsao_tempo() {
   
  return array(
    'title' => utf8_encode('Previsão do Tempo'),
    'content' => '<img src="images/throbber.gif" />',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/dvs_PrevisaoTempoReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/dvs_PrevisaoTempoReloadContent.js'
  );
}


