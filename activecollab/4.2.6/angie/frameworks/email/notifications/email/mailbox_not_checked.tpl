{lang language=$language}Mailbox not Checked{/lang}
================================================================================
{notification_wrapper title='Mailbox not Checked' recipient=$recipient sender=$sender}
	<p>{lang language=$language}Frequently scheduled task has experienced some issues{/lang}.</p>
  <p>{lang language=$language mailbox_name=$mailbox_name}Mailbox :mailbox_name has not been checked and emails aren't imported from this mailbox{/lang}. {lang}Info{/lang}:</p>
  <div style="margin-top: 20px;">{$error nofilter}</div>
{/notification_wrapper}