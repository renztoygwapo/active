{title}New Subtask{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="add_subtask">
  {form action=$active_object->subtasks()->getAddUrl()}
    {include file=get_view_path('_subtask_form', 'fw_subtasks', $smarty.const.SUBTASKS_FRAMEWORK)}
    
    {wrap_buttons}
      {submit}Add Subtask{/submit}
    {/wrap_buttons}
  {/form}
</div>