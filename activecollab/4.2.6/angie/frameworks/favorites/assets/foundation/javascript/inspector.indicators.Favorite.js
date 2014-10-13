/**
 * Favorite indicator handler
 */
App.Inspector.Indicators.Favorite = function (object, client_interface) {
  var wrapper = $(this);
  
  var check_string = 'is_favorite_' + object.is_favorite;
    
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  
  var toggler = $('<a href="' + (object.is_favorite ? object.urls.remove_from_favorites : object.urls.add_to_favorites) + '"><img src="' + (object.is_favorite ? App.Wireframe.Utils.imageUrl('icons/12x12/favorite-on.png', 'favorites') : App.Wireframe.Utils.imageUrl('icons/12x12/favorite-off.png', 'favorites'))  + '" alt="" /></a>').appendTo(wrapper.empty());
  if (object.is_favorite) {
    toggler.attr('title', App.lang('Object is on your list of favorite objects. Click to remove it.'));
  } else {
    toggler.attr('title', App.lang('Object is not on your list of favorite objects. Click to add it.'));    
  } // if

  toggler.asyncLink({
    'success_event' : object.event_names.updated,
    'success_message' : object.is_favorite ? App.lang(':object_type has been removed from favorites', {'object_type' : object.verbose_type}) : App.lang(':object_type has been added to favorites', {'object_type' : object.verbose_type})
  });
}; 