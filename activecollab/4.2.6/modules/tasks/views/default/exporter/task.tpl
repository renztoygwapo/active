<div id="object_main_info" class="object_info">
  <h1>{lang}Task{/lang} #{$task->getTaskId()}: {$task->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$task}
</div>

{project_exporter_object_subtasks object=$task}

{if array_key_exists('tracking', $navigation_sections)}
  {project_exporter_object_timerecords object=$task user=$logged_user}
{/if}

{project_exporter_object_comments object=$task}