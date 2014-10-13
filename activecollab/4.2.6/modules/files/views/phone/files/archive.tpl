{title}Archived Files{/title}
{add_bread_crumb}Archived Files{/add_bread_crumb}

<div id="assets_files">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($files)}
	    {foreach $files as $file}
	    	<li><a href="{$file->getViewUrl()}">{$file->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}No archived Files{/lang}</li>
		{/if}
	</ul>
</div>