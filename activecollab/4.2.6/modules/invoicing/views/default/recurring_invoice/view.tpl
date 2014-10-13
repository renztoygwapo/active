{add_bread_crumb}Details{/add_bread_crumb}
{use_widget name="invoice_update_property" module="invoicing"}

<div id="recurring_profile_details">
{object object=$active_recurring_profile user=$logged_user}

  <div class="wireframe_content_wrapper">
    <div id="invoice_{$active_recurring_profile->getId()}">
      <div class="object_inspector"><div class="object_inspector_inner">
                
      </div></div>
    </div>
  </div>
  
  <div class="invoice_paper_wrapper recurring_profile_wrapper recurring_profile_{$active_recurring_profile->getId()}">
    <div class="invoice_paper recurring_profile">
      <div class="invoice_paper_top"></div>
      <div class="invoice_paper_center">
        <div class="invoice_paper_area">
          <div class="invoice_paper_logo"></div>
          
          <div class="invoice_comment property_wrapper" style="display: {if $active_recurring_profile->getPrivateNote()}block{else}none{/if}"><span class="invoice_comment_paperclip"></span><span class="property_invoice_comment">{$active_recurring_profile->getPrivateNote()}</span></div>
          
          <div class="invoice_paper_header">
            <div class="invoice_paper_details">
              <h2><span class="property_invoice_name">{$active_recurring_profile->getName()}</span></h2>
              <ul>                
                <li class="invoice_created_on property_wrapper">{lang}Created On{/lang}: <span class="property_invoice_created_on">{$active_recurring_profile->getCreatedOn()|date:0}</span></li>                
              </ul>
            </div>
            
            <div class="invoice_paper_client"><div class="invoice_paper_client_inner">
              <div class="invoice_paper_client_name property_wrapper"><span class="property_invoice_client_name">{company_link company=$active_recurring_profile->getCompany()}</span></div>
              <div class="invoice_paper_client_address property_wrapper"><span class="property_invoice_client_address">{$active_recurring_profile->getCompanyAddress()|clean|nl2br nofilter}</span></div>
            </div></div>
          </div>
          
          <div class="invoice_paper_items">
            {if is_foreachable($active_recurring_profile->getItems())}
              {if $active_recurring_profile->getSecondTaxIsEnabled()}
                {assign var="totals_colspan" value=$invoice_template->getColumnsCount() + 1}
              {else}
                {assign var="totals_colspan" value=$invoice_template->getColumnsCount()}
              {/if}

              <table cellspacing="0" >
                <thead>
                  <tr>
                    {if $invoice_template->getDisplayItemOrder()}<td class="num"></td>{/if}
                    <td class="description">{lang}Description{/lang}</td>
                    {if $invoice_template->getDisplayQuantity()}<td class="quantity">{lang}Qty.{/lang}</td>{/if}
                    {if $invoice_template->getDisplayUnitCost()}<td class="unit_cost">{lang}Unit Cost{/lang}</td>{/if}
                    {if $invoice_template->getDisplaySubtotal()}<td class="subtotal">{lang}Subtotal{/lang}</td>{/if}
                    {if $active_recurring_profile->getSecondTaxIsEnabled()}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #1{/lang}</td>{/if}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #2{/lang}</td>{/if}
                    {else}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax{/lang}</td>{/if}
                    {/if}
                    {if $invoice_template->getDisplayTotal()}<td class="total">{lang}Total{/lang}</td>{/if}
                  </tr>
                </thead>
                <tbody>
                {foreach from=$active_recurring_profile->getItems() item=invoice_item name=item_foreach}
                  <tr class="{cycle values='odd,even'}">
                    {if $invoice_template->getDisplayItemOrder()}<td class="num">#{$smarty.foreach.item_foreach.iteration}</td>{/if}
                    <td class="description">{$invoice_item->getFormattedDescription() nofilter}</td>
                    {if $invoice_template->getDisplayQuantity()}<td class="quantity">{$invoice_item->getQuantity()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplayUnitCost()}<td class="unit_cost">{$invoice_item->getUnitCost()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplaySubtotal()}<td class="subtotal">{$invoice_item->getSubtotal()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$invoice_item->getFirstTax()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                    {if $active_recurring_profile->getSecondTaxIsEnabled()}
                      {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$invoice_item->getSecondTax()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                    {/if}
                    {if $invoice_template->getDisplayTotal()}<td class="total">{$invoice_item->getTotal()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                  </tr>
                {/foreach}
                </tbody>
                <tfoot>
                  <tr class="subtotals_row">
                    <td colspan="{$totals_colspan}" class="label">{lang}Subtotal{/lang}</td>
                    <td class="value"><span class="property_wrapper property_invoice_subtotal">{$active_recurring_profile->getSubTotal()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</span></td>
                  </tr>

                  {if $invoice_template->getSummarizeTax() || !is_foreachable($active_recurring_profile->getTaxGroupedByType())}
                    <tr class="tax_row" style="{if $invoice_template->getHideTaxSubtotal() && $active_recurring_profile->getTax() == 0}display: none;{/if}">
                      <td colspan={$totals_colspan} class="label">{lang}Tax{/lang}</td>
                      <td class="value"><span class="property_wrapper property_invoice_tax">{$active_recurring_profile->getTax()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</span></td>
                    </tr>
                  {else}
                    {assign var=grouped_taxes value=$active_recurring_profile->getTaxGroupedByType()}
                    {foreach from=$grouped_taxes item=grouped_tax}
                      <tr class="tax_row">
                        <td colspan={$totals_colspan} class="label">{$grouped_tax.name}</td>
                        <td class="value"><span class="property_wrapper property_invoice_tax">{$grouped_tax.amount|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</span></td>
                      </tr>
                    {/foreach}
                  {/if}

                  <tr class="property_wrapper" style="{if !$active_recurring_profile->requireRounding()}display: none{/if}">
                    <td colspan="{$totals_colspan}" class="label">{lang}Rounding Difference{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_invoice_rounding_difference">{$active_recurring_profile->getRoundingDifference()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</span></td>
                  </tr>
                  <tr class="total">
                    <td colspan="{$totals_colspan}" class="label">{lang}Total{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_invoice_total">{$active_recurring_profile->getTotal(true)|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</span></td>
                  </tr>
                </tfoot>
              </table>
            {else}
              <p class="empty_page"><span class="inner">{lang}This invoice has no items{/lang}</span></p>
            {/if}
          </div>
          
          
          <div class="invoice_paper_notes property_wrapper" style="display: {if $active_recurring_profile->getNote()}block{else}none{/if}">
            <h3>{lang}Note{/lang}</h3>
            <p><span class="property_invoice_note">{$active_recurring_profile->getNote()|nl2br nofilter}</span></p>
          </div>
          
        </div>
      </div>
      <div class="invoice_paper_bottom"></div>
      
      <div class="invoice_paper_peel_draft"></div>
      <div class="invoice_paper_stamp_paid"></div>
      <div class="invoice_paper_stamp_canceled"></div>
    </div>
  </div>
{/object}
</div>
  
<script type="text/javascript">
  var scope = "{$request->getEventScope()|json}";
  var get_display_item_order = {$invoice_template->getDisplayItemOrder()|json nofilter};
  var get_display_quantity = {$invoice_template->getDisplayQuantity()|json nofilter};
  var get_display_unit_cost = {$invoice_template->getDisplayUnitCost()|json nofilter};
  var get_display_subtotal = {$invoice_template->getDisplaySubtotal()|json nofilter};
  var get_display_tax_rate = {$invoice_template->getDisplayTaxRate()|json nofilter};
  var get_display_tax_amount = {$invoice_template->getDisplayTaxAmount()|json nofilter};
  var get_display_total = {$invoice_template->getDisplayTotal()|json nofilter};

  var summarize_tax = {$invoice_template->getSummarizeTax()|json nofilter};
  var hide_tax_subtotal = {$invoice_template->getHideTaxSubtotal()|json nofilter};
  var second_tax_enabled = {$active_invoice->getSecondTaxIsEnabled()|json nofilter};

{literal}
  App.Wireframe.Events.bind('request_resolved.' + scope, function(event, approval_request) {
		$("#approval_req_view").remove();
	});

  App.Wireframe.Events.bind('recurring_profile_updated.' + scope, function(event, invoice) {
		var wrapper = $('.recurring_profile_' + invoice.id);
    var wrapper_paper = wrapper.find('.invoice_paper:first');

    wrapper.invoiceUpdateProperty([
      {name: 'invoice_name', value: App.clean(invoice.name), auto_hide : false },
      {name: 'invoice_currency', value: App.clean(invoice.currency.code), auto_hide: false},
      {name: 'invoice_project', value: invoice.project ? '<a href="' + invoice.project.permalink + '">' + App.clean(invoice.project.name) + '</a>' : null},
      {name: 'invoice_created_on', value : invoice.created_on.formatted_date},
      {name: 'invoice_issued_on', value : invoice.issued_on},
      {name: 'invoice_due_on', value : invoice.due_on, auto_hide : true },
      {name: 'invoice_paid_on', value : invoice.paid_on},
      {name: 'invoice_closed_on', value : invoice.closed_on},
      {name: 'invoice_client_name', value : '<a href="' + invoice.client.permalink + '">' + App.clean(invoice.client.name) + '</a>'},
      {name: 'invoice_client_address', value : App.clean(invoice.client_address).nl2br()},
      {name: 'invoice_subtotal', value : App.moneyFormat(invoice.subtotal, invoice.currency, invoice.language), auto_hide : false},
      {name: 'invoice_total', value : App.moneyFormat(invoice.total, invoice.currency, invoice.language), auto_hide : false},
      {name: 'invoice_note', value : App.clean(invoice.note).nl2br(), auto_hide : true},
      {name: 'invoice_comment', value : App.clean(invoice.private_note), auto_hide : true},
      {name: 'invoice_rounding_difference', value : App.moneyFormat(invoice.rounding_difference, invoice.currency, invoice.language), auto_hide : true}
    ]);
    
    var items_parent = wrapper.find('.invoice_paper_items table tbody').empty();
    var new_items = '';
    if (invoice.items && invoice.items.length) {
      var counter = 1;
      $.each(invoice.items, function (item_index, item) {
        new_items += '<tr>';

        // item number
        if (get_display_item_order) {
          new_items += '<td class="num">#' + counter + '</td>';
        } // if

        // description
        new_items += '<td class="description">' + item['formatted_description'] + '</td>';

        // quantity
        if (get_display_quantity) {
          new_items += '<td class="quantity">' + App.moneyFormat(item['quantity'], invoice['currency'], invoice['language']) + '</td>';
        } // if

        // unit cost
        if (get_display_unit_cost) {
          new_items += '<td class="unit_cost">' + App.moneyFormat(item['unit_cost'], invoice['currency'], invoice['language']) + '</td>';
        } // if

        // item subtotal
        if (get_display_subtotal) {
          new_items += '<td class="subtotal">' + App.moneyFormat(item['subtotal'], invoice['currency'], invoice['language']) + '</td>';
        } // if

        // item tax
        if (get_display_tax_rate) {
          new_items += '<td class="tax_rate">' + item['first_tax']['verbose_percentage'] + '</td>';
          if (second_tax_enabled) {
            new_items += '<td class="tax_rate">' + item['second_tax']['verbose_percentage'] + '</td>';
          } // if
        } else if (get_display_tax_amount) {
          new_items += '<td class="tax_amount">' + App.moneyFormat(item['first_tax']['value'], invoice['currency'], invoice['language']) + '</td>';
          if (second_tax_enabled) {
            new_items += '<td class="tax_amount">' + App.moneyFormat(item['second_tax']['value'], invoice['currency'], invoice['language']) + '</td>';
          } // if
        } // if

        // item total
        if (get_display_total) {
          new_items += '<td class="total">' + App.moneyFormat(item['total'], invoice['currency'], invoice['language']) + '</td>';
        } // if

        new_items += '</tr>'
        counter ++;
      });
      items_parent.html(new_items);
    } // if

    var tax_row = wrapper.find('tr.tax_row');

    if (summarize_tax) {
      wrapper.invoiceUpdateProperty([{
        'name': 'invoice_tax',
        'value' : App.moneyFormat(invoice.tax, invoice.currency, invoice.language),
        'auto_hide' : false
      }]);

      if(hide_tax_subtotal && invoice.tax == 0) {
        tax_row.hide();
      } else {
        tax_row.show();
      } // if
    } else {
      tax_row.remove();

      var insert_pointer = wrapper.find('tr.subtotals_row');
      var grouped_taxes = invoice['tax_grouped_by_type'];
      var col_span = insert_pointer.find('td.label').attr('colspan');
      var inserted = false;

      if (grouped_taxes && !$.isEmptyObject(grouped_taxes)) {
        $.each(grouped_taxes, function (index, grouped_tax) {
          if (grouped_tax['amount']) {
            insert_pointer = $('<tr class="tax_row"><td colspan="' + col_span + '" class="label">' + App.clean(grouped_tax['name']) + '</td><td class="value"><span class="property_wrapper property_invoice_tax">' + App.moneyFormat(grouped_tax['amount'], invoice.currency, invoice.language) + '</span></td></tr>').insertAfter(insert_pointer);
            inserted = true;
          } // if
        });
      } // if

      if (!inserted) {
        insert_pointer = $('<tr class="tax_row"><td colspan="' + col_span + '" class="label">' + App.lang('Tax') + '</td><td class="value"><span class="property_wrapper property_invoice_tax">' + App.moneyFormat(0, invoice.currency, invoice.language) + '</span></td></tr>').insertAfter(insert_pointer);
      } // if
    } // if
  });
  
{/literal}
</script>