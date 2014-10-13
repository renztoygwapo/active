{title}All Text Documents{/title}
{add_bread_crumb}All Text Documents{/add_bread_crumb}

<div id="assets_text_documents">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($text_documents)}
			{foreach $text_documents as $text_document}
				<li><a href="{$text_document->getViewUrl()}">{$text_document->getName()}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no Text Documents{/lang}</li>
		{/if}
	</ul>
</div>

<div class="archived_objects">
	<a href="{assemble route=project_assets_text_documents_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Archive{/lang}</a>
</div>