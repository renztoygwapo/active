{title}Update Notebook{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_notebook->getEditUrl() method=post enctype="multipart/form-data" ask_on_leave=yes class='big_form'}
  {include file=get_view_path('_notebook_form', 'notebooks', 'notebooks')}
    
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}