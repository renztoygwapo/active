/**
 * Subscribe indicator handler
 */
App.Inspector.Indicators.Subscribe = function (object, inspector) {
  var wrapper = $(this);
  
  var check_string = 'is_subscribed_' + object.user_is_subscribed;
      
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  
  var toggler = $('<a href="' + (object.user_is_subscribed ? object.urls.unsubscribe : object.urls.subscribe) + '"><img src="' + (object.user_is_subscribed ? App.Wireframe.Utils.imageUrl('icons/16x16/object-subscription-active.png', 'subscriptions') : App.Wireframe.Utils.imageUrl('icons/16x16/object-subscription-inactive.png', 'subscriptions'))  + '" alt="" /></a>').appendTo(wrapper.empty());

  if (object.user_is_subscribed) {
    toggler.attr('title', App.lang('You are subscribed to receive email notifications regarding this :object_type. Click to unsubscribe!', { 'object_type' : App.lang(object.verbose_type_lowercase) }));
  } else {
    toggler.attr('title', App.lang('You are not subscribed to receive email notifications regarding this :object_type. Click to subscribe!', { 'object_type' : App.lang(object.verbose_type_lowercase) }));
  } // if

  toggler.asyncLink({
    'success_event' : object.event_names.updated,
    'success_message' : object.user_is_subscribed ? App.lang('You have successfully unsubscribed from this :object_type', {'object_type' : object.verbose_type}) : App.lang('You have successfully subscribed to this :object_type', {'object_type' : object.verbose_type})
  });
}; 