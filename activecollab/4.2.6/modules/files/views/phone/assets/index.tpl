{title}All Files{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="assets_subitems" class="ui-grid-a">
	{if is_foreachable($subitems)}
		{foreach from=$subitems item=subitem name=item}
			{assign_var name=iteration}{$smarty.foreach.item.iteration}{/assign_var}
		  <a href="{$subitem.url}" class="ui-block-{if $iteration % 2 == 0}b{else}a{/if}"><img src="{$subitem.icon}" alt="" /><span>{$subitem.text}</span></a>
		{/foreach}
	{/if}
</div>

<div id="assets">
	{if is_foreachable($assets)}
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
			<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
			{foreach from=$assets item=asset}
				<li><a href="{$asset->getViewUrl()}"><img class="ui-li-icon" src="{$asset->getIconUrl()}" alt=""/>{$asset->getName()}</a></li>
			{/foreach}
		</ul>
	{/if}
</div>

<div class="archived_objects">
	<a href="{assemble route=project_assets_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Archive{/lang}</a>
</div>