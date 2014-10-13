{title name=$active_note->getName()}Edit Note Template: :name{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div class="add_invoice_note">
  {form action=$active_note->getEditUrl() method=POST}
    {include file=get_view_path('_note_form', 'invoice_note_templates_admin', 'invoicing')}  
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>