{use_widget name=create_invoice_from_tracked_data module=$smarty.const.INVOICING_MODULE}

{if !$is_based_on_quote}
<script type="text/javascript">
  App.widgets.FlyoutDialog.front().addButton('change_description_formats', {$wireframe->actions->get('change_description_formats')|json nofilter});
</script>
{/if}

<div id="create_invoice_from_tracked_data" data-preview-items-url="{$preview_items_url}">
  {form action=$active_object->invoice()->getUrl()}
    <input type="hidden" name="filter_data" value="{$filter_data}">

  	<div class="content_stack_wrapper">
      {if !$is_based_on_quote}
      <div class="content_stack_element full_width with_columns three_columns">
        <div class="content_stack_element_body">
          <div class="content_stack_element_body_column" style="width: 30%">
            <h3>{lang}Time{/lang}</h3>
            <ul>
              <li>{lang}Total{/lang}: {sum_time object=$active_object user=$logged_user}</li>
              <li>{lang}Billable{/lang}: {sum_time object=$active_object user=$logged_user mode=billable}</li>
            </ul>
          </div>

          <div class="content_stack_element_body_column" style="width: 30%">
            <h3>{lang}Expenses{/lang}</h3>
            <ul>
              <li>{lang}Total{/lang}: {sum_expenses object=$active_object user=$logged_user}</li>
              <li>{lang}Billable{/lang}: {sum_expenses object=$active_object user=$logged_user mode=billable}</li>
            </ul>
          </div>

          <div id="tracked_data_sum_by_wrapper" class="content_stack_element_body_column" style="width: 40%">
            <h3>{lang}How Would You Like this Data to be Grouped?{/lang}</h3>
            {when_invoice_is_based_on name="invoice_data[sum_by]" id='tracked_data_sum_by' mode='select' required=true}
            <img src="{image_url name='create-invoice-arrow.png' module=$smarty.const.INVOICING_MODULE}">
          </div>
        </div>
      </div>
      <div class="content_stack_element full_width" id="create_invoice_from_tracked_data_items_section">
        <div class="content_stack_element_body">
          <div id="invoice_items_preview">
            <table class="common" cellspacing="0">
              <thead>
              <tr>
                <th class="num"></th>
                <th class="description">{lang}Description{/lang}</th>
                <th class="quantity center">{lang}Quantity{/lang}</th>
                <th class="unit_cost center">{lang}Unit Cost{/lang}</th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      {/if}

      <div class="content_stack_element full_width with_columns two_columns">
        <div class="content_stack_element_body wrap_invoice_settings">
          <div class="content_stack_element_body_column">
            {wrap field=company_id}
              {select_company name="invoice_data[company_id]" value=$invoice_data.company_id id='client_company_id' user=$logged_user can_create_new=false label='Client' required=true exclude_owner_company=true}
            {/wrap}

            {wrap field=company_address class=companyAddressContainer}
              {address_field name="invoice_data[company_address]" id='client_company_address' label='Address' required=true}
            {/wrap}

            {wrap field=language}
              {select_language name="invoice_data[language_id]" value=$invoice_data.language_id label='Language' preselect_default=true}
            {/wrap}

            {wrap field=po_number}
              {text_field name="invoice_data[purchase_order_number]" label='Purchase Order Number' value=$invoice_data.purchase_order_number optional=true}
            {/wrap}
          </div>
          <div class="content_stack_element_body_column">
            {wrap field=first_tax_rate_id}
              {select_tax_rate name='invoice_data[first_tax_rate_id]' label='Tax Rate #1' optional=true}
            {/wrap}

          {if Invoices::isSecondTaxEnabled()}
            {wrap field=second_tax_rate_id}
              {select_tax_rate name='invoice_data[second_tax_rate_id]' label='Tax Rate #2' optional=true first_tax_rate=false}
            {/wrap}
          {/if}

            {wrap field=project_id}
              {select_project name="invoice_data[project_id]" value=$invoice_data.project_id user=$logged_user label='Project' optional=true}
            {/wrap}

          {if $is_based_on_tracking_report}
            {wrap field=currency}
              {select_currency name="invoice_data[currency_id]" label='Currency' optional=true}
            {/wrap}
          {/if}

            {wrap field=allow_partial}
              {select_payments_type name="invoice_data[payments_type]" label='Payments' required=true}
            {/wrap}
          </div>
        </div>
      </div>
         
      <div class="content_stack_element full_width with_columns two_columns">
        <div class="content_stack_element_body wrap_invoice_note_and_comment">
          <div class="content_stack_element_body_column">
            {wrap field=invoice_note class='wrap_invoice_note'}
              {invoice_note name="invoice_data[note]" class='long' label='Invoice Note'}{$invoice_data.note nofilter}{/invoice_note}
            {/wrap}
          </div>
          <div class="content_stack_element_body_column">
            {wrap field=comment class='wrap_invoice_comment'}
              {invoice_comment name="invoice_data[private_note]" label='Our Comment (Private, Not Visible to the Client)'}{/invoice_comment}
            {/wrap}
          </div>
        </div>
      </div>
    </div>
    
    {wrap_buttons}
	  	{submit}Create Invoice{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#create_invoice_from_tracked_data').createInvoiceFromTrackedData();

  $('#create_invoice_from_tracked_data').each(function() {
    var wrapper = $(this);
    
    var company_id = wrapper.find('#client_company_id');
    var company_address = wrapper.find('#client_company_address');
    var company_details_url = {$js_company_details_url|json nofilter};
    
    var ajax_request;    
    company_id.change(function() {
  	  add_address();
    });
    add_address();

    function add_address() {
  	   if(company_id.length > 0) {
  	    var ajax_url = App.extendUrl(company_details_url, {
  	      'company_id' : company_id.val(),
  	      'skip_layout' : 1
  	    });
  	    
  	    // abort request if already exists and it's active
  	    if ((ajax_request) && (ajax_request.readyState !=4)) {
  	      ajax_request.abort();
  	    } // if
  	    
  	    if (!company_address.is('loading')) {
  	      company_address.addClass('loading');
  	    } // if
  	    
  	    company_address.attr("disabled", true);
  	    company_id.attr("disabled", true);
  	    
  	    ajax_request = $.ajax({
  	      'url' : ajax_url,
  	      'success' : function (response) {
  	        company_address.val(response);
  	        company_address.removeClass('loading');
  	        company_address.attr("disabled", false);
  	        company_id.attr("disabled", false);
  	      }
  	    });
  	   }
    }
  });
</script>