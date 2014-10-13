{title lang=false}{$active_company->getName()}{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div id="company_projects">
	<div class="active_company_projects">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	    <li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Active Projects{/lang}</li>
	  	{if is_foreachable($active_projects)}
		  	{foreach $active_projects as $active_project}
		  		<li><a href="{$active_project->getViewUrl()}"><img class="ui-li-icon" src="{image_url name="layout/avatars/project-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt=""/>{$active_project->getName()}</a></li>
		  	{/foreach}
		  {else}
		  	<li>{lang}There are no active projects{/lang}</li>
	  	{/if}
	  </ul>
	</div>
	
	<div id="archived_company_projects">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	    <li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Completed Projects{/lang}</li>
	  	{if is_foreachable($completed_projects)}
		  	{foreach $completed_projects as $completed_project}
		  		<li><a href="{$completed_project->getViewUrl()}"><img class="ui-li-icon" src="{image_url name="layout/avatars/project-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt=""/>{$completed_project->getName()}</a></li>
		  	{/foreach}
		  {else}
		  	<li>{lang}There are no completed projects{/lang}</li>
	  	{/if}
	  </ul>
	</div>
</div>