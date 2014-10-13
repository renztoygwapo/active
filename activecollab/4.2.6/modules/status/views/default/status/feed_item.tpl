<p>{user_link user=$status_update->getCreatedBy()}: {$status_update->getMessage()|clean|clickable nofilter}</p>
{if $status_update->hasReplies(true)}
<p>{lang}Replies{/lang}:</p>
<ul>
{foreach from=$status_update->getReplies() item=status_update_reply}
  <li>{user_link user=$status_update_reply->getCreatedBy()}: {$status_update_reply->getMessage()|clean|clickable nofilter}<br />{$status_update_reply->getCreatedOn()|datetime}</li>
{/foreach}
</ul>
{/if}