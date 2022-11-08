<?php
/**
 * @file
 *    Defines the hello_bevan widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_cms_adsense() {
  // A widget object as per jquery.dashboard.js.
  $titulo = "Google Ad Sense	 " ;
  return array(
    'title' => $titulo,
    'content' => '',
    'classes' => 'search',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>',
	'initScript' => 'widgets/scripts/cms_AdsenseReloadContent.js',
	'reloadContentScript' => 'widgets/scripts/cms_AdsenseReloadContent.js'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
