{title}Send Test Message{/title}
{add_bread_crumb}Send Test Message{/add_bread_crumb}

<div id="send_test_message" class="page_wrapper">
  {form action=$test_email_url method=post}
    {wrap field=recipient}
      {label}From{/label}
      {$test_email_from}
      <p class="details">{lang}This value is set in <strong>Notifications From</strong> block on <strong>Administration</strong> &raquo; <strong>Outgoing Mail</strong> page{/lang}</p>
    {/wrap}
  
    {wrap field=recipient}
      {label for=emailRecipient required=yes}Recipient{/label}
      {text_field name='email[recipient]' value=$email_data.recipient id=emailRecipient class=title}
    {/wrap}
    
    {wrap field=subject}
      {label for=emailSubject required=yes}Subject{/label}
      {text_field name='email[subject]' value=$email_data.subject id=emailSubject class="title"}
    {/wrap}
    
    {wrap field=message}
      {label for=emailMessage}Body{/label}
      {textarea_field name='email[message]' id=emailMessage class=editor}{$email_data.message nofilter}{/textarea_field}
    {/wrap}
    
    {wrap_buttons}
      {submit}Send Test Message{/submit}
    {/wrap_buttons}
  {/form}
</div>