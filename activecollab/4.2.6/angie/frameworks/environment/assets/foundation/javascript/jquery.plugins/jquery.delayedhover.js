/**
 * hoverout with a delay
 * 
 * @param function hover_in_function - function that will be executed on hover over event
 * @param function hover_out_function - function that will be executed on hover out event
 * @param delay_timeout - time in miliseconds that will be treated as hover out when mouse leaves element
 *
 * @author Goran Radulovic <goran.radulovic@gmail.com>s
 */
jQuery.fn.delayedHover = function(hover_in_function, hover_out_function, delay_timeout) {
  return this.each(function() {
    var element = $(this);
    var _this = this;
    element.delayed_hover_timeout = null;
    
    element.hover(function () {
      if (element.delayed_hover_timeout != null) {
        clearTimeout(element.delayed_hover_timeout);
        element.delayed_hover_timeout = null;
      } else {
        if (typeof hover_in_function == 'function') {
          hover_in_function.apply(_this);
        }
      } // if
    }, function () {
      if (element.delayed_hover_timeout) {
        clearTimeout(element.delayed_hover_timeout);
        element.delayed_hover_timeout = null;
      } // if
           
      element.delayed_hover_timeout = setTimeout(function () {       
        if (typeof hover_out_function == 'function') {
          clearTimeout(element.delayed_hover_timeout);
          element.delayed_hover_timeout = null;
          hover_out_function.apply(_this);
        } // if
      }, delay_timeout)
    });
  });
};