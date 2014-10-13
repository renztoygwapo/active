{use_widget name="assignments_list" module="system"}

<div id="assignments_filter_{$widget->getId()}"></div>

<script type="text/javascript">
  $('#assignments_filter_{$widget->getId()}').assignmentsList({
    'assignments' : {$assignments|map nofilter},
    'labels' : {$labels|json nofilter},
    'project_slugs' : {$project_slugs|json nofilter},
    'task_url' : {$task_url|json nofilter},
    'task_subtask_url' : {$task_subtask_url|json nofilter},
    'todo_url' : {$todo_url|json nofilter},
    'todo_subtask_url' : {$todo_subtask_url|json nofilter},
    'show_group_headers' : {if $show_group_headers}true{else}false{/if},
    'show_assignment_type' : true,
    'show_no_assignments_message' : true,
    'no_assignments_message' : {$widget->getEmptyResultMessage()|json nofilter},
    'no_assignments_message_css_class' : 'homescreen_empty_widget'
  });
</script>