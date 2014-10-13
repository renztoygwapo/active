<div class="content_stack_wrapper">
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}People{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      <table class="people_multi_invite" cellspacing="0">
        <thead>
          <tr>
            <th class="first_name">{lang}First Name{/lang}:</th>
            <th class="last_name">{lang}Last Name{/lang}:</th>
            <th class="email">{lang}Email{/lang}: *</th>
          </tr>
        </thead>
        <tbody>
          <tr class="user_row" row_number="0">
            <td class="first_name_input">{text_field name="invite[users][0][first_name]" value=""}</td>
            <td class="last_name_input">{text_field name="invite[users][0][last_name]" value=""}</td>
            <td class="email_input">{email_field name="invite[users][0][email]" value="" required=true}</td>
            <td class="options"></td>
          </tr>
        </tbody>
      </table>

      <div class="invite_buttons">
        {link_button label="Invite Another User" icon_class=button_add id="invite_new"}
      </div>
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Role{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=role_id}
        {select_user_role name="invite" active_user=$logged_user value=$invite_data label='Role' class=required}
      {/wrap}
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Company{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=company_id}
	      {select_company name='invite[company_id]' value=$invite_data.company_id user=$logged_user success_event='company_created' optional=false label='Company' required=true}
	    {/wrap}
    </div>
  </div>

  <div class="content_stack_element default_or_specified_behavior">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="invite[send_welcome_message]" class="turn_on" for_id="subject" label="Send" value=1 checked=$invite_data.send_welcome_message}</div>
      <h3>{lang}Welcome Message{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      <div class="default_behavior">
        <p>{lang}System will not email a welcome message to the user. You can do that later on using <b>Send Welcome Message</b> tool that will be available in <b>Options</b> drop-down of the newly created account{/lang}.</p>
      </div>

      <div class="specified_behavior">
        {wrap field=welcome_message}
          {textarea_field name="invite[welcome_message]" label='Personalize welcome message'}{$invite_data.welcome_message nofilter}{/textarea_field}
          <p class="aid">{lang}New lines will be preserved. HTML is not allowed{/lang}</p>
        {/wrap}
      </div>
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Projects{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap_fields}
        <table>
          <tr>
            <td class="projects_list">
              {wrap field=projects class="select_projects_add_permissions"}
                {add_user_projects_select name=projects user=$logged_user show_all=true label='Select Projects'}
              {/wrap}
            </td>
            <td class="people_permissions">
              {wrap field=user_permissions}
                {select_user_project_permissions name=project_permissions role_id=$default_project_role_id label='Permissions'}
              {/wrap}
            </td>
          </tr>
        </table>
      {/wrap_fields}
    </div>
  </div>
</div>