/**
 * Trashed bar
 */
App.Inspector.Bars.State = function (object, client_interface) {
  var wrapper = $(this);
  var inspector = wrapper.parents('.object_inspector:first');
    
  var check_string = 'state_' + object.state;
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty().hide();

  var message = '';
  
  if (object.state == 1) {
    if (object['permissions']['can_untrash'] && object['urls']['untrash']) {
      message = App.lang('This item is in the trash. <a href=":untrash_url">Click here</a> if you wish to restore it.', {'untrash_url' : object['urls']['untrash']})
    } else {
      message = App.lang('This item is in the trash.');
    } // if

    wrapper.append('<img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/trashed.png', 'environment') + '" alt="" /> ' + message).show();
    wrapper.find('a:first').asyncLink({
      'success_event' : object.event_names.updated,
      'confirmation' : App.lang('Are you sure that you want to restore this item from the trash?')
    });
    
    inspector.removeClass('archived').addClass('trashed');
  } else if (object.state == 2) {
    if (object['permissions']['can_unarchive'] && object['urls']['unarchive']) {
      message = App.lang('This item is archived. <a href=":unarchive_url">Click here</a> to restore it from archive.', {'unarchive_url' : object['urls']['unarchive'] });
    } else {
      message = App.lang('This item is archived.');
    } // if

    wrapper.append('<img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/archive.png', 'environment') + '" alt="" /> ' + message).show();
    wrapper.find('a:first').asyncLink({
      'success_event' : object.event_names.updated,
      'confirmation' : App.lang('Are you sure that you want to restore this item from the archive?')
    });
    inspector.addClass('archived').removeClass('trashed');
  } else {
    inspector.removeClass('archived trashed');
  } // if
};