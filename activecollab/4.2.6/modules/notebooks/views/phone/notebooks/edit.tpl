{title}Update Notebook{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_notebook->getEditUrl()}
  {include file=get_view_path('_notebook_form', 'notebooks', $smarty.const.NOTEBOOKS_MODULE)}
    
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}