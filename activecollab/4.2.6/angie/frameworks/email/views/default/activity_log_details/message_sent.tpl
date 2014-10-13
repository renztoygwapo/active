<div id="message_sent_details" class="message_mailing_log_details">
  <div class="head">
    <div class="properties">
      <div class="property">
        <div class="label">{lang}Sender{/lang}</div>
        <div class="data">{user_link user=$log_entry->getFrom()}</div>
      </div>
      
      <div class="property">
        <div class="label">{lang}Recipient{/lang}</div>
        <div class="data">{user_link user=$log_entry->getTo()}</div>
      </div>
      
      <div class="property">
        <div class="label">{lang}Subject{/lang}</div>
        <div class="data">{$log_entry->getAdditionalProperty('subject')}</div>
      </div>
    </div>
  </div>
  
  <div class="body">{$log_entry->getAdditionalProperty('body') nofilter}</div>
</div>