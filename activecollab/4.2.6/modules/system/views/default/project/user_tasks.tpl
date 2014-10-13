{title}My Assignments on this Project{/title}
{add_bread_crumb}My Assignments on this Project{/add_bread_crumb}
{use_widget name="assignments_list" module="system"}

<div id="user_assignments"></div>

<script type="text/javascript">
  $('#user_assignments').assignmentsList({
    'assignments' : {$assignments|map nofilter},
    'labels' : {$labels|json nofilter},
    'project_slugs' : {$project_slugs|json nofilter},
    'task_url' : {$task_url|json nofilter},
    'task_subtask_url' : {$task_subtask_url|json nofilter},
    'todo_url' : {$todo_url|json nofilter},
    'todo_subtask_url' : {$todo_subtask_url|json nofilter},
    'show_assignment_type' : true, 
    'additional_column_1' : {$smarty.const.AssignmentFilter::ADDITIONAL_COLUMN_CATEGORY|json nofilter},
    'additional_column_2' : {$smarty.const.AssignmentFilter::ADDITIONAL_COLUMN_MILESTONE|json nofilter},
    'show_no_assignments_message' : true, 
    'no_assignments_message' : App.lang("You don't have any assignments in this project")
  });
</script>