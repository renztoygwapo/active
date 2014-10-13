{form action=Router::assemble('notifications_settings')}
  {wrap_fields}
    {wrap field=notifications_show_indicators}
      {select_how_to_show_notification_indicators name='settings[notifications_show_indicators]' value=$settings_data.notifications_show_indicators label='Display New Notifications'}
    {/wrap}

    {if $can_override_channel_settings}
      {wrap field=notification_channels_settings}
        {user_notification_channels_settings name='settings[channels_settings]' value=$settings_data.channels_settings user=$logged_user}
      {/wrap}
    {/if}

    <div id="empty_slate_system_roles" class="empty_slate">
      <h3>{lang}About These Settings{/lang}</h3>

      <ul class="icon_list">
        <li>
          <img src="{image_url name="empty-slates/bulb.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" class="icon_list_icon" alt="" />
          <span class="icon_list_title">{lang}New Notifications Display{/lang}</span>
          <span class="icon_list_description">{lang}System can show that there are new things happening in several ways: using number of updates in the lower right corner, by showing notification messages in upper right corner or by updating the list in the background without interrupting you{/lang}.<br><br></span>
        </li>
      {if $can_override_channel_settings}
        <li>
          <img src="{image_url name="empty-slates/help.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" class="icon_list_icon" alt="" />
          <span class="icon_list_title">{lang}Additional Notification Channels{/lang}</span>
          <span class="icon_list_description">{lang}Notifications are always delivered to your web browser using Notifications status bar widget (available in the lower left corner of this page). Additional notification channels, like email, can be individually enabled or disable to fit your workflow{/lang}.</span>
        </li>
      {/if}
      </ul>
    </div>
  {/wrap_fields}

  {wrap_buttons}
    {submit}Save{/submit}
  {/wrap_buttons}
{/form}