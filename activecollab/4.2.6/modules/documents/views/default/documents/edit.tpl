{title}Edit Document{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_document">
  {form action=$active_document->getEditUrl() class='big_form'}
    {include file=get_view_path('_document_form', 'documents', 'documents')}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>