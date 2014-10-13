<div id="projects_admin">
  {form action=Router::assemble('admin_projects')}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Morning Paper{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=project_tabs}
            {yes_no name="settings[morning_paper_enabled]" value=$settings_data.morning_paper_enabled label='Enabled'}
            <p class="aid">{lang}Morning Paper is an email notification that activeCollab sends each morning to all team members. It contains information about assignments that where completed yesterday, as well as what is due today{/lang}.</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Tabs{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=project_tabs}
            {select_project_tabs name="settings[project_tabs]" value=$settings_data.project_tabs}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Mail To Project{/lang}</h3>
          <p class="aid">{lang}This feature turns every project into a mailbox. Incoming messages are routed based on their subject{/lang}.</p>
        </div>
        <div class="content_stack_element_body">
          {wrap field=mail_to_project}
            {yes_no name="settings[mail_to_project]" value=$settings_data.mail_to_project label='Enable Mail To Project Interceptor'}
          {/wrap}
          {wrap field=mail_to_project_default_action}
            {select_mail_to_project_action name="settings[mail_to_project_default_action]" label="By Default create" value=$settings_data.mail_to_project_default_action}
            <p class="aid">{lang}This object will be created if email subject doesn't meet any of the predefined Mail To Project actions.{/lang}</p>

          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Assignment Delegation{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=clients_can_delegate_to_employees}
            {yes_no name="settings[clients_can_delegate_to_employees]" value=$settings_data.clients_can_delegate_to_employees label='Clients can Delegate Assignments to all Project Members'}
            <p class="aid">{lang}When this option is set to Yes, client can delegate assignments to all project members. Set this option to No to limit delegation list only to project members from client's company{/lang}.</p>
          {/wrap}
        </div>
      </div>
      
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Visibility{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=default_project_object_visibility}
            {select_visibility name="settings[default_project_object_visibility]" value=$settings_data.default_project_object_visibility label='Default Visibility'}
            <p class="aid">{lang}When creating a new objects in projects, set their visibility to selected value by default{/lang}.</p>
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
            {configure_custom_fields name='settings[custom_fields]' type='Project'}
          {/wrap}
        </div>
      </div>
    </div>
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>