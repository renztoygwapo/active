{title}Update Currency{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="add_tax_rate" class="add_invoice_tax_rate">
  {form action=$active_tax_rate->getEditUrl() method=post}
    {include file=get_view_path('_tax_rate_form', 'tax_rates_admin', 'invoicing')}

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>