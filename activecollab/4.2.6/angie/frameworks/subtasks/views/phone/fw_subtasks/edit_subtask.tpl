{title}Edit Subtask{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="edit_subtask">
  {form action=$active_subtask->getEditUrl()}
    {include file=get_view_path('_subtask_form', 'fw_subtasks', $smarty.const.SUBTASKS_FRAMEWORK)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>