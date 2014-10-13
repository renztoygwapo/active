{if $context->isOverdue()}
  {lang name=$context->getName() language=$language}Invoice Overdue Reminder{/lang}
{else}
  {lang name=$context->getName() language=$language}Invoice Reminder{/lang}
{/if}
================================================================================
{if $context->isOverdue()}
  {assign_var name=notify_title}{lang language=$language}Invoice Overdue{/lang}{/assign_var}
{else}
  {assign_var name=notify_title}{lang language=$language}Invoice Details{/lang}{/assign_var}
{/if}

{notification_wrapper title=$notify_title context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
  {if $additional_message}
    <div style="padding-bottom: 30px;">
      <p>{lang language=$recipient->getLanguage()}Additionally, the following message has been provided{/lang}:</p>
      {notification_wrap_body}{$additional_message|nl2br nofilter}{/notification_wrap_body}
    </div>
  {/if}

  {notification_invoice_info context=$context recipient=$recipient}
  {notification_invoice_comment recipient=$recipient}{$context->getNote() nofilter}{/notification_invoice_comment}
  {notification_invoice_pay context=$context context_view_url=$context_view_url recipient=$recipient}
{/notification_wrapper}