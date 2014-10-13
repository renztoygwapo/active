/**
 * Object attachments widgets behavior
 */
jQuery.fn.objectAttachments = function(s) {
  var settings = jQuery.extend({
    'attachments' : null 
  }, s);

  
  return this.each(function() {
    var wrapper = $(this).addClass('attachments');
    
    /**
     * Render the attachments
     * 
     * @param Array attachments
     */
    var render_attachments = function (attachments) {
      wrapper.find('ul.attachments_table').remove();
      var list = $('<ul class="attachments_table"></ul>').appendTo(wrapper);
      
      if (attachments && attachments.length) {
        $.each(attachments, function (index, attachment) {
          var row = $('<li class="attachment"></li>').attr('id', 'attachment_' + attachment['id']).appendTo(list);
          
          var attachment_row = $('<a href="' + App.clean(attachment['urls']['view']) + '" target="_blank"><img src="' + attachment['preview']['icons']['large'] + '"> <span class="filename">' + App.clean(App.Wireframe.Utils.shortFilename(attachment['name'])) + '</span></a>').appendTo(row);
          if (attachment['urls'] && attachment['urls']['preview']) {
            attachment_row.addClass('quick_view_item').attr('quick_view_url', attachment['urls']['preview']);
          } // if
          
          if(typeof(attachment['options']) == 'object' && attachment['options']) {
            $('<span class="options"></span>').appendTo(row);
          } // if
        });
      } // if
      
      if(list.find('li.attachment').length < 1) {
        list.hide();
      } else {
        list.show();
      } // if
    }; // render_attachments
    
    if(typeof(settings['attachments']) == 'object' && settings['attachments']) {
      render_attachments(settings['attachments']);
    } // if
    
    // listen to parent updates
    if (settings.object.listen) {
      App.Wireframe.Events.bind(settings.object.listen + '.single', function (event, updated_object) {
        if (updated_object['id'] == settings.object['id'] && updated_object['class'] == settings.object['class']) {
          render_attachments(updated_object.attachments);
        } // if
      });
    } // if
  });
  
};