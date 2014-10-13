{title}Update Form{/title}
{add_bread_crumb}Update Form{/add_bread_crumb}

<div id="update_public_task_form">
  {form action=$active_public_task_form->getEditUrl() class='big_form'}
    {include file=get_view_path('_public_task_form_form', 'public_task_forms_admin', 'tasks')}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>