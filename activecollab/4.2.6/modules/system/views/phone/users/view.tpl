{title lang=false}{$active_user->getName()}{/title}
{add_bread_crumb lang=false}Profile{/add_bread_crumb}

{object object=$active_user user=$logged_user}
  <div class="user_projects">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	    <li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Projects{/lang}</li>
	  	{if is_foreachable($active_projects)}
		  	{foreach $active_projects as $active_project}
		 			<li><a href="{$active_project->getViewUrl()}"><img class="ui-li-icon" src="{$active_project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt=""/>{$active_project->getName()}</a></li>
		  	{/foreach}
		  {else}
		  	<li>{lang}No active projects{/lang}</li>
	  	{/if}
	  </ul>
	</div>
  
	{if $can_view_activities}
		<div class="user_recent_activity">
			{activity_logs_by user=$logged_user by=$active_user}
		</div>
	{/if}
{/object}

<div class="archived_objects">
	<a href="{$completed_projects_url}" data-role="button" data-theme="k">Completed Projects</a>
</div>