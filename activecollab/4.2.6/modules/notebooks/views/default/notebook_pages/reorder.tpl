{form action=$reorder_url method=post}
  {wrap_fields}
    {reorder_pages_tree notebook=$active_notebook user=$logged_user}
  {/wrap_fields}
  
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}