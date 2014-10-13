{title}New Form{/title}
{add_bread_crumb}New Form{/add_bread_crumb}

<div id="new_public_task_form">
  {form action=Router::assemble('public_task_forms_add') class='big_form'}
    {include file=get_view_path('_public_task_form_form', 'public_task_forms_admin', 'tasks')}
    
    {wrap_buttons}
      {submit}Add Form{/submit}
    {/wrap_buttons}
  {/form}
</div>