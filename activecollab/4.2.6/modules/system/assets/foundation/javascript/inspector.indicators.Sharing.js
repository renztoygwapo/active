
/**
 * sharing indicator handler
 */
App.Inspector.Indicators.Sharing = function (object, client_interface) {
  var wrapper = $(this);
  var wrapper_parent = $(this).parents('li:first');

  if (object.sharing) {
    var check_string = 'is_shared';
  } else {
    var check_string = 'is_not_shared';
  } // if
    
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty();
  wrapper_parent.hide();
  
  if (object.sharing && client_interface == 'default') {
    var toggler = $('<a href="' + object.urls.sharing_settings + '"><img src="' + (object.sharing ? App.Wireframe.Utils.imageUrl('icons/16x16/sharing-on.png', 'system') : App.Wireframe.Utils.imageUrl('icons/16x16/sharing-on.png', 'system'))  + '" alt="" /></a>').appendTo(wrapper);
    toggler.flyout({
      'title' : App.lang('Sharing'),
      'width' : 'narrow'
    });
    
    toggler.attr('title', App.lang('Item is shared. Click to see details of sharing'));
    
    wrapper_parent.show();
  } // if 
};