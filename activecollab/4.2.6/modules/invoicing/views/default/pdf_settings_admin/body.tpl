{form method="POST" action=$form_url id="invoice_body_form" enctype="multipart/form-data"}
  <div class="content_stack_wrapper">
  
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Layout{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
      
        {wrap field="body_layout"}
          {label}Choose layout{/label}
	        {radio_field name="template[body_layout]" label="Client details on the left, invoice details on the right" value=0 checked=!$template_data.body_layout}<br/>
	        {radio_field name="template[body_layout]" label="Invoice details on the left, client details on the right" value=1 checked=$template_data.body_layout}
         {/wrap}
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Client Details{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field="client_details_font"}
          {label}Text Style{/label}
          {select_font name="template[client_details_font]" value=$template_data.client_details_font}
          {color_field name="template[client_details_text_color]" value=$template_data.client_details_text_color class="inline_color_picker"}
        {/wrap}            
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Invoice Details{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field="invoice_details_font"}
          {label}Text Style{/label}
          {select_font name="template[invoice_details_font]" value=$template_data.invoice_details_font}
          {color_field name="template[invoice_details_text_color]" value=$template_data.invoice_details_text_color class="inline_color_picker"}
        {/wrap}            
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Items{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field="items_font"}
          {label}Text Style{/label}
          {select_font name="template[items_font]" value=$template_data.items_font}
          {color_field name="template[items_text_color]" value=$template_data.items_text_color class="inline_color_picker"}
        {/wrap}
        
        {wrap field="header_text_color"}
          {checkbox_field name="template[print_table_border]" label="Show table bottom and top border" value=1 checked=$template_data.print_table_border id="table_border_toggler"}&nbsp;
          {color_field name="template[table_border_color]" value=$template_data.table_border_color class="inline_color_picker table_border_property"}
        {/wrap}        
        
        {wrap field="header_text_color"}
          {checkbox_field name="template[print_items_border]" label="Show border between invoice items" value=1 checked=$template_data.print_items_border id="border_toggler"}&nbsp;
          {color_field name="template[items_border_color]" value=$template_data.items_border_color class="inline_color_picker border_property"}
        {/wrap}

        {wrap field="item_columns"}
          {label}Columns to Display{/label}
          <ul>
            <li>{checkbox_field name="template[display_item_order]" label="Item #" value=1 checked=$template_data.display_item_order id="display_item_order_toggler"}</li>
            <li>{checkbox_field name="" label="Description" value=1 checked=true disabled=disabled}</li>
            <li>{checkbox_field name="template[display_quantity]" label="Quantity" value=1 checked=$template_data.display_quantity id="display_quantity_toggler"}</li>
            <li>{checkbox_field name="template[display_unit_cost]" label="Unit Price" value=1 checked=$template_data.display_unit_cost id="display_unit_cost_toggler"}</li>
            <li>{checkbox_field name="template[display_subtotal]" label="Subtotal" value=1 checked=$template_data.display_subtotal id="display_subtotal_toggler"}</li>
            <li>{checkbox_field name="template[display_tax_rate]" label="Tax Rate" value=1 checked=$template_data.display_tax_rate id="display_tax_rate_toggler"} {lang}or{/lang} {checkbox_field name="template[display_tax_amount]" label="Tax Amount" value=1 checked=$template_data.display_tax_amount id="display_tax_amount_toggler"}</li>
            <li>{checkbox_field name="template[display_total]" label="Total" value=1 checked=$template_data.display_total id="display_total_toggler"}</li>
          </ul>

          <script type="text/javascript">
            var tax_rate_toggler = $('#display_tax_rate_toggler');
            var tax_rate_amount = $('#display_tax_amount_toggler');

            tax_rate_toggler.click(function () {
              if (tax_rate_toggler.is(':checked')) {
                tax_rate_amount.removeAttr('checked');
              } // if
            });

            tax_rate_amount.click(function () {
              if (tax_rate_amount.is(':checked')) {
                tax_rate_toggler.removeAttr('checked');
              } // if
            });
          </script>
        {/wrap}

        {wrap field="summarize_tax"}
          {label}Data Summarizing{/label}
          {checkbox_field name="template[summarize_tax]" label="Summarize Tax" value=1 checked=$template_data.summarize_tax id="summarize_tax_toggler"}
          <span class="details checked" style="{if !$template_data.summarize_tax}display: none;{/if}">&mdash; {lang}Tax of every item will be summed and shown as one item in totals section{/lang}</span>
          <span class="details unchecked" style="{if $template_data.summarize_tax}display: none;{/if}">&mdash; {lang}Tax information will be shown summed by tax type{/lang}</span>

          </br>{checkbox_field name="template[hide_tax_subtotal]" label="Hide Tax Subtotal if it is 0" value=1 checked=$template_data.hide_tax_subtotal id="hide_tax_subtotal_toggler"}
          </br>{checkbox_field name="template[show_amount_paid_balance_due]" label="Always Show Amount Paid and Balance Due" value=1 checked=$template_data.show_amount_paid_balance_due id="show_amount_paid_balance_due_toggler"}
        {/wrap}
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Note{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field="items_font"}
          {label}Text Style{/label}
          {select_font name="template[note_font]" value=$template_data.note_font}
          {color_field name="template[note_text_color]" value=$template_data.note_text_color class="inline_color_picker"}
        {/wrap}
      </div>
    </div>
    
  </div>

  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}

  <script type="text/javascript">
    var wrapper = $('#invoice_body_form');

    var border_toggler = wrapper.find('#border_toggler');
    var border_properties = wrapper.find('.border_property');
    var check_border_properties = function () {
      var is_checked = border_toggler.is(':checked');
      if (is_checked) {
        border_properties.show();
      } else {
        border_properties.hide();
      } // if      
    };    
    border_toggler.bind('click', check_border_properties);
    check_border_properties();

    var table_border_toggler = wrapper.find('#table_border_toggler');
    var table_border_properties = wrapper.find('.table_border_property');
    var check_table_border_properties = function () {
      var is_checked = table_border_toggler.is(':checked');
      if (is_checked) {
        table_border_properties.show();
      } else {
        table_border_properties.hide();
      } // if            
    };

    table_border_toggler.bind('click', check_table_border_properties);
    check_table_border_properties();

    var summarize_tax_toggler = wrapper.find('#summarize_tax_toggler');
    var details_checked = summarize_tax_toggler.parent().siblings('span.checked');
    var details_unchecked = summarize_tax_toggler.parent().siblings('span.unchecked');

    var check_summarize_tax_details = function () {
      if(summarize_tax_toggler.is(':checked')) {
        details_checked.show();
        details_unchecked.hide();
      } else {
        details_checked.hide();
        details_unchecked.show();
      } // if
    };

    summarize_tax_toggler.bind('click', check_summarize_tax_details);
  </script>

