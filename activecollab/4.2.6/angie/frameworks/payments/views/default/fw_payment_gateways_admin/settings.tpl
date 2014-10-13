{title}Payments Settings{/title}
{add_bread_crumb}Payments Settings{/add_bread_crumb}

<div id="payments_settings">
  {form action=Router::assemble('payment_gateways_settings') method=post id="payments_settings_admin"}
    <div class="content_stack_wrapper">
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Payments Settings{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=paymentsSettingsDoNotAllow}
            {radio_field name="payments_config[allow_payments]" value=Payment::DO_NOT_ALLOW label="Don't allow payments" pre_selected_value=$allow_payments class="allow_payments_radio"}
          {/wrap}
          {wrap field=paymentsSettingsAllow}
            {radio_field name="payments_config[allow_payments]" value=Payment::ALLOW_FULL label="Allow only full payments" pre_selected_value=$allow_payments class="allow_payments_radio"}
          {/wrap}
          {wrap field=paymentsSettingsAllowFull}
            {radio_field name="payments_config[allow_payments]" value=Payment::ALLOW_PARTIAL label="Allow full and partial payments" pre_selected_value=$allow_payments class="allow_payments_radio"}
          {/wrap}
          
          
          {wrap field=invoicePaymentsSettings}
            {label}Default Invoice Payments Settings{/label}
          	<select name="payments_config[allow_payments_for_invoice]" class="default_invoice_payments_select">
          		
          	</select>
          {/wrap}
          
          {wrap field=enforceSettings}
            {checkbox_field name="payments_config[enforce]" label="Enforce these settings to all existing invoices" value="1"}
          {/wrap}
            
        </div>
      </div>
   
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	var do_not_allow = {Payment::DO_NOT_ALLOW|json nofilter};
	var allow_full = {Payment::ALLOW_FULL|json nofilter};
	var allow_partial = {Payment::ALLOW_PARTIAL|json nofilter};

	var checked_value = {$allow_payments_for_invoice|json nofilter};
	
	var allow_payments_radio = $("#payments_settings .allow_payments_radio");

	allow_payments_radio.click(function() {
		var value = $(this).val();
		populate_allow_payment_for_invoice(value);
	});

	var option_do_not_allow = new Option(App.lang('Do not allow payments'), do_not_allow);
	if(checked_value == do_not_allow) {
		$(option_do_not_allow).attr('selected','selected');
	}
	var option_allow_full = new Option(App.lang('Allow only full payments'), allow_full);
	if(checked_value == allow_full) {
		$(option_allow_full).attr('selected','selected');
	}
	var option_allow_partial = new Option(App.lang('Allow full and partial payments'), allow_partial);
	if(checked_value == allow_partial) {
		$(option_allow_partial).attr('selected','selected');
	}
	
	function populate_allow_payment_for_invoice(allow_payments_value) {
		
		var default_invoice_payments_select = $("#payments_settings .default_invoice_payments_select");
		default_invoice_payments_select.empty();
		
		default_invoice_payments_select.append(option_do_not_allow);
		//allow full only
		if(allow_payments_value > do_not_allow) {
			default_invoice_payments_select.append(option_allow_full);
		}//if
		//and partial
		if(allow_payments_value > allow_full) {
			default_invoice_payments_select.append(option_allow_partial);
		}//if
	}//render_allow_payment_for_invoice

	//set initial options
	var predefined_allow_payments = $("#payments_settings .allow_payments_radio:checked").val();
	populate_allow_payment_for_invoice(predefined_allow_payments);
	
</script>