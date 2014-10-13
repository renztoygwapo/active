/**
 * Object subtasks implementation
 */
jQuery.fn.objectSubtasks = function(s) {
  var settings = jQuery.extend({ 
    'subtasks' : null
  }, s);
  
  // Walk through elements and initialize them
  return this.each(function() {
    var wrapper = $(this).append('<h2>' + App.lang('Subtasks') + '</h2>');
    var list = $('<ul data-role="listview" data-inset="true"></ul>').appendTo(wrapper).listview();
    
    /**
     * Add subtask to the list
     * 
     * @param Object subtask
     * @param Boolean bulk
     */
    var add = function(subtask, bulk) {
      var row = $('<li><a href="' + subtask['urls']['view'] + '"></a></li>').appendTo(list);
      var link = row.find('a');
      
      if(typeof(subtask['assignee']) == 'object' && subtask['assignee']) {
        $('<span class="assignee">' + App.clean(subtask['assignee']['short_display_name']) + ': </span>').appendTo(link);
      } // if
      
      // can_change_complete_status
      
      $('<span class="content">' + subtask['name'] + '</span>').appendTo(link);
      
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
    
    if(typeof(settings['subtasks']) == 'object' && settings['subtasks']) {
      for(var i in settings['subtasks']) {
        add(settings['subtasks'][i], true);
      } // if
      
      refresh();
    } // if
  });
  
};