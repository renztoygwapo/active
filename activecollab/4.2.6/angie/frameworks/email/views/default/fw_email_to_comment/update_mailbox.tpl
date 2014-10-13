{form action=$form_action_url}
  {wrap_fields}
    {wrap field=type}
      {select_mailbox_type name="mailbox[server_type]" value=$mailbox_data.server_type id=mailboxType label='Server Type' required=true}
    {/wrap}

    {wrap field=host}
      {text_field name="mailbox[host]" value=$mailbox_data.host id=hostName label='Server Address' required=true}
    {/wrap}

    {wrap field=port}
      {port_field name="mailbox[port]" value=$mailbox_data.port id=mailboxPort label='Port' required=true}
    {/wrap}

    {wrap field=username}
      {text_field name="mailbox[username]" value=$mailbox_data.username id=username label='Username' required=true}
    {/wrap}

    {wrap field=password}
      {password_field name="mailbox[password]" value=$mailbox_data.password id=password label='Password' required=true}
    {/wrap}

    {wrap field=security}
      {select_mailbox_security name="mailbox[security]" value=$mailbox_data.security id=mailboxSecurity label='Security' required=true}
    {/wrap}

    {wrap field=mailbox}
      {text_field name="mailbox[mailbox]" value=$mailbox_data.mailbox id=mailboxName label='Mailbox Name' required=true}
      <p class="details">{lang}This is mailbox name on your POP3/IMAP server. In most cases it should be left as default value ('INBOX') unless you want to check some other mailbox{/lang}.</p>
    {/wrap}
  {/wrap_fields}

  {wrap_buttons}
    {submit}Test and Update Connection Parameters{/submit}
  {/wrap_buttons}
{/form}
