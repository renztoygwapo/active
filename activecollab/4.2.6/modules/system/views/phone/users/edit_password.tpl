{title}Update Password{/title}
{add_bread_crumb}Update Password{/add_bread_crumb}

<div id="update_password">
  {form action=$active_user->getEditPasswordUrl() csfr_protect=true}
    {wrap field=password}
      {password_field name='user[password]' label='Password' id=user_form_password required=true}
    {/wrap}
    
    {wrap field=repeat_password}
      {password_field name='user[repeat_password]' label='Repeat password' id=user_form_repeat_password required=true}
    {/wrap}
    
    {wrap_buttons}
    	{submit}Update Password{/submit}
    {/wrap_buttons}
  {/form}
</div>