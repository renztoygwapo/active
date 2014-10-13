/**
 * Object attachments
 */
App.widgets.ObjectAttachments = function() {
  /**
   * Init all attachments in current object
   *
   * @var null
   * @return void
   */
  var init_attachments = function (attachments_table) {
    var attachments = attachments_table.find('li.attachment');
    attachments.each(function () {
      init_attachment($(this), attachments, attachments_table);
    });
    
    align_attachments(attachments_table);
  } // init_attachments
  
  /**
   * Align attachments
   *
   * @var null
   * @return null
   */
  var align_attachments = function (attachments_table) {
    return false;
    var previous_attachment_top = 0;
    var counter = -1;
    
    var attachment_rows = new Array();
    var attachments = attachments_table.find('li.attachment');
    attachments.each(function () {
      var attachment = $(this);
      
      if (attachment.offset().top != previous_attachment_top) {
        counter++;
        
        attachment_rows[counter] = {};
        attachment_rows[counter].max_height = 0;
        attachment_rows[counter].attachments = new Array();
      } // if
      
      previous_attachment_top = attachment.offset().top;
      
      attachment_rows[counter].max_height = Math.max(attachment_rows[counter].max_height, attachment.find('img.image:first').outerHeight());
      attachment_rows[counter].attachments.push(attachment);
    });
    
    if (attachment_rows.length) {
      for (var x=0; x < attachment_rows.length; x++) {
        if (attachment_rows[x].attachments.length) {
          for (var y=0; y < attachment_rows[x].attachments.length; y++) {
            var image = attachment_rows[x].attachments[y].find('img.image:first');
            image.css('marginTop', attachment_rows[x].max_height - image.outerHeight());
          } // for
        } // if
      } // for
    } // if
  } // align_attachmnets
  
  /**
   * Init provided attachment in current object
   *
   * @var null
   * @return void
   */
  var init_attachment = function (attachment, attachments, attachments_table) {
    var attachment_options = attachment.find('.attachment_options:first');
    
    // hover method
    attachment.hover(function () {
      attachment.addClass('hover');
    }, function () {
      attachment.removeClass('hover');
    });
    
    // attachment download
    attachment.find('img.image, a.shortened_filename').click(function (event) {
      var url = $(this).parents('li.attachment').find('a.download').attr('href');
      window.location = url;
      return false;
    });
    
    // attachment delete
    attachment.find('a.delete').click(function (event) {
      event.stopPropagation();
      
      if (!confirm(App.lang('Are you sure that you want to permanently remove this attachment? There is no Undo!'))) {
        return false;
      } // if
      
      link = $(this);
      attachment.addClass('deleting');
      
      $.ajax({
        url     : link.attr('href'),
        type    : 'POST',
        data    : {'submitted' : 'submitted'},
        success : function() {
          attachment.remove();
          attachment.removeClass('deleting');
        },
        error   : function() {
          attachment.removeClass('deleting');
        }
      });
     return false;
    });
  } // init_attachment
  
  // Public interface
  return {
    
    /**
     * Initialize behavior
     *
     * @param string wrapper_id
     */
    init : function(wrapper_id) {
      var attachments_table = $('#' + wrapper_id).find('ul.attachments_table');
      init_attachments(attachments_table);
    },
    
    refresh : function (wrapper_id) {
      var wrapper = $('#'+wrapper_id);
      var reference = this;
      wrapper.block();
      
      var refresh_url = App.extendUrl(wrapper.attr('manage_url'), {
        'list_only' : 1
      });
      $.ajax({
        'url' : refresh_url,
        'success' : function (response) {
          wrapper.empty();
          wrapper.html(response);
          reference.init(wrapper_id);
        },
        'error' : function (response, response_text) {
          
        }
      })
    }
    
  };
}();