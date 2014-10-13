{title}New Text Document{/title}
{add_bread_crumb}New Text Document{/add_bread_crumb}

<div id="add_text_document">
  {form action=$add_text_document_url}
    {include file=get_view_path('_text_document_form', 'text_documents', $smarty.const.FILES_MODULE)}
    
    {wrap_buttons}
      {submit}Add Document{/submit}
    {/wrap_buttons}
  {/form}
</div>