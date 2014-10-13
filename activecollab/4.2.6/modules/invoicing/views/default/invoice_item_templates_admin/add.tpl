{title}New Predefined Invoice Item{/title}
{add_bread_crumb}New Item{/add_bread_crumb}

<div class="add_invoice_item">
  {form action=$add_template_url method=post autofocus=yes ask_on_leave=false}
    {include file=get_view_path('_invoice_item_template_form', 'invoice_item_templates_admin', 'invoicing')}  
    
    {wrap_buttons}
      {submit}Add Predefined Invoice Item{/submit}
    {/wrap_buttons}
  {/form}
</div>