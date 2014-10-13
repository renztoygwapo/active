{title}{$project_requests_page_title}{/title}
{add_bread_crumb}Submit{/add_bread_crumb}

<div id="submit_project_request">
  <div class="public_form">
	  {form action=Router::assemble('project_request_submit')}
    
			{if $project_requests_page_description}
			  <p class="project_request_description">{$project_requests_page_description|clean|clickable|nl2br nofilter}</p>
			{/if}    
    
	    {wrap field=created_by_name}
	      {text_field name='project_request[created_by_name]' value=$project_request_data.created_by_name required=true label="Your Name"}
	    {/wrap}
	  
	    {wrap field=created_by_email}
	      {text_field name='project_request[created_by_email]' value=$project_request_data.created_by_email required=true label="Your Email Address"}
	    {/wrap}
	    
	    {wrap field=created_by_company_name}
	      {text_field name='project_request[created_by_company_name]' value=$project_request_data.created_by_company_name required=true label="Your Company Name"}
	    {/wrap}

      {wrap field=created_by_company_address}
	      {textarea_field name='project_request[created_by_company_address]' class='company_address' label="Company Address" required=false}{$project_request_data.created_by_company_address nofilter}{/textarea_field}
	    {/wrap}
	    
	    {wrap field=name}
	      {text_field name='project_request[name]' value=$project_request_data.name required=true label="Project Name"}
	    {/wrap}
	  
	    {wrap field=body}
	      {textarea_field name='project_request[body]' label="Project Description" required=true}{$project_request_data.body nofilter}{/textarea_field}
	    {/wrap}
	    
	    {if is_foreachable($project_requests_custom_fields)}
	      {foreach $project_requests_custom_fields as $custom_field_key => $custom_field}
	  	    {if isset($custom_field.enabled) && $custom_field.enabled == 1}
	  	    	{wrap field=$custom_field_key}
	  		    	{text_field name="project_request[$custom_field_key]" value=$project_request_data.$custom_field_key label=$custom_field.name}
	  		    {/wrap}
	  		  {/if}
	      {/foreach}
	    {/if}
	  
	    {if $project_requests_captcha_enabled}
	  		{wrap field=captcha}
	  			{label for=captcha required=yes}Type the code shown{/label}
	  			{captcha name='project_request[captcha]' value=$project_request_data.captcha id=captcha class=required}
	  		{/wrap}
	  	{/if}
	  
	  	{wrap_buttons}
	  		{submit}Submit Project Request{/submit}
	  	{/wrap_buttons}
	  {/form}
  </div>
</div>