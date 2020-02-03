<?php

/**
 * @file
 *    Defines the google_analytics widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_google_analytics() {
  // A widget object as per jquery.dashboard.js.
  $titulo = "Google Analytics -	 " ;
  return array(
    'title' => $titulo,
    'content' => 'Texto original',
    'classes' => 'personal',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
