<script type="text/javascript"> 
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars invoice_form quote_form">
  <div class="main_form_column">
    <div class="main_invoice_settings">
      <table>
        <tr>
          <td class="left_cell">
            <div class="invoice_client_address labels_inline">
              {wrap field=choose_client class="client_chooser"}
                {radio_field name=client_type class='existing_client' value='existing_client' label='Existing Client'}
                {radio_field name=client_type class='new_client' value='new_client' label='New Client'}
              {/wrap}
              <span class="quote_new_client">
                {wrap field=new_company_name}
                  {text_field id='new_company_name' name="new_client[company_name]" value=$new_client.company_name label='Company Name'}
                {/wrap}
                {wrap field=new_company_address class=companyAddressContainer}
                  {textarea_field name="new_client[company_address]" id='new_company_address' class='long' label='Address'}{$new_client.company_address nofilter}{/textarea_field}
                {/wrap}
                {wrap field=new_recipient_name}
                  {text_field id='new_recipient_name' name="new_client[recipient_name]" value=$new_client.recipient_name label="Contact Person"}
                {/wrap}
                {wrap field=new_recipient_email}
                  {text_field id='new_recipient_email' name="new_client[recipient_email]" value=$new_client.recipient_email label="Contact's E-mail"}
                {/wrap}
              </span>
              <span class="quote_existing_client">
                {wrap field=company_id}
                  {select_company name="client[company_id]" can_create_new=false value=$quote_data.company_id class=required id="companyId" user=$logged_user label='Company' exclude_owner_company=true}
                {/wrap}

                {wrap field=company_address class=companyAddressContainer}
                  {textarea_field name="client[company_address]" id=companyAddress class='required long' label='Address'}{$quote_data.company_address nofilter}{/textarea_field}
                {/wrap}

                {wrap field=recipient class=notificationRecipient}
                  {select_client_with_permissions name="client[recipient_id]" value=$quote_data.recipient_id permissions='can_manage_client_finances' id=recipient company_select_id='companyId' label='Contact person'}
                {/wrap}
              </span>
            </div>
          </td>
          <td class="right_cell">
            <div class="invoice_common_settings">
              {wrap field=currency_id}
                {select_currency name="quote[currency_id]" value=$quote_data.currency_id label='Currency'}
              {/wrap}

              {wrap field=language}
                {select_language name="quote[language_id]" value=$quote_data.language_id label='Language' preselect_default=true}
              {/wrap}
            </div>
          </td>
        </tr>
      </table>
    </div>

    {wrap field=items class="invoice_items_wrapper"}
      <table class="validate_callback validate_invoice_items" cellspacing="0">
      <thead>
        <tr class="header">
          <th class="num header_cell">
            <input type="hidden" name="invoice_sub_total" id="invoice_sub_total" />
            <input type="hidden" name="invoice_total" id="invoice_total" />
          </th>
          <th class="description header_cell" style="{if $active_quote->getSecondTaxIsEnabled()}width: 283px;{/if}">{lang}Description{/lang}</th>
          <th class="quantity header_cell">{lang}Quantity{/lang}</th>
          <th class="unit_cost header_cell">{lang}Unit Cost{/lang}</th>
          {if $active_quote->getSecondTaxIsEnabled()}
            <th class="tax_rate header_cell">{lang}Tax #1{/lang}</th>
            <th class="tax_rate header_cell">{lang}Tax #2{/lang}</th>
          {else}
            <th class="tax_rate header_cell">{lang}Tax{/lang}</th>
          {/if}
          <th class="subtotal header_cell" style="display: none">{lang}Subtotal{/lang}</th>
          <th class="total header_cell">{lang}Total{/lang}</th>
          <th class="options header_cell"></th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr class="invoice_subtotal">
          <td colspan="{if $active_quote->getSecondTaxIsEnabled()}6{else}5{/if}" class="header_cell">{lang}Subtotal{/lang}</td>
          <td class="total field_cell"><input class="subtotal" type="text" disabled="disabled" /></td>
          <td></td>
        </tr>
        <tr class="invoice_total">
          <td colspan="{if $active_quote->getSecondTaxIsEnabled()}6{else}5{/if}" class="header_cell">{lang}Total{/lang}</td>
          <td class="total field_cell"><input class="total" type="text" disabled="disabled"/></td>
          <td></td>
        </tr>
      </tfoot>
    </table>

    <div class="invoice_item_buttons">
      {link_button label="Add New Item" icon_class=button_add id="add_new"}
      {if is_foreachable($invoice_item_templates)}
        {link_button_dropdown label="Add From Template" icon_class=button_duplicate id="add_from_template"}
          <ul>
            {foreach from=$invoice_item_templates item=invoice_item_template}
              <li><a href="{$invoice_item_template->getId()}">{$invoice_item_template->getDescription()}</a></li>
            {/foreach}
          </ul>
        {/link_button_dropdown}
      {/if}

      {if $active_quote->getSecondTaxIsEnabled()}
        {wrap field="second_tax_is_compound"}
          {select_invoice_second_tax_mode name='quote[second_tax_is_compound]' value=$quote_data.second_tax_is_compound id=second_tax_is_compound_toggler}
        {/wrap}
      {/if}
    </div>
    {/wrap}  
  </div>
  
  <div class="form_sidebar form_second_sidebar">
		{wrap field=name}
	    {text_field name="quote[name]" required=true value=$quote_data.name label='Summary' maxlength="255"}
	  {/wrap}


    <div class="invoice_comment_wrapper">
      {wrap field=private_note}
        {invoice_comment name='quote[private_note]' label='Our Private Comment'}{$quote_data.private_note nofilter}{/invoice_comment}
        <p class="aid">{lang}This comment will never be displayed to the client{/lang}</p>
      {/wrap}
    </div>
	
	  <input type="hidden" name="quote[project_request_id]" value="{$quote_data.project_request_id}" />         
  </div>

  <div class="invoice_details_wrapper">
    <div class="invoice_note_wrapper">
		  {wrap field=note}
		    {invoice_note name='quote[note]' label='Add Note to this Quote' select_default=$active_quote->isNew()}{$quote_data.note nofilter}{/invoice_note}
        <p class="aid">{lang}This note will be visible to the client{/lang}</p>
		  {/wrap}
    </div> 
  </div>
  
