/**
 * Simple boolean property handler
 */
App.Inspector.Properties.SourceCommitCommitedBy = function (object, client_interface, field_name) {
  var wrapper = $(this);
  
  var commited_by = '';
  
  if (field_name.indexOf('.') == -1) {
    commited_by = object[$.trim(field_name)];
  } else {
    field_name = field_name.split('.');
    commited_by = object;
    $.each(field_name, function (index, field_name_step) {
      commited_by = commited_by[$.trim(field_name_step)];
    });
  } // if
    
  if (typeof(commited_by) == 'object' && commited_by.display_name) {
    var check_string = App.clean(commited_by['display_name']) + App.clean(commited_by['permalink']);
  } else {
    var check_string = App.clean(commited_by);
  } // if

  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if  
  wrapper.attr('check_string', check_string);
  
  if (typeof(commited_by) == 'object' && commited_by.display_name) {
    wrapper.empty().append('<a href="' + App.clean(commited_by['permalink']) + '">' + App.clean(commited_by['display_name']) + '</a>')
  } else {
    wrapper.empty().append(App.clean(commited_by));
  } // if
}; // SourceCommitCommitedBy property