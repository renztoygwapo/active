/**
 * Label title widget indicator handler
 */
App.Inspector.TitlebarWidgets.Label = function (object, client_interface) {
  var wrapper = $(this);
  
  if (object.label && object.label.id) {
    var check_string = object.label.id;
  } else {
    var check_string = 'no_label';
  }
   
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty();
  
  var label = $(App.Wireframe.Utils.renderLabel(object.label, (object.permissions.can_edit ? object.urls.update_label : null)));
  wrapper.append(label);
  
  if(object.permissions.can_edit && client_interface == 'default') {
    label.attr('title', App.lang('Change Label')).flyoutForm({
      'success_message' : App.lang('Label successfully changed'),
      'success_event' : object.event_names.updated,
      'width' : 'narrow'
    });
  } // if
}; 