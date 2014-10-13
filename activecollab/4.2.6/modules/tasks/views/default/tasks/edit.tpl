{title task_id=$active_task->getTaskId()}Edit Task #:task_id{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_task">
  {form action=$active_task->getEditUrl() method=post ask_on_leave=yes class="big_form" enctype="multipart/form-data"}
    {include file=get_view_path('_task_form', 'tasks', 'tasks')}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>