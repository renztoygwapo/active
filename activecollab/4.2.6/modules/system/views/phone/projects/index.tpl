{title}Projects{/title}
{add_bread_crumb}Active{/add_bread_crumb}

<div id="projects">
	<div class="favorite_projects">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
			<li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Favorite Projects{/lang}</li>
			{if is_foreachable($favorite_projects)}
			  {foreach $favorite_projects as $project}
		 			<li><a href="{$project->getViewUrl()}"><img class="ui-li-icon" src="{$project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt=""/>{$project->getName()}</a></li>
			  {/foreach}
			{else}
				<li>{lang}There are no favorite projects{/lang}</li>
			{/if}
		</ul>
	</div>
	
	<div class="other_active_projects">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
			<li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Other Active Projects{/lang}</li>
			{if is_foreachable($other_projects)}
			  {foreach $other_projects as $project}
		 			<li><a href="{$project->getViewUrl()}"><img class="ui-li-icon" src="{$project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt=""/>{$project->getName()}</a></li>
			  {/foreach}
			{else}
				<li>{lang}There are no other active projects{/lang}</li>
			{/if}
		</ul>
	</div>
	
	<div class="archived_objects">
  	<a href="{assemble route=projects_archive}" data-role="button" data-theme="k">Completed Projects</a>
  </div>
</div>