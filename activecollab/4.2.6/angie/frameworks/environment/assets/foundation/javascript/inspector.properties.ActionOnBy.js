/**
 * Created on property handler
 */
App.Inspector.Properties.ActionOnBy = function (object, client_interface, widget_field_prefix, localize_time) {
  var wrapper = $(this);
  
  if (!widget_field_prefix) {
    widget_field_prefix = 'created';
  } // if
 
  localize_time = (typeof localize_time === "undefined") ? false : true;
  
  var action_on = App.Inspector.Utils.getFieldValue(widget_field_prefix + '_on', object);
  var action_by = App.Inspector.Utils.getFieldValue(widget_field_prefix + '_by', object);  
  
  if (!action_on || !action_by) {
    var check_string = 'not-set';
  } else {
    var check_string = action_on.formatted_date + App.clean(action_by.display_name);
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if  
  wrapper.attr('check_string', check_string);
  
  wrapper_row = wrapper.parents('div.property:first');
  
  if (!action_on || !action_by) {
    wrapper_row.hide();
    return true;
  } // if
  
  wrapper_row.show();

  var created_on_date = localize_time ? action_on.formatted_date : action_on.formatted_date_gmt;
  
  var lang_params = {
    'when' : created_on_date,
    'permalink' : action_by.permalink,
    'display_name' : App.clean(action_by.display_name)
  };

  var property_value;
  if (action_by['id']) {
    property_value = App.lang(':when by <a href=":permalink" class="quick_view_item">:display_name</a>', lang_params);
  } else {
    property_value = App.lang(':when by <a href=":permalink">:display_name</a>', lang_params);
  } // if

  wrapper.empty().append(property_value);
};