/**
 * Created on property handler
 */
App.Inspector.Widgets.Assignees = function (object, client_interface) {
  var wrapper = $(this), check_string;
  
  if (object.assignee) {
    check_string = 'responsible_' + object.assignee.id + '.' + (object.other_assignees && object.other_assignees.length ? object.other_assignees.length : '');
  } else if (!object.assignee && (object.other_assignees && object.other_assignees.length)) {
    check_string = 'responsible_0.' + object.other_assignees.length;
  } else {
    check_string = 'no_assignees';
  } // if

  check_string += object.permissions.can_edit ? "1" : "0";
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  
  wrapper.empty();
    
  if (object.permissions.can_edit) {
    var trigger = $('<a href="' + object.urls.manage_assignees + '" class="widget_trigger" title="' + App.lang('Manage Assignees') + '"></a>');
  } else {
    var trigger = $('<span class="widget_trigger" ></span>');
  } // if

  trigger.appendTo(wrapper);
  
  trigger.append('<span class="widget_title">' + App.lang('Assignees') + '</span>');

  if (object.assignee) {
    trigger.append('<span class="assignees_widget_avatar"><img src="' + object.assignee.avatar.large + '" alt="" /></span>');
    trigger.append('<span class="assignees_responsible">' + App.clean(object.assignee.display_name) + '</span>');
    if (object.other_assignees) {
      trigger.append('<span class="assignees_other_assignees">' + App.lang('and :num more', { 'num' : object.other_assignees.length }) + '</span>');
    } // if
  } else if (!object.assignee && (object.other_assignees && object.other_assignees.length)) {
    trigger.append('<span class="assignees_widget_avatar"><img src="' + App.Wireframe.Utils.imageUrl('icons/32x32/assignees.png', 'assignees') + '" alt="" /></span>');
    trigger.append('<span class="assignees_responsible">' + App.lang('No one is<br />responsible') + '</span>');
    trigger.append('<span class="assignees_other_assignees">' + App.lang(':num other assignee(s)', { 'num' : object.other_assignees.length }) + '</span>');
  } else {
    trigger.append('<span class="assignees_widget_avatar"><img src="' + App.Wireframe.Utils.imageUrl('icons/32x32/assignees.png', 'assignees') + '" alt="" /></span>');
    trigger.append('<span class="assignees_responsible">' + App.lang('No one is<br />assigned') + '</span>');
  } // if
  
    
  if (object.permissions.can_edit && client_interface == 'default') {
    trigger.flyoutForm({
      'success_message' : App.lang('Assignees have been successfully updated'),
      'success_event' : object.event_names.updated,
      'width': 'narrow'
    });
  } // if
}; // CreatedOn property