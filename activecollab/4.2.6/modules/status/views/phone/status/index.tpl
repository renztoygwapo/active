{title}Status Updates{/title}
{add_bread_crumb}All Status Updates{/add_bread_crumb}

<div id="status_updates">
  {if is_foreachable($status_updates)}
	  {foreach $status_updates as $status_update}
	  	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="p">
		  	{assign var=status_update_user value=$status_update->getCreatedBy()}
				<li class="update"><a href="{$status_update->getViewUrl()}">
					<img class="ui-li-icon" src="{$status_update_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt=""/>
					<p class="update_details ui-li-desc">{$status_update_user->getDisplayName(true)} {$status_update->getCreatedOn()|ago nofilter}</p>
					<p class="update_overflow ui-li-desc">{$status_update->getMessage()}</p>
				</a></li>
				
				{if $status_update->hasReplies(true)}
				  {foreach from=$status_update->getReplies() item=status_update_reply}
				    {assign var=status_update_reply_user value=$status_update_reply->getCreatedBy()}
				    <li>
				    	<img class="ui-li-icon" src="{$status_update_reply_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" alt=""/>
				    	<p class="comment_details ui-li-desc">{$status_update_reply_user->getDisplayName(true)} {$status_update_reply->getCreatedOn()|ago nofilter}</p>
							<p class="comment_overflow ui-li-desc">{$status_update_reply->getMessage()}</p>
				    </li>
				  {/foreach}
				{/if}
			</ul>
	  {/foreach}
	{else}
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  		<li>{lang}There are no status updates{/lang}</li>
  	</ul>
	{/if}
</div>