{title}New Task{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="new_task">
  {form action=$add_task_url}
    {include file=get_view_path('_task_form', 'tasks', $smarty.const.TASKS_MODULE)}

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('task_created', 'mobile')}">
  {/if}
    
    {wrap_buttons}
      {submit}Add Task{/submit}
    {/wrap_buttons}
  {/form}
</div>