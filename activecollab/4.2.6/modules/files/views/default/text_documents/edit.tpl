{title}Edit Text Document{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_text_document">
  {form action=$active_asset->getEditUrl() method=post ask_on_leave=yes class="big_form"}
    {include file=get_view_path('_text_document_form', 'text_documents', 'files')}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>