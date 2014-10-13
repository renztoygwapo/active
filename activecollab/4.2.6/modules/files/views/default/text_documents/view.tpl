{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_asset user=$logged_user}
  <div class="wireframe_content_wrapper">
	  <div class="project_asset_text_document_preview object_body with_shadow">      
	    <div class="object_body_content formatted_content">
		    {if $active_asset->inspector()->hasBody()}
		      {$active_asset->inspector()->getBody() nofilter}
		    {/if}
      </div>
      {object_attachments object=$active_asset user=$logged_user}

	 </div>
  </div>

  <div class="wireframe_content_wrapper">{text_document_versions document=$active_asset user=$logged_user id="document_versions_for_{$active_asset->getId()}"}</div>  
  <div class="wireframe_content_wrapper">{object_comments object=$active_asset user=$logged_user show_first=yes}</div>
{/object}