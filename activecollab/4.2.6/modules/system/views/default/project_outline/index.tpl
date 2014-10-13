{title}Project Outline{/title}
{add_bread_crumb}Project Outline{/add_bread_crumb}

<div id="project_outline"></div>

<script type="text/javascript">
  $('#project_outline').projectOutline({
    'initial_object' : {$active_project|json nofilter},
    'default_visibility' : {$default_visibility|json nofilter},
    'initial_subobjects' : {$initial_subobjects nofilter},
    'subobjects_url' : '{$subobjects_url}',
    'reorder_url' : '{$reorder_url}',
    'users' : {$users|json nofilter},
    'labels' : {$labels_map|map nofilter},
    'default_labels' : {$default_labels|json nofilter},
    'categories' : {$categories_map|json nofilter},
    'milestones' : {$milestones_map|json nofilter},
    'users_map' : {$users_map|json nofilter},
    'companies_map' : {$companies_map|json nofilter},
    'job_types_map' : {$job_types_map|map nofilter},
    'add_urls' : {$add_urls|json nofilter},
    'mass_edit_urls' : {$mass_edit_urls|json nofilter},
    'permissions' : {$permissions|json nofilter},
    'shortcuts_url' : {$shortcuts_url|json nofilter},
    'unclassified_label' : {$unclassified_label|json nofilter},
    'default_billable_status' : {$default_billable_status|json nofilter}
  });
</script>