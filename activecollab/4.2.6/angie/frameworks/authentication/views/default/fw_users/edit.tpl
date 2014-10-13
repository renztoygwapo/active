{title}Update User{/title}

<div id="update_user">
  {form action=$user->getEditUrl()}
    {include file=get_view_path('_user_form', 'fw_users', $smarty.const.AUTHENTICATION_FRAMEWORK)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>