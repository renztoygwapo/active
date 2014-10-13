{title}All Milestones{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="milestones">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($milestones)}
			{foreach $milestones as $milestone}
				<li><a href="{$milestone->getViewUrl()}"><h3 class="ui-li-heading">{$milestone->getName()}</h3><p class="ui-li-desc">{due_on object=$milestone}</p></a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no active milestones{/lang}</li>
		{/if}
	</ul>
</div>

<div class="archived_objects">
	<a href="{assemble route=project_milestones_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Completed Milestones{/lang}</a>
</div>