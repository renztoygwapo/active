{title}Edit Page{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_notebook_page->getEditUrl() method=post ask_on_leave=yes class='big_form' enctype="multipart/form-data"}
  {include file=get_view_path('_notebook_page_form', 'notebook_pages', 'notebooks')}
  
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}