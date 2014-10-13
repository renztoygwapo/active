
<div id="incoming_received_details" class="message_mailing_log_details">
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

      {if $log_entry->getAdditionalProperty('filter_name')}
        <div class="property">
          <div class="label">{lang}Filter{/lang}</div>
          <div class="data">{$log_entry->getAdditionalProperty('filter_name')}</div>
        </div>
      {/if}

      <div class="property">
        <div class="label">{lang}Action{/lang}</div>
        <div class="data">{$log_entry->getAdditionalProperty('action_name')}</div>
      </div>
      
      {if $log_entry->getResultingObjectUrl()}
        <div class="property">
          <div class="label">{lang}Target{/lang}</div>
          <div class="data">{link href=$log_entry->getResultingObjectUrl()  class='import_button'}View created object{/link}</div>
        </div>
       {/if}
      
      <div class="property">
        <div class="label">{lang}Subject{/lang}</div>
        <div class="data">{$log_entry->getAdditionalProperty('subject')}</div>
      </div>
      
      {if $log_entry->getIncomingMail()}
        <div class="property">
          <div class="label">{lang}Resolve{/lang}</div>
          <div class="data">{lang}Click {link href=$log_entry->getIncomingMail()->getImportUrl() title='Resolve Conflict' class='import_button'}here{/link} to resolve this conflict.{/lang}</div>
        </div>
      {/if}
      
      
    </div>
  </div>
  
  <div class="body">{$log_entry->getAdditionalProperty('body') nofilter}</div>
  
</div>