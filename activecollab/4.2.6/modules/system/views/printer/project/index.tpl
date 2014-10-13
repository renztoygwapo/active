<div id="print_container">
{object object=$active_project user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_project->inspector()->hasBody()}
          {$active_project->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      </div>
      
    </div>
  </div>


  <div class="wireframe_content_wrapper">{people_on_project project=$active_project user=$logged_user}</div>
    <div class="wireframe_content_wrapper">{project_milestone project=$active_project user=$logged_user}</div>
    <div class="wireframe_content_wrapper">{activity_logs_in in=$active_project id=recent_activities user=$logged_user}</div>
{/object}
</div>
