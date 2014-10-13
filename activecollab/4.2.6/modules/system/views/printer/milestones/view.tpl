<div id="print_container">
{object object=$active_milestone user=$logged_user}
    <div class="wireframe_content_wrapper">{object_comments object=$active_milestone user=$logged_user show_first=yes}</div>

  {if AngieApplication::isModuleLoaded('tasks')}
    <div class="wireframe_content_wrapper">{milestone_list_objects object=$active_milestone user=$logged_user type=tasks}</div>
  {/if}

  {if AngieApplication::isModuleLoaded('discussions')}
    <div class="wireframe_content_wrapper">{milestone_list_objects object=$active_milestone user=$logged_user type=discussions}</div>
  {/if}

  {if AngieApplication::isModuleLoaded('todo')}
    <div class="wireframe_content_wrapper">{milestone_list_objects object=$active_milestone user=$logged_user type=todo}</div>
  {/if}
{/object}
</div>