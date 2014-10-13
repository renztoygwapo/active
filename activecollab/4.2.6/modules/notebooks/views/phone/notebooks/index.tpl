{title}All Notebooks{/title}
{add_bread_crumb}All Notebooks{/add_bread_crumb}

<div id="notebooks">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($notebooks)}
			{foreach $notebooks as $notebook}
				<li><a href="{$notebook.permalink}"><img class="ui-li-thumb" src="{$notebook.avatar.large}" alt=""/>{$notebook.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no Notebooks{/lang}</li>
		{/if}
	</ul>
</div>

<div class="archived_objects">
	<a href="{assemble route=project_notebooks_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Archive{/lang}</a>
</div>