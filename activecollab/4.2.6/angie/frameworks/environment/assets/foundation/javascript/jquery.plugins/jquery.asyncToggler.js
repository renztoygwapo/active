/**
 * Async toggler is a link that switches between on and off state
 * 
 * When state is changed, toggler switches href as well as body and fires 
 * appropriate events
 */
jQuery.fn.asyncToggler = function(s) {
  var settings = jQuery.extend({
    'method' : 'post', 
    'is_on' : null, 
    'content_when_on' : App.lang('On'), 
    'content_when_off' : App.lang('Off'), 
    'title_when_on' : '', 
    'title_when_off' : '', 
    'url_when_on' : '#', 
    'url_when_off' : '#', 
    'confirmation_when_on' : null, 
    'confirmation_when_off' : null, 
    'success' : null, 
    'error' : null, 
    'indicator_url' : App.Wireframe.Utils.indicatorUrl('small'),
    'before_send' : null,
    'success_event' : null,
    'success_message' : null, 
    'error_event' : 'async_operation_error'
  }, s);
  
  return this.each(function() {
    var link = $(this);
    
    // We can set is_on via parameter and via attribute
    if(settings['is_on'] !== null && typeof(link.attr('is_on')) == 'undefined') {
      link.attr('is_on', (settings['is_on'] ? 1 : 0));
    } // if
    
    /**
     * Set up link for given state
     * 
     * @param Integer state
     */
    var set_for_state = function(state) {
      if(state == 1) {
        link.attr({
          'title' : settings['title_when_on'], 
          'href' : settings['url_when_on']
        });
        
        var content = settings['content_when_on'];
      } else {
        link.attr({
          'title' : settings['title_when_off'], 
          'href' : settings['url_when_off']
        });
        
        var content = settings['content_when_off'];
      } // if
      
      if(typeof(content) == 'object' && content instanceof jQuery) {
        link.append(content);
      } else if(typeof(content) == 'string') {
        if(content.substr(0, 1) == '<' && content.substr(content.length - 1, 1) == '>') {
          link.html(content);          
        } else {
          link.text(App.clean(content));
        } // if
      } // if
    }; // set_for_state
    
    set_for_state(link.attr('is_on') == '1' ? 1 : 0);
    
    // Handle toggler click
    link.click(function(event) {
      var old_state = link.attr('is_on') == '1' ? 1 : 0;
      var new_state = old_state == 1 ? 0 : 1;
      
      // Block additional clicks
      if(link[0].async_link_block_clicks) {
        return false;
      } else {
        link[0].async_link_block_clicks = true;
      } // if
      
      link.empty();
      var img = $('<img />').attr('src', settings['indicator_url']).appendTo(link);
      
      /**
       * Unblock link (remove indicator and show content)
       * 
       * If revert is true, old_state will be preserved and function will return 
       * FALSE (useful for quick execution abortion)
       */
      var unblock = function(revert) {
        img.remove();
        link[0].async_link_block_clicks = false;
        
        if(revert) {
          link.attr('is_on', old_state);
          set_for_state(old_state);
        } else {
          link.attr('is_on', new_state);
          set_for_state(new_state);
        } // if
        
        if(revert) {
          return false;
        } // if
      }; // unblock
      
      if(old_state == 1) {
        if(settings['confirmation_when_on']) {
          if(typeof(settings['confirmation_when_on']) == 'function') {
            var confirmation_message = settings['confirmation_when_on'].apply(link[0]);
          } else {
            var confirmation_message = settings['confirmation_when_on'];
          } // if
          
          if(!confirm(confirmation_message)) {
            return unblock(true);
          } // if
        } // if
      } else {
        if(settings['confirmation_when_off']) {
          if(typeof(settings['confirmation_when_off']) == 'function') {
            var confirmation_message = settings['confirmation_when_off'].apply(link[0]);
          } else {
            var confirmation_message = settings['confirmation_when_off'];
          } // if
          
          if(!confirm(confirmation_message)) {
            return unblock(true);
          } // if
        } // if
      } // if
      
      // Before send
      if(typeof(settings['before_send']) == 'function') {
        settings['before_send'].apply(this);
      } // if
      
      $.ajax({
        'url' : link.attr('href'),
        'type' : settings['method'],
        'data' : settings['method'].toLowerCase() == 'post' ? { 'submitted' : 'submitted' } : null,
        'success' : function(response) {
          unblock();
          
          if(typeof(settings['success']) == 'function') {
            settings['success'].apply(link[0], [response]);
          } // if
          
          if(settings['success_message']) {
            if(typeof(settings['success_message']) == 'object') {
              App.Wireframe.Flash.success(settings['success_message'][new_state]);
            } else {
              App.Wireframe.Flash.success(settings['success_message']);
            } // if
          } // if
          
          if(settings['success_event']) {
            if(jQuery.isArray(settings['success_event'])) {
              App.Wireframe.Events.trigger(settings['success_event'][new_state], [ response ]);
            } else {
              App.Wireframe.Events.trigger(settings['success_event'], [ response ]);
            } // if
          } // if
        },
        'error' : function(response) {
          unblock(true);
          
          if(typeof(settings['error']) == 'function') {
            settings['error'].apply(link[0], [response]);
          } // if
          
          // Throw error event
          if(settings['error_event']) {
            App.Wireframe.Events.trigger(settings['error_event'], [ response ]);
          } // if
        }
      });
      
      event.preventDefault();
    });
  });
  
};