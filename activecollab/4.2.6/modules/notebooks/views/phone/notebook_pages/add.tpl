{title}New Page{/title}
{add_bread_crumb}New Page{/add_bread_crumb}

{form action=$active_notebook->getAddPageUrl()}
  {include file=get_view_path('_notebook_page_form', 'notebook_pages', $smarty.const.NOTEBOOKS_MODULE)}
    
  {wrap_buttons}
    {submit}Add Page{/submit}
  {/wrap_buttons}
{/form}