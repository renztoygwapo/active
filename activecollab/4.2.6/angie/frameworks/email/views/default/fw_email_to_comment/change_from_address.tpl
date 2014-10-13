<div id="change_from_address">
  {form action=Router::assemble('email_admin_reply_to_comment_change_from_address')}
    {wrap_fields}
      {wrap field=from_name}
        {text_field name='from[name]' value=$from_data.name label="From: Name"}
      {/wrap}

      {wrap field=from_name}
        {email_field name='from[email]' value=$from_data.email label="From: Email"}
      {/wrap}

      <p>{lang admin_email=$admin_email}Leave email field empty to use default address (:admin_email){/lang}</p>
    {/wrap_fields}

    {wrap_buttons}
      {submit}Change Settings{/submit}
    {/wrap_buttons}
  {/form}
</div>