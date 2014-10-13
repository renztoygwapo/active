<div id="print_container">
{object object=$active_discussion user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_discussion->inspector()->hasBody()}
          {$active_discussion->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
        {object_attachments object=$active_discussion user=$logged_user}
      </div>
    </div>
  </div>
  <div class="wireframe_content_wrapper">{object_comments object=$active_discussion user=$logged_user show_first=yes}</div>
{/object}
</div>
