<?php


function widget_cms_grafico_acessos() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => 'Acessos ',
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/cms_GraficoAcessosReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/cms_GraficoAcessosReloadContent.js'
  );
}
