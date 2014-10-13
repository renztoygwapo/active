{title}Forgot Password{/title}

<div id="forgot_password">
	{form action=Router::assemble('forgot_password')}
    {wrap field=name}
		  {email_field name='forgot_password[email]' value=$forgot_password_data.email placeholder="Email Address" id=forgot_password_form_email required=true}
		{/wrap}
    
    {wrap_buttons}
      {submit theme=r}Submit{/submit}
    {/wrap_buttons}
  {/form}
  
  <div id="login_footer" class="top_shadow">
		{link href=Router::assemble('login') class=login_link}Back to Login Form{/link}
	</div>
</div>