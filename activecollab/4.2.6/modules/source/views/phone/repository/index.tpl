{title}Source Version Control{/title}
{add_bread_crumb}All Repositories{/add_bread_crumb}

<div id="repositories">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($repositories)}
			{foreach $repositories as $repository}
				<li><a href="{$repository->getViewUrl()}">
					{assign var=last_commit value=$repository->source_repository->getLastCommit()}
		      {if $last_commit instanceof SourceCommit}
		        <h3 class="ui-li-heading">{$repository->getName()}</h3>
				    <p class="ui-li-desc">
				      {$last_commit->getAuthor(null,false)} {$last_commit->getCommitedOn()|date}
				      <span class="ui-li-count">{substr($last_commit->getName(),0,8)}</span>
				    </p>
		      {else}
		        {$repository->getName()}
		        <span class="ui-li-count">0</span>
		      {/if}
		    </a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no repositories{/lang}</li>
		{/if}
	</ul>
</div>