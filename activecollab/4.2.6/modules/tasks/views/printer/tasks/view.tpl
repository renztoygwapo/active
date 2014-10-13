<div id="print_container">
{object object=$active_task user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_task->inspector()->hasBody()}
          {$active_task->inspector()->getBody($smarty.const.AngieApplication::INTERFACE_PRINTER) nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
        {object_attachments object=$active_task user=$logged_user}
      </div>

      {object_subtasks object=$active_task user=$logged_user}
    </div>
  </div>

  <div class="wireframe_content_wrapper">{object_comments object=$active_task user=$logged_user show_first=yes}</div>
  <div class="wireframe_content_wrapper">{object_history object=$active_task user=$logged_user}</div>
{/object}
</div>