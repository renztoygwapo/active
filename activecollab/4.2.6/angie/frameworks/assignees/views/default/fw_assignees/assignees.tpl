  {form action=$active_object->assignees()->getManageAssigneesUrl() method=post }
    {wrap_fields}
      {wrap field=assignees}
	      {label for=taskAssignees}Choose Assignees{/label}
	      {select_assignees name="object" value=$object_data.assignee_id  exclude=$exclude_user_ids other_assignees=$object_data.other_assignees object=$active_object user=$logged_user choose_responsible=true choose_subscribers=false small_form=true}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Update Assignees{/submit}
    {/wrap_buttons}
  {/form}