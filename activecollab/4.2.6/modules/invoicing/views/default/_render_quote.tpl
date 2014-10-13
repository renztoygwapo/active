{use_widget name="invoice_update_property" module="invoicing"}

{object object=$active_quote user=$logged_user show_inspector=true}
  {assign_var name=quote_class}
    {if $active_quote->isDraft()}
      quote_draft
    {elseif $active_quote->isSent()}
      quote_sent
    {elseif $active_quote->isWon()}
      quote_won
    {elseif $active_quote->isLost()}
      quote_lost
    {/if}
  {/assign_var}

  <div class="invoice_paper_wrapper {$quote_class|trim}_wrapper quote_{$active_quote->getId()}">
    <div class="invoice_paper {$quote_class|trim}">
      <div class="invoice_paper_top"></div>
      <div class="invoice_paper_center">
        <div class="invoice_paper_area">
          <div class="invoice_paper_logo"></div>
          {if Quotes::canManage($logged_user)}
          <div class="invoice_comment property_wrapper" style="display: {if $active_quote->getPrivateNote()}block{else}none{/if}"><span class="invoice_comment_paperclip"></span><span class="property_invoice_comment property_quote_private_note">{$active_quote->getPrivateNote()}</span></div>
          {/if}
          <div class="invoice_paper_header">
            <div class="invoice_paper_details">
              <h2><span class="property_quote_name">{$active_quote->getName()}</span></h2>
              <ul>
                <li class="quote_currency property_wrapper">{lang}Currency{/lang}: <strong><span class="property_quote_currency">{$active_quote->getCurrencyCode()}</span></strong></li>
                <li class="quote_created_on property_wrapper">{lang}Created On{/lang}: <span class="property_quote_created_on">{if $active_quote->getCreatedOn()}{$active_quote->getCreatedOn()|date}{/if}</span></li>
                <li class="quote_sent_on property_wrapper">{lang}Sent On{/lang}: <span class="property_quote_sent_on">{if $active_quote->getSentOn()}{$active_quote->getSentOn()|date}{/if}</span></li>
                <li class="quote_closed_on property_wrapper">{lang}Closed On{/lang}: <span class="property_quote_closed_on">{if $active_quote->getClosedOn()}{$active_quote->getClosedOn()|date}{/if}</span></li>
              </ul>
            </div>

            <div class="invoice_paper_client"><div class="invoice_paper_client_inner">
              <div class="invoice_paper_client_name property_wrapper">
                <span class="property_quote_client_name">
                  {if $active_quote->getCompany() instanceof Company}
                    {company_link company=$active_quote->getCompany()}
                  {else}
                    <b>{$active_quote->getCompanyName()}</b>
                  {/if}
                  <br/>
                  {$active_quote->getCompanyAddress()|clean|nl2br nofilter}
                </span>
              </div>
              <div class="invoice_paper_client_address property_wrapper">
                <span class="property_quote_client_address">
                  {lang}Contact Person{/lang}: {user_link user=$active_quote->getRecipient()}
                </span>
              </div>
              {if !($active_quote->getCompany() instanceof Company) && $logged_user->isPeopleManager()}
              <!--<div class="property_wrapper">-->
                <span class="client_save_data">
                  <a href="{$active_quote->getSaveClientUrl()}" title="{lang}Add Client to People{/lang}">
                    <img src="{image_url name='icons/16x16/save_client.png' module=system}" alt="{lang}Add Client to People{/lang}"/>
                  </a>
                </span>
              <!--</div>-->
              {/if}
            </div></div>
          </div>

          <div class="invoice_paper_items">
            {if is_foreachable($active_quote->getItems())}
              {if $active_quote->getSecondTaxIsEnabled()}
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
                    {if $active_quote->getSecondTaxIsEnabled()}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #1{/lang}</td>{/if}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #2{/lang}</td>{/if}
                    {else}
                      {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax{/lang}</td>{/if}
                    {/if}
                    {if $invoice_template->getDisplayTotal()}<td class="total">{lang}Total{/lang}</td>{/if}
                  </tr>
                </thead>
                <tbody>
                {foreach from=$active_quote->getItems() item=quote_item name=item_foreach}
                  <tr class="{cycle values='odd,even'}">
                    {if $invoice_template->getDisplayItemOrder()}<td class="num">#{$smarty.foreach.item_foreach.iteration}</td>{/if}
                    <td class="description">{$quote_item->getFormattedDescription() nofilter}</td>
                    {if $invoice_template->getDisplayQuantity()}<td class="quantity">{$quote_item->getQuantity()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplayUnitCost()}<td class="unit_cost">{$quote_item->getUnitCost()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplaySubtotal()}<td class="subtotal">{$quote_item->getSubtotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                    {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$quote_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$quote_item->getFirstTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                    {if $active_quote->getSecondTaxIsEnabled()}
                      {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$quote_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$quote_item->getSecondTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                    {/if}
                    {if $invoice_template->getDisplayTotal()}<td class="total">{$quote_item->getTotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                  </tr>
                {/foreach}
                </tbody>
                <tfoot>
                  <tr class="subtotals_row">
                    <td colspan="{$totals_colspan}" class="label">{lang}Subtotal{/lang}</td>
                    <td class="value"><span class="property_wrapper property_quote_subtotal">{$active_quote->getSubTotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                  </tr>

                  {if $invoice_template->getSummarizeTax() || !is_foreachable($active_quote->getTaxGroupedByType())}
                    <tr class="tax_row" style="{if $invoice_template->getHideTaxSubtotal() && $active_quote->getTax() == 0}display: none;{/if}">
                      <td colspan={$totals_colspan} class="label">{lang}Tax{/lang}</td>
                      <td class="value"><span class="property_wrapper property_quote_tax">{$active_quote->getTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                    </tr>
                  {else}
                    {assign var=grouped_taxes value=$active_quote->getTaxGroupedByType()}
                    {foreach from=$grouped_taxes item=grouped_tax}
                    <tr class="tax_row">
                      <td colspan={$totals_colspan} class="label">{$grouped_tax.name}</td>
                      <td class="value"><span class="property_wrapper property_quote_tax">{$grouped_tax.amount|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                    </tr>
                    {/foreach}
                  {/if}

                  <tr class="property_wrapper" style="{if !$active_quote->requireRounding()}display: none{/if}">
                    <td colspan="{$totals_colspan}" class="label">{lang}Rounding Difference{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_quote_rounding_difference">{$active_quote->getRoundingDifference()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                  </tr>
                  <tr class="total">
                    <td colspan="{$totals_colspan}" class="label">{lang}Total{/lang}</td>
                    <td class="value total"><span class="property_wrapper property_quote_total">{$active_quote->getTotal(true)|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                  </tr>
                </tfoot>
              </table>
            {else}
              <p class="empty_page"><span class="inner">{lang}This quote has no items{/lang}</span></p>
            {/if}
          </div>


          <div class="invoice_paper_notes property_wrapper" style="display: {if $active_quote->getNote()}block{else}none{/if}">
            <h3>{lang}Note{/lang}</h3>
            <p><span class="property_quote_note">{$active_quote->getNote()|clean|nl2br nofilter}</span></p>
          </div>

        </div>
      </div>
      <div class="invoice_paper_bottom"></div>

      <div class="invoice_paper_peel_draft"></div>
      <div class="invoice_paper_stamp_paid"></div>
      <div class="invoice_paper_stamp_canceled"></div>
    </div>
  </div>

  <div class="wireframe_content_wrapper">
    {object_comments object=$active_quote user=$logged_user}
  </div>

  <script type="text/javascript">
    var decimal_spaces = {$active_quote->getCurrency()->getDecimalSpaces()|json nofilter};
    var second_tax_enabled = {$active_quote->getSecondTaxIsEnabled()|json nofilter};

    var get_display_item_order = {$invoice_template->getDisplayItemOrder()|json nofilter};
    var get_display_quantity = {$invoice_template->getDisplayQuantity()|json nofilter};
    var get_display_unit_cost = {$invoice_template->getDisplayUnitCost()|json nofilter};
    var get_display_subtotal = {$invoice_template->getDisplaySubtotal()|json nofilter};
    var get_display_tax_rate = {$invoice_template->getDisplayTaxRate()|json nofilter};
    var get_display_tax_amount = {$invoice_template->getDisplayTaxAmount()|json nofilter};
    var get_display_total = {$invoice_template->getDisplayTotal()|json nofilter};
    var summarize_tax = {$invoice_template->getSummarizeTax()|json nofilter};
    var hide_tax_subtotal = {$invoice_template->getHideTaxSubtotal()|json nofilter};

    var client_save_link = $('div.invoice_paper_client').find('span.client_save_data');
    var can_save_client = {if $logged_user->isPeopleManager() && !$active_quote->getCompany() instanceof Company}true{else}false{/if};

    /**
     * Handle what happens when invoices get edited
     *
     */
    App.Wireframe.Events.bind('quote_updated.{$request->getEventScope()}', function(event, quote) {
      var wrapper = $('.quote_' + quote.id).removeClass('quote_draft_wrapper quote_sent_wrapper quote_won_wrapper quote_lost_wrapper');
      var wrapper_paper = wrapper.find('.invoice_paper:first').removeClass('quote_draft quote_sent quote_won quote_lost');

      var quote_client_name;
      if (quote.client['class'] == undefined) {
        quote_client_name = '<b>' + App.clean(quote.client.name) + '</b>';
      } else {
        if (client_save_link && client_save_link.length) {
          client_save_link.hide();
        } // if
        quote_client_name = '<a href="' + quote.client.permalink + '">' + App.clean(quote.client.name) + '</a>';
      } // if
      quote_client_name += '<br/>' + App.nl2br(quote.company_address);

      wrapper.invoiceUpdateProperty(
        [{
          'name' : 'quote_name',
          'value' : App.clean(quote.name),
          'auto_hide' : false
        }, {
          'name' : 'quote_currency',
          'value' : App.clean(quote.currency.code),
          'auto_hide' : false
        }, {
          'name' : 'quote_project',
          'value' : quote.project ? '<a href="' + quote.project.permalink + '">' + App.clean(quote.project.name) + '</a>' : null
        }, {
          'name' : 'quote_sent_on',
          'value' : quote.sent_on ? quote.sent_on.formatted_date : ''
        }, {
          'name' : 'quote_closed_on',
          'value' : quote.closed_on ? quote.closed_on.formatted_date : ''
        }, {
          'name' : 'quote_client_name',
          'value' : quote_client_name
        },{
          'name' : 'quote_client_address',
          'value' : App.lang('Contact Person') + ': <a href="' + quote.recipient.permalink + '">' + quote.recipient.display_name + '</a>'
        },{
          'name' : 'quote_subtotal',
          'value' : App.moneyFormat(quote['subtotal'], quote['currency'], quote['language']),
          'auto_hide' : false
        }, {
          'name' : 'quote_total',
          'value' : App.moneyFormat(quote['total'], quote['currency'], quote['language']),
          'auto_hide' : false
        }, {
          'name' : 'quote_note',
          'value' : App.clean(quote.note).nl2br(),
          'auto_hide' : true
        }, {
          'name' : 'quote_private_note',
          'value' : App.clean(quote.private_note),
          'auto_hide' : true
        }, {
          'name' : 'quote_rounding_difference',
          'value' : App.moneyFormat(quote.rounding_difference, quote.currency, quote.language),
          'auto_hide' : true
        }]
      );

      var items_parent = wrapper.find('.invoice_paper_items table tbody').empty();
      var new_items = '';
      if (quote.items && quote.items.length) {
        var counter = 1;
        $.each(quote.items, function (item_index, item) {
          new_items += '<tr>';

          // item number
          if (get_display_item_order) {
            new_items += '<td class="num">#' + counter + '</td>';
          } // if

          // description
          new_items += '<td class="description">' + item['formatted_description'] + '</td>';

          // quantity
          if (get_display_quantity) {
            new_items += '<td class="quantity">' + App.moneyFormat(item['quantity'], quote['currency'], quote['language']) + '</td>';
          } // if

          // unit cost
          if (get_display_unit_cost) {
            new_items += '<td class="unit_cost">' + App.moneyFormat(item['unit_cost'], quote['currency'], quote['language']) + '</td>';
          } // if

          // item subtotal
          if (get_display_subtotal) {
            new_items += '<td class="subtotal">' + App.moneyFormat(item['subtotal'], quote['currency'], quote['language']) + '</td>';
          } // if

          // item tax
          if (get_display_tax_rate) {
            new_items += '<td class="tax_rate">' + item['first_tax']['verbose_percentage'] + '</td>';
            if (second_tax_enabled) {
              new_items += '<td class="tax_rate">' + item['second_tax']['verbose_percentage'] + '</td>';
            } // if
          } else if (get_display_tax_amount) {
            new_items += '<td class="tax_amount">' + App.moneyFormat(item['first_tax']['value'], quote['currency'], quote['language']) + '</td>';
            if (second_tax_enabled) {
              new_items += '<td class="tax_amount">' + App.moneyFormat(item['second_tax']['value'], quote['currency'], quote['language']) + '</td>';
            } // if
          } // if

          // item total
          if (get_display_total) {
            new_items += '<td class="total">' + App.moneyFormat(item['total'], quote['currency'], quote['language']) + '</td>';
          } // if

          new_items += '</tr>'

          counter++;
        });
        items_parent.html(new_items);
      } // if

      var tax_row = wrapper.find('tr.tax_row');

      if (summarize_tax) {
        wrapper.invoiceUpdateProperty([{
          'name': 'quote_tax',
          'value' : App.moneyFormat(quote.tax, quote.currency, quote.language),
          'auto_hide' : false
        }]);

        if(hide_tax_subtotal && quote.tax == 0) {
          tax_row.hide();
        } else {
          tax_row.show();
        } // if
      } else {
        tax_row.remove();

        var insert_pointer = wrapper.find('tr.subtotals_row');
        var grouped_taxes = quote['tax_grouped_by_type'];
        var col_span = insert_pointer.find('td.label').attr('colspan');
        var inserted = false;

        if (grouped_taxes && !$.isEmptyObject(grouped_taxes)) {
          $.each(grouped_taxes, function (index, grouped_tax) {
            if (grouped_tax['amount']) {
              insert_pointer = $('<tr class="tax_row"><td colspan="' + col_span + '" class="label">' + App.clean(grouped_tax['name']) + '</td><td class="value"><span class="property_wrapper property_quote_tax">' + App.moneyFormat(grouped_tax['amount'], quote.currency, quote.language) + '</span></td></tr>').insertAfter(insert_pointer);
              inserted = true;
            } // if
          });
        } // if

        if (!inserted) {
          insert_pointer = $('<tr class="tax_row"><td colspan="' + col_span + '" class="label">' + App.lang('Tax') + '</td><td class="value"><span class="property_wrapper property_quote_tax">' + App.moneyFormat(0, quote.currency, quote.language) + '</span></td></tr>').insertAfter(insert_pointer);
        } // if
      } // if

      if (quote.status_conditions.is_draft) {
        wrapper.addClass('quote_draft_wrapper');
        wrapper_paper.addClass('quote_draft');
      } else if (quote.status_conditions.is_sent) {
        wrapper.addClass('quote_sent_wrapper');
        wrapper_paper.addClass('quote_sent');
      } else if (quote.status_conditions.is_won) {
        wrapper.addClass('quote_won_wrapper');
        wrapper_paper.addClass('quote_won');
      } else if (quote.status_conditions.is_lost) {
        wrapper.addClass('quote_lost_wrapper');
        wrapper_paper.addClass('quote_lost');
      } // if
    });

    App.Wireframe.Events.bind('create_invoice_from_quote', function (event, invoice) {
      if (invoice['class'] == 'Invoice') {
        App.Wireframe.Flash.success(App.lang('New invoice created.'));
        App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
      } // if
    });

    $('div.invoice_paper_client').each(function() {
      if (can_save_client) {
        var link = $(this).find('span.client_save_data a');
        if (link) {
          link.flyoutForm({
            'title' : App.lang('Add Client to People'),
            'success_event' : 'quote_updated',
            'width' : 550
          });
        }
      } // if
    });

  </script>
{/object}