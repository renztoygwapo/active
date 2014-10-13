{use_widget name="paged_objects_list" module="environment"}
{use_widget name="invoice_update_property" module="invoicing"}
{use_widget name="payment_container" module="payments"}

{object object=$active_invoice user=$logged_user}
  {assign_var name=invoice_class}
    {if $active_invoice->isDraft()}
      invoice_draft
    {elseif $active_invoice->isIssued()}
    	{if ActiveCollab::getBrandingRemoved()}
      	invoice_issued branding_removed
      {else}
      	invoice_issued
      {/if}
    {elseif $active_invoice->isPaid()}
      invoice_paid
    {elseif $active_invoice->isCanceled()}
      invoice_canceled
    {/if}
  {/assign_var}
  
  <div class="invoice_paper_wrapper {$invoice_class|trim}_wrapper invoice_{$active_invoice->getId()}">
    <div class="invoice_paper {$invoice_class|trim}">
      <div class="invoice_paper_top"></div>
      <div class="invoice_paper_center">
        <div class="invoice_paper_area">
          <div class="invoice_paper_logo"></div>
          {if $logged_user->isFinancialManager()}
          <div class="invoice_comment property_wrapper" style="display: {if $active_invoice->getPrivateNote()}block{else}none{/if}"><span class="invoice_comment_paperclip"></span><span class="property_invoice_comment">{$active_invoice->getPrivateNote()}</span></div>
          {/if}
          <div class="invoice_paper_header">
            <div class="invoice_paper_details">
              <h2><span class="property_invoice_name">{$active_invoice->getName()}</span></h2>
              <ul>

                <li class="invoice_purchase_order_number property_wrapper" {if !$active_invoice->getPurchaseOrderNumber()}style="display: none"{/if}>{lang}Purchase Order #{/lang}: <strong><span class="property_invoice_purchase_order_number">{$active_invoice->getPurchaseOrderNumber()|clean}</span></strong></li>
                <li class="invoice_currency property_wrapper">{lang}Currency{/lang}: <strong><span class="property_invoice_currency">{$active_invoice->getCurrencyCode()}</span></strong></li>
                <li class="invoice_project property_wrapper" {if !($active_invoice->getProject() instanceof Project)} style="display: none" {/if}>{lang}Project{/lang}: <span class="property_invoice_project">{if ($active_invoice->getProject() instanceof Project)}{object_link object=$active_invoice->getProject()}{/if}</span></li>

                <li class="invoice_created_on property_wrapper">{lang}Created On{/lang}: <span class="property_invoice_created_on">{$active_invoice->getCreatedOn()|date}</span></li>
                <li class="invoice_issued_on property_wrapper">{lang}Issued On{/lang}: <span class="property_invoice_issued_on">{if $active_invoice->getIssuedOn()}{$active_invoice->getIssuedOn()|date:0}{/if}</span></li>
                <li class="invoice_due_on property_wrapper" {if !$active_invoice->getDueOn()} style="display: none" {/if}>{lang}Payment Due On{/lang}: <span class="property_invoice_due_on">{if $active_invoice->getDueOn()}{$active_invoice->getDueOn()|date:0}{/if}</span></li>
                <li class="invoice_paid_on property_wrapper">{lang}Paid On{/lang}: <span class="property_invoice_paid_on">{if $active_invoice->getClosedOn()}{$active_invoice->getClosedOn()|date}{/if}</span></li>
                <li class="invoice_closed_on property_wrapper">{lang}Closed On{/lang}: <span class="property_invoice_closed_on">{if $active_invoice->getClosedOn()}{$active_invoice->getClosedOn()|date}{/if}</span></li>
                
              </ul>
            </div>

            <div class="invoice_paper_client"><div class="invoice_paper_client_inner">
              <div class="invoice_paper_client_name property_wrapper"><span class="property_invoice_client_name">{company_link company=$active_invoice->getCompany()}</span></div>
              <div class="invoice_paper_client_address property_wrapper"><span class="property_invoice_client_address">{$active_invoice->getCompanyAddress()|clean|nl2br nofilter}</span></div>
            </div></div>
          </div>

          <div class="invoice_paper_items">
            {if is_foreachable($active_invoice->getItems())}
              {if $active_invoice->getSecondTaxIsEnabled()}
                {assign var="totals_colspan" value=$invoice_template->getColumnsCount() + 1}
              {else}
                {assign var="totals_colspan" value=$invoice_template->getColumnsCount()}
              {/if}

              <table cellspacing="0">
                <thead>
                  <tr class='items_head'>
                    {if $invoice_template->getDisplayItemOrder()}<td class="num"></td>{/if}
                    <td class="description">{lang}Description{/lang}</td>
                    {if $invoice_template->getDisplayQuantity()}<td class="quantity">{lang}Qty.{/lang}</td>{/if}
                    {if $invoice_template->getDisplayUnitCost()}<td class="unit_cost">{lang}Unit Cost{/lang}</td>{/if}
                    {if $invoice_template->getDisplaySubtotal()}<td class="subtotal">{lang}Subtotal{/lang}</td>{/if}
                    {if $active_invoice->getSecondTaxIsEnabled()}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #1{/lang}</td>{/if}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #2{/lang}</td>{/if}
                    {else}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax{/lang}</td>{/if}
                    {/if}
                    {if $invoice_template->getDisplayTotal()}<td class="total">{lang}Total{/lang}</td>{/if}
                  </tr>
                </thead>

                <tbody>
                {foreach from=$active_invoice->getItems() item=invoice_item name=item_foreach}
                  <tr class="{cycle values='odd,even'}">
                    {if $invoice_template->getDisplayItemOrder()}<td class="num">#{$smarty.foreach.item_foreach.iteration}</td>{/if}
                    <td class="description">{$invoice_item->getFormattedDescription() nofilter}</td>
                    {if $invoice_template->getDisplayQuantity()}<td class="quantity">{$invoice_item->getQuantity()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplayUnitCost()}<td class="unit_cost">{$invoice_item->getUnitCost()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplaySubtotal()}<td class="subtotal">{$invoice_item->getSubtotal()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$invoice_item->getFirstTax()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                    {if $active_invoice->getSecondTaxIsEnabled()}
                      {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$invoice_item->getSecondTax()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                    {/if}
                    {if $invoice_template->getDisplayTotal()}<td class="total">{$invoice_item->getTotal()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                  </tr>
                {/foreach}
                </tbody>
                <tfoot>
                  <tr class="subtotals_row">
                    <td colspan={$totals_colspan} class="label">{lang}Subtotal{/lang}</td>
                    <td class="value"><span class="property_wrapper property_invoice_subtotal">{$active_invoice->getSubTotal()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                  </tr>

                  {if $invoice_template->getSummarizeTax() || !is_foreachable($active_invoice->getTaxGroupedByType())}
                    <tr class="tax_row" style="{if $invoice_template->getHideTaxSubtotal() && $active_invoice->getTax() == 0}display: none;{/if}">
                      <td colspan={$totals_colspan} class="label">{lang}Tax{/lang}</td>
                      <td class="value"><span class="property_wrapper property_invoice_tax">{$active_invoice->getTax()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                    </tr>
                  {else}
                    {assign var=grouped_taxes value=$active_invoice->getTaxGroupedByType()}
                    {foreach from=$grouped_taxes item=grouped_tax}
                    <tr class="tax_row">
                      <td colspan={$totals_colspan} class="label">{$grouped_tax.name}</td>
                      <td class="value"><span class="property_wrapper property_invoice_tax">{$grouped_tax.amount|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                    </tr>
                    {/foreach}
                  {/if}

                  <tr class="property_wrapper" style="{if !$active_invoice->requireRounding()}display: none{/if}">
                    <td colspan={$totals_colspan} class="label">{lang}Rounding Difference{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_invoice_rounding_difference">{$active_invoice->getRoundingDifference()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                  </tr>

                  <tr class="total">
                    <td colspan={$totals_colspan} class="label">{lang}Total{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_invoice_total">{$active_invoice->getTotal(true)|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                  </tr>

                  <tr class="amount_paid" style="{if !$invoice_template->getShowAmountPaidBalanceDue() && (!$active_invoice->isPaid() && $active_invoice->payments()->getPayments() === null || $active_invoice->isPaid())}display: none;{/if}">
                    <td colspan={$totals_colspan} class="label">{lang}Amount Paid{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_invoice_amount_paid">{$active_invoice->getPaidAmount()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                  </tr>

                  <tr class="balance_due" style="{if !$invoice_template->getShowAmountPaidBalanceDue() && (!$active_invoice->isPaid() && $active_invoice->payments()->getPayments() === null || $active_invoice->isPaid())}display: none;{/if}">
                    <td colspan={$totals_colspan} class="label">{lang}Balance Due{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_balance_due">{$active_invoice->getBalanceDue()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                  </tr>

                </tfoot>
              </table>
            {else}
              <p class="empty_page"><span class="inner">{lang}This invoice has no items{/lang}</span></p>
            {/if}
          </div>


          <div class="invoice_paper_notes property_wrapper" style="display: {if $active_invoice->getNote()}block{else}none{/if}">
            <h3>{lang}Note{/lang}</h3>
            <p><span class="property_invoice_note">{$active_invoice->getNote()|clean|nl2br nofilter}</span></p>
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

<script type="text/javascript">
  var decimal_spaces = {$active_invoice->getCurrency()->getDecimalSpaces()|json nofilter};
  var second_tax_enabled = {$active_invoice->getSecondTaxIsEnabled()|json nofilter};

  var get_display_item_order = {$invoice_template->getDisplayItemOrder()|json nofilter};
  var get_display_quantity = {$invoice_template->getDisplayQuantity()|json nofilter};
  var get_display_unit_cost = {$invoice_template->getDisplayUnitCost()|json nofilter};
  var get_display_subtotal = {$invoice_template->getDisplaySubtotal()|json nofilter};
  var get_display_tax_rate = {$invoice_template->getDisplayTaxRate()|json nofilter};
  var get_display_tax_amount = {$invoice_template->getDisplayTaxAmount()|json nofilter};
  var get_display_total = {$invoice_template->getDisplayTotal()|json nofilter};
  var summarize_tax = {$invoice_template->getSummarizeTax()|json nofilter};
  var hide_tax_subtotal = {$invoice_template->getHideTaxSubtotal()|json nofilter};
  var show_amount_paid_balance_due = {$invoice_template->getShowAmountPaidBalanceDue()|json nofilter};

  $('.invoice_{$active_invoice->getId()}').each(function() {
    var wrapper = $(this);

    var wrapper_paper = wrapper.find('.invoice_paper:first');

  {if $active_invoice->isOverdue() && $active_invoice->isIssued()}
    wrapper.addClass('invoice_overdue_wrapper');
    wrapper_paper.addClass('invoice_overdue');
  {/if}

    App.Wireframe.Events.bind('resend_email.{$request->getEventScope()}', function(event, invoice) {
      if(invoice['id']) {
        App.Wireframe.Flash.success('Email notification has been sent');
        App.Wireframe.Events.trigger('invoice_updated',invoice);
      } //if
    });

    /**
     * Handle what happens when invoices get issued
     */
    App.Wireframe.Events.bind('invoice_updated.{$request->getEventScope()}', function(event, invoice) {
      $('#render_object_payments').paymentContainer('refresh', invoice);

      wrapper.removeClass('invoice_canceled_wrapper invoice_draft_wrapper invoice_issued_wrapper invoice_paid_wrapper invoice_overdue_wrapper');
      wrapper_paper.removeClass('invoice_canceled invoice_draft invoice_issued invoice_paid invoice_overdue');

      // update invoice properties
      wrapper.invoiceUpdateProperty([
        {
          'name' : 'invoice_name',
          'value' : App.clean(invoice.name),
          'auto_hide' : false
        }, {
          'name': 'invoice_currency',
          'value': App.clean(invoice.currency.code),
          'auto_hide': false
        }, {
          'name': 'invoice_purchase_order_number',
          'value': invoice.purchase_order_number ? App.clean(invoice.purchase_order_number) : null,
          'auto_hide': true
        }, {
          'name': 'invoice_project',
          'value': invoice.project ? '<a href="' + invoice.project.permalink + '">' + App.clean(invoice.project.name) + '</a>' : null
        }, {
          'name': 'invoice_created_on',
          'value' : invoice.created_on.formatted_date
        }, {
          'name': 'invoice_issued_on',
          'value' : typeof(invoice['issued_on']) == 'object' && invoice['issued_on'] ? invoice['issued_on']['formatted_date_gmt'] : null
        }, {
          'name': 'invoice_due_on',
          'value' : typeof(invoice['due_on']) == 'object' && invoice['due_on'] ? invoice['due_on']['formatted_date_gmt'] : null,
          'auto_hide' : true
        }, {
          'name': 'invoice_paid_on',
          'value' : typeof(invoice['paid_on']) == 'object' && invoice['paid_on'] ? invoice['paid_on']['formatted_date'] : null
        }, {
          'name': 'invoice_closed_on',
          'value' : typeof(invoice['closed_on']) == 'object' && invoice['closed_on'] ? invoice['closed_on']['formatted_date'] : null
        }, {
          'name': 'invoice_client_name',
          'value' : '<a href="' + invoice.client.permalink + '">' + App.clean(invoice.client.name) + '</a>'
        }, {
          'name': 'invoice_client_address',
          'value' : typeof(invoice['client_address']) == 'string' ? App.clean(invoice['client_address']).nl2br() : ''
        }, {
          'name': 'invoice_subtotal',
          'value' : App.moneyFormat(invoice.subtotal, invoice.currency, invoice.language),
          'auto_hide' : false
        }, {
          'name': 'invoice_total',
          'value' : App.moneyFormat(invoice.total, invoice.currency, invoice.language),
          'auto_hide' : false
        }, {
          'name': 'invoice_amount_paid',
          'value' : App.moneyFormat(invoice.paid_amount, invoice.currency, invoice.language),
          'auto_hide' : false
        }, {
          'name': 'balance_due',
          'value' : App.moneyFormat(invoice.balance_due, invoice.currency, invoice.language),
          'auto_hide' : false
        }, {
          'name': 'invoice_note',
          'value' : typeof(invoice['note']) == 'string' ? App.clean(invoice['note']).nl2br() : '',
          'auto_hide' : true
        }, {
          'name': 'invoice_comment',
          'value' : typeof(invoice['private_note']) == 'string' ? App.clean(invoice['private_note']) : '',
          'auto_hide' : true
        }, {
          'name' : 'invoice_rounding_difference',
          'value' : App.moneyFormat(invoice.rounding_difference, invoice.currency, invoice.language),
          'auto_hide' : true
        }
      ]);

      // update invoice items
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

          counter++;
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

      if(!show_amount_paid_balance_due && (!invoice.paid_on && !invoice.payments.paid_amount || invoice.paid_on)) {
        wrapper.find('tr.amount_paid, tr.balance_due').hide();
      } else {
        wrapper.find('tr.amount_paid, tr.balance_due').show();
      } // if

      // DRAFT
      if (invoice.status_conditions.is_draft) {
        wrapper.addClass('invoice_draft_wrapper');
        wrapper_paper.addClass('invoice_draft');

      // ISSUED
      } else if (invoice.status_conditions.is_issued) {
        wrapper.addClass('invoice_issued_wrapper');
        wrapper_paper.addClass('invoice_issued');

        if (invoice.status_conditions.is_overdue) {
          wrapper.addClass('invoice_overdue_wrapper');
          wrapper_paper.addClass('invoice_overdue');
        } // if

        wrapper.invoiceUpdateProperty([{
          'name' : 'invoice_name',
          'value' : App.clean(invoice.name),
          'auto_hide' : false
        }, {
          'name' : 'invoice_issued_on',
          'value' : invoice.issued_on.formatted_date_gmt
        }, {
          'name' : 'invoice_due_on',
          'value' : invoice.due_on.formatted_date_gmt,
          'auto_hide' : true
        }]);

      // Paid
      } else if (invoice.status_conditions.is_paid) {
        wrapper.addClass('invoice_paid_wrapper');
        wrapper_paper.addClass('invoice_paid');

      // CANCELED
      } else if (invoice.status_conditions.is_canceled) {
        wrapper.addClass('invoice_canceled_wrapper');
        wrapper_paper.addClass('invoice_canceled');

        wrapper.invoiceUpdateProperty([{
          'name' : 'invoice_name',
          'value' : App.clean(invoice.name),
          'auto_hide' : false
        }, {
          'name' : 'invoice_closed_on',
          'value' : invoice.closed_on.formatted_date
        }]);
      } // if

    });
  });
</script>