{title}Add Document{/title}
{add_bread_crumb}Add Text{/add_bread_crumb}

<div id="add_text_document">
  {form action=Router::assemble('documents_add_text') class='big_form'}
    {include file=get_view_path('_document_form', 'documents', 'documents')}
    
    {wrap_buttons}
      {submit}Create Document{/submit}
    {/wrap_buttons}
  {/form}
</div>