<div id="object_main_info" class="object_info">
  <h1>{lang}Discussion{/lang}: {$discussion->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$discussion}
</div>

{project_exporter_object_comments object=$discussion}