  <div class="content_stack_wrapper">
    <div class="content_stack_element odd">
      <div class="content_stack_element_info">
        <h3>{lang}Account{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
      	{wrap field=name}
          {text_field name="mailbox[name]" value=$mailbox_data.name label='Account Name' required=true}
        {/wrap}
      
        {wrap field=email id=incoming_mailbox_email}
          {email_field name="mailbox[email]" value=$mailbox_data.email id=fromEmail label='Email Address' required=true}
          <p class="details">{lang}<b>Warning:</b> Please <u>do not use a personal address</u>. Depending on server settings and connection type, system will probably delete all messages from this mailbox after it imports them. It is recommended that you use special addresses that are checked by the system only (ex. projects@company.com, support@company.com etc){/lang}</p>
        {/wrap}
      </div>
    </div>
    
    <!-- Connection -->
    <div class="content_stack_element even">
      <div class="content_stack_element_info">
        <h3>{lang}Connection{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
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
        
        <div id="test_connection">
          <button type="button"><span>{lang}Test Connection{/lang}</span></button>
          <span class="test_connection_results">
            <img src="{image_url name="layout/bits/indicator-pending.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt='' />
            <span></span>
          </span>
        </div>
      </div>
    </div>
  </div>
