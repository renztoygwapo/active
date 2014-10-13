{title}Update Settings{/title}
{add_bread_crumb}Update Settings{/add_bread_crumb}

<div id="edit_user_settings">
  {form action=$active_user->getEditSettingsUrl() csfr_protect=true}
    <div class="content_stack_wrapper">
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Localization{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=language}
            {select_language name='user[language]' value=$user_data.language optional=true label='Language'}
          {/wrap}
        
          <div class="col">
          {wrap field=format_date}
            {select_date_format name='user[format_date]' value=$user_data.format_date optional=true label='Date Format'}
          {/wrap}
          </div>
          
          <div class="col">
          {wrap field=format_time}
            {select_time_format name='user[format_time]' value=$user_data.format_time optional=true label='Time Format'}
          {/wrap}
          </div>
        </div>
      </div>
      
      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Date and Time{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=first_weekday}
            {select_week_day name='user[time_first_week_day]' value=$user_data.time_first_week_day label='First Day of the Week'}
          {/wrap}
          
          <div class="col">
          {wrap field=timezone}
            {select_timezone name='user[time_timezone]' value=$user_data.time_timezone optional=true label='Timezone'}
          {/wrap}
          </div>
          
          <div class="col">
          {wrap field=dst}
            {yes_no_default name='user[time_dst]' value=$user_data.time_dst default=$default_dst_value label='Daylight Saving Time'}
          {/wrap}
          </div>
        </div>
      </div>

      {if $can_override_notification_settings || (ConfigOptions::getValue('morning_paper_enabled') && MorningPaper::canReceiveMorningPaper($active_user))}
      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Notifications{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
        {if $can_override_notification_settings}
          {wrap field=notifications_show_indicators}
            {select_how_to_show_notification_indicators name='user[notifications_show_indicators]' value=$user_data.notifications_show_indicators label='Display New Notifications'}
          {/wrap}

          {wrap field=notification_channels_settings}
            {user_notification_channels_settings name='user[notification_channels_settings]' value=$user_data.notification_channels_settings user=$active_user}
          {/wrap}
        {/if}

        {if ConfigOptions::getValue('morning_paper_enabled') && MorningPaper::canReceiveMorningPaper($active_user)}
          {wrap field=morning_paper_enabled}
            {yes_no name='user[morning_paper_enabled]' value=$user_data.morning_paper_enabled label='Receive Morning Paper'}
          {/wrap}

          {if $active_user->isProjectManager()}
            {wrap field=morning_paper_include_all_projects}
              {yes_no name='user[morning_paper_include_all_projects]' value=$user_data.morning_paper_include_all_projects label='Include Data from All Projects'}
            {/wrap}
          {/if}
        {/if}
        </div>
      </div>
      {/if}
      
      {if $logged_user->isPeopleManager()}
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Auto-Assign{/lang}</h3>
        </div>
        <div class="content_stack_element_body" id="auto_assign_user">
          {wrap field=auto_assign}
            {label first_name=$active_user->getFirstName(true)}Automatically Add :first_name to New Projects{/label}
            {yes_no name="user[auto_assign]" value=$user_data.auto_assign id=userAutoAssign}
            <p class="details">{lang}Select <b>Yes</b> to have this user automatically added to each new project when the project is created{/lang}</p>
          {/wrap}
          
          <div id="auto_assign_role_and_permissions" {if !$user_data.auto_assign}style="display: none"{/if}>
            <p>{lang}Please select a role or set custom permissions for user in this project{/lang}:</p>
            {select_user_project_permissions name=user role_id=$user_data.auto_assign_role_id permissions=$user_data.auto_assign_permissions role_id_field=auto_assign_role_id permissions_field=auto_assign_permissions}
          </div>
        </div>
      </div>
      {/if}

      {if $active_user->isMember() || $active_user instanceof Subcontractor}
      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Home Screen{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=userJobType}
            {select_default_homescreen_tab name='user[default_homescreen_tab_id]' value=$user_data.default_homescreen_tab_id label='Default Home Screen Tab' user=$active_user}
          {/wrap}
        </div>
      </div>
      {/if}

      {if AngieApplication::isModuleLoaded('tracking') && $logged_user->isProjectManager()}
        <div class="content_stack_element even last">
          <div class="content_stack_element_info">
            <h3>{lang}Available Job Types{/lang}</h3>
          </div>
          <div class="content_stack_element_body">
            {wrap field=userJobType}
              {select_user_job_type name='user[job_type_id]' value=$user_data.job_type_id label='Available Job Types' id=userJobType}
            {/wrap}
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
  $('#userAutoAssignYesInput').click(function() {
    $('#auto_assign_role_and_permissions').show();
  });
  
  $('#userAutoAssignNoInput').click(function() {
    $('#auto_assign_role_and_permissions').hide();
  });
</script>