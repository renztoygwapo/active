<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.framework.globalization
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function globalization_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToGeneral('datetime', lang('Date and Time'), Router::assemble('date_time_settings'), AngieApplication::getImageUrl('admin_panel/date-time.png', GLOBALIZATION_FRAMEWORK), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'datetime_settings_updated', 
        'success_message' => lang('Settings updated')
      )), 
    ));
    $admin_panel->addToGeneral('workweek', lang('Workweek'), Router::assemble('workweek_settings'), AngieApplication::getImageUrl('admin_panel/workweek.png', GLOBALIZATION_FRAMEWORK), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'workweek_settings_updated', 
        'success_message' => lang('Settings updated'),
      )),
    ));
    $admin_panel->addToGeneral('currencies', lang('Currencies'), Router::assemble('admin_currencies'), AngieApplication::getImageUrl('admin_panel/currencies.png', GLOBALIZATION_FRAMEWORK), array(
      'onclick' => new FlyoutCallback(),
    ));
    $admin_panel->addToGeneral('languages', lang('Languages'), Router::assemble('admin_languages'), AngieApplication::getImageUrl('admin_panel/languages.png', GLOBALIZATION_FRAMEWORK));
  } // globalization_handle_on_admin_panel