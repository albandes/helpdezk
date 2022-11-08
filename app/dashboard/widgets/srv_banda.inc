<?php


function widget_srv_banda() {
   
  return array(
    'title' => utf8_encode('Tráfego Link Embratel'),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/srv_BandaReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/srv_BandaReloadContent.js'
  );
}


