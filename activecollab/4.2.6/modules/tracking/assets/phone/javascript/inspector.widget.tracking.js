/**
 * Tracking property handler
 */
App.Inspector.Widgets.Tracking = function (object, client_interface, default_billable_status) {
  var wrapper = $(this);
  
  var check_string = object.object_time + '.' + object.object_expenses + '.' + (object.estimate ? object.estimate.value : 0);
  
  if(wrapper.attr('check_string') == check_string) {
    return true;
  } // if

  wrapper.attr('check_string', check_string);  
  wrapper.empty();
  
  if(object.permissions.can_manage_tracking) {
    $('<a href="' + object.urls.tracking + '" title="' + App.lang('Time and Expenses') + '"><span class="tracking_label">' + object.object_time + 'h</span></a>').appendTo(wrapper);
  } else {
    $('<span class="tracking_label">' + object.object_time + 'h</span>').appendTo(wrapper);
  } // if
  
  if(object.object_expenses) {
    wrapper.find('span.tracking_label').append('/<span class="expense_label">' + App.lang(':expense spent', { 'expense' : object.object_expenses }) + '</span>');
  } // if
}; // Tracking property