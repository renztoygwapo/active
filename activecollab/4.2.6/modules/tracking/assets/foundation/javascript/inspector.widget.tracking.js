/**
 * Created on property handler
 */
App.Inspector.Widgets.Tracking = function (object, currency, client_interface, default_billable_status) {
  var wrapper = $(this);
  var inspector = wrapper.parents('.object_inspector:first');
  var scope = inspector.objectInspector('getEventScope');

  // if it's not already bounded
  if (!wrapper.data('bounded')) {
    wrapper.data('bounded', true);
    App.Wireframe.Events.bind('time_record_created.' + scope + ' time_record_updated.' + scope + ' time_record_deleted.' + scope + ' expense_created.' + scope + ' expense_updated.' + scope + ' expense_deleted.' + scope + '', function (event, tracking_object) {
      var parent = tracking_object.parent;
      if (parent && (parent['id'] == object['id'] && parent['class'] == object['class']) && parent.event_names && parent.event_names.updated) {
        inspector.objectInspector('refresh');
      } // if
    });
  } // if
  
  var check_string = object.object_time + '.' + object.object_expenses + '.' + (object.estimate ? object.estimate.value : 0);
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if

  wrapper.attr('check_string', check_string);  
  wrapper.empty();  
    
  if (object.permissions.can_manage_tracking) {
    var stopwatch = $('<a class="tracking_widget_stopwatch widget_trigger" href="' + object.urls.tracking + '" title="' + App.lang('Time and Expenses') + '"></a>').appendTo(wrapper);
  } else {
    var stopwatch = $('<span class="tracking_widget_stopwatch widget_trigger"></span>').appendTo(wrapper);
  } // if

  var image_url;
  var tracking_label;

  if (!object.estimate || !object.estimate.value) {
    image_url = App.Wireframe.Utils.imageUrl('tracking-widget/stopwatch-blank.png', 'tracking');
    tracking_label = App.lang(':hoursh', {
      'hours' : object.object_time
    });
  } else if (object.estimate && object.estimate.value && (object.estimate.value < object.object_time)) {
    image_url = App.Wireframe.Utils.imageUrl('tracking-widget/stopwatch-underestimated.png', 'tracking');
    tracking_label = App.lang(':hoursh of :estimatedh', {
      'hours' : object.object_time,
      'estimated' : object.estimate.value
    });
  } else {
    var rounding_factor = 5;
    percentage = (object.object_time / object.estimate.value) * 100;
    var rounded = Math.ceil(percentage / rounding_factor) * rounding_factor;
    image_url = App.Wireframe.Utils.imageUrl('tracking-widget/stopwatch-' + rounded + '.png', 'tracking');
    tracking_label = App.lang(':hoursh of :estimatedh', {
      'hours' : object.object_time,
      'estimated' : object.estimate.value
    });
  } // if

  stopwatch.append('<img src="' + image_url + '" />');
  stopwatch.append('<span class="tracking_label">' + tracking_label + '</span>');

  if (object.object_expenses) {
    if (currency['code'] == 'USD') {
      var expense_label = App.lang(':currency:expense spent', { 'expense' : object.object_expenses, 'currency' : '$'});
    } else {
      var expense_label = App.lang(':expense :currency spent', { 'expense' : object.object_expenses, 'currency' : currency['code'] });
    } // if

    stopwatch.append('<span class="expense_label">' + expense_label + '</span>').addClass('with_expenses');
  } // if

  if (object.permissions.can_manage_tracking && client_interface == 'default') {
    stopwatch.objectTimeExpensesFlyout(null);
  } // if
}; // Tracking widget