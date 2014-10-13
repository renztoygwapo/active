/**
 * Invited on property handler
 */
App.Inspector.Properties.InvitedOn = function (object, client_interface) {
  var wrapper = $(this);
  var invited_on = object['invited_on'];

  if(invited_on) {
    var check_string = 'invited_on_' + invited_on.formatted_date;
  } else {
    var check_string = 'not_yet_invited';
  } // if

  if(wrapper.attr('check_string') == check_string) {
    return true;
  } // if

  wrapper.attr('check_string', check_string).empty();

  if(invited_on) {
    var property = invited_on.formatted_date;
  } else {
    var property = '<span>' + App.lang('Not Yet Invited') + '</span>';
  } // if

  var trigger_wrapper = $('<span class="inspector_edit_wrapper"></span>').append(property).appendTo(wrapper);

  if(!invited_on && client_interface != 'phone' && client_interface != 'printer') {
    if(object['options']['send_welcome_message']) {
      $('<a href="' + object['options']['send_welcome_message']['url'] + '" class="editor_trigger" title="' + App.lang('Invite Now') + '"><img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/invite.png', 'environment') + '"></a>').flyoutForm({
        'title' : App.lang('Send Welcome Message'),
        'success_message' : App.lang('Welcome message has been sent'),
        'success_event' : object.event_names.updated,
        'width' : 600
      }).appendTo(trigger_wrapper);
    } // if

    if(object['permissions']['can_set_as_invited']) {
      $('<a href="' + object['urls']['set_as_invited'] + '" class="editor_trigger" title="' + App.lang('Set as Invited') + '"><img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/set-as-invited.png', 'environment') + '"></a>').asyncLink({
        'indicator_url' : App.Wireframe.Utils.imageUrl('layout/bits/indicator-loading-small.gif', 'environment'),
        'success_message' : App.lang('User has been set as invited'),
        'success_event' : object.event_names.updated
      }).appendTo(trigger_wrapper);
    } // if
  } // if
}; // InvitedOn