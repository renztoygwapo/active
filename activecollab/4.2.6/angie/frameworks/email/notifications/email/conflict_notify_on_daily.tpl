{if $conflict_num == 1}
  {lang language=$language}One Incoming Mail Conflict{/lang}
{else}
  {lang num=$conflict_num language=$language}:num Incoming Mail Conflicts{/lang}
{/if}
================================================================================
{notification_wrapper title='Incoming Mail Conflicts' recipient=$recipient sender=$sender}
	{if $conflict_num == 1} 
  	<p>{lang url=$conflict_page_url link_style=$style.link language=$language}There is <a href=":url" style=":link_style" target="_blank">one incoming mail conflict</a> that requires your attention{/lang}.</p>
  {else}
  	<p>{lang num=$conflict_num url=$conflict_page_url link_style=$style.link  language=$language}There are <a href=":url" style=":link_style" target="_blank">:num incoming mail conflicts</a> that requires your attention{/lang}.</p>
  {/if}
{/notification_wrapper}