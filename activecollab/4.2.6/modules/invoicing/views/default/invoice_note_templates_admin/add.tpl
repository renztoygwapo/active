{title}Add Invoicing Note{/title}
{add_bread_crumb}New Invoicing Note{/add_bread_crumb}

<div class="add_invoice_note">
  {form action=$add_note_url method=POST}
    {include file=get_view_path('_note_form', 'invoice_note_templates_admin', 'invoicing')}  
    
    {wrap_buttons}
      {submit}Add Invoicing Note{/submit}
    {/wrap_buttons}
  {/form}
</div>