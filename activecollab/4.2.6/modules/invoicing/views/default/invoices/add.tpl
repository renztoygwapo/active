{title}New Invoice{/title}
{add_bread_crumb}New Invoice{/add_bread_crumb}
{use_widget name="invoice_form" module="invoicing"}

<div id="add_invoice">
{form action=$add_invoice_url method=post id=add_invoice_form class="big_form"}
{include file=get_view_path('_invoice_form', 'invoices', 'invoicing')}

  {wrap_buttons}
    {submit}Create Invoice{/submit}
  {/wrap_buttons}
{/form}
</div>

<script type="text/javascript">
  $('#add_invoice_form').invoiceForm({
    'items'                   : {$invoice_data.items|json nofilter},
    'notes'                   : {$js_invoice_notes|json nofilter},
    'initial_note'            : {$js_original_note|json nofilter},
    'item_templates'          : {$js_invoice_item_templates|json nofilter},
    'tax_rates'               : {$tax_rates|json nofilter},
    'company_details_url'     : {$js_company_details_url|json nofilter},
    'company_projects_url'    : {$js_company_projects_url|json nofilter},
    'delete_icon_url'         : {$js_delete_icon_url|json nofilter},
    'move_icon_url'           : {$js_move_icon_url|json nofilter},
    'default_tax_rate'        : {$default_tax_rate|json nofilter},
    'currencies'              : {Currencies::getIdDetailsMap()|json nofilter},
    'second_tax_is_enabled'   : {$js_second_tax_is_enabled|json nofilter},
    'second_tax_is_compound'  : {$js_second_tax_is_compound|json nofilter}
  });
</script>