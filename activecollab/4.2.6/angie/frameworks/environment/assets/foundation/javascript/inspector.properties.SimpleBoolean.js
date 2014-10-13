/**
 * Simple boolean property handler
 */
App.Inspector.Properties.SimpleBoolean = function (object, client_interface, boolean_field, true_label, false_label) {
  var wrapper = $(this);
  var boolean_property = '';
    
  if (boolean_field.indexOf('.') == -1) {
    boolean_property = object[$.trim(boolean_field)];
  } else {
    boolean_field = boolean_field.split('.');
    boolean_property = object;
    $.each(boolean_field, function (index, boolean_step) {
      boolean_property = boolean_property[$.trim(boolean_step)];
    });
  } // if
  
  var check_string = boolean_property + '_boolean';
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if  
  wrapper.attr('check_string', check_string);
  
  if (!!boolean_property) {
    wrapper.empty().append(true_label);    
  } else {
    wrapper.empty().append(false_label);
  } // if
}; // SimpleBoolean property