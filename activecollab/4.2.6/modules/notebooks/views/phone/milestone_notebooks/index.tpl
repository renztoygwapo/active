{title}Notebooks{/title}
{add_bread_crumb}Notebooks{/add_bread_crumb}

<div id="milestone_notebooks">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate.png" module=$smarty.const.NOTEBOOKS_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Notebooks{/lang}</li>
		{if is_foreachable($notebooks)}
			{foreach $notebooks as $notebook}
				<li><a href="{replace search='--NOTEBOOKID--' in=$notebook_url replacement=$notebook.id}">{$notebook.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no Notebooks{/lang}</li>
		{/if}
	</ul>
</div>