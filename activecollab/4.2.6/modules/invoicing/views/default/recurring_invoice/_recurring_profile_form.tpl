<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars invoice_form recurring_profile_form">
  <div class="main_form_column">
    <div class="main_invoice_settings">
      <table>
        <tr>
          <td class="left_cell">
            <div class="invoice_client_address labels_inline">
              {wrap field=company_id}
                {select_company name="recurring_profile[company_id]" value=$recurring_profile_data.company_id class=required id="companyId" user=$logged_user can_create_new=false required=true label='Client' exclude_owner_company=true}
              {/wrap}

              {wrap field=company_address class=companyAddressContainer}
                {textarea_field name="recurring_profile[company_address]" id=companyAddress required=true label='Address'}{$recurring_profile_data.company_address nofilter}{/textarea_field}
              {/wrap}

              {wrap field=recipient class=notificationRecipient}
                {select_client_with_permissions name="recurring_profile[recipient_id]" value=$recurring_profile_data.recipient_id permissions='can_manage_client_finances' skip_owners_without_finances=true id=recipient company_select_id='companyId' required=true label='Notify person'}
              {/wrap}
            </div>
          </td>
          <td class="right_cell">
            <div class="invoice_common_settings">
              {wrap field=currencyId class=firstHolder}
                {select_currency name="recurring_profile[currency_id]" value=$recurring_profile_data.currency_id class=short label='Currency' id=currencyId}
              {/wrap}

              {wrap field=language class=secondHolder}
                {select_language name="recurring_profile[language_id]" value=$recurring_profile_data.language_id label='Language' preselect_default=true}
              {/wrap}

              {wrap field=project_id}
                {select_project name="recurring_profile[project_id]" value=$recurring_profile_data.project_id user=$logged_user optional=true label='Project'}
                {checkbox label="Only selected client's projects" name="selected_company_projects" id="selected_company_projects"}
              {/wrap}
            </div>
          </td>
        </tr>
      </table>
    </div>

  {wrap field=items  class="invoice_items_wrapper"}
    <table class="validate_callback validate_invoice_items" cellspacing="0">
      <thead>
        <tr class="header">
          <th class="num header_cell">
            <input type="hidden" name="invoice_sub_total" id="invoice_sub_total" />
            <input type="hidden" name="invoice_total" id="invoice_total" />
            #
         </th>
          <th class="description header_cell" style="{if $active_recurring_profile->getSecondTaxIsEnabled()}width: 283px;{/if}">{lang}Description{/lang}</th>
          <th class="unit_cost header_cell">{lang}Quantity{/lang}</th>
          <th class="quantity header_cell">{lang}Unit Cost{/lang}</th>
          {if $active_recurring_profile->getSecondTaxIsEnabled()}
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
      <tbody>
      
      </tbody>
      <tfoot>
        <tr class="invoice_subtotal">
          <td colspan="{if $active_recurring_profile->getSecondTaxIsEnabled()}6{else}5{/if}" class="header_cell">{lang}Subtotal{/lang}</td>
          <td class="total field_cell"><input class="subtotal" type="text" disabled="disabled" /></td>
          <td></td>
        </tr>
        <tr class="invoice_total">
          <td colspan="{if $active_recurring_profile->getSecondTaxIsEnabled()}6{else}5{/if}" class="header_cell">{lang}Total{/lang}</td>
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

      {if $active_recurring_profile->getSecondTaxIsEnabled()}
        {wrap field="second_tax_is_compound"}
          {select_invoice_second_tax_mode name='recurring_profile[second_tax_is_compound]' value=$recurring_profile_data.second_tax_is_compound id=second_tax_is_compound_toggler}
        {/wrap}
      {/if}
    </div>
  {/wrap}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    {wrap field=profile_name}
      {text_field name="recurring_profile[name]" value=$recurring_profile_data.name id=profile_name required=true label='Profile Name'}
    {/wrap}

    {if $active_recurring_profile->isStarted() && !$active_recurring_profile->isNew() && !$is_duplicat}
      {wrap field=start_on class=firstHolder}
        {label for="start_on"}Started On{/label}
        <p>{$recurring_profile_data.start_on|date}</p>
      {/wrap}
      {wrap field=frequency class=secondHolder}
        {label for="frequency"}Frequency{/label}
        <p>{$recurring_profile_data.frequency|ucwords}</p>
      {/wrap}
    {else}
      {wrap field=start_on class=firstHolder}
        {select_date  name="recurring_profile[start_on]" skip_days_off=false value=$recurring_profile_data.start_on required=true label='Start On' start_date=$today}
      {/wrap}

      {wrap field=frequency class=secondHolder}
        {select_recurring_profile_frequently name="recurring_profile[frequency]" value=$recurring_profile_data.frequency required=true label='Frequency'}
      {/wrap}
    {/if}

    {wrap field=recurring_occurancies class=firstHolder}
      {text_field name="recurring_profile[occurrences]" value=$recurring_profile_data.occurrences class='occurrence' required=true label='Occurrence'}
    {/wrap}

    {wrap field=recurring_approve class=secondHolder}
      {yes_no name='recurring_profile[auto_issue]' value=$recurring_profile_data.auto_issue mode='select' label='Auto Issue' id="auto_issue_select"}
    {/wrap}
    
    <div id="invoice_due_on_holder">
       {wrap field=invoice_due_after}
        {select_invoice_due_on name="recurring_profile[invoice_due_after]" value=$recurring_profile_data.invoice_due_after label='Due After Issue' mode='select'}
       {/wrap}
    </div>

      {if $allow_payment}
        {wrap field=allow_partial}
          {select_payments_type name="recurring_profile[allow_payments]" value=$recurring_profile_data.allow_payments label='Payments' required=true}
         {/wrap}
      {/if}

    <div class="invoice_po_wrapper">
      {wrap field=po_number}
      {text_field name="recurring_profile[purchase_order_number]" value=$recurring_profile_data.purchase_order_number label='PO Number'}
      {/wrap}
    </div>

    <div class="invoice_comment_wrapper">
      {wrap field=comment}
        {invoice_comment name="recurring_profile[private_note]" label='Our Private Comment'}{$recurring_profile_data.private_note nofilter}{/invoice_comment}
        <p class="aid">{lang}This comment is never displayed to the client or included in the final invoice{/lang}</p>
      {/wrap}
    </div>


  </div>
  
  <div class="invoice_details_wrapper"> 
    <div class="invoice_note_wrapper">
    	{wrap field=note}
    	  {invoice_note name='recurring_profile[note]' label='Public Invoice Note' select_default=$active_recurring_profile->isNew()}{$recurring_profile_data.note nofilter}{/invoice_note}
      	<p class="aid">{lang}Invoice note is included in the final invoice and visible to the client{/lang}</p>
      {/wrap}
    </div>
  </div>
</div>

<script type="text/javascript">
	var wrapper = $('div.recurring_profile_form');
	var invoice_due_on_holder = wrapper.find('div#invoice_due_on_holder');
	var auto_issue_select = wrapper.find('select#auto_issue_select');

	auto_issue_select.change(function(){
		check_auto_issue();
	});
	
	function check_auto_issue() {
  	if(auto_issue_select.val() == '0') {
  		invoice_due_on_holder.hide();
  	} else {
  		invoice_due_on_holder.show();
  	}//if
	}//check_auto_issue

	check_auto_issue();
	
</script>
