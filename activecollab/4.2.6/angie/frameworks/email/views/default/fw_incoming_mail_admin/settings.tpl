{title}Incoming Email Settings{/title}
{add_bread_crumb}Incoming Email Settings{/add_bread_crumb}

<div id="incoming_mail_settings">
  {form action=Router::assemble('incoming_email_admin_change_settings') method=post id="incoming_mail_settings_admin"}
    <div class="content_stack_wrapper">


    {if !AngieApplication::mailer()->isConnectionConfigurationLocked()}
    	<div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}General Settings{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
        	{wrap field="disable_mailbox_settings"}
        		<div>
          		{radio_field name="settings[disable_mailbox_on_successive_connection_failures]" id="disable_mailbox_off" class="disable_mailbox" pre_selected_value=$settings_data.disable_mailbox_on_successive_connection_failures value=IncomingMail::AUTO_DISABLE_MAILBOX_OFF label='Never Disable Mailboxes on Successive Connection Failures'}
          	</div>
          	<div>  
          		{radio_field name="settings[disable_mailbox_on_successive_connection_failures]" id="disable_mailbox_on" class="disable_mailbox" pre_selected_value=$settings_data.disable_mailbox_on_successive_connection_failures value=IncomingMail::AUTO_DISABLE_MAILBOX_ON label='Automatically Disable Mailbox on Successive Connection Failures'}
          	</div>
          	<div id="successive_connection_attempts">
          		<div>
          			{select_successive_connection_attempts name="settings[successive_connection_attempts]" value=$settings_data.successive_connection_attempts}
          		</div>
          		<div>
          		{checkbox_field name="settings[notify_administrator_when_mailbox_is_disabled]" value=1 label='Notify administrators when mailbox is disabled' class="notify_administrator" checked=$settings_data.notify_administrator_when_mailbox_is_disabled}
          		</div>
          	</div>
          {/wrap}
        </div>
      </div>
    {/if}
      
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Conflict Notifications{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
        	{wrap field="notify_instantly"}
            {select_conflict_notification_delivery name="settings[conflict_notifications_delivery]" value=$settings_data.conflict_notifications_delivery}
        	{/wrap}    
        </div>
      </div>

    </div>
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#incoming_mail_settings').each(function() {
    var wrapper = $(this);

		var successive_connection_attempts_container = wrapper.find('div#successive_connection_attempts'); 
		
    wrapper.find('#disable_mailbox_off').each(function() {
        $(this).click(function() {
        	successive_connection_attempts_container.slideUp('fast');
        });
        
        if(this.checked) {
        	successive_connection_attempts_container.hide();
        } // if 
      });

    wrapper.find('#disable_mailbox_on').each(function() {
        $(this).click(function() {
        	successive_connection_attempts_container.slideDown('fast');
        });
        
        if(this.checked) {
        	successive_connection_attempts_container.show();
        } // if 
      });
    
  });
</script>