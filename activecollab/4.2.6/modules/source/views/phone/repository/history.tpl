{title}Commit History{/title}
{add_bread_crumb}Commit History{/add_bread_crumb}

{object object=$project_object_repository user=$logged_user}{/object}

<div id="commit_history">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		{if is_foreachable($commits) and $commits|@count > 0}
	    {foreach from=$commits item=commits_day key=date}
	      <li data-role="list-divider">{$date}</li>
	        {foreach from=$commits_day item=commit}
	          <li>
              {assign var=commit_author value=$commit->getCommitedBy()}
	            <h3 class="ui-li-heading">#{substr($commit->getName(),0,8)} {lang}by{/lang}  {if $commit_author instanceof User}{$commit_author->getDisplayName(true)}{else}{$commit->getCommitedByName()}{/if}</h3>
	            <p class="ui-li-desc">{$commit->getMessageTitle()|stripslashes nofilter}</p>
	          </li>
	        {/foreach}
	    {/foreach}
		{else}
		  <li>{lang}There are no commits{/lang}</li>
		{/if}
	</ul>
</div>