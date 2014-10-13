/**
 * Custom field property handler
 */
App.Inspector.Properties.CustomField = function (object, client_interface) {
  var wrapper = $(this);
  var wrapper_row = wrapper.parents('div.property:first');
  var property_name = wrapper_row.attr('property_name');

  if (typeof(object['custom_fields']) == 'object' && object['custom_fields'] && typeof(object['custom_fields'][property_name]) != 'undefined') {
    if(typeof(object['custom_fields'][property_name]['value']) == 'string') {
      var check_string = property_name + App.clean(object['custom_fields'][property_name]['value']);
    } else {
      var check_string = property_name + object['custom_fields'][property_name]['value'];
    } // if
  } else {
    var check_string = 'no_value';
  } // if

  if(wrapper.attr('check_string') == check_string) {
    return true;
  } // if

  // Prepare wrapper
  wrapper.attr('check_string', check_string).empty();

  if(object['custom_fields'][property_name]['value']) {
    if(typeof(object['custom_fields'][property_name]['value']) == 'string') {
      wrapper.append(App.clean(object['custom_fields'][property_name]['value']));
    } else {
      wrapper.append(object['custom_fields'][property_name]['value']);
    } // if

    wrapper_row.show();
  } else {
    wrapper_row.hide();
  } // if

}; // CustomField