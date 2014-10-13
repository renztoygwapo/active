<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper one_form_sidebar" id="project_request_client_picker">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="project_request[name]" value=$project_request_data.name class=title required=true label="Name" required=true}
    {/wrap}
    
    {wrap_editor field=body}
      {label}Description{/label}
      {editor_field name="project_request[body]" inline_attachments=$project_request_data.inline_attachments}{$project_request_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    <div class="request_client_address">
    {wrap field=choose_client}
      {radio_field name=client_type class='existing_client' value='existing_client' label='Existing Client'}<br/>
      {radio_field name=client_type class='new_client' value='new_client' label='New Client'}
    {/wrap}
      <span class="request_new_client">
        {wrap field=new_company_name}
          {text_field id='new_company_name' name="new_client[created_by_company_name]" value=$new_client.created_by_company_name label='Company Name'}
        {/wrap}
        {wrap field=new_company_address class=companyAddressContainer}
          {textarea_field name="new_client[created_by_company_address]" id='new_company_address' class='long' label='Company Address'}{$new_client.created_by_company_address nofilter}{/textarea_field}
        {/wrap}
        {wrap field=new_recipient_name}
          {text_field id='new_recipient_name' name="new_client[created_by_name]" value=$new_client.created_by_name label="Contact Person"}
        {/wrap}
        {wrap field=new_recipient_email}
          {text_field id='new_recipient_email' name="new_client[created_by_email]" value=$new_client.created_by_email label="Contact Person's E-mail"}
        {/wrap}
      </span>
      <span class="request_existing_client">
        {wrap field=company_id}
          {select_company name="client[created_by_company_id]" can_create_new=false value=$project_request_data.created_by_company_id class=required id="companyId" user=$logged_user label='Company'}
        {/wrap}

        {wrap field=company_address class=companyAddressContainer}
          {textarea_field name="client[created_by_company_address]" id=companyAddress class='required long' label='Company Address'}{$project_request_data.created_by_company_address nofilter}{/textarea_field}
        {/wrap}
        {wrap field=recipient class=notificationRecipient}
          {select_client_with_permissions name="client[created_by_id]" value=$project_request_data.created_by_id permissions='can_request_projects,can_manage_client_finances' require_all_permissions=false id=created_by company_select_id='companyId' label='Contact person'}
        {/wrap}
      </span>
    </div>

    {wrap field=taken_by_id}
      {select_project_requests_manager name="project_request[taken_by_id]" value=$project_request_data.taken_by_id user=$logged_user optional=true label="Taken By"}
    {/wrap}
    
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
<script type="text/javascript">
  $('#project_request_client_picker').each(function() {
    var wrapper = $(this);
    var project_request_data = {$project_request_data|json nofilter};
    var new_client = {$new_client|json nofilter};
    var wrapper_existing_client = wrapper.find('span.request_existing_client');
    var wrapper_new_client = wrapper.find('span.request_new_client');
    var form_mode = {if $active_project_request->isNew()}'add'{else}'edit'{/if};

    var set_required_fields = function(selected_wrapper) {
      var client_fields = $('span.request_' + selected_wrapper).find('input, select, textarea');
      if (client_fields.length) {
        client_fields.each(function() {
          $(this).attr('required', 'true').addClass('required');

          var client_field_label = $(this).parent().find('label');
          if (typeof(client_field_label) == 'object' && client_field_label.html().indexOf(' *') == -1) {
            client_field_label.html(client_field_label.html() + ' *');
          } // if
        });
      } // if

      var other_wrapper = selected_wrapper == 'new_client' ? 'existing_client' : 'new_client';
      var unrequire_fields = $('span.request_' + other_wrapper).find('input, select, textarea');
      if (unrequire_fields.length) {
        unrequire_fields.each(function() {
          $(this).removeClass('required').removeAttr('required');
        });
      } // if
    };

    var toggle_wrappers = function(selected_wrapper) {
      if (selected_wrapper == 'new_client') {
        wrapper_existing_client.hide();
        wrapper_new_client.show();
      } else {
        wrapper_existing_client.show();
        wrapper_new_client.hide();
      } // if

      set_required_fields(selected_wrapper);
    };

    var default_wrapper = new_client !== null ? 'new_client' : 'existing_client';
    toggle_wrappers(default_wrapper);
    wrapper.find('input.'+default_wrapper).attr('checked', true);

    $('input[name="client_type"]').click(function() {
      toggle_wrappers($(this).val());
    });

    /**
     * Company picker
     */
    var company_id = wrapper.find('#companyId');
    var company_address = wrapper.find('#companyAddress');
    var company_details_url = {$js_company_details_url|json nofilter}

    var ajax_request;
    company_id.change(function () {
      if (company_id.val()) {
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
          url         : ajax_url,
          success     : function (response) {
            company_address.val(response);
            company_address.removeClass('loading');
            company_address.attr("disabled", false);
            company_id.attr("disabled", false);
          }
        });
      } // if
    });


    if (form_mode == 'add') {
      company_id.change();
    } // if
  });
</script>