<?php

  AngieApplication::useController('backend', NOTIFICATIONS_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level notifications controller
   *
   * @package angie.frameworks.notifications
   * @subpackage controllers
   */
  class FwNotificationsController extends BackendController {

    /**
     * Execute before every action
     */
    function __before() {
      parent::__before();

      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('notifications', lang('Notifications'), Router::assemble('notifications'), null, true);
      $this->wireframe->breadcrumbs->add('notifications', lang('Notifications'), Router::assemble('notifications'));

      Notifications::cleanUp();
    } // __before

    /**
     * Show notifications index page
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Notifications::findRecentByUser($this->logged_user), array(
          'as' => 'notifications',
        ));
      } // if
    } // index

    /**
     * Show popup notifications list (launched from status bar)
     */
    function popup() {
      $unseen_notifications = array();
      $unread_notifications = array();

      $notifications = Notifications::findRecentByUser($this->logged_user);

      if($notifications) {
        foreach($notifications as $notification) {
          if(!$notification->isSeen($this->logged_user)) {
            $unseen_notifications[] = $notification->getId();
          } // if

          if(!$notification->isRead($this->logged_user)) {
            $unread_notifications[] = $notification->getId();
          } // if

          Notifications::markSeen($notification, $this->logged_user);
        } // foreach
      } // if

      // get config options for user
      $show_only_unread = ConfigOptions::getValueFor('popup_show_only_unread', $this->logged_user);

      $this->response->assign(array(
        'notifications' => $notifications,
        'unseen_notifications' => $unseen_notifications,
        'unread_notifications' => $unread_notifications,
        'show_only_unread' => $show_only_unread
      ));
    } // popup

    /**
     * Madd edit all or selected notifications
     */
    function mass_edit() {
      if(($this->request->isAsyncCall() && $this->request->isSubmitted()) || $this->request->isApiCall()) {
        $action = $this->request->post('mass_edit_action');
        $notification_ids = $this->request->post('selected_notification_ids') ? explode(',', $this->request->post('selected_notification_ids')) : null;

        if ($action == 'mark_all_seen') {
          Notifications::updateSeenStatusForRecipient($this->logged_user, true);
        } elseif($action == 'mark_all_read') {
          Notifications::updateReadStatusForRecipient($this->logged_user, true);
        } elseif($action == 'delete_all') {
          Notifications::clearForRecipient($this->logged_user);
        } elseif($action == 'mark_read') {
          Notifications::updateReadStatusForRecipient($this->logged_user, true, false, $notification_ids);
        } elseif($action == 'mark_unread') {
          Notifications::updateReadStatusForRecipient($this->logged_user, false, false, $notification_ids);
        } elseif($action == 'delete') {
          Notifications::clearForRecipient($this->logged_user, false, $notification_ids);
        } else {
          $this->response->operationFailed();
        } // if

        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // mass_edit

    /**
     * Refresh notifications
     */
    function refresh() {
      if($this->request->isAsyncCall()) {
        $last_refresh = $this->request->get('last_refresh_timestamp') ? DateTimeValue::makeFromString($this->request->get('last_refresh_timestamp')) : null;

        if($last_refresh instanceof DateTimeValue) {
          $unread_notification_ids = $this->request->get('unread_notification_ids') ? explode(',', $this->request->get('unread_notification_ids')) : null;

          $mark_listed_as_seen = $this->request->get('mark_listed_as_seen', false);

          $described = array();

          $notifications = Notifications::findRecentByUser($this->logged_user, $last_refresh);
          if($notifications) {
            foreach($notifications as $notification) {
              $described[] = AngieApplication::describe()->object($notification, $this->logged_user, false, AngieApplication::INTERFACE_DEFAULT);

              if($mark_listed_as_seen) {
                Notifications::markSeen($notification, $this->logged_user);
              } // if
            } // foreach
          } // if

          $mark_as_read_ids = array();

          if($unread_notification_ids && is_foreachable($unread_notification_ids)) {
            foreach($unread_notification_ids as $unread_notification_id) {
              $unread_notification_id = (integer) $unread_notification_id;

              if($unread_notification_id && Notifications::isRead($unread_notification_id, $this->logged_user)) {
                $mark_as_read_ids[] = $unread_notification_id;
              }
            } // foreach
          } // if

          $this->response->respondWithData(array(
            'last_refresh_timestamp' => DateTimeValue::now()->toMySQL(),
            'notifications' => JSON::valueToMap($described),
            'mark_as_read_ids' => $mark_as_read_ids,
          ));
        } else {
          $this->response->badRequest();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // refresh

    /**
     * Show notifications settings dialog for logged in user
     */
    function settings() {
      $settings_data = $this->request->post('settings');
      $can_override_channel_settings = AngieApplication::notifications()->canOverrideDefaultSettings($this->logged_user);

      if(!is_array($settings_data)) {
        $settings_data = array(
          'notifications_show_indicators' => ConfigOptions::getValueFor('notifications_show_indicators', $this->logged_user),
        );

        if($can_override_channel_settings) {
          $settings_data['channels_settings'] = array();

          foreach(AngieApplication::notifications()->getChannels() as $channel) {
            if($channel instanceof WebInterfaceNotificationChannel || !$channel->canOverrideDefaultStatus($this->logged_user)) {
              continue;
            } // if

            $settings_data['channels_settings'][$channel->getShortName()] = ConfigOptions::hasValueFor($channel->getShortName() . '_notifications_enabled', $this->logged_user) ? $channel->isEnabledFor($this->logged_user) : null;
          } // foreach
        } // if
      } // if

      $this->response->assign(array(
        'settings_data' => $settings_data,
        'can_override_channel_settings' => $can_override_channel_settings,
      ));

      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating notification settings @ ' . __CLASS__);

          if(array_key_exists('notifications_show_indicators', $settings_data)) {
            ConfigOptions::setValueFor('notifications_show_indicators', $this->logged_user, (integer) $settings_data['notifications_show_indicators']);
          } // if

          if($can_override_channel_settings) {
            $notification_settings = array_var($settings_data, 'channels_settings');

            if(empty($notification_settings) || !is_foreachable($notification_settings)) {
              $notification_settings = array();
            } // if

            foreach(AngieApplication::notifications()->getChannels() as $channel) {
              if($channel instanceof WebInterfaceNotificationChannel || !$channel->canOverrideDefaultStatus($this->logged_user)) {
                continue;
              } // if

              $short_name = $channel->getShortName();

              if(isset($notification_settings[$short_name]) && ($notification_settings[$short_name] === '1' || $notification_settings[$short_name] === '0')) {
                $value_to_set = $notification_settings[$short_name] === '1';
              } else {
                $value_to_set = null;
              } // if

              $channel->setEnabledFor($this->logged_user, $value_to_set);
            } // foreach
          } // if

          DB::commit('Notification settings updated @ ' . __CLASS__);

          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to save notification settings');
          throw $e;
        } // try
      } // if
    } // settings

    /**
     * Mark selected notification as read
     */
    function mark_read() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        $notification_id = $this->request->getId('notification_id');
        $notification = $notification_id ? Notifications::findById($notification_id) : null;

        if($notification instanceof Notification) {
          Notifications::markRead($notification, $this->logged_user);
          $this->response->respondWithData($notification, array(
            'as' => 'notification',
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // mark_read

    /**
     * Mark selected notification as unread
     */
    function mark_unread() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        $notification_id = $this->request->getId('notification_id');
        $notification = $notification_id ? Notifications::findById($notification_id) : null;

        if($notification instanceof Notification) {
          Notifications::markUnread($notification, $this->logged_user);
          $this->response->respondWithData($notification, array(
            'as' => 'notification',
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // mark_unread

    /**
     * Delete selected notification for logged user (don't remove entire notification instance)
     */
    function delete() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        $notification_id = $this->request->getId('notification_id');
        $notification = $notification_id ? Notifications::findById($notification_id) : null;

        if($notification instanceof Notification) {
          Notifications::clearForRecipient($this->logged_user, false, $notification->getId());
          $this->response->ok();
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete

    /**
     * Show only unread action
     */
    function show_only_unread() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      ConfigOptions::setValueFor('popup_show_only_unread', $this->logged_user, true);
      $this->response->ok();
    } // show_only_unread

    /**
     * Show read and unread
     */
    function show_read_and_unread() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      ConfigOptions::setValueFor('popup_show_only_unread', $this->logged_user, false);
      $this->response->ok();
    } // show_read_and_unread
  }