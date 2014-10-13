<?php

  // Build on top of application level controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Notification settings controller
   *
   * @package angie.frameworks.notifications
   * @subpackage controllers
   */
  class FwNotificationsAdminController extends AdminController {

    /**
     * Show and process settings page
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $settings_data = $this->request->post('settings', ConfigOptions::getValue(array(
          'notifications_show_indicators',
        )));

        if(!array_key_exists('notification_channels_settings', $settings_data)) {
          $settings_data['notification_channels_settings'] = array();

          foreach(AngieApplication::notifications()->getChannels() as $channel) {
            if($channel instanceof WebInterfaceNotificationChannel) {
              continue;
            } // if

            $settings_data['notification_channels_settings'][get_class($channel)] = array(
              'is_enabled' => $channel->isEnabledByDefault(),
              'roles_can_override' => $channel->whoCanOverrideDefaultStatus(),
            );
          } // foreach
        } // if

        $this->response->assign('settings_data', $settings_data);

        if($this->request->isSubmitted()) {
          DB::transact(function() use ($settings_data) {
            if(array_key_exists('notifications_show_indicators', $settings_data)) {
              ConfigOptions::setValue('notifications_show_indicators', (integer) $settings_data['notifications_show_indicators']);
            } // if

            if(array_key_exists('notification_channels_settings', $settings_data)) {
              foreach($settings_data['notification_channels_settings'] as $channel_class => $channel_settings) {
                $channel = new $channel_class;

                if($channel instanceof NotificationChannel) {
                  $channel->setEnabledByDefault(isset($channel_settings['is_enabled']) && $channel_settings['is_enabled']);
                  $channel->setWhoCanOverrideDefaultStatus(isset($channel_settings['roles_can_override']) && is_foreachable($channel_settings['roles_can_override']) ? $channel_settings['roles_can_override'] : null);
                } else {
                  throw new InvalidParamError('notification_channels_settings', $settings_data['notification_channels_settings'], "Unknown channel '$channel_class'");
                } // if
              } // foreach
            } // if
          }, 'Updating notification settings');

          $this->response->ok();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index

  }