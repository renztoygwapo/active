{title}Sign In{/title}
<div id="login_company_logo">
  <img src="{brand what=logo}" alt="{$owner_company->getName()} logo" />
</div>

<div id="auth_dialog_container">
  <div id="auth_dialog_container_inner">
    <div id="auth_dialog">
    {form action=Router::assemble('login') method=post autofocus=$auto_focus show_errors=false}
      {wrap field=login}
        {wrap field=email}
          {text_field name='login[email]' value=$login_data.email id=login_form_email label="Email Address" required=yes tabindex=1}
        {/wrap}
        
        {wrap field=password}
          {password_field name='login[password]' id=login_form_password label="Password" required=yes tabindex=2}
        {/wrap}
        
        {wrap field=interface}
          {select_interface name='login[interface]' value=$login_data.interface id=login_form_interface label="Interface" tabindex=3}
        {/wrap}
        
        {wrap field=remember_me}
          {checkbox_field name="login[remember]" checked=$login_data.remember id=login_form_remember label="Remember me for 14 days" tabindex=4}
        {/wrap}
      {/wrap}
      
      {wrap_buttons}
        {link href=Router::assemble('forgot_password') class=forgot_password_link}Forgot password?{/link}
        {submit tabindex=5}Sign In{/submit}
      {/wrap_buttons}
      <div class="clear"></div>
    {/form}
    </div>
  </div>
</div>