<?php


// The get widget handler.
function widget_hdk_grafico_sla() {
  // A widget object as per jquery.dashboard.js.
  
  return array(
    'title' => utf8_encode('SLA'),
    'content' => '<div style="padding-top: 5px;"><div id="grafico-sla" style="width:400px;height:100px;font-size:small;text-align: center;"></div><div id="hover"></div>',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/hdk_SlaReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/hdk_SlaReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
