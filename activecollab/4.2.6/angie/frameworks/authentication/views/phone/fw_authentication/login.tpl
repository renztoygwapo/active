{title}Sign In{/title}

<div id="login">
	<h3>{lang}Sign in with your credentials{/lang}</h3>
	{form}
    {wrap field=name}
		  {email_field name='login[email]' value=$login_data.email placeholder="Email Address" id=login_form_email required=true}
		{/wrap}
		
		{wrap field=name}
		  {password_field name='login[password]' placeholder="Password" id=login_form_password required=true}
		{/wrap}
		
		{wrap field=interface}
	    {select_interface name='login[interface]' value=$login_data.interface id=login_form_interface}
	  {/wrap}
	  
	  {wrap field=remember_me}
      {remember_me name="login[remember]" checked=$login_data.remember id=login_form_remember label="Remember me"}
    {/wrap}
    
    {wrap_buttons}
      {submit theme=r}Sign In{/submit}
    {/wrap_buttons}
  {/form}
  
  <div id="login_footer" class="top_shadow">
		{link href=Router::assemble('forgot_password') class=forgot_password_link}Forgot password?{/link}
	</div>
</div>