/**
 * Sort table rows by selector
 */
jQuery.fn.SortBySelector = function(s) {
  var settings = jQuery.extend({
    'row_selector' : 'tr', 
    'value_selector' : 'td.sort_by', 
    'desc' : false
  }, s);
  
  return this.each(function() {
    var table = $(this);
    
    var to_sort = [];
    var counter = 1;
    
    var rows = table.find('>tbody>' + settings['row_selector']).each(function() {
      var row = $(this);
      
      row.attr('sort_by_selector_id', counter++);
      
      if(typeof(settings['value_selector']) == 'function') {
        var value = settings['value_selector'].apply(row[0]);
      } else {
        var value_node = row.find(settings['value_selector']);
        if(value_node.length > 0) {
          var value = value_node[0].nodeName == 'INPUT' || value_node[0].nodeName == 'SELECT' || value_node[0].nodeName == 'TEXTAREA' ? value_node.val() : value_node.text();
        } else {
          var value = '';
        } // if
      } // if
      
      to_sort.push({
        'id' : row.attr('sort_by_selector_id'), 
        'value' : value
      });
    });
    
    if(to_sort.length) {
      to_sort.sort(function(a, b) {
        var x = a['value'].toLowerCase();
        var y = b['value'].toLowerCase();
        
        if(settings['desc']) {
          return ((x < y) ? 1 : ((x > y) ? -1 : 0));
        } else {
          return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        } // if
      });
      
      for(var i in to_sort) {
        table.find('>tbody').append(table.find('>tbody>tr[sort_by_selector_id=' + to_sort[i]['id'] + ']'));
      } // for
    } // if
  });
  
};