{title}Archived Files{/title}
{add_bread_crumb}Archive{/add_bread_crumb}

<div id="archived_assets">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($assets)}
	    {foreach $assets as $asset}
	    	<li><a href="{$asset->getViewUrl()}"><img class="ui-li-icon" src="{$asset->getIconUrl()}" alt=""/>{$asset->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}No archived files{/lang}</li>
		{/if}
	</ul>
</div>