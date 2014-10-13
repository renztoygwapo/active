/**
 * Project property handler
 */
App.Inspector.Properties.Project = function (object, client_interface) {
  var wrapper = $(this);
  
  if (object.project && object.project.id) {
    var check_string = object.project.id + App.clean(object.project.name);
  } else {
    var check_string = 'no_project';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper_row = wrapper.parents('div.property:first');
  wrapper.empty();
  
  if (object.project && object.project.id) {
    wrapper.append('<a href="' + object.project.permalink + '" class="quick_view_item quick_view_item_invert">' + App.clean(object.project.name) + '</a>');
  } else {
    wrapper.append('<span>' + App.lang('Project not set') + '</span>');
  } // if

  if (object.project && object.project.id) {
    wrapper_row.show();
  } else {
    wrapper_row.hide();
  } // if
}; // Project property