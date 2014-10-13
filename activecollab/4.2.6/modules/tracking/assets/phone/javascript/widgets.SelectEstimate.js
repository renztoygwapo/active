/**
 * Select estimate value
 */
App.widgets.SelectEstimate = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize select box behavior
     *
     * @param String select_id
     */
    init : function(select_id) {
      if (typeof(select_id) == 'object') {
        var select = select_id;
      } else {
        var select  = $("#" + select_id);        
      } // if
      
      select.change(function() {
        var selected_option = select.find('option:selected');
        
        if(selected_option.attr('value') == 'other') {
          var input = prompt(App.lang('Please insert estimate, in hours. Example: 0.25, 1, 8 etc'), '');

          if(input) {
            var value = App.Wireframe.Utils.parseEstimate(input);
          
            if(value >= 0) {
              if(select.find('option[value=' + value + ']').length == 0) {
                var option = $('<option value="' + value + '" class="other">' + App.Wireframe.Utils.formatEstimate(value) + '</option>');

                if(select.find('option.other').length > 0) {
                  select.find('option.other:last').after(option);
                } else {
                  select.find('option.before_other').after(option);
                  option.after('<option value=""></option>');
                } // if
              } // if

              select.val(value);
            } else {
              select.val('');
            } // if
          } // if
        } // if
      });
    }
    
  };
  
}();