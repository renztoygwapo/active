{form method="POST" action=$form_url id="invoice_header_form" enctype="multipart/form-data"}
  <div class="content_stack_wrapper">
  
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Layout{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
      
        {wrap field="body_layout"}
          {label}Choose layout{/label}
	        {radio_field name="template[header_layout]" label="Logo on the left, company details on the right" value=0 checked=!$template_data.header_layout}<br/>
	        {radio_field name="template[header_layout]" label="Company details on the left, logo on the right" value=1 checked=$template_data.header_layout}
         {/wrap}
      </div>
    </div>
  
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <div class="content_stack_optional">{checkbox name="template[print_logo]" label="Print" value=1 checked=$template_data.print_logo}</div>
        <h3>{lang}Logo{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
		     {wrap field="company_name"}
           {label}Upload New Company Logo{/label}
		       {file_field name="company_logo" class="company_name"}
		     {/wrap}
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <div class="content_stack_optional">{checkbox name="template[print_company_details]" label="Print" value=1 checked=$template_data.print_company_details}</div>
        <h3>{lang}Company Details{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
		     {wrap field="company_name"}
		       {text_field name="template[company_name]" label="Company Name" value=$template_data.company_name class="company_name"}
		     {/wrap}
	    
		     {wrap field="company_details"}
		       {textarea_field name="template[company_details]" label="Company Address and Details" rows="7" class="company_details"}{$template_data.company_details nofilter}{/textarea_field}
		     {/wrap}
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Appearance{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
				{wrap field="header_text_color"}
					{label}Text Style{/label}
		      {select_font name="template[header_font]" value=$template_data.header_font}
			  	{color_field name="template[header_text_color]" value=$template_data.header_text_color class="inline_color_picker"}
				{/wrap}
        
        {wrap field="header_text_color"}
          {checkbox_field name="template[print_header_border]" label="Show header border" value=1 checked=$template_data.print_header_border id="border_toggler"}&nbsp;
          {color_field name="template[header_border_color]" value=$template_data.header_border_color class="inline_color_picker border_property"}
        {/wrap}        
      </div>
    </div>
        
  </div>

	{wrap_buttons}
	  {submit}Save Changes{/submit}
	{/wrap_buttons}
{/form}

  <script type="text/javascript">
    var wrapper = $('#invoice_header_form');

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

  </script>