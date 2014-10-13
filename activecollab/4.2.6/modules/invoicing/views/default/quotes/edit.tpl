{title}Edit Quote{/title}
{add_bread_crumb}Edit{/add_bread_crumb}
{use_widget name="invoice_form" module="invoicing"}

<div id="edit_invoice">
  {form action=$active_quote->getEditUrl() method=post id=edit_quote_form class="big_form"}
    {include file=get_view_path('_quote_form', 'quotes', 'invoicing')}  

    {wrap_buttons}
      {submit}Save Changes{/submit}
      {if $active_quote->isSent()}
      <span><input type="checkbox" id="quote_skip_notification" name="quote_skip_notification"/> {label for='quote_skip_notification' after_text=""}Do not notify the client about this modification{/label}</span>
      {/if}
    {/wrap_buttons}
  {/form}
</div>
<script type="text/javascript">
	$('#edit_invoice').invoiceForm({
    'mode'                    : 'edit',
	  'items'                   : {$quote_data.items|json nofilter},
	  'notes'                   : {$js_invoice_notes|json nofilter},
	  'initial_note'            : {$js_original_note|json nofilter},
	  'item_templates'          : {$js_invoice_item_templates|json nofilter},
	  'tax_rates'               : {$tax_rates|json nofilter},
	  'company_details_url'     : {$js_company_details_url|json nofilter},
    'delete_icon_url'         : {$js_delete_icon_url|json nofilter},
	  'move_icon_url'           : {$js_move_icon_url|json nofilter},
    'default_tax_rate'        : {$default_tax_rate|json nofilter},
    'currencies'              : {Currencies::getIdDetailsMap()|json nofilter},
    'second_tax_is_enabled'   : {$js_second_tax_is_enabled|json nofilter},
    'second_tax_is_compound'  : {$js_second_tax_is_compound|json nofilter}
	});
</script>