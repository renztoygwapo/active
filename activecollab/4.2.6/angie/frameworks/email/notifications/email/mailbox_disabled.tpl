{lang language=$language}Mailbox has been Disabled{/lang}
================================================================================
{notification_wrapper title='Mailbox Disabled' recipient=$recipient sender=$sender}
  <p>{lang mailbox_name=$mailbox_name resolve_url=$resolve_url link_style=$style.link}Mailbox ":mailbox_name" has been disabled. This happens when system fails to connect to the mailbox several times in a row. <a href=":resolve_url" style=":link_style">Click here</a> to see disabled mailbox and check why it can't connect{/lang}.</p>
{/notification_wrapper}