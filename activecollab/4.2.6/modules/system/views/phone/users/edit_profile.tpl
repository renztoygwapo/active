{title}Update Profile{/title}
{add_bread_crumb}Update Profile{/add_bread_crumb}

<div id="update_profile">
	{form action=$active_user->getEditProfileUrl() csfr_protect=true}
		{wrap field=email}
	    {text_field name='user[email]' value=$user_data.email label='Email' id=user_form_email required=true}
	  {/wrap}
	  
	  {wrap field=first_name}
	    {text_field name='user[first_name]' value=$user_data.first_name label='First Name' id=user_form_first_name}
	  {/wrap}
	  
	  {wrap field=last_name}
	    {text_field name='user[last_name]' value=$user_data.last_name label='Last Name' id=user_form_last_name}
	  {/wrap}
	  
	  {wrap field=title}
	    {text_field name='user[title]' value=$user_data.title label='Title' id=user_form_title}
	  {/wrap}
	  
	  {wrap field=phone_work}
	    {text_field name='user[phone_work]' value=$user_data.phone_work label='Office Phone Number' id=user_form_phone_work}
	  {/wrap}
	  
	  {wrap field=phone_mobile}
	    {text_field name='user[phone_mobile]' value=$user_data.phone_mobile label='Mobile Phone Number' id=user_form_phone_mobile}
	  {/wrap}
	  
	  {wrap field=im}
	    {select_im_type name='user[im_type]' value=$user_data.im_type label='Instant Messenger' id=user_form_im}
	    <script type="text/javascript">
      	$(document).ready(function() {
	  			App.Wireframe.SelectBox.init();
	  		});
			</script>
	  {/wrap}
	  
	  {wrap field=im_value}
	    {text_field name='user[im_value]' value=$user_data.im_value label='Instant Messenger ID' id=user_form_im_value}
	  {/wrap}
	  
	  {wrap_buttons}
	  	{submit}Save Changes{/submit}
	  {/wrap_buttons}
	{/form}
</div>