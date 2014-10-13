{title}New Notebook{/title}
{add_bread_crumb}New Notebook{/add_bread_crumb}

{form action=$add_notebook_url}
  {include file=get_view_path('_notebook_form', 'notebooks', $smarty.const.NOTEBOOKS_MODULE)}
    
  {wrap_buttons}
    {submit}Add Notebook{/submit}
  {/wrap_buttons}
{/form}