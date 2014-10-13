{lang language=$language}Incoming Mail Conflict Created{/lang}
================================================================================
{notification_wrapper title='New Incoming Mail Conflict' recipient=$recipient sender=$sender}
	<p>{lang language=$language}System has just found an email in your mailbox that can't be handled automatically. Please resolve this situation manually{/lang}.</p>
	<p>
  	{lang url=$conflict_page_url link_style=$style.link language=$language subject=$pending_email->getSubject()}To resolve conflict with ':subject' email click on <a href=":url" style=":link_style" target="_blank">this</a> link{/lang}.
  	<br/>
  	{lang conflict_reason=$pending_email->getStatus() language=$language}Conflict reason: :conflict_reason{/lang}
	</p>
	{notification_wrap_body}{$pending_email->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}