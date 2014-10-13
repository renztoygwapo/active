<div id="print_container">
{object object=$active_notebook_page user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_notebook_page->inspector()->hasBody()}
          {$active_notebook_page->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      {object_attachments object=$active_notebook_page user=$logged_user}
      </div>
      
    </div>
  </div>
  <div class="wireframe_content_wrapper">{notebook_pages_tree notebook_pages=$subpages user=$logged_user}</div>
  <div class="wireframe_content_wrapper">{notebook_page_versions page=$active_notebook_page user=$logged_user}</div>
  <div class="wireframe_content_wrapper">{object_comments object=$active_notebook_page user=$logged_user show_first=yes}</div>
{/object}
</div>