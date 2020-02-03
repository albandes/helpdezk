<?php
/**
 * @file
 *    Defines the hello_bevan widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_hello_bevan() {
  // A widget object as per jquery.dashboard.js.
  return array(
    'title' => 'Hello Bevan',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'classes' => 'personal',
    'fullscreen' => '<strong>Some fullscreen stuff</strong>'
  );
}

// Widgets don't have settings by default, so widget_hello_bevan_settings() is not defined.
