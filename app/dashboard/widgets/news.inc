<?php
/**
 * @file
 *    Defines the hello_world widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_news() {
  // A widget object as per jquery.dashboard.js.
  return array(
    'title' => 'News',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'classes' => 'search',
    'reloadContentScript' => 'widgets/scripts/reloadContent.js',
  );
}
