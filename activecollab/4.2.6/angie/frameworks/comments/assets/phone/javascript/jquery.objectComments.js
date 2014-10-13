/**
 * Object comments widgets behavior
 */
jQuery.fn.objectComments = function(s) {
  var settings = jQuery.extend({ 
    'comments' : null
  }, s);
  
  return this.each(function() {
    var wrapper = $(this).append('<h2>' + App.lang('Comments') + '</h2>');
    var list = $('<ul data-role="listview" data-inset="true"></ul>').appendTo(wrapper).listview();
    
    /**
     * Add single comment to the list
     * 
     * @param Object comment
     * @param boolean bulk
     */
    var add = function(comment, bulk) {
      var row = $('<li></li>').appendTo(list);
      
      $('<p class="author">' + App.lang('By <a href=":user_link">:user_name</a> on :when', {
        'user_link' : comment['created_by']['urls']['view'],  
        'user_name' : comment['created_by']['short_display_name'], 
        'when' : comment['created_on']['formatted']
      }) + '</p>').appendTo(row);
      
      row.append('<div class="content">' + comment['body'] + '</div>');
      
      if(typeof(comment['attachments_count']) == 'number') {
        if(comment['attachments_count'] == 1) {
          $('<a href="#" data-role="button" data-inline="true">' + App.lang('One Attachment') + '</a>').appendTo(row).button();
        } else if(comment['attachments_count'] > 1) {
          $('<a href="' + comment['attachments_url'] + '" data-role="button" data-inline="true">' + App.lang(':num Attachments', { 'num' : comment['attachments_count'] }) + '</a>').appendTo(row).button();
        } // if
      } // if
      
      if(!bulk) {
        refresh();
      } // if
    }; // add
    
    /**
     * Refresh listview
     */
    var refresh = function() {
      list.listview('refresh');
    }; // refresh
    
    if(typeof(settings['comments']) == 'object' && settings['comments']) {
      for(var i in settings['comments']) {
        add(settings['comments'][i], true);
      } // if
      
      refresh();
    } // if
  });
  
};