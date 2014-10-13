{title}Completed Projects{/title}
{add_bread_crumb}Complete{/add_bread_crumb}

<div id="archived_projects">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Completed Projects{/lang}</li>
		{if is_foreachable($projects)}
	    {foreach $projects as $project}
				<li><a href="{$project->getViewUrl()}"><img class="ui-li-icon" src="{$project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt=""/>{$project->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}There are no completed projects{/lang}</li>
		{/if}
	</ul>
</div>