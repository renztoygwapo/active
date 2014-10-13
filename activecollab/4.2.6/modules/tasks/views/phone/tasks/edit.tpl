{title}Update Task{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="new_project">
  {form action=$active_task->getEditUrl()}
    {include file=get_view_path('_task_form', 'tasks', $smarty.const.TASKS_MODULE)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>