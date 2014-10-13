{title}New User{/title}

<div id="add_user">
  {form action=Router::assemble('users_add')}
    {include file=get_view_path('_user_form', 'fw_users', $smarty.const.AUTHENTICATION_FRAMEWORK)}
    
    {wrap_buttons}
      {submit}Add User{/submit}
    {/wrap_buttons}
  {/form}
</div>