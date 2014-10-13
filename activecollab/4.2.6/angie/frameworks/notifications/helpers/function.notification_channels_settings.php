<?php
  /**
   * Class description
   *
   * @package
   * @subpackage
   */

  /**
   * Return notifications channels settings widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_channels_settings($params, &$smarty) {
    AngieApplication::useWidget('notification_channels_settings', NOTIFICATIONS_FRAMEWORK);

    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value');

    if(!is_array($value)) {
      $value = array();
    } // if

    $roles = array();

    foreach(Users::getAvailableUserInstances() as $user_instance) {
      if($user_instance->isAdministrator()) {
        continue; // Administrators can always control their notification channels
      } // if

      $roles[get_class($user_instance)] = $user_instance->getRoleName();
    } // foreach

    $result = '<div class="notification_channel_settings">';

    foreach(AngieApplication::notifications()->getChannels() as $channel) {
      if($channel instanceof WebInterfaceNotificationChannel) {
        continue;
      } // if

      $channel_class = get_class($channel);

      $is_enabled = false;
      $roles_that_can_override = array();

      if(isset($value[$channel_class]) && is_array($value[$channel_class])) {
        $is_enabled = array_var($value[$channel_class], 'is_enabled');
        $roles_that_can_override = array_var($value[$channel_class], 'roles_can_override');
      } // if

      $result .= '<div class="notification_channel_settings_is_enabled">' . HTML::label($channel->getVerboseName(), null, false, array(
        'class' => 'main_label',
      ));

      $result .= '<div>' . HTML::checkbox($name . "[{$channel_class}][is_enabled]", $is_enabled, array(
        'label' => lang('Enable for Everyone by Default'),
        'value' => 1,
      )) . '</div></div>';

      $result .= HTML::label(lang('Users with Following Roles can Individually Enable or Disable :notification_channel for their Accounts', array(
        'notification_channel' => $channel->getVerboseName(),
      )), null, false, array(
        'class' => 'main_label',
      ));

      $result .= '<div class="notification_channel_settings_roles">';

      foreach($roles as $role_class => $role_name) {
        $result .= '<div>' . HTML::checkbox($name . "[{$channel_class}][roles_can_override][]", in_array($role_class, $roles_that_can_override), array(
          'label' => $role_name,
          'value' => $role_class,
        )) . '</div>';
      } // foreach

      $result .= '</div>';
    } // if

    return $result . '</div>';
  } // smarty_function_notification_channels_settings