<div id="object_main_info" class="object_info">
  <h1>{lang}Notebook{/lang}: {$notebook->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$notebook}
</div>

<div id="object_index_info" class="object_info">
  <h3>{lang}Pages{/lang}:</h3>
  {project_exporter_page_list parent=$notebook}
</div>
