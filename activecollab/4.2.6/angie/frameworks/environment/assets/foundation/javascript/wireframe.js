/**
 * Wireframe foundation, extended by wireframe implementations for specific 
 * interfaces (web, phone, tablet etc)
 */

/**
 * Decorate all URL-s with async parameter and list of loaded widgets
 */
$.ajaxSetup({
  'beforeSend' : function (ajax_request, settings) {
    if(typeof(settings['decorate_url']) == 'undefined') {
      settings['decorate_url'] = true;
    } // if

    if(settings['decorate_url'] === true) {
      var extend_with = { 'async' : 1 };
    } else if(typeof(settings['decorate_url']) == 'object' && settings['decorate_url']) {
      var extend_with = settings['decorate_url'];
    } else {
      var extend_with = false;
    } // if

    if(extend_with) {
      var loaded_widgets = App.Wireframe.Widgets.getLoadedWidgets();

      if(loaded_widgets.length > 0) {
        extend_with['loaded_widgets'] = loaded_widgets.join(',');
      } // if

      settings['url'] = App.extendUrl(settings['url'], extend_with);
    } // if
  }
});

App.Wireframe = {};

/**
 * Utility methods
 */
App.Wireframe.Utils = {
  
  // Interface constants
  'INTERFACE_DEFAULT' : 'default', 
  'INTERFACE_PHONE' : 'phone', 
  'INTERFACE_TABLET' : 'tablet',
  'INTERFACE_PRINTER' : 'printer', 
  'INTERFACE_API' : 'api', 
  
  /**
   * Get error message from given response object
   *
   * @param Mixed response
   * @return string
   */
  'responseToErrorMessage' : function(response) {
    var error_message = '';
    
    if((response.status == 417 || response.status == 500) && response.responseText.substr(0, 1) == '{') {
      var error_object;
      eval('error_object = ' + response.responseText);
      
      if(typeof(error_object['type']) != 'undefined') {
        if(error_object['type'] == 'ValidationErrors') {
          error_message = App.lang('Please fix the following to continue:');
          
          if(Object.size(error_object['field_errors'])) {
            var counter = 1;
            
            error_message += '<br>';
            
            for(var field in error_object['field_errors']) {
              for(var i in error_object['field_errors'][field]) {
                if(typeof(error_object['field_errors'][field][i]) == 'string' && error_object['field_errors'][field][i]) {
                  error_message += '<br>' + counter++ + '. ' + App.clean(error_object['field_errors'][field][i]);
                } // if
              } // for
            } // for
          } // if
        } // if
      } // if
      
      if(error_message == '') {
        error_message = error_object['message'];
      } // if
    } // if
    
    if(error_message == '') {
      if(response.responseText) {
        return response.responseText;
      } else {
        return response.status && response.statusText ? App.lang('An error occurred. Reason: :status (:status_code)', {
          'status_code' : response.status, 
          'status' : response.statusText 
        }) : App.lang('Unknown error occurred');
      } // if
    } else {
      return error_message;
    } // if
  },
  
  /**
   * Return asset URL by name, type, module and interface
   * 
   * @param String name
   * @param String m
   * @param String i
   */
  'assetUrl' : function (name, m, asset_type, i) {
    if(typeof(m) == 'undefined' || m == '') {
      m = App.Config.get('default_module') ? App.Config.get('default_module') : 'system';
    } // if
    
    if(typeof(i) == 'undefined' || i == '') {
      i = App.Config.get('prefered_interface') ? App.Config.get('prefered_interface') : 'default';
    } // if
    
    return App.Config.get('assets_url') + '/' + asset_type + '/' + m + '/' + i + '/' + name;    
  },

  /**
   * Get Widget Image
   *
   * @param String name
   * @param String widget
   * @param String m
   * @return string
   */
  'widgetImageUrl' : function (name, widget, m) {
    if(typeof(m) == 'undefined' || m == '') {
      m = App.Config.get('default_module') ? App.Config.get('default_module') : 'system';
    } // if

    return App.Config.get('assets_url') + '/images/' + m + '/widgets/' + widget + '/' + name;
  },
  
  /**
   * Return image URL by name, module and interface
   * 
   * @param String name
   * @param String m
   * @param String i
   */
  'imageUrl' : function(name, m, i) {
    return App.Wireframe.Utils.assetUrl(name, m, 'images', i);
  },

  /**
   * Return brand image url
   *
   * @param String name
   * @param Boolean include_timestamp
   * @return String
   */
  'brandImageUrl' : function (name, include_timestamp) {
    return App.Config.get('branding_url') + name + (include_timestamp ? '&timestamp=' + $.now() : '');
  },

  /**
   * Application branding url
   *
   * @param String size
   * @return String
   */
  'applicationBrandImageUrl' : function (name) {
    return App.Wireframe.Utils.imageUrl('application-branding/' + name, 'system');
  },

  /**
   * Application Icon url
   *
   * @param size
   * @return {String}
   */
  'applicationIconUrl' : function (size) {
    return App.Wireframe.Utils.applicationBrandImageUrl('logo.' + size + 'x' + size + '.png');
  },

  /**
   * Return edit icon URL
   * 
   * @returns String
   */
  'editIconUrl' : function() {
    return App.Wireframe.Utils.imageUrl('/icons/12x12/edit.png', 'environment');
  },
  
  /**
   * Return trash icon URL
   * 
   * @returns String
   */
  'trashIconUrl' : function() {
    return App.Wireframe.Utils.imageUrl('/icons/12x12/move-to-trash.png', 'system');
  },
  
  /**
   * Return delete icon URL
   * 
   * @returns String
   */
  'deleteIconUrl' : function() {
    return App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment');
  }
  
};

