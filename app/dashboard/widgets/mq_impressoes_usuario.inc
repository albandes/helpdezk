<?php


function widget_mq_impressoes_usuario() {
  
  return array(
    'title' => utf8_encode('Impressões Usuário '),
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/mq_ImpressoesUsuarioReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/mq_ImpressoesUsuarioContent.js'
  );
}

