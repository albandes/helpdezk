<?php


function widget_mq_impressoes_usuario() {
  
  return array(
    'title' => utf8_encode('Impress�es Usu�rio '),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_ImpressoesUsuarioReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_ImpressoesUsuarioContent.js'
  );
}

