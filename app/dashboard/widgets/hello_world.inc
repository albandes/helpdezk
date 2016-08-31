<?
/**
 * @file
 *    Defines the hello_world widget.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

// The get widget handler.
function widget_hello_world() {
  // A widget object as per jquery.dashboard.js.
  return array(
    'title' => 'Hello World',
    'content' => '
				<script type="text/javascript">
					
					function clica() {
						alert("Hello");
						location.reload();
					}
							//
					
					       
				</script>

				 <input type=button onclick=javascript:clica(); value=Enviar> 

	
	
				',
    'initScript' => 'widgets/scripts/init.js',
    'classes' => 'social',
    
    'fullscreen' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <em>Excepteur sint</em> occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
    'fullscreenScript' => 'widgets/scripts/fullscreen.js',
    
  );
}

// The widget settings handler.
