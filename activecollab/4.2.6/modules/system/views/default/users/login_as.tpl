{title user_display_name=$active_user->getDisplayName()}Login As :user_display_name{/title}
{add_bread_crumb}Login As{/add_bread_crumb}

<div id="login_as">
  {form action=$active_user->getLoginAsUrl() method=post}
    <p>{lang user_display_name=$active_user->getDisplayName()}One click login as :user_display_name{/lang}</p>

    {wrap_buttons}
      {submit}Sign In{/submit}
    {/wrap_buttons}
  {/form}
</div>