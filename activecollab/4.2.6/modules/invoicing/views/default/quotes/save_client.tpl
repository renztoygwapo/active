<div id="new_user">
  {form action=$active_quote->getSaveClientUrl() method='post'}
    <div class="fields_wrapper">
      {wrap field=company_name}
        {text_field name="company_data[company_name]" id='company_name' required=true value=$company_data.company_name label='Client Company Name'}
      {/wrap}

      {wrap field=company_address}
        {textarea_field name="company_data[company_address]" id=company_address class='required' required=true label='Client Company Address'}{$company_data.company_address nofilter}{/textarea_field}
      {/wrap}

      {wrap field=email}
        {email_field name='user_data[email]' value=$user_data.email label="Client's Email"  required=true}
      {/wrap}

      {wrap field=first_name}
        {text_field name='user_data[first_name]' value=$user_data.first_name label="Client's First Name"  required=true}
      {/wrap}

      {wrap field=last_name}
        {text_field name='user_data[last_name]' value=$user_data.last_name label="Client's Last Name"}
      {/wrap}

      {wrap field=notify_client}
        {checkbox_field name='notify_client' label='Send Welcome e-mail to the client' value=$user_data.notify_client}
      {/wrap}
    </div>
    {wrap_buttons}
      {submit}Create Client{/submit}
    {/wrap_buttons}
  {/form}
</div>