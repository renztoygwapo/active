<div id="print_container">
{object object=$active_user user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_user->inspector()->hasBody()}
          {$active_user->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      </div>
    </div>
  </div>
  <div class="wireframe_content_wrapper">{project_list projects=$user_projects user=$active_user}</div>
  <div class="wireframe_content_wrapper">{activity_log activity_logs=$activity_logs user=$logged_user}</div>
{/object}
</div>