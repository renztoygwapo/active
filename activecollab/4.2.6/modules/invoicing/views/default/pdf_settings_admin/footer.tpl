{form method="POST" action=$form_url id="footer_body_form" enctype="multipart/form-data"}
  <div class="content_stack_wrapper">
  
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <div class="content_stack_optional">{checkbox name="template[print_footer]" label="Print" value=1 checked=$template_data.print_footer}</div>
        <h3>{lang}Footer layout{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
      
        {wrap field="footer_layout"}
          {label}Choose layout{/label}
          {radio_field name="template[footer_layout]" label="Invoice number on the left, page number on the right" value=0 checked=!$template_data.footer_layout}<br/>
          {radio_field name="template[footer_layout]" label="Page number on the left, invoice number on the right" value=1 checked=$template_data.footer_layout}
         {/wrap}
      </div>
    </div>
    
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}Appearance{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field="client_details_font"}
          {label}Text Style{/label}
          {select_font name="template[footer_font]" value=$template_data.footer_font}
          {color_field name="template[footer_text_color]" value=$template_data.footer_text_color class="inline_color_picker"}
        {/wrap}           
        
        {wrap field="header_text_color"}
          {checkbox_field name="template[print_footer_border]" label="Show footer border" value=1 checked=$template_data.print_footer_border id="border_toggler"}&nbsp;
          {color_field name="template[footer_border_color]" value=$template_data.footer_border_color class="inline_color_picker border_property"}
        {/wrap}    
      </div>
    </div>

  </div>

  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}

  <script type="text/javascript">
    var wrapper = $('#footer_body_form');

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