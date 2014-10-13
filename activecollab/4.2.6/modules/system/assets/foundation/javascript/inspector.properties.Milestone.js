/**
 * Milestone property handler
 */
App.Inspector.Properties.Milestone = function (object, client_interface) {
  var wrapper = $(this);
  
  if (object.milestone && object.milestone.id) {
    var check_string = object.milestone.id + App.clean(object.milestone.name);
  } else {
    var check_string = 'no_milestone';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper_row = wrapper.parents('div.property:first');
  wrapper.empty();
  
  if (object.milestone && object.milestone.id) {
    var milestone_property = $('<a href="' + object.milestone.permalink + '" class="quick_view_item quick_view_item_invert">' + App.clean(object.milestone.name) + '</a>');
  } else {
    var milestone_property = $('<span>' + App.lang('Milestone not set') + '</span>');
  } // if
  
  if (object.permissions.can_edit && client_interface == 'default') {
    wrapper_row.show();
    
    var trigger_wrapper = $('<span class="inspector_edit_wrapper"></span>');
    var trigger = $('<a href="' + object.urls.update_milestone + '" class="editor_trigger" title="' + App.lang('Change Milestone') + '"><img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/inspector-edit.png', 'environment') + '" alt="" /></a>');
    
    trigger_wrapper.append(milestone_property).append(trigger).appendTo(wrapper);
    
    trigger.flyoutForm({
      'success_message' : App.lang('Milestone updated successfully'),
      'success_event' : object.event_names.updated,
      'width' : 'narrow'
    });

  } else {
    milestone_property.appendTo(wrapper);
    if (object.milestone && object.milestone.id) {
      wrapper_row.show();
    } else {
      wrapper_row.hide();
    } // if
  } // if
}; // Milestone property