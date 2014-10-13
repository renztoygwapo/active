{title description=$active_invoice_item_template->getDescription}Edit Item: :description{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div class="add_invoice_item">
  {form action=$active_item_template->getEditUrl()}
    {include file=get_view_path('_invoice_item_template_form', 'invoice_item_templates_admin', 'invoicing')}  
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>