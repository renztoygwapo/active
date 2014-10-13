{title}New Tax Rate{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div class="add_invoice_tax_rate">
  {form action=$add_tax_rate_url method=post}
    {include file=get_view_path('_tax_rate_form', 'tax_rates_admin', 'invoicing')}  

    {wrap_buttons}
      {submit}Add Tax Rate{/submit}
    {/wrap_buttons}
  {/form}
</div>