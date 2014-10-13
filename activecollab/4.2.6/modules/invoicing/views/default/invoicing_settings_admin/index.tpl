{title}Settings{/title}

{use_widget name=invoicing_settings_dialog module=$smarty.const.INVOICING_MODULE}

<div id="invoicing_settings">
  {form action=Router::assemble('invoicing_settings')}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Show Invoice As{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=print_invoices_as}
            {text_field name='settings[print_invoices_as]' value=$settings_data.print_invoices_as label="Issued Invoices"}
          {/wrap}

          {wrap field=print_proforma_invoices_as}
            {text_field name='settings[print_proforma_invoices_as]' value=$settings_data.print_proforma_invoices_as label="Draft (Proforma) Invoices"}
            <p class="aid">{lang}Specify how invoices will be called in generated PDF files ("Tax Invoice" for example). To use default value, <u>leave these fields blank</u> (activeCollab will use "Invoice" and "Proforma Invoice", optionally translated using localization feature){/lang}</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Invoice from Tracked Records{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=when_invoice_is_based_on}
            {when_invoice_is_based_on name="settings[on_invoice_based_on]" value=$settings_data.on_invoice_based_on label='By Default, Group Records By' mode='select' required=true}
          {/wrap}

          {wrap field=description_formats id=invoicing_settings_description_formats}
            {label}Description Formats{/label}

            <table class="common" cellspacing="0">
              {foreach $description_formats as $description_format_config_option => $description_format}
                <tr data-format-config-option="{$description_format_config_option}">
                  <td>{$description_format.text}</td>
                  <td class="format">{$description_format.format}</td>
                </tr>
              {/foreach}
            </table>

            {button href=Router::assemble('invoicing_settings_change_description_formats') mode=flyout_form flyout_title='Change Formats' flyout_width=700 success_event='description_formats_updated' success_message='Settings updated'}Change Formats{/button}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Taxes{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=invoice_tax_options}
            <div>{checkbox_field name="settings[invoice_second_tax_is_enabled]" value=1 checked=$settings_data.invoice_second_tax_is_enabled label="Enable Second Tax for Invoices"}</div>
            <div>{checkbox_field name="settings[invoice_second_tax_is_compound]" value=1 checked=$settings_data.invoice_second_tax_is_compound label="Second Tax is Compound Tax"}</div>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Notifications{/lang}</h3>
        </div>
        <div class="content_stack_element_body">

          {wrap field=invoice_notify_on_payment}
            <label class="main_label">Please select which financial managers will be notified when new payment is received:</label>
            <div>
              {radio_field name="settings[invoice_notify_financial_managers]" class="notify_managers_radio" pre_selected_value=$settings_data.invoice_notify_financial_managers value=Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_NONE label="Don't Notify Financial Managers"}
            </div>
            <div>
              {radio_field name="settings[invoice_notify_financial_managers]" class="notify_managers_radio" pre_selected_value=$settings_data.invoice_notify_financial_managers value=Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_ALL label='Notify All Financial Managers'}
            </div>
            <div>
              {radio_field name="settings[invoice_notify_financial_managers]" class="notify_managers_radio" pre_selected_value=$settings_data.invoice_notify_financial_managers value=Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_SELECTED label='Notify Selected Financial Managers'}
              {list_financial_managers id="list_financial_managers" name="settings[invoice_notify_financial_manager_ids]" value=$settings_data.invoice_notify_financial_manager_ids label="Financial Managers"}
            </div>
          {/wrap}

          {wrap field=invoice_payment_notification_option}
            <div>
              {checkbox_field name="settings[invoice_notify_on_payment]" value=1 checked=$settings_data.invoice_notify_on_payment label="Notify Client when Invoice is Fully Paid"}
            </div>
            <div>
              {checkbox_field name="settings[invoice_notify_on_cancel]" value=1 checked=$settings_data.invoice_notify_on_cancel label="Notify Client when Invoice is Canceled"}
            </div>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Due After Issue{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=invoicing_default_due}
            {select_invoice_due_on name="settings[invoicing_default_due]" value=$settings_data.invoicing_default_due label='Default Due Date' allow_selected=false required=true}
            <p class="aid">{lang}Users are able to change due date when they issue an invoice. This is just the default, pre-selected value{/lang}</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element" >
        <div class="content_stack_element_info">
          <h3>{lang}Number Generator{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=invoice_generator_pattern_input}
            {text_field name="settings[invoicing_number_pattern]" value=$settings_data.invoicing_number_pattern class="invoice_generator_pattern_input" label="Generator Pattern"} <span class="invoice_generator_pattern_preview"></span>
          {/wrap}

          <div class="generator_patterns_and_counters">
            <ul class="invoice_generator_variables">
              <li>
                <strong>Counters</strong>
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_NUMBER_COUNTER_TOTAL}</a>

                <span class="change_counter_wrapper" counter_type="total_counter">
                  &mdash; {lang}Next Value{/lang}: <strong>{$counters.total_counter}</strong> {link href=$change_counter_value_url title="Click to Change"}<img src="{image_url name='icons/12x12/edit.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="{lang}Change{/lang}">{/link}</span>
                </span>

                <br>

                {lang}Invoice number in total{/lang}
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_NUMBER_COUNTER_YEAR}</a>

                <span class="change_counter_wrapper" counter_type="year_counter">
                  &mdash; {lang}Next Value{/lang}: <strong>{$counters.year_counter}</strong> {link href=$change_counter_value_url title="Click to Change"}<img src="{image_url name='icons/12x12/edit.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="{lang}Change{/lang}">{/link}</span>
                </span>

                <br>

                {lang}Invoice number in current year{/lang}
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_NUMBER_COUNTER_MONTH}</a>

                <span class="change_counter_wrapper" counter_type="month_counter">
                  &mdash; {lang}Next Value{/lang}: <strong>{$counters.month_counter}</strong> {link href=$change_counter_value_url title="Click to Change"}<img src="{image_url name='icons/12x12/edit.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="{lang}Change{/lang}">{/link}</span>
                </span>

                <br>

                {lang}Invoice number in current month{/lang}
              </li>
            </ul>
            <ul class="invoice_generator_variables">
              <li>
                <strong>Variables</strong>
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_VARIABLE_CURRENT_YEAR}</a><br>
                {lang}Current year in number format{/lang}
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_VARIABLE_CURRENT_MONTH}</a><br>
                {lang}Current month in number format{/lang}
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_VARIABLE_CURRENT_MONTH_SHORT}</a><br>
                {lang}Current month in short text format{/lang}
              </li>
              <li>
                <a href="#" class="number_pattern_variable">{$smarty.const.INVOICE_VARIABLE_CURRENT_MONTH_LONG}</a><br>
                {lang}Current month in long text format{/lang}
              </li>
            </ul>
          </div>

          {wrap field=invoicing_number_counter_padding}
            {select_counter_padding name="settings[invoicing_number_counter_padding]" value=$settings_data.invoicing_number_counter_padding class=invoice_generator_padding_select label="Fix Counter Length"}
            <p class="aid">{lang}When counter value length is fixed, system will prefix current counter value with appropriate number of zeros{/lang}</p>
          {/wrap}
        </div>
      </div>
    </div>
  
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#invoicing_settings').each(function() {
    var wrapper = $(this);

    App.Wireframe.Events.bind('description_formats_updated.' + wrapper.parents('.flyout_dialog:first').attr('id'), function (event, response) {
      if(App.each(response, function(k, v) {
        var row = wrapper.find('#invoicing_settings_description_formats tr[data-format-config-option=' + k + ']');

        if(row.length) {
          var format = row.find('td.format');

          if(format.text() != v) {
            row.find('td.format').text(v);
            row.find('td').highlightFade();
          } // if
        } // if
      }));
    });

    var pattern_input = wrapper.find('input.invoice_generator_pattern_input');
    var pattern_preview = wrapper.find('span.invoice_generator_pattern_preview');
    var padding_select = wrapper.find('select.invoice_generator_padding_select');
    var pattern_variables = {$pattern_variables|json nofilter};

    var notify_managers_radio = wrapper.find('input.notify_managers_radio');
    var list_financial_managers = wrapper.find('div#list_financial_managers');
		var  notify_managers_radio_checked = wrapper.find("input.notify_managers_radio:checked");

    // Hide financial manager list if "all" is selected
		if(wrapper.find("input.notify_managers_radio:checked").val() != "{Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_SELECTED}") {
			list_financial_managers.hide();
		} // if
    
    notify_managers_radio.change(function(){
			var obj = $(this);
			if(obj.val() === "{Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_SELECTED}") {
				list_financial_managers.show('slow');
			} else {
				list_financial_managers.hide('slow');
			} // if
    });
    
    /**
     * Prepare pattern preview value
     *
     * @param String pattern
     */
    var prepare_pattern_preview = function(pattern) {
      var pattern = pattern_input.val();
      var padding = padding_select.val() == '' ? 0 : parseInt(padding_select.val());
      
      for(var key in pattern_variables) {
        var regexp = new RegExp(key, "g");
        var replace_with = pattern_variables[key];

        if(padding && (key == ':invoice_in_total' || key == ':invoice_in_year' || key == ':invoice_in_month')) {
          replace_with = App.strPad(replace_with, padding, '0', 'STR_PAD_LEFT');
        } // if

        pattern = pattern.replace(regexp, replace_with);
      } // for
      
      pattern_preview.text(App.lang('Preview: :generated_preview', { 'generated_preview' : pattern }));
    }; // prepare_pattern_preview

    // Bind
    pattern_input.change(prepare_pattern_preview).keyup(prepare_pattern_preview);
    padding_select.change(prepare_pattern_preview);

    // Initial value
    pattern_input.change();

    wrapper.find('a.number_pattern_variable').click(function() {
      pattern_input.insertAtCursor($(this).text()).change();
      
      return false;
    });

    wrapper.find('span.change_counter_wrapper').each(function() {
      var change_counter_wrapper = $(this);

      change_counter_wrapper.find('a').click(function() {
        var link = $(this);

        if(link[0].block_async_clicks) {
          return false;
        } // if

        var counter_value_wrapper = change_counter_wrapper.find('strong');
        
        var counter_value = parseInt(counter_value_wrapper.text());
        var counter_type = change_counter_wrapper.attr('counter_type');
        
        switch(counter_type) {
        	case 'total_counter':
          	var input = prompt(App.lang('Change total invoices counter value'), counter_value); break;
        	case 'year_counter':
        	  var input = prompt(App.lang('Change counter value for the current year'), counter_value); break;
        	case 'month_counter':
        	  var input = prompt(App.lang('Change counter value for the current month'), counter_value); break;
         	default:
           	App.Wireframe.Flash.error('Unknown counter');
         		return false;
        } // switch

        if(input === null || input === '') {
          return false;
        } else {
          var img = link.find('img');

          var old_image_url = img.attr('src');
          img.attr('src', App.Wireframe.Utils.indicatorUrl('small'));

          link[0].block_async_clicks = true;
          
          $.ajax({
            'url' : link.attr('href'),
            'type' : 'post', 
            'data' : {
              'counter_type' : counter_type, 
              'counter_value' : input, 
              'submitted' : 'submitted'
            }, 
            'success' : function(response) {
              img.attr('src', old_image_url);
              link[0].block_async_clicks = false;

              switch(counter_type) {
              	case 'total_counter':
              	  pattern_variables[':invoice_in_total'] = response; break;
              	case 'year_counter':
              	  pattern_variables[':invoice_in_year'] = response; break;
              	case 'month_counter':
              	  pattern_variables[':invoice_in_month'] = response; break;
              } // switch

              counter_value_wrapper.text(response); // Update displayed value
              pattern_input.change(); // Update preview
            }, 
            'error' : function() {
              img.attr('src', old_image_url);
              link[0].block_async_clicks = false;
              
              App.Wireframe.Flash.error('Failed to update counter value. Please try again later');
            }
          });
        } // if

        return false;
      });
    });
  });
</script>