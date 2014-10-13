<div id="notification_settings">
  {form action=Router::assemble('notifications_admin')}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Notification Channels{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=notification_channels_settings}
            {notification_channels_settings name='settings[notification_channels_settings]' value=$settings_data.notification_channels_settings}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element last">
        <div class="content_stack_element_info">
          <h3>{lang}Miscellaneous{/lang}</h3>
          <p class="aid">{lang}Various settings and features{/lang}</p>
        </div>
        <div class="content_stack_element_body">
          {wrap field=notifications_show_indicators}
            {select_how_to_show_notification_indicators name='settings[notifications_show_indicators]' value=$settings_data.notifications_show_indicators label='Display New Notifications'}
          {/wrap}
        </div>
      </div>
    </div>

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>