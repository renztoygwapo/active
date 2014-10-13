<div id="object_main_info" class="object_info">
  <h1>{lang}Page{/lang}: {$page->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_page_properties object=$page}
</div>

<div id="object_index_info" class="object_info">
  <h3>{lang}Subpages{/lang}:</h3>
  {project_exporter_page_list parent=$page}
</div>

{project_exporter_object_comments object=$page}

{assign var="revisions" value=NotebookPageVersions::findByNotebookPage($page)}

{if is_foreachable($revisions)}
  <div id="object_index_info" class="object_info">
    <h3>{lang}Revisions{/lang}:</h3>
    {project_exporter_revision_list revisions=$revisions page=$page}
  </div>
{/if}