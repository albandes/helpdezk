<?php


function widget_cms_analytics_acessos() {
  
  return array(
    'title' => 'Acessos Portal - Google Analytics',
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/cms_AnalyticsAcessosReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/cms_AnalyticsReloadContent.js'
  );
}
