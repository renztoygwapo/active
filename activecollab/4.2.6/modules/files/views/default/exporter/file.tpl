<div id="object_main_info" class="object_info">
  <h1>{$project_asset->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$project_asset}
</div>

{if ($project_asset instanceof File)}
  <div id="object_revisions" class="object_info">
    {project_exporter_file_versions file=$project_asset}
  </div>
{elseif ($project_asset instanceof TextDocument)}
  <div id="object_revisions" class="object_info">
    {project_exporter_document_versions text_document=$project_asset}
  </div>
{/if}

{project_exporter_object_comments object=$project_asset}

