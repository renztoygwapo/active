<div id="print_container">
{object object=$active_notebook user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_notebook->inspector()->hasBody()}
          {$active_notebook->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      </div>
      
    </div>
  </div>
  <div class="wireframe_content_wrapper">{notebook_pages_tree notebook_pages=$notebook_pages user=$logged_user}</div>
  <div class="wireframe_content_wrapper">{object_history object=$active_notebook user=$logged_user}</div>
{/object}
</div>