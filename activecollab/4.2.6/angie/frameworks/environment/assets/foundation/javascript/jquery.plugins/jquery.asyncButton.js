/**
 * Button that submits data to the server
 */
jQuery.fn.asyncButton = function(settings) {
  var settings = jQuery.extend({
    'href' : null, 
    'async' : true, 
    'method' : 'post', 
    'confirmation'  : null, 
    'success' : null, 
    'error' : null, 
    'before_send' : null,
    'success_event' : null, 
    'error_event' : 'async_operation_error'
  }, settings);
  
  return this.each(function() {
    var button = $(this);
    
    button.click(function() {
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
      if(button[0].async_button_block_clicks) {
        return false;
      } else {
        button[0].async_button_block_clicks = true;
      } // if
      
      // Before send
      if(typeof(settings['before_send']) == 'function') {
        settings['before_send'].apply(this);
      } // if
      
      var old_label = button.text();
      button.text(App.lang('Working...'));
      
      var href = settings['href'] ? settings['href'] : button.attr('button_url');
      
      // Make AJAX request
      if(settings['async']) {
        $.ajax({
          'url' : href,
          'type' : settings['method'],
          'data' : { 'submitted' : 'submitted' },
          'defaultErrorHandler' : settings['error_event'],
          'success' : function(response) {
            button[0].async_button_block_clicks = false;
            button.text(old_label);
            
            if(typeof(settings['success']) == 'function') {
              settings['success'].apply(button[0], [response]);
            } // if
            
            if(settings['success_event']) {
              App.Wireframe.Events.trigger(settings['success_event'], [ response ]);
            } // if
          },
          'error' : function(response) {
            button[0].async_button_block_clicks = false;
            button.text(old_label);
            
            if(typeof(settings['error']) == 'function') {
              settings['error'].apply(button[0], [response]);
            } // if
          }
        });
        
      // Redirect
      } else {
        window.location = href;
      } // if
      
      return false;
    });
  });
};