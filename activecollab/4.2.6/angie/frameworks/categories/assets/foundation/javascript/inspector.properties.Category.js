/**
 * Category property handler
 */
App.Inspector.Properties.Category = function (object, client_interface) {
  var wrapper = $(this);
  
  if (object.category && object.category.id) {
    var check_string = object.category.id + App.clean(object.category.name);
  } else {
    var check_string = 'no_category';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty();
  wrapper_row = wrapper.parents('div.property:first');
  
  if (object.category && object.category.id) {
    var category_property = $('<span>' + App.clean(object.category.name) + '</span>');
  } else {
    var category_property = $('<span>' + App.lang('No Category') + '</span>');
  } // if
  
  if (object.permissions.can_edit && client_interface == 'default') {
    wrapper_row.show();
    
    var trigger_wrapper = $('<span class="inspector_edit_wrapper"></span>');
    var trigger = $('<a href="' + object.urls.update_category + '" class="editor_trigger" title="' + App.lang('Change Category') + '"><img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/inspector-edit.png', 'environment') + '" alt="" /></a>');
    
    trigger_wrapper.append(category_property).append(trigger).appendTo(wrapper);
    
    trigger.flyoutForm({
      'success_message' : App.lang('Category updated successfully'),
      'success_event' : object.event_names.updated,
      'width' : 'narrow'
    });
    
  } else {
    wrapper.append(category_property);
    if (object.category && object.category.id) {
      wrapper_row.show();
    } else {
      wrapper_row.hide();      
    } // if
  } // if

}; // Category property