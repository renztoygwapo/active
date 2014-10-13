{title}New Page{/title}
{add_bread_crumb}New Page{/add_bread_crumb}

{form action=$active_notebook->getAddPageUrl() method=post ask_on_leave=yes enctype="multipart/form-data" autofocus=yes class='big_form'}
  {include file=get_view_path('_notebook_page_form', 'notebook_pages', 'notebooks')}
    
  {wrap_buttons}
    {submit}Add Page{/submit}
  {/wrap_buttons}
{/form}