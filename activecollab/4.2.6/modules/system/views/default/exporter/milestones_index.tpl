<div id="object_main_info" class="object_info">
  <h1>{lang}Active Milestones{/lang}</h1>
</div>

<div class="category_objects">
  {project_exporter_milestone_list project=$project completed=false}
</div>

<div id="object_main_info" class="object_info">
  <h1>{lang}Completed Milestones{/lang}</h1>
</div>

<div class="category_objects">
  {project_exporter_milestone_list project=$project completed=true}
</div>