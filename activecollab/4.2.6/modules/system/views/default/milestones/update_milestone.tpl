  {form action=$form_url method=post }
    {wrap_fields}
      {wrap field=milestone_id}
        {select_milestone name="object[milestone_id]" value=$object_data.milestone_id project=$active_project user=$logged_user label='Milestone'}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Update Milestone{/submit}
    {/wrap_buttons}
  {/form}