{title}Incoming Mail{/title}
{add_bread_crumb}All Mailboxes{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="incoming_mailboxes"></div>

<script type="text/javascript">
  $('#incoming_mailboxes').pagedObjectsList({
    'load_more_url' : '{assemble route=incoming_email_admin_mailboxes}', 
    'items' : {$mailboxes|json nofilter},
    'items_per_load' : {$mailboxes_per_page}, 
    'total_items' : {$total_mailboxes}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'mailbox' }, 
    'columns' : {
      'is_enabled' : '', 
      'name' : App.lang('Name'), 
      'host' : App.lang('Host Name'), 
      'email' : App.lang('Address'), 
      'status' : App.lang('Last Connection Status'), 
      'options' : '' 
    }, 
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no incoming mailboxes defined'), 
    'listen' : 'incoming_mailbox', 
    'on_add_item' : function(item) {
      var mailbox = $(this);
      
      mailbox.append('<td class="is_enabled">' + 
        '<td class="name"></td>' + 
        '<td class="host"></td>' + 
        '<td class="email"></td>' +  
        '<td class="status"></td>' +  
        '<td class="options"></td>'
      );

      var chc = $('<input type="checkbox" />').attr({
        'on_url' : item['urls']['enable'], 
        'off_url' : item['urls']['disable']
      }).asyncCheckbox({
        'success_event' : 'incoming_mailbox_updated', 
        'success_message' : [ App.lang('Mailbox has been disabled'), App.lang('Mailbox has been enabled') ],
      });
      if(item['is_enabled']) { 
          chc.attr('checked','checked');
      }//if
      chc.appendTo(mailbox.find('td.is_enabled'));

      $('<a></a>').attr('href', item['urls']['list_messages']).text(item['name']).appendTo(mailbox.find('td.name'));
      mailbox.find('td.host').text(App.clean(item['host']));
      mailbox.find('td.email').text(App.clean(item['email']));
      
      if(item['status'] == 1) {
        mailbox.find('td.status').append(App.lang('OK'));
      } else if(item['status'] == 2) {
        mailbox.find('td.status').append(App.lang('Failed'));
      } else {
        mailbox.find('td.status').append(App.lang('Not Checked'));
      } // if

      mailbox.find('td.options')
        .append('<a href="' + item['urls']['view'] + '" class="mailbox_details" title="' + App.lang('View Details') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_mailbox" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_mailbox" title="' + App.lang('Remove Mailbox') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      //mailbox.find('td.options a.mailbox_details').flyout();
      mailbox.find('td.options a.edit_mailbox').flyoutForm({
        'success_event' : 'incoming_mailbox_updated'
      });
      mailbox.find('td.options a.delete_mailbox').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this mailbox? Messages imported through this mailbox will not be removed'), 
        'success_event' : 'incoming_mailbox_deleted', 
        'success_message' : App.lang('Mailbox has been deleted successfully')
      });
    }
  });
</script>