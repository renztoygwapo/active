<?php

  /**
   * Render controls that let user configure whether they want to receive notification via various channels or not
   *
   * @package angie.framework.notifications
   * @subpackage helpers
   */

  /**
   * Render user notification settings
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_notification_channels_settings($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $user = array_required_var($params, 'user', true, 'User');

    AngieApplication::useHelper('yes_no_default', ENVIRONMENT_FRAMEWORK);

    $value = array_var($params, 'value');

    if(empty($value) || !is_foreachable($value)) {
      $value = array();
    } // if

    $result = '<div class="user_notification_channels_settings">';

    foreach(AngieApplication::notifications()->getChannels() as $channel) {
      if($channel instanceof WebInterfaceNotificationChannel || !$channel->canOverrideDefaultStatus($user)) {
        continue;
      } // if

      $result .= smarty_function_yes_no_default(array(
        'label' => $channel->getVerboseName(),
        'name' => $name . '[' . $channel->getShortName() . ']',
        'value' => array_var($value, $channel->getShortName(), null),
        'default' => $channel->isEnabledByDefault(),
      ), $smarty);
    } // foreach

    return "{$result}</div>";
  } // smarty_function_user_notification_channels_settings