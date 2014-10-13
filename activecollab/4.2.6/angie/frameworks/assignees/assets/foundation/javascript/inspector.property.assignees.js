/**
 * Assignees property
 */
App.Inspector.Properties.Assignees = function (object, client_interface) {
  var wrapper = $(this);
    
  if (object.assignee) {
    var check_string = object.assignee.id + '.' + (object.other_assignees ? object.other_assignees.length : '');
  } else if (!object.assignee && (object.other_assignees && object.other_assignees.length)) {
    var check_string = 0 + '.' + object.other_assignees.length;
  } else {
    var check_string = 'no_assignees';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string).empty();
  var wrapper_row = wrapper.parents('div.property:first');
  
  if(object.assignee) {
	  wrapper.append(App.lang('<a href=":url">:name</a> is responsible.', { 'url' : object.assignee.permalink, 'name' : object.assignee.display_name }));
  } else {
    wrapper.append(App.lang('There is no responsible person'));
  } // if

  if (object.other_assignees && object.other_assignees.length) {
    var other_assignees = [];
    $.each(object.other_assignees, function (index, assignee) {
      other_assignees.push('<a href="' + assignee.permalink + '">' + assignee.display_name + '</a>');
    });
    wrapper.append(' ' + App.lang('Other assignees are:') + other_assignees.join(', '));
  } // if
  
  if (object.assignee != null || (object.other_assignees && object.other_assignees.length)) {
    wrapper_row.show();
  } else {
    wrapper_row.hide();
  } // if
  
  return false;
}; // CreatedOn property