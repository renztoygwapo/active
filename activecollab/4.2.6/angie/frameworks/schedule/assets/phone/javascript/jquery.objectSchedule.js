/**
 * Object schedule widget
 *
 */
(function($) {
  
  /**
   * Public methods
   * 
   * @var Object
   */
  var public_methods = {
      
    /**
     * Initialise object schedule
     * 
     * @param Object options
     *      object
     *      interface
     * @returns jQuery
     */
    init : function(options) {
      return this.each(function () {
        var wrapper = $(this);
        var wrapper_dom = this;
        
        var label = get_label(options.start_on, options.due_on);
        
        if (options.show_label) {
          var label_span = $('<span class="scheduled_label">' + label + '</span>').appendTo(wrapper);
          wrapper.append(' ');
        } // if
        
        // Default interface
        if(options['interface'] == App.Wireframe.Utils.INTERFACE_DEFAULT) {
        	if(options.can_reschedule) {
	          var anchor = $('<a href="' + options.reschedule_url + '" title="' + label + '"></a>').appendTo(wrapper);
	          var icon = $('<img src="' + get_icon_url.apply(this, [options.start_on || options.due_on]) + '" />').appendTo(anchor);
	          anchor.flyoutForm({
	            'width' : 'narrow',
	            'title' : App.lang('Reschedule') + ': ' + App.clean(options.object_name),
	            'success_event' : options.event_name
	          });
	        } else {
	          var icon = $('<img src="' + get_icon_url.apply(this, [options.start_on || options.due_on]) + '" />').appendTo(wrapper);
	        } // if
        	
        	$.each(options.listen_events, function (index, event_name) {
        	  options.listen_events[index] = options.listen_events[index] + '.single';
        	});
	        
	        App.Wireframe.Events.bind(options.listen_events.join(' '), function (event, object) {
	          // wrong object
	          if (object.id != options.object_id) {
	            return false;
	          } // if
	          
	          var label = get_label(object.start_on, object.due_on);
	          
	          // update label
	          if (options.show_label) {
	            label_span.text(label);
	          } // if
	          
	          // update ancor title
	          if (options.can_reschedule) {
	            anchor.attr('title', label);
	          } // if
	          
	          // update icon
	          icon.attr('src', get_icon_url(object.start_on || object.due_on));;
	        });
        } // if
        
        return true;
      });
    }
  };
    
  /**
   * Get label
   * 
   * @param Object start_on
   * @param Object due_on
   * @return string
   */
  var get_label = function (start_on, due_on) {
    if (due_on && start_on) {
      return start_on.formatted_date_gmt + ' - ' + due_on.formatted_date_gmt;
    } else if (start_on) {
      return start_on.formatted_date_gmt;            
    } else if (due_on) {
      return due_on.formatted_date_gmt;
    } else {
      return App.lang('Not scheduled');
    } // if
  }; // get_label
      
  /**
   * Get icon url depending of item tracking state
   * 
   * @param void
   * @return null
   */
  var get_icon_url = function (scheduled) {
    if (scheduled) {
      return App.Wireframe.Utils.imageUrl('object-schedule-active.png', 'schedule');
    } else {
      return App.Wireframe.Utils.imageUrl('object-schedule-inactive.png', 'schedule');
    } // if
  }; // get_icon_url

  ////////////////////////////////////////////////////////// PLUGIN INITIAL DATA ///////////////////////////////////////////////////////////  
  
  /**
   * Plugin name
   * 
   * @var String
   */
  var plugin_name = 'objectSchedule';
        
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