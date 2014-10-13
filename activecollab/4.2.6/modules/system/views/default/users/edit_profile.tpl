{title}Update Profile{/title}
{add_bread_crumb}Update Profile{/add_bread_crumb}

<div id="edit_user_profile">
  {form action=$active_user->getEditProfileUrl() csfr_protect=true}
    <div class="content_stack_wrapper">
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Basic Information{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="col">
            {wrap field=first_name}
              {text_field name='user[first_name]' value=$user_data.first_name id=userFirstName label='First Name' required=true}
            {/wrap}
          </div>

          <div class="col">
            {wrap field=last_name}
              {text_field name='user[last_name]' value=$user_data.last_name id=userLastName label='Last Name' required=true}
            {/wrap}
          </div>

          <div class="col">
            {wrap field=title}
              {text_field name='user[title]' value=$user_data.title id=userTitle label='Title'}
            {/wrap}
          </div>
        </div>
      </div>

      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Email Addresses{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=email}
            {email_field name='user[email]' value=$user_data.email id=userEmail disabled=!$active_user->canChangePassword($logged_user) label='Primary Email Address' required=true}
            <p class="aid">{lang}Email notifications will be sent to this address{/lang}.</p>
          {/wrap}

          {wrap field=alternative_email id=alternative_user_addresses}
          {label}Alternative Email Addresses{/label}

          {if $additional_email_addresses}
            {foreach $additional_email_addresses as $additional_email_address}
              <div class="alternative_user_address_wrapper">
                <input name="user[additional_email_addresses][]" value="{$additional_email_address}" type="email"> <img src="{image_url name='icons/12x12/delete.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}">
              </div>
            {/foreach}
          {/if}

            <a href="#" id="add_alternative_user_address" class="button_add">{lang}Add{/lang}</a>
            <p class="aid">{lang}Alternative email addresses can be used for login, as well as for mailing in tasks, discussions and comments{/lang}.</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Contact Information{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
		      <div class="col">
		      {wrap field=phone_work}
		        {text_field name='user[phone_work]' value=$user_data.phone_work id=userPhoneWork label='Office Phone Number'}
		      {/wrap}
		      </div>
		      
		      <div class="col">
		      {wrap field=phone_mobile}
		        {text_field name='user[phone_mobile]' value=$user_data.phone_mobile id=userPhoneMobile label='Mobile Phone Number'}
		      {/wrap}
		      </div>
          
          <div class="clear"></div>
		      
		      {wrap field=im}
		        {label for=userIm}Instant Messenger{/label}
		        {select_im_type name='user[im_type]' value=$user_data.im_type class=auto} {text_field name='user[im_value]' value=$user_data.im_value id=userIm}
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
  $('#edit_user_profile').each(function() {
    var wrapper = $(this);

    var alternative_addresses_wrapper = wrapper.find('#alternative_user_addresses');

    alternative_addresses_wrapper.on('click', 'a#add_alternative_user_address', function() {
      var last_input = alternative_addresses_wrapper.find('div.alternative_user_address_wrapper:last');

      var to_append = '<div class="alternative_user_address_wrapper">' +
        '<input name="user[additional_email_addresses][]" type="email"> <img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/delete.png', 'environment') + '">' +
      '</div>';

      if(last_input.length > 0) {
        last_input.after(to_append);
      } else {
        alternative_addresses_wrapper.find('label').after(to_append);
      } // if

      alternative_addresses_wrapper.find('div.alternative_user_address_wrapper:last input').focus();

      if(alternative_addresses_wrapper.find('div.alternative_user_address_wrapper').length >= 5) {
        alternative_addresses_wrapper.find('a#add_alternative_user_address').hide();
      } // if

      return false;
    });

    alternative_addresses_wrapper.on('click', 'div.alternative_user_address_wrapper img', function() {
      $(this).parent().remove();

      if(alternative_addresses_wrapper.find('div.alternative_user_address_wrapper').length < 5) {
        alternative_addresses_wrapper.find('a#add_alternative_user_address').show();
      } // if
    });
  });
</script>