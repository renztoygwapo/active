{title lang=false}{$active_user->getName()}{/title}
{add_bread_crumb}Completed{/add_bread_crumb}

<div class="archived_user_projects">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
    <li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Completed Projects{/lang}</li>
  	{if is_foreachable($completed_projects)}
	  	{foreach $completed_projects as $completed_project}
		  	<li><a href="{$completed_project->getViewUrl()}"><img class="ui-li-icon" src="{$completed_project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt=""/>{$completed_project->getName()}</a></li>
	  	{/foreach}
	  {else}
	  	<li>{lang}There are no completed projects{/lang}</li>
  	{/if}
  </ul>
</div>