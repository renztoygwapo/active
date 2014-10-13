/**
 * Delegate async checkbox change
 *
 * Example:
 *
 * wrapper.on('change', 'tr.task td.name input[type=checkbox]', function(event) {
 *   return App.Delegates.asyncCheckboxChange.apply(this, [event, {
 *     'confirmation' : App.lang('Are you sure?')
 *   }]);
 * });
 *
 * @param event
 * @param s
 * @return {Boolean}
 */
App.Delegates.asyncCheckboxChange = function(event, s) {
  var checkbox = $(this);

  var settings = jQuery.extend({
    'method' : 'post',
    'confirmation'  : null,
    'success' : null,
    'error' : null,
    'indicator_url' : App.Wireframe.Utils.indicatorUrl(),
    'before_send' : null,
    'success_event' : null,
    'success_message' : null,
    'error_event' : 'async_operation_error'
  }, s);

  console.log(settings);

  var new_state = checkbox[0].checked ? 1 : 0;
  var old_state = checkbox[0].checked ? 0 : 1;

  // Block additional clicks
  if(checkbox[0].async_checkbox_block_clicks) {
    return false;
  } else {
    checkbox[0].async_checkbox_block_clicks = true;
  } // if

  var img = $('<img />').attr('src', settings['indicator_url']);
  checkbox.hide().after(img);

  /**
   * Unblock checkbox (hide indicator and show checkbox again)
   *
   * If revert is true, old_state will be preserved and function will return
   * FALSE (useful for quick execution abortion)
   */
  var unblock = function(revert) {
    img.remove();

    checkbox[0].async_checkbox_block_clicks = false;
    if(revert) {
      checkbox[0].checked = old_state;
    } // if

    checkbox.show();

    if(revert) {
      return false;
    } // if
  }; // unblock

  // Before send
  if(typeof(settings['before_send']) == 'function') {
    var before_send = settings['before_send'].apply(this);

    if(before_send === false) {
      unblock(true);
      return false;
    } // if
  } // if

  if(settings['confirmation']) {
    if(typeof(settings['confirmation']) == 'function') {
      var confirmation_message = settings['confirmation'].apply(this);
    } else {
      var confirmation_message = settings['confirmation'];
    } // if

    if(typeof(confirmation_message) == 'object' && confirmation_message instanceof Array) {
      if(!confirm(confirmation_message[old_state])) {
        return unblock(true);
      } // if
    } else {
      if(!confirm(confirmation_message)) {
        return unblock(true);
      } // if
    } // if
  } // if

  $.ajax({
    'url' : new_state ? checkbox.attr('on_url') : checkbox.attr('off_url'),
    'type' : settings['method'],
    'data' : settings['method'].toLowerCase() == 'post' ? { 'submitted' : 'submitted' } : null,
    'defaultErrorHandler' : settings['error_event'],
    'success' : function(response) {
      unblock();

      if(typeof(settings['success']) == 'function') {
        settings['success'].apply(checkbox[0], [response]);
      } // if

      if(settings['success_message']) {
        if(typeof(settings['success_message']) == 'object' && settings['success_message'] instanceof Array) {
          App.Wireframe.Flash.success(settings['success_message'][new_state]);
        } else {
          App.Wireframe.Flash.success(settings['success_message']);
        } // if
      } // if


      if(settings['success_event']) {
        App.Wireframe.Events.trigger(settings['success_event'], [ response ]);
      } // if
    },
    'error' : function(response) {
      unblock(true);

      if(typeof(settings['error']) == 'function') {
        settings['error'].apply(checkbox[0], [response]);
      } // if
    }
  });
};

/**
 * Checkbox that changes on and off state via async call
 */
jQuery.fn.asyncCheckbox = function(s) {
  return this.each(function() {
    $(this).change(function(event) {
      var response = App.Delegates.asyncCheckboxChange.apply(this, [event, s]);

      if(response === false) {
        return false;
      } // if
    });
  });
};