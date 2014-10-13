{title}Archived Notebook Pages{/title}
{add_bread_crumb}Archived Notebook Pages{/add_bread_crumb}

<div id="notebook_pages_archive">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($notebook_pages)}
	    {foreach $notebook_pages as $notebook_page}
	    	<li><a href="{$notebook_page->getViewUrl()}">{$notebook_page->getName()}<p class="ui-li-aside ui-li-desc">{lang version=$notebook_page->getVersion()}v:version{/lang}</p></a></li>
	    {/foreach}
		{else}
			<li>{lang}No archived Notebook Pages{/lang}</li>
		{/if}
	</ul>
</div>