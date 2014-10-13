<li class="{if $created_by instanceof User}registered{else}anonymous{/if}">
	<img src="{$created_by->avatar()->getUrl()}" class="ui-li-icon" alt="{lang name=$created_by->getFirstName()}:name's avatar{/lang}">
	<p class="comment_details ui-li-desc">By <a class="ui-link" href="{$created_by->getViewUrl()}">{$created_by->getDisplayName(true)}</a> on {$created_on|date}</p>
	<div class="comment_overflow ui-li-desc">{$body|rich_text:frontend nofilter}</div>
	
	{if $attachments}
    {foreach $attachments as $attachment}
    	<div class="comment_attachment">
      	<a href="{$attachment->getPublicViewUrl(true)}"><img src="{$attachment->preview()->getLargeIconUrl()}" /><span class="filename">{$attachment->getName()}</span></a>
      </div>
    {/foreach}
  {/if}
</li>