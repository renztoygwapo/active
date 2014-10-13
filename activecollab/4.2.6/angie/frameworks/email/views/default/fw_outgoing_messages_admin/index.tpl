{title}Outgoing Queue{/title}
{add_bread_crumb}Queue{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="outgoing_mail_queue"></div>

<script type="text/javascript">
	$('#outgoing_mail_queue').pagedObjectsList({
    'load_more_url' : '{assemble route=outgoing_messages_admin}', 
    'items' : {$messages|json nofilter},
    'items_per_load' : {$messages_per_page}, 
    'total_items' : {$total_messages}, 
    'empty_message' : App.lang('There are no email messages in the queue'),   
    'on_add_item' : function(item) {
      var message = $(this);
      
      message.append('<div class="info">' + 
        '<div class="subject"></div>' + 
        '<div class="recipient"></div>' + 
        '<div class="created_on"></div>' +  
      '</div>' + 
      '<div class="retries"></div>' + 
      '<div class="options"></div>');

      message.find('div.subject').append(App.lang('Subject: <a href=":url">:subject</a>', {
        'url' : item['urls']['view'], 
        'subject' : item['subject']
      }));

      message.find('div.recipient').append(App.lang('Recipient: <a href=":url">:recipient</a>', {
        'url' : item['recipient']['urls']['view'],
        'recipient' : item['recipient']['short_display_name'] 
      }));

      message.find('div.created_on').append(App.lang('Added to Queue: :date', {
        'date' : item['created_on']['formatted']
      }));

      if(item['send_retries']) {
        message.find('div.retries').append(App.lang('Retries: <span>:retries</span>', {
          'retries' : item['send_retries']
        }));

        message.addClass('has_retries');
      } else {
        message.find('div.retries').append('--');
      } // if

      message.find('div.options')
        .append('<a href="' + item['urls']['view'] + '" class="preview_message" title="' + App.lang('Preview Message') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['send'] + '" class="send_message" title="' + App.lang('Send Now') + '"><img src="{image_url name="icons/12x12/email.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_message" title="' + App.lang('Remove from Queue') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      message.find('div.options a.preview_message').flyout();

      // Send message
      message.find('div.options a.send_message').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to send selected message now?'), 
        'success' : function(response) {
          if(message.find('div.retries span').length == 1) {
          	var retries = parseInt(message.find('div.retries span').text());
          } else {
            var retries = 0;
          } // if

          if(typeof(response) == 'object' && response && response['send_retries'] > retries) {
            if(message.find('div.retries span').length > 0) {
              message.find('div.retries span').text(response['send_retries']);
            } else {
              message.find('div.retries').empty().append(App.lang('Retries: <span>:retries</span>', {
                'retries' : response['send_retries']
              }));

              message.addClass('has_retries');
            } // if
            
            App.Wireframe.Flash.error(App.lang('Failed to send message. Reason: :reason', {
              'reason' : response['last_send_error']
            }));
          } else {
            message.addClass('sent');
            App.Wireframe.Flash.success(App.lang('Message has been sent successfully'));
          } // if
        }, 
        'error' : function() {
          App.Wireframe.Flash.error(App.lang('Failed to send selected message'));
        }
      });

      // Delete message
      message.find('div.options a.delete_message').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this message? If you delete it, message will not be sent and there is no undo!'), 
        'success' : function() {
          message.addClass('deleted');
          App.Wireframe.Flash.success(App.lang('Message has been deleted successfully'));
        }, 
        'error' : function() {
          App.Wireframe.Flash.error(App.lang('Failed to delete selected message'));
        }
      });
    }
  });
</script>