<?php

  /**
   * select_how_to_show_notification_indicators helper implementation
   *
   * @package angie.frameworks.notifications
   * @subpackage helpers
   */

  /**
   * Render picker that lets user select how notification indicators should be disaplyed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_how_to_show_notification_indicators($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);

    if(empty($value) && $value !== Notifications::SHOW_NOTHING) {
      $value = ConfigOptions::getValue('notifications_show_indicators');
    } // if

    return HTML::radioGroupFromPossibilities($name, array(
      Notifications::SHOW_BADGE_AND_MESSAGE => lang('Number of Unread Notifications in the Status Bar plus Notifications in the Lower Right Corner of the Interface'),
      Notifications::SHOW_BADGE => lang('Only Number of Unread Notifications in the Status Bar'),
      Notifications::SHOW_NOTHING => lang('Show No Indicators of New Notifications'),
    ), $value, $params);
  } // smarty_function_select_how_to_show_notification_indicators