/**
 * Object inspector
 */
(function($) {
  
  /**
   * Public methods
   * 
   * @var Object
   */
  var public_methods = {
      
    /**
     * Initialise object inspector
     * 
     * @param Object options
     * @returns jQuery
     */
    init : function(options) {
      return this.each(function () {
        
        var indicators = options.indicators;
        var widgets = options.widgets;
        var properties = options.properties;
        
        this.insp_data = {'settings' : jQuery.extend({}, settings, options)};
        
        if (!this.insp_data.settings.object || typeof(this.insp_data.settings.object) != 'object' || $.isEmptyObject(this.insp_data.settings.object)) {
          return false;
        } // if
                       
        // render initial wireframe
        render_initial_wireframe.apply(this);
        // refresh inpector
        refresh_inspector.apply(this, [options.object]);
        // catch all events
        handle_events.apply(this);
      });
    },
    
    /**
     * Refresh object inspector
     */
    refresh : function () {
      return this.each(function () {
        refresh_from_server.apply(this);
      });
    },

    /**
     * Get inspector event scope
     *
     * @return string
     */
    getEventScope : function () {
      return this.get(0).insp_data.settings.event_scope;
    }
  };
  
  /**
   * Render initial wireframe
   */
  var render_initial_wireframe = function () {
    var wrapper = $(this);
    if (!this.insp_data.settings.renderer) {
      this.insp_data.settings.renderer = 'App.Inspector.Renderers.Default';
    } // if
    
    eval('var renderer = ' + this.insp_data.settings.renderer);
    renderer(this);
    
    wrapper.attr({
      'object_id' : this.insp_data.settings.object['id'],
      'object_class' : this.insp_data.settings.object['class']
    });
  }; // render_initial_wireframe

  
  /**
   * Render properties 
   * 
   * @param Object properties
   */
  var refresh_properties = function (properties) {
    var wrapper = $(this);
    var wrapper_dom = this;
    
    if ($.isEmptyObject(properties)) {
      return false;
    } // if
        
    $.each(properties, function (property_id, property) {
      if (!property.handler) {
        return true;
      } // if
      
      var field_id = wrapper.attr('id') + '_property_' + property_id;
      var property_field = wrapper_dom.insp_data.properties.find('div#' + field_id + ' div.content');
      if (!property_field.length) {
        var rendered_property = $('<div id="' + field_id + '" property_name="' + property_id + '" class="property"><div class="label">' + property.label + '</div><div class="content"></div></div>').appendTo(wrapper_dom.insp_data.properties);
        var property_field = rendered_property.find('div.content:first');        
      } // if
      
      eval('var handler = ' + property.handler.render);  
      handler(property_field, wrapper_dom.insp_data.settings.object, wrapper_dom.insp_data.settings['interface']);
    });
  }; // refresh_properties
  
  /**
   * Render inspector indicators
   */
  var refresh_indicators = function (indicators) {
    var wrapper = $(this);
    var wrapper_dom = this;
    
    if ($.isEmptyObject(indicators)) {
      return false;
    } // if
    
    $.each(indicators, function (indicator_id, indicator) {
      if (!indicator.handler) {
        return true;
      } // if
      
      var field_id = wrapper.attr('id') + '_indicator_' + indicator_id;
      var indicator_field = wrapper_dom.insp_data.indicators.find('li#' + field_id + ' span.content');
      if (!indicator_field.length) {
        var rendered_indicator = $('<li id="' + field_id + '"><span class="content"></span></li>').appendTo(wrapper_dom.insp_data.indicators);
        var indicator_field = rendered_indicator.find('span.content:first');
      } // if
      
      eval('var handler = ' + indicator.handler.render);  
      handler(indicator_field, wrapper_dom.insp_data.settings.object, wrapper_dom.insp_data.settings['interface']);
    });
  }; // refresh_indicators
  
  /**
   * Refresh inspector widgets
   */
  var refresh_widgets = function(widgets) {
    var wrapper = $(this);
    var wrapper_dom = this;
    
    var widgets_wrapper = wrapper_dom.insp_data.widgets;
    
    if ($.isEmptyObject(widgets)) {
      return false;
    } // if
    
    $.each(widgets, function (widget_id, widget) {
      if (!widget.handler) {
        return true;
      } // if
      
      var field_id = wrapper.attr('id') + '_widgets_' + widget_id;
      var widgets_field = widgets_wrapper.find('#' + field_id);
      if (!widgets_field.length) {
        if (widgets_wrapper.is('tr')) {
          widgets_field = $('<td id="' + field_id + '" class="widget"></td>').appendTo(widgets_wrapper);
        } else {
          widgets_field = $('<div id="' + field_id + '" class="widget"></div>').appendTo(widgets_wrapper);
        } // if
      } // if
      
      eval('var handler = ' + widget.handler.render);
      handler(widgets_field, wrapper_dom.insp_data.settings.object, wrapper_dom.insp_data.settings['interface']);
    });
  }; // refresh_widgets
  
  /**
   * Refresh object actions
   */
  var refresh_actions = function () {
    var wrapper_dom = this;
    var wrapper = $(this);
    
    var object = this.insp_data.settings.object;
    
    // no wrapper
    if (!(this.insp_data.actions && this.insp_data.actions.length)) {
      return false;
    } // if
    
    this.insp_data.actions.empty();
    
    wrapper.removeClass('with_actions');
    
    if (!object.options || $.isEmptyObject(object.options)) {
      return false;
    } // if

    
    var with_actions = false;
    $.each(object.options, function (option_id, option) {
      if (!option.important) {
        return true;
      } // if

      var option_wrapper = $('<li id="inspector_object_action_' + option_id + '"></li>').prependTo(wrapper_dom.insp_data.actions);
      var option_link = $('<a href="' + option.url + '" title="' + option.text + '">' + option.text + '</a>').appendTo(option_wrapper);

      if (option.onclick) {
        eval('var onclick = ' + option.onclick);

        if (typeof onclick == 'function') {
          onclick.apply(option_link[0]);
        } // if
      } // if
          
      with_actions = true;
    });
      
    if (with_actions) {
      wrapper.addClass('with_actions');
    } // if
  }; // refresh_actions
  
  /**
   * Refresh object bars
   */
  var refresh_bars = function (bars) {
    var wrapper_dom = this;
    var wrapper = $(this);
    
    var object = this.insp_data.settings.object;
    this.insp_data.bars_wrapper.empty();
    
    wrapper.removeClass('with_bars');
    
    if (!bars) {
      return false;
    } // if
    
    var with_bars = false;
    $.each(bars, function (bar_id, bar) {
      if (!bar.handler) {
        return true;
      } // if
      
      var field_id = wrapper.attr('id') + '_bars_' + bar_id;
      var bars_field = wrapper_dom.insp_data.bars_wrapper.find('div#' + field_id);
      if (!bars_field.length) {
        bars_field = $('<div id="' + field_id + '" class="bar"></div>').appendTo(wrapper_dom.insp_data.bars_wrapper);
      } // if
      
      eval('var handler = ' + bar.handler.render);
      handler(bars_field, wrapper_dom.insp_data.settings.object, wrapper_dom.insp_data.settings['interface']);
    });
    
    if (wrapper_dom.insp_data.bars_wrapper.find('div.bar').length) {
      wrapper.addClass('with_bars');
    } // if
  }; // refresh_bars
  
  /**
   * Refresh titlebar widgets 
   */
  var refresh_titlebar_widgets = function (widgets) {
    // there is no page title instance
    if (!(App && App.Wireframe && App.Wireframe.PageTitle)) {
      return false;
    } // if
    
    if (!widgets) {
      return false;
    } // if

    var wrapper_dom = this;
    var wrapper = $(this);
    
    var object = this.insp_data.settings.object;
    
    $.each(widgets, function (widget_id, widget) {
      if (!widget.handler) {
        return true;
      } // if
      
      var field_id = wrapper.attr('id') + '_titlebar_widgets_' + widget_id;
      var widget_field = $('<span id="' + field_id + '" class="titlebar_widget ' + (widget.left ? 'titlebar_widget_left' : 'titlebar_widget_right') + '"></span>');     
      eval('var handler = ' + widget.handler.render);
      handler(widget_field, wrapper_dom.insp_data.settings.object, wrapper_dom.insp_data.settings['interface']);

      if (wrapper_dom.insp_data.settings.render_scope == 'quick_view') {
        App.widgets.QuickView.addTitlebarWidget(wrapper_dom.insp_data.settings.event_scope, !widget.left, widget_id, widget_field);
      } else {
        if (widget.left) {
          App.Wireframe.PageTitle.addWidgetBefore(widget_id, widget_field);
        } else {
          App.Wireframe.PageTitle.addWidgetAfter(widget_id, widget_field);
        } // if
      } // if
    });

  };

  /**
   * Refresh the page title
   */
  var refresh_page_title = function () {
    var object = this.insp_data.settings.object;
    var name_format = this.insp_data.settings.name_format;

    // By default, use object's name
    var name = object.name;

    // Format name?
    if(typeof(name_format) == 'string' && name_format) {
      var matches = name_format.match(/(\:\w+)/g);

      if(matches) {
        var name = name_format;
        $.each(matches, function (index, match) {
          var property_name = match.substr(1);

          if(typeof(object[property_name]) != 'undefined') {
            name = name.replace(match, object[property_name]);
          } // if
        });
      } // if
    } // if

    if (this.insp_data.settings.render_scope == 'quick_view') {
      if (App && App.widgets && App.widgets.QuickView) {
        App.widgets.QuickView.updateDropDown(this.insp_data.settings.event_scope, 'quick_view_card_action_object_options', object.options);
        App.widgets.QuickView.setTitle(this.insp_data.settings.event_scope, name);
      } // if
    } else {
      // there is page title instance
      if (App && App.Wireframe && App.Wireframe.PageTitle) {
        App.Wireframe.PageTitle.updateDropDown($('#page_action_object_options:first'), object.options);
        App.Wireframe.PageTitle.set(name);

        if (object.verbose_type) {
          App.Wireframe.PageTitle.addWidgetBefore('object_type', '<span class="object_type object_type_' + object['class'].toLowerCase() + '">' + object.verbose_type + '</span>');
        } // if
      } else {
        var page_title = $('#page_title');
        if (!page_title.length) {
          return false;
        } // if

        page_title.html(App.clean(object.name));
        if (object.verbose_type) {
          page_title.prepend('<span class="title_prefix">' + object.verbose_type + ':</span>');
        } // if
      } // if
    } // if
  }; // refresh_page_title
  
  /**
   * Refresh body field
   */
  var refresh_page_body = function () {
    var wrapper = $(this);

    wrapper.parents('.object_wrapper:first').find('.object_name_content').html(App.clean(this.insp_data.settings.object.name));

    var body_content = $.trim(this.insp_data.settings.object[this.insp_data.settings.body_field + '_formatted']);

    if (this.insp_data.cached_body_content == body_content) {
      return false;
    } // if

    // cache_body_content
    this.insp_data.cached_body_content = body_content;

    var body_element = wrapper.parents('.object_wrapper:first').find('.object_body_content');
    var object_body = body_element.parents('div.object_body:first');
    if (body_element.length)  {
      if(!body_content && this.insp_data.settings.body_optional) {
        body_content = this.insp_data.settings.body_optional;
      } // if

      body_element.html(body_content);

      if ($.trim(body_content)) {
        body_element.show();
        object_body.show();
      } else {
        body_element.hide();
        // hide entire object body if there are no additional elements inside (subtasks etc), otherwise blank div element remains
        if (object_body.find('div').length == 1 && object_body.find('div').hasClass('object_body_content')) {
          object_body.hide();
        } // if
      } // if
    } // if
  }; // refresh_page_body
  
  /**
   * Refresh inspector with new data
   * 
   * @param Object object
   */
  var refresh_inspector = function (object) {
    this.insp_data.settings.object = object;
    
    refresh_page_title.apply(this);
    
    refresh_page_body.apply(this);
    
    if (this.insp_data.settings.supports_properties) {
      refresh_properties.apply(this, [this.insp_data.settings.properties]);
    } // if
    
    if (this.insp_data.settings.supports_indicators) {
      refresh_indicators.apply(this, [this.insp_data.settings.indicators]);
    } // if
    
    if (this.insp_data.settings.supports_widgets) {
      refresh_widgets.apply(this, [this.insp_data.settings.widgets]);
    } // if
    
    if (this.insp_data.settings.supports_actions) {
      refresh_actions.apply(this)
    } // if
    
    if (this.insp_data.settings.supports_bars) {
      refresh_bars.apply(this, [this.insp_data.settings.bars]);
    } // if
    
    if (this.insp_data.settings.supports_titlebar_widgets) {
      refresh_titlebar_widgets.apply(this, [this.insp_data.settings.titlebar_widgets]);
    } // if
  }; // refresh_inspector
  
  /**
   * Handle events and update fields we need to update
   */
  var handle_events = function () {
    var wrapper_dom = this;
    var listened_events = new Array();
        
    if ($.isEmptyObject(this.insp_data.settings.object.event_names)) {
      return true;
    } // if
    
    $.each(this.insp_data.settings.object.event_names, function (event_id, event_name) {
      if (event_id != 'created') {
        listened_events.push(event_name + '.' + wrapper_dom.insp_data.settings.event_scope);
      } // if
    });
                
    App.Wireframe.Events.bind(listened_events.join(' '), function (event, object) {
      handle_server_response.apply(wrapper_dom, [object]);
    });
  }; // handle_events
  
  /**
   * Refresh object from server
   */
  var refresh_from_server = function () {
    var wrapper_dom = this;
    var original_object = this.insp_data.settings.object;
    
    // we don't have view url, so we cannot refresh it
    if (!original_object || !original_object.urls || !original_object.urls.view) {
      return false;
    } // if
    
    $.ajax({
      'url' : App.extendUrl(original_object.urls.view, {
        'format' : 'application/json',
        'detailed' : 1,
        'for_interface' : 1
      }),
      'success' : function (object) {
        if (!object || typeof(object) != 'object' || $.isEmptyObject(object)) {
          return false;
        } // if

        if (object['event_names'] && object['event_names']['updated']) {
          App.Wireframe.Events.trigger(object['event_names']['updated'], [object]);
        } // if
      }
    });
  }; // refresh_from_server
  
  /**
   * Handle server response
   * 
   * @param Object response
   */
  var handle_server_response = function (object) {
    var wrapper_dom = this;
    var original_object = this.insp_data.settings.object;
    
    // if object inspector is already unloaded
    if (!$(wrapper_dom).parents('body').length) {
      return true;
    } // if

    // if provided object is really an object
    if (!object || typeof(object) != 'object' || $.isEmptyObject(object)) {
      return true;
    } // if
        
    // if we're handling the correct object
    if (!(object['id'] == original_object['id'] && object['class'] == original_object['class'])) {
      return true;
    } // if

    refresh_inspector.apply(wrapper_dom, [object]);
  }; // handle_server_response
  
  ////////////////////////////////////////////////////////// PLUGIN INITIAL DATA ///////////////////////////////////////////////////////////  
  
  /**
   * Plugin name
   * 
   * @var String
   */
  var plugin_name = 'objectInspector';
  
  /**
   * Initial inspector settings
   * 
   * @var Object
   */
  var settings = {
    'supports_properties' : true,
    'supports_widgets' : true,
    'supports_indicators' : true,
    'supports_actions' : true,
    'supports_bars' : true,
    'supports_titlebar_widgets' : true,
    'supports_body' : true,
    'body_field' : 'body',
    'name_format' : null,
    'renderer' : 'App.Inspector.Renderers.Default'
  };
        
  ///////////////////////////////////////////////////////// PLUGIN FRAMEWORK STUFF /////////////////////////////////////////////////////////
  /**
   * Register jQuery plugin and handle public methods
   * 
   * @param mixed method
   * @return null
   */
  $.fn[plugin_name] = function(method) { 
    if (public_methods[method]) {
      return public_methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method) {
      return public_methods.init.apply(this, arguments);
    } else {
      $.error('Method ' +  method + ' does not exist in jQuery.' + plugin_name);
    } // if
  };

})(jQuery);