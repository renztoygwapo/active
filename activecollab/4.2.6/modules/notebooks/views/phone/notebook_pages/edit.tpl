{title}Edit Page{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_notebook_page">
	{form action=$active_notebook_page->getEditUrl()}
	  {include file=get_view_path('_notebook_page_form', 'notebook_pages', $smarty.const.NOTEBOOKS_MODULE)}
	  
	  {wrap_buttons}
	    {submit}Save Changes{/submit}
	  {/wrap_buttons}
	{/form}
</div>