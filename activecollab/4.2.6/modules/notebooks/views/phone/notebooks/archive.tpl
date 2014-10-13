{title}Archived Notebooks{/title}
{add_bread_crumb}Archived Notebooks{/add_bread_crumb}

<div id="notebooks_archive">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($notebooks)}
			{foreach $notebooks as $notebook}
				<li><a href="{$notebook->getViewUrl()}"><img class="ui-li-thumb" src="{$notebook->avatar()->getUrl(INotebookAvatarImplementation::SIZE_BIG)}" alt=""/>{$notebook->getName()}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}No archived Notebooks{/lang}</li>
		{/if}
	</ul>
</div>