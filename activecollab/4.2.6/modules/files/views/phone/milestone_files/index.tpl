{title}Files{/title}
{add_bread_crumb}Files{/add_bread_crumb}

<div id="milestone_files">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate.png" module=$smarty.const.FILES_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Files{/lang}</li>
		{if is_foreachable($files)}
			{foreach $files as $file}
				<li><a href="{replace search='--ASSETID--' in=$file_url replacement=$file.id}">{$file.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no Files{/lang}</li>
		{/if}
	</ul>
</div>