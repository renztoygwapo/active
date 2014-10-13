{title}Outgoing Email Settings{/title}
{add_bread_crumb}Outgoing Email Settings{/add_bread_crumb}
{use_widget name="form" module="environment"}

<div id="mailing_settings">
  {form action=Router::assemble('outgoing_email_admin_settings') method=post id="mailing_settings_admin"}
    <div class="content_stack_wrapper">
    {if !AngieApplication::mailer()->isConnectionConfigurationLocked()}
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Connection{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=mailing id=mailingType}
            <div>
              <input type="radio" name="mailing[mailing]" value="disabled" id="mailingTypeDisabled" {if $mailing_data.mailing == 'disabled'}checked="checked"{/if} class="auto mailing_connection_type" /> <label class="inline" for="mailingTypeDisabled">{lang}Disabled{/lang}</label> <span class="details">&mdash; {lang}Don't send any email messages or notifications{/lang}</span>
            </div>
            
            <div>
              <input type="radio" name="mailing[mailing]" value="silent" id="mailingTypeSilent" {if $mailing_data.mailing == 'silent'}checked="checked"{/if} class="auto mailing_connection_type" /> <label class="inline" for="mailingTypeSilent">{lang}Silent{/lang}</label> <span class="details">&mdash; {lang}Notification and email system is working, but does not send any messages. This mode is great for testing{/lang}</span>
            </div>
            
            <!-- Native Mailing -->
            <div>
              <input type="radio" name="mailing[mailing]" value="native" id="mailingTypeNative" {if $mailing_data.mailing == 'native'}checked="checked"{/if} class="auto mailing_connection_type" /> <label class="inline" for="mailingTypeNative">{lang}Native{/lang}</label> <span class="details">&mdash; {lang}Native PHP mailing. PHP needs to be properly configured so system can send messages using <a href="http://www.php.net/mail" target="_blank">mail()</a> function{/lang}</span>
              
              <div id="nativeSettings" class="slide_down_settings" {if $mailing_data.mailing != 'native'}style="display: none"{/if}>
                {wrap field=mailing_native_options}
                  {label for=mailingNativeOptions}Native Mailer Options{/label}
                  {text_field name="mailing[mailing_native_options]" value=$mailing_data.mailing_native_options id=mailingNativeOptions}
                  <p class="details">{lang}Default value is "-oi -f %s". If you are <strong>experiencing problems</strong> with default value, try with no native mailer options{/lang}</p>
                {/wrap}
              </div>
            </div>
            
            <!-- SMTP connection -->
            <div>
              <input type="radio" name="mailing[mailing]" value="smtp" id="mailingTypeSMTP" {if $mailing_data.mailing == 'smtp'}checked="checked"{/if} class="auto mailing_connection_type" /> <label class="inline" for="mailingTypeSMTP">{lang}SMTP{/lang}</label> <span class="details">&mdash; {lang}Use external SMTP server to send messages{/lang}</span>
              <div id="smtpSettings" class="slide_down_settings" {if $mailing_data.mailing != 'smtp'}style="display: none"{/if}>
                <div class="col">
                {wrap field=mailing_smtp_host}
                  {label for=mailingSmtpHost required=yes}SMTP host{/label}
                  {text_field name="mailing[mailing_smtp_host]" value=$mailing_data.mailing_smtp_host id=mailingSmtpHost}
                {/wrap}
                </div>
                
                <div class="col">
                {wrap field=mailing_smtp_port}
                  {label for=mailingSmtpPort required=yes}SMTP port{/label}
                  {port_field name="mailing[mailing_smtp_port]" value=$mailing_data.mailing_smtp_port id=mailingSmtpPort class=short}
                {/wrap}
                </div>
                
                <div class="clear"></div>
                
                {wrap field=mailing_smtp_authenticate id=mailingAuthenticateRadioWrapper}
                  {label for=mailingAuthenticate}SMTP Authentication{/label}
                  {yes_no name="mailing[mailing_smtp_authenticate]" value=$mailing_data.mailing_smtp_authenticate id=mailingAuthenticate}
                {/wrap}
                
                <div id="mailingAuthenticateWrapper" style="display: none">
                  <div class="col">
                  {wrap field=mailing_smtp_username}
                    {label for=mailingUsername required=true}Username{/label}
                    {text_field name="mailing[mailing_smtp_username]" value=$mailing_data.mailing_smtp_username id=mailingUsername}
                  {/wrap}
                  </div>
                  
                  <div class="col">
                  {wrap field=mailing_smtp_password}
                    {label for=mailingPassword required=true}Password{/label}
                    {password_field name="mailing[mailing_smtp_password]" value=$mailing_data.mailing_smtp_password id=mailingPassword}
                  {/wrap}
                  </div>
                  
                  <div class="clear"></div>
                  
                  {wrap field=mailing_smtp_security}
                    {label for=mailingType}Security{/label}
                    <select name="mailing[mailing_smtp_security]" id="mailingSecurity">
                      <option value="off" {if $mailing_data.mailing_smtp_security == 'off'}selected{/if}>Off</option>
                      <option value="ssl" {if $mailing_data.mailing_smtp_security == 'ssl'}selected{/if}>SSL</option>
                      <option value="tls" {if $mailing_data.mailing_smtp_security == 'tls'}selected{/if}>TLS</option>
                    </select>
                  {/wrap}
                </div>
                
                <div class="clear"></div>
                <div id="test_connection" class="test_smtp_connection">
                  {button}Test Connection{/button}
                  <span class="test_connection_results">
                    <img src="{image_url name="layout/bits/indicator-pending.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt='' />
                    <span></span>
                  </span>
                </div>
              </div>
            </div>
          {/wrap}
        </div>
      </div>
    {/if}
      
      <div class="content_stack_element {if AngieApplication::mailer()->isConnectionConfigurationLocked()}odd{else}even{/if} {if !AngieApplication::mailer()->isMessageConfigurationLocked()}last{/if}">
        <div class="content_stack_element_info">
          <h3>{lang}Send Emails{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=mailing_method id=mailingMethod}
            <div>
              <input type="radio" name="mailing[mailing_method]" value="instantly" id="mailingMethodInstantly" {if $mailing_data.mailing_method == 'instantly'}checked="checked"{/if} class="auto" /> <label class="inline" for="mailingMethodInstantly">{lang}Instantly{/lang}</label> <span class="details">&mdash; {lang}Send emails in the same requests in which they were prepared{/lang}</span>
            </div>
            <div>
              <input type="radio" name="mailing[mailing_method]" value="in_background" id="mailingMethodInBackground" {if $mailing_data.mailing_method == 'in_background'}checked="checked"{/if} class="auto" /> <label class="inline" for="mailingMethodInBackground">{lang}In the Background{/lang}</label> <span class="details">&mdash; {lang}Don't slow down the request that prepared the notification, but send the message as soon as possible{/lang}</span>
            </div>
          {/wrap}
          
          {wrap field=mailing_method_override}
            {label}Allow users to individually configure how they would like to have email messages delivered{/label}
            {yes_no name="mailing[mailing_method_override]" value=$mailing_data.mailing_method_override}
          {/wrap}
        </div>
      </div>
      
    {if !AngieApplication::mailer()->isMessageConfigurationLocked()}
      <div class="content_stack_element odd last">
        <div class="content_stack_element_info">
          <h3>{lang}Message Settings{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="col">
            {wrap field=notifications_from_email}
              {email_field name="mailing[notifications_from_email]" value=$mailing_data.notifications_from_email label='From: Email'}
            {/wrap}
            
            {wrap field=notifications_options}
              {checkbox name="mailing[notifications_from_force]" checked=$mailing_data.notifications_from_force label="Force for All Notifications (Recommended)"}
              {checkbox name="mailing[mailing_mark_as_bulk]" checked=$mailing_data.mailing_mark_as_bulk label="Mark Notifications as Bulk Email (Recommended)"}
            {/wrap}
          </div>
          
          <div class="col">
          {wrap field=notifications_from_name}
            {text_field name="mailing[notifications_from_name]" value=$mailing_data.notifications_from_name label='From: Name'}
          {/wrap}
          </div>
        </div>
      </div>
    {/if}
    </div>
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#mailing_settings').each(function() {
    var wrapper = $(this);

    var smtp_host_input = wrapper.find('#smtpSettings #mailingSmtpHost');
    var smpt_port_input = wrapper.find('#smtpSettings #mailingSmtpPort');
    var smpt_username_input = wrapper.find('#smtpSettings #mailingUsername');
    var smpt_password_input = wrapper.find('#smtpSettings #mailingPassword');

    wrapper.find('#mailingType input.mailing_connection_type').click(function() {
      switch($(this).attr('value')) {
        case 'disabled':
        case 'silent':
          wrapper.find('#nativeSettings').hide();

          // Hide SMTP settings
          wrapper.find('#smtpSettings').hide();

          smtp_host_input.prop('required', false);
          smpt_port_input.prop('required', false);
          smpt_username_input.prop('required', false);
          smpt_password_input.prop('required', false);

          break;
        case 'native':
          wrapper.find('#nativeSettings').slideDown('fast');

          // Hide SMTP settings
          wrapper.find('#smtpSettings').hide();

          smtp_host_input.prop('required', false);
          smpt_port_input.prop('required', false);
          smpt_username_input.prop('required', false);
          smpt_password_input.prop('required', false);

          break;
        case 'smtp':
          wrapper.find('#nativeSettings').hide();

          // Show SMTP settings
          wrapper.find('#smtpSettings').slideDown('fast');

          smtp_host_input.prop('required', true);
          smpt_port_input.prop('required', true);

          if(wrapper.find('#mailingAuthenticateRadioWrapper input:checked').val() == '0') {
            smpt_username_input.prop('required', false);
            smpt_password_input.prop('required', false);
          } else {
            smpt_username_input.prop('required', true);
            smpt_password_input.prop('required', true);
          } // if

          break;
      }
    });

    var enable_disable_smpt_authentication = function() {
      if(wrapper.find('#mailingAuthenticateRadioWrapper input:checked').val() == '0') {
        smpt_username_input.prop('required', false);
        smpt_password_input.prop('required', false)
        wrapper.find('#mailingAuthenticateWrapper').hide('fast');
      } else {
        smpt_username_input.prop('required', true);
        smpt_password_input.prop('required', true)

        wrapper.find('#mailingAuthenticateWrapper').show('fast');
      } // if
    };

    wrapper.find('#mailingAuthenticateRadioWrapper input[type=radio]').click(enable_disable_smpt_authentication);
    enable_disable_smpt_authentication();

    var mailbox_form = wrapper.find('#mailing_settings_admin');
    var result_container = wrapper.find('#test_connection .test_connection_results', mailbox_form);
    var result_image = result_container.find('img');
    var result_output = result_container.find('span');

    wrapper.find('#test_connection button').click(function () {
      result_output.text('');
      result_image.attr('src', App.Wireframe.Utils.indicatorUrl());

      mailbox_form.ajaxSubmit({
        'dataType' : 'json',
        'success' : function(response) {
          result_output.text(response['message']);
          if (response['isSuccess']) {
            result_image.attr('src', App.Wireframe.Utils.indicatorUrl('ok'));
            result_container.removeClass('connection_error');
            result_container.addClass('connection_ok');
          } else {
            result_image.attr('src', App.Wireframe.Utils.indicatorUrl('error'));
            result_container.removeClass('connection_ok');
            result_container.addClass('connection_error');
          } // if
        },
        'error' : function(response) {
          result_output.text(App.lang('Could not connect to server with given parameters'));
          result_image.attr('src', App.Wireframe.Utils.indicatorUrl('error'));
          result_container.removeClass('connection_ok');
          result_container.addClass('connection_error');
        },
        'url' : App.extendUrl('{assemble route=outgoing_email_admin_test_smtp_connection}', {
          'async' : 1
        })
      });
    });
  });
</script>