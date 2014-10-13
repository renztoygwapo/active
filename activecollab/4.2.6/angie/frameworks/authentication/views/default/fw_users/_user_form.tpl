{wrap field=email}
  {email_field name='user[email]' value=$user_data.email label='Email Address'}
{/wrap}

{wrap field=first_name}
  {text_field name='user[first_name]' value=$user_data.first_name label='First Name'}
{/wrap}

{wrap field=last_name}
  {text_field name='user[last_name]' value=$user_data.last_name label='Last Name'}
{/wrap}

{wrap field=passwords}
  {wrap field=password}
    {password_field name='user[password]' label='Password'}
  {/wrap}
  
  {wrap field=password_a}
    {password_field name='user[password_a]' label='Repeat Password'}
  {/wrap}
{/wrap}

{wrap field=role_id}
  {select_user_role name='user[role_id]' value=$user_data.role_id user=$logged_user label='Role' required=true}
{/wrap}