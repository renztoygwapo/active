/**
 * Object time widget
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
     * Initialise object time
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
        
        var item = options.object;
        
        // generate label text
        var label_text = generate_label_string(item);

        if (options['interface'] == 'printer') {
          wrapper.append('<span class="object_tracking_text">' + label_text + '</span> ');
          return true;
        } // if
        
        // if label is shown, show it
        if (options.show_label === undefined || options.show_label) {
          wrapper.append('<span class="object_tracking_text">' + label_text + '</span> ');
        } // if
        
        // Default interface
        if(options['interface'] == App.Wireframe.Utils.INTERFACE_DEFAULT) {
	        // add icon for tracking
	        var anchor = $('<a href="' + item.urls.tracking + '" title="' + label_text + '"><img src="' + get_icon_url(item) + '" alt="" /></a>').appendTo(wrapper);
	        
	        anchor.objectTimeExpensesFlyout(updated_callback_function);
        } // if
      });
    }
  };
  
  /**
   * function which will be triggered when something in dialog has changed
   * 
   * @param time
   * @param expenses
   * @param formatted
   * @returns null
   */
  var updated_callback_function = function(time, expenses, formatted) {
    var wrapper = $(this).parent();
    wrapper.find('a').attr('title', formatted);
    
    if (wrapper.find('span.object_tracking_text').length) {
      wrapper.find('span.object_tracking_text').html(formatted);
    } // if
    
    if (time || expenses) {
      wrapper.find('img').attr('src', App.Wireframe.Utils.imageUrl('icons/12x12/object-time-active.png', 'tracking'));
    } else {
      wrapper.find('img').attr('src', App.Wireframe.Utils.imageUrl('icons/12x12/object-time-inactive.png', 'tracking'));
    } // if
  }; // updated_callback_function
    
  /**
   * Generates the label string for item
   * 
   * @param Object item
   * @return string
   */
  var generate_label_string = function (item) {
    // object time part of the label
    var time_string = false;
    
    if (item.object_time) {
      time_string = App.lang('Time: :totalh', {
        'totalh' : item.object_time 
      });
    } // if
    
    var expenses_string = false;
    
    if (item.object_expenses) {
      expenses_string = App.lang('Expenses: :total', {
        'total' : item.object_expenses
      });
    } // if
    
    if (time_string && expenses_string) {
      return time_string + '. ' + expenses_string;
    } else if (time_string) {
      return time_string;
    } else if (expenses_string) {
      return expenses_string;
    } // if
    
    return App.lang("No Time");
  }; // generate_label_string
  
  /**
   * Get icon url depending of item tracking state
   * 
   * @return string
   */
  var get_icon_url = function (item) {
    if (item.object_time || item.object_expenses) {
      return App.Wireframe.Utils.imageUrl('icons/12x12/object-time-active.png', 'tracking');
    } else {
      return App.Wireframe.Utils.imageUrl('icons/12x12/object-time-inactive.png', 'tracking');
    } // if
  }; // get_icon_url

  ////////////////////////////////////////////////////////// PLUGIN INITIAL DATA ///////////////////////////////////////////////////////////  
  
  /**
   * Plugin name
   * 
   * @var String
   */
  var plugin_name = 'objectTime';
        
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