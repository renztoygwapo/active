/**
 * Avatar Widget
 */
App.Inspector.Widgets.Avatar = function (object, client_interface, size) {
  var wrapper = $(this);
  
  var check_string = object.avatar[size];
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if

  wrapper.attr('check_string', check_string);  
  wrapper.empty();
  
  if (object.permissions.can_edit && client_interface == 'default') {
    var avatar = $('<a href="' + object.urls.update_avatar + '" title="' + App.lang('Update Avatar') + '"><img class="properties_icon" alt="" src="' + object.avatar[size] + '"></a>');
    avatar.flyout();
  } else {
    var avatar = $('<span title="' + App.lang('Avatar') + '"><img class="properties_icon" alt="" src="' + object.avatar[size] + '"></span>');
  } // if
  
  avatar.appendTo(wrapper);
}; // Avatar widget