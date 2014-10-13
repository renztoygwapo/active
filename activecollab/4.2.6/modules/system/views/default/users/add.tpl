{title}New User{/title}
{add_bread_crumb}New User{/add_bread_crumb}
<div id="new_user">
  {form action=$active_company->getAddUserUrl() csfr_protect=true}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Email Address{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=email}
            {email_field name="user[email]" value=$user_data.email label="Email" required=true}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}First Name{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=first_name}
            {text_field name="user[first_name]" value=$user_data.first_name label='First Name' required=true}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Last Name{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=last_name}
            {text_field name="user[last_name]" value=$user_data.private_url label='Last Name' required=true}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Role and Permissions{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=role_id}
            {label for=userRole required=yes}Role{/label}

            {if $only_administrator}
              {$active_user->getRoleName()}
            {else}
              {select_user_role name='user' active_user=$active_user value=$user_data class=required}
            {/if}
          {/wrap}

          <div class="hiddenPrivateUrlEnable" style="display:none">
            {wrap field=enable_private_url}
                {label for=enable_private_url}Enable Private URL{/label}
                 {select_enable_private_url name='user[private_url_enabled]' value="" class=auto optional=true}
            {/wrap}
          </div>

          <div class="hiddenPrivateUrl" style="display:none">
            {wrap field=private_url}
              {text_field name="user[private_url]" value=$user_data.private_url label='Private Url'}
              <span>.{$user_data.rep_site_domain}</span>
              <p class="aid">{lang}eg: <i>john</i>.abuckagallon.com{/lang}</p>
            {/wrap}
          </div>

          <div class="hiddenPersonalityType" style="display:none">
            {wrap field=personality_type}
              {label for=personality_type}Personality Type{/label}
              {select_personality_type name='user[personality_type]' value="" class=auto optional=true}
            {/wrap}
          </div>
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Manage By{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=managed_by_id}
            {select_manage_by name="user[managed_by_id]" class='select_managed_by' value="" label="Managed By" optional=true}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Title{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=title}
              {text_field name='user[title]' value=$user_data.title label='Title'}
            {/wrap}
        </div>
      </div>
      
      <!-- Moved first, last name to top , set both for required=true

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional">{checkbox name="user[profile_details]" class="turn_on" for_id="subject" label="Specify" value=1 checked=$user_data.profile_details}</div>
          <h3>{lang}Name and Title{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p>{lang}Full name and title will be left blank. You can always populate these details later on{/lang}.</p>
          </div>

          <div class="specified_behavior">
            <div class="col">
              {wrap field=first_name}
                {text_field name="user[first_name]" value=$user_data.first_name label='First Name'}
              {/wrap}
            </div>

            <div class="col">
              {wrap field=last_name}
                {text_field name="user[last_name]" value=$user_data.last_name label='Last Name'}
              {/wrap}
            </div>

            <div class="clear"></div>

            {wrap field=title}
              {text_field name='user[title]' value=$user_data.title label='Title'}
            {/wrap}
          </div>
        </div>
      </div>
      -->

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional">{checkbox name="user[specify_password]" class="turn_on" for_id="subject" label="Specify" value=1 checked=$user_data.specify_password}</div>
          <h3>{lang}Password{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p>{lang}System will automatically generate a safe password for this account{/lang}.</p>
          </div>

          <div class="specified_behavior">
            <div class="col">
              {wrap field=password}
                {password_field name='user[password]' value=$user_data.password label='Password'}
              {/wrap}
            </div>

            <div class="col">
              {wrap field=password_a}
                {password_field name='user[password_a]' value=$user_data.password_a label='Retype'}
              {/wrap}
            </div>

            <div class="clear"></div>
            {password_rules}
          </div>
        </div>
      </div>

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional">{checkbox name="user[send_welcome_message]" class="turn_on" for_id="subject" label="Send Now" value=1 checked=$user_data.send_welcome_message}</div>
          <h3>{lang}Welcome Message{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p>{lang}System will not email a welcome message to the user. You can do that later on using <b>Send Welcome Message</b> tool that will be available in <b>Options</b> drop-down of the newly created account{/lang}.</p>
          </div>

          <div class="specified_behavior">
            {wrap field=welcome_message}
              {textarea_field name="user[welcome_message]" label='Personalize welcome message'}{$user_data.welcome_message nofilter}{/textarea_field}
              <p class="aid">{lang}New lines will be preserved. HTML is not allowed{/lang}</p>
            {/wrap}
          </div>
        </div>
      </div>

      <div class="content_stack_element default_or_specified_behavior">
        <div class="content_stack_element_info">
          <div class="content_stack_optional">{checkbox name="user[auto_assign]" class="turn_on" for_id="subject" label="Enabled" value=1 checked=$user_data.auto_assign}</div>
          <h3>{lang}Auto Assign{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="default_behavior">
            <p>{lang}System will <b>not</b> add this user to new projects automatically. Administrators and project managers will need to manually add this user to new projects{/lang}.</p>
          </div>

          <div class="specified_behavior">
            <p>{lang}Set a role or custom permissions to be used when user is automatically added to the project{/lang}:</p>
            {select_user_project_permissions name="user" role_id=$user_data.auto_assign_role_id permissions=$user_data.auto_assign_permissions role_id_field=auto_assign_role_id permissions_field=auto_assign_permissions}
          </div>
        </div>
      </div>
    </div>

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('user_created')}">
  {/if}
  
    {wrap_buttons}
      {submit}Add User{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $(document).ready(function(){
    // appemd personality_type field after role:Client
    var personalityTypeHtml = $('.hiddenPersonalityType').html();
    $('.role_client').prepend(personalityTypeHtml);
    $('.hiddenPersonalityType').html('');

    

    var privateUrlHtml = $('.hiddenPrivateUrlEnable').html();
    $('.role_subcontractor').prepend(privateUrlHtml);
    $('.hiddenPrivateUrlEnable').html('');

    var privateUrlHtml = $('.hiddenPrivateUrl').html();
    $('.role_subcontractor').prepend(privateUrlHtml);
    $('.hiddenPrivateUrl').html('');

  }); //document

  $('#new_user').each(function() {
    var wrapper = $(this);

    wrapper.find('div.default_or_specified_behavior').each(function() {
      var section_wrapper = $(this);

      section_wrapper.find('input.turn_on').click(function() {
        if(this.checked) {
          section_wrapper.find('div.default_behavior').hide();
          section_wrapper.find('div.specified_behavior').slideDown(function() {
            var first_input = section_wrapper.find('input[type=text]:first, input[type=password]:first');

            if(first_input.length) {
              first_input.focus();
            } else {
              var first_textarea = section_wrapper.find('textarea:first');

              if(first_textarea.length) {
                first_textarea.focus();
              } // if
            } // if
          });
        } else {
          section_wrapper.find('div.specified_behavior').slideUp(function() {
            section_wrapper.find('div.default_behavior').show();
          });
        } // if
      });
    });
  });
</script>