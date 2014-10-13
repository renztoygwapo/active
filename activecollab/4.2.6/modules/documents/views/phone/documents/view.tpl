{add_bread_crumb}View{/add_bread_crumb}

<div id="document_details">
{if $active_document->getType() == 'file'}
	{object object=$active_document user=$logged_user show_body=false}
		{$active_document->preview()->renderLarge() nofilter}
		
		<div class="file_preview">
			<p><a href="{$active_document->getDownloadUrl()}" target="_blank">{$active_document->getName()}</a> ({$active_document->getSize()|filesize})</p>
		</div>
	{/object}
{else}
	{object object=$active_document user=$logged_user}{/object}
{/if}
</div>