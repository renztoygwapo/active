<div id="project_requests_admin">
  {form action=Router::assemble('admin_project_requests')}
  	<div class="content_stack_wrapper">
  	
  		<!-- Enable Project Requests -->
	  	<div class="content_stack_element">
	      <div class="content_stack_element_info">
	        <h3>{lang}Settings{/lang}</h3>
	      </div>
	      <div class="content_stack_element_body">
	        {wrap field=enabled}
			      {label for=projectRequestsEnabled}Enable Project Requests{/label}
			      {yes_no name='project_request[enabled]' value=$project_request_data.project_requests_enabled id=projectRequestsEnabled}
			    {/wrap}
			    
			    <p class="project_request_url">{lang}Request submission page{/lang}: <a href="{assemble route=project_request_submit}" target="_blank">{assemble route=project_request_submit}</a></p>
	      </div>
	    </div>
  	
  		<!-- Page Details -->
	  	<div class="content_stack_element">
	      <div class="content_stack_element_info">
	        <h3>{lang}Page Details{/lang}</h3>
	      </div>
	      <div class="content_stack_element_body">
	        {wrap field=page_title}
				    {label for=pageTitle}Title{/label}
				    {text_field name='project_request[page_title]' value=$project_request_data.project_requests_page_title id='pageTitle'}
				  {/wrap}
			    
			    {wrap field=page_description}
			      {label for=pageDescription}Description{/label}
			      {textarea_field name='project_request[page_description]' id='pageDescription'}{$project_request_data.project_requests_page_description nofilter}{/textarea_field}
			    {/wrap}
	      </div>
	    </div>
	    
	    <!-- Custom Fields -->
	    <div class="content_stack_element">
	      <div class="content_stack_element_info">
	        <h3>{lang}Custom Fields{/lang}</h3>
	      </div>
	      <div class="content_stack_element_body">
	        {wrap field=custom_fields}
			    	{label for=customFileds}Labels{/label}
				    <table class="form">
					    {if is_foreachable($project_request_data.project_requests_custom_fields)}
						    {foreach $project_request_data.project_requests_custom_fields as $custom_field_key => $custom_field}
						      <tr>
						        <td><input name="project_request[custom_fields][{$custom_field_key}][enabled]" type="checkbox" value="1" class="inline" {if $custom_field.enabled}checked="checked"{/if} /></td>
						        <td>{text_field name="project_request[custom_fields][$custom_field_key][name]" value=$custom_field.name}</td>
						      </tr>
						    {/foreach}
					    {/if}
				    </table>
				  {/wrap}
	      </div>
	    </div>
	    
	    <!-- Enable CAPTCHA -->
	    {if $gd_loaded}
		  	<div class="content_stack_element">
		      <div class="content_stack_element_info">
		        <h3>{lang}CAPTCHA Protection{/lang}</h3>
		      </div>
		      <div class="content_stack_element_body">
		        {wrap field=captcha_enabled}
				      {label for=captchaEnabled}Enable Captcha{/label}
				      {yes_no name='project_request[captcha_enabled]' value=$project_request_data.project_requests_captcha_enabled id=captchaEnabled}
				    {/wrap}
		      </div>
		    </div>
		  {/if}
	    
	    <!-- Notify Users -->
	    <div class="content_stack_element">
	      <div class="content_stack_element_info">
	        <h3>{lang}Notify Users{/lang}</h3>
	      </div>
	      <div class="content_stack_element_body">
	        {wrap field=subscribers}
			      {select_project_requests_managers name="project_request[notify_user_ids]" value=$project_request_data.project_requests_notify_user_ids user=$logged_user}
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
  (function () {
    var project_request_url = $('p.project_request_url');

    if ($('#projectRequestsEnabledNoInput').is(':checked')) {
      project_request_url.hide();
    } // if

    $('span.yes_no').find('input[type=radio]').click(function() {
      if ($(this).attr('id') == 'projectRequestsEnabledNoInput') {
        project_request_url.hide();
      } else {
        project_request_url.show();
      } // if
    });

    $('#project_requests_admin').find('button[type=submit]').click(function() {
      var error = false;

      $('#project_requests_admin').find('table tr').each(function() {
        if ($(this).find('input[type=checkbox]').is(':checked') && $(this).find('input[type=text]').val() == '') {
          error = true;
        } // if
      });

      if (error == true) {
        App.Wireframe.Flash.error('Please fill in labels for selected custom fields');
      } // if

      return !error;
    });
  }())
</script>