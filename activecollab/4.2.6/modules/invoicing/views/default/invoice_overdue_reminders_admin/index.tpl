{title}Settings{/title}

<div id="invoice_overdue_reminders" class="content_stack_wrapper">
  {form action=Router::assemble('admin_invoicing_invoice_overdue_reminders')}
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Settings{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field=enable}
          {label for=enableInvoiceOverdueReminders}Enable Invoice Overdue Reminders{/label}
          {yes_no name='reminders[invoice_overdue_reminders_enabled]' value=$reminders_data.invoice_overdue_reminders_enabled id=enableInvoiceOverdueReminders}
        {/wrap}
      </div>
    </div>

    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}When To Send{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field=when_to_send}
          {lang}Send first reminder after{/lang} {select_when_to_send_overdue_reminder name='reminders[invoice_overdue_reminders_send_first]' value=$reminders_data.invoice_overdue_reminders_send_first class="first" optional=false} {lang}and then send reminders every{/lang} {select_when_to_send_overdue_reminder name='reminders[invoice_overdue_reminders_send_every]' value=$reminders_data.invoice_overdue_reminders_send_every class="every" optional=false}
        {/wrap}
      </div>
    </div>

    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}First Reminder Message{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field=first_message}
          {textarea_field name='reminders[invoice_overdue_reminders_first_message]'}{$reminders_data.invoice_overdue_reminders_first_message nofilter}{/textarea_field}
        {/wrap}
      </div>
    </div>

    <div class="content_stack_element default_or_specified_behavior">
      <div class="content_stack_element_info">
        <div class="content_stack_optional">{checkbox name="reminders[invoice_overdue_reminders_escalation_enabled]" class="turn_on" for_id="subject" label="Enabled" value=1 checked=$reminders_data.invoice_overdue_reminders_escalation_enabled}</div>
        <h3>{lang}Escalation Messages{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        <div class="default_behavior">
          <p>{lang}Enable this feature to send different messages depending on the number of days that the customer is late with the payment. This feature is helpful when you need to change the tone of the message as time goes by{/lang}.</p>
        </div>

        <div class="specified_behavior">
          <table class="escalation_messages" cellspacing="0">
            {if is_foreachable($reminders_data.invoice_overdue_reminders_escalation_messages)}
              {foreach $reminders_data.invoice_overdue_reminders_escalation_messages as $k => $escalation_message}
                <tbody class="escalation_message_wrapper" row_number="{$k}">
                <tr>
                  <td class="escalation_overdue">
                    {wrap field=send_escalated}
                      {select_when_to_send_overdue_reminder name="reminders[invoice_overdue_reminders_escalation_messages][$k][send_escalated]" value=$escalation_message.send_escalated optional=false} {lang}overdue{/lang}
                    {/wrap}
                  </td>
                </tr>
                <tr>
                  <td class="escalation_message">
                    {wrap field=escalated_message}
                      {textarea_field name="reminders[invoice_overdue_reminders_escalation_messages][$k][escalated_message]" id=escalatedMessages}{$escalation_message.escalated_message}{/textarea_field}
                    {/wrap}
                  </td>
                  <td class="options"><a class="delete_message" href="#"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a></td>
                </tr>
                </tbody>
              {/foreach}
            {/if}
          </table>

          <div class="escalation_button">
            {link_button label="Add New Escalation" icon_class=button_add id="new_escalation_message"}
          </div>
        </div>
      </div>
    </div>

    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Don't Send To{/lang}</h3>
        <p class="aid">{lang}Select clients that you don't want to remind automatically{/lang}</p>
      </div>
      <div class="content_stack_element_body">
        {wrap field=dont_send_to}
          {checkbox name="" class="select_all_companies" label="Select All"}
          {select_companies name="reminders[invoice_overdue_reminders_dont_send_to]" value=$reminders_data.invoice_overdue_reminders_dont_send_to user=$logged_user exclude_owner_company=true class="dont_send_to"}
        {/wrap}
      </div>
    </div>
  
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#invoice_overdue_reminders').each(function() {
    var wrapper = $(this);

    /**
     * Show escalation message textarea
     */
    var show_escalation_message_textarea = function(section_wrapper, focus) {
      section_wrapper.find('div.default_behavior').hide();
      section_wrapper.find('div.specified_behavior').slideDown(function() {
        if(focus) {
          var first_textarea = section_wrapper.find('textarea:first');

          if(first_textarea.length) {
            first_textarea.focus();
          } // if
        } // if
      });
    };

    // Escalation message textarea handler
    wrapper.find('div.default_or_specified_behavior').each(function() {
      var section_wrapper = $(this);

      if(section_wrapper.find('input.turn_on').is(':checked')) {
        show_escalation_message_textarea(section_wrapper, false);
      } // if

      section_wrapper.find('input.turn_on').click(function() {
        if(this.checked) {
          show_escalation_message_textarea(section_wrapper, true);

          // Pre-select drop-down option based on previous chosen option (send reminder every [X] day)
          section_wrapper.find('.escalation_overdue select option[value=' + wrapper.find('select.every').val() + ']').next('option:not(:disabled):not(.custom_value)').attr('selected', 'selected').prevAll('option').attr('disabled', 'disabled');
        } else {
          section_wrapper.find('div.specified_behavior').slideUp(function() {
            section_wrapper.find('div.default_behavior').show();
          });
        } // if
      });
    });

    var table = wrapper.find('table.escalation_messages');

    // Prevent first escalation message deletion
    table.find('.escalation_message_wrapper:first .options').empty();

    // Add new escalation message handler
    wrapper.find('#new_escalation_message:first').click(function () {
      var last_message = table.find('tbody.escalation_message_wrapper:last');
      var row_number = Number(last_message.attr('row_number')) + 1;

      var row = last_message.clone().attr('row_number', row_number);

      row.find('.escalation_overdue select').attr('name', 'reminders[invoice_overdue_reminders_escalation_messages][' + row_number + '][send_escalated]');
      row.find('.escalation_message textarea').attr('name', 'reminders[invoice_overdue_reminders_escalation_messages][' + row_number + '][escalated_message]').empty();
      row.find('.options').html('<a class="delete_message" href="#"><img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment') + '" alt="" /></a>');

      // Pre-select next value and disable previous ones
      row.find('.escalation_overdue select option[value=' + last_message.find('.escalation_overdue select').val() + ']').next('option:not(:disabled):not(.custom_value)').attr('selected', 'selected').prevAll('option').attr('disabled', 'disabled');

      var row_select = row.find('.escalation_overdue select');

      row_select.change(function() {
        if(row_select.val() == 'other') {
          var input = prompt(App.lang('Please insert when to send overdue reminder, in days. Example: 1, 3, 7 etc.'), '');

          if(input) {
            var value = App.Wireframe.Utils.parseWhenToSendOverdueReminder(input);

            if(value > 0) {
              if(row_select.find('option[value="' + value + '"]').length == 0) {
                row_select.find('option.when_to_send_overdue_reminder_value:last').after('<option value="' + value + '" class="when_to_send_overdue_reminder_value">' + App.Wireframe.Utils.formatWhenToSendOverdueReminder(value) + '</option>');
              } // if

              row_select.val(value);
            } else {
              App.Wireframe.Flash.error(App.lang('Value must be 1 or more days.'));
              row_select.val('');
            } // if
          } // if
        } // if
      });

      row.appendTo(table).find('textarea').focus();
    });

    // Delete escalation message
    wrapper.delegate('a.delete_message', 'click', function(event) {
      $(this).parents('.escalation_message_wrapper:first').remove();
    });

    // Enable/disable all companies we won't send reminders to
    wrapper.delegate('input[type="checkbox"].select_all_companies', 'change', function() {
      var select_all_checkbox = $(this);
      var all_companies = select_all_checkbox.parent().next('.checkbox_group').find('input[type="checkbox"]');

      if(select_all_checkbox.is(':checked')) {
        all_companies.attr('checked', true);
      } else {
        all_companies.attr('checked', false);
      } // if
    });

    // Disable all companies checkbox if we deselect one of them
    wrapper.delegate('div.dont_send_to input[type="checkbox"]', 'click', function() {
      var company = $(this);
      var select_all_checkbox = company.parents('.dont_send_to').prev().find('input[type="checkbox"]');

      if(!company.is(':checked') && select_all_checkbox.is(':checked')) {
        select_all_checkbox.attr('checked', false);
      } // if
    });
  });
</script>