<div id="print_container">
{object object=$active_asset user=$logged_user}
<div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_asset->inspector()->hasBody()}
          {$active_asset->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      </div>
      
    </div>
  </div>
  <div class="wireframe_content_wrapper">{object_comments object=$active_asset user=$logged_user show_first=yes}</div>
  <div class="wireframe_content_wrapper">{text_document_versions document=$active_asset user=$logged_user}</div>
{/object}
</div>