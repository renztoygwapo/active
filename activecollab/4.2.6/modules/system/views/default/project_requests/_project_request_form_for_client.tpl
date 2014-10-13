<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper one_form_sidebar" id="project_request_client_picker">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="project_request[name]" value=$project_request_data.name class=title required=true label="Name" required=true}
    {/wrap}
    
    {wrap_editor field=body}
      {editor_field name="project_request[body]" inline_attachments=$project_request_data.inline_attachments label='Description'}{$project_request_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    <div class="request_client_address">
      {wrap field=company_id}
        {label}Company{/label}
        {$logged_user->getCompany()->getName()}
      {/wrap}

      {wrap field=company_address class=companyAddressContainer}
        {textarea_field name="client[created_by_company_address]" id=companyAddress class='required long' label='Company Address'}{$project_request_data.created_by_company_address nofilter}{/textarea_field}
      {/wrap}
    </div>
    
    {if is_foreachable($custom_fields)}
	    {foreach $custom_fields as $custom_field_key => $custom_field}
		    {if isset($custom_field.enabled) && $custom_field.enabled == 1}
		    	{wrap field=$custom_field_key}
			      {label for=$custom_field.name|lower}{$custom_field.name}{/label}
			    	{text_field name="project_request[$custom_field_key]" value=$project_request_data.$custom_field_key id="{$custom_field.name|lower}"}
			    {/wrap}
			  {/if}
	    {/foreach}
	  {/if}
  </div>
</div>