</div>
<script type="text/javascript">
  $('div.invoice_client_address').each(function() {
    var wrapper = $(this);
    var quote_data = {$quote_data|json nofilter};
    var new_client = {$new_client|json nofilter};
    var wrapper_existing_client = wrapper.find('span.quote_existing_client');
    var wrapper_new_client = wrapper.find('span.quote_new_client');

    var set_required_fields = function(selected_wrapper) {
      var client_fields = $('span.quote_' + selected_wrapper).find('input, select, textarea');
      if (client_fields.length) {
        client_fields.each(function() {
          $(this).attr('required', 'true').addClass('required');
          
          var client_field_label = $(this).parent().find('label');
          if (typeof(client_field_label) == 'object' && client_field_label.html().indexOf(' *') == -1) {
            client_field_label.html(client_field_label.html() + ' *');
          } // if
        });
      } // if

      var other_wrapper = selected_wrapper == 'new_client' ? 'existing_client' : 'new_client';
      unrequire_fields = $('span.quote_' + other_wrapper).find('input, select, textarea');
      if (unrequire_fields.length) {
        unrequire_fields.each(function() {
          $(this).removeClass('required').removeAttr('required');
        });
      } // if
    };

    var toggle_wrappers = function(selected_wrapper) {
      if (selected_wrapper == 'new_client') {
        wrapper_existing_client.hide();
        wrapper_new_client.show();
      } else {
        wrapper_existing_client.show();
        wrapper_new_client.hide();
      } // if

      set_required_fields(selected_wrapper);
    };

    var default_wrapper = new_client !== null ? 'new_client' : 'existing_client';
    toggle_wrappers(default_wrapper);
    wrapper.find('input.'+default_wrapper).attr('checked', true);

    $('input[name="client_type"]').click(function() {
      toggle_wrappers($(this).val());
    });
  });
</script>