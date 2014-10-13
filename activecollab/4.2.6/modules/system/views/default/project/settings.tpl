{title}Settings{/title}
{add_bread_crumb}Settings{/add_bread_crumb}

<div id="project_settings">
  {form action=$active_project->getSettingsUrl() method=post}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Tabs{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          <div>
            <input type="radio" name="settings[use_custom_tabs]" value="0" id="projectSettingsDontUseCustomTabs" class="inline" {if !$settings_data.use_custom_tabs}checked="checked"{/if} /> <label for="projectSettingsDontUseCustomTabs" class="inline">{lang}Use default set of tabs{/lang}</label>
          </div>
          <div>
            <input type="radio" name="settings[use_custom_tabs]" value="1" id="projectSettingsUseCustomTabs" class="inline" {if $settings_data.use_custom_tabs}checked="checked"{/if} /> <label for="projectSettingsUseCustomTabs" class="inline">{lang}Configure tabs for this project{/lang}</label>
          </div>

          <div id="project_tabs_settings_custom" style="display: {if $settings_data.use_custom_tabs}block{else}none{/if}">
            {select_project_tabs name="settings[project_tabs]" value=$settings_data.project_tabs}
          </div>
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Assignment Delegation{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=clients_can_delegate_to_employees}
            {yes_no_default name="settings[clients_can_delegate_to_employees]" value=$settings_data.clients_can_delegate_to_employees label='Clients can Delegate Assignments to all Project Members' default=$default_clients_can_delegate_to_employees}
            <p class="aid">{lang}When this option is set to Yes, client can delegate assignments to all project members. Set this option to No to limit delegation list only to project members from client's company{/lang}.</p>
          {/wrap}
        </div>
      </div>

    {if AngieApplication::isModuleLoaded('tracking')}
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Time and Expenses{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=default_billable_status}
            {yes_no_default name="settings[default_billable_status]" value=$settings_data.default_billable_status label='Default Billable Status for New Entries' yes_label='Billable' no_label='Non-Billable' default=$default_default_billable_status}
          {/wrap}
        </div>
      </div>
    {/if}

      <div class="content_stack_element last">
        <div class="content_stack_element_info">
          <h3>{lang}Visibility{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=default_project_object_visibility}
            {select_visibility name="settings[default_project_object_visibility]" value=$settings_data.default_project_object_visibility label='Default Visibility' optional=true}
            <p class="aid">{lang}When creating a new object in this project, set its visibility to selected value by default{/lang}.</p>
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
  $('#projectSettingsDontUseCustomTabs').click(function() {
    $('#project_tabs_settings_custom').hide();
  });
  
  $('#projectSettingsUseCustomTabs').click(function() {
    $('#project_tabs_settings_custom').show();
  });
</script>