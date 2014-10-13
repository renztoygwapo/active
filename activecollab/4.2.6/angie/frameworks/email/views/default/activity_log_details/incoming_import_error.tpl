<div id="incoming_import_error_details" class="message_mailing_log_details">
  <div class="head">
    <div class="properties">
      <div class="property">
        <div class="label">{lang}Sender{/lang}</div>
        <div class="data">{user_link user=$log_entry->getFrom()}</div>
      </div>
      
      {if $log_entry->getAdditionalProperty('from_email_original')}
        <div class="property">
          <div class="label">{lang}Original Sender{/lang}</div>
          <div class="data">{$log_entry->getAdditionalProperty('from_email_original')}</div>
        </div>
      {/if}
      
      <div class="property">
        <div class="label">{lang}Recipient{/lang}</div>
        <div class="data">{user_link user=$log_entry->getTo()}</div>
      </div>
      
      <div class="property">
        <div class="label">{lang}Mailbox{/lang}</div>
        <div class="data">{$log_entry->getMailboxDisplayName()}</div>
      </div>
      <div class="property">
        <div class="label">{lang}Subject{/lang}</div>
        <div class="data">{$log_entry->getAdditionalProperty('subject')}</div>
      </div>
      <div class="property">
        <div class="label">{lang}Error Message{/lang}</div>
        <div class="data">{$log_entry->getStatusDescription()}</div>
      </div>

      {if $log_entry->getAdditionalProperty('interceptor_name')}
        <div class="property">
          <div class="label">{lang}Interceptor Name{/lang}</div>
          <div class="data">{$log_entry->getAdditionalProperty('interceptor_name')}</div>
        </div>
      {/if}
      {if $log_entry->getAdditionalProperty('interceptor_action')}
        <div class="property">
          <div class="label">{lang}Action{/lang}</div>
          <div class="data">{$log_entry->getAdditionalProperty('interceptor_action')}</div>
        </div>
      {/if}

      {if $log_entry->getAdditionalProperty('filter_name')}
        <div class="property">
          <div class="label">{lang}Filter{/lang}</div>
          <div class="data">{$log_entry->getAdditionalProperty('filter_name')}</div>
        </div>
     {/if} 
     {if $log_entry->getAdditionalProperty('action_name')}
      <div class="property">
        <div class="label">{lang}Action{/lang}</div>
        <div class="data">{$log_entry->getAdditionalProperty('action_name')}</div>
      </div>
     {/if}

      {if $log_entry->getIncomingMail()}
        <div class="property">
          <div class="label">{lang}Resolve{/lang}</div>
          <div class="data">{lang}To resolve incoming mail conflict go to 'Incoming Mail Conflicts' tab.{/lang}</div>
        </div>
      {/if}
      
    </div>
  </div>
  
  <div class="body">{$log_entry->getAdditionalProperty('body') nofilter}</div>
</div>
