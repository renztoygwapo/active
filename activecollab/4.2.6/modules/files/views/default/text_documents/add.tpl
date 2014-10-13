{title}New Text Document{/title}
{add_bread_crumb}New Text Document{/add_bread_crumb}

<div id="add_task">
  {form action=$add_text_document_url method=post enctype="multipart/form-data" autofocus=yes ask_on_leave=yes class='big_form'}
    {include file=get_view_path('_text_document_form', 'text_documents', 'files')}
    
    {wrap_buttons}
      {submit}Create Document{/submit}
    {/wrap_buttons}
  {/form}
</div>