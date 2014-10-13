/**
 * Visibility indicator handler
 */
App.Inspector.Indicators.Visibility = function (object, client_interface) {
  var wrapper = $(this);
  var wrapper_parent = $(this).parents('li:first');

  if (object.visibility) {
    var check_string = 'public';
  } else {
    var check_string = 'hidden';
  } // if
    
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty();
  wrapper_parent.hide();
  
  if(client_interface == 'default') {
    if(!object.visibility) {
      var toggler = $('<img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/private-object.png', 'environment') + '" alt="" />').appendTo(wrapper).attr('title', App.lang('This object is private. Only users with permission to see private objects will be able to see this object'));
      wrapper_parent.show();
    } // if
  } else if(client_interface == 'phone') {
    if(!object.visibility) {
      return $('<span>Private</span>').appendTo(wrapper);
    } else {
      return $('<span>Normal</span>').appendTo(wrapper);
    } // if
  } // if
};