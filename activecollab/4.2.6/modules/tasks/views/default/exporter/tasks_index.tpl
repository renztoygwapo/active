<div id="object_main_info" class="object_info">
  <h1>{lang}Tasks{/lang}</h1>
</div>

<div id="categories" class="object_info">
  {project_exporter_categories categories=$categories category=$category type='tasks'}
</div>

<div class="category_objects">
  {project_exporter_task_list project=$project category=$category}
</div>