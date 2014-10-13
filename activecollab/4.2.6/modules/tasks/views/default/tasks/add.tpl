{title}New Task{/title}
{add_bread_crumb}New Task{/add_bread_crumb}

<div id="add_project_task">
  {form action=$add_task_url enctype="multipart/form-data" autofocus=yes ask_on_leave=yes class='big_form'}
    {include file=get_view_path('_task_form', 'tasks', $smarty.const.TASKS_MODULE)}

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('task_created')}">
  {/if}
    
    {wrap_buttons}
      {submit}Add Task{/submit}
    {/wrap_buttons}
  {/form}
</div>