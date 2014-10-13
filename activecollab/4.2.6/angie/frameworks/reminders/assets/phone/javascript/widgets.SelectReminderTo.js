/**
 * Select reminder to widget behavior
 */
App.widgets.SelectReminderTo = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize widget
     * 
     * @param Integer wrapper_id
     */
    init : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      
      wrapper.trigger('create');
      
      wrapper.delegate('input[type="radio"]', 'change', function(event, ui) {
      	var selection = $(this);
      	
      	if(selection.is('#reminderSendOn_selected')) {
      		$('#reminderSelectedUserId-button').parent().parent().show();
      	} else {
      		$('#reminderSelectedUserId-button').parent().parent().hide();
      	} // if
			});
    }
    
  }
  
}();