/**
 * Delegate async link click
 *
 * Example:
 *
 * tasks_table.delegate('a.remove_related_task', 'click', function(event) {
 *   App.Delegates.asyncLinkClick.apply(this, [event, {
 *    'confirmation' : App.lang('Are you sure that you want to remove selected task from the list of related tasks?'),
 *    'success' : function() {
 *      // Do something
 *    }
 *   }]);
 *
 *   return false;
 * });
 *
 * @param event
 * @param s
 * @return {Boolean}
 */
App.Delegates.asyncLinkClick = function(event, s) {
  var link = $(this);

  var settings = jQuery.extend({
    'method' : 'post',
    'confirmation'  : null,
    'success' : null,
    'error' : null,
    'indicator_url' : null,
    'before' : null,
    'before_send' : null,
    'success_event' : null,
    'success_message' : null,
    'error_event' : 'async_operation_error',
    'complete' : null
  }, s);

  // Before everything
  if(typeof(settings['before']) == 'function') {
    var before = settings['before'].apply(this);

    if(before === false) {
      return false;
    } // if
  } // if

  if(settings['confirmation']) {
    if(typeof(settings['confirmation']) == 'function') {
      var confirmation_message = settings['confirmation'].apply(this);
    } else {
      var confirmation_message = settings['confirmation'];
    } // if

    if(!confirm(confirmation_message)) {
      return false;
    } // if
  } // if

  // Block additional clicks
  if(link[0].async_link_block_clicks) {
    return false;
  } else {
    link[0].async_link_block_clicks = true;
  } // if

  // Before send
  if(typeof(settings['before_send']) == 'function') {
    settings['before_send'].apply(this);
  } // if

  var img = link.find('img');
  if(img.length) {
    if(!settings['indicator_url']) {
      settings['indicator_url'] = App.Wireframe.Utils.indicatorUrl();
    } // if

    var old_src = img.attr('src');

    img.attr('src', settings['indicator_url']);
  } // if

  // If we need to send POST request, add submitted parameter
  var data = settings['method'].toLowerCase() == 'post' ? { 'submitted' : 'submitted' } : null;

  $.ajax({
    'url' : link.attr('href'),
    'type' : settings['method'],
    'data' : data,
    'defaultErrorHandler' : settings['error_event'],
    'success' : function(response) {
      link[0].async_link_block_clicks = false;

      if(img.length) {
        img.attr('src', old_src);
      } // if

      if(typeof(settings['success']) == 'function') {
        settings['success'].apply(link[0], [response]);
      } // if

      if(settings['success_message']) {
        App.Wireframe.Flash.success(settings['success_message']);
      } // if

      if(settings['success_event']) {
        App.Wireframe.Events.trigger(settings['success_event'], [ response ]);
      } // if
    },
    'error' : function(response) {
      link[0].async_link_block_clicks = false;

      if(img.length) {
        img.attr('src', old_src);
      } // if

      if(typeof(settings['error']) == 'function') {
        settings['error'].apply(link[0], [ response ]);
      } // if
    },
    'complete' : function (response) {
      if(typeof(settings['complete']) == 'function') {
        settings['complete'].apply(link[0], [response]);
      } // if
    }
  });

  event.preventDefault();
};

/**
 * Link that submits data to the server based on its URL
 */
jQuery.fn.asyncLink = function(s) {
  return this.each(function() {
    $(this).click(function(event) {
      var response = App.Delegates.asyncLinkClick.apply(this, [event, s]);

      if(response === false) {
        return false;
      } // if
    });
  });
};