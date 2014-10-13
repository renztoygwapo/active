{add_bread_crumb}View{/add_bread_crumb}

<div id="document_details">
{if $active_document->getType() == 'file'}
	{object object=$active_document user=$logged_user show_body=false}
		<div class="wireframe_content_wrapper">
    <div class="project_asset_file_preview">
      <div class="real_preview">
        <div class="file_preview">{$active_document->preview()->renderLarge() nofilter}</div>
      </div>
      <p class="center"><a href="{$active_document->getDownloadUrl()}" target="_blank">{$active_document->getName()}</a> ({$active_document->getSize()|filesize})</p>
      <div class="object_body_content formatted_content"></div>
    </div>
  </div>
	{/object}
{else}
	{object object=$active_document user=$logged_user}
  
  {/object}
{/if}
</div>