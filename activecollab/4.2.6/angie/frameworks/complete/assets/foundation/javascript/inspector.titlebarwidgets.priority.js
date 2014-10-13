/**
 * Priority title widget indicator handler
 */
App.Inspector.TitlebarWidgets.Priority = function (object, client_interface) {
  var wrapper = $(this);

  var check_string = 'priority_' + object.priority;   
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty();
    
  var priority_label = '';
  var priority_filename_sufix = '';
  switch (object.priority) {
    case 2:
      var priority_label = App.lang('Highest');
      var priority_filename_sufix = 'highest';
      break;
      
    case 1:
      var priority_label = App.lang('High');
      var priority_filename_sufix = 'high';
      break;
      
    case 0:
      var priority_label = App.lang('Normal');
      var priority_filename_sufix = 'normal';
      break;
      
    case -1:
      var priority_label = App.lang('Low');
      var priority_filename_sufix = 'low';
      break;
      
    case -2:
      var priority_label = App.lang('Lowest');
      var priority_filename_sufix = 'lowest';
      break;
  }; // switch
  
  if(!object.priority && client_interface == 'default') {
    return false;
  } // if
  
  var image_url = App.Wireframe.Utils.imageUrl('priority-widget/priority-' + priority_filename_sufix + '.png', 'complete');
    
  if(object.permissions.can_edit && client_interface == 'default') {
    var widget = $('<a href="' + object.urls.update_priority + '" title="' + App.lang('Change Priority') + '"><img src="' + image_url + '" alt="' + priority_label + '" /></a>').appendTo(wrapper);
    widget.flyoutForm({
      'success_message' : App.lang('Priority successfully changed'),
      'success_event' : object.event_names.updated,
      'width' : 'narrow'
    });
  } else {
  	if(client_interface == 'default') {
  		wrapper.append('<span class="title_priority"><img src="' + image_url + '" alt="' + priority_label + '" /></span>');
  	} else if(client_interface == 'phone') {
  		wrapper.append('<span class="title_priority">' + priority_label + '</span>');
  	} // if
  } // if
}; 