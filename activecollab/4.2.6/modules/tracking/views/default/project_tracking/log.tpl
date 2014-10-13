{title}Time and Expenses{/title}
{add_bread_crumb}Log{/add_bread_crumb}
{use_widget name="project_tracking_totals" module="tracking"}
{use_widget name="time_expenses_log" module="tracking"}

<div class="wireframe_content_wrapper">
  <div id="project_tracking_totals"></div>

  <div class="project_time_expenses_wrapper"><div class="project_time_expenses_wrapper_inner">
    <div id="project_time_expenses"></div>
  </div></div>

  {empty_slate name=time_expenses module=$smarty.const.TRACKING_MODULE}
</div>

<script type="text/javascript">
  $('#project_tracking_totals').projectTrackingTotals({
    'totals' : {$totals|json nofilter},
    'project_id' : {$active_project->getId()|json nofilter},
    'currency' : {$active_project->getCurrency()|json nofilter},
    'refresh_totals_url' : '{assemble route=project_tracking_get_totals project_slug=$active_project->getSlug()}'
  });

  $('#project_time_expenses').timeExpensesLog({
    'initial_data' : {$items|map nofilter},
    'parent_tasks' : {$parent_tasks|json nofilter},
    'currency' : {$active_project->getCurrency()|json nofilter},
    'project_id' : {$active_project->getId()|json nofilter},
    'can_manage_items' : {$can_manage_items|json nofilter},
    'mass_update_url' : '{assemble route=project_tracking_mass_update project_slug=$active_project->getSlug()}'
  });  
</script>