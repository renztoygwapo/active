{title}New Notebook{/title}
{add_bread_crumb}New Notebook{/add_bread_crumb}

{form action=$add_notebook_url method=post enctype="multipart/form-data" ask_on_leave=yes autofocus=yes class='big_form'}
  {include file=get_view_path('_notebook_form', 'notebooks', 'notebooks')}
    
  {wrap_buttons}
    {submit}Add Notebook{/submit}
  {/wrap_buttons}
{/form}