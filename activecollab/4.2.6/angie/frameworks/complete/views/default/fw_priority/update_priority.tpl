  {form action=$active_object->complete()->getUpdatePriorityUrl() method=post }
    {wrap_fields}
	    {wrap field=priority}
	      {select_priority name="object[priority]" value=$object_data.priority label='Priority'}
	    {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Update Priority{/submit}
    {/wrap_buttons}
  {/form}