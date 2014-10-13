{title}All Discussions{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="discussions">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($discussions)}
			{foreach $discussions as $discussion}
				<li><a href="{$discussion.permalink}"><img class="ui-li-icon" src="{$discussion.icon}" alt=""/>{$discussion.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no discussions{/lang}</li>
		{/if}
	</ul>
</div>

<div class="archived_objects">
	<a href="{assemble route=project_discussions_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Archive{/lang}</a>
</div>