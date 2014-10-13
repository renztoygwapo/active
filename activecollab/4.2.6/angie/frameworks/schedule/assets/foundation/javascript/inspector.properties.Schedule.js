/**
 * Schedule property handler
 */
App.Inspector.Properties.Schedule = function (object, client_interface) {
  var wrapper = $(this);
  
  if (object.due_on && object.start_on) {
    var check_string = object.due_on.timestamp + object.start_on.timestamp;
  } else if (object.due_on) {
    var check_string = object.due_on.timestamp;
  } else {
    var check_string = 'not_scheduled';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  
  var return_string = '';
  var is_scheduled = false;
  
  if (object.due_on && object.start_on) {
    return_string = object.start_on.formatted_date_gmt + ' - ' + object.due_on.formatted_date_gmt;
    is_scheduled = true;
  } else if (object.due_on) {
    return_string = object.due_on.formatted_date_gmt;
    is_scheduled = true;
  } else {
    return_string = App.lang('Not Scheduled');
  } // if
  
  wrapper.empty().append(return_string + ' ');

  if (object['class'] == 'Milestone') {
    App.widgets.MilestoneDateRange.set('milestone_date_range_' + object['id'], {
      'start_date' : object.start_on,
      'end_date' : object.due_on
    });
  } // if
  
  if (object.permissions.can_edit && client_interface == 'default') {
    var reschedule_button = $('<a href="' + object.urls.reschedule + '" class="editor_trigger always_visible"><img src="' + (is_scheduled ? App.Wireframe.Utils.imageUrl('object-schedule-active.png', 'schedule') : App.Wireframe.Utils.imageUrl('object-schedule-inactive.png', 'schedule')) + '" alt="' + App.lang('Reschedule') + '"/></a>').appendTo(wrapper);
    reschedule_button.flyoutForm({
      'width' : 'narrow',
      'title' : App.lang('Reschedule :type', {'type' : App.clean(object.type)}),
      'success_event' : object.event_names.updated,
      'success_message' : App.lang(':type successfully rescheduled', {'type' : App.clean(object.type)})
    });
  } // if
}; // Schedule property