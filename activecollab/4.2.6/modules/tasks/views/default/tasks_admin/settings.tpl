{title}Settings{/title}

<div id="tasks_admin_settings">
  {form action=Router::assemble('tasks_admin_settings')}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Auto-Reopen{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=auto_reopen id="tasks_admin_settings_tasks_admin_settings"}
            {checkbox name="settings[tasks_auto_reopen]" checked=$tasks_auto_reopen label="Automatically Reopen Completed Tasks on New Comment"}
          {/wrap}
          
          {wrap field=auto_reopen_clients_only id="tasks_admin_settings_tasks_auto_reopen_clients_only"}
            {checkbox name="settings[tasks_auto_reopen_clients_only]" checked=$tasks_auto_reopen_clients_only label="Only for Comments Posted by Clients"}
          {/wrap}
        </div>
      </div>
    
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Public Forms{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=public_submit_enabled id="tasks_admin_settings_tasks_public_submit_enabled"}
            {checkbox name="settings[tasks_public_submit_enabled]" checked=$tasks_public_submit_enabled label="Enable Public Task Forms"}
          {/wrap}
          
          {wrap field=use_captcha id="tasks_admin_settings_tasks_use_captcha"}
            {checkbox name="settings[tasks_use_captcha]" checked=$tasks_use_captcha label="Protect Public Task Forms from SPAM"}
            <p class="details">{lang}Select Yes to enable graphic form protection (CAPTCHA){/lang}</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element last">
        <div class="content_stack_element_info">
          <h3>{lang}Custom Fields{/lang}</h3>
          <p class="aid">{lang}Define up to 3 custom fields{/lang}</p>
        </div>
        <div class="content_stack_element_body">
          {wrap field=custom_fields}
            {configure_custom_fields name='settings[custom_fields]' type='Task'}
          {/wrap}
        </div>
      </div>
    </div>
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#tasks_admin_settings_tasks_admin_settings input[type=checkbox]').each(function() {
    if(!this.checked) {
      $('#tasks_admin_settings_tasks_auto_reopen_clients_only').hide();
    } // if 

    $(this).click(function() {
      if(this.checked) {
        $('#tasks_admin_settings_tasks_auto_reopen_clients_only').slideDown('fast');
      } else {
        $('#tasks_admin_settings_tasks_auto_reopen_clients_only').slideUp('fast');
      } // if
    });
  });

  $('#tasks_admin_settings_tasks_public_submit_enabled input[type=checkbox]').each(function() {
    if(!this.checked) {
      $('#tasks_admin_settings_tasks_use_captcha').hide();
    } // if 

    $(this).click(function() {
      if(this.checked) {
        $('#tasks_admin_settings_tasks_use_captcha').slideDown('fast');
      } else {
        $('#tasks_admin_settings_tasks_use_captcha').slideUp('fast');
      } // if
    });
  });
</script>