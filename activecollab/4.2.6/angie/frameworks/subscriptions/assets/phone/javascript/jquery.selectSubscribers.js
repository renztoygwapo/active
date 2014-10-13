/**
 * Select subscribers
 */
jQuery.fn.selectSubscribers = function(s) {
  var settings = jQuery.extend({  }, s);
  
  var removed_options = [];
  
  return this.each(function() {
    var wrapper = $(this);
    
    wrapper.find('optgroup option').each(function () {
      var option_element = $(this);
      
      if($.inArray(option_element.val(), s.can_see_private) !== -1) {
        option_element.attr('can_see_private', 1);
      } else {
        option_element.attr('can_see_private', 0);        
      } // if
    });
    
    /**
     * filter list by visibility
     * 
     * @param integer visibility
     * @return null
     */
    var filter_by_visibility = function (visibility) {
      var wrapper = $(this);
      
      wrapper.find('optgroup').each(function () {
        var group = $(this);
        group.show();
        
        if(visibility == 1) {
          group.find('option').show();
          
        	if(removed_options.length) {
        		$.each(removed_options, function(k, removed_option) {
        			if(removed_option.group == group.attr('label')) {
        				group.append($(removed_option.option));
        				
        				// Cut removed_option from the array
        				removed_options = $.grep(removed_options, function(value) {
        					return value != removed_option;
        				});
        			} // if
        		});
        	} // if
        } else {
        	var options_to_remove = group.find('option[can_see_private="0"]');
        	
        	// Collect all removed options
        	if(options_to_remove.length) {
        		$.each(options_to_remove, function(k, option_to_remove) {
        			removed_options.push({
        				'group' : group[0].label,
        				'option' : option_to_remove.outerHTML
        			});
        		});
        	} // if
          options_to_remove.remove();
          
          group.find('option[can_see_private="1"]').show();
        } // if
      });
      
      wrapper.selectmenu('refresh', true);
    }; // filter_by_visibility
    
    // provide public interface for this function
    this.filterByVisibility = filter_by_visibility;
  });
  
};