/**
 * Flash implementation
 */
App.Wireframe.Flash = function() {
  
  /**
   * Flash message instance
   *
   * @var jQuery
   */
  var message_flash;
  
  /**
   * Currently focused element
   *
   * @var jQuery
   */
  var currently_focused_item;

  /**
   * Messages wrapper
   *
   * @type Array
   */
  var messages_wrapper = false;

  /**
   * Auto hide delay
   *
   * @type Number
   */
  var auto_hide_delay = 5000;
  
  /**
   * Show flash message box
   *
   * @param String message
   * @param Object parameters
   * @param Number type
   * @param Boolean auto_hide
   * @param Object additional
   */
  var show_flash_message = function(message, parameters, type, auto_hide, additional) {
    if (messages_wrapper === false) {
      messages_wrapper = $('<ul id="flash_messages"></ul>').appendTo($('body'));
    } // if

    parameters = parameters || null;

    var flash_class = '';
    if (type == 0) {
      flash_class = 'success';
      auto_hide = auto_hide === undefined ? true : auto_hide;
    } else if (type == 1) {
      flash_class = 'error'
      auto_hide = auto_hide === undefined ? false : auto_hide;
    } else if (type == 2) {
      flash_class = 'information'
      auto_hide = auto_hide === undefined ? true : auto_hide;
    } // if

    // create message
    var flash_message = $('<li class="flash_message"></li>').prependTo(messages_wrapper).addClass(flash_class).on('click', function (event) {
      close_flash_message(flash_message);
      return false;
    });

    // create message title
    var flash_message_title = $('<span class="flash_message_title"></span>').appendTo(flash_message).html(App.lang(message, parameters));
    if (additional && additional['icon']) {
      flash_message_title.css('background-image', 'url(' + additional['icon'] + ')');
    } // if

    // flash message can have URL as target
    if (additional && additional['url']) {
      var cmd_key = 'Ctrl';
      if ($.platform.mac) {
        cmd_key = 'Cmd';
      } // if

      var permalink_title = App.lang('(Click) Open in Current Tab') + '\n' +
        App.lang('(Shift + Click) Open in Quick View') + '\n' +
        App.lang('(:cmd_key + Click) Open in New Tab', {'cmd_key' : cmd_key});

      var permalink_anchor = $('<a href="' + additional['url'] + '" title="' + permalink_title + '" class="flash_permalink">' + App.lang('Open Link') + '</a>').appendTo(flash_message);
      permalink_anchor.click(function (event) {
        if (additional['quick_view'] || event['shiftKey']) {
          permalink_anchor.addClass('quick_view_item force_quick_view').attr('title', App.lang('Loading') + '...');
          App.widgets.QuickView.preview(permalink_anchor);
          return true;
        } // if

        if (event.metaKey) {
          window.open(permalink_anchor.attr('href'), '_blank');
          return true;
        } // if

        App.Wireframe.Content.setFromUrl(permalink_anchor.attr('href'));
        return true;
      })
    } // if

    // fade the message in
    flash_message.css('opacity', 0).stop().animate({
      'opacity' : 1
    }, 300);

    flash_message.addClass(auto_hide ? 'auto_hide' : 'not_auto_hide');

    if (auto_hide) {
      setTimeout(function () {
        close_flash_message(flash_message);
      }, auto_hide_delay);
    } // if
  }; // show_flash_message

  /**
   * Close flash message
   *
   * @param flash_message
   */
  var close_flash_message = function (flash_message) {
    flash_message.stop().animate({
      'opacity' : 0
    }, 300, function () {
      flash_message.animate({
        'height' : 0
      }, 200, function () {
        flash_message.remove();
      });
    });
  } // close_flash_message
  
  // Public interface
  return {
    
    /**
     * Display success message
     *
     * @param String message
     * @param Object parameters
     * @param Boolean auto_hide
     * @param Object additional
     */
    success : function(message, parameters, auto_hide, additional) {
      show_flash_message(message, parameters, 0, auto_hide, additional);
    },
    
    /**
     * Display error message
     *
     * @param String message
     * @param Object parameters
     * @param Boolean auto_hide
     * @param Object additional
     */
    error : function(message, parameters, auto_hide, additional) {
      show_flash_message(message, parameters, 1, auto_hide, additional);
    },

    /**
     * Show information message
     *
     * @param String message
     * @param Object parameters
     * @param Boolean auto_hide
     * @param Object additional
     */
    'information' : function (message, parameters, auto_hide, additional) {
      show_flash_message(message, parameters, 2, auto_hide, additional);
    }
    
  };
  
}();

