<div id="object_main_info" class="object_info">
  <h1>{lang}Milestone{/lang}: {$milestone->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$milestone}
</div>

{project_exporter_object_comments object=$milestone}

{if $milestone_submodules.todo && array_key_exists('todo', $navigation_sections)}
  {include file=get_view_path('exporter/milestone_todo_list')}
{/if}

{if $milestone_submodules.discussions && array_key_exists('discussions', $navigation_sections)}
  {include file=get_view_path('exporter/milestone_discussions_list')}
{/if}

{if $milestone_submodules.notebooks && array_key_exists('notebooks', $navigation_sections)}
  {include file=get_view_path('exporter/milestone_notebooks_list')}
{/if}

{if $milestone_submodules.tasks && array_key_exists('tasks', $navigation_sections)}
  {include file=get_view_path('exporter/milestone_tasks_list')}
{/if}

{if $milestone_submodules.tasks && array_key_exists('files', $navigation_sections)}
{include file=get_view_path('exporter/milestone_files_list')}
{/if}