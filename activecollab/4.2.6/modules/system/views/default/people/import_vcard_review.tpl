{title}Review Uploaded vCard{/title}
{add_bread_crumb}Review{/add_bread_crumb}

<div id="import_from_vcard">
	{if is_foreachable($prepared_contacts)}
		{form action=Router::assemble('people_import_vcard') method=post}
		
			<div class="fields_wrapper">
				<div id="select_objects">
					{foreach from=$prepared_contacts key=key item=object}
					  {if $object.object_type == "Company"}
					  	<!-- Import company -->
					  	<div class="company_data">
					  		{list_prepared_object object=$object type=$object.object_type name="company[$key]"}
					  	</div>
						{elseif $object.object_type == "User"}
		
							<!-- Import user -->
							<div class="user_data">
								{list_prepared_object object=$object type=$object.object_type name="user[$key]"}
							</div>
					  {/if}
				  {/foreach}
				</div>
				
				<div id="send_welcome_message">
					<label for="SendWelcomeEmail" class="send_welcome_email">{lang}Send welcome email{/lang}</label>
					<input type="checkbox" name="user[send_welcome_message]" value="1" id="SendWelcomeEmail" />
					
					{wrap field=welcome_message}
		        {label for="personalizeWelcomeMessage"}Personalize welcome message{/label}
		        {textarea_field name="user[welcome_message]" id=personalizeWelcomeMessage}{/textarea_field}
		      {/wrap}
				</div>
				<div class="clear"></div>
			</div>
	
		  {wrap_buttons}
		    {submit}Import{/submit}
		  {/wrap_buttons}
		{/form}
	{else}
	  <p class="empty_page"><span class="inner">{lang}No valid contacts were found.{/lang}</span></p>
	{/if}
</div>

<script type="text/javascript">
  $('#import_from_vcard').each(function() {
    var wrapper = $(this);

    App.Wireframe.Events.bind('company_created.content', function(event, company) {
      var company_selections = wrapper.find('select.company');
      var new_company_option = '<option class="object_option" value="' + company.id + '">' + company.name + '</option>';

      App.each(company_selections, function() {
        var select_company = $(this);
        var option_exists = select_company.children('.object_option[value="' + company.id + '"]').length;

        if(!option_exists) {
          select_company.children('.object_option').last().after(new_company_option);
        } // if
      });
    });
  });

</script>