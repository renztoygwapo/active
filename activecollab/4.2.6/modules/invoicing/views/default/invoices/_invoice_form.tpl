<script type="text/javascript"> 
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars invoice_form">
  <div class="main_form_column">

      <div class="main_invoice_settings">
        <table>
          <tr>
            <td class="left_cell">
              <div class="invoice_client_address labels_inline">
                {wrap field=company_id}
                  {select_company name="invoice[company_id]" value=$invoice_data.company_id class=required id="companyId" user=$logged_user can_create_new=true required=true label='Client' success_event=company_created exclude_owner_company=true}
                {/wrap}

                {wrap field=company_address class=companyAddressContainer}
                  {textarea_field name="invoice[company_address]" id=companyAddress class='required long' label='Address' required=true}{$invoice_data.company_address nofilter}{/textarea_field}
                {/wrap}
              </div>
            </td>
            <td class="right_cell">
              <div class="invoice_common_settings">
                {wrap field=currencyId class=firstHolder}
                  {select_currency name="invoice[currency_id]" value=$invoice_data.currency_id class=short id=currencyId label='Currency'}
                {/wrap}

                {wrap field=language class=secondHolder}
                  {select_language name="invoice[language_id]" value=$invoice_data.language_id label='Language' preselect_default=true}
                {/wrap}

                {wrap field=project_id}
                  {if $active_invoice->isLoaded() && $active_invoice->getBasedOn() instanceof Project}
                    {label}Project{/label}
                    <p>{$active_invoice->getBasedOn()->getName()}</p>
                    <input type="hidden" name="invoice[project_id]" value="{$active_invoice->getBasedOn()->getId()}">
                  {else}
                    {select_project name="invoice[project_id]" value=$invoice_data.project_id user=$logged_user show_all=true optional=true label='Project'}
                    {checkbox label="Only client's projects" name="selected_company_projects" id="selected_company_projects"}
                  {/if}
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
	            <th class="num">
	              <input type="hidden" name="invoice_sub_total" id="invoice_sub_total" />
	              <input type="hidden" name="invoice_total" id="invoice_total" />
	            </th>
	            <th class="description header_cell" style="{if $active_invoice->getSecondTaxIsEnabled()}width: 283px;{/if}">{lang}Description{/lang}</th>
	            <th class="quantity header_cell">{lang}Quantity{/lang}</th>
	            <th class="unit_cost header_cell">{lang}Unit Cost{/lang}</th>
              {if $active_invoice->getSecondTaxIsEnabled()}
                <th class="tax_rate header_cell">{lang}Tax #1{/lang}</th>
                <th class="tax_rate header_cell">{lang}Tax #2{/lang}</th>
              {else}
                <th class="tax_rate header_cell">{lang}Tax{/lang}</th>
              {/if}
	            <th class="subtotal header_cell" style="display: none">{lang}Subtotal{/lang}</th>
	            <th class="total header_cell">{lang}Total{/lang}</th>
	            <th class="options"></th>
	          </tr>
          </thead>
          <tbody>
          </tbody>
	        <tfoot>
	          <tr class="invoice_subtotal">
              <td colspan="{if $active_invoice->getSecondTaxIsEnabled()}5{else}4{/if}"></td>
	            <td class="header_cell">{lang}Subtotal{/lang}</td>
	            <td class="total field_cell"><input class="subtotal" type="text" disabled="disabled" /></td>
	            <td class="end_cell"></td>
	          </tr>
	          <tr class="invoice_total">
              <td colspan="{if $active_invoice->getSecondTaxIsEnabled()}5{else}4{/if}"></td>
	            <td class="header_cell">{lang}Total{/lang}</td>
	            <td class="total field_cell"><input class="total" type="text" disabled="disabled"/></td>
	            <td class="end_cell"></td>
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

          {if $active_invoice->getSecondTaxIsEnabled()}
            {wrap field="second_tax_is_compound"}
              {select_invoice_second_tax_mode name='invoice[second_tax_is_compound]' value=$invoice_data.second_tax_is_compound id=second_tax_is_compound_toggler}
            {/wrap}
          {/if}
        </div>
      {/wrap}
    
    {if $active_invoice->isNew() && is_foreachable($invoice_data.time_record_ids)}
      {foreach from=$invoice_data.time_record_ids item=time_record_id}
      <input type="hidden" name="invoice[time_record_ids][]" value="{$time_record_id}" />
      {/foreach}
    {/if}    
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    {wrap field=number id=invoiceNumberGenerator}
      {label for=invoiceNumber}Invoice ID{/label}
      {if $active_invoice->getStatus() == $smarty.const.INVOICE_STATUS_ISSUED}
        {text_field name="invoice[number]" value=$invoice_data.number class='' id=invoiceNumber disabled=disabled}
      {else}
        <div id="autogenerateID" style="{if $invoice_data.number}display:none{/if}">
          <div class="field_wrapper">{lang}Auto-generate on issue{/lang}<a href="#">({lang}Specify{/lang})</a></div>
        </div>
        <div id="manuallyID" style="{if !$invoice_data.number}display:none{/if}">
          <div class="field_wrapper">{text_field name="invoice[number]" value=$invoice_data.number class='' id=invoiceNumber}<a href="#">({lang}Generate{/lang})</a></div>
        </div>        
      {/if}
    {/wrap}
    
    {if $active_invoice->getStatus() == $smarty.const.INVOICE_STATUS_ISSUED}
      {wrap field=issued_on class=firstHolder}
        {select_date name="invoice[issued_on]" value=$invoice_data.issued_on label="Issued On" required=true}
      {/wrap}

      {wrap field=issued_on class=secondHolder}
        {select_date name="invoice[due_on]" value=$invoice_data.due_on label='Payment Due On' required=true}
      {/wrap}
    {/if}

    {wrap field=purchase_order_number}
      {label}Purchase Order Number{/label}
      {text_field name="invoice[purchase_order_number]" value=$invoice_data.purchase_order_number id="purchase_order_number"}
    {/wrap}

    {if $allow_payment}
      <div class="invoice_client_address">
        {wrap field=allow_partial}
          {select_payments_type name="invoice[allow_payments]" selected=$invoice_data.payment_type label='Payments' required=true}
        {/wrap}
      </div>
    {/if}

    <div class="invoice_comment_wrapper">
      {wrap field=comment}
        {invoice_comment name="invoice[private_note]" label='Our Private Comment'}{$invoice_data.private_note nofilter}{/invoice_comment}
        <p class="aid">{lang}This comment is never displayed to the client or included in the final invoice{/lang}</p>
      {/wrap}
    </div>
    
  </div>
  
  <div class="invoice_details_wrapper"> 
    <div class="invoice_note_wrapper">
    	{wrap field=note}
      	{invoice_note name='invoice[note]' original_note=$original_note label='Public Invoice Note' select_default=$active_invoice->isNew()}{$invoice_data.note nofilter}{/invoice_note}
      	<p class="aid">{lang}Invoice note is included in the final invoice and visible to the client{/lang}</p>
     	{/wrap}
    </div>
  </div>  
</div>

<input type="hidden" name="invoice[quote_id]" value="{$invoice_data.quote_id}" />