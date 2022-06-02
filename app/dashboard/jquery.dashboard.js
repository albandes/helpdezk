/**
 * @file
 *    Defines the jQuery.dashboard() plugin.
 *
 * Uses jQuery 1.3, jQuery UI 1.6 and several jQuery UI extensions, most of all Sortable
 *    http://visualjquery.com/
 *    http://docs.jquery.com/UI/Sortable
 *    http://ui.jquery.com/download
 *      Sortable
 *      Draggable
 *      UI Core
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */
 

//alert('teste: '+ tema);
//var tema    = "mq"; 
var temaimg = "temas/"+tema+"/images/" ;

(function($) { // Create closure.

	document.write("<link rel='stylesheet' type='text/css' href='temas/"+tema+"/dashboard.css' />");
	
	// Constructor for dashboard object.
	$.fn.dashboard = function(options) {
    // Public properties of dashboard.
    var dashboard = {};
    dashboard.element = this.empty();
    dashboard.ready = false;
    dashboard.columns = Array();
    dashboard.widgets = Array();
	
    // End of public properties of dashboard.
coisas = {   a: 'abc',   b: 1235 }; 
    /**
     * Public methods of dashboard.
     */

    // Saves the order of widgets for all columns including the widget.minimized status to options.ajaxCallbacks.saveColumns.
    dashboard.saveColumns = function() {
      // Update the display status of the empty placeholders.
      for (var c in dashboard.columns) {
        var col = dashboard.columns[c];
        // Are there any visible children of the column (excluding the empty placeholder)?
        if (col.element.children(':visible').not(col.emptyPlaceholder).length > 0) {
          col.emptyPlaceholder.hide();
        }
        else {
          col.emptyPlaceholder.show();
        }
      }

      // Don't save any changes to the server unless the dashboard has finished initiating.
      if (!dashboard.ready) {
        return;
      }

      // Build a list of params to post to the server.
      var params = {};

      // For each column...
      for (var c in dashboard.columns) {

        // IDs of the sortable elements in this column.
        var ids = dashboard.columns[c].element.sortable('toArray');

        // For each id...
        for (var w in ids) {
          // Chop 'widget-' off of the front so that we have the real widget id.
          var id = ids[w].substring('widget-'.length);

          // Add one flat property to the params object that will look like an array element to the PHP server.
          // Unfortunately jQuery doesn't do this for us.
          params['columns[' + c + '][' + id + ']'] = (dashboard.widgets[id].minimized ? '1' : '0');
        }
      }

      // The ajaxCallback settings overwrite any duplicate properties.
      $.extend(params, opts.ajaxCallbacks.saveColumns.data);
      $.post(opts.ajaxCallbacks.saveColumns.url, params, function(response, status) {
        invokeCallback(opts.callbacks.saveColumns, dashboard);

        // Log the response to aid server-side debugging.
        if (window.console && console.log) {
          console.log(response);
        }
      });
    };

    // Puts the dashboard into full screen mode, saving element for when the user exits full-screen mode.
    // Does not add element to the DOM – this is the caller's responsibility.
    // Does show and hide element though.
    dashboard.enterFullscreen = function(element) {
      // Hide the columns.
      for (var c in dashboard.columns) {
        dashboard.columns[c].element.hide();
      }

      if (!dashboard.fullscreen) {
        // Initialize.
        var markup = '<a id="full-screen-header" class="full-screen-close-icon">' + opts.fullscreenHeaderInner + '</a>';
        dashboard.fullscreen = {
          headerElement: $(markup).prependTo(dashboard.element).click(dashboard.exitFullscreen).hide()
        };
      }

      dashboard.fullscreen.headerElement.slideDown();
      dashboard.fullscreen.currentElement = element.show();
      dashboard.fullscreen.displayed = true;
      invokeCallback(opts.callbacks.enterFullscreen, dashboard, dashboard.fullscreen.currentElement);
    };

    // Takes the dashboard out of full screen mode, hiding the active fullscreen element.
    dashboard.exitFullscreen = function() {
      if (!dashboard.fullscreen.displayed) {
        return;
      }

      dashboard.fullscreen.headerElement.slideUp();
      dashboard.fullscreen.currentElement.hide();
      dashboard.fullscreen.displayed = false;

      // Show the columns.
      for (var c in dashboard.columns) {
        dashboard.columns[c].element.show();
      }

      invokeCallback(opts.callbacks.exitFullscreen, dashboard, dashboard.fullscreen.currentElement);
    };
    // End of public methods of dashboard.

    /**
     * Private properties of dashboard.
     */
    
    // Used to determine whether there are any incomplete ajax requests pending initialization of the dashboard.
    var asynchronousRequestCounter = 0;

    // Used to determine whether two resort events are resulting from the same UI event.
    var currentReSortEvent = null;

    // Merge in the caller's options with the defaults.
    var opts = $.extend({}, $.fn.dashboard.defaults, options);

    // Execution 'forks' here and restarts in init().  Tell the user we're busy with a throbber.
    var throbber = $(opts.throbberMarkup).appendTo(dashboard.element);
    $.getJSON(opts.ajaxCallbacks.getWidgetsByColumn.url, opts.ajaxCallbacks.getWidgetsByColumn.data, init);
    asynchronousRequestCounter++;
    return dashboard;
    // End of constructor and private properties for dashboard object.

    /**
     * Private methods of dashboard.
     */

    // Ajax callback for getWidgetsByColumn.
    function init(widgets, status) {
      asynchronousRequestCounter--;
      throbber.remove();
      var markup = '<li class="empty-placeholder">' + opts.emptyPlaceholderInner + '</li>';

      // Build the dashboard in the DOM.  For each column...
      // (Don't iterate on widgets since this will break badly if the dataset has empty columns.)
      for (var c = 0; c < opts.columns; c++) {
        // Save the column to both the public scope for external accessibility and the local scope for readability.
        var col = dashboard.columns[c] = {
          initialWidgets: Array(),
          element: $('<ul id="column-' + c + '" class="column"></ul>').appendTo(dashboard.element)
        };

        // Add the empty placeholder now, hide it and save it.
        col.emptyPlaceholder = $(markup).appendTo(col.element).hide();

        // For each widget in this column.
        for (var id in widgets[c]) {
          // Build a new widget object and save it to various publicly accessible places.
          col.initialWidgets[id] = dashboard.widgets[id] = widget({
            id: id,
            element: $('<li class="widget"></li>').appendTo(col.element),
            initialColumn: col,
            minimized: widgets[c][id]
          });
        }
      }

      invokeCallback(opts.callbacks.init, dashboard);
    }

    // Contructors for each widget call this when initialization has finished so that dashboard can complete it's intitialization.
    function completeInit() {
      // Don't do anything if any widgets are waiting for ajax requests to complete in order to finish initialization.
      if (asynchronousRequestCounter > 0) {
        return;
      }

      // Make widgets sortable across columns.
      dashboard.sortableElement = $('.column').sortable({
        connectWith: ['.column'],

        // The class of the element by which widgets are draggable.
        handle: '.widget-header',

        // The class of placeholder elements (the 'ghost' widget showing where the dragged item would land if released now.)
        placeholder: 'placeholder',

        // This worked in jQuery UI 1.6 rc5, but broke in rc6.
        // @todo: Test this and/or report sortable's opacity bug.
        // opacity: 0.2,

        // Maks sure that only widgets are sortable, and not empty placeholders.
        items: '> .widget',
        forcePlaceholderSize: true,

        // Callback functions.
        update: resorted,
        start: hideEmptyPlaceholders
      });

      // Update empty placeholders.
      dashboard.saveColumns();
      dashboard.ready = true;
      invokeCallback(opts.callbacks.ready, dashboard);
    }

    // Callback for when any list has changed (and the user has finished resorting).
    function resorted(e, ui) {
      // Only do anything if we haven't already handled resorts based on changes from this UI DOM event.
      // (resorted() gets invoked once for each list when an item is moved from one to another.)
      if (!currentReSortEvent || e.originalEvent != currentReSortEvent) {
        currentReSortEvent = e.originalEvent;
        dashboard.saveColumns();
      }
    }

    // Callback for when a user starts resorting a list.  Hides all the empty placeholders.
    function hideEmptyPlaceholders(e, ui) {
      for (var c in dashboard.columns) {
        dashboard.columns[c].emptyPlaceholder.hide();
      }
    }

    // @todo use an event library to register, bind to and invoke events.
    //  @param callback is a function.
    //  @param theThis is the context given to that function when it executes.  It becomes 'this' inside of that function.
    function invokeCallback(callback, theThis, parameterOne) {
      if (callback) {
        callback.call(theThis, parameterOne);
      }
    }

    /**
     * widget object
     *    Private sub-class of dashboard
     * Constructor starts
     */
    function widget(widget) {
      // Merge default options with the options defined for this widget.
      widget = $.extend({}, $.fn.dashboard.widget.defaults, widget);

      /**
       * Public methods of widget.
       */

      // Toggles the minimize() & maximize() methods.
      widget.toggleMinimize = function() {
        if (widget.minimized) {
          widget.maximize();
        }
        else {
          widget.minimize();
        }

        widget.hideSettings();
        dashboard.saveColumns();
      };
      widget.minimize = function() {
        $('.widget-content', widget.element).slideUp(opts.animationSpeed);
        var img = $('img', widget.controls.minimize.element);
        img.attr('src', img.attr('src').replace('minimize', 'maximize'));
        widget.minimized = true;
      };
      widget.maximize = function() {
        $('.widget-content', widget.element).slideDown(opts.animationSpeed);
        var img = $('img', widget.controls.minimize.element);
        img.attr('src', img.attr('src').replace('maximize', 'minimize'));
        widget.minimized = false;
      };

      // Toggles whether the widget is in settings-display mode or not.
      widget.toggleSettings = function() {
        if (widget.settings.displayed) {
          // Widgets always exit settings into maximized state.
          widget.maximize();
          widget.hideSettings();
          invokeCallback(opts.widgetCallbacks.hideSettings, widget);
        }
        else {
          widget.minimize();
          widget.showSettings();
          invokeCallback(opts.widgetCallbacks.showSettings, widget);
        }
      };
      widget.showSettings = function() {
        if (widget.settings.element) {
          widget.settings.element.show();

          // Settings are loaded via AJAX.  Only execute the script if the settings have been loaded.
          if (widget.settings.ready) {
            getJavascript(widget.settings.script);
          }
        }
        else {
          // Settings have not been initialized.  Do so now.
          initSettings();
        }
        widget.settings.displayed = true;
      };
      widget.hideSettings = function() {
        if (widget.settings.element) {
          widget.settings.element.hide();
        }
        widget.settings.displayed = false;
      };
      widget.saveSettings = function() {
        // Build list of parameters to POST to server.
        var params = {};
        // serializeArray() returns an array of objects.  Process it.
        var fields = widget.settings.element.serializeArray();
        for (var i in fields) {
          var field = fields[i];
          // Put the values into flat object properties that PHP will parse into an array server-side.
          // (Unfortunately jQuery doesn't do this)
          params['settings[' + field.name + ']'] = field.value;
        }

        // Things get messy here.
        // @todo Refactor to use currentState and targetedState properties to determine what needs 
        // to be done to get to any desired state on any UI or AJAX event – since these don't always 
        // match.  
        // E.g.  When a user starts a new UI event before the Ajax event handler from a previous 
        // UI event gets invoked.

        // Hide the settings first of all.
        widget.toggleSettings();
        // Save the real settings element so that we can restore the reference later.
        var settingsElement = widget.settings.element;
        // Empty the settings form.
        widget.settings.innerElement.empty();
        initThrobber();
        // So that showSettings() and hideSettings() can do SOMETHING, without showing the empty settings form.
        widget.settings.element = widget.throbber.hide();
        widget.settings.ready = false;

        // Save the settings to the server.
        $.extend(params, opts.ajaxCallbacks.widgetSettings.data, { id: widget.id });
        $.post(opts.ajaxCallbacks.widgetSettings.url, params, function(response, status) {
          // Merge the response into widget.settings.
          $.extend(widget.settings, response);
          // Restore the reference to the real settings element.
          widget.settings.element = settingsElement;
          // Make sure the settings form is empty and add the updated settings form.
          widget.settings.innerElement.empty().append(widget.settings.markup);
          widget.settings.ready = true;

          // Did the user already jump back into settings-display mode before we could finish reloading the settings form?
          if (widget.settings.displayed) {
            // Ooops!  We had better take care of hiding the throbber and showing the settings form then.
            widget.throbber.hide();
            widget.showSettings();
            invokeCallback(opts.widgetCallbacks.saveSettings, dashboard);
          }
        }, 'json');

        // Don't let form submittal bubble up.
        return false;
      };

      widget.enterFullscreen = function() {
        // Make sure the widget actually supports full screen mode.
        if (!widget.fullscreen) {
          return;
        }

        if (!widget.fullscreen.element) {
          // Initialize the full screen element for this widget.
          var markup = '<div id="widget-' + widget.id + '-full-screen">' + widget.fullscreen + '</div>';

          // Overwrite the widget.fullscreen string.
          widget.fullscreen = {
            initialMarkup: widget.fullscreen,
            element: $(markup).appendTo(dashboard.element)
          };

          getJavascript(widget.fullscreenInitScript);
        }

        // Let dashboard.enterFullscreen() do the heavy lifting.
        dashboard.enterFullscreen(widget.fullscreen.element);
        getJavascript(widget.fullscreenScript);
        widget.fullscreen.displayed = true;
      };
      // Exit fullscreen mode.
      widget.exitFullscreen = function() {
        // This is just a wrapper for dashboard.exitFullscreen() which does the heavy lifting.
        dashboard.exitFullscreen();
      };

      // Adds controls to a widget.  id is for internal use and image file name in images/ (a .gif).
      widget.addControl = function(id, control) {
        var markup = '<a class="widget-icon ' + id + '-icon"><img src="' + temaimg + id + '.gif" alt="' + control.description + '" /></a>';
        control.element = $(markup).prependTo($('.widget-controls', widget.element)).click(control.callback);
      };

      // An external method used only by and from external scripts to reload content.  Not invoked or used internally.
      // The widget must provide the script that executes this, as well as the script that invokes it.
      widget.reloadContent = function() {
        getJavascript(widget.reloadContentScript);
        invokeCallback(opts.widgetCallbacks.reloadContent, widget);
      };

      // Removes the widget from the dashboard, and saves columns.
      widget.remove = function() {
        invokeCallback(opts.widgetCallbacks.remove, widget);
        widget.element.fadeOut(opts.animationSpeed, function() {
          $(this).remove();
          dashboard.saveColumns();
        });
      };
      // End public methods of widget.

      /**
       * Public properties of widget.
       */

      // Default controls.  External script can add more with widget.addControls()
      widget.controls = {
        settings: {
          description: 'Configure this widget',
          callback: widget.toggleSettings
        },
        minimize: {
          description: 'Show & hide this widget',
          callback: widget.toggleMinimize
        },
        fullscreen: {
          description: 'Open widget to full screen mode',
          callback: widget.enterFullscreen
        },
        close: {
          description: 'Remove this widget',
          callback: widget.remove
        }
      };
      // End public properties of widget.

      /**
       * Private properties of widget.
       */

      // We're gonna 'fork' execution again, so let's tell the user to hold with us till the AJAX callback gets invoked.
      var throbber = $(opts.throbberMarkup).appendTo(widget.element);
      var params = $.extend({}, opts.ajaxCallbacks.getWidget.data, {id: widget.id});
      $.getJSON(opts.ajaxCallbacks.getWidget.url, params, init);

      // Help dashboard track whether we've got any outstanding requests on which initialization is pending.
      asynchronousRequestCounter++;
      return widget;
      // End of private properties of widget.

      /**
       * Private methods of widget.
       */

      // Ajax callback for widget initialization.
      function init(data, status) {
        asynchronousRequestCounter--;
        $.extend(widget, data);

        // Delete controls that don't apply to this widget.
        if (!widget.settings) {
          delete widget.controls.settings;
        }
        if (!widget.fullscreen) {
          delete widget.controls.fullscreen;
        }

        widget.element.attr('id', 'widget-' + widget.id).addClass(widget.classes);
        throbber.remove();
        // Build and add the widget's DOM element.
        $(widgetHTML()).appendTo(widget.element);
        // Save the content element so that external scripts can reload it easily.
        widget.contentElement = $('.widget-content', widget.element);
        $.each(widget.controls, widget.addControl);

        // Switch the initial state so that it initializes to the correct state.
        widget.minimized = !widget.minimized;
        widget.toggleMinimize();
        getJavascript(widget.initScript);
        invokeCallback(opts.widgetCallbacks.get, widget);

        // completeInit() is a private method of the dashboard.  Let it complete initialization of the dashboard.
        completeInit();
      }

      // Builds inner HTML for widgets.
      function widgetHTML() {
        var html = '';
        html += '<div class="widget-wrapper">';
        html += '  <div class="widget-controls"></div>';
        html += '  <div class="widget-header">' + widget.title + '</div>';
        html += '  <div class="widget-content">' + widget.content + '</div>';
        html += '</div>';
        return html;
      }

      // Initializes a widgets settings pane.
      function initSettings() {
        // Overwrite widget.settings (boolean).
        initThrobber();
        widget.settings = {
          element: widget.throbber.show(),
          ready: false
        };

        // Get the settings markup and script executables for this widget.
        var params = $.extend({}, opts.ajaxCallbacks.widgetSettings.data, { id: widget.id });
        $.getJSON(opts.ajaxCallbacks.widgetSettings.url, params, function(response, status) {
          $.extend(widget.settings, response);
          // Build and add the settings form to the DOM.  Bind the form's submit event handler/callback.
          widget.settings.element = $(widgetSettingsHTML()).appendTo($('.widget-wrapper', widget.element)).submit(widget.saveSettings);
          // Bind the cancel button's event handler too.
          widget.settings.cancelButton = $('.widget-settings-cancel', widget.settings.element).click(cancelEditSettings);
          // Build and add the inner form elements from the HTML markup provided in the AJAX data.
          widget.settings.innerElement = $('.widget-settings-inner', widget.settings.element).append(widget.settings.markup);
          widget.settings.ready = true;

          if (widget.settings.displayed) {
            // If the user hasn't clicked away from the settings pane, then display the form.
            widget.throbber.hide();
            widget.showSettings();
          }

          getJavascript(widget.settings.initScript);
        });
      }

      // Builds HTML for widget settings forms.
      function widgetSettingsHTML() {
        var html = '';
        html += '<form class="widget-settings">';
        html += '  <div class="widget-settings-inner"></div>';
        html += '  <div class="widget-settings-buttons">';
        html += '    <input id="' + widget.id + '-settings-save" class="widget-settings-save" value="Save" type="submit" />';
        html += '    <input id="' + widget.id + '-settings-cancel" class="widget-settings-cancel" value="Cancel" type="submit" />';
        html += '  </div>';
        html += '</form>';
        return html;
      }

      // Initializes a generic widget content throbber, for use by settings form and external scripts.
      function initThrobber() {
        if (!widget.throbber) {
          widget.throbber = $(opts.throbberMarkup).appendTo($('.widget-wrapper', widget.element));
        }
      };

      // Event handler/callback for cancel button clicks.
      // @todo test this gets caught by all browsers when the cancel button is 'clicked' via the keyboard.
      function cancelEditSettings() {
        widget.toggleSettings();
        return false;
      };

      // Helper function to execute external script on the server.
      // @todo It would be nice to provide some context to the script.  How?
      function getJavascript(url) {
        if (url) {
          $.getScript(url);
        }
      }
    };
  };

  // Public static properties of dashboard.  Default settings.
  $.fn.dashboard.defaults = {
    columns: 3,
    emptyPlaceholderInner: 'There are no widgets in this column of your dashboard.',
    fullscreenHeaderInner: '<img alt="Close this widget" src="'+temaimg+'close.gif" /> Return to Dashboard',
    throbberMarkup: '<div class="throbber"><img alt="Loading, please wait" src="'+temaimg+'throbber.gif" /><p>Loading...</p></div>',
    animationSpeed: 200,
    callbacks: {},
    widgetCallbacks: {}
  };

  // Default widget settings.
  $.fn.dashboard.widget = {
    defaults: {
      minimized: false,
      settings: false,
      fullscreen: false
    }
  };
})(jQuery); // end of closure
