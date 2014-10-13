{title}Reset Password{/title}

<div id="reset_password">
	<h3>{lang name=$user->getDisplayName()}Use the form below to reset password for :name's account{/lang}</h3>
	{form}
		{wrap field=password}
		  {password_field name='reset[password]' placeholder="Password" id=reset_password_form_password required=true}
		{/wrap}
		
		{wrap field=password_a}
		  {password_field name='reset[password_a]' placeholder="Repeat" id=reset_password_form_password_a required=true}
		{/wrap}
    
    {wrap_buttons}
      {submit theme=r}Reset Password{/submit}
    {/wrap_buttons}
  {/form}
  
  <div id="login_footer" class="top_shadow">
		{link href=Router::assemble('login') class=forgot_password_link}Back to Login Form{/link}
	</div>
</div>