/**
 * Wireframe updates framework
 */
App.Wireframe.Updates = function() {
  
  /**
   * Interval that will trigger updates requests
   *
   * @var Interval
   */
  var updates_timeout;
  
  /**
   * Object where we'll store registered update callbacks
   *
   * @var Object
   */
  var update_callbacks = {};
  
  /**
   * URL that's triggered to get notifications
   * 
   * @var String
   */
  var updates_url;

  /**
   * Refresh interval for wireframe updates
   *  - default value is 3 minutes
   *
   * @type Number
   */
  var refresh_timeout = 1000 * 60 * 3;

  /**
   * How often we have to check if session is near expiring
   * - default value is 1 minute
   *
   * @type {number}
   */
  var session_interval = 1000 * 60;

  /**
   * If session is older then this value, we have to refresh it
   *  - default value is 10 minutes
   *
   * @type Number
   */
  var session_refresh_threshold = 1000 * 60 * 10;

  /**
   * Number between 1 and 10 seconds indicating delay in which
   * session checking will be initialized
   *
   * @type Number
   */
  var session_interval_delay = Math.floor((Math.random()*10)+1) * 1000;

  /**
   * Interval started
   *
   * @type Boolean
   */
  var interval_started = false;

  /**
   * Remember when the last update was triggered
   *
   * @var integer
   */
  var last_triggered_on = false;

  /**
   * Time of first start
   *
   * @type Boolean
   */
  var initialized_on = false;

  // initialize session preserving code with delay
  setTimeout(function () {

    // check if we need to refresh the session
    setInterval(function () {
      // check if name is existing
      var session_timestamp_cookie_name = App.Config.get('session_timestamp_cookie');
      if (!session_timestamp_cookie_name) {
        return false;
      } // if

      // get the last session refresh timestamp
      var session_timestamp_cookie_value = $.cookie(session_timestamp_cookie_name) * 1000;

      // check how old is session
      var session_is_old = $.now() - session_timestamp_cookie_value;

      // if we're over refresh threshold, refresh it
      if (session_is_old >= session_refresh_threshold) {
        if (updates_timeout) {
          clearTimeout(updates_timeout);
        } // if

        App.Wireframe.Updates.get();
      } // if

    }, session_interval);

  }, session_interval_delay);
  
  // Public interface
  return {
    
    /**
     * Subscribe specific set of update callbacks
     *
     * @param String name
     * @param Function parepare_request
     * @param Function handle_response
     */
    'subscribe' : function(name, parepare_request, handle_response) {
      update_callbacks[name] = {
        'parepare_request' : parepare_request,
        'handle_response' : handle_response
      };
    },
    
    /**
     * Unsubscribe specific set of callbacks
     *
     * @param String name
     */
    'unsubscribe' : function(name) {
      if(typeof(update_callbacks[name]) == 'object') {
        update_callbacks[name] = null;
      } // if
    },
    
    /**
     * Start checking given URL for wireframe updates
     *
     * @param String url
     * @param Integer refresh_interfal
     */
    'start' : function(url) {
      updates_url = url;
      interval_started = true;

      if (!initialized_on) {
        initialized_on = $.now();
      } // if

      // by default we trigger first update after refresh_timeout
      var trigger_timeout = refresh_timeout;
      if (last_triggered_on) {
        trigger_timeout = refresh_timeout - ($.now() - last_triggered_on);
      } else {
        trigger_timeout = refresh_timeout - ($.now() - initialized_on);
      } // if

      // check if we maybe have negative trigger timeout
      // (which means we need to trigger event right away)
      trigger_timeout = trigger_timeout < 0 ? 0 : trigger_timeout;

      if (!trigger_timeout) {
        App.Wireframe.Updates.get();
      } else {
        updates_timeout = window.setTimeout('App.Wireframe.Updates.get()', trigger_timeout);
      } // if
    },
    
    /**
     * Unregister interval
     */
    'stop' : function() {
      interval_started = false;

      if(updates_timeout) {
        window.clearTimeout(updates_timeout);
      } // if
    }, 
    
    /**
     * Call server and get updates
     * 
     * @param Object collected_data
     */
    'get' : function(collected_data) {
      if(typeof(updates_url) == 'undefined') {
        return false;
      } // if

      last_triggered_on = $.now();

      if(typeof(collected_data) != 'object') {
        collected_data = {};
      } // if

      App.each(update_callbacks, function(key, callback) {
        if(typeof(callback) == 'object' && typeof(callback['parepare_request']) == 'function') {
          callback['parepare_request'](collected_data);
        } // if
      });
      
      var data = {
        'submitted' : 'submitted'
      };

      App.each(collected_data, function(k, v) {
        data['data[' + k + ']'] = v;
      });
      
      $.ajax({
        'url' : updates_url, 
        'type' : 'post', 
        'data' : data,
        'async' : typeof(data['on_unload']) != 'undefined' && data['on_unload'] ? false : true,
        'defaultErrorHandler' : false,
        'complete' : function () {
          if (interval_started) {
            updates_timeout = window.setTimeout('App.Wireframe.Updates.get()', refresh_timeout);
          } // if
        },
        'success' : function(response) {
          if(response === 'on_unload') {
            return;
          } // if

          if(typeof(response) != 'object') {
            response = {};
          } // if

          App.each(update_callbacks, function(key, callback) {
            if(typeof(callback) == 'object' && typeof(callback['handle_response']) == 'function') {
              callback['handle_response'](response);
            } // if
          });

          if(typeof(response['status_bar_badges']) == 'object' && response['status_bar_badges']) {
            App.each(response['status_bar_badges'], function(status_bar_item, badge_value) {
              App.Wireframe.Statusbar.setItemBadge('statusbar_item_' + status_bar_item, badge_value);
            });
          } // if

          if(typeof(response['menu_bar_badges']) == 'object' && response['menu_bar_badges']) {
            App.each(response['menu_bar_badges'], function(menu_bar_item, badge_value) {
              App.Wireframe.MainMenu.setItemBadge(menu_bar_item, badge_value);
            });
          } // if
        }
      });
    }
    
  };
  
}();

//// Make sure that we refresh the wireframe data (send what we collected) on unload
//$(window).unload(function() {
//  App.Wireframe.Updates.get({
//    'on_unload' : true
//  });
//});

/**
 * Event mangement system
 */
App.Wireframe.Events = function () {
  
  /**
   * Events container
   *
   * @var jQuery
   */
//  var event_container = $('<div class="events_container"></div>');
  var event_container = $('body:first');

  /**
   * Application namespace
   *
   * @var String
   */
  var base_namespace = '.activecollab_events';
  
  // Public interface
  return {
    
    /**
     * Bind event handler
     *
     * @param String event
     * @param Function callback
     */
    'bind' : function (event, callback) {
      var events = event.split(' ');
      var new_events = [];
      $.each(events, function (index, event_name) {
        if (event_name = $.trim(event_name)) {
          new_events.push(event_name + base_namespace);
        } // if
      });      
      
      event_container.bind(new_events.join(' '), callback);
    },
    
    /**
     * Unbind event handlers
     *
     * @param String event
     */
    'unbind' : function (event) {
      if (event == undefined) { 
        // unbind all
        event_container.unbind(base_namespace);
        return true;
      } // if
      
      var events = event.split(' ');
      var new_events = [];
      $.each(events, function (index, event_name) {
        if (event_name = $.trim(event_name)) {
          new_events.push(event_name + base_namespace);
        } // if
      });
          
      event_container.unbind(new_events.join(' '));
    },
    
    /**
     * Trigger specific event
     *
     * @param String event
     * @param mixed params
     */
    'trigger' : function (event, params) {
      
      // if we are in development, and console is availabled
      if (App.isInDevelopment() && typeof(console) != 'undefined' && console && console.log) {
        if (jQuery.inArray(event, ['history_state_changed', 'content_updated', 'single_content_updated', 'window_resize', 'keydown', 'window_resize_start', 'window_resize_end', 'navigate_away']) == -1) {
          console.log('EVENT triggered', event, params);
        } // if
      } // if
      
      if (event == undefined) { 
        event = ''; 
      } // if
      
      event_container.trigger(event+base_namespace, params);
    },
    
    /**
     * backup handlers with event
     */
    'backup' : function (event) {
      if (event == undefined) {
        event = '';
      } // if

      if (event.indexOf('.') == -1) {
        var namespace = base_namespace.substring(1);
        var event_name = event;
      } else {
        var event_name = $.trim(event.substr(0, event.indexOf('.')));
        var namespace = base_namespace.substring(1) + $.trim(event.substr(event.indexOf('.')));
      } // if
      
      var events = event_container.data('events');
      
      var matching_handlers = {};
      $.each(events, function (event_name, event_handlers) {
        $.each(event_handlers, function (handler_index, handler) {
          if ((handler.type == event_name || !event_name) && (handler.namespace == namespace)) {
            if (!matching_handlers[event_name]) {
              matching_handlers[event_name] = new Array ();
            } // if
            matching_handlers[event_name].push(handler);
          } // if
        });
      });
      
      return matching_handlers;
    },
    
    /**
     * Restore events
     */
    'restore' : function (backup) {
      $.each(backup, function (event_name, handlers) {
        $.each(handlers, function (handler_index, handler) {
          var temp_base_namespace = base_namespace.substr(1);
          
          if (handler.namespace.indexOf(temp_base_namespace) == 0) {
            if (handler.namespace != temp_base_namespace) {
              event_name = event_name + '.' + handler.namespace.substr(temp_base_namespace.length + 1);
            } // if
            App.Wireframe.Events.bind(event_name, handler['handler']);
          } // if
        });
      });
    }
  };
  
}();

/**
 * Wireframe widgets management interface
 *
 * @return {Object}
 */
App.Wireframe.Widgets = function() {

  /**
   * Cached array of loaded widgets
   *
   * @type {Array}
   */
  var loaded_widgets = [];

  return {

    /**
     * Return array of loaded widgets
     *
     * @return {Array}
     */
    'getLoadedWidgets' : function() {
      return loaded_widgets;
    }, // getLoadedWidgets

    /**
     * Returns true if $widget is loaded
     *
     * @param String widget
     * @return {Boolean}
     */
    'isLoaded' : function(widget) {
      return loaded_widgets.indexOf(widget) !== -1;
    },

    /**
     * Set $widget as loaded
     *
     * @param String widget
     */
    'setAsLoaded' : function(widget) {
      if(loaded_widgets.indexOf(widget) === -1) {
        loaded_widgets.push(widget);

        if (App.Config.get('application_mode') == 'in_development' && App.debug_widgets) {
          console.info('Loading ' + widget + ' widget');
        } // if
      } // if
    },

    /**
     * Set stylesheet for a given widget
     *
     * @param String widget
     * @param String css_content
     */
    'setWidgetStylesheets' : function(widget, css_content) {
      if (App.Wireframe.Widgets.isLoaded(widget)) {
        return true;
      } // if

      var head = $('head');
      if(head.find('style#' + widget + '_widget_stylesheets').length < 1) {
        head.append('<style type="text/css" id="' + widget + '_widget_stylesheets">' + css_content + '</style>');
      } // if
    },

    /**
     * Set javascript content
     *
     * @param String widget
     * @param String js_content
     */
    'setWidgetJavaScript' : function(widget, js_content) {
      if (App.Wireframe.Widgets.isLoaded(widget)) {
        if (App.Config.get('application_mode') == 'in_development') {
          console.info('Skipped ' + widget + ' - already loaded');
        } // if
        return true;
      } // if

      var head = $('head');
      if(head.find('script#' + widget + '_widget_javascript').length < 1) {
        // add the script tag old fashioned way
        var script_tag = document.createElement("script");
        script_tag.id = widget + '_widget_javascript';
        script_tag.type  = "text/javascript";
        script_tag.text  = js_content;

        var document_head = document.head ? document.head : document.getElementsByTagName('head')[0];
        document_head.appendChild(script_tag);
      } // if
    }
  };

}();

/**
 * Default handler for flyout error (flash error message)
 *
 * @param Object event
 * @param string message
 * @param Object xhr
 */
App.Wireframe.Events.bind('async_operation_error', function(event, message, xhr) {
  App.Wireframe.Flash.error(message);
});

/**
 * Handle escape key
 *
 * @type {handleEscape}
 */
App.Wireframe.handleEscape = function () {

  /**
   * Handlers
   *
   * @type {Array}
   */
  var handlers = [];

  /**
   * Is tracking initalized
   *
   * @type {Boolean}
   */
  var initialized = false;

  /**
   * Last triggered
   *
   * @type {boolean}
   */
  var last_triggered = false;

  /**
   * Trigger delay
   *
   * @type {number}
   */
  var trigger_delay = 300;

  /**
   * Start handling
   */
  var startHandling = function () {
    App.Wireframe.Events.bind('keydown.handleEscape', function (event, key_event) {
      // check if pressed key is escape
      if (!key_event || (key_event['keyCode'] != 27) || !handlers.length) {
        return true;
      } // if

      // don't let event trigger more often then trigger_delay
      var now = $.now();
      if (last_triggered && (now - last_triggered) < trigger_delay) {
        return false;
      } // if

      // remember time of last trigger
      last_triggered = now;

      // execute proper handler
      for (var index = (handlers.length - 1); index >= 0; index++) {
        if (handlers[index]['handler']() === false) {
          break;
        } // if
      } // for
    });

    initialized = true;
  }; // startHandling

  /**
   * Stop handling
   */
  var stopHandling = function () {
    App.Wireframe.Events.unbind('keydown.handleEscape');
    initialized = false;
  }; // stopHandling

  // public
  return {

    /**
     * Add handler
     *
     * @param scope
     * @param handler
     */
    'add' : function (scope, handler) {
      handlers.push({
        'handler' : handler,
        'scope' : scope
      });

      if (!initialized) {
        startHandling();
      } // if
    },

    /**
     * Remove scope
     *
     * @param String scope
     */
    'remove' : function (scope) {
      $.each(handlers, function (index, handler) {
        if (handler['scope'] == scope) {
          handlers.splice(index, 1);
        } // if
      });

      if (initialized && !handlers.length) {
        stopHandling();
      } // if
    }
  };
}();

// handle keydown event
$('body').keydown(function (event) {
  App.Wireframe.Events.trigger('keydown', arguments